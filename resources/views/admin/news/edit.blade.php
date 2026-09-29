@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Bài Viết Cẩm Nang')
@section('page-title', 'Chỉnh Sửa Bài Viết Cẩm Nang Rèm #' . $news->id)

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Chỉnh Sửa Bài Viết Cẩm Nang</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Cập nhật kiến thức tư vấn rèm cửa, hướng dẫn đo đạc và mẹo bài trí nội thất.</p>
        </div>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card">
        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data" class="card-body" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">Tiêu Đề Bài Viết *:</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" placeholder="Vd: 5 Sai lầm phổ biến khi chọn mua rèm phòng ngủ..." required style="font-size: 15px; font-weight: 600;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label class="form-label">Chuyên Mục *:</label>
                    <select name="category" class="form-select">
                        <option value="guide" {{ old('category', $news->category) === 'guide' ? 'selected' : '' }}>Cẩm nang chọn rèm</option>
                        <option value="trends" {{ old('category', $news->category) === 'trends' ? 'selected' : '' }}>Xu hướng thiết kế nội thất</option>
                        <option value="news" {{ old('category', $news->category) === 'news' ? 'selected' : '' }}>Tin tức khuyến mãi</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Trạng Thái Xuất Bản *:</label>
                    <select name="status" class="form-select">
                        <option value="published" {{ old('status', $news->status) === 'published' ? 'selected' : '' }}>Xuất bản ngay</option>
                        <option value="draft" {{ old('status', $news->status) === 'draft' ? 'selected' : '' }}>Lưu bản nháp</option>
                    </select>
                </div>
            </div>

            <!-- Thumbnail Preview & Replacement -->
            <div>
                <label class="form-label">Hình ảnh đại diện hiện tại:</label>
                @if($news->thumbnail_url)
                    <div style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 6px; display: inline-block; background: var(--surface-alt); margin-bottom: 10px;">
                        <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" style="height: 120px; border-radius: 4px; object-fit: cover; display: block;">
                    </div>
                @else
                    <p style="color: var(--on-surface-variant); font-size: 13px;">Chưa có ảnh đại diện.</p>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label class="form-label">Tải tệp ảnh mới thay thế:</label>
                        <input type="file" name="thumbnail_file" class="form-control" accept="image/*">
                        <small style="color: var(--on-surface-variant); font-size: 11px;">Hỗ trợ JPG, PNG, WEBP tối đa 5MB.</small>
                    </div>
                    <div>
                        <label class="form-label">Hoặc nhập link ảnh (URL):</label>
                        <input type="text" name="thumbnail_url" class="form-control" value="{{ old('thumbnail_url', $news->thumbnail_url) }}" placeholder="https://images.unsplash.com/...">
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label">Tóm Tắt Ngắn Gọn (Excerpt):</label>
                <textarea name="excerpt" rows="2" class="form-control" placeholder="Tóm tắt ngắn để hiển thị trên danh sách bài viết...">{{ old('excerpt', $news->excerpt) }}</textarea>
            </div>

            <div>
                <label class="form-label">Nội Dung Chi Tiết *:</label>
                <textarea name="content" rows="14" class="form-control" placeholder="Soạn nội dung chi tiết bài viết..." required style="line-height: 1.6;">{{ old('content', $news->content) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 8px;">
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> Cập Nhật Bài Viết
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
