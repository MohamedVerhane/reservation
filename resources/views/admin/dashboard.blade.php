<x-layouts.admin :title="__('admin.nav.dashboard')" active="dashboard">

    {{-- ═══ Stat Cards ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6 gap-5 mb-8">
        <x-admin.stat-card :title="__('admin.dashboard.stats_hotels')" :value="$stats['hotels']" icon="bi-building" color="indigo" />
        <x-admin.stat-card :title="__('admin.dashboard.stats_rooms')" :value="$stats['rooms']" icon="bi-door-open" color="blue" />
        <x-admin.stat-card :title="__('admin.dashboard.stats_reservations')" :value="$stats['reservations']" icon="bi-calendar-check" color="purple" />
        <x-admin.stat-card :title="__('admin.dashboard.stats_revenue')" :value="__('auth.notif_currency_format', ['amount' => number_format($stats['revenue'], 0)])" icon="bi-currency-dollar" color="emerald" />
        <x-admin.stat-card :title="__('admin.dashboard.stats_guests')" :value="$stats['guests']" icon="bi-people" color="amber" />
        <x-admin.stat-card :title="__('admin.dashboard.stats_avg_rating')" :value="__('auth.notif_rating_format', ['rating' => $stats['rating']])" icon="bi-star-fill" color="rose" />
    </div>

    {{-- ═══ Charts Row ═══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">

        {{-- Reservations trend --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-blue-50/60 dark:bg-slate-900 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950">
                    <i class="bi bi-graph-up text-sm text-indigo-600 dark:text-indigo-400"></i>
                </span>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.dashboard.reservations_trend') }}</h3>
            </div>
            <div class="relative h-64">
                <canvas id="reservationsChart"></canvas>
            </div>
        </div>

        {{-- Revenue trend --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-emerald-50/60 dark:bg-slate-900 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950">
                    <i class="bi bi-cash-stack text-sm text-emerald-600 dark:text-emerald-400"></i>
                </span>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.dashboard.revenue_trend') }}</h3>
            </div>
            <div class="relative h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ═══ Status + Top Hotels ═══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-8">

        {{-- Status breakdown --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-purple-50/60 dark:bg-slate-900 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-950">
                    <i class="bi bi-pie-chart text-sm text-purple-600 dark:text-purple-400"></i>
                </span>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ __('admin.dashboard.reservation_status') }}</h3>
            </div>
            <div class="relative h-56">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Revenue summary --}}
        <div class="xl:col-span-2">
            <x-admin.revenue-summary :revenue="$revenue" />
        </div>
    </div>

    {{-- ═══ Latest Bookings ═══ --}}
    <div class="mb-8">
        <x-admin.latest-bookings :bookings="$latestBookings" />
    </div>

    {{-- ═══ Reviews + Calendar Row ═══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mb-8">

        {{-- Latest Reviews --}}
        <div class="xl:col-span-2">
            <x-admin.latest-reviews :reviews="$latestReviews" />
        </div>

        {{-- Calendar --}}
        <div class="xl:col-span-3">
            <x-admin.calendar :events="$calendarEvents" />
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.08)' : 'rgba(148, 163, 184, 0.15)';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
            };

            // ── Reservations Trend ──
            new Chart(document.getElementById('reservationsChart'), {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: @json(__('admin.dashboard.stats_reservations')),
                        data: @json($chartData['reservations_monthly']),
                        borderColor: '#6366f1',
                        backgroundColor: isDark ? 'rgba(99, 102, 241, 0.1)' : 'rgba(99, 102, 241, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#6366f1',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    }],
                },
                options: {
                    ...chartDefaults,
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 6 },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 10 }, stepSize: 1 },
                        },
                    },
                    interaction: { intersect: false, mode: 'index' },
                },
            });

            // ── Revenue Trend ──
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: @json(__('admin.dashboard.stats_revenue')),
                        data: @json($chartData['revenue_monthly']),
                        backgroundColor: isDark ? 'rgba(16, 185, 129, 0.3)' : 'rgba(16, 185, 129, 0.15)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                    }],
                },
                options: {
                    ...chartDefaults,
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 6 },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 10 }, callback: v => '$' + v.toLocaleString() },
                        },
                    },
                },
            });

            // ── Status Breakdown ──
            const statusKeys = @json(array_keys($chartData['status_breakdown']));
            const statusLabelMap = {!! json_encode([
                'pending' => __('admin.status.pending'),
                'confirmed' => __('admin.status.confirmed'),
                'checked_in' => __('admin.status.checked_in'),
                'checked_out' => __('admin.status.checked_out'),
                'cancelled' => __('admin.status.cancelled'),
            ]) !!};
            const statusLabels = statusKeys.map(s => statusLabelMap[s] || s);
            const statusValues = @json(array_values($chartData['status_breakdown']));
            const statusColors = {
                pending: '#f59e0b',
                confirmed: '#3b82f6',
                checked_in: '#10b981',
                checked_out: '#6b7280',
                cancelled: '#ef4444',
            };

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels.map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusLabels.map(s => statusColors[s] || '#6b7280'),
                        borderWidth: 0,
                        spacing: 2,
                        borderRadius: 4,
                    }],
                },
                options: {
                    ...chartDefaults,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                font: { size: 11 },
                                padding: 12,
                                usePointStyle: true,
                                pointStyleWidth: 8,
                            },
                        },
                    },
                },
            });

        });
    </script>
    @endpush

</x-layouts.admin>
