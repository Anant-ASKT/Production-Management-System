@extends('layouts.supplier')

@section('title', 'Review Selection')
@section('page-title', 'Review Selection')

@section('content')
<style>
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 18px;
    }
    .nav-tabs .nav-link.active {
        color: #2b5288;
        border-color: #2b5288;
        background: transparent;
        font-weight: 600;
    }
    .bulk-action-bar {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 16px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="m-0 fw-bold text-dark">Review Selection</h4>
        <p class="text-muted small mb-0 mt-1">Review your selected products, send to the Photo Section, or delete</p>
    </div>
    <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Product
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Status Tabs commented as requested
<ul class="nav nav-tabs mb-3 border-bottom">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('supplier.products.index') }}">
            <i class="bi bi-check2-square me-1"></i> Review Selection
            <span class="badge bg-primary ms-1 rounded-pill">{{ $pendingCount }}</span>
        </a>
    </li>
</ul>
--}}

<div class="card shadow-sm border-0">
    <div class="card-body">
        {{-- Bulk Actions Toolbar --}}
        <form id="bulkActionForm" action="{{ route('supplier.products.send-to-photo-section') }}" method="POST">
            @csrf
            <div class="bulk-action-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check m-0">
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox" style="cursor: pointer;">
                        <label class="form-check-label fw-semibold small text-dark" for="selectAllCheckbox" style="cursor: pointer;">
                            Select All on this page
                        </label>
                    </div>
                    <span id="selectedCountBadge" class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                        0 selected
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnBulkDelete" class="btn btn-sm btn-outline-danger" disabled>
                        <i class="bi bi-trash me-1"></i> Delete Selected (<span id="btnDeleteCount">0</span>)
                    </button>
                    <button type="submit" id="btnBulkSend" class="btn btn-sm btn-primary" disabled>
                        <i class="bi bi-camera me-1"></i> Send to Photo Section (<span id="btnBulkCount">0</span>)
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
                            <th style="width: 70px;">Image</th>
                            <th>Product Details</th>
                            <th>Item Type</th>
                            <th>Source</th>
                            <th>Stock</th>
                            <th>Added Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->sno }}" class="form-check-input product-checkbox" style="cursor: pointer;">
                                </td>
                                <td>
                                    @if($product->main_image_url)
                                        <a href="{{ route('supplier.products.show', $product->sno) }}" title="View Details">
                                            <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-muted small\'>No Image</span>';">
                                        </a>
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('supplier.products.show', $product->sno) }}" class="fw-bold text-dark text-decoration-none d-block" title="View Details">
                                        {{ $product->name }}
                                    </a>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        @if($product->product_sku)
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">
                                                SKU: {{ $product->product_sku }}
                                            </span>
                                        @endif
                                        @if($product->price)
                                            <span class="text-muted small">₹{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $product->item_type_name ?? '-' }}</td>
                                <td>
                                    @if($product->is_integrated)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-link-45deg me-1"></i> Ready List
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                            Manual
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ $product->stock ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $product->created_at ? $product->created_at->format('d-m-Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('supplier.products.show', $product->sno) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-single" data-id="{{ $product->sno }}" data-name="{{ $product->name }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No products in Review Selection. Add products from "Add Product" to review and send them to the Photo Section.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        {{-- Commented Sent to Photo Section view table as requested
        @if($tab === 'sent')
            ...
        @endif
        --}}
        
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Hidden Form for Single Delete --}}
<form id="singleDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const btnBulkSend = document.getElementById('btnBulkSend');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkCount = document.getElementById('btnBulkCount');
    const btnDeleteCount = document.getElementById('btnDeleteCount');
    const selectedCountBadge = document.getElementById('selectedCountBadge');
    const bulkActionForm = document.getElementById('bulkActionForm');

    function updateBulkState() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        if (btnBulkCount) btnBulkCount.textContent = checkedCount;
        if (btnDeleteCount) btnDeleteCount.textContent = checkedCount;
        if (selectedCountBadge) selectedCountBadge.textContent = checkedCount + ' selected';

        if (btnBulkSend) {
            btnBulkSend.disabled = (checkedCount === 0);
        }
        if (btnBulkDelete) {
            btnBulkDelete.disabled = (checkedCount === 0);
        }

        if (selectAllCheckbox && productCheckboxes.length > 0) {
            selectAllCheckbox.checked = (checkedCount === productCheckboxes.length);
            selectAllCheckbox.indeterminate = (checkedCount > 0 && checkedCount < productCheckboxes.length);
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            productCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateBulkState();
        });
    }

    productCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    // Bulk Send Click (SweetAlert2)
    if (btnBulkSend) {
        btnBulkSend.addEventListener('click', function (e) {
            e.preventDefault();
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            if (checkedCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one product to send to the Photo Section.',
                    confirmButtonColor: '#2b5288',
                });
                return;
            }

            Swal.fire({
                title: 'Send to Photo Section?',
                html: `Are you sure you want to send <b>${checkedCount}</b> selected product(s) to the Photo Section?<br><small class="text-muted">Once sent, they will be removed from Review Selection.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b5288',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-camera me-1"></i> Yes, Send Now',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkActionForm.action = "{{ route('supplier.products.send-to-photo-section') }}";
                    bulkActionForm.submit();
                }
            });
        });
    }

    // Bulk Delete Click (SweetAlert2)
    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function () {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            if (checkedCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one product to delete.',
                    confirmButtonColor: '#2b5288',
                });
                return;
            }

            Swal.fire({
                title: 'Delete Selected Products?',
                html: `Are you sure you want to delete <b>${checkedCount}</b> selected product(s)?<br><small class="text-danger">This action cannot be undone!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkActionForm.action = "{{ route('supplier.products.bulk-delete') }}";
                    bulkActionForm.submit();
                }
            });
        });
    }

    // Single Delete handler (SweetAlert2)
    const singleDeleteForm = document.getElementById('singleDeleteForm');
    document.querySelectorAll('.btn-delete-single').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'Delete Product?',
                html: `Are you sure you want to delete <b>${name}</b>?<br><small class="text-danger">This action cannot be undone.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    singleDeleteForm.action = "{{ url('supplier/products') }}/" + id;
                    singleDeleteForm.submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection
