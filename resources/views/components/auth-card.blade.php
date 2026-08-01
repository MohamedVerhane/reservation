@props(['heading', 'description' => null])

<div {{ $attributes->class(['gradient-border-card']) }}>
    <h2 class="text-xl font-bold text-[var(--text-primary)]">{{ $heading }}</h2>
    @if ($description)
        <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
