@props(['as' => 'div'])

@php
    $classes = 'rounded-xl border border-border bg-card text-card-foreground shadow-sm';
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</{{ $as }}>
