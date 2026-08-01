@props(['hotel' => null, 'submitLabel' => null])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Basic Info ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                <i class="bi bi-building text-sm text-indigo-600 dark:text-indigo-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.form.basic_info') }}</h3>
        </div>

        <div class="space-y-4">
            {{-- Name --}}
            <div class="floating-label {{ old('name', $hotel?->name) ? 'filled' : '' }}">
                <label for="name">{{ __('admin.form.hotel_name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $hotel?->name) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-building absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div class="floating-label {{ old('slug', $hotel?->slug) ? 'filled' : '' }}">
                <label for="slug">{{ __('admin.form.slug') }}</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $hotel?->slug) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    placeholder="{{ __('admin.form.slug_placeholder') }}" />
                <i class="bi bi-link-45deg absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('slug')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.form.description') }}</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all resize-none">{{ old('description', $hotel?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Star Rating --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.form.star_rating') }}</label>
                <div class="flex items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer group/star">
                            <input type="radio" name="star_rating" value="{{ $i }}" class="hidden peer"
                                {{ (int) old('star_rating', $hotel?->star_rating) === $i ? 'checked' : '' }} />
                            <i class="bi bi-star-fill text-2xl transition-colors
                                peer-checked:text-amber-400 text-slate-200 dark:text-slate-700 group-hover/star:text-amber-300"></i>
                        </label>
                    @endfor
                    <span class="ms-2 text-xs text-slate-400 dark:text-slate-500" id="starLabel">
                        {{ $hotel?->star_rating_label ?? __('admin.form.standard') }}
                    </span>
                </div>
                @error('star_rating')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ── Contact & Location ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-rose-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950">
                <i class="bi bi-geo-alt text-sm text-emerald-600 dark:text-emerald-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.form.contact_location') }}</h3>
        </div>

        <div class="space-y-4">
            {{-- Email --}}
            <div class="floating-label {{ old('email', $hotel?->email) ? 'filled' : '' }}">
                <label for="email">{{ __('admin.form.email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', $hotel?->email) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-envelope absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="floating-label {{ old('phone', $hotel?->phone) ? 'filled' : '' }}">
                <label for="phone">{{ __('admin.form.phone') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $hotel?->phone) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-telephone absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('phone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Address --}}
            <div class="floating-label {{ old('address', $hotel?->address) ? 'filled' : '' }}">
                <label for="address">{{ __('admin.form.address') }}</label>
                <input type="text" id="address" name="address" value="{{ old('address', $hotel?->address) }}"
                    class="input-icon w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                    required />
                <i class="bi bi-pin-map absolute start-4 top-1/2 -translate-y-1/2 text-slate-400 text-base z-10 pointer-events-none"></i>
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- City + Country --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="floating-label {{ old('city', $hotel?->city) ? 'filled' : '' }}">
                    <label for="city">{{ __('admin.form.city') }}</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $hotel?->city) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                        required />
                    @error('city')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="floating-label {{ old('country', $hotel?->country) ? 'filled' : '' }}">
                    <label for="country">{{ __('admin.form.country') }}</label>
                    <input type="text" id="country" name="country" value="{{ old('country', $hotel?->country) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all"
                        required />
                    @error('country')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Lat/Lng --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="latitude" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.form.latitude') }}</label>
                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $hotel?->latitude) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    @error('latitude')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="longitude" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.form.longitude') }}</label>
                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $hotel?->longitude) }}"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                    @error('longitude')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Image + Status ── --}}
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Cover Image --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-cyan-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-950">
                <i class="bi bi-image text-sm text-purple-600 dark:text-purple-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.form.cover_image') }}</h3>
        </div>

        {{-- Current image preview --}}
        @if ($hotel?->cover_image)
            <div class="mb-4 relative group/img">
                <img src="{{ $hotel->cover_image_url }}" alt="{{ $hotel->name }}"
                    class="w-full h-48 object-cover rounded-xl border border-slate-200 dark:border-slate-700" />
                <div class="absolute inset-0 rounded-xl bg-black/0 group-hover/img:bg-black/20 transition-colors"></div>
            </div>
        @endif

        <div class="relative" data-image-upload>
            <label for="cover_image"
                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-800/50 hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-all">
                <div class="flex flex-col items-center justify-center pt-3 pb-3">
                    <i class="bi bi-cloud-arrow-up text-2xl text-slate-400 dark:text-slate-500 mb-1"></i>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        {{ __('admin.form.click_upload') }}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ __('admin.form.image_formats') }}</p>
                </div>
                <input type="file" id="cover_image" name="cover_image" accept="image/*" class="hidden"
                    data-image-input />
            </label>
        </div>
        <div id="imagePreview" class="mt-3 hidden">
            <div class="relative inline-block">
                <img src="" alt="Preview" class="h-20 w-20 object-cover rounded-lg border border-slate-200 dark:border-slate-700" data-image-preview />
                <button type="button" data-image-remove
                    class="absolute -top-1.5 -end-1.5 h-5 w-5 rounded-full bg-red-500 text-white flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
        @error('cover_image')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-indigo-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-950">
                <i class="bi bi-toggle2-on text-sm text-amber-600 dark:text-amber-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.form.status') }}</h3>
        </div>

        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                {{ old('is_active', $hotel?->is_active ?? true) ? 'checked' : '' }} />
            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            <span class="ms-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.form.active') }}</span>
        </label>
        <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">{{ __('admin.form.inactive_hint') }}</p>
    </div>
</div>

{{-- ── Submit ── --}}
<div class="mt-8 flex items-center justify-end gap-3">
    <a href="{{ route('admin.hotels.index') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
        <i class="bi bi-x-lg text-sm"></i> {{ __('admin.form.cancel') }}
    </a>
    <button type="submit"
        class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
        <i class="bi bi-check-lg text-base"></i> {{ $submitLabel ?? __('admin.form.save') }}
    </button>
</div>
