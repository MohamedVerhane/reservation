<x-layouts.frontend :title="__('reviews.reviews')">
    <x-frontend.page-hero :title="__('reviews.reviews')" :subtitle="__('reviews.write_review_subtitle')" />

    <section class="max-w-4xl mx-auto px-6 py-12">
        @if($reservations->count() > 0)
            <div class="space-y-8">
                @foreach($reservations as $reservation)
                    @php
                        $alreadyReviewed = in_array($reservation->id, $reviewedReservationIds);
                    @endphp
                    <div class="card p-6 reveal d{{ ($loop->index % 3) + 1 }}">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-[var(--text-primary)] text-lg">{{ $reservation->hotel->name }}</h3>
                                <p class="text-sm text-[var(--text-secondary)]">
                                    {{ $reservation->room->roomType->name }}
                                    &middot;
                                    {{ \Carbon\Carbon::parse($reservation->check_in_date)->translatedFormat(__('auth.date_format_short')) }}
                                    &mdash;
                                    {{ \Carbon\Carbon::parse($reservation->check_out_date)->translatedFormat(__('auth.date_format_short')) }}
                                </p>
                            </div>
                            <span class="badge-{{ $reservation->status_color }}">{{ __('reservations.status.' . $reservation->status) }}</span>
                        </div>

                        @if($alreadyReviewed)
                            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3">
                                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ __('reviews.already_submitted') }}
                                </p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('frontend.hotel.reviews.store', ['hotelSlug' => $reservation->hotel->slug]) }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">

                                <div>
                                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-2">{{ __('reviews.rating_label') }}</label>
                                    <x-frontend.star-rating-picker :selected="old('rating', 0)" name="rating" />
                                    @error('rating') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-2">{{ __('reviews.comment_label') }}</label>
                                    <textarea name="comment" rows="4" placeholder="{{ __('reviews.comment_placeholder') }}"
                                        class="textarea w-full resize-none rounded-xl border border-[var(--border)] bg-[var(--surface)] px-4 py-3 text-sm text-[var(--text-primary)] focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20 outline-none transition-all">{{ old('comment') }}</textarea>
                                    @error('comment') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <button type="submit" class="btn-primary">
                                    <i class="bi bi-star"></i> {{ __('reviews.submit_review') }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20">
                <i class="bi bi-chat-square-text text-[var(--text-muted)] text-6xl mb-4"></i>
                <p class="text-[var(--text-secondary)] text-lg mb-2">{{ __('reviews.no_eligible_bookings') }}</p>
                <p class="text-sm text-[var(--text-muted)] mb-6">{{ __('reviews.no_eligible_bookings_hint') }}</p>
                <a href="{{ route('home') }}" class="btn-primary">
                    <i class="bi bi-search"></i> {{ __('booking.find_hotel') }}
                </a>
            </div>
        @endif
    </section>
</x-layouts.frontend>
