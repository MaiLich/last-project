from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from chatbot import fashion_chat
from faq import check_faq
from dbsetup import setup_db

app = FastAPI()

# 👉 THÊM ĐOẠN NÀY
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Gọi setup_db khi app khởi động
setup_db()

class ChatRequest(BaseModel):
    message: str

@app.post("/chat")
def chat(req: ChatRequest):
    faq = check_faq(req.message)
    if faq:
        return {"answer": faq, "products": [], "outfit_products": []}
    return fashion_chat(req.message)
