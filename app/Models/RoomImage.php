<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $room_id
 * @property string $path
 * @property string|null $alt_text
 * @property string|null $caption
 * @property int $sort_order
 * @property bool $is_primary
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Room $room
 * @property-read string $url
 * @method static Builder<static>|RoomImage newModelQuery()
 * @method static Builder<static>|RoomImage newQuery()
 * @method static Builder<static>|RoomImage ordered()
 * @method static Builder<static>|RoomImage primary()
 * @method static Builder<static>|RoomImage query()
 * @method static Builder<static>|RoomImage whereAltText($value)
 * @method static Builder<static>|RoomImage whereCaption($value)
 * @method static Builder<static>|RoomImage whereCreatedAt($value)
 * @method static Builder<static>|RoomImage whereId($value)
 * @method static Builder<static>|RoomImage whereIsPrimary($value)
 * @method static Builder<static>|RoomImage wherePath($value)
 * @method static Builder<static>|RoomImage whereRoomId($value)
 * @method static Builder<static>|RoomImage whereSortOrder($value)
 * @method static Builder<static>|RoomImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoomImage extends Model
{
    use HasMedia;

    /** @var list<string> */
    protected $fillable = [
        'room_id',
        'path',
        'alt_text',
        'caption',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    /** @return BelongsTo<Room, RoomImage> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }
}
