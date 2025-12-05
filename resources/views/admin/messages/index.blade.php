<x-layout>
    <x-slot:title>Messages</x-slot:title>

    <div class="max-w-6xl mx-auto">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 mb-8 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Customer Messages</h1>
                <p class="text-xl text-white/80">{{ Str::limit(Auth::guard('admin')->user()->branch_address, 50) }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
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

        @if($conversations->isEmpty())
            <!-- Empty State -->
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
                    <p class="text-lg text-gray-600">When customers message you, they'll appear here</p>
                </div>
            </div>
        @else
            <!-- Conversations List -->
            <div class="space-y-3">
                @foreach($conversations as $conversation)
                    <a href="{{ route('admin.messages.show', $conversation->user_id) }}" 
                       class="group block bg-white hover:bg-gray-50 rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div class="w-16 h-16 bg-gradient-to-br from-wash to-wash-dark rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-2xl font-black text-white">
                                        @if($conversation->user)
                                            {{ strtoupper(substr($conversation->user->fname, 0, 1)) }}{{ strtoupper(substr($conversation->user->lname, 0, 1)) }}
                                        @else
                                            AU
                                        @endif
                                    </span>
                                </div>
                                @if($conversation->unread_count > 0)
                                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-warning text-white text-xs font-black rounded-full flex items-center justify-center border-4 border-white">
                                        {{ $conversation->unread_count }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xl font-black {{ $conversation->user ? 'text-gray-900' : 'text-gray-500' }} truncate">
                                        @if($conversation->user)
                                            {{ $conversation->user->fname }} {{ $conversation->user->lname }}
                                        @else
                                            Archived User
                                        @endif
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
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
