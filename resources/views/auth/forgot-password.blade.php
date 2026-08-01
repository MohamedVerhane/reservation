<x-layouts.auth :title="__('auth.forgot_password_title')">
    <div class="animate-fade-in-up">
        <x-auth-card :heading="__('auth.forgot_password')" :description="__('auth.forgot_password_description')">

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="form-label">{{ __('auth.email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input w-full" placeholder="you@example.com">
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-send"></i> {{ __('auth.send_reset_link') }}
                </button>
            </form>
        </x-auth-card>

        <p class="mt-6 text-center text-sm text-[var(--text-secondary)]">
            <a href="{{ route('login') }}" class="font-semibold text-[var(--gold)] hover:underline">
                <i class="bi bi-arrow-left"></i> {{ __('auth.back_to_login') }}
            </a>
        </p>
    </div>
</x-layouts.auth>
