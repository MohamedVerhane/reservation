<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.app_name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches))
                document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|fraunces:400,500,600,700,900|amiri:400,700|cairo:400,600,700,900" rel="stylesheet" />
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950/40 text-slate-900 dark:text-slate-100 antialiased">

    {{-- ═══ Glass navbar ═══ --}}
    <header class="sticky top-0 z-50 border-b border-white/60 dark:border-slate-800/60 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
            <span class="flex items-center gap-2 text-lg font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent animate-fade-in-up" data-animate>
                <i class="bi bi-shield-lock-fill text-indigo-600 text-xl"></i>{{ __('auth.app_name') }}
            </span>

            {{-- Desktop controls --}}
            <div class="hidden md:flex items-center gap-2 animate-fade-in-up delay-100" data-animate>
                @if (app()->getLocale() !== 'en')
                    <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md">
                        <i class="bi bi-globe"></i>{{ __('auth.english') }}
                    </a>
                @endif
                @if (app()->getLocale() !== 'ar')
                    <a href="{{ route('language.switch', ['locale' => 'ar']) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md">
                        <i class="bi bi-globe"></i>{{ __('auth.arabic') }}
                    </a>
                @endif
                @if (app()->getLocale() !== 'fr')
                    <a href="{{ route('language.switch', ['locale' => 'fr']) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md">
                        <i class="bi bi-globe"></i>{{ __('auth.french') }}
                    </a>
                @endif
                <button type="button" data-theme-toggle class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 shadow-sm cursor-pointer transition-all duration-300 hover:border-indigo-300 hover:text-indigo-600 hover:shadow-md">
                    <i class="bi bi-sun-fill text-base icon-sun"></i>
                    <i class="bi bi-moon-fill text-base icon-moon" style="display:none"></i>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 ring-1 ring-black/5 dark:ring-white/5 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
                        <i class="bi bi-box-arrow-right"></i>{{ __('auth.sign_out') }}
                    </button>
                </form>
            </div>

            {{-- Mobile hamburger --}}
            <button type="button" data-menu-toggle class="inline-flex md:hidden items-center justify-center w-9 h-9 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 shadow-sm cursor-pointer transition-all duration-300 hover:border-indigo-300 hover:text-indigo-600 hover:shadow-md">
                <i class="bi bi-list text-lg menu-icon-open"></i>
                <i class="bi bi-x-lg text-lg menu-icon-close" style="display:none"></i>
            </button>
        </div>

            {{-- Mobile dropdown menu --}}
        <div data-mobile-menu class="md:hidden hidden border-t border-white/40 dark:border-slate-800/40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl">
            <div class="flex flex-col gap-2 px-5 py-4">
                @if (app()->getLocale() !== 'en')
                    <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md">
                        <i class="bi bi-globe"></i>{{ __('auth.english') }}
                    </a>
                @endif
                @if (app()->getLocale() !== 'ar')
                    <a href="{{ route('language.switch', ['locale' => 'ar']) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md">
                        <i class="bi bi-globe"></i>{{ __('auth.arabic') }}
                    </a>
                @endif
                @if (app()->getLocale() !== 'fr')
                    <a href="{{ route('language.switch', ['locale' => 'fr']) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md">
                        <i class="bi bi-globe"></i>{{ __('auth.french') }}
                    </a>
                @endif
                <button type="button" data-theme-toggle class="inline-flex items-center gap-3 w-full rounded-xl border border-slate-200/80 dark:border-slate-700/80 bg-white/70 dark:bg-slate-800/70 px-4 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400 shadow-sm backdrop-blur-sm transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-500 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 hover:shadow-md cursor-pointer">
                    <i class="bi bi-sun-fill text-base icon-sun"></i>
                    <i class="bi bi-moon-fill text-base icon-moon" style="display:none"></i>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 ring-1 ring-black/5 dark:ring-white/5 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
                        <i class="bi bi-box-arrow-right"></i>{{ __('auth.sign_out') }}
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ═══ Content ═══ --}}
    <main class="mx-auto max-w-6xl px-5 py-16">
        <div class="gradient-border-card p-8 sm:p-10 animate-fade-in-up delay-200" data-animate>
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                    <i class="bi bi-person-check-fill text-xl"></i>
                </span>
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">{{ __('auth.authenticated') }}</p>
            </div>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ __('auth.home_welcome', ['name' => auth()->user()->name]) }}</h1>
            <p class="mt-3 text-slate-500 dark:text-slate-400">{{ __('auth.home_text') }}</p>
        </div>
    </main>
</body>

</html>
