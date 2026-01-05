@extends('front.layout.layout')

@section('content')
<div class="container my-4">
    <h3 class="mb-3">Chat với Admin</h3>

    <div id="chat-app" class="card">
        <div class="card-body" style="height: 420px; overflow:auto;" id="chat-messages">
            @if(isset($messages) && count($messages) > 0)
                @foreach($messages as $message)
                    <div class="mb-2 d-flex {{ $message->sender_type == 'App\\Models\\User' ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="px-3 py-2 rounded"
                             style="max-width: 75%;
                                {{ $message->sender_type == 'App\\Models\\User'
                                    ? 'background:#0d6efd;color:#fff;'
                                    : 'background:#e9ecef;color:#212529;' }}">
                            <div>{{ $message->message }}</div>
                            <div style="font-size:12px;opacity:.7;margin-top:4px;text-align:right;">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted mt-3">Bắt đầu cuộc trò chuyện...</div>
            @endif
        </div>

        <div class="card-footer d-flex gap-2">
            <input id="chat-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off" />
            <button id="chat-send" class="btn btn-primary">Gửi</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // conversationId từ Controller
    window.conversationId = {!! isset($conversation) && $conversation ? $conversation->id : 'null' !!};

    const messagesBox = document.getElementById('chat-messages');
    const input = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send');

    // Lấy CSRF từ meta (layout bạn đã có meta csrf-token)
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Scroll xuống cuối
    messagesBox.scrollTop = messagesBox.scrollHeight;

    function renderMessage(text, isUser, timestamp = null) {
        const wrap = document.createElement('div');
        wrap.className = 'mb-2 d-flex ' + (isUser ? 'justify-content-end' : 'justify-content-start');

        const bubble = document.createElement('div');
        bubble.className = 'px-3 py-2 rounded';
        bubble.style.maxWidth = '75%';
        bubble.style.background = isUser ? '#0d6efd' : '#e9ecef';
        bubble.style.color = isUser ? '#fff' : '#212529';

        const msg = document.createElement('div');
        msg.textContent = text;

        const time = document.createElement('div');
        const dt = timestamp ? new Date(timestamp) : new Date();
        time.textContent = dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        time.style.fontSize = '12px';
        time.style.opacity = '0.7';
        time.style.marginTop = '4px';
        time.style.textAlign = 'right';

        bubble.appendChild(msg);
        bubble.appendChild(time);
        wrap.appendChild(bubble);
        messagesBox.appendChild(wrap);

        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function loadMessages() {
        if (!window.conversationId) return;

        fetch('/chat/messages/' + window.conversationId, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(list => {
            messagesBox.innerHTML = '';
            list.forEach(m => {
                const isUser = (m.sender_type === 'App\\\\Models\\\\User');
                renderMessage(m.message, isUser, m.created_at);
            });
            messagesBox.scrollTop = messagesBox.scrollHeight;
        })
        .catch(err => console.error('Load messages error:', err));
    }

    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Optimistic UI
        renderMessage(text, true);
        input.value = '';
        input.focus();

        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: window.conversationId ? window.conversationId : null,
                message: text
            })
        })
        .then(async (res) => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            // server có thể tạo hội thoại mới
            if (data.conversation_id) window.conversationId = data.conversation_id;
        })
        .catch(err => {
            console.error('Send message error:', err);
        });
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function(e){
        if (e.key === 'Enter') sendMessage();
    });

    // Nếu đã có conversationId thì có thể load lại cho chắc
    if (window.conversationId) loadMessages();

    // Realtime (nếu Echo chạy)
    if (window.Echo && window.conversationId) {
        window.Echo.private('chat.' + window.conversationId)
            .listen('MessageSent', function(e) {
                // admin gửi sang user
                renderMessage(e.message.message, false, e.message.created_at);
            });
    }
});
</script>
@endsection
