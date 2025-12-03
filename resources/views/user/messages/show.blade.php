<x-layout>
    <x-slot:title>Chat with {{ $branchAddress }}</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('user.messages.index') }}" class="bg-white/20 backdrop-blur rounded-xl p-3 hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="bg-white/20 backdrop-blur rounded-xl p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold mb-1">{{ Str::limit($branchAddress, 40) }}</h1>
                        <p class="text-white/80 text-sm">Branch Chat</p>
                    </div>
                </div>

                <!-- Branch Dropdown -->
                @if($allBranches->count() > 1)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center px-4 py-2 text-sm bg-white/20 hover:bg-white/30 rounded-xl transition-colors">
                            <span>Switch</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border z-50 max-h-64 overflow-y-auto">
                            @foreach($allBranches as $branch)
                                <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($branch)]) }}"
                                   class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $branch === $branchAddress ? 'bg-green-50 text-green-700' : 'text-gray-700' }} first:rounded-t-xl last:rounded-b-xl">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-3 {{ $branch === $branchAddress ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span class="truncate text-sm">{{ $branch }}</span>
                                        @if($branch === $branchAddress)
                                            <svg class="w-4 h-4 ml-auto text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Messages -->
            <div id="messages-container" class="h-[450px] overflow-y-auto p-6 space-y-4 bg-gray-50">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'user' ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                        <div class="max-w-[75%] {{ $message->sender_type === 'user' ? 'bg-green-600 text-white rounded-2xl rounded-br-md' : 'bg-white text-gray-900 rounded-2xl rounded-bl-md shadow-sm border border-gray-100' }} px-4 py-3">
                            @if($message->sender_type === 'admin')
                                <p class="text-xs font-semibold text-green-600 mb-1">{{ $message->sender_name }}</p>
                            @endif
                            <p class="text-sm leading-relaxed">{{ $message->message }}</p>
                            <p class="text-xs {{ $message->sender_type === 'user' ? 'text-green-200' : 'text-gray-400' }} mt-2 text-right">
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
                           class="flex-1 px-4 py-3 bg-gray-100 border-0 rounded-xl focus:ring-2 focus:ring-green-500 focus:bg-white transition-all"
                           maxlength="1000"
                           autocomplete="off">
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors flex items-center gap-2 font-medium">
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
        const branchAddress = '{{ $branchAddress }}';
        const conversationId = {{ $conversation->id }};
        const sendUrl = '{{ route('user.messages.send', ['branchAddress' => urlencode($branchAddress)]) }}';
        const fetchUrl = '{{ route('user.api.messages', ['branchAddress' => urlencode($branchAddress)]) }}';
        const csrfToken = '{{ csrf_token() }}';
        const currentUserType = 'user';
    </script>
    @vite(['resources/js/pages/chat.js'])
    @endpush
</x-layout>
