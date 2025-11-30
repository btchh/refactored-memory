<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function showDashboard()
    {
        return view('admin.dashboard');
    }
}
