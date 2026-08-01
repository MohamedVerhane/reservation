<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = $this->getStats();
        $latestBookings = $this->getLatestBookings();
        $latestReviews = $this->getLatestReviews();
        $revenue = $this->getRevenueSummary();
        $chartData = $this->getChartData();
        $calendarEvents = $this->getCalendarEvents();

        return view('admin.dashboard', compact(
            'stats',
            'latestBookings',
            'latestReviews',
            'revenue',
            'chartData',
            'calendarEvents',
        ));
    }

    /** @return array{hotels: int, rooms: int, reservations: int, revenue: float, guests: int, rating: float} */
    private function getStats(): array
    {
        return Cache::remember('admin.dashboard.stats', 300, function () {
            $totalRevenue = Payment::completed()->sum('amount') ?? 0;

            return [
                'hotels' => Hotel::count(),
                'rooms' => Room::count(),
                'reservations' => Reservation::count(),
                'revenue' => (float) $totalRevenue,
                'guests' => User::guests()->count(),
                'rating' => round(Review::avg('rating') ?? 0, 1),
            ];
        });
    }

    /** @return Collection<Reservation> */
    private function getLatestBookings()
    {
        return Cache::remember('admin.dashboard.latest-bookings', 300, function () {
            return Reservation::with(['user', 'hotel', 'room.roomType'])
                ->latest()
                ->limit(8)
                ->get();
        });
    }

    /** @return Collection<Review> */
    private function getLatestReviews()
    {
        return Cache::remember('admin.dashboard.latest-reviews', 300, function () {
            return Review::with(['user', 'hotel'])
                ->latest()
                ->limit(5)
                ->get();
        });
    }

    /** @return array{total: float, this_month: float, last_month: float, growth: float} */
    private function getRevenueSummary(): array
    {
        return Cache::remember('admin.dashboard.revenue', 300, function () {
            $total = (float) Payment::completed()->sum('amount');
            $thisMonth = (float) Payment::completed()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount');
            $lastMonth = (float) Payment::completed()
                ->whereMonth('paid_at', now()->subMonth()->month)
                ->whereYear('paid_at', now()->subMonth()->year)
                ->sum('amount');

            $growth = $lastMonth > 0
                ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
                : ($thisMonth > 0 ? 100.0 : 0.0);

            return [
                'total' => $total,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'growth' => $growth,
            ];
        });
    }

    /** @return array{reservations_monthly: array<int, int>, revenue_monthly: array<int, float>, status_breakdown: array<string, int>, hotels_top: array<int, int>} */
    private function getChartData(): array
    {
        return Cache::remember('admin.dashboard.charts', 300, function () {
            $months = collect();
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months->push([
                    'label' => $date->format('M Y'),
                    'month' => $date->month,
                    'year' => $date->year,
                ]);
            }

            $startDate = now()->subMonths(11)->startOfMonth();

            $driver = DB::getDriverName();

            $monthExpr = $driver === 'sqlite'
                ? "strftime('%m', created_at)"
                : 'MONTH(created_at)';
            $yearExpr = $driver === 'sqlite'
                ? "strftime('%Y', created_at)"
                : 'YEAR(created_at)';

            // Single aggregated query for reservations per month
            $reservationsData = Reservation::where('created_at', '>=', $startDate)
                ->selectRaw("{$monthExpr} as month, {$yearExpr} as year, COUNT(*) as count")
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(fn ($r) => $r->year.'-'.str_pad((string) $r->month, 2, '0', STR_PAD_LEFT));

            $reservationsMonthly = $months->map(fn ($m) => (int) ($reservationsData->get($m['year'].'-'.str_pad((string) $m['month'], 2, '0', STR_PAD_LEFT))?->count ?? 0))->toArray();

            $payMonthExpr = $driver === 'sqlite'
                ? "strftime('%m', paid_at)"
                : 'MONTH(paid_at)';
            $payYearExpr = $driver === 'sqlite'
                ? "strftime('%Y', paid_at)"
                : 'YEAR(paid_at)';

            // Single aggregated query for revenue per month
            $revenueData = Payment::completed()
                ->where('paid_at', '>=', $startDate)
                ->selectRaw("{$payMonthExpr} as month, {$payYearExpr} as year, SUM(amount) as total")
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(fn ($r) => $r->year.'-'.str_pad((string) $r->month, 2, '0', STR_PAD_LEFT));

            $revenueMonthly = $months->map(fn ($m) => (float) ($revenueData->get($m['year'].'-'.str_pad((string) $m['month'], 2, '0', STR_PAD_LEFT))?->total ?? 0))->toArray();

            $statusBreakdown = Reservation::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $hotelsTop = Reservation::select('hotel_id', DB::raw('count(*) as total'))
                ->groupBy('hotel_id')
                ->orderByDesc('total')
                ->limit(5)
                ->pluck('total', 'hotel_id')
                ->toArray();

            return [
                'labels' => $months->pluck('label')->toArray(),
                'reservations_monthly' => $reservationsMonthly,
                'revenue_monthly' => $revenueMonthly,
                'status_breakdown' => $statusBreakdown,
                'hotels_top' => $hotelsTop,
            ];
        });
    }

    /** @return array<int, array{id: int, title: string, start: string, end: string, color: string, status: string}> */
    private function getCalendarEvents(): array
    {
        return Cache::remember('admin.dashboard.calendar', 300, function () {
            return Reservation::with(['hotel:id,name', 'room:id,room_number'])
                ->select('id', 'room_id', 'hotel_id', 'check_in', 'check_out', 'status')
                ->where('check_out', '>=', now()->startOfMonth()->subMonth())
                ->where('check_in', '<=', now()->endOfMonth()->addMonth())
                ->whereNotIn('status', ['cancelled'])
                ->get()
                ->map(fn (Reservation $r) => [
                    'id' => $r->id,
                    'title' => ($r->hotel?->name ?? 'Hotel').' — '.($r->room?->room_number ?? '#'),
                    'start' => $r->check_in->format('Y-m-d'),
                    'end' => $r->check_out->format('Y-m-d'),
                    'color' => match ($r->status) {
                        'pending' => '#f59e0b',
                        'confirmed' => '#3b82f6',
                        'checked_in' => '#10b981',
                        'checked_out' => '#6b7280',
                        default => '#6b7280',
                    },
                    'status' => $r->status,
                ])
                ->toArray();
        });
    }
}
