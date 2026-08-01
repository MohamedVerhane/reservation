<x-layouts.admin :title="__('admin.rooms.show_title', ['number' => $room->room_number])" active="rooms-actual">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.rooms.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.rooms') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $room->room_number }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 overflow-hidden mb-6">
        @if ($room->images->count())
            <div class="grid grid-cols-5 gap-px bg-slate-200 dark:bg-slate-700 h-48 sm:h-64">
                @foreach ($room->images->take(5) as $img)
                    <img src="{{ $img->url }}" alt="" class="w-full h-full object-cover" />
                @endforeach
            </div>
        @else
            <div class="h-48 sm:h-64 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                <i class="bi bi-door-open text-6xl text-white/30"></i>
            </div>
        @endif
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.rooms.room_label', ['number' => $room->room_number]) }}</h1>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                            {{ match($room->status) {
                                'available' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400',
                                'occupied' => 'bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400',
                                'maintenance' => 'bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400',
                                'out_of_order' => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400',
                            } }}">
                            {{ $room->status_label }}
                        </span>
                        @if (!$room->is_active)
                            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-semibold text-slate-500">{{ __('admin.filter.inactive') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-sm text-slate-500 dark:text-slate-400">
                        <span><i class="bi bi-building me-1"></i>{{ $room->hotel->name ?? '—' }}</span>
                        <span><i class="bi bi-tag me-1"></i>{{ $room->roomType->name ?? '—' }}</span>
                        @if ($room->floor !== null)
                            <span><i class="bi bi-layers me-1"></i>{{ __('admin.rooms.floor_label', ['floor' => $room->floor]) }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.rooms.edit', $room) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="bi bi-pencil text-sm"></i> {{ __('admin.action.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.rooms.toggle', $room) }}" data-ajax-action>
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold shadow-sm transition-colors
                            {{ match($room->status) {
                                'available' => 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400',
                                'occupied' => 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-950/30 dark:text-red-400',
                                'maintenance' => 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-400',
                                default => 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400',
                            } }}">
                            <i class="bi bi-arrow-repeat text-sm"></i> {{ __('admin.action.cycle_status') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">${{ number_format($room->roomType->base_price ?? 0, 2) }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.rooms.per_night') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $room->roomType->max_guests ?? '—' }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.rooms.max_guests') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $room->reservations_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.rooms.stats_bookings') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $room->images_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.rooms.stats_images') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Amenities --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('admin.rooms.amenities_section') }}</h3>
            @if ($room->amenities->count())
                <div class="flex flex-wrap gap-2">
                    @foreach ($room->amenities as $amenity)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 px-3 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-400">
                            @if ($amenity->icon)
                                <i class="bi {{ $amenity->icon }}"></i>
                            @endif
                            {{ $amenity->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 dark:text-slate-500 italic">{{ __('admin.rooms.no_amenities') }}</p>
            @endif
        </div>

        {{-- Quick Info --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('admin.rooms.details_section') }}</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.room_number') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $room->room_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.floor') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $room->floor ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.hotel') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $room->hotel->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.type') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $room->roomType->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.created_at') }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $room->created_at->translatedFormat(__('auth.date_format')) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload form --}}
    <div class="mt-6 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('admin.rooms.add_images') }}</h3>
        <form method="POST" action="{{ route('admin.rooms.images.upload', $room) }}" enctype="multipart/form-data" data-ajax-action data-success="{{ __('admin.room.image_uploaded') }}">
            @csrf
            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                <div class="flex-1">
                    <input type="file" name="image" accept="image/*" required
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 outline-none" />
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                    <i class="bi bi-upload text-sm"></i> {{ __('admin.action.upload') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Recent bookings --}}
    @if ($room->reservations->count())
        <div class="mt-6 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-rose-50/60 dark:bg-slate-900 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.rooms.recent_bookings') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('admin.th.guest') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('admin.th.check_in') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('admin.th.check_out') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('admin.th.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($room->reservations as $res)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-3 text-slate-800 dark:text-slate-200 font-semibold">{{ $res->user->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-400">{{ $res->check_in->translatedFormat(__('auth.date_format')) }}</td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-400">{{ $res->check_out->translatedFormat(__('auth.date_format')) }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ match($res->status) {
                                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
                                            'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
                                            'checked_in' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
                                            'checked_out' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
                                        } }}">
                                        {{ $res->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.admin>
