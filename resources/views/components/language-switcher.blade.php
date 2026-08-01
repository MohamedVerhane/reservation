@php $locale = app()->getLocale(); @endphp
@foreach(['en', 'ar', 'fr'] as $code)
    @php $langLabel = __('auth.' . $code) @endphp
    @if($locale !== $code)
        <a href="{{ route('language.switch', ['locale' => $code]) }}"
            {{ $attributes->merge(['class' => 'cursor-pointer transition-all duration-200']) }}>
            {{ $langLabel }}
        </a>
    @endif
@endforeach