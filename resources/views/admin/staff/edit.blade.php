@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Tài Khoản Nhân Sự')
@section('page-title', 'Chỉnh Sửa Nhân Sự & Phân Quyền Chi Tiết')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Chỉnh Sửa Tài Khoản: {{ $user->name }}</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Cập nhật thông tin định danh, chức vụ và ma trận phân quyền truy cập các module.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.staff.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

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
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Địa chỉ email đăng nhập *:</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Số điện thoại liên hệ:</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="0901234567">
                    </div>

                    <div>
                        <label class="form-label">Đổi mật khẩu mới:</label>
                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không muốn đổi">
                        <small style="color: var(--on-surface-variant); font-size: 11px;">Chỉ nhập khi cần đặt lại mật khẩu mới cho nhân viên (tối thiểu 6 ký tự).</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Vai trò chức vụ chính *:</label>
                        <select name="role" id="staffRoleSelect" class="form-select">
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Super Admin (Quản Trị Viên Cấp Cao - Toàn quyền)</option>
                            <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Quản Lý Showroom / Cửa Hàng</option>
                            <option value="sales" {{ old('role', $user->role) === 'sales' ? 'selected' : '' }}>Nhân Viên Tư Vấn & Bán Hàng</option>
                            <option value="technician" {{ old('role', $user->role) === 'technician' ? 'selected' : '' }}>Kỹ Thuật Viên / Thợ Khảo Sát Đo Đạc</option>
                            <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Nhân Viên Nội Bộ Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Trạng thái tài khoản *:</label>
                        <select name="status" class="form-select" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="banned" {{ old('status', $user->status) === 'banned' ? 'selected' : '' }}>Đã khóa tài khoản</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="status" value="active">
                            <small style="color: var(--brand); font-size: 11px;">Bạn không thể tự khóa tài khoản của chính mình.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Matrix Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <div class="card-title">
                        <i class="fa-solid fa-shield-halved" style="color: var(--brand);"></i>
                        Ma Trận Phân Quyền Chi Tiết (Granular Permissions)
                    </div>
                    <span style="font-size: 12.5px; color: var(--on-surface-variant);">
                        Đánh dấu các quyền mà nhân sự được phép truy cập & thao tác. Super Admin tự động có toàn quyền.
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
                                        <input type="checkbox" name="permissions[]" value="{{ $permKey }}"
                                               {{ $user->isAdmin() || $user->hasPermission($permKey) ? 'checked' : '' }}>
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
                <i class="fa-solid fa-floppy-disk"></i> Lưu Cập Nhật Quyền Hạn
            </button>
        </div>
    </form>
</div>
@endsection
