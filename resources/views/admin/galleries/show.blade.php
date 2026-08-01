<x-layouts.admin title="{{ $gallery->title }}" active="galleries">

    {{-- ═══ Breadcrumb ═══ --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.galleries.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.galleries') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $gallery->title }}</span>
        </div>
    </div>

    {{-- ═══ Flash Messages ═══ --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ═══ Header Card ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-rose-50/60 dark:bg-slate-900 overflow-hidden mb-6">
        @if ($gallery->images->count())
            <div class="grid grid-cols-5 gap-px bg-slate-200 dark:bg-slate-700 h-48 sm:h-64">
                @foreach ($gallery->images->take(5) as $img)
                    <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover" />
                @endforeach
            </div>
        @else
            <div class="h-48 sm:h-64 bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                <i class="bi bi-images text-6xl text-white/30"></i>
            </div>
        @endif
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $gallery->title }}</h1>
                    <div class="flex items-center gap-3 mt-1 text-sm text-slate-500 dark:text-slate-400">
                        <span><i class="bi bi-building me-1"></i>{{ $gallery->hotel->name ?? '—' }}</span>
                        <span><i class="bi bi-sort-numeric-up me-1"></i>{{ __('admin.galleries.order') }}: {{ $gallery->sort_order }}</span>
                        <span><i class="bi bi-image me-1"></i>{{ __('admin.galleries.image_count', ['count' => $gallery->images_count]) }}</span>
                    </div>
                    @if ($gallery->description)
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $gallery->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.galleries.edit', $gallery) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="bi bi-pencil text-sm"></i> {{ __('admin.action.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.galleries.destroy', $gallery) }}"
                        x-data x-on:submit="return confirm('{{ __("admin.confirm.gallery_delete") }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-4 py-2 text-sm font-semibold text-red-700 dark:text-red-400 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                            <i class="bi bi-trash3 text-sm"></i> {{ __('admin.action.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Stats Row ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-cyan-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $gallery->images_count }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.galleries.images_count_label') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-indigo-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $gallery->sort_order }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.galleries.sort_order') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-sky-50/60 dark:bg-slate-900 p-4 text-center">
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white text-sm">{{ $gallery->hotel->name ?? '—' }}</p>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.th.hotel') }}</p>
        </div>
    </div>

    {{-- ═══ Upload Form ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-lime-50/60 dark:bg-slate-900 p-6 mb-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
            <i class="bi bi-cloud-arrow-up text-base me-1"></i> {{ __('admin.galleries.upload_images') }}
        </h3>
        <form method="POST" action="{{ route('admin.galleries.images.upload', $gallery) }}" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                <div class="flex-1">
                    <label for="images" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">{{ __('admin.galleries.select_images') }}</label>
                    <input type="file" name="images[]" id="images" accept="image/*" multiple required
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 outline-none" />
                    @error('images')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                    <i class="bi bi-upload text-sm"></i> {{ __('admin.action.upload') }}
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ Images Grid ═══ --}}
    @if ($gallery->images->count())
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-yellow-50/60 dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                <i class="bi bi-images text-base me-1"></i> {{ __('admin.galleries.gallery_images') }}
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($gallery->images as $image)
                    <div class="relative group/img rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-square">
                        <img src="{{ $image->url }}" alt="{{ $image->alt_text ?? $gallery->title }}" class="w-full h-full object-cover" />

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/40 transition-colors flex items-center justify-center opacity-0 group-hover/img:opacity-100">
                            <form method="POST" action="{{ route('admin.galleries.images.delete', [$gallery, $image]) }}"
                                x-data x-on:submit="return confirm('{{ __("admin.confirm.delete_image") }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="{{ __("admin.action.delete") }}"
                                    class="h-9 w-9 rounded-full bg-white/90 dark:bg-slate-800/90 flex items-center justify-center text-red-500 hover:bg-white dark:hover:bg-slate-800 transition-colors shadow-lg">
                                    <i class="bi bi-trash3 text-sm"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Caption overlay --}}
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent px-3 py-2">
                            <p class="text-[11px] font-semibold text-white truncate">{{ $image->alt_text ?? $image->caption ?? __('admin.galleries.image_fallback', ['order' => $image->sort_order]) }}</p>
                            @if ($image->caption)
                                <p class="text-[10px] text-white/70 truncate">{{ $image->caption }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 px-6 py-12 text-center">
            <i class="bi bi-image text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.empty.gallery_no_images') }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.empty.gallery_upload_hint') }}</p>
        </div>
    @endif

</x-layouts.admin>
