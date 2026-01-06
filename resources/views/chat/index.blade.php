@extends('front.layout.layout')

@section('content')

<style>
#chatbot-app{max-width:520px;margin:auto}
#chatbot-messages{height:420px;overflow-y:auto;background:#f8f9fa;padding:15px;display:flex;flex-direction:column;gap:8px}
.message{max-width:80%;padding:8px 12px;border-radius:18px;word-wrap:break-word}
.message.sent{background:#1877f2;color:#fff;margin-left:auto;border-bottom-right-radius:5px}
.message.received{background:#e4e6eb;color:#000;border-bottom-left-radius:5px}
.message-time{font-size:11px;opacity:.6;margin-top:4px;text-align:right}
</style>

<div class="container my-4">
    <h3 class="mb-3">Chat với Admin</h3>

    <div id="chatbot-app" class="card shadow-sm">
        <div id="chatbot-messages"></div>

        <div class="card-footer d-flex gap-2">
            <input id="chatbot-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
            <button id="chatbot-send" class="btn btn-primary">Gửi</button>
        </div>
    </div>

    <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let conversationId = {{ $conversation->id ?? 'null' }};

    const box = document.getElementById('chatbot-messages');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const csrf = document.getElementById('csrf-token')?.value;

    if (!box || !input || !sendBtn) return;

    let lastId = 0;
    let sending = false;
    let pollTimer = null;

    // chống render trùng dù backend trả lại cùng message nhiều lần
    let renderedIds = new Set();

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function fmtTime(iso) {
        const d = iso ? new Date(iso) : new Date();
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        return `${hh}:${mm}`;
    }

    function normalizeSenderType(t) {
        let s = String(t || '');
        while (s.includes('\\\\')) s = s.replaceAll('\\\\', '\\');
        return s;
    }

    function isUserMessage(msg) {
        const tail = normalizeSenderType(msg?.sender_type).split('\\').pop();
        return tail === 'User';
    }

    function scrollToBottom() {
        box.scrollTop = box.scrollHeight;
    }

    function renderMessage(msg) {
        const idNum = Number(msg?.id || 0);
        if (idNum > 0) {
            if (renderedIds.has(idNum)) return;
            renderedIds.add(idNum);
        }

        const isMe = isUserMessage(msg);
        const wrap = document.createElement('div');
        wrap.className = 'message ' + (isMe ? 'sent' : 'received');

        wrap.innerHTML = `
            <div>${escapeHtml(msg.message ?? '').replace(/\n/g,'<br>')}</div>
            <div class="message-time">${fmtTime(msg.created_at)}</div>
        `;

        box.appendChild(wrap);

        if (idNum > lastId) lastId = idNum;
    }

    async function fetchJson(url, options = {}) {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...(options.headers || {})
            },
            ...options
        });

        const data = await res.json().catch(() => null);
        if (!res.ok) {
            const msg = data?.error || data?.message || `HTTP ${res.status}`;
            throw new Error(msg);
        }
        return data;
    }

    async function loadAll() {
        if (!conversationId) return;
        const list = await fetchJson(`/chat/messages/${conversationId}?t=${Date.now()}`);

        box.innerHTML = '';
        lastId = 0;
        renderedIds = new Set();

        (list || []).forEach(renderMessage);
        scrollToBottom();
    }

    async function loadNew() {
        if (!conversationId) return;

        const url = lastId
            ? `/chat/messages/${conversationId}?since_id=${lastId}&t=${Date.now()}`
            : `/chat/messages/${conversationId}?t=${Date.now()}`;

        const list = await fetchJson(url);
        let added = false;

        (list || []).forEach(m => {
            const before = lastId;
            renderMessage(m);
            if (lastId !== before) added = true;
        });

        if (added) scrollToBottom();
    }

    function stopPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = null;
    }

    function startPolling() {
        stopPolling();
        pollTimer = setInterval(() => {
            if (document.hidden) return;
            if (sending) return; // tránh đua nhau khi đang gửi
            loadNew().catch(() => {});
        }, 1000);
    }

    async function send() {
        const text = input.value.trim();
        if (!text || sending) return;

        sending = true;
        sendBtn.disabled = true;

        try {
            const payload = { message: text, conversation_id: conversationId };

            const data = await fetchJson('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(payload)
            });

            input.value = '';

            // nếu lần đầu tạo conversation
            if (data?.conversation_id) conversationId = data.conversation_id;

            //KHÔNG render ngay ở đây để tránh bị nhân đôi
            await loadNew();
        } catch (e) {
            alert(e.message || 'Không gửi được tin nhắn');
        } finally {
            sending = false;
            sendBtn.disabled = false;
        }
    }

    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            send();
        }
    });

    loadAll().catch(console.error);
    startPolling();
});
</script>

@endsection