<x-layouts.admin :title="__('admin.title.edit', ['resource' => __('admin.nav.room_types')])" active="rooms">

    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.room-types.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.room_types') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $roomType->name }}</span>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('admin.action.edit') }}</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 px-5 py-3">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('admin.form.fix_errors') }}
            </p>
            <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.room-types.update', $roomType) }}">
        @csrf
        @method('PUT')
        <x-admin.room-type-form :roomType="$roomType" :hotels="$hotels" :submitLabel="__('admin.room_types.update_submit')" />
    </form>

</x-layouts.admin>
