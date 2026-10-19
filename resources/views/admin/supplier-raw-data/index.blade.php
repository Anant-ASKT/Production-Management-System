@extends('layouts.app')

@section('title', 'Supplier Raw Data')

@section('content')
<style>
    .raw-data-page {
        padding: 20px;
    }
    .raw-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .raw-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .raw-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .table-thumb {
        width: 54px;
        height: 54px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .table-thumb:hover {
        transform: scale(1.08);
    }
    .sub-image-pill {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
    }
    .detail-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .detail-val {
        font-size: 15px;
        color: #1e293b;
        font-weight: 600;
    }
    .gallery-sub-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .gallery-sub-img:hover {
        border-color: #2b5288;
        transform: scale(1.05);
    }
</style>

<div class="raw-data-page">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-inbox-fill text-primary"></i>
                <span>Supplier Raw Data</span>
            </h3>
            <p class="text-muted small mb-0">
                Review products submitted by suppliers and convert them into Design Specifications
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('design-specifications.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-palette2"></i>
                <span>Design Specifications</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="raw-stat-card d-flex align-items-center gap-3">
                <div class="raw-stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Raw Products</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($totalProducts) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="raw-stat-card d-flex align-items-center gap-3">
                <div class="raw-stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Pending Specification</div>
                    <div class="fs-4 fw-bold text-warning-emphasis">{{ number_format($pendingCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="raw-stat-card d-flex align-items-center gap-3">
                <div class="raw-stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Specification Done</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($doneCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="raw-stat-card d-flex align-items-center gap-3">
                <div class="raw-stat-icon bg-info-subtle text-info">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Active Suppliers</div>
                    <div class="fs-4 fw-bold text-dark">{{ count($suppliers) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.supplier-raw-data.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted mb-1">
                        <i class="bi bi-shop me-1"></i> Filter by Supplier
                    </label>
                    <select name="supplier_id" class="form-select select2-supplier" id="filterSupplier">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->sno }}" {{ request('supplier_id') == $s->sno ? 'selected' : '' }}>
                                {{ $s->name }} @if(!empty($s->nickname)) ({{ $s->nickname }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted mb-1">
                        <i class="bi bi-filter-circle me-1"></i> Status
                    </label>
                    <select name="status" class="form-select" id="filterStatus">
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending Specification</option>
                        <option value="done" {{ $statusFilter === 'done' ? 'selected' : '' }}>Specification Completed</option>
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Product name, description, SKU...">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted mb-1">
                        <i class="bi bi-calendar3 me-1"></i> From Date
                    </label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-funnel-fill"></i>
                            <span>Filter</span>
                        </button>
                        <a href="{{ route('admin.supplier-raw-data.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Data Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th style="width: 70px;">Image</th>
                        <th>Product Details</th>
                        <th>Supplier</th>
                        <th>Stock</th>
                        <th>Pricing</th>
                        <th>Description</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="text-center fw-bold text-muted small">
                                #{{ $product->sno }}
                            </td>
                            <td>
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="table-thumb js-preview-img" data-full="{{ $product->image_url }}" data-title="{{ $product->name }}" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-muted small\'>No Image</span>';">
                                @else
                                    <span class="badge bg-light text-secondary border">No Image</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $product->name ?: '-' }}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    @if($product->product_sku)
                                        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">
                                            SKU: {{ $product->product_sku }}
                                        </span>
                                    @endif
                                    @if($product->is_integrated)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">Ready List</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 11px;">Manual</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $product->supplier_name ?: 'Unknown' }}</div>
                                @if(!empty($product->supplier_nickname))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">
                                        {{ $product->supplier_nickname }}
                                    </span>
                                @endif
                                @if(!empty($product->supplier_user_name))
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-person me-1"></i>{{ $product->supplier_user_name }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold fs-6">
                                    {{ $product->stock ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($product->price))
                                    <div class="fw-bold text-dark">₹{{ number_format($product->price, 2) }}</div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                                @if(!empty($product->min_price))
                                    <div class="text-muted small">Min: ₹{{ number_format($product->min_price, 2) }}</div>
                                @endif
                            </td>
                            <td style="max-width: 220px;">
                                <div class="text-truncate text-muted small" title="{{ $product->description }}">
                                    {{ $product->description ?: '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $product->created_at ? \Carbon\Carbon::parse($product->created_at)->format('d-M-Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($product->status_done === 'Done')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check2-circle me-1"></i> Specified
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-clock me-1"></i> Pending Spec
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    {{-- View Full Details Page --}}
                                    <a href="{{ route('admin.supplier-raw-data.show', $product->sno) }}" 
                                       class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 shadow-sm"
                                       title="View Full Details on Dedicated Page">
                                        <i class="bi bi-eye"></i>
                                        <span>View</span>
                                    </a>

                                    {{-- Make Specification Button --}}
                                    <a href="{{ route('design-specifications.index', ['supplier_product_id' => $product->sno]) }}" 
                                       class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm"
                                       title="Open Design Specification for this product">
                                        <i class="bi bi-palette2"></i>
                                        <span>Specification</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                No supplier raw products found matching the filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing <b>{{ $products->firstItem() ?? 0 }}</b> to <b>{{ $products->lastItem() ?? 0 }}</b> of <b>{{ $products->total() }}</b> products
            </div>
            <div>
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     MODAL: IMAGE ZOOM PREVIEW
========================================================= --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-2 px-3 bg-light border-bottom">
                <h6 class="modal-title fw-bold text-dark mb-0" id="imagePreviewModalTitle">Image Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 d-flex align-items-center justify-content-center" style="background-color: #0b1120; min-height: 350px; max-height: 80vh; overflow: auto;">
                <img id="imagePreviewModalImg" src="" alt="Full Preview" class="img-fluid rounded shadow" style="max-height: 75vh; max-width: 100%; object-fit: contain;">
            </div>
            <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize select2 on supplier filter if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-supplier').select2({
            placeholder: 'All Suppliers',
            allowClear: true,
            width: '100%'
        });
    }

    // Image Zoom Modal
    const imagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
    const imagePreviewModalImg = document.getElementById('imagePreviewModalImg');
    const imagePreviewModalTitle = document.getElementById('imagePreviewModalTitle');

    document.addEventListener('click', function(e) {
        const target = e.target.closest('.js-preview-img');
        if (target) {
            const src = target.getAttribute('data-full') || target.getAttribute('src');
            const title = target.getAttribute('data-title') || 'Product Photo';
            if (src) {
                imagePreviewModalImg.src = src;
                imagePreviewModalTitle.textContent = title;
                imagePreviewModal.show();
            }
        }
});
</script>
@endpush
@endsection
