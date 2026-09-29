@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Mẫu Rèm Cửa')
@section('page-title', 'Chỉnh Sửa Mẫu Rèm & Quản Lý Bộ Sưu Tập Ảnh')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách mẫu rèm
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Danh Mục Phân Loại *</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Mã SKU / Mã Sản Phẩm</label>
                                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mô Tả Chi Tiết Sản Phẩm</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
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
                                    <option value="meter" {{ old('price_unit', $product->price_unit) === 'meter' ? 'selected' : '' }}>Theo mét ngang hoàn thiện (Vải sóng)</option>
                                    <option value="sqm" {{ old('price_unit', $product->price_unit) === 'sqm' ? 'selected' : '' }}>Theo mét vuông - m² (Rèm cuốn/cầu vồng)</option>
                                    <option value="piece" {{ old('price_unit', $product->price_unit) === 'piece' ? 'selected' : '' }}>Theo bộ / chiếc</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Đơn Giá Cơ Sở (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" required value="{{ old('price', $product->price) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Giá Khuyến Mãi (VNĐ)</label>
                                <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Độ Cản Sáng (% Chống Nắng)</label>
                                <input type="number" name="blackout_rate" class="form-control" min="0" max="100" value="{{ old('blackout_rate', $product->blackout_rate ?: 100) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Diện Tích Tối Thiểu (m²)</label>
                                <input type="number" name="min_area" class="form-control" step="0.1" value="{{ old('min_area', $product->min_area ?: 1.0) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kiểu Lắp Đặt</label>
                                <select name="installation_type" class="form-select">
                                    <option value="indoor" {{ old('installation_type', $product->installation_type) === 'indoor' ? 'selected' : '' }}>Trong nhà (Indoor)</option>
                                    <option value="balcony" {{ old('installation_type', $product->installation_type) === 'balcony' ? 'selected' : '' }}>Ban công / Bán ngoài trời</option>
                                    <option value="skylight" {{ old('installation_type', $product->installation_type) === 'skylight' ? 'selected' : '' }}>Giếng trời / Trần kính</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label">Chất Liệu Sợi / Vải</label>
                                <input type="text" name="material" class="form-control" value="{{ old('material', $product->material) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Xuất Xứ / Nhập Khẩu</label>
                                <input type="text" name="origin" class="form-control" value="{{ old('origin', $product->origin) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Existing Gallery Images Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-photo-film" style="color: #0284c7;"></i>
                            Bộ Sưu Tập Hình Ảnh Hiện Có ({{ $product->images->count() }} ảnh phụ)
                        </div>
                    </div>
                    <div class="card-body">
                        @if($product->images->count() > 0)
                            <div class="product-gallery-grid">
                                @foreach($product->images as $img)
                                    <div class="gallery-item">
                                        <img src="{{ $img->image_url }}" alt="Gallery photo">
                                        @if($img->is_primary || $img->image_url === $product->image)
                                            <span class="primary-tag">Chính</span>
                                        @endif
                                        <button type="button" class="btn-remove-img" data-delete-url="{{ route('admin.products.images.destroy', $img->id) }}" title="Xóa ảnh này khỏi bộ sưu tập">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="padding: 20px; text-align: center; color: var(--on-surface-variant); background: var(--surface-alt); border-radius: var(--radius-md); border: 1px dashed var(--border);">
                                <i class="fa-regular fa-images" style="font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.6;"></i>
                                <p style="margin: 0; font-size: 13px;">Chưa có ảnh phụ trong bộ sưu tập. Hãy chọn nhiều file bên dưới để tải lên thêm ảnh.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upload More Images Card -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #16a34a;"></i>
                            Tải Lên Thêm Ảnh Mới (Upload Được Nhiều Ảnh)
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Multi-file Dropzone -->
                        <div class="image-upload-dropzone">
                            <input type="file" name="image_files[]" multiple accept="image/*">
                            <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
                            <h4 class="dropzone-title">Nhấp vào đây hoặc kéo thả nhiều ảnh rèm cùng lúc</h4>
                            <p class="dropzone-desc">Hỗ trợ JPG, PNG, WEBP. Giữ phím Ctrl / Shift để chọn nhiều ảnh cùng lúc.</p>
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
                            <input type="text" name="image" class="form-control" value="{{ old('image', $product->image) }}">
                        </div>

                        @if($product->image)
                            <div style="margin-top: 12px; text-align: center;">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: var(--radius-md); border: 1.5px solid var(--border); box-shadow: var(--shadow-sm);">
                                <small style="display: block; margin-top: 6px; font-size: 11px; color: var(--on-surface-variant);">Hình ảnh đại diện hiện tại trên trang bán lẻ</small>
                            </div>
                        @endif
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
                            <input type="number" name="stock" class="form-control" required value="{{ old('stock', $product->stock) }}">
                            <small style="color: var(--on-surface-variant); font-size: 11px; margin-top: 4px; display: block;">
                                Mét vải hoặc số bộ phụ kiện khả dụng tại xưởng.
                            </small>
                        </div>

                        <div style="background: var(--surface-alt); padding: 14px; border-radius: var(--radius-md); border: 1px solid var(--border); display: flex; flex-direction: column; gap: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} style="width: 16px; height: 16px;">
                                Đặt làm sản phẩm Nổi Bật (Trang chủ)
                            </label>

                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width: 16px; height: 16px;">
                                Kích hoạt hiển thị trên cửa hàng
                            </label>
                        </div>

                        <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 10px;">
                            <button type="submit" class="btn btn-primary" style="padding: 12px; justify-content: center; font-size: 14px;">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu & Cập Nhật Mẫu Rèm
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
