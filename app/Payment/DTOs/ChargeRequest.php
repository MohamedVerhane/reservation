<?php

namespace App\Payment\DTOs;

use App\Models\Reservation;

class ChargeRequest
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly ?string $reservationId = null,
        public readonly ?string $paymentId = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $customerName = null,
        public readonly ?string $paymentMethod = null,
        public readonly array $metadata = [],
        public readonly ?string $returnUrl = null,
        public readonly ?string $cancelUrl = null,
        public readonly ?string $notifyUrl = null,
    ) {}

    public static function fromReservation(
        Reservation $reservation,
        string $paymentMethod = 'credit_card',
        ?string $returnUrl = null,
        ?string $cancelUrl = null,
        ?string $notifyUrl = null,
    ): self {
        return new self(
            amount: (float) $reservation->total_price,
            currency: config('payment.currency', 'USD'),
            description: "Reservation #{$reservation->id} - " . ($reservation->hotel->name ?? 'Hotel'),
            reservationId: (string) $reservation->id,
            paymentId: null,
            customerEmail: $reservation->user?->email,
            customerName: $reservation->user?->name,
            paymentMethod: $paymentMethod,
            metadata: [
                'reservation_id' => $reservation->id,
                'hotel_id' => $reservation->hotel_id,
                'room_id' => $reservation->room_id,
                'check_in' => $reservation->check_in,
                'check_out' => $reservation->check_out,
            ],
            returnUrl: $returnUrl,
            cancelUrl: $cancelUrl,
            notifyUrl: $notifyUrl,
        );
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'reservation_id' => $this->reservationId,
            'payment_id' => $this->paymentId,
            'customer_email' => $this->customerEmail,
            'customer_name' => $this->customerName,
            'payment_method' => $this->paymentMethod,
            'metadata' => $this->metadata,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
        ];
    }
}
