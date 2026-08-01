<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'hotel_id'        => $this->hotel_id,
            'reservation_id'  => $this->reservation_id,
            'rating'          => $this->rating,
            'star_display'    => $this->star_display,
            'comment'         => $this->comment,
            'reply'           => $this->reply,
            'replied_at'      => $this->replied_at?->toISOString(),
            'has_reply'       => $this->has_reply,
            'is_approved'     => $this->is_approved,
            'user'            => new UserResource($this->whenLoaded('user')),
            'hotel'           => new HotelSummaryResource($this->whenLoaded('hotel')),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
