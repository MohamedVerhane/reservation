<x-layouts.frontend :title="__('meta.about')">
    <x-frontend.page-hero :title="__('about.title')" :subtitle="__('about.subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-20 reveal">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative rounded-3xl overflow-hidden h-96 lg:h-[480px]">
                <div class="absolute inset-0 bg-gradient-to-br from-[var(--gold)] to-[var(--gold-dark)]"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="bi bi-building text-white/20 text-[14rem]"></i>
                </div>
                <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-[var(--text-primary)] mb-6">
                    {{ __('about.story_title') }}
                </h2>
                <p class="text-[var(--text-secondary)] leading-relaxed text-lg mb-6">
                    {{ __('about.story_p1') }}
                </p>
                <p class="text-[var(--text-secondary)] leading-relaxed text-lg">
                    {{ __('about.story_p2') }}
                </p>
            </div>
        </div>
    </section>

    <section class="w-full bg-[var(--surface-alt)] py-20">
        <div class="max-w-3xl mx-auto px-6 text-center reveal">
            <div class="flex justify-center mb-8">
                <div class="w-24 h-24 rounded-full bg-[var(--gold)]/10 flex items-center justify-center">
                    <i class="bi bi-bullseye text-[var(--gold)] text-4xl"></i>
                </div>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-[var(--text-primary)] mb-6">
                {{ __('about.mission_title') }}
            </h2>
            <p class="text-[var(--text-secondary)] leading-relaxed text-lg">
                {{ __('about.mission_text') }}
            </p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="section-header">
            <h2>{{ __('about.values_title') }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['icon' => 'bi-trophy', 'color' => 'var(--gold)', 'key' => 'excellence'],
                ['icon' => 'bi-patch-check', 'color' => '#6366f1', 'key' => 'authenticity'],
                ['icon' => 'bi-leaf', 'color' => '#059669', 'key' => 'sustainability'],
                ['icon' => 'bi-heart', 'color' => '#e11d48', 'key' => 'warmth'],
            ] as $index => $value)
                <div class="card p-8 text-center reveal d{{ $index + 1 }}">
                    <div class="flex justify-center mb-5">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: {{ $value['color'] }}15">
                            <i class="bi {{ $value['icon'] }} text-2xl" style="color: {{ $value['color'] }}"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--text-primary)] mb-3">
                        {{ __('about.value_' . $value['key'] . '_title') }}
                    </h3>
                    <p class="text-[var(--text-secondary)] text-sm leading-relaxed">
                        {{ __('about.value_' . $value['key'] . '_desc') }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="section-header">
            <h2>{{ __('about.team_title') }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($team as $index => $member)
                <div class="card p-8 text-center reveal d{{ $index + 1 }}">
                    <div class="flex justify-center mb-5">
                        <div class="avatar avatar-lg w-24 h-24 text-3xl">
                            <i class="{{ $member['icon'] }}"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-[var(--text-primary)]">
                        {{ $member['name'] }}
                    </h3>
                    <p class="text-sm text-[var(--gold)] mt-1">
                        {{ $member['role'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <x-frontend.cta-section />
</x-layouts.frontend>
