<x-layouts.frontend :title="__('booking.book') . ' - ' . $hotel->name">
    <x-frontend.page-hero :title="$hotel->name" :subtitle="__('booking.book_subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex items-center justify-center gap-4 mb-12 reveal">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--gold)] text-[var(--text-inverse)] flex items-center justify-center font-bold text-sm">1</div>
                <span class="text-[var(--text-primary)] font-bold">{{ __('booking.step_dates') }}</span>
            </div>
            <div class="w-16 h-0.5 bg-[var(--border)]"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[var(--surface-alt)] text-[var(--text-muted)] flex items-center justify-center font-bold text-sm">2</div>
                <span class="text-[var(--text-muted)]">{{ __('booking.step_room') }}</span>
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
            <div class="lg:col-span-2 space-y-8">
                <div class="card p-8 reveal">
                    <h2 class="text-2xl font-bold text-[var(--text-primary)] mb-6">{{ __('booking.select_dates') }}</h2>
                    <form action="{{ route('frontend.booking.show', $hotel->slug) }}" method="GET" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="form-label">
                                <label>{{ __('booking.check_in') }}</label>
                                <input type="date" name="check_in" class="input w-full"
                                       value="{{ $checkIn ? $checkIn->toDateString() : '' }}"
                                       min="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="form-label">
                                <label>{{ __('booking.check_out') }}</label>
                                <input type="date" name="check_out" class="input w-full"
                                       value="{{ $checkOut ? $checkOut->toDateString() : '' }}"
                                       min="{{ now()->addDay()->toDateString() }}" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div class="form-label">
                                <label>{{ __('booking.adults') }}</label>
                                <select name="adults" class="select w-full">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $guests == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-label">
                                <label>{{ __('booking.children') }}</label>
                                <select name="children" class="select w-full">
                                    @for($i = 0; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ ($children ?? 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            <i class="bi bi-search"></i> {{ __('booking.check_availability') }}
                        </button>
                    </form>
                </div>

                @if($checkIn && $checkOut)
                    <div class="reveal d1">
                        <div class="section-header">
                            <h2>{{ __('booking.available_rooms') }}</h2>
                        </div>

                        @if($roomTypes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($roomTypes as $roomType)
                                    <div class="card p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="font-bold text-[var(--text-primary)] text-lg">{{ $roomType->name }}</h3>
                                                <p class="text-sm text-[var(--text-secondary)]">{{ $roomType->description }}</p>
                                            </div>
                                            @if($roomType->image_path)
                                                <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-[var(--surface-alt)]">
                                                    <img src="{{ asset('storage/' . $roomType->image_path) }}" alt="" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <span class="chip">{{ $roomType->capacity }} {{ __('rooms.guests') }}</span>
                                            <span class="chip">{{ $roomType->beds_count ?? '-' }} {{ __('rooms.beds') }}</span>
                                            <span class="chip">{{ $roomType->rooms_count ?? $roomType->available_count ?? 0 }} {{ __('rooms.available') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between pt-4 border-t border-[var(--border)]">
                                            <p class="text-2xl font-bold text-[var(--gold)]">
                                                ${{ number_format($roomType->total_price ?? $roomType->price_per_night, 2) }}
                                            </p>
                                            <form action="{{ route('frontend.booking.select-room') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                                                <input type="hidden" name="check_in" value="{{ $checkIn->toDateString() }}">
                                                <input type="hidden" name="check_out" value="{{ $checkOut->toDateString() }}">
                                                <input type="hidden" name="adults" value="{{ $guests }}">
                                                <input type="hidden" name="children" value="{{ $children ?? 0 }}">
                                                <button type="submit" class="btn-primary btn-sm">
                                                    <i class="bi bi-arrow-right"></i> {{ __('booking.select') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <i class="bi bi-door-open text-[var(--text-muted)] text-5xl mb-3"></i>
                                <p class="text-[var(--text-secondary)]">{{ __('rooms.no_available') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="card p-8 sticky top-24 reveal">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 rounded-full bg-[var(--gold)]/10 flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-building text-[var(--gold)] text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-[var(--text-primary)] text-lg">{{ $hotel->name }}</h3>
                        <p class="text-sm text-[var(--text-secondary)]">{{ $hotel->city }}, {{ $hotel->country }}</p>
                    </div>
                    <div class="divider-gold mb-6"></div>
                    <div class="space-y-4 text-sm">
                        @if($checkIn)
                            <div class="flex justify-between">
                                <span class="text-[var(--text-muted)]">{{ __('booking.check_in') }}</span>
                                <span class="font-bold text-[var(--text-primary)]">{{ $checkIn->translatedFormat(__('auth.date_format')) }}</span>
                            </div>
                        @endif
                        @if($checkOut)
                            <div class="flex justify-between">
                                <span class="text-[var(--text-muted)]">{{ __('booking.check_out') }}</span>
                                <span class="font-bold text-[var(--text-primary)]">{{ $checkOut->translatedFormat(__('auth.date_format')) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-[var(--text-muted)]">{{ __('search.guests') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $guests }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.frontend>
