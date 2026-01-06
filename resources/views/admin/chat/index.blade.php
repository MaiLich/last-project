@extends('admin.layout.layout')

@section('content')
<style>
    .chat-shell{
        display:flex;
        gap:16px;
        height: calc(100vh - 160px);
        min-height: 620px;
    }

    .chat-left{
        width: 240px;
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:12px;
        overflow:hidden;
        display:flex;
        flex-direction:column;
    }

    .chat-left .left-head{
        padding:12px 12px 10px;
        border-bottom:1px solid #f1f5f9;
        background:#fff;
    }

    .chat-left .left-head input{
        width:100%;
        border:1px solid #e5e7eb;
        border-radius:10px;
        padding:10px 12px;
        outline:none;
    }

    .conv-list{
        overflow:auto;
        flex:1;
    }

    .conv-item{
        padding:12px 14px;
        cursor:pointer;
        border-bottom:1px solid #f1f5f9;
    }
    .conv-item:hover{ background:#f8fafc; }
    .conv-item.active{ background:#eef2ff; }

    .conv-name{ font-weight:600; }
    .conv-last{
        font-size:13px;
        opacity:.75;
        margin-top:4px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
        max-width: 100%;
    }

    .chat-right{
        flex: 1 1 900px;   /* ưu tiên rộng ~900px */
        min-width: 900px;  /* đảm bảo không bị co lại */
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:12px;
        overflow:hidden;
        display:flex;
        flex-direction:column;
    }

    #chat-body{
        flex:1;
        min-height:0;
        display:flex;
        flex-direction:column;
    }

    /* bubbles */
    .message{
        max-width:30%;
        padding:10px 12px;
        border-radius:16px;
        word-break: break-word;
        margin-bottom:10px;
    }
    .message.sent{
        background:#1877f2;
        color:#fff;
        margin-left:auto;
        border-bottom-right-radius:6px;
    }
    .message.received{
        background:#eef2f7;
        color:#0f172a;
        border:1px solid #e5e7eb;
        border-bottom-left-radius:6px;
    }
    .message .msg-meta{
        margin-top:6px;
        font-size:11px;
        opacity:.75;
    }
    .message.sent .msg-meta{ text-align:right; color:rgba(255,255,255,.85); }
    .message.received .msg-meta{ text-align:right; color:rgba(15,23,42,.65); }
</style>

<div class="chat-shell">
    <div class="chat-left">
        <div class="left-head">
            <input id="conv-search" type="text" placeholder="Tìm hội thoại...">
        </div>

        <div class="conv-list" id="conv-list">
            @foreach($conversations as $conv)
                <div
                    class="conv-item conversation-item {{ (($selectedConversation?->id ?? null) == $conv->id) ? 'active' : '' }}"
                    data-id="{{ $conv->id }}"
                    data-name="{{ strtolower($conv->user?->name ?? 'user') }}"
                >
                    <div class="conv-name">{{ $conv->user?->name ?? 'User' }}</div>
                    <div class="conv-last">{{ $conv->latestMessage?->message ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="chat-right">
        <div id="chat-body">
            @if($selectedConversation)
                @include('admin.chat.conversation_detail', [
                    'conversation' => $selectedConversation,
                    'messages' => $messages
                ])
            @else
                <div class="p-4">Chưa có cuộc trò chuyện.</div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const CSRF = @json(csrf_token());
    let currentConversationId = @json($selectedConversation?->id);

    let pollTimer = null;
    let lastId = 0;
    const seenIds = new Set();

    let adminSending = false;
    let warnedSession = false;

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'",'&#039;');
    }

    function normalizeSenderType(t) {
        let s = String(t || '');
        while (s.includes('\\\\')) s = s.replaceAll('\\\\', '\\');
        return s;
    }

    function isAdminMessage(msg) {
        const senderType = normalizeSenderType(msg?.sender_type);
        const tail = senderType.split('\\').pop();
        return tail === 'Admin';
    }

    function messagesBox() {
        return document.querySelector('#chat-body #chat-messages');
    }

    function scrollToBottom() {
        const box = messagesBox();
        if (!box) return;
        box.scrollTop = box.scrollHeight;
    }

    function renderMessage(msg) {
        const box = messagesBox();
        if (!box) return;

        const idNum = Number(msg.id || 0);

        // CHỐNG TRÙNG (fix: UI hiện 2 lần)
        if (idNum && seenIds.has(idNum)) return;
        if (idNum) seenIds.add(idNum);

        const cls = isAdminMessage(msg) ? 'sent' : 'received';
        const time = msg.created_at ? new Date(msg.created_at).toLocaleString('vi-VN') : '';

        const wrap = document.createElement('div');
        wrap.className = `message ${cls}`;
        wrap.innerHTML = `
            <div>${escapeHtml(msg.message ?? '').replace(/\n/g,'<br>')}</div>
            <div class="msg-meta">${escapeHtml(time)}</div>
        `;
        box.appendChild(wrap);

        if (idNum > lastId) lastId = idNum;
    }

    async function fetchJsonSafe(url, options = {}) {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...(options.headers || {})
            },
            ...options
        });

        const ct = (res.headers.get('content-type') || '').toLowerCase();
        const raw = await res.text();

        if (!ct.includes('application/json')) {
            const looksHtml = raw.includes('<html') || raw.includes('<!DOCTYPE');
            if (looksHtml) throw new Error('Hết phiên đăng nhập. Refresh trang admin/chat.');
            throw new Error('Response không phải JSON.');
        }

        let data = null;
        try { data = JSON.parse(raw); } catch(e) {}

        if (!res.ok) throw new Error(data?.error || data?.message || `HTTP ${res.status}`);

        if (data && Array.isArray(data.messages)) return data.messages;
        return data;
    }

    async function loadAll(convId) {
        const box = messagesBox();
        if (!box) return;

        const list = await fetchJsonSafe(`/admin/chat/messages/${convId}?t=${Date.now()}`);

        box.innerHTML = '';
        lastId = 0;
        seenIds.clear();

        (list || []).forEach(m => {
            renderMessage(m);
            lastId = Math.max(lastId, Number(m.id || 0));
        });

        scrollToBottom();
    }

    async function loadNew(convId) {
        const box = messagesBox();
        if (!box) return;

        const url = lastId
            ? `/admin/chat/messages/${convId}?since_id=${lastId}&t=${Date.now()}`
            : `/admin/chat/messages/${convId}?t=${Date.now()}`;

        const list = await fetchJsonSafe(url);

        let added = false;
        (list || []).forEach(m => {
            renderMessage(m);
            added = true;
        });

        if (added) scrollToBottom();
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    function startPolling(convId) {
        stopPolling();
        warnedSession = false;

        loadAll(convId).catch(err => console.error(err));

        pollTimer = setInterval(() => {
            if (document.hidden) return;
            if (adminSending) return;

            loadNew(convId).catch((err) => {
                if (!warnedSession) {
                    warnedSession = true;
                    console.error(err);
                    alert('Lỗi: ' + (err?.message || 'Server error'));
                }
                stopPolling();
            });
        }, 1000);
    }

    async function loadConversationDetail(convId) {
        stopPolling();

        const html = await fetch(`/admin/chat/conversation/${convId}?t=${Date.now()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.text());

        document.getElementById('chat-body').innerHTML = html;

        // reset state cho hội thoại mới
        lastId = 0;
        seenIds.clear();

        startPolling(convId);
    }

    // Click chọn conversation
    document.querySelectorAll('.conversation-item').forEach(el => {
        el.addEventListener('click', function () {
            const convId = Number(this.dataset.id);
            if (!convId) return;

            currentConversationId = convId;

            document.querySelectorAll('.conversation-item').forEach(x => x.classList.remove('active'));
            this.classList.add('active');

            loadConversationDetail(convId).catch(err => console.error(err));
        });
    });

    // Search
    const searchInput = document.getElementById('conv-search');
    if (searchInput) {
        searchInput.addEventListener('input', function(){
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('.conversation-item').forEach(el => {
                const name = el.dataset.name || '';
                el.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }

    // GLOBAL: gọi từ form trong partial
    window.sendAdminMessage = async function (convId) {
        convId = convId || currentConversationId;
        if (!convId) return;

        const textarea = document.getElementById(`message-input-${convId}`);
        if (!textarea) return;

        const message = (textarea.value || '').trim();
        if (!message) return;

        if (adminSending) return;

        adminSending = true;
        textarea.disabled = true;

        try {
            const data = await fetchJsonSafe('/admin/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    conversation_id: convId,
                    message: message
                })
            });

            textarea.value = '';
            textarea.focus();

            // Backend trả message -> append 1 lần, KHÔNG gọi loadNew nữa
            if (data?.message) {
                renderMessage(data.message);
                scrollToBottom();
                return;
            }

            // fallback nếu backend không trả message
            await loadNew(convId);
        } catch (err) {
            console.error(err);
            alert('Lỗi: ' + (err.message || 'Server error'));
        } finally {
            textarea.disabled = false;
            adminSending = false;
        }
    };

    // Init
    if (currentConversationId) {
        startPolling(currentConversationId);
    }
});
</script>
@endsection
