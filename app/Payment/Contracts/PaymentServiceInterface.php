<?php

namespace App\Payment\Contracts;

use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;
use App\Payment\Enums\PaymentProvider;

interface PaymentServiceInterface
{
    /**
     * Process a charge through the specified gateway.
     * Creates/updates the Payment model record.
     */
    public function charge(ChargeRequest $request, PaymentProvider $provider): ChargeResponse;

    /**
     * Process a refund through the specified gateway.
     * Updates the Payment model record.
     */
    public function refund(RefundRequest $request, PaymentProvider $provider): RefundResponse;

    /**
     * Get a gateway instance by provider.
     */
    public function getGateway(PaymentProvider $provider): PaymentGatewayInterface;

    /**
     * Check if a specific provider is available and configured.
     */
    public function isProviderAvailable(PaymentProvider $provider): bool;

    /**
     * Get all available providers.
     *
     * @return list<PaymentProvider>
     */
    public function getAvailableProviders(): array;

    /**
     * Get all registered gateways.
     *
     * @return array<string, PaymentGatewayInterface>
     */
    public function getGateways(): array;
}
