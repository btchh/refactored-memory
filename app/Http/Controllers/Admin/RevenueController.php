<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('admin.revenue.index', $data);
    }

    public function export(Request $request)
    {
        $data = $this->getReportData($request);
        $data['reportConfig'] = config('reports.revenue');
        $data['companyConfig'] = config('reports.company');
        $data['printConfig'] = config('reports.print');
        $data['currency'] = $data['reportConfig']['currency'] ?? '₱';
        $data['dateFormat'] = $data['reportConfig']['date_format'] ?? 'F d, Y';
        $data['timeFormat'] = $data['reportConfig']['time_format'] ?? 'h:i A';

        return view('admin.revenue.export', $data);
    }

    public function exportCsv(Request $request)
    {
        $data = $this->getReportData($request);
        $reportConfig = config('reports.revenue');
        $companyConfig = config('reports.company');
        $currency = $reportConfig['currency'] ?? '₱';
        $dateFormat = $reportConfig['date_format'] ?? 'F d, Y';

        $filename = 'revenue_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data, $reportConfig, $companyConfig, $currency, $dateFormat) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, [$companyConfig['name']]);
            fputcsv($file, [$reportConfig['title'] ?? 'Revenue Report']);
            fputcsv($file, ['Report Period: ' . ($data['allTime'] ? 'All Time' : $data['start']->format($dateFormat) . ' - ' . $data['end']->format($dateFormat))]);
            fputcsv($file, ['Generated: ' . now()->format($dateFormat . ' h:i A')]);
            fputcsv($file, ['Report ID: REV-' . now()->format('YmdHis')]);
            fputcsv($file, []);

            // Summary
            fputcsv($file, ['FINANCIAL SUMMARY']);
            fputcsv($file, ['Total Revenue', $currency . number_format($data['totalRevenue'], 2)]);
            fputcsv($file, ['Total Transactions', $data['totalTransactions']]);
            fputcsv($file, ['Average Order Value', $currency . number_format($data['averageOrder'], 2)]);
            fputcsv($file, []);

            // Revenue by Service
            if ($data['revenueByService']->count() > 0) {
                fputcsv($file, ['REVENUE BY SERVICE TYPE']);
                fputcsv($file, ['Service', 'Revenue', 'Transactions']);
                foreach ($data['revenueByService'] as $service => $serviceData) {
                    fputcsv($file, [
                        ucfirst(str_replace('_', ' ', $service)),
                        $currency . number_format($serviceData['revenue'], 2),
                        $serviceData['count']
                    ]);
                }
                fputcsv($file, []);
            }

            // Transaction Details
            fputcsv($file, ['TRANSACTION DETAILS']);
            fputcsv($file, ['Date', 'Transaction ID', 'Customer', 'Service Type', 'Status', 'Amount']);

            foreach ($data['transactions'] as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('M d, Y'),
                    '#' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
                    $transaction->user ? $transaction->user->fname . ' ' . $transaction->user->lname : 'N/A',
                    ucfirst(str_replace('_', ' ', $transaction->item_type)),
                    ucfirst($transaction->status),
                    $currency . number_format($transaction->total_price, 2)
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL', $currency . number_format($data['totalRevenue'], 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData(Request $request): array
    {
        $admin = auth()->guard('admin')->user();
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $allTime = $request->get('all_time', false) === 'true' || $request->get('all_time') === '1';

        // Set date range based on period
        if ($allTime) {
            $start = Carbon::create(2000, 1, 1)->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($startDate && $endDate) {
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

        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        // Get completed transactions for this branch
        $transactions = Transaction::with('user')
            ->whereIn('admin_id', $branchAdminIds)
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['completed', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stats
        $totalRevenue = $transactions->sum('total_price');
        $totalTransactions = $transactions->count();
        $averageOrder = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Group by item type
        $revenueByService = $transactions->groupBy('item_type')->map(function ($items) {
            return [
                'count' => $items->count(),
                'revenue' => $items->sum('total_price')
            ];
        });

        return compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'averageOrder',
            'revenueByService',
            'period',
            'start',
            'end',
            'allTime'
        );
    }
}
