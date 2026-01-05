<div class="d-flex flex-column h-100">
    <div id="chat-messages" class="p-3" style="height: 430px; overflow-y: auto;">
        @foreach($messages as $message)
            @php
                $isAdmin = $message->sender_type === \App\Models\Admin::class;
            @endphp

            <div class="d-flex flex-column mb-2 {{ $isAdmin ? 'align-items-end' : 'align-items-start' }}">
                <div
                    class="px-3 py-2 rounded-4 {{ $isAdmin ? 'bg-primary text-white' : 'bg-light text-dark border' }}"
                    style="max-width: 80%; word-break: break-word;"
                >
                    <div class="mb-1">{{ $message->message }}</div>
                    <div style="font-size: 11px; opacity: .7; text-align: right;">
                        {{ $message->created_at->format('H:i - d/m/Y') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="p-3 border-top bg-light">
        <form onsubmit="window.sendAdminMessage({{ $conversation->id }}); return false;">
            @csrf
            <div class="input-group">
                <textarea
                    id="message-input-{{ $conversation->id }}"
                    class="form-control"
                    rows="2"
                    placeholder="Nhập tin nhắn..."
                    required></textarea>

                <button type="submit" class="btn btn-primary px-4">Gửi</button>
            </div>
        </form>
    </div>
</div>
