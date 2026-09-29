@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Banner & Quảng Cáo')
@section('page-title', 'Chỉnh Sửa Banner Quảng Cáo #' . $banner->id)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Chỉnh Sửa Banner Quảng Cáo</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Cập nhật hình ảnh hiển thị, tiêu đề chiến dịch và liên kết chuyển hướng.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card">
        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: flex; flex-direction: column; gap: 20px; padding: 10px 0;">

                <!-- Current Image Preview -->
                <div>
                    <label class="form-label">Hình ảnh banner hiện tại:</label>
                    <div style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; background: var(--surface-alt); padding: 8px; display: inline-block;">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="max-width: 100%; height: 180px; object-fit: cover; border-radius: var(--radius-sm); display: block;">
                    </div>
                </div>

                <!-- Replace Image Section -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Tải tệp ảnh thay thế mới:</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                        <small style="color: var(--on-surface-variant); font-size: 11px; margin-top: 4px; display: block;">
                            Hỗ trợ JPG, PNG, WEBP tối đa 5MB. Để trống nếu giữ nguyên ảnh hiện tại.
                        </small>
                    </div>
                    <div>
                        <label class="form-label">Hoặc nhập đường dẫn ảnh (URL):</label>
                        <input type="text" name="image_url" class="form-control" value="{{ old('image_url', $banner->image_url) }}" placeholder="https://...">
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border); margin: 6px 0;"></div>

                <div>
                    <label class="form-label">Tiêu đề Banner *:</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}" placeholder="Vd: Bộ Sưu Tập Rèm Cửa Mùa Hè 2026" required>
                </div>

                <div>
                    <label class="form-label">Phụ đề / Slogan chiến dịch:</label>
                    <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="Vd: Giảm 20% phụ kiện động cơ Aqara & Miễn phí đo đạc">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Vị trí hiển thị *:</label>
                        <select name="position" class="form-select">
                            <option value="hero" {{ old('position', $banner->position) === 'hero' ? 'selected' : '' }}>Hero Slider (Đầu trang chủ)</option>
                            <option value="promo" {{ old('position', $banner->position) === 'promo' ? 'selected' : '' }}>Promo Banner (Giữa trang)</option>
                            <option value="popup" {{ old('position', $banner->position) === 'popup' ? 'selected' : '' }}>Popup Quảng Cáo</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Thứ tự sắp xếp:</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Đường dẫn khi click (Link URL):</label>
                        <input type="text" name="link_url" class="form-control" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://... hoặc /products/...">
                    </div>

                    <div>
                        <label class="form-label">Trạng thái hiển thị *:</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $banner->status) === 'active' ? 'selected' : '' }}>Hiển thị ngay</option>
                            <option value="inactive" {{ old('status', $banner->status) === 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 18px; margin-top: 10px;">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Cập Nhật Banner
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
