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
 * @property string $title
 * @property string|null $description
 * @property int $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Hotel $hotel
 * @property-read \Illuminate\Database\Eloquent\Collection<Image> $images
 * @property-read int $images_count
 * @property-read \App\Models\Image|null $cover_image
 * @method static Builder<static>|Gallery byHotel(int $hotelId)
 * @method static \Database\Factories\GalleryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Gallery newModelQuery()
 * @method static Builder<static>|Gallery newQuery()
 * @method static Builder<static>|Gallery ordered()
 * @method static Builder<static>|Gallery query()
 * @method static Builder<static>|Gallery whereCreatedAt($value)
 * @method static Builder<static>|Gallery whereDescription($value)
 * @method static Builder<static>|Gallery whereHotelId($value)
 * @method static Builder<static>|Gallery whereId($value)
 * @method static Builder<static>|Gallery whereSortOrder($value)
 * @method static Builder<static>|Gallery whereTitle($value)
 * @method static Builder<static>|Gallery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Gallery extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'hotel_id',
        'title',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<Hotel, Gallery> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return HasMany<Image> */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('sort_order');
    }

    // ─── Accessors ───────────────────────────────────────

    public function getCoverImageAttribute(): ?Image
    {
        return $this->images()->first();
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeByHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ─── Helpers ─────────────────────────────────────────

    public function isOwnedByHotel(Hotel $hotel): bool
    {
        return $this->hotel_id === $hotel->id;
    }
}
