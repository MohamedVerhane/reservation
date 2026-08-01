<?php

namespace App\Payment\Gateways;

use App\Payment\Contracts\PaymentGatewayInterface;
use App\Payment\DTOs\ChargeRequest;
use App\Payment\DTOs\ChargeResponse;
use App\Payment\DTOs\RefundRequest;
use App\Payment\DTOs\RefundResponse;
use App\Payment\Exceptions\PaymentException;

abstract class AbstractGateway implements PaymentGatewayInterface
{
    protected array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->defaults(), $config);
    }

    abstract protected function defaults(): array;

    protected function getConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    protected function requireConfig(string ...$keys): void
    {
        foreach ($keys as $key) {
            if (empty($this->config[$key])) {
                throw PaymentException::gatewayUnavailable($this->getDisplayName());
            }
        }
    }

    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new PaymentException('Charge amount must be greater than zero.', 'INVALID_AMOUNT');
        }
    }

    protected function generateTransactionId(): string
    {
        return strtoupper($this->getProviderName() . '_' . uniqid() . '_' . bin2hex(random_bytes(8)));
    }

    public function getSupportedCurrencies(): array
    {
        return ['USD', 'EUR', 'GBP', 'AED', 'SAR'];
    }

    public function getMaxAmount(string $currency = 'USD'): float
    {
        return 999999.99;
    }
}
