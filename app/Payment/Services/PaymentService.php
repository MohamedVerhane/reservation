<?php

namespace App\Payment\Services;

use App\Models\Payment;
use App\Models\Reservation;
use App\Payment\Contracts\PaymentGatewayInterface;
use App\Payment\Contracts\PaymentServiceInterface;
use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;
use App\Payment\Enums\PaymentProvider;
use App\Payment\Enums\PaymentStatus;
use App\Payment\Exceptions\PaymentException;
use App\Payment\Gateways\StripeGateway;
use App\Payment\Gateways\PayPalGateway;
use App\Payment\Gateways\FlutterwaveGateway;
use App\Payment\Gateways\MollieGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService implements PaymentServiceInterface
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $gateways = [];

    public function __construct(array $config)
    {
        $this->registerGateways($config);
    }

    protected function registerGateways(array $config): void
    {
        $gatewayClasses = [
            PaymentProvider::Stripe      => StripeGateway::class,
            PaymentProvider::PayPal      => PayPalGateway::class,
            PaymentProvider::Flutterwave => FlutterwaveGateway::class,
            PaymentProvider::Mollie      => MollieGateway::class,
        ];

        foreach ($gatewayClasses as $provider => $class) {
            $gatewayConfig = $config['gateways'][$provider->value] ?? [];
            $this->gateways[$provider->value] = new $class($gatewayConfig);
        }
    }

    public function charge(ChargeRequest $request, PaymentProvider $provider): ChargeResponse
    {
        $gateway = $this->getGateway($provider);

        if (! $gateway->isAvailable()) {
            throw PaymentException::gatewayUnavailable($gateway->getDisplayName());
        }

        try {
            $response = $gateway->charge($request);
            $this->recordCharge($request, $response, $provider);

            if ($response->success) {
                Log::info("Payment processed via {$provider->label()}", [
                    'transaction_id' => $response->transactionId,
                    'amount' => $response->amount,
                    'currency' => $response->currency,
                ]);
            } else {
                Log::warning("Payment failed via {$provider->label()}", [
                    'error' => $response->message,
                    'error_code' => $response->errorCode,
                    'amount' => $request->amount,
                ]);
            }

            return $response;

        } catch (PaymentException $e) {
            Log::error("Payment exception via {$provider->label()}: {$e->getMessage()}", [
                'error_code' => $e->getErrorCode(),
                'amount' => $request->amount,
            ]);

            $this->recordFailedCharge($request, $e, $provider);

            throw $e;
        }
    }

    public function refund(RefundRequest $request, PaymentProvider $provider): RefundResponse
    {
        $gateway = $this->getGateway($provider);

        if (! $gateway->isAvailable()) {
            throw PaymentException::gatewayUnavailable($gateway->getDisplayName());
        }

        $payment = Payment::where('transaction_id', $request->transactionId)->first();

        if (! $payment) {
            throw new PaymentException(
                "Payment record not found for transaction: {$request->transactionId}",
                'PAYMENT_NOT_FOUND',
            );
        }

        if (! $payment->is_refunded && ! $payment->is_completed) {
            throw new PaymentException(
                'Only completed payments can be refunded.',
                'INVALID_REFUND_STATE',
            );
        }

        if ($payment->is_refunded) {
            throw new PaymentException(
                'This payment has already been refunded.',
                'ALREADY_REFUNDED',
            );
        }

        try {
            $response = $gateway->refund($request);

            if ($response->success) {
                $this->recordRefund($payment, $response);

                Log::info("Refund processed via {$provider->label()}", [
                    'refund_id' => $response->refundId,
                    'original_transaction' => $request->transactionId,
                    'amount' => $response->amount,
                ]);
            }

            return $response;

        } catch (PaymentException $e) {
            Log::error("Refund exception via {$provider->label()}: {$e->getMessage()}", [
                'transaction_id' => $request->transactionId,
            ]);

            throw $e;
        }
    }

    public function getGateway(PaymentProvider $provider): PaymentGatewayInterface
    {
        $gateway = $this->gateways[$provider->value] ?? null;

        if (! $gateway) {
            throw new PaymentException(
                "Payment provider [{$provider->value}] is not registered.",
                'PROVIDER_NOT_REGISTERED',
            );
        }

        return $gateway;
    }

    public function isProviderAvailable(PaymentProvider $provider): bool
    {
        try {
            return $this->getGateway($provider)->isAvailable();
        } catch (PaymentException) {
            return false;
        }
    }

    public function getAvailableProviders(): array
    {
        return array_filter(
            PaymentProvider::cases(),
            fn (PaymentProvider $provider) => $this->isProviderAvailable($provider),
        );
    }

    public function getGateways(): array
    {
        return $this->gateways;
    }

    // ─── Private Record Helpers ─────────────────────────

    protected function recordCharge(ChargeRequest $request, ChargeResponse $response, PaymentProvider $provider): void
    {
        if ($request->paymentId) {
            $payment = Payment::find($request->paymentId);
            if ($payment) {
                $status = $this->mapStatus($response->status);
                $payment->update([
                    'status' => $status,
                    'transaction_id' => $response->transactionId,
                    'paid_at' => $response->status === PaymentStatus::Paid ? now() : null,
                    'method' => $provider->value,
                ]);
                return;
            }
        }

        if ($request->reservationId) {
            Payment::create([
                'reservation_id' => $request->reservationId,
                'amount' => $request->amount,
                'method' => $provider->value,
                'status' => $this->mapStatus($response->status),
                'transaction_id' => $response->transactionId,
                'paid_at' => $response->status === PaymentStatus::Paid ? now() : null,
            ]);
        }
    }

    protected function recordFailedCharge(ChargeRequest $request, PaymentException $e, PaymentProvider $provider): void
    {
        if ($request->paymentId) {
            $payment = Payment::find($request->paymentId);
            if ($payment) {
                $payment->markAsFailed();
                return;
            }
        }

        if ($request->reservationId) {
            Payment::create([
                'reservation_id' => $request->reservationId,
                'amount' => $request->amount,
                'method' => $provider->value,
                'status' => Payment::STATUS_FAILED,
                'transaction_id' => null,
            ]);
        }
    }

    protected function recordRefund(Payment $payment, RefundResponse $response): void
    {
        $payment->markAsRefunded();
    }

    protected function mapStatus(PaymentStatus $status): string
    {
        return match ($status) {
            PaymentStatus::Pending  => Payment::STATUS_PENDING,
            PaymentStatus::Paid     => Payment::STATUS_COMPLETED,
            PaymentStatus::Failed   => Payment::STATUS_FAILED,
            PaymentStatus::Refunded => Payment::STATUS_REFUNDED,
        };
    }
}
