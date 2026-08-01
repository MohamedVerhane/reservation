<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'hotel_id'    => $this->hotel_id,
            'title'       => $this->title,
            'description' => $this->description,
            'images'      => GalleryImageResource::collection($this->whenLoaded('images')),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
