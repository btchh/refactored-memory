<x-layout>
    <x-slot:title>Chat with {{ $branchAddress }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <x-modules.card class="p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('user.messages.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900">{{ $branchAddress }}</h2>
                        <p class="text-sm text-gray-500">Branch Chat</p>
                    </div>
                </div>

                <!-- Branch Dropdown -->
                @if($allBranches->count() > 1)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <span>Switch Branch</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border z-50 max-h-64 overflow-y-auto">
                            @foreach($allBranches as $branch)
                                <a href="{{ route('user.messages.show', ['branchAddress' => urlencode($branch)]) }}"
                                   class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $branch === $branchAddress ? 'bg-primary-50 text-primary-700' : 'text-gray-700' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 {{ $branch === $branchAddress ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $branch }}</span>
                                        @if($branch === $branchAddress)
                                            <svg class="w-4 h-4 ml-auto text-primary-600" fill="currentColor" viewBox="0 0 20 20">
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
        </x-modules.card>

        <!-- Messages Container -->
        <x-modules.card :padding="false">
            <div id="messages-container" class="h-[500px] overflow-y-auto p-4 space-y-4">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] {{ $message->sender_type === 'user' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2">
                            @if($message->sender_type === 'admin')
                                <p class="text-xs font-medium {{ $message->sender_type === 'user' ? 'text-primary-200' : 'text-gray-600' }} mb-1">
                                    {{ $message->sender_name }}
                                </p>
                            @endif
                            <p class="text-sm">{{ $message->message }}</p>
                            <p class="text-xs {{ $message->sender_type === 'user' ? 'text-primary-200' : 'text-gray-500' }} mt-1">
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
                    <x-modules.button type="submit" variant="primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </x-modules.button>
                </form>
            </div>
        </x-modules.card>
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
