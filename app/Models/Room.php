<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $room_type_id
 * @property string $room_number
 * @property int|null $floor
 * @property string $status
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Hotel $hotel
 * @property-read RoomType $roomType
 * @property-read \Illuminate\Database\Eloquent\Collection<Amenity> $amenities
 * @property-read \Illuminate\Database\Eloquent\Collection<RoomImage> $images
 * @property-read \Illuminate\Database\Eloquent\Collection<Reservation> $reservations
 * @property-read string $status_label
 * @property-read string $status_color
 * @property-read int|null $amenities_count
 * @property-read string $display_name
 * @property-read int|null $images_count
 * @property-read int|null $reservations_count
 * @method static Builder<static>|Room active()
 * @method static Builder<static>|Room available()
 * @method static Builder<static>|Room availableForDates(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut)
 * @method static Builder<static>|Room byFloor(int $floor)
 * @method static Builder<static>|Room byHotel(int $hotelId)
 * @method static Builder<static>|Room byStatus(string $status)
 * @method static Builder<static>|Room byType(int $roomTypeId)
 * @method static \Database\Factories\RoomFactory factory($count = null, $state = [])
 * @method static Builder<static>|Room newModelQuery()
 * @method static Builder<static>|Room newQuery()
 * @method static Builder<static>|Room query()
 * @method static Builder<static>|Room whereCreatedAt($value)
 * @method static Builder<static>|Room whereFloor($value)
 * @method static Builder<static>|Room whereHotelId($value)
 * @method static Builder<static>|Room whereId($value)
 * @method static Builder<static>|Room whereIsActive($value)
 * @method static Builder<static>|Room whereRoomNumber($value)
 * @method static Builder<static>|Room whereRoomTypeId($value)
 * @method static Builder<static>|Room whereStatus($value)
 * @method static Builder<static>|Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_OUT_OF_ORDER = 'out_of_order';

    /** @var list<string> */
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<Hotel, Room> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<RoomType, Room> */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /** @return BelongsToMany<Amenity> */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities')
            ->withTimestamps();
    }

    /** @return HasMany<Reservation> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<RoomImage> */
    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->ordered();
    }

    // ─── Accessors ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_OUT_OF_ORDER => 'Out of Order',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'emerald',
            self::STATUS_OCCUPIED => 'red',
            self::STATUS_MAINTENANCE => 'amber',
            self::STATUS_OUT_OF_ORDER => 'gray',
            default => 'gray',
        };
    }

    public function getDisplayNameAttribute(): string
    {
        $type = $this->roomType?->name ?? 'Room';

        return "{$type} — {$this->room_number}";
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeByHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeByType(Builder $query, int $roomTypeId): Builder
    {
        return $query->where('room_type_id', $roomTypeId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByFloor(Builder $query, int $floor): Builder
    {
        return $query->where('floor', $floor);
    }

    public function scopeAvailableForDates(Builder $query, \Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut): Builder
    {
        return $query->available()
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut): void {
                $q->whereNotIn('status', ['cancelled'])
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            });
    }

    // ─── Helpers ─────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->is_active && $this->status === self::STATUS_AVAILABLE;
    }

    public function isAvailableForDates(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut, ?int $excludeReservationId = null): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $hasConflict = $this->reservations()
            ->whereNotIn('status', ['cancelled'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->when($excludeReservationId, fn ($q) => $q->where('id', '!=', $excludeReservationId))
            ->exists();

        return !$hasConflict;
    }

    public function calculateNights(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut): int
    {
        return (int) $checkIn->diffInDays($checkOut);
    }

    public function calculateTotalPrice(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut): float
    {
        $nights = $this->calculateNights($checkIn, $checkOut);

        return $this->roomType->calculatePrice($nights);
    }
}
