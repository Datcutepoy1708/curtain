@extends('admin.layouts.app')

@section('title', 'Quản Lý Câu Hỏi Thường Gặp (FAQ)')
@section('page-title', 'Quản Lý Hỏi Đáp (FAQ)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--on-surface); margin: 0;">Danh Sách Câu Hỏi Thường Gặp</h2>
            <p style="font-size: 0.85rem; color: var(--on-surface-variant); margin: 4px 0 0;">Quản lý nội dung hỏi đáp xuất hiện tại trang hỗ trợ khách hàng và chatbot.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('faq.index') }}" target="_blank" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem Giao Diện Khách
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="openFaqModal('createFaqModal')">
                <i class="fa-solid fa-plus"></i> Thêm Câu Hỏi Mới
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: 0; padding: 18px 24px;">
        <form method="GET" action="{{ route('admin.faqs.index') }}" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px; position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--on-surface-variant); font-size: 13px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tiêu đề hoặc nội dung câu hỏi..."
                       style="width: 100%; padding: 8px 12px 8px 34px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
            </div>

            <div style="min-width: 180px;">
                <select name="category" onchange="this.form.submit()"
                        style="width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
                    <option value="">-- Tất cả chủ đề --</option>
                    @foreach($categories as $code => $cat)
                        <option value="{{ $code }}" {{ request('category') === $code ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-secondary btn-sm">Lọc dữ liệu</button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-sm" style="color: var(--danger);">Xóa lọc</a>
            @endif
        </form>
    </div>

    <!-- FAQ Table Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th>Câu Hỏi & Chủ Đề</th>
                        <th style="width: 38%;">Tóm Tắt Câu Trả Lời</th>
                        <th style="width: 120px; text-align: center;">Trạng Thái</th>
                        <th style="width: 130px; text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td style="color: var(--on-surface-variant); font-weight: 600;">
                                {{ $faq->sort_order ?: ($loop->iteration + ($faqs->currentPage() - 1) * $faqs->perPage()) }}
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: var(--on-surface);">{{ $faq->question }}</strong>
                                <div style="margin-top: 4px;">
                                    <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px;">
                                        <i class="fa-solid {{ $faq->category_icon }}"></i> {{ $faq->category_label }}
                                    </span>
                                </div>
                            </td>
                            <td style="color: var(--on-surface-variant); font-size: 13px; line-height: 1.5;">
                                {{ \Illuminate\Support\Str::limit($faq->answer, 110) }}
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.faqs.toggle', $faq->id) }}" method="POST" style="margin: 0; display: inline-block;">
                                    @csrf
                                    <button type="submit" class="badge {{ $faq->is_active ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer;" title="Nhấn để đổi trạng thái">
                                        {{ $faq->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openEditFaqModal({{ $faq->id }}, '{{ addslashes($faq->question) }}', '{{ addslashes($faq->answer) }}', '{{ $faq->category }}', {{ $faq->sort_order }}, {{ $faq->is_active ? 1 : 0 }})" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger);" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                Không có câu hỏi nào phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Thêm Câu Hỏi Mới -->
<div class="admin-modal-backdrop" id="createFaqModal" style="display: none;">
    <div class="admin-modal" style="max-width: 600px;">
        <div class="admin-modal-header">
            <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-circle-question" style="color: var(--brand);"></i> Thêm Câu Hỏi FAQ Mới</h3>
            <button type="button" class="admin-modal-close" onclick="closeFaqModal('createFaqModal')">&times;</button>
        </div>
        <form action="{{ route('admin.faqs.store') }}" method="POST">
            @csrf
            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 16px; padding: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chủ Đề Nhóm *</label>
                    <select name="category" required style="width: 100%; padding: 9px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);">
                        @foreach($categories as $code => $cat)
                            <option value="{{ $code }}">{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Câu Hỏi (Tiêu đề) *</label>
                    <input type="text" name="question" required placeholder="Ví dụ: Rèm vải 2 lớp có giặt nước được không?"
                           style="width: 100%; padding: 9px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Câu Trả Lời Chi Tiết *</label>
                    <textarea name="answer" rows="5" required placeholder="Nhập câu trả lời cụ thể, giải thích cặn kẽ để tư vấn khách hàng..."
                              style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); line-height: 1.6;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Thứ Tự Sắp Xếp</label>
                        <input type="number" name="sort_order" value="0" min="0"
                               style="width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 24px;">
                        <input type="checkbox" name="is_active" value="1" id="create_is_active" checked style="width: 18px; height: 18px;">
                        <label for="create_is_active" style="font-size: 13px; font-weight: 600; cursor: pointer;">Hiển thị ngay</label>
                    </div>
                </div>
            </div>
            <div class="admin-modal-footer" style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeFaqModal('createFaqModal')">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i> Lưu Câu Hỏi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Chỉnh Sửa Câu Hỏi -->
<div class="admin-modal-backdrop" id="editFaqModal" style="display: none;">
    <div class="admin-modal" style="max-width: 600px;">
        <div class="admin-modal-header">
            <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-pen-to-square" style="color: var(--brand);"></i> Cập Nhật Câu Hỏi FAQ</h3>
            <button type="button" class="admin-modal-close" onclick="closeFaqModal('editFaqModal')">&times;</button>
        </div>
        <form id="editFaqForm" method="POST">
            @csrf
            @method('PUT')
            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 16px; padding: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chủ Đề Nhóm *</label>
                    <select name="category" id="edit_category" required style="width: 100%; padding: 9px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);">
                        @foreach($categories as $code => $cat)
                            <option value="{{ $code }}">{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Câu Hỏi (Tiêu đề) *</label>
                    <input type="text" name="question" id="edit_question" required
                           style="width: 100%; padding: 9px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Câu Trả Lời Chi Tiết *</label>
                    <textarea name="answer" id="edit_answer" rows="5" required
                              style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); line-height: 1.6;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Thứ Tự Sắp Xếp</label>
                        <input type="number" name="sort_order" id="edit_sort_order" min="0"
                               style="width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 24px;">
                        <input type="checkbox" name="is_active" value="1" id="edit_is_active" style="width: 18px; height: 18px;">
                        <label for="edit_is_active" style="font-size: 13px; font-weight: 600; cursor: pointer;">Hiển thị ngay</label>
                    </div>
                </div>
            </div>
            <div class="admin-modal-footer" style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeFaqModal('editFaqModal')">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openFaqModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('is-open');
    }
}
function closeFaqModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('is-open');
    }
}
function openEditFaqModal(id, question, answer, category, sortOrder, isActive) {
    const form = document.getElementById('editFaqForm');
    form.action = '/admin/faqs/' + id;
    document.getElementById('edit_question').value = question;
    document.getElementById('edit_answer').value = answer;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_sort_order').value = sortOrder;
    document.getElementById('edit_is_active').checked = (isActive == 1);
    openFaqModal('editFaqModal');
}
</script>
@endpush
@endsection
