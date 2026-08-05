@props(['stats' => []])

@php
    $defaultStats = [
        ['key' => 'hotels', 'value' => $stats['hotels'] ?? 0, 'label' => __('auth.home_stats_hotels'), 'icon' => 'building-2'],
        ['key' => 'rooms', 'value' => $stats['rooms'] ?? 0, 'label' => __('auth.home_stats_rooms'), 'icon' => 'door-open'],
        ['key' => 'guests', 'value' => $stats['guests'] ?? 0, 'label' => __('auth.home_stats_guests'), 'icon' => 'users'],
        ['key' => 'awards', 'value' => $stats['awards'] ?? 0, 'label' => __('auth.home_stats_awards'), 'icon' => 'award'],
    ];
@endphp

<section class="relative w-full overflow-hidden bg-gradient-to-br from-[#b3261e] via-[#7f1610] to-[#0e8a5d]">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -end-20 -top-20 h-48 w-48 rounded-full bg-[#34d399]/10 blur-3xl animate-glow-pulse"></div>
        <div class="absolute -start-10 bottom-0 h-36 w-36 rounded-full bg-[#e35d4e]/15 blur-2xl animate-glow-pulse" style="animation-delay: 1.5s"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-6 py-14">
        <div class="rounded-3xl border border-white/15 bg-white/10 p-8 shadow-2xl backdrop-blur-sm">
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                @foreach($defaultStats as $index => $stat)
                    <div class="reveal d{{ $index + 1 }} flex flex-col items-center text-center">
                        <div class="preserve-3d mb-4 flex h-14 w-14 animate-float-3d items-center justify-center rounded-2xl bg-white/15 shadow-lg shadow-black/10" style="animation-delay: {{ $index * 0.6 }}s">
                            <i data-lucide="{{ $stat['icon'] }}" class="h-6 w-6 text-white/90"></i>
                        </div>
                        <span
                            class="text-3xl font-extrabold tabular-nums text-white sm:text-4xl"
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
