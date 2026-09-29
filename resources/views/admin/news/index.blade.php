@extends('admin.layouts.app')

@section('title', 'Tin Tức & Cẩm Nang Rèm')
@section('page-title', 'Cẩm Nang & Bài Viết Tư Vấn Rèm Cửa')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Danh Sách Bài Viết & Cẩm Nang</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Quản lý các bài viết hướng dẫn chọn rèm, mẹo vệ sinh và xu hướng thiết kế nội thất.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-pen-nib"></i> Viết Bài Mới
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 14px 20px;">
        <form action="{{ route('admin.news.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tiêu đề bài viết..."
                   style="min-width: 240px; flex: 1; padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">

            <select name="status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            @if(request()->anyFilled(['keyword', 'status']))
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-rotate-right"></i> Đặt lại
                </a>
            @endif
        </form>
    </div>

    <!-- News Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Hình Ảnh</th>
                        <th>Tiêu Đề Bài Viết</th>
                        <th>Chuyên Mục</th>
                        <th>Tác Giả</th>
                        <th style="width: 100px; text-align: center;">Lượt Xem</th>
                        <th style="width: 110px;">Ngày Đăng</th>
                        <th style="width: 110px; text-align: center;">Trạng Thái</th>
                        <th style="text-align: right; width: 120px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->thumbnail_url ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=150&q=80' }}" 
                                     alt="{{ $item->title }}"
                                     style="width: 70px; height: 45px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                            </td>
                            <td style="max-width: 320px;">
                                <strong style="font-size: 13px; color: var(--on-surface); display: block;">{{ $item->title }}</strong>
                                <small style="color: var(--on-surface-variant); font-size: 11px;">Slug: {{ $item->slug }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $item->category === 'guide' ? 'Cẩm nang chọn rèm' : ($item->category === 'trends' ? 'Xu hướng nội thất' : 'Tin tức') }}
                                </span>
                            </td>
                            <td style="font-size: 13px;">
                                {{ $item->author->name ?? 'Ban Biên Tập' }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-neutral"><i class="fa-solid fa-eye"></i> {{ number_format($item->views) }}</span>
                            </td>
                            <td style="font-size: 12px; color: var(--on-surface-variant);">
                                {{ $item->created_at->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center;">
                                <button type="button" data-toggle-url="{{ route('admin.news.toggle', $item->id) }}" class="badge {{ $item->status === 'published' ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 10px;" title="Nhấp để chuyển trạng thái">
                                    {{ $item->status === 'published' ? 'Đã xuất bản' : 'Bản nháp' }}
                                </button>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Sửa bài viết">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" title="Xóa bài viết">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                <i class="fa-regular fa-newspaper" style="font-size: 36px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                Chưa có bài viết nào. Nhấp vào "Viết Bài Mới" để đăng bài.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($news->hasPages())
            <div class="pagination-container">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
