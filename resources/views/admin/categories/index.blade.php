@extends('admin.layouts.app')

@section('title', 'Danh Mục Loại Rèm')
@section('page-title', 'Danh Mục Loại Rèm Cửa & Phân Nhóm')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div class="card-title">Quản Lý Danh Mục Phân Loại Rèm Cửa</div>
            <span style="font-size: 13px; color: var(--on-surface-variant);">
                Tổng số <strong>{{ $categories->total() }}</strong> danh mục rèm đang vận hành
            </span>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm Danh Mục Mới
        </a>
    </div>

    <!-- ── Bulk Action Synchronized Floating Bar ── -->
    <div class="bulk-action-bar" data-bulk-url="{{ route('admin.categories.bulk-action') }}">
        <div class="bulk-action-info">
            <i class="fa-solid fa-square-check" style="color: var(--brand);"></i>
            <span>Đã chọn: <strong class="bulk-count-badge">0</strong> danh mục rèm</span>
        </div>
        <div class="bulk-action-buttons">
            <button type="button" class="btn btn-outline btn-sm" data-bulk-action="activate" title="Kích hoạt hiển thị cho các danh mục đã chọn">
                <i class="fa-solid fa-eye" style="color: #16a34a;"></i> Kích Hoạt
            </button>
            <button type="button" class="btn btn-outline btn-sm" data-bulk-action="deactivate" title="Ẩn các danh mục đã chọn">
                <i class="fa-solid fa-eye-slash" style="color: #64748b;"></i> Ẩn
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-bulk-action="delete" title="Xóa các danh mục đã chọn">
                <i class="fa-solid fa-trash"></i> Xóa Hàng Loạt
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="selectAll" style="width: 16px; height: 16px; cursor: pointer;">
                    </th>
                    <th style="width: 50px;">STT</th>
                    <th style="width: 70px;">Hình Ảnh</th>
                    <th>Tên Danh Mục Rèm</th>
                    <th>Mô Tả Danh Mục</th>
                    <th style="width: 140px; text-align: center;">Số Mẫu Rèm</th>
                    <th style="width: 110px; text-align: center;">Trạng Thái</th>
                    <th style="width: 130px; text-align: right;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $key => $category)
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" class="item-checkbox" value="{{ $category->id }}" style="width: 16px; height: 16px; cursor: pointer;">
                        </td>
                        <td style="color: var(--on-surface-variant); font-weight: 600;">
                            {{ $categories->firstItem() + $key }}
                        </td>
                        <td>
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="product-thumb">
                            @else
                                <div class="product-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--on-surface-variant); font-size: 18px;">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="font-size: 14px; color: var(--on-surface);">{{ $category->name }}</strong>
                            <div style="font-size: 12px; color: var(--on-surface-variant); margin-top: 2px;">
                                Slug: <code style="background: var(--surface-alt); padding: 1px 5px; border-radius: 4px;">{{ $category->slug }}</code>
                            </div>
                        </td>
                        <td style="color: var(--on-surface-variant); max-width: 260px;">
                            {{ Str::limit($category->description, 80) ?: 'Chưa có mô tả chi tiết' }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info" style="font-size: 12px; padding: 4px 10px;">
                                <i class="fa-solid fa-shapes"></i> {{ $category->products_count }} mẫu
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" data-toggle-url="{{ route('admin.categories.toggle', $category->id) }}" class="badge {{ $category->is_active ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 10px;" title="Nhấp để chuyển trạng thái">
                                {{ $category->is_active ? 'Hiển thị' : 'Đang ẩn' }}
                            </button>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-secondary btn-sm" title="Chỉnh sửa danh mục">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');" style="margin: 0; display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 5px 8px;" title="Xóa danh mục">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px 20px; color: var(--on-surface-variant);">
                            <i class="fa-regular fa-folder-open" style="font-size: 36px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            Chưa có danh mục rèm cửa nào được tạo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="pagination-container">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
