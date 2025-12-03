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
    /**
     * Apply branch filter to query - includes transactions for this branch OR with null admin_id
     */
    private function applyBranchFilter($query, $branchAdminIds, $column = 'admin_id')
    {
        return $query->where(function ($q) use ($branchAdminIds, $column) {
            $q->whereIn($column, $branchAdminIds)
              ->orWhereNull($column);
        });
    }

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id')->toArray();
        
        // Check for all_time parameter (handles both string 'true' and boolean)
        $allTime = filter_var($request->get('all_time', false), FILTER_VALIDATE_BOOLEAN);
        $period = $request->get('period', $allTime ? 'year' : 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $useCustomRange = $startDate && $endDate;
        
        $revenueData = $this->getRevenueData($branchAdminIds, $period, $date, $allTime, $startDate, $endDate);
        $popularServices = $this->getPopularServices($branchAdminIds, $period, $date, $allTime, $startDate, $endDate);
        $popularProducts = $this->getPopularProducts($branchAdminIds, $period, $date, $allTime, $startDate, $endDate);
        $itemTypeDistribution = $this->getItemTypeDistribution($branchAdminIds, $period, $date, $allTime, $startDate, $endDate);
        $statusDistribution = $this->getStatusDistribution($branchAdminIds, $period, $date, $allTime, $startDate, $endDate);
        $metrics = $this->getKeyMetrics($branchAdminIds, $period, $date, $allTime, $startDate, $endDate);

        return view('admin.analytics.index', compact(
            'revenueData',
            'popularServices',
            'popularProducts',
            'itemTypeDistribution',
            'statusDistribution',
            'metrics',
            'period',
            'date',
            'allTime',
            'startDate',
            'endDate',
            'useCustomRange'
        ));
    }

    private function getDateRange($period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        // Custom date range takes priority
        if ($startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay(),
                'all_time' => false
            ];
        }
        
        // All Time - no date restrictions
        if ($allTime) {
            return [
                'start' => null,
                'end' => null,
                'all_time' => true
            ];
        }
        
        $currentDate = Carbon::parse($date);
        
        return match ($period) {
            'day' => [
                'start' => $currentDate->copy()->startOfDay(),
                'end' => $currentDate->copy()->endOfDay(),
                'all_time' => false
            ],
            'week' => [
                'start' => $currentDate->copy()->startOfWeek(),
                'end' => $currentDate->copy()->endOfWeek(),
                'all_time' => false
            ],
            'month' => [
                'start' => $currentDate->copy()->startOfMonth(),
                'end' => $currentDate->copy()->endOfMonth(),
                'all_time' => false
            ],
            default => [
                'start' => $currentDate->copy()->startOfYear(),
                'end' => $currentDate->copy()->endOfYear(),
                'all_time' => false
            ]
        };
    }

    /**
     * Apply date range filter - skips if all_time is true
     */
    private function applyDateFilter($query, $range, $column = 'booking_date')
    {
        if ($range['all_time']) {
            return $query; // No date filter for all time
        }
        return $query->whereBetween($column, [$range['start'], $range['end']]);
    }

    private function getRevenueData($branchAdminIds, $period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        $labels = [];
        $revenue = [];
        $currentDate = Carbon::parse($date);

        // Custom date range
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $daysDiff = $start->diffInDays($end);
            
            if ($daysDiff <= 31) {
                // Show daily data
                for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                    $labels[] = $d->format('M j');
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                        ->whereDate('booking_date', $d)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
            } else {
                // Show monthly data
                for ($m = $start->copy()->startOfMonth(); $m->lte($end); $m->addMonth()) {
                    $labels[] = $m->format('M Y');
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                        ->whereYear('booking_date', $m->year)
                        ->whereMonth('booking_date', $m->month)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
            }
            return ['labels' => $labels, 'data' => $revenue];
        }

        if ($allTime) {
            // For all time, show monthly data for the current year or yearly if data spans multiple years
            $firstTransaction = $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                ->orderBy('booking_date', 'asc')->first();
            $lastTransaction = $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                ->orderBy('booking_date', 'desc')->first();
            
            if (!$firstTransaction) {
                // No data - show current year months with zeros
                for ($month = 1; $month <= 12; $month++) {
                    $labels[] = Carbon::create(now()->year, $month, 1)->format('M');
                    $revenue[] = 0;
                }
                return ['labels' => $labels, 'data' => $revenue];
            }
            
            $startYear = Carbon::parse($firstTransaction->booking_date)->year;
            $endYear = $lastTransaction ? Carbon::parse($lastTransaction->booking_date)->year : now()->year;
            
            // If data spans multiple years, show yearly
            if ($endYear - $startYear > 1) {
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $labels[] = (string) $year;
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                        ->whereYear('booking_date', $year)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
            } else {
                // Show monthly data
                $start = Carbon::parse($firstTransaction->booking_date)->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                
                for ($m = $start->copy(); $m->lte($end); $m->addMonth()) {
                    $labels[] = $m->format('M Y');
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                        ->whereYear('booking_date', $m->year)
                        ->whereMonth('booking_date', $m->month)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
            }
            return ['labels' => $labels, 'data' => $revenue];
        }

        switch ($period) {
            case 'day':
                for ($hour = 0; $hour < 24; $hour++) {
                    $labels[] = sprintf('%02d:00', $hour);
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
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
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
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
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                        ->whereDate('booking_date', $dayDate)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
                break;

            default: // year
                for ($month = 1; $month <= 12; $month++) {
                    $monthDate = $currentDate->copy()->month($month);
                    $labels[] = $monthDate->format('M');
                    $revenue[] = (float) $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
                        ->whereYear('booking_date', $currentDate->year)
                        ->whereMonth('booking_date', $month)
                        ->where('status', 'completed')
                        ->sum('total_price');
                }
                break;
        }

        return ['labels' => $labels, 'data' => $revenue];
    }

    private function getPopularServices($branchAdminIds, $period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        $range = $this->getDateRange($period, $date, $allTime, $startDate, $endDate);
        
        $query = DB::table('service_transactions')
            ->join('services', 'service_transactions.service_id', '=', 'services.id')
            ->join('transactions', 'service_transactions.transaction_id', '=', 'transactions.id')
            ->where(function ($q) use ($branchAdminIds) {
                $q->whereIn('transactions.admin_id', $branchAdminIds)
                  ->orWhereNull('transactions.admin_id');
            });
        
        // Apply date filter only if not all time
        if (!$range['all_time']) {
            $query->whereBetween('transactions.booking_date', [$range['start'], $range['end']]);
        }
        
        return $query->select(
                'services.service_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(service_transactions.price_at_purchase) as total_revenue')
            )
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getPopularProducts($branchAdminIds, $period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        $range = $this->getDateRange($period, $date, $allTime, $startDate, $endDate);
        
        $query = DB::table('product_transactions')
            ->join('products', 'product_transactions.product_id', '=', 'products.id')
            ->join('transactions', 'product_transactions.transaction_id', '=', 'transactions.id')
            ->where(function ($q) use ($branchAdminIds) {
                $q->whereIn('transactions.admin_id', $branchAdminIds)
                  ->orWhereNull('transactions.admin_id');
            });
        
        // Apply date filter only if not all time
        if (!$range['all_time']) {
            $query->whereBetween('transactions.booking_date', [$range['start'], $range['end']]);
        }
        
        return $query->select(
                'products.product_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(product_transactions.price_at_purchase) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getItemTypeDistribution($branchAdminIds, $period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        $range = $this->getDateRange($period, $date, $allTime, $startDate, $endDate);
        
        $query = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $query = $this->applyDateFilter($query, $range);
        
        $distribution = $query->select('item_type', DB::raw('COUNT(*) as count'))
            ->groupBy('item_type')
            ->get();

        return [
            'labels' => $distribution->pluck('item_type')->map(fn($type) => ucfirst($type))->toArray(),
            'data' => $distribution->pluck('count')->toArray()
        ];
    }

    private function getStatusDistribution($branchAdminIds, $period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        $range = $this->getDateRange($period, $date, $allTime, $startDate, $endDate);
        
        $query = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $query = $this->applyDateFilter($query, $range);
        
        $distribution = $query->select('status', DB::raw('COUNT(*) as count'))
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

    private function getKeyMetrics($branchAdminIds, $period, $date, $allTime = false, $startDate = null, $endDate = null)
    {
        $range = $this->getDateRange($period, $date, $allTime, $startDate, $endDate);
        $currentDate = Carbon::parse($date);
        $useCustomRange = $startDate && $endDate;
        
        // Previous period (not applicable for all time or custom range)
        $prevRange = ($allTime || $useCustomRange) ? null : match ($period) {
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

        // Build queries with branch filter
        $revenueQuery = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $revenueQuery = $this->applyDateFilter($revenueQuery, $range);
        $currentRevenue = $revenueQuery->where('status', 'completed')->sum('total_price');
            
        $previousRevenue = $prevRange ? $this->applyBranchFilter(Transaction::query(), $branchAdminIds)
            ->whereBetween('booking_date', [$prevRange['start'], $prevRange['end']])
            ->where('status', 'completed')
            ->sum('total_price') : 0;

        $bookingsQuery = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $bookingsQuery = $this->applyDateFilter($bookingsQuery, $range);
        $periodBookings = $bookingsQuery->count();

        $completedQuery = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $completedQuery = $this->applyDateFilter($completedQuery, $range);
        $completedBookings = $completedQuery->where('status', 'completed')->count();
            
        $cancelledQuery = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $cancelledQuery = $this->applyDateFilter($cancelledQuery, $range);
        $cancelledBookings = $cancelledQuery->where('status', 'cancelled')->count();
            
        $averageOrderValue = $completedBookings > 0 ? $currentRevenue / $completedBookings : 0;

        $pendingQuery = $this->applyBranchFilter(Transaction::query(), $branchAdminIds);
        $pendingQuery = $this->applyDateFilter($pendingQuery, $range);
        $pendingBookings = $pendingQuery->where('status', 'pending')->count();

        return [
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'period_bookings' => $periodBookings,
            'pending_bookings' => $pendingBookings,
            'completed_bookings' => $completedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'average_order_value' => $averageOrderValue,
            'all_time' => $allTime,
            'use_custom_range' => $useCustomRange,
        ];
    }
}
