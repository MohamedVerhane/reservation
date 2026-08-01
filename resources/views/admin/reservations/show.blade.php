<x-layouts.admin title="{{ __('admin.reservations.show_title', ['id' => $reservation->id]) }}" active="reservations">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.reservations.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.reservations') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">#{{ $reservation->id }}</span>
        </div>
    </div>

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

    {{-- ═══ Header Card ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 overflow-hidden mb-6">
        <div class="h-32 sm:h-40 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center relative">
            <i class="bi bi-calendar-check text-7xl text-white/20"></i>
            <div class="absolute top-4 end-4">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white shadow-lg
                    {{ match($reservation->status) {
                        'pending' => 'bg-amber-500',
                        'confirmed' => 'bg-blue-500',
                        'checked_in' => 'bg-emerald-500',
                        'checked_out' => 'bg-slate-500',
                        'cancelled' => 'bg-red-500',
                        default => 'bg-slate-500',
                    } }}">
                    {{ strtoupper($reservation->status_label) }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.reservations.show_title', ['id' => $reservation->id]) }}</h1>
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-sm text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="bi bi-calendar-event"></i>
                            {{ $reservation->check_in->translatedFormat(__('auth.date_format')) }} — {{ $reservation->check_out->translatedFormat(__('auth.date_format')) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="bi bi-moon"></i>
                            {{ trans_choice('admin.reservations.nights', $reservation->nights, ['count' => $reservation->nights]) }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($reservation->status === 'pending')
                        <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}" data-ajax-action>
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/30 px-4 py-2 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                <i class="bi bi-check2-circle text-sm"></i> {{ __('admin.action.confirm') }}
                            </button>
                        </form>
                    @endif

                    @if ($reservation->canBeCheckedIn())
                        <form method="POST" action="{{ route('admin.reservations.check-in', $reservation) }}" data-ajax-action>
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                                <i class="bi bi-box-arrow-in-right text-sm"></i> {{ __('admin.action.check_in') }}
                            </button>
                        </form>
                    @endif

                    @if ($reservation->canBeCheckedOut())
                        <form method="POST" action="{{ route('admin.reservations.check-out', $reservation) }}" data-ajax-action>
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 px-4 py-2 text-sm font-semibold text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                                <i class="bi bi-box-arrow-right text-sm"></i> {{ __('admin.action.check_out') }}
                            </button>
                        </form>
                    @endif

                    @if ($reservation->canBeCancelled())
                        <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                            x-data x-on:submit="return confirm('{{ __("admin.confirm.cancel_reservation") }}')" data-ajax-action>
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-4 py-2 text-sm font-semibold text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                <i class="bi bi-x-circle text-sm"></i> {{ __('admin.action.cancel') }}
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.reservations.toggle', $reservation) }}" data-ajax-action>
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="bi bi-arrow-repeat text-sm"></i> {{ __('admin.action.cycle_status') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Stats Row ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($reservation->total_price, 2) }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.reservations.total_price') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">${{ number_format($totalPaid, 2) }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.reservations.paid') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold {{ $balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">
                ${{ number_format($balance, 2) }}
            </p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.reservations.balance') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $reservation->nights }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.reservations.nights_label') }}</p>
        </div>
    </div>

    {{-- ═══ Details Grid ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Guest Info --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                <i class="bi bi-person me-1.5 text-indigo-500"></i> {{ __('admin.reservations.guest_information') }}
            </h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                        {{ strtoupper(substr($reservation->user->name ?? 'U', 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $reservation->user->email ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.guest_phone') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->user->phone ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.guest_role') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ $reservation->user->role ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.guests') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->guests }}{{ $reservation->children_count ? __('admin.reservations.children', ['count' => $reservation->children_count]) : '' }}</span>
                </div>
            </div>
        </div>

        {{-- Hotel & Room --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                <i class="bi bi-building me-1.5 text-indigo-500"></i> {{ __('admin.reservations.hotel_room') }}
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.hotel') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->hotel->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.location') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->hotel->city ?? '—' }}, {{ $reservation->hotel->country ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.room_number') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->room->room_number ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.room_type') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->room->roomType->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.room_status') }}</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ match($reservation->room->status ?? '') {
                            'available' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400',
                            'occupied' => 'bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400',
                            'maintenance' => 'bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400',
                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400',
                        } }}">
                        {{ $reservation->room->status_label ?? '—' }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reservations.created') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $reservation->created_at->translatedFormat(__('auth.date_format') . ' g:i A') }}</span>
                </div>
                @if ($reservation->notes)
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">{{ __('admin.reservations.notes') }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $reservation->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ Payment History ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                <i class="bi bi-credit-card me-1.5 text-indigo-500"></i> {{ __('admin.reservations.payment_history') }}
            </h3>
        </div>

        {{-- Inline Payment Form --}}
        @if ($reservation->status !== 'cancelled')
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                <form method="POST" action="{{ route('admin.payments.store') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3" x-data="{ processing: false }" x-on:submit="processing = true" data-ajax-action data-success="{{ __('admin.payment.created') }}">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.amount_label') }}</label>
                        <input type="number" name="amount" step="0.01" min="0.01" max="{{ number_format($balance, 2, '.', '') }}" required
                            placeholder="{{ number_format($balance, 2) }}"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>

                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.th.method') }}</label>
                        <select name="method" required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            <option value="">{{ __('admin.reservations.select_method') }}</option>
                            <option value="cash">{{ __('admin.payment_method.cash') }}</option>
                            <option value="credit_card">{{ __('admin.payment_method.credit_card') }}</option>
                            <option value="debit_card">{{ __('admin.payment_method.debit_card') }}</option>
                            <option value="bank_transfer">{{ __('admin.payment_method.bank_transfer') }}</option>
                            <option value="online">{{ __('admin.payment_method.online') }}</option>
                        </select>
                    </div>

                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.reservations.transaction_id') }}</label>
                        <input type="text" name="transaction_id" maxlength="255" placeholder="{{ __('admin.form.optional') }}"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>

                    <button type="submit"
                        class="btn-gradient inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98] whitespace-nowrap"
                        :disabled="processing">
                        <i class="bi bi-plus-lg text-sm"></i> {{ __('admin.reservations.add_payment') }}
                    </button>
                </form>
            </div>
        @endif

        {{-- Payments Table --}}
        @if ($reservation->payments->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.id') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.amount') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.method') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reservations.transaction') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.status') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($reservation->payments as $payment)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <span class="font-mono text-xs font-semibold text-slate-500 dark:text-slate-400">#{{ $payment->id }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">${{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                    {{ $payment->method_label }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="font-mono text-xs text-slate-500 dark:text-slate-400">{{ $payment->transaction_id ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ match($payment->status) {
                                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
                                            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
                                            'failed' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
                                            'refunded' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
                                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400',
                                        } }}">
                                        {{ $payment->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">
                                    {{ $payment->paid_at ? $payment->paid_at->translatedFormat(__('auth.date_format') . ' g:i A') : $payment->created_at->translatedFormat(__('auth.date_format') . ' g:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="bi bi-credit-card text-3xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.reservations.no_payments') }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.reservations.add_payment_hint') }}</p>
            </div>
        @endif
    </div>

</x-layouts.admin>
