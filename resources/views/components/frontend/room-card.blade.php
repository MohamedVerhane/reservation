@props(['room'])

@php
    $statusClass = match($room->status ?? 'available') {
        'available' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
        'occupied' => 'bg-red-500/10 text-red-600 dark:text-red-400',
        'maintenance' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
        'reserved' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
        default => 'bg-muted text-muted-foreground',
    };
    $amenities = $room->amenities ?? collect();
@endphp

<x-ui.card :as="'article'" x-data="tiltCard({ max: 9 })" @mousemove="onMove" @mouseleave="onLeave" x-bind:style="style()"
           class="group overflow-hidden transition-shadow duration-300 hover:shadow-2xl hover:shadow-primary/20">
    <div class="pointer-events-none absolute inset-0 z-20" :style="glareStyle()" aria-hidden="true"></div>
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
            <div class="flex h-56 w-full items-center justify-center bg-gradient-to-br from-primary/15 via-primary/10 to-primary/5">
                <i data-lucide="door-open" class="h-12 w-12 text-primary/30"></i>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>

        {{-- Room number badge --}}
        <div class="absolute start-4 top-4">
            <x-ui.badge variant="outline" class="border-white/20 bg-black/30 text-white backdrop-blur-md">
                <i data-lucide="hash" class="h-3 w-3"></i>
                {{ $room->room_number }}
            </x-ui.badge>
        </div>

        {{-- Status badge --}}
        <div class="absolute end-4 top-4">
            <span class="{{ $statusClass }} inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold backdrop-blur-md">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                {{ __("rooms." . ($room->status ?? 'available')) }}
            </span>
        </div>

        {{-- Price overlay --}}
        <div class="absolute inset-x-0 bottom-4 px-5">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-white drop-shadow-lg">${{ number_format($room->roomType->base_price ?? 0, 0) }}</span>
                <span class="text-sm text-white/70">/ {{ __('auth.night') }}</span>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="p-5">
        <h3 class="mb-1 text-lg font-bold text-foreground">
            {{ $room->roomType->name ?? __('auth.room') . ' #' . $room->room_number }}
        </h3>

        @if($room->hotel)
            <div class="mb-3 flex items-center gap-1.5 text-sm text-muted-foreground">
                <i data-lucide="building-2" class="h-3.5 w-3.5 text-primary"></i>
                <span>{{ $room->hotel->name }}</span>
            </div>
        @endif

        <div class="mb-4 flex items-center gap-3 text-sm text-muted-foreground">
            @if($room->floor)
                <span class="inline-flex items-center gap-1">
                    <i data-lucide="layers" class="h-3.5 w-3.5 text-primary"></i>
                    {{ __('auth.floor') }} {{ $room->floor }}
                </span>
            @endif
            @if($room->roomType?->max_guests)
                <span class="inline-flex items-center gap-1">
                    <i data-lucide="users" class="h-3.5 w-3.5 text-primary"></i>
                    {{ $room->roomType->max_guests }} {{ __('auth.guests') }}
                </span>
            @endif
        </div>

        @if($amenities->count() > 0)
            <div class="mb-5 flex flex-wrap items-center gap-1.5">
                @foreach($amenities->take(3) as $amenity)
                    <span class="inline-flex items-center gap-1 rounded-full bg-secondary px-2 py-1 text-xs font-medium text-secondary-foreground">
                        <span class="text-primary"><i class="{{ $amenity->icon }}"></i></span>
                        {{ $amenity->name }}
                    </span>
                @endforeach
                @if($amenities->count() > 3)
                    <span class="text-xs font-medium text-muted-foreground">+{{ $amenities->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <a
            href="{{ route('frontend.room.show', [$room->hotel_id, $room->id]) }}"
            class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-gradient-to-br from-primary to-primary/80 px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm shadow-primary/20 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-primary/30"
        >
            {{ __('auth.view_details') }}
            <i data-lucide="arrow-right" class="h-4 w-4 rtl:rotate-180"></i>
        </a>
    </div>
</x-ui.card>
