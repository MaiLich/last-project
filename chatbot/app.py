from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from chatbot import fashion_chat
from faq import check_faq
from dbsetup import setup_db
import uvicorn

app = FastAPI()

# 👉 THÊM ĐOẠN NÀY
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


class ChatRequest(BaseModel):
    message: str

@app.post("/chat")
def chat(req: ChatRequest):
    faq = check_faq(req.message)
    if faq:
        return {"answer": faq, "products": [], "outfit_products": []}
    return fashion_chat(req.message)
if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)