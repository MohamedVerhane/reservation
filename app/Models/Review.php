<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $hotel_id
 * @property int|null $reservation_id
 * @property int $rating
 * @property string|null $comment
 * @property string|null $reply
 * @property \Carbon\Carbon|null $replied_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read User $user
 * @property-read Hotel $hotel
 * @property-read Reservation|null $reservation
 * @property-read bool $has_reply
 * @property-read string $star_display
 */
class Review extends Model
{
    use HasFactory, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'hotel_id',
        'reservation_id',
        'rating',
        'comment',
        'reply',
        'replied_at',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'replied_at'  => 'datetime',
            'is_approved' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsTo<User, Review> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Hotel, Review> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<Reservation, Review> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // ─── Accessors ───────────────────────────────────────

    public function getHasReplyAttribute(): bool
    {
        return $this->reply !== null;
    }

    public function getStarDisplayAttribute(): string
    {
        $filled = str_repeat('★', $this->rating);
        $empty = str_repeat('☆', 5 - $this->rating);

        return $filled . $empty;
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeByRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    public function scopeMinRating(Builder $query, int $rating): Builder
    {
        return $query->where('rating', '>=', $rating);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithReplies(Builder $query): Builder
    {
        return $query->whereNotNull('reply');
    }

    public function scopeWithoutReplies(Builder $query): Builder
    {
        return $query->whereNull('reply');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false);
    }

    // ─── Helpers ─────────────────────────────────────────

    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }

    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    public function isPending(): bool
    {
        return ! $this->is_approved;
    }

    public function addReply(string $reply): void
    {
        $this->update([
            'reply' => $reply,
            'replied_at' => now(),
        ]);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeRepliedBy(User $user): bool
    {
        return $user->isAdmin() || $user->ownsHotel($this->hotel);
    }
}
