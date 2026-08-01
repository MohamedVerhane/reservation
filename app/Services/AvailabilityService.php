<?php

namespace App\Services;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    /**
     * Get available room types with counts for a given date range.
     * Uses subquery for room counting and WHERE instead of HAVING for SQLite compat.
     */
    public function getAvailableRoomTypes(
        int $hotelId,
        Carbon $checkIn,
        Carbon $checkOut
    ): array {
        $availableRoomTypeIds = Room::query()
            ->select('room_type_id')
            ->selectRaw('COUNT(*) as available_count')
            ->availableForDates($checkIn, $checkOut)
            ->groupBy('room_type_id')
            ->pluck('available_count', 'room_type_id');

        if ($availableRoomTypeIds->isEmpty()) {
            return [];
        }

        return DB::table('room_types')
            ->where('room_types.hotel_id', $hotelId)
            ->where('room_types.is_active', true)
            ->whereIn('room_types.id', $availableRoomTypeIds->keys())
            ->select(
                'room_types.id',
                'room_types.name',
                'room_types.description',
                'room_types.base_price',
                'room_types.max_guests',
                'room_types.max_children',
            )
            ->get()
            ->map(function ($roomType) use ($availableRoomTypeIds) {
                $roomType->available_count = $availableRoomTypeIds[$roomType->id] ?? 0;
                return $roomType;
            })
            ->toArray();
    }

    /**
     * Get available rooms for a specific room type and date range.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Room>
     */
    public function getAvailableRooms(
        int $hotelId,
        int $roomTypeId,
        Carbon $checkIn,
        Carbon $checkOut
    ): \Illuminate\Database\Eloquent\Collection {
        return Room::availableForDates($checkIn, $checkOut)
            ->where('rooms.hotel_id', $hotelId)
            ->where('rooms.room_type_id', $roomTypeId)
            ->where('rooms.is_active', true)
            ->with(['roomType', 'amenities', 'images'])
            ->get();
    }

    /**
     * Get calendar availability for a hotel.
     */
    public function getCalendarAvailability(
        int $hotelId,
        int $month,
        int $year
    ): array {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $totalRooms = Room::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->count();

        $reservationsByDate = DB::table('reservations')
            ->where('reservations.hotel_id', $hotelId)
            ->where('reservations.status', '!=', 'cancelled')
            ->where('reservations.check_in', '<', $end->copy()->addDay())
            ->where('reservations.check_out', '>', $start)
            ->select(
                DB::raw('DATE(reservations.check_in) as start_date'),
                DB::raw('DATE(reservations.check_out) as end_date'),
            )
            ->get();

        $bookedPerDay = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $bookedPerDay[$date->toDateString()] = 0;
        }

        foreach ($reservationsByDate as $reservation) {
            $resStart = Carbon::parse($reservation->start_date);
            $resEnd = Carbon::parse($reservation->end_date);
            $effectiveStart = $resStart->gt($start) ? $resStart : $start;
            $effectiveEnd = $resEnd->lt($end) ? $resEnd : $end;

            for ($date = $effectiveStart->copy(); $date->lte($effectiveEnd); $date->addDay()) {
                $dateStr = $date->toDateString();
                if (isset($bookedPerDay[$dateStr])) {
                    $bookedPerDay[$dateStr]++;
                }
            }
        }

        $calendar = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateStr = $date->toDateString();
            $booked = $bookedPerDay[$dateStr] ?? 0;

            $calendar[] = [
                'date' => $dateStr,
                'available' => max(0, $totalRooms - $booked),
                'total' => $totalRooms,
                'booked' => $booked,
            ];
        }

        return $calendar;
    }
}
