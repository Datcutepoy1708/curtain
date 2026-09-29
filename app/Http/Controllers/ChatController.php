<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\ChatBotEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected function getChatSessionId(): string
    {
        if (!session()->has('customer_chat_session_id')) {
            session()->put('customer_chat_session_id', 'chat_' . bin2hex(random_bytes(12)));
        }
        return session()->get('customer_chat_session_id');
    }

    protected function verifyOwnership(ChatConversation $conversation): void
    {
        $sessionId = $this->getChatSessionId();
        $userId = Auth::id();

        $isOwner = false;
        if ($sessionId && $conversation->session_id === $sessionId) {
            $isOwner = true;
        } elseif ($userId && $conversation->user_id === $userId) {
            $isOwner = true;
        }

        if (!$isOwner) {
            abort(403, 'Bạn không có quyền truy cập cuộc trò chuyện này.');
        }
    }

    /**
     * Khởi tạo hoặc tiếp tục hội thoại (tương tự CustomerChatController /init bên java_angular)
     */
    public function init(Request $request)
    {
        $sessionId = $this->getChatSessionId();
        $user = Auth::user();
        $forceNew = $request->boolean('force_new', false);

        $conversation = null;

        if (!$forceNew) {
            $conversation = ChatConversation::where(function ($q) use ($sessionId, $user) {
                    $q->where('session_id', $sessionId);
                    if ($user) {
                        $q->orWhere('user_id', $user->id);
                    }
                })
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();
        }

        if (!$conversation) {
            $customerName = $user ? $user->name : 'Khách hàng #' . substr($sessionId, -4);
            $conversation = ChatConversation::create([
                'session_id' => $sessionId,
                'user_id' => $user?->id,
                'customer_name' => $customerName,
                'customer_phone' => $user?->phone,
                'status' => 'bot',
                'bot_unmatched_count' => 0,
                'last_message_at' => now(),
            ]);

            // Default welcome message from CurtainBot (với quick replies phong cách java_angular)
            $botEngine = new ChatBotEngine();
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'message' => "Dạ em chào anh/chị! Em là CurtainBot — Trợ lý tư vấn rèm cửa thông minh của CurtainLux. Em có thể hỗ trợ anh/chị chọn mẫu rèm, tính kích thước hoặc báo giá dự kiến ngay bây giờ ạ!",
                'quick_replies' => $botEngine->buildDefaultQuickReplies(),
                'is_read' => true,
            ]);
        } else {
            // Hợp nhất phiên Guest vào User khi khách đã đăng nhập
            if ($user && !$conversation->user_id) {
                $conversation->user_id = $user->id;
                if ($user->name) {
                    $conversation->customer_name = $user->name;
                }
                if ($user->phone) {
                    $conversation->customer_phone = $user->phone;
                }
                $conversation->save();
            }
        }

        $messages = $conversation->messages()->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
            'status' => $conversation->status,
            'is_closed' => ($conversation->status === 'closed'),
            'staff_name' => $conversation->staff ? $conversation->staff->name : null,
        ]);
    }

    /**
     * Gửi tin nhắn từ phía khách hàng
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        $conversation = ChatConversation::findOrFail($request->conversation_id);
        $this->verifyOwnership($conversation);

        // Chặn nếu phiên trò chuyện đã kết thúc
        if ($conversation->status === 'closed') {
            return response()->json([
                'status' => 'closed',
                'message' => 'Phiên trò chuyện này đã kết thúc. Vui lòng bấm bắt đầu phiên mới để tiếp tục.',
            ], 400);
        }

        $customerText = trim($request->message);

        // 1. Lưu tin nhắn của khách hàng
        $custMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_id' => Auth::id(),
            'message' => $customerText,
            'is_read' => false,
        ]);

        $conversation->last_message_at = now();
        $conversation->save();

        $botReply = null;
        $botEngine = new ChatBotEngine();

        // 2. Nếu đang ở chế độ Bot (BOT_ACTIVE)
        if ($conversation->status === 'bot') {
            // Trường hợp A: Khách yêu cầu gặp nhân viên chủ động
            if ($botEngine->isExplicitHandover($customerText)) {
                $conversation->status = 'waiting_staff';
                $conversation->bot_unmatched_count = 0;
                $conversation->save();

                $botReply = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_type' => 'bot',
                    'message' => "Dạ em đã chuyển cuộc trò chuyện đến chuyên viên tư vấn rèm của CurtainLux! Quý khách vui lòng đợi trong giây lát, chuyên viên trực tuyến sẽ tiếp nhận ngay bây giờ ạ...",
                    'quick_replies' => ['Để lại số điện thoại', 'Xem các mẫu rèm hot'],
                    'is_read' => true,
                ]);
            } else {
                // Trường hợp B: Quét kịch bản ChatBotRule
                $matchedRule = $botEngine->findMatchingRule($customerText);

                if ($matchedRule) {
                    $botEngine->resetUnmatched($conversation);

                    if ($matchedRule->action_type === 'HANDOVER_STAFF') {
                        $conversation->status = 'waiting_staff';
                        $conversation->save();
                    }

                    $botReply = ChatMessage::create([
                        'conversation_id' => $conversation->id,
                        'sender_type' => 'bot',
                        'message' => $matchedRule->response_message,
                        'quick_replies' => $matchedRule->quick_replies,
                        'is_read' => true,
                    ]);
                } else {
                    // Trường hợp C: Không khớp kịch bản -> Kích hoạt cơ chế 2-step Escalation
                    $unmatchedResult = $botEngine->handleUnmatched($conversation);

                    $botReply = ChatMessage::create([
                        'conversation_id' => $conversation->id,
                        'sender_type' => 'bot',
                        'message' => $unmatchedResult['message'],
                        'quick_replies' => $unmatchedResult['quick_replies'] ?? [],
                        'is_read' => true,
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'customer_message' => $custMsg,
            'bot_message' => $botReply,
            'conversation_status' => $conversation->status,
            'is_closed' => ($conversation->status === 'closed'),
            'staff_name' => $conversation->staff ? $conversation->staff->name : null,
        ]);
    }

    /**
     * Polling tin nhắn mới cho widget khách hàng (mỗi 3s)
     */
    public function poll(Request $request, $id)
    {
        $conversation = ChatConversation::with('staff')->findOrFail($id);
        $this->verifyOwnership($conversation);
        $lastId = (int) $request->input('last_id', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->get();

        // Đánh dấu tin nhắn chuyên viên là đã đọc
        if ($newMessages->where('sender_type', 'staff')->count() > 0) {
            $conversation->messages()
                ->where('sender_type', 'staff')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'status' => $conversation->status,
            'is_closed' => ($conversation->status === 'closed'),
            'staff_name' => $conversation->staff ? $conversation->staff->name : null,
            'messages' => $newMessages,
        ]);
    }
}
