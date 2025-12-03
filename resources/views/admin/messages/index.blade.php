<x-layout>
    <x-slot:title>Messages</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="Messages"
            subtitle="Chat with customers at {{ Str::limit(Auth::guard('admin')->user()->branch_address, 40) }}"
            icon="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            gradient="blue"
        >
            <x-slot name="stats">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-white/70 text-xs">Conversations</p>
                    <p class="text-xl font-bold">{{ $conversations->count() }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-white/70 text-xs">Unread</p>
                    <p class="text-xl font-bold">{{ $conversations->sum('unread_count') }}</p>
                </div>
            </x-slot>
        </x-modules.page-header>

        @if($conversations->isEmpty())
            <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No messages yet</h3>
                <p class="text-gray-500">When customers message you, they'll appear here</p>
            </div>
        @else
            <!-- Conversations List -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden divide-y divide-gray-100">
                @foreach($conversations as $conversation)
                    <a href="{{ route('admin.messages.show', $conversation->user_id) }}" 
                       class="flex items-center p-5 hover:bg-gray-50 transition-colors">
                        <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-4 font-bold text-lg">
                            {{ strtoupper(substr($conversation->user->fname, 0, 1)) }}{{ strtoupper(substr($conversation->user->lname, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-semibold text-gray-900 truncate">
                                    {{ $conversation->user->fname }} {{ $conversation->user->lname }}
                                </h3>
                                @if($conversation->latestMessage)
                                    <span class="text-xs text-gray-400">
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
                            <span class="ml-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full min-w-[24px] text-center">
                                {{ $conversation->unread_count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
