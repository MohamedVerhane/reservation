<x-layouts.admin :title="__('admin.title.edit', ['resource' => __('admin.nav.hotels')])" active="hotels">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.hotels.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.hotels') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $hotel->name }}</span>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('admin.action.edit') }}</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('admin.form.fix_errors') }}
            </p>
            <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.hotels.update', $hotel) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-admin.hotel-form :hotel="$hotel" :submitLabel="__('admin.hotels.update_submit')" />
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ── Auto-generate slug from name ──
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', () => {
                    slugInput.value = nameInput.value
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-|-$/g, '');
                });
            }

            // ── Star rating label ──
            const labels = { 1: '{{ __("admin.hotels.star_economy") }}', 2: '{{ __("admin.hotels.star_budget") }}', 3: '{{ __("admin.hotels.star_standard") }}', 4: '{{ __("admin.hotels.star_superior") }}', 5: '{{ __("admin.hotels.star_luxury") }}' };
            const starLabel = document.getElementById('starLabel');
            document.querySelectorAll('input[name="star_rating"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    if (starLabel) starLabel.textContent = labels[radio.value] || '{{ __("admin.hotels.star_standard") }}';
                });
            });

            // ── Image preview ──
            const input = document.querySelector('[data-image-input]');
            const preview = document.getElementById('imagePreview');
            const previewImg = document.querySelector('[data-image-preview]');
            const removeBtn = document.querySelector('[data-image-remove]');

            if (input) {
                input.addEventListener('change', () => {
                    const file = input.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            if (previewImg) previewImg.src = e.target.result;
                            preview?.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    input.value = '';
                    preview?.classList.add('hidden');
                });
            }
        });
    </script>
    @endpush

</x-layouts.admin>
