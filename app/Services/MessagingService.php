<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Events\MessagesRead;
use App\Events\UserTyping;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MessagingService
{
    /**
     * Get or create a conversation between user and branch
     */
    public function getOrCreateConversation(int $userId, string $branchAddress): Conversation
    {
        return Conversation::findOrCreateForBranch($userId, $branchAddress);
    }

    /**
     * Send a message
     */
    public function sendMessage(
        int $conversationId,
        string $senderType,
        int $senderId,
        string $messageText,
        ?UploadedFile $attachment = null
    ): Message {
        $attachmentData = [];
        
        if ($attachment) {
            $attachmentData = $this->handleAttachment($attachment, $conversationId);
        }
        
        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $messageText,
            ...$attachmentData,
        ]);

        // Update conversation's last message timestamp
        Conversation::where('id', $conversationId)->update([
            'last_message_at' => now(),
        ]);

        // Broadcast the message for real-time updates
        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Handle file attachment upload
     */
    protected function handleAttachment(UploadedFile $file, int $conversationId): array
    {
        $extension = $file->getClientOriginalExtension();
        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        
        $path = $file->store("chat-attachments/{$conversationId}", 'public');
        
        return [
            'attachment_path' => $path,
            'attachment_type' => $isImage ? 'image' : 'file',
            'attachment_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(int $conversationId, int $limit = 50): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Mark messages as read and broadcast
     */
    public function markAsRead(int $conversationId, string $readerType): void
    {
        $senderType = $readerType === 'user' ? 'admin' : 'user';
        
        // Get IDs of messages being marked as read
        $messageIds = Message::where('conversation_id', $conversationId)
            ->where('sender_type', $senderType)
            ->where('is_read', false)
            ->pluck('id')
            ->toArray();
        
        if (empty($messageIds)) {
            return;
        }
        
        Message::whereIn('id', $messageIds)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        // Broadcast read receipt
        broadcast(new MessagesRead($conversationId, $readerType, $messageIds))->toOthers();
    }

    /**
     * Broadcast typing indicator
     */
    public function broadcastTyping(int $conversationId, string $senderType, string $senderName, bool $isTyping = true): void
    {
        broadcast(new UserTyping($conversationId, $senderType, $senderName, $isTyping))->toOthers();
    }

    /**
     * Get conversations for a user
     */
    public function getUserConversations(int $userId): Collection
    {
        return Conversation::where('user_id', $userId)
            ->with(['latestMessage'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_type', 'admin')->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    /**
     * Get conversations for an admin (by branch)
     */
    public function getAdminConversations(int $adminId): Collection
    {
        $admin = Admin::find($adminId);
        if (!$admin) {
            return collect();
        }

        return Conversation::where('branch_address', $admin->branch_address)
            ->with(['user', 'latestMessage'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_type', 'user')->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    /**
     * Get total unread count for user
     */
    public function getUnreadCountForUser(int $userId): int
    {
        return Message::whereHas('conversation', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get total unread count for admin (by branch)
     */
    public function getUnreadCountForAdmin(int $adminId): int
    {
        $admin = Admin::find($adminId);
        if (!$admin) {
            return 0;
        }

        return Message::whereHas('conversation', function ($query) use ($admin) {
            $query->where('branch_address', $admin->branch_address);
        })
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get all unique branches that a user has booked with
     */
    public function getUserBookedBranches(int $userId): Collection
    {
        return Admin::whereIn('id', function ($query) use ($userId) {
            $query->select('admin_id')
                ->from('transactions')
                ->where('user_id', $userId);
        })
            ->whereNotNull('branch_address')
            ->where('branch_address', '!=', '')
            ->pluck('branch_address')
            ->unique()
            ->values();
    }

    /**
     * Get all branches for dropdown
     */
    public function getAllBranches(): Collection
    {
        return Admin::select('branch_address')
            ->distinct()
            ->whereNotNull('branch_address')
            ->pluck('branch_address');
    }
}
