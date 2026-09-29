@extends('admin.layouts.app')

@section('title', 'Quản Lý Đánh Giá Sản Phẩm')
@section('page-title', 'Đánh Giá & Nhận Xét Của Khách Hàng')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="card" style="margin-bottom: 0; padding: 18px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Tổng Đánh Giá</span>
            <div style="font-size: 24px; font-weight: 800; color: var(--brand); margin: 6px 0 2px;">{{ $totalReviews }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Tất cả sản phẩm rèm</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 18px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Điểm Đánh Giá TB</span>
            <div style="font-size: 24px; font-weight: 800; color: #d97706; margin: 6px 0 2px;">
                {{ number_format($avgRating, 1) }} <i class="fa-solid fa-star" style="font-size: 18px;"></i>
            </div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Mức độ hài lòng của khách</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 18px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Đang Hiển Thị</span>
            <div style="font-size: 24px; font-weight: 800; color: #16a34a; margin: 6px 0 2px;">{{ $approvedReviews }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Đã duyệt lên trang chi tiết</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 14px 20px;">
        <form action="{{ route('admin.reviews.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <select name="status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt (Hiển thị)</option>
                <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
            </select>

            <select name="rating" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Số sao đánh giá --</option>
                <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 sao (Cực kỳ hài lòng)</option>
                <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4 sao (Hài lòng)</option>
                <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3 sao (Bình thường)</option>
                <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2 sao (Chưa ưng ý)</option>
                <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1 sao (Thất vọng)</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc đánh giá
            </button>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Khách Hàng</th>
                        <th>Mẫu Rèm Cửa</th>
                        <th>Số Sao</th>
                        <th>Nội Dung Nhận Xét</th>
                        <th>Ngày Đánh Giá</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $r)
                        <tr>
                            <td>
                                <strong style="font-size: 13px; color: var(--on-surface);">{{ $r->customer_name }}</strong>
                                <div style="font-size: 11px; color: var(--on-surface-variant);">{{ $r->customer_phone ?: ($r->user->email ?? '') }}</div>
                                @if($r->is_verified_buyer)
                                    <span style="display: inline-block; margin-top: 3px; font-size: 10px; color: #15803d; background: #dcfce7; padding: 1px 6px; border-radius: 4px; font-weight: 600;">
                                        <i class="fa-solid fa-circle-check"></i> Đã mua hàng
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('shop.show', $r->product->slug ?? '') }}" target="_blank" style="color: var(--brand); font-weight: 700; text-decoration: none; font-size: 13px;">
                                    {{ $r->product->name ?? 'Sản phẩm đã xóa' }}
                                </a>
                            </td>
                            <td>
                                <div style="color: #f59e0b; font-size: 13px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-{{ $i <= $r->rating ? 'solid' : 'regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </td>
                            <td style="min-width: 260px; max-width: 380px; font-size: 13px; color: var(--on-surface);">
                                <div style="line-height: 1.5; color: #1c1917;">
                                    {{ $r->comment }}
                                </div>

                                @if($r->hasReply())
                                    <div style="margin-top: 8px; padding: 9px 12px; background: #faf6f0; border-left: 3px solid var(--brand); border-radius: 6px; font-size: 12px;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; gap: 8px;">
                                            <span style="font-weight: 700; color: var(--brand);">
                                                <i class="fa-solid fa-reply"></i> Phản hồi từ CurtainLux
                                                <span style="font-weight: 500; color: var(--on-surface-variant); font-size: 11px;">({{ $r->replier->name ?? 'Quản trị viên' }})</span>
                                            </span>
                                            <span style="color: var(--on-surface-variant); font-size: 11px; white-space: nowrap;">
                                                {{ $r->replied_at ? $r->replied_at->format('d/m/Y H:i') : '' }}
                                            </span>
                                        </div>
                                        <div style="color: #44403c; line-height: 1.5; white-space: pre-line;">{{ $r->reply_content }}</div>
                                    </div>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: var(--on-surface-variant); white-space: nowrap;">
                                {{ $r->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <form action="{{ route('admin.reviews.toggle', $r->id) }}" method="POST" style="margin: 0; display: inline;">
                                    @csrf
                                    <button type="submit" class="badge {{ $r->status === 'approved' ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 10px;" title="Bấm để ẩn/hiện đánh giá">
                                        {{ $r->status === 'approved' ? 'Đã duyệt' : 'Đang ẩn' }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <button type="button" 
                                            class="btn btn-sm {{ $r->hasReply() ? 'btn-outline' : 'btn-primary' }}" 
                                            style="padding: 4px 10px; font-size: 12px;"
                                            onclick="openReplyModal({{ $r->id }}, @js($r->customer_name), @js($r->comment), @js($r->reply_content ?? ''), @js($r->rating), @js($r->product->name ?? ''))">
                                        <i class="fa-solid {{ $r->hasReply() ? 'fa-pen-to-square' : 'fa-reply' }}"></i> 
                                        {{ $r->hasReply() ? 'Sửa' : 'Trả lời' }}
                                    </button>

                                    @if($r->hasReply())
                                        <form action="{{ route('admin.reviews.delete-reply', $r->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa nội dung phản hồi này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: #b45309; padding: 4px 8px;" title="Xóa phản hồi">
                                                <i class="fa-solid fa-reply-slash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.reviews.destroy', $r->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa hoàn toàn đánh giá này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" title="Xóa đánh giá">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                Chưa có đánh giá nào từ khách hàng.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 20px;">
            {{ $reviews->links() }}
        </div>
    </div>
</div>

<!-- Modal Trả Lời / Phản Hồi Đánh Giá -->
<div id="replyModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #ffffff; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #faf8f5;">
            <div>
                <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #1c1917; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-reply" style="color: var(--brand);"></i> Phản Hồi Đánh Giá Khách Hàng
                </h4>
                <div id="modalProductSubtitle" style="font-size: 12px; color: var(--on-surface-variant); margin-top: 3px;"></div>
            </div>
            <button type="button" onclick="closeReplyModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <!-- Form -->
        <form id="replyForm" method="POST" action="" style="padding: 20px;">
            @csrf
            
            <!-- Customer Review Snippet -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                    <span id="modalCustomerName" style="font-weight: 700; font-size: 13px; color: #0f172a;"></span>
                    <span id="modalCustomerStars" style="color: #f59e0b; font-size: 12px;"></span>
                </div>
                <div id="modalCustomerComment" style="font-size: 13px; color: #475569; line-height: 1.5; font-style: italic;"></div>
            </div>

            <!-- Quick Template Chips -->
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px;">
                    <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--brand);"></i> Mẫu phản hồi nhanh (Click để chọn):
                </label>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="applyTemplate('thank_5star')" style="font-size: 11px; padding: 4px 10px; border-radius: 20px; border-color: #cbd5e1;">
                        ⭐ Cảm ơn 5 sao
                    </button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="applyTemplate('warranty_support')" style="font-size: 11px; padding: 4px 10px; border-radius: 20px; border-color: #cbd5e1;">
                        🛠️ Hỗ trợ bảo hành / Phàn nàn
                    </button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="applyTemplate('upsell_curtain')" style="font-size: 11px; padding: 4px 10px; border-radius: 20px; border-color: #cbd5e1;">
                        ✨ Tư vấn rèm voan / Động cơ
                    </button>
                </div>
            </div>

            <!-- Reply Content Textarea -->
            <div style="margin-bottom: 18px;">
                <label for="reply_content" style="display: block; font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                    Nội Dung Phản Hồi Từ Xưởng CurtainLux: <span style="color: #dc2626;">*</span>
                </label>
                <textarea name="reply_content" id="modalReplyContent" rows="5" required
                          placeholder="Nhập nội dung phản hồi chính thức từ CurtainLux gửi đến khách hàng..."
                          style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; line-height: 1.5; color: #1e293b; font-family: inherit; resize: vertical; outline: none;"></textarea>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                    * Phản hồi sẽ hiển thị công khai ngay bên dưới nhận xét của khách trên trang sản phẩm và tự động duyệt đánh giá.
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                <button type="button" class="btn btn-outline" onclick="closeReplyModal()" style="padding: 8px 16px;">Đóng</button>
                <button type="submit" class="btn btn-primary" style="padding: 8px 20px; font-weight: 600;">
                    <i class="fa-solid fa-paper-plane"></i> Xuất Bản Phản Hồi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const templates = {
    thank_5star: "Dạ CurtainLux chân thành cảm ơn Anh/Chị đã tin tưởng lựa chọn và dành tặng đánh giá 5 sao cho xưởng may! Sự hài lòng của Anh/Chị về chất lượng vải cũng như sự tỉ mỉ của từng đường may chính là động lực lớn nhất của đội ngũ thợ may chúng em. Chúc Anh/Chị và gia đình luôn có không gian sống ấm cúng và an yên ạ! ✨",
    warranty_support: "Dạ CurtainLux rất tiếc vì trải nghiệm với sản phẩm chưa thực sự trọn vẹn như kỳ vọng của Anh/Chị. Xưởng may chúng em luôn cam kết bảo hành chính hãng 2 năm và hỗ trợ chỉnh sửa kích thước tận nơi miễn phí. Đội ngũ kỹ thuật viên sẽ liên hệ ngay qua số điện thoại của Anh/Chị để kiểm tra và xử lý thỏa đáng nhất ạ!",
    upsell_curtain: "Dạ cảm ơn Anh/Chị đã yêu thích và đánh giá tốt cho mẫu rèm cửa này! Mẫu này khi phối cùng một lớp voan xước nhẹ nhàng hoặc lắp thêm bộ động cơ tự động thông minh sẽ càng tôn thêm vẻ sang trọng cho ngôi nhà. Nếu cần tư vấn thêm cho các không gian khác, Anh/Chị đừng ngần ngại nhắn cho xưởng nhé ạ!"
};

function openReplyModal(reviewId, customerName, comment, currentReply, rating, productName) {
    const modal = document.getElementById('replyModal');
    const form = document.getElementById('replyForm');
    
    form.action = `/admin/reviews/${reviewId}/reply`;
    document.getElementById('modalCustomerName').textContent = customerName;
    document.getElementById('modalProductSubtitle').textContent = `Mẫu rèm: ${productName}`;
    document.getElementById('modalCustomerComment').textContent = `"${comment}"`;
    document.getElementById('modalReplyContent').value = currentReply || '';
    
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<i class="fa-${i <= rating ? 'solid' : 'regular'} fa-star"></i>`;
    }
    document.getElementById('modalCustomerStars').innerHTML = starsHtml;
    
    modal.style.display = 'flex';
    document.getElementById('modalReplyContent').focus();
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}

function applyTemplate(key) {
    if (templates[key]) {
        document.getElementById('modalReplyContent').value = templates[key];
        document.getElementById('modalReplyContent').focus();
    }
}

// Close modal on click outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('replyModal');
    if (e.target === modal) {
        closeReplyModal();
    }
});
</script>
@endsection
