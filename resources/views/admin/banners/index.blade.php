@extends('admin.layouts.app')

@section('title', 'Banner & Quảng Cáo')
@section('page-title', 'Quản Lý Banner & Slider Trang Chủ')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Quản Lý Banner & Slider Khuyến Mãi</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Cấu hình các hình ảnh trình chiếu tại trang chủ và chiến dịch marketing theo mùa.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-modal-target="#newBannerModal">
            <i class="fa-solid fa-plus"></i> Thêm Banner Mới
        </button>
    </div>

    <!-- Banners Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">Hình Ảnh</th>
                        <th>Tiêu Đề & Phụ Đề</th>
                        <th>Vị Trí Hiển Thị</th>
                        <th>Đường Dẫn Chuyển Hướng</th>
                        <th style="width: 80px; text-align: center;">Thứ Tự</th>
                        <th style="width: 110px; text-align: center;">Trạng Thái</th>
                        <th style="text-align: right; width: 120px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $b)
                        <tr>
                            <td>
                                <img src="{{ $b->image_url }}" alt="{{ $b->title }}" 
                                     style="width: 120px; height: 55px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                            </td>
                            <td>
                                <strong style="font-size: 13px; color: var(--on-surface);">{{ $b->title }}</strong>
                                @if($b->subtitle)
                                    <div style="font-size: 11px; color: var(--on-surface-variant); margin-top: 2px;">{{ $b->subtitle }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $b->position === 'hero' ? 'Slider Trang Chủ' : ($b->position === 'promo' ? 'Khuyến Mãi Giữa Trang' : 'Popup') }}
                                </span>
                            </td>
                            <td style="font-size: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $b->link_url ?: 'Không chuyển hướng' }}
                            </td>
                            <td style="text-align: center; font-weight: 600; color: var(--on-surface-variant);">
                                {{ $b->sort_order }}
                            </td>
                            <td style="text-align: center;">
                                <button type="button" data-toggle-url="{{ route('admin.banners.toggle', $b->id) }}" class="badge {{ $b->status === 'active' ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 10px;" title="Nhấp để bật/tắt">
                                    {{ $b->status === 'active' ? 'Hiển thị' : 'Đang ẩn' }}
                                </button>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('admin.banners.edit', $b->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Chỉnh sửa banner">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa banner này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" title="Xóa banner">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                <i class="fa-regular fa-images" style="font-size: 36px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                Chưa có banner quảng cáo nào. Nhấp vào "Thêm Banner Mới" để tải lên.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="pagination-container">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: New Banner -->
<div class="admin-modal-backdrop" id="newBannerModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Thêm Banner Quảng Cáo Mới</h3>
            <button type="button" class="admin-modal-close" data-modal-close>&times;</button>
        </div>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="admin-modal-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label class="form-label">Tiêu Đề Banner *:</label>
                    <input type="text" name="title" class="form-control" placeholder="Vd: Bộ Sưu Tập Rèm Cửa Mùa Hè 2026" required>
                </div>

                <div>
                    <label class="form-label">Phụ Đề / Slogan:</label>
                    <input type="text" name="subtitle" class="form-control" placeholder="Vd: Giảm 20% phụ kiện động cơ Aqara & Miễn phí đo đạc tận nhà">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">Tải tệp ảnh lên:</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                    <div>
                        <label class="form-label">Hoặc nhập link ảnh (URL):</label>
                        <input type="text" name="image_url" class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>
                </div>

                <div>
                    <label class="form-label">Đường Dẫn Khi Click (Link URL):</label>
                    <input type="text" name="link_url" class="form-control" placeholder="https://... hoặc /products">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Vị Trí Hiển Thị:</label>
                        <select name="position" class="form-select">
                            <option value="hero">Hero Slider (Đầu trang chủ)</option>
                            <option value="promo">Promo Banner (Khuyến mãi)</option>
                            <option value="popup">Popup Quảng Cáo</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Thứ Tự Sắp Xếp:</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>

                <div>
                    <label class="form-label">Trạng Thái:</label>
                    <select name="status" class="form-select">
                        <option value="active">Hiển thị ngay</option>
                        <option value="inactive">Tạm ẩn</option>
                    </select>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu Banner</button>
            </div>
        </form>
    </div>
</div>
@endsection
