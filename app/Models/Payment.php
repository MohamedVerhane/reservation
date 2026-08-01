<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $reservation_id
 * @property float $amount
 * @property string $method
 * @property string $status
 * @property string|null $transaction_id
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read Reservation $reservation
 * @property-read bool $is_completed
 * @property-read bool $is_pending
 * @property-read bool $is_failed
 * @property-read bool $is_refunded
 * @property-read string $status_label
 * @property-read string $status_color
 */
class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_CASH = 'cash';
    public const METHOD_CREDIT_CARD = 'credit_card';
    public const METHOD_DEBIT_CARD = 'debit_card';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_ONLINE = 'online';

    /** @var list<string> */
    protected $fillable = [
        'reservation_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<Reservation, Payment> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // ─── Accessors ───────────────────────────────────────

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'amber',
            self::STATUS_COMPLETED => 'emerald',
            self::STATUS_FAILED => 'red',
            self::STATUS_REFUNDED => 'blue',
            default => 'gray',
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            self::METHOD_CASH => 'Cash',
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_DEBIT_CARD => 'Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_ONLINE => 'Online',
            default => ucfirst(str_replace('_', ' ', $this->method)),
        };
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByMethod(Builder $query, string $method): Builder
    {
        return $query->where('method', $method);
    }

    public function scopeByReservation(Builder $query, int $reservationId): Builder
    {
        return $query->where('reservation_id', $reservationId);
    }

    // ─── Helpers ─────────────────────────────────────────

    public function markAsCompleted(?string $transactionId = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
            'transaction_id' => $transactionId,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    public function markAsRefunded(): void
    {
        $this->update(['status' => self::STATUS_REFUNDED]);
    }
}
