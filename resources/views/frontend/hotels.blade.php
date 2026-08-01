<x-layouts.frontend :title="__('meta.hotels')">
    <x-frontend.page-hero :title="__('hotels.title')" :subtitle="__('hotels.subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-16">
        @if($featuredHotels->count() > 0)
            <div class="mb-16 reveal">
                <div class="section-header">
                    <h2>{{ __('hotels.featured') }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredHotels as $hotel)
                        <x-frontend.hotel-card :hotel="$hotel" />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="reveal">
            <div class="section-header">
                <h2>{{ __('hotels.all') }}</h2>
            </div>

            @if($hotels->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($hotels as $hotel)
                        <x-frontend.hotel-card :hotel="$hotel" />
                    @endforeach
                </div>
                <div class="mt-12">
                    {{ $hotels->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <i class="bi bi-building text-[var(--text-muted)] text-6xl mb-4"></i>
                    <p class="text-[var(--text-secondary)] text-lg">{{ __('hotels.none_found') }}</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.frontend>
