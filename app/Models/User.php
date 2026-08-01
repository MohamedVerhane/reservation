<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $role
 * @property string $password
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<Hotel> $hotels
 * @property-read \Illuminate\Database\Eloquent\Collection<Reservation> $reservations
 * @property-read \Illuminate\Database\Eloquent\Collection<Review> $reviews
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return HasMany<Hotel> */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
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

    /** @return HasMany<Favorite> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /** @return HasMany<Reservation> */
    public function recentReservations(): HasMany
    {
        return $this->reservations()->latest()->limit(5);
    }

    /** @return HasMany<Review> */
    public function recentReviews(): HasMany
    {
        return $this->reviews()->latest()->limit(5);
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopeOwners(Builder $query): Builder
    {
        return $query->where('role', 'owner');
    }

    public function scopeGuests(Builder $query): Builder
    {
        return $query->where('role', 'guest');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    // ─── Helpers ─────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner' || $this->hasRole('owner');
    }

    public function isGuest(): bool
    {
        return $this->role === 'guest' || $this->hasRole('guest');
    }

    public function ownsHotel(Hotel $hotel): bool
    {
        return $this->id === $hotel->user_id;
    }
}
