<x-layouts.auth :title="__('auth.confirm_password_title')">
    <div class="animate-fade-in-up">
        <x-auth-card :heading="__('auth.confirm_password')" :description="__('auth.confirm_password_description')">

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
                @csrf

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

                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-shield-check"></i> {{ __('auth.confirm') }}
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
