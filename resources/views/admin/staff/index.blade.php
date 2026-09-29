@extends('admin.layouts.app')

@section('title', 'Quản Lý Nhân Sự & Phân Quyền')
@section('page-title', 'Danh Sách Đội Ngũ Nhân Sự & Phân Quyền')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Quản Lý Đội Ngũ Nhân Viên & Phân Quyền</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Danh sách tài khoản nội bộ: Quản trị viên, quản lý showroom, tư vấn bán hàng và thợ kỹ thuật lắp đặt.</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus"></i> Thêm Nhân Sự Mới
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 14px 20px;">
        <form action="{{ route('admin.staff.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, email, số điện thoại..."
                   style="min-width: 260px; flex: 1; padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">

            <select name="role" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Tất cả chức vụ --</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Quản Lý Showroom</option>
                <option value="sales" {{ request('role') === 'sales' ? 'selected' : '' }}>Nhân Viên Bán Hàng</option>
                <option value="technician" {{ request('role') === 'technician' ? 'selected' : '' }}>Thợ Kỹ Thuật Đo Đạc</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Nhân Viên Khác</option>
            </select>

            <select name="status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Đã khóa</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            @if(request()->anyFilled(['search', 'role', 'status']))
                <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-rotate-right"></i> Đặt lại
                </a>
            @endif
        </form>
    </div>

    <!-- Staff Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nhân Sự</th>
                        <th>Email & Số Điện Thoại</th>
                        <th>Chức Vụ / Vai Trò</th>
                        <th>Phân Quyền Chi Tiết</th>
                        <th style="width: 130px; text-align: center;">Trạng Thái</th>
                        <th style="width: 110px;">Ngày Tạo</th>
                        <th style="text-align: right; width: 130px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $s)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--brand); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                                        {{ $s->initials }}
                                    </div>
                                    <div>
                                        <strong style="font-size: 13.5px; color: var(--on-surface);">{{ $s->name }}</strong>
                                        @if($s->id === auth()->id())
                                            <span class="badge badge-info" style="font-size: 10px; margin-left: 4px;">(Chính bạn)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: var(--on-surface);">{{ $s->email }}</div>
                                <small style="color: var(--on-surface-variant); font-size: 11px;">
                                    <i class="fa-solid fa-phone" style="font-size: 10px;"></i> {{ $s->phone ?: 'Chưa cập nhật SĐT' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge {{ $s->role_badge_class }}">
                                    {{ $s->role_name }}
                                </span>
                            </td>
                            <td>
                                @if($s->isAdmin())
                                    <span class="badge badge-success" style="font-size: 11px;">
                                        <i class="fa-solid fa-crown"></i> Toàn quyền hệ thống
                                    </span>
                                @else
                                    @php
                                        $pCount = is_array($s->permissions) ? count($s->permissions) : 0;
                                    @endphp
                                    @if($pCount > 0)
                                        <span class="badge badge-info" style="font-size: 11px;" title="{{ implode(', ', $s->permissions ?? []) }}">
                                            <i class="fa-solid fa-key"></i> {{ $pCount }} quyền được cấp
                                        </span>
                                    @else
                                        <span class="badge badge-neutral" style="font-size: 11px;">
                                            Chưa cấp quyền module
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($s->id !== auth()->id())
                                    <button type="button" data-toggle-url="{{ route('admin.staff.toggle', $s->id) }}" class="badge {{ $s->status === 'active' ? 'badge-success' : 'badge-danger' }}" style="border: none; cursor: pointer; padding: 4px 10px;" title="Nhấp để chuyển trạng thái">
                                        {{ $s->status === 'active' ? 'Đang hoạt động' : 'Đã khóa' }}
                                    </button>
                                @else
                                    <span class="badge badge-success" style="padding: 4px 10px;">
                                        Đang hoạt động
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: var(--on-surface-variant);">
                                {{ $s->created_at->format('d/m/Y') }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('admin.staff.edit', $s->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Chỉnh sửa tài khoản & phân quyền">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                    @if($s->id !== auth()->id())
                                        <form action="{{ route('admin.staff.destroy', $s->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản nhân sự \'{{ $s->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" title="Xóa tài khoản nhân viên">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                <i class="fa-solid fa-users-slash" style="font-size: 36px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                Không tìm thấy tài khoản nhân sự phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffMembers->hasPages())
            <div class="pagination-container">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
