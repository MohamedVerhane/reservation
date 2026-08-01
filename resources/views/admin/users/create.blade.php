<x-layouts.admin :title="__('admin.title.create', ['resource' => __('admin.nav.users')])" active="users">
    <div class="space-y-6">
        <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('admin.nav.users') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-900 dark:text-white">{{ __('admin.action.create') }}</span>
        </nav>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
                <div class="flex items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill mt-0.5 text-red-600 dark:text-red-400"></i>
                    <div>
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ __('admin.form.fix_errors') }}</p>
                        <ul class="mt-1 list-inside list-disc text-sm text-red-600 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" data-ajax>
            @csrf
            <x-admin.user-form :submitLabel="__('admin.users.create')" />
        </form>
    </div>
</x-layouts.admin>
