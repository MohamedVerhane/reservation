@props(['stats' => []])

@php
    $defaultStats = [
        ['key' => 'hotels', 'value' => $stats['hotels'] ?? 0, 'label' => __('auth.home_stats_hotels'), 'icon' => 'bi-building'],
        ['key' => 'rooms', 'value' => $stats['rooms'] ?? 0, 'label' => __('auth.home_stats_rooms'), 'icon' => 'bi-door-open'],
        ['key' => 'guests', 'value' => $stats['guests'] ?? 0, 'label' => __('auth.home_stats_guests'), 'icon' => 'bi-people'],
        ['key' => 'awards', 'value' => $stats['awards'] ?? 0, 'label' => __('auth.home_stats_awards'), 'icon' => 'bi-award'],
    ];
@endphp

<section class="w-full relative overflow-hidden bg-gradient-to-r from-[var(--gold)] via-[var(--gold-dark)] to-[var(--gold)]">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -end-20 -top-20 h-48 w-48 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -start-10 bottom-0 h-36 w-36 rounded-full bg-white/5 blur-2xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 py-14">
        <div class="rounded-3xl border border-white/15 bg-white/10 p-8 backdrop-blur-sm shadow-2xl">
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                @foreach($defaultStats as $index => $stat)
                    <div
                        class="flex flex-col items-center text-center reveal d{{ $index + 1 }}"
                    >
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 shadow-lg shadow-black/10">
                            <i class="{{ $stat['icon'] }} text-xl text-white/90"></i>
                        </div>
                        <span
                            class="text-3xl font-extrabold text-white sm:text-4xl tabular-nums"
                            data-count="{{ $stat['value'] }}"
                        >
                            {{ number_format($stat['value']) }}
                        </span>
                        <span class="mt-1.5 text-xs font-semibold uppercase tracking-wider text-white/70">
                            {{ $stat['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
