<?php

namespace App\Payment\DTOs;

use App\Payment\Enums\PaymentStatus;

class RefundResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly PaymentStatus $status,
        public readonly ?string $refundId = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $message = null,
        public readonly array $gatewayResponse = [],
        public readonly ?string $errorCode = null,
    ) {}

    public static function success(
        string $refundId,
        float $amount,
        string $currency,
        array $gatewayResponse = [],
    ): self {
        return new self(
            success: true,
            status: PaymentStatus::Refunded,
            refundId: $refundId,
            amount: $amount,
            currency: $currency,
            message: 'Refund processed successfully.',
            gatewayResponse: $gatewayResponse,
        );
    }

    public static function failed(
        string $message,
        ?string $errorCode = null,
        array $gatewayResponse = [],
    ): self {
        return new self(
            success: false,
            status: PaymentStatus::Failed,
            message: $message,
            errorCode: $errorCode,
            gatewayResponse: $gatewayResponse,
        );
    }
}
