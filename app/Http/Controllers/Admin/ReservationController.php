<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReservationRequest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        $query = Reservation::with(['user', 'hotel', 'room.roomType']);

        if (request('search')) {
            $term = '%' . addslashes(request('search')) . '%';
            $query->where(function ($q) use ($term): void {
                $q->where('id', (int) request('search'))
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'LIKE', $term))
                  ->orWhereHas('hotel', fn ($hq) => $hq->where('name', 'LIKE', $term));
            });
        }

        if (request('hotel_id')) {
            $query->where('hotel_id', request('hotel_id'));
        }

        if (request('status') !== null && request('status') !== '') {
            $query->where('status', request('status'));
        }

        if (request('date_from')) {
            $query->where('check_in', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->where('check_out', '<=', request('date_to'));
        }

        $reservations = $query->latest()->paginate(12)->withQueryString();
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.reservations.index', compact('reservations', 'hotels'));
    }

    public function create(): View
    {
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');
        $guests = User::orderBy('name')->pluck('name', 'id');

        return view('admin.reservations.create', compact('hotels', 'guests'));
    }

    public function store(StoreReservationRequest $request): RedirectResponse|JsonResponse
    {
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $room = Room::lockForUpdate()->findOrFail($request->room_id);
            $checkIn = \Carbon\Carbon::parse($request->check_in);
            $checkOut = \Carbon\Carbon::parse($request->check_out);

            if ($room->hotel_id !== (int) $request->hotel_id) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['room_id' => __('admin.reservation.room_belongs_to_hotel')]);
            }

            if (!$room->isAvailableForDates($checkIn, $checkOut)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['room_id' => __('admin.reservation.room_not_available')]);
            }

            $totalPrice = $room->calculateTotalPrice($checkIn, $checkOut);

            $reservation = Reservation::create([
                'user_id' => $request->user_id,
                'hotel_id' => $request->hotel_id,
                'room_id' => $request->room_id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $request->guests,
                'children_count' => $request->children_count ?? 0,
                'total_price' => $totalPrice,
                'status' => Reservation::STATUS_PENDING,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.reservations.show', $reservation)
                ->with('success', __('admin.reservation.created'));
        });

        if ($result instanceof RedirectResponse) {
            return $result->orJson();
        }

        return $result;
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        $reservation->load([
            'user',
            'hotel',
            'room.roomType',
            'payments' => fn ($q) => $q->latest(),
        ])->loadCount('payments');

        $totalPaid = $reservation->total_paid;
        $balance = $reservation->balance;

        return view('admin.reservations.show', compact('reservation', 'totalPaid', 'balance'));
    }

    public function edit(Reservation $reservation): View
    {
        $this->authorize('update', $reservation);
        
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');
        $guests = User::orderBy('name')->pluck('name', 'id');
        
        $reservation->load(['user', 'hotel', 'room.roomType']);
        
        return view('admin.reservations.edit', compact('reservation', 'hotels', 'guests'));
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        $reservation->cancel();
        $reservation->delete();

        return redirect()->route('admin.reservations.index')
            ->with('success', __('admin.reservation.deleted'));
    }

    public function confirm(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return redirect()->back()->with('error', __('admin.reservation.cannot_confirm'));
        }

        $reservation->confirm();
        $reservation->load(['user', 'hotel', 'room']);
        $reservation->user->notify(new BookingConfirmed($reservation));

        return redirect()->back()->with('success', __('admin.reservation.confirmed'));
    }

    public function checkIn(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (!$reservation->canBeCheckedIn()) {
            return redirect()->back()->with('error', __('admin.reservation.cannot_checkin'));
        }

        $reservation->checkIn();

        return redirect()->back()->with('success', __('admin.reservation.checked_in'));
    }

    public function checkOut(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (!$reservation->canBeCheckedOut()) {
            return redirect()->back()->with('error', __('admin.reservation.cannot_checkout'));
        }

        $reservation->checkOut();

        return redirect()->back()->with('success', __('admin.reservation.checked_out'));
    }

    public function cancel(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (!$reservation->canBeCancelled()) {
            return redirect()->back()->with('error', __('admin.reservation.cannot_cancel'));
        }

        $reservation->cancel();
        $reservation->load(['user', 'hotel', 'room']);
        $reservation->user->notify(new BookingCancelled($reservation, __('admin.reservation.cancelled_by_admin')));

        return redirect()->back()->with('success', __('admin.reservation.cancelled'));
    }

    public function toggleStatus(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        $statuses = [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_CHECKED_IN,
            Reservation::STATUS_CHECKED_OUT,
        ];
        $currentIndex = array_search($reservation->status, $statuses);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }
        $nextIndex = ($currentIndex + 1) % count($statuses);
        $reservation->update(['status' => $statuses[$nextIndex]]);

        return redirect()->back()->with('success', __('admin.reservation.status_updated', ['status' => $reservation->status_label]));
    }

    public function getRooms(Hotel $hotel, Request $request): JsonResponse
    {
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');

        $query = Room::where('hotel_id', $hotel->id)
            ->active()
            ->with('roomType:id,name,base_price');

        if ($checkIn && $checkOut) {
            $query->availableForDates(\Carbon\Carbon::parse($checkIn), \Carbon\Carbon::parse($checkOut));
        }

        $rooms = $query->get()->map(fn ($room) => [
            'id' => $room->id,
            'label' => $room->display_name,
            'room_number' => $room->room_number,
            'type_name' => $room->roomType?->name,
            'base_price' => $room->roomType?->base_price,
        ]);

        return response()->json($rooms);
    }

    public function calculatePrice(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $room = Room::findOrFail($request->room_id);
        $checkIn = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $nights = $room->calculateNights($checkIn, $checkOut);
        $totalPrice = $room->calculateTotalPrice($checkIn, $checkOut);

        return response()->json([
            'nights' => $nights,
            'total_price' => number_format($totalPrice, 2, '.', ''),
            'per_night' => number_format($room->roomType->base_price, 2, '.', ''),
        ]);
    }
}
