@props(['room' => null, 'hotels' => collect(), 'roomTypes' => collect(), 'roomTypesByHotel' => collect(), 'amenities' => collect(), 'submitLabel' => null])

@php
    $selectedAmenities = old('amenities', $room?->amenities->pluck('id')->toArray() ?? []);
@endphp

<div class="space-y-6">

    {{-- ── Basic Info ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                <i class="bi bi-door-open text-sm text-indigo-600 dark:text-indigo-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.room_form.room_information') }}</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Hotel --}}
            <div>
                <label for="hotel_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_form.hotel') }}</label>
                <select id="hotel_id" name="hotel_id" required
                    data-room-types='@json($roomTypesByHotel)'
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">{{ __('admin.room_form.select_hotel') }}</option>
                    @foreach ($hotels as $id => $name)
                        <option value="{{ $id }}" {{ (int) old('hotel_id', $room?->hotel_id) === (int) $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('hotel_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Room Type --}}
            <div>
                <label for="room_type_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_form.room_type') }}</label>
                <select id="room_type_id" name="room_type_id" required
                    data-empty="{{ __('admin.room_form.no_room_types') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">{{ __('admin.room_form.select_type') }}</option>
                    @foreach ($roomTypes as $id => $name)
                        <option value="{{ $id }}" {{ (int) old('room_type_id', $room?->room_type_id) === (int) $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('room_type_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Room Number --}}
            <div class="floating-label {{ old('room_number', $room?->room_number) ? 'filled' : '' }}">
                <label for="room_number">{{ __('admin.room_form.room_number') }}</label>
                <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $room?->room_number) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-hash absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('room_number')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Floor --}}
            <div class="floating-label {{ old('floor', $room?->floor) ? 'filled' : '' }}">
                <label for="floor">{{ __('admin.room_form.floor') }}</label>
                <input type="number" id="floor" name="floor" value="{{ old('floor', $room?->floor) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                <i class="bi bi-layers absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('floor')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_form.status') }}</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['available' => __('admin.room_status.available'), 'occupied' => __('admin.room_status.occupied'), 'maintenance' => __('admin.room_status.maintenance'), 'out_of_order' => __('admin.room_status.out_of_order')] as $val => $label)
                        <label class="relative flex items-center gap-2 rounded-xl border px-3 py-2.5 cursor-pointer transition-all
                            {{ old('status', $room?->status) === $val
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/30 ring-2 ring-indigo-500/20'
                                : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' }}">
                            <input type="radio" name="status" value="{{ $val }}" class="hidden peer"
                                {{ old('status', $room?->status ?? 'available') === $val ? 'checked' : '' }} />
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ match($val) {
                                'available' => 'bg-emerald-500',
                                'occupied' => 'bg-red-500',
                                'maintenance' => 'bg-amber-500',
                                'out_of_order' => 'bg-slate-400',
                            } }} peer-checked:ring-2 peer-checked:ring-offset-1 peer-checked:ring-indigo-400"></span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('status')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active --}}
            <div class="flex items-center gap-3 pt-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $room?->is_active ?? true) ? 'checked' : '' }} />
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ms-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.form.active') }}</span>
                </label>
            </div>
        </div>
    </div>

    {{-- ── Amenities ── --}}
    @if ($amenities->count())
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
            <div class="flex items-center gap-2 mb-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-950">
                    <i class="bi bi-gear-wide-connected text-sm text-amber-600 dark:text-amber-400"></i>
                </span>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.room_form.amenities') }}</h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach ($amenities as $amenity)
                    <label class="flex items-center gap-2 rounded-xl border px-3 py-2.5 cursor-pointer transition-all
                        {{ in_array($amenity->id, $selectedAmenities)
                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/30'
                            : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' }}">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }} />
                        @if ($amenity->icon)
                            <i class="bi {{ $amenity->icon }} text-sm text-slate-500 dark:text-slate-400"></i>
                        @endif
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Images ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-950">
                <i class="bi bi-images text-sm text-purple-600 dark:text-purple-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.room_form.images') }}</h3>
        </div>

        {{-- Existing images --}}
        @if ($room?->images?->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-5" data-images-grid>
                @foreach ($room->images()->ordered()->get() as $img)
                    <div class="relative group/img rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-square" data-image-id="{{ $img->id }}">
                        <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/40 transition-colors flex items-center justify-center gap-2 opacity-0 group-hover/img:opacity-100">
                            @unless ($img->is_primary)
                                <form method="POST" action="{{ route('admin.rooms.images.primary', [$room, $img]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ __('admin.room_form.set_primary') }}" class="h-8 w-8 rounded-full bg-white/90 dark:bg-slate-800/90 flex items-center justify-center text-amber-500 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                                        <i class="bi bi-star-fill text-xs"></i>
                                    </button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('admin.rooms.images.delete', [$room, $img]) }}"
                                x-data x-on:submit="return confirm('{{ __("admin.confirm.delete_image") }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="{{ __('admin.action.delete') }}" class="h-8 w-8 rounded-full bg-white/90 dark:bg-slate-800/90 flex items-center justify-center text-red-500 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                                    <i class="bi bi-trash3 text-xs"></i>
                                </button>
                            </form>
                        </div>
                        @if ($img->is_primary)
                            <span class="absolute top-2 start-2 inline-flex items-center rounded-full bg-amber-400 px-2 py-0.5 text-[9px] font-bold text-white shadow">{{ __('admin.room_form.primary_badge') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Upload --}}
        @if (!$room)
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_form.upload_images') }}</label>
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-800/50 hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-all">
                    <div class="flex flex-col items-center justify-center pt-3 pb-3">
                        <i class="bi bi-cloud-arrow-up text-2xl text-slate-400 dark:text-slate-500 mb-1"></i>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.room_form.click_upload_images') }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('admin.room_form.image_formats_multi') }}</p>
                    </div>
                    <input type="file" name="images[]" accept="image/*" multiple class="hidden" data-multi-image />
                </label>
                <div class="mt-3 flex flex-wrap gap-2" data-upload-preview></div>
                @error('images')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        @endif
    </div>

    {{-- ── Submit ── --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.rooms.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
            <i class="bi bi-x-lg text-sm"></i> {{ __('admin.form.cancel') }}
        </a>
        <button type="submit"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
            <i class="bi bi-check-lg text-base"></i> {{ $submitLabel ?? __('admin.form.save') }}
        </button>
    </div>
</div>
