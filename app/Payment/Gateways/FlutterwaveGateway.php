<?php

namespace App\Payment\Gateways;

use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;

class FlutterwaveGateway extends AbstractGateway
{
    protected function defaults(): array
    {
        return [
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY', ''),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY', ''),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY', ''),
            'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET', ''),
            'currency' => 'USD',
        ];
    }

    public function getProviderName(): string
    {
        return 'flutterwave';
    }

    public function getDisplayName(): string
    {
        return 'Flutterwave';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->config['public_key']) && ! empty($this->config['secret_key']);
    }

    public function getSupportedCurrencies(): array
    {
        return ['USD', 'EUR', 'GBP', 'NGN', 'KES', 'GHS', 'ZAR', 'TZS', 'UGX', 'RWF'];
    }

    public function charge(ChargeRequest $request): ChargeResponse
    {
        $this->requireConfig('public_key', 'secret_key');
        $this->validateAmount($request->amount);

        // Flutterwave supports direct charges and redirect flows
        $transactionId = $this->generateTransactionId();

        // TODO: Implement actual Flutterwave API call
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $this->config['secret_key'],
        //     'Content-Type' => 'application/json',
        // ])->post('https://api.flutterwave.com/v3/payments', [...]);

        // Test/skeleton response
        if ($request->paymentMethod === 'fail_payment') {
            return ChargeResponse::failed(
                message: 'Flutterwave payment failed.',
                errorCode: 'FLW_PAYMENT_FAILED',
                transactionId: $transactionId,
                gatewayResponse: ['test_mode' => true],
            );
        }

        if ($request->returnUrl) {
            return ChargeResponse::pending(
                transactionId: $transactionId,
                amount: $request->amount,
                currency: $request->currency,
                redirectUrl: "https://checkout.flutterwave.com/" . uniqid(),
                gatewayResponse: [
                    'test_mode' => true,
                    'flw_ref' => 'FLW-' . uniqid(),
                    'status' => 'pending',
                ],
            );
        }

        return ChargeResponse::success(
            transactionId: $transactionId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: [
                'test_mode' => true,
                'flw_ref' => 'FLW-' . uniqid(),
                'status' => 'successful',
            ],
        );
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        $this->requireConfig('secret_key');

        $refundId = 'FLWRF-' . uniqid();
        return RefundResponse::success(
            refundId: $refundId,
            amount: $request->amount,
            currency: $request->currency,
            gatewayResponse: [
                'test_mode' => true,
                'flw_ref' => $refundId,
            ],
        );
    }

    public function verifyWebhook(array $payload, array $headers): bool
    {
        $signature = $headers['verif-hash'] ?? '';
        $secret = $this->config['webhook_secret'] ?? '';

        if (empty($signature) || empty($secret)) {
            return false;
        }

        // TODO: Implement actual Flutterwave webhook verification
        // $hash = hash_hmac('sha256', json_encode($payload), $secret);
        // return hash_equals($hash, $signature);

        return true;
    }
}
