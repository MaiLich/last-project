from unidecode import unidecode
import re

FAQS = {
    "ship": {
        "keywords": ["ship", "giao hang", "van chuyen", "phi ship", "bao lau"],
        "answer": "Shop hỗ trợ giao hàng toàn quốc:\n- Nội thành: 1-2 ngày.\n- Ngoại thành: 3-5 ngày.\n- Phí ship từ 20k-50k tùy khu vực."
    },
    "return": {
        "keywords": ["doi tra", "hoan tien", "bao hanh", "loi"],
        "answer": "Chính sách đổi trả:\n- Đổi size/mẫu trong vòng 7 ngày.\n- Sản phẩm phải còn nguyên tem mác.\n- Lỗi do nhà sản xuất đổi mới 100%."
    },
    "size": {
        "keywords": ["kich thuoc", "bang size", "chon size"],
        "answer": "Shop có size S, M, L, XL. Bạn hãy nhập chiều cao và cân nặng (VD: 170cm 60kg) để mình tư vấn size chuẩn nhất nhé!"
    },
    "payment": {
        "keywords": ["thanh toan", "tra tien", "chuyen khoan", "cod"],
        "answer": "Shop hỗ trợ:\n1. Thanh toán khi nhận hàng (COD).\n2. Chuyển khoản ngân hàng.\n3. Ví điện tử."
    },
    "contact": {
        "keywords": ["lien he", "dia chi", "so dien thoai", "hotline"],
        "answer": "Hotline: 1900 xxxx\n Địa chỉ: 123 Đường ABC, Hà Nội."
    }
}

def detect_size_intent(text):
    text = unidecode(text.lower())
    return bool(re.search(r"\d+\s*cm", text) and re.search(r"\d+\s*kg", text))

def check_faq(query: str):
    norm_query = unidecode(query.lower())
    
    for topic, content in FAQS.items():
        for kw in content["keywords"]:
            if kw in norm_query:
                if topic == "size" and detect_size_intent(query):
                    return None
                return content["answer"]
    return None
