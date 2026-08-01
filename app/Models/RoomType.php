<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $hotel_id
 * @property string $name
 * @property string|null $description
 * @property float $base_price
 * @property int $max_guests
 * @property int $max_children
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read Hotel $hotel
 * @property-read \Illuminate\Database\Eloquent\Collection<Room> $rooms
 * @property-read int $active_rooms_count
 * @property-read string $price_formatted
 */
class RoomType extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'base_price',
        'max_guests',
        'max_children',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'max_guests' => 'integer',
            'max_children' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<Hotel, RoomType> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return HasMany<Room> */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    // ─── Accessors ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getActiveRoomsCountAttribute(): int
    {
        return $this->rooms()->where('is_active', true)->count();
    }

    public function getAvailableRoomsCountAttribute(): int
    {
        return $this->rooms()
            ->where('is_active', true)
            ->where('status', 'available')
            ->count();
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->base_price, 2);
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeByPriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('base_price', [$min, $max]);
    }

    public function scopeMaxPrice(Builder $query, float $price): Builder
    {
        return $query->where('base_price', '<=', $price);
    }

    public function scopeMinGuests(Builder $query, int $guests): Builder
    {
        return $query->where('max_guests', '>=', $guests);
    }

    // ─── Helpers ─────────────────────────────────────────

    public function hasAvailableRoomsForDates(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut): bool
    {
        return $this->rooms()
            ->availableForDates($checkIn, $checkOut)
            ->exists();
    }

    public function calculatePrice(int $nights): float
    {
        return $this->base_price * $nights;
    }
}
