<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'year');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        // Revenue data based on period
        $revenueData = $this->getRevenueData($period, $date);
        
        // Popular services
        $popularServices = $this->getPopularServices($period, $date);
        
        // Popular products
        $popularProducts = $this->getPopularProducts($period, $date);
        
        // Item type distribution
        $itemTypeDistribution = $this->getItemTypeDistribution($period, $date);
        
        // Status distribution
        $statusDistribution = $this->getStatusDistribution($period, $date);
        
        // Key metrics
        $metrics = $this->getKeyMetrics($period, $date);

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
        
        switch ($period) {
            case 'day':
                return [
                    'start' => $currentDate->copy()->startOfDay(),
                    'end' => $currentDate->copy()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => $currentDate->copy()->startOfWeek(),
                    'end' => $currentDate->copy()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $currentDate->copy()->startOfMonth(),
                    'end' => $currentDate->copy()->endOfMonth()
                ];
            case 'year':
            default:
                return [
                    'start' => $currentDate->copy()->startOfYear(),
                    'end' => $currentDate->copy()->endOfYear()
                ];
        }
    }

    private function getRevenueData($period, $date)
    {
        $labels = [];
        $revenue = [];
        $currentDate = Carbon::parse($date);

        switch ($period) {
            case 'day':
                // Hourly data for the day
                for ($hour = 0; $hour < 24; $hour++) {
                    $labels[] = sprintf('%02d:00', $hour);
                    $hourRevenue = Transaction::whereDate('created_at', $currentDate)
                        ->whereHour('created_at', $hour)
                        ->where('status', '!=', 'cancelled')
                        ->sum('total_price');
                    $revenue[] = (float) $hourRevenue;
                }
                break;

            case 'week':
                // Daily data for the week
                $startOfWeek = $currentDate->copy()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $day = $startOfWeek->copy()->addDays($i);
                    $labels[] = $day->format('D, M d');
                    $dayRevenue = Transaction::whereDate('created_at', $day)
                        ->where('status', '!=', 'cancelled')
                        ->sum('total_price');
                    $revenue[] = (float) $dayRevenue;
                }
                break;

            case 'month':
                // Daily data for the month
                $daysInMonth = $currentDate->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $labels[] = (string) $day;
                    $dayDate = $currentDate->copy()->day($day);
                    $dayRevenue = Transaction::whereDate('created_at', $dayDate)
                        ->where('status', '!=', 'cancelled')
                        ->sum('total_price');
                    $revenue[] = (float) $dayRevenue;
                }
                break;

            case 'year':
            default:
                // Monthly data for the year
                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = $currentDate->copy()->month($month);
                    $labels[] = $monthDate->format('M');
                    $monthRevenue = Transaction::whereYear('created_at', $currentDate->year)
                        ->whereMonth('created_at', $month)
                        ->where('status', '!=', 'cancelled')
                        ->sum('total_price');
                    $revenue[] = (float) $monthRevenue;
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $revenue
        ];
    }

    private function getPopularServices($period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        return DB::table('service_transactions')
            ->join('services', 'service_transactions.service_id', '=', 'services.id')
            ->join('transactions', 'service_transactions.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$range['start'], $range['end']])
            ->select(
                'services.service_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(service_transactions.price_at_purchase) as total_revenue')
            )
            ->groupBy('services.id', 'services.service_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getPopularProducts($period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        return DB::table('product_transactions')
            ->join('products', 'product_transactions.product_id', '=', 'products.id')
            ->join('transactions', 'product_transactions.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$range['start'], $range['end']])
            ->select(
                'products.product_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(product_transactions.price_at_purchase) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getItemTypeDistribution($period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        $distribution = Transaction::whereBetween('created_at', [$range['start'], $range['end']])
            ->select('item_type', DB::raw('COUNT(*) as count'))
            ->groupBy('item_type')
            ->get();

        return [
            'labels' => $distribution->pluck('item_type')->map(fn($type) => ucfirst($type))->toArray(),
            'data' => $distribution->pluck('count')->toArray()
        ];
    }

    private function getStatusDistribution($period, $date)
    {
        $range = $this->getDateRange($period, $date);
        
        $distribution = Transaction::whereBetween('created_at', [$range['start'], $range['end']])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $distribution->pluck('status')->map(fn($status) => ucfirst($status))->toArray(),
            'data' => $distribution->pluck('count')->toArray()
        ];
    }

    private function getKeyMetrics($period, $date)
    {
        $range = $this->getDateRange($period, $date);
        $currentDate = Carbon::parse($date);
        
        // Calculate previous period for comparison
        switch ($period) {
            case 'day':
                $prevStart = $currentDate->copy()->subDay()->startOfDay();
                $prevEnd = $currentDate->copy()->subDay()->endOfDay();
                break;
            case 'week':
                $prevStart = $currentDate->copy()->subWeek()->startOfWeek();
                $prevEnd = $currentDate->copy()->subWeek()->endOfWeek();
                break;
            case 'month':
                $prevStart = $currentDate->copy()->subMonth()->startOfMonth();
                $prevEnd = $currentDate->copy()->subMonth()->endOfMonth();
                break;
            case 'year':
            default:
                $prevStart = $currentDate->copy()->subYear()->startOfYear();
                $prevEnd = $currentDate->copy()->subYear()->endOfYear();
                break;
        }

        $currentRevenue = Transaction::whereBetween('created_at', [$range['start'], $range['end']])
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');
            
        $previousRevenue = Transaction::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        return [
            'total_revenue' => $currentRevenue,
            'total_bookings' => Transaction::whereBetween('created_at', [$range['start'], $range['end']])->count(),
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'pending_bookings' => Transaction::whereBetween('created_at', [$range['start'], $range['end']])
                ->where('status', 'pending')->count(),
            'completed_bookings' => Transaction::whereBetween('created_at', [$range['start'], $range['end']])
                ->where('status', 'completed')->count(),
            'period_bookings' => Transaction::whereBetween('created_at', [$range['start'], $range['end']])->count(),
        ];
    }
}
