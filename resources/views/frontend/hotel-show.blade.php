<x-layouts.frontend :title="$hotel->name">

    {{-- ═══════════════════════════════════════════════════════
         IMMERSIVE COVER HERO
    ═══════════════════════════════════════════════════════ --}}
    <section class="relative flex min-h-[76vh] items-end overflow-hidden bg-[#0b1121]">
        {{-- Background image / gradient --}}
        @if($hotel->cover_image_url)
            <img src="{{ $hotel->cover_image_url }}" alt="{{ $hotel->name }}"
                 class="absolute inset-0 h-full w-full object-cover">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-[var(--gold)]/25 via-[#0b1121] to-[#0b1121]"></div>
        @endif

        {{-- Overlays --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b1121] via-[#0b1121]/45 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b1121]/70 via-transparent to-transparent"></div>

        {{-- Decorative orbs --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -start-24 top-1/4 h-80 w-80 animate-float rounded-full bg-[var(--gold)]/15 blur-[120px]"></div>
            <div class="absolute end-0 top-1/3 h-64 w-64 animate-float-delayed rounded-full bg-indigo-400/10 blur-[100px]"></div>
            <div class="absolute bottom-16 start-1/2 h-52 w-52 animate-float-slow rounded-full bg-[var(--gold)]/10 blur-[100px]"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-6 pb-32 pt-40">
            {{-- Breadcrumb --}}
            <nav class="mb-6 animate-fade-in-down" aria-label="{{ __('common.breadcrumb') }}">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-white/70">
                    <li>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 transition-colors hover:text-white">
                            <i class="bi bi-house-door-fill text-xs"></i>
                            {{ __('auth.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-[10px] text-white/50"></i></li>
                    <li>
                        <a href="{{ route('frontend.hotels') }}" class="inline-flex items-center gap-1.5 transition-colors hover:text-white">
                            <i class="bi bi-buildings text-xs"></i>
                            {{ __('hotels.title') }}
                        </a>
                    </li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-[10px] text-white/50"></i></li>
                    <li class="font-semibold text-white">{{ $hotel->name }}</li>
                </ol>
            </nav>

            {{-- Badges --}}
            <div class="mb-5 flex flex-wrap items-center gap-3 animate-fade-in-up">
                <span class="inline-flex items-center gap-2 rounded-full border border-[var(--gold)]/40 bg-[var(--gold)]/20 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[var(--gold-light)] backdrop-blur-md">
                    <i class="bi bi-gem text-[10px]"></i>
                    {{ $hotel->star_rating_label }}
                </span>
                @if($hotel->average_rating > 0)
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold text-white backdrop-blur-md">
                        <i class="bi bi-star-fill text-[var(--gold-light)]"></i>
                        {{ number_format($hotel->average_rating, 1) }}
                        <span class="text-white/60 font-medium">({{ $hotel->reviews_count }})</span>
                    </span>
                @endif
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/30 bg-emerald-400/15 px-4 py-1.5 text-xs font-bold text-emerald-200 backdrop-blur-md">
                    <i class="bi bi-patch-check-fill"></i>
                    {{ __('hotels.verified') }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="animate-fade-in-up delay-100 font-serif text-4xl font-bold text-white drop-shadow-lg sm:text-5xl lg:text-6xl">
                {{ $hotel->name }}
            </h1>

            {{-- Location --}}
            <div class="mt-4 flex flex-wrap items-center gap-4 text-white/85 animate-fade-in-up delay-150">
                <span class="inline-flex items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-[var(--gold-light)]"></i>
                    {{ $hotel->city }}, {{ $hotel->country }}
                </span>
                <span class="hidden h-1 w-1 rounded-full bg-white/40 sm:inline-block"></span>
                <span class="inline-flex items-center gap-2">
                    <i class="bi bi-telephone-fill text-[var(--gold-light)]"></i>
                    {{ $hotel->phone }}
                </span>
            </div>

            {{-- Amenities --}}
            @if($availableAmenities->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2 animate-fade-in-up delay-200">
                    @foreach($availableAmenities->take(8) as $amenity)
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-sm text-white backdrop-blur-md transition-all hover:bg-white/20">
                            <span class="text-[var(--gold-light)]">{!! $amenity->icon !!}</span>
                            {{ $amenity->name }}
                        </span>
                    @endforeach
                    @if($availableAmenities->count() > 8)
                        <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-sm font-semibold text-white backdrop-blur-md">
                            +{{ $availableAmenities->count() - 8 }}
                        </span>
                    @endif
                </div>
            @endif

            {{-- Stats + CTA --}}
            <div class="mt-8 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between animate-fade-in-up delay-300">
                <dl class="flex flex-wrap gap-6 sm:gap-10">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-white/50">{{ __('rooms.available') }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold text-white">{{ $hotel->available_rooms_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-white/50">{{ __('hotels.room_types') }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold text-white">{{ $hotel->roomTypes->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-white/50">{{ __('reviews.reviews') }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold text-white">{{ $hotel->reviews_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-widest text-white/50">{{ __('hotels.starts_from') }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold text-[var(--gold-light)]">
                            @if($hotel->min_price)
                                ${{ number_format($hotel->min_price, 2) }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="#rooms" class="inline-flex items-center justify-center gap-2 rounded-[var(--radius-lg)] border border-white/30 bg-white/10 px-6 py-2.5 text-sm font-semibold text-white backdrop-blur-md transition-all hover:bg-white/20 hover:border-white/50">
                        <i class="bi bi-door-open"></i>
                        {{ __('hotels.view_rooms') }}
                    </a>
                    <a href="{{ route('frontend.booking.show', $hotel->slug) }}" class="btn-luxury">
                        <i class="bi bi-calendar-check"></i>
                        {{ __('booking.book_now') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════
         MAIN CONTENT + BOOKING SIDEBAR
    ═══════════════════════════════════════════════════════ --}}
    <section class="relative z-10 mx-auto -mt-24 max-w-7xl px-6 pb-20">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            {{-- ────────── LEFT / MAIN ────────── --}}
            <div class="lg:col-span-2 space-y-10">

                {{-- About --}}
                <div class="card-flat p-8 reveal">
                    <div class="mb-5">
                        <span class="inline-flex items-center gap-2 rounded-full border border-[var(--gold)]/20 bg-[var(--gold)]/8 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[var(--gold)]">
                            <i class="bi bi-info-circle text-[10px]"></i>
                            {{ __('hotels.about') }}
                        </span>
                        <h2 class="mt-4 text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ $hotel->name }}</h2>
                    </div>

                    <p class="text-[var(--text-secondary)] leading-relaxed text-lg">
                        {{ $hotel->description }}
                    </p>

                    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="flex items-start gap-3 rounded-2xl border border-[var(--border-light)] bg-[var(--surface-alt)] p-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--gold)]/10 text-[var(--gold)]">
                                <i class="bi bi-geo-alt-fill"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">{{ __('hotels.quick_info') }}</p>
                                <p class="mt-1 text-sm text-[var(--text-primary)]">{{ $hotel->full_address }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-[var(--border-light)] bg-[var(--surface-alt)] p-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--gold)]/10 text-[var(--gold)]">
                                <i class="bi bi-telephone-fill"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">{{ __('booking.phone') }}</p>
                                <p class="mt-1 text-sm text-[var(--text-primary)]" dir="ltr">{{ $hotel->phone }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-[var(--border-light)] bg-[var(--surface-alt)] p-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[var(--gold)]/10 text-[var(--gold)]">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">{{ __('booking.email') }}</p>
                                <a href="mailto:{{ $hotel->email }}" class="mt-1 block truncate text-sm text-[var(--text-primary)] hover:text-[var(--gold)]" dir="ltr">{{ $hotel->email }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $hotelImages = $hotel->galleries->flatMap(fn ($g) => $g->images);
                @endphp

                {{-- Gallery --}}
                @if($hotelImages->count() > 0)
                    <div class="reveal d1" id="gallery">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ __('hotels.gallery') }}</h2>
                            <span class="text-sm text-[var(--text-muted)]">{{ $hotelImages->count() }} {{ __('hotels.images') }}</span>
                        </div>
                        <div class="grid auto-rows-[10rem] grid-cols-2 gap-3 md:grid-cols-4">
                            @foreach($hotelImages->take(5) as $index => $image)
                                <div class="{{ $index === 0 ? 'col-span-2 row-span-2' : '' }} img-zoom card overflow-hidden rounded-2xl">
                                    <img src="{{ $image->full_url }}"
                                         alt="{{ $image->caption ?? $hotel->name }}"
                                         class="h-full w-full object-cover"
                                         loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Room types --}}
                <div class="reveal d2" id="rooms">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ __('hotels.available_rooms') }}</h2>
                        <span class="text-sm text-[var(--text-muted)]">{{ $hotel->roomTypes->count() }} {{ __('hotels.room_types') }}</span>
                    </div>

                    @if($hotel->roomTypes->count() > 0)
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            @foreach($hotel->roomTypes as $roomType)
                                <article class="card group overflow-hidden">
                                    <div class="relative h-40 bg-gradient-to-br from-[var(--gold)]/20 via-[var(--gold)]/8 to-transparent">
                                        <div class="absolute inset-0 grid place-items-center">
                                            <i class="bi bi-door-open text-5xl text-[var(--gold)]/40 transition-transform duration-300 group-hover:scale-110"></i>
                                        </div>
                                        <div class="absolute end-4 top-4">
                                            <span class="badge bg-[var(--surface)]/80 backdrop-blur-md border border-[var(--border)]">
                                                <i class="bi bi-people-fill text-[var(--gold)]"></i>
                                                {{ $roomType->max_guests }} {{ __('rooms.guests') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="text-lg font-bold text-[var(--text-primary)]">{{ $roomType->name }}</h3>
                                        @if($roomType->description)
                                            <p class="mt-1 text-sm text-[var(--text-secondary)] line-clamp-2">{{ $roomType->description }}</p>
                                        @endif
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <span class="chip"><i class="bi bi-door-open text-[var(--gold)] text-xs"></i> {{ $roomType->rooms_count }} {{ __('rooms.available') }}</span>
                                        </div>
                                        <div class="mt-5 flex items-center justify-between border-t border-[var(--border)] pt-5">
                                            <p class="text-[var(--text-primary)]">
                                                <span class="text-2xl font-extrabold text-[var(--gold)]">${{ number_format($roomType->base_price, 2) }}</span>
                                                <span class="text-sm text-[var(--text-muted)]">/ {{ __('hotels.per_night') }}</span>
                                            </p>
                                            <a href="{{ route('frontend.booking.show', $hotel->slug) }}" class="btn-primary btn-sm">
                                                {{ __('booking.book_now') }}
                                                <i class="bi bi-arrow-right text-xs transition-transform group-hover:translate-x-0.5"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="card-flat text-center py-16">
                            <i class="bi bi-door-open text-5xl text-[var(--text-muted)] opacity-40 mb-3"></i>
                            <p class="text-[var(--text-secondary)]">{{ __('rooms.no_available') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Reviews --}}
                @if($hotel->reviews->count() > 0)
                    <div class="reveal d3" id="reviews">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ __('reviews.reviews') }}</h2>
                            <x-frontend.star-rating :rating="$hotel->average_rating" :count="$hotel->reviews_count" size="md" />
                        </div>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            @foreach($hotel->reviews as $review)
                                <div class="card p-6">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar avatar-md">{{ substr($review->user->name, 0, 1) }}</div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[var(--text-primary)] truncate">{{ $review->user->name }}</p>
                                            <p class="text-xs text-[var(--text-muted)]">{{ $review->created_at->diffForHumans() }}</p>
                                        </div>
                                        <x-frontend.star-rating :rating="$review->rating" size="sm" class="ms-auto shrink-0" />
                                    </div>
                                    <p class="mt-4 text-[var(--text-secondary)] leading-relaxed">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ────────── RIGHT / STICKY BOOKING ────────── --}}
            <aside class="lg:col-span-1">
                <div class="card sticky top-24 overflow-hidden reveal">
                    <div class="relative bg-gradient-to-br from-[var(--gold)] to-[var(--gold-dark)] px-8 py-6">
                        <div class="pointer-events-none absolute inset-0 opacity-10" aria-hidden="true">
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.5) 1px, transparent 0); background-size: 18px 18px;"></div>
                        </div>
                        <div class="relative flex items-end justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-widest text-white/80">{{ __('hotels.starts_from') }}</p>
                                <p class="mt-1 text-4xl font-extrabold text-white">
                                    @if($hotel->min_price)
                                        ${{ number_format($hotel->min_price, 2) }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <p class="text-sm font-medium text-white/80">{{ __('hotels.per_night') }}</p>
                        </div>
                    </div>

                    <form action="{{ route('frontend.booking.show', $hotel->slug) }}" method="GET" class="space-y-5 px-8 py-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="mini-check-in">{{ __('booking.check_in') }}</label>
                                <input type="date" id="mini-check-in" name="check_in" class="input" min="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="form-group">
                                <label for="mini-check-out">{{ __('booking.check_out') }}</label>
                                <input type="date" id="mini-check-out" name="check_out" class="input" min="{{ now()->addDay()->toDateString() }}" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="mini-adults">{{ __('booking.adults') }}</label>
                                <select id="mini-adults" name="adults" class="select">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mini-children">{{ __('booking.children') }}</label>
                                <select id="mini-children" name="children" class="select">
                                    @for($i = 0; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            <i class="bi bi-search"></i>
                            {{ __('booking.check_availability') }}
                        </button>
                    </form>

                    <div class="space-y-3 border-t border-[var(--border-light)] bg-[var(--surface-alt)]/60 px-8 py-6 text-sm">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-geo-alt text-[var(--gold)]"></i>
                            <span class="text-[var(--text-secondary)]">{{ $hotel->city }}, {{ $hotel->country }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="bi bi-telephone text-[var(--gold)]"></i>
                            <span class="text-[var(--text-secondary)]" dir="ltr">{{ $hotel->phone }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="bi bi-envelope text-[var(--gold)]"></i>
                            <a href="mailto:{{ $hotel->email }}" class="text-[var(--text-secondary)] truncate hover:text-[var(--gold)]" dir="ltr">{{ $hotel->email }}</a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <x-frontend.cta-section
        :title="__('booking.book') . ' ' . $hotel->name"
        :text="$hotel->description ? Str::limit($hotel->description, 160) : null"
        :buttonText="__('booking.book_now')"
        :buttonUrl="route('frontend.booking.show', $hotel->slug)"
    />

</x-layouts.frontend>
