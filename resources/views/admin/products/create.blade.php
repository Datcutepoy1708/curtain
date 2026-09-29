@extends('admin.layouts.app')

@section('title', 'Thêm Mẫu Rèm Cửa Mới')
@section('page-title', 'Tạo Mới Mẫu Rèm Cửa & Tải Lên Bộ Sưu Tập Ảnh')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách mẫu rèm
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            
            <!-- Left Column: Primary Curtain Information -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Basic Info Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-circle-info" style="color: var(--brand);"></i>
                            Thông Tin Cơ Bản Mẫu Rèm
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Tên Mẫu Rèm Cửa *</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: Rèm Vải Nhung Bỉ Luxury Velvet" required value="{{ old('name') }}">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Danh Mục Phân Loại *</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Mã SKU / Mã Sản Phẩm</label>
                                <input type="text" name="sku" class="form-control" placeholder="REM-BI-01 (Tự sinh nếu để trống)" value="{{ old('sku') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mô Tả Chi Tiết Sản Phẩm</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Mô tả về đặc tính sợi vải, khả năng cách âm chống nắng, độ bền và cảm hứng nội thất...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Curtain Technical Specifications Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-ruler-combined" style="color: #7c3aed;"></i>
                            Bảng Giá & Thông Số Kỹ Thuật May Đo
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Đơn Vị Tính Giá *</label>
                                <select name="price_unit" class="form-select" required>
                                    <option value="meter" {{ old('price_unit') === 'meter' ? 'selected' : '' }}>Theo mét ngang hoàn thiện (Vải sóng)</option>
                                    <option value="sqm" {{ old('price_unit') === 'sqm' ? 'selected' : '' }}>Theo mét vuông - m² (Rèm cuốn/cầu vồng)</option>
                                    <option value="piece" {{ old('price_unit') === 'piece' ? 'selected' : '' }}>Theo bộ / chiếc</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Đơn Giá Cơ Sở (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" placeholder="990000" required value="{{ old('price') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Giá Khuyến Mãi (VNĐ)</label>
                                <input type="number" name="sale_price" class="form-control" placeholder="850000" value="{{ old('sale_price') }}">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Độ Cản Sáng (% Chống Nắng)</label>
                                <input type="number" name="blackout_rate" class="form-control" placeholder="100" min="0" max="100" value="{{ old('blackout_rate', 100) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Diện Tích Tối Thiểu (m²)</label>
                                <input type="number" name="min_area" class="form-control" step="0.1" placeholder="1.0" value="{{ old('min_area', 1.0) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kiểu Lắp Đặt</label>
                                <select name="installation_type" class="form-select">
                                    <option value="indoor">Trong nhà (Indoor)</option>
                                    <option value="balcony">Ban công / Bán ngoài trời</option>
                                    <option value="skylight">Giếng trời / Trần kính</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Chất Liệu Sợi / Vải</label>
                                <input type="text" name="material" class="form-control" placeholder="Vải Nhung Bỉ & Voan Thêu / Gấm Nhật" value="{{ old('material') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Xuất Xứ / Nhập Khẩu</label>
                                <input type="text" name="origin" class="form-control" placeholder="Bỉ (Belgium) / Hàn Quốc / Nhật Bản" value="{{ old('origin') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multi-Image Gallery Upload Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-images" style="color: #16a34a;"></i>
                            Bộ Sưu Tập Hình Ảnh (Upload Được Nhiều Ảnh)
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Multi-file Dropzone -->
                        <div class="image-upload-dropzone">
                            <input type="file" name="image_files[]" multiple accept="image/*">
                            <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
                            <h4 class="dropzone-title">Nhấp vào đây hoặc kéo thả nhiều ảnh rèm cùng lúc</h4>
                            <p class="dropzone-desc">Hỗ trợ JPG, PNG, WEBP. Chọn nhiều ảnh để tạo bộ sưu tập chi tiết (ảnh phối cảnh, ảnh cận cảnh chất vải, ảnh phụ kiện).</p>
                        </div>

                        <!-- Dynamic Instant Preview Container for Newly Selected Files -->
                        <div class="dropzone-preview-grid" id="dropzonePreviewGrid" style="display: none;"></div>

                        <div style="margin-top: 16px;">
                            <label class="form-label">Hoặc nhập thêm các liên kết ảnh từ Internet (Mỗi link một dòng):</label>
                            <textarea name="additional_image_urls" class="form-control" rows="3" placeholder="https://images.unsplash.com/...&#10;https://images.unsplash.com/..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Primary Image -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Main Thumbnail Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-image" style="color: var(--brand);"></i>
                            Ảnh Đại Diện Chính
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Đường Dẫn Ảnh Đại Diện (URL)</label>
                            <input type="text" name="image" class="form-control" placeholder="https://images.unsplash.com/..." value="{{ old('image') }}">
                            <small style="color: var(--on-surface-variant); font-size: 12px; margin-top: 4px; display: block;">
                                (Nếu bạn upload file ở mục bộ sưu tập bên cạnh, ảnh đầu tiên sẽ tự động làm ảnh đại diện).
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Stock & Publishing Settings Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-sliders" style="color: #d97706;"></i>
                            Tồn Kho & Trạng Thái
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Số Lượng Tồn Kho Khả Dụng *</label>
                            <input type="number" name="stock" class="form-control" required value="{{ old('stock', 50) }}">
                            <small style="color: var(--on-surface-variant); font-size: 11px; margin-top: 4px; display: block;">
                                Mét vải hoặc số bộ phụ kiện khả dụng tại xưởng.
                            </small>
                        </div>

                        <div style="background: var(--surface-alt); padding: 14px; border-radius: var(--radius-md); border: 1px solid var(--border); display: flex; flex-direction: column; gap: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} style="width: 16px; height: 16px;">
                                Đặt làm sản phẩm Nổi Bật (Trang chủ)
                            </label>

                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" checked style="width: 16px; height: 16px;">
                                Kích hoạt hiển thị trên cửa hàng
                            </label>
                        </div>

                        <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 10px;">
                            <button type="submit" class="btn btn-primary" style="padding: 12px; justify-content: center; font-size: 14px;">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu & Xuất Bản Mẫu Rèm
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary" style="padding: 10px; justify-content: center;">
                                Hủy Bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
