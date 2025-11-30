<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $revenueData = $this->getRevenueData($admin->id, $period, $date);
        $popularServices = $this->getPopularServices($admin->id, $period, $date);
        $popularProducts = $this->getPopularProducts($admin->id, $period, $date);
        $itemTypeDistribution = $this->getItemTypeDistribution($admin->id, $period, $date);
        $statusDistribution = $this->getStatusDistribution($admin->id, $period, $date);
        $metrics = $this->getKeyMetrics($admin->id, $period, $date);

        return view('admin.analytics.index', compact(
            'revenueData',
            'popularServices',
            'popularProducts',
            'itemTypeDistribution',
            'statusDistribution',
            'metrics',
            'period',
            'date'
        ));
    }

    private function getDateRange($period, $date)
    {
        $currentDate = Carbon::parse($date);
        
        return match ($period) {
            'day' => [
                'start' => $currentDate->copy()->startOfDay(),
                'end' => $currentDate->copy()->endOfDay()
            ],
            'week' => [
                'start' => $currentDate->copy()->startOfWeek(),
                'end' => $currentDate->copy()->endOfWeek()
            ],
            'month' => [
                'start' => $currentDate->copy()->startOfMonth(),
                'end' => $currentDate->copy()->endOfMonth()
            ],
            default => [
                'start' => $currentDate->copy()->startOfYear(),
                'end' => $currentDate->copy()->endOfYear()
            ]
        };
    }

    private function getRevenueData($adminId, $period, $date)
    {
        $labels = [];
        $revenue = [];
        $currentDate = Carbon::parse($date);

        switch ($period) {
            case 'day':
                for ($hour = 0; $hour < 24; $hour++) {
                    $labels[] = sprintf('%02d:00', $hour);
                    $revenue[] = (float) Transaction::where('admin_id', $adminId)
                        ->whereDate('booking_date', $currentDate)
                        ->whereRaw("HOUR(booking_time) = ?", [$hour])
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
                break;

            case 'week':
                $startOfWeek = $currentDate->copy()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $day = $startOfWeek->copy()->addDays($i);
                    $labels[] = $day->format('D');
                    $revenue[] = (float) Transaction::where('admin_id', $adminId)
                        ->whereDate('booking_date', $day)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
                break;

            case 'month':
                $daysInMonth = $currentDate->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $labels[] = (string) $day;
                    $dayDate = $currentDate->copy()->day($day);
                    $revenue[] = (float) Transaction::where('admin_id', $adminId)
                        ->whereDate('booking_date', $dayDate)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
                break;

            default: // year
                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = $currentDate->copy()->month($month);
                    $labels[] = $monthDate->format('M');
                    $revenue[] = (float) Transaction::where('admin_id', $adminId)
                        ->whereYear('booking_date', $currentDate->year)
                        ->whereMonth('booking_date', $month)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
                break;
        }

        return ['labels' => $labels, 'data' => $revenue];
    }

    private function getPopularServices($adminId, $period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        return DB::table('service_transactions')
            ->join('services', 'service_transactions.service_id', '=', 'services.id')
            ->join('transactions', 'service_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.admin_id', $adminId)
            ->whereBetween('transactions.booking_date', [$range['start'], $range['end']])
            ->select(
                'services.service_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(service_transactions.price_at_purchase) as total_revenue')
            )
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getPopularProducts($adminId, $period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        return DB::table('product_transactions')
            ->join('products', 'product_transactions.product_id', '=', 'products.id')
            ->join('transactions', 'product_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.admin_id', $adminId)
            ->whereBetween('transactions.booking_date', [$range['start'], $range['end']])
            ->select(
                'products.product_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(product_transactions.price_at_purchase) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getItemTypeDistribution($adminId, $period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        $distribution = Transaction::where('admin_id', $adminId)
            ->whereBetween('booking_date', [$range['start'], $range['end']])
            ->select('item_type', DB::raw('COUNT(*) as count'))
            ->groupBy('item_type')
            ->get();

        return [
            'labels' => $distribution->pluck('item_type')->map(fn($type) => ucfirst($type))->toArray(),
            'data' => $distribution->pluck('count')->toArray()
        ];
    }

    private function getStatusDistribution($adminId, $period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        $distribution = Transaction::where('admin_id', $adminId)
            ->whereBetween('booking_date', [$range['start'], $range['end']])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return [
            'labels' => $distribution->pluck('status')->map(fn($s) => $statusLabels[$s] ?? ucfirst($s))->toArray(),
            'data' => $distribution->pluck('count')->toArray()
        ];
    }

    private function getKeyMetrics($adminId, $period, $date)
    {
        $range = $this->getDateRange($period, $date);
        $currentDate = Carbon::parse($date);
        
        // Previous period
        $prevRange = match ($period) {
            'day' => [
                'start' => $currentDate->copy()->subDay()->startOfDay(),
                'end' => $currentDate->copy()->subDay()->endOfDay()
            ],
            'week' => [
                'start' => $currentDate->copy()->subWeek()->startOfWeek(),
                'end' => $currentDate->copy()->subWeek()->endOfWeek()
            ],
            'month' => [
                'start' => $currentDate->copy()->subMonth()->startOfMonth(),
                'end' => $currentDate->copy()->subMonth()->endOfMonth()
            ],
            default => [
                'start' => $currentDate->copy()->subYear()->startOfYear(),
                'end' => $currentDate->copy()->subYear()->endOfYear()
            ]
        };

        $currentRevenue = Transaction::where('admin_id', $adminId)
            ->whereBetween('booking_date', [$range['start'], $range['end']])
            ->where('status', 'completed')
            ->sum('total_price');
            
        $previousRevenue = Transaction::where('admin_id', $adminId)
            ->whereBetween('booking_date', [$prevRange['start'], $prevRange['end']])
            ->where('status', 'completed')
            ->sum('total_price');

        $periodBookings = Transaction::where('admin_id', $adminId)
            ->whereBetween('booking_date', [$range['start'], $range['end']])
            ->count();

        return [
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'period_bookings' => $periodBookings,
            'pending_bookings' => Transaction::where('admin_id', $adminId)
                ->whereBetween('booking_date', [$range['start'], $range['end']])
                ->where('status', 'pending')
                ->count(),
            'completed_bookings' => Transaction::where('admin_id', $adminId)
                ->whereBetween('booking_date', [$range['start'], $range['end']])
                ->where('status', 'completed')
                ->count(),
        ];
    }
}
