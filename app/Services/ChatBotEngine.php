<?php

namespace App\Services;

use App\Models\ChatBotRule;
use App\Models\ChatConversation;

class ChatBotEngine
{
    /**
     * Danh sách từ khóa khách hàng chủ động yêu cầu gặp nhân viên trực tiếp
     */
    protected const HANDOVER_KEYWORDS = [
        'gặp nhân viên', 'gap nhan vien',
        'tư vấn viên', 'tu van vien',
        'gặp người thật', 'gap nguoi that',
        'người thật', 'nguoi that',
        'hỗ trợ trực tiếp', 'ho tro truc tiep',
        'kết nối nhân viên', 'ket noi nhan vien',
        'nói chuyện người', 'noi chuyen nguoi',
        'gặp tư vấn', 'gap tu van',
        'nhân viên', 'nhan vien',
        'admin',
    ];

    /**
     * Loại bỏ dấu tiếng Việt để so khớp từ khóa chính xác
     */
    public static function stripVietnamese(string $str): string
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", 'y', $str);
        $str = preg_replace("/(đ)/u", 'd', $str);
        return mb_strtolower(trim($str), 'UTF-8');
    }

    /**
     * Kiểm tra khách hàng có yêu cầu gặp nhân viên trực tiếp không
     */
    public function isExplicitHandover(string $message): bool
    {
        $normalized = self::stripVietnamese($message);
        foreach (self::HANDOVER_KEYWORDS as $kw) {
            $kwNorm = self::stripVietnamese($kw);
            if (!empty($kwNorm) && str_contains($normalized, $kwNorm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Tìm quy tắc ChatBotRule khớp với nội dung
     */
    public function findMatchingRule(string $message): ?ChatBotRule
    {
        $rules = ChatBotRule::active()->get();
        foreach ($rules as $rule) {
            if ($rule->matches($message)) {
                return $rule;
            }
        }
        return null;
    }

    /**
     * Xử lý trường hợp Bot không hiểu câu hỏi (2-step Auto-Escalate)
     * - Lần 1: Gợi ý thân thiện + gửi danh sách chủ đề phổ biến
     * - Lần 2: Tự động chuyển giao sang WAITING_STAFF để nhân viên hỗ trợ
     */
    public function handleUnmatched(ChatConversation $conversation): array
    {
        $unmatchedCount = (int) $conversation->bot_unmatched_count;

        if ($unmatchedCount >= 1) {
            // Lần 2 liên tiếp không hiểu -> Tự động chuyển giao sang Chờ nhân viên
            $conversation->status = 'waiting_staff';
            $conversation->bot_unmatched_count = 0;
            $conversation->save();

            return [
                'action_type' => 'HANDOVER_STAFF',
                'message' => "Dạ câu hỏi của anh/chị cần chuyên môn tư vấn chi tiết hơn. CurtainBot xin phép chuyển ngay cuộc trò chuyện đến chuyên viên trực tuyến của CurtainLux, vui lòng chờ trong giây lát ạ...",
                'quick_replies' => ['Để lại số điện thoại', 'Xem các mẫu rèm hot'],
            ];
        }

        // Lần 1 không hiểu -> Gợi ý trợ giúp
        $conversation->bot_unmatched_count = 1;
        $conversation->save();

        return [
            'action_type' => 'REPLY',
            'message' => "Dạ câu hỏi của anh/chị CurtainBot chưa nắm bắt trọn vẹn. Anh/chị có thể tham khảo nhanh các chủ đề tư vấn phổ biến bên dưới hoặc bấm 'Gặp nhân viên tư vấn' để được hỗ trợ chi tiết ạ:",
            'quick_replies' => $this->buildDefaultQuickReplies(),
        ];
    }

    /**
     * Reset bộ đếm không khớp khi bot trả lời đúng kịch bản
     */
    public function resetUnmatched(ChatConversation $conversation): void
    {
        if ($conversation->bot_unmatched_count > 0) {
            $conversation->bot_unmatched_count = 0;
            $conversation->save();
        }
    }

    /**
     * Danh mục Quick Replies mặc định
     */
    public function buildDefaultQuickReplies(): array
    {
        return [
            'Báo giá các loại rèm',
            'Cách đo kích thước cửa sổ',
            'Khảo sát mang mẫu tận nơi',
            'Gặp nhân viên tư vấn',
        ];
    }
}
