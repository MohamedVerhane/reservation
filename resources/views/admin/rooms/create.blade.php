<x-layouts.admin :title="__('admin.title.create', ['resource' => __('admin.nav.rooms')])" active="rooms-actual">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-400 dark:text-slate-500">
            <a href="{{ route('admin.rooms.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ __('admin.nav.rooms') }}</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ __('admin.action.create') }}</span>
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

    <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" data-ajax>
        @csrf
        <x-admin.room-form :hotels="$hotels" :roomTypes="$roomTypes" :roomTypesByHotel="$roomTypesByHotel" :amenities="$amenities" :submitLabel="__('admin.rooms.create_submit')" />
    </form>
</x-layouts.admin>
