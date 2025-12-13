<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Conversation;
use App\Services\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(
        private MessagingService $messagingService
    ) {}

    /**
     * Show messages page
     */
    public function index()
    {
        $user = Auth::guard('web')->user();
        $conversations = $this->messagingService->getUserConversations($user->id);
        
        // Get branches the user has booked with (for starting new conversations)
        $bookedBranches = $this->messagingService->getUserBookedBranches($user->id);
        $existingBranches = $conversations->pluck('branch_address');
        $availableBranches = $bookedBranches->diff($existingBranches);

        return view('user.messages.index', compact('conversations', 'availableBranches'));
    }

    /**
     * Show conversation with specific branch
     */
    public function show($branchAddress)
    {
        // Validate branch address doesn't contain path segments (security check)
        if (str_contains($branchAddress, '/')) {
            abort(404, 'Invalid branch address');
        }
        
        $user = Auth::guard('web')->user();
        
        // Get branches user has booked with
        $bookedBranches = $this->messagingService->getUserBookedBranches($user->id);
        
        // Check if user has booked with this branch
        if (!$bookedBranches->contains($branchAddress)) {
            abort(404, 'Branch not found or you have no bookings with this branch');
        }
        
        // Get branch info (first admin of this branch)
        $branchAdmin = Admin::where('branch_address', $branchAddress)->first();
        if (!$branchAdmin) {
            abort(404, 'Branch not found');
        }
        
        $conversation = $this->messagingService->getOrCreateConversation($user->id, $branchAddress);
        $messages = $this->messagingService->getMessages($conversation->id);
        
        // Mark messages as read
        $this->messagingService->markAsRead($conversation->id, 'user');

        // Get all branches for dropdown
        $allBranches = $bookedBranches;

        return view('user.messages.show', compact('conversation', 'messages', 'branchAddress', 'branchAdmin', 'allBranches'));
    }

    /**
     * Send a message
     */
    public function send(Request $request, $branchAddress)
    {
        // Validate branch address
        if (str_contains($branchAddress, '/')) {
            return response()->json(['success' => false, 'message' => 'Invalid branch'], 400);
        }
        
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        // Require either message or attachment
        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'Message or attachment required'], 422);
        }

        $user = Auth::guard('web')->user();
        
        // Verify branch exists
        if (!Admin::where('branch_address', $branchAddress)->exists()) {
            return response()->json(['success' => false, 'message' => 'Branch not found'], 404);
        }
        
        $conversation = $this->messagingService->getOrCreateConversation($user->id, $branchAddress);
        
        $message = $this->messagingService->sendMessage(
            $conversation->id,
            'user',
            $user->id,
            $request->message ?? '',
            $request->file('attachment')
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender_name,
                'has_attachment' => $message->has_attachment,
                'attachment_url' => $message->attachment_url,
                'attachment_type' => $message->attachment_type,
                'attachment_name' => $message->attachment_name,
                'is_read' => false,
                'created_at' => $message->created_at->toISOString(),
                'formatted_time' => $message->created_at->format('g:i A'),
            ],
        ]);
    }

    /**
     * Broadcast typing indicator
     */
    public function typing(Request $request, $branchAddress)
    {
        if (str_contains($branchAddress, '/')) {
            return response()->json(['success' => false], 400);
        }
        
        $user = Auth::guard('web')->user();
        $conversation = Conversation::where('user_id', $user->id)
            ->where('branch_address', $branchAddress)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false], 404);
        }

        $this->messagingService->broadcastTyping(
            $conversation->id,
            'user',
            $user->fname . ' ' . $user->lname,
            $request->boolean('is_typing', true)
        );

        return response()->json(['success' => true]);
    }

    /**
     * Get messages (AJAX polling fallback)
     */
    public function getMessages($branchAddress)
    {
        // Validate branch address
        if (str_contains($branchAddress, '/')) {
            return response()->json(['messages' => []]);
        }
        
        $user = Auth::guard('web')->user();
        $conversation = Conversation::where('user_id', $user->id)
            ->where('branch_address', $branchAddress)
            ->first();

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = $this->messagingService->getMessages($conversation->id);
        $this->messagingService->markAsRead($conversation->id, 'user');

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'has_attachment' => $m->has_attachment,
                'attachment_url' => $m->attachment_url,
                'attachment_type' => $m->attachment_type,
                'attachment_name' => $m->attachment_name,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at->toISOString(),
                'formatted_time' => $m->created_at->format('g:i A'),
            ]),
        ]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $user = Auth::guard('web')->user();
        $count = $this->messagingService->getUnreadCountForUser($user->id);

        return response()->json(['count' => $count]);
    }
}
