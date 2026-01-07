document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("chatbot-input");
    const sendBtn = document.getElementById("chatbot-send");
    const box = document.getElementById("chatbot-messages");

    const scrollBottom = () => box.scrollTop = box.scrollHeight;
    const price = v => new Intl.NumberFormat("vi-VN").format(v) + "đ";

    /* ======================
       MESSAGE UI
    ====================== */

    const userMsg = txt => {
        box.innerHTML += `
            <div class="text-end mb-3">
                <div class="small text-muted mb-1">Bạn</div>
                <div class="d-inline-block px-3 py-2 rounded-3 text-white"
                     style="background:#0d6efd; max-width:75%;">
                    ${txt}
                </div>
            </div>`;
        scrollBottom();
    };

    const botMsg = txt => {
        box.innerHTML += `
            <div class="text-start mb-3">
                <div class="small text-muted mb-1">Chatbot</div>
                <div class="d-inline-block px-3 py-2 rounded-3 bg-light text-dark"
                     style="max-width:75%;">
                    ${txt}
                </div>
            </div>`;
        scrollBottom();
    };

    /* ======================
       PRODUCT LIST (NO IMAGE)
       CTA ON THE RIGHT
    ====================== */

    const renderProducts = (title, list = []) => {
        if (!Array.isArray(list) || list.length === 0) return;

        let html = `
        <div class="mt-3">
            <div class="fw-bold mb-2">${title}</div>
            <div class="list-group">`;

        list.forEach(p => {
            html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">${p.name}</div>
                    <div class="text-danger fw-bold">${price(p.price)}</div>
                </div>
                <a href="/product/${p.slug ?? p.id}"
                   class="btn btn-sm btn-outline-primary">
                   Xem chi tiết →
                </a>
            </div>`;
        });

        html += `</div></div>`;
        box.innerHTML += html;
        scrollBottom();
    };

    /* ======================
       SEND MESSAGE
    ====================== */

    async function send() {
        const text = input.value.trim();
        if (!text) return;

        userMsg(text);
        input.value = "";
        sendBtn.disabled = true;

        try {
            const res = await fetch(`${window.CHATBOT_BASE_URL}/chat`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            console.log("CHATBOT RESPONSE:", data);

            if (data.answer) botMsg(data.answer);

            renderProducts("Outfit gợi ý", data.outfit_products);

            if (data.outfit_total_price) {
                botMsg("Tổng outfit: " + price(data.outfit_total_price));
            }

            renderProducts("Combo tiết kiệm", data.budget_combo);
            renderProducts("Sản phẩm phù hợp", data.products);
            renderProducts("Có thể bạn thích", data.you_may_like);

            if (data.need_admin) {
                botMsg("Trường hợp này bạn nên chat trực tiếp với admin nhé!");
            }

        } catch (e) {
            console.error(e);
            botMsg("Có lỗi xảy ra, vui lòng thử lại.");
        } finally {
            sendBtn.disabled = false;
        }
    }

    sendBtn.onclick = send;
    input.addEventListener("keydown", e => e.key === "Enter" && send());
});
