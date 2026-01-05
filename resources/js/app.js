// resources/js/app.js
import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './chatbot';

$(document).ready(function() {
    console.log('Chat JS ready');
    // Lấy ID từ global variables (truyền từ Blade)
    let conversationId = window.conversationId || 1;
    let currentUserId = window.currentUserId || 0;

    // Mở/đóng chat
    $('#open-chat').click(function() {
        $('#chat-window').show();
        loadMessages();  // Tải lại tin nhắn khi mở
    });

    $('#close-chat').click(function() {
        $('#chat-window').hide();
    });

    // Draggable (cải thiện mượt hơn, giống Facebook)
    $('#chat-header').on('mousedown', function(e) {
        e.preventDefault();  // Ngăn select text
        let $chatWindow = $('#chat-window');
        let offset = $chatWindow.offset();
        let dx = e.clientX - offset.left;
        let dy = e.clientY - offset.top;

        $(document).on('mousemove.chat', function(e) {
            $chatWindow.css({
                position: 'fixed',
                left: Math.max(0, e.clientX - dx),  // Không cho ra ngoài màn hình
                top: Math.max(0, e.clientY - dy)
            });
        });

        $(document).one('mouseup.chat', function() {
            $(document).off('.chat');  // Cleanup events
        });
    });

    // Hàm load tin nhắn
    function loadMessages() {
    $.get('/chat/messages/' + conversationId)
        .done(function(messages) {
            console.log('Loaded messages:', messages);  // Debug: In array tin nhắn
            $('#chat-body').empty();
            messages.forEach(function(msg) {
                let className = (msg.sender_type === 'App\\Models\\User') ? 'sent' : 'received';
                $('#chat-body').append('<div class="message ' + className + '">' + escapeHtml(msg.message) + '</div>');
            });
            $('#chat-body').scrollTop( $('#chat-body')[0].scrollHeight);
        })
        .fail(function(xhr) {
            console.error('Load fail:', xhr.responseText);
        });
    }
    // Gọi load khi mở chat và sau gửi
$('#open-chat').click(function() {
    $('#chat-window').show();
    loadMessages();
});

    // Gửi tin nhắn (hỗ trợ Enter)
    $('#send-message').click(function() {
    let message = $('#message-input').val().trim();
    if (message) {
        console.log('Sending message:', message);  // Debug
        $.ajax({
            url: '/chat/send',
            type: 'POST',
            data: {
                conversation_id: conversationId,
                message: message,
                _token: $('#csrf-token').val()  // CSRF token (thêm vào view nếu cần parse)
            },
            success: function(data) {
                $('#message-input').val('');
                loadMessages();
            },
            error: function(xhr) {
                console.error('Send fail:', xhr.status, xhr.responseText);  // In lỗi 404/419/500
                if (xhr.status === 419) {
                    alert('CSRF token expired. Reload page!');
                }
            }
        });
    }
});

    // Real-time (kiểm tra Echo tồn tại)
    if (window.Echo) {
        window.Echo.private('chat.' + conversationId)
            .listen('MessageSent', function(e) {
                loadMessages();
            });
    } else {
        console.warn('Echo chưa load, chat không real-time');
    }

    // Tải tin nhắn ban đầu (khi script chạy)
    loadMessages();
});

// Hàm helper chống XSS
function escapeHtml(text) {
    let map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}