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
        $search = $request->get('search');
        $status = $request->get('status');
        
        $query = User::query()->withCount('transactions');
        
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
        
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'disabled' => User::where('status', 'disabled')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats', 'search', 'status'));
    }

    public function show($id)
    {
        $user = User::with(['transactions' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);
        
        $bookingStats = [
            'total' => $user->transactions()->count(),
            'completed' => $user->transactions()->where('status', 'completed')->count(),
            'pending' => $user->transactions()->where('status', 'pending')->count(),
            'cancelled' => $user->transactions()->where('status', 'cancelled')->count(),
            'total_spent' => $user->transactions()->where('status', '!=', 'cancelled')->sum('total_price'),
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
