<x-layouts.frontend :title="__('meta.my_reservations')">
    <x-frontend.page-hero :title="__('booking.my_reservations')" :subtitle="__('booking.my_reservations_subtitle')" />

    {{-- Stats overview --}}
    @if($reservations->total() > 0)
        <section class="max-w-7xl mx-auto px-6 -mt-8 relative z-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $statCards = [
                        ['icon' => 'bi-journal-bookmark', 'label' => __('auth.cd_reservations'), 'value' => $stats['total'], 'prefix' => '', 'tone' => 'from-[var(--gold)]/12 to-[var(--gold)]/4 text-[var(--gold)]'],
                        ['icon' => 'bi-calendar-event', 'label' => __('auth.booking_upcoming'), 'value' => $stats['upcoming'], 'prefix' => '', 'tone' => 'from-blue-500/12 to-blue-500/4 text-blue-600 dark:text-blue-400'],
                        ['icon' => 'bi-moon-stars', 'label' => __('auth.booking_nights'), 'value' => $stats['nights'], 'prefix' => '', 'tone' => 'from-emerald-500/12 to-emerald-500/4 text-emerald-600 dark:text-emerald-400'],
                        ['icon' => 'bi-wallet2', 'label' => __('auth.cd_total_paid'), 'value' => $stats['spent'], 'prefix' => '$', 'tone' => 'from-amber-500/12 to-amber-500/4 text-amber-600 dark:text-amber-400'],
                    ];
                @endphp
                @foreach($statCards as $stat)
                    <div class="card p-5 animate-fade-in-up reveal d{{ ($loop->index % 3) + 1 }}">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br {{ $stat['tone'] }}">
                                <i class="bi {{ $stat['icon'] }} text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-2xl font-extrabold tracking-tight text-[var(--text-primary)]">
                                    @if(is_int($stat['value']))
                                        <span data-count="{{ (int) $stat['value'] }}">{{ $stat['prefix'] }}{{ number_format($stat['value']) }}</span>
                                    @else
                                        {{ $stat['prefix'] }}{{ number_format($stat['value'], 2) }}
                                    @endif
                                </p>
                                <p class="text-xs font-semibold text-[var(--text-muted)]">{{ $stat['label'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-6 py-12">
        @if($reservations->total() > 0)
            {{-- Filter tabs --}}
            <div class="flex flex-wrap items-center gap-2 mb-8 animate-fade-in-up reveal d1">
                @php
                    $filters = [
                        '' => __('auth.cd_filter_all'),
                        'pending' => __('auth.cd_filter_pending'),
                        'confirmed' => __('auth.cd_filter_confirmed'),
                        'checked_in' => __('auth.cd_filter_checked_in'),
                        'checked_out' => __('auth.cd_filter_checked_out'),
                        'cancelled' => __('auth.cd_filter_cancelled'),
                    ];
                @endphp
                @foreach($filters as $value => $label)
                    <a href="{{ route('frontend.booking.my-reservations', $value ? ['status' => $value] : []) }}"
                       class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold transition-all duration-300 {{ $currentStatus === $value ? 'bg-[var(--gold)] text-white shadow-lg shadow-[var(--gold)]/25' : 'border border-[var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] hover:border-[var(--gold)] hover:text-[var(--gold)]' }}">
                        <i class="bi {{ match ($value) {
                            '' => 'bi-grid-3x3-gap',
                            'pending' => 'bi-hourglass-split',
                            'confirmed' => 'bi-check2-circle',
                            'checked_in' => 'bi-box-arrow-in-right',
                            'checked_out' => 'bi-box-arrow-right',
                            'cancelled' => 'bi-x-circle',
                        } }}"></i>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reservations as $reservation)
                    @php
                        $barColor = match ($reservation->status) {
                            'pending' => 'bg-gradient-to-r from-amber-400 to-amber-600',
                            'confirmed' => 'bg-gradient-to-r from-blue-400 to-blue-600',
                            'checked_in' => 'bg-gradient-to-r from-emerald-400 to-emerald-600',
                            'checked_out' => 'bg-gradient-to-r from-slate-400 to-slate-500',
                            'cancelled' => 'bg-gradient-to-r from-red-400 to-red-600',
                            default => 'bg-gradient-to-r from-slate-400 to-slate-500',
                        };
                        $iconTone = match ($reservation->status) {
                            'pending' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400',
                            'confirmed' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400',
                            'checked_in' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400',
                            'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400',
                            default => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                        };
                    @endphp
                    <article class="card flex flex-col overflow-hidden animate-fade-in-up reveal d{{ ($loop->index % 3) + 1 }}" style="animation-delay: {{ $loop->index * 60 }}ms">
                        {{-- Status header --}}
                        <div class="{{ $barColor }} px-5 py-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-white">
                                    <i class="bi {{ match ($reservation->status) {
                                        'pending' => 'bi-hourglass-split',
                                        'confirmed' => 'bi-check2-circle',
                                        'checked_in' => 'bi-box-arrow-in-right',
                                        'checked_out' => 'bi-box-arrow-right',
                                        'cancelled' => 'bi-x-circle',
                                    } }}"></i>
                                    {{ __('reservations.status.' . $reservation->status) }}
                                </span>
                                <span class="text-xs font-semibold text-white/80 tracking-wider">#RES-{{ $reservation->id }}</span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            {{-- Hotel & room --}}
                            <div class="flex items-start gap-4 mb-5">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $iconTone }}">
                                    <i class="bi bi-building text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold leading-snug text-[var(--text-primary)]">{{ $reservation->hotel->name }}</h3>
                                    <p class="text-sm text-[var(--text-secondary)]">
                                        {{ $reservation->room->roomType->name ?? '' }}
                                        <span class="text-[var(--text-muted)]">· {{ __('rooms.room_number') }} {{ $reservation->room->room_number }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-[var(--surface-alt)] px-4 py-3 mb-4">
                                <div class="text-center">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-[var(--text-muted)]">{{ __('auth.booking_check_in') }}</p>
                                    <p class="mt-0.5 text-sm font-bold text-[var(--text-primary)]">
                                        {{ \Carbon\Carbon::parse($reservation->check_in)->translatedFormat(__('auth.date_format_short')) }}
                                    </p>
                                </div>
                                <i class="bi bi-arrow-right text-lg text-[var(--gold)] {{ app()->getLocale() === 'ar' ? 'rtl:-scale-x-100' : '' }}"></i>
                                <div class="text-center">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-[var(--text-muted)]">{{ __('auth.booking_check_out') }}</p>
                                    <p class="mt-0.5 text-sm font-bold text-[var(--text-primary)]">
                                        {{ \Carbon\Carbon::parse($reservation->check_out)->translatedFormat(__('auth.date_format_short')) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Meta badges --}}
                            <div class="flex flex-wrap items-center gap-2 mb-5">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                    <i class="bi bi-moon-stars"></i>
                                    {{ __('auth.cd_nights_count', ['count' => $reservation->nights]) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-600 dark:text-blue-400">
                                    <i class="bi bi-people"></i>
                                    {{ $reservation->guests }} {{ __('booking.adults') }}
                                    @if($reservation->children_count > 0)
                                        , {{ $reservation->children_count }} {{ __('booking.children') }}
                                    @endif
                                </span>
                            </div>

                            {{-- Price & actions --}}
                            <div class="mt-auto flex items-center justify-between gap-3 border-t border-[var(--border)] pt-4">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-[var(--text-muted)]">{{ __('auth.total_price') }}</p>
                                    <p class="text-xl font-extrabold text-[var(--gold)]">${{ number_format($reservation->total_price, 2) }}</p>
                                </div>
                                <a href="{{ route('frontend.booking.confirmation', $reservation) }}"
                                   class="btn-luxury btn-sm cursor-pointer">
                                    <i class="bi bi-eye"></i> {{ __('common.view') }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($reservations->hasPages())
                <div class="pagination-luxury mt-12 animate-fade-in-up reveal d1">
                    {{ $reservations->links() }}
                </div>
            @endif
        @else
            <div class="card max-w-2xl mx-auto px-6 py-16 text-center animate-fade-in-up reveal d1">
                <div class="relative mx-auto mb-6 flex h-24 w-24 items-center justify-center">
                    <div class="absolute inset-0 animate-pulse-soft rounded-full bg-[var(--gold)]/10"></div>
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[var(--gold)]/15 to-[var(--gold)]/5">
                        <i class="bi bi-calendar2-x text-4xl text-[var(--gold)]"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold text-[var(--text-primary)]">{{ __('booking.no_reservations') }}</h2>
                <p class="mt-2 text-sm text-[var(--text-secondary)]">{{ __('auth.cd_no_reservations_text') }}</p>
                <a href="{{ route('frontend.hotels') }}" class="btn-luxury mt-8 cursor-pointer">
                    <i class="bi bi-search"></i> {{ __('booking.find_hotel') }}
                </a>
            </div>
        @endif
    </section>
</x-layouts.frontend>
