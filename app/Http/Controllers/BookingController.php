<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Traits\NotifyAdmins;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use NotifyAdmins;

    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availabilityService
    ) {}

    /**
     * Display the booking page for a hotel.
     */
    public function show(Request $request, string $hotelSlug)
    {
        $hotel = Hotel::where('slug', $hotelSlug)
            ->where('is_active', true)
            ->with([
                'roomTypes' => fn ($q) => $q->where('is_active', true)
                    ->withCount(['rooms' => fn ($q) => $q->where('is_active', true)]),
            ])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->approved()])
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        $checkIn = null;
        $checkOut = null;
        $guests = $request->integer('adults', 1);
        $children = $request->integer('children', 0);
        $roomTypes = $hotel->roomTypes;

        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = Carbon::parse($request->input('check_in'));
            $checkOut = Carbon::parse($request->input('check_out'));

            $errors = $this->bookingService->validateDates($checkIn, $checkOut);

            if (! empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            $nights = $this->bookingService->calculateNights($checkIn, $checkOut);

            $availableRoomTypeIds = Room::availableForDates($checkIn, $checkOut)
                ->where('is_active', true)
                ->pluck('room_type_id')
                ->unique();

            $roomTypes = $hotel->roomTypes()
                ->where('is_active', true)
                ->whereIn('id', $availableRoomTypeIds)
                ->withCount(['rooms' => fn ($q) => $q->where('is_active', true)])
                ->get()
                ->map(function ($roomType) use ($nights) {
                    $roomType->total_price = $roomType->calculatePrice($nights);
                    $roomType->nights = $nights;

                    return $roomType;
                });
        }

        return view('frontend.booking.show', [
            'hotel' => $hotel,
            'roomTypes' => $roomTypes,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'guests' => $guests,
            'children' => $children,
        ]);
    }

    /**
     * Check room availability via AJAX.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:10',
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        $errors = $this->bookingService->validateDates($checkIn, $checkOut);

        if (! empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $nights = $this->bookingService->calculateNights($checkIn, $checkOut);

        $availableRoomTypes = $this->availabilityService->getAvailableRoomTypes(
            $validated['hotel_id'],
            $checkIn,
            $checkOut
        );

        $result = collect($availableRoomTypes)
            ->map(function ($roomType) use ($nights) {
                return [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                    'description' => $roomType->description,
                    'base_price' => $roomType->base_price,
                    'available_count' => (int) $roomType->available_count,
                    'max_guests' => $roomType->max_guests,
                    'max_children' => $roomType->max_children,
                    'total_price' => $roomType->base_price * $nights,
                    'nights' => $nights,
                ];
            })
            ->values();

        return response()->json([
            'room_types' => $result,
            'nights' => $nights,
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
        ]);
    }

    /**
     * Get calendar availability via AJAX.
     */
    public function calendar(int $hotelId, Request $request): JsonResponse
    {
        $month = $request->integer('month', (int) Carbon::now()->month);
        $year = $request->integer('year', (int) Carbon::now()->year);

        $calendar = $this->availabilityService->getCalendarAvailability($hotelId, $month, $year);

        return response()->json([
            'calendar' => $calendar,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Step 2: Select a room type and show available rooms.
     */
    public function selectRoom(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:10',
            'room_type_id' => 'required|exists:room_types,id',
        ]);

        $hotel = Hotel::findOrFail($validated['hotel_id']);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        $errors = $this->bookingService->validateDates($checkIn, $checkOut);

        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $nights = $this->bookingService->calculateNights($checkIn, $checkOut);

        $roomType = $hotel->roomTypes()->findOrFail($validated['room_type_id']);

        $rooms = $this->availabilityService->getAvailableRooms(
            $hotel->id,
            $validated['room_type_id'],
            $checkIn,
            $checkOut
        )->map(function ($room) use ($nights, $checkIn, $checkOut) {
            $room->total_price = $room->calculateTotalPrice($checkIn, $checkOut);
            $room->nights = $nights;

            return $room;
        });

        return view('frontend.booking.select-room', [
            'hotel' => $hotel,
            'roomType' => $roomType,
            'rooms' => $rooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'nights' => $nights,
            'roomTypeId' => $validated['room_type_id'],
        ]);
    }

    /**
     * Show the hotel review/rating form for past bookings.
     */
    public function reviewForm()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'checked_out', 'confirmed'])
            ->with([
                'hotel',
                'room.roomType',
            ])
            ->latest()
            ->get();

        $reviewedReservationIds = Review::where('user_id', Auth::id())
            ->whereNotNull('reservation_id')
            ->pluck('reservation_id')
            ->toArray();

        return view('frontend.booking.hotel-review', compact('reservations', 'reviewedReservationIds'));
    }

    /**
     * Step 3: Review the booking before confirmation.
     */
    public function review(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:10',
        ]);

        $room = Room::with(['hotel', 'roomType', 'amenities', 'images'])
            ->where('id', $validated['room_id'])
            ->where('hotel_id', $validated['hotel_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        $errors = $this->bookingService->validateDates($checkIn, $checkOut);

        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        if (! $this->bookingService->isRoomAvailable($room->id, $checkIn, $checkOut)) {
            return back()->withErrors([
                'room_id' => __('admin.booking.room_unavailable'),
            ])->withInput();
        }

        $nights = $this->bookingService->calculateNights($checkIn, $checkOut);
        $totalPrice = $room->calculateTotalPrice($checkIn, $checkOut);

        return view('frontend.booking.review', [
            'hotel' => $room->hotel,
            'room' => $room,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'nights' => $nights,
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * Step 4: Store the booking (create reservation + payment).
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $result = $this->bookingService->createReservation($validated, Auth::id());

        if ($result['success']) {
            $this->bookingService->sendBookingNotifications($result['reservation']);

            return redirect()->route('frontend.booking.confirmation', $result['reservation'])
                ->with('success', __('admin.booking.confirmed'));
        }

        return back()->withErrors(['booking' => $result['error']])
            ->setStatusCode($result['status']);
    }

    /**
     * Step 5: Show booking confirmation.
     */
    public function confirmation(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, __('admin.booking.unauthorized'));
        }

        $reservation->load([
            'hotel',
            'room.roomType',
            'payments',
        ]);

        return view('frontend.booking.confirmation', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Display the authenticated user's reservations.
     */
    public function myReservations()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->with([
                'hotel',
                'room.roomType',
                'payments',
            ])
            ->latest()
            ->paginate(10);

        return view('frontend.booking.my-reservations', [
            'reservations' => $reservations,
        ]);
    }
}
