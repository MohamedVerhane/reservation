@props(['reviews'])

<div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-950">
                <i class="bi bi-star-half text-sm text-amber-600 dark:text-amber-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.dashboard.latest_reviews') }}</h3>
        </div>
        <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('admin.dashboard.view_all') }}</a>
    </div>

    <div class="divide-y divide-slate-100 dark:divide-slate-800">
        @forelse ($reviews as $review)
            <div class="px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $review->user->name ?? '—' }}</p>
                            <span class="text-xs text-slate-400 dark:text-slate-500 shrink-0">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $review->hotel->name ?? '—' }}</p>

                        {{-- Stars --}}
                        <div class="mt-1.5 flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill text-xs {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                            @endfor
                            <span class="ms-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $review->rating }}/5</span>
                        </div>

                        @if ($review->comment)
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">{{ $review->comment }}</p>
                        @endif

                        @if ($review->reply)
                            <div class="mt-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900 px-3 py-2">
                                <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-0.5">{{ __('admin.dashboard.reply') }}</p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2">{{ $review->reply }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <i class="bi bi-chat-dots text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                <p class="text-sm text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.no_reviews') }}</p>
            </div>
        @endforelse
    </div>
</div>
