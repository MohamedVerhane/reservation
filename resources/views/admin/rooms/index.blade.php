<x-layouts.admin :title="__('admin.nav.rooms')" active="rooms-actual">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.nav.rooms') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.rooms.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            <i class="bi bi-plus-lg text-base"></i> {{ __('admin.rooms.add') }}
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-indigo-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="rooms-grid-wrap">
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.rooms.search_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
            </div>
            <select name="hotel_id" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="">{{ __('admin.filter.all_hotels') }}</option>
                @foreach ($hotels as $id => $name)
                    <option value="{{ $id }}" {{ request('hotel_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="type_id" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="">{{ __('admin.filter.all_types') }}</option>
                @foreach ($roomTypes as $id => $name)
                    <option value="{{ $id }}" {{ request('type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="">{{ __('admin.filter.all_status') }}</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>{{ __('admin.room_status.available') }}</option>
                <option value="occupied" {{ request('status') === 'occupied' ? 'selected' : '' }}>{{ __('admin.room_status.occupied') }}</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>{{ __('admin.room_status.maintenance') }}</option>
                <option value="out_of_order" {{ request('status') === 'out_of_order' ? 'selected' : '' }}>{{ __('admin.room_status.out_of_order') }}</option>
            </select>
            <select name="floor" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="">{{ __('admin.filter.all_floors') }}</option>
                @foreach ($floors as $f)
                    <option value="{{ $f }}" {{ request('floor') == $f ? 'selected' : '' }}>{{ __('admin.rooms.floor_prefix') }} {{ $f }}</option>
                @endforeach
            </select>
            <select name="sort" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none">
                <option value="id" {{ request('sort') === 'id' ? 'selected' : '' }}>{{ __('admin.rooms.sort_newest') }}</option>
                <option value="room_number" {{ request('sort') === 'room_number' ? 'selected' : '' }}>{{ __('admin.th.room_number') }}</option>
                <option value="floor" {{ request('sort') === 'floor' ? 'selected' : '' }}>{{ __('admin.th.floor') }}</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">
                <i class="bi bi-funnel text-sm"></i> {{ __('admin.action.filter') }}
            </button>
        </form>
    </div>

    {{-- Grid --}}
    <div id="rooms-grid-wrap">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
        @forelse ($rooms as $room)
            <div class="group rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-indigo-50/60 dark:bg-slate-900 shadow-sm hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
                {{-- Image --}}
                <div class="relative h-40 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700">
                    @if ($room->images->count())
                        <img src="{{ $room->images->first()->url }}" alt="{{ __('admin.rooms.room_label', ['number' => $room->room_number]) }}" class="w-full h-full object-cover" />
                    @else
                        <div class="flex items-center justify-center h-full">
                            <i class="bi bi-door-open text-4xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                    @endif
                    {{-- Status badge --}}
                    <span class="absolute top-3 end-3 inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white shadow-lg
                        {{ match($room->status) {
                            'available' => 'bg-emerald-500',
                            'occupied' => 'bg-red-500',
                            'maintenance' => 'bg-amber-500',
                            'out_of_order' => 'bg-slate-500',
                            default => 'bg-slate-500',
                        } }}">
                        {{ strtoupper($room->status_label) }}
                    </span>
                    @if ($room->images_count > 1)
                        <span class="absolute bottom-3 end-3 inline-flex items-center gap-1 rounded-full bg-black/60 px-2 py-0.5 text-[10px] font-semibold text-white">
                            <i class="bi bi-images"></i> {{ $room->images_count }}
                        </span>
                    @endif
                </div>

                {{-- Body --}}
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.rooms.room_label', ['number' => $room->room_number]) }}</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ $room->hotel->name ?? __('admin.common.na') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.rooms.toggle', $room) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex h-7 items-center rounded-full px-2 text-[9px] font-bold cursor-pointer transition-colors
                                {{ $room->is_active ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                {{ $room->is_active ? __('admin.rooms.on') : __('admin.rooms.off') }}
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-3">
                        <span class="inline-flex items-center gap-1"><i class="bi bi-tag"></i> {{ $room->roomType->name ?? '—' }}</span>
                        @if ($room->floor !== null)
                            <span class="inline-flex items-center gap-1"><i class="bi bi-layers"></i> F{{ $room->floor }}</span>
                        @endif
                    </div>

                    @if ($room->amenities->count())
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach ($room->amenities->take(4) as $amenity)
                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[9px] font-semibold text-slate-600 dark:text-slate-400">
                                    {{ $amenity->name }}
                                </span>
                            @endforeach
                            @if ($room->amenities->count() > 4)
                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[9px] font-semibold text-slate-500">+{{ $room->amenities->count() - 4 }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center border-t border-slate-100 dark:border-slate-800 divide-x divide-slate-100 dark:divide-slate-800">
                    <a href="{{ route('admin.rooms.show', $room) }}" title="{{ __("admin.action.view") }}" class="flex-1 flex items-center justify-center gap-1 px-3 py-2.5 text-xs font-semibold text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.rooms.edit', $room) }}" title="{{ __("admin.action.edit") }}" class="flex-1 flex items-center justify-center gap-1 px-3 py-2.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="flex-1" x-data x-on:submit="return confirm('{{ __("admin.confirm.delete") }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="{{ __("admin.action.delete") }}" class="flex items-center justify-center gap-1 w-full px-3 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full px-6 py-16 text-center rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-indigo-50/60 dark:bg-slate-900">
                <i class="bi bi-door-open text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.rooms") }}</p>
            </div>
        @endforelse
    </div>

    @if ($rooms->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <p class="text-xs text-slate-400 dark:text-slate-500">
                {{ __('admin.pagination.showing', ['from' => $rooms->firstItem() ?? 0, 'to' => $rooms->lastItem() ?? 0, 'total' => $rooms->total()]) }}
            </p>
            <div>{{ $rooms->withQueryString()->links() }}</div>
        </div>
    @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-multi-image]').forEach(input => {
                input.addEventListener('change', () => {
                    const preview = input.closest('.rounded-2xl').querySelector('[data-upload-preview]');
                    if (!preview) return;
                    preview.innerHTML = '';
                    Array.from(input.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const div = document.createElement('div');
                            div.className = 'relative w-16 h-16 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700';
                            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" />`;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            });
        });
    </script>
    @endpush

</x-layouts.admin>
