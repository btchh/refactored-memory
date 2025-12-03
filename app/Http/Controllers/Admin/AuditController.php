<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    /**
     * Display audit logs for the current admin's branch
     */
    public function index(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        
        $query = AuditLog::forAdmin($adminId)
            ->with('admin')
            ->orderBy('created_at', 'desc');

        // Filter by action type
        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->byModel($request->model);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(20);

        // Get unique actions and models for filters
        $actions = AuditLog::forAdmin($adminId)
            ->distinct()
            ->pluck('action');
        
        $models = AuditLog::forAdmin($adminId)
            ->whereNotNull('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(fn($m) => class_basename($m));

        return view('admin.audit.index', compact('logs', 'actions', 'models'));
    }

    /**
     * Get audit logs as JSON (for AJAX)
     */
    public function getLogs(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        
        $query = AuditLog::forAdmin($adminId)
            ->with('admin')
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        $logs = $query->limit(50)->get();

        return response()->json([
            'success' => true,
            'logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'model_type' => $log->model_type ? class_basename($log->model_type) : null,
                    'model_id' => $log->model_id,
                    'admin_name' => $log->admin->fname . ' ' . $log->admin->lname,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('M j, Y g:i A'),
                    'time_ago' => $log->created_at->diffForHumans(),
                ];
            }),
        ]);
    }
}
