<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $hotel_id
 * @property int $room_id
 * @property \Carbon\Carbon $check_in
 * @property \Carbon\Carbon $check_out
 * @property int $guests
 * @property int $children_count
 * @property float $total_price
 * @property string $status
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read User $user
 * @property-read Hotel $hotel
 * @property-read Room $room
 * @property-read \Illuminate\Database\Eloquent\Collection<Payment> $payments
 * @property-read int $nights
 * @property-read string $status_label
 * @property-read string $status_color
 * @property-read bool $is_upcoming
 * @property-read bool $is_past
 * @property-read bool $is_active
 * @property-read float $balance
 * @property-read float $total_paid
 * @property-read int|null $payments_count
 * @method static Builder<static>|Reservation active()
 * @method static Builder<static>|Reservation byHotel(int $hotelId)
 * @method static Builder<static>|Reservation byStatus(string $status)
 * @method static Builder<static>|Reservation byUser(int $userId)
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Reservation forDateRange(\Carbon\Carbon $start, \Carbon\Carbon $end)
 * @method static Builder<static>|Reservation newModelQuery()
 * @method static Builder<static>|Reservation newQuery()
 * @method static Builder<static>|Reservation onlyTrashed()
 * @method static Builder<static>|Reservation past()
 * @method static Builder<static>|Reservation query()
 * @method static Builder<static>|Reservation upcoming()
 * @method static Builder<static>|Reservation whereCheckIn($value)
 * @method static Builder<static>|Reservation whereCheckOut($value)
 * @method static Builder<static>|Reservation whereChildrenCount($value)
 * @method static Builder<static>|Reservation whereCreatedAt($value)
 * @method static Builder<static>|Reservation whereDeletedAt($value)
 * @method static Builder<static>|Reservation whereGuests($value)
 * @method static Builder<static>|Reservation whereHotelId($value)
 * @method static Builder<static>|Reservation whereId($value)
 * @method static Builder<static>|Reservation whereNotes($value)
 * @method static Builder<static>|Reservation whereRoomId($value)
 * @method static Builder<static>|Reservation whereStatus($value)
 * @method static Builder<static>|Reservation whereTotalPrice($value)
 * @method static Builder<static>|Reservation whereUpdatedAt($value)
 * @method static Builder<static>|Reservation whereUserId($value)
 * @method static Builder<static>|Reservation withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Reservation withoutTrashed()
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_CANCELLED = 'cancelled';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CHECKED_IN,
        self::STATUS_CHECKED_OUT,
        self::STATUS_CANCELLED,
    ];

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'children_count',
        'total_price',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'guests' => 'integer',
            'children_count' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<User, Reservation> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Hotel, Reservation> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<Room, Reservation> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return HasMany<Payment> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Accessors ───────────────────────────────────────

    public function getNightsAttribute(): int
    {
        return (int) $this->check_in->diffInDays($this->check_out);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CHECKED_IN => 'Checked In',
            self::STATUS_CHECKED_OUT => 'Checked Out',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'amber',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_CHECKED_IN => 'emerald',
            self::STATUS_CHECKED_OUT => 'gray',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->check_in->isFuture() && in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function getIsPastAttribute(): bool
    {
        return $this->check_out->isPast() || $this->status === self::STATUS_CHECKED_OUT;
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            && $this->check_in->isPast()
            && $this->check_out->isFuture();
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_price - $this->total_paid;
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('check_in', '>=', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->where('check_out', '<', now())
              ->orWhere('status', self::STATUS_CHECKED_OUT);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            ->where('check_in', '<=', now())
            ->where('check_out', '>=', now());
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeForDateRange(Builder $query, \Carbon\Carbon $start, \Carbon\Carbon $end): Builder
    {
        return $query->where('check_in', '<', $end)
            ->where('check_out', '>', $start)
            ->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    // ─── Helpers ─────────────────────────────────────────

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            && $this->check_in->isFuture();
    }

    public function canBeCheckedIn(): bool
    {
        return $this->status === self::STATUS_CONFIRMED
            && $this->check_in->lte(now());
    }

    public function canBeCheckedOut(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    public function confirm(): bool
    {
        return $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    public function checkIn(): void
    {
        $this->update(['status' => self::STATUS_CHECKED_IN]);
        $this->room->update(['status' => Room::STATUS_OCCUPIED]);
    }

    public function checkOut(): void
    {
        $this->update(['status' => self::STATUS_CHECKED_OUT]);
        $this->room->update(['status' => Room::STATUS_AVAILABLE]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);

        if ($this->room->status === Room::STATUS_OCCUPIED) {
            $this->room->update(['status' => Room::STATUS_AVAILABLE]);
        }
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
