@props(['hotel'])

<x-ui.card :as="'article'" x-data="tiltCard({ max: 9 })" @mousemove="onMove" @mouseleave="onLeave" x-bind:style="style()"
           class="group overflow-hidden transition-shadow duration-300 hover:shadow-2xl hover:shadow-primary/20">
    <div class="pointer-events-none absolute inset-0 z-20" :style="glareStyle()" aria-hidden="true"></div>
    {{-- Image area --}}
    <div class="relative img-zoom">
        @if($hotel->cover_image_url)
            <img
                src="{{ $hotel->cover_image_url }}"
                alt="{{ e($hotel->name) }}"
                class="h-60 w-full object-cover"
                loading="lazy"
            />
        @else
            <div class="flex h-60 w-full items-center justify-center bg-gradient-to-br from-primary/20 via-primary/10 to-primary/5">
                <i data-lucide="building-2" class="h-12 w-12 text-primary/30"></i>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

        {{-- Star badge --}}
        @if($hotel->star_rating)
            <div class="absolute start-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-full border border-white/20 bg-black/30 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-md">
                    <i data-lucide="star" class="h-3 w-3 fill-primary text-primary"></i>
                    {{ $hotel->star_rating }} {{ Str::plural(__('auth.star'), $hotel->star_rating) }}
                </span>
            </div>
        @endif

        {{-- Rating badge --}}
        <div class="absolute end-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-full border border-white/20 bg-black/30 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-md">
                <i data-lucide="star" class="h-3 w-3 fill-primary text-primary"></i>
                {{ number_format($hotel->average_rating ?? 0, 1) }}
            </span>
        </div>

        {{-- Location + name at bottom of image --}}
        <div class="absolute inset-x-0 bottom-0 p-5">
            <h3 class="mb-1 text-xl font-bold text-white drop-shadow-lg">{{ $hotel->name }}</h3>
            <div class="flex items-center gap-1.5 text-sm text-white/80">
                <i data-lucide="map-pin" class="h-3.5 w-3.5 text-primary"></i>
                <span>{{ $hotel->city }}, {{ $hotel->country }}</span>
            </div>
        </div>
    </div>

    {{-- Info area --}}
    <div class="p-5">
        <div class="mb-4 flex items-center justify-between text-sm text-muted-foreground">
            <span class="inline-flex items-center gap-1.5">
                <i data-lucide="door-open" class="h-3.5 w-3.5 text-primary"></i>
                {{ $hotel->available_rooms_count ?? $hotel->rooms_count ?? 0 }} {{ __('auth.rooms') }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <i data-lucide="message-circle" class="h-3.5 w-3.5 text-primary"></i>
                {{ $hotel->reviews_count ?? 0 }} {{ __('auth.reviews') }}
            </span>
        </div>

        <div class="mb-5 flex items-baseline gap-2">
            @if(isset($hotel->min_price) && $hotel->min_price > 0)
                <span class="text-sm text-muted-foreground">{{ __('auth.home_from') }}</span>
                <span class="text-2xl font-extrabold text-foreground">${{ number_format($hotel->min_price, 0) }}</span>
                <span class="text-sm text-muted-foreground">/ {{ __('auth.home_night') }}</span>
            @else
                <span class="text-lg font-bold text-foreground">{{ __('auth.rooms') }}</span>
                <span class="text-sm text-muted-foreground">{{ __('auth.hotels_available') }}</span>
            @endif
        </div>

        <div class="flex gap-3">
            <a
                href="{{ route('frontend.hotel.show', $hotel->slug) }}"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-md border border-border bg-background px-4 py-2 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
            >
                {{ __('auth.view_details') }}
            </a>
            <a
                href="{{ route('frontend.booking.show', $hotel->slug) }}"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-md bg-gradient-to-br from-primary to-primary/80 px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm shadow-primary/20 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-primary/30"
            >
                <i data-lucide="calendar-check" class="h-4 w-4"></i>{{ __('auth.booking_book_now') }}
            </a>
        </div>
    </div>
</x-ui.card>
