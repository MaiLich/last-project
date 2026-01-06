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
document.addEventListener('DOMContentLoaded',function(){

window.conversationId = {{ isset($conversation) && $conversation ? $conversation->id : 'null' }};

const box = document.getElementById('chatbot-messages');
const input = document.getElementById('chatbot-input');
const sendBtn = document.getElementById('chatbot-send');
const csrf = document.getElementById('csrf-token').value;

function scroll(){box.scrollTop = box.scrollHeight;}

function render(text,type,time=null){
    const el=document.createElement('div');
    el.className='message '+type;
    const t=time?new Date(time):new Date();
    el.innerHTML=`${text}<div class="message-time">${t.getHours()}:${String(t.getMinutes()).padStart(2,'0')}</div>`;
    box.appendChild(el);
    scroll();
}

function load(){
    if(!conversationId) return;
    fetch('/chat/messages/'+conversationId)
    .then(r=>r.json())
    .then(data=>{
        box.innerHTML='';
        data.forEach(m=>{
            render(m.message,m.sender_type==='App\\\\Models\\\\User'?'sent':'received',m.created_at);
        });
        scroll();
    });
}

function send(){
    const msg=input.value.trim();
    if(!msg) return;

    render(msg,'sent');
    input.value='';

    fetch('/chat/send',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
        body:JSON.stringify({conversation_id:conversationId,message:msg})
    })
    .then(r=>r.json())
    .then(d=>{ if(d.conversation_id) conversationId=d.conversation_id; });
}

sendBtn.onclick=send;
input.addEventListener('keydown',e=>{if(e.key==='Enter') send();});

load();

if(window.Echo && conversationId){
    window.Echo.private('chat.'+conversationId)
        .listen('MessageSent',e=>{
            render(e.message.message,'received',e.message.created_at);
        });
}

});
</script>

@endsection
