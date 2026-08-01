<footer class="bg-[var(--surface-alt)] border-t border-[var(--border)] relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-5 group">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--gold)] text-white text-sm font-bold">
                        <i class="bi bi-gem"></i>
                    </span>
                    <span class="text-lg font-extrabold tracking-tight text-[var(--text-primary)]">{{ __('auth.app_name') }}</span>
                </a>
                <p class="text-sm leading-relaxed mb-6 text-[var(--text-muted)]">
                    {{ __('auth.footer_about') }}
                </p>
                <div class="flex items-center gap-3">
                    @foreach(['facebook', 'twitter-x', 'instagram', 'linkedin'] as $social)
                        <a href="#"
                            class="flex items-center justify-center w-9 h-9 rounded-full border border-[var(--border)] text-[var(--text-muted)] transition-all duration-300 hover:border-[var(--gold)] hover:text-[var(--gold)] hover:bg-[var(--gold)]/5">
                            <i class="bi bi-{{ $social }} text-sm"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-[var(--text-primary)] font-semibold text-sm uppercase tracking-wider mb-5">{{ __('auth.quick_links') }}</h3>
                <ul class="space-y-3">
                    @foreach([
                        ['route' => 'home', 'label' => __('auth.home')],
                        ['route' => 'frontend.hotels', 'label' => __('auth.hotels')],
                        ['route' => 'frontend.gallery', 'label' => __('auth.gallery')],
                        ['route' => 'frontend.about', 'label' => __('auth.about')],
                        ['route' => 'frontend.contact', 'label' => __('auth.contact')],
                    ] as $link)
                        <li>
                            <a href="{{ route($link['route']) }}"
                                class="text-sm inline-flex items-center gap-2 transition-colors duration-300 hover:text-[var(--gold)] text-[var(--text-muted)]">
                                <i class="bi bi-chevron-right text-[10px] text-[var(--text-muted)] transition-transform group-hover:translate-x-0.5"></i>{{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-[var(--text-primary)] font-semibold text-sm uppercase tracking-wider mb-5">{{ __('auth.contact_us') }}</h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i class="bi bi-geo-alt text-[var(--gold)] text-base mt-0.5 shrink-0"></i>
                        <span class="text-sm text-[var(--text-muted)]">{{ __('auth.address') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="bi bi-telephone text-[var(--gold)] text-base shrink-0"></i>
                        <a href="tel:{{ __('auth.phone') }}" class="text-sm text-[var(--text-muted)] transition-colors duration-300 hover:text-[var(--gold)]">
                            {{ __('auth.phone') }}
                        </a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="bi bi-envelope text-[var(--gold)] text-base shrink-0"></i>
                        <a href="mailto:{{ __('auth.email') }}" class="text-sm text-[var(--text-muted)] transition-colors duration-300 hover:text-[var(--gold)]">
                            {{ __('auth.email') }}
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="bi bi-clock text-[var(--gold)] text-base mt-0.5 shrink-0"></i>
                        <span class="text-sm text-[var(--text-muted)]">{{ __('auth.hours') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h3 class="text-[var(--text-primary)] font-semibold text-sm uppercase tracking-wider mb-5">{{ __('auth.newsletter') }}</h3>
                <p class="text-sm leading-relaxed mb-4 text-[var(--text-muted)]">
                    {{ __('auth.newsletter_text') }}
                </p>
                <form class="space-y-3">
                    @csrf
                    <input type="email" name="email" required
                        placeholder="{{ __('auth.email_placeholder') }}"
                        class="input">
                    <button type="submit"
                        class="btn-primary w-full">
                        {{ __('auth.subscribe') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-[var(--border)]">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-[var(--text-muted)]">
                &copy; {{ date('Y') }} {{ __('auth.app_name') }}. {{ __('auth.all_rights_reserved') }}
            </p>
            <div class="flex items-center gap-5">
                <a href="#" class="text-xs text-[var(--text-muted)] transition-colors duration-300 hover:text-[var(--gold)]">
                    {{ __('auth.privacy_policy') }}
                </a>
                <a href="#" class="text-xs text-[var(--text-muted)] transition-colors duration-300 hover:text-[var(--gold)]">
                    {{ __('auth.terms_of_service') }}
                </a>
            </div>
        </div>
    </div>
</footer>
