<x-layouts.admin :title="__('admin.reviews.index_title')" active="reviews">

    {{-- ═══ Header ═══ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('admin.reviews.index_title') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('admin.reviews.index_subtitle') }}</p>
        </div>
    </div>

    {{-- ═══ Flash Messages ═══ --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ═══ Filters ═══ --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3" data-ajax-filter="reviews-table-wrap">
            {{-- Search --}}
            <div class="relative flex-1">
                <i class="bi bi-search absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.reviews.search_placeholder') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 ps-10 pe-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
            </div>

            {{-- Hotel filter --}}
            <select name="hotel_id"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.reviews.all_hotels') }}</option>
                @foreach ($hotels as $id => $name)
                    <option value="{{ $id }}" {{ request('hotel_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            {{-- Rating filter --}}
            <select name="rating"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.reviews.all_ratings') }}</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ trans_choice('admin.reviews.star_rating', $i, ['count' => $i]) }}</option>
                @endfor
            </select>

            {{-- Reply status filter --}}
            <select name="reply_status"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.reviews.all_status') }}</option>
                <option value="replied" {{ request('reply_status') === 'replied' ? 'selected' : '' }}>{{ __('admin.filter.replied') }}</option>
                <option value="pending" {{ request('reply_status') === 'pending' ? 'selected' : '' }}>{{ __('admin.reviews.pending') }}</option>
            </select>

            {{-- Approval status filter --}}
            <select name="approval_status"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option value="">{{ __('admin.reviews.all_approval') }}</option>
                <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>{{ __('admin.filter.approved') }}</option>
                <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>{{ __('admin.reviews.pending') }}</option>
            </select>

            {{-- Trashed toggle --}}
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="trashed" value="1" class="hidden peer"
                    {{ request('trashed') === '1' ? 'checked' : '' }} />
                <span class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-red-500 relative after:content-[''] after:absolute after:top-0.5 after:start-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></span>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('admin.reviews.trashed_label') }}</span>
            </label>

            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors">
                <i class="bi bi-funnel text-sm"></i> {{ __('admin.reviews.filter_btn') }}
            </button>
        </form>
    </div>

    {{-- ═══ Table ═══ --}}
    <div id="reviews-table-wrap">
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_id') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_guest') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_hotel') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_rating') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_comment') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_status') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_date') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('admin.reviews.th_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 font-mono text-xs">#{{ $review->id }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($review->user)
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=6366f1&color=fff&size=32&bold=true" alt="{{ __('admin.reviews.th_guest') }}"
                                            class="h-8 w-8 rounded-full object-cover border border-slate-200 dark:border-slate-700" />
                                    @else
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-xs font-bold text-white">
                                            ?
                                        </span>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->user->name ?? __('admin.reviews.deleted_user_label') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400">{{ $review->hotel->name ?? __('admin.common.na') }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill text-[10px] {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600 dark:text-slate-400 max-w-[200px] truncate">
                                {{ Str::limit($review->comment, 60) ?? __('admin.common.na') }}
                            </td>
                            <td class="px-6 py-3.5">
                                @if ($review->trashed())
                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-950 px-2.5 py-0.5 text-xs font-semibold text-red-600 dark:text-red-400">
                                        Deleted
                                    </span>
                                @elseif ($review->is_approved)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-950 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-950 px-2.5 py-0.5 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                        Pending Approval
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 text-xs">{{ $review->created_at->translatedFormat(__('auth.date_format')) }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($review->trashed())
                                        <form method="POST" action="{{ route('admin.reviews.restore', $review->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="{{ __('admin.reviews.restore_title') }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors">
                                                <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.reviews.show', $review) }}" title="{{ __("admin.action.view") }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                            <i class="bi bi-eye text-sm"></i>
                                        </a>
                                        @unless ($review->is_approved)
                                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="{{ __("admin.action.approve") }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors">
                                                    <i class="bi bi-check-circle text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="{{ __("admin.action.reject") }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors">
                                                    <i class="bi bi-x-circle text-sm"></i>
                                                </button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                            x-data x-on:submit="return confirm('{{ __("admin.confirm.delete") }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="{{ __("admin.action.delete") }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                                <i class="bi bi-trash3 text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <i class="bi bi-star-half text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ __("admin.empty.reviews") }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('admin.empty.create_review') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    {{-- ═══ Stats row ═══ --}}
    <div class="mt-6 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
        <p>{{ __('admin.pagination.showing', ['from' => $reviews->firstItem() ?? 0, 'to' => $reviews->lastItem() ?? 0, 'total' => $reviews->total()]) }}</p>
        <p>{{ __('admin.pagination.page', ['current' => $reviews->currentPage(), 'last' => $reviews->lastPage()]) }}</p>
    </div>
    </div>

</x-layouts.admin>
