<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $gallery_id
 * @property string $path
 * @property string|null $alt_text
 * @property string|null $caption
 * @property int $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read Gallery $gallery
 * @property-read string $url
 * @property-read string $full_url
 */
class Image extends Model
{
    use HasFactory, HasMedia;

    /** @var list<string> */
    protected $fillable = [
        'gallery_id',
        'path',
        'alt_text',
        'caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<Gallery, Image> */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getFullUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    // ─── Scopes ──────────────────────────────────────────

    public function scopeByGallery(Builder $query, int $galleryId): Builder
    {
        return $query->where('gallery_id', $galleryId);
    }
}
