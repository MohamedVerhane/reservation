<?php

namespace App\Payment\DTOs;

use App\Payment\Enums\PaymentStatus;

class ChargeResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly PaymentStatus $status,
        public readonly ?string $transactionId = null,
        public readonly ?string $paymentId = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $message = null,
        public readonly ?string $redirectUrl = null,
        public readonly array $gatewayResponse = [],
        public readonly ?string $errorCode = null,
    ) {}

    public static function success(
        string $transactionId,
        float $amount,
        string $currency,
        ?string $paymentId = null,
        ?string $redirectUrl = null,
        array $gatewayResponse = [],
    ): self {
        return new self(
            success: true,
            status: PaymentStatus::Paid,
            transactionId: $transactionId,
            paymentId: $paymentId,
            amount: $amount,
            currency: $currency,
            message: 'Payment processed successfully.',
            redirectUrl: $redirectUrl,
            gatewayResponse: $gatewayResponse,
        );
    }

    public static function pending(
        string $transactionId,
        float $amount,
        string $currency,
        ?string $redirectUrl = null,
        ?string $paymentId = null,
        array $gatewayResponse = [],
    ): self {
        return new self(
            success: true,
            status: PaymentStatus::Pending,
            transactionId: $transactionId,
            paymentId: $paymentId,
            amount: $amount,
            currency: $currency,
            message: 'Payment is pending confirmation.',
            redirectUrl: $redirectUrl,
            gatewayResponse: $gatewayResponse,
        );
    }

    public static function failed(
        string $message,
        ?string $errorCode = null,
        ?string $transactionId = null,
        array $gatewayResponse = [],
    ): self {
        return new self(
            success: false,
            status: PaymentStatus::Failed,
            transactionId: $transactionId,
            message: $message,
            errorCode: $errorCode,
            gatewayResponse: $gatewayResponse,
        );
    }

    public function requiresRedirect(): bool
    {
        return $this->redirectUrl !== null && $this->redirectUrl !== '';
    }
}
