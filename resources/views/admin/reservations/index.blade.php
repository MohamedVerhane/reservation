<x-layouts.admin :title="__('admin.nav.reservations')" active="reservations">

    {{-- ═══ Header ═══ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.nav.reservations') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.reservations.index_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.reservations.create') }}"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            <i class="bi bi-plus-lg text-base"></i> {{ __('admin.reservations.add_new') }}
        </a>
    </div>

    {{-- ═══ Flash Messages ═══ --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ═══ Filters ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="reservations-table-wrap">
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.reservations.search_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
            </div>

            <select name="hotel_id"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.filter.all_hotels') }}</option>
                @foreach ($hotels as $id => $name)
                    <option value="{{ $id }}" {{ request('hotel_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="status"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.filter.all_status') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('admin.status.pending') }}</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('admin.status.confirmed') }}</option>
                <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>{{ __('admin.status.checked_in') }}</option>
                <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>{{ __('admin.status.checked_out') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('admin.status.cancelled') }}</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="{{ __('admin.filter.from_date') }}"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />

            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="{{ __('admin.filter.to_date') }}"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
        </form>
    </div>

    {{-- ═══ Table ═══ --}}
    <div id="reservations-table-wrap">
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.id') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.guest') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.hotel') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.room') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.check_in') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.check_out') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.nights') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.total') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.status') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($reservations as $reservation)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="font-mono text-xs font-semibold text-slate-500 dark:text-slate-400">#{{ $reservation->id }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                                        {{ strtoupper(substr($reservation->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $reservation->user->name ?? __('admin.common.na') }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ $reservation->user->email ?? __('admin.common.na') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                <span class="truncate max-w-[120px] block">{{ $reservation->hotel->name ?? __('admin.common.na') }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                <span class="truncate max-w-[100px] block">{{ $reservation->room->room_number ?? __('admin.common.na') }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                {{ $reservation->check_in->translatedFormat(__('auth.date_format')) }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                {{ $reservation->check_out->translatedFormat(__('auth.date_format')) }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                {{ $reservation->nights }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-slate-800 dark:text-slate-200">${{ number_format($reservation->total_price, 2) }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {{ match($reservation->status) {
                                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
                                        'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
                                        'checked_in' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
                                        'checked_out' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
                                        default => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400',
                                    } }}">
                                    {{ $reservation->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.reservations.show', $reservation) }}" title="{{ __("admin.action.view") }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                        <i class="bi bi-eye text-sm"></i>
                                    </a>

                                    @if ($reservation->status === 'pending')
                                        <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}" data-ajax-action>
                                            @csrf
                                            <button type="submit" title="{{ __("admin.action.confirm") }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors">
                                                <i class="bi bi-check2-circle text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($reservation->status === 'confirmed' && $reservation->check_in->lessThanOrEqualTo(now()))
                                        <form method="POST" action="{{ route('admin.reservations.check-in', $reservation) }}" data-ajax-action>
                                            @csrf
                                            <button type="submit" title="{{ __("admin.action.check_in") }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors">
                                                <i class="bi bi-box-arrow-in-right text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($reservation->status === 'checked_in')
                                        <form method="POST" action="{{ route('admin.reservations.check-out', $reservation) }}" data-ajax-action>
                                            @csrf
                                            <button type="submit" title="{{ __("admin.action.check_out") }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors">
                                                <i class="bi bi-box-arrow-right text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if (in_array($reservation->status, ['pending', 'confirmed']) && $reservation->check_in->isFuture())
                                        <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                                            x-data x-on:submit="return confirm('{{ __("admin.confirm.cancel_reservation") }}')" data-ajax-action>
                                            @csrf
                                            <button type="submit" title="{{ __("admin.action.cancel") }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                                <i class="bi bi-x-circle text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}"
                                        x-data x-on:submit="return confirm('{{ __("admin.confirm.permanent_delete") }}')" data-ajax-action>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="{{ __("admin.action.delete") }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                            <i class="bi bi-trash3 text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <i class="bi bi-calendar-check text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.reservations") }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.empty.create_reservation') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reservations->hasPages())
            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-4">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

    <div class="mt-6 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
        <p>{{ __('admin.pagination.showing', ['from' => $reservations->firstItem() ?? 0, 'to' => $reservations->lastItem() ?? 0, 'total' => $reservations->total()]) }}</p>
        <p>{{ __('admin.pagination.page', ['current' => $reservations->currentPage(), 'last' => $reservations->lastPage()]) }}</p>
    </div>
    </div>

</x-layouts.admin>
