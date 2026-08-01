<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<Room> $rooms
 * @property-read int $rooms_count
 * @property-read string $status_label
 * @property-read string $status_color
 */
class Amenity extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /** @return BelongsToMany<Room> */
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_amenities')
            ->withTimestamps();
    }

    // ─── Accessors ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'emerald' : 'slate';
    }

    public function getIconHtmlAttribute(): string
    {
        return $this->icon ? '<i class="' . e($this->icon) . '"></i>' : '';
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'LIKE', '%' . addslashes($term) . '%');
    }

    // ─── Helpers ─────────────────────────────────────────

    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }
}
