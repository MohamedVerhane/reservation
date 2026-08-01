<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'hotel_id'        => $this->hotel_id,
            'room_id'         => $this->room_id,
            'check_in'        => $this->check_in?->toDateString(),
            'check_out'       => $this->check_out?->toDateString(),
            'guests'          => $this->guests,
            'children_count'  => $this->children_count,
            'total_price'     => (float) $this->total_price,
            'status'          => $this->status,
            'status_label'    => $this->status_label,
            'status_color'    => $this->status_color,
            'notes'           => $this->notes,
            'nights'          => $this->nights,
            'total_paid'      => $this->total_paid,
            'balance'         => $this->balance,
            'is_upcoming'     => $this->is_upcoming,
            'is_past'         => $this->is_past,
            'is_active'       => $this->is_active,
            'user'            => new UserResource($this->whenLoaded('user')),
            'hotel'           => new HotelSummaryResource($this->whenLoaded('hotel')),
            'room'            => new RoomResource($this->whenLoaded('room')),
            'payments'        => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
