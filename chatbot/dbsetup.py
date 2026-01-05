# dbsetup.py
import pandas as pd
import json
import os
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_community.vectorstores import Chroma

DATA_PATH = "products.csv"
DB_PATH = "chroma_db"

def setup_db():
    if not os.path.exists(DATA_PATH):
        print(f"❌ Không tìm thấy file {DATA_PATH}")
        return

    print("🔹 Đang đọc dữ liệu sản phẩm...")
    try:
        df = pd.read_csv(DATA_PATH, encoding='utf-8')
    except UnicodeDecodeError:
        df = pd.read_csv(DATA_PATH, encoding='utf-16') # Fallback nếu lỗi encode

    # Fix description NULL
    df["description"] = df["description"].fillna("")
    
    # Parse size JSON string -> text
    def parse_size(val):
        try:
            # Xử lý trường hợp chuỗi json lỗi hoặc format lạ
            if isinstance(val, str):
                val = val.replace("'", '"') # Fix quote
                return ", ".join(json.loads(val))
            return ""
        except:
            return ""

    df["size_text"] = df["size"].apply(parse_size)

    # Build text cho embedding
    def build_text(row):
        return f"""
        Tên: {row['name']}
        Loại: {row['category']} | {row['section']}
        Giá: {row['price']}
        Mô tả: {row['description']}
        Màu: {row['color']}
        """.strip()

    texts = df.apply(build_text, axis=1).tolist()
    
    # Metadata cần sạch để lưu vào DB
    metadatas = []
    for _, row in df.iterrows():
        meta = {
            "id": str(row['id']),
            "name": str(row['name']),
            "price": int(row['price']) if pd.notnull(row['price']) else 0,
            "category": str(row['category']),
            "size": str(row['size'])
        }
        metadatas.append(meta)

    print(f"✅ Tổng số sản phẩm: {len(texts)}")

    print("🔹 Load embedding model (CPU)...")
    embeddings = HuggingFaceEmbeddings(
        model_name="sentence-transformers/all-MiniLM-L6-v2"
    )

    print("🔹 Tạo Chroma DB...")
    # Xóa DB cũ nếu tồn tại để tránh duplicate khi chạy lại
    if os.path.exists(DB_PATH):
        import shutil
        shutil.rmtree(DB_PATH)
        
    vectordb = Chroma.from_texts(
        texts=texts,
        embedding=embeddings,
        metadatas=metadatas,
        persist_directory=DB_PATH
    )
    
    # vectordb.persist() # Các phiên bản mới của Chroma tự động persist
    print("🎉 HOÀN TẤT DB")

if __name__ == "__main__":
    setup_db()