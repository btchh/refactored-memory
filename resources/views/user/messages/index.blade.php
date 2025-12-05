<x-layout title="Messages">

    <div class="max-w-6xl mx-auto">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 mb-8 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Messages</h1>
                <p class="text-xl text-white/80">Connect with your laundry branches</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="group relative bg-white rounded-2xl p-8 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-wash/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-600 uppercase mb-1">Conversations</p>
                            <p class="text-4xl font-black text-gray-900">{{ $conversations->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="group relative bg-white rounded-2xl p-8 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-warning/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-600 uppercase mb-1">Unread</p>
                            <p class="text-4xl font-black text-gray-900">{{ $conversations->sum('unread_count') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($conversations->isEmpty() && $availableBranches->isEmpty())
            <!-- Empty State - Redesigned -->
            <div class="relative bg-white rounded-2xl border-2 border-gray-200 p-20 text-center overflow-hidden">
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-wash rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                </div>
                <div class="relative">
                    <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 mb-3">No Messages Yet</h3>
                    <p class="text-lg text-gray-600 mb-10 max-w-md mx-auto">Book a laundry service to start chatting with branches</p>
                    <a href="{{ route('user.booking') }}" class="btn btn-primary btn-lg shadow-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Book Your First Order
                    </a>
                </div>
            </div>
        @else
            <!-- New Conversation Section -->
            @if($availableBranches->isNotEmpty())
                <div class="bg-gradient-to-r from-wash/5 to-wash-dark/5 rounded-2xl p-6 mb-8 border-2 border-wash/20">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-wash rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">Start New Chat</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($availableBranches as $branch)
                            <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($branch)]) }}" 
                               class="group flex items-center gap-3 p-4 bg-white hover:bg-wash border-2 border-gray-200 hover:border-wash rounded-xl transition-all">
                                <div class="w-12 h-12 bg-gray-100 group-hover:bg-white rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                                    <svg class="w-6 h-6 text-gray-600 group-hover:text-wash transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-22 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 1
                                    {{ $conversation->branch_address }}0v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="font-bold text-gray-900 group-hover:text-white truncate transition-colors">{{ Str::limit($branch, 25) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Conversations List - Redesigned -->
            <div class="space-y-3">
                @forelse($conversations as $conversation)
                    <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($conversation->branch_address)]) }}" 
                       class="group block bg-white hover:bg-gray-50 rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-wash to-wash-dark rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                @if($conversation->unread_count > 0)
                                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-warning text-white text-xs font-black rounded-full flex items-center justify-center border-4 border-white">
                                        {{ $conversation->unread_count }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xl font-black text-gray-900 truncate">
                                        {{ $conversation->branch_address }}
                                    </h3>
                                    @if($conversation->latestMessage)
                                        <span class="text-xs font-bold text-gray-500 uppercase">
                                            {{ $conversation->latestMessage->created_at->diffForHumans(null, true) }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 truncate">
                                    @if($conversation->latestMessage)
                                        {{ $conversation->latestMessage->message }}
                                    @else
                                        No messages yet
                                    @endif
                                </p>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-wash transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-2xl border-2 border-gray-200 p-16 text-center">
                        <svg class="w-20 h-20 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-lg font-bold text-gray-900 mb-2">No conversations yet</p>
                        <p class="text-gray-600">Start a conversation with a branch above</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</x-layout>
