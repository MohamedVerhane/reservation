<x-layouts.frontend :title="__('auth.home')">

    @php $heroImage = $featuredHotels->first()?->cover_image_url; @endphp

    {{-- ═══════════ HERO ═══════════ --}}
    <section x-data="heroParallax" @mousemove="onMove" @mouseleave="onLeave"
             class="persp-1600 relative flex min-h-[calc(100vh-4rem)] items-center overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0 preserve-3d" :style="far()">
            @if($heroImage)
                <img src="{{ $heroImage }}" alt="" class="h-full w-full object-cover" />
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-background/85 via-background/55 to-background"></div>
        </div>

        {{-- 3D parallax glow blobs --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true" :style="mid()">
            <div class="absolute -start-24 top-1/4 h-96 w-96 animate-float rounded-full bg-primary/30 blur-[110px]"></div>
            <div class="absolute end-10 top-1/3 h-80 w-80 animate-float-delayed rounded-full bg-green/25 blur-[110px]"></div>
            <div class="absolute bottom-24 start-1/3 h-72 w-72 animate-float-slow rounded-full bg-primary/15 blur-[110px]"></div>
        </div>

        {{-- Floating 3D chips (desktop) --}}
        <div class="absolute start-[5%] top-[30%] z-10 hidden animate-float-3d lg:block" aria-hidden="true">
            <div class="preserve-3d translate-z-0 flex items-center gap-2.5 rounded-2xl border border-border/60 bg-card/80 p-3 shadow-2xl shadow-primary/10 backdrop-blur-md">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <i data-lucide="badge-dollar-sign" class="h-4 w-4"></i>
                </span>
                <div class="text-start">
                    <p class="text-xs font-bold text-foreground">{{ __('auth.home_feature_price') }}</p>
                    <p class="text-[11px] text-muted-foreground">{{ __('auth.home_feature_price_text') }}</p>
                </div>
            </div>
        </div>

        <div class="absolute end-[5%] top-[42%] z-10 hidden animate-float-3d lg:block" style="animation-delay: 1.2s" aria-hidden="true">
            <div class="preserve-3d translate-z-0 flex items-center gap-2.5 rounded-2xl border border-border/60 bg-card/80 p-3 shadow-2xl shadow-green/10 backdrop-blur-md">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-green/10 text-green-dark dark:text-green-light">
                    <i data-lucide="concierge-bell" class="h-4 w-4"></i>
                </span>
                <div class="text-start">
                    <p class="text-xs font-bold text-foreground">{{ __('auth.home_feature_concierge') }}</p>
                    <p class="text-[11px] text-muted-foreground">{{ __('auth.home_feature_concierge_text') }}</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-6 py-24 text-center">

            {{-- Badge --}}
            <span class="relative inline-flex">
                <span class="absolute -inset-3 animate-ring-breathe rounded-full border border-primary/30 blur-[1px]" aria-hidden="true"></span>
                <span class="animate-fade-in-down relative inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-5 py-2 text-sm font-semibold text-primary backdrop-blur-md">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    {{ __('auth.app_name') }}
                </span>
            </span>

            {{-- Heading --}}
            <h1 class="animate-fade-in-up delay-100 mx-auto mt-7 max-w-4xl font-serif text-5xl font-bold leading-[1.05] tracking-tight text-foreground sm:text-6xl lg:text-7xl">
                {{ __('auth.home_hero_title') }}
            </h1>

            {{-- Subtitle --}}
            <p class="animate-fade-in-up delay-200 mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-muted-foreground sm:text-xl">
                {{ __('auth.home_hero_subtitle') }}
            </p>

            {{-- CTAs --}}
            <div class="animate-fade-in-up delay-300 mt-9 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <x-ui.button href="{{ route('frontend.hotels') }}" variant="3d" size="lg" shine>
                    <i data-lucide="building-2" class="h-5 w-5"></i>
                    {{ __('auth.home_hero_cta') }}
                </x-ui.button>
                <x-ui.button href="#featured-hotels" variant="outline" size="lg">
                    <i data-lucide="play" class="h-5 w-5 rtl:rotate-180"></i>
                    {{ __('auth.home_hero_cta2') }}
                </x-ui.button>
            </div>

            {{-- Quick search --}}
            <form action="{{ route('frontend.search') }}" method="GET" class="animate-fade-in-up delay-300 mx-auto mt-14 max-w-4xl">
                <div class="grid grid-cols-1 gap-2 rounded-2xl border border-border/60 bg-card/70 p-3 text-start shadow-xl shadow-primary/5 backdrop-blur-md sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_0.9fr_auto]">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="qs-destination" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('search.destination') }}</label>
                        <div class="relative">
                            <i data-lucide="map-pin" class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"></i>
                            <input id="qs-destination" name="search" type="text" placeholder="{{ __('search.destination_placeholder') }}"
                                   class="h-11 w-full rounded-lg border border-input bg-background ps-9 pe-3 text-sm text-foreground shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40" />
                        </div>
                    </div>
                    <div>
                        <label for="qs-check-in" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('search.check_in') }}</label>
                        <input id="qs-check-in" name="check_in" type="date"
                               class="h-11 w-full rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40" />
                    </div>
                    <div>
                        <label for="qs-check-out" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('search.check_out') }}</label>
                        <input id="qs-check-out" name="check_out" type="date"
                               class="h-11 w-full rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40" />
                    </div>
                    <div>
                        <label for="qs-guests" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ __('search.guests') }}</label>
                        <select id="qs-guests" name="guests"
                                class="h-11 w-full rounded-lg border border-input bg-background px-3 text-sm text-foreground shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40">
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? __('search.guests_singular') : __('search.guests_plural') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-primary to-primary/80 px-6 text-sm font-semibold text-primary-foreground shadow-md shadow-primary/20 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/30">
                            <i data-lucide="search" class="h-4 w-4"></i>
                            {{ __('search.search_button') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- Trust row --}}
            <div class="animate-fade-in-up delay-400 mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-sm text-muted-foreground">
                <span class="inline-flex items-center gap-2">
                    <i data-lucide="shield-check" class="h-4 w-4 text-primary"></i>
                    {{ __('auth.home_trust_label') }}
                </span>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-6 start-1/2 z-10 -translate-x-1/2 rtl:translate-x-1/2">
            <a href="#featured-hotels" class="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-background/50 text-muted-foreground backdrop-blur-md transition-all hover:border-primary hover:text-primary">
                <i data-lucide="chevron-down" class="h-5 w-5"></i>
            </a>
        </div>
    </section>

    {{-- Destination marquee --}}
    @if($featuredHotels->isNotEmpty())
        <section class="relative overflow-hidden border-y border-border/60 bg-muted/40 py-4">
            <div class="flex w-max animate-marquee items-center gap-10">
                @for($rep = 0; $rep < 2; $rep++)
                    @foreach($featuredHotels as $hotel)
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-muted-foreground">
                            <i data-lucide="map-pin" class="h-4 w-4 text-primary"></i>{{ $hotel->city }}, {{ $hotel->country }}
                        </span>
                    @endforeach
                @endfor
            </div>
        </section>
    @endif

    {{-- ═══════════ WHY LUXESTAY ═══════════ --}}
    <section class="relative py-24">
        <div class="mx-auto w-full max-w-7xl px-6">
            <div class="mx-auto mb-16 max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-green/30 bg-green/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-green-dark dark:text-green-light">
                    <i data-lucide="crown" class="h-3.5 w-3.5"></i>
                    {{ __('auth.home_features_title') }}
                </span>
                <h2 class="mt-5 font-serif text-4xl font-bold tracking-tight text-foreground">{{ __('auth.home_features_title') }}</h2>
                <p class="mt-4 text-muted-foreground">{{ __('auth.home_features_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $features = [
                        ['icon' => 'badge-dollar-sign', 'title' => __('auth.home_feature_price'), 'text' => __('auth.home_feature_price_text'), 'color' => 'red'],
                        ['icon' => 'concierge-bell', 'title' => __('auth.home_feature_concierge'), 'text' => __('auth.home_feature_concierge_text'), 'color' => 'green'],
                        ['icon' => 'spray-can', 'title' => __('auth.home_feature_clean'), 'text' => __('auth.home_feature_clean_text'), 'color' => 'red'],
                        ['icon' => 'award', 'title' => __('auth.home_feature_award'), 'text' => __('auth.home_feature_award_text'), 'color' => 'green'],
                    ];
                @endphp
                @foreach($features as $i => $feature)
                    <x-ui.card x-data="tiltCard({ max: 14 })" @mousemove="onMove" @mouseleave="onLeave" x-bind:style="style()"
                               class="reveal d{{ $i + 1 }} p-6 transition-shadow duration-300 hover:shadow-2xl hover:shadow-primary/20">
                        <div class="pointer-events-none absolute inset-0 rounded-xl" :style="glareStyle()" aria-hidden="true"></div>
                        <div class="relative">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl {{ $feature['color'] === 'red' ? 'bg-primary/10 text-primary' : 'bg-green/10 text-green-dark dark:text-green-light' }}">
                                <i data-lucide="{{ $feature['icon'] }}" class="h-6 w-6"></i>
                            </div>
                            <h3 class="mb-2 text-lg font-semibold text-foreground">{{ $feature['title'] }}</h3>
                            <p class="text-sm leading-relaxed text-muted-foreground">{{ $feature['text'] }}</p>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════ FEATURED HOTELS ═══════════ --}}
    <section id="featured-hotels" class="bg-muted/40 py-24">
        <div class="mx-auto w-full max-w-7xl px-6">
            <div class="mb-14 max-w-2xl text-center mx-auto">
                <span class="inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-primary">
                    <i data-lucide="star" class="h-3.5 w-3.5 fill-primary"></i>
                    {{ __('auth.home_featured_title') }}
                </span>
                <h2 class="mt-5 font-serif text-4xl font-bold tracking-tight text-foreground">{{ __('auth.home_featured_title') }}</h2>
                <p class="mt-4 text-muted-foreground">{{ __('auth.home_featured_subtitle') }}</p>
            </div>

            @if($featuredHotels->isNotEmpty())
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredHotels as $hotel)
                        <div class="reveal d{{ $loop->index + 1 }}">
                            <x-frontend.hotel-card :hotel="$hotel" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center gap-3 rounded-xl border border-border bg-card p-16 text-center">
                    <i data-lucide="building-2" class="h-10 w-10 text-muted-foreground"></i>
                    <h3 class="text-lg font-semibold text-foreground">{{ __('auth.hotels_no_results') }}</h3>
                </div>
            @endif

            <div class="mt-12 text-center reveal">
                <x-ui.button href="{{ route('frontend.hotels') }}" variant="outline" size="lg">
                    {{ __('auth.home_view_all') }} {{ __('auth.nav_hotels') }}
                    <i data-lucide="arrow-right" class="h-4 w-4 rtl:rotate-180"></i>
                </x-ui.button>
            </div>
        </div>
    </section>

    {{-- ═══════════ ROOMS SHOWCASE ═══════════ --}}
    <section class="py-24">
        <div class="mx-auto w-full max-w-7xl px-6">
            <div class="mb-14 max-w-2xl text-center mx-auto">
                <span class="inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-primary">
                    <i data-lucide="door-open" class="h-3.5 w-3.5"></i>
                    {{ __('auth.home_rooms_title') }}
                </span>
                <h2 class="mt-5 font-serif text-4xl font-bold tracking-tight text-foreground">{{ __('auth.home_rooms_title') }}</h2>
                <p class="mt-4 text-muted-foreground">{{ __('auth.home_rooms_subtitle') }}</p>
            </div>

            @if($featuredRooms->isNotEmpty())
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredRooms as $room)
                        <div class="reveal d{{ $loop->index + 1 }}">
                            <x-frontend.room-card :room="$room" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center gap-3 rounded-xl border border-border bg-card p-16 text-center">
                    <i data-lucide="door-open" class="h-10 w-10 text-muted-foreground"></i>
                    <h3 class="text-lg font-semibold text-foreground">{{ __('auth.hotels_no_results') }}</h3>
                </div>
            @endif
        </div>
    </section>

    <x-frontend.stats-bar :stats="$stats" />

    <x-frontend.cta-section
        :title="__('auth.home_cta_title')"
        :text="__('auth.home_cta_text')"
        :buttonText="__('auth.home_cta_button')"
        :buttonUrl="route('frontend.hotels')"
    />

</x-layouts.frontend>
