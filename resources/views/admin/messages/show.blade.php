<x-layout>
    <x-slot:title>Chat with {{ $user->fname }} {{ $user->lname }}</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.messages.index') }}" class="bg-white/20 backdrop-blur rounded-xl p-3 hover:bg-white/30 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center font-bold text-xl">
                    {{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold mb-1">{{ $user->fname }} {{ $user->lname }}</h1>
                    <p class="text-white/80 text-sm">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Messages -->
            <div id="messages-container" class="h-[450px] overflow-y-auto p-6 space-y-4 bg-gray-50">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                        <div class="max-w-[75%] {{ $message->sender_type === 'admin' ? 'bg-blue-600 text-white rounded-2xl rounded-br-md' : 'bg-white text-gray-900 rounded-2xl rounded-bl-md shadow-sm border border-gray-100' }} px-4 py-3">
                            @if($message->sender_type === 'user')
                                <p class="text-xs font-semibold text-blue-600 mb-1">{{ $user->fname }} {{ $user->lname }}</p>
                            @endif
                            <p class="text-sm leading-relaxed">{{ $message->message }}</p>
                            <p class="text-xs {{ $message->sender_type === 'admin' ? 'text-blue-200' : 'text-gray-400' }} mt-2 text-right">
                                {{ $message->created_at->format('g:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-lg font-medium">No messages yet</p>
                        <p class="text-sm">Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="message-form" class="flex gap-3">
                    <input type="text" 
                           id="message-input" 
                           placeholder="Type your message..." 
                           class="flex-1 px-4 py-3 bg-gray-100 border-0 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                           maxlength="1000"
                           autocomplete="off">
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors flex items-center gap-2 font-medium">
                        <span class="hidden sm:inline">Send</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const userId = {{ $user->id }};
        const conversationId = {{ $conversation->id }};
        const sendUrl = '{{ route('admin.messages.send', $user->id) }}';
        const fetchUrl = '{{ route('admin.api.messages', $user->id) }}';
        const csrfToken = '{{ csrf_token() }}';
        const currentUserType = 'admin';
    </script>
    @vite(['resources/js/pages/chat.js'])
    @endpush
</x-layout>
