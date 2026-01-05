import pandas as pd
import re
import os
import math
import json
import random
from unidecode import unidecode
from rapidfuzz import fuzz
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_community.vectorstores import Chroma

# ================= SYNONYM & KEYWORDS MỞ RỘNG =================

SYNONYMS = {
    "áo phông": "áo thun",
    "áo phông tay dài": "áo thun dài tay",
    "áo ấm": "áo len áo nỉ áo khoác áo phao",
    "áo khoác nhẹ": "áo khoác mỏng áo gió cardigan",
    "đi làm văn phòng": "đi làm công sở văn phòng",
    "dạo phố": "đi chơi dạo phố hàng ngày",
    "đi tiệc": "hẹn hò date tiệc tùng",
    "gym tập gym": "thể thao gym tập thể dục",
    "du lịch đi chơi xa": "du lịch travel phượt",
    "ở nhà mặc nhà": "ở nhà bộ mặc nhà đồ ngủ",
    "trời lạnh mùa đông": "mùa đông trời lạnh rét",
    "nóng bức oi bức": "hè nóng oi bức",
    # Từ khóa ví tiền
    "giàu có": "nhiều tiền giàu sang sang chảnh high-end luxury",
    "khá giả": "trung bình khá giả có tiền chút",
    "tiết kiệm": "nghèo bình dân ít tiền tiết kiệm giá rẻ rẻ tiền dưới 500k",
}

INTENT_MAP = {
    "winter": ["mùa đông", "trời lạnh", "đông", "lạnh", "rét", "mùa lạnh", "thời tiết lạnh", "mùa rét"],
    "summer": ["mùa hè", "nóng", "oi bức", "nóng bức", "hè", "thời tiết nóng", "nắng nóng"],
    "autumn": ["mùa thu", "thu", "giao mùa", "mát mẻ", "se lạnh"],
    "spring": ["mùa xuân", "xuân", "mùa xuân hè", "xuân hè"],

    "work": ["đi làm", "công sở", "văn phòng", "office", "đi làm văn phòng", "làm việc"],
    "home": ["ở nhà", "mặc nhà", "ở nhà mặc", "nghỉ ngơi", "ở nhà chill"],
    "casual": ["đi chơi", "dạo phố", "đi dạo", "hàng ngày", "thường ngày", "daily"],
    "sport": ["thể thao", "gym", "tập gym", "chạy bộ", "tập thể dục", "sport"],
    "travel": ["du lịch", "đi chơi xa", "đi phượt", "đi chơi", "travel"],
    "date": ["hẹn hò", "đi tiệc", "tiệc tùng", "date", "đi chơi với bạn trai", "đi chơi với bạn gái", "đi hẹn"]
}

CONTEXT_KEYWORDS = {
    "winter": ["áo nỉ", "áo len", "áo khoác", "hoodie", "áo phao", "áo ấm", "áo dạ", "áo khoác dày", "áo lông"],
    "summer": ["áo thun", "áo phông", "quần short", "váy", "đầm", "ba lỗ", "tank top", "áo croptop"],
    "autumn": ["áo khoác mỏng", "cardigan", "áo dài tay", "áo khoác nhẹ", "áo gió", "áo khoác bomber"],
    "spring": ["áo sơ mi", "áo thun dài tay", "áo khoác mỏng", "áo blouse"],

    "work": ["áo sơ mi", "quần tây", "vest", "quần âu", "áo blouse", "áo vest"],
    "home": ["bộ mặc nhà", "đồ ngủ", "pijama", "đồ bộ", "đồ mặc nhà"],
    "casual": ["áo thun", "hoodie", "jeans", "quần jogger", "quần short"],
    "sport": ["đồ thể thao", "giày thể thao", "quần short thể thao", "legging", "áo thể thao"],
    "travel": ["áo khoác nhẹ", "quần short", "áo thun", "balo", "giày sneaker"],
    "date": ["váy", "đầm", "áo kiểu", "áo hai dây", "chân váy", "áo croptop", "áo ôm"]
}

TOP_KEYWORDS = ["áo", "hoodie", "len", "nỉ", "sơ mi", "áo khoác", "áo thun", "áo blouse"]
BOTTOM_KEYWORDS = ["quần", "jeans", "kaki", "váy", "short", "đầm", "chân váy"]

PRODUCT_SCORES = {
    "áo": 100,
    "hoodie": 95,
    "len": 95,
    "nỉ": 95,
    "áo khoác": 90,
    "áo thun": 90,
    "quần": 80,
    "váy": 85,
    "đầm": 85,
    "giày": 70,
    "phụ kiện": 40,
    "mũ": 30,
    "đồng hồ": 30
}

UNISEX = ["unisex", "cả nam và nữ", "nam nữ"]

WALLET_LEVELS = {
    "giàu": ["giàu", "nhiều tiền", "sang chảnh", "high-end", "luxury", "không quan tâm giá", "không ngại giá"],
    "khá": ["khá giả", "trung bình", "khá", "có tiền chút", "trên 500k", "trên 500"],
    "tiết kiệm": ["tiết kiệm", "nghèo", "bình dân", "rẻ", "ít tiền", "giá rẻ", "dưới 500k", "dưới 500"]
}

# ================= HÀM HỖ TRỢ =================

def normalize_text(text: str) -> str:
    text = text.lower()
    text = unidecode(text)
    for key, value in SYNONYMS.items():
        text = text.replace(unidecode(key.lower()), unidecode(value.lower()))
    text = " ".join(text.split())
    return text

def fuzzy_match(keyword: str, text: str, threshold: int = 85) -> bool:
    norm_keyword = unidecode(keyword.lower())
    words = text.split()
    for word in words:
        if fuzz.ratio(norm_keyword, word) >= threshold:
            return True
    if fuzz.partial_ratio(norm_keyword, text) >= threshold:
        return True
    return False

def fuzzy_any(keywords: list[str], text: str) -> bool:
    norm_text = normalize_text(text)
    return any(fuzzy_match(k, norm_text) for k in keywords)

# ================= LOAD DB =================

DB_PATH = "chroma_db"
_VECTORDB_CACHE = None

def get_vectordb():
    global _VECTORDB_CACHE
    if _VECTORDB_CACHE is None:
        embeddings = HuggingFaceEmbeddings(
            model_name="sentence-transformers/all-MiniLM-L6-v2"
        )
        _VECTORDB_CACHE = Chroma(
            persist_directory=DB_PATH,
            embedding_function=embeddings
        )
    return _VECTORDB_CACHE

# ================= LOAD DATA (Fallback nếu DB lỗi) =================

def load_products(csv_path="products.csv"):
    base = os.path.dirname(os.path.abspath(__file__))
    path = os.path.join(base, csv_path)

    df = pd.read_csv(path)
    df["price"] = pd.to_numeric(df["price"], errors="coerce")
    df = df.dropna(subset=["price"])
    df["price"] = df["price"].astype(int)

    def parse_size(val):
        try:
            return json.loads(val.replace("'", '"'))
        except:
            return []

    df["size"] = df["size"].apply(parse_size)
    return df.to_dict(orient="records")

_PRODUCTS_CACHE = None

def get_products_cached():
    global _PRODUCTS_CACHE
    if _PRODUCTS_CACHE is None:
        _PRODUCTS_CACHE = load_products()
    return _PRODUCTS_CACHE

# ================= SIZE INTENT =================

def detect_size_intent(text):
    text = normalize_text(text)
    return bool(re.search(r"\d+\s*cm", text) and re.search(r"\d+\s*kg", text))

def parse_height_weight(text):
    h = re.search(r"(\d+)\s*cm", text.lower())
    w = re.search(r"(\d+)\s*kg", text.lower())
    return (int(h.group(1)) if h else None, int(w.group(1)) if w else None)

def suggest_size(height, weight):
    if not height or not weight:
        return None
    if height >= 175:
        if weight <= 60: return "M"
        if weight <= 75: return "L"
        return "XL"
    if height >= 165:
        if weight <= 55: return "S"
        if weight <= 70: return "M"
        return "L"
    return "S"

def filter_by_size(products, size):
    if not size:
        return products
    return [p for p in products if size in p.get("size", [])]

# ================= INTENT DETECTION =================

def detect_contexts(text):
    norm_text = normalize_text(text)
    contexts = []
    for ctx, keys in INTENT_MAP.items():
        if fuzzy_any(keys, norm_text):
            contexts.append(ctx)
    return contexts

def detect_gender(text):
    norm_text = normalize_text(text)
    if fuzzy_any(["nam", "con trai", "dành cho nam", "nam giới"], norm_text):
        return "nam"
    if fuzzy_any(["nữ", "con gái", "dành cho nữ", "nữ giới"], norm_text):
        return "nữ"
    if fuzzy_any(["trẻ em", "bé", "trẻ con", "kid", "em bé"], norm_text):
        return "trẻ em"
    return None

def detect_budget(text):
    norm_text = normalize_text(text)
    m = re.search(r"(\d+)\s*(k|tr|trieu|củ)", norm_text)
    if not m:
        return None
    value = int(m.group(1))
    unit = m.group(2)
    return value * (1_000_000 if unit in ["tr", "trieu", "củ"] else 1_000)

def detect_wallet_level(text):
    norm_text = normalize_text(text)
    if fuzzy_any(WALLET_LEVELS["giàu"], norm_text):
        return "giàu"
    if fuzzy_any(WALLET_LEVELS["khá"], norm_text):
        return "khá"
    if fuzzy_any(WALLET_LEVELS["tiết kiệm"], norm_text):
        return "tiết kiệm"
    return None

# ================= FILTER =================

def filter_by_gender(products, gender):
    if not gender:
        return products
    filtered = []
    for p in products:
        cat = p["category"].lower()
        name = p["name"].lower()
        if gender in cat or gender in name:
            filtered.append(p)
            continue
        if any(u in cat or u in name for u in UNISEX):
            filtered.append(p)
    return filtered

def filter_by_budget(products, budget):
    if not budget:
        return products
    return [p for p in products if p["price"] <= budget]

def filter_by_contexts(products, contexts):
    if not contexts:
        return products
    result = products
    for ctx in contexts:
        keys = CONTEXT_KEYWORDS.get(ctx)
        if not keys:
            continue
        tmp = []
        for p in result:
            if any(fuzzy_match(k, normalize_text(p["name"]), threshold=80) for k in keys):
                tmp.append(p)
        if tmp:
            result = tmp
    return result if result else products

# ================= SCORE & SORT =================

def score_product(p):
    name = normalize_text(p["name"])
    cat = p["category"].lower()
    score = 50
    for k, v in PRODUCT_SCORES.items():
        if fuzzy_match(k, name) or k in cat:
            score = max(score, v)
    return score

def sort_products(products):
    for p in products:
        p["score"] = score_product(p)
    return sorted(products, key=lambda x: x["score"], reverse=True)

# ================= COMBO & OUTFIT THÔNG MINH =================

def get_price_category(total_price):
    if total_price >= 1_000_000:
        return "giàu", "sang trọng, chất lượng cao cấp, đẳng cấp"
    elif total_price >= 500_000:
        return "khá", "hiện đại, chất lượng tốt, phong cách"
    else:
        return "tiết kiệm", "trẻ trung, năng động, giá cực hời, nghèo, khó khăn"

def build_smart_outfit(products, contexts, budget=None):
    if not products:
        return []

    sorted_prods = sorted(products, key=lambda x: x["price"])
    outfit = []
    total = 0
    max_budget = budget * 0.9 if budget else float('inf')

    has_winter = "winter" in contexts
    has_spring_autumn = any(c in contexts for c in ["spring", "autumn"])
    has_work = "work" in contexts
    has_date = "date" in contexts

    used_ids = set()

    # 1. Chọn TOP
    top_candidates = [p for p in sorted_prods if p["id"] not in used_ids
                      if any(fuzzy_match(k, normalize_text(p["name"])) for k in TOP_KEYWORDS)]
    if has_work:
        top_candidates = sorted(top_candidates,
                                key=lambda p: 100 if fuzzy_match("sơ mi", normalize_text(p["name"])) or "blouse" in p["name"].lower() else 0,
                                reverse=True)
    if has_date:
        top_candidates = sorted(top_candidates,
                                key=lambda p: 100 if "áo kiểu" in p["name"].lower() or "croptop" in p["name"].lower() else 0,
                                reverse=True)

    top = next((p for p in top_candidates if total + p["price"] <= max_budget), None)
    if top:
        outfit.append(top)
        total += top["price"]
        used_ids.add(top["id"])

    # 2. Chọn BOTTOM
    bottom_candidates = [p for p in sorted_prods if p["id"] not in used_ids
                         if any(fuzzy_match(k, normalize_text(p["name"])) for k in BOTTOM_KEYWORDS)]
    if has_work:
        bottom_candidates = sorted(bottom_candidates,
                                   key=lambda p: 100 if "quần tây" in p["name"].lower() or "quần âu" in p["name"].lower() or "chân váy" in p["name"].lower() else 0,
                                   reverse=True)

    bottom = next((p for p in bottom_candidates if total + p["price"] <= max_budget), None)
    if bottom:
        outfit.append(bottom)
        total += bottom["price"]
        used_ids.add(bottom["id"])

    # 3. Thêm áo khoác nếu mùa lạnh hoặc giao mùa
    if has_winter or has_spring_autumn:
        jacket_keys = CONTEXT_KEYWORDS["winter"] if has_winter else (CONTEXT_KEYWORDS.get("autumn", []) + CONTEXT_KEYWORDS.get("spring", []))
        jacket_candidates = [p for p in sorted_prods if p["id"] not in used_ids
                             if any(fuzzy_match(k, normalize_text(p["name"])) for k in jacket_keys)]
        jacket = next((p for p in jacket_candidates if total + p["price"] <= max_budget), None)
        if jacket:
            outfit.append(jacket)

    return outfit

def build_combo(products, budget=None, max_items=5):
    if not products:
        return []
    if not budget:
        return random.sample(products[:10], min(max_items, len(products)))
    combo = []
    total = 0
    for p in sorted(products, key=lambda x: x["price"]):
        if len(combo) >= max_items:
            break
        if total + p["price"] <= budget:
            combo.append(p)
            total += p["price"]
    return combo if combo else products[:2]

# ================= YOU MAY LIKE =================

def recommend_you_may_like(products, k=4):
    high_score = [p for p in products if p.get("score", 0) >= 85]
    if not high_score:
        return []
    return random.sample(high_score, min(len(high_score), k))

# ================= SAFE JSON =================

def clean_for_json(obj):
    if isinstance(obj, float):
        if math.isnan(obj) or math.isinf(obj):
            return 0
        return obj
    if isinstance(obj, dict):
        return {k: clean_for_json(v) for k, v in obj.items()}
    if isinstance(obj, list):
        return [clean_for_json(v) for v in obj]
    return obj

# ================= MAIN FUNCTION =================

def fashion_chat(user_message: str):
    # Load DB
    vectordb = get_vectordb()

    # Ưu tiên size
    if detect_size_intent(user_message):
        height, weight = parse_height_weight(user_message)
        size = suggest_size(height, weight)
        # Query DB với size
        query = normalize_text(user_message) + f" size {size}"
        docs = vectordb.similarity_search_with_score(query, k=50)
        products_size = [doc[0].metadata for doc in docs if doc[1] > 0.5]  # Lấy metadata, filter score
        products_size = filter_by_size(products_size, size)
        products_size = sort_products(products_size)
        return clean_for_json({
            "answer": f"Với chiều cao {height}cm và cân nặng {weight}kg, mình khuyên bạn chọn size {size} 👕",
            "suggested_size": size,
            "products": products_size[:6],
            "you_may_like": recommend_you_may_like(products_size)
        })

    # Detect intent
    gender = detect_gender(user_message)
    budget = detect_budget(user_message)
    wallet_level = detect_wallet_level(user_message)
    contexts = detect_contexts(user_message)

    # Build query cho DB từ user_message + intents
    query_parts = [normalize_text(user_message)]
    if gender:
        query_parts.append(gender)
    if budget:
        query_parts.append(f"giá dưới {budget}")
    if contexts:
        query_parts.extend([", ".join(CONTEXT_KEYWORDS.get(ctx, [])) for ctx in contexts])
    query = " ".join(query_parts)

    # Query DB
    docs = vectordb.similarity_search_with_score(query, k=50)  # Top 50 để lọc tiếp
    products = [doc[0].metadata for doc in docs if doc[1] > 0.5]  # Lấy metadata (id, name, price, etc.), filter low score

    if not products:
        # Fallback tải CSV nếu DB không match
        products = get_products_cached()

    # Filter như cũ
    products = filter_by_gender(products, gender)
    products = filter_by_budget(products, budget)
    products = filter_by_contexts(products, contexts)

    # Dedup
    seen = set()
    uniq = []
    for p in products:
        if p["id"] not in seen:
            uniq.append(p)
            seen.add(p["id"])
    products = uniq

    products = sort_products(products)

    if len(products) < 2:
        return {
            "answer": "Yêu cầu của bạn hơi đặc biệt quá hoặc mình chưa tìm được món phù hợp 😥 Hãy chat trực tiếp với admin để được tư vấn chi tiết hơn nhé!",
            "products": [],
            "need_admin": True
        }

    # Build outfit & combo
    outfit = build_smart_outfit(products, contexts, budget)
    combo = build_combo(products, budget)

    outfit_total = sum(p["price"] for p in outfit)
    _, style_desc = get_price_category(outfit_total)

    # Xác định giọng điệu trả lời
    effective_wallet = wallet_level or get_price_category(outfit_total)[0]

    if effective_wallet == "giàu":
        answer = f"Mình gợi ý cho bạn một outfit {style_desc} cực kỳ đẳng cấp và chất lượng "
    elif effective_wallet == "khá":
        answer = f"Đây là set đồ {style_desc} mà mình thấy hợp với bạn nhất "
    else:
        answer = f"Mình chọn cho bạn outfit {style_desc} "

    # Thêm hoàn cảnh nếu có
    if contexts:
        context_names = {
            "winter": "mùa đông",
            "summer": "mùa hè",
            "autumn": "mùa thu",
            "spring": "mùa xuân",
            "work": "đi làm văn phòng",
            "casual": "dạo phố/hàng ngày",
            "date": "hẹn hò/đi tiệc",
            "sport": "tập gym/thể thao",
            "travel": "du lịch",
            "home": "mặc nhà"
        }
        displayed = [context_names.get(c, c) for c in contexts]
        answer = f"Dành cho {', '.join(displayed)} – " + answer

    return clean_for_json({
        "answer": answer,
        "products": products[:6],
        "outfit_products": outfit,
        "budget_combo": combo,
        "you_may_like": recommend_you_may_like(products),
        "outfit_total_price": outfit_total,
        "need_admin": False
    })