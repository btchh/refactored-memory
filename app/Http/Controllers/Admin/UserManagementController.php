<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        $search = $request->get('search');
        $status = $request->get('status');
        
        // Only show users who have booked with this branch
        $query = User::query()
            ->whereHas('transactions', function ($q) use ($branchAdminIds) {
                $q->whereIn('admin_id', $branchAdminIds);
            })
            ->withCount(['transactions' => function ($q) use ($branchAdminIds) {
                $q->whereIn('admin_id', $branchAdminIds);
            }]);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($status) {
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
        ];
        
        return view('admin.users.index', compact('users', 'stats', 'search', 'status'));
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
            
            // Check if user has bookings
            $bookingCount = $user->transactions()->count();
            
            if ($bookingCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete user with {$bookingCount} booking(s). Disable the account instead."
                ], 400);
            }
            
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
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
