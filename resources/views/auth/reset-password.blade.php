<x-layouts.auth :title="__('auth.reset_password_title')">
    <div class="animate-fade-in-up">
        <x-auth-card :heading="__('auth.reset_password')" :description="__('auth.reset_password_description')">

            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="form-label">{{ __('auth.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                           class="input w-full" readonly>
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div>
                    <label for="password" class="form-label">{{ __('auth.password') }}</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
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
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="input w-full pe-10" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="password-toggle">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" />
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-check-circle"></i> {{ __('auth.reset_password') }}
                </button>
            </form>
        </x-auth-card>
    </div>
</x-layouts.auth>

<script>
    function togglePassword(id, btn) {
        var input = document.getElementById(id);
        if (input.type === 'password') { input.type = 'text'; btn.innerHTML = '<i class="bi bi-eye-slash"></i>'; }
        else { input.type = 'password'; btn.innerHTML = '<i class="bi bi-eye"></i>'; }
    }
</script>
