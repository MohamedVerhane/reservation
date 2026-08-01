@props(['hotel'])

<article class="group card overflow-hidden">
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
            <div class="flex h-60 w-full items-center justify-center bg-gradient-to-br from-[var(--gold)]/20 via-[var(--gold)]/10 to-[var(--gold)]/5">
                <i class="bi bi-building text-5xl text-[var(--gold)]/30"></i>
            </div>
        @endif

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

        {{-- Star badge top-left --}}
        @if($hotel->star_rating)
            <div class="absolute start-4 top-4">
                <span class="badge backdrop-blur-md bg-black/30 border border-white/20 text-white">
                    @for($i = 0; $i < min($hotel->star_rating, 5); $i++)
                        <i class="bi bi-star-fill text-[var(--gold-light)] text-[10px]"></i>
                    @endfor
                </span>
            </div>
        @endif

        {{-- Rating badge top-right --}}
        <div class="absolute end-4 top-4">
            <span class="badge backdrop-blur-md bg-black/30 border border-white/20 text-white">
                <i class="bi bi-star-fill text-[var(--gold-light)] text-[10px]"></i>
                {{ number_format($hotel->average_rating ?? 0, 1) }}
            </span>
        </div>

        {{-- Location + name at bottom of image --}}
        <div class="absolute bottom-0 inset-x-0 p-5">
            <h3 class="text-xl font-bold text-white drop-shadow-lg mb-1">
                {{ $hotel->name }}
            </h3>
            <div class="flex items-center gap-1.5 text-sm text-white/80">
                <i class="bi bi-geo-alt-fill text-[var(--gold-light)] text-xs"></i>
                <span>{{ $hotel->city }}, {{ $hotel->country }}</span>
            </div>
        </div>
    </div>

    {{-- Info area --}}
    <div class="p-5">
        <div class="flex items-center justify-between text-sm text-[var(--text-muted)] mb-4">
            <span class="inline-flex items-center gap-1.5">
                <i class="bi bi-door-open-fill text-[var(--gold)] text-xs"></i>
                {{ $hotel->available_rooms_count ?? $hotel->rooms_count ?? 0 }} {{ __('auth.rooms') }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <i class="bi bi-chat-dots-fill text-[var(--gold)] text-xs"></i>
                {{ $hotel->reviews_count ?? 0 }} {{ __('auth.reviews') }}
            </span>
        </div>

        <div class="flex items-baseline gap-2 mb-5">
            @if(isset($hotel->min_price) && $hotel->min_price > 0)
                <span class="text-sm text-[var(--text-muted)]">{{ __('auth.home_from') }}</span>
                <span class="text-2xl font-extrabold text-[var(--text-primary)]">
                    ${{ number_format($hotel->min_price, 0) }}
                </span>
                <span class="text-sm text-[var(--text-muted)]">/ {{ __('auth.home_night') }}</span>
            @else
                <span class="text-lg font-bold text-[var(--text-primary)]">{{ __('auth.rooms') }}</span>
                <span class="text-sm text-[var(--text-muted)]">{{ __('auth.hotels_available') }}</span>
            @endif
        </div>

        <div class="flex gap-3">
            <a
                href="{{ route('frontend.hotel.show', $hotel->slug) }}"
                class="btn-outline flex-1 text-center"
            >
                {{ __('auth.view_details') }}
                <i class="bi bi-arrow-right text-xs transition-transform group-hover:translate-x-0.5"></i>
            </a>
            <a
                href="{{ route('frontend.booking.show', $hotel->slug) }}"
                class="btn-primary flex-1 text-center"
            >
                <i class="bi bi-calendar-check text-xs"></i>{{ __('auth.booking_book_now') }}
            </a>
        </div>
    </div>
</article>
