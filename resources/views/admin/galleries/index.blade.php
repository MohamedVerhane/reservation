<x-layouts.admin :title="__('admin.nav.galleries')" active="galleries">

    {{-- ═══ Header ═══ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.nav.galleries') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.galleries.index_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.galleries.create') }}"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            <i class="bi bi-plus-lg text-base"></i> {{ __('admin.galleries.add') }}
        </a>
    </div>

    {{-- ═══ Flash Messages ═══ --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ═══ Filters ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="galleries-grid-wrap">
            {{-- Search --}}
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" :placeholder="__('admin.galleries.search_placeholder')"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
            </div>

            {{-- Hotel filter --}}
            <select name="hotel_id"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.filter.all_hotels') }}</option>
                @foreach ($hotels as $id => $name)
                    <option value="{{ $id }}" {{ request('hotel_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ═══ Card Grid ═══ --}}
    <div id="galleries-grid-wrap">
    @if ($galleries->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
            @foreach ($galleries as $gallery)
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Cover image --}}
                    <div class="h-40 bg-gradient-to-br from-indigo-500 to-purple-500 relative">
                        @if ($gallery->cover_image)
                            <img src="{{ $gallery->cover_image->url }}" alt="{{ $gallery->title }}"
                                class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="bi bi-images text-4xl text-white/30"></i>
                            </div>
                        @endif
                        <span class="absolute top-3 end-3 inline-flex items-center gap-1 rounded-full bg-black/50 backdrop-blur-sm px-2.5 py-1 text-[11px] font-bold text-white">
                            <i class="bi bi-image text-[10px]"></i> {{ $gallery->images_count }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $gallery->title }}</h3>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">{{ $gallery->hotel->name ?? '—' }}</p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-slate-400 dark:text-slate-500">
                            <span><i class="bi bi-sort-numeric-up me-1"></i>{{ __('admin.galleries.order') }}: {{ $gallery->sort_order }}</span>
                        </div>
                        @if ($gallery->description)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">{{ Str::limit($gallery->description, 100) }}</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="px-4 pb-4 flex items-center gap-1">
                        <a href="{{ route('admin.galleries.show', $gallery) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i class="bi bi-eye text-sm"></i> {{ __("admin.action.view") }}
                        </a>
                        <a href="{{ route('admin.galleries.edit', $gallery) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                            <i class="bi bi-pencil text-sm"></i> {{ __("admin.action.edit") }}
                        </a>
                        <form method="POST" action="{{ route('admin.galleries.destroy', $gallery) }}"
                            x-data x-on:submit="return confirm('{{ __("admin.confirm.gallery_delete") }}')" class="ms-auto" data-ajax-action>
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="{{ __("admin.action.delete") }}"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                <i class="bi bi-trash3 text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 px-6 py-16 text-center">
            <i class="bi bi-images text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.galleries") }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __("admin.empty.create_gallery") }}</p>
        </div>
    @endif

    {{-- ═══ Pagination ═══ --}}
    @if ($galleries->hasPages())
        <div class="mt-6">
            {{ $galleries->links() }}
        </div>
    @endif

    {{-- ═══ Stats row ═══ --}}
    <div class="mt-6 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
        <p>{{ __('admin.galleries.showing', ['from' => $galleries->firstItem() ?? 0, 'to' => $galleries->lastItem() ?? 0, 'total' => $galleries->total()]) }}</p>
        <p>{{ __('admin.galleries.pagination', ['current' => $galleries->currentPage(), 'last' => $galleries->lastPage()]) }}</p>
    </div>
    </div>

</x-layouts.admin>
