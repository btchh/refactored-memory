<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set date range based on period
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            switch ($period) {
                case 'today':
                    $start = Carbon::today();
                    $end = Carbon::today()->endOfDay();
                    break;
                case 'week':
                    $start = Carbon::now()->startOfWeek();
                    $end = Carbon::now()->endOfWeek();
                    break;
                case 'year':
                    $start = Carbon::now()->startOfYear();
                    $end = Carbon::now()->endOfYear();
                    break;
                case 'month':
                default:
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
                    break;
            }
        }

        // Get completed transactions
        $transactions = Transaction::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['completed', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stats
        $totalRevenue = $transactions->sum('total_price');
        $totalTransactions = $transactions->count();
        $averageOrder = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Group by service type
        $revenueByService = $transactions->groupBy('service_type')->map(function ($items) {
            return [
                'count' => $items->count(),
                'revenue' => $items->sum('total_price')
            ];
        });

        return view('admin.revenue.index', compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'averageOrder',
            'revenueByService',
            'period',
            'start',
            'end'
        ));
    }

    public function export(Request $request)
    {
        // This will be handled by JavaScript print functionality
        return redirect()->route('admin.revenue.index', $request->all());
    }
}
