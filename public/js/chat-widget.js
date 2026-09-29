/**
 * CurtainLux Customer Chatbot & Real-Time Staff Socket Module (Aligned with java_angular Architecture)
 */
document.addEventListener('DOMContentLoaded', function () {
    const launcher = document.getElementById('chatWidgetToggle');
    const windowEl = document.getElementById('chatWidgetWindow');
    const minimizeBtn = document.getElementById('btnChatMinimize');
    const handoverBtn = document.getElementById('btnRequestStaff');
    const form = document.getElementById('chatWidgetForm');
    const input = document.getElementById('chatMessageInput');
    const messagesContainer = document.getElementById('chatWidgetMessages');
    const quickRepliesContainer = document.getElementById('chatQuickReplies');
    const closedNotice = document.getElementById('chatClosedNotice');
    const btnStartNewChat = document.getElementById('btnStartNewChat');
    const headerTitle = document.getElementById('chatHeaderTitle');
    const headerSubtitle = document.getElementById('chatHeaderSubtitle');
    const headerAvatar = document.getElementById('chatHeaderAvatar');

    if (!launcher || !windowEl) return;

    let conversation = null;
    let lastMessageId = 0;
    let pollInterval = null;
    let isInitialized = false;

    // 1. Toggle Window
    launcher.addEventListener('click', function () {
        const isOpen = windowEl.classList.contains('is-open');
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    });

    if (minimizeBtn) {
        minimizeBtn.addEventListener('click', closeChat);
    }

    function openChat() {
        windowEl.classList.add('is-open');
        if (!isInitialized) {
            initChatSession(false);
        }
        if (input && (!conversation || conversation.status !== 'closed')) {
            setTimeout(() => input.focus(), 250);
        }
    }

    function closeChat() {
        windowEl.classList.remove('is-open');
    }

    // 2. Initialize Chat Session (hỗ trợ forceNew khi bắt đầu phiên mới)
    async function initChatSession(forceNew = false) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/api/chat/init', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ force_new: forceNew }),
            });

            if (!res.ok) throw new Error('Cannot init chat');

            const data = await res.json();
            conversation = data.conversation;
            isInitialized = true;
            lastMessageId = 0;

            // Render existing messages
            if (messagesContainer) messagesContainer.innerHTML = '';
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    appendMessage(msg);
                    if (msg.id > lastMessageId) lastMessageId = msg.id;
                });

                // Render quick replies on the last bot message if active
                const lastMsg = data.messages[data.messages.length - 1];
                if (lastMsg.sender_type === 'bot' && lastMsg.quick_replies && conversation.status !== 'closed') {
                    renderQuickReplies(lastMsg.quick_replies);
                }
            }

            updateHeaderState(conversation.status, data.staff_name);
            startPollingIfNeeded();
        } catch (err) {
            console.error('Chat init error:', err);
        }
    }

    // Nút Bắt đầu cuộc trò chuyện mới
    if (btnStartNewChat) {
        btnStartNewChat.addEventListener('click', function () {
            initChatSession(true);
        });
    }

    // 3. Send Message
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const text = input ? input.value.trim() : '';
            if (!text || !conversation) return;

            if (conversation.status === 'closed') {
                updateHeaderState('closed');
                return;
            }

            input.value = '';
            if (quickRepliesContainer) quickRepliesContainer.innerHTML = '';

            // Optimistically append customer message
            appendMessage({
                sender_type: 'customer',
                message: text,
                created_at: new Date().toISOString(),
            });

            showTypingIndicator();

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        conversation_id: conversation.id,
                        message: text,
                    }),
                });

                hideTypingIndicator();

                if (!res.ok) {
                    const errJson = await res.json().catch(() => ({}));
                    if (errJson.status === 'closed') {
                        conversation.status = 'closed';
                        updateHeaderState('closed');
                    }
                    return;
                }

                const data = await res.json();

                if (data.customer_message && data.customer_message.id > lastMessageId) {
                    lastMessageId = data.customer_message.id;
                }

                if (data.bot_message) {
                    appendMessage(data.bot_message);
                    if (data.bot_message.id > lastMessageId) lastMessageId = data.bot_message.id;

                    if (data.bot_message.quick_replies && conversation.status !== 'closed') {
                        renderQuickReplies(data.bot_message.quick_replies);
                    }
                }

                if (data.conversation_status) {
                    conversation.status = data.conversation_status;
                    updateHeaderState(conversation.status, data.staff_name);
                    startPollingIfNeeded();
                }
            } catch (err) {
                hideTypingIndicator();
                console.error('Send error:', err);
            }
        });
    }

    // 4. Handover to Live Staff (Chuyển giao cho nhân viên)
    if (handoverBtn) {
        handoverBtn.addEventListener('click', function () {
            if (input && form) {
                input.value = 'Em cần gặp chuyên viên tư vấn rèm trực tiếp';
                form.dispatchEvent(new Event('submit'));
            }
        });
    }

    // 5. Append message bubble to UI
    function appendMessage(msg) {
        if (!messagesContainer) return;

        const row = document.createElement('div');
        row.className = `chat-msg-row ${msg.sender_type}`;

        let avatarHtml = '';
        if (msg.sender_type === 'bot') {
            avatarHtml = `<div class="chat-msg-avatar"><i class="fa-solid fa-robot"></i></div>`;
        } else if (msg.sender_type === 'staff') {
            avatarHtml = `<div class="chat-msg-avatar staff"><i class="fa-solid fa-user-tie"></i></div>`;
        }

        const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : '';

        const bubble = document.createElement('div');
        bubble.className = 'chat-msg-bubble';
        bubble.textContent = msg.message;

        const timeSpan = document.createElement('span');
        timeSpan.className = 'chat-msg-time';
        timeSpan.textContent = timeStr;
        bubble.appendChild(timeSpan);

        if (msg.sender_type !== 'customer') {
            row.innerHTML = avatarHtml;
            row.appendChild(bubble);
        } else {
            row.appendChild(bubble);
        }

        messagesContainer.appendChild(row);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // 6. Quick Replies
    function renderQuickReplies(replies) {
        if (!quickRepliesContainer) return;
        quickRepliesContainer.innerHTML = '';

        if (!Array.isArray(replies) || replies.length === 0 || conversation?.status === 'closed') return;

        replies.forEach(text => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'chat-chip';
            chip.textContent = text;
            chip.addEventListener('click', function () {
                if (input && form) {
                    input.value = text;
                    form.dispatchEvent(new Event('submit'));
                }
            });
            quickRepliesContainer.appendChild(chip);
        });
    }

    // 7. Typing Indicator
    function showTypingIndicator() {
        if (!messagesContainer || document.getElementById('chatTypingIndicator')) return;

        const ind = document.createElement('div');
        ind.id = 'chatTypingIndicator';
        ind.className = 'chat-msg-row bot';
        ind.innerHTML = `
            <div class="chat-msg-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="chat-typing-dots">
                <span></span><span></span><span></span>
            </div>
        `;
        messagesContainer.appendChild(ind);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function hideTypingIndicator() {
        const ind = document.getElementById('chatTypingIndicator');
        if (ind) ind.remove();
    }

    // 8. Update Header State (State Machine: bot -> waiting_staff -> staff_connected -> closed)
    function updateHeaderState(status, staffName = null) {
        if (!headerTitle || !headerSubtitle) return;

        if (status === 'closed') {
            headerTitle.textContent = 'Phiên Tư Vấn Đã Kết Thúc';
            headerSubtitle.innerHTML = `<i class="fa-solid fa-circle" style="color: #94a3b8; font-size: 8px;"></i> <span>Đã đóng phiên</span>`;
            if (headerAvatar) headerAvatar.innerHTML = `<i class="fa-solid fa-check"></i><span class="status-dot" style="background:#94a3b8;"></span>`;
            if (handoverBtn) handoverBtn.style.display = 'none';
            if (closedNotice) closedNotice.style.display = 'block';
            if (form) form.style.display = 'none';
            if (quickRepliesContainer) quickRepliesContainer.innerHTML = '';
        } else if (status === 'staff_connected') {
            headerTitle.textContent = staffName ? `Chuyên viên: ${staffName}` : 'Chuyên Viên Tư Vấn CurtainLux';
            headerSubtitle.innerHTML = `<i class="fa-solid fa-circle" style="color: #22c55e; font-size: 8px;"></i> <span>Đang trực tiếp hỗ trợ bạn</span>`;
            if (headerAvatar) headerAvatar.innerHTML = `<i class="fa-solid fa-headset"></i><span class="status-dot"></span>`;
            if (handoverBtn) handoverBtn.style.display = 'none';
            if (closedNotice) closedNotice.style.display = 'none';
            if (form) form.style.display = 'flex';
        } else if (status === 'waiting_staff') {
            headerTitle.textContent = 'Đang Kết Nối Nhân Viên...';
            headerSubtitle.innerHTML = `<i class="fa-solid fa-spinner fa-spin" style="color: #f59e0b; font-size: 10px;"></i> <span>Vui lòng đợi giây lát</span>`;
            if (headerAvatar) headerAvatar.innerHTML = `<i class="fa-solid fa-headset"></i><span class="status-dot" style="background:#f59e0b;"></span>`;
            if (handoverBtn) handoverBtn.style.display = 'none';
            if (closedNotice) closedNotice.style.display = 'none';
            if (form) form.style.display = 'flex';
        } else {
            // bot mode
            headerTitle.textContent = 'CurtainBot — Tư Vấn Rèm';
            headerSubtitle.innerHTML = `<i class="fa-solid fa-circle" style="color: #22c55e; font-size: 8px;"></i> <span>Trực tuyến • Sẵn sàng hỗ trợ</span>`;
            if (headerAvatar) headerAvatar.innerHTML = `<i class="fa-solid fa-robot"></i><span class="status-dot"></span>`;
            if (handoverBtn) handoverBtn.style.display = 'flex';
            if (closedNotice) closedNotice.style.display = 'none';
            if (form) form.style.display = 'flex';
        }
    }

    // 9. Real-time Polling Engine (Chu kỳ 3 giây khi mở widget)
    function startPollingIfNeeded() {
        if (pollInterval) clearInterval(pollInterval);

        pollInterval = setInterval(async function () {
            if (!conversation || !windowEl.classList.contains('is-open')) return;

            try {
                const res = await fetch(`/api/chat/poll/${conversation.id}?last_id=${lastMessageId}`);
                if (!res.ok) return;

                const data = await res.json();

                if (data.status && data.status !== conversation.status) {
                    conversation.status = data.status;
                    updateHeaderState(data.status, data.staff_name);
                }

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        if (msg.sender_type !== 'customer') {
                            appendMessage(msg);
                        }
                        if (msg.id > lastMessageId) lastMessageId = msg.id;
                    });
                }
            } catch (e) {
                // Background poll fail tolerance
            }
        }, 3000);
    }
});
