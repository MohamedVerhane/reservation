<x-layouts.admin :title="$hotel->name" active="hotels">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.hotels.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.hotels') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $hotel->name }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ═══ Header Card ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 overflow-hidden mb-6">
        @if ($hotel->cover_image)
            <img src="{{ $hotel->cover_image_url }}" alt="{{ $hotel->name }}"
                class="w-full h-48 sm:h-64 object-cover" />
        @else
            <div class="w-full h-48 sm:h-64 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                <i class="bi bi-building text-6xl text-white/30"></i>
            </div>
        @endif
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $hotel->name }}</h1>
                        @if ($hotel->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-950 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ __('admin.filter.active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.filter.inactive') }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $hotel->full_address }}</p>
                    <div class="flex items-center gap-0.5 mt-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill text-sm {{ $i <= $hotel->star_rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                        @endfor
                        <span class="ms-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $hotel->star_rating_label }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.hotels.edit', $hotel) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="bi bi-pencil text-sm"></i> {{ __('admin.action.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.hotels.toggle', $hotel) }}" data-ajax-action>
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold shadow-sm transition-colors
                                {{ $hotel->is_active
                                    ? 'border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50'
                                    : 'border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50' }}">
                            <i class="bi bi-toggle2-on text-sm"></i> {{ $hotel->is_active ? __('admin.hotels.deactivate') : __('admin.hotels.activate') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Stats Row ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $hotel->rooms_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.hotels.stats_rooms') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $hotel->reservations_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.hotels.stats_reservations') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $hotel->reviews_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.hotels.stats_reviews') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $hotel->average_rating }}/5</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.hotels.stats_avg_rating') }}</p>
        </div>
    </div>

    {{-- ═══ Details Grid ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Contact --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('admin.hotels.contact_info') }}</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <i class="bi bi-envelope text-slate-400"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $hotel->email }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="bi bi-telephone text-slate-400"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $hotel->phone }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="bi bi-geo-alt text-slate-400"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ $hotel->full_address }}</span>
                </div>
                @if ($hotel->latitude && $hotel->longitude)
                    <div class="flex items-center gap-3">
                        <i class="bi bi-crosshair text-slate-400"></i>
                        <span class="text-sm text-slate-600 dark:text-slate-400">{{ $hotel->latitude }}, {{ $hotel->longitude }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Description --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('admin.hotels.description_section') }}</h3>
            @if ($hotel->description)
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $hotel->description }}</p>
            @else
                <p class="text-sm text-slate-400 dark:text-slate-500 italic">{{ __('admin.hotels.no_description') }}</p>
            @endif
        </div>
    </div>

    {{-- ═══ Latest Reviews ═══ --}}
    @if ($hotel->reviews->count())
        <div class="mt-6 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('admin.hotels.latest_reviews') }}</h3>
            <div class="space-y-4">
                @foreach ($hotel->reviews->take(5) as $review)
                    <div class="flex items-start gap-3 pb-4 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $review->user->name ?? '—' }}</p>
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill text-[10px] {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $review->created_at->diffForHumans() }}</p>
                            @if ($review->comment)
                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-layouts.admin>
