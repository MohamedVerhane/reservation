<?php

namespace App\Providers;

use App\Payment\Contracts\PaymentServiceInterface;
use App\Payment\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentServiceInterface::class, function ($app) {
            $config = config('payment', []);

            return new PaymentService($config);
        });

        $this->app->alias(PaymentServiceInterface::class, PaymentService::class);
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(
            $this->configPath(),
            'payment'
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => config_path('payment.php'),
            ], 'payment-config');
        }
    }

    protected function configPath(): string
    {
        return __DIR__ . '/../../config/payment.php';
    }
}
