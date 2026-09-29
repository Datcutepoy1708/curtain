<?php

namespace Database\Seeders;

use App\Models\ChatBotRule;
use Illuminate\Database\Seeder;

class ChatBotRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'rule_name' => 'Báo giá các loại rèm cửa',
                'keywords' => 'giá, báo giá, bao nhiêu, chi phí, tính tiền, giá rèm',
                'match_type' => 'CONTAINS',
                'response_message' => "Dạ CurtainLux xin gửi anh/chị bảng giá tham khảo các dòng rèm cao cấp:\n• Rèm vải gấm Bỉ 2 lớp: từ 650.000₫ - 1.200.000₫ / mét ngang hoàn thiện (đã gồm độ nhún 2.5 và thanh ray).\n• Rèm cầu vồng Hàn Quốc: từ 450.000₫ - 850.000₫ / m² cản sáng 100%.\n• Rèm cuốn văn phòng / chống nắng: từ 280.000₫ - 420.000₫ / m².\n• Động cơ rèm thông minh tự động: từ 1.850.000₫ / bộ (điều khiển qua App & giọng nói).\nAnh/chị đang quan tâm mẫu rèm cho phòng khách hay phòng ngủ ạ?",
                'quick_replies' => ['Rèm phòng khách', 'Rèm phòng ngủ', 'Đặt lịch đo tận nhà', 'Gặp tư vấn viên'],
                'action_type' => 'REPLY',
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Hướng dẫn cách đo đạc cửa sổ',
                'keywords' => 'cách đo, đo rèm, kích thước, số đo, đo cửa',
                'match_type' => 'CONTAINS',
                'response_message' => "Dạ cách đo kích thước cửa sổ chuẩn xác nhất:\n1. Lắp lọt lòng: Đo chiều ngang và chiều cao lọt trong khung cửa (lấy số đo nhỏ nhất trừ đi 1cm).\n2. Lắp phủ bì: Chiều ngang cộng thêm 15-20cm mỗi bên, chiều cao cộng thêm 15cm phía trên và 15-25cm dưới mép cửa để chống lọt sáng tuyệt đối.\n\n✨ CurtainLux có thợ kỹ thuật qua khảo sát đo đạc tận nhà hoàn toàn MIỄN PHÍ mang theo cả tập catalogue mẫu vải thực tế ạ!",
                'quick_replies' => ['Đặt lịch đo miễn phí', 'Báo giá rèm vải', 'Gặp tư vấn viên'],
                'action_type' => 'REPLY',
                'priority' => 8,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Khảo sát mang mẫu tận nơi',
                'keywords' => 'khảo sát, đo tận nhà, mang mẫu, xem mẫu tại nhà, tận nơi',
                'match_type' => 'CONTAINS',
                'response_message' => "Dạ CurtainLux hỗ trợ chuyên viên mang theo trọn bộ bảng mẫu vải, thanh ray và phụ kiện đến tận nhà khảo sát, tư vấn màu sắc theo phong thủy hoàn toàn MIỄN PHÍ 100%!\nAnh/chị có thể bấm nút 'Đặt lịch đo miễn phí' hoặc để lại Số điện thoại để bên em sắp xếp thợ kỹ thuật liên hệ ngay nhé ạ.",
                'quick_replies' => ['Đặt lịch đo miễn phí', 'Gặp tư vấn viên'],
                'action_type' => 'REPLY',
                'priority' => 9,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Chính sách bảo hành & Động cơ',
                'keywords' => 'bảo hành, động cơ, motor, aqara, tuya, thông minh, đổi trả',
                'match_type' => 'CONTAINS',
                'response_message' => "Dạ chính sách bảo hành tại CurtainLux cam kết số 1 thị trường:\n• Vải rèm & phụ kiện thanh treo: Bảo hành chính hãng 24 tháng.\n• Động cơ rèm thông minh tự động (Aqara, Tuya, Somfy): Bảo hành 1 đổi 1 trong 36 tháng.\n• Hỗ trợ kỹ thuật và kiểm tra định kỳ miễn phí trọn đời công trình.",
                'quick_replies' => ['Báo giá động cơ', 'Đặt lịch đo miễn phí', 'Gặp tư vấn viên'],
                'action_type' => 'REPLY',
                'priority' => 7,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Chuyển sang nhân viên trực tiếp',
                'keywords' => 'gặp nhân viên, tư vấn viên, nhân viên, nói chuyện người, trực tiếp, hỗ trợ viên, admin',
                'match_type' => 'CONTAINS',
                'response_message' => "Dạ em đã chuyển cuộc trò chuyện đến nhân viên tư vấn rèm của CurtainLux! Quý khách vui lòng đợi trong giây lát, chuyên viên trực tuyến sẽ kết nối ngay bây giờ ạ...",
                'quick_replies' => ['Để lại số điện thoại', 'Xem mẫu rèm hot'],
                'action_type' => 'HANDOVER_STAFF',
                'priority' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $r) {
            ChatBotRule::updateOrCreate(['rule_name' => $r['rule_name']], $r);
        }
    }
}
