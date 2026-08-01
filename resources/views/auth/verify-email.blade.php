<x-layouts.auth :title="__('auth.verify_email_title')">
    <div class="animate-fade-in-up">
        <x-auth-card :heading="__('auth.verify_email')" :description="__('auth.verify_email_description')">

            @if (session('message') === 'verification-link-sent')
                <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ __('auth.verification_sent') }}
                </div>
            @endif

            <p class="text-sm text-[var(--text-secondary)] mb-6">
                {{ __('auth.verify_email_text') }}
            </p>

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-envelope"></i> {{ __('auth.resend_verification') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn-ghost w-full text-sm text-[var(--text-muted)]">
                    <i class="bi bi-box-arrow-right"></i> {{ __('auth.sign_out') }}
                </button>
            </form>
        </x-auth-card>
    </div>
</x-layouts.auth>
