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
                        <div id="connection-status" class="flex items-center gap-2 mt-0.5">
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            <p class="text-sm font-semibold text-gray-500">Connecting...</p>
                        </div>
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
                                    @if($message->has_attachment)
                                        @if($message->attachment_type === 'image')
                                            <img src="{{ $message->attachment_url }}" alt="Image" class="max-w-full rounded-lg mb-2 cursor-pointer hover:opacity-90" onclick="window.open('{{ $message->attachment_url }}', '_blank')">
                                        @else
                                            <a href="{{ $message->attachment_url }}" target="_blank" class="flex items-center gap-2 p-2 bg-gray-100 rounded-lg mb-2 hover:bg-gray-200">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-sm truncate text-gray-700">{{ $message->attachment_name }}</span>
                                            </a>
                                        @endif
                                    @endif
                                    @if($message->message)
                                        <p class="text-sm text-gray-800 leading-relaxed">{{ $message->message }}</p>
                                    @endif
                                </div>
                                <p class="text-xs font-semibold text-gray-400 mt-2 ml-3">{{ $message->created_at->format('g:i A') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Admin Message -->
                        <div class="flex flex-col items-end max-w-[75%]">
                            <div class="bg-gradient-to-br from-wash to-wash-dark rounded-2xl rounded-tr-md px-5 py-4 shadow-lg">
                                @if($message->has_attachment)
                                    @if($message->attachment_type === 'image')
                                        <img src="{{ $message->attachment_url }}" alt="Image" class="max-w-full rounded-lg mb-2 cursor-pointer hover:opacity-90" onclick="window.open('{{ $message->attachment_url }}', '_blank')">
                                    @else
                                        <a href="{{ $message->attachment_url }}" target="_blank" class="flex items-center gap-2 p-2 bg-white/20 rounded-lg mb-2 hover:bg-white/30">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span class="text-sm truncate text-white">{{ $message->attachment_name }}</span>
                                        </a>
                                    @endif
                                @endif
                                @if($message->message)
                                    <p class="text-sm text-white leading-relaxed font-medium">{{ $message->message }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-2 mr-3">
                                <p class="text-xs font-semibold text-gray-400">{{ $message->created_at->format('g:i A') }}</p>
                                @if($message->is_read)
                                    <div class="flex items-center gap-0.5 read-status" data-read="true" title="Seen">
                                        <svg class="w-3.5 h-3.5 text-wash" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3.5 h-3.5 text-wash -ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="read-status" data-read="false" title="Sent">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
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
            
            <!-- Typing Indicator (inside messages, at bottom) -->
            <div id="typing-indicator" class="hidden"></div>
        </div>

        <!-- Attachment Preview -->
        <div id="attachment-preview" class="hidden px-6 py-3 bg-gray-50 border-t border-gray-200"></div>

        <!-- Message Input -->
        <div class="bg-white border-t-2 border-gray-200 px-6 py-4 shadow-lg">
            <form id="message-form" class="flex items-end gap-3" enctype="multipart/form-data">
                <!-- Attachment Button -->
                <label class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center cursor-pointer transition-colors flex-shrink-0">
                    <input type="file" id="attachment-input" accept="image/*,.pdf,.doc,.docx" class="hidden">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                </label>
                
                <div class="flex-1">
                    <input type="text" 
                           id="message-input" 
                           placeholder="Type your message..." 
                           class="w-full px-5 py-3 bg-gray-100 border-2 border-transparent rounded-xl focus:border-wash focus:bg-white focus:ring-4 focus:ring-wash/20 transition-all text-gray-900 placeholder:text-gray-400 font-medium"
                           maxlength="1000"
                           autocomplete="off">
                </div>
                
                <button type="submit" class="w-12 h-12 bg-gradient-to-br from-wash to-wash-dark hover:shadow-xl text-white rounded-xl transition-all flex items-center justify-center flex-shrink-0 hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        const typingUrl = '{{ route('admin.messages.typing', $user->id) }}';
        const csrfToken = '{{ csrf_token() }}';
        const currentUserType = 'admin';
    </script>
    @vite(['resources/js/pages/chat.js'])
    @endpush
</x-layout>
