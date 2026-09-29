/**
 * CurtainLux Admin Live Chat Desk JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {
    const chatFeed = document.getElementById('adminChatMessages');
    const replyForm = document.getElementById('adminReplyForm');
    const replyInput = document.getElementById('adminReplyInput');
    const conversationIdInput = document.getElementById('currentConversationId');

    if (chatFeed) {
        chatFeed.scrollTop = chatFeed.scrollHeight;
    }

    if (!conversationIdInput) return;
    const conversationId = conversationIdInput.value;
    if (!conversationId) return;

    let lastMsgId = 0;
    const existingMsgs = chatFeed ? chatFeed.querySelectorAll('[data-msg-id]') : [];
    if (existingMsgs.length > 0) {
        lastMsgId = parseInt(existingMsgs[existingMsgs.length - 1].getAttribute('data-msg-id') || 0);
    }

    // 1. Reply via AJAX
    if (replyForm && replyInput) {
        replyForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const text = replyInput.value.trim();
            if (!text) return;

            replyInput.value = '';

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch(`/admin/chats/${conversationId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text }),
                });

                if (!res.ok) throw new Error('Send failed');

                const data = await res.json();
                if (data.message) {
                    appendStaffMessage(data.message);
                    if (data.message.id > lastMsgId) lastMsgId = data.message.id;
                }
            } catch (err) {
                console.error('Reply error:', err);
            }
        });
    }

    function appendStaffMessage(msg) {
        if (!chatFeed) return;
        const row = document.createElement('div');
        row.className = 'admin-msg-row staff';
        row.setAttribute('data-msg-id', msg.id);

        const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : 'Vừa xong';

        row.innerHTML = `
            <div class="admin-msg-bubble staff">
                <div class="msg-text">${escapeHtml(msg.message)}</div>
                <span class="admin-msg-time">${timeStr}</span>
            </div>
        `;
        chatFeed.appendChild(row);
        chatFeed.scrollTop = chatFeed.scrollHeight;
    }

    function appendCustomerMessage(msg) {
        if (!chatFeed) return;
        const row = document.createElement('div');
        row.className = 'admin-msg-row customer';
        row.setAttribute('data-msg-id', msg.id);

        const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : 'Vừa xong';

        row.innerHTML = `
            <div class="admin-msg-bubble customer">
                <div class="msg-text">${escapeHtml(msg.message)}</div>
                <span class="admin-msg-time">${timeStr}</span>
            </div>
        `;
        chatFeed.appendChild(row);
        chatFeed.scrollTop = chatFeed.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // 2. Fast Polling for New Messages from Customer
    setInterval(async function () {
        try {
            const res = await fetch(`/admin/chats/${conversationId}/poll?last_id=${lastMsgId}`);
            if (!res.ok) return;

            const data = await res.json();
            if (data.status === 'closed') {
                const replyForm = document.getElementById('adminReplyForm');
                if (replyForm) replyForm.style.display = 'none';
            }

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.sender_type === 'customer') {
                        appendCustomerMessage(msg);
                    }
                    if (msg.id > lastMsgId) lastMsgId = msg.id;
                });
            }
        } catch (e) {
            // Tolerate polling error
        }
    }, 3000);
});
