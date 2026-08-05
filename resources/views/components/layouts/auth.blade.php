@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.app_name') }} — {{ $title ?? 'Authentication' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|fraunces:400,500,600,700,900|amiri:400,700|cairo:400,600,700,900" rel="stylesheet" />
    <script>
        (function() { var t = localStorage.getItem('theme'); if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="min-h-screen bg-[var(--surface-alt)] text-[var(--text-primary)] antialiased">
    <div class="flex min-h-screen">
        <div class="hidden lg:flex lg:w-[480px] relative overflow-hidden bg-[var(--surface-elevated)] border-e border-[var(--border)] p-12 flex-col justify-between">
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: radial-gradient(circle at 1px 1px, var(--gold) 1px, transparent 0); background-size: 32px 32px;">
            </div>
            <div class="absolute top-1/3 -end-32 w-96 h-96 rounded-full bg-[var(--gold)]/5 blur-3xl"></div>
            <div class="absolute bottom-1/4 -start-32 w-80 h-80 rounded-full bg-[var(--gold)]/5 blur-3xl"></div>

            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--gold)] text-white">
                        <i class="bi bi-gem text-lg"></i>
                    </span>
                    <span class="text-lg font-extrabold tracking-tight">{{ __('auth.app_name') }}</span>
                </a>
            </div>

            <div class="relative z-10 max-w-sm">
                <h1 class="text-3xl font-extrabold leading-tight mb-4">{{ __('auth.hero_title') }}</h1>
                <p class="text-[var(--text-secondary)] leading-relaxed">{{ __('auth.hero_text') }}</p>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <div class="flex -space-x-2">
                    @foreach(range(1,4) as $i)
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[var(--gold)] to-[var(--gold-dark)] flex items-center justify-center text-[10px] font-bold text-white ring-2 ring-[var(--surface-elevated)]">
                            {{ chr(64 + $i) }}
                        </div>
                    @endforeach
                </div>
                <p class="text-sm text-[var(--text-muted)]">{{ __('auth.built_with') }}</p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center px-4 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <div class="flex items-center justify-between mb-8">
                    <a href="{{ url('/') }}" class="lg:hidden inline-flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--gold)] text-white text-sm">
                            <i class="bi bi-gem"></i>
                        </span>
                        <span class="text-base font-extrabold">{{ __('auth.app_name') }}</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <x-language-switcher class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--gold)] hover:text-[var(--gold)] transition-all" />
                        <x-theme-toggle class="w-8 h-8 rounded-lg border border-[var(--border)]" />
                    </div>
                </div>

                {{ $slot }}
            </div>
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
</body>
</html>