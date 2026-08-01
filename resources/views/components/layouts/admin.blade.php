@props(['title' => null, 'active' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.app_name') }} — {{ $title ?? __('admin.nav.dashboard') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|fraunces:400,500,600,700,900|amiri:400,700|cairo:400,600,700,900" rel="stylesheet" />
    <script>
        (function() { var t = localStorage.getItem('theme'); if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @stack('head')
</head>

<body class="min-h-screen bg-[var(--surface-alt)] text-[var(--text-primary)] antialiased">
    <div data-sidebar-overlay class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden lg:hidden"></div>

    <div class="flex min-h-screen">
        <x-admin.sidebar :active="$active" />

        <div class="flex-1 flex flex-col min-w-0 lg:ms-64">
            <header class="sticky top-0 z-30 border-b border-[var(--border)] bg-[var(--surface)]/80 backdrop-blur-lg">
                <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" data-sidebar-toggle
                            class="lg:hidden w-9 h-9 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] flex items-center justify-center hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all cursor-pointer">
                            <i class="bi bi-list text-base"></i>
                        </button>
                        <h1 class="text-base font-bold text-[var(--text-primary)] hidden sm:block">{{ $title }}</h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-language-switcher class="hidden sm:inline-flex px-2.5 py-1 text-xs font-semibold rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />

                        <x-theme-toggle class="w-9 h-9 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />

                        <x-notifications-dropdown viewAllRoute="admin.notifications" :limit="8" />

                        <div class="relative" x-data="{ show: false }" @click.away="show = false">
                            <button @click="show = !show" type="button"
                                class="flex items-center gap-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-1.5 text-sm font-medium hover:border-[var(--gold)] transition-all cursor-pointer">
                                <span class="w-6 h-6 rounded-full bg-[var(--gold)] text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="hidden sm:inline text-[var(--text-primary)]">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down text-xs text-[var(--text-muted)]"></i>
                            </button>

                            <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 rounded-xl border border-[var(--border)] bg-[var(--surface-elevated)] shadow-xl py-1 z-50">
                                <div class="px-4 py-2 border-b border-[var(--border-light)]">
                                    <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ url('/') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:bg-[var(--surface-alt)] hover:text-[var(--gold)] transition-colors cursor-pointer">
                                    <i class="bi bi-house-door"></i>{{ __('auth.welcome') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors cursor-pointer">
                                        <i class="bi bi-box-arrow-right"></i>{{ __('auth.sign_out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 min-w-0 p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.querySelector('[data-sidebar-toggle]');            var sidebar = document.querySelector('[data-sidebar]');
            var overlay = document.querySelector('[data-sidebar-overlay]');
            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                sidebar?.classList.add('translate-x-0');
                overlay?.classList.remove('hidden');
                overlay?.classList.add('block');
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                sidebar?.classList.remove('translate-x-0');
                sidebar?.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
                overlay?.classList.remove('block');
                document.body.style.overflow = '';
            }
            if (toggle) {
                toggle.addEventListener('click', function() {
                    if (sidebar?.classList.contains('translate-x-0')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
        });
    </script>
    @endpush
    @stack('scripts')

    <div class="ajax-toast-container" aria-live="polite"></div>
</body>
</html>