<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $hotel_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read User $user
 * @property-read Hotel $hotel
 * @method static Builder<static>|Favorite byHotel(int $hotelId)
 * @method static Builder<static>|Favorite byUser(int $userId)
 * @method static \Database\Factories\FavoriteFactory factory($count = null, $state = [])
 * @method static Builder<static>|Favorite newModelQuery()
 * @method static Builder<static>|Favorite newQuery()
 * @method static Builder<static>|Favorite query()
 * @method static Builder<static>|Favorite whereCreatedAt($value)
 * @method static Builder<static>|Favorite whereHotelId($value)
 * @method static Builder<static>|Favorite whereId($value)
 * @method static Builder<static>|Favorite whereUpdatedAt($value)
 * @method static Builder<static>|Favorite whereUserId($value)
 * @mixin \Eloquent
 */
class Favorite extends Model
{
    /** @use HasFactory<\Database\Factories\FavoriteFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'hotel_id',
    ];

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<User, Favorite> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Hotel, Favorite> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    // ─── Helpers ─────────────────────────────────────────

    public static function isFavorited(int $userId, int $hotelId): bool
    {
        return static::query()->where('user_id', $userId)->where('hotel_id', $hotelId)->exists();
    }

    public static function toggle(int $userId, int $hotelId): bool
    {
        $deleted = static::query()
            ->where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->delete();

        if ($deleted > 0) {
            return false;
        }

        static::query()->create(['user_id' => $userId, 'hotel_id' => $hotelId]);

        return true;
    }
}
