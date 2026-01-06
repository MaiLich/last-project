import pandas as pd
import json
import os
import shutil
from langchain_huggingface import HuggingFaceEmbeddings
from langchain_community.vectorstores import Chroma

DATA_PATH = "products.csv"
DB_PATH = "chroma_db"

def setup_db():
    if not os.path.exists(DATA_PATH):
        print(f"Không tìm thấy file {DATA_PATH}")
        return

    try:
        df = pd.read_csv(DATA_PATH, encoding="utf-8")
    except UnicodeDecodeError:
        df = pd.read_csv(DATA_PATH, encoding="utf-16")

    #Làm sạch dữ liệu cơ bản
    for col in ["name", "section", "category", "parent_category", "color", "size", "description"]:
        if col in df.columns:
            df[col] = df[col].fillna("").astype(str)

    df["price"] = pd.to_numeric(df["price"], errors="coerce").fillna(0).astype(int)

    #Sinh tag giới tính
    def normalize_gender(row):
        text = f"{row['parent_category']} {row['category']} {row['name']}".lower()
        if "trẻ em" in text:
            return "trẻ em"
        if "nữ" in text or "váy" in text or "đầm" in text:
            return "nữ"
        if "nam" in text:
            return "nam"
        return "unisex"

    df["gender_tag"] = df.apply(normalize_gender, axis=1)

    #Build texts for embedding
    def build_text(row):
        return " ".join([
            row["name"],
            row["category"],
            row["parent_category"],
            row["section"],
            row["color"],
            row["description"],
            row["size"]
        ]).strip()

    texts = df.apply(build_text, axis=1).tolist()

    #Build metadatas
    metadatas = []
    for _, row in df.iterrows():
        meta = {
            "id": str(row["id"]),
            "name": row["name"],
            "section": row["section"],
            "category": row["category"],
            "parent_category": row["parent_category"],  
            "price": row["price"],
            "color": row["color"],
            "size": row["size"],
            "description": row["description"],
            "gender": row["gender_tag"],              
            "image": ""
        }
        metadatas.append(meta)

    # Tạo embedding
    print("Load embedding model (CPU)...")
    embeddings = HuggingFaceEmbeddings(
        model_name="bkai-foundation-models/vietnamese-bi-encoder"
    )

    if os.path.exists(DB_PATH):
        shutil.rmtree(DB_PATH)

    Chroma.from_texts(
        texts=texts,
        embedding=embeddings,
        metadatas=metadatas,
        persist_directory=DB_PATH
    )

    print("SETUP DB THÀNH CÔNG!")

if __name__ == "__main__":
    setup_db()
