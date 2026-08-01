<x-layouts.frontend :title="__('meta.contact')">
    <x-frontend.page-hero :title="__('contact.title')" :subtitle="__('contact.subtitle')" />

    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            @foreach([
                ['icon' => 'bi-geo-alt', 'key' => 'address'],
                ['icon' => 'bi-telephone', 'key' => 'phone'],
                ['icon' => 'bi-envelope', 'key' => 'email'],
            ] as $index => $contact)
                <div class="card p-8 text-center reveal d{{ $index + 1 }}">
                    <div class="w-14 h-14 rounded-full bg-[var(--gold)]/10 flex items-center justify-center mx-auto mb-5">
                        <i class="bi {{ $contact['icon'] }} text-xl text-[var(--gold)]"></i>
                    </div>
                    <h3 class="text-lg font-bold text-[var(--text-primary)] mb-2">
                        {{ __('contact.' . $contact['key'] . '_title') }}
                    </h3>
                    <p class="text-[var(--text-secondary)] leading-relaxed">
                        {!! __('contact.' . $contact['key'] . '_detail') !!}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="reveal">
                <h2 class="text-3xl font-bold text-[var(--text-primary)] mb-2">
                    {{ __('contact.form_title') }}
                </h2>
                <p class="text-[var(--text-secondary)] mb-8">
                    {{ __('contact.form_subtitle') }}
                </p>

                @if (session('success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-check-circle-fill text-emerald-500 text-xl"></i>
                            <p class="text-emerald-400">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl"></i>
                            <p class="text-red-400">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST" class="space-y-5" data-ajax-action data-success="{{ __('contact.success') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-label">
                            <label>{{ __('contact.name') }} *</label>
                            <input type="text" name="name" class="input w-full" value="{{ old('name') }}" required>
                            @error('name') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-label">
                            <label>{{ __('contact.email') }} *</label>
                            <input type="email" name="email" class="input w-full" value="{{ old('email') }}" required>
                            @error('email') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-label">
                        <label>{{ __('contact.subject') }} *</label>
                        <input type="text" name="subject" class="input w-full" value="{{ old('subject') }}" required>
                        @error('subject') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-label">
                        <label>{{ __('contact.message') }} *</label>
                        <textarea name="message" class="textarea w-full h-40 resize-none" required>{{ old('message') }}</textarea>
                        @error('message') <span class="text-[var(--danger)] text-sm">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-send"></i> {{ __('contact.send') }}
                    </button>
                </form>
            </div>

            <div class="reveal d2">
                <h2 class="text-3xl font-bold text-[var(--text-primary)] mb-8">
                    {{ __('contact.map_title') }}
                </h2>
                <div class="w-full h-96 rounded-3xl overflow-hidden bg-[var(--surface-alt)] border border-[var(--border)]">
                    <iframe
                        src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        class="w-full h-full border-0"
                        loading="lazy"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </div>
    </section>

    <x-frontend.cta-section />
</x-layouts.frontend>
