@php $locale = app()->getLocale(); @endphp

<nav x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50 border-b border-border/60 bg-background/80 backdrop-blur-xl">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 font-serif text-sm font-black leading-none text-white shadow-sm shadow-primary/25">MD</span>
                <span class="text-lg font-extrabold tracking-tight text-foreground">{{ __('auth.app_name') }}</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden items-center gap-1 lg:flex">
                @foreach([
                    ['route' => 'home', 'name' => __('auth.home'), 'check' => 'home'],
                    ['route' => 'frontend.search', 'name' => __('auth.nav_hotels'), 'check' => 'frontend.search*|frontend.hotels*'],
                    ['route' => 'frontend.gallery', 'name' => __('auth.gallery'), 'check' => 'frontend.gallery*'],
                    ['route' => 'frontend.about', 'name' => __('auth.about'), 'check' => 'frontend.about*'],
                    ['route' => 'frontend.contact', 'name' => __('auth.contact'), 'check' => 'frontend.contact*'],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                       class="rounded-lg px-3.5 py-2 text-sm font-medium transition-colors duration-200
                       {{ request()->routeIs($item['check']) ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                        {{ $item['name'] }}
                    </a>
                @endforeach
            </div>

            {{-- Desktop right side --}}
            <div class="hidden items-center gap-2 lg:flex">
                <x-language-switcher class="inline-flex items-center rounded-md px-2.5 py-1.5 text-xs font-semibold border border-border text-muted-foreground hover:border-primary hover:text-primary transition-all duration-200" />

                <x-theme-toggle class="btn-icon btn-ghost text-muted-foreground hover:text-primary" />

                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex btn-ghost btn-sm text-muted-foreground">
                            <i data-lucide="layout-grid" class="h-4 w-4"></i> {{ __('auth.dashboard') }}
                        </a>
                    @endif
                    <a href="{{ route('frontend.booking.my-reservations') }}" class="inline-flex btn-ghost btn-sm text-muted-foreground">
                        <i data-lucide="bookmark" class="h-4 w-4"></i> {{ __('auth.booking_my_reservations') }}
                    </a>

                    <x-notifications-dropdown viewAllRoute="customer.notifications" class="btn-ghost text-muted-foreground" />

                    {{-- User menu --}}
                    <div class="relative" x-data="{ show: false }" @click.outside="show = false">
                        <button @click="show = !show" class="flex items-center gap-2 rounded-full p-1 pe-2 hover:bg-muted transition-colors">
                            <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden xl:inline text-sm font-medium text-foreground">{{ auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-muted-foreground transition-transform" :class="show && 'rotate-180'"></i>
                        </button>

                        <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute {{ $locale === 'ar' ? 'start-0' : 'end-0' }} mt-2 w-56 rounded-xl border border-border bg-card shadow-xl py-1 z-50">
                            <div class="border-b border-border px-4 py-3">
                                <p class="text-sm font-semibold text-foreground">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-muted-foreground">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ url('/profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
                                <i data-lucide="user" class="h-4 w-4"></i> {{ __('auth.profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-destructive hover:bg-destructive/10 transition-colors">
                                    <i data-lucide="log-out" class="h-4 w-4"></i> {{ __('auth.sign_out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex btn-ghost btn-sm text-muted-foreground">{{ __('auth.login') }}</a>
                    <x-ui.button href="{{ route('register') }}" variant="gold" size="sm">
                        <i data-lucide="sparkles" class="h-4 w-4"></i> {{ __('auth.book_now') }}
                    </x-ui.button>
                @endauth
            </div>

            {{-- Mobile: theme + hamburger --}}
            <div class="flex items-center gap-1 lg:hidden">
                <x-theme-toggle class="btn-icon btn-ghost text-muted-foreground" />
                <button @click="open = !open" class="btn-icon btn-ghost" aria-label="{{ __('auth.menu') }}">
                    <i data-lucide="menu" class="h-5 w-5" :class="open && 'hidden'"></i>
                    <i data-lucide="x" class="h-5 w-5 hidden" :class="open && 'block'"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile sheet --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-[70] lg:hidden">
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="open = false" class="absolute inset-0 bg-foreground/50 backdrop-blur-sm"></div>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="{{ $locale === 'ar' ? '-translate-x-full' : 'translate-x-full' }}"
                 x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="{{ $locale === 'ar' ? '-translate-x-full' : 'translate-x-full' }}"
                 class="absolute inset-y-0 end-0 flex w-80 max-w-[85vw] flex-col border-s border-border bg-card shadow-2xl">
                <div class="flex items-center justify-between border-b border-border px-4 py-3.5">
                    <span class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 font-serif text-sm font-black leading-none text-white">MD</span>
                        <span class="text-base font-extrabold text-foreground">{{ __('auth.app_name') }}</span>
                    </span>
                    <button @click="open = false" class="btn-icon btn-ghost text-muted-foreground" aria-label="{{ __('auth.close') }}">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-3 py-4">
                    <div class="flex flex-col gap-1">
                        @foreach([
                            ['route' => 'home', 'name' => __('auth.home'), 'icon' => 'house', 'check' => 'home'],
                            ['route' => 'frontend.search', 'name' => __('auth.nav_hotels'), 'icon' => 'building-2', 'check' => 'frontend.search*|frontend.hotels*'],
                            ['route' => 'frontend.gallery', 'name' => __('auth.gallery'), 'icon' => 'images', 'check' => 'frontend.gallery*'],
                            ['route' => 'frontend.about', 'name' => __('auth.about'), 'icon' => 'info', 'check' => 'frontend.about*'],
                            ['route' => 'frontend.contact', 'name' => __('auth.contact'), 'icon' => 'mail', 'check' => 'frontend.contact*'],
                        ] as $item)
                            <a href="{{ route($item['route']) }}"
                               @click="open = false"
                               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors
                               {{ request()->routeIs($item['check']) ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="h-4.5 w-4.5"></i> {{ $item['name'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="my-3 h-px bg-border"></div>

                    <div class="flex items-center gap-2 px-4 py-2">
                        @foreach(['en', 'ar', 'fr'] as $code)
                            @if($locale !== $code)
                                <a href="{{ route('language.switch', ['locale' => $code]) }}" @click="open = false"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-muted-foreground hover:border-primary hover:text-primary transition-all">
                                    {{ __('auth.' . $code) }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    <div class="my-3 h-px bg-border"></div>

                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                            <a href="{{ route('admin.dashboard') }}" @click="open = false" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                                <i data-lucide="layout-grid" class="h-4.5 w-4.5"></i> {{ __('auth.dashboard') }}
                            </a>
                        @endif
                        <a href="{{ route('frontend.booking.my-reservations') }}" @click="open = false" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                            <i data-lucide="bookmark" class="h-4.5 w-4.5"></i> {{ __('auth.booking_my_reservations') }}
                        </a>
                        <a href="{{ route('customer.notifications') }}" @click="open = false" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                            <i data-lucide="bell" class="h-4.5 w-4.5"></i> {{ __('notifications.title') }}
                        </a>
                        <a href="{{ url('/profile') }}" @click="open = false" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                            <i data-lucide="user" class="h-4.5 w-4.5"></i> {{ __('auth.profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full rounded-lg px-4 py-3 text-sm font-medium text-destructive hover:bg-destructive/10">
                                <i data-lucide="log-out" class="h-4.5 w-4.5"></i> {{ __('auth.sign_out') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" @click="open = false" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                            <i data-lucide="log-in" class="h-4.5 w-4.5"></i> {{ __('auth.login') }}
                        </a>
                        <div class="px-4 pt-1">
                            <a href="{{ route('register') }}" @click="open = false" class="flex w-full items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-md shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/30">
                                {{ __('auth.book_now') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </template>
</nav>
