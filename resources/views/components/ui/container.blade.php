@props(['size' => 'default'])

@php
    $sizes = [
        'default' => 'mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8',
        'sm' => 'mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8',
        'full' => 'w-full',
    ];
@endphp

<div {{ $attributes->merge(['class' => $sizes[$size] ?? $sizes['default']]) }}>{{ $slot }}</div>
