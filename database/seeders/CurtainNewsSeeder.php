<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurtainNewsSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first() ?: User::first();
        $authorId = $adminUser?->id;

        $articles = [
            [
                'title' => 'Xu Hướng Rèm Cửa Chung Cư 2026: Tối Giản, Tiện Nghi Thông Minh & Bền Vững',
                'slug' => 'xu-huong-rem-cua-chung-cu-2026',
                'category' => 'trends',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=900&q=80',
                'excerpt' => 'Khám phá các phong cách rèm cửa thịnh hành nhất năm 2026 dành cho căn hộ chung cư cao cấp: rèm vải 2 lớp Japandi, rèm cầu vồng Hàn Quốc và hệ thống động cơ rèm tự động thông minh.',
                'content' => '<h2>Định Hình Không Gian Sống Tinh Tế Năm 2026</h2>
<p>Trong thiết kế nội thất hiện đại năm 2026, rèm cửa không đơn thuần là vật dụng chắn sáng mà đã trở thành điểm nhấn nghệ thuật định hình phong cách kiến trúc của cả ngôi nhà. Đặc biệt đối với các căn hộ chung cư cao tầng, việc lựa chọn rèm cửa phù hợp đóng vai trò quan trọng trong việc điều tiết ánh sáng tự nhiên và mở rộng không gian thị giác.</p>

<h3>1. Rèm Vải 2 Lớp Phong Cách Japandi & Minimalism</h3>
<p>Sự kết hợp giữa nét tối giản tinh tế của Nhật Bản và sự ấm áp của phong cách Bắc Âu (Japandi) tiếp tục giữ vị trí độc tôn. Bộ rèm bao gồm một lớp voan lụa trắng hoặc be xước bồng bềnh giúp lấy ánh sáng tán xạ êm dịu vào ban ngày, kết hợp cùng lớp vải gấm dệt sợi thô nhập khẩu từ Bỉ hoặc Nhật Bản để cản nhiệt, cản nắng 100% vào ban đêm.</p>
<p>Các gam màu trung tính như màu cát sa mạc, màu be yến mạch, xám ghi ấm và nâu gỗ sồi nhạt được ưa chuộng nhờ khả năng kết hợp hài hòa với sàn gỗ và ghế sofa nỉ.</p>

<h3>2. Rèm Cầu Vồng Hàn Quốc — Giải Pháp Linh Hoạt Cho Ô Cửa Sổ Nhỏ</h3>
<p>Với cấu tạo gồm các dải vải cản sáng đan xen dải lưới xuyên sáng, rèm cầu vồng mang đến khả năng điều chỉnh ánh sáng chuẩn xác theo từng milimet. Khi các dải sáng – tối so le nhau, ánh nắng chiếu vào phòng được giảm bớt nhưng căn phòng vẫn giữ được độ thông thoáng tự nhiên.</p>

<h3>3. Động Cơ Rèm Tự Động Kết Nối Nhà Thông Minh (Smart Home)</h3>
<p>Xu hướng nhà thông minh bùng nổ kéo theo sự lên ngôi của động cơ rèm điện tử không dây. Người dùng có thể điều khiển đóng mở rèm bằng giọng nói tiếng Việt, qua ứng dụng trên điện thoại Smartphone hoặc hẹn giờ tự động mở rèm đón nắng sớm vào mỗi buổi sáng.</p>

<blockquote>"Một bộ rèm cửa đẳng cấp phải tôn vinh được đường nét của căn phòng mà không gây cảm giác nặng nề. Vải rèm cao cấp chính là tấm áo mềm mại biến khối bê tông thô cứng thành tổ ấm tràn đầy cảm xúc." — Chuyên gia thiết kế CurtainLux.</blockquote>',
                'views' => 1240,
            ],
            [
                'title' => 'Hướng Dẫn Cách Tự Đo Kích Thước Cửa Chuẩn Xác 100% Cho Rèm Vải & Rèm Cầu Vồng',
                'slug' => 'huong-dan-tu-do-kich-thuoc-cua-rem-vai-rem-cau-vong',
                'category' => 'guide',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1540518614846-7ede433c5172?auto=format&fit=crop&w=900&q=80',
                'excerpt' => 'Cẩm nang chi tiết các bước tự đo đạc khung cửa sổ lọt lòng và phủ bì bằng thước kim loại, giúp bạn tính toán diện tích và chi phí may rèm chuẩn xác, tránh lãng phí.',
                'content' => '<h2>Đo Kích Thước Chuẩn — Bước Tiên Quyết Để Có Bộ Rèm Hoàn Hảo</h2>
<p>Đo đạc sai số dù chỉ 1-2cm cũng có thể khiến bộ rèm bị cạ vào gờ tường hoặc hở sáng làm mất đi vẻ đẹp vốn có. Dưới đây là phương pháp đo chuyên nghiệp được các thợ may của xưởng CurtainLux đúc kết.</p>

<h3>Chuẩn Bị Dụng Cụ:</h3>
<ul>
    <li>Thước cuộn kim loại cứng (không dùng thước dây may đồ vì độ co giãn dễ gây sai số).</li>
    <li>Sổ tay hoặc ứng dụng ghi chú để ghi lại số đo chiều rộng (W) và chiều cao (H).</li>
</ul>

<h3>1. Cách Đo Rèm Vải Lắp Phủ Bì Tường</h3>
<p>Rèm vải hầu như luôn được khuyên lắp phủ bì ngoài khung cửa sổ để tạo cảm giác trần nhà cao ráo và cửa rộng hơn thực tế.</p>
<ul>
    <li><strong>Chiều rộng hoàn thiện:</strong> Đo chiều rộng khung cửa, sau đó cộng thêm từ <strong>20cm đến 30cm</strong> (mỗi bên mép cửa phủ qua 10 - 15cm).</li>
    <li><strong>Chiều cao hoàn thiện:</strong> Đo từ vị trí thanh treo rèm (thường cách mép trên cửa 15 - 20cm hoặc sát trần thạch cao) kéo dài xuống cách mặt sàn đúng <strong>1.5cm đến 2cm</strong> để rèm không bị quét đất bám bụi.</li>
    <li><strong>Độ nhún sóng vải:</strong> Để rèm có nếp sóng đều đặn lượn sóng chữ S, tỷ lệ vải chuẩn là <strong>2.5m vải cho 1m ngang cửa</strong>.</li>
</ul>

<h3>2. Cách Đo Rèm Cầu Vồng & Rèm Cuốn Lọt Lòng Khung Cửa</h3>
<p>Kiểu lắp lọt lòng rất gọn gàng và hiện đại, yêu cầu lòng cửa phải có độ sâu tối thiểu từ <strong>6cm trở lên</strong> để đặt vừa hộp phụ kiện.</p>
<ul>
    <li><strong>Chiều rộng (W):</strong> Đo tại 3 điểm (mép trên, giữa và mép dưới khung cửa), chọn <strong>kích thước nhỏ nhất</strong> và trừ đi <strong>0.5cm</strong> dung sai kỹ thuật.</li>
    <li><strong>Chiều cao (H):</strong> Đo chiều cao lọt lòng của ô cửa từ mép trên xuống bậu cửa sổ.</li>
</ul>',
                'views' => 985,
            ],
            [
                'title' => 'Bí Quyết Chọn Màu Sắc Rèm Cửa Phong Thủy Theo Bản Mệnh Rước Tài Lộc',
                'slug' => 'bi-quyet-chon-mau-sac-rem-cua-phong-thuy',
                'category' => 'guide',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
                'excerpt' => 'Phối màu rèm cửa tương sinh theo ngũ hành Kim, Mộc, Thủy, Hỏa, Thổ giúp điều hòa sinh khí, gia tăng tài lộc và mang lại cảm giác an yên cho mọi thành viên trong gia đình.',
                'content' => '<h2>Màu Sắc Rèm Cửa & Ý Nghĩa Phong Thủy Trong Nhà Ở</h2>
<p>Cửa sổ và cửa chính là nơi đón nguồn ánh sáng và sinh khí chính của ngôi nhà. Lựa chọn màu sắc rèm cửa tương sinh với bản mệnh của gia chủ giúp cân bằng nguồn năng lượng âm dương, mang đến tài lộc và sức khỏe dồi dào.</p>

<h3>1. Gia Chủ Mệnh Kim</h3>
<p>Nên chọn rèm cửa có tông màu trắng tinh khôi, ánh bạc lụa, xám khói hoặc màu vàng kem, nâu đất (Thổ sinh Kim). Tránh sử dụng quá nhiều rèm màu đỏ, hồng đậm (Hỏa khắc Kim).</p>

<h3>2. Gia Chủ Mệnh Mộc</h3>
<p>Các gam màu xanh ngọc bích, xanh lá cây nhạt hoặc xanh lam than, đen huyền bí (Thủy sinh Mộc) là lựa chọn lý tưởng. Sự tươi mát của màu xanh giúp căn phòng ngập tràn sức sống thiên nhiên.</p>

<h3>3. Gia Chủ Mệnh Thủy</h3>
<p>Ưu tiên các sắc thái xanh dương mát mẻ, xanh biển sâu hoặc màu trắng, ánh bạc sáng (Kim sinh Thủy). Không nên dùng rèm màu vàng đất sẫm hoặc nâu cà phê quá đậm (Thổ khắc Thủy).</p>

<h3>4. Gia Chủ Mệnh Hỏa</h3>
<p>Nên chọn màu rèm ấm cúng như đỏ đô rượu vang, cam đất nung, hồng pastel kết hợp xanh lá mạ (Mộc sinh Hỏa). Không nên lạm dụng rèm màu đen hoặc xanh nước biển đậm.</p>

<h3>5. Gia Chủ Mệnh Thổ</h3>
<p>Màu vàng mù tạt, nâu socola, màu be vàng cát hoặc màu cam đào, tím nhạt (Hỏa sinh Thổ) sẽ mang lại cảm giác vững chãi, ấm áp và bình an.</p>',
                'views' => 840,
            ],
            [
                'title' => 'Kinh Nghiệm Chọn Rèm Cản Nhiệt Và Chống Tia UV Cho Căn Hộ Hướng Tây',
                'slug' => 'kinh-nghiem-chon-rem-can-nhiet-chong-uv-huong-tay',
                'category' => 'guide',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=80',
                'excerpt' => 'Giải pháp xử lý nắng gắt buổi chiều cho chung cư hướng Tây bằng rèm vải tráng cao su non silicone và rèm cuốn chống nắng cản nhiệt 100%, tiết kiệm 30% điện năng điều hòa.',
                'content' => '<h2>Nỗi Ám Ảnh Của Căn Hộ Hướng Tây Vào Mùa Hè</h2>
<p>Các ô cửa kính ban công hoặc phòng ngủ hướng Tây phải hứng chịu luồng bức xạ nhiệt gay gắt từ 13h đến 17h hàng ngày. Nhiệt độ trong phòng có thể tăng từ 4 đến 7 độ C, khiến hóa đơn tiền điện điều hòa tăng vọt và làm bong tróc đồ nội thất gỗ.</p>

<h3>Giải Pháp 1: Rèm Vải Phủ Lớp Silicone Cách Nhiệt Chuyên Dụng</h3>
<p>Dòng vải rèm công nghệ dệt 3 lớp có màng ngăn tia hồng ngoại và tia cực tím UV giúp chặn đứng 100% ánh sáng mặt trời. Mặt ngoài của rèm phản xạ nhiệt ngược ra môi trường, giúp không khí trong phòng luôn dịu mát.</p>

<h3>Giải Pháp 2: Rèm Cuốn Tráng Nhựa Cản Nhiệt Chống Bám Bụi</h3>
<p>Đối với các ô cửa sổ nhỏ hoặc khu vực bàn làm việc, rèm cuốn chống nắng sợi thủy tinh phủ nhựa PVC là giải pháp kinh tế nhưng đạt hiệu quả cách nhiệt cực cao. Bề mặt nhẵn bóng không thấm nước, dễ dàng lau chùi bằng khăn ẩm.</p>',
                'views' => 760,
            ],
            [
                'title' => 'So Sánh Rèm Cầu Vồng Hàn Quốc Và Rèm Cuốn Trơn: Ưu Nhược Điểm & Chi Phí',
                'slug' => 'so-sanh-rem-cau-vong-va-rem-cuon-tron',
                'category' => 'news',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=900&q=80',
                'excerpt' => 'Phân tích chi tiết giữa hai loại rèm hiện đại được ưa chuộng nhất hiện nay về tính thẩm mỹ, độ bền cơ học, khả năng lấy sáng và bảng giá trên từng mét vuông.',
                'content' => '<h2>Nên Chọn Rèm Cầu Vồng Hay Rèm Cuốn Trơn?</h2>
<p>Cả rèm cầu vồng và rèm cuốn đều là các dòng rèm hiện đại có cơ chế cuốn trục gọn gàng. Tuy nhiên mỗi loại lại có những thế mạnh riêng biệt phù hợp với từng không gian sống.</p>

<h3>1. Rèm Cầu Vồng Hàn Quốc (Combi Blinds)</h3>
<ul>
    <li><strong>Thẩm mỹ:</strong> Rất cao, các sọc ngang tạo cảm giác phòng rộng hơn, mang hơi thở sang trọng và thanh lịch.</li>
    <li><strong>Khả năng lấy sáng:</strong> Linh hoạt tuyệt đối, có thể kéo so le để lấy gió và ánh sáng nhẹ mà người ngoài không nhìn thấy được bên trong.</li>
    <li><strong>Mức giá tham khảo:</strong> Dao động từ <strong>450.000đ – 850.000đ / m²</strong> tùy vào độ dày sợi vải và xuất xứ hộp phụ kiện.</li>
</ul>

<h3>2. Rèm Cuốn Trơn (Roller Blinds)</h3>
<ul>
    <li><strong>Thẩm mỹ:</strong> Đơn giản, hiện đại, thích hợp cho phong cách văn phòng, phòng đọc sách hoặc quán cafe.</li>
    <li><strong>Khả năng cản nắng:</strong> Cản sáng và cách nhiệt tuyệt đối 100%.</li>
    <li><strong>Mức giá tham khảo:</strong> Dao động từ <strong>280.000đ – 420.000đ / m²</strong>.</li>
</ul>',
                'views' => 1120,
            ],
        ];

        foreach ($articles as $art) {
            News::updateOrCreate(
                ['slug' => $art['slug']],
                [
                    'title'         => $art['title'],
                    'thumbnail_url' => $art['thumbnail_url'],
                    'excerpt'       => $art['excerpt'],
                    'content'       => $art['content'],
                    'category'      => $art['category'],
                    'author_id'     => $authorId,
                    'status'        => 'published',
                    'views'         => $art['views'],
                ]
            );
        }
    }
}
