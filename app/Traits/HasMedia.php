<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasMedia
{
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function deleteFromStorage(): void
    {
        Storage::disk('public')->delete($this->path);
        $this->delete();
    }
}
