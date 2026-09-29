@extends('admin.layouts.app')

@section('title', 'Tùy Chọn May Rèm & Phụ Kiện')
@section('page-title', 'Quản Lý Tùy Chọn May Rèm & Bảng Giá Phụ Kiện')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Top Header & Action -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Quy Cách May Đo & Bảng Giá Phụ Kiện</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Cấu hình các kiểu may (ore, xếp ly, định hình), hệ thanh ray, động cơ thông minh và phụ phí tính toán khi khách đặt hàng.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-modal-target="#newGroupModal">
            <i class="fa-solid fa-plus"></i> Thêm Nhóm Tùy Chọn Mới
        </button>
    </div>

    <!-- ── Bulk Action Synchronized Floating Bar for Option Values ── -->
    <div class="bulk-action-bar" data-bulk-url="{{ route('admin.options.bulk-action') }}">
        <div class="bulk-action-info">
            <i class="fa-solid fa-square-check" style="color: var(--brand);"></i>
            <span>Đã chọn: <strong class="bulk-count-badge">0</strong> tùy chọn phụ kiện</span>
        </div>
        <div class="bulk-action-buttons">
            <button type="button" class="btn btn-outline btn-sm" data-bulk-action="activate" title="Kích hoạt các tùy chọn đã chọn">
                <i class="fa-solid fa-eye" style="color: #16a34a;"></i> Kích Hoạt
            </button>
            <button type="button" class="btn btn-outline btn-sm" data-bulk-action="deactivate" title="Vô hiệu hóa các tùy chọn đã chọn">
                <i class="fa-solid fa-eye-slash" style="color: #64748b;"></i> Vô Hiệu Hóa
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-bulk-action="delete" title="Xóa các tùy chọn đã chọn">
                <i class="fa-solid fa-trash"></i> Xóa Hàng Loạt
            </button>
        </div>
    </div>

    <!-- Option Groups Cards -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse($groups as $group)
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header" style="background: var(--surface-alt); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span class="badge badge-info" style="font-size: 11px;">Mã: {{ $group->code }}</span>
                        <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--on-surface);">{{ $group->name }}</h3>
                        @if($group->is_required)
                            <span class="badge badge-warning" style="font-size: 10px;">Bắt buộc chọn</span>
                        @endif
                        <button type="button" data-toggle-url="{{ route('admin.options.toggle-group', $group->id) }}" class="badge {{ $group->is_active ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 3px 8px; font-size: 11px;" title="Nhấp để bật/tắt nhóm này">
                            <i class="fa-solid {{ $group->is_active ? 'fa-check' : 'fa-ban' }}"></i>
                            {{ $group->is_active ? 'Đang hoạt động' : 'Tạm dừng' }}
                        </button>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="button" class="btn btn-secondary btn-sm" data-modal-target="#editGroupModal-{{ $group->id }}" title="Chỉnh sửa thông tin nhóm">
                            <i class="fa-solid fa-pen-to-square"></i> Sửa Nhóm
                        </button>
                        <form action="{{ route('admin.options.destroy-group', $group->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa toàn bộ nhóm tùy chọn \'{{ $group->name }}\' và tất cả tùy chọn phụ kiện thuộc nhóm?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 5px 8px;" title="Xóa nhóm tùy chọn">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" class="item-checkbox-all" data-select-group="{{ $group->id }}" style="width: 15px; height: 15px; cursor: pointer;" title="Chọn tất cả trong nhóm này">
                                </th>
                                <th>Tên Tùy Chọn / Phụ Kiện</th>
                                <th>Mô Tả Quy Cách</th>
                                <th>Cách Tính Phụ Phí</th>
                                <th>Mức Phụ Thu</th>
                                <th>Mặc Định</th>
                                <th style="text-align: center; width: 110px;">Trạng Thái</th>
                                <th style="text-align: right; width: 110px;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group->values as $val)
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" class="item-checkbox" value="{{ $val->id }}" data-group-id="{{ $group->id }}" style="width: 16px; height: 16px; cursor: pointer;">
                                    </td>
                                    <td>
                                        <strong style="font-size: 13px; color: var(--on-surface);">{{ $val->name }}</strong>
                                    </td>
                                    <td style="color: var(--on-surface-variant); font-size: 12px; max-width: 250px;">
                                        {{ $val->description ?: 'Tiêu chuẩn xưởng CurtainLux' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-neutral">
                                            @if($val->price_type === 'fixed')
                                                Cố định / bộ
                                            @elseif($val->price_type === 'per_meter')
                                                Theo mét ngang
                                            @else
                                                Theo m²
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($val->surcharge > 0)
                                            <strong style="color: #d97706; font-size: 13px;">+{{ number_format($val->surcharge, 0, ',', '.') }}₫</strong>
                                        @else
                                            <span style="color: #16a34a; font-weight: 600; font-size: 12px;">Miễn phí kèm theo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($val->is_default)
                                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> Mặc định</span>
                                        @else
                                            <span style="color: var(--on-surface-variant); font-size: 12px;">Tùy chọn</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" data-toggle-url="{{ route('admin.options.toggle-value', $val->id) }}" class="badge {{ $val->is_active ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 8px;" title="Nhấp để bật/tắt">
                                            {{ $val->is_active ? 'Hoạt động' : 'Tạm tắt' }}
                                        </button>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                            <button type="button" class="btn btn-secondary btn-sm" data-modal-target="#editValueModal-{{ $val->id }}" style="padding: 4px 8px;" title="Sửa tùy chọn">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <form action="{{ route('admin.options.destroy-value', $val->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tùy chọn này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" title="Xóa tùy chọn">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal: Edit Value -->
                                <div class="admin-modal-backdrop" id="editValueModal-{{ $val->id }}">
                                    <div class="admin-modal">
                                        <div class="admin-modal-header">
                                            <h3 class="admin-modal-title">Sửa Tùy Chọn: {{ $val->name }}</h3>
                                            <button type="button" class="admin-modal-close" data-modal-close>&times;</button>
                                        </div>
                                        <form action="{{ route('admin.options.update-value', $val->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 14px;">
                                                <div>
                                                    <label class="form-label">Tên tùy chọn / phụ kiện:</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $val->name }}" required>
                                                </div>

                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                    <div>
                                                        <label class="form-label">Mức phụ thu (₫):</label>
                                                        <input type="number" name="surcharge" class="form-control" value="{{ (int)$val->surcharge }}" min="0" step="1000" required>
                                                    </div>
                                                    <div>
                                                        <label class="form-label">Cách tính giá:</label>
                                                        <select name="price_type" class="form-select">
                                                            <option value="fixed" {{ $val->price_type === 'fixed' ? 'selected' : '' }}>Cố định (₫ / bộ)</option>
                                                            <option value="per_meter" {{ $val->price_type === 'per_meter' ? 'selected' : '' }}>Theo mét ngang (₫ / m)</option>
                                                            <option value="per_sqm" {{ $val->price_type === 'per_sqm' ? 'selected' : '' }}>Theo diện tích (₫ / m²)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="form-label">Mô tả quy cách kỹ thuật:</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $val->description }}</textarea>
                                                </div>

                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                                    <div>
                                                        <label class="form-label">Thứ tự hiển thị:</label>
                                                        <input type="number" name="sort_order" class="form-control" value="{{ $val->sort_order ?? 0 }}">
                                                    </div>
                                                    <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center; padding-top: 18px;">
                                                        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
                                                            <input type="checkbox" name="is_default" value="1" {{ $val->is_default ? 'checked' : '' }}>
                                                            Mặc định chọn khi mở form
                                                        </label>
                                                        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
                                                            <input type="checkbox" name="is_active" value="1" {{ $val->is_active ? 'checked' : '' }}>
                                                            Kích hoạt hoạt động
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="admin-modal-footer">
                                                <button type="button" class="btn btn-secondary" data-modal-close>Đóng</button>
                                                <button type="submit" class="btn btn-primary">Cập Nhật Tùy Chọn</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 24px; color: var(--on-surface-variant);">
                                        Chưa có tùy chọn phụ kiện nào trong nhóm này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Add Value Inline Form -->
                <div style="padding: 14px 20px; background: var(--surface-alt); border-top: 1px solid var(--border);">
                    <form action="{{ route('admin.options.store-value') }}" method="POST" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                        @csrf
                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                        
                        <input type="text" name="name" placeholder="Tên phụ kiện / kiểu may mới..." required
                               style="padding: 7px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; flex: 2; min-width: 180px;">

                        <input type="number" name="surcharge" value="0" min="0" step="1000" placeholder="Phụ phí (₫)" required
                               style="padding: 7px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; width: 120px;">

                        <select name="price_type" style="padding: 7px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px;">
                            <option value="fixed">Cố định (₫ / bộ)</option>
                            <option value="per_meter">Theo mét ngang (₫ / m)</option>
                            <option value="per_sqm">Theo diện tích (₫ / m²)</option>
                        </select>

                        <input type="text" name="description" placeholder="Mô tả ngắn gọn..."
                               style="padding: 7px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; flex: 2; min-width: 160px;">

                        <label style="display: flex; align-items: center; gap: 4px; font-size: 12px; cursor: pointer;">
                            <input type="checkbox" name="is_default" value="1">
                            Mặc định
                        </label>

                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 7px 14px;">
                            <i class="fa-solid fa-plus"></i> Thêm vào nhóm
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal: Edit Group -->
            <div class="admin-modal-backdrop" id="editGroupModal-{{ $group->id }}">
                <div class="admin-modal">
                    <div class="admin-modal-header">
                        <h3 class="admin-modal-title">Sửa Nhóm Tùy Chọn: {{ $group->name }}</h3>
                        <button type="button" class="admin-modal-close" data-modal-close>&times;</button>
                    </div>
                    <form action="{{ route('admin.options.update-group', $group->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 14px;">
                            <div>
                                <label class="form-label">Tên nhóm tùy chọn:</label>
                                <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                            </div>

                            <div>
                                <label class="form-label">Mã code định danh:</label>
                                <input type="text" name="code" class="form-control" value="{{ $group->code }}" required>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label class="form-label">Áp dụng cho:</label>
                                    <input type="text" name="applies_to" class="form-control" value="{{ $group->applies_to }}" placeholder="all, hoặc để trống">
                                </div>
                                <div>
                                    <label class="form-label">Thứ tự sắp xếp:</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $group->sort_order ?? 0 }}">
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 10px; padding-top: 6px;">
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                    <input type="checkbox" name="is_required" value="1" {{ $group->is_required ? 'checked' : '' }}>
                                    Khách hàng bắt buộc phải chọn khi đặt hàng
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                    <input type="checkbox" name="is_active" value="1" {{ $group->is_active ? 'checked' : '' }}>
                                    Kích hoạt nhóm tùy chọn
                                </label>
                            </div>
                        </div>
                        <div class="admin-modal-footer">
                            <button type="button" class="btn btn-secondary" data-modal-close>Hủy</button>
                            <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 40px; text-align: center; color: var(--on-surface-variant);">
                Chưa có nhóm tùy chọn nào. Nhấp vào "Thêm Nhóm Tùy Chọn Mới" để bắt đầu thiết lập.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal: New Group -->
<div class="admin-modal-backdrop" id="newGroupModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Thêm Nhóm Tùy Chọn Rèm Mới</h3>
            <button type="button" class="admin-modal-close" data-modal-close>&times;</button>
        </div>
        <form action="{{ route('admin.options.store-group') }}" method="POST">
            @csrf
            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label class="form-label">Tên nhóm tùy chọn:</label>
                    <input type="text" name="name" class="form-control" placeholder="Vd: Kiểu may rèm, Hệ thanh treo, Động cơ..." required>
                </div>

                <div>
                    <label class="form-label">Mã code định danh (viết liền, không dấu):</label>
                    <input type="text" name="code" class="form-control" placeholder="Vd: header_style, track_system, smart_motor" required>
                </div>

                <div>
                    <label class="form-label">Loại chọn lựa:</label>
                    <select name="type" class="form-select">
                        <option value="single_select">Chọn duy nhất 1 mục (Radio)</option>
                        <option value="multi_select">Chọn nhiều mục (Checkbox)</option>
                        <option value="boolean">Bật / Tắt (Có / Không)</option>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                        <input type="checkbox" name="is_required" value="1" checked>
                        Khách hàng bắt buộc phải chọn khi đặt hàng
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Kích hoạt nhóm tùy chọn ngay lập tức
                    </label>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu Nhóm Mới</button>
            </div>
        </form>
    </div>
</div>
@endsection
