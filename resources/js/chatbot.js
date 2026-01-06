document.addEventListener("DOMContentLoaded", function () {
  function el(id) { return document.getElementById(id); }

  const messagesBox = el("chatbot-messages");
  const input = el("chatbot-input");
  const sendBtn = el("chatbot-send");

  if (!messagesBox || !input || !sendBtn) {
    console.error("Chatbot DOM not found:", { messagesBox, input, sendBtn });
    return;
  }

  // ---- helpers ----
  function toNumber(v) {
    const n = Number(v);
    return Number.isFinite(n) ? n : null;
  }

  function formatPrice(v) {
    const n = toNumber(v);
    if (n === null) return "";
    return n.toLocaleString("vi-VN") + "đ";
  }

  function safeText(v) {
    return (v === null || v === undefined) ? "" : String(v);
  }

  function appendMessage(role, text) {
    const wrap = document.createElement("div");
    wrap.className = "mb-2";

    const badge = document.createElement("span");
    badge.className = role === "user" ? "badge bg-secondary" : "badge bg-success";
    badge.textContent = role === "user" ? "Bạn" : "Bot";

    const msg = document.createElement("div");
    msg.className = "mt-1";
    msg.innerText = safeText(text);

    wrap.appendChild(badge);
    wrap.appendChild(msg);
    messagesBox.appendChild(wrap);
    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  function uniqById(arr) {
    if (!Array.isArray(arr)) return [];
    const seen = new Set();
    const out = [];
    for (const p of arr) {
      const id = p?.id ?? `${p?.name ?? ""}-${p?.price ?? ""}`;
      if (seen.has(id)) continue;
      seen.add(id);
      out.push(p);
    }
    return out;
  }

  function renderProductsList(titleText, products, opts = {}) {
    const { limit = 8, showTotal = false, totalPrice = null } = opts;

    const listData = uniqById(products);
    if (!listData.length) return;

    const title = document.createElement("div");
    title.className = "mt-3 fw-bold";
    title.innerText = titleText;
    messagesBox.appendChild(title);

    const ul = document.createElement("ul");
    ul.className = "mt-2";

    const slice = listData.slice(0, limit);
    slice.forEach((p) => {
      const li = document.createElement("li");

      const name = safeText(p?.name);
      const price = formatPrice(p?.price);
      const color = safeText(p?.color);

      // thêm chút meta nhưng vẫn gọn
      const size = safeText(p?.size);
      const cat = safeText(p?.category);
      const section = safeText(p?.section);

      const metaParts = [];
      if (section) metaParts.push(section);
      if (cat) metaParts.push(cat);
      if (size) metaParts.push(`Size: ${size}`);

      const meta = metaParts.length ? ` (${metaParts.join(" • ")})` : "";

      li.innerText = `${name}${meta} • ${price || "—"} ${color ? ` • ${color}` : ""}`;
      ul.appendChild(li);
    });

    messagesBox.appendChild(ul);

    if (showTotal) {
      const total =
        (toNumber(totalPrice) !== null)
          ? toNumber(totalPrice)
          : slice.reduce((s, p) => s + (toNumber(p?.price) ?? 0), 0);

      const totalLine = document.createElement("div");
      totalLine.className = "mt-2";
      totalLine.innerHTML = `<span class="badge bg-dark">Tổng: ${formatPrice(total) || "0đ"}</span>`;
      messagesBox.appendChild(totalLine);
    }

    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  function renderNeedAdmin(needAdmin) {
    if (!needAdmin) return;
    const box = document.createElement("div");
    box.className = "alert alert-warning mt-3 mb-0";
    box.innerText = "Không đủ sản phẩm phù hợp. Bạn nên liên hệ admin để được tư vấn kỹ hơn.";
    messagesBox.appendChild(box);
    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  function renderResponse(data) {
    // answer
    appendMessage("bot", data?.answer ?? "(Không có answer)");

    // nếu là nhánh tư vấn size
    if (data?.suggested_size) {
      const sz = document.createElement("div");
      sz.className = "mt-2";
      sz.innerHTML = `<span class="badge bg-info">Size gợi ý: ${safeText(data.suggested_size)}</span>`;
      messagesBox.appendChild(sz);
    }

    // outfit + total (nếu có)
    renderProductsList(
      "Set đồ gợi ý:",
      data?.outfit_products,
      { showTotal: true, totalPrice: data?.outfit_total_price }
    );

    // combo ngân sách
    renderProductsList(
      "Combo theo ngân sách:",
      data?.budget_combo,
      { showTotal: true }
    );

    // you may like
    renderProductsList(
      "Bạn có thể thích:",
      data?.you_may_like,
      { limit: 6 }
    );

    // fallback: products
    renderProductsList(
      "Gợi ý sản phẩm:",
      data?.products,
      { limit: 8 }
    );

    renderNeedAdmin(Boolean(data?.need_admin));
  }

  async function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    appendMessage("user", text);
    input.value = "";
    sendBtn.disabled = true;

    try {
      const base = window.CHATBOT_BASE_URL || "http://127.0.0.1:8001";

      const res = await fetch(`${base}/chat`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: text })
      });

      if (!res.ok) {
        const t = await res.text();
        appendMessage("bot", `Lỗi API (${res.status}): ${t}`);
        return;
      }

      const data = await res.json();
      console.log("API response data:", data); // Debug: Kiểm tra JSON trả về
      renderResponse(data);

    } catch (e) {
      appendMessage("bot", `Không gọi được API: ${e.message}`);
      console.error(e);
    } finally {
      sendBtn.disabled = false;
    }
  }

  sendBtn.addEventListener("click", sendMessage);
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendMessage();
  });

  console.log("Chatbot JS ready ✅ (render outfit/budget/you_may_like)");
});