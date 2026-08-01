<?php

namespace App\Payment\Gateways;

use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;
use App\Payment\Exceptions\PaymentException;

class MollieGateway extends AbstractGateway
{
    protected function defaults(): array
    {
        return [
            'api_key' => env('MOLLIE_API_KEY', ''),
            'profile_id' => env('MOLLIE_PROFILE_ID', ''),
            'webhook_url' => env('MOLLIE_WEBHOOK_URL', ''),
            'test_mode' => true,
            'currency' => 'EUR',
        ];
    }

    public function getProviderName(): string
    {
        return 'mollie';
    }

    public function getDisplayName(): string
    {
        return 'Mollie';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->config['api_key']);
    }

    public function getSupportedCurrencies(): array
    {
        return ['EUR', 'USD', 'GBP', 'CHF', 'PLN', 'SEK', 'DKK', 'NOK', 'CZK', 'HUF'];
    }

    public function charge(ChargeRequest $request): ChargeResponse
    {
        $this->requireConfig('api_key');
        $this->validateAmount($request->amount);

        // Mollie always creates a payment and redirects to checkout
        $transactionId = $this->generateTransactionId();

        // TODO: Implement actual Mollie API call
        // $mollie = new \Mollie\Api\MollieApiClient();
        // $mollie->setApiKey($this->config['api_key']);
        // $payment = $mollie->payments->create([...]);

        if ($request->paymentMethod === 'fail_payment') {
            return ChargeResponse::failed(
                message: 'Mollie payment creation failed.',
                errorCode: 'MOLLIE_CREATION_FAILED',
                transactionId: $transactionId,
                gatewayResponse: ['test_mode' => true],
            );
        }

        $mollieId = 'tr_' . uniqid();

        if ($request->returnUrl) {
            return ChargeResponse::pending(
                transactionId: $transactionId,
                amount: $request->amount,
                currency: $request->currency,
                redirectUrl: $request->returnUrl . '?mollie_id=' . $mollieId,
                gatewayResponse: [
                    'test_mode' => true,
                    'mollie_id' => $mollieId,
                    'status' => 'open',
                    'checkout_url' => "https://www.mollie.com/checkout/qr/" . uniqid(),
                ],
            );
        }

        return ChargeResponse::success(
            transactionId: $transactionId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: [
                'test_mode' => true,
                'mollie_id' => $mollieId,
                'status' => 'paid',
            ],
        );
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        $this->requireConfig('api_key');

        // TODO: Implement actual Mollie refund API
        // $mollie = new \Mollie\Api\MollieApiClient();
        // $mollie->setApiKey($this->config['api_key']);
        // $payment = $mollie->payments->get($request->transactionId);
        // $refund = $payment->refund([...]);

        $refundId = 'rfnd_' . uniqid();
        return RefundResponse::success(
            refundId: $refundId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: [
                'test_mode' => true,
                'mollie_refund_id' => $refundId,
            ],
        );
    }

    public function verifyWebhook(array $payload, array $headers): bool
    {
        // Mollie sends webhook ID in payload
        $id = $payload['id'] ?? '';

        if (empty($id)) {
            return false;
        }

        // TODO: Implement actual Mollie webhook verification
        // $mollie = new \Mollie\Api\MollieApiClient();
        // $mollie->setApiKey($this->config['api_key']);
        // $payment = $mollie->payments->get($id);

        return true;
    }
}
