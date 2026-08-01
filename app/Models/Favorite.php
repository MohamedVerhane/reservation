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
 *
 * @property-read User $user
 * @property-read Hotel $hotel
 */
class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
    ];

    // ─── Relationships ───────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
        return static::where('user_id', $userId)->where('hotel_id', $hotelId)->exists();
    }

    public static function toggle(int $userId, int $hotelId): bool
    {
        $existing = static::where('user_id', $userId)->where('hotel_id', $hotelId)->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        static::create(['user_id' => $userId, 'hotel_id' => $hotelId]);
        return true;
    }
}
