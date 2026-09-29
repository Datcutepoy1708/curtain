<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư Tri Ân & Xác Nhận Đơn Hàng — CurtainLux</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f7f4ee; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #332a24;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f7f4ee; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e8decb;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 35px 30px 25px; background: linear-gradient(135deg, #2d2621 0%, #1f1a17 100%); color: #ffffff;">
                            <div style="font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #d4af37; font-weight: 700; margin-bottom: 8px;">CURTAINLUX BESPOKE INTERIORS</div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">THƯ TRI ÂN KHÁCH HÀNG</h1>
                            <div style="font-size: 13px; color: #d6cbbf; margin-top: 6px; font-style: italic;">Kiến tạo vẻ đẹp tinh tế & ấm cúng cho tổ ấm của bạn</div>
                        </td>
                    </tr>

                    <!-- Letter Body -->
                    <tr>
                        <td style="padding: 35px 35px 25px; font-size: 15px; line-height: 1.7; color: #4a3e35;">
                            <p style="margin-top: 0; font-size: 16px;">
                                Kính gửi Quý khách <strong style="color: #b8935c;">{{ $order->customer_name }}</strong>,
                            </p>

                            <p>
                                Đội ngũ nghệ nhân và kỹ thuật viên tại <strong>CurtainLux</strong> xin gửi tới Quý khách lời tri ân chân thành nhất vì đã tin tưởng lựa chọn thương hiệu của chúng tôi để may đo rèm cửa cho ngôi nhà của mình.
                            </p>

                            <!-- Order Summary Box -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #faf6f0; border: 1px solid #e8decb; border-radius: 12px; margin: 24px 0; padding: 18px 20px;">
                                <tr>
                                    <td style="font-size: 14px; padding-bottom: 8px;">
                                        <strong>Mã đơn may rèm:</strong> <span style="color: #b8935c; font-weight: 800; font-family: monospace; font-size: 15px;">{{ $order->order_code }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; padding-bottom: 8px;">
                                        <strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; padding-bottom: 8px;">
                                        <strong>Phương thức:</strong> {{ $order->payment_method === 'cod' ? 'Thanh toán tiền mặt khi nhận rèm (COD)' : 'Chuyển khoản Ngân hàng (VietQR MB Bank)' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; padding-bottom: 8px;">
                                        <strong>Trạng thái thanh toán:</strong> 
                                        @if($order->payment_status === 'paid')
                                            <span style="color: #16a34a; font-weight: 700;">Đã thanh toán thành công</span>
                                        @else
                                            <span style="color: #d97706; font-weight: 700;">Chờ thanh toán khi nhận hàng</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 15px; padding-top: 8px; border-top: 1px dashed #d8c8b4;">
                                        <strong>Tổng chi phí:</strong> 
                                        <span style="color: #d97706; font-size: 18px; font-weight: 800;">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                                    </td>
                                </tr>
                            </table>

                            <p>
                                Đơn hàng của Quý khách đã được chuyển sang <strong>xưởng may đo chuyên trách</strong>. Chúng tôi cam kết từng đường kim mũi chỉ, sóng rèm và phụ kiện thanh treo đều đạt tiêu chuẩn thẩm mỹ cao nhất trước khi bàn giao.
                            </p>

                            <!-- Commitments -->
                            <div style="background-color: #ffffff; border-left: 4px solid #b8935c; padding: 14px 18px; margin: 20px 0; font-size: 13.5px; line-height: 1.6; color: #5c4735;">
                                ✦ <strong>Chính sách bảo hành:</strong> Bảo hành chính hãng 3 - 5 năm ray treo & động cơ.<br>
                                ✦ <strong>Hỗ trợ kỹ thuật:</strong> Kiểm tra, vệ sinh và căn chỉnh rèm trọn đời.<br>
                                ✦ <strong>Cam kết chất lượng:</strong> Đúng 100% cây mẫu vải Quý khách đã chọn.
                            </div>

                            <p>
                                Chúc Quý khách và gia đình luôn an khang, thịnh vượng trong không gian sống ấm áp và sang trọng!
                            </p>

                            <!-- Signature -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e8decb;">
                                <tr>
                                    <td align="left" style="font-size: 13px; color: #786959;">
                                        Hotline CSKH: <strong>0912.345.678</strong> (24/7)<br>
                                        Địa chỉ Showroom: Số 12A Phố Nội Thất, Hà Nội
                                    </td>
                                    <td align="right" style="font-size: 13px; color: #786959; font-style: italic;">
                                        Trân trọng cảm ơn,<br>
                                        <strong style="color: #b8935c; font-size: 15px; font-style: normal;">CurtainLux Bespoke Team</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #faf6f0; font-size: 12px; color: #8c7865; border-top: 1px solid #e8decb;">
                            Email này được gửi tự động từ hệ thống thương mại điện tử CurtainLux.<br>
                            Mọi thắc mắc xin vui lòng liên hệ hotline 0912.345.678 hoặc email cskh@curtainlux.vn.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
