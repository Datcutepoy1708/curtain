@extends('admin.layouts.app')

@section('title', 'Thêm Danh Mục Rèm Mới')
@section('page-title', 'Tạo Danh Mục Loại Rèm Mới')

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
                <i class="fa-solid fa-layer-group" style="color: var(--brand);"></i>
                Thông Tin Danh Mục Rèm Cửa
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Tên Danh Mục Rèm *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Rèm Vải 2 Lớp, Rèm Cầu Vồng Hàn Quốc, Rèm Gỗ Tự Nhiên..." required value="{{ old('name') }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Tải Lên File Ảnh Đại Diện</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                        <small style="color: var(--on-surface-variant); font-size: 11px; margin-top: 4px; display: block;">Hỗ trợ JPG, PNG, WEBP (Tối đa 4MB)</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hoặc Nhập Đường Dẫn Ảnh (URL)</label>
                        <input type="text" name="image" class="form-control" placeholder="https://images.unsplash.com/..." value="{{ old('image') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mô Tả Chi Tiết Danh Mục</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Mô tả về phong cách kiến trúc phù hợp, công năng cản sáng, chống nóng và tính thẩm mỹ của dòng rèm này...">{{ old('description') }}</textarea>
                </div>

                <div style="background: var(--surface-alt); padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked style="width: 17px; height: 17px;">
                        Kích hoạt hiển thị danh mục trên thanh điều hướng & bộ lọc
                    </label>
                </div>

                <div style="display: flex; gap: 12px; align-items: center;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Danh Mục Mới
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
