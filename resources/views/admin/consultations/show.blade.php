@extends('admin.layouts.app')

@section('title', 'Quản Lý Khảo Sát & Báo Giá May Đo #' . $consultation->code)
@section('page-title', 'Hồ Sơ Khảo Sát & Báo Giá May Đo #' . $consultation->code)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-consultation-show.css') }}">
@endpush

@section('content')
<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách lịch hẹn
    </a>

    <div style="display: flex; gap: 10px; align-items: center;">
        <span style="font-size: 13px; color: var(--on-surface-variant);">Trạng thái cuộc hẹn:</span>
        @php $badge = $consultation->status_badge; @endphp
        <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 13px; font-weight: 800; padding: 6px 16px; border-radius: 999px;">
            {{ $badge['label'] }}
        </span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
    
    <!-- LEFT MAIN COLUMN: Windows & Quotations -->
    <div style="display: flex; flex-direction: column; gap: 24px;">

        <!-- 1. Customer & Appointment Info -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="card-title">
                    <i class="fa-solid fa-id-card-clip" style="color: var(--brand);"></i>
                    Thông Tin Khách Hàng & Địa Chỉ Khảo Sát
                </div>
                <span style="font-size: 12px; font-weight: 700; color: var(--on-surface-variant);">Mã: {{ $consultation->code }}</span>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div style="background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        <small style="color: var(--on-surface-variant); display: block; font-size: 11px;">Họ tên khách:</small>
                        <strong style="font-size: 15px;">{{ $consultation->customer_name }}</strong>
                    </div>
                    <div style="background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        <small style="color: var(--on-surface-variant); display: block; font-size: 11px;">Số điện thoại (Zalo):</small>
                        <strong style="font-size: 15px; color: var(--brand);">{{ $consultation->customer_phone }}</strong>
                    </div>
                    <div style="background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        <small style="color: var(--on-surface-variant); display: block; font-size: 11px;">Ngày hẹn đo:</small>
                        <strong style="font-size: 15px;">{{ $consultation->preferred_date->format('d/m/Y') }} ({{ $consultation->preferred_time ?: 'Giờ hành chính' }})</strong>
                    </div>
                    <div style="background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        <small style="color: var(--on-surface-variant); display: block; font-size: 11px;">Thợ kỹ thuật phụ trách:</small>
                        <strong style="font-size: 15px; color: #2563eb;">{{ $consultation->staff ? $consultation->staff->name : 'Chưa phân công' }}</strong>
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 13px; line-height: 1.5;">
                    <strong><i class="fa-solid fa-location-dot" style="color: #ef4444;"></i> Địa chỉ:</strong> {{ $consultation->address }}, {{ $consultation->district ? $consultation->district . ', ' : '' }}{{ $consultation->city }}
                    @if($consultation->notes)
                        <div style="margin-top: 6px; color: #64748b;">
                            <strong><i class="fa-regular fa-comment-dots"></i> Yêu cầu khách:</strong> {{ $consultation->notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Survey Windows Section (Danh Sách Ô Cửa Đo Đạc Thực Tế) -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div class="card-title">
                    <i class="fa-solid fa-ruler-combined" style="color: #10b981;"></i>
                    Danh Sách Ô Cửa Đo Đạc Thực Tế Tại Nhà ({{ $consultation->windows->count() }} ô cửa)
                </div>
                <button type="button" onclick="openNewSurveyWindow()" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> + Thêm Ô Cửa Đo Đạc
                </button>
            </div>
            <div class="card-body">
                @if($consultation->windows->isEmpty())
                    <div style="text-align: center; padding: 30px; background: var(--surface-alt); border-radius: var(--radius-sm); border: 2px dashed var(--border);">
                        <i class="fa-solid fa-window-maximize" style="font-size: 36px; color: var(--on-surface-variant); margin-bottom: 12px; opacity: 0.5;"></i>
                        <p style="font-size: 14px; font-weight: 700; color: var(--on-surface); margin-bottom: 6px;">Chưa ghi nhận ô cửa đo đạc nào</p>
                        <p style="font-size: 13px; color: var(--on-surface-variant); margin-bottom: 16px;">Nhân viên kỹ thuật sau khi đến nhà khách hãy nhập số đo và cấu hình từng ô cửa (Phòng khách, Phòng ngủ...) để hệ thống tự động tính báo giá chính thức.</p>
                        <button type="button" onclick="openNewSurveyWindow()" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Nhập Ô Cửa Đầu Tiên
                        </button>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($consultation->windows as $idx => $win)
                            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center; box-shadow: var(--shadow-sm);">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                        <span style="background: var(--brand); color: #fff; font-size: 12px; font-weight: 800; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                                            {{ $idx + 1 }}
                                        </span>
                                        <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--on-surface);">{{ $win->room_name }}</h4>
                                        <span style="background: #f1f5f9; color: #475569; font-size: 12px; padding: 2px 8px; border-radius: 4px;">{{ $win->install_type_label }}</span>
                                        <span style="background: #e0f2fe; color: #0369a1; font-size: 12px; padding: 2px 8px; border-radius: 4px;">{{ $win->quantity }} bộ</span>
                                    </div>

                                    <div style="font-size: 13px; color: var(--on-surface-variant); margin-bottom: 8px; line-height: 1.6;">
                                        <strong>Sản phẩm:</strong> <span style="color: var(--brand); font-weight: 700;">{{ $win->product_name }}</span> |
                                        <strong>Kích thước:</strong> Rộng <strong>{{ $win->width }} cm</strong> × Cao <strong>{{ $win->height }} cm</strong>
                                        (Quy đổi: <strong>{{ $win->calculated_units }} {{ $win->unit_label }}</strong>)
                                    </div>

                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 12px;">
                                        <span style="background: #fdf2f8; color: #be185d; padding: 2px 8px; border-radius: 4px; border: 1px solid #fbcfe8;">
                                            <i class="fa-solid fa-paint-roller"></i> {{ $win->fabric_color }}
                                        </span>
                                        @if($win->has_sheer)
                                            <span style="background: #f0fdf4; color: #15803d; padding: 2px 8px; border-radius: 4px; border: 1px solid #bbf7d0;">
                                                <i class="fa-solid fa-feather"></i> Kèm voan trắng lấy sáng
                                            </span>
                                        @endif
                                        <span style="background: #f8fafc; color: #334155; padding: 2px 8px; border-radius: 4px; border: 1px solid var(--border);">
                                            <i class="fa-solid fa-scissors"></i> {{ $win->sewing_style_label }}
                                        </span>
                                        <span style="background: #eff6ff; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; border: 1px solid #bfdbfe;">
                                            <i class="fa-solid fa-bolt"></i> {{ $win->motor_type_label }}
                                        </span>
                                    </div>

                                    @if($win->notes)
                                        <div style="margin-top: 8px; font-size: 12px; color: #b45309; background: #fefce8; padding: 6px 10px; border-radius: 4px; border: 1px dashed #fde047;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> <strong>Lưu ý kỹ thuật:</strong> {{ $win->notes }}
                                        </div>
                                    @endif
                                </div>

                                <div style="text-align: right; min-width: 140px;">
                                    <small style="color: var(--on-surface-variant); display: block; font-size: 11px;">Thành tiền dự toán:</small>
                                    <div style="font-size: 16px; font-weight: 800; color: var(--brand); margin-bottom: 8px;">
                                        {{ number_format($win->estimated_price, 0, ',', '.') }} ₫
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="editSurveyWindow({{ $win->id }})" style="margin-bottom: 6px;"><i class="fa-solid fa-pen"></i> Sửa</button>

                                    <form action="{{ route('admin.consultation-windows.destroy', $win->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa ô cửa này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 12px; color: #dc2626;">
                                            <i class="fa-solid fa-trash-can"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- 3. Official Quotations & Versioning Section (Bảng Báo Giá Chính Thức Đa Phiên Bản) -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div>
                    <div class="card-title" style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </span>
                        <span>Bảng Báo Giá Chính Thức Đa Phiên Bản</span>
                    </div>
                    <small style="color: var(--on-surface-variant); margin-top: 4px; display: block;">Báo giá chốt cho từng ô cửa với lưu vết phiên bản (v1, v2...) khi khách yêu cầu điều chỉnh</small>
                </div>

                @if(!$consultation->windows->isEmpty())
                    <form action="{{ route('admin.quotations.generate', $consultation->id) }}" method="POST" class="quote-generate-toolbar">
                        @csrf
                        <div class="quote-mini-field">
                            <label>Chiết khấu:</label>
                            <input type="number" name="discount_amount" min="0" step="1" value="0" style="width: 100px;" placeholder="0 ₫" aria-label="Chiết khấu">
                        </div>
                        <div class="quote-mini-field">
                            <label>Phí lắp:</label>
                            <input type="number" name="installation_fee" min="0" step="1" value="0" style="width: 95px;" placeholder="0 ₫" aria-label="Phí lắp đặt">
                        </div>
                        <div class="quote-mini-field">
                            <label>Cọc %:</label>
                            <input type="number" name="deposit_percent" min="10" max="100" value="30" style="width: 55px;" aria-label="Tỷ lệ cọc">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; padding: 7px 14px; font-weight: 700;">
                            <i class="fa-solid fa-bolt"></i> 
                            {{ $consultation->quotations->isEmpty() ? 'Tự Động Lập Báo Giá v1' : '+ Lập Phiên Bản Mới (v' . ($consultation->quotations->first()->version + 1) . ')' }}
                        </button>
                    </form>
                @endif
            </div>

            <div class="card-body">
                @if($consultation->quotations->isEmpty())
                    <div style="text-align: center; padding: 36px 20px; color: var(--on-surface-variant); background: var(--surface-alt); border-radius: var(--radius-md); border: 1px dashed var(--border);">
                        <i class="fa-solid fa-calculator" style="font-size: 36px; margin-bottom: 12px; color: var(--brand); opacity: 0.6;"></i>
                        <h4 style="font-size: 15px; font-weight: 700; color: var(--on-surface); margin-bottom: 6px;">Chưa Có Bản Báo Giá Nào Được Tạo</h4>
                        <p style="font-size: 13.5px; max-width: 520px; margin: 0 auto; line-height: 1.5;">Sau khi thêm các ô cửa đo đạc thực tế tại nhà khách ở danh sách trên, nhấn nút <strong>"Tự Động Lập Báo Giá"</strong> để hệ thống tự quy đổi số mét và lập bảng giá chi tiết.</p>
                    </div>
                @else
                    <!-- Version Tabs -->
                    <div class="quote-tabs-container">
                        @foreach($consultation->quotations as $qIdx => $quote)
                            <a href="#quotation-{{ $quote->id }}" 
                               class="quote-tab-btn {{ $qIdx === 0 ? 'active' : '' }}">
                                <i class="fa-regular fa-file-lines"></i>
                                <span>Phiên bản v{{ $quote->version }}</span>
                                <span class="quote-tab-badge" style="background: {{ $quote->status_badge['bg'] }}; color: {{ $quote->status_badge['color'] }};">
                                    {{ $quote->status_badge['label'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    @foreach($consultation->quotations as $qIdx => $quote)
                        <div id="quotation-{{ $quote->id }}" style="{{ $qIdx > 0 ? 'display: none;' : '' }} margin-bottom: 20px;">
                            <!-- Quotation Status Header Banner -->
                            @php
                                $bannerClass = match($quote->status) {
                                    'accepted' => 'banner-accepted',
                                    'revision_requested' => 'banner-revision',
                                    default => 'banner-draft'
                                };
                            @endphp
                            <div class="quote-status-banner {{ $bannerClass }}">
                                <div>
                                    <div class="quote-code-title">
                                        <span>Mã Báo Giá:</span>
                                        <span style="color: #2563eb; font-family: monospace; letter-spacing: 0.02em;">{{ $quote->quotation_code }}</span>
                                        <span style="font-size: 12px; font-weight: 700; background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 6px;">v{{ $quote->version }}</span>
                                    </div>
                                    <div class="quote-meta-chips">
                                        <span class="quote-meta-chip"><i class="fa-regular fa-calendar-check"></i> Lập: <strong>{{ $quote->created_at->format('d/m/Y H:i') }}</strong></span>
                                        <span class="quote-meta-chip"><i class="fa-regular fa-clock"></i> Hiệu lực: <strong>{{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : '15 ngày' }}</strong></span>
                                        <span class="quote-meta-chip"><i class="fa-solid fa-truck-fast"></i> Lắp đặt: <strong>{{ $quote->estimated_delivery_date ? $quote->estimated_delivery_date->format('d/m/Y') : '7 ngày sau chốt' }}</strong></span>
                                    </div>
                                    @if($quote->customer_notes)
                                        <div style="margin-top: 10px; font-size: 13px; color: #b45309; background: #ffffff; padding: 10px 14px; border-radius: 6px; border: 1px dashed #f59e0b; display: flex; align-items: flex-start; gap: 8px;">
                                            <i class="fa-solid fa-comment-dots" style="margin-top: 2px;"></i>
                                            <div><strong>Phản hồi từ khách:</strong> {{ $quote->customer_notes }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                    @if($quote->status === 'draft')
                                        <form action="{{ route('admin.quotations.send', $quote->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; padding: 9px 18px; font-weight: 700; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);">
                                                <i class="fa-solid fa-paper-plane"></i> Công Bố Báo Giá
                                            </button>
                                        </form>
                                    @elseif($quote->status === 'sent')
                                        <span style="background: #eff6ff; color: #1d4ed8; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 999px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-clock-rotate-left"></i> Đang chờ khách hàng phản hồi
                                        </span>
                                    @elseif($quote->status === 'revision_requested')
                                        <span style="background: #fffbeb; color: #b45309; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 999px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-pen-to-square"></i> Cần điều chỉnh theo yêu cầu khách
                                        </span>
                                    @elseif($quote->status === 'accepted')
                                        <!-- BIG CONVERT BUTTON -->
                                        <form action="{{ route('admin.quotations.convert-order', $quote->id) }}" method="POST" onsubmit="return confirm('Xác nhận chuyển bản báo giá đã duyệt này thành Đơn Hàng May Đo chính thức? Hệ thống sẽ tự động trừ tồn kho vải xưởng.');" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #16a34a, #15803d); border: none; padding: 11px 22px; font-size: 14px; font-weight: 800; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35); letter-spacing: 0.02em;">
                                                <i class="fa-solid fa-rocket"></i> CHUYỂN THÀNH ĐƠN HÀNG MAY ĐO
                                            </button>
                                        </form>
                                    @elseif($quote->status === 'converted')
                                        @if($quote->order)
                                            <a href="{{ route('admin.orders.show', $quote->order->id) }}" class="btn btn-primary" style="background: #7c3aed; border: none; padding: 8px 16px; font-weight: 700;">
                                                <i class="fa-solid fa-box-open"></i> Xem Đơn Hàng #{{ $quote->order->order_code }}
                                            </a>
                                        @else
                                            <span style="background: #f5f3ff; color: #7c3aed; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 999px; border: 1px solid #ddd6fe;">
                                                ✓ Đã chuyển thành đơn may đo
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if($quote->customer_offer_amount)
                                <div style="margin: 12px 0; padding: 10px 14px; background: #fffbeb; border-radius: 8px; border: 1px solid #fde68a; color: #92400e; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                    <span>Khách hàng mong muốn mức giá: <strong>{{ number_format($quote->customer_offer_amount, 0, ',', '.') }} ₫</strong>. (Admin có thể điều chỉnh chiết khấu ở bảng bên dưới và bấm Lưu giá).</span>
                                </div>
                            @endif

                            @if($quote->status === 'draft')
                                <div class="quote-adjust-box">
                                    <div class="quote-adjust-title">
                                        <i class="fa-solid fa-sliders"></i>
                                        <span>Tham Số Báo Giá & Ưu Đãi (Bản Nháp)</span>
                                    </div>
                                    <form action="{{ route('admin.quotations.update', $quote->id) }}" method="POST" class="quote-adjust-grid">
                                        @csrf
                                        @method('PUT')
                                        <div class="quote-input-group">
                                            <label>Chiết khấu (₫):</label>
                                            <input type="number" name="discount_amount" min="0" step="1" value="{{ $quote->discount_amount }}" required class="quote-control" style="width: 140px;">
                                        </div>
                                        <div class="quote-input-group">
                                            <label>Phí lắp đặt (₫):</label>
                                            <input type="number" name="installation_fee" min="0" step="1" value="{{ $quote->installation_fee }}" required class="quote-control" style="width: 140px;">
                                        </div>
                                        <div class="quote-input-group">
                                            <label>Cọc (%):</label>
                                            <input type="number" name="deposit_percent" min="10" max="100" value="{{ $quote->deposit_percent }}" required class="quote-control" style="width: 80px;">
                                        </div>
                                        <div class="quote-input-group">
                                            <label>Hiệu lực đến:</label>
                                            <input type="date" name="valid_until" value="{{ $quote->valid_until?->format('Y-m-d') }}" class="quote-control" style="width: 160px;">
                                        </div>
                                        <button type="submit" class="btn btn-outline" style="background: #0f172a; color: #ffffff; border: none; padding: 8px 16px; font-size: 13px; font-weight: 700; height: 38px; border-radius: 6px;">
                                            <i class="fa-solid fa-floppy-disk"></i> Lưu giá
                                        </button>
                                    </form>
                                </div>
                            @elseif(in_array($quote->status, ['sent', 'revision_requested', 'accepted', 'converted']))
                                @php $shareUrl = $consultation->user_id ? route('customer.quotation.detail', $quote->quotation_code) : \Illuminate\Support\Facades\URL::temporarySignedRoute('customer.quotation.guest.detail', now()->addDays(30), ['code' => $quote->quotation_code]); @endphp
                                <div class="quote-share-card">
                                    <div style="font-size: 13px; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-link"></i> Link báo giá gửi khách duyệt:
                                    </div>
                                    <div class="quote-share-input-group">
                                        <input type="text" id="shareUrl-{{ $quote->id }}" readonly value="{{ $shareUrl }}" class="quote-share-input" onclick="this.select()" aria-label="Link báo giá">
                                        <button type="button" class="quote-share-btn" onclick="copyShareLink('shareUrl-{{ $quote->id }}', this)">
                                            <i class="fa-regular fa-copy"></i> Sao chép
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Quotation Items Table -->
                            <div class="table-responsive" style="margin-bottom: 16px;">
                                <table class="quote-modern-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 48px; text-align: center;">#</th>
                                            <th style="width: 170px;">Ô Cửa / Phòng</th>
                                            <th>Mẫu Rèm & Kích Thước</th>
                                            <th style="width: 130px;">Đơn Vị Tính</th>
                                            <th style="text-align: right; width: 140px;">Đơn Giá Vải</th>
                                            <th style="text-align: right; width: 150px;">Phụ Phí Cấu Hình</th>
                                            <th style="text-align: right; width: 150px;">Thành Tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quote->items as $iIdx => $qItem)
                                            <tr>
                                                <td style="text-align: center;">
                                                    <div class="quote-row-badge">{{ $iIdx + 1 }}</div>
                                                </td>
                                                <td>
                                                    <div class="quote-room-title">{{ $qItem->room_name }}</div>
                                                    <span class="quote-install-tag">
                                                        <i class="fa-solid fa-ruler-combined"></i> {{ $qItem->install_type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="quote-prod-name">{{ $qItem->product_name }}</div>
                                                    <div class="quote-dim-badge">
                                                        <i class="fa-solid fa-arrows-up-down-left-right"></i> R: <strong>{{ $qItem->width }} cm</strong> × C: <strong>{{ $qItem->height }} cm</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="quote-unit-val">{{ $qItem->calculated_units }} {{ $qItem->unit_label }}</div>
                                                    <div class="quote-unit-sub">Số lượng: <strong>{{ $qItem->quantity }}</strong> bộ</div>
                                                </td>
                                                <td class="quote-price-col">
                                                    {{ number_format($qItem->unit_price, 0, ',', '.') }} ₫
                                                </td>
                                                <td style="text-align: right;">
                                                    <span class="quote-options-tag">
                                                        +{{ number_format($qItem->options_cost, 0, ',', '.') }} ₫
                                                    </span>
                                                </td>
                                                <td class="quote-subtotal-col">
                                                    {{ number_format($qItem->subtotal, 0, ',', '.') }} ₫
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="quote-foot-label">Tổng tiền rèm các ô cửa:</td>
                                            <td colspan="2" class="quote-foot-val">
                                                {{ number_format($quote->subtotal, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                        @if($quote->discount_amount > 0)
                                            <tr style="background: #fffcf0;">
                                                <td colspan="5" class="quote-foot-label" style="color: #ea580c;">Chiết khấu / Khuyến mãi:</td>
                                                <td colspan="2" class="quote-foot-val" style="color: #ea580c;">
                                                    -{{ number_format($quote->discount_amount, 0, ',', '.') }} ₫
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="5" class="quote-foot-label">Công lắp đặt & Phụ kiện trọn gói:</td>
                                            <td colspan="2" class="quote-foot-val" style="color: #16a34a;">
                                                @if($quote->installation_fee > 0)
                                                    {{ number_format($quote->installation_fee, 0, ',', '.') }} ₫
                                                @else
                                                    <span style="background: #ecfdf5; color: #16a34a; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 700;">MIỄN PHÍ</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="quote-total-row">
                                            <td colspan="5" class="quote-total-label">TỔNG BÁO GIÁ THANH TOÁN:</td>
                                            <td colspan="2" class="quote-total-val">
                                                {{ number_format($quote->total_amount, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                        <tr class="quote-deposit-row">
                                            <td colspan="5" class="quote-deposit-label">
                                                Tiền cọc may đo yêu cầu ({{ $quote->deposit_percent }}%):
                                            </td>
                                            <td colspan="2" class="quote-deposit-val">
                                                {{ number_format($quote->deposit_amount, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

    <!-- RIGHT SIDEBAR: Actions & Appointment Status -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- Status & Assignment Form -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-sliders" style="color: var(--brand);"></i>
                    Điều Phối & Phân Công Thợ
                </div>
            </div>

            <form action="{{ route('admin.consultations.update-status', $consultation->id) }}" method="POST" class="card-body">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Trạng Thái Lịch Khảo Sát:</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="pending" {{ $consultation->status === 'pending' ? 'selected' : '' }}>1. Chờ tiếp nhận</option>
                        <option value="assigned" {{ $consultation->status === 'assigned' ? 'selected' : '' }}>2. Đã phân công thợ kỹ thuật</option>
                        <option value="surveying" {{ $consultation->status === 'surveying' ? 'selected' : '' }}>3. Đang đo đạc tại nhà</option>
                        <option value="quoted" {{ $consultation->status === 'quoted' ? 'selected' : '' }}>4. Đã lập báo giá</option>
                        <option value="completed" {{ $consultation->status === 'completed' ? 'selected' : '' }}>5. Hoàn thành / Ký hợp đồng</option>
                        <option value="cancelled" {{ $consultation->status === 'cancelled' ? 'selected' : '' }}>6. Đã hủy</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Phân Công Thợ Đi Đo:</label>
                    <select name="staff_id" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="">-- Chọn chuyên viên mang mẫu đi đo --</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ $consultation->staff_id == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} • {{ $staff->role_name }}{{ $staff->phone ? ' (' . ($staff->formatted_phone ?: $staff->phone) . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ghi Chú Kỹ Thuật Nội Bộ:</label>
                    <textarea name="admin_note" rows="3" placeholder="Nhập ghi chú điều phối nội bộ..."
                              style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; line-height: 1.5;">{{ $consultation->admin_note }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Điều Phối
                </button>
            </form>
        </div>

        <!-- Quick Summary Box -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title" style="font-size: 14px;">
                    <i class="fa-solid fa-calculator" style="color: #10b981;"></i> Tóm Tắt Khảo Sát
                </div>
            </div>
            <div class="card-body" style="font-size: 13px; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--on-surface-variant);">Số ô cửa đã đo:</span>
                    <strong>{{ $consultation->windows->count() }} ô cửa</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--on-surface-variant);">Bản báo giá hiện hành:</span>
                    <strong>{{ $consultation->currentQuotation ? 'Phiên bản v' . $consultation->currentQuotation->version : 'Chưa lập' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--on-surface-variant);">Tổng giá trị chốt:</span>
                    <strong style="color: var(--brand); font-size: 15px;">
                        {{ $consultation->currentQuotation ? number_format($consultation->currentQuotation->total_amount, 0, ',', '.') . ' ₫' : '0 ₫' }}
                    </strong>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL: Thêm Ô Cửa Đo Đạc Thực Tế Tại Nhà -->
<div id="addWindowModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--surface); border-radius: var(--radius-md); max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-lg);">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: var(--on-surface); display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-ruler-combined" style="color: #10b981;"></i> Nhập Số Đo Ô Cửa Thực Tế Tại Nhà
            </h3>
            <button type="button" onclick="document.getElementById('addWindowModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--on-surface-variant);">&times;</button>
        </div>

        <form id="windowForm" action="{{ route('admin.consultation-windows.store', $consultation->id) }}" method="POST" enctype="multipart/form-data" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <input type="hidden" id="windowMethod" name="_method" value="POST">

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Vị Trí / Tên Ô Cửa *</label>
                <input type="text" name="room_name" required placeholder="Ví dụ: Phòng khách - Cửa ban công, Phòng ngủ Master..."
                       style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mẫu Rèm Khách Chọn *</label>
                <select name="product_id" required style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    <option value="">-- Chọn mẫu rèm từ catalogue --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">
                            {{ $prod->name }} — {{ number_format($prod->effective_price ?: $prod->price, 0, ',', '.') }} ₫ / {{ $prod->unit_label ?: 'bộ' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chiều Rộng (cm) *</label>
                    <input type="number" step="0.1" name="width" value="250" required
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chiều Cao (cm) *</label>
                    <input type="number" step="0.1" name="height" value="260" required
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Số Lượng (bộ) *</label>
                    <input type="number" name="quantity" value="1" min="1" max="50" required
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Kiểu Lắp Đặt *</label>
                    <select name="install_type" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="inside">Lọt lòng (Trong hộc thạch cao/khung cửa)</option>
                        <option value="outside">Phủ bì (Trùm tường, cách mép 15-20cm)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mã Màu / Tên Màu Vải</label>
                    <input type="text" name="fabric_color" placeholder="VD: Ghi sáng #04, Be nhạt #12..."
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Kiểu May Rèm *</label>
                    <select name="sewing_style" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="wave">May định hình sóng rèm cao cấp (+120k/m)</option>
                        <option value="pleat">May xếp ly 2-3 cánh truyền thống</option>
                        <option value="eyelet">May ore khuyên xỏ lỗ</option>
                        <option value="roman">May rèm xếp lớp Roman</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Hệ Ray / Động Cơ *</label>
                    <select name="motor_type" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="manual">Ray cơ trượt nhôm chống ồn tiêu chuẩn</option>
                        <option value="smart_wifi">Động cơ tự động Tuya Smart Wifi (+1.850k)</option>
                        <option value="somfy">Động cơ cao cấp Somfy Pháp (+3.900k)</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px 12px; background: #f0fdf4; border-radius: var(--radius-sm); border: 1px solid #bbf7d0;">
                    <input type="checkbox" name="has_sheer" value="1" style="width: 18px; height: 18px; accent-color: #16a34a;">
                    <span style="font-size: 13px; font-weight: 700; color: #15803d;">
                        May kèm lớp voan trắng lấy sáng mềm mại 2 lớp (+350k/m)
                    </span>
                </label>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ghi Chú Kỹ Thuật Đo Đạc</label>
                <textarea name="notes" rows="2" placeholder="Ví dụ: Cửa bị vướng hộp thạch cao điều hòa, cần bát chữ L 15cm; tường yếu cần dùng vít nở chuyên dụng..."
                          style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; line-height: 1.5;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('addWindowModal').style.display='none'" class="btn btn-secondary">
                    Hủy bỏ
                </button>
                <button type="submit" class="btn btn-primary" style="background: #10b981;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Số Đo Ô Cửa
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    window.consultationConfig = {
        surveyWindows: @json($consultation->windows->keyBy('id')),
        createWindowUrl: @json(route('admin.consultation-windows.store', $consultation->id)),
        updateWindowUrl: @json(route('admin.consultation-windows.update', ['id' => '__ID__']))
    };
</script>
<script src="{{ asset('js/admin-consultation-show.js') }}"></script>
@endpush
@endsection
