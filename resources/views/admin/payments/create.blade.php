<x-layouts.admin :title="__('admin.payments.create_title')" active="payments">
    <div class="space-y-6">
        <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin.payments.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('admin.payments.index_title') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-900 dark:text-white">{{ __('admin.payments.create_title') }}</span>
        </nav>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
                <div class="flex items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill mt-0.5 text-red-600 dark:text-red-400"></i>
                    <div>
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ __('admin.form.fix_errors') }}</p>
                        <ul class="mt-1 list-inside list-disc text-sm text-red-600 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.payments.store') }}" data-ajax>
            @csrf

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="reservation_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.payments.reservation_id') }}</label>
                        <input type="number" name="reservation_id" id="reservation_id" value="{{ old('reservation_id') }}" required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        @error('reservation_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.payments.amount') }}</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount') }}" required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        @error('amount')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="method" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.payments.method') }}</label>
                        <select name="method" id="method" required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            <option value="">{{ __('admin.payments.method_select') }}</option>
                            <option value="cash" {{ old('method') === 'cash' ? 'selected' : '' }}>{{ __('admin.payments.method_cash') }}</option>
                            <option value="credit_card" {{ old('method') === 'credit_card' ? 'selected' : '' }}>{{ __('admin.payments.method_credit_card') }}</option>
                            <option value="debit_card" {{ old('method') === 'debit_card' ? 'selected' : '' }}>{{ __('admin.payments.method_debit_card') }}</option>
                            <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>{{ __('admin.payments.method_bank_transfer') }}</option>
                            <option value="online" {{ old('method') === 'online' ? 'selected' : '' }}>{{ __('admin.payments.method_online') }}</option>
                        </select>
                        @error('method')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transaction_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.payments.transaction_id') }}</label>
                        <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                        @error('transaction_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('admin.payments.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
                    <i class="bi bi-x-lg text-sm"></i> {{ __('admin.payments.cancel') }}
                </a>
                <button type="submit"
                    class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
                    <i class="bi bi-check-lg text-base"></i> {{ __('admin.payments.create_submit') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
