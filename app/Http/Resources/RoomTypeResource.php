<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'hotel_id'      => $this->hotel_id,
            'name'          => $this->name,
            'description'   => $this->description,
            'base_price'    => (float) $this->base_price,
            'price_formatted' => $this->price_formatted,
            'max_guests'    => $this->max_guests,
            'max_children'  => $this->max_children,
            'is_active'     => $this->is_active,
            'rooms_count'   => $this->whenCounted('rooms'),
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}
