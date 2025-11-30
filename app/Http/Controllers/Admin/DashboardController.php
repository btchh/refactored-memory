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
        
        // Customer stats
        $totalCustomers = User::whereHas('transactions', function ($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })->count();
        
        // Booking stats
        $pendingBookings = Transaction::where('admin_id', $admin->id)
            ->where('status', 'pending')
            ->count();
            
        $inProgressBookings = Transaction::where('admin_id', $admin->id)
            ->where('status', 'in_progress')
            ->count();
            
        $completedBookings = Transaction::where('admin_id', $admin->id)
            ->where('status', 'completed')
            ->count();
            
        $todayBookings = Transaction::where('admin_id', $admin->id)
            ->whereDate('booking_date', now()->toDateString())
            ->count();
        
        // Revenue stats
        $todayRevenue = Transaction::where('admin_id', $admin->id)
            ->where('status', 'completed')
            ->whereDate('updated_at', now()->toDateString())
            ->sum('total_price');
            
        $weekRevenue = Transaction::where('admin_id', $admin->id)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_price');
            
        $monthRevenue = Transaction::where('admin_id', $admin->id)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_price');
        
        // Recent bookings (last 5)
        $recentBookings = Transaction::where('admin_id', $admin->id)
            ->with(['user', 'services'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Today's schedule
        $todaySchedule = Transaction::where('admin_id', $admin->id)
            ->whereDate('booking_date', now()->toDateString())
            ->with(['user', 'services'])
            ->orderBy('booking_time')
            ->get();
        
        return view('admin.dashboard', compact(
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
