@props(['amenity' => null, 'submitLabel' => null])

<div class="space-y-6">
    {{-- ── Amenity Info ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                <i class="bi bi-gear-wide-connected text-sm text-indigo-600 dark:text-indigo-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.amenity_form.amenity_information') }}</h3>
        </div>

        <div class="space-y-4" x-data="{ icon: '{{ old('icon', $amenity?->icon ?? '') }}' }">
            {{-- Name --}}
            <div class="floating-label {{ old('name', $amenity?->name) ? 'filled' : '' }}">
                <label for="name">{{ __('admin.amenity_form.name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $amenity?->name) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-tag absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icon --}}
            <div>
                <label for="icon" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.amenity_form.icon_class') }}</label>
                <input type="text" id="icon" name="icon" x-model="icon"
                    value="{{ old('icon', $amenity?->icon) }}"
                    placeholder="{{ __('admin.amenity_form.icon_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all font-mono" />
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ __('admin.amenity_form.icon_help') }} <code class="bg-slate-100 dark:bg-slate-800 px-1 rounded">bi bi-wifi</code></p>
                @error('icon')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Live Icon Preview --}}
            <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-800/50 px-4 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">{{ __('admin.amenity_form.preview') }}</p>
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-950 text-lg text-indigo-600 dark:text-indigo-400">
                        <template x-if="icon">
                            <i :class="icon"></i>
                        </template>
                        <template x-if="!icon">
                            <i class="bi bi-plus-circle"></i>
                        </template>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200" x-text="'{{ old('name', $amenity?->name ?? __('admin.amenity_form.amenity_name_default')) }}'"></p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            <template x-if="icon">
                                <code class="text-[10px] bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded" x-text="icon"></code>
                            </template>
                            <template x-if="!icon">
                                <span>{{ __('admin.amenity_form.no_icon') }}</span>
                            </template>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Status ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950">
                <i class="bi bi-toggle2-on text-sm text-emerald-600 dark:text-emerald-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.form.status') }}</h3>
        </div>

        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $amenity?->is_active ?? true) ? 'checked' : '' }}
                class="sr-only peer" />
            <span class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full border-2 border-transparent bg-slate-200 dark:bg-slate-700 transition-colors peer-checked:bg-emerald-500 peer-focus:ring-2 peer-focus:ring-emerald-500/20">
                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition-transform peer-checked:translate-x-5"></span>
            </span>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.form.active') }}</span>
            <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('admin.amenity_form.active_hint') }}</span>
        </label>
    </div>

    {{-- ── Submit ── --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.amenities.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
            <i class="bi bi-x-lg text-sm"></i> {{ __('admin.form.cancel') }}
        </a>
        <button type="submit"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
            <i class="bi bi-check-lg text-base"></i> {{ $submitLabel ?? __('admin.form.save') }}
        </button>
    </div>
</div>
