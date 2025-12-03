<x-layout>
    <x-slot:title>Messages</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="Messages"
            subtitle="Chat with laundry branches you've booked with"
            icon="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            gradient="emerald"
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

        @if($conversations->isEmpty() && $availableBranches->isEmpty())
            <x-modules.card class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                <p class="text-gray-500">Book a laundry service to start messaging with branches</p>
                <a href="{{ route('user.booking') }}" class="btn btn-primary mt-4">Book Now</a>
            </x-modules.card>
        @else
            <!-- Start New Conversation -->
            @if($availableBranches->isNotEmpty())
                <x-modules.card class="p-4 mb-6">
                    <h3 class="font-medium text-gray-900 mb-3">Start a new conversation</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableBranches as $branch)
                            <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($branch)]) }}" 
                               class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $branch }}
                            </a>
                        @endforeach
                    </div>
                </x-modules.card>
            @endif

            <!-- Conversations List -->
            <x-modules.card :padding="false" class="divide-y">
                @forelse($conversations as $conversation)
                    <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($conversation->branch_address)]) }}" 
                       class="flex items-center p-4 hover:bg-gray-50 transition-colors">
                        <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium text-gray-900 truncate">
                                    {{ $conversation->branch_address }}
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
            </x-modules.card>
        @endif
    </div>
</x-layout>
