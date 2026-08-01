<x-layouts.frontend :title="__('meta.notifications')">
    <x-frontend.page-hero :title="__('notifications.title')" :subtitle="__('notifications.subtitle')" />

    <section class="max-w-4xl mx-auto px-6 py-12">
        @if($notifications->count() > 0)
            <div class="flex justify-end mb-6">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" data-ajax-action data-success="{{ __('auth.notif_all_read') }}">
                    @csrf
                    <button type="submit" class="btn-ghost text-sm">
                        <i class="bi bi-check-all"></i> {{ __('notifications.mark_all_read') }}
                    </button>
                </form>
            </div>
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="card p-5 flex items-start gap-4 {{ is_null($notification->read_at) ? 'border-s-[var(--gold)]' : '' }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ is_null($notification->read_at) ? 'bg-[var(--gold)]/10' : 'bg-[var(--surface-alt)]' }}">
                            <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} {{ is_null($notification->read_at) ? 'text-[var(--gold)]' : 'text-[var(--text-muted)]' }}"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-[var(--text-primary)] mb-1">
                                {{ $notification->data['title'] ?? __('notifications.new') }}
                            </h3>
                            <p class="text-sm text-[var(--text-secondary)] mb-2">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-[var(--text-muted)]">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" data-ajax-action data-success="{{ __('auth.notif_mark_read') }}" class="shrink-0">
                                @csrf
                                <button type="submit" class="btn-ghost btn-sm text-xs">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="bi bi-bell-slash text-[var(--text-muted)] text-6xl mb-4"></i>
                <p class="text-[var(--text-secondary)] text-lg">{{ __('notifications.empty') }}</p>
            </div>
        @endif
    </section>
</x-layouts.frontend>
