/**
 * Chat Page JavaScript
 * Handles real-time messaging with Pusher and polling fallback
 */

document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    
    let lastMessageId = 0;
    let pollingInterval = null;
    let echoConnected = false;

    // Scroll to bottom of messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Add message to UI
    function addMessage(message, isOwn) {
        // Check if message already exists
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} animate-slide-in`;
        messageDiv.setAttribute('data-message-id', message.id);
        
        if (isOwn) {
            // Own message - gradient wash bubble
            messageDiv.innerHTML = `
                <div class="flex flex-col items-end max-w-[75%]">
                    <div class="bg-gradient-to-br from-wash to-wash-dark rounded-2xl rounded-tr-md px-5 py-4 shadow-lg">
                        <p class="text-sm text-white leading-relaxed font-medium">${escapeHtml(message.message)}</p>
                    </div>
                    <p class="text-xs font-semibold text-gray-400 mt-2 mr-3">${message.formatted_time}</p>
                </div>
            `;
        } else {
            // Other party's message - white bubble with avatar
            const avatarContent = message.sender_type === 'admin' 
                ? `<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                   </svg>`
                : `<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                   </svg>`;
            
            messageDiv.innerHTML = `
                <div class="flex gap-3 max-w-[75%]">
                    <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center flex-shrink-0">
                        ${avatarContent}
                    </div>
                    <div class="flex-1">
                        <div class="bg-white border-2 border-gray-200 rounded-2xl rounded-tl-md px-5 py-4 shadow-sm">
                            <p class="text-xs font-black text-gray-900 mb-2 uppercase">${escapeHtml(message.sender_name || 'User')}</p>
                            <p class="text-sm text-gray-800 leading-relaxed">${escapeHtml(message.message)}</p>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 mt-2 ml-3">${message.formatted_time}</p>
                    </div>
                </div>
            `;
        }
        
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
        
        if (message.id > lastMessageId) {
            lastMessageId = message.id;
        }
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Send message
    async function sendMessage(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        messageInput.value = '';
        messageInput.disabled = true;

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            if (data.success) {
                addMessage(data.message, true);
            } else {
                window.Toast?.error('Failed to send message');
                messageInput.value = message;
            }
        } catch (error) {
            console.error('Error sending message:', error);
            window.Toast?.error('Failed to send message. Please try again.');
            messageInput.value = message;
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    }

    // Fetch new messages (polling fallback)
    async function fetchMessages() {
        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        const isOwn = msg.sender_type === currentUserType;
                        addMessage(msg, isOwn);
                    }
                });
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    // Initialize Echo/Pusher if available
    function initRealtime() {
        if (typeof window.Echo !== 'undefined' && typeof conversationId !== 'undefined') {
            try {
                window.Echo.private(`conversation.${conversationId}`)
                    .listen('.message.sent', (data) => {
                        // Only add if it's from the other party
                        if (data.sender_type !== currentUserType) {
                            addMessage(data, false);
                        }
                    });
                
                echoConnected = true;
                console.log('Real-time messaging connected via Pusher');
                
                // Still poll occasionally as backup, but less frequently
                pollingInterval = setInterval(fetchMessages, 10000);
            } catch (error) {
                console.warn('Failed to connect to Pusher, using polling:', error);
                pollingInterval = setInterval(fetchMessages, 3000);
            }
        } else {
            // Fallback to polling
            console.log('Using polling for messages (Echo not available)');
            pollingInterval = setInterval(fetchMessages, 3000);
        }
    }

    // Get last message ID from existing messages
    function initLastMessageId() {
        const messages = messagesContainer.querySelectorAll('[data-message-id]');
        messages.forEach(msg => {
            const id = parseInt(msg.dataset.messageId) || 0;
            if (id > lastMessageId) {
                lastMessageId = id;
            }
        });
    }

    // Initialize
    scrollToBottom();
    initLastMessageId();
    messageForm.addEventListener('submit', sendMessage);
    initRealtime();

    // Cleanup on page leave
    window.addEventListener('beforeunload', () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        if (echoConnected && typeof window.Echo !== 'undefined' && typeof conversationId !== 'undefined') {
            window.Echo.leave(`conversation.${conversationId}`);
        }
    });
});
