@php $locale = app()->getLocale(); @endphp

<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 border-b border-[var(--border-light)] bg-[var(--surface)]/80 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between lg:h-[4.25rem]">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--gold)] text-white text-sm font-bold">
                    <i class="bi bi-gem"></i>
                </span>
                <span class="text-lg font-extrabold tracking-tight text-[var(--text-primary)]">{{ __('auth.app_name') }}</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach([
                    ['route' => 'home', 'name' => __('auth.home'), 'check' => 'home'],
                    ['route' => 'frontend.search', 'name' => __('auth.nav_hotels'), 'check' => 'frontend.search*|frontend.hotels*'],
                    ['route' => 'frontend.gallery', 'name' => __('auth.gallery'), 'check' => 'frontend.gallery*'],
                    ['route' => 'frontend.about', 'name' => __('auth.about'), 'check' => 'frontend.about*'],
                    ['route' => 'frontend.contact', 'name' => __('auth.contact'), 'check' => 'frontend.contact*'],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition-all duration-200
                       {{ request()->routeIs($item['check']) ? 'bg-[var(--gold)]/8 text-[var(--gold)] font-semibold' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-alt)]' }}">
                        {{ $item['name'] }}
                    </a>
                @endforeach
            </div>

            {{-- Desktop right side (hidden on mobile) --}}
            <div class="hidden md:flex items-center gap-2">
                <x-language-switcher class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md border border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all duration-200" />

                <x-theme-toggle class="btn-icon btn-ghost text-[var(--text-muted)] hover:text-[var(--gold)]" />

                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex btn-ghost btn-sm text-[var(--text-muted)]">
                            <i class="bi bi-grid-1x2"></i> {{ __('auth.dashboard') }}
                        </a>
                    @endif
                    <a href="{{ route('frontend.booking.my-reservations') }}" class="inline-flex btn-ghost btn-sm text-[var(--text-muted)]">
                        <i class="bi bi-bookmark"></i> {{ __('auth.booking_my_reservations') }}
                    </a>

                    {{-- User menu --}}
                    <div class="relative" x-data="{ show: false }" @click.outside="show = false">
                        <button @click="show = !show" class="flex items-center gap-2 rounded-full p-1 pr-2 hover:bg-[var(--surface-alt)] transition-colors">
                            <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden lg:inline text-sm font-medium text-[var(--text-primary)]">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down text-xs text-[var(--text-muted)] transition-transform" :class="show && 'rotate-180'"></i>
                        </button>

                        <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute {{ $locale === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-56 rounded-xl border border-[var(--border)] bg-[var(--surface-elevated)] shadow-xl py-1 z-50">
                            <div class="px-4 py-3 border-b border-[var(--border-light)]">
                                <p class="text-sm font-semibold text-[var(--text-primary)]">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ url('/profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:bg-[var(--surface-alt)] hover:text-[var(--gold)] transition-colors">
                                <i class="bi bi-person"></i> {{ __('auth.profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                                    <i class="bi bi-box-arrow-right"></i> {{ __('auth.sign_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex btn-ghost btn-sm">{{ __('auth.login') }}</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-sm btn-pill">
                        {{ __('auth.book_now') }}
                    </a>
                @endauth
            </div>


        </div>
    </div>

    {{-- Mobile dropdown (all items here on mobile) --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-[var(--border-light)] bg-[var(--surface)] px-4 pb-4 pt-2">
        <div class="flex flex-col gap-0.5">

            {{-- Mobile nav links --}}
            @foreach([
                ['route' => 'home', 'name' => __('auth.home'), 'icon' => 'house-door', 'check' => 'home'],
                ['route' => 'frontend.search', 'name' => __('auth.nav_hotels'), 'icon' => 'building', 'check' => 'frontend.search*|frontend.hotels*'],
                ['route' => 'frontend.gallery', 'name' => __('auth.gallery'), 'icon' => 'image', 'check' => 'frontend.gallery*'],
                ['route' => 'frontend.about', 'name' => __('auth.about'), 'icon' => 'info-circle', 'check' => 'frontend.about*'],
                ['route' => 'frontend.contact', 'name' => __('auth.contact'), 'icon' => 'envelope', 'check' => 'frontend.contact*'],
            ] as $item)
                <a href="{{ route($item['route']) }}"
                   @click="open = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors
                   {{ request()->routeIs($item['check']) ? 'bg-[var(--gold)]/8 text-[var(--gold)]' : 'text-[var(--text-secondary)] hover:bg-[var(--surface-alt)]' }}">
                    <i class="bi bi-{{ $item['icon'] }}"></i> {{ $item['name'] }}
                </a>
            @endforeach

            <div class="divider my-2"></div>

            {{-- Mobile language + theme --}}
            <div class="flex items-center gap-2 px-4 py-2">
                @foreach(['en', 'ar', 'fr'] as $code)
                    @if($locale !== $code)
                        <a href="{{ route('language.switch', ['locale' => $code]) }}" @click="open = false"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all">
                            {{ __('auth.' . $code) }}
                        </a>
                    @endif
                @endforeach
                <x-theme-toggle class="ms-auto w-8 h-8 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />
            </div>

            <div class="divider my-2"></div>

            @auth
                @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                    <a href="{{ route('admin.dashboard') }}" @click="open = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-alt)]">
                        <i class="bi bi-grid-1x2"></i> {{ __('auth.dashboard') }}
                    </a>
                @endif
                <a href="{{ route('frontend.booking.my-reservations') }}" @click="open = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-alt)]">
                    <i class="bi bi-bookmark"></i> {{ __('auth.booking_my_reservations') }}
                </a>
                <a href="{{ url('/profile') }}" @click="open = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-alt)]">
                    <i class="bi bi-person"></i> {{ __('auth.profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20">
                        <i class="bi bi-box-arrow-right"></i> {{ __('auth.sign_out') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" @click="open = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--text-secondary)] hover:bg-[var(--surface-alt)]">
                    <i class="bi bi-box-arrow-in-right"></i> {{ __('auth.login') }}
                </a>
                <div class="px-4 pt-1">
                    <a href="{{ route('register') }}" @click="open = false" class="btn-primary btn-pill w-full text-center">{{ __('auth.book_now') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>