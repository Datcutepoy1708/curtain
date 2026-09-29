@extends('admin.layouts.app')

@section('title', 'Soạn Thảo Bài Viết Mới')
@section('page-title', 'Soạn Thảo Bài Viết & Cẩm Nang Rèm')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách bài
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-file-lines" style="color: var(--brand);"></i>
                Nội Dung Bài Viết Mới
            </div>
        </div>

        <form action="{{ route('admin.news.store') }}" method="POST" class="card-body" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Tiêu Đề Bài Viết *:</label>
                <input type="text" name="title" placeholder="Vd: 5 Sai lầm phổ biến khi chọn mua rèm phòng ngủ chống nắng" required
                       style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 15px; font-weight: 600;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chuyên Mục *:</label>
                    <select name="category" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="guide">Cẩm nang chọn rèm</option>
                        <option value="trends">Xu hướng thiết kế nội thất</option>
                        <option value="news">Tin tức khuyến mãi</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Trạng Thái Xuất Bản *:</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="published">Xuất bản ngay</option>
                        <option value="draft">Lưu bản nháp</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Đường Dẫn Ảnh Đại Diện (Thumbnail URL):</label>
                <input type="url" name="thumbnail_url" placeholder="https://images.unsplash.com/..."
                       style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Tóm Tắt Ngắn Gọn (Excerpt):</label>
                <textarea name="excerpt" rows="2" placeholder="Tóm tắt nội dung để hiển thị trên thẻ bài viết hoặc kết quả SEO..."
                          style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; line-height: 1.5;"></textarea>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Nội Dung Chi Tiết *:</label>
                <textarea name="content" rows="12" placeholder="Soạn nội dung chi tiết cẩm nang, mẹo chọn rèm, hướng dẫn đo đạc..." required
                          style="width: 100%; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; line-height: 1.6; font-family: inherit;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu & Đăng Bài
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
