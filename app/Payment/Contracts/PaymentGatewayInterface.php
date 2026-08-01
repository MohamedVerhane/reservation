<?php

namespace App\Payment\Contracts;

use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;

interface PaymentGatewayInterface
{
    /**
     * Get the unique provider identifier (e.g., 'stripe', 'paypal').
     */
    public function getProviderName(): string;

    /**
     * Get the human-readable provider name (e.g., 'Stripe', 'PayPal').
     */
    public function getDisplayName(): string;

    /**
     * Charge a payment using the gateway.
     *
     * @throws \App\Payment\Exceptions\PaymentException
     */
    public function charge(ChargeRequest $request): ChargeResponse;

    /**
     * Refund a previous charge.
     *
     * @throws \App\Payment\Exceptions\PaymentException
     */
    public function refund(RefundRequest $request): RefundResponse;

    /**
     * Check if the gateway is properly configured and available.
     */
    public function isAvailable(): bool;

    /**
     * Get supported currencies (ISO 4217).
     *
     * @return list<string>
     */
    public function getSupportedCurrencies(): array;

    /**
     * Get the maximum chargeable amount in the smallest currency unit.
     */
    public function getMaxAmount(string $currency = 'USD'): float;

    /**
     * Verify a webhook signature/payload from the gateway.
     */
    public function verifyWebhook(array $payload, array $headers): bool;
}
