<x-frontend.dashboard-layout :title="__('auth.cd_reservations')">

    {{-- Page Header --}}
    <div class="mb-8 animate-fade-in-up" data-animate>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ __('auth.cd_reservations') }}</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">{{ __('auth.booking_subtitle') }}</p>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-8 animate-fade-in-up" data-animate style="animation-delay: 100ms">
        @php
            $filters = [
                '' => __('auth.cd_filter_all'),
                'pending' => __('auth.cd_filter_pending'),
                'confirmed' => __('auth.cd_filter_confirmed'),
                'checked_in' => __('auth.cd_filter_checked_in'),
                'checked_out' => __('auth.cd_filter_checked_out'),
                'cancelled' => __('auth.cd_filter_cancelled'),
            ];
            $currentStatus = request('status', '');
        @endphp
        <div class="flex flex-wrap gap-2">
            @foreach($filters as $value => $label)
                <a href="{{ route('customer.reservations', array_merge(request()->query(), ['status' => $value])) }}"
                    class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold transition-all duration-300 {{ $currentStatus === $value ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/25' : 'bg-white/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-amber-300 dark:hover:border-amber-600 hover:text-amber-600 dark:hover:text-amber-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Reservation Cards --}}
    @if($reservations->count())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($reservations as $reservation)
                @php
                    $statusBarColor = match($reservation->status) {
                        'pending' => 'bg-amber-500',
                        'confirmed' => 'bg-blue-500',
                        'checked_in' => 'bg-emerald-500',
                        'checked_out' => 'bg-slate-500',
                        'cancelled' => 'bg-red-500',
                        default => 'bg-slate-400',
                    };
                    $statusBadge = match($reservation->status) {
                        'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400'],
                        'confirmed' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400'],
                        'checked_in' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                        'checked_out' => ['bg' => 'bg-slate-100 dark:bg-slate-700/30', 'text' => 'text-slate-700 dark:text-slate-400'],
                        'cancelled' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400'],
                        default => ['bg' => 'bg-slate-100 dark:bg-slate-700/30', 'text' => 'text-slate-700 dark:text-slate-400'],
                    };
                @endphp
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl animate-fade-in-up" data-animate style="animation-delay: {{ ($loop->index * 80) + 150 }}ms">
                    {{-- Status Bar --}}
                    <div class="{{ $statusBarColor }} px-6 py-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-white uppercase tracking-wider">
                                {{ $reservation->status_label }}
                            </span>
                            <span class="text-xs text-white/80">#RES-{{ $reservation->id }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Hotel & Room --}}
                        <div class="flex items-start gap-4 mb-5">
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-building text-xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $reservation->hotel->name }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $reservation->room->roomType->name ?? '' }}
                                </p>
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="flex items-center gap-3 text-sm mb-4">
                            <div class="text-center">
                                <p class="text-xs text-slate-400 dark:text-slate-500 uppercase">{{ __('auth.booking_check_in') }}</p>
                                <p class="font-bold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($reservation->check_in)->translatedFormat(__('auth.date_format')) }}</p>
                            </div>
                            <i class="bi bi-arrow-right text-amber-500"></i>
                            <div class="text-center">
                                <p class="text-xs text-slate-400 dark:text-slate-500 uppercase">{{ __('auth.booking_check_out') }}</p>
                                <p class="font-bold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($reservation->check_out)->translatedFormat(__('auth.date_format')) }}</p>
                            </div>
                        </div>

                        {{-- Meta Info --}}
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                <i class="bi bi-moon-stars"></i>
                                {{ __('auth.cd_nights_count', ['count' => $reservation->nights]) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-400">
                                <i class="bi bi-people"></i>
                                {{ $reservation->guests }} {{ __('auth.guests') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full {{ $statusBadge['bg'] }} px-3 py-1 text-xs font-semibold {{ $statusBadge['text'] }}">
                                <i class="bi bi-circle-fill text-[0.4rem]"></i>
                                {{ $reservation->status_label }}
                            </span>
                        </div>

                        {{-- Price & Actions --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <div>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ __('auth.total_price') }}</p>
                                <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400">${{ number_format($reservation->total_price, 2) }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('frontend.booking.confirmation', $reservation) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                                    <i class="bi bi-eye"></i>{{ __('auth.view_details') }}
                                </a>
                                @if($reservation->canBeCancelled())
                                    <div x-data="{ showCancel: false }">
                                        <button @click="showCancel = true" class="inline-flex items-center gap-2 rounded-xl border border-red-200 dark:border-red-800 bg-white/80 dark:bg-slate-800/80 px-4 py-2.5 text-sm font-bold text-red-600 dark:text-red-400 transition-all duration-300 hover:bg-red-50 dark:hover:bg-red-950/30">
                                            <i class="bi bi-x-circle"></i>{{ __('auth.cd_cancel_reservation') }}
                                        </button>
                                        <div x-show="showCancel" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                                            <div @click="showCancel = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                                            <div class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 max-w-md w-full animate-fade-in-up">
                                                <div class="text-center">
                                                    <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                                                        <i class="bi bi-exclamation-triangle text-2xl text-red-500"></i>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('auth.cd_cancel_reservation') }}</h3>
                                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ __('auth.cd_cancel_confirm') }}</p>
                                                    <div class="flex items-center justify-center gap-3">
                                                        <button @click="showCancel = false" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                                                            {{ __('auth.select') }}
                                                        </button>
                                                        <form action="{{ route('customer.reservations.cancel', $reservation) }}" method="POST" data-ajax-action data-success="{{ __('auth.cd_cancel_reservation') }}">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/25 transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-xl">
                                                                <i class="bi bi-x-circle"></i>{{ __('auth.cd_cancel_reservation') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
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
            <i class="bi bi-journal-bookmark text-6xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
            <p class="text-slate-500 dark:text-slate-400 text-lg mb-2">{{ __('auth.cd_no_reservations') }}</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mb-6">{{ __('auth.cd_no_reservations_text') }}</p>
            <a href="{{ route('frontend.search') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                <i class="bi bi-building"></i>{{ __('auth.booking_back_to_hotels') }}
            </a>
        </div>
    @endif

</x-frontend.dashboard-layout>
