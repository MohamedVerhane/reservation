@props(['title', 'subtitle' => null, 'centered' => true])

<section class="relative w-full overflow-hidden bg-gradient-to-br from-[var(--surface)] via-[var(--surface)] to-[var(--gold)]/5">
    {{-- Decorative floating orbs --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-20 -start-20 h-64 w-64 animate-float rounded-full bg-[var(--gold)]/15 blur-3xl"></div>
        <div class="absolute top-1/3 end-10 h-48 w-48 animate-float-delayed rounded-full bg-indigo-400/10 blur-3xl"></div>
        <div class="absolute bottom-0 start-1/3 h-52 w-52 animate-float-slow rounded-full bg-[var(--gold)]/8 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 32px 32px;"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 py-16 sm:py-20 {{ $centered ? 'text-center' : '' }}">
        @if($centered)
            <nav class="mb-6 animate-fade-in-down" aria-label="{{ __('common.breadcrumb') }}">
                <ol class="flex items-center justify-center gap-2 text-sm text-[var(--text-muted)]">
                    <li>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 transition-colors hover:text-[var(--gold)]">
                            <i class="bi bi-house-door-fill text-xs"></i>
                            {{ __('auth.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-[10px] text-[var(--text-muted)]"></i></li>
                    <li class="font-semibold text-[var(--text-primary)]">{{ $title }}</li>
                </ol>
            </nav>
        @endif

        <h1 class="animate-fade-in-up text-4xl font-extrabold tracking-tight text-[var(--text-primary)] sm:text-5xl lg:text-6xl">
            {{ $title }}
        </h1>

        @if($subtitle)
            <p class="animate-fade-in-up delay-100 mx-auto mt-4 max-w-2xl text-lg text-[var(--text-secondary)] leading-relaxed">
                {{ $subtitle }}
            </p>
        @endif

        @if(isset($slots->actions) && $slots->actions)
            <div class="animate-fade-in-up delay-200 mt-8">
                {{ $actions }}
            </div>
        @endif
    </div>
</section>
