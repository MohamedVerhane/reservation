<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'hotel_id'      => $this->hotel_id,
            'room_type_id'  => $this->room_type_id,
            'room_number'   => $this->room_number,
            'floor'         => $this->floor,
            'status'        => $this->status,
            'status_label'  => $this->status_label,
            'is_active'     => $this->is_active,
            'display_name'  => $this->display_name,
            'hotel'         => new HotelSummaryResource($this->whenLoaded('hotel')),
            'room_type'     => new RoomTypeResource($this->whenLoaded('roomType')),
            'amenities'     => AmenityResource::collection($this->whenLoaded('amenities')),
            'images'        => RoomImageResource::collection($this->whenLoaded('images')),
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}
