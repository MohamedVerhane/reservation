<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $address
 * @property string $city
 * @property string $country
 * @property string $phone
 * @property string $email
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int $star_rating
 * @property bool $is_active
 * @property string|null $cover_image
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<RoomType> $roomTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<Room> $rooms
 * @property-read \Illuminate\Database\Eloquent\Collection<Reservation> $reservations
 * @property-read \Illuminate\Database\Eloquent\Collection<Review> $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection<Gallery> $galleries
 * @property-read string $full_address
 * @property-read float $average_rating
 * @property-read int $reviews_count
 */
class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'latitude',
        'longitude',
        'star_rating',
        'is_active',
        'cover_image',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'star_rating' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Hotel $hotel): void {
            if (empty($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<User, Hotel> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<RoomType> */
    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    /** @return HasMany<Room> */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /** @return HasMany<Reservation> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<Review> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<Gallery> */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    // ─── Accessors ───────────────────────────────────────

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->country}";
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->approved()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->approved()->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getStarRatingLabelAttribute(): string
    {
        $labels = [
            1 => 'Economy',
            2 => 'Budget',
            3 => 'Standard',
            4 => 'Superior',
            5 => 'Luxury',
        ];

        return $labels[$this->star_rating] ?? 'Standard';
    }

    // getCoverImageAttribute removed — Eloquent already provides this attribute natively.

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    public function scopeByCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', $country);
    }

    public function scopeByStarRating(Builder $query, int $rating): Builder
    {
        return $query->where('star_rating', $rating);
    }

    public function scopeMinStarRating(Builder $query, int $rating): Builder
    {
        return $query->where('star_rating', '>=', $rating);
    }

    public function scopeByOwner(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $safeTerm = '%' . addslashes($term) . '%';

        return $query->where(function (Builder $q) use ($safeTerm): void {
            $q->where('name', 'LIKE', $safeTerm)
              ->orWhere('city', 'LIKE', $safeTerm)
              ->orWhere('country', 'LIKE', $safeTerm)
              ->orWhere('address', 'LIKE', $safeTerm);
        });
    }

    // ─── Helpers ─────────────────────────────────────────

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isAvailableForDates(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut, ?int $excludeReservationId = null): bool
    {
        $query = $this->rooms()
            ->availableForDates($checkIn, $checkOut);

        if ($excludeReservationId) {
            $query->whereDoesntHave('reservations', function ($q) use ($excludeReservationId): void {
                $q->where('id', '!=', $excludeReservationId);
            });
        }

        return $query->exists();
    }

    public function getAvailableRoomsCount(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut): int
    {
        return $this->rooms()
            ->availableForDates($checkIn, $checkOut)
            ->count();
    }
}
