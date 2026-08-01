@props(['selected' => 0, 'name' => 'rating'])

<div class="flex gap-1 items-center" x-data="{ rating: {{ $selected }} }">
    @for($i = 1; $i <= 5; $i++)
        <button
            type="button"
            @click="rating = {{ $i }}"
            class="text-2xl transition-colors duration-200 hover:scale-110 focus:outline-none"
            :class="rating >= {{ $i }} ? 'text-[var(--gold)]' : 'text-[var(--border)]'"
            aria-label="{{ __('reviews.star_count', ['count' => $i]) }}"
        >
            <i class="bi bi-star-fill"></i>
        </button>
    @endfor
    <input type="hidden" name="{{ $name }}" :value="rating" />
    <span class="ml-2 text-sm text-[var(--text-muted)]" x-text="rating > 0 ? rating + '/5' : ''"></span>
</div>
