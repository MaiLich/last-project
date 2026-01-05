@extends('admin.layout.layout')

@section('content')
<style>
    .chat-wrapper { display:flex; gap:16px; height: calc(100vh - 160px); }
    .conv-list { width: 320px; background:#fff; border-radius:8px; overflow:auto; border:1px solid #e5e7eb; }
    .conv-item { padding:12px 14px; cursor:pointer; border-bottom:1px solid #f1f5f9; }
    .conv-item.active { background:#eef2ff; }
    .conv-name { font-weight:600; }
    .conv-last { font-size:13px; opacity:.75; margin-top:4px; }

    .chat-panel { flex:1; background:#fff; border-radius:8px; border:1px solid #e5e7eb; overflow:hidden; display:flex; flex-direction:column; }
    #chat-body { flex:1; min-height:0; }

    .message { max-width:80%; padding:10px 12px; border-radius:18px; word-wrap:break-word; margin-bottom:8px; }
    .message.sent { background:#1877f2; color:#fff; margin-left:auto; border-bottom-right-radius:6px; }
    .message.received { background:#e4e6eb; color:#000; border-bottom-left-radius:6px; }
</style>

<div class="chat-wrapper">
    <div class="conv-list">
        @foreach($conversations as $conv)
            <div
                class="conv-item conversation-item {{ (($selectedConversation?->id ?? null) == $conv->id) ? 'active' : '' }}"
                data-id="{{ $conv->id }}"
            >
                <div class="conv-name">{{ $conv->user?->name ?? 'User' }}</div>
                <div class="conv-last">{{ $conv->latestMessage?->message ?? '' }}</div>
            </div>
        @endforeach
    </div>

    <div class="chat-panel">
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

    let warnedSession = false; // tránh alert spam

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'",'&#039;');
    }

    function normalizeSenderType(t) {
        // Chuẩn hoá mọi kiểu: "App\\Models\\Admin" / "App\\\\Models\\\\Admin" / ...
        let s = String(t || '');
        // đổi mọi cụm "\\\\" (2 backslash) thành "\\" (1 backslash)
        while (s.includes('\\\\')) s = s.replaceAll('\\\\', '\\');
        return s;
    }

    function isAdminMessage(msg) {
        const senderType = normalizeSenderType(msg?.sender_type);
        // Lấy phần cuối sau dấu "\" => Admin/User
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
        const adminMsg = isAdminMessage(msg);
        const cls = adminMsg ? 'sent' : 'received';
        const time = msg.created_at ? new Date(msg.created_at).toLocaleString('vi-VN') : '';

        const wrap = document.createElement('div');
        wrap.className = `message ${cls}`;
        wrap.innerHTML = `
            <div>${escapeHtml(msg.message ?? '').replace(/\n/g,'<br>')}</div>
            <small class="text-muted" style="opacity:.7; display:block; margin-top:6px">${escapeHtml(time)}</small>
        `;
        return wrap;
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

        // Nếu backend trả HTML (thường do hết session/redirect), coi như lỗi rõ ràng
        if (!ct.includes('application/json')) {
            const looksHtml = raw.includes('<html') || raw.includes('<!DOCTYPE');
            if (looksHtml) throw new Error('Hết phiên đăng nhập. Refresh trang admin/chat.');
            // không phải JSON thì cũng báo lỗi
            throw new Error('Response không phải JSON.');
        }

        let data = null;
        try { data = JSON.parse(raw); } catch(e) {}

        if (!res.ok) {
            throw new Error(data?.error || data?.message || `HTTP ${res.status}`);
        }

        // Một số backend có thể bọc dạng {messages:[...]}
        if (data && Array.isArray(data.messages)) return data.messages;

        return data;
    }

    async function loadAll(convId) {
        const box = messagesBox();
        if (!box) return;

        const list = await fetchJsonSafe(`/admin/chat/messages/${convId}?t=${Date.now()}`);
        box.innerHTML = '';
        lastId = 0;

        (list || []).forEach(m => {
            box.appendChild(renderMessage(m));
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
            box.appendChild(renderMessage(m));
            lastId = Math.max(lastId, Number(m.id || 0));
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
        lastId = 0;
        warnedSession = false;

        loadAll(convId).catch(err => console.error(err));

        pollTimer = setInterval(() => {
            if (document.hidden) return;

            loadNew(convId).catch((err) => {
                // nếu hết session -> báo 1 lần rồi dừng poll
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
        const html = await fetch(`/admin/chat/conversation/${convId}?t=${Date.now()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.text());

        document.getElementById('chat-body').innerHTML = html;
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

    // GLOBAL: gọi từ form trong partial
    window.sendAdminMessage = async function (convId) {
        convId = convId || currentConversationId;
        if (!convId) return;

        const textarea = document.getElementById(`message-input-${convId}`);
        if (!textarea) return;

        const message = (textarea.value || '').trim();
        if (!message) return;

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

            // append luôn
            if (data?.message) {
                const box = messagesBox();
                if (box) {
                    box.appendChild(renderMessage(data.message));
                    lastId = Math.max(lastId, Number(data.message.id || 0));
                    scrollToBottom();
                }
            } else {
                await loadNew(convId);
            }
        } catch (err) {
            console.error(err);
            alert('Lỗi: ' + (err.message || 'Server error'));
        } finally {
            textarea.disabled = false;
        }
    };

    // Init
    if (currentConversationId) {
        startPolling(currentConversationId);
    }
});
</script>
@endsection
