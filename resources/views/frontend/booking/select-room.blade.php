<x-layouts.frontend :title="__('booking.select_room') . ' - ' . $hotel->name">
    <x-frontend.page-hero :title="$hotel->name" :subtitle="__('booking.select_room')" />

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex items-center justify-center gap-4 mb-12 reveal">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--gold)] text-[var(--text-inverse)] flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-check-lg"></i>
                </div>
                <span class="text-[var(--text-primary)] font-bold">{{ __('booking.step_dates') }}</span>
            </div>
            <div class="w-16 h-0.5 bg-[var(--gold)]"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--gold)] text-[var(--text-inverse)] flex items-center justify-center font-bold text-sm">2</div>
                <span class="text-[var(--text-primary)] font-bold">{{ __('booking.step_room') }}</span>
            </div>
            <div class="w-16 h-0.5 bg-[var(--border)]"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--surface-alt)] text-[var(--text-muted)] flex items-center justify-center font-bold text-sm">3</div>
                <span class="text-[var(--text-muted)]">{{ __('booking.step_review') }}</span>
            </div>
            <div class="w-16 h-0.5 bg-[var(--border)]"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--surface-alt)] text-[var(--text-muted)] flex items-center justify-center font-bold text-sm">4</div>
                <span class="text-[var(--text-muted)]">{{ __('booking.step_confirm') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6 reveal">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-[var(--surface-alt)] shrink-0">
                            @if($roomType->image_path)
                                <img src="{{ asset('storage/' . $roomType->image_path) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="bi bi-door-open text-[var(--text-muted)] text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-[var(--text-primary)]">{{ $roomType->name }}</h2>
                            <p class="text-sm text-[var(--text-secondary)]">{{ $roomType->description }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="badge-gold">{{ $roomType->capacity }} {{ __('rooms.guests') }}</span>
                        <span class="chip">${{ number_format($roomType->price_per_night, 2) }} / {{ __('hotels.per_night') }}</span>
                    </div>
                </div>

                @if($rooms->count() > 0)
                    <form action="{{ route('frontend.booking.review') }}" method="POST" class="space-y-4 reveal d1">
                        @csrf
                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                        <input type="hidden" name="check_in" value="{{ $checkIn->toDateString() }}">
                        <input type="hidden" name="check_out" value="{{ $checkOut->toDateString() }}">
                        <input type="hidden" name="adults" value="{{ $adults }}">
                        <input type="hidden" name="children" value="{{ $children ?? 0 }}">

                        <div class="section-header">
                            <h2>{{ __('booking.available_units') }}</h2>
                        </div>

                        @foreach($rooms as $room)
                            <label class="card p-5 flex items-center gap-4 cursor-pointer has-[:checked]:border-[var(--gold)] has-[:checked]:bg-[var(--gold)]/5 transition-all">
                                <input type="radio" name="room_id" value="{{ $room->id }}" class="sr-only peer" required
                                       {{ $loop->first ? 'checked' : '' }}>
                                <div class="flex-1 flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-[var(--text-primary)]">{{ __('rooms.room_number') }} {{ $room->room_number }}</p>
                                        <p class="text-sm text-[var(--text-muted)]">{{ $room->floor ? __('rooms.floor') . ' ' . $room->floor : '' }}</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-lg font-bold text-[var(--gold)]">${{ number_format($room->total_price ?? $room->calculateTotalPrice($checkIn, $checkOut), 2) }}</p>
                                        <p class="text-xs text-[var(--text-muted)]">{{ $nights }} {{ __('booking.nights') }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach

                        <button type="submit" class="btn-primary w-full mt-4">
                            <i class="bi bi-arrow-right"></i> {{ __('booking.continue_review') }}
                        </button>
                    </form>
                @else
                    <div class="text-center py-16">
                        <i class="bi bi-door-open text-[var(--text-muted)] text-5xl mb-3"></i>
                        <p class="text-[var(--text-secondary)]">{{ __('rooms.no_available') }}</p>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="card p-8 sticky top-24 reveal d2">
                    <h3 class="font-bold text-[var(--text-primary)] text-lg mb-4">{{ __('booking.booking_summary') }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('booking.check_in') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $checkIn->translatedFormat(__('auth.date_format')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('booking.check_out') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $checkOut->translatedFormat(__('auth.date_format')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('booking.nights') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $nights }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('search.guests') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $adults }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.frontend>
