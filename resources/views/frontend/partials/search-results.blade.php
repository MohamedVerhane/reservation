@if($hotels->isNotEmpty())
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach($hotels as $hotel)
            <div class="animate-fade-in-up" style="animation-delay: {{ $loop->index * 80 }}ms">
                <x-frontend.hotel-card :hotel="$hotel" />
            </div>
        @endforeach
    </div>
@else
    <div class="py-20 text-center">
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-100 dark:bg-slate-800">
            <i class="bi bi-search text-3xl text-slate-300 dark:text-slate-600"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ __('auth.hotels_no_results') }}</h3>
        <p class="mx-auto mt-2 max-w-md text-slate-500 dark:text-slate-400">{{ __('auth.hotels_no_results_text') }}</p>
        <button
            @click="clearAll()"
            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-amber-50 px-6 py-3 text-sm font-semibold text-amber-700 transition-all hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20"
        >
            <i class="bi bi-arrow-counterclockwise"></i>
            {{ __('auth.hotels_filter_city') }}
        </button>
    </div>
@endif
