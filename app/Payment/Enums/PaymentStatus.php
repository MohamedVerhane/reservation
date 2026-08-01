<?php

namespace App\Payment\Enums;

enum PaymentStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Failed    = 'failed';
    case Refunded  = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Pending',
            self::Paid     => 'Paid',
            self::Failed   => 'Failed',
            self::Refunded => 'Refunded',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'amber',
            self::Paid     => 'emerald',
            self::Failed   => 'red',
            self::Refunded => 'blue',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending  => 'bi-clock-fill',
            self::Paid     => 'bi-check-circle-fill',
            self::Failed   => 'bi-x-circle-fill',
            self::Refunded => 'bi-arrow-counterclockwise',
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Paid, self::Failed, self::Refunded => true,
            self::Pending => false,
        };
    }

    public function canBeRefunded(): bool
    {
        return $this === self::Paid;
    }

    public function canBeRetried(): bool
    {
        return $this === self::Failed;
    }
}
