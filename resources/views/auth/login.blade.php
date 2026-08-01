<x-layouts.auth :title="__('auth.login_title')">
    <div class="animate-fade-in-up">
        <x-auth-card :heading="__('auth.welcome_back')" :description="__('auth.login_description')">

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="form-label">{{ __('auth.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                           class="input w-full" placeholder="you@example.com">
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div>
                    <label for="password" class="form-label">{{ __('auth.password') }}</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="input w-full pe-10" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password', this)" class="password-toggle">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-[var(--text-secondary)] cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-[var(--border)] text-[var(--gold)] focus:ring-[var(--gold)]/30"
                               style="accent-color: var(--gold)">
                        {{ __('auth.remember_me') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[var(--gold)] hover:underline">
                            {{ __('auth.forgot_password') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-box-arrow-in-right"></i> {{ __('auth.login') }}
                </button>
            </form>
        </x-auth-card>

        <p class="mt-6 text-center text-sm text-[var(--text-secondary)]">
            {{ __('auth.no_account') }}
            <a href="{{ route('register') }}" class="font-semibold text-[var(--gold)] hover:underline">{{ __('auth.register') }}</a>
        </p>
    </div>
</x-layouts.auth>

<script>
    function togglePassword(id, btn) {
        var input = document.getElementById(id);
        if (input.type === 'password') { input.type = 'text'; btn.innerHTML = '<i class="bi bi-eye-slash"></i>'; }
        else { input.type = 'password'; btn.innerHTML = '<i class="bi bi-eye"></i>'; }
    }
</script>
