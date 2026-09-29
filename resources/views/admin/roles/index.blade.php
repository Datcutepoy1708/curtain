@extends('admin.layouts.app')

@section('title', 'Vai Trò & Phân Quyền')
@section('page-title', 'Quản Lý Vai Trò & Ma Trận Phân Quyền Hệ Thống')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- ── 1. Role Selection Cards ────────────────────────────────────── -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 15px; font-weight: 700; color: var(--on-surface);">
                    Chọn chức vụ / vai trò để cấu hình ma trận quyền hạn
                </h3>
                <span style="font-size: 12.5px; color: var(--on-surface-variant);">
                    Hệ thống có <strong>{{ $roles->count() }}</strong> vai trò nghiệp vụ được định nghĩa sẵn
                </span>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-modal-target="#createRoleModal">
                <i class="fa-solid fa-plus"></i> Thêm Vai Trò Mới
            </button>
        </div>

        <div style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            @foreach($roles as $r)
                @php 
                    $isSelected = ($r->id === $selectedRole->id); 
                    $permCount = is_array($r->permissions) ? count($r->permissions) : 0;
                @endphp
                <a href="{{ route('admin.roles.index', ['role_id' => $r->id]) }}" 
                   style="text-decoration: none; border: 2px solid {{ $isSelected ? 'var(--brand)' : 'var(--border)' }}; border-radius: var(--radius-md); padding: 16px; background: {{ $isSelected ? 'var(--brand-light)' : 'var(--surface-alt)' }}; transition: all 0.2s ease; display: block;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <strong style="font-size: 14.5px; color: {{ $isSelected ? 'var(--brand)' : 'var(--on-surface)' }};">{{ $r->name }}</strong>
                        <span class="badge {{ $isSelected ? 'badge-primary' : 'badge-neutral' }}" style="font-size: 11px; font-weight: 700;">
                            {{ $r->code === 'ROLE_ADMIN' ? 'Toàn quyền' : $permCount . ' quyền' }}
                        </span>
                    </div>
                    <code style="font-size: 11px; color: var(--on-surface-variant); display: block; margin-bottom: 4px;">{{ $r->code }}</code>
                    <p style="font-size: 12px; color: var(--on-surface-variant); margin: 0; line-height: 1.4;">
                        {{ $r->description ?? 'Vai trò nghiệp vụ hệ thống CurtainLux' }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>

    <!-- ── 2. Permission Matrix Form ──────────────────────────────────── -->
    <div class="card" style="margin-bottom: 0;">
        <form action="{{ route('admin.roles.update-permissions', $selectedRole->id) }}" method="POST">
            @csrf
            
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0 0 4px;">
                        Phân Quyền Cho: <span style="color: var(--brand);">{{ $selectedRole->name }}</span>
                        <code style="font-size: 12px; font-weight: normal; margin-left: 6px;">({{ $selectedRole->code }})</code>
                    </h2>
                    <p style="font-size: 13px; color: var(--on-surface-variant); margin: 0;">
                        Đang kích hoạt: <strong style="color: var(--brand);">{{ count($selectedPermissions) }}</strong> quyền chức năng trên tổng số module.
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-outline btn-sm" data-role-perm-select-all="true">
                        <i class="fa-solid fa-check-double"></i> Chọn Tất Cả
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" data-role-perm-select-all="false">
                        <i class="fa-solid fa-xmark"></i> Bỏ Chọn Hết
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Ma Trận Phân Quyền
                    </button>
                </div>
            </div>

            <div style="padding: 24px;">
                @if($selectedRole->code === 'ROLE_ADMIN' || $selectedRole->code === 'admin')
                    <div class="alert alert-info" style="margin-bottom: 20px;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span><strong>Lưu ý:</strong> Vai trò <code>ROLE_ADMIN</code> là tài khoản Quản trị cấp cao nhất, mặc định luôn có toàn quyền truy cập mọi tài nguyên trong hệ thống.</span>
                    </div>
                @endif

                <!-- Grouped Matrix Cards -->
                <div class="permissions-matrix-grid">
                    @foreach($permissionGroups as $moduleKey => $moduleData)
                        <div class="permission-card">
                            <div class="permission-card-header">
                                <span class="permission-card-title">
                                    <i class="fa-solid fa-folder-tree" style="color: var(--brand);"></i>
                                    {{ $moduleData['label'] }}
                                </span>
                                <label style="font-size: 12px; color: var(--brand); cursor: pointer; display: flex; align-items: center; gap: 6px; font-weight: 600;">
                                    <input type="checkbox" class="module-group-toggle" data-module-toggle="{{ $moduleKey }}" style="width: 15px; height: 15px; cursor: pointer;">
                                    <span>Chọn nhóm</span>
                                </label>
                            </div>
                            <div class="permission-card-body" data-module-container="{{ $moduleKey }}">
                                @foreach($moduleData['permissions'] as $pKey => $pLabel)
                                    @php $isChecked = in_array($pKey, $selectedPermissions); @endphp
                                    <label class="permission-item">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $pKey }}" 
                                               class="role-perm-item perm-mod-{{ $moduleKey }}"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <div>
                                            <div class="permission-item-label">{{ $pLabel }}</div>
                                            <div class="permission-item-key">{{ $pKey }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 700;">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Ma Trận Phân Quyền
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create Role -->
<div class="admin-modal-backdrop" id="createRoleModal">
    <div class="admin-modal" style="max-width: 480px;">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Thêm Chức Vụ / Vai Trò Mới</h3>
            <button type="button" class="admin-modal-close" data-modal-close>&times;</button>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label class="form-label">Tên chức vụ / vai trò *:</label>
                    <input type="text" name="name" class="form-control" placeholder="Vd: Chuyên Viên Chăm Sóc Khách Hàng" required>
                </div>

                <div>
                    <label class="form-label">Mã định danh chức vụ (Tự động thêm ROLE_) *:</label>
                    <input type="text" name="code" class="form-control" placeholder="CSKH, WAREHOUSE, ACCOUNTANT..." required style="text-transform: uppercase;">
                    <small style="color: var(--on-surface-variant); font-size: 11px; margin-top: 4px; display: block;">
                        Ví dụ nhập: <code>CSKH</code> &rarr; Hệ thống lưu: <code>ROLE_CSKH</code>
                    </small>
                </div>

                <div>
                    <label class="form-label">Mô tả tóm tắt quyền hạn:</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Vd: Nhân viên phụ trách trực chat và gọi điện chăm sóc khách hàng..."></textarea>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Hủy bỏ</button>
                <button type="submit" class="btn btn-primary">Tạo Vai Trò Mới</button>
            </div>
        </form>
    </div>
</div>
@endsection
