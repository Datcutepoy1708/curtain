@extends('shop.account.layout')

@section('account-title', 'Bảng Báo Giá May Đo Rèm #' . $quotation->quotation_code)

@section('account-content')
<div style="margin-bottom: 20px;">
    <a href="{{ $guestAccess ? url('/') : route('customer.consultations') }}" class="btn-account-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; padding: 6px 14px; border-radius: 6px; border: 1px solid var(--border); color: var(--text-main);">
        <i class="fa-solid fa-arrow-left"></i> Quay lại lịch khảo sát
    </a>
</div>

<!-- Header Card -->
<div class="account-card" style="background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 20px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-main);">
                    Bảng Báo Giá May Đo Chính Thức
                </h2>
                <span style="background: var(--brand); color: #fff; font-size: 13px; font-weight: 800; padding: 3px 10px; border-radius: 6px;">
                    Phiên bản v{{ $quotation->version }}
                </span>
                <span style="background: {{ $quotation->status_badge['bg'] }}; color: {{ $quotation->status_badge['color'] }}; font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                    {{ $quotation->status_badge['label'] }}
                </span>
            </div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 6px;">
                Mã báo giá: <strong>{{ $quotation->quotation_code }}</strong> | Ngày lập: {{ $quotation->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <div style="text-align: right;">
            <small style="color: var(--text-muted); display: block; font-size: 12px;">{{ in_array($quotation->status, ['accepted', 'converted']) ? 'Giá đã chốt:' : 'Tổng báo giá:' }}</small>
            <div style="font-size: 24px; font-weight: 800; color: var(--brand);">
                {{ number_format($quotation->total_amount, 0, ',', '.') }} ₫
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 13px;">
        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
            <span style="color: var(--text-muted); display: block; font-size: 11px;">Khách hàng & Liên hệ:</span>
            <strong>{{ $quotation->consultation->customer_name }}</strong> — {{ $quotation->consultation->customer_phone }}
        </div>
        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
            <span style="color: var(--text-muted); display: block; font-size: 11px;">Địa chỉ đo đạc & lắp đặt:</span>
            <strong>{{ $quotation->consultation->address }}</strong>
        </div>
        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
            <span style="color: var(--text-muted); display: block; font-size: 11px;">Chuyên viên tư vấn & đo mẫu:</span>
            <strong style="color: #2563eb;">{{ $quotation->consultation->staff ? $quotation->consultation->staff->name : 'CurtainLux Master' }}</strong>
        </div>
        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
            <span style="color: var(--text-muted); display: block; font-size: 11px;">Thời gian hiệu lực báo giá:</span>
            <strong>{{ $quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : '15 ngày kể từ ngày gửi' }}</strong>
        </div>
    </div>
</div>

<!-- Status Banners & Actions -->
@if($quotation->status === 'accepted')
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <i class="fa-solid fa-circle-check" style="font-size: 32px; color: #16a34a;"></i>
            <div>
                <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #15803d;">Quý Khách Đã Duyệt Bản Báo Giá Này!</h4>
                <p style="margin: 4px 0 0; font-size: 13px; color: #166534;">CurtainLux đã tiếp nhận xác nhận và chuyển thông tin cắt vải gia công tại xưởng. Vui lòng thanh toán số tiền cọc <strong>{{ number_format($quotation->deposit_amount, 0, ',', '.') }} ₫ ({{ $quotation->deposit_percent }}%)</strong> để kích hoạt sản xuất.</p>
            </div>
        </div>
        @if($quotation->order)
            <a href="{{ route('customer.order.detail', $quotation->order->order_code) }}" class="btn-account-primary" style="background: #16a34a; text-decoration: none; padding: 10px 20px; font-weight: 700; border-radius: 8px;">
                <i class="fa-solid fa-list-check"></i> Xem Tiến Độ May Đơn #{{ $quotation->order->order_code }}
            </a>
        @endif
    </div>
@elseif($quotation->status === 'revision_requested')
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px;">
        <i class="fa-solid fa-clock-rotate-left" style="font-size: 28px; color: #d97706;"></i>
        <div>
            <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #b45309;">Đang Xử Lý Yêu Cầu Chỉnh Sửa Của Quý Khách</h4>
            <p style="margin: 4px 0 0; font-size: 13px; color: #92400e;">
                Ghi chú: "{{ $quotation->customer_notes }}". Chuyên viên kỹ thuật đang điều chỉnh cấu hình và sẽ cập nhật phiên bản báo giá mới (v{{ $quotation->version + 1 }}) cho bạn sớm nhất!
            </p>
        </div>
    </div>
@elseif($quotation->canBeAccepted())
    <!-- Action Bar for Customer Approval -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #1e40af;">
                <i class="fa-solid fa-circle-question"></i> Bạn đồng ý với cấu hình và đơn giá của các ô cửa dưới đây?
            </h4>
            <p style="margin: 4px 0 0; font-size: 13px; color: #3b82f6;">
                Bạn có thể bấm duyệt ngay hoặc yêu cầu chuyên viên đổi mẫu vải, ray rèm hay cấu hình ô cửa.
            </p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <!-- Modal trigger for Revision -->
            <button type="button" onclick="document.getElementById('revisionModal').style.display='flex'" class="btn-account-outline" style="border-color: #d97706; color: #b45309; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; background: #fff;">
                <i class="fa-solid fa-pen-to-square"></i> Yêu Cầu Sửa Ô Cửa
            </button>

            <!-- Accept Form -->
            <form action="{{ $acceptUrl }}" method="POST" onsubmit="return confirm('Xác nhận duyệt bản báo giá này để tiến hành may rèm?');" style="margin:0;">
                @csrf
                <button type="submit" class="btn-account-primary" style="background: linear-gradient(135deg, #16a34a, #15803d); padding: 10px 24px; font-size: 15px; font-weight: 800; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);">
                    <i class="fa-solid fa-check-double"></i> ĐỒNG Ý & CHỐT GIÁ
                </button>
            </form>
        </div>
    </div>
@endif

<!-- Windows & Items Detail Table -->
<div class="account-card" style="background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 17px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-list-check" style="color: var(--brand);"></i> Chi Tiết Báo Giá Từng Ô Cửa May Đo
    </h3>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid var(--border); text-align: left;">
                    <th style="padding: 12px 14px;">#</th>
                    <th style="padding: 12px 14px;">Vị Trí Ô Cửa</th>
                    <th style="padding: 12px 14px;">Mẫu Rèm & Cấu Hình</th>
                    <th style="padding: 12px 14px;">Kích Thước Đo Đạc</th>
                    <th style="padding: 12px 14px; text-align: right;">Đơn Giá</th>
                    <th style="padding: 12px 14px; text-align: right;">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $idx => $item)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px; font-weight: 700; color: var(--text-muted);">{{ $idx + 1 }}</td>
                        <td style="padding: 14px;">
                            <strong style="font-size: 14px; color: var(--text-main);">{{ $item->room_name }}</strong>
                            <div style="color: var(--text-muted); font-size: 11px;">Kiểu lắp: {{ $item->install_type_label }}</div>
                        </td>
                        <td style="padding: 14px;">
                            <div style="font-weight: 800; color: var(--brand); font-size: 14px;">{{ $item->product_name }}</div>
                            @if(!empty($item->options_detail))
                                <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 2px;">
                                    @foreach($item->options_detail as $opt)
                                        <span style="font-size: 11px; color: #0284c7;">• {{ $opt['name'] }} (+{{ number_format($opt['price'], 0, ',', '.') }} ₫)</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td style="padding: 14px;">
                            <div>R: <strong>{{ $item->width }} cm</strong> × C: <strong>{{ $item->height }} cm</strong></div>
                            <div style="color: var(--text-muted); font-size: 11px;">Quy đổi: <strong>{{ $item->calculated_units }} {{ $item->unit_label }}</strong> (Số lượng: {{ $item->quantity }})</div>
                        </td>
                        <td style="padding: 14px; text-align: right; font-weight: 600;">
                            {{ number_format($item->unit_price, 0, ',', '.') }} ₫ / {{ $item->unit_label }}
                        </td>
                        <td style="padding: 14px; text-align: right; font-weight: 800; font-size: 15px; color: var(--text-main);">
                            {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; border-top: 2px solid var(--border);">
                    <td colspan="4" style="padding: 12px 14px; text-align: right; font-weight: 700;">Tổng tiền rèm các ô cửa:</td>
                    <td colspan="2" style="padding: 12px 14px; text-align: right; font-weight: 800; font-size: 15px;">
                        {{ number_format($quotation->subtotal, 0, ',', '.') }} ₫
                    </td>
                </tr>
                @if($quotation->discount_amount > 0)
                    <tr style="background: #f8fafc; color: #16a34a;">
                        <td colspan="4" style="padding: 10px 14px; text-align: right; font-weight: 700;">Chiết khấu đặc quyền:</td>
                        <td colspan="2" style="padding: 10px 14px; text-align: right; font-weight: 800;">
                            -{{ number_format($quotation->discount_amount, 0, ',', '.') }} ₫
                        </td>
                    </tr>
                @endif
                <tr style="background: #f8fafc; color: #16a34a;">
                    <td colspan="4" style="padding: 10px 14px; text-align: right; font-weight: 700;">Phí vận chuyển & Nhân công lắp đặt:</td>
                    <td colspan="2" style="padding: 10px 14px; text-align: right; font-weight: 800;">
                        {{ $quotation->installation_fee > 0 ? number_format($quotation->installation_fee, 0, ',', '.') . ' ₫' : 'MIỄN PHÍ' }}
                    </td>
                </tr>
                <tr style="background: #fff; border-top: 2px solid var(--brand);">
                    <td colspan="4" style="padding: 16px 14px; text-align: right; font-size: 16px; font-weight: 800; color: var(--brand);">
                        TỔNG GIÁ TRỊ BÁO GIÁ:
                    </td>
                    <td colspan="2" style="padding: 16px 14px; text-align: right; font-size: 22px; font-weight: 800; color: var(--brand);">
                        {{ number_format($quotation->total_amount, 0, ',', '.') }} ₫
                    </td>
                </tr>
                <tr style="background: #ecfdf5; border-top: 1px dashed #a7f3d0;">
                    <td colspan="4" style="padding: 12px 14px; text-align: right; font-weight: 700; color: #15803d;">
                        Tiền đặt cọc may đo yêu cầu ({{ $quotation->deposit_percent }}%):
                    </td>
                    <td colspan="2" style="padding: 12px 14px; text-align: right; font-weight: 800; color: #15803d; font-size: 17px;">
                        {{ number_format($quotation->deposit_amount, 0, ',', '.') }} ₫
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Modal Yêu Cầu Chỉnh Sửa Báo Giá -->
<div id="revisionModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-pen-to-square" style="color: #d97706;"></i> Yêu Cầu Chỉnh Sửa Báo Giá
            </h3>
            <button type="button" onclick="document.getElementById('revisionModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ $revisionUrl }}" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
            @csrf
            <div>
                <label for="revision_item_id" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ô cửa cần điều chỉnh</label>
                <select id="revision_item_id" name="revision_item_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px;">
                    <option value="">Toàn bộ báo giá</option>
                    @foreach($quotation->items as $item)
                        <option value="{{ $item->id }}">{{ $item->room_name }} - {{ $item->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="customer_offer_amount" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giá bạn mong muốn (₫, không bắt buộc)</label>
                <input id="customer_offer_amount" type="number" name="customer_offer_amount" min="1" step="1" value="{{ old('customer_offer_amount') }}" placeholder="Ví dụ: 5000000" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Nội dung mong muốn thay đổi *</label>
                <textarea name="revision_notes" required rows="4" placeholder="Ví dụ: Tôi muốn đổi rèm phòng ngủ sang loại cản sáng màu xám và bỏ bớt ô cửa phòng làm việc..."
                          style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-size: 13px; line-height: 1.5; outline: none;"></textarea>
                <small style="color: var(--text-muted); font-size: 12px; margin-top: 4px; display: block;">Chuyên viên CurtainLux sẽ gọi điện tư vấn thêm và gửi lại bạn phiên bản báo giá mới.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('revisionModal').style.display='none'" class="btn-account-outline" style="border: 1px solid var(--border); padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                    Hủy bỏ
                </button>
                <button type="submit" class="btn-account-primary" style="background: #d97706; padding: 8px 18px; border-radius: 6px; font-weight: 700; cursor: pointer;">
                    <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
