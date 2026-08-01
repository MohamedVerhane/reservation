<x-layouts.admin :title="__('admin.payments.index_title')" active="payments">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('admin.payments.index_title') }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('admin.payments.index_subtitle') }}</p>
            </div>
        </div>

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

        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-6">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-6" data-ajax-filter="payments-table-wrap">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.action.search') }}</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('admin.form.search_payments_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 pl-10 pr-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.payments.status_label') }}</label>
                        <select
                            name="status"
                            id="status"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                        >
                            <option value="">{{ __('admin.payments.all_statuses') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('admin.payments.status_pending') }}</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('admin.payments.status_completed') }}</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('admin.payments.status_failed') }}</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>{{ __('admin.payments.status_refunded') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="method" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.payments.method_label') }}</label>
                        <select
                            name="method"
                            id="method"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                        >
                            <option value="">{{ __('admin.payments.all_methods') }}</option>
                            <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>{{ __('admin.payments.method_cash') }}</option>
                            <option value="credit_card" {{ request('method') === 'credit_card' ? 'selected' : '' }}>{{ __('admin.payments.method_credit_card') }}</option>
                            <option value="debit_card" {{ request('method') === 'debit_card' ? 'selected' : '' }}>{{ __('admin.payments.method_debit_card') }}</option>
                            <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>{{ __('admin.payments.method_bank_transfer') }}</option>
                            <option value="online" {{ request('method') === 'online' ? 'selected' : '' }}>{{ __('admin.payments.method_online') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.payments.sort_by') }}</label>
                        <div class="flex gap-2">
                            <select
                                name="sort"
                                id="sort"
                                class="flex-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                            >
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>{{ __('admin.payments.sort_date') }}</option>
                                <option value="amount" {{ request('sort') === 'amount' ? 'selected' : '' }}>{{ __('admin.payments.sort_amount') }}</option>
                                <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>{{ __('admin.payments.sort_status') }}</option>
                            </select>
                            <select
                                name="direction"
                                class="w-24 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                            >
                                <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>{{ __('admin.payments.sort_desc') }}</option>
                                <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>{{ __('admin.payments.sort_asc') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('admin.payments.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <i class="bi bi-x-circle mr-1"></i> {{ __('admin.payments.clear_btn') }}
                    </a>
                </div>
            </form>

            <div id="payments-table-wrap">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.payment') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.reservation') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.payments.th_guest') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.payments.th_hotel') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.payments.th_amount') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.payments.method') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.payments.status_label') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.paid_at') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.payments.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="font-mono text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                                        #{{ $payment->id }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.reservations.show', $payment->reservation_id) }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                                        #{{ $payment->reservation_id }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $payment->reservation->user->name ?? __('admin.common.na') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $payment->reservation->hotel->name ?? __('admin.common.na') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">${{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @switch($payment->method)
                                            @case('cash')
                                                <i class="bi bi-cash-stack text-emerald-500"></i>
                                                @break
                                            @case('credit_card')
                                                <i class="bi bi-credit-card text-blue-500"></i>
                                                @break
                                            @case('debit_card')
                                                <i class="bi bi-credit-card-2-front text-purple-500"></i>
                                                @break
                                            @case('bank_transfer')
                                                <i class="bi bi-bank text-orange-500"></i>
                                                @break
                                            @case('online')
                                                <i class="bi bi-globe text-cyan-500"></i>
                                                @break
                                        @endswitch
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ __("admin.payments.method_{$payment->method}") }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @switch($payment->status)
                                        @case('pending')
                                            <span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <i class="bi bi-clock mr-1"></i> {{ __('admin.payments.status_pending') }}
                                            </span>
                                            @break
                                        @case('completed')
                                            <span class="inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                <i class="bi bi-check-circle mr-1"></i> {{ __('admin.payments.status_completed') }}
                                            </span>
                                            @break
                                        @case('failed')
                                            <span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                <i class="bi bi-x-circle mr-1"></i> {{ __('admin.payments.status_failed') }}
                                            </span>
                                            @break
                                        @case('refunded')
                                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                <i class="bi bi-arrow-return-left mr-1"></i> {{ __('admin.payments.status_refunded') }}
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-500 dark:text-slate-400">
                                        {{ $payment->paid_at ? $payment->paid_at->translatedFormat(__('auth.date_format') . ' H:i') : __('admin.common.na') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 p-1.5 text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800" title="{{ __("admin.action.view") }}">
                                            <i class="bi bi-eye text-sm"></i>
                                        </a>
                                        @if (in_array($payment->status, ['pending', 'failed']))
                                            <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('{{ __("admin.confirm.delete") }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 dark:border-red-800 p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20" title="{{ __("admin.action.delete") }}">
                                                    <i class="bi bi-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="bi bi-credit-card text-4xl text-slate-300 dark:text-slate-600"></i>
                                        <p class="mt-3 text-sm font-medium text-slate-500 dark:text-slate-400">{{ __("admin.empty.payments") }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="mt-6 rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-4">
                    {{ $payments->links() }}
                </div>
            @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
