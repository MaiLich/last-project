from fastapi import FastAPI
from pydantic import BaseModel
from chatbot import fashion_chat
from faq import check_faq
from dbsetup import setup_db  # Thêm import

app = FastAPI()

# Gọi setup_db khi app khởi động
setup_db()  # Nếu DB chưa tồn tại, sẽ tạo; nếu có thì skip

class ChatRequest(BaseModel):
    message: str

@app.post("/chat")
def chat(req: ChatRequest):
    faq = check_faq(req.message)
    if faq:
        return {
            "answer": faq,
            "products": [],
            "outfit_products": []
        }
    return fashion_chat(req.message)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=8000)