<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBookingRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Reservation::where('user_id', Auth::id())
            ->with(['hotel:id,name,slug,city', 'room.roomType:id,name', 'payments']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $reservations = $query->latest()->paginate($perPage);

        return $this->paginatedResponse(ReservationResource::collection($reservations));
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $result = DB::transaction(function () use ($request) {
            $roomId  = $request->room_id;
            $checkIn  = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);

            $room = Room::lockForUpdate()
                ->where('id', $roomId)
                ->where('is_active', true)
                ->first();

            if (!$room) {
                return ['success' => false, 'error' => 'The selected room no longer exists.', 'code' => 404];
            }

            $hasConflict = Reservation::where('room_id', $roomId)
                ->where('status', '!=', 'cancelled')
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn)
                ->exists();

            if ($hasConflict) {
                return ['success' => false, 'error' => 'This room is no longer available for the selected dates.', 'code' => 422];
            }

            $nights = (int) $checkIn->diffInDays($checkOut);
            $totalPrice = $room->calculateTotalPrice($checkIn, $checkOut);

            $reservation = Reservation::create([
                'user_id'        => Auth::id(),
                'hotel_id'       => $request->hotel_id,
                'room_id'        => $roomId,
                'check_in'       => $checkIn,
                'check_out'      => $checkOut,
                'guests'         => $request->adults,
                'children_count' => $request->children ?? 0,
                'total_price'    => $totalPrice,
                'status'         => Reservation::STATUS_CONFIRMED,
                'notes'          => $request->special_requests,
            ]);

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $totalPrice,
                'method'         => $request->payment_method,
                'status'         => Payment::STATUS_PENDING,
                'transaction_id' => 'TXN-' . time() . strtoupper(Str::random(8)),
            ]);

            $reservation->load(['hotel:id,name,slug', 'room.roomType:id,name', 'payments']);

            return ['success' => true, 'reservation' => $reservation];
        });

        if (!$result['success']) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->createdResponse(
            new ReservationResource($result['reservation']),
            'Booking created successfully'
        );
    }

    public function show(Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Unauthorized access to this reservation.');
        }

        $reservation->load([
            'hotel:id,name,slug,city,country,phone,email',
            'room.roomType:id,name,base_price',
            'room.images',
            'payments',
            'user:id,name,email',
        ]);

        return $this->successResponse(
            new ReservationResource($reservation),
            'Reservation retrieved'
        );
    }

    public function cancel(Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Unauthorized access to this reservation.');
        }

        if (!$reservation->canBeCancelled()) {
            return $this->errorResponse('This reservation cannot be cancelled.', 422);
        }

        $reservation->cancel();

        return $this->successResponse(
            new ReservationResource($reservation->fresh()->load(['hotel', 'room.roomType', 'payments'])),
            'Reservation cancelled'
        );
    }
}
