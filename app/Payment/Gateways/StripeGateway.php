<?php

namespace App\Payment\Gateways;

use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;
use App\Payment\Exceptions\PaymentException;

class StripeGateway extends AbstractGateway
{
    protected function defaults(): array
    {
        return [
            'api_key' => env('STRIPE_SECRET_KEY', ''),
            'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
            'currency' => 'USD',
            'test_mode' => true,
        ];
    }

    public function getProviderName(): string
    {
        return 'stripe';
    }

    public function getDisplayName(): string
    {
        return 'Stripe';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->config['api_key']) && ! empty($this->config['publishable_key']);
    }

    public function charge(ChargeRequest $request): ChargeResponse
    {
        $this->requireConfig('api_key');
        $this->validateAmount($request->amount);

        if ($this->getConfig('test_mode')) {
            return $this->handleTestCharge($request);
        }

        // TODO: Implement actual Stripe API call
        // $stripe = new \Stripe\StripeClient($this->config['api_key']);
        // $paymentIntent = $stripe->paymentIntents->create([...]);

        throw PaymentException::gatewayUnavailable('Stripe');
    }

    private function handleTestCharge(ChargeRequest $request): ChargeResponse
    {
        $transactionId = $this->generateTransactionId();

        if ($request->paymentMethod === 'fail_card') {
            return ChargeResponse::failed(
                message: 'Your card was declined.',
                errorCode: 'card_declined',
                transactionId: $transactionId,
                gatewayResponse: ['test_mode' => true, 'decline_code' => 'insufficient_funds'],
            );
        }

        return ChargeResponse::success(
            transactionId: $transactionId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: [
                'test_mode' => true,
                'payment_intent' => 'pi_test_' . uniqid(),
                'client_secret' => 'pi_test_' . uniqid() . '_secret_' . bin2hex(random_bytes(16)),
            ],
        );
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        $this->requireConfig('api_key');

        if ($this->getConfig('test_mode')) {
            $refundId = 're_test_' . uniqid();
            return RefundResponse::success(
                refundId: $refundId,
                amount: $request->amount,
                currency: $request->currency,
                gatewayResponse: ['test_mode' => true, 'refund_id' => $refundId],
            );
        }

        // TODO: Implement actual Stripe refund API call
        throw PaymentException::gatewayUnavailable('Stripe');
    }

    public function verifyWebhook(array $payload, array $headers): bool
    {
        $signature = $headers['stripe-signature'] ?? '';

        if (empty($signature) || empty($this->config['webhook_secret'])) {
            return false;
        }

        // TODO: Implement actual Stripe webhook signature verification
        // $event = \Stripe\Webhook::constructEvent(
        //     json_encode($payload),
        //     $signature,
        //     $this->config['webhook_secret']
        // );

        return true;
    }
}
