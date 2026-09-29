@extends('shop.account.layout')

@section('title', 'Lịch Hẹn Khảo Sát & Đo Rèm Tận Nhà')
@section('breadcrumb-active', 'Lịch Khảo Sát')

@section('account-content')
<div class="account-content-card">
    <div class="content-header">
        <div>
            <h2 class="content-title">
                <i class="fa-solid fa-calendar-check" style="color: var(--primary, #b8935c);"></i> Lịch Hẹn Khảo Sát & Đo Rèm Tận Nhà
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                Danh sách các cuộc hẹn thợ kỹ thuật CurtainLux mang mẫu vải và đo đạc miễn phí tại công trình
            </div>
        </div>
        <button type="button" onclick="openSurveyModal()" class="btn-book-survey" style="font-size: 13px;">
            <i class="fa-solid fa-plus"></i> Đặt Lịch Đo Mới
        </button>
    </div>

    @if($consultations->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($consultations as $c)
                @php $badge = $c->status_badge; @endphp
                <div style="border: 1px solid var(--border-line); border-radius: 10px; padding: 20px; background: #fff; display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: center;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-weight: 800; font-size: 15px; color: #7c3aed;">{{ $c->code }}</span>
                            <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px;">
                                {{ $badge['label'] }}
                            </span>
                        </div>

                        <div style="font-size: 13px; color: var(--text-main); line-height: 1.7;">
                            <div><i class="fa-regular fa-calendar" style="color: var(--primary, #b8935c); width: 18px;"></i> <strong>Thời gian hẹn:</strong> {{ $c->preferred_date->format('d/m/Y') }} &bull; {{ $c->preferred_time ?: 'Giờ hành chính' }}</div>
                            <div><i class="fa-solid fa-location-dot" style="color: var(--primary, #b8935c); width: 18px;"></i> <strong>Địa chỉ khảo sát:</strong> {{ $c->address }}, {{ $c->city }}</div>
                            <div><i class="fa-solid fa-ruler-vertical" style="color: var(--primary, #b8935c); width: 18px;"></i> 
                                <strong>Số ô cửa:</strong> {{ $c->windows->count() > 0 ? $c->windows->count() . ' ô cửa đã đo đạc thực tế' : ($c->estimated_windows ?: 1) . ' ô cửa dự kiến' }}
                            </div>
                            @if(!empty($c->curtain_types_interested))
                                <div><i class="fa-solid fa-tags" style="color: var(--primary, #b8935c); width: 18px;"></i> <strong>Mẫu quan tâm:</strong> {{ implode(', ', $c->curtain_types_interested) }}</div>
                            @endif
                        </div>

                        @php $quote = in_array($c->currentQuotation?->status, ['sent', 'revision_requested', 'accepted', 'converted']) ? $c->currentQuotation : $c->quotations->first(fn ($item) => in_array($item->status, ['sent', 'revision_requested', 'accepted', 'converted'])); @endphp
                        @if($quote)
                            <div style="margin-top: 12px; padding: 10px 14px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <span style="font-weight: 800; color: #15803d; font-size: 13px;">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> Báo giá chính thức: {{ $quote->quotation_code }} (v{{ $quote->version }})
                                    </span>
                                    <div style="font-size: 12px; color: #166534;">
                                        Tổng tiền: <strong>{{ number_format($quote->total_amount, 0, ',', '.') }} ₫</strong> | Cọc: {{ number_format($quote->deposit_amount, 0, ',', '.') }} ₫ ({{ $quote->deposit_percent }}%)
                                    </div>
                                </div>
                                <a href="{{ route('customer.quotation.detail', $quote->quotation_code) }}" class="btn-account-primary" style="background: #16a34a; font-size: 12px; padding: 6px 14px; text-decoration: none; border-radius: 6px;">
                                    <i class="fa-solid fa-eye"></i> Xem & Duyệt Báo Giá
                                </a>
                            </div>
                        @endif
                    </div>

                    <div style="text-align: right; display: flex; flex-direction: column; gap: 8px;">
                        @if($quote)
                            <a href="{{ route('customer.quotation.detail', $quote->quotation_code) }}" style="display: inline-flex; align-items: center; gap: 6px; background: #16a34a; color: #fff; text-decoration: none; font-size: 12px; font-weight: 700; padding: 8px 14px; border-radius: 6px;">
                                <i class="fa-solid fa-file-signature"></i> Xem Báo Giá
                            </a>
                        @endif
                        <a href="tel:0912345678" class="btn-wishlist" style="font-size: 12px; padding: 6px 12px; text-decoration: none;">
                            <i class="fa-solid fa-phone"></i> Hotline Kỹ Thuật
                        </a>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div style="margin-top: 16px;">
                {{ $consultations->links('vendor.pagination.custom') }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-calendar-xmark" style="font-size: 44px; color: #cbd5e1; margin-bottom: 12px;"></i>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Bạn chưa có lịch hẹn khảo sát nào</h3>
            <p style="font-size: 13px; margin-bottom: 18px;">CurtainLux cung cấp dịch vụ mang cây mẫu tận nhà và tư vấn kích thước hoàn toàn miễn phí.</p>
            <button type="button" onclick="openSurveyModal()" class="btn-book-survey">
                <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Đo Miễn Phí Ngay
            </button>
        </div>
    @endif
</div>
@endsection
