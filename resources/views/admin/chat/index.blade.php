@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- List conversations bên trái -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Danh sách các cuộc trò chuyện</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($conversations as $conv)
                            <li class="list-group-item conversation-item {{ $conv->id == $selectedConversation?->id ? 'active' : '' }}" data-id="{{ $conv->id }}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $conv->user->name ?? 'User' }}</strong>
                                        <p class="mb-0 text-truncate">{{ $conv->latestMessage?->message ?? 'No message' }}</p>
                                    </div>
                                    <small class="text-muted">{{ $conv->updated_at->diffForHumans() }}</small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">Chưa có cuộc trò chuyện</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Detail bên phải -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" id="conversation-header">
                    @if($selectedConversation)
                        Chat với {{ $selectedConversation->user->name }}
                    @else
                        Chọn cuộc trò chuyện
                    @endif
                </div>
                <div class="card-body" id="chat-body" style="height: 500px; overflow-y: auto;">
                    @if($selectedConversation)
                        @include('admin.chat.conversation_detail', ['conversation' => $selectedConversation, 'messages' => $messages])
                    @endif
                </div>
                @if($selectedConversation)
                    
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentConversationId = {{ $selectedConversation?->id ?? 'null' }};
    let echoListener = null; // Để unsubscribe khi change conv

    // Click conversation để load detail via AJAX
    $('.conversation-item').click(function() {
        currentConversationId = $(this).data('id');
        console.log('Clicked conversation ID: ' + currentConversationId); // Debug
        loadConversationDetail(currentConversationId);
        $('.conversation-item').removeClass('active');
        $(this).addClass('active');
    });

    // Load detail
    function loadConversationDetail(id) {
        $.get('/admin/chat/conversation/' + id, function(data) {
            $('#chat-body').html($(data).html());  // Load full detail (messages)
            $('#conversation-header').text('Chat với ' + $(data).find('#user-name').text());
            $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);
            // Update form conversation_id
            $('input[name="conversation_id"]').val(id);
            console.log('Loaded conversation ID: ' + id); // Debug
            setupRealTimeListener(id); // Setup Echo for new conv
        }).fail(function(xhr) {
            console.error('Load fail: ' + xhr.status + ' - ' + xhr.responseText);
        });
    }

    // Function render message (tích hợp từ detail)
    function renderMessage(msg) {
        const isMe = msg.sender_type === 'App\\Models\\Admin' && msg.sender_id === {{ auth('admin')->id() }};

        const wrapper = document.createElement('div');
        wrapper.className = 'mb-3 ' + (isMe ? 'text-end' : '');

        const time = new Date(msg.created_at);
        const timeStr = time.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'}) +
                        ' - ' + time.toLocaleDateString('vi-VN');

        wrapper.innerHTML = `
            <div class="d-inline-block p-3 rounded shadow-sm ${isMe ? 'bg-primary text-white' : 'bg-white border'}"
                 style="max-width: 75%;">
                ${msg.message.replace(/\n/g, '<br>')}
                <small class="d-block mt-2 opacity-75">${timeStr}</small>
            </div>
        `;
        return wrapper;
    }


    // Setup real-time Echo listener dynamic
    function setupRealTimeListener(convId) {
        if (echoListener) {
            window.Echo.leave('chat.' + echoListener); // Unsubscribe old
        }
        echoListener = convId;
        if (window.Echo) {
            window.Echo.private('chat.' + convId)
                .listen('MessageSent', function(e) {
                    $('#chat-messages').append(renderMessage(e.message));
                    $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);
                });
        }
    }

    // Init for first load
    if (currentConversationId) {
        setupRealTimeListener(currentConversationId);
    }
});
</script>

<style>
.message { padding: 10px; border-radius: 10px; margin-bottom: 10px; }
.sent { background: #007bff; color: white; text-align: right; }
.received { background: #e9ecef; text-align: left; }
.h-100 { height: 100%; }
.flex-grow-1 { flex-grow: 1; }
.mb-3 { margin-bottom: 1rem; }
.text-end { text-align: right; }
.rounded { border-radius: 0.5rem; }
.opacity-75 { opacity: 0.75; }
.input-group textarea { resize: none; }
</style>
@endsection