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
        
        $conversation = $this->messagingService->getOrCreateConversation($userId, $admin->branch_address);
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
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        // Require either message or attachment
        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'Message or attachment required'], 422);
        }

        $admin = Auth::guard('admin')->user();
        $conversation = $this->messagingService->getOrCreateConversation($userId, $admin->branch_address);
        
        $message = $this->messagingService->sendMessage(
            $conversation->id,
            'admin',
            $admin->id,
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
    public function typing(Request $request, $userId)
    {
        $admin = Auth::guard('admin')->user();
        $conversation = Conversation::where('user_id', $userId)
            ->where('branch_address', $admin->branch_address)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false], 404);
        }

        $this->messagingService->broadcastTyping(
            $conversation->id,
            'admin',
            $admin->fname . ' ' . $admin->lname,
            $request->boolean('is_typing', true)
        );

        return response()->json(['success' => true]);
    }

    /**
     * Get messages (AJAX polling fallback)
     */
    public function getMessages($userId)
    {
        $admin = Auth::guard('admin')->user();
        $conversation = Conversation::where('user_id', $userId)
            ->where('branch_address', $admin->branch_address)
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
        $admin = Auth::guard('admin')->user();
        $count = $this->messagingService->getUnreadCountForAdmin($admin->id);

        return response()->json(['count' => $count]);
    }
}
