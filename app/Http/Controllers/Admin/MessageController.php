<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
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
        $admin = Auth::guard('admin')->user();
        $conversations = $this->messagingService->getAdminConversations($admin->id);

        return view('admin.messages.index', compact('conversations'));
    }

    /**
     * Show conversation with specific user
     */
    public function show($userId)
    {
        $admin = Auth::guard('admin')->user();
        $user = User::findOrFail($userId);
        
        $conversation = $this->messagingService->getOrCreateConversation($userId, $admin->id);
        $messages = $this->messagingService->getMessages($conversation->id);
        
        // Mark messages as read
        $this->messagingService->markAsRead($conversation->id, 'admin');

        return view('admin.messages.show', compact('conversation', 'messages', 'user'));
    }

    /**
     * Send a message
     */
    public function send(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $admin = Auth::guard('admin')->user();
        $conversation = $this->messagingService->getOrCreateConversation($userId, $admin->id);
        
        $message = $this->messagingService->sendMessage(
            $conversation->id,
            'admin',
            $admin->id,
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
    public function getMessages($userId)
    {
        $admin = Auth::guard('admin')->user();
        $conversation = Conversation::where('user_id', $userId)
            ->where('admin_id', $admin->id)
            ->first();

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = $this->messagingService->getMessages($conversation->id);
        $this->messagingService->markAsRead($conversation->id, 'admin');

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
        $admin = Auth::guard('admin')->user();
        $count = $this->messagingService->getUnreadCountForAdmin($admin->id);

        return response()->json(['count' => $count]);
    }
}
