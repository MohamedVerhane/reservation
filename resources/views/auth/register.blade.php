<x-layouts.auth :title="__('auth.register_title')">
    <div class="animate-fade-in-up">
        <x-auth-card :heading="__('auth.create_account')" :description="__('auth.register_description')">

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="form-label">{{ __('auth.name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           class="input w-full" placeholder="{{ __('auth.name_placeholder') }}">
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <label for="email" class="form-label">{{ __('auth.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                           class="input w-full" placeholder="you@example.com">
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div>
                    <label for="password" class="form-label">{{ __('auth.password') }}</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="input w-full pe-10" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password', this)" class="password-toggle">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="input w-full pe-10" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="password-toggle">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" />
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-person-plus"></i> {{ __('auth.register') }}
                </button>
            </form>
        </x-auth-card>

        <p class="mt-6 text-center text-sm text-[var(--text-secondary)]">
            {{ __('auth.already_account') }}
            <a href="{{ route('login') }}" class="font-semibold text-[var(--gold)] hover:underline">{{ __('auth.login') }}</a>
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
