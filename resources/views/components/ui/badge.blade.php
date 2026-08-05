@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'border-transparent bg-primary/10 text-primary',
        'secondary' => 'border-transparent bg-secondary text-secondary-foreground',
        'outline' => 'border-border text-foreground',
        'destructive' => 'border-transparent bg-destructive/10 text-destructive',
        'success' => 'border-transparent bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    ];

    $classes = 'inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors ' . ($variants[$variant] ?? $variants['default']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
