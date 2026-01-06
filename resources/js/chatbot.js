document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("chatbot-input");
    const sendBtn = document.getElementById("chatbot-send");
    const box = document.getElementById("chatbot-messages");

    const scrollBottom = () => box.scrollTop = box.scrollHeight;

    const price = v => new Intl.NumberFormat("vi-VN").format(v) + "đ";

    const userMsg = txt => {
        box.innerHTML += `
            <div class="text-end mb-2">
                <span class="badge bg-primary p-2">${txt}</span>
            </div>`;
        scrollBottom();
    };

    const botMsg = txt => {
        box.innerHTML += `
            <div class="mb-2">
                <span class="badge bg-secondary p-2">${txt}</span>
            </div>`;
        scrollBottom();
    };

    const renderProducts = (title, list = []) => {
        if (!Array.isArray(list) || list.length === 0) return;

        let html = `<div class="mt-3">
            <h6 class="fw-bold">${title}</h6>
            <div class="row g-2">`;

        list.forEach(p => {
            html += `
            <div class="col-6 col-md-4">
                <div class="card h-100">
                    <img src="${p.image ?? '/images/no-image.png'}"
                         class="card-img-top"
                         style="height:130px;object-fit:cover">
                    <div class="card-body p-2">
                        <div class="small fw-bold">${p.name}</div>
                        <div class="text-danger fw-bold">${price(p.price)}</div>
                        <a href="/product/${p.slug ?? p.id}"
                           class="btn btn-sm btn-outline-primary w-100 mt-1">
                           Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>`;
        });

        html += `</div></div>`;
        box.innerHTML += html;
        scrollBottom();
    };

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
            console.log("CHATBOT RESPONSE:", data); // 👈 RẤT QUAN TRỌNG

            // 1️⃣ Answer LUÔN render
            if (data.answer) {
                botMsg(data.answer);
            }

            // 2️⃣ Outfit
            renderProducts("👗 Outfit gợi ý", data.outfit_products);

            if (data.outfit_total_price) {
                botMsg("💰 Tổng outfit: " + price(data.outfit_total_price));
            }

            // 3️⃣ Combo
            renderProducts("🎁 Combo tiết kiệm", data.budget_combo);

            // 4️⃣ Sản phẩm chính
            renderProducts("🛍️ Sản phẩm phù hợp", data.products);

            // 5️⃣ You may like
            renderProducts("✨ Có thể bạn thích", data.you_may_like);

            // 6️⃣ Admin
            if (data.need_admin) {
                botMsg("👉 Trường hợp này bạn nên chat trực tiếp với admin nhé!");
            }

        } catch (e) {
            console.error(e);
            botMsg("❌ Có lỗi xảy ra, vui lòng thử lại.");
        } finally {
            sendBtn.disabled = false;
        }
    }

    sendBtn.onclick = send;
    input.addEventListener("keydown", e => e.key === "Enter" && send());
});
