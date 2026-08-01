<x-layouts.frontend :title="__('booking.review_booking')">
    <x-frontend.page-hero :title="__('booking.review_booking')" :subtitle="$hotel->name" />

    <section class="max-w-4xl mx-auto px-6 py-12">
        <div class="flex items-center justify-center gap-4 mb-12 reveal">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--gold)] text-[var(--text-inverse)] flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-check-lg"></i>
                </div>
                <span class="text-[var(--text-primary)] font-bold">{{ __('booking.step_dates') }}</span>
            </div>
            <div class="w-16 h-0.5 bg-[var(--gold)]"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--gold)] text-[var(--text-inverse)] flex items-center justify-center font-bold text-sm">
                    <i class="bi bi-check-lg"></i>
                </div>
                <span class="text-[var(--text-primary)] font-bold">{{ __('booking.step_room') }}</span>
            </div>
            <div class="w-16 h-0.5 bg-[var(--gold)]"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--gold)] text-[var(--text-inverse)] flex items-center justify-center font-bold text-sm">3</div>
                <span class="text-[var(--text-primary)] font-bold">{{ __('booking.step_review') }}</span>
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
                        <div class="w-24 h-20 rounded-xl overflow-hidden bg-[var(--surface-alt)] shrink-0">
                            @if($room->roomType->image_path)
                                <img src="{{ asset('storage/' . $room->roomType->image_path) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="bi bi-door-open text-[var(--text-muted)] text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-[var(--text-primary)] text-lg">{{ $room->roomType->name }}</h3>
                            <p class="text-sm text-[var(--text-secondary)]">{{ $hotel->name }}</p>
                        </div>
                    </div>
                    <div class="divider-gold my-4"></div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-[var(--text-muted)]">{{ __('booking.check_in') }}</p>
                            <p class="font-bold text-[var(--text-primary)]">{{ $checkIn->translatedFormat(__('auth.date_format')) }}</p>
                        </div>
                        <div>
                            <p class="text-[var(--text-muted)]">{{ __('booking.check_out') }}</p>
                            <p class="font-bold text-[var(--text-primary)]">{{ $checkOut->translatedFormat(__('auth.date_format')) }}</p>
                        </div>
                        <div>
                            <p class="text-[var(--text-muted)]">{{ __('booking.nights') }}</p>
                            <p class="font-bold text-[var(--text-primary)]">{{ $nights }}</p>
                        </div>
                        <div>
                            <p class="text-[var(--text-muted)]">{{ __('search.guests') }}</p>
                            <p class="font-bold text-[var(--text-primary)]">{{ $adults }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('frontend.booking.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="check_in" value="{{ $checkIn->toDateString() }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut->toDateString() }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">
                    <input type="hidden" name="children" value="{{ $children ?? 0 }}">

                    <div class="card p-6 reveal d1">
                        <h3 class="font-bold text-[var(--text-primary)] mb-4">{{ __('booking.guest_info') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-label">
                                <label>{{ __('booking.full_name') }}</label>
                                <input type="text" name="guest_name" class="input w-full"
                                       value="{{ old('guest_name', auth()->user()?->name ?? '') }}" required>
                                @error('guest_name') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-label">
                                <label>{{ __('booking.email') }}</label>
                                <input type="email" name="guest_email" class="input w-full"
                                       value="{{ old('guest_email', auth()->user()?->email ?? '') }}" required>
                                @error('guest_email') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-label">
                                <label>{{ __('booking.phone') }}</label>
                                <input type="tel" name="guest_phone" class="input w-full"
                                       value="{{ old('guest_phone', auth()->user()?->phone ?? '') }}">
                                @error('guest_phone') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-label">
                                <label>{{ __('booking.num_children') }}</label>
                                <input type="number" name="num_children" class="input w-full"
                                       value="{{ old('num_children', $children ?? 0) }}" min="0" max="10">
                            </div>
                        </div>
                        <div class="form-label mt-4">
                            <label>{{ __('booking.special_requests') }}</label>
                            <textarea name="special_requests" class="textarea w-full h-24 resize-none">{{ old('special_requests') }}</textarea>
                        </div>
                        <div class="form-label mt-4">
                            <label>{{ __('booking.payment_method') }}</label>
                            <select name="payment_method" required
                                class="input w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 text-sm text-[var(--text-primary)] focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/20 outline-none transition-all">
                                <option value="">{{ __('booking.select_payment') }}</option>
                                <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>{{ __('booking.credit_card') }}</option>
                                <option value="debit_card" {{ old('payment_method') === 'debit_card' ? 'selected' : '' }}>{{ __('booking.debit_card') }}</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>{{ __('booking.cash') }}</option>
                                <option value="online" {{ old('payment_method') === 'online' ? 'selected' : '' }}>{{ __('booking.online') }}</option>
                            </select>
                            @error('payment_method') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full mt-6">
                        <i class="bi bi-check-circle"></i> {{ __('booking.confirm_booking') }}
                    </button>
                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="card p-8 sticky top-24 reveal d2">
                    <h3 class="font-bold text-[var(--text-primary)] text-lg mb-4">{{ __('booking.price_summary') }}</h3>
                    <div class="space-y-3 text-sm mb-6">
                        <div class="flex justify-between">
                            <span class="text-[var(--text-secondary)]">${{ number_format($room->roomType->price_per_night, 2) }} x {{ $nights }} {{ __('booking.nights') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">${{ number_format($room->roomType->price_per_night * $nights, 2) }}</span>
                        </div>
                    </div>
                    <div class="divider-gold my-4"></div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-[var(--text-primary)] text-lg">{{ __('booking.total') }}</span>
                        <span class="text-2xl font-bold text-[var(--gold)]">${{ number_format($totalPrice, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.frontend>
