@props(['gallery' => null, 'hotels' => collect(), 'submitLabel' => null])

<div class="space-y-6">

    {{-- ── Gallery Info ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-sky-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                <i class="bi bi-images text-sm text-indigo-600 dark:text-indigo-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.gallery_form.gallery_information') }}</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Hotel --}}
            <div>
                <label for="hotel_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.gallery_form.hotel') }}</label>
                <select id="hotel_id" name="hotel_id" required
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                    <option value="">{{ __('admin.gallery_form.select_hotel') }}</option>
                    @foreach ($hotels as $id => $name)
                        <option value="{{ $id }}" {{ (int) old('hotel_id', $gallery?->hotel_id) === (int) $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('hotel_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.gallery_form.title') }}</label>
                <input type="text" id="title" name="title" value="{{ old('title', $gallery?->title) }}" required
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                @error('title')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.gallery_form.sort_order') }}</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $gallery?->sort_order ?? 0) }}" min="0" max="9999"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Description --}}
        <div class="mt-4">
            <label for="description" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.form.description') }}</label>
            <textarea id="description" name="description" rows="3"
                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all resize-none">{{ old('description', $gallery?->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- ── Images ── --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-lime-50/60 dark:bg-slate-900 p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-950">
                <i class="bi bi-image text-sm text-purple-600 dark:text-purple-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.gallery_form.images') }}</h3>
        </div>

        {{-- Existing images with delete buttons (edit mode) --}}
        @if ($gallery && $gallery->images->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-5" data-images-grid>
                @foreach ($gallery->images()->ordered()->get() as $img)
                    <div class="relative group/img rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-square" data-image-id="{{ $img->id }}">
                        <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/40 transition-colors flex items-center justify-center opacity-0 group-hover/img:opacity-100">
                            <form method="POST" action="{{ route('admin.galleries.images.delete', [$gallery, $img]) }}"
                                x-data x-on:submit="return confirm('{{ __("admin.confirm.delete_image") }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="{{ __('admin.action.delete') }}" class="h-8 w-8 rounded-full bg-white/90 dark:bg-slate-800/90 flex items-center justify-center text-red-500 hover:bg-white dark:hover:bg-slate-800 transition-colors shadow">
                                    <i class="bi bi-trash3 text-xs"></i>
                                </button>
                            </form>
                        </div>
                        <span class="absolute bottom-2 start-2 inline-flex items-center rounded-full bg-black/50 backdrop-blur-sm px-2 py-0.5 text-[9px] font-bold text-white">
                            #{{ $img->sort_order }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- File upload dropzone (create mode) --}}
        @if (!$gallery)
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.gallery_form.upload_images') }}</label>
                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-800/50 hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-all">
                    <div class="flex flex-col items-center justify-center pt-4 pb-4">
                        <i class="bi bi-cloud-arrow-up text-3xl text-slate-400 dark:text-slate-500 mb-2"></i>
                        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.form.click_upload') }}</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.gallery_form.image_formats') }}</p>
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
        <a href="{{ route('admin.galleries.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 shadow-sm transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-md active:scale-[0.98]">
            <i class="bi bi-x-lg text-sm"></i> {{ __('admin.form.cancel') }}
        </a>
        <button type="submit"
            class="btn-gradient inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 active:scale-[0.98]">
            <i class="bi bi-check-lg text-base"></i> {{ $submitLabel ?? __('admin.form.save') }}
        </button>
    </div>
</div>
