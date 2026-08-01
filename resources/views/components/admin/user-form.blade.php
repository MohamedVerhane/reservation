@props(['user' => null, 'submitLabel' => null])

<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-6">
        <h3 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
            <i class="bi bi-person mr-2 text-indigo-500"></i> {{ __('admin.user_form.user_information') }}
        </h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.name') }} <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name ?? '') }}"
                    required
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                    placeholder="{{ __('admin.user_form.name_placeholder') }}"
                />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.email') }} <span class="text-red-500">*</span></label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email ?? '') }}"
                    required
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                    placeholder="{{ __('admin.user_form.email_placeholder') }}"
                />
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.phone') }}</label>
                <input
                    type="text"
                    name="phone"
                    id="phone"
                    value="{{ old('phone', $user->phone ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                    placeholder="{{ __('admin.user_form.phone_placeholder') }}"
                />
                @error('phone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.role') }} <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 p-3 transition-all hover:border-indigo-500 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20">
                        <input
                            type="radio"
                            name="role"
                            value="admin"
                            {{ old('role', $user->role ?? '') === 'admin' ? 'checked' : '' }}
                            class="sr-only"
                            required
                        />
                        <i class="bi bi-shield-lock text-xl text-slate-400 has-[:checked]:text-indigo-600 dark:has-[:checked]:text-indigo-400"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.role_admin') }}</span>
                    </label>
                    <label class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 p-3 transition-all hover:border-indigo-500 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20">
                        <input
                            type="radio"
                            name="role"
                            value="owner"
                            {{ old('role', $user->role ?? '') === 'owner' ? 'checked' : '' }}
                            class="sr-only"
                        />
                        <i class="bi bi-building text-xl text-slate-400 has-[:checked]:text-indigo-600 dark:has-[:checked]:text-indigo-400"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.role_owner') }}</span>
                    </label>
                    <label class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 p-3 transition-all hover:border-indigo-500 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20">
                        <input
                            type="radio"
                            name="role"
                            value="guest"
                            {{ old('role', $user->role ?? '') === 'guest' ? 'checked' : '' }}
                            class="sr-only"
                        />
                        <i class="bi bi-person text-xl text-slate-400 has-[:checked]:text-indigo-600 dark:has-[:checked]:text-indigo-400"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ __('admin.user_form.role_guest') }}</span>
                    </label>
                </div>
                @error('role')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 p-6">
        <h3 class="mb-4 text-lg font-bold text-slate-900 dark:text-white">
            <i class="bi bi-lock mr-2 text-indigo-500"></i> {{ __('admin.user_form.password') }}
            @if ($user)
                <span class="ml-2 text-xs font-normal text-slate-400">{{ __('admin.user_form.password_keep') }}</span>
            @endif
        </h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Password {!! $user ? '' : '<span class="text-red-500">*</span>' !!}
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    {{ $user ? '' : 'required' }}
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                    placeholder="{{ __('admin.user_form.password_placeholder') }}"
                />
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                    {{ __('admin.user_form.confirm_password') }} {!! $user ? '' : '<span class="text-red-500">*</span>' !!}
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                    placeholder="{{ __('admin.user_form.confirm_password_placeholder') }}"
                />
                @error('password_confirmation')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
            {{ __('admin.form.cancel') }}
        </a>
        <button type="submit" class="btn-gradient rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25">
            <i class="bi bi-check-lg mr-1"></i> {{ $submitLabel ?? __('admin.form.save') }}
        </button>
    </div>
</div>
