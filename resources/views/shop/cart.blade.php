<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng & Cấu Hình Rèm Cửa | CurtainLux</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- External Shop CSS -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
</head>
<body>

    @include('shop.partials.header')

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('shop.index') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <span>/</span>
            <span style="color: var(--text-main); font-weight: 600;">Giỏ Hàng Đặt May Rèm</span>
        </div>

        @if(session('success'))
            <div class="alert-success" style="margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-danger" style="background: #fef2f2; border: 1px solid #f87171; color: #b91c1c; padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger" style="background: #fef2f2; border: 1px solid #f87171; color: #b91c1c; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-circle-exclamation"></i> Vui lòng kiểm tra lại thông tin:</div>
                <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('order_success'))
            @php $order = session('order_success'); @endphp
            <div style="background: #FFFFFF; border: 2px solid #16a34a; border-radius: var(--radius-md); padding: 40px 30px; text-align: center; margin-bottom: 30px; box-shadow: 0 10px 25px -5px rgba(22, 163, 74, 0.1);">
                <div style="width: 70px; height: 70px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: #15803d; margin-bottom: 8px;">
                    Đặt May Rèm Cửa Thành Công!
                </h2>
                <p style="font-size: 15px; color: var(--text-muted); max-width: 600px; margin: 0 auto 20px;">
                    Cảm ơn quý khách <strong>{{ $order->customer_name }}</strong>. Mã đơn hàng may đo của quý khách là:
                </p>
                <div style="display: inline-block; background: #f0fdf4; border: 1px dashed #22c55e; padding: 10px 24px; border-radius: 8px; font-size: 20px; font-weight: 800; color: #16a34a; letter-spacing: 1px; margin-bottom: 24px;">
                    <i class="fa-solid fa-receipt"></i> {{ $order->order_code }}
                </div>
                <div style="background: #f8fafc; border-radius: 8px; padding: 20px; max-width: 600px; margin: 0 auto 24px; text-align: left; font-size: 14px; line-height: 1.8;">
                    <div><i class="fa-solid fa-phone" style="color: var(--accent-cta); width: 20px;"></i> <strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                    <div><i class="fa-solid fa-location-dot" style="color: var(--accent-cta); width: 20px;"></i> <strong>Địa chỉ lắp đặt:</strong> {{ $order->shipping_address }}</div>
                    <div><i class="fa-solid fa-money-bill-wave" style="color: var(--accent-cta); width: 20px;"></i> <strong>Tổng chi phí:</strong> <span style="font-weight: 800; color: var(--accent-cta);">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span></div>
                    <div><i class="fa-solid fa-credit-card" style="color: var(--accent-cta); width: 20px;"></i> <strong>Hình thức thanh toán:</strong> {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng / nghiệm thu lắp đặt' : 'Chuyển khoản ngân hàng' }}</div>
                    <div><i class="fa-solid fa-clock" style="color: var(--accent-cta); width: 20px;"></i> <strong>Trạng thái đơn:</strong> <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 4px; font-weight: 600;">Chờ xưởng may xác nhận & lên lịch</span></div>
                    @if($order->notes)
                        <div><i class="fa-solid fa-clipboard" style="color: var(--accent-cta); width: 20px;"></i> <strong>Ghi chú:</strong> {{ $order->notes }}</div>
                    @endif
                </div>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">
                    <i class="fa-solid fa-headset" style="color: var(--accent-cta);"></i> Chuyên viên kỹ thuật CurtainLux sẽ liên hệ quý khách trong vòng 15 phút để xác nhận số đo thực tế trước khi may.
                </p>
                <a href="{{ route('shop.index') }}" class="btn-book-survey" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                    <i class="fa-solid fa-store"></i> Tiếp Tục Xem Mẫu Rèm Khác
                </a>
            </div>
        @endif

        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 26px; font-weight: 800; color: var(--text-main);">
                Danh Sách Ô Cửa Đặt May ({{ $cartItems->count() }} bộ rèm)
            </h1>
            <p style="font-size: 14px; color: var(--text-muted);">
                Các thông số kích thước và phụ kiện được xưởng may CurtainLux gia công may đo thủ công chính xác theo yêu cầu của bạn.
            </p>
        </div>

        @if($cartItems->count() > 0)
            <div class="cart-layout">
                <!-- Left: Cart Items List + Checkout Customer Form -->
                <div>
                    <!-- Items Table Card -->
                    <div class="cart-table-card">
                        <div style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border-line); display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-ruler-combined" style="color: var(--accent-cta);"></i> Chi Tiết Các Ô Cửa Rèm Trong Đơn
                        </div>

                        @foreach($cartItems as $item)
                            <div class="cart-item-row">
                                <img src="{{ $item->product->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=300&q=80' }}" 
                                     alt="{{ $item->product->name }}" class="cart-item-thumb">

                                <div class="cart-item-content">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <h3 class="cart-item-title">
                                            <a href="{{ route('shop.show', $item->product->slug) }}" style="color: inherit; text-decoration: none;">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-remove-item" onclick="return confirm('Bạn có chắc muốn xóa bộ rèm này khỏi giỏ hàng?')">
                                                <i class="fa-solid fa-trash-can"></i> Xóa
                                            </button>
                                        </form>
                                    </div>

                                    <div style="background: var(--bg-subtle); padding: 8px 12px; border-radius: var(--radius-sm); margin: 6px 0 10px; display: inline-block;">
                                        <strong style="color: var(--accent-cta); font-size: 13px;">
                                            <i class="fa-solid fa-tag"></i> {{ $item->room_label ?: 'Ô cửa chưa đặt tên' }}
                                        </strong>
                                    </div>

                                    <div class="cart-item-specs">
                                        <div>
                                            <strong>Kích thước may:</strong> Rộng <strong>{{ $item->width }} cm</strong> &times; Cao <strong>{{ $item->height }} cm</strong>
                                            ({{ $item->mount_type === 'inside' ? 'Lắp lọt lòng' : 'Lắp phủ bì tường' }})
                                            &bull; Quy đổi tính tiền: <strong>{{ $item->calculated_units }} {{ $item->product->unit_label }}</strong>
                                        </div>

                                        @if(!empty($item->selected_options) && is_array($item->selected_options))
                                            <div style="margin-top: 4px;">
                                                <strong>Tùy chọn gia công:</strong>
                                                @foreach($item->selected_options as $groupCode => $opt)
                                                    <span class="chip" style="margin-right: 4px; margin-top: 4px; display: inline-block; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                        {{ $opt['name'] }}
                                                        @if(isset($opt['calculated_cost']) && $opt['calculated_cost'] > 0)
                                                            (+{{ number_format($opt['calculated_cost'], 0, ',', '.') }}₫)
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Interactive Quantity Stepper & Real-time Stock Badge -->
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 14px; padding-top: 10px; border-top: 1px dashed var(--border-line);">
                                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                            <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">Số lượng may:</span>
                                            <div style="display: inline-flex; align-items: center; border: 1px solid var(--border-line); border-radius: 6px; overflow: hidden; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                <button type="button" class="btn-qty" onclick="updateCartItemQty({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }} 
                                                        style="border: none; background: #f8fafc; padding: 6px 12px; cursor: pointer; font-weight: 700; color: var(--text-main); font-size: 14px; transition: background 0.2s;">
                                                    <i class="fa-solid fa-minus" style="font-size: 11px;"></i>
                                                </button>
                                                <input type="number" id="cart-item-qty-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="100" 
                                                       onchange="updateCartItemQty({{ $item->id }}, this.value)" 
                                                       style="width: 46px; text-align: center; border: none; font-weight: 700; font-size: 14px; outline: none; background: #fff;">
                                                <button type="button" class="btn-qty" onclick="updateCartItemQty({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                                        style="border: none; background: #f8fafc; padding: 6px 12px; cursor: pointer; font-weight: 700; color: var(--text-main); font-size: 14px; transition: background 0.2s;">
                                                    <i class="fa-solid fa-plus" style="font-size: 11px;"></i>
                                                </button>
                                            </div>
                                            <span class="stock-badge" style="font-size: 12px; background: #f0fdf4; color: #15803d; padding: 4px 10px; border-radius: 4px; font-weight: 600; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 6px;">
                                                <i class="fa-solid fa-boxes-stacked" style="color: #16a34a;"></i> 
                                                Tồn xưởng: <strong>{{ $item->product->stock }} {{ $item->product->stock_unit_label }}</strong>
                                            </span>
                                        </div>

                                        <div style="text-align: right;">
                                            <span style="font-size: 12px; color: var(--text-muted); display: block;">Thành tiền ô cửa:</span>
                                            <span id="cart-item-subtotal-{{ $item->id }}" style="font-size: 17px; font-weight: 800; color: var(--accent-cta);">
                                                {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Customer Information & Shipping Form Card -->
                    <div class="cart-table-card" style="margin-top: 24px;">
                        <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 16px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-truck-ramp-box" style="color: var(--accent-cta);"></i> Thông Tin Người Nhận & Địa Chỉ Lắp Đặt May Đo
                        </h3>

                        <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div class="form-field">
                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Họ và Tên Quý Khách *</label>
                                    <input type="text" name="customer_name" required value="{{ old('customer_name', Auth::user()?->name) }}" 
                                           placeholder="Vd: Nguyễn Văn An" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: var(--radius-sm); font-size: 14px;">
                                </div>
                                <div class="form-field">
                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Số Điện Thoại Liên Hệ *</label>
                                    <input type="tel" name="customer_phone" required value="{{ old('customer_phone', Auth::user()?->phone) }}" 
                                           placeholder="Vd: 0912345678" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: var(--radius-sm); font-size: 14px;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div class="form-field">
                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Địa Chỉ Nhận Hàng & Lắp Đặt *</label>
                                    <input type="text" name="shipping_address" required value="{{ old('shipping_address', Auth::user()?->address) }}" 
                                           placeholder="Vd: Tầng 12, Chung cư Sunrise City, Quận 7, TP.HCM" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: var(--radius-sm); font-size: 14px;">
                                </div>
                                <div class="form-field">
                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Email Nhận Báo Giá (Không bắt buộc)</label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email', Auth::user()?->email) }}" 
                                           placeholder="email@example.com" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: var(--radius-sm); font-size: 14px;">
                                </div>
                            </div>

                            <div class="form-field" style="margin-bottom: 16px;">
                                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Ghi Chú Đơn May (Khung giờ tiện nghe máy hoặc yêu cầu đặc biệt cho thợ)</label>
                                <textarea name="notes" rows="2" placeholder="Vd: Thợ đo sau 17h chiều; Mang thêm bảng mẫu vải thực tế..." 
                                          style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: var(--radius-sm); font-size: 14px; resize: vertical;">{{ old('notes') }}</textarea>
                            </div>

                            @php
                                $bankTransferEnabled = \App\Models\Setting::get('enable_bank_transfer', '1') !== '0';
                            @endphp

                            <div class="form-field">
                                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">Phương Thức Thanh Toán *</label>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                                    
                                    <!-- 1. Bank Transfer VietQR Option (Mã QR Ngân Hàng MB) -->
                                    <label style="display: flex; align-items: flex-start; gap: 10px; padding: 14px; border: 2px solid #2563eb; border-radius: var(--radius-sm); cursor: pointer; background: #eff6ff; transition: border-color 0.2s;">
                                        <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method', 'bank_transfer') === 'bank_transfer' ? 'checked' : '' }} style="margin-top: 3px;">
                                        <div>
                                            <div style="font-weight: 700; font-size: 13px; color: #1e40af;"><i class="fa-solid fa-qrcode" style="color: #2563eb;"></i> Chuyển Khoản Ngân Hàng (Mã QR VietQR)</div>
                                            <div style="font-size: 12px; color: #3b82f6; margin-top: 2px;">Tự động tạo mã QR tài khoản MB Bank, quét mã bằng app ngân hàng nhận diện ngay 24/7.</div>
                                        </div>
                                    </label>

                                    <!-- 2. COD Option -->
                                    <label style="display: flex; align-items: flex-start; gap: 10px; padding: 14px; border: 1px solid var(--border-line); border-radius: var(--radius-sm); cursor: pointer; background: #f8fafc; transition: border-color 0.2s;">
                                        <input type="radio" name="payment_method" value="cod" {{ old('payment_method') === 'cod' ? 'checked' : '' }} style="margin-top: 3px;">
                                        <div>
                                            <div style="font-weight: 700; font-size: 13px; color: var(--text-main);"><i class="fa-solid fa-hand-holding-dollar" style="color: var(--accent-cta);"></i> Thanh Toán Tiền Mặt Khi Nhận & Lắp Đặt</div>
                                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Kiểm tra rèm chuẩn kích thước rồi mới thanh toán tiền mặt (COD).</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Summary Card -->
                <div class="cart-summary-card">
                    <h3 class="cart-summary-title">Tóm Tắt Đơn May Rèm</h3>

                    <div class="cart-summary-row">
                        <span>Số lượng bộ rèm:</span>
                        <strong id="summary-total-items" style="color: var(--text-main);">{{ $cartItems->sum('quantity') }} bộ</strong>
                    </div>

                    <div class="cart-summary-row">
                        <span>Công lắp đặt tận nhà:</span>
                        <span style="color: #16a34a; font-weight: 700;">MIỄN PHÍ</span>
                    </div>

                    <div class="cart-summary-row">
                        <span>Phụ kiện & Ốc vít gia cố:</span>
                        <span style="color: #16a34a; font-weight: 700;">ĐÃ BAO GỒM</span>
                    </div>

                    <div class="cart-summary-total">
                        <span>Tổng Chi Phí:</span>
                        <span id="summary-total-amount" class="val">{{ number_format($totalAmount, 0, ',', '.') }} ₫</span>
                    </div>

                    <button type="submit" form="checkoutForm" class="btn-add-cart" 
                            style="width: 100%; margin-top: 20px; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fa-solid fa-circle-check"></i> Xác Nhận Đặt May Rèm
                    </button>

                    <div style="margin-top: 18px; font-size: 12px; color: var(--text-muted); line-height: 1.5; text-align: center;">
                        <i class="fa-solid fa-shield-check" style="color: #16a34a;"></i> Cam kết đúng chất liệu vải 100%. Khách hàng được kiểm tra rèm trước khi thanh toán.
                    </div>
                </div>
            </div>
        @else
            <div style="background: #FFFFFF; border: 1px dashed var(--border-line); border-radius: var(--radius-md); padding: 60px 20px; text-align: center;">
                <i class="fa-solid fa-bag-shopping" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Giỏ hàng của bạn đang trống</h3>
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Hãy khám phá các mẫu rèm vải, rèm cầu vồng và rèm cuốn cao cấp để bắt đầu đặt may.</p>
                <a href="{{ route('shop.index') }}" class="btn-book-survey" style="display: inline-flex; text-decoration: none;">
                    <i class="fa-solid fa-store"></i> Khám Phá Bộ Sưu Tập Rèm
                </a>
            </div>
        @endif
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} CurtainLux - Thương Hiệu Rèm Cửa & Nội Thất Vải Tinh Tế. Hotline: 0912.345.678</p>
    </footer>

    <script>
    async function updateCartItemQty(itemId, newQty) {
        newQty = parseInt(newQty);
        if (isNaN(newQty) || newQty < 1) {
            newQty = 1;
        }

        const inputEl = document.getElementById(`cart-item-qty-${itemId}`);
        if (inputEl) inputEl.value = newQty;

        try {
            const res = await fetch(`{{ url('/gio-hang/cap-nhat') }}/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity: newQty })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                // Update item subtotal
                const subtotalEl = document.getElementById(`cart-item-subtotal-${itemId}`);
                if (subtotalEl) subtotalEl.textContent = data.subtotal_formatted;

                // Update summary total
                const totalEl = document.getElementById('summary-total-amount');
                if (totalEl) totalEl.textContent = data.total_amount_formatted;

                // Reload to refresh items summary cleanly
                window.location.reload();
            } else {
                alert(data.message || 'Không thể cập nhật số lượng do giới hạn tồn kho của xưởng.');
                if (data.current_quantity && inputEl) {
                    inputEl.value = data.current_quantity;
                }
            }
        } catch (e) {
            console.error('Error updating cart item quantity:', e);
            alert('Có lỗi xảy ra khi cập nhật số lượng.');
        }
    }
    </script>
</body>
</html>
