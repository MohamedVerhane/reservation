<x-frontend.dashboard-layout :title="__('auth.cd_dashboard_title')">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 rounded-2xl p-6 sm:p-8 mb-8 shadow-xl shadow-amber-500/20 animate-fade-in-up" data-animate>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-1">
                    {{ __('auth.cd_welcome_back', ['name' => auth()->user()->name]) }}
                </h1>
                <p class="text-amber-100 text-sm sm:text-base">{{ __('auth.cd_dashboard_title') }}</p>
            </div>
            <a href="{{ route('frontend.search') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 px-5 py-2.5 text-sm font-bold text-white transition-all duration-300 hover:bg-white/30 hover:shadow-lg shrink-0">
                <i class="bi bi-building"></i>{{ __('auth.booking_book_now') }}
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Active Bookings --}}
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-6 animate-fade-in-up" data-animate style="animation-delay: 100ms">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar-check text-2xl text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $activeCount }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('auth.cd_active_bookings') }}</p>
                </div>
            </div>
        </div>

        {{-- Upcoming Trips --}}
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-6 animate-fade-in-up" data-animate style="animation-delay: 200ms">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-airplane text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $upcomingCount }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('auth.cd_upcoming_trips') }}</p>
                </div>
            </div>
        </div>

        {{-- Reviews Written --}}
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-6 animate-fade-in-up" data-animate style="animation-delay: 300ms">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-star text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $reviewsCount }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('auth.cd_total_reviews') }}</p>
                </div>
            </div>
        </div>

        {{-- Saved Hotels --}}
        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-6 animate-fade-in-up" data-animate style="animation-delay: 400ms">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-heart text-2xl text-rose-600 dark:text-rose-400"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $favoritesCount }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('auth.cd_saved_hotels') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Two-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Recent Reservations --}}
        <div class="lg:col-span-2 animate-fade-in-up" data-animate style="animation-delay: 500ms">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('auth.cd_reservations') }}</h2>
                <a href="{{ route('customer.reservations') }}" class="text-sm font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                    {{ __('auth.home_view_all') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($upcomingReservations->count())
                <div class="space-y-4">
                    @foreach($upcomingReservations as $reservation)
                        @php
                            $statusColors = match($reservation->status) {
                                'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400'],
                                'confirmed' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400'],
                                'checked_in' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                                default => ['bg' => 'bg-slate-100 dark:bg-slate-700/30', 'text' => 'text-slate-700 dark:text-slate-400'],
                            };
                        @endphp
                        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-5 transition-all duration-300 hover:shadow-2xl">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shrink-0">
                                        <i class="bi bi-building text-xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white">{{ $reservation->hotel->name }}</h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $reservation->room->roomType->name ?? '' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 sm:gap-4">
                                    <div class="text-sm text-slate-600 dark:text-slate-300">
                                        <span class="font-semibold">{{ \Carbon\Carbon::parse($reservation->check_in)->translatedFormat(__('auth.date_format_short')) }}</span>
                                        <i class="bi bi-arrow-right text-amber-500 mx-1"></i>
                                        <span class="font-semibold">{{ \Carbon\Carbon::parse($reservation->check_out)->translatedFormat(__('auth.date_format_short')) }}</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $statusColors['bg'] }} px-3 py-1 text-xs font-semibold {{ $statusColors['text'] }}">
                                        <i class="bi bi-circle-fill text-[0.4rem]"></i>
                                        {{ $reservation->status_label }}
                                    </span>
                                    <span class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                        ${{ number_format($reservation->total_price, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-8 text-center">
                    <i class="bi bi-calendar-x text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                    <p class="text-slate-500 dark:text-slate-400 mb-3">{{ __('auth.cd_no_reservations') }}</p>
                    <a href="{{ route('frontend.search') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl">
                        <i class="bi bi-building"></i>{{ __('auth.cd_no_reservations_text') }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Recent Reviews --}}
        <div class="animate-fade-in-up" data-animate style="animation-delay: 600ms">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('auth.cd_reviews') }}</h2>
                <a href="{{ route('customer.reviews') }}" class="text-sm font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                    {{ __('auth.home_view_all') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @if($recentReviews->count())
                <div class="space-y-4">
                    @foreach($recentReviews as $review)
                        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-5 transition-all duration-300 hover:shadow-2xl">
                            <div class="flex items-center justify-between mb-2">
                                <a href="{{ route('frontend.hotel.show', $review->hotel->slug) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors text-sm">
                                    {{ $review->hotel->name }}
                                </a>
                                <x-frontend.star-rating :rating="$review->rating" size="sm" />
                            </div>
                            @if($review->comment)
                                <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2 mb-2">{{ $review->comment }}</p>
                            @endif
                            <p class="text-xs text-slate-400 dark:text-slate-500">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl p-8 text-center">
                    <i class="bi bi-star text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                    <p class="text-slate-500 dark:text-slate-400">{{ __('auth.cd_no_reviews') }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('auth.cd_no_reviews_text') }}</p>
                </div>
            @endif
        </div>
    </div>

</x-frontend.dashboard-layout>
