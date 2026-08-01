@props(['roomType' => null, 'hotels' => collect(), 'submitLabel' => null])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Basic Info ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                <i class="bi bi-door-open text-sm text-indigo-600 dark:text-indigo-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.form.basic_info') }}</h3>
        </div>

        <div class="space-y-4">
            {{-- Hotel --}}
            <div>
                <label for="hotel_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_type_form.hotel') }}</label>
                <select id="hotel_id" name="hotel_id" required
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">{{ __('admin.room_type_form.select_hotel') }}</option>
                    @foreach ($hotels as $id => $name)
                        <option value="{{ $id }}" {{ (int) old('hotel_id', $roomType?->hotel_id) === (int) $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('hotel_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="floating-label {{ old('name', $roomType?->name) ? 'filled' : '' }}">
                <label for="name">{{ __('admin.room_type_form.name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $roomType?->name) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-tag absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.form.description') }}</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all resize-none">{{ old('description', $roomType?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ── Pricing & Capacity ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950">
                <i class="bi bi-currency-dollar text-sm text-emerald-600 dark:text-emerald-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.room_type_form.pricing_capacity') }}</h3>
        </div>

        <div class="space-y-4">
            {{-- Base Price --}}
            <div class="floating-label {{ old('base_price', $roomType?->base_price) ? 'filled' : '' }}">
                <label for="base_price">{{ __('admin.room_type_form.base_price') }}</label>
                <input type="number" id="base_price" name="base_price" step="0.01" min="0"
                    value="{{ old('base_price', $roomType?->base_price) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-cash absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('base_price')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Max Guests + Max Children --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="max_guests" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_type_form.max_guests') }}</label>
                    <input type="number" id="max_guests" name="max_guests" min="1" max="20"
                        value="{{ old('max_guests', $roomType?->max_guests ?? 2) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                        required />
                    @error('max_guests')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="max_children" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.room_type_form.max_children') }}</label>
                    <input type="number" id="max_children" name="max_children" min="0" max="10"
                        value="{{ old('max_children', $roomType?->max_children ?? 0) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    @error('max_children')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Status --}}
            <div class="pt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $roomType?->is_active ?? true) ? 'checked' : '' }} />
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ms-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.form.active') }}</span>
                </label>
                <p class="mt-1.5 ms-14 text-xs text-slate-400 dark:text-slate-500">{{ __('admin.room_type_form.inactive_hint') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Submit ── --}}
<div class="mt-8 flex items-center justify-end gap-3">
    <a href="{{ route('admin.room-types.index') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
        <i class="bi bi-x-lg text-sm"></i> {{ __('admin.form.cancel') }}
    </a>
    <button type="submit"
        class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
        <i class="bi bi-check-lg text-base"></i> {{ $submitLabel ?? __('admin.form.save') }}
    </button>
</div>
