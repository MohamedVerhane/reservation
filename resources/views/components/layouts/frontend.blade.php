@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.app_name') }} — {{ $title ?? __('auth.home') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|fraunces:400,500,600,700,900|amiri:400,700|cairo:400,600,700,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        (function() {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches))
                document.documentElement.classList.add('dark');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="min-h-screen bg-background text-foreground antialiased">

    <x-frontend.navbar />

    <main data-ajax-page class="pt-16 lg:pt-[4.25rem]">
        {{ $slot }}
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) { e.target.classList.add('revealed'); obs.unobserve(e.target); }
                });
            }, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });
            document.querySelectorAll('.reveal').forEach(function(el) { obs.observe(el); });
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

    <div class="ajax-toast-container" aria-live="polite"></div>
</body>
</html>