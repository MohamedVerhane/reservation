@props(['bookings'])

@php
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
        'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
        'checked_in' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
        'checked_out' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
        'cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400',
    ];
@endphp

<div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                <i class="bi bi-calendar-check text-sm text-indigo-600 dark:text-indigo-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.dashboard.latest_bookings') }}</h3>
        </div>
        <a href="{{ route('admin.reservations.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('admin.dashboard.view_all') }}</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800">
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.guest') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.hotel') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.room') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.check_in') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.check_out') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.amount') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                                    {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                </span>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $booking->user->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $booking->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $booking->hotel->name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $booking->room->room_number ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $booking->check_in->translatedFormat(__('auth.date_format')) }}</td>
                        <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $booking->check_out->translatedFormat(__('auth.date_format')) }}</td>
                        <td class="px-6 py-3.5 font-semibold text-slate-800 dark:text-slate-200">{{ __('auth.notif_currency_format', ['amount' => number_format($booking->total_price, 2)]) }}</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="bi bi-calendar-x text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                            <p class="text-sm text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.no_bookings') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
