/**
 * Chat Page JavaScript
 * Real-time messaging with typing indicators, read receipts, and image uploads
 */

class ChatManager {
    constructor() {
        this.messagesContainer = document.getElementById('messages-container');
        this.messageForm = document.getElementById('message-form');
        this.messageInput = document.getElementById('message-input');
        this.attachmentInput = document.getElementById('attachment-input');
        this.attachmentPreview = document.getElementById('attachment-preview');
        this.typingIndicator = document.getElementById('typing-indicator');
        this.statusIndicator = null;

        this.lastMessageId = 0;
        this.pollingInterval = null;
        this.connectionStatus = 'disconnected';
        this.channel = null;
        this.typingTimeout = null;
        this.isTyping = false;
        this.selectedFile = null;

        this.init();
    }

    init() {
        this.createStatusIndicator();
        this.initLastMessageId();
        this.scrollToBottom();
        this.bindEvents();
        this.initRealtime();
    }

    createStatusIndicator() {
        this.statusIndicator = document.getElementById('connection-status');
        if (this.statusIndicator) {
            this.updateStatusIndicator();
        }
    }

    updateStatusIndicator() {
        if (!this.statusIndicator) return;

        const dot = this.statusIndicator.querySelector('div');
        const text = this.statusIndicator.querySelector('p');

        if (!dot || !text) return;

        switch (this.connectionStatus) {
            case 'connected':
                dot.className = 'w-2 h-2 bg-success rounded-full animate-pulse';
                text.textContent = 'Connected';
                text.className = 'text-sm font-semibold text-success';
                break;
            case 'connecting':
                dot.className = 'w-2 h-2 bg-yellow-500 rounded-full animate-pulse';
                text.textContent = 'Connecting...';
                text.className = 'text-sm font-semibold text-yellow-600';
                break;
            case 'polling':
                dot.className = 'w-2 h-2 bg-blue-500 rounded-full animate-pulse';
                text.textContent = 'Active';
                text.className = 'text-sm font-semibold text-blue-600';
                break;
            default:
                dot.className = 'w-2 h-2 bg-gray-400 rounded-full';
                text.textContent = 'Offline';
                text.className = 'text-sm font-semibold text-gray-500';
        }
    }

    bindEvents() {
        this.messageForm.addEventListener('submit', (e) => this.sendMessage(e));

        // Typing indicator
        this.messageInput.addEventListener('input', () => this.handleTyping());
        this.messageInput.addEventListener('blur', () => this.stopTyping());

        // File attachment
        if (this.attachmentInput) {
            this.attachmentInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        // Cleanup on page leave
        window.addEventListener('beforeunload', () => this.cleanup());
    }

    initRealtime() {
        this.setStatus('connecting');

        if (typeof window.Echo !== 'undefined' && typeof conversationId !== 'undefined') {
            try {
                this.channel = window.Echo.private(`conversation.${conversationId}`);

                // Listen for new messages
                this.channel.listen('.message.sent', (data) => {
                    if (data.sender_type !== currentUserType) {
                        this.addMessage(data, false);
                        this.playNotificationSound();
                    }
                });

                // Listen for typing indicators
                this.channel.listen('.user.typing', (data) => {
                    if (data.sender_type !== currentUserType) {
                        this.showTypingIndicator(data.sender_name, data.is_typing);
                    }
                });

                // Listen for read receipts
                this.channel.listen('.messages.read', (data) => {
                    if (data.reader_type !== currentUserType) {
                        this.markMessagesAsRead(data.message_ids);
                    }
                });

                // Connection state handling
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    this.setStatus('connected');
                    this.startPolling(15000);
                });

                window.Echo.connector.pusher.connection.bind('disconnected', () => {
                    this.setStatus('polling');
                    this.startPolling(3000);
                });

                setTimeout(() => {
                    if (this.connectionStatus === 'connecting') {
                        this.setStatus('polling');
                        this.startPolling(3000);
                    }
                }, 5000);

            } catch (error) {
                console.warn('Failed to initialize Echo:', error);
                this.setStatus('polling');
                this.startPolling(3000);
            }
        } else {
            this.setStatus('polling');
            this.startPolling(3000);
        }
    }

    setStatus(status) {
        this.connectionStatus = status;
        this.updateStatusIndicator();
    }

    startPolling(interval) {
        if (this.pollingInterval) clearInterval(this.pollingInterval);
        this.pollingInterval = setInterval(() => this.fetchMessages(), interval);
    }

    async fetchMessages() {
        try {
            const response = await fetch(fetchUrl, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;

            const data = await response.json();
            if (data.messages?.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.id > this.lastMessageId) {
                        this.addMessage(msg, msg.sender_type === currentUserType);
                    }
                });
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    addMessage(message, isOwn) {
        if (document.querySelector(`[data-message-id="${message.id}"]`)) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} animate-slide-in`;
        messageDiv.setAttribute('data-message-id', message.id);

        const attachmentHtml = this.renderAttachment(message);
        const messageContent = message.message ? `<p class="text-sm ${isOwn ? 'text-white' : 'text-gray-800'} leading-relaxed ${isOwn ? 'font-medium' : ''}">${this.escapeHtml(message.message)}</p>` : '';
        const readStatus = isOwn ? this.getReadStatusIcon(message.is_read) : '';

        if (isOwn) {
            messageDiv.innerHTML = `
                <div class="flex flex-col items-end max-w-[75%]">
                    <div class="bg-gradient-to-br from-wash to-wash-dark rounded-2xl rounded-tr-md px-5 py-4 shadow-lg">
                        ${attachmentHtml}
                        ${messageContent}
                    </div>
                    <div class="flex items-center gap-2 mt-2 mr-3">
                        <p class="text-xs font-semibold text-gray-400">${message.formatted_time}</p>
                        ${readStatus}
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="flex gap-3 max-w-[75%]">
                    <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white border-2 border-gray-200 rounded-2xl rounded-tl-md px-5 py-4 shadow-sm">
                            <p class="text-xs font-black text-gray-900 mb-2 uppercase">${this.escapeHtml(message.sender_name || 'User')}</p>
                            ${attachmentHtml}
                            ${messageContent}
                        </div>
                        <p class="text-xs font-semibold text-gray-400 mt-2 ml-3">${message.formatted_time}</p>
                    </div>
                </div>
            `;
        }

        // Remove empty state
        const emptyState = this.messagesContainer.querySelector('.flex.flex-col.items-center.justify-center.h-full');
        if (emptyState) emptyState.remove();

        // Insert before typing indicator to keep it at the bottom
        if (this.typingIndicator && this.typingIndicator.parentNode === this.messagesContainer) {
            this.messagesContainer.insertBefore(messageDiv, this.typingIndicator);
        } else {
            this.messagesContainer.appendChild(messageDiv);
        }
        this.scrollToBottom();

        if (message.id > this.lastMessageId) this.lastMessageId = message.id;
    }

    renderAttachment(message) {
        if (!message.has_attachment) return '';

        if (message.attachment_type === 'image') {
            return `<img src="${message.attachment_url}" alt="Image" class="max-w-full rounded-lg mb-2 cursor-pointer hover:opacity-90" onclick="window.open('${message.attachment_url}', '_blank')">`;
        }
        return `<a href="${message.attachment_url}" target="_blank" class="flex items-center gap-2 p-2 bg-white/20 rounded-lg mb-2 hover:bg-white/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="text-sm truncate">${this.escapeHtml(message.attachment_name)}</span>
        </a>`;
    }

    getReadStatusIcon(isRead) {
        if (isRead) {
            // Double check - Messenger style "Seen"
            return `<div class="flex items-center gap-0.5 read-status" data-read="true">
                <svg class="w-3.5 h-3.5 text-wash" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <svg class="w-3.5 h-3.5 text-wash -ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>`;
        }
        // Single check - "Sent"
        return `<div class="read-status" data-read="false">
            <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>`;
    }

    markMessagesAsRead(messageIds) {
        messageIds.forEach(id => {
            const statusEl = document.querySelector(`[data-message-id="${id}"] .read-status`);
            if (statusEl && statusEl.dataset.read === 'false') {
                // Replace with double check
                statusEl.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-wash" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg class="w-3.5 h-3.5 text-wash -ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                `;
                statusEl.classList.add('flex', 'items-center', 'gap-0.5');
                statusEl.dataset.read = 'true';
            }
        });
    }

    async sendMessage(e) {
        e.preventDefault();

        const message = this.messageInput.value.trim();
        if (!message && !this.selectedFile) return;

        const formData = new FormData();
        if (message) formData.append('message', message);
        if (this.selectedFile) formData.append('attachment', this.selectedFile);

        // Store file reference before clearing
        const hadAttachment = !!this.selectedFile;

        this.messageInput.value = '';
        this.messageInput.disabled = true;
        this.stopTyping();

        const sendBtn = this.messageForm.querySelector('button[type="submit"]');
        const originalBtnContent = sendBtn.innerHTML;
        sendBtn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
        sendBtn.disabled = true;

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                this.clearAttachment();
                this.addMessage(data.message, true);
            } else {
                window.Toast?.error(data.message || 'Failed to send');
                // Keep attachment preview on failure so user can retry
            }
        } catch (error) {
            console.error('Error:', error);
            window.Toast?.error('Failed to send message');
            // Keep attachment preview on failure so user can retry
        } finally {
            this.messageInput.disabled = false;
            sendBtn.innerHTML = originalBtnContent;
            sendBtn.disabled = false;
            this.messageInput.focus();
        }
    }

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            window.Toast?.error('File too large (max 5MB)');
            return;
        }

        this.selectedFile = file;
        this.showAttachmentPreview(file);
    }

    showAttachmentPreview(file) {
        if (!this.attachmentPreview) return;

        const isImage = file.type.startsWith('image/');
        this.attachmentPreview.innerHTML = `
            <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-xl">
                ${isImage ? `<img src="${URL.createObjectURL(file)}" class="w-16 h-16 object-cover rounded-lg">` : 
                    `<div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center"><svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>`}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                </div>
                <button type="button" onclick="chatManager.clearAttachment()" class="p-2 hover:bg-gray-200 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `;
        this.attachmentPreview.classList.remove('hidden');
    }

    clearAttachment() {
        this.selectedFile = null;
        if (this.attachmentInput) this.attachmentInput.value = '';
        if (this.attachmentPreview) {
            this.attachmentPreview.innerHTML = '';
            this.attachmentPreview.classList.add('hidden');
        }
    }

    handleTyping() {
        if (!this.isTyping) {
            this.isTyping = true;
            this.sendTypingStatus(true);
        }
        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => this.stopTyping(), 2000);
    }

    stopTyping() {
        if (this.isTyping) {
            this.isTyping = false;
            this.sendTypingStatus(false);
        }
        clearTimeout(this.typingTimeout);
    }

    async sendTypingStatus(isTyping) {
        if (typeof typingUrl === 'undefined') return;
        try {
            await fetch(typingUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ is_typing: isTyping })
            });
        } catch (e) {}
    }

    showTypingIndicator(_name, isTyping) {
        if (!this.typingIndicator) return;
        if (isTyping) {
            // Messenger-style typing bubble with animated dots
            this.typingIndicator.innerHTML = `
                <div class="flex items-start gap-3 py-2">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="bg-gray-200 rounded-2xl rounded-tl-md px-4 py-3">
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
            `;
            this.typingIndicator.classList.remove('hidden');
            this.scrollToBottom();
        } else {
            this.typingIndicator.classList.add('hidden');
        }
    }

    playNotificationSound() {
        if (document.hidden) {
            document.title = '(New Message) Chat';
            setTimeout(() => document.title = document.title.replace('(New Message) ', ''), 3000);
        }
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    initLastMessageId() {
        this.messagesContainer.querySelectorAll('[data-message-id]').forEach(msg => {
            const id = parseInt(msg.dataset.messageId) || 0;
            if (id > this.lastMessageId) this.lastMessageId = id;
        });
    }

    cleanup() {
        if (this.pollingInterval) clearInterval(this.pollingInterval);
        if (this.channel && typeof window.Echo !== 'undefined') {
            window.Echo.leave(`conversation.${conversationId}`);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.chatManager = new ChatManager();
});
