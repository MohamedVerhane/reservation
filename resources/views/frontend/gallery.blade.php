<x-layouts.frontend :title="__('meta.gallery')">
    <x-frontend.page-hero :title="__('gallery.title')" :subtitle="__('gallery.subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex flex-wrap gap-3 mb-10 justify-center reveal">
            <button type="button" class="filter-btn active" onclick="filterGallery('all')">
                {{ __('gallery.all') }}
            </button>
            @foreach($categories as $category)
                <button type="button" class="filter-btn" onclick="filterGallery('{{ Str::slug($category) }}')">
                    {{ $category }}
                </button>
            @endforeach
        </div>

        <div id="gallery-grid" class="columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4">
            @forelse($images as $image)
                <div class="break-inside-avoid gallery-item"
                     data-category="{{ Str::slug($image['category']) }}">
                    <div class="card overflow-hidden group cursor-pointer">
                        <div class="aspect-[4/3] bg-gradient-to-br from-[var(--surface-alt)] to-[var(--surface)] flex items-center justify-center relative">
                            @if($image['image_path'])
                                <img src="{{ asset('storage/' . $image['image_path']) }}"
                                     alt="{{ $image['caption'] }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[var(--gold)]/10 to-[var(--gold)]/5">
                                    <i class="bi bi-image text-[var(--gold)]/30 text-6xl"></i>
                                </div>
                            @endif

                            <button class="absolute top-4 end-4 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center backdrop-blur-sm hover:bg-[var(--gold)] hover:text-[var(--text-inverse)] transition-colors">
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>
                        </div>
                        <div class="p-5">
                            <span class="badge-gold mb-3">
                                {{ $image['category'] }}
                            </span>
                            <h3 class="text-lg font-bold text-[var(--text-primary)]">
                                {{ $image['caption'] }}
                            </h3>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <i class="bi bi-image text-[var(--text-muted)] text-6xl mb-4"></i>
                    <p class="text-[var(--text-secondary)] text-lg">
                        {{ __('gallery.no_images') }}
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    <x-frontend.cta-section />
</x-layouts.frontend>
