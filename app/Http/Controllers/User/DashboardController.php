<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function showDashboard()
    {
        $user = Auth::user();
        
        // Get stats
        $activeOrders = Transaction::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
            
        $completedOrders = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Get next upcoming booking
        $nextBooking = Transaction::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->first();
        
        // Get recent bookings (last 5)
        $recentBookings = Transaction::where('user_id', $user->id)
            ->with(['services', 'admin'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('user.dashboard', compact(
            'activeOrders',
            'completedOrders', 
            'nextBooking',
            'recentBookings'
        ));
    }
}
