<x-layouts.admin :title="__('admin.nav.room_types')" active="rooms">

    {{-- ═══ Header ═══ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.nav.room_types') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.room_types.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.room-types.create') }}"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            <i class="bi bi-plus-lg text-base"></i> {{ __('admin.room_types.add') }}
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
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="room-types-grid-wrap">
            {{-- Search --}}
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.room_types.search_placeholder') }}"
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

            {{-- Status filter --}}
            <select name="status"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.filter.all_status') }}</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('admin.filter.active') }}</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('admin.filter.inactive') }}</option>
            </select>

            {{-- Sort --}}
            <select name="sort"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>{{ __('admin.room_types.sort_newest') }}</option>
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('admin.th.name') }}</option>
                <option value="base_price" {{ request('sort') === 'base_price' ? 'selected' : '' }}>{{ __('admin.th.price') }}</option>
                <option value="max_guests" {{ request('sort') === 'max_guests' ? 'selected' : '' }}>{{ __('admin.room_types.sort_guests') }}</option>
            </select>

            {{-- Sort direction --}}
            <select name="dir"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="desc" {{ request('dir') === 'desc' ? 'selected' : '' }}>{{ __('admin.room_types.descending') }}</option>
                <option value="asc" {{ request('dir') === 'asc' ? 'selected' : '' }}>{{ __('admin.room_types.ascending') }}</option>
            </select>

            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">
                <i class="bi bi-funnel text-sm"></i> {{ __('admin.action.filter') }}
            </button>
        </form>
    </div>

    {{-- ═══ Grid ═══ --}}
    <div id="room-types-grid-wrap">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
        @forelse ($roomTypes as $type)
            <div class="group rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 shadow-sm hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
                {{-- Header --}}
                <div class="px-5 pt-5 pb-3">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $type->name }}</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                <i class="bi bi-building me-1"></i>{{ $type->hotel->name ?? '—' }}
                            </p>
                        </div>
                        @if ($type->is_active)
                            <form method="POST" action="{{ route('admin.room-types.toggle', $type) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-950 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-900 transition-colors cursor-pointer">
                                    {{ __('admin.filter.active') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.room-types.toggle', $type) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    {{ __('admin.filter.inactive') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    @if ($type->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ $type->description }}</p>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="px-5 pb-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center rounded-lg bg-slate-50 dark:bg-slate-800/50 py-2">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">${{ number_format($type->base_price, 0) }}</p>
                            <p class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase">{{ __('admin.room_types.label_price') }}</p>
                        </div>
                        <div class="text-center rounded-lg bg-slate-50 dark:bg-slate-800/50 py-2">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $type->max_guests }}</p>
                            <p class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase">{{ __('admin.room_types.label_guests') }}</p>
                        </div>
                        <div class="text-center rounded-lg bg-slate-50 dark:bg-slate-800/50 py-2">
                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $type->rooms_count }}</p>
                            <p class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase">{{ __('admin.room_types.label_rooms') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center border-t border-slate-100 dark:border-slate-800 divide-x divide-slate-100 dark:divide-slate-800">
                    <a href="{{ route('admin.room-types.show', $type) }}"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <i class="bi bi-eye"></i> {{ __("admin.action.view") }}
                    </a>
                    <a href="{{ route('admin.room-types.edit', $type) }}"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                        <i class="bi bi-pencil"></i> {{ __("admin.action.edit") }}
                    </a>
                    <form method="POST" action="{{ route('admin.room-types.destroy', $type) }}" class="flex-1"
                        x-data x-on:submit="return confirm('{{ __("admin.confirm.delete") }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center justify-center gap-1.5 w-full px-3 py-2.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                            <i class="bi bi-trash3"></i> {{ __("admin.action.delete") }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full px-6 py-16 text-center rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900">
                <i class="bi bi-door-open text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.room_types") }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.room_types.create_first') }}</p>
            </div>
        @endforelse
    </div>

    {{-- ═══ Pagination ═══ --}}
    @if ($roomTypes->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <p class="text-xs text-slate-400 dark:text-slate-500">
                {{ __('admin.pagination.showing', ['from' => $roomTypes->firstItem() ?? 0, 'to' => $roomTypes->lastItem() ?? 0, 'total' => $roomTypes->total()]) }}
            </p>
            <div>{{ $roomTypes->withQueryString()->links() }}</div>
        </div>
    @endif
    </div>

</x-layouts.admin>
