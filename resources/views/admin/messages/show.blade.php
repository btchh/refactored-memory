<x-layout>
    <x-slot:title>Chat with {{ $user->fname }} {{ $user->lname }}</x-slot:title>

    <!-- Modern Chat Interface -->
    <div class="fixed inset-0 top-[72px] left-0 lg:left-64 bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-white border-b-2 border-gray-200 px-6 py-5 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.messages.index') }}" class="w-11 h-11 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-wash to-wash-dark rounded-2xl flex items-center justify-center shadow-md">
                        <span class="text-2xl font-black text-white">
                            {{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900">{{ $user->fname }} {{ $user->lname }}</h1>
                        <p class="text-sm font-semibold text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="messages-container" class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }} animate-slide-in" data-message-id="{{ $message->id }}">
                    @if($message->sender_type === 'user')
                        <!-- User Message -->
                        <div class="flex gap-3 max-w-[75%]">
                            <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-black text-gray-600">
                                    {{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="bg-white border-2 border-gray-200 rounded-2xl rounded-tl-md px-5 py-4 shadow-sm">
                                    <p class="text-xs font-black text-gray-900 mb-2 uppercase">{{ $user->fname }} {{ $user->lname }}</p>
                                    <p class="text-sm text-gray-800 leading-relaxed">{{ $message->message }}</p>
                                </div>
                                <p class="text-xs font-semibold text-gray-400 mt-2 ml-3">{{ $message->created_at->format('g:i A') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Admin Message -->
                        <div class="flex flex-col items-end max-w-[75%]">
                            <div class="bg-gradient-to-br from-wash to-wash-dark rounded-2xl rounded-tr-md px-5 py-4 shadow-lg">
                                <p class="text-sm text-white leading-relaxed font-medium">{{ $message->message }}</p>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 mt-2 mr-3">{{ $message->created_at->format('g:i A') }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-2xl font-black text-gray-900 mb-2">Start the Conversation</p>
                    <p class="text-gray-600">Send your first message below</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="bg-white border-t-2 border-gray-200 px-6 py-6 shadow-lg">
            <form id="message-form" class="flex gap-4">
                <div class="flex-1 relative">
                    <input type="text" 
                           id="message-input" 
                           placeholder="Type your message..." 
                           class="w-full px-6 py-4 bg-gray-100 border-2 border-transparent rounded-2xl focus:border-wash focus:bg-white focus:ring-4 focus:ring-wash/20 transition-all text-gray-900 placeholder:text-gray-400 font-medium"
                           maxlength="1000"
                           autocomplete="off">
                </div>
                <button type="submit" class="w-14 h-14 bg-gradient-to-br from-wash to-wash-dark hover:shadow-xl text-white rounded-2xl transition-all flex items-center justify-center flex-shrink-0 hover:scale-105">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
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
