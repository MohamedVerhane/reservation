<?php

namespace App\Payment\Enums;

enum PaymentProvider: string
{
    case Stripe      = 'stripe';
    case PayPal      = 'paypal';
    case Flutterwave = 'flutterwave';
    case Mollie      = 'mollie';

    public function label(): string
    {
        return match ($this) {
            self::Stripe      => 'Stripe',
            self::PayPal      => 'PayPal',
            self::Flutterwave => 'Flutterwave',
            self::Mollie      => 'Mollie',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Stripe      => 'bi-credit-card',
            self::PayPal      => 'bi-wallet2',
            self::Flutterwave => 'bi-globe',
            self::Mollie      => 'bi-bank',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Stripe      => 'indigo',
            self::PayPal      => 'blue',
            self::Flutterwave => 'purple',
            self::Mollie      => 'orange',
        };
    }
}
