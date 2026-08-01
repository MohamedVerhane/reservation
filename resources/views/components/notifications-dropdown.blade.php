@props(['viewAllRoute' => 'admin.notifications', 'limit' => 5])

<div class="relative" x-data="{ open: false }" @click.away="open = false" data-notifications-dropdown>
    <button @click="open = !open" type="button"
        {{ $attributes->merge(['class' => 'relative cursor-pointer transition-all duration-200']) }}>
        <i class="bi bi-bell text-sm"></i>
        @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
        @if($unread > 0)
            <span class="absolute -top-1 -end-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $unread > 9 ? '9+' : $unread }}</span>
        @endif
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-80 rounded-xl border border-[var(--border)] bg-[var(--surface-elevated)] shadow-xl z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--border-light)]">
            <h3 class="text-sm font-bold text-[var(--text-primary)]">{{ __('auth.notif_title') }}</h3>
            @if($unread > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" data-ajax-action data-success="{{ __('auth.notif_all_read') }}">
                    @csrf
                    <button type="submit" class="text-xs text-[var(--gold)] hover:underline font-semibold cursor-pointer">{{ __('auth.notif_mark_all_read') }}</button>
                </form>
            @endif
        </div>
        <div class="max-h-72 overflow-y-auto">
            @php $notifs = auth()->user()->notifications()->latest()->take($limit)->get(); @endphp
            @forelse($notifs as $notif)
                <div class="px-4 py-3 border-b border-[var(--border-light)] hover:bg-[var(--surface-alt)] transition-colors {{ $notif->read_at ? '' : 'bg-[var(--gold)]/5' }}">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 w-7 h-7 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] flex items-center justify-center shrink-0">
                            <i class="bi {{ $notif->data['icon'] ?? 'bi-bell' }} text-xs"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-[var(--text-primary)] leading-snug">{{ $notif->data['message'] ?? __('auth.notif_new') }}</p>
                            <p class="text-[10px] text-[var(--text-muted)] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @unless($notif->read_at)
                            <form method="POST" action="{{ route('notifications.mark-read', $notif->id) }}" data-ajax-action data-success="{{ __('auth.notif_mark_read') }}">
                                @csrf
                                <button type="submit" class="text-[var(--text-muted)] hover:text-[var(--gold)] cursor-pointer"><i class="bi bi-check2 text-sm"></i></button>
                            </form>
                        @endunless
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <i class="bi bi-bell-slash text-2xl text-[var(--text-muted)] mb-2 block opacity-50"></i>
                    <p class="text-xs text-[var(--text-muted)]">{{ __('auth.notif_empty') }}</p>
                </div>
            @endforelse
        </div>
        <a href="{{ route($viewAllRoute) }}" class="block px-4 py-3 text-center text-xs font-semibold text-[var(--gold)] hover:bg-[var(--surface-alt)] border-t border-[var(--border-light)] rounded-b-xl transition-colors cursor-pointer">
            {{ __('auth.notif_view_all') }}
        </a>
    </div>
</div>