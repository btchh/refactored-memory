<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // Show only current admin's logs by default
        $currentAdminId = auth('admin')->id();
        $viewAll = $request->get('view_all', false);
        
        $query = AuditLog::with('admin')->orderBy('created_at', 'desc');
        
        // Filter by current admin unless viewing all
        if (!$viewAll) {
            $query->where('admin_id', $currentAdminId);
        }
        
        if ($request->has('action') && $request->action) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        
        if ($request->has('admin_id') && $request->admin_id) {
            $query->where('admin_id', $request->admin_id);
        }
        
        $logs = $query->paginate(50)->appends($request->query());
        $admins = \App\Models\Admin::all();
        
        return view('admin.audit.index', compact('logs', 'admins', 'viewAll'));
    }
}
