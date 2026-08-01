<?php

namespace App\Payment\DTOs;

class RefundRequest
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $transactionId,
        public readonly ?string $paymentId = null,
        public readonly ?string $reason = null,
        public readonly array $metadata = [],
    ) {}

    public static function full(string $transactionId, float $amount, string $currency, ?string $reason = null): self
    {
        return new self(
            amount: $amount,
            currency: $currency,
            transactionId: $transactionId,
            reason: $reason ?? 'Full refund requested.',
        );
    }

    public static function partial(string $transactionId, float $amount, string $currency, ?string $reason = null): self
    {
        return new self(
            amount: $amount,
            currency: $currency,
            transactionId: $transactionId,
            reason: $reason ?? 'Partial refund requested.',
        );
    }
}
