@props(['room'])

@php
    $statusClass = match($room->status ?? 'available') {
        'available' => 'badge-green',
        'occupied' => 'badge-red',
        'maintenance' => 'badge-gold',
        'reserved' => 'badge-blue',
        default => 'badge-slate',
    };
    $amenities = $room->amenities ?? collect();
@endphp

<article class="group card overflow-hidden">
    {{-- Image area --}}
    <div class="relative img-zoom">
        @if($room->images && count($room->images) > 0)
            <img
                src="{{ $room->images->first()->url ?? $room->images->first() }}"
                alt="{{ e($room->name ?? $room->roomType->name ?? __('auth.room')) }}"
                class="h-56 w-full object-cover"
                loading="lazy"
            />
        @else
            <div class="flex h-56 w-full items-center justify-center bg-gradient-to-br from-[var(--gold)]/15 via-indigo-500/10 to-purple-500/10">
                <i class="bi bi-door-open text-5xl text-[var(--gold)]/30"></i>
            </div>
        @endif

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>

        {{-- Room number badge --}}
        <div class="absolute start-4 top-4">
            <span class="badge backdrop-blur-md bg-black/30 border border-white/20 text-white">
                <i class="bi bi-hash text-[10px]"></i>
                {{ $room->room_number }}
            </span>
        </div>

        {{-- Status badge --}}
        <div class="absolute end-4 top-4">
            <span class="badge {{ $statusClass }} backdrop-blur-md">
                <i class="bi bi-circle-fill text-[0.4rem]"></i>
                {{ __('auth.' . ($room->status ?? 'available')) }}
            </span>
        </div>

        {{-- Price overlay --}}
        <div class="absolute bottom-4 inset-x-0 px-5">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-white drop-shadow-lg">
                    ${{ number_format($room->roomType->base_price ?? 0, 0) }}
                </span>
                <span class="text-sm text-white/70">/ {{ __('auth.night') }}</span>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="p-5">
        <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">
            {{ $room->roomType->name ?? __('auth.room') . ' #' . $room->room_number }}
        </h3>

        @if($room->hotel)
            <div class="flex items-center gap-1.5 text-sm text-[var(--text-muted)] mb-3">
                <i class="bi bi-building text-[var(--gold)] text-xs"></i>
                <span>{{ $room->hotel->name }}</span>
            </div>
        @endif

        <div class="flex items-center gap-3 text-sm text-[var(--text-muted)] mb-4">
            @if($room->floor)
                <span class="inline-flex items-center gap-1">
                    <i class="bi bi-layers text-[var(--gold)] text-xs"></i>
                    {{ __('auth.floor') }} {{ $room->floor }}
                </span>
            @endif
            @if($room->roomType?->max_guests)
                <span class="inline-flex items-center gap-1">
                    <i class="bi bi-people text-[var(--gold)] text-xs"></i>
                    {{ $room->roomType->max_guests }} {{ __('auth.guests') }}
                </span>
            @endif
        </div>

        @if($amenities->count() > 0)
            <div class="flex flex-wrap items-center gap-1.5 mb-5">
                @foreach($amenities->take(3) as $amenity)
                    <span class="chip text-xs py-1 px-2">
                        <span class="text-[var(--gold)]">{!! $amenity->icon !!}</span>
                        {{ $amenity->name }}
                    </span>
                @endforeach
                @if($amenities->count() > 3)
                    <span class="text-xs font-medium text-[var(--text-muted)]">+{{ $amenities->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <a
            href="{{ route('frontend.room.show', [$room->hotel_id, $room->id]) }}"
            class="btn-primary w-full text-center"
        >
            {{ __('auth.view_details') }}
            <i class="bi bi-arrow-right text-xs"></i>
        </a>
    </div>
</article>
