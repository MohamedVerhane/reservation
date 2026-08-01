<x-frontend.dashboard-layout :title="__('auth.cd_invoices')">

    {{-- Page Header --}}
    <div class="mb-8 animate-fade-in-up" data-animate>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ __('auth.cd_invoices') }}</h1>

    </div>

    @if($reservations->count())
        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden animate-fade-in-up" data-animate style="animation-delay: 100ms">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-4 text-start text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.cd_invoice_number', ['number' => '']) }}</th>
                            <th class="px-6 py-4 text-start text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.hotel') }}</th>
                            <th class="px-6 py-4 text-start text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.booking_check_in') }}</th>
                            <th class="px-6 py-4 text-start text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.total_price') }}</th>
                            <th class="px-6 py-4 text-start text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.cd_total_paid') }}</th>
                            <th class="px-6 py-4 text-start text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.status') }}</th>
                            <th class="px-6 py-4 text-end text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('auth.cd_view_details') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($reservations as $reservation)
                            @php
                                $payment = $reservation->payments->first();
                                $paymentStatus = $payment->status ?? 'pending';
                                $statusBadge = match($paymentStatus) {
                                    'completed' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                                    'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400'],
                                    'failed' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400'],
                                    'refunded' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400'],
                                    default => ['bg' => 'bg-slate-100 dark:bg-slate-700/30', 'text' => 'text-slate-700 dark:text-slate-400'],
                                };
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">#INV-{{ $reservation->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $reservation->hotel->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-700 dark:text-slate-300">
{{ \Carbon\Carbon::parse($reservation->check_in)->translatedFormat(__('auth.date_format')) }}
                                        <i class="bi bi-arrow-right text-amber-500 mx-1 text-xs"></i>
{{ \Carbon\Carbon::parse($reservation->check_out)->translatedFormat(__('auth.date_format')) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">${{ number_format($reservation->total_price, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format($reservation->total_paid, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $statusBadge['bg'] }} px-3 py-1 text-xs font-semibold {{ $statusBadge['text'] }}">
                                        <i class="bi bi-circle-fill text-[0.4rem]"></i>
                                        {{ $payment->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-end">
                                    <a href="{{ route('frontend.booking.confirmation', $reservation) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-lg">
                                        <i class="bi bi-eye"></i>{{ __('auth.cd_view_details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden space-y-4">
            @foreach($reservations as $reservation)
                @php
                    $payment = $reservation->payments->first();
                    $paymentStatus = $payment->status ?? 'pending';
                    $statusBadge = match($paymentStatus) {
                        'completed' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                        'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400'],
                        'failed' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400'],
                        'refunded' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400'],
                        default => ['bg' => 'bg-slate-100 dark:bg-slate-700/30', 'text' => 'text-slate-700 dark:text-slate-400'],
                    };
                @endphp
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-5 animate-fade-in-up" data-animate style="animation-delay: {{ ($loop->index * 80) + 100 }}ms">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold text-slate-900 dark:text-white">#INV-{{ $reservation->id }}</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full {{ $statusBadge['bg'] }} px-3 py-1 text-xs font-semibold {{ $statusBadge['text'] }}">
                            <i class="bi bi-circle-fill text-[0.4rem]"></i>
                            {{ $payment->status_label }}
                        </span>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $reservation->hotel->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        {{ \Carbon\Carbon::parse($reservation->check_in)->translatedFormat(__('auth.date_format')) }}
                        <i class="bi bi-arrow-right text-amber-500 mx-1"></i>
                        {{ \Carbon\Carbon::parse($reservation->check_out)->translatedFormat(__('auth.date_format')) }}
                    </p>
                    <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-700">
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">{{ __('auth.cd_total_paid') }}</p>
                            <p class="text-lg font-extrabold text-amber-600 dark:text-amber-400">${{ number_format($reservation->total_paid, 2) }}</p>
                        </div>
                        <a href="{{ route('frontend.booking.confirmation', $reservation) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                            <i class="bi bi-eye"></i>{{ __('auth.cd_view_details') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($reservations->hasPages())
            <div class="mt-10">
                {{ $reservations->links() }}
            </div>
        @endif
    @else
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-12 text-center animate-fade-in-up" data-animate>
            <i class="bi bi-receipt text-6xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
            <p class="text-slate-500 dark:text-slate-400 text-lg mb-2">{{ __('auth.cd_no_invoices') }}</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mb-6">{{ __('auth.cd_no_invoices_text') }}</p>
            <a href="{{ route('frontend.search') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                <i class="bi bi-building"></i>{{ __('auth.booking_back_to_hotels') }}
            </a>
        </div>
    @endif

</x-frontend.dashboard-layout>
