@props(['title' => null, 'text' => null, 'buttonText' => null, 'buttonUrl' => '#'])

<section class="relative w-full overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-[#b3261e] via-[#7f1610] to-[#0e8a5d]"></div>

    <div class="pointer-events-none absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.4) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -end-16 -top-16 h-48 w-48 animate-float rounded-full bg-[#34d399]/10 blur-2xl"></div>
        <div class="absolute -bottom-8 -start-8 h-36 w-36 animate-float-delayed rounded-full bg-[#e35d4e]/15 blur-2xl"></div>
    </div>

    <div class="relative mx-auto max-w-3xl px-6 py-16 text-center sm:py-20">
        <h2 class="text-3xl font-extrabold text-white drop-shadow-sm sm:text-4xl">
            {{ $title ?? __('auth.home_cta_title') }}
        </h2>

        @if($text)
            <p class="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-white/80">
                {{ $text }}
            </p>
        @endif

        <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a
                href="{{ $buttonUrl }}"
                class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-3.5 text-sm font-bold text-[#7f1610] shadow-xl transition-all duration-300 hover:-translate-y-0.5 hover:scale-105 hover:shadow-2xl"
            >
                {{ $buttonText ?? __('auth.home_cta_button') }}
                <i data-lucide="arrow-right" class="h-4 w-4 rtl:rotate-180"></i>
            </a>
        </div>
    </div>
</section>
