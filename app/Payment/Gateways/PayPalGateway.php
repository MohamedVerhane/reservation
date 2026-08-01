<?php

namespace App\Payment\Gateways;

use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;
use App\Payment\Exceptions\PaymentException;

class PayPalGateway extends AbstractGateway
{
    protected function defaults(): array
    {
        return [
            'client_id' => env('PAYPAL_CLIENT_ID', ''),
            'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID', ''),
            'mode' => 'sandbox', // sandbox | live
            'currency' => 'USD',
        ];
    }

    public function getProviderName(): string
    {
        return 'paypal';
    }

    public function getDisplayName(): string
    {
        return 'PayPal';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->config['client_id']) && ! empty($this->config['client_secret']);
    }

    public function getSupportedCurrencies(): array
    {
        return ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY'];
    }

    public function charge(ChargeRequest $request): ChargeResponse
    {
        $this->requireConfig('client_id', 'client_secret');
        $this->validateAmount($request->amount);

        if ($this->config['mode'] === 'sandbox') {
            return $this->handleTestCharge($request);
        }

        // TODO: Implement actual PayPal API call
        // Create PayPal order, then capture it

        throw PaymentException::gatewayUnavailable('PayPal');
    }

    private function handleTestCharge(ChargeRequest $request): ChargeResponse
    {
        $transactionId = $this->generateTransactionId();

        if ($request->paymentMethod === 'fail_payment') {
            return ChargeResponse::failed(
                message: 'PayPal payment was declined.',
                errorCode: 'PAYMENT_DECLINED',
                transactionId: $transactionId,
                gatewayResponse: ['test_mode' => true],
            );
        }

        // PayPal typically requires redirect flow
        if ($request->returnUrl) {
            $approvalUrl = $this->config['mode'] === 'sandbox'
                ? "https://www.sandbox.paypal.com/checkoutnow?token=EC-TEST-" . uniqid()
                : "https://www.paypal.com/checkoutnow?token=EC-" . uniqid();

            return ChargeResponse::pending(
                transactionId: $transactionId,
                amount: $request->amount,
                currency: $request->currency,
                redirectUrl: $approvalUrl,
                gatewayResponse: [
                    'test_mode' => true,
                    'order_id' => 'PAY-' . uniqid(),
                    'status' => 'CREATED',
                ],
            );
        }

        return ChargeResponse::success(
            transactionId: $transactionId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: [
                'test_mode' => true,
                'order_id' => 'PAY-' . uniqid(),
                'status' => 'COMPLETED',
            ],
        );
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        $this->requireConfig('client_id', 'client_secret');

        $refundId = 'REFUND-' . uniqid();
        return RefundResponse::success(
            refundId: $refundId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: ['test_mode' => true, 'refund_id' => $refundId],
        );
    }

    public function verifyWebhook(array $payload, array $headers): bool
    {
        $webhookId = $this->config['webhook_id'] ?? '';

        if (empty($webhookId)) {
            return false;
        }

        // TODO: Implement actual PayPal webhook verification
        // $verified = PayPalWebhook::verify($payload, $headers, $webhookId);

        return true;
    }
}
