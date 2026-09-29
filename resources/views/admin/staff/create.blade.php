@extends('admin.layouts.app')

@section('title', 'Thêm Tài Khoản Nhân Sự')
@section('page-title', 'Thêm Tài Khoản Nhân Sự & Thiết Lập Quyền Hạn')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Thêm Tài Khoản Nhân Sự Mới</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Cấp tài khoản đăng nhập nội bộ và cấu hình vai trò, quyền thao tác theo vị trí làm việc.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.staff.store') }}" method="POST">
        @csrf

        <!-- Basic Info Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-id-badge" style="color: var(--brand);"></i>
                    Thông Tin Cơ Bản & Vai Trò
                </div>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Họ và tên nhân viên *:</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Vd: Nguyễn Văn Hùng" required>
                    </div>

                    <div>
                        <label class="form-label">Địa chỉ email đăng nhập *:</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="hung.nguyen@curtainlux.vn" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Số điện thoại liên hệ:</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="0901234567">
                    </div>

                    <div>
                        <label class="form-label">Mật khẩu khởi tạo *:</label>
                        <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Vai trò chức vụ chính *:</label>
                        <select name="role" class="form-select">
                            <option value="sales" {{ old('role') === 'sales' ? 'selected' : '' }}>Nhân Viên Tư Vấn & Bán Hàng</option>
                            <option value="technician" {{ old('role') === 'technician' ? 'selected' : '' }}>Kỹ Thuật Viên / Thợ Khảo Sát Đo Đạc</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Quản Lý Showroom / Cửa Hàng</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Super Admin (Quản Trị Cấp Cao - Toàn quyền)</option>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Nhân Viên Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Trạng thái khởi tạo *:</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Kích hoạt hoạt động ngay</option>
                            <option value="banned" {{ old('status') === 'banned' ? 'selected' : '' }}>Tạm khóa tài khoản</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Matrix Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div>
                    <div class="card-title">
                        <i class="fa-solid fa-shield-halved" style="color: var(--brand);"></i>
                        Ma Trận Phân Quyền Chi Tiết (Granular Permissions)
                    </div>
                    <span style="font-size: 12.5px; color: var(--on-surface-variant);">
                        Tích chọn các quyền mà nhân viên được phép truy cập & thao tác. Super Admin tự động có toàn bộ quyền.
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="permissions-matrix-grid">
                    @foreach($permissionGroups as $groupKey => $groupData)
                        <div class="permission-card">
                            <div class="permission-card-header">
                                <span class="permission-card-title">
                                    <i class="fa-solid fa-folder-tree" style="color: var(--brand);"></i>
                                    {{ $groupData['label'] }}
                                </span>
                            </div>
                            <div class="permission-card-body">
                                @foreach($groupData['permissions'] as $permKey => $permLabel)
                                    <label class="permission-item">
                                        <input type="checkbox" name="permissions[]" value="{{ $permKey }}">
                                        <div>
                                            <div class="permission-item-label">{{ $permLabel }}</div>
                                            <div class="permission-item-key">{{ $permKey }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 30px;">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Hủy bỏ</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                <i class="fa-solid fa-user-check"></i> Lưu Tài Khoản Mới
            </button>
        </div>
    </form>
</div>
@endsection
