<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Thanh Toán Trực Tuyến VNPAY (Sandbox) | CurtainLux</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --vnpay-blue: #005baa;
            --vnpay-red: #ed1c24;
            --surface: #ffffff;
            --bg: #f4f6f9;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }
        .vnpay-header { background: #fff; border-bottom: 1px solid var(--border); padding: 14px 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
        .vnpay-header-inner { max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo-box { display: flex; align-items: center; gap: 12px; }
        .vnpay-logo { font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .vnpay-logo .vn { color: var(--vnpay-red); }
        .vnpay-logo .pay { color: var(--vnpay-blue); }
        .sandbox-badge { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; }
        
        .main-container { max-width: 1000px; margin: 30px auto; padding: 0 16px; flex: 1; width: 100%; display: grid; grid-template-columns: 360px 1fr; gap: 24px; }
        @media (max-width: 860px) { .main-container { grid-template-columns: 1fr; } }

        /* Left: Order Info */
        .order-card { background: #fff; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .merchant-title { font-size: 13px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .merchant-name { font-size: 18px; font-weight: 800; color: var(--vnpay-blue); margin-bottom: 16px; }
        .amount-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px; text-align: center; margin-bottom: 20px; }
        .amount-label { font-size: 12px; color: var(--text-muted); }
        .amount-val { font-size: 24px; font-weight: 800; color: var(--vnpay-red); }
        .order-meta-row { display: flex; justify-content: space-between; font-size: 13px; padding: 8px 0; border-bottom: 1px dashed var(--border); }
        .timer-badge { margin-top: 20px; background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 700; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px; }

        /* Right: Gateway Portal Tabs & Payment Simulation */
        .portal-card { background: #fff; border-radius: 12px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .tab-nav { display: flex; border-bottom: 2px solid var(--border); gap: 12px; margin-bottom: 20px; }
        .tab-btn { background: none; border: none; padding: 10px 14px; font-size: 14px; font-weight: 700; color: var(--text-muted); cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; display: flex; align-items: center; gap: 8px; }
        .tab-btn.active { color: var(--vnpay-blue); border-bottom-color: var(--vnpay-blue); }

        .bank-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-bottom: 20px; }
        .bank-item { border: 1px solid var(--border); border-radius: 6px; padding: 10px; text-align: center; cursor: pointer; transition: all 0.2s; font-size: 12px; font-weight: 700; background: #fff; }
        .bank-item:hover, .bank-item.active { border-color: var(--vnpay-blue); background: #eff6ff; color: var(--vnpay-blue); }

        .test-credential-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; margin-bottom: 20px; font-size: 13px; }
        .test-credential-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .test-credential-row strong { color: var(--vnpay-blue); }

        .btn-pay-success { width: 100%; background: #16a34a; color: #fff; border: none; padding: 14px; border-radius: 8px; font-size: 15px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s; box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2); }
        .btn-pay-success:hover { background: #15803d; }
        
        .simulation-actions { margin-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .btn-fail-cancel { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; text-align: center; text-decoration: none; }
        .btn-fail-cancel:hover { background: #fecaca; }
        .btn-fail-balance { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; text-align: center; text-decoration: none; }
        .btn-fail-balance:hover { background: #fde68a; }

        .vnpay-footer { text-align: center; padding: 20px; font-size: 12px; color: var(--text-muted); border-top: 1px solid var(--border); background: #fff; margin-top: auto; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="vnpay-header">
        <div class="vnpay-header-inner">
            <div class="logo-box">
                <div class="vnpay-logo"><span class="vn">VN</span><span class="pay">PAY</span></div>
                <span class="sandbox-badge">Môi trường thử nghiệm (Sandbox)</span>
            </div>
            <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-lock" style="color: #16a34a;"></i> 256-bit SSL Bảo mật an toàn
            </div>
        </div>
    </header>

    <div class="main-container">
        <!-- Left: Order Summary -->
        <div class="order-card">
            <div class="merchant-title">Đơn vị thụ hưởng</div>
            <div class="merchant-name">CÔNG TY CP CURTAINLUX VIỆT NAM</div>

            <div class="amount-box">
                <div class="amount-label">Số tiền thanh toán</div>
                <div class="amount-val">{{ number_format($amount, 0, ',', '.') }} ₫</div>
            </div>

            <div class="order-meta-row">
                <span style="color: var(--text-muted);">Mã đơn hàng:</span>
                <strong>{{ $order->order_code }}</strong>
            </div>
            <div class="order-meta-row">
                <span style="color: var(--text-muted);">Người đặt may:</span>
                <strong>{{ $order->customer_name }}</strong>
            </div>
            <div class="order-meta-row">
                <span style="color: var(--text-muted);">Số điện thoại:</span>
                <strong>{{ $order->customer_phone }}</strong>
            </div>
            <div class="order-meta-row">
                <span style="color: var(--text-muted);">Nội dung giao dịch:</span>
                <span style="font-size: 12px; text-align: right; max-width: 180px;">{{ $orderInfo }}</span>
            </div>

            <div class="timer-badge">
                <i class="fa-regular fa-clock"></i> Giao dịch hết hạn sau: <span id="countdown">14:59</span>
            </div>
        </div>

        <!-- Right: Payment Gateway Tabs -->
        <div class="portal-card">
            <div class="tab-nav">
                <button type="button" class="tab-btn active" onclick="switchTab('atm')">
                    <i class="fa-solid fa-building-columns"></i> Thẻ ATM Nội Địa & Tài Khoản
                </button>
                <button type="button" class="tab-btn" onclick="switchTab('qr')">
                    <i class="fa-solid fa-qrcode"></i> VNPAY-QR
                </button>
                <button type="button" class="tab-btn" onclick="switchTab('intl')">
                    <i class="fa-brands fa-cc-visa"></i> Thẻ Quốc Tế (Visa/Master)
                </button>
            </div>

            <!-- TAB 1: Thẻ ATM Nội Địa -->
            <div id="tab-atm" class="tab-content">
                <div style="font-size: 13px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">
                    Chọn ngân hàng nội địa thanh toán:
                </div>

                <div class="bank-grid">
                    <div class="bank-item active" onclick="selectBank(this, 'NCB')">NCB</div>
                    <div class="bank-item" onclick="selectBank(this, 'VCB')">Vietcombank</div>
                    <div class="bank-item" onclick="selectBank(this, 'MB')">MBBank</div>
                    <div class="bank-item" onclick="selectBank(this, 'TCB')">Techcombank</div>
                    <div class="bank-item" onclick="selectBank(this, 'ACB')">ACB</div>
                    <div class="bank-item" onclick="selectBank(this, 'BIDV')">BIDV</div>
                    <div class="bank-item" onclick="selectBank(this, 'VTB')">VietinBank</div>
                    <div class="bank-item" onclick="selectBank(this, 'VPB')">VPBank</div>
                </div>

                <div class="test-credential-box">
                    <div style="font-weight: 700; color: var(--vnpay-blue); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-credit-card"></i> Thông tin thẻ Test Sandbox hợp lệ (NCB):
                    </div>
                    <div class="test-credential-row">
                        <span>Số thẻ test:</span>
                        <strong>9704198526191432198</strong>
                    </div>
                    <div class="test-credential-row">
                        <span>Tên chủ thẻ:</span>
                        <strong>NGUYEN VAN A</strong>
                    </div>
                    <div class="test-credential-row">
                        <span>Ngày phát hành:</span>
                        <strong>07/15</strong>
                    </div>
                    <div class="test-credential-row">
                        <span>Mã OTP xác thực:</span>
                        <strong>123456</strong>
                    </div>
                </div>

                <!-- Callback Form Simulation -->
                <form id="vnpayForm" action="{{ route('payment.vnpay.callback') }}" method="GET">
                    <input type="hidden" name="vnp_TxnRef" value="{{ $order->order_code }}">
                    <input type="hidden" name="vnp_Amount" value="{{ $amount * 100 }}">
                    <input type="hidden" id="vnpBankCode" name="vnp_BankCode" value="NCB">
                    <input type="hidden" id="vnpResponseCode" name="vnp_ResponseCode" value="00">
                    <input type="hidden" name="vnp_TransactionNo" value="VNP{{ date('YmdHis') }}">
                    <input type="hidden" name="vnp_OrderInfo" value="{{ $orderInfo }}">

                    <button type="submit" class="btn-pay-success" onclick="document.getElementById('vnpResponseCode').value='00'">
                        <i class="fa-solid fa-circle-check"></i> Xác Nhận Thanh Toán Thành Công (Mã 00)
                    </button>
                </form>

                <div style="margin-top: 16px; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                    Kiểm thử tình huống lỗi / Hủy giao dịch:
                </div>
                <div class="simulation-actions">
                    <button type="button" class="btn-fail-cancel" onclick="submitFail('24')">
                        <i class="fa-solid fa-ban"></i> Mô Phỏng Khách Hủy (Mã 24)
                    </button>
                    <button type="button" class="btn-fail-balance" onclick="submitFail('51')">
                        <i class="fa-solid fa-triangle-exclamation"></i> Không Đủ Số Dư (Mã 51)
                    </button>
                </div>
            </div>

            <!-- TAB 2: VNPAY-QR -->
            <div id="tab-qr" class="tab-content" style="display: none; text-align: center; padding: 20px 0;">
                <div style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">Mở ứng dụng Ngân hàng hoặc Ví VNPAY để quét mã</div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode('VNPAY_SANDBOX_' . $order->order_code . '_' . $amount) }}" 
                     alt="VNPAY-QR" style="border: 2px solid var(--vnpay-blue); border-radius: 12px; padding: 8px; background: #fff;">
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 12px;">
                    Mã kiểm thử: <strong>{{ $order->order_code }}</strong> &bull; Số tiền: <strong style="color: var(--vnpay-red);">{{ number_format($amount, 0, ',', '.') }} ₫</strong>
                </div>
                <div style="margin-top: 20px;">
                    <button type="button" class="btn-pay-success" onclick="submitFail('00')" style="max-width: 320px; margin: 0 auto;">
                        <i class="fa-solid fa-mobile-screen-button"></i> Mô Phỏng Quét QR Thành Công
                    </button>
                </div>
            </div>

            <!-- TAB 3: Thẻ Quốc Tế -->
            <div id="tab-intl" class="tab-content" style="display: none; padding: 10px 0;">
                <div class="test-credential-box">
                    <div style="font-weight: 700; color: var(--vnpay-blue); margin-bottom: 8px;">
                        <i class="fa-brands fa-cc-visa"></i> Thẻ Visa / MasterCard Quốc Tế Sandbox:
                    </div>
                    <div class="test-credential-row">
                        <span>Số thẻ:</span> <strong>4000 0012 3456 7890</strong>
                    </div>
                    <div class="test-credential-row">
                        <span>Hết hạn:</span> <strong>12/28</strong> &bull; CVV: <strong>123</strong>
                    </div>
                </div>
                <button type="button" class="btn-pay-success" onclick="submitFail('00')">
                    <i class="fa-solid fa-circle-check"></i> Xác Nhận Thanh Toán Thẻ Quốc Tế
                </button>
            </div>
        </div>
    </div>

    <footer class="vnpay-footer">
        Cổng thanh toán điện tử VNPAY &bull; Bản quyền &copy; {{ date('Y') }} VNPAY. Môi trường kiểm thử Sandbox phục vụ kiểm thử đơn hàng rèm CurtainLux.
    </footer>

    <script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(`tab-${tabId}`).style.display = 'block';
        event.currentTarget.classList.add('active');
    }

    function selectBank(el, code) {
        document.querySelectorAll('.bank-item').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('vnpBankCode').value = code;
    }

    function submitFail(code) {
        document.getElementById('vnpResponseCode').value = code;
        document.getElementById('vnpayForm').submit();
    }

    // Countdown Timer
    let secondsLeft = 15 * 60;
    setInterval(() => {
        if (secondsLeft > 0) {
            secondsLeft--;
            const mins = Math.floor(secondsLeft / 60).toString().padStart(2, '0');
            const secs = (secondsLeft % 60).toString().padStart(2, '0');
            document.getElementById('countdown').textContent = `${mins}:${secs}`;
        }
    }, 1000);
    </script>
</body>
</html>
