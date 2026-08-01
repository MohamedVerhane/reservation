<x-layouts.frontend :title="__('meta.my_reservations')">
    <x-frontend.page-hero :title="__('booking.my_reservations')" :subtitle="__('booking.my_reservations_subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-12">
        @if($reservations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reservations as $reservation)
                    <div class="card p-6 reveal d{{ ($loop->index % 3) + 1 }}">
                        <div class="flex items-center justify-between mb-4">
                            <span class="badge-{{ $reservation->status_color }}">
                                {{ __('reservations.status.' . $reservation->status) }}
                            </span>
                            <span class="text-xs text-[var(--text-muted)]">
                                #{{ $reservation->id }}
                            </span>
                        </div>
                        <h3 class="font-bold text-[var(--text-primary)] mb-2">
                            {{ $reservation->room->hotel->name }}
                        </h3>
                        <p class="text-sm text-[var(--text-secondary)] mb-3">
                            {{ $reservation->room->roomType->name }} - {{ __('rooms.room_number') }} {{ $reservation->room->room_number }}
                        </p>
                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-calendar text-[var(--gold)]"></i>
                                <span class="text-[var(--text-secondary)]">
                                    {{ \Carbon\Carbon::parse($reservation->check_in_date)->translatedFormat(__('auth.date_format_short')) }}
                                    —
                                    {{ \Carbon\Carbon::parse($reservation->check_out_date)->translatedFormat(__('auth.date_format')) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bi bi-people text-[var(--gold)]"></i>
                                <span class="text-[var(--text-secondary)]">
                                    {{ $reservation->num_adults }} {{ __('booking.adults') }}
                                    @if($reservation->num_children > 0)
                                        , {{ $reservation->num_children }} {{ __('booking.children') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-[var(--border)]">
                            <p class="text-xl font-bold text-[var(--gold)]">
                                ${{ number_format($reservation->total_price, 2) }}
                            </p>
                            <a href="{{ route('frontend.booking.my-reservations') }}" class="btn-ghost btn-sm">
                                <i class="bi bi-eye"></i> {{ __('common.view') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-12">
                {{ $reservations->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="bi bi-calendar-x text-[var(--text-muted)] text-6xl mb-4"></i>
                <p class="text-[var(--text-secondary)] text-lg mb-6">{{ __('booking.no_reservations') }}</p>
                <a href="{{ route('home') }}" class="btn-primary">
                    <i class="bi bi-search"></i> {{ __('booking.find_hotel') }}
                </a>
            </div>
        @endif
    </section>
</x-layouts.frontend>
