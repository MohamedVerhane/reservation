<footer class="relative overflow-hidden border-t border-border bg-muted/40">
    <div class="mx-auto w-full max-w-7xl px-6 py-16">
        <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="mb-5 inline-flex items-center gap-2.5 group">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 font-serif text-sm font-black leading-none text-white shadow-sm shadow-primary/25">MD</span>
                    <span class="text-lg font-extrabold tracking-tight text-foreground">{{ __('auth.app_name') }}</span>
                </a>
                <p class="mb-6 text-sm leading-relaxed text-muted-foreground">
                    {{ __('auth.footer_about') }}
                </p>
                <div class="flex items-center gap-3">
                    @foreach(['thumbs-up' => 'Facebook', 'at-sign' => 'X / Twitter', 'camera' => 'Instagram', 'briefcase' => 'LinkedIn'] as $icon => $label)
                        <a href="#" aria-label="{{ $label }}"
                            class="flex items-center justify-center w-9 h-9 rounded-full border border-border text-muted-foreground transition-all duration-300 hover:border-primary hover:text-primary hover:bg-primary/5">
                            <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="mb-5 text-sm font-semibold uppercase tracking-wider text-foreground">{{ __('auth.quick_links') }}</h3>
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
                                class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors duration-300 hover:text-primary">
                                <i data-lucide="chevron-right" class="h-3 w-3 rtl:rotate-180"></i>{{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="mb-5 text-sm font-semibold uppercase tracking-wider text-foreground">{{ __('auth.contact_us') }}</h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary"></i>
                        <span class="text-sm text-muted-foreground">{{ __('auth.address') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="phone" class="h-4 w-4 shrink-0 text-primary"></i>
                        <a href="tel:{{ __('auth.phone') }}" class="text-sm text-muted-foreground transition-colors duration-300 hover:text-primary">
                            {{ __('auth.phone') }}
                        </a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="mail" class="h-4 w-4 shrink-0 text-primary"></i>
                        <a href="mailto:{{ __('auth.email') }}" class="text-sm text-muted-foreground transition-colors duration-300 hover:text-primary">
                            {{ __('auth.email') }}
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="clock" class="mt-0.5 h-4 w-4 shrink-0 text-primary"></i>
                        <span class="text-sm text-muted-foreground">{{ __('auth.hours') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h3 class="mb-5 text-sm font-semibold uppercase tracking-wider text-foreground">{{ __('auth.newsletter') }}</h3>
                <p class="mb-4 text-sm leading-relaxed text-muted-foreground">
                    {{ __('auth.newsletter_text') }}
                </p>
                <form class="space-y-3">
                    @csrf
                    <x-ui.input type="email" name="email" required :placeholder="__('auth.email_placeholder')" />
                    <x-ui.button variant="gold" type="submit" class="w-full">
                        {{ __('auth.subscribe') }}
                    </x-ui.button>
                </form>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-border">
        <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-4 px-6 py-6 sm:flex-row">
            <p class="text-xs text-muted-foreground">
                &copy; {{ date('Y') }} {{ __('auth.app_name') }}. {{ __('auth.all_rights_reserved') }}
            </p>
            <div class="flex items-center gap-5">
                <a href="#" class="text-xs text-muted-foreground transition-colors duration-300 hover:text-primary">
                    {{ __('auth.privacy_policy') }}
                </a>
                <a href="#" class="text-xs text-muted-foreground transition-colors duration-300 hover:text-primary">
                    {{ __('auth.terms_of_service') }}
                </a>
            </div>
        </div>
    </div>
</footer>
