<x-layout>
    <x-slot:title>Messages</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
            <p class="text-gray-600">Chat with laundry shops you've booked with</p>
        </div>

        @if($conversations->isEmpty() && $availableAdmins->isEmpty())
            <div class="card p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                <p class="text-gray-500">Book a laundry service to start messaging with shops</p>
                <a href="{{ route('user.booking') }}" class="btn btn-primary mt-4">Book Now</a>
            </div>
        @else
            <!-- Start New Conversation -->
            @if($availableAdmins->isNotEmpty())
                <div class="card p-4 mb-6">
                    <h3 class="font-medium text-gray-900 mb-3">Start a new conversation</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableAdmins as $admin)
                            <a href="{{ route('user.messages.show', $admin->id) }}" 
                               class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                <span class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-2 text-xs font-medium">
                                    {{ strtoupper(substr($admin->fname, 0, 1)) }}{{ strtoupper(substr($admin->lname, 0, 1)) }}
                                </span>
                                {{ $admin->fname }} {{ $admin->lname }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Conversations List -->
            <div class="card divide-y">
                @forelse($conversations as $conversation)
                    <a href="{{ route('user.messages.show', $conversation->admin_id) }}" 
                       class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-4 font-medium">
                            {{ strtoupper(substr($conversation->admin->fname, 0, 1)) }}{{ strtoupper(substr($conversation->admin->lname, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium text-gray-900 truncate">
                                    {{ $conversation->admin->fname }} {{ $conversation->admin->lname }}
                                </h3>
                                @if($conversation->latestMessage)
                                    <span class="text-xs text-gray-500">
                                        {{ $conversation->latestMessage->created_at->diffForHumans(null, true) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 truncate">
                                @if($conversation->latestMessage)
                                    {{ $conversation->latestMessage->message }}
                                @else
                                    No messages yet
                                @endif
                            </p>
                        </div>
                        @if($conversation->unread_count > 0)
                            <span class="ml-2 bg-primary-600 text-white text-xs font-medium px-2 py-1 rounded-full">
                                {{ $conversation->unread_count }}
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        No conversations yet
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</x-layout>
