<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Room::active()
            ->with(['hotel:id,name,slug', 'roomType:id,name,base_price', 'images'])
            ->withCount('amenities');

        if ($hotelId = $request->input('hotel_id')) {
            $query->where('hotel_id', $hotelId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($roomTypeId = $request->input('room_type_id')) {
            $query->where('room_type_id', $roomTypeId);
        }

        if ($minPrice = $request->input('min_price')) {
            $query->whereHas('roomType', fn ($q) => $q->where('base_price', '>=', $minPrice));
        }

        if ($maxPrice = $request->input('max_price')) {
            $query->whereHas('roomType', fn ($q) => $q->where('base_price', '<=', $maxPrice));
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $rooms = $query->orderBy('room_number')->paginate($perPage);

        return $this->paginatedResponse(RoomResource::collection($rooms));
    }

    public function show(int $hotelId, int $roomId): JsonResponse
    {
        $room = Room::where('hotel_id', $hotelId)
            ->where('id', $roomId)
            ->active()
            ->with([
                'hotel:id,name,slug,city,country',
                'roomType:id,name,base_price,description',
                'amenities',
                'images',
            ])
            ->first();

        if (!$room) {
            return $this->notFoundResponse('Room not found.');
        }

        return $this->successResponse(
            new RoomResource($room),
            'Room retrieved'
        );
    }

    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'hotel_id'  => 'required|exists:hotels,id',
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $rooms = Room::availableForDates($checkIn, $checkOut)
            ->where('hotel_id', $request->hotel_id)
            ->where('is_active', true)
            ->with(['roomType:id,name,base_price', 'images'])
            ->get()
            ->map(function ($room) use ($checkIn, $checkOut) {
                $nights = (int) $checkIn->diffInDays($checkOut);
                $room->total_price = $room->calculateTotalPrice($checkIn, $checkOut);
                $room->nights = $nights;
                return $room;
            });

        return $this->successResponse(
            RoomResource::collection($rooms),
            'Available rooms retrieved'
        );
    }
}
