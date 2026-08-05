@props(['as' => 'h3'])

<{{ $as }} {{ $attributes->merge(['class' => 'text-lg font-semibold leading-none tracking-tight']) }}>{{ $slot }}</{{ $as }}>
