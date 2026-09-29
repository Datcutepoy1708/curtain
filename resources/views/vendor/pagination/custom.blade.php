@if ($paginator->hasPages())
    <nav class="custom-pagination-nav" role="navigation" aria-label="Điều hướng phân trang">
        <div class="pagination-summary-text">
            Hiển thị <strong>{{ $paginator->firstItem() }}</strong> - <strong>{{ $paginator->lastItem() }}</strong> trong tổng số <strong>{{ $paginator->total() }}</strong> mục
        </div>

        <ul class="custom-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="page-btn disabled" aria-disabled="true">
                        <i class="fa-solid fa-angle-left"></i> Trước
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn">
                        <i class="fa-solid fa-angle-left"></i> Trước
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><span class="page-btn disabled">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="page-btn active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="page-btn">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn">
                        Sau <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
            @else
                <li>
                    <span class="page-btn disabled" aria-disabled="true">
                        Sau <i class="fa-solid fa-angle-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
