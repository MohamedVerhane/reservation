<x-layouts.admin title="{{ $user->name }}" active="users">
    <div class="space-y-6">
        <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('admin.nav.users') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-900 dark:text-white">{{ $user->name }}</span>
        </nav>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 p-4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill text-red-600 dark:text-red-400"></i>
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 text-3xl font-bold text-white">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                        <div class="mt-1 flex items-center gap-2">
                            @switch($user->role)
                                @case('admin')
                                    <span class="inline-flex items-center rounded-lg bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <i class="bi bi-shield-lock mr-1"></i> {{ __('admin.role.admin') }}
                                    </span>
                                    @break
                                @case('owner')
                                    <span class="inline-flex items-center rounded-lg bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <i class="bi bi-building mr-1"></i> {{ __('admin.role.owner') }}
                                    </span>
                                    @break
                                @case('guest')
                                    <span class="inline-flex items-center rounded-lg bg-white/20 px-2.5 py-1 text-xs font-semibold text-white">
                                        <i class="bi bi-person mr-1"></i> {{ __('admin.role.guest') }}
                                    </span>
                                    @break
                            @endswitch
                            @if ($user->email_verified_at)
                                <span class="inline-flex items-center rounded-lg bg-emerald-400/20 px-2.5 py-1 text-xs font-semibold text-emerald-100">
                                    <i class="bi bi-patch-check mr-1"></i> {{ __('admin.status.verified') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-yellow-400/20 px-2.5 py-1 text-xs font-semibold text-yellow-100">
                                    <i class="bi bi-clock mr-1"></i> {{ __('admin.status.unverified') }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-white/80"><i class="bi bi-envelope mr-1"></i> {{ $user->email }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 rounded-xl bg-white/20 px-4 py-2 text-sm font-medium text-white hover:bg-white/30">
                    <i class="bi bi-pencil"></i> {{ __('admin.action.edit') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                        <i class="bi bi-building text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.dashboard.stats_hotels') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->hotels_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/30">
                        <i class="bi bi-calendar-check text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.dashboard.stats_reservations') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->reservations_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                        <i class="bi bi-star text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.users.reviews') }}</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $user->reviews_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-orange-50/60 dark:bg-slate-900 p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                        <i class="bi bi-calendar-plus text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('admin.users.joined') }}</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->created_at->translatedFormat(__('auth.date_format')) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-amber-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-person-lines-fill mr-2 text-indigo-500"></i> {{ __('admin.users.contact_info') }}
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.name') }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.email') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.phone') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.th.role') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ ucfirst($user->role) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.email_verified') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $user->email_verified_at ? $user->email_verified_at->translatedFormat(__('auth.date_format')) : __('admin.status.unverified') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-pink-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-graph-up mr-2 text-purple-500"></i> {{ __('admin.users.activity_summary') }}
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.total_reservations') }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->reservations_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.total_hotels') }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->hotels_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.total_reviews') }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->reviews_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.member_since') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.last_updated') }}</span>
                        <span class="text-sm text-slate-900 dark:text-white">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($user->recentReservations && $user->recentReservations->count() > 0)
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-teal-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-calendar-check mr-2 text-indigo-500"></i> {{ __('admin.users.recent_reservations') }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.hotel') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.check_in') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.check_out') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.status') }}</th>
                                <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($user->recentReservations as $reservation)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-medium text-slate-900 dark:text-white">{{ $reservation->hotel->name ?? __('admin.common.na') }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $reservation->check_in ? \Carbon\Carbon::parse($reservation->check_in)->translatedFormat(__('auth.date_format')) : '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $reservation->check_out ? \Carbon\Carbon::parse($reservation->check_out)->translatedFormat(__('auth.date_format')) : '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @switch($reservation->status)
                                            @case('pending')
                                                <span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">{{ __('admin.status.pending') }}</span>
                                                @break
                                            @case('confirmed')
                                                <span class="inline-flex items-center rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ __('admin.status.confirmed') }}</span>
                                                @break
                                            @case('checked_in')
                                                <span class="inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">{{ __('admin.status.checked_in') }}</span>
                                                @break
                                            @case('checked_out')
                                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-300">{{ __('admin.status.checked_out') }}</span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ __('admin.status.cancelled') }}</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">${{ number_format($reservation->total_price ?? 0, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($user->recentReviews && $user->recentReviews->count() > 0)
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
                    <i class="bi bi-star mr-2 text-amber-500"></i> {{ __('admin.users.recent_reviews') }}
                </h2>
                <div class="space-y-4">
                    @foreach ($user->recentReviews as $review)
                        <div class="rounded-xl border border-slate-100 dark:border-slate-800 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $review->hotel->name ?? __('admin.common.na') }}</p>
                                    <div class="mt-1 flex items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="bi bi-star-fill text-xs text-amber-400"></i>
                                            @else
                                                <i class="bi bi-star text-xs text-slate-300 dark:text-slate-600"></i>
                                            @endif
                                        @endfor
                                        <span class="ml-1 text-xs text-slate-500 dark:text-slate-400">({{ $review->rating }}/5)</span>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
