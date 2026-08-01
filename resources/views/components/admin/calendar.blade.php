@props(['events' => []])

@php
    $now = now();
    $currentMonth = $now->copy()->startOfMonth();
    $daysInMonth = $currentMonth->daysInMonth;
    $startDayOfWeek = (int) $currentMonth->dayOfWeek; // 0=Sun
    $today = $now->day;

    $weekDays = [__('admin.calendar.sun'), __('admin.calendar.mon'), __('admin.calendar.tue'), __('admin.calendar.wed'), __('admin.calendar.thu'), __('admin.calendar.fri'), __('admin.calendar.sat')];

    $eventsByDay = collect($events)->flatMap(function ($event) use ($currentMonth) {
        $start = \Carbon\Carbon::parse($event['start']);
        $end = \Carbon\Carbon::parse($event['end']);
        $days = [];
        $cursor = $start->copy()->startOfDay();
        while ($cursor->lte($end)) {
            if ($cursor->month === $currentMonth->month && $cursor->year === $currentMonth->year) {
                $days[$cursor->day][] = $event;
            }
            $cursor->addDay();
        }
        return $days;
    });

    $calendarCells = [];
    for ($i = 0; $i < $startDayOfWeek; $i++) {
        $calendarCells[] = null;
    }
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $calendarCells[] = $day;
    }
@endphp

<div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-violet-50/60 dark:bg-slate-900 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-950">
                <i class="bi bi-calendar3 text-sm text-purple-600 dark:text-purple-400"></i>
            </span>
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $currentMonth->translatedFormat(__('auth.date_format_month_year')) }}</h3>
        </div>
        <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span> {{ __('admin.dashboard.legend_pending') }}</span>
            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-blue-400"></span> {{ __('admin.dashboard.legend_confirmed') }}</span>
            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span> {{ __('admin.dashboard.legend_checked_in') }}</span>
            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span> {{ __('admin.status.checked_out') }}</span>
            <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-red-400"></span> {{ __('admin.status.cancelled') }}</span>
        </div>
    </div>

    <div class="p-4">
        {{-- Week day headers --}}
        <div class="grid grid-cols-7 mb-2">
            @foreach ($weekDays as $wd)
                <div class="text-center text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 py-1">{{ $wd }}</div>
            @endforeach
        </div>

        {{-- Calendar grid --}}
        <div class="grid grid-cols-7 gap-px bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden">
            @foreach ($calendarCells as $cell)
                @if ($cell === null)
                    <div class="bg-slate-50 dark:bg-slate-900/50 h-20 sm:h-24"></div>
                @else
                    @php
                        $isToday = $cell === $today;
                        $dayEvents = $eventsByDay->get($cell, []);
                    @endphp
                    <div class="bg-white dark:bg-slate-900 h-20 sm:h-24 p-1.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50 relative">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full text-xs font-semibold
                            {{ $isToday ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $cell }}
                        </span>
                        <div class="mt-0.5 space-y-0.5">
                            @foreach (array_slice($dayEvents, 0, 2) as $evt)
                                <div class="truncate rounded px-1 py-0.5 text-[9px] font-semibold text-white leading-tight"
                                     style="background-color: {{ $evt['color'] }}"
                                     title="{{ $evt['title'] }} ({{ __("admin.status.{$evt['status']}") }})">
                                    {{ Str::limit($evt['title'], 15) }}
                                </div>
                            @endforeach
                            @if (count($dayEvents) > 2)
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500">{{ __('admin.dashboard.more', ['count' => count($dayEvents) - 2]) }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
