<x-frontend.dashboard-layout :title="__('auth.cd_profile_title')">

    @php
        $tabs = [
            'profile' => ['icon' => 'bi-person', 'label' => __('auth.cd_profile_tab')],
            'security' => ['icon' => 'bi-shield-lock', 'label' => __('auth.cd_security_tab')],
            'account' => ['icon' => 'bi-gear', 'label' => __('auth.cd_account_tab')],
        ];
    @endphp

    <div x-data="{ tab: 'profile' }" class="space-y-6">

        {{-- Cover & Profile Card --}}
        <div class="relative overflow-hidden rounded-2xl bg-[var(--surface-elevated)] border border-[var(--border-light)] shadow-sm">
            <div class="h-36 sm:h-48 bg-gradient-to-r from-[var(--gold)] via-[var(--gold-light)] to-[var(--gold-dark)] relative">
                <div class="absolute inset-0 bg-black/10"></div>
            </div>
            <div class="px-6 sm:px-8 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end gap-5 -mt-14 sm:-mt-16">
                    <div class="relative shrink-0">
                        <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-[var(--gold)] to-[var(--gold-dark)] flex items-center justify-center text-white text-4xl font-extrabold shadow-xl ring-4 ring-[var(--surface-elevated)]">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 pt-4 sm:pt-0 sm:pb-1">
                        <h1 class="text-2xl font-extrabold text-[var(--text-primary)] truncate">{{ $user->name }}</h1>
                        <p class="text-sm text-[var(--text-muted)]">{{ $user->email }}</p>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ __('auth.cd_member_since', ['date' => $user->created_at->translatedFormat(__('auth.date_format'))]) }}
                        </p>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-[var(--border-light)]">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-[var(--text-primary)]">{{ $reservationsCount }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ trans_choice('auth.cd_reservations_count', $reservationsCount, ['count' => $reservationsCount]) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-[var(--text-primary)]">{{ $reviewsCount }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ trans_choice('auth.cd_reviews_count', $reviewsCount, ['count' => $reviewsCount]) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-[var(--text-primary)]">{{ $user->role }}</p>
                        <p class="text-xs text-[var(--text-muted)] capitalize">{{ __('auth.cd_profile') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/80 dark:bg-emerald-950/50 px-5 py-4 text-sm text-emerald-700 dark:text-emerald-400 backdrop-blur-sm flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-emerald-500 text-lg"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50/80 dark:bg-red-950/50 px-5 py-4 backdrop-blur-sm">
                <div class="flex items-start gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">{{ __('admin.form.fix_errors') }}</p>
                        <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="flex gap-1 p-1 rounded-xl bg-[var(--surface-alt)] border border-[var(--border-light)] w-fit">
            @foreach($tabs as $key => $tab)
                <button @click="tab = '{{ $key }}'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200"
                    :class="tab === '{{ $key }}' ? 'bg-[var(--gold)] text-white shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-[var(--surface)]'">
                    <i class="bi {{ $tab['icon'] }}"></i>
                    <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Tab: Profile Details --}}
        <div x-show="tab === 'profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="rounded-2xl bg-[var(--surface-elevated)] border border-[var(--border-light)] shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">{{ __('auth.cd_profile_tab') }}</h3>
                <p class="text-sm text-[var(--text-muted)] mb-6">{{ __('auth.cd_settings') }}</p>

                <form action="{{ route('customer.profile.update') }}" method="POST" data-ajax-action data-no-refresh data-success="{{ __('admin.profile.updated') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">{{ __('auth.name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="input w-full" required />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">{{ __('auth.email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="input w-full" required />
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">{{ __('auth.hotel_phone') }}</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="input w-full" placeholder="{{ __('auth.hotel_phone') }}" />
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-3 pt-6 border-t border-[var(--border-light)]">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold">
                            <i class="bi bi-check-lg"></i>
                            {{ __('auth.cd_save_changes') }}
                        </button>
                        <a href="{{ route('customer.profile') }}" class="btn-ghost inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold">
                            {{ __('auth.cd_cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tab: Security --}}
        <div x-show="tab === 'security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="rounded-2xl bg-[var(--surface-elevated)] border border-[var(--border-light)] shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">{{ __('auth.cd_change_password') }}</h3>
                <p class="text-sm text-[var(--text-muted)] mb-6">{{ __('auth.cd_password_requirements') }}</p>

                <form action="{{ route('customer.profile.password') }}" method="POST" data-ajax-action data-no-refresh data-success="{{ __('auth.cd_password_updated') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-2xl">
                        <div class="sm:col-span-2">
                            <label for="current_password" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">{{ __('auth.cd_current_password') }}</label>
                            <input type="password" id="current_password" name="current_password" required
                                class="input w-full" autocomplete="current-password" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">{{ __('auth.cd_new_password') }}</label>
                            <input type="password" id="password" name="password" required
                                class="input w-full" autocomplete="new-password" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">{{ __('auth.cd_confirm_new_password') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="input w-full" autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-3 pt-6 border-t border-[var(--border-light)]">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold">
                            <i class="bi bi-shield-check"></i>
                            {{ __('auth.cd_change_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tab: Account --}}
        <div x-show="tab === 'account'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="rounded-2xl bg-[var(--surface-elevated)] border border-[var(--border-light)] shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">{{ __('auth.cd_account_tab') }}</h3>
                <p class="text-sm text-[var(--text-muted)] mb-6">{{ __('auth.cd_activity_text') }}</p>

                {{-- Activity stats --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="rounded-xl bg-[var(--surface-alt)] border border-[var(--border-light)] p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[var(--gold)]/10 flex items-center justify-center">
                                <i class="bi bi-calendar-check text-lg text-[var(--gold)]"></i>
                            </div>
                            <div>
                                <p class="text-lg font-extrabold text-[var(--text-primary)]">{{ $reservationsCount }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ trans_choice('auth.cd_reservations_count', $reservationsCount, ['count' => $reservationsCount]) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl bg-[var(--surface-alt)] border border-[var(--border-light)] p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[var(--gold)]/10 flex items-center justify-center">
                                <i class="bi bi-star text-lg text-[var(--gold)]"></i>
                            </div>
                            <div>
                                <p class="text-lg font-extrabold text-[var(--text-primary)]">{{ $reviewsCount }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ trans_choice('auth.cd_reviews_count', $reviewsCount, ['count' => $reviewsCount]) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl bg-[var(--surface-alt)] border border-[var(--border-light)] p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[var(--gold)]/10 flex items-center justify-center">
                                <i class="bi bi-person-badge text-lg text-[var(--gold)]"></i>
                            </div>
                            <div>
                                <p class="text-lg font-extrabold text-[var(--text-primary)] capitalize">{{ $user->role }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ __('auth.cd_profile') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50/50 dark:bg-red-950/20 p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                            <i class="bi bi-exclamation-triangle text-lg text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-red-700 dark:text-red-400">{{ __('auth.cd_delete_account') }}</h4>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 mb-4">{{ __('auth.cd_delete_account_warning') }}</p>
                            <form method="POST" action="" x-data x-on:submit.prevent="if(confirm('{{ __('auth.cd_delete_confirm') }}')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white px-5 py-2 text-xs font-bold transition-colors shadow-sm">
                                    <i class="bi bi-trash3"></i>
                                    {{ __('auth.cd_delete_account_btn') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-frontend.dashboard-layout>