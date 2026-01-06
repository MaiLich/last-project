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

#Tăng độ nhận diện từ khóa bằng cách thêm từ đồng nghĩa
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
    "giàu có": "nhiều tiền giàu sang sang chảnh high-end luxury",
    "khá giả": "trung bình khá giả có tiền chút",
    "tiết kiệm": "nghèo bình dân ít tiền tiết kiệm giá rẻ rẻ tiền dưới 500k",
}
# Ngữ cảnh
INTENT_MAP = {
    "winter": ["mùa đông", "trời lạnh", "đông", "lạnh", "rét", "mùa lạnh", "thời tiết lạnh", "mùa rét"],
    "summer": ["nóng", "oi bức", "nóng bức", "hè", "thời tiết nóng", "nắng nóng"],
    "autumn": ["mùa thu", "thu", "giao mùa", "mát mẻ", "se lạnh"],
    "spring": ["mùa xuân", "xuân", "mùa xuân hè", "xuân hè"],

    "work": ["đi làm", "công sở", "văn phòng", "office", "đi làm văn phòng", "làm việc"],
    "home": ["ở nhà", "mặc nhà", "ở nhà mặc", "nghỉ ngơi", "ở nhà chill"],
    "casual": ["đi chơi", "dạo phố", "đi dạo", "hàng ngày", "thường ngày", "daily"],
    "sport": ["thể thao", "gym", "tập gym", "chạy bộ", "tập thể dục", "sport"],
    "travel": ["du lịch", "đi chơi xa", "đi phượt", "đi chơi", "travel"],
    "date": ["hẹn hò", "đi tiệc", "tiệc tùng", "date", "đi chơi với bạn trai", "đi chơi với bạn gái", "đi hẹn"]
}
#Chia đồ theo ngữ cảnh
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
#Từ khóa nhận diện trang phục trên và dưới
TOP_KEYWORDS = ["áo", "hoodie", "len", "nỉ", "sơ mi", "áo khoác", "áo thun", "áo blouse"]
BOTTOM_KEYWORDS = ["quần", "jeans", "kaki", "váy", "short", "đầm", "chân váy"]
ACCESSORY_KEYWORDS = ["giày", "phụ kiện", "mũ", "đồng hồ", "túi xách", "balo"]
#Mức độ ưu tiên sản phẩm
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
# Từ khóa nhận diện unisex
UNISEX = ["unisex", "cả nam và nữ", "nam nữ"]
# Mức độ chi tiêu
WALLET_LEVELS = {
    "giàu": ["giàu", "nhiều tiền", "sang chảnh", "high-end", "luxury", "không quan tâm giá", "không ngại giá", "cao cấp"],
    "khá": ["khá giả", "trung bình", "khá", "có tiền chút", "trên 500k", "trên 500"],
    "tiết kiệm": ["tiết kiệm", "nghèo", "bình dân", "rẻ", "ít tiền", "giá rẻ", "dưới 500k", "dưới 500"]
}


#nomalize text bằng cách chuyển về chữ thường, bỏ dấu và thay thế từ đồng nghĩa
def normalize_text(text: str) -> str:
    text = text.lower()
    text = unidecode(text)
    for key, value in SYNONYMS.items():
        text = text.replace(unidecode(key.lower()), unidecode(value.lower()))
    text = " ".join(text.split())
    return text
# So khớp từ khóa với ngưỡng nhất định
def fuzzy_match(keyword: str, text: str, threshold: int = 85) -> bool:
    norm_keyword = unidecode(keyword.lower())
    words = text.split()
    for word in words:
        if fuzz.ratio(norm_keyword, word) >= threshold:
            return True
    if fuzz.partial_ratio(norm_keyword, text) >= threshold:
        return True
    return False
# Kiểm tra có khớp bất kỳ từ khóa nào không
def fuzzy_any(keywords: list[str], text: str) -> bool:
    norm_text = normalize_text(text)
    return any(fuzzy_match(k, norm_text) for k in keywords)


#load vectordb
DB_PATH = "chroma_db"
_VECTORDB_CACHE = None
# Hàm lấy vectordb với cache
def get_vectordb():
    global _VECTORDB_CACHE
    if _VECTORDB_CACHE is None:
        embeddings = HuggingFaceEmbeddings(
            model_name="bkai-foundation-models/vietnamese-bi-encoder"
        )
        _VECTORDB_CACHE = Chroma(
            persist_directory=DB_PATH,
            embedding_function=embeddings
        )
    return _VECTORDB_CACHE


# Load sản phẩm từ CSV
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
#hàm chuyển size từ chuỗi JSON sang list
    df["size"] = df["size"].apply(parse_size)
    return df.to_dict(orient="records")
# Cache sản phẩm để tránh load nhiều lần
_PRODUCTS_CACHE = None
# Hàm lấy sản phẩm với cache
def get_products_cached():
    global _PRODUCTS_CACHE
    if _PRODUCTS_CACHE is None:
        _PRODUCTS_CACHE = load_products()
    return _PRODUCTS_CACHE


#Hàm nhận diện ý định hỏi về size
def detect_size_intent(text):
    text = normalize_text(text)
    return bool(re.search(r"\d+(?:\.\d+)?\s*(cm|dm|m)", text) and re.search(r"\d+(?:\.\d+)?\s*kg", text))
# Hàm phân tích chiều cao và cân nặng từ chuỗi
def parse_height_weight(text):
    text_lower = text.lower()
    h_match = re.search(r"(\d+(?:\.\d+)?)\s*(cm|dm|m)", text_lower)
    w_match = re.search(r"(\d+(?:\.\d+)?)\s*kg", text_lower)
    
    height_cm = None
    if h_match:
        value = float(h_match.group(1))
        unit = h_match.group(2)
        if unit == 'cm':
            height_cm = value
        elif unit == 'dm':
            height_cm = value * 10
        elif unit == 'm':
            height_cm = value * 100
    
    weight = float(w_match.group(1)) if w_match else None
    
    return (int(height_cm) if height_cm else None, int(weight) if weight else None)
#Ham gợi ý size dựa trên chiều cao và cân nặng
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
# Hàm lọc sản phẩm theo size
def filter_by_size(products, size):
    if not size:
        return products
    return [p for p in products if size in p.get("size", [])]


#Hàm nhận diện các ngữ cảnh từ văn bản
def detect_contexts(text):
    norm_text = normalize_text(text)
    contexts = []
    for ctx, keys in INTENT_MAP.items():
        if fuzzy_any(keys, norm_text):
            contexts.append(ctx)
    return contexts
#Hàm nhận diện giới tính từ văn bản
def detect_gender(text):
    norm_text = normalize_text(text)
    if fuzzy_any(["nam", "con trai", "dành cho nam", "nam giới", "phái mạnh"], norm_text):
        return "nam"
    if fuzzy_any(["nữ", "con gái", "dành cho nữ", "nữ giới", "nữ tính"], norm_text):
        return "nữ"
    if fuzzy_any(["trẻ em", "bé", "trẻ con", "kid", "em bé", "baby"], norm_text):
        return "trẻ em"
    return None
# Hàm nhận diện ngân sách từ văn bản
def detect_budget(text):
    norm_text = normalize_text(text)
    m = re.search(r"(\d+)\s*(k|tr|trieu|củ)", norm_text)
    if not m:
        return None
    value = int(m.group(1))
    unit = m.group(2)
    return value * (1_000_000 if unit in ["tr", "trieu", "củ"] else 1_000)
# Hàm nhận diện mức độ chi tiêu từ văn bản
def detect_wallet_level(text):
    norm_text = normalize_text(text)
    if fuzzy_any(WALLET_LEVELS["giàu"], norm_text):
        return "giàu"
    if fuzzy_any(WALLET_LEVELS["khá"], norm_text):
        return "khá"
    if fuzzy_any(WALLET_LEVELS["tiết kiệm"], norm_text):
        return "tiết kiệm"
    return None
# Hàm mới: Nhận diện số lượng từ văn bản (ví dụ: "gợi ý 3 áo", "hiển thị 5 sản phẩm")
def detect_quantity(text):
    norm_text = normalize_text(text)
    m = re.search(r"(gợi ý|hiển thị|tìm|cho xem|muốn xem)\s*(\d+)", norm_text)
    if m:
        return int(m.group(2))
    return None
# Hàm mới: Kiểm tra nếu chỉ hỏi về giá (không có ngữ cảnh khác)
def is_price_only_query(text, budget, contexts):
    if budget and not contexts:
        norm_text = normalize_text(text)
        price_keywords = ["giá", "dưới", "trên", "khoảng", "rẻ", "đắt"]
        if any(kw in norm_text for kw in price_keywords):
            return True
    return False

ROLE_KEYWORDS = {
    "top": TOP_KEYWORDS,
    "bottom": BOTTOM_KEYWORDS,
    "accessory": ACCESSORY_KEYWORDS
}
# Hàm nhận diện vai trò sản phẩm 
def detect_role(p):
    name = normalize_text(p["name"])
    for role, keys in ROLE_KEYWORDS.items():
        if any(fuzzy_match(k, name) for k in keys):
            return role
    return "other"
#Nhận diện loại trang phục từ văn bản người dùng
def detect_item_type(text):
    norm_text = normalize_text(text)
    for role, keys in ROLE_KEYWORDS.items():
        if fuzzy_any(keys, norm_text):
            return role
    return None


#Hàm lọc sản phẩm theo giới tính
def filter_by_gender(products, gender):
    if not gender:
        return products
    gender = normalize_text(gender)
    result = []
    for p in products:
        text = normalize_text(p["parent_category"] + " " + p["name"])
        if gender in text:
            result.append(p)
            continue
        if any(u in text for u in UNISEX):
            result.append(p)
    return result

#Hàm lọc sản phẩm theo số tiền
def filter_by_budget(products, max_budget=None, min_budget=None):
    if max_budget is None and min_budget is None:
        return products
    if min_budget:
        products = [p for p in products if p["price"] >= min_budget]
    if max_budget:
        products = [p for p in products if p["price"] <= max_budget]
    return products
#Hàm lọc sản phẩm theo ngữ cảnh
def filter_by_contexts(products, contexts):
    if not contexts:
        return products
    all_keys = set()
    for ctx in contexts:
        all_keys.update(CONTEXT_KEYWORDS.get(ctx, []))
    if not all_keys:
        return products
    return [p for p in products if any(fuzzy_match(k, normalize_text(p["name"])) for k in all_keys)]

# Hàm tính điểm ưu tiên sản phẩm
def score_product(p):
    name = normalize_text(p["name"])
    cat = p["category"].lower()
    score = 50
    for k, v in PRODUCT_SCORES.items():
        if fuzzy_match(k, name) or k in cat:
            score = max(score, v)
    return score
#Hàm sắp xếp 
def sort_products(products, wallet_level=None):
    for p in products:
        p["score"] = score_product(p)
    if wallet_level == "giàu":
        return sorted(products, key=lambda x: (-x["score"], -x["price"]))  
    elif wallet_level == "tiết kiệm":
        return sorted(products, key=lambda x: (-x["score"], x["price"])) 
    else:
        return sorted(products, key=lambda x: (-x["score"], x["price"])) 

#Lọc sản phẩm người lớn
def filter_adult_products(products):
    child_keywords = ["trẻ em", "bé", "trẻ con", "kid", "em bé", "baby"]
    return [p for p in products if not fuzzy_any(child_keywords, normalize_text(p["parent_category"] + " " + p["name"]))]


#Hàm phân loại mức giá theo tài chính
def get_price_category(total_price):
    if total_price >= 1_000_000:
        return "giàu", "sang trọng, chất lượng cao cấp, đẳng cấp"
    elif total_price >= 500_000:
        return "khá", "hiện đại, chất lượng tốt, phong cách"
    else:
        return "tiết kiệm", "trẻ trung, năng động, giá cực hời, nghèo, khó khăn"
#Ham xây dựng outfit dựa trên ngữ cảnh và ngân sách
def build_smart_outfit(products, contexts, budget=None):
    if not products:
        return []

    # Ưu tiên score trước, giá sau
    sorted_prods = sorted(products, key=lambda x: (-x.get("score", 0), x["price"]))

    outfit = []
    total = 0
    max_budget = budget * 0.9 if budget else float("inf")
    used_ids = set()

    #Tạo outfit trên
    top_candidates = [
        p for p in sorted_prods
        if p["id"] not in used_ids
        if any(fuzzy_match(k, normalize_text(p["name"])) for k in TOP_KEYWORDS)
    ]

    for ctx in contexts:
        priorities = CONTEXT_KEYWORDS.get(ctx)
        if priorities:
            top_candidates = sorted(
                top_candidates,
                key=lambda p: any(k in p["name"].lower() for k in priorities),
                reverse=True
            )

    top = next((p for p in top_candidates if total + p["price"] <= max_budget), None)
    if not top:
        return [] 

    outfit.append(top)
    total += top["price"]
    used_ids.add(top["id"])
    #Tạo outfit dưới
    bottom_candidates = [
        p for p in sorted_prods
        if p["id"] not in used_ids
        if any(fuzzy_match(k, normalize_text(p["name"])) for k in BOTTOM_KEYWORDS)
    ]

    for ctx in contexts:
        priorities = CONTEXT_KEYWORDS.get(ctx)
        if priorities:
            bottom_candidates = sorted(
                bottom_candidates,
                key=lambda p: any(k in p["name"].lower() for k in priorities),
                reverse=True
            )

    bottom = next((p for p in bottom_candidates if total + p["price"] <= max_budget), None)
    if not bottom:
        return []

    outfit.append(bottom)
    total += bottom["price"]
    used_ids.add(bottom["id"])

    #Tạo outfit theo mùa
    if "winter" in contexts or any(c in contexts for c in ["spring", "autumn"]):
        jacket_keys = (
            CONTEXT_KEYWORDS["winter"]
            if "winter" in contexts
            else CONTEXT_KEYWORDS["autumn"] + CONTEXT_KEYWORDS["spring"]
        )

        jackets = [
            p for p in sorted_prods
            if p["id"] not in used_ids
            if any(fuzzy_match(k, normalize_text(p["name"])) for k in jacket_keys)
            if total + p["price"] <= max_budget
        ]

        if jackets:
            jacket = jackets[0]
            outfit.append(jacket)
            total += jacket["price"]
            used_ids.add(jacket["id"])

    return outfit

#Hàm xây dựng combo sản phẩm trong ngân sách
def build_combo(products, budget=None, max_items=5):
    if not products:
        return []

#Ưu tiên điểm số trước, giá sau
    sorted_prods = sorted(
        products,
        key=lambda x: (-x.get("score", 0), x["price"])
    )

#Build combo không có ngân sách
    if budget is None:
        combo = []
        used_roles = set()

        for p in sorted_prods:
            role = detect_role(p)
            if role in used_roles:
                continue
            combo.append(p)
            used_roles.add(role)
            if len(combo) >= max_items:
                break

        return combo

# Build combo với ngân sách
    combo = []
    total = 0
    used_roles = set()

    for p in sorted_prods:
        if len(combo) >= max_items:
            break

        price = p["price"]
        if total + price > budget:
            continue

        role = detect_role(p)

        # Đảm bảo không trùng vai trò, trừ phụ kiện
        if role in used_roles and role != "accessory":
            continue

        combo.append(p)
        total += price
        used_roles.add(role)

    #Hàm đảm bảo combo có ít nhất 2 món
    if not combo:
        return sorted_prods[:min(2, len(sorted_prods))]
    return combo


#Hàm gợi ý sản phẩm 
def recommend_you_may_like(
    products,
    contexts=None,
    gender=None,
    exclude_ids=None,
    k=4,
    min_score=80
):
    if not products:
        return []

    contexts = contexts or []
    exclude_ids = set(exclude_ids or [])

    #Loại bỏ sản phẩm đã có trước đó 
    candidates = [p for p in products if p.get("id") not in exclude_ids]

    if not candidates:
        return []

    #Gợi ý theo giới tính
    if gender:
        g = gender.lower()
        candidates = [
            p for p in candidates
            if g in p.get("parent_category", "").lower()
            or g in p.get("name", "").lower()
            or any(u in p.get("parent_category", "").lower() for u in UNISEX)
        ]

    if not candidates:
        return []

    #Tính điểm ưu tiên dựa trên ngữ cảnh
    scored = []
    for p in candidates:
        score = p.get("score", 0)
        name = normalize_text(p["name"])

        for ctx in contexts:
            for kw in CONTEXT_KEYWORDS.get(ctx, []):
                if fuzzy_match(kw, name):
                    score += 10

        if score >= min_score:
            scored.append((score, p))

    if not scored:
        return []

    #Sắp xếp theo điểm ưu tiên
    scored.sort(key=lambda x: x[0], reverse=True)

    #Lấy top k sản phẩm
    return [p for _, p in scored[:2]]


#Hàm làm sạch dữ liệu
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

#Hàm xử lý chính
def fashion_chat(user_message: str):
    vectordb = get_vectordb()

    # Ưu tiên size
    if detect_size_intent(user_message):
        height, weight = parse_height_weight(user_message)
        size = suggest_size(height, weight)
        
        query = normalize_text(user_message) + f" size {size}"
        docs = vectordb.similarity_search_with_score(query, k=50)
        products_size = [doc[0].metadata for doc in docs if doc[1] > 0.5]  
        products_size = filter_by_size(products_size, size)
        
        # Thêm lọc dựa trên chiều cao
        gender = detect_gender(user_message)
        if gender is None and height is not None:
            if height < 120:
                gender = "trẻ em"
        
        products_size = filter_by_gender(products_size, gender)
        
        if gender is None and height is not None and height > 120:
            products_size = filter_adult_products(products_size)
        
        # Thêm lọc theo loại trang phục và ngữ cảnh
        item_type = detect_item_type(user_message)
        if item_type:
            products_size = [p for p in products_size if detect_role(p) == item_type]
        
        contexts = detect_contexts(user_message)
        products_size = filter_by_contexts(products_size, contexts)
        
        products_size = sort_products(products_size)
        
        # Xử lý số lượng nếu có
        quantity = detect_quantity(user_message)
        display_limit = quantity if quantity else 6
        
        return clean_for_json({
            "answer": f"Với chiều cao {height}cm và cân nặng {weight}kg, mình khuyên bạn chọn size {size} nhé!",
            "suggested_size": size,
            "products": products_size[:display_limit],
            "you_may_like": recommend_you_may_like(products_size)
        })

    #Phân tích ý định
    gender = detect_gender(user_message)
    budget = detect_budget(user_message)
    wallet_level = detect_wallet_level(user_message)
    contexts = detect_contexts(user_message)
    quantity = detect_quantity(user_message)

    #setbuget theo hoàn cảnh
    min_budget = None
    if wallet_level and not budget:
        if wallet_level == "giàu":
            budget = float('inf')  
            min_budget = 1_000_000  
        elif wallet_level == "khá":
            budget = 1_000_000  
            min_budget = 500_000
        elif wallet_level == "tiết kiệm":
            budget = 500_000  

    # Kiểm tra nếu chỉ hỏi về giá
    price_only = is_price_only_query(user_message, budget, contexts)
    if price_only:
        contexts = []

    # Xây dựng truy vấn tìm kiếm
    query_parts = [normalize_text(user_message)]
    if gender:
        query_parts.append(gender)
    if budget:
        query_parts.append(f"giá dưới {budget}")
    if wallet_level:
        query_parts.append(f"giá {wallet_level}")  
    if contexts:
        query_parts.extend([", ".join(CONTEXT_KEYWORDS.get(ctx, [])) for ctx in contexts])
    query = " ".join(query_parts)

    # Tìm kiếm sản phẩm
    docs = vectordb.similarity_search_with_score(query, k=50)  
    products = [doc[0].metadata for doc in docs if doc[1] > 0.5]  
    if not products:
        products = get_products_cached()

    # Lọc sản phẩm
    products = filter_by_gender(products, gender)
    products = filter_by_contexts(products, contexts)

    # Lọc budget 
    filtered_products = filter_by_budget(products, max_budget=budget, min_budget=min_budget)
    if len(filtered_products) < 2 and min_budget:
        filtered_products = filter_by_budget(products, max_budget=budget)

    products = filtered_products

    #Loại các sản phẩm trùng lặp
    seen = set()
    uniq = []
    for p in products:
        if p["id"] not in seen:
            uniq.append(p)
            seen.add(p["id"])
    products = uniq

    # Sort với wallet_level
    products = sort_products(products, wallet_level=wallet_level)
    # Nếu không đủ sản phẩm, yêu cầu admin tư vấn
    if len(products) < 2:
        return {
            "answer": " Mình chưa tìm được món phù hợp hãy chat trực tiếp với admin để được tư vấn chi tiết hơn nhé!",
            "products": [],
            "need_admin": True
        }

    #Gợi ý outfit và combo (bỏ qua outfit nếu chỉ hỏi giá)
    outfit = [] if price_only else build_smart_outfit(products, contexts, budget)
    combo = build_combo(products, budget)
    #Tính tổng giá outfit
    outfit_total = sum(p["price"] for p in outfit)
    _, style_desc = get_price_category(outfit_total)

    #Trả lời dựa theo hoàn cảnh giàu/ nghèo
    effective_wallet = wallet_level or get_price_category(outfit_total)[0]
    if effective_wallet == "giàu":
        answer = f"Mình gợi ý cho bạn một outfit cực sang trọng!"
    elif effective_wallet == "khá":
        answer = f"Đây là set đồ mà mình thấy hợp với bạn "
    else:
        answer = f"Mình gợi cho bạn outfit "

    #Thêm ngữ cảnh vào câu trả lời
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

    # Nếu chỉ hỏi giá
    if price_only:
        answer = f"Dưới đây là các sản phẩm phù hợp với mức giá bạn hỏi:"

    # Xử lý số lượng hiển thị
    display_limit = quantity if quantity else 6

# Hoàn thiện câu trả lời
    return clean_for_json({
        "answer": answer,
        "products": products[:display_limit],
        "outfit_products": outfit,
        "budget_combo": combo,
        "you_may_like": recommend_you_may_like(products),
        "outfit_total_price": outfit_total,
        "need_admin": False
    })