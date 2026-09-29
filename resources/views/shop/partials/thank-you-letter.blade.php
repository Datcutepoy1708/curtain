<div class="thank-you-letter-card" id="thankYouLetterSection">
    <div class="letter-seal-wrapper">
        <div class="letter-wax-seal">
            <i class="fa-solid fa-award"></i>
            <span>CURTAINLUX</span>
        </div>
    </div>

    <div class="letter-header">
        <div class="letter-brand-eyebrow">CURTAINLUX BESPOKE INTERIORS</div>
        <h2 class="letter-title">THƯ TRI ÂN KHÁCH HÀNG</h2>
        <div class="letter-subtitle">Thương hiệu rèm may đo & Kiến tạo không gian sống cao cấp</div>
        <div class="letter-divider">
            <span class="divider-line"></span>
            <span class="divider-ornament">✦</span>
            <span class="divider-line"></span>
        </div>
    </div>

    <div class="letter-body">
        <p class="letter-salutation">
            Kính gửi Quý khách <strong class="letter-highlight">{{ $order->customer_name }}</strong>,
        </p>

        <p>
            Lời đầu tiên, toàn thể đội ngũ nghệ nhân may đo và kỹ thuật viên tại <strong>CurtainLux</strong> xin gửi tới Quý khách lời tri ân chân thành và sâu sắc nhất vì đã tin tưởng lựa chọn chúng tôi để đồng hành làm đẹp cho không gian sống của gia đình mình.
        </p>

        <div class="letter-order-badge-row">
            <div class="order-badge-pill">
                <i class="fa-solid fa-receipt"></i>
                <span>Mã đơn: <strong>{{ $order->order_code }}</strong></span>
            </div>
            <div class="order-badge-pill">
                <i class="fa-solid fa-calendar-day"></i>
                <span>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="order-badge-pill pill-status">
                @if($order->payment_status === 'paid')
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                    <span style="color: #065f46; font-weight: 700;">Đã Thanh Toán Hoàn Tất</span>
                @else
                    <i class="fa-solid fa-clock" style="color: #f59e0b;"></i>
                    <span style="color: #92400e; font-weight: 700;">Đã Đặt Hàng Thành Công (COD)</span>
                @endif
            </div>
        </div>

        <p>
            Chúng tôi hiểu rằng mỗi khung cửa sổ không đơn thuần là nơi đón ánh sáng, mà là linh hồn kết nối sự ấm cúng, sang trọng và phong cách riêng của từng tổ ấm. Đơn hàng của Quý khách đã được tiếp nhận và chuyển đến <strong>xưởng may đo chuyên trách</strong>, nơi từng mét vải, sợi chỉ và phụ kiện thanh treo đều được gia công với độ chính xác cao nhất theo chuẩn kích thước thực tế.
        </p>

        <div class="letter-commitments-grid">
            <div class="commitment-item">
                <i class="fa-solid fa-shield-halved"></i>
                <div>
                    <strong>Bảo Hành 3 - 5 Năm</strong>
                    <p>Bảo hành ray treo, phụ kiện và hỗ trợ căn chỉnh động cơ thông minh dài hạn.</p>
                </div>
            </div>
            <div class="commitment-item">
                <i class="fa-solid fa-ruler-combined"></i>
                <div>
                    <strong>May Chuẩn Centimet</strong>
                    <p>Kiểm tra phôi vải, cắt may tỉ mỉ, độ chun rủ sóng hoàn hảo 100%.</p>
                </div>
            </div>
            <div class="commitment-item">
                <i class="fa-solid fa-headset"></i>
                <div>
                    <strong>Đồng Hành Trọn Đời</strong>
                    <p>Hỗ trợ vệ sinh, giặt ủi định kỳ và tư vấn phong thủy ánh sáng miễn phí.</p>
                </div>
            </div>
        </div>

        <p style="margin-top: 20px;">
            Kính chúc Quý khách và gia đình luôn dồi dào sức khỏe, hạnh phúc và luôn tìm thấy sự thư thái, bình yên trọn vẹn trong không gian sống tiện nghi!
        </p>

        <div class="letter-footer-signature">
            <div class="sig-left">
                <div class="sig-title">Tổng đài hỗ trợ khách hàng:</div>
                <div class="sig-hotline"><i class="fa-solid fa-phone"></i> 0912.345.678 (24/7)</div>
                <div class="sig-sub">Showroom CurtainLux: Số 12A Phố Nội Thất, Hà Nội</div>
            </div>
            <div class="sig-right">
                <div class="sig-role">Thay mặt đội ngũ CurtainLux</div>
                <div class="sig-signature">CurtainLux Bespoke</div>
                <div class="sig-signer">Ban Giám Đốc & Xưởng May Đo Nghệ Nhân</div>
            </div>
        </div>
    </div>

    <!-- Letter Action Buttons -->
    <div class="letter-actions no-print">
        <button type="button" class="btn-letter btn-print-letter" onclick="window.print()">
            <i class="fa-solid fa-print"></i> In Thư Cảm Ơn & Hóa Đơn
        </button>
        <a href="{{ route('shop.index') }}" class="btn-letter btn-shop-more">
            <i class="fa-solid fa-store"></i> Tiếp Tục Mua Sắm
        </a>
        <a href="{{ route('order.tracking', ['keyword' => $order->order_code]) }}" class="btn-letter btn-track-order">
            <i class="fa-solid fa-truck-fast"></i> Theo Dõi Tiến Độ Đơn
        </a>
    </div>
</div>

<style>
/* ═════════════════════════════════════════════════════════════════
   THƯ CẢM ƠN JAPANDI LUXURY STYLES
   ═════════════════════════════════════════════════════════════════ */
.thank-you-letter-card {
    position: relative;
    background: #fffdfa;
    border: 2px dashed #e2d1bc;
    border-radius: 18px;
    padding: 44px 38px 36px;
    margin: 32px 0;
    box-shadow: 0 14px 34px -10px rgba(184, 147, 92, 0.15), 0 2px 6px rgba(0, 0, 0, 0.02);
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: #2c251e;
    overflow: hidden;
}

.thank-you-letter-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #b8935c 0%, #e2d1bc 50%, #b8935c 100%);
}

.letter-seal-wrapper {
    position: absolute;
    top: 24px;
    right: 32px;
}

.letter-wax-seal {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: radial-gradient(circle, #b8935c 0%, #8a6936 100%);
    box-shadow: 0 4px 12px rgba(138, 105, 54, 0.35), inset 0 2px 4px rgba(255, 255, 255, 0.3);
    border: 2px solid #ecd8bf;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 20px;
    text-align: center;
    line-height: 1;
    transform: rotate(-8deg);
}

.letter-wax-seal span {
    font-size: 8px;
    font-weight: 800;
    letter-spacing: 0.5px;
    margin-top: 3px;
    text-transform: uppercase;
}

.letter-header {
    text-align: center;
    margin-bottom: 24px;
    padding-top: 6px;
}

.letter-brand-eyebrow {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
    color: #b8935c;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.letter-title {
    font-size: 24px;
    font-weight: 800;
    color: #2d2621;
    margin: 0 0 6px;
    letter-spacing: -0.3px;
}

.letter-subtitle {
    font-size: 13.5px;
    color: #786959;
    font-style: italic;
}

.letter-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 14px;
}

.divider-line {
    width: 80px;
    height: 1px;
    background: #e2d1bc;
}

.divider-ornament {
    color: #b8935c;
    font-size: 14px;
}

.letter-body {
    font-size: 14.5px;
    line-height: 1.8;
    color: #4a3e35;
}

.letter-salutation {
    font-size: 16px;
    margin-bottom: 14px;
}

.letter-highlight {
    color: #b8935c;
    font-weight: 700;
}

.letter-order-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 18px 0 20px;
}

.order-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: #f5ede1;
    border: 1px solid #ebd9c2;
    border-radius: 9999px;
    font-size: 13px;
    color: #5c4735;
}

.order-badge-pill.pill-status {
    background: #ecfdf5;
    border-color: #a7f3d0;
}

.letter-commitments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin: 22px 0;
    padding: 16px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #ebd9c2;
}

.commitment-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.commitment-item i {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #faf4eb;
    color: #b8935c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}

.commitment-item strong {
    font-size: 13.5px;
    color: #2d2621;
    display: block;
    margin-bottom: 2px;
}

.commitment-item p {
    font-size: 12px;
    color: #786959;
    margin: 0;
    line-height: 1.45;
}

.letter-footer-signature {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #ebd9c2;
    flex-wrap: wrap;
    gap: 20px;
}

.sig-left .sig-title {
    font-size: 12px;
    color: #8c7865;
    margin-bottom: 3px;
}

.sig-left .sig-hotline {
    font-size: 16px;
    font-weight: 800;
    color: #b8935c;
    display: flex;
    align-items: center;
    gap: 8px;
}

.sig-left .sig-sub {
    font-size: 12px;
    color: #8c7865;
    margin-top: 3px;
}

.sig-right {
    text-align: right;
}

.sig-role {
    font-size: 12.5px;
    color: #786959;
    font-style: italic;
    margin-bottom: 6px;
}

.sig-signature {
    font-family: 'Brush Script MT', 'Dancing Script', cursive, sans-serif;
    font-size: 28px;
    color: #b8935c;
    line-height: 1.2;
    letter-spacing: 1px;
}

.sig-signer {
    font-size: 12px;
    font-weight: 700;
    color: #2d2621;
    margin-top: 4px;
}

.letter-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 28px;
    flex-wrap: wrap;
}

.btn-letter {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}

.btn-print-letter {
    background: #b8935c;
    color: #ffffff;
}

.btn-print-letter:hover {
    background: #9d7b47;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(184, 147, 92, 0.25);
}

.btn-shop-more {
    background: #ffffff;
    color: #4a3e35;
    border: 1px solid #ebd9c2;
}

.btn-shop-more:hover {
    background: #faf4eb;
    color: #2d2621;
}

.btn-track-order {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.btn-track-order:hover {
    background: #dcfce7;
}

/* In ấn tối ưu */
@media print {
    body * {
        visibility: hidden;
    }
    #thankYouLetterSection, #thankYouLetterSection * {
        visibility: visible;
    }
    #thankYouLetterSection {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    .no-print {
        display: none !important;
    }
}

@media (max-width: 640px) {
    .thank-you-letter-card {
        padding: 30px 20px 24px;
    }
    .letter-seal-wrapper {
        position: static;
        display: flex;
        justify-content: center;
        margin-bottom: 12px;
    }
    .letter-footer-signature {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .sig-right {
        text-align: center;
    }
}
</style>
