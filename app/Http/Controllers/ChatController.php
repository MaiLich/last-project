<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // ===== USER =====
    public function index()
    {
        $userId = Auth::id();

        $admin = Admin::first();
        if (!$admin) {
            abort(500, 'No admin found.');
        }

        $conversation = Conversation::firstOrCreate([
            'user_id'  => $userId,
            'admin_id' => $admin->id,
        ]);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get();

        // Mark admin -> user messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_type', Admin::class)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat.index', compact('conversation', 'messages'));
    }

    // ===== ADMIN =====
    public function adminIndex()
    {
        $conversations = Conversation::with(['user', 'latestMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $selectedConversation = $conversations->first();
        $messages = collect();

        if ($selectedConversation) {
            $messages = $selectedConversation->messages()
                ->with('sender')
                ->orderBy('id', 'asc')
                ->get();

            // Mark user -> admin messages as read
            Message::where('conversation_id', $selectedConversation->id)
                ->where('sender_type', User::class)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('admin.chat.index', compact('conversations', 'selectedConversation', 'messages'));
    }

    public function adminShowConversation($conversationId)
    {
        $conversation = Conversation::with('user')->findOrFail($conversationId);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get();

        // Mark user -> admin messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_type', User::class)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.chat.conversation_detail', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'message' => 'required|string|max:2000',
            ]);

            $conversation = Conversation::findOrFail($request->conversation_id);

            // Nhận diện đang gửi từ admin route hay user route
            $isAdminRoute = $request->is('admin/*') || $request->routeIs('admin.*');

            if ($isAdminRoute) {
                // ADMIN gửi: luôn trỏ về admins (ưu tiên guard admin, fallback về conversation->admin_id)
                $senderType = \App\Models\Admin::class;
                $senderId   = auth('admin')->id() ?: (int) $conversation->admin_id;

                // Chặn gửi sai hội thoại (phòng trường hợp giả conversation_id)
                if ((int) $conversation->admin_id !== (int) $senderId) {
                    return response()->json(['error' => 'Forbidden'], 403);
                }
            } else {
                // USER gửi: trỏ về users
                if (!auth('web')->check()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                $senderType = \App\Models\User::class;
                $senderId   = (int) auth('web')->id();

                if ((int) $conversation->user_id !== (int) $senderId) {
                    return response()->json(['error' => 'Forbidden'], 403);
                }
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $senderId,
                'sender_type'     => $senderType,
                'message'         => $request->message,
                'is_read'         => false,
            ]);

            $conversation->touch();

            return response()->json(['message' => $message], 200);
        } catch (\Throwable $e) {
            \Log::error('Send message error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }



    // Polling endpoint (1s/lần), hỗ trợ since_id
    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        $isAdminRoute = $request->is('admin/*') || $request->routeIs('admin.*');

        if ($isAdminRoute) {
            // Admin đang xem: ưu tiên guard admin, fallback conversation->admin_id
            $adminId = auth('admin')->id() ?: (int) $conversation->admin_id;

            if ((int) $conversation->admin_id !== (int) $adminId) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        } else {
            // User đang xem
            if (!auth('web')->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userId = (int) auth('web')->id();
            if ((int) $conversation->user_id !== (int) $userId) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        }

        $sinceId = (int) $request->query('since_id', 0);

        $q = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('id', 'asc');

        if ($sinceId > 0) $q->where('id', '>', $sinceId);

        $messages = $q->get();

        // Mark read theo phía đối diện
        if ($isAdminRoute) {
            Message::where('conversation_id', $conversationId)
                ->where('sender_type', \App\Models\User::class)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            Message::where('conversation_id', $conversationId)
                ->where('sender_type', \App\Models\Admin::class)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json($messages);
    }
}
