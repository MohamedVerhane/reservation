<x-layouts.frontend :title="$room->roomType->name . ' - ' . $room->hotel->name">
    <x-frontend.page-hero :title="$room->roomType->name" :subtitle="$room->hotel->name" />

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-10">
                <div class="reveal">
                    @if($room->images->count() > 0)
                        <div class="rounded-3xl overflow-hidden aspect-video card mb-6">
                            <img src="{{ $room->images->first()->url }}"
                                 alt="{{ $room->roomType->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="badge-gold">{{ $room->roomType->max_guests }} {{ __('rooms.guests') }}</span>
                        <span class="text-[var(--text-muted)]">{{ $room->room_number }} {{ __('rooms.beds') }}</span>
                    </div>
                    <p class="text-[var(--text-secondary)] leading-relaxed text-lg">
                        {{ $room->roomType->description }}
                    </p>
                </div>

                <div class="reveal d1">
                    <div class="section-header">
                        <h2>{{ __('rooms.amenities') }}</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @forelse($room->amenities as $amenity)
                            <x-frontend.amenity-badge :amenity="$amenity" />
                        @empty
                            <p class="text-[var(--text-muted)]">{{ __('rooms.no_amenities') }}</p>
                        @endforelse
                    </div>
                </div>

                @if($room->images->count() > 1)
                    <div class="reveal d2">
                        <div class="section-header">
                            <h2>{{ __('rooms.gallery') }}</h2>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($room->images as $image)
                                <div class="aspect-square rounded-2xl overflow-hidden card">
                                    <img src="{{ $image->url }}" alt="{{ $image->alt_text ?? $room->roomType->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="card p-8 sticky top-24 reveal">
                    <div class="text-center mb-6">
                        <p class="text-sm text-[var(--text-muted)] mb-1">{{ __('rooms.price_per_night') }}</p>
                        <p class="text-4xl font-bold text-[var(--gold)]">
                            ${{ number_format($room->roomType->base_price, 2) }}
                        </p>
                    </div>
                    <div class="divider-gold mb-6"></div>
                    <div class="space-y-3 mb-6 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('rooms.capacity') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $room->roomType->max_guests }} {{ __('rooms.guests') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('rooms.room_number') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">#{{ $room->room_number }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('rooms.floor') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $room->floor ? __('rooms.floor') . ' ' . $room->floor : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--text-secondary)]">{{ __('rooms.available') }}</span>
                            <span class="font-bold text-[var(--text-primary)]">{{ $room->status_label }}</span>
                        </div>
                    </div>
                    <a href="{{ route('frontend.booking.show', $room->hotel->slug) }}" class="btn-primary w-full text-center">
                        <i class="bi bi-calendar-check"></i> {{ __('booking.book_now') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.frontend>
