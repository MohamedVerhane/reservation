<x-frontend.dashboard-layout :title="__('auth.cd_reviews')">

    {{-- Page Header --}}
    <div class="mb-8 animate-fade-in-up" data-animate>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ __('auth.cd_reviews') }}</h1>
            <span class="inline-flex items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30 px-3 py-1 text-sm font-bold text-amber-700 dark:text-amber-400">
                {{ $reviews->total() }}
            </span>
        </div>
    </div>

    @if($reviews->count())
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-6 transition-all duration-300 hover:shadow-2xl animate-fade-in-up" data-animate style="animation-delay: {{ ($loop->index * 80) + 100 }}ms">
                    {{-- Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-building text-sm text-white"></i>
                            </div>
                            <div>
                                <a href="{{ route('frontend.hotel.show', $review->hotel->slug) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                    {{ $review->hotel->name }}
                                </a>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <x-frontend.star-rating :rating="$review->rating" size="sm" />
                                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('frontend.hotel.show', $review->hotel->slug) }}#review-{{ $review->id }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400 transition-all duration-300 hover:border-amber-300 dark:hover:border-amber-600 hover:text-amber-600 dark:hover:text-amber-400">
                                <i class="bi bi-pencil"></i>{{ __('auth.edit') }}
                            </a>
                            <div x-data="{ showDelete: false }">
                                <button @click="showDelete = true" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 dark:border-red-800 bg-white/80 dark:bg-slate-800/80 px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 transition-all duration-300 hover:bg-red-50 dark:hover:bg-red-950/30">
                                    <i class="bi bi-trash"></i>{{ __('auth.cd_delete_review') }}
                                </button>
                                <div x-show="showDelete" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                                    <div @click="showDelete = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                                    <div class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 max-w-md w-full animate-fade-in-up">
                                        <div class="text-center">
                                            <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                                                <i class="bi bi-trash text-2xl text-red-500"></i>
                                            </div>
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('auth.cd_delete_review') }}</h3>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ __('auth.cd_cancel_confirm') }}</p>
                                            <div class="flex items-center justify-center gap-3">
                                                <button @click="showDelete = false" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 transition-all duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                                                    {{ __('auth.select') }}
                                                </button>
                                                <form action="{{ route('customer.reviews.destroy', $review) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/25 transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-xl">
                                                        <i class="bi bi-trash"></i>{{ __('auth.cd_delete_review') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Comment --}}
                    @if($review->comment)
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-4">{{ $review->comment }}</p>
                    @endif

                    {{-- Hotel Reply --}}
                    @if($review->has_reply)
                        <div class="bg-amber-50/80 dark:bg-amber-950/20 backdrop-blur-sm rounded-xl border border-amber-200/60 dark:border-amber-800/40 p-4 mt-2">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-reply-all text-amber-600 dark:text-amber-400"></i>
                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">{{ __('auth.hotel') }} {{ __('auth.edit') }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $review->reply }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="mt-10">
                {{ $reviews->links() }}
            </div>
        @endif
    @else
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-12 text-center animate-fade-in-up" data-animate>
            <i class="bi bi-star text-6xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
            <p class="text-slate-500 dark:text-slate-400 text-lg mb-2">{{ __('auth.cd_no_reviews') }}</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mb-6">{{ __('auth.cd_no_reviews_text') }}</p>
            <a href="{{ route('frontend.search') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                <i class="bi bi-building"></i>{{ __('auth.booking_back_to_hotels') }}
            </a>
        </div>
    @endif

</x-frontend.dashboard-layout>
