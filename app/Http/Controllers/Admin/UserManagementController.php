<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        $search = $request->get('search');
        $status = $request->get('status');
        $showDeleted = $request->get('deleted') === 'true';
        
        // Only show users who have booked with this branch
        $query = User::query()
            ->whereHas('transactions', function ($q) use ($branchAdminIds) {
                $q->whereIn('admin_id', $branchAdminIds);
            })
            ->withCount(['transactions' => function ($q) use ($branchAdminIds) {
                $q->whereIn('admin_id', $branchAdminIds);
            }]);
        
        // Show deleted or active users
        if ($showDeleted) {
            $query->onlyTrashed();
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($status && !$showDeleted) {
            $query->where('status', $status);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Stats for users who have booked with this branch
        $userIds = User::whereHas('transactions', function ($q) use ($branchAdminIds) {
            $q->whereIn('admin_id', $branchAdminIds);
        })->pluck('id');
        
        $stats = [
            'total' => $userIds->count(),
            'active' => User::whereIn('id', $userIds)->where('status', 'active')->count(),
            'disabled' => User::whereIn('id', $userIds)->where('status', 'disabled')->count(),
            'archived' => User::whereIn('id', $userIds)->onlyTrashed()->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats', 'search', 'status', 'showDeleted'));
    }

    public function show($id)
    {
        $admin = auth()->guard('admin')->user();
        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        // Only show user if they have booked with this branch
        $user = User::whereHas('transactions', function ($q) use ($branchAdminIds) {
            $q->whereIn('admin_id', $branchAdminIds);
        })->with(['transactions' => function($query) use ($branchAdminIds) {
            $query->whereIn('admin_id', $branchAdminIds)
                  ->orderBy('created_at', 'desc')
                  ->limit(10);
        }])->findOrFail($id);
        
        // Stats only for this branch's transactions
        $bookingStats = [
            'total' => $user->transactions()->whereIn('admin_id', $branchAdminIds)->count(),
            'completed' => $user->transactions()->whereIn('admin_id', $branchAdminIds)->where('status', 'completed')->count(),
            'pending' => $user->transactions()->whereIn('admin_id', $branchAdminIds)->where('status', 'pending')->count(),
            'cancelled' => $user->transactions()->whereIn('admin_id', $branchAdminIds)->where('status', 'cancelled')->count(),
            'total_spent' => $user->transactions()->whereIn('admin_id', $branchAdminIds)->where('status', '!=', 'cancelled')->sum('total_price'),
        ];
        
        return view('admin.users.show', compact('user', 'bookingStats'));
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            
            $newStatus = $user->status === 'active' ? 'disabled' : 'active';
            $user->status = $newStatus;
            $user->save();
            
            // Send SMS notification
            try {
                if ($newStatus === 'disabled') {
                    $message = "WashHour: Your account has been temporarily disabled. You will not be able to access our services until it is re-enabled. Please contact support if you have questions.";
                } else {
                    $message = "WashHour: Your account has been re-enabled! You can now access our services again. Thank you for your patience.";
                }
                $this->smsService->sendSms($user->phone, $message);
                Log::info("Status change notification SMS sent to user {$user->id} ({$user->phone}) - New status: {$newStatus}");
            } catch (\Exception $smsError) {
                Log::error("Failed to send status change SMS to user {$user->id}: " . $smsError->getMessage());
                // Don't fail the status change if SMS fails
            }
            
            return response()->json([
                'success' => true,
                'message' => "User {$newStatus} successfully",
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Archive the user (soft delete)
            $user->delete();
            
            // Send SMS notification
            try {
                $message = "WashHour: Your account has been suspended. You will no longer be able to access our services. If you believe this is a mistake, please contact our support team.";
                $this->smsService->sendSms($user->phone, $message);
                Log::info("Archive notification SMS sent to user {$user->id} ({$user->phone})");
            } catch (\Exception $smsError) {
                Log::error("Failed to send archive SMS to user {$user->id}: " . $smsError->getMessage());
                // Don't fail the archive operation if SMS fails
            }
            
            return response()->json([
                'success' => true,
                'message' => 'User archived successfully. You can restore them from the archived users list.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive user'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            
            // Send SMS notification
            try {
                $message = "WashHour: Good news! Your account suspension has been lifted. You can now access our services again. Welcome back!";
                $this->smsService->sendSms($user->phone, $message);
                Log::info("Restore notification SMS sent to user {$user->id} ({$user->phone})");
            } catch (\Exception $smsError) {
                Log::error("Failed to send restore SMS to user {$user->id}: " . $smsError->getMessage());
                // Don't fail the restore operation if SMS fails
            }
            
            return response()->json([
                'success' => true,
                'message' => 'User unarchived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unarchive user'
            ], 500);
        }
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);
        
        try {
            $user = User::findOrFail($id);
            $user->password = bcrypt($request->password);
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password'
            ], 500);
        }
    }
}
