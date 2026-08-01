<x-layouts.frontend :title="__('meta.search')">
    <x-frontend.page-hero :title="__('search.title')" :subtitle="__('search.subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-10">
        <form action="{{ route('frontend.search') }}" method="GET" class="mb-10 reveal">
            <div class="card p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="form-label">
                        <label>{{ __('search.destination') }}</label>
                        <input type="text" name="destination" class="input w-full"
                               placeholder="{{ __('search.destination_placeholder') }}"
                               value="{{ request('destination') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.check_in') }}</label>
                        <input type="date" name="check_in" class="input w-full"
                               value="{{ request('check_in') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.check_out') }}</label>
                        <input type="date" name="check_out" class="input w-full"
                               value="{{ request('check_out') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.guests') }}</label>
                        <select name="guests" class="select w-full">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('guests', 1) == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i > 1 ? __('search.guests_plural') : __('search.guests_singular') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4 mb-4">
                    <div class="form-label">
                        <label>{{ __('search.min_price') }}</label>
                        <input type="number" name="min_price" class="input w-full"
                               placeholder="{{ __('search.any') }}" value="{{ request('min_price') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.max_price') }}</label>
                        <input type="number" name="max_price" class="input w-full"
                               placeholder="{{ __('search.any') }}" value="{{ request('max_price') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.min_stars') }}</label>
                        <select name="min_stars" class="select w-full">
                            <option value="">{{ __('search.any') }}</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('min_stars') == $i ? 'selected' : '' }}>
                                    {{ $i }}+
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.sort') }}</label>
                        <select name="sort" class="select w-full">
                            <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>
                                {{ __('search.sort_relevance') }}
                            </option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                {{ __('search.sort_price_asc') }}
                            </option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                {{ __('search.sort_price_desc') }}
                            </option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>
                                {{ __('search.sort_rating') }}
                            </option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>
                                {{ __('search.sort_name') }}
                            </option>
                        </select>
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.min_rooms') }}</label>
                        <input type="number" name="min_rooms_available" class="input w-full"
                               placeholder="{{ __('search.any') }}" value="{{ request('min_rooms_available') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.max_distance') }}</label>
                        <input type="number" name="max_distance" class="input w-full"
                               placeholder="{{ __('search.any') }}" step="0.1" value="{{ request('max_distance') }}">
                    </div>
                    <div class="form-label">
                        <label>{{ __('search.amenities') }}</label>
                        <input type="text" name="amenities" class="input w-full"
                               placeholder="{{ __('search.amenities_placeholder') }}"
                               value="{{ request('amenities') }}">
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-search"></i> {{ __('search.search_button') }}
                </button>
            </div>
        </form>

        <div id="search-results" class="reveal">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-[var(--text-primary)]">
                    {{ $hotels->total() }} {{ __('search.results_found') }}
                </h2>
            </div>

            @if($hotels->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($hotels as $hotel)
                        <x-frontend.hotel-card :hotel="$hotel" />
                    @endforeach
                </div>
                <div class="mt-10">
                    {{ $hotels->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <i class="bi bi-search text-[var(--text-muted)] text-6xl mb-4"></i>
                    <p class="text-[var(--text-secondary)] text-lg">{{ __('search.no_results') }}</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.frontend>
