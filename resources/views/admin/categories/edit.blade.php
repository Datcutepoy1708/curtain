@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Danh Mục Rèm')
@section('page-title', 'Chỉnh Sửa Danh Mục: ' . $category->name)

@section('content')
<div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
    <div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách danh mục
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-pen-to-square" style="color: var(--brand);"></i>
                Cập Nhật Thông Tin Danh Mục Rèm Cửa
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Tên Danh Mục Rèm *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $category->name) }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Tải Lên File Ảnh Mới</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                        <small style="color: var(--on-surface-variant); font-size: 11px; margin-top: 4px; display: block;">Chọn file mới nếu muốn thay thế ảnh hiện tại</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Đường Dẫn Ảnh (URL)</label>
                        <input type="text" name="image" class="form-control" value="{{ old('image', $category->image) }}">
                    </div>
                </div>

                @if($category->image)
                    <div class="form-group" style="margin-top: -6px;">
                        <label class="form-label">Ảnh Minh Họa Hiện Tại</label>
                        <div style="display: flex; align-items: center; gap: 16px; background: var(--surface-alt); padding: 12px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                            <img src="{{ $category->image }}" alt="{{ $category->name }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
                            <div style="font-size: 12px; color: var(--on-surface-variant);">
                                <div><strong>Nguồn ảnh:</strong> {{ Str::limit($category->image, 50) }}</div>
                                <div>Ảnh này được dùng làm ảnh đại diện cho phân loại rèm trên website.</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Mô Tả Chi Tiết Danh Mục</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>

                <div style="background: var(--surface-alt); padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} style="width: 17px; height: 17px;">
                        Kích hoạt hiển thị danh mục trên thanh điều hướng & bộ lọc
                    </label>
                </div>

                <div style="display: flex; gap: 12px; align-items: center;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Hủy Bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
