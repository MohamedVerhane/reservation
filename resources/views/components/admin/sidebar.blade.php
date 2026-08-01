@props(['active' => ''])

@php
    $notifCount = auth()->user()->unreadNotifications()->count();
    $navItems = [
        ['key' => 'dashboard', 'label' => __('admin.nav.dashboard'), 'icon' => 'bi-grid-1x2-fill', 'route' => 'admin.dashboard'],
        ['key' => 'hotels', 'label' => __('admin.nav.hotels'), 'icon' => 'bi-building', 'route' => 'admin.hotels.index'],
        ['key' => 'rooms', 'label' => __('admin.nav.room_types'), 'icon' => 'bi-tag', 'route' => 'admin.room-types.index'],
        ['key' => 'rooms-actual', 'label' => __('admin.nav.rooms'), 'icon' => 'bi-door-open', 'route' => 'admin.rooms.index'],
        ['key' => 'reservations', 'label' => __('admin.nav.reservations'), 'icon' => 'bi-calendar-check', 'route' => 'admin.reservations.index'],
        ['key' => 'payments', 'label' => __('admin.nav.payments'), 'icon' => 'bi-credit-card', 'route' => 'admin.payments.index'],
        ['key' => 'reviews', 'label' => __('admin.nav.reviews'), 'icon' => 'bi-star-half', 'route' => 'admin.reviews.index'],
        ['key' => 'amenities', 'label' => __('admin.nav.amenities'), 'icon' => 'bi-gear-wide-connected', 'route' => 'admin.amenities.index'],
        ['key' => 'users', 'label' => __('admin.nav.users'), 'icon' => 'bi-people', 'route' => 'admin.users.index'],
        ['key' => 'galleries', 'label' => __('admin.nav.galleries'), 'icon' => 'bi-images', 'route' => 'admin.galleries.index'],
        ['key' => 'notifications', 'label' => __('admin.nav.notifications'), 'icon' => 'bi-bell', 'route' => 'admin.notifications', 'badge' => $notifCount > 0 ? $notifCount : null],
    ];
@endphp

<aside data-sidebar
    class="fixed top-0 start-0 z-50 h-screen w-64 -translate-x-full lg:translate-x-0 transition-transform duration-300
           bg-[var(--surface-elevated)] border-e border-[var(--border)] flex flex-col shadow-xl lg:shadow-none">

    <div class="flex items-center gap-3 px-5 py-4 border-b border-[var(--border-light)]">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--gold)] text-white">
            <i class="bi bi-gem text-sm"></i>
        </span>
        <div>
            <p class="text-sm font-extrabold text-[var(--text-primary)]">{{ __('auth.app_name') }}</p>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-[var(--text-muted)]">{{ __('admin.nav.admin_panel') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        @foreach ($navItems as $item)
            @php $isActive = $active === $item['key']; @endphp
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                       {{ $isActive
                           ? 'bg-[var(--gold)]/10 text-[var(--gold)] font-semibold'
                           : 'text-[var(--text-secondary)] hover:bg-[var(--surface-alt)] hover:text-[var(--text-primary)]' }}">
                <i class="bi {{ $item['icon'] }} text-base {{ $isActive ? 'text-[var(--gold)]' : '' }}"></i>
                {{ $item['label'] }}
                @if(isset($item['badge']) && $item['badge'])
                    <span class="ms-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white px-1">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="px-3 py-3 border-t border-[var(--border-light)]">
        <a href="{{ url('/') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-[var(--text-muted)] hover:bg-[var(--surface-alt)] hover:text-[var(--gold)] transition-colors">
            <i class="bi bi-arrow-left-short text-lg"></i>
            {{ __('admin.nav.back_to_site') }}
        </a>
    </div>
</aside>
