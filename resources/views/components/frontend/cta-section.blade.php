@props(['title' => null, 'text' => null, 'buttonText' => null, 'buttonUrl' => '#'])

<section class="relative w-full overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-[var(--gold)] via-[var(--gold-dark)] to-[var(--gold)]"></div>

    <div class="pointer-events-none absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.4) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -end-16 -top-16 h-48 w-48 animate-float rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-8 -start-8 h-36 w-36 animate-float-delayed rounded-full bg-white/10 blur-2xl"></div>
    </div>

    <div class="relative mx-auto max-w-3xl px-6 py-16 text-center sm:py-20">
        <h2 class="text-3xl font-extrabold text-white sm:text-4xl drop-shadow-sm">
            {{ $title ?? __('auth.home_cta_title') }}
        </h2>

        @if($text)
            <p class="mx-auto mt-4 max-w-xl text-lg text-white/80 leading-relaxed">
                {{ $text }}
            </p>
        @endif

        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a
                href="{{ $buttonUrl }}"
                class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-3.5 text-sm font-bold text-[var(--gold-dark)] shadow-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:-translate-y-0.5"
            >
                {{ $buttonText ?? __('auth.home_cta_button') }}
                <i class="bi bi-arrow-right text-base transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>
    </div>
</section>
