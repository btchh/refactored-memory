<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function showDashboard()
    {
        return view('user.dashboard');
    }
}
