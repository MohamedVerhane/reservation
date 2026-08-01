<x-layouts.frontend title="{{ __('auth.app_name') }}">

    {{-- Hero --}}
    <section class="relative flex min-h-[calc(100vh-4rem)] items-center justify-center overflow-hidden bg-[var(--surface)]">
        <div class="absolute inset-0 bg-gradient-to-br from-[var(--surface-alt)] via-[var(--surface)] to-[var(--surface-alt)] opacity-60"></div>
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -start-20 top-1/4 h-96 w-96 animate-float rounded-full bg-[var(--gold)]/10 blur-[100px]"></div>
            <div class="absolute end-10 top-1/3 h-80 w-80 animate-float-delayed rounded-full bg-[var(--gold)]/8 blur-[100px]"></div>
            <div class="absolute bottom-20 start-1/3 h-72 w-72 animate-float-slow rounded-full bg-[var(--gold)]/5 blur-[100px]"></div>
            <div class="absolute top-1/2 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[var(--gold)]/10 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 text-center">

            <div class="animate-fade-in-down">
                <span class="inline-flex items-center gap-2 rounded-full border border-[var(--gold)]/20 bg-[var(--gold)]/10 px-5 py-2 text-sm font-semibold text-[var(--gold)] backdrop-blur-md">
                    <i class="bi bi-gem text-xs"></i>
                    {{ __('auth.app_name') }}
                </span>
            </div>

            <h1 class="animate-fade-in-up delay-100 mt-8 text-5xl font-extrabold leading-[1.1] tracking-tight text-[var(--text-primary)] sm:text-6xl lg:text-7xl">
                {{ __('auth.home_hero_title') }}
            </h1>

            <p class="animate-fade-in-up delay-200 mx-auto mt-6 max-w-2xl text-xl text-[var(--text-secondary)] leading-relaxed">
                {{ __('auth.home_hero_subtitle') }}
            </p>

            <div class="animate-fade-in-up delay-300 mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('frontend.hotels') }}" class="btn-primary btn-lg btn-pill">
                    <i class="bi bi-building text-lg"></i>
                    {{ __('auth.home_hero_cta') }}
                </a>
                <a href="#featured-hotels" class="btn-outline btn-lg btn-pill">
                    <i class="bi bi-play-circle text-lg"></i>
                    {{ __('auth.home_hero_cta2') }}
                </a>
            </div>
        </div>

        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 scroll-indicator">
            <a href="#featured-hotels" class="flex h-10 w-10 items-center justify-center rounded-full border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--gold)] hover:border-[var(--gold)] transition-all">
                <i class="bi bi-chevron-double-down text-lg"></i>
            </a>
        </div>
    </section>

    {{-- Featured Hotels --}}
    <section id="featured-hotels" class="py-24 bg-[var(--surface-alt)]">
        <div class="mx-auto max-w-7xl px-6">
            <div class="section-header mb-14">
                <span class="inline-flex items-center gap-2 rounded-full border border-[var(--gold)]/20 bg-[var(--gold)]/8 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[var(--gold)]">
                    <i class="bi bi-star-fill text-[10px]"></i>
                    {{ __('auth.home_featured_title') }}
                </span>
                <h2 class="mt-5">{{ __('auth.home_featured_title') }}</h2>
                <p class="mx-auto mt-4 max-w-2xl">{{ __('auth.home_featured_subtitle') }}</p>
                <div class="divider-gold mx-auto mt-6"></div>
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
                <div class="empty-state">
                    <i class="bi bi-building"></i>
                    <h3>{{ __('auth.hotels_no_results') }}</h3>
                </div>
            @endif

            <div class="mt-12 text-center reveal">
                <a href="{{ route('frontend.hotels') }}" class="btn-outline btn-pill">
                    {{ __('auth.home_view_all') }} {{ __('auth.nav_hotels') }}
                    <i class="bi bi-arrow-right text-base"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Rooms Showcase --}}
    <section class="py-24 bg-[var(--surface)]">
        <div class="mx-auto max-w-7xl px-6">
            <div class="section-header mb-14">
                <span class="inline-flex items-center gap-2 rounded-full border border-[var(--gold)]/20 bg-[var(--gold)]/8 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-[var(--gold)]">
                    <i class="bi bi-door-open-fill text-[10px]"></i>
                    {{ __('auth.home_rooms_title') }}
                </span>
                <h2 class="mt-5">{{ __('auth.home_rooms_title') }}</h2>
                <p class="mx-auto mt-4 max-w-2xl">{{ __('auth.home_rooms_subtitle') }}</p>
                <div class="divider-gold mx-auto mt-6"></div>
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
                <div class="empty-state">
                    <i class="bi bi-door-open"></i>
                    <h3>{{ __('auth.hotels_no_results') }}</h3>
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
