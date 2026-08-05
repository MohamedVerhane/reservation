@props(['orientation' => 'horizontal'])

@if($orientation === 'vertical')
    <div {{ $attributes->merge(['class' => 'h-full w-px shrink-0 bg-border']) }}></div>
@else
    <div {{ $attributes->merge(['class' => 'h-px w-full shrink-0 bg-border']) }}></div>
@endif
