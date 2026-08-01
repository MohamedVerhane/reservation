<x-layouts.frontend :title="__('booking.confirmed')">
    <section class="max-w-3xl mx-auto px-6 py-20 text-center">
        <div class="reveal">
            <div class="w-24 h-24 rounded-full bg-emerald-500/10 flex items-center justify-center mx-auto mb-8">
                <i class="bi bi-check-circle-fill text-emerald-500 text-5xl"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-[var(--text-primary)] mb-4">
                {{ __('booking.confirmed_title') }}
            </h1>
            <p class="text-[var(--text-secondary)] text-lg mb-2">
                {{ __('booking.confirmed_message') }}
            </p>
            <p class="text-sm text-[var(--text-muted)] mb-10">
                {{ __('booking.confirmation_email') }}
            </p>
        </div>

        <div class="card p-8 text-left mb-8 reveal d1">
            <h2 class="text-xl font-bold text-[var(--text-primary)] mb-4">{{ __('booking.booking_details') }}</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.booking_id') }}</p>
                    <p class="font-bold text-[var(--text-primary)]">#{{ $reservation->id }}</p>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.hotel') }}</p>
                    <p class="font-bold text-[var(--text-primary)]">{{ $reservation->room->hotel->name }}</p>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.room') }}</p>
                    <p class="font-bold text-[var(--text-primary)]">{{ $reservation->room->roomType->name }}</p>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.status') }}</p>
                    <span class="badge-green">{{ __('reservations.status.' . $reservation->status) }}</span>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.check_in') }}</p>
                    <p class="font-bold text-[var(--text-primary)]">{{ \Carbon\Carbon::parse($reservation->check_in_date)->translatedFormat(__('auth.date_format')) }}</p>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.check_out') }}</p>
                    <p class="font-bold text-[var(--text-primary)]">{{ \Carbon\Carbon::parse($reservation->check_out_date)->translatedFormat(__('auth.date_format')) }}</p>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.nights') }}</p>
                    <p class="font-bold text-[var(--text-primary)]">{{ $reservation->nights }}</p>
                </div>
                <div>
                    <p class="text-[var(--text-muted)]">{{ __('booking.total') }}</p>
                    <p class="font-bold text-[var(--gold)] text-xl">${{ number_format($reservation->total_price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-4 reveal d2">
            <a href="{{ route('frontend.booking.my-reservations') }}" class="btn-primary">
                <i class="bi bi-list-ul"></i> {{ __('booking.view_all') }}
            </a>
            <a href="{{ route('home') }}" class="btn-ghost">
                <i class="bi bi-house"></i> {{ __('common.home') }}
            </a>
        </div>
    </section>
</x-layouts.frontend>
