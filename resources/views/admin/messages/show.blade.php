<x-layout>
    <x-slot:title>Chat with {{ $user->fname }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="card p-4 mb-4">
            <div class="flex items-center">
                <a href="{{ route('admin.messages.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 font-medium">
                    {{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">{{ $user->fname }} {{ $user->lname }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="card">
            <div id="messages-container" class="h-[500px] overflow-y-auto p-4 space-y-4">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] {{ $message->sender_type === 'admin' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2">
                            <p class="text-sm">{{ $message->message }}</p>
                            <p class="text-xs {{ $message->sender_type === 'admin' ? 'text-primary-200' : 'text-gray-500' }} mt-1">
                                {{ $message->created_at->format('g:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="border-t p-4">
                <form id="message-form" class="flex gap-2">
                    <input type="text" 
                           id="message-input" 
                           placeholder="Type a message..." 
                           class="form-input flex-1"
                           maxlength="1000"
                           autocomplete="off">
                    <button type="submit" class="btn btn-primary">
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
        const recipientId = {{ $user->id }};
        const conversationId = {{ $conversation->id }};
        const sendUrl = '{{ route('admin.messages.send', $user->id) }}';
        const fetchUrl = '{{ route('admin.api.messages', $user->id) }}';
        const csrfToken = '{{ csrf_token() }}';
        const currentUserType = 'admin';
    </script>
    @vite(['resources/js/pages/chat.js'])
    @endpush
</x-layout>
