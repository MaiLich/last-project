<div class="d-flex flex-column h-100">
    <div class="px-3 py-2 border-bottom bg-white">
        <div class="fw-semibold">
            {{ $conversation->user?->name ?? 'User' }}
        </div>
        <div class="text-muted" style="font-size:12px;">
            Conversation #{{ $conversation->id }}
        </div>
    </div>

    <div id="chat-messages" class="p-3" style="flex:1; min-height:0; overflow-y:auto;">
        @foreach($messages as $message)
            <div class="message {{ $message->sender_type === \App\Models\Admin::class ? 'sent' : 'received' }}">
                <div>{{ $message->message }}</div>
                <div class="msg-meta">{{ $message->created_at->format('H:i - d/m/Y') }}</div>
            </div>
        @endforeach
    </div>

    <div class="p-3 border-top bg-light">
        <form onsubmit="window.sendAdminMessage({{ $conversation->id }}); return false;">
            @csrf

            <div class="d-flex gap-2 align-items-stretch">
                <textarea
                    id="message-input-{{ $conversation->id }}"
                    class="form-control"
                    rows="3"
                    placeholder="Nhập tin nhắn..."
                    style="min-height:80px; font-size:15px; line-height:1.4; padding:12px 14px; resize:vertical;"
                    required></textarea>

                <button type="submit" class="btn btn-primary px-4" style="min-width:90px;">
                    Gửi
                </button>
            </div>
        </form>
    </div>
</div>
