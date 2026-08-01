<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'address'         => $this->address,
            'city'            => $this->city,
            'country'         => $this->country,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'star_rating'     => $this->star_rating,
            'star_rating_label' => $this->star_rating_label,
            'is_active'       => $this->is_active,
            'cover_image'     => $this->cover_image_url,
            'full_address'    => $this->full_address,
            'average_rating'  => $this->average_rating,
            'reviews_count'   => $this->reviews_count,
            'owner'           => new UserResource($this->whenLoaded('user')),
            'room_types'      => RoomTypeResource::collection($this->whenLoaded('roomTypes')),
            'rooms'           => RoomResource::collection($this->whenLoaded('rooms')),
            'reviews'         => ReviewResource::collection($this->whenLoaded('reviews')),
            'galleries'       => GalleryResource::collection($this->whenLoaded('galleries')),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
