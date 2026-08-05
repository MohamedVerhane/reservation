@props(['amenity', 'size' => 'sm'])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        default => 'px-2.5 py-1 text-xs',
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full border border-[var(--gold)]/20 bg-[var(--gold)]/5 {{ $sizeClasses }} text-[var(--text-secondary)]">
    <span class="text-[var(--gold)]">
        <i class="{{ $amenity->icon }}"></i>
    </span>
    <span>{{ $amenity->name }}</span>
</span>
