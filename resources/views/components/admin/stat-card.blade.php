@props(['title', 'value', 'icon', 'color' => 'indigo', 'change' => null, 'changeLabel' => null])

@php
    $colorClasses = match ($color) {
        'indigo' => 'bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400',
        'emerald' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400',
        'amber' => 'bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400',
        'blue' => 'bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400',
        'rose' => 'bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400',
        'purple' => 'bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400',
        default => 'bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400',
    };

    $iconColorClasses = match ($color) {
        'indigo' => 'text-indigo-600 dark:text-indigo-400',
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'blue' => 'text-blue-600 dark:text-blue-400',
        'rose' => 'text-rose-600 dark:text-rose-400',
        'purple' => 'text-purple-600 dark:text-purple-400',
        default => 'text-indigo-600 dark:text-indigo-400',
    };

    $cardTint = match ($color) {
        'indigo' => 'bg-indigo-50/60',
        'emerald' => 'bg-emerald-50/60',
        'amber' => 'bg-amber-50/60',
        'blue' => 'bg-blue-50/60',
        'rose' => 'bg-rose-50/60',
        'purple' => 'bg-purple-50/60',
        default => 'bg-indigo-50/60',
    };
@endphp

<div class="group relative rounded-2xl border border-slate-200/80 dark:border-slate-800/80 {{ $cardTint }} dark:bg-slate-900 p-6 shadow-sm transition-all duration-300 hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-0.5">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $title }}</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $value }}</p>

            @if ($change !== null)
                @php
                    $isPositive = $change >= 0;
                @endphp
                <div class="mt-2 flex items-center gap-1">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold {{ $isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        <i class="bi {{ $isPositive ? 'bi-arrow-up' : 'bi-arrow-down' }} text-[10px]"></i>
                        {{ abs($change) }}%
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $changeLabel ?? __('admin.dashboard.vs_last_month') }}</span>
                </div>
            @endif
        </div>
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $colorClasses }} transition-transform duration-300 group-hover:scale-110">
            <i class="bi {{ $icon }} text-xl {{ $iconColorClasses }}"></i>
        </span>
    </div>
</div>
