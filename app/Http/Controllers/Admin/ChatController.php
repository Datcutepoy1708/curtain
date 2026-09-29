<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'all');
        $staffId = Auth::id();

        // 1. Bộ đếm theo tab phân loại (giống hàng đợi trong java_angular)
        $waitingCount = ChatConversation::waiting()->count();
        $myCount = ChatConversation::staffActive($staffId)->count();
        $botCount = ChatConversation::botActive()->count();
        $closedCount = ChatConversation::closed()->count();
        $allCount = ChatConversation::count();

        // 2. Query lọc danh sách theo tab
        $query = ChatConversation::with(['messages', 'staff', 'user'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        switch ($tab) {
            case 'waiting':
                $query->waiting();
                break;
            case 'my_chats':
                $query->staffActive($staffId);
                break;
            case 'bot':
                $query->botActive();
                break;
            case 'closed':
                $query->closed();
                break;
            default:
                // 'all'
                break;
        }

        $conversations = $query->paginate(20)->withQueryString();

        $selectedId = (int) $request->input('conversation_id', $conversations->first()?->id ?? 0);
        $selectedConversation = ChatConversation::with(['messages', 'staff', 'user'])->find($selectedId) ?: $conversations->first();

        // Đánh dấu đã đọc tin nhắn của khách trong cuộc hội thoại được chọn
        if ($selectedConversation) {
            $selectedConversation->messages()
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('admin.chats.index', compact(
            'conversations', 
            'selectedConversation', 
            'tab', 
            'waitingCount', 
            'myCount', 
            'botCount', 
            'closedCount',
            'allCount'
        ));
    }

    public function getMessages($id)
    {
        $conversation = ChatConversation::with(['messages', 'staff', 'user'])->findOrFail($id);

        $conversation->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'conversation' => $conversation,
            'messages' => $conversation->messages,
            'staff' => $conversation->staff,
            'status' => $conversation->status,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $conversation = ChatConversation::findOrFail($id);
        $staffUser = Auth::user();

        // Tự động tiếp nhận nếu cuộc hội thoại đang chờ hoặc ở bot
        if ($conversation->status !== 'staff_connected' || !$conversation->staff_id) {
            $conversation->status = 'staff_connected';
            $conversation->staff_id = $staffUser->id;
        }

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'staff',
            'sender_id' => $staffUser->id,
            'message' => trim($request->message),
            'is_read' => false,
        ]);

        $conversation->last_message_at = now();
        $conversation->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'conversation_status' => $conversation->status,
            ]);
        }

        return redirect()->back()->with('success', 'Đã gửi tin nhắn cho khách hàng.');
    }

    /**
     * Tiếp nhận cuộc trò chuyện (Claim hội thoại - chống trùng lặp)
     */
    public function takeover($id)
    {
        $conversation = ChatConversation::with('staff')->findOrFail($id);
        $staffUser = Auth::user();

        // Kiểm tra xem đã có nhân viên khác tiếp nhận chưa
        if ($conversation->status === 'staff_connected' && $conversation->staff_id && $conversation->staff_id !== $staffUser->id) {
            return redirect()->back()->with('error', "Cuộc trò chuyện này đã được nhân viên {$conversation->staff->name} tiếp nhận.");
        }

        $conversation->status = 'staff_connected';
        $conversation->staff_id = $staffUser->id;
        $conversation->last_message_at = now();
        $conversation->save();

        // Tự động gửi tin nhắn chào mừng từ nhân viên
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'staff',
            'sender_id' => $staffUser->id,
            'message' => "Chào anh/chị! Em là {$staffUser->name} - Chuyên viên tư vấn rèm của CurtainLux. Em đã tiếp nhận cuộc trò chuyện và sẵn sàng giải đáp chi tiết cho mình ngay ạ!",
            'is_read' => false,
        ]);

        return redirect()->route('admin.chats.index', ['conversation_id' => $conversation->id, 'tab' => 'my_chats'])
            ->with('success', "Đã tiếp nhận phiên tư vấn của {$conversation->customer_name}.");
    }

    /**
     * Đóng phiên trò chuyện (kèm tin nhắn hệ thống báo khách)
     */
    public function close($id)
    {
        $conversation = ChatConversation::findOrFail($id);
        $staffUser = Auth::user();

        $conversation->status = 'closed';
        $conversation->save();

        // Gửi tin nhắn kết thúc phiên để khách hàng trên website nhận biết
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'bot',
            'message' => "[Hệ thống]: Cuộc trò chuyện đã được kết thúc bởi chuyên viên tư vấn {$staffUser->name}. Cảm ơn quý khách đã tin tưởng và liên hệ CurtainLux!",
            'is_read' => true,
        ]);

        return redirect()->route('admin.chats.index', ['tab' => 'closed'])
            ->with('success', 'Đã kết thúc phiên trò chuyện.');
    }

    public function poll(Request $request, $id)
    {
        $conversation = ChatConversation::findOrFail($id);
        $lastId = (int) $request->input('last_id', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->get();

        if ($newMessages->where('sender_type', 'customer')->count() > 0) {
            $conversation->messages()
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'status' => $conversation->status,
            'waiting_count' => ChatConversation::waiting()->count(),
            'messages' => $newMessages,
        ]);
    }
}
