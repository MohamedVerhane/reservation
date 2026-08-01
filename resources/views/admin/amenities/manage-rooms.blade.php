<x-layouts.admin :title="__('admin.amenities.manage_rooms_title', ['name' => $amenity->name])" active="amenities">

    {{-- ── Breadcrumb ── --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.amenities.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.amenities') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $amenity->name }}</span>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('admin.amenities.manage_rooms_breadcrumb') }}</span>
        </div>
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

    {{-- ── Amenity Info Card ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 overflow-hidden mb-6">
        <div class="h-24 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
            @if ($amenity->icon)
                <i class="{{ $amenity->icon }} text-5xl text-white/30"></i>
            @else
                <i class="bi bi-gear-wide-connected text-5xl text-white/30"></i>
            @endif
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-950 text-xl text-indigo-600 dark:text-indigo-400">
                        @if ($amenity->icon)
                            <i class="{{ $amenity->icon }}"></i>
                        @else
                            <i class="bi bi-plus-circle"></i>
                        @endif
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $amenity->name }}</h1>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider
                                {{ $amenity->is_active
                                    ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400'
                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                                {{ $amenity->status_label }}
                            </span>
                        </div>
                        @if ($amenity->icon)
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 font-mono">{{ $amenity->icon }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.amenities.edit', $amenity) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="bi bi-pencil text-sm"></i> {{ __('admin.action.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $amenity->rooms_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.amenities.assigned_rooms_stat', ['count' => $amenity->rooms_count]) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold {{ $amenity->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                {{ $amenity->is_active ? __('admin.filter.active') : __('admin.filter.inactive') }}
            </p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.th.status') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $hotels->count() }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.amenities.hotels_stat', ['count' => $hotels->count()]) }}</p>
        </div>
    </div>

    {{-- ── Currently Assigned Rooms ── --}}
    @if ($amenity->rooms->count())
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.amenities.currently_assigned') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.room') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.hotel') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.type') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.floor') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($amenity->rooms as $room)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $room->room_number }}</td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-400">{{ $room->hotel->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-400">{{ $room->roomType?->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-400">{{ $room->floor ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ match($room->status) {
                                            'available' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400',
                                            'occupied' => 'bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400',
                                            'maintenance' => 'bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400',
                                            'out_of_order' => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400',
                                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-500',
                                        } }}">
                                        {{ $room->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ── Assign Rooms Form ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6"
        x-data="roomAssigner()" x-init="init()">

        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-950">
                <i class="bi bi-plus-circle text-sm text-purple-600 dark:text-purple-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.amenities.assign_rooms_section') }}</h3>
        </div>

        <form method="POST" action="{{ route('admin.amenities.assign-rooms', $amenity) }}" data-ajax-action data-success="{{ __('admin.amenity.room_assignments_updated') }}">
            @csrf

            {{-- Hotel Selector --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.amenities.filter_by_hotel') }}</label>
                <select x-model="selectedHotelId" @change="fetchRooms()"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">{{ __('admin.filter.all_hotels') }}</option>
                    @foreach ($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div class="mb-4">
                <div class="relative">
                    <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" x-model="searchQuery" @input="filterRooms()" placeholder="{{ __('admin.amenities.search_rooms_placeholder') }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                </div>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="py-8 text-center">
                <i class="bi bi-arrow-repeat text-2xl text-slate-400 animate-spin block mb-2"></i>
                <p class="text-xs text-slate-400">{{ __('admin.amenities.loading_rooms') }}</p>
            </div>

            {{-- Room Checkboxes --}}
            <div x-show="!loading" class="max-h-96 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-800">
                <template x-if="filteredRooms.length === 0">
                    <div class="px-6 py-8 text-center">
                        <i class="bi bi-door-open text-2xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                        <p class="text-sm text-slate-400 dark:text-slate-500">{{ __("admin.empty.rooms") }}</p>
                    </div>
                </template>
                <template x-for="room in filteredRooms" :key="room.id">
                    <label class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <input type="checkbox" name="room_ids[]" :value="room.id"
                            :checked="selectedRooms.includes(room.id)"
                            @change="toggleRoom(room.id)"
                            class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500/20" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200" x-text="room.label"></p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                <span x-show="room.floor">{{ __('admin.rooms.floor_prefix') }} <span x-text="room.floor"></span> · </span>
                                <span x-text="room.type_name || '{{ __("admin.rooms.room") }}'"></span>
                            </p>
                        </div>
                        <span x-show="selectedRooms.includes(room.id)"
                            class="inline-flex items-center gap-1 rounded-full bg-indigo-100 dark:bg-indigo-950 px-2 py-0.5 text-[10px] font-bold text-indigo-600 dark:text-indigo-400">
                            <i class="bi bi-check-lg"></i> {{ __('admin.amenities.assigned_badge') }}
                        </span>
                    </label>
                </template>
            </div>

            {{-- Selected Count --}}
            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    <span x-text="selectedRooms.length"></span> {{ __('admin.amenities.rooms_selected') }}
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.amenities.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        {{ __('admin.action.cancel') }}
                    </a>
                    <button type="submit"
                        class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
                        <i class="bi bi-check-lg text-base"></i> {{ __('admin.amenities.save_assignments') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function roomAssigner() {
            return {
                selectedHotelId: '',
                searchQuery: '',
                rooms: [],
                filteredRooms: [],
                selectedRooms: @js($selectedRoomIds),
                loading: false,

                init() {
                    this.fetchRooms();
                },

                async fetchRooms() {
                    this.loading = true;
                    try {
                        let url = '{{ route("admin.amenities.rooms.ajax") }}';
                        if (this.selectedHotelId) {
                            url += '?hotel_id=' + this.selectedHotelId;
                        }
                        const res = await fetch(url);
                        this.rooms = await res.json();
                        this.filterRooms();
                    } catch (e) {
                        this.rooms = [];
                        this.filteredRooms = [];
                    }
                    this.loading = false;
                },

                filterRooms() {
                    if (!this.searchQuery) {
                        this.filteredRooms = this.rooms;
                        return;
                    }
                    const q = this.searchQuery.toLowerCase();
                    this.filteredRooms = this.rooms.filter(r =>
                        r.room_number.toLowerCase().includes(q) ||
                        (r.type_name && r.type_name.toLowerCase().includes(q))
                    );
                },

                toggleRoom(roomId) {
                    const idx = this.selectedRooms.indexOf(roomId);
                    if (idx > -1) {
                        this.selectedRooms.splice(idx, 1);
                    } else {
                        this.selectedRooms.push(roomId);
                    }
                },
            }
        }
    </script>
    @endpush

</x-layouts.admin>
