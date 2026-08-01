<x-layouts.admin title="{{ __('auth.notif_title') }}" active="notifications">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ __('auth.notif_title') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $unreadCount }} {{ __('auth.notif_unread') }}</p>
        </div>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all">
                    <i class="bi bi-check-all text-sm"></i> {{ __('auth.notif_mark_all_read') }}
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-sky-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            @php
                $type = $notification->data['type'] ?? 'unknown';
                $isUnread = is_null($notification->read_at);
            @endphp
            <div class="flex items-start gap-4 px-6 py-4 border-b border-slate-100 dark:border-slate-800 transition-colors {{ $isUnread ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                    {{ match($type) {
                        'booking_confirmed' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                        'booking_cancelled' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                        'payment_successful' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'payment_failed' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                        'new_booking' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                        'new_review' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
                        'review_approved' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                        'review_reply' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
                    } }}">
                    <i class="bi {{ match($type) {
                        'booking_confirmed' => 'bi-check-circle',
                        'booking_cancelled' => 'bi-x-circle',
                        'payment_successful' => 'bi-credit-card',
                        'payment_failed' => 'bi-exclamation-triangle',
                        'new_booking' => 'bi-calendar-plus',
                        'new_review' => 'bi-star',
                        'review_approved' => 'bi-hand-thumbs-up',
                        'review_reply' => 'bi-reply',
                        default => 'bi-bell',
                    } }}"></i>
                </span>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $notification->data['message'] ?? __('auth.notif_new') }}</p>
                    @if(isset($notification->data['hotel_name']))
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $notification->data['hotel_name'] }}</p>
                    @endif
                    @if(isset($notification->data['total_price']))
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('auth.notif_total') }}: ${{ number_format($notification->data['total_price'], 2) }}</p>
                    @endif
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>

                <div class="flex items-center gap-1 shrink-0">
                    @unless($notification->read_at)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors" title="{{ __('auth.notif_mark_read') }}">
                                <i class="bi bi-check2 text-sm"></i>
                            </button>
                        </form>
                    @endunless
                    <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" x-data x-on:submit="return confirm('{{ __('auth.notif_delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors" title="{{ __('auth.notif_delete') }}">
                            <i class="bi bi-trash3 text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-20 text-center">
                <i class="bi bi-bell-slash text-5xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
                <p class="text-lg font-semibold text-slate-500 dark:text-slate-400">{{ __('auth.notif_empty') }}</p>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">{{ __('auth.notif_empty_admin_text') }}</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

</x-layouts.admin>
