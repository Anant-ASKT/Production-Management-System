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
    .clickable-photo {
        cursor: zoom-in !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
    }
    .clickable-photo:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        filter: brightness(1.05);
    }
    /* Floating Hover Image Preview */
    .image-hover-preview-card {
        position: fixed;
        z-index: 1055;
        pointer-events: none;
        transition: opacity 0.15s ease-out;
        opacity: 0;
        transform: translate(16px, -50%);
    }
    .image-hover-preview-card.show {
        opacity: 1;
    }
    .hover-preview-inner {
        background: #ffffff;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        max-width: 270px;
        animation: hoverZoomIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes hoverZoomIn {
        from {
            transform: scale(0.92);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    .hover-preview-inner img {
        width: 258px;
        height: 258px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
        background: #f8fafc;
    }
    .hover-preview-meta {
        padding: 6px 4px 2px 4px;
        text-align: center;
    }
    .hover-preview-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .hover-preview-hint {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
        font-weight: 500;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="m-0 fw-bold text-dark">Review Selection</h4>
        <p class="text-muted small mb-0 mt-1">
            @if(!empty($isIntegrated))
                Review your selected products, send to the Photo Section, or delete
            @else
                Review your selected products, send to Specification, or delete
            @endif
        </p>
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
        <form id="bulkActionForm" action="{{ !empty($isIntegrated) ? route('supplier.products.send-to-photo-section') : route('supplier.products.send-to-specification') }}" method="POST">
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
                        @if(!empty($isIntegrated))
                            <i class="bi bi-camera me-1" id="btnBulkSendIcon"></i> <span id="btnBulkSendText">Send to Photo Section</span> (<span id="btnBulkCount">0</span>)
                        @else
                            <i class="bi bi-file-earmark-text me-1" id="btnBulkSendIcon"></i> <span id="btnBulkSendText">Send to Specification</span> (<span id="btnBulkCount">0</span>)
                        @endif
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
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->sno }}" data-is-integrated="{{ $product->is_integrated ? '1' : '0' }}" class="form-check-input product-checkbox" style="cursor: pointer;">
                                </td>
                                <td>
                                    @if($product->main_image_url)
                                        <img src="{{ $product->main_image_url }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded border clickable-photo product-table-image" 
                                             style="width: 50px; height: 50px; object-fit: cover;" 
                                             data-name="{{ $product->name }}"
                                             data-sku="{{ $product->product_sku }}"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-muted small\'>No Image</span>';">
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
                                    @if(!empty($product->is_set))
                                        <span class="badge text-white px-2 py-1" style="background-color: #7c3aed !important;">
                                            <i class="bi bi-collection-fill me-1"></i> Product Set
                                        </span>
                                    @elseif($product->is_integrated)
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
                                    <div class="d-inline-flex gap-1 align-items-center">
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

<!-- Floating Hover Image Preview -->
<div id="imageHoverPreviewCard" class="image-hover-preview-card d-none">
    <div class="hover-preview-inner">
        <img id="imageHoverPreviewImg" src="" alt="Hover Preview">
        <div class="hover-preview-meta">
            <div id="imageHoverPreviewTitle" class="hover-preview-title"></div>
            <div class="hover-preview-hint"><i class="bi bi-arrows-fullscreen me-1"></i> Click to view full image</div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-2 px-3 bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-zoom-in text-primary fs-5"></i>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="imagePreviewModalLabel">Product Image Preview</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px; max-height: 78vh; overflow: hidden;">
                <img id="imagePreviewModalImg" src="" alt="Full Preview" style="max-height: 76vh; max-width: 100%; object-fit: contain; display: block; margin: 0 auto;">
            </div>
            <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small fw-medium" id="imagePreviewModalCaption"></span>
                <div class="d-flex gap-2">
                    <a id="imagePreviewModalNewTab" href="#" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open in New Tab
                    </a>
                    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

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
        const checkedBoxes = Array.from(document.querySelectorAll('.product-checkbox:checked'));
        const checkedCount = checkedBoxes.length;
        if (btnBulkCount) btnBulkCount.textContent = checkedCount;
        if (btnDeleteCount) btnDeleteCount.textContent = checkedCount;
        if (selectedCountBadge) selectedCountBadge.textContent = checkedCount + ' selected';

        const anyNonIntegrated = checkedBoxes.some(cb => cb.dataset.isIntegrated === '0');
        const anyIntegrated = checkedBoxes.some(cb => cb.dataset.isIntegrated === '1');
        const defaultIntegrated = {{ !empty($isIntegrated) ? 'true' : 'false' }};

        const btnSendText = document.getElementById('btnBulkSendText');
        const btnSendIcon = document.getElementById('btnBulkSendIcon');

        if (btnSendText && btnSendIcon) {
            if (anyNonIntegrated && !anyIntegrated) {
                btnSendText.textContent = 'Send to Specification';
                btnSendIcon.className = 'bi bi-file-earmark-text me-1';
            } else if (anyNonIntegrated && anyIntegrated) {
                btnSendText.textContent = 'Send Selected';
                btnSendIcon.className = 'bi bi-send me-1';
            } else if (anyIntegrated) {
                btnSendText.textContent = 'Send to Photo Section';
                btnSendIcon.className = 'bi bi-camera me-1';
            } else {
                btnSendText.textContent = defaultIntegrated ? 'Send to Photo Section' : 'Send to Specification';
                btnSendIcon.className = defaultIntegrated ? 'bi bi-camera me-1' : 'bi bi-file-earmark-text me-1';
            }
        }

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
            const checkedBoxes = Array.from(document.querySelectorAll('.product-checkbox:checked'));
            const checkedCount = checkedBoxes.length;
            if (checkedCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one product to send.',
                    confirmButtonColor: '#2b5288',
                });
                return;
            }

            const anyNonIntegrated = checkedBoxes.some(cb => cb.dataset.isIntegrated === '0');
            const anyIntegrated = checkedBoxes.some(cb => cb.dataset.isIntegrated === '1');
            const defaultIntegrated = {{ !empty($isIntegrated) ? 'true' : 'false' }};

            let title = 'Send to Specification?';
            let confirmText = '<i class="bi bi-file-earmark-text me-1"></i> Yes, Send to Specification';
            let html = `Are you sure you want to send <b>${checkedCount}</b> selected product(s) to Specification?<br><small class="text-muted">They will be submitted for Admin Design Specification.</small>`;
            let targetAction = "{{ route('supplier.products.send-to-specification') }}";

            if (anyIntegrated && !anyNonIntegrated) {
                title = 'Send to Photo Section?';
                confirmText = '<i class="bi bi-camera me-1"></i> Yes, Send to Photo Section';
                html = `Are you sure you want to send <b>${checkedCount}</b> selected product(s) to the Photo Section?<br><small class="text-muted">Once sent, they will be removed from Review Selection.</small>`;
                targetAction = "{{ route('supplier.products.send-to-photo-section') }}";
            } else if (anyIntegrated && anyNonIntegrated) {
                title = 'Send Selected Products?';
                confirmText = '<i class="bi bi-send me-1"></i> Yes, Send Now';
                html = `Are you sure you want to send <b>${checkedCount}</b> selected product(s)?<br><small class="text-muted">Ready products will go to Photo Section, and manual products will go to Specification.</small>`;
                targetAction = "{{ route('supplier.products.send-to-photo-section') }}";
            } else if (defaultIntegrated) {
                title = 'Send to Photo Section?';
                confirmText = '<i class="bi bi-camera me-1"></i> Yes, Send to Photo Section';
                html = `Are you sure you want to send <b>${checkedCount}</b> selected product(s) to the Photo Section?`;
                targetAction = "{{ route('supplier.products.send-to-photo-section') }}";
            }

            Swal.fire({
                title: title,
                html: html,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b5288',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkActionForm.action = targetAction;
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

    // Image Modal handler
    function showImageInModal(src, title, caption) {
        if (!src) return;
        const modalEl = document.getElementById('imagePreviewModal');
        if (!modalEl) return;

        const modalImg = document.getElementById('imagePreviewModalImg');
        const modalTitle = document.getElementById('imagePreviewModalLabel');
        const modalCaption = document.getElementById('imagePreviewModalCaption');
        const newTabBtn = document.getElementById('imagePreviewModalNewTab');

        if (modalImg) modalImg.src = src;
        if (modalTitle) modalTitle.textContent = title || 'Product Image Preview';
        if (modalCaption) modalCaption.textContent = caption || '';

        if (newTabBtn) {
            newTabBtn.href = src;
            newTabBtn.target = '_blank';
        }

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    }

    // Floating Hover Image Preview & Click Modal handlers
    const hoverCard = $('#imageHoverPreviewCard');
    const hoverImg = $('#imageHoverPreviewImg');
    const hoverTitle = $('#imageHoverPreviewTitle');

    function positionHoverCard(e) {
        const cardWidth = 280;
        const cardHeight = 310;
        const winWidth = $(window).width();
        const winHeight = $(window).height();

        let x = e.clientX + 16;
        let y = e.clientY;

        if (x + cardWidth > winWidth) {
            x = e.clientX - cardWidth - 16;
        }

        const minY = cardHeight / 2 + 10;
        const maxY = winHeight - (cardHeight / 2) - 10;
        if (y < minY) y = minY;
        if (y > maxY) y = maxY;

        hoverCard.css({
            left: x + 'px',
            top: y + 'px'
        });
    }

    $(document).on('mouseenter', '.clickable-photo', function(e) {
        const src = $(this).attr('src');
        if (!src || src.includes('data:image/svg+xml')) return;

        const name = $(this).data('name') || $(this).attr('alt') || 'Product Image';
        const sku = $(this).data('sku') || '';

        hoverImg.attr('src', src);
        hoverTitle.text(name + (sku ? ` (${sku})` : ''));
        positionHoverCard(e);
        hoverCard.removeClass('d-none').addClass('show');
    });

    $(document).on('mousemove', '.clickable-photo', function(e) {
        positionHoverCard(e);
    });

    $(document).on('mouseleave', '.clickable-photo', function() {
        hoverCard.removeClass('show').addClass('d-none');
        hoverImg.attr('src', '');
    });

    $(document).on('click', '.clickable-photo', function(e) {
        e.preventDefault();
        e.stopPropagation();
        hoverCard.removeClass('show').addClass('d-none');
        const src = $(this).attr('src');
        const name = $(this).data('name') || $(this).attr('alt') || 'Product Image';
        const sku = $(this).data('sku') || '';

        if (src && !src.includes('data:image/svg+xml')) {
            showImageInModal(src, 'Product Image Preview', name + (sku ? ` (${sku})` : ''));
        }
    });
});
</script>
@endpush
@endsection
