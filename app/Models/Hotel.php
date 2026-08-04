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
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<RoomType> $roomTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<Room> $rooms
 * @property-read \Illuminate\Database\Eloquent\Collection<Reservation> $reservations
 * @property-read \Illuminate\Database\Eloquent\Collection<Review> $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection<Gallery> $galleries
 * @property-read string $full_address
 * @property-read float $average_rating
 * @property-read int $reviews_count
 * @property-read int|null $galleries_count
 * @property-read string|null $cover_image_url
 * @property-read string $star_rating_label
 * @property-read string $status_label
 * @property-read int|null $reservations_count
 * @property-read int|null $room_types_count
 * @property-read int|null $rooms_count
 * @method static Builder<static>|Hotel active()
 * @method static Builder<static>|Hotel byCity(string $city)
 * @method static Builder<static>|Hotel byCountry(string $country)
 * @method static Builder<static>|Hotel byOwner(int $userId)
 * @method static Builder<static>|Hotel byStarRating(int $rating)
 * @method static \Database\Factories\HotelFactory factory($count = null, $state = [])
 * @method static Builder<static>|Hotel minStarRating(int $rating)
 * @method static Builder<static>|Hotel newModelQuery()
 * @method static Builder<static>|Hotel newQuery()
 * @method static Builder<static>|Hotel onlyTrashed()
 * @method static Builder<static>|Hotel query()
 * @method static Builder<static>|Hotel search(string $term)
 * @method static Builder<static>|Hotel whereAddress($value)
 * @method static Builder<static>|Hotel whereCity($value)
 * @method static Builder<static>|Hotel whereCountry($value)
 * @method static Builder<static>|Hotel whereCoverImage($value)
 * @method static Builder<static>|Hotel whereCreatedAt($value)
 * @method static Builder<static>|Hotel whereDeletedAt($value)
 * @method static Builder<static>|Hotel whereDescription($value)
 * @method static Builder<static>|Hotel whereEmail($value)
 * @method static Builder<static>|Hotel whereId($value)
 * @method static Builder<static>|Hotel whereIsActive($value)
 * @method static Builder<static>|Hotel whereLatitude($value)
 * @method static Builder<static>|Hotel whereLongitude($value)
 * @method static Builder<static>|Hotel whereName($value)
 * @method static Builder<static>|Hotel wherePhone($value)
 * @method static Builder<static>|Hotel whereSlug($value)
 * @method static Builder<static>|Hotel whereStarRating($value)
 * @method static Builder<static>|Hotel whereUpdatedAt($value)
 * @method static Builder<static>|Hotel whereUserId($value)
 * @method static Builder<static>|Hotel withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Hotel withoutTrashed()
 * @mixin \Eloquent
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
        if (!$this->cover_image) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($this->cover_image);
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
        $term = '%' . $term . '%';

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'LIKE', $term)
              ->orWhere('city', 'LIKE', $term)
              ->orWhere('country', 'LIKE', $term)
              ->orWhere('address', 'LIKE', $term);
        });
    }

    // ─── Helpers ─────────────────────────────────────────

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isAvailableForDates(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut, ?int $excludeReservationId = null): bool
    {
        return $this->rooms()
            ->available()
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut, $excludeReservationId): void {
                $q->whereNotIn('status', [Reservation::STATUS_CANCELLED])
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn)
                  ->when($excludeReservationId, fn ($q2) => $q2->where('id', '!=', $excludeReservationId));
            })
            ->exists();
    }

    public function getAvailableRoomsCount(\Carbon\Carbon $checkIn, \Carbon\Carbon $checkOut): int
    {
        return $this->rooms()
            ->availableForDates($checkIn, $checkOut)
            ->count();
    }
}
