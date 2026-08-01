<x-layouts.admin :title="__('admin.nav.users')" active="users">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('admin.nav.users') }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.index_subtitle') }}</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-gradient rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg shadow-indigo-500/25">
                <i class="bi bi-plus-lg mr-1"></i> {{ __('admin.users.add') }}
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 p-4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill text-red-600 dark:text-red-400"></i>
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 p-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6" data-ajax-filter="users-table-wrap">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.action.search') }}</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('admin.form.search_users_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 pl-10 pr-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="role" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.th.role') }}</label>
                        <select
                            name="role"
                            id="role"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                        >
                            <option value="">{{ __('admin.filter.all_roles') }}</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('admin.role.admin') }}</option>
                            <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>{{ __('admin.role.owner') }}</option>
                            <option value="guest" {{ request('role') === 'guest' ? 'selected' : '' }}>{{ __('admin.role.guest') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="verified" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.status.verified') }}</label>
                        <select
                            name="verified"
                            id="verified"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none"
                        >
                            <option value="">{{ __('admin.filter.all') }}</option>
                            <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>{{ __('admin.status.verified') }}</option>
                            <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>{{ __('admin.status.unverified') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('admin.users.trashed') }}</label>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 cursor-pointer has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500 dark:has-[:checked]:bg-indigo-900/20">
                                <input type="checkbox" name="trashed" value="1" {{ request('trashed') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ __('admin.users.show_trashed') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                        {{ __('admin.action.clear') }}
                    </a>
                </div>
            </form>

            <div id="users-table-wrap">
                <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.user') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.phone') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.role') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.status.verified') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.bookings') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.users.joined') }}</th>
                            <th class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ __('admin.th.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-slate-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">{{ $user->name }}</a>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $user->phone ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                <i class="bi bi-shield-lock mr-1"></i> {{ __('admin.role.admin') }}
                                            </span>
                                            @break
                                        @case('owner')
                                            <span class="inline-flex items-center rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                <i class="bi bi-building mr-1"></i> {{ __('admin.role.owner') }}
                                            </span>
                                            @break
                                        @case('guest')
                                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                <i class="bi bi-person mr-1"></i> {{ __('admin.role.guest') }}
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3">
                                    @if ($user->email_verified_at)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                            <i class="bi bi-patch-check-fill"></i> {{ __('admin.status.verified') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400">
                                            <i class="bi bi-clock"></i> {{ __('admin.status.unverified') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $user->reservations_count ?? 0 }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-slate-500 dark:text-slate-400">{{ $user->created_at->translatedFormat(__('auth.date_format')) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($user->trashed())
                                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" data-ajax-action>
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 dark:border-emerald-800 p-1.5 text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20" :title="__('admin.action.restore')">
                                                    <i class="bi bi-arrow-counterclockwise text-sm"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" onsubmit="return confirm('{{ __("admin.confirm.user_permanent_delete") }}')" data-ajax-action>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 dark:border-red-800 p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20" title="{{ __("admin.action.force_delete") }}">
                                                    <i class="bi bi-trash3 text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 p-1.5 text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800" title="{{ __("admin.action.view") }}">
                                                <i class="bi bi-eye text-sm"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 p-1.5 text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800" title="{{ __("admin.action.edit") }}">
                                                <i class="bi bi-pencil text-sm"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __("admin.confirm.delete") }}')" data-ajax-action>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 dark:border-red-800 p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20" title="{{ __("admin.action.delete") }}">
                                                    <i class="bi bi-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="bi bi-people text-4xl text-slate-300 dark:text-slate-600"></i>
                                        <p class="mt-3 text-sm font-medium text-slate-500 dark:text-slate-400">{{ __("admin.empty.users") }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-6 rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-4">
                    {{ $users->links() }}
                </div>
            @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
