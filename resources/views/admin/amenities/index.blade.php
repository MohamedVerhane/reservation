<x-layouts.admin :title="__('admin.nav.amenities')" active="amenities">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.nav.amenities') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.amenities.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.amenities.create') }}"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            <i class="bi bi-plus-lg text-base"></i> {{ __('admin.amenities.add') }}
        </a>
    </div>

    {{-- ── Flash Messages ── --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-exclamation-circle-fill text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ── Filters ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="amenities-grid-wrap">
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.amenities.search_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
            </div>
            <select name="status"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="">{{ __('admin.filter.all_status') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin.filter.active') }}</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin.filter.inactive') }}</option>
            </select>
            <select name="sort"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('admin.th.name') }}</option>
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>{{ __('admin.amenities.sort_newest') }}</option>
                <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>{{ __('admin.th.id') }}</option>
            </select>
            <select name="direction"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>{{ __('admin.amenities.ascending') }}</option>
                <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>{{ __('admin.amenities.descending') }}</option>
            </select>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">
                <i class="bi bi-funnel text-sm"></i> {{ __('admin.action.filter') }}
            </button>
            @if (request()->hasAny(['search', 'status', 'sort', 'direction']))
                <a href="{{ route('admin.amenities.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i> {{ __('admin.action.clear') }}
                </a>
            @endif
        </form>
    </div>

    {{-- ── Grid ── --}}
    <div id="amenities-grid-wrap">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
        @forelse ($amenities as $amenity)
            <div class="group rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 shadow-sm hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
                {{-- Header --}}
                <div class="px-5 pt-5 pb-3">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-950 text-lg text-indigo-600 dark:text-indigo-400">
                            @if ($amenity->icon)
                                <i class="{{ $amenity->icon }}"></i>
                            @else
                                <i class="bi bi-plus-circle"></i>
                            @endif
                        </span>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $amenity->name }}</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                @if ($amenity->icon)
                                    <code class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">{{ $amenity->icon }}</code>
                                @else
                                    {{ __('admin.amenities.no_icon') }}
                                @endif
                            </p>
                        </div>
                        {{-- Status Badge --}}
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider
                            {{ $amenity->is_active
                                ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400'
                                : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                            {{ $amenity->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="px-5 pb-3">
                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800/50 py-2.5 text-center">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $amenity->rooms_count }}</p>
                        <p class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase">{{ __('admin.amenities.rooms_label', ['count' => $amenity->rooms_count]) }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center border-t border-slate-100 dark:border-slate-800 divide-x divide-slate-100 dark:divide-slate-800">
                    <a href="{{ route('admin.amenities.manage-rooms', $amenity) }}"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/30 transition-colors"
                        title="{{ __("admin.action.manage_rooms") }}">
                        <i class="bi bi-door-open"></i> {{ __("admin.action.rooms") }}
                    </a>
                    <a href="{{ route('admin.amenities.edit', $amenity) }}"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                        <i class="bi bi-pencil"></i> {{ __("admin.action.edit") }}
                    </a>
                    @if ($amenity->rooms_count > 0)
                        <span class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold text-slate-400 dark:text-slate-600 cursor-not-allowed"
                            title="{{ __("admin.action.cannot_delete", ["count" => $amenity->rooms_count]) }}">
                            <i class="bi bi-trash3"></i> {{ __("admin.status.in_use") }}
                        </span>
                    @else
                        <form method="POST" action="{{ route('admin.amenities.destroy', $amenity) }}" class="flex-1"
                            x-data x-on:submit="return confirm('{{ __("admin.confirm.delete") }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center justify-center gap-1.5 w-full px-3 py-2.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                <i class="bi bi-trash3"></i> {{ __("admin.action.delete") }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full px-6 py-16 text-center rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900">
                <i class="bi bi-gear-wide-connected text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.amenities") }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.amenities.create_first') }}</p>
                <a href="{{ route('admin.amenities.create') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    <i class="bi bi-plus-lg text-xs"></i> {{ __('admin.amenities.add') }}
                </a>
            </div>
        @endforelse
    </div>

    {{-- ── Pagination ── --}}
    @if ($amenities->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <p class="text-xs text-slate-400 dark:text-slate-500">
                {{ __('admin.pagination.showing', ['from' => $amenities->firstItem() ?? 0, 'to' => $amenities->lastItem() ?? 0, 'total' => $amenities->total()]) }}
            </p>
            <div>{{ $amenities->withQueryString()->links() }}</div>
        </div>
    @endif
    </div>

</x-layouts.admin>
