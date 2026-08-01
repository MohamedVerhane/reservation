<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'city'           => $this->city,
            'country'        => $this->country,
            'star_rating'    => $this->star_rating,
            'cover_image'    => $this->cover_image_url,
            'average_rating' => $this->average_rating,
            'reviews_count'  => $this->reviews_count,
        ];
    }
}
