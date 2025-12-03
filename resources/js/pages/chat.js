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
        
        // Determine colors based on user type (green for user chat, blue for admin chat)
        const isUserSide = currentUserType === 'user';
        const ownBgColor = isUserSide ? 'bg-green-600' : 'bg-blue-600';
        const ownTextColor = isUserSide ? 'text-green-200' : 'text-blue-200';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
        messageDiv.setAttribute('data-message-id', message.id);
        
        // Show sender name for admin messages (since multiple admins can respond from same branch)
        const showSenderName = message.sender_type === 'admin' && message.sender_name;
        const senderNameColor = isUserSide ? 'text-green-600' : 'text-blue-600';
        const senderNameHtml = showSenderName 
            ? `<p class="text-xs font-semibold ${isOwn ? ownTextColor : senderNameColor} mb-1">${escapeHtml(message.sender_name)}</p>` 
            : '';
        
        const bubbleClass = isOwn 
            ? `${ownBgColor} text-white rounded-2xl rounded-br-md` 
            : 'bg-white text-gray-900 rounded-2xl rounded-bl-md shadow-sm border border-gray-100';
        
        messageDiv.innerHTML = `
            <div class="max-w-[75%] ${bubbleClass} px-4 py-3">
                ${senderNameHtml}
                <p class="text-sm leading-relaxed">${escapeHtml(message.message)}</p>
                <p class="text-xs ${isOwn ? ownTextColor : 'text-gray-400'} mt-2 text-right">
                    ${message.formatted_time}
                </p>
            </div>
        `;
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
