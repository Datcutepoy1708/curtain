@extends('admin.layouts.app')

@section('title', 'Kịch Bản Bot Tư Vấn')
@section('page-title', 'Quản Lý Kịch Bản Trả Lời Tự Động (CurtainBot Rules)')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start;">
    
    <!-- Left: Rules Table & Bulk Toolbar -->
    <div>
        <form id="bulkBotForm" action="{{ route('admin.bot-rules.bulk-action') }}" method="POST">
            @csrf

            <!-- Bulk Toolbar -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; padding: 12px 18px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="selectAllRules" style="width: 16px; height: 16px; cursor: pointer;">
                    <span>Chọn tất cả (<strong id="selectedRulesCount" style="color: var(--brand);">0</strong> đã chọn)</span>
                </label>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <select name="action" id="bulkRuleAction" class="form-select" style="font-size: 12.5px; padding: 6px 12px; width: auto;" required>
                        <option value="">-- Thao tác hàng loạt --</option>
                        <option value="activate">Bật kịch bản đã chọn</option>
                        <option value="deactivate">Tắt kịch bản đã chọn</option>
                        <option value="delete">Xóa kịch bản đã chọn</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnApplyRuleBulk">
                        Áp dụng
                    </button>
                </div>
            </div>

            <!-- Rules Table Card -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title" style="margin: 0; font-size: 15px; font-weight: 700;">
                        Danh Sách Kịch Bản Trả Lời Tự Động ({{ count($rules) }})
                    </h3>
                    <span style="font-size: 12px; color: var(--on-surface-variant);">Sắp xếp theo độ ưu tiên</span>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th>Tên Kịch Bản</th>
                                <th>Từ Khóa Kích Hoạt</th>
                                <th>Hành Động</th>
                                <th style="text-align: center; width: 70px;">Ưu Tiên</th>
                                <th style="text-align: center; width: 90px;">Trạng Thái</th>
                                <th style="text-align: right; width: 110px;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rules as $rule)
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="rule_ids[]" value="{{ $rule->rule_id }}" class="rule-item-checkbox" style="width: 16px; height: 16px; cursor: pointer;">
                                    </td>
                                    <td>
                                        <strong style="color: var(--on-surface); font-size: 13.5px;">{{ $rule->rule_name }}</strong>
                                        <div style="font-size: 11px; color: var(--on-surface-variant); margin-top: 2px;">
                                            Kiểu khớp: 
                                            @if($rule->match_type === 'EXACT')
                                                <span class="badge badge-info" style="font-size: 10px;">Chính xác</span>
                                            @elseif($rule->match_type === 'REGEX')
                                                <span class="badge badge-warning" style="font-size: 10px;">Regex</span>
                                            @else
                                                <span class="badge badge-neutral" style="font-size: 10px;">Chứa từ khóa</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <code style="background: var(--surface-alt); padding: 3px 6px; border-radius: 4px; font-size: 11.5px; display: inline-block; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $rule->keywords }}">
                                            {{ $rule->keywords }}
                                        </code>
                                    </td>
                                    <td>
                                        @if($rule->action_type === 'HANDOVER_STAFF')
                                            <span class="badge badge-warning" style="font-size: 11px; font-weight: 700;">
                                                <i class="fa-solid fa-headset"></i> Chuyển nhân viên
                                            </span>
                                        @else
                                            <span class="badge badge-success" style="font-size: 11px; font-weight: 700;">
                                                <i class="fa-solid fa-robot"></i> Trả lời tự động
                                            </span>
                                        @endif
                                        @if(!empty($rule->quick_replies) && is_array($rule->quick_replies))
                                            <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 4px;">
                                                @foreach($rule->quick_replies as $qr)
                                                    <span style="font-size: 10px; background: var(--brand-light); color: var(--brand); padding: 1px 6px; border-radius: 999px;">{{ $qr }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: var(--on-surface-variant);">
                                        {{ $rule->priority }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" data-toggle-url="{{ route('admin.bot-rules.toggle', $rule->rule_id) }}" class="badge {{ $rule->is_active ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 8px;" title="Nhấp để bật/tắt">
                                            {{ $rule->is_active ? 'Đang bật' : 'Tắt' }}
                                        </button>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 4px;">
                                            <button type="button" class="btn btn-secondary btn-sm" data-modal-target="#editRuleModal-{{ $rule->rule_id }}" style="padding: 4px 8px;" title="Sửa kịch bản">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form action="{{ route('admin.bot-rules.destroy', $rule->rule_id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa kịch bản bot này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" title="Xóa">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Edit Rule -->
                                <div class="admin-modal-backdrop" id="editRuleModal-{{ $rule->rule_id }}">
                                    <div class="admin-modal" style="max-width: 520px;">
                                        <div class="admin-modal-header">
                                            <h3 class="admin-modal-title">Sửa Kịch Bản: {{ $rule->rule_name }}</h3>
                                            <button type="button" class="admin-modal-close" data-modal-close>&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bot-rules.update', $rule->rule_id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 14px;">
                                                <div>
                                                    <label class="form-label">Tên kịch bản *:</label>
                                                    <input type="text" name="rule_name" class="form-control" value="{{ $rule->rule_name }}" required>
                                                </div>

                                                <div>
                                                    <label class="form-label">Từ khóa kích hoạt * (phân cách dấu phẩy):</label>
                                                    <input type="text" name="keywords" class="form-control" value="{{ $rule->keywords }}" required>
                                                </div>

                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                    <div>
                                                        <label class="form-label">Kiểu so khớp *:</label>
                                                        <select name="match_type" class="form-select">
                                                            <option value="CONTAINS" {{ $rule->match_type === 'CONTAINS' ? 'selected' : '' }}>Chứa từ khóa</option>
                                                            <option value="EXACT" {{ $rule->match_type === 'EXACT' ? 'selected' : '' }}>Khớp chính xác</option>
                                                            <option value="REGEX" {{ $rule->match_type === 'REGEX' ? 'selected' : '' }}>Biểu thức Regex</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="form-label">Hành động *:</label>
                                                        <select name="action_type" class="form-select">
                                                            <option value="REPLY" {{ $rule->action_type === 'REPLY' ? 'selected' : '' }}>Trả lời tự động</option>
                                                            <option value="HANDOVER_STAFF" {{ $rule->action_type === 'HANDOVER_STAFF' ? 'selected' : '' }}>Chuyển cho nhân viên</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="form-label">Nội dung phản hồi *:</label>
                                                    <textarea name="response_message" rows="4" class="form-control" required>{{ $rule->response_message }}</textarea>
                                                </div>

                                                <div>
                                                    <label class="form-label">Gợi ý câu trả lời nhanh (phân cách dấu phẩy):</label>
                                                    <input type="text" name="quick_replies" class="form-control" value="{{ is_array($rule->quick_replies) ? implode(', ', $rule->quick_replies) : '' }}">
                                                </div>

                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-items: center;">
                                                    <div>
                                                        <label class="form-label">Độ ưu tiên (lớn hơn chạy trước):</label>
                                                        <input type="number" name="priority" class="form-control" value="{{ $rule->priority }}">
                                                    </div>
                                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer; padding-top: 20px;">
                                                        <input type="checkbox" name="is_active" value="1" {{ $rule->is_active ? 'checked' : '' }}>
                                                        Kích hoạt kịch bản
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="admin-modal-footer">
                                                <button type="button" class="btn btn-secondary" data-modal-close>Đóng</button>
                                                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                        <i class="fa-solid fa-robot" style="font-size: 32px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                                        Chưa có kịch bản bot nào được tạo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!-- Right: Create Form Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title" style="margin: 0; font-size: 15px; font-weight: 700;">
                <i class="fa-solid fa-plus" style="color: var(--brand);"></i> Tạo Kịch Bản Mới
            </h3>
        </div>
        <form action="{{ route('admin.bot-rules.store') }}" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
            @csrf
            
            <div>
                <label class="form-label">Tên kịch bản *:</label>
                <input type="text" name="rule_name" class="form-control" placeholder="Vd: Hỏi báo giá rèm phòng khách" required>
            </div>

            <div>
                <label class="form-label">Từ khóa kích hoạt * (cách bằng dấu phẩy):</label>
                <input type="text" name="keywords" class="form-control" placeholder="giá, báo giá, bao nhiêu, chi phí" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label class="form-label">Kiểu khớp:</label>
                    <select name="match_type" class="form-select">
                        <option value="CONTAINS">Chứa từ khóa</option>
                        <option value="EXACT">Khớp chính xác</option>
                        <option value="REGEX">Regex</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Hành động:</label>
                    <select name="action_type" class="form-select">
                        <option value="REPLY">Trả lời tự động</option>
                        <option value="HANDOVER_STAFF">Chuyển nhân viên</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label">Nội dung phản hồi tự động *:</label>
                <textarea name="response_message" rows="4" class="form-control" placeholder="Nội dung bot sẽ gửi khi khách nhắn từ khóa này..." required></textarea>
            </div>

            <div>
                <label class="form-label">Gợi ý trả lời nhanh (cách bằng dấu phẩy):</label>
                <input type="text" name="quick_replies" class="form-control" placeholder="Xem mẫu rèm, Đặt lịch đo, Gặp tư vấn">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; align-items: center;">
                <div>
                    <label class="form-label">Độ ưu tiên:</label>
                    <input type="number" name="priority" value="0" class="form-control">
                </div>
                <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer; padding-top: 18px;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    Kích hoạt ngay
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 8px;">
                <i class="fa-solid fa-save"></i> Lưu Kịch Bản Bot
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllRules');
    const ruleCheckboxes = document.querySelectorAll('.rule-item-checkbox');
    const countDisplay = document.getElementById('selectedRulesCount');
    const bulkForm = document.getElementById('bulkBotForm');
    const bulkActionSelect = document.getElementById('bulkRuleAction');

    function updateRuleSelectionCount() {
        const checkedCount = document.querySelectorAll('.rule-item-checkbox:checked').length;
        if (countDisplay) {
            countDisplay.textContent = checkedCount;
        }
        if (selectAll) {
            selectAll.checked = (checkedCount > 0 && checkedCount === ruleCheckboxes.length);
            selectAll.indeterminate = (checkedCount > 0 && checkedCount < ruleCheckboxes.length);
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const isChecked = this.checked;
            ruleCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateRuleSelectionCount();
        });
    }

    ruleCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateRuleSelectionCount();
        });
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const checked = document.querySelectorAll('.rule-item-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một kịch bản bot để thực hiện thao tác.');
                return;
            }

            const action = bulkActionSelect ? bulkActionSelect.value : '';
            if (!action) {
                e.preventDefault();
                alert('Vui lòng chọn một thao tác hàng loạt (Kích hoạt, Tạm ngưng, hoặc Xóa).');
                return;
            }

            if (action === 'delete') {
                if (!confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn ${checked.length} kịch bản bot đã chọn? Thao tác này không thể khôi phục.`)) {
                    e.preventDefault();
                    return;
                }
            }
        });
    }

    // Toggle button AJAX handler
    document.querySelectorAll('[data-toggle-url]').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-toggle-url');
            if (!url) return;
            
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            const inputCsrf = document.createElement('input');
            inputCsrf.type = 'hidden';
            inputCsrf.name = '_token';
            inputCsrf.value = csrf;
            form.appendChild(inputCsrf);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
@endpush
