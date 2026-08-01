<x-layouts.admin title="{{ __('admin.reviews.show_title', ['id' => $review->id]) }}" active="reviews">

    {{-- ═══ Breadcrumb ═══ --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.reviews.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.reviews') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">#{{ $review->id }}</span>
        </div>
    </div>

    {{-- ═══ Flash Messages ═══ --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ═══ Left Column (2/3) ═══ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Review Card --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-6">
                <div class="flex items-start gap-4">
                    @if ($review->user)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=6366f1&color=fff&size=48&bold=true" alt="{{ $review->user->name }}"
                            class="h-12 w-12 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shrink-0" />
                    @else
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-lg font-bold text-white">
                            ?
                        </span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ $review->user->name ?? __('admin.reviews.deleted_user') }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $review->user->email ?? '' }}</p>
                            </div>
                            <span class="text-xs text-slate-400 dark:text-slate-500">{{ $review->created_at->translatedFormat(__('auth.date_format') . ' \a\t g:i A') }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill text-sm {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $review->rating }}.0 — {{ match($review->rating) {
                                5 => __('admin.rating.excellent'),
                                4 => __('admin.rating.very_good'),
                                3 => __('admin.rating.average'),
                                2 => __('admin.rating.below_average'),
                                1 => __('admin.rating.poor'),
                                default => ''
                            } }}</span>
                        </div>
                        @if ($review->comment)
                            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Reply Section --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                    <i class="bi bi-reply-all text-base me-1"></i> {{ __('admin.reviews.reply_section') }}
                </h3>

                @if ($review->has_reply)
                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('admin.reviews.admin_reply') }}</span>
                            <span class="text-xs text-emerald-500 dark:text-emerald-500">· {{ $review->replied_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-emerald-800 dark:text-emerald-300 leading-relaxed whitespace-pre-wrap">{{ $review->reply }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.reviews.reply', $review) }}">
                        @csrf
                        @method('PATCH')
                        <div>
                            <textarea name="reply" rows="4" required placeholder="{{ __('admin.form.reply_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all resize-none">{{ old('reply') }}</textarea>
                            @error('reply')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end mt-3">
                            <button type="submit"
                                class="btn-gradient inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
                                <i class="bi bi-send text-sm"></i> {{ __('admin.reviews.send_reply') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- ═══ Right Sidebar (1/3) ═══ --}}
        <div class="space-y-6">

            {{-- Guest Info --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                    <i class="bi bi-person text-base me-1"></i> {{ __('admin.reviews.guest_info') }}
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.name') }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->user->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.email') }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 text-xs">{{ $review->user->email ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.phone') }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->user->phone ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Hotel Info --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                    <i class="bi bi-building text-base me-1"></i> {{ __('admin.reviews.hotel_info') }}
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.name') }}</span>
                        <a href="{{ route('admin.hotels.show', $review->hotel) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ $review->hotel->name ?? '—' }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reviews.address') }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 text-xs text-end max-w-[200px]">{{ $review->hotel->full_address ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.rating') }}</span>
                        <div class="flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill text-[10px] {{ $i <= ($review->hotel->star_rating ?? 0) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- Linked Reservation --}}
            @if ($review->reservation)
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-6">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                        <i class="bi bi-calendar-check text-base me-1"></i> {{ __('admin.reviews.reservation') }}
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.id') }}</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono">#{{ $review->reservation->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.check_in') }}</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->reservation->check_in->translatedFormat(__('auth.date_format')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.check_out') }}</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->reservation->check_out->translatedFormat(__('auth.date_format')) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.status') }}</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ match($review->reservation->status) {
                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
                                    'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
                                    'checked_in' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
                                    'checked_out' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400',
                                } }}">
                                {{ $review->reservation->status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Review Details --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">
                    <i class="bi bi-info-circle text-base me-1"></i> {{ __('admin.reviews.review_details') }}
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.id') }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono">#{{ $review->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.th.created_at') }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reviews.reply_status') }}</span>
                        @if ($review->has_reply)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-950 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ __('admin.reviews.replied') }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-950 px-2.5 py-0.5 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                {{ __('admin.reviews.pending') }}
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400">{{ __('admin.reviews.approval') }}</span>
                        @if ($review->is_approved)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-950 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ __('admin.reviews.approved') }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-950 px-2.5 py-0.5 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                {{ __('admin.reviews.pending') }}
                            </span>
                        @endif
                    </div>
                    <div class="pt-2">
                        @if (! $review->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 transition-all">
                                    <i class="bi bi-check-circle text-sm"></i> {{ __('admin.reviews.approve_review') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-amber-500/25 transition-all">
                                    <i class="bi bi-x-circle text-sm"></i> {{ __('admin.reviews.reject_review') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>
