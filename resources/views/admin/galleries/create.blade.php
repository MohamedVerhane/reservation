<x-layouts.admin :title="__('admin.title.create', ['resource' => __('admin.nav.galleries')])" active="galleries">

    {{-- ═══ Breadcrumb ═══ --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.galleries.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.galleries') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('admin.action.create') }}</span>
        </div>
    </div>

    {{-- ═══ Flash Messages ═══ --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-5 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400"></i>
            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ═══ Errors ═══ --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3">
            <div class="flex items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400"></i>
                <p class="text-sm font-bold text-red-700 dark:text-red-400">{{ __('admin.form.fix_errors') }}</p>
            </div>
            <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.galleries.store') }}" enctype="multipart/form-data" data-ajax>
        @csrf
        <x-admin.gallery-form :hotels="$hotels" :submitLabel="__('admin.galleries.create')" />
    </form>

</x-layouts.admin>
