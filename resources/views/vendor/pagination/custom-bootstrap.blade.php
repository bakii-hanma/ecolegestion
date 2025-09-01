@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center">
        <ul class="pagination pagination-custom shadow-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link page-link-custom">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-sm-inline ms-1">Précédent</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link page-link-custom" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-sm-inline ms-1">Précédent</span>
                    </a>
                </li>
            @endif

            {{-- First Page Link --}}
            @if ($paginator->currentPage() > 3)
                <li class="page-item">
                    <a class="page-link page-link-custom" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if ($paginator->currentPage() > 4)
                    <li class="page-item disabled">
                        <span class="page-link page-link-custom">...</span>
                    </li>
                @endif
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link page-link-custom">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link page-link-custom page-link-active">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link page-link-custom" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Last Page Link --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="page-item disabled">
                        <span class="page-link page-link-custom">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link page-link-custom" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link page-link-custom" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <span class="d-none d-sm-inline me-1">Suivant</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link page-link-custom">
                        <span class="d-none d-sm-inline me-1">Suivant</span>
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    {{-- Page Info --}}
    <div class="text-center mt-3">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }} 
            ({{ $paginator->total() }} résultats au total)
        </small>
    </div>

    <style>
    .pagination-custom {
        --bs-pagination-padding-x: 0.75rem;
        --bs-pagination-padding-y: 0.5rem;
        --bs-pagination-border-radius: 12px;
        gap: 0.25rem;
    }
    
    .page-link-custom {
        border: none !important;
        border-radius: 12px !important;
        color: #2563eb !important;
        background: #f8f9fa !important;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 0 2px;
        min-width: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .page-link-custom:hover {
        background: #2563eb !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    .page-link-active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }
    
    .page-item.disabled .page-link-custom {
        background: #e9ecef !important;
        color: #6c757d !important;
    }
    
    .page-item.disabled .page-link-custom:hover {
        transform: none;
        box-shadow: none;
    }
    </style>
@endif
