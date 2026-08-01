<x-frontend.dashboard-layout :title="__('auth.cd_favorites')">

    {{-- Page Header --}}
    <div class="mb-8 animate-fade-in-up" data-animate>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ __('auth.cd_favorites') }}</h1>
            <span class="inline-flex items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-sm font-bold text-rose-700 dark:text-rose-400">
                {{ $favorites->total() }}
            </span>
        </div>
    </div>

    @if($favorites->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $favorite)
                @php $hotel = $favorite->hotel; @endphp
                <div class="group overflow-hidden bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl animate-fade-in-up" data-animate style="animation-delay: {{ ($loop->index * 80) + 100 }}ms">
                    {{-- Image --}}
                    <div class="relative">
                        @if($hotel->cover_image_url)
                            <img src="{{ $hotel->cover_image_url }}" alt="{{ e($hotel->name) }}" class="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" />
                        @else
                            <div class="flex h-56 w-full items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600">
                                <i class="bi bi-building text-5xl text-white/60"></i>
                            </div>
                        @endif

                        {{-- Star Rating Badge --}}
                        @if($hotel->star_rating)
                            <div class="absolute start-4 top-4">
                                <span class="inline-flex items-center gap-1 rounded-full border border-white/30 bg-white/20 px-3 py-1 text-xs font-bold text-white shadow-lg backdrop-blur-md">
                                    @for($i = 0; $i < $hotel->star_rating; $i++)
                                        <i class="bi bi-star-fill text-amber-300"></i>
                                    @endfor
                                </span>
                            </div>
                        @endif

                        {{-- Heart Toggle --}}
                        <div class="absolute end-4 top-4">
                            <button
                                x-data="{ favorited: true }"
                                @click="
                                    favorited = !favorited;
                                    fetch('{{ route('customer.favorites.toggle') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ hotel_id: {{ $hotel->id }} })
                                    }).then(r => r.json()).then(d => {
                                        if (d.is_favorited === false) {
                                            let card = $el.closest('.group');
                                            card.style.opacity = '0';
                                            card.style.transform = 'scale(0.95)';
                                            setTimeout(() => card.remove(), 300);
                                        }
                                    })
                                "
                                class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center transition-all duration-300 hover:bg-white/40"
                            >
                                <i class="bi text-lg transition-colors" :class="favorited ? 'bi-heart-fill text-rose-400' : 'bi-heart text-white'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $hotel->name }}</h3>

                        <div class="mt-2 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                            <i class="bi bi-geo-alt text-amber-500"></i>
                            <span>{{ $hotel->city }}, {{ $hotel->country }}</span>
                        </div>

                        <div class="mt-3 flex items-center gap-3">
                            <x-frontend.star-rating :rating="$hotel->star_rating ?? 0" size="sm" />
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $hotel->reviews_count ?? 0 }} {{ __('auth.reviews') }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="bi bi-door-open text-amber-500"></i>
                                {{ $hotel->rooms_count ?? 0 }} {{ __('auth.rooms') }}
                            </span>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <a href="{{ route('frontend.hotel.show', $hotel->slug) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-amber-200 dark:border-amber-700 bg-white/80 dark:bg-slate-800/80 px-5 py-3 text-sm font-bold text-amber-700 dark:text-amber-400 shadow-sm transition-all duration-300 hover:bg-amber-50 dark:hover:bg-amber-950/30 hover:shadow-md">
                                {{ __('auth.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($favorites->hasPages())
            <div class="mt-10">
                {{ $favorites->links() }}
            </div>
        @endif
    @else
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-12 text-center animate-fade-in-up" data-animate>
            <i class="bi bi-heart text-6xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
            <p class="text-slate-500 dark:text-slate-400 text-lg mb-2">{{ __('auth.cd_no_favorites') }}</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mb-6">{{ __('auth.cd_no_favorites_text') }}</p>
            <a href="{{ route('frontend.search') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                <i class="bi bi-building"></i>{{ __('auth.booking_back_to_hotels') }}
            </a>
        </div>
    @endif

</x-frontend.dashboard-layout>
