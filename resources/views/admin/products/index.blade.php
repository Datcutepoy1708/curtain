@extends('admin.layouts.app')

@section('title', 'Quản Lý Mẫu Rèm Cửa')
@section('page-title', 'Danh Sách Mẫu Rèm Cửa & Phụ Kiện')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Catalogue Sản Phẩm Rèm Cửa</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Quản lý thông số may đo, đơn giá mét ngang/m², độ cản sáng và bộ sưu tập ảnh của từng mẫu rèm.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Thêm Mẫu Rèm Mới
        </a>
    </div>

    <!-- Advanced Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 16px 20px;">
        <form method="GET" action="{{ route('admin.products.index') }}" style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                <!-- Tìm kiếm từ khóa -->
                <div>
                    <label class="form-label">Từ khóa (Tên rèm, SKU...)</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên rèm, mã SKU..." value="{{ request('search') }}">
                </div>

                <!-- Lọc Danh mục -->
                <div>
                    <label class="form-label">Danh mục rèm</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Tất cả danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Lọc Đơn vị tính giá -->
                <div>
                    <label class="form-label">Đơn vị tính giá</label>
                    <select name="price_unit" class="form-select">
                        <option value="">-- Tất cả đơn vị --</option>
                        <option value="meter" {{ request('price_unit') === 'meter' ? 'selected' : '' }}>Theo mét ngang (Vải may sóng)</option>
                        <option value="sqm" {{ request('price_unit') === 'sqm' ? 'selected' : '' }}>Theo mét vuông (m²)</option>
                        <option value="piece" {{ request('price_unit') === 'piece' ? 'selected' : '' }}>Theo bộ / chiếc</option>
                    </select>
                </div>

                <!-- Lọc Tình trạng kho -->
                <div>
                    <label class="form-label">Tình trạng vải / kho</label>
                    <select name="stock_status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Còn nhiều (>10)</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Sắp hết (1-10)</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Tạm hết hàng</option>
                    </select>
                </div>

                <!-- Lọc Nổi bật -->
                <div>
                    <label class="form-label">Sản phẩm nổi bật</label>
                    <select name="is_featured" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Nổi bật (Trang chủ) ⭐</option>
                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Bình thường</option>
                    </select>
                </div>

                <!-- Lọc Trạng thái hiển thị -->
                <div>
                    <label class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Đang hiển thị</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Đang ẩn</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; align-items: center; border-top: 1px dashed var(--border); padding-top: 12px;">
                @if(request()->anyFilled(['search', 'category_id', 'price_unit', 'stock_status', 'is_featured', 'is_active']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-rotate-right"></i> Đặt lại bộ lọc
                    </a>
                @endif
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-filter"></i> Lọc Kết Quả
                </button>
            </div>
        </form>
    </div>

    <!-- ── Bulk Action Synchronized Floating Bar ── -->
    <div class="bulk-action-bar" data-bulk-url="{{ route('admin.products.bulk-action') }}">
        <div class="bulk-action-info">
            <i class="fa-solid fa-square-check" style="color: var(--brand);"></i>
            <span>Đã chọn: <strong class="bulk-count-badge">0</strong> mẫu rèm</span>
        </div>
        <div class="bulk-action-buttons">
            <button type="button" class="btn btn-outline btn-sm" data-bulk-action="activate" title="Kích hoạt hiển thị cho các mẫu rèm đã chọn">
                <i class="fa-solid fa-eye" style="color: #16a34a;"></i> Kích Hoạt
            </button>
            <button type="button" class="btn btn-outline btn-sm" data-bulk-action="deactivate" title="Ẩn các mẫu rèm đã chọn khỏi cửa hàng">
                <i class="fa-solid fa-eye-slash" style="color: #64748b;"></i> Ẩn
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-bulk-action="delete" title="Xóa các mẫu rèm đã chọn">
                <i class="fa-solid fa-trash"></i> Xóa Hàng Loạt
            </button>
        </div>
    </div>

    <!-- Products Data Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAll" style="width: 16px; height: 16px; cursor: pointer;">
                        </th>
                        <th style="width: 70px;">Hình Ảnh</th>
                        <th>Tên Mẫu Rèm & Mã SKU</th>
                        <th>Danh Mục</th>
                        <th>Đơn Giá / Quy Cách</th>
                        <th>Thông Số Rèm</th>
                        <th>Tồn Kho</th>
                        <th>Nổi Bật</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" value="{{ $product->id }}" class="item-checkbox" style="width: 16px; height: 16px; cursor: pointer;">
                            </td>
                            <td>
                                <div style="position: relative; width: 52px; height: 52px;">
                                    <img src="{{ $product->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=150&q=80' }}" 
                                         alt="{{ $product->name }}" class="product-thumb">
                                    @if($product->images->count() > 0)
                                        <span style="position: absolute; bottom: -4px; right: -4px; background: var(--brand); color: #fff; font-size: 10px; font-weight: 800; padding: 1px 5px; border-radius: 999px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" title="Có {{ $product->images->count() }} ảnh phụ trong bộ sưu tập">
                                            +{{ $product->images->count() }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td style="max-width: 260px;">
                                <a href="{{ route('admin.products.edit', $product) }}" style="font-weight: 700; color: var(--on-surface); text-decoration: none; font-size: 13.5px; display: block;">
                                    {{ $product->name }}
                                </a>
                                <small style="color: var(--on-surface-variant); font-size: 11px;">Mã SKU: {{ $product->sku }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                            </td>
                            <td>
                                <strong style="color: #d97706; font-size: 13.5px;">{{ $product->formatted_price }}</strong>
                                @if($product->sale_price)
                                    <div style="font-size: 11px; color: var(--on-surface-variant); text-decoration: line-through;">
                                        {{ $product->formatted_sale_price }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span class="badge badge-purple" style="font-size: 10px; width: fit-content;">
                                        Cản sáng: {{ $product->blackout_rate }}%
                                    </span>
                                    <small style="color: var(--on-surface-variant); font-size: 11px;">
                                        {{ $product->material ?: 'Vải gấm Bỉ' }} ({{ $product->origin ?: 'Nhập khẩu' }})
                                    </small>
                                </div>
                            </td>
                            <td>
                                @if($product->stock > 10)
                                    <span class="badge badge-success">{{ $product->stock }} {{ $product->price_unit === 'sqm' ? 'm²' : ($product->price_unit === 'meter' ? 'm' : 'bộ') }}</span>
                                @elseif($product->stock > 0)
                                    <span class="badge badge-warning">{{ $product->stock }} (Sắp hết)</span>
                                @else
                                    <span class="badge badge-danger">Hết hàng</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_featured)
                                    <span style="color: #f59e0b; font-size: 14px;" title="Sản phẩm nổi bật"><i class="fa-solid fa-star"></i></span>
                                @else
                                    <span style="color: var(--border-strong); font-size: 14px;"><i class="fa-regular fa-star"></i></span>
                                @endif
                            </td>
                            <td>
                                <button type="button" data-toggle-url="{{ route('admin.products.toggle', $product->id) }}" class="badge {{ $product->is_active ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 4px 10px;" title="Nhấp để chuyển trạng thái">
                                    {{ $product->is_active ? 'Hiển thị' : 'Ẩn' }}
                                </button>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary btn-sm" title="Chỉnh sửa thông tin & ảnh">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa mẫu rèm này cùng toàn bộ ảnh phụ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 5px 8px;" title="Xóa mẫu rèm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--on-surface-variant); padding: 40px;">
                                <i class="fa-solid fa-box-open" style="font-size: 32px; margin-bottom: 8px; display: block;"></i>
                                Không tìm thấy sản phẩm rèm cửa nào phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
