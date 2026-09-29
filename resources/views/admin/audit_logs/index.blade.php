@extends('admin.layouts.app')

@section('title', 'Nhật Ký Hoạt Động (Audit Log)')
@section('page-title', 'Nhật Ký Hoạt Động Hệ Thống')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--on-surface); margin: 0;">Nhật Ký Hoạt Động (Audit Trail)</h2>
            <p style="font-size: 0.85rem; color: var(--on-surface-variant); margin: 4px 0 0;">Theo dõi toàn bộ các thao tác nghiệp vụ, đổi trạng thái đơn hàng, phân công kỹ thuật và cấu hình của nhân sự.</p>
        </div>
        <div>
            <span class="badge badge-success" style="font-size: 12px; padding: 6px 14px;">
                <i class="fa-solid fa-shield-halved"></i> Tự động ghi nhận 24/7
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: 0; padding: 18px 24px;">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 14px;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--on-surface-variant); font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo mô tả, người thực hiện, mã đối tượng hoặc IP..."
                           style="width: 100%; padding: 8px 12px 8px 34px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
                </div>

                <div>
                    <select name="module"
                            style="width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
                        <option value="">-- Tất cả phân hệ --</option>
                        @foreach($modules as $code => $label)
                            <option value="{{ $code }}" {{ request('module') === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="action"
                            style="width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
                        <option value="">-- Tất cả hành động --</option>
                        @foreach($actions as $code => $act)
                            <option value="{{ $code }}" {{ request('action') === $code ? 'selected' : '' }}>{{ $act['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="user_id"
                            style="width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
                        <option value="">-- Tất cả người dùng --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role_name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 12px; color: var(--on-surface-variant); font-weight: 600;">Từ ngày:</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" style="padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 12px; background: var(--surface); color: var(--on-surface);">
                    <span style="font-size: 12px; color: var(--on-surface-variant); font-weight: 600;">Đến ngày:</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" style="padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 12px; background: var(--surface); color: var(--on-surface);">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Lọc Nhật Ký</button>
                    @if(request()->hasAny(['search', 'module', 'action', 'user_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary btn-sm" style="color: var(--danger);">Xóa lọc</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 170px;">Thời Gian</th>
                        <th style="width: 180px;">Người Thực Hiện</th>
                        <th style="width: 110px;">Hành Động</th>
                        <th style="width: 140px;">Phân Hệ</th>
                        <th>Nội Dung Thao Tác</th>
                        <th style="width: 130px;">IP / Thiết Bị</th>
                        <th style="width: 80px; text-align: right;">Chi Tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php $act = $log->action_badge; @endphp
                        <tr>
                            <td>
                                <strong style="font-size: 12.5px; color: var(--on-surface);">{{ $log->created_at->format('d/m/Y H:i:s') }}</strong>
                                <div style="font-size: 11px; color: var(--on-surface-variant);">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <strong style="font-size: 13px; color: var(--on-surface);">{{ $log->user_name }}</strong>
                                <div style="font-size: 11px; color: var(--brand); font-weight: 600;">
                                    {{ ucfirst($log->user_role) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $act['badge'] }}" style="font-size: 11px;">
                                    {{ $act['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px;">
                                    {{ $log->module_label }}
                                </span>
                            </td>
                            <td style="line-height: 1.5; font-size: 13px; color: var(--on-surface);">
                                {{ $log->description }}
                                @if($log->target_id)
                                    <span style="font-size: 11px; color: var(--brand); font-weight: 700; background: var(--surface-variant); padding: 1px 6px; border-radius: 4px; margin-left: 4px;">
                                        #{{ $log->target_id }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <code style="font-size: 11px; color: var(--on-surface-variant); background: var(--surface-variant); padding: 2px 6px; border-radius: 4px;">
                                    {{ $log->ip_address }}
                                </code>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="showLogDetail({{ $log->id }})" title="Xem chi tiết biến động dữ liệu">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                Chưa có nhật ký thao tác nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Chi Tiết Nhật Ký Payload -->
<div class="admin-modal-backdrop" id="logDetailModal" style="display: none;">
    <div class="admin-modal" style="max-width: 650px;">
        <div class="admin-modal-header">
            <h3 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--brand);"></i> Chi Tiết Nhật Ký Hệ Thống</h3>
            <button type="button" class="admin-modal-close" onclick="closeLogModal()">&times;</button>
        </div>
        <div class="admin-modal-body" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: var(--surface-variant); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px;">
                <div><strong>Người thực hiện:</strong> <span id="modal_user"></span></div>
                <div><strong>Thời gian:</strong> <span id="modal_time"></span></div>
                <div><strong>Phân hệ:</strong> <span id="modal_module"></span></div>
                <div><strong>Địa chỉ IP:</strong> <span id="modal_ip"></span></div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Mô tả:</label>
                <div id="modal_desc" style="font-size: 14px; font-weight: 600; color: var(--on-surface); margin-top: 4px;"></div>
            </div>

            <div id="modal_changes_box" style="display: none;">
                <label style="font-size: 12px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Biến động dữ liệu (Payload):</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 6px;">
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: var(--danger);">Giá trị cũ (Before):</span>
                        <pre id="modal_old_json" style="background: #f8fafc; border: 1px solid var(--border); padding: 10px; border-radius: 6px; font-size: 11.5px; max-height: 180px; overflow-y: auto; color: #334155; margin-top: 4px;"></pre>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; color: #16a34a;">Giá trị mới (After):</span>
                        <pre id="modal_new_json" style="background: #f8fafc; border: 1px solid var(--border); padding: 10px; border-radius: 6px; font-size: 11.5px; max-height: 180px; overflow-y: auto; color: #334155; margin-top: 4px;"></pre>
                    </div>
                </div>
            </div>
        </div>
        <div class="admin-modal-footer" style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeLogModal()">Đóng cửa sổ</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showLogDetail(id) {
    fetch('/admin/audit-logs/' + id)
        .then(res => res.json())
        .then(data => {
            document.getElementById('modal_user').textContent = data.user_name + ' (' + data.user_role + ')';
            document.getElementById('modal_time').textContent = data.created_at;
            document.getElementById('modal_module').textContent = data.module_label;
            document.getElementById('modal_ip').textContent = data.ip_address;
            document.getElementById('modal_desc').textContent = data.description;

            const changesBox = document.getElementById('modal_changes_box');
            if (data.old_values || data.new_values) {
                changesBox.style.display = 'block';
                document.getElementById('modal_old_json').textContent = data.old_values ? JSON.stringify(data.old_values, null, 2) : 'Không có';
                document.getElementById('modal_new_json').textContent = data.new_values ? JSON.stringify(data.new_values, null, 2) : 'Không có';
            } else {
                changesBox.style.display = 'none';
            }

            const modal = document.getElementById('logDetailModal');
            modal.style.display = 'flex';
            modal.classList.add('is-open');
        })
        .catch(err => alert('Không thể tải chi tiết nhật ký: ' + err));
}

function closeLogModal() {
    const modal = document.getElementById('logDetailModal');
    modal.style.display = 'none';
    modal.classList.remove('is-open');
}
</script>
@endpush
@endsection
