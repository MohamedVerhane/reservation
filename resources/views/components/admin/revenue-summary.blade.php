@props(['revenue'])

@php
    $growth = $revenue['growth'];
    $isPositive = $growth >= 0;
@endphp

<div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950">
                <i class="bi bi-currency-dollar text-sm text-emerald-600 dark:text-emerald-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.dashboard.revenue_summary') }}</h3>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('admin.dashboard.view_all') }}</a>
    </div>

    <div class="p-6">
        {{-- Total --}}
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.total_revenue') }}</p>
            <p class="mt-1 text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ __('auth.notif_currency_format', ['amount' => number_format($revenue['total'], 2)]) }}</p>
            @if ($growth != 0)
                <div class="mt-1 flex items-center gap-1">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold {{ $isPositive ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        <i class="bi {{ $isPositive ? 'bi-arrow-up' : 'bi-arrow-down' }} text-[10px]"></i>
                        {{ abs($growth) }}%
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.vs_last_month') }}</span>
                </div>
            @endif
        </div>

        {{-- Monthly breakdown --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl bg-amber-50/80 dark:bg-slate-800/50 p-4">
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.this_month') }}</p>
                <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ __('auth.notif_currency_format', ['amount' => number_format($revenue['this_month'], 2)]) }}</p>
            </div>
            <div class="rounded-xl bg-orange-50/80 dark:bg-slate-800/50 p-4">
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.last_month') }}</p>
                <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ __('auth.notif_currency_format', ['amount' => number_format($revenue['last_month'], 2)]) }}</p>
            </div>
        </div>

        {{-- Visual bar --}}
        <div class="mt-6">
            <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 mb-1.5">
                <span>{{ __('admin.dashboard.monthly_target') }}</span>
                <span class="font-semibold text-slate-600 dark:text-slate-300">
                    {{ $revenue['last_month'] > 0 ? number_format(($revenue['this_month'] / max($revenue['last_month'], 1)) * 100, 0) : ($revenue['this_month'] > 0 ? '100' : '0') }}%
                </span>
            </div>
            <div class="h-2.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                @php
                    $pct = $revenue['last_month'] > 0
                        ? min(($revenue['this_month'] / max($revenue['last_month'], 1)) * 100, 100)
                        : ($revenue['this_month'] > 0 ? 100 : 0);
                @endphp
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-1000"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>
</div>
