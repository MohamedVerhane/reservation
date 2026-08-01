<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'reservation_id'  => $this->reservation_id,
            'amount'          => (float) $this->amount,
            'method'          => $this->method,
            'method_label'    => $this->method_label,
            'status'          => $this->status,
            'status_label'    => $this->status_label,
            'status_color'    => $this->status_color,
            'transaction_id'  => $this->transaction_id,
            'paid_at'         => $this->paid_at?->toISOString(),
            'reservation'     => new ReservationResource($this->whenLoaded('reservation')),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
