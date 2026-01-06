@extends('front.layout.layout')

@section('content')
<div class="container my-4">
    <h3 class="mb-3">Trợ lý Chatbot</h3>

    <div id="chatbot-app" class="card">

        {{-- CHÚ THÍCH / INTRO CHATBOT --}}
        <div id="chatbot-intro" class="px-3 py-2 border-bottom bg-light">
            <div class="d-flex align-items-start gap-2">
                <div style="font-size:20px;">🤖</div>
                <div>
                    <div class="fw-semibold">Chatbot hỗ trợ tự động</div>
                    <div class="text-muted" style="font-size:13px; line-height:1.4;">
                        Tôi có thể hỗ trợ giải đáp câu hỏi,đưa ra sản phẩm theo yêu cầu, tư vấn size khi bạn nhập chiều cao, cân nặng và trả lời nhanh 24/7.
                        <br>
                        <span class="text-warning">Lưu ý:</span> Câu trả lời mang tính tham khảo. Nếu thấy câu trả lời không phù hợp, vui lòng liên hệ với admin.
                    </div>
                </div>
            </div>
        </div>

        {{-- VÙNG TIN NHẮN --}}
        <div
            class="card-body"
            style="height:420px; overflow:auto;"
            id="chatbot-messages">
        </div>

        {{-- Ô NHẬP --}}
        <div class="card-footer d-flex gap-2">
            <input
                id="chatbot-input"
                class="form-control"
                placeholder="Nhập tin nhắn..."
            />
            <button
                id="chatbot-send"
                class="btn btn-primary">
                Gửi
            </button>
        </div>
    </div>
</div>

<script>
    window.CHATBOT_BASE_URL = @json($chatbotBaseUrl);
</script>
<script>
    window.CHATBOT_BASE_URL = "http://127.0.0.1:8001";
</script>
<script src="/js/chatbot.js"></script>

{{-- TÙY CHỌN: ẨN INTRO KHI BẮT ĐẦU CHAT --}}
<script>
    document.getElementById('chatbot-send')?.addEventListener('click', () => {
        const intro = document.getElementById('chatbot-intro');
        if (intro) intro.style.display = 'none';
    });
</script>
@endsection
