<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Notifications\BookingConfirmed;
use App\Traits\NotifyAdmins;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    use NotifyAdmins;

    /**
     * Calculate the number of nights between check-in and check-out.
     */
    public function calculateNights(Carbon $checkIn, Carbon $checkOut): int
    {
        return (int) $checkIn->diffInDays($checkOut);
    }

    /**
     * Check if a room is available for the given date range.
     */
    public function isRoomAvailable(
        int $roomId,
        Carbon $checkIn,
        Carbon $checkOut,
        ?int $excludeId = null
    ): bool {
        $query = Room::where('id', $roomId)
            ->availableForDates($checkIn, $checkOut);

        if ($excludeId !== null) {
            $query->whereDoesntHave('reservations', fn ($q) => $q->where('id', '!=', $excludeId));
        }

        return $query->exists();
    }

    /**
     * Generate a unique transaction ID.
     */
    public function generateTransactionId(): string
    {
        return 'TXN-'.time().strtoupper(Str::random(8));
    }

    /**
     * Validate booking date range and return any errors.
     *
     * @return array<string, string>
     */
    public function validateDates(Carbon $checkIn, Carbon $checkOut): array
    {
        $errors = [];

        if ($checkIn->lt(Carbon::today())) {
            $errors['check_in'] = 'The check-in date must be today or in the future.';
        }

        if ($checkOut->lte($checkIn)) {
            $errors['check_out'] = 'The check-out date must be after the check-in date.';
        }

        if ($this->calculateNights($checkIn, $checkOut) > 30) {
            $errors['check_out'] = 'The maximum stay is 30 nights.';
        }

        return $errors;
    }

    /**
     * Create a reservation with double-booking prevention.
     */
    public function createReservation(array $validated, int $userId): array
    {
        return DB::transaction(function () use ($validated, $userId) {
            $roomId = $validated['room_id'];
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);

            $room = Room::lockForUpdate()
                ->where('id', $roomId)
                ->where('is_active', true)
                ->first();

            if (! $room) {
                return [
                    'success' => false,
                    'error' => 'The selected room no longer exists.',
                    'status' => 404,
                ];
            }

            if (! $this->isRoomAvailable($roomId, $checkIn, $checkOut)) {
                return [
                    'success' => false,
                    'error' => 'This room is no longer available for the selected dates. Please choose another room.',
                    'status' => 422,
                ];
            }

            $nights = $this->calculateNights($checkIn, $checkOut);
            $totalPrice = $room->roomType->calculatePrice($nights);

            $reservation = Reservation::create([
                'user_id' => $userId,
                'hotel_id' => $validated['hotel_id'],
                'room_id' => $roomId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $validated['adults'],
                'children_count' => $validated['children'] ?? 0,
                'total_price' => $totalPrice,
                'status' => Reservation::STATUS_CONFIRMED,
                'notes' => $validated['special_requests'] ?? null,
            ]);

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $totalPrice,
                'method' => $validated['payment_method'],
                'status' => Payment::STATUS_PENDING,
                'transaction_id' => $this->generateTransactionId(),
            ]);

            return [
                'success' => true,
                'reservation' => $reservation,
                'status' => 200,
            ];
        });
    }

    /**
     * Send booking notifications to user and admins.
     */
    public function sendBookingNotifications(Reservation $reservation): void
    {
        $reservation->load(['hotel', 'room.roomType', 'user']);

        $reservation->user->notify(new BookingConfirmed($reservation));
        $this->notifyAdminsNewBooking($reservation);
    }
}
