@props(['messages' => []])
@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-3 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/30 px-4 py-3 text-sm text-red-700 dark:text-red-400']) }}>
        <div class="flex items-start gap-2.5">
            <i class="bi bi-exclamation-circle-fill mt-0.5 text-red-500"></i>
            <ul class="space-y-0.5">
                @foreach ((array) $messages as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
