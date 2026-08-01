@props(['rating' => 0, 'max' => 5, 'size' => 'md', 'count' => null])

@php
    $sizeClasses = match($size) {
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-xl',
        default => 'text-base',
    };
@endphp

<div class="inline-flex items-center gap-1 {{ $sizeClasses }}">
    @for($i = 1; $i <= $max; $i++)
        @if($i <= floor($rating))
            <span class="text-[var(--gold)]" aria-hidden="true">&#9733;</span>
        @elseif($i - $rating < 1 && $i - $rating > 0)
            <span class="relative text-[var(--border)]" aria-hidden="true">
                <span class="absolute inset-0 overflow-hidden text-[var(--gold)]" style="width: {{ ($rating - floor($rating)) * 100 }}%">&#9733;</span>
                <span>&#9733;</span>
            </span>
        @else
            <span class="text-[var(--border)]" aria-hidden="true">&#9734;</span>
        @endif
    @endfor

    <span class="sr-only">{{ number_format($rating, 1) }} / {{ $max }}</span>

    @if($count !== null)
        <span class="ms-1 text-sm text-[var(--text-muted)]">
            ({{ $count }})
        </span>
    @endif
</div>
