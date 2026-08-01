<x-layouts.admin title="{{ __('admin.payments.show_title', ['id' => $payment->id]) }}" active="payments">
    <div class="space-y-6">
        <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin.payments.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('admin.nav.payments') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-900 dark:text-white">#{{ $payment->id }}</span>
        </nav>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 p-4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill text-red-600 dark:text-red-400"></i>
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-white">
                        @switch($payment->method)
                            @case('cash')
                                <i class="bi bi-cash-stack text-3xl"></i>
                                @break
                            @case('credit_card')
                                <i class="bi bi-credit-card text-3xl"></i>
                                @break
                            @case('debit_card')
                                <i class="bi bi-credit-card-2-front text-3xl"></i>
                                @break
                            @case('bank_transfer')
                                <i class="bi bi-bank text-3xl"></i>
                                @break
                            @case('online')
                                <i class="bi bi-globe text-3xl"></i>
                                @break
                        @endswitch
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">${{ number_format($payment->amount, 2) }}</h1>
                        <p class="mt-1 text-sm text-white/80">{{ __('admin.payments.payment_method', ['method' => __('admin.payment_method.' . $payment->method)]) }}</p>
                    </div>
                    @switch($payment->status)
                        @case('pending')
                            <span class="inline-flex items-center rounded-lg bg-yellow-400/20 px-3 py-1 text-sm font-semibold text-yellow-100">
                                <i class="bi bi-clock mr-1"></i> {{ __('admin.payment_status.pending') }}
                            </span>
                            @break
                        @case('completed')
                            <span class="inline-flex items-center rounded-lg bg-emerald-400/20 px-3 py-1 text-sm font-semibold text-emerald-100">
                                <i class="bi bi-check-circle mr-1"></i> {{ __('admin.payment_status.completed') }}
                            </span>
                            @break
                        @case('failed')
                            <span class="inline-flex items-center rounded-lg bg-red-400/20 px-3 py-1 text-sm font-semibold text-red-100">
                                <i class="bi bi-x-circle mr-1"></i> {{ __('admin.payment_status.failed') }}
                            </span>
                            @break
                        @case('refunded')
                            <span class="inline-flex items-center rounded-lg bg-white/20 px-3 py-1 text-sm font-semibold text-white">
                                <i class="bi bi-arrow-return-left mr-1"></i> {{ __('admin.payment_status.refunded') }}
                            </span>
                            @break
                    @endswitch
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reservations.show', $payment->reservation_id) }}" class="inline-flex items-center gap-2 rounded-xl bg-white/20 px-4 py-2 text-sm font-medium text-white hover:bg-white/30">
                        <i class="bi bi-calendar-check"></i> {{ __('admin.action.view_reservation') }}
                    </a>
                    @if (in_array($payment->status, ['pending', 'failed']))
                        <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('{{ __("admin.confirm.delete") }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-500/20 px-4 py-2 text-sm font-medium text-red-100 hover:bg-red-500/30">
                                <i class="bi bi-trash"></i> {{ __('admin.action.delete') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="mt-4 text-sm text-white/60">
                <i class="bi bi-calendar mr-1"></i> {{ __('admin.payments.created', ['date' => $payment->created_at->translatedFormat(__('auth.date_format') . ' \a\t h:i A')]) }}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-rose-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/30">
                        <i class="bi bi-cash text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.th.amount') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($payment->amount, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-cyan-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                        <i class="bi bi-receipt text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.payments.total_price') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($payment->reservation->total_price ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-indigo-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                        <i class="bi bi-check-circle text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.payments.total_paid') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($payment->reservation->payments->where('status', 'completed')->sum('amount') ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-sky-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/30">
                        <i class="bi bi-wallet2 text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.payments.balance_due') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">
                            ${{ number_format(max(0, ($payment->reservation->total_price ?? 0) - ($payment->reservation->payments->where('status', 'completed')->sum('amount') ?? 0)), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-lime-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-credit-card mr-2 text-indigo-500"></i> {{ __('admin.payments.payment_details') }}
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.payments.payment_id') }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white font-mono">#{{ $payment->id }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.amount') }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">${{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.method') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ __('admin.payment_method.' . $payment->method) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.status') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ __('admin.payment_status.' . $payment->status) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.payments.transaction_id') }}</span>
                        <span class="text-sm font-mono text-slate-900 dark:text-white">{{ $payment->transaction_id ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.payments.paid_at') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $payment->paid_at ? $payment->paid_at->translatedFormat(__('auth.date_format') . ' h:i A') : '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.created_at') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $payment->created_at->translatedFormat(__('auth.date_format') . ' h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-yellow-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-calendar-check mr-2 text-purple-500"></i> {{ __('admin.payments.reservation_summary') }}
                </h2>
                @if ($payment->reservation)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.payments.reservation') }}</span>
                            <a href="{{ route('admin.reservations.show', $payment->reservation) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                                #{{ $payment->reservation->id }}
                            </a>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.guest') }}</span>
                            <span class="text-sm text-slate-900 dark:text-white">{{ $payment->reservation->user->name ?? __('admin.common.na') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.hotel') }}</span>
                            <span class="text-sm text-slate-900 dark:text-white">{{ $payment->reservation->hotel->name ?? __('admin.common.na') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.room') }}</span>
                            <span class="text-sm text-slate-900 dark:text-white">{{ $payment->reservation->room->number ?? __('admin.common.na') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.check_in') }}</span>
                            <span class="text-sm text-slate-900 dark:text-white">{{ $payment->reservation->check_in ? \Carbon\Carbon::parse($payment->reservation->check_in)->translatedFormat(__('auth.date_format')) : __('admin.common.na') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.check_out') }}</span>
                            <span class="text-sm text-slate-900 dark:text-white">{{ $payment->reservation->check_out ? \Carbon\Carbon::parse($payment->reservation->check_out)->translatedFormat(__('auth.date_format')) : __('admin.common.na') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.status') }}</span>
                            @switch($payment->reservation->status)
                                @case('pending')
                                    <span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">{{ __('admin.filter.pending') }}</span>
                                    @break
                                @case('confirmed')
                                    <span class="inline-flex items-center rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ __('admin.filter.confirmed') }}</span>
                                    @break
                                @case('checked_in')
                                    <span class="inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">{{ __('admin.filter.checked_in') }}</span>
                                    @break
                                @case('checked_out')
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-300">{{ __('admin.filter.checked_out') }}</span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ __('admin.filter.cancelled') }}</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.payments.reservation_unavailable') }}</p>
                @endif
            </div>
        </div>

        @if ($payment->status === 'pending')
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-gear mr-2 text-indigo-500"></i> {{ __('admin.payments.status_actions') }}
                </h2>
                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('admin.payments.update-status', $payment) }}" method="POST" class="flex items-end gap-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed" />
                        <div>
                            <label for="transaction_id" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.payments.transaction_id_optional') }}</label>
                            <input
                                type="text"
                                name="transaction_id"
                                id="transaction_id"
                                placeholder="{{ __('admin.form.transaction_id') }}"
                                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                            />
                        </div>
                        <button type="submit" class="btn-gradient rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25">
                            <i class="bi bi-check-circle mr-1"></i> {{ __('admin.payments.mark_completed') }}
                        </button>
                    </form>
                    <form action="{{ route('admin.payments.update-status', $payment) }}" method="POST" class="inline" onsubmit="return confirm('{{ __("admin.confirm.mark_failed") }}')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="failed" />
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 dark:border-red-800 px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                            <i class="bi bi-x-circle"></i> {{ __('admin.payments.mark_failed') }}
                        </button>
                    </form>
                </div>
            </div>
        @elseif ($payment->status === 'completed')
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-gear mr-2 text-indigo-500"></i> {{ __('admin.payments.status_actions') }}
                </h2>
                <form action="{{ route('admin.payments.update-status', $payment) }}" method="POST" class="inline" onsubmit="return confirm('{{ __("admin.confirm.refund_payment") }}')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="refunded" />
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-orange-200 dark:border-orange-800 px-5 py-2.5 text-sm font-bold text-orange-600 hover:bg-orange-50 dark:text-orange-400 dark:hover:bg-orange-900/20">
                        <i class="bi bi-arrow-return-left"></i> {{ __('admin.payments.refund_payment') }}
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.admin>
