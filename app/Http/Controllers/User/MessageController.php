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
        
        // Get admins the user has booked with (for starting new conversations)
        $bookedAdminIds = $user->transactions()->distinct()->pluck('admin_id');
        $availableAdmins = Admin::whereIn('id', $bookedAdminIds)
            ->whereNotIn('id', $conversations->pluck('admin_id'))
            ->get();

        return view('user.messages.index', compact('conversations', 'availableAdmins'));
    }

    /**
     * Show conversation with specific admin
     */
    public function show($adminId)
    {
        $user = Auth::guard('web')->user();
        $admin = Admin::findOrFail($adminId);
        
        $conversation = $this->messagingService->getOrCreateConversation($user->id, $adminId);
        $messages = $this->messagingService->getMessages($conversation->id);
        
        // Mark messages as read
        $this->messagingService->markAsRead($conversation->id, 'user');

        return view('user.messages.show', compact('conversation', 'messages', 'admin'));
    }

    /**
     * Send a message
     */
    public function send(Request $request, $adminId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::guard('web')->user();
        $conversation = $this->messagingService->getOrCreateConversation($user->id, $adminId);
        
        $message = $this->messagingService->sendMessage(
            $conversation->id,
            'user',
            $user->id,
            $request->message
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender_name,
                'created_at' => $message->created_at->toISOString(),
                'formatted_time' => $message->created_at->format('g:i A'),
            ],
        ]);
    }

    /**
     * Get messages (AJAX polling fallback)
     */
    public function getMessages($adminId)
    {
        $user = Auth::guard('web')->user();
        $conversation = Conversation::where('user_id', $user->id)
            ->where('admin_id', $adminId)
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
