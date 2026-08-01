<x-layouts.admin :title="__('admin.nav.hotels')" active="hotels">

    {{-- ═══ Header ═══ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.nav.hotels') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.hotels.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.hotels.create') }}"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            <i class="bi bi-plus-lg text-base"></i> {{ __('admin.hotels.add') }}
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
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="hotels-table-wrap">
            {{-- Search --}}
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.hotels.search_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
            </div>

            {{-- Status filter --}}
            <select name="status"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.filter.all_status') }}</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('admin.filter.active') }}</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('admin.filter.inactive') }}</option>
            </select>

            {{-- Trashed toggle --}}
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="trashed" value="1" class="hidden peer"
                    {{ request('trashed') === '1' ? 'checked' : '' }} />
                <span class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-red-500 relative after:content-[''] after:absolute after:top-0.5 after:start-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></span>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.filter.trashed') }}</span>
            </label>

            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">
                <i class="bi bi-funnel text-sm"></i> {{ __('admin.action.filter') }}
            </button>
        </form>
    </div>

    {{-- ═══ Table ═══ --}}
    <div id="hotels-table-wrap">
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.hotel') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.hotels.location') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.hotels.stars') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.hotels.rooms_short') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.hotels.bookings') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.status') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($hotels as $hotel)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($hotel->cover_image)
                                        <img src="{{ $hotel->cover_image_url }}" alt="{{ $hotel->name }}"
                                            class="h-10 w-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700" />
                                    @else
                                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                                            {{ strtoupper(substr($hotel->name, 0, 1)) }}
                                        </span>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $hotel->name }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $hotel->user->name ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $hotel->city }}, {{ $hotel->country }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill text-[10px] {{ $i <= $hotel->star_rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $hotel->rooms_count }}</td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $hotel->reservations_count }}</td>
                            <td class="px-6 py-3.5">
                                @if ($hotel->trashed())
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-950 px-2.5 py-0.5 text-xs font-semibold text-red-600 dark:text-red-400">
                                        {{ __('admin.hotels.deleted') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.hotels.toggle', $hotel) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold cursor-pointer transition-colors
                                                {{ $hotel->is_active
                                                    ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-900'
                                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                                            {{ $hotel->is_active ? __('admin.filter.active') : __('admin.filter.inactive') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($hotel->trashed())
                                        <form method="POST" action="{{ route('admin.hotels.restore', $hotel) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="{{ __('admin.action.restore') }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors">
                                                <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.hotels.force-delete', $hotel) }}"
                                            x-data x-on:submit="return confirm('{{ __("admin.confirm.permanent_delete") }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="{{ __('admin.action.force_delete') }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                                <i class="bi bi-trash3 text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.hotels.show', $hotel) }}" title="{{ __("admin.action.view") }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                            <i class="bi bi-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('admin.hotels.edit', $hotel) }}" title="{{ __("admin.action.edit") }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                                            <i class="bi bi-pencil text-sm"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.hotels.destroy', $hotel) }}"
                                            x-data x-on:submit="return confirm('{{ __("admin.confirm.delete") }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="{{ __("admin.action.delete") }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                                <i class="bi bi-trash3 text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <i class="bi bi-building text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.hotels") }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __("admin.empty.create_hotel") }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($hotels->hasPages())
            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-4">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>

    {{-- ═══ Stats row ═══ --}}
    <div class="mt-6 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
        <p>{{ __('admin.pagination.showing', ['from' => $hotels->firstItem() ?? 0, 'to' => $hotels->lastItem() ?? 0, 'total' => $hotels->total()]) }}</p>
        <p>{{ __('admin.pagination.page', ['current' => $hotels->currentPage(), 'last' => $hotels->lastPage()]) }}</p>
    </div>
    </div>

</x-layouts.admin>
