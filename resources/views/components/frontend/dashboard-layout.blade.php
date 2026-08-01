@props(['title' => __('admin.nav.dashboard')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.app_name') }} — {{ $title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|fraunces:400,500,600,700,900|amiri:400,700|cairo:400,600,700,900" rel="stylesheet" />
    <script>
        (function() { var t = localStorage.getItem('theme'); if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="min-h-screen bg-[var(--surface)] text-[var(--text-primary)] antialiased">

<div class="min-h-screen">
    <nav class="sticky top-0 z-50 bg-[var(--surface)]/80 backdrop-blur-xl border-b border-[var(--border-light)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--gold)] text-white text-sm font-bold">
                        <i class="bi bi-gem"></i>
                    </span>
                    <span class="text-lg font-extrabold tracking-tight text-[var(--text-primary)]">{{ __('auth.app_name') }}</span>
                </a>
                <div class="hidden md:flex items-center gap-3">
                    <x-language-switcher class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md border border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all duration-200" />
                    <a href="{{ route('home') }}" class="inline-flex btn-ghost btn-sm text-[var(--text-muted)] cursor-pointer">
                        <i class="bi bi-house"></i> {{ __('auth.back_to_site') }}
                    </a>
                    <x-theme-toggle class="w-8 h-8 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />
                    <x-notifications-dropdown viewAllRoute="customer.notifications" :limit="5" />
                </div>
                <div class="flex md:hidden items-center gap-2">
                    <a href="{{ route('home') }}" class="btn-icon btn-ghost text-[var(--text-muted)] cursor-pointer">
                        <i class="bi bi-house"></i>
                    </a>
                    <button type="button" data-dashboard-sidebar-toggle class="btn-icon btn-ghost cursor-pointer">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row gap-8">

            <aside id="dashboard-sidebar" class="fixed md:sticky top-20 md:top-24 z-40 md:z-auto inset-y-0 start-0 md:start-auto w-72 md:w-64 flex-shrink-0 transition-transform duration-300 overflow-y-auto overscroll-contain">
                <div class="card-flat p-4 mb-4 flex items-center gap-2 md:hidden">
                    <x-language-switcher class="flex-1 inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />
                    <x-theme-toggle class="w-8 h-8 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />
                </div>
                <div class="card-flat p-6 mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="avatar avatar-lg">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-[var(--text-primary)]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[var(--text-muted)]">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <nav class="card-flat p-2 space-y-1">
                    @php
                        $notifUnread = auth()->user()->unreadNotifications()->count();
                        $navItems = [
                            ['route' => 'customer.dashboard', 'icon' => 'bi-grid-1x2', 'label' => __('auth.cd_overview')],
                            ['route' => 'customer.reservations', 'icon' => 'bi-calendar-check', 'label' => __('auth.cd_reservations')],
                            ['route' => 'customer.history', 'icon' => 'bi-clock-history', 'label' => __('auth.cd_history')],
                            ['route' => 'customer.reviews', 'icon' => 'bi-star', 'label' => __('auth.cd_reviews')],
                            ['route' => 'customer.favorites', 'icon' => 'bi-heart', 'label' => __('auth.cd_favorites')],
                            ['route' => 'customer.notifications', 'icon' => 'bi-bell', 'label' => __('auth.notif_title'), 'badge' => $notifUnread > 0 ? $notifUnread : null],
                            ['route' => 'customer.invoices', 'icon' => 'bi-receipt', 'label' => __('auth.cd_invoices')],
                            ['route' => 'customer.profile', 'icon' => 'bi-person', 'label' => __('auth.cd_profile')],
                        ];
                    @endphp
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 cursor-pointer {{ request()->routeIs($item['route']) ? 'bg-[var(--gold)]/8 text-[var(--gold)] shadow-sm' : 'text-[var(--text-secondary)] hover:bg-[var(--surface-alt)] hover:text-[var(--gold)]' }}">
                            <i class="bi {{ $item['icon'] }} text-lg {{ request()->routeIs($item['route']) ? 'text-[var(--gold)]' : '' }}"></i>
                            {{ $item['label'] }}
                            @if(isset($item['badge']) && $item['badge'])
                                <span class="ms-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white px-1">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
                            @elseif(request()->routeIs($item['route']))
                                <span class="ms-auto w-1.5 h-1.5 rounded-full bg-[var(--gold)]"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </aside>

            <div data-dashboard-sidebar-overlay class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm md:hidden hidden"></div>

            <main class="flex-1 min-w-0">
                {{ $slot }}
            </main>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sidebarToggle = document.querySelector('[data-dashboard-sidebar-toggle]');
            var sidebar = document.getElementById('dashboard-sidebar');
            var sidebarOverlay = document.querySelector('[data-dashboard-sidebar-overlay]');
            function openSidebar() {
                sidebar.classList.add('mobile-open');
                sidebarOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function () {
                    if (sidebar.classList.contains('mobile-open')) closeSidebar();
                    else openSidebar();
                });
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) { e.target.classList.add('revealed'); obs.unobserve(e.target); }
                });
            }, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });
            document.querySelectorAll('.reveal, .animate-on-scroll').forEach(function(el) { obs.observe(el); });
        });

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-theme-toggle]');
            if (!btn) return;
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            document.querySelectorAll('[data-theme-toggle]').forEach(function(b) {
                var s = b.querySelector('.icon-sun'), m = b.querySelector('.icon-moon');
                if (s) s.style.display = isDark ? 'none' : 'inline';
                if (m) m.style.display = isDark ? 'inline' : 'none';
            });
        });

        (function() {
            var dk = document.documentElement.classList.contains('dark');
            document.querySelectorAll('[data-theme-toggle]').forEach(function(b) {
                var s = b.querySelector('.icon-sun'), m = b.querySelector('.icon-moon');
                if (s) s.style.display = dk ? 'none' : 'inline';
                if (m) m.style.display = dk ? 'inline' : 'none';
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>