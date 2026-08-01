<x-layouts.admin :title="$roomType->name" active="rooms">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.room-types.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.room_types') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $roomType->name }}</span>
        </div>
    </div>

    {{-- ═══ Header Card ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $roomType->name }}</h1>
                    @if ($roomType->is_active)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-950 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ __('admin.filter.active') }}</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.filter.inactive') }}</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    <i class="bi bi-building me-1"></i>{{ $roomType->hotel->name ?? '—' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.room-types.edit', $roomType) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="bi bi-pencil text-sm"></i> {{ __('admin.action.edit') }}
                </a>
                <form method="POST" action="{{ route('admin.room-types.toggle', $roomType) }}" data-ajax-action>
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold shadow-sm transition-colors
                            {{ $roomType->is_active
                                ? 'border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100'
                                : 'border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100' }}">
                        <i class="bi bi-toggle2-on text-sm"></i> {{ $roomType->is_active ? __('admin.room_types.deactivate') : __('admin.room_types.activate') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ Stats ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($roomType->base_price, 2) }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.room_types.per_night') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $roomType->max_guests }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.room_types.max_guests') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $roomType->max_children }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.room_types.max_children') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $roomType->rooms_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.room_types.label_rooms') }}</p>
        </div>
    </div>

    {{-- ═══ Description ═══ --}}
    @if ($roomType->description)
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6 mb-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">{{ __('admin.room_types.description_section') }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $roomType->description }}</p>
        </div>
    @endif

    {{-- ═══ Rooms List ═══ --}}
    @if ($roomType->rooms->count())
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.room_types.rooms_section', ['count' => $roomType->rooms_count]) }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.room_number') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.floor') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.status') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.th.active') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($roomType->rooms as $room)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $room->room_number }}</td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-400">{{ $room->floor ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ match($room->status) {
                                            'available' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
                                            'occupied' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
                                            'maintenance' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
                                            default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                        } }}">
                                        {{ $room->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    @if ($room->is_active)
                                        <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill text-slate-300 dark:text-slate-600"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-layouts.admin>
