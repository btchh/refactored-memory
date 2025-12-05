<x-layout>
    <x-slot:title>Chat - {{ $branchAddress }}</x-slot:title>

    <!-- Modern Chat Interface -->
    <div class="fixed inset-0 top-[72px] left-0 lg:left-64 bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-white border-b-2 border-gray-200 px-6 py-5 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('user.messages.index') }}" class="w-11 h-11 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-wash to-wash-dark rounded-2xl flex items-center justify-center shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900">{{ Str::limit($branchAddress, 30) }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
                            <p class="text-sm font-semibold text-gray-600">Active</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($allBranches->count() > 1)
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-bold text-gray-700 flex items-center gap-2 transition-colors">
                        Switch Branch
                        <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 w-96 bg-white rounded-2xl shadow-2xl border-2 border-gray-200 z-50 max-h-96 overflow-y-auto">
                        @foreach($allBranches as $branch)
                            <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($branch)]) }}"
                               class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors {{ $branch === $branchAddress ? 'bg-wash/5' : '' }} first:rounded-t-2xl last:rounded-b-2xl border-b border-gray-100 last:border-b-0">
                                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                </div>
                                <span class="flex-1 truncate font-bold text-gray-900">{{ $branch }}</span>
                                @if($branch === $branchAddress)
                                    <div class="w-8 h-8 bg-wash rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Messages Area -->
        <div id="messages-container" class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_type === 'user' ? 'justify-end' : 'justify-start' }} animate-slide-in" data-message-id="{{ $message->id }}">
                    @if($message->sender_type === 'admin')
                        <!-- Admin Message -->
                        <div class="flex gap-3 max-w-[75%]">
                            <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="bg-white border-2 border-gray-200 rounded-2xl rounded-tl-md px-5 py-4 shadow-sm">
                                    <p class="text-xs font-black text-gray-900 mb-2 uppercase">{{ $message->sender_name }}</p>
                                    <p class="text-sm text-gray-800 leading-relaxed">{{ $message->message }}</p>
                                </div>
                                <p class="text-xs font-semibold text-gray-400 mt-2 ml-3">{{ $message->created_at->format('g:i A') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- User Message -->
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
