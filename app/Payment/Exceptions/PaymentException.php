<?php

namespace App\Payment\Exceptions;

use RuntimeException;

class PaymentException extends RuntimeException
{
    protected string $errorCode;
    protected array $gatewayResponse;

    public function __construct(
        string $message,
        string $errorCode = 'PAYMENT_ERROR',
        array $gatewayResponse = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
        $this->errorCode = $errorCode;
        $this->gatewayResponse = $gatewayResponse;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getGatewayResponse(): array
    {
        return $this->gatewayResponse;
    }

    public static function insufficientFunds(float $amount, string $currency): static
    {
        return new static(
            "Insufficient funds for charge of {$currency} " . number_format($amount, 2),
            'INSUFFICIENT_FUNDS',
        );
    }

    public static function cardDeclined(?string $reason = null): static
    {
        return new static(
            "Card was declined" . ($reason ? ": {$reason}" : ""),
            'CARD_DECLINED',
        );
    }

    public static function invalidPaymentMethod(): static
    {
        return new static(
            'The specified payment method is invalid or not supported.',
            'INVALID_PAYMENT_METHOD',
        );
    }

    public static function gatewayUnavailable(string $provider): static
    {
        return new static(
            "{$provider} gateway is currently unavailable. Please try again later.",
            'GATEWAY_UNAVAILABLE',
        );
    }

    public static function refundFailed(?string $reason = null): static
    {
        return new static(
            "Refund failed" . ($reason ? ": {$reason}" : ""),
            'REFUND_FAILED',
        );
    }
}
