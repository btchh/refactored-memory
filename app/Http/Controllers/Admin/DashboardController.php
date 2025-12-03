<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function showDashboard()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        // Customer stats - users who booked at this branch
        $totalCustomers = User::whereHas('transactions', function ($q) use ($branchAdminIds) {
            $q->whereIn('admin_id', $branchAdminIds);
        })->count();
        
        // Booking stats for this branch
        $pendingBookings = Transaction::whereIn('admin_id', $branchAdminIds)
            ->where('status', 'pending')
            ->count();
            
        $inProgressBookings = Transaction::whereIn('admin_id', $branchAdminIds)
            ->where('status', 'in_progress')
            ->count();
            
        $completedBookings = Transaction::whereIn('admin_id', $branchAdminIds)
            ->where('status', 'completed')
            ->count();
            
        $todayBookings = Transaction::whereIn('admin_id', $branchAdminIds)
            ->whereDate('booking_date', now()->toDateString())
            ->count();
        
        // Revenue stats for this branch
        $todayRevenue = Transaction::whereIn('admin_id', $branchAdminIds)
            ->where('status', 'completed')
            ->whereDate('updated_at', now()->toDateString())
            ->sum('total_price');
            
        $weekRevenue = Transaction::whereIn('admin_id', $branchAdminIds)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_price');
            
        $monthRevenue = Transaction::whereIn('admin_id', $branchAdminIds)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_price');
        
        // Recent bookings for this branch (last 5)
        $recentBookings = Transaction::whereIn('admin_id', $branchAdminIds)
            ->with(['user', 'services'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Today's schedule for this branch
        $todaySchedule = Transaction::whereIn('admin_id', $branchAdminIds)
            ->whereDate('booking_date', now()->toDateString())
            ->with(['user', 'services'])
            ->orderBy('booking_time')
            ->get();
        
        return view('admin.dashboard.index', compact(
            'totalCustomers',
            'pendingBookings',
            'inProgressBookings',
            'completedBookings',
            'todayBookings',
            'todayRevenue',
            'weekRevenue',
            'monthRevenue',
            'recentBookings',
            'todaySchedule'
        ));
    }
}
