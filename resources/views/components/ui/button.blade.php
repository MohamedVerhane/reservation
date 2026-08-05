@props([
    'variant' => 'default',
    'size' => 'default',
    'type' => 'button',
    'shine' => false,
])

@php
    $variants = [
        'default' => 'bg-primary text-primary-foreground shadow-sm hover:bg-primary/90',
        'secondary' => 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
        'outline' => 'border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
        'ghost' => 'hover:bg-accent hover:text-accent-foreground',
        'destructive' => 'bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90',
        'link' => 'text-primary underline-offset-4 hover:underline',
        'gold' => 'bg-gradient-to-br from-primary to-primary/80 text-primary-foreground shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5',
        '3d' => 'btn3d bg-gradient-to-b from-primary to-primary/85 text-primary-foreground shadow-[0_5px_0_#5d0f0a,0_8px_16px_rgba(179,38,30,0.35)] active:translate-y-[3px] active:shadow-[0_2px_0_#5d0f0a,0_4px_8px_rgba(179,38,30,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_0_#5d0f0a,0_10px_20px_rgba(179,38,30,0.4)]',
        '3d-green' => 'btn3d bg-gradient-to-b from-[#0e8a5d] to-[#065f46] text-white shadow-[0_5px_0_#043c2c,0_8px_16px_rgba(14,138,93,0.35)] active:translate-y-[3px] active:shadow-[0_2px_0_#043c2c,0_4px_8px_rgba(14,138,93,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_0_#043c2c,0_10px_20px_rgba(14,138,93,0.4)]',
    ];

    $sizes = [
        'sm' => 'h-9 rounded-md px-3 text-xs',
        'default' => 'h-10 rounded-md px-4 py-2',
        'lg' => 'h-11 rounded-lg px-8 text-base',
        'icon' => 'h-10 w-10',
        'icon-sm' => 'h-9 w-9',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:ring-offset-1 disabled:pointer-events-none disabled:opacity-50 ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']) . ($shine ? ' btn3d' : '');
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>{{ $slot }}</button>
@endif
