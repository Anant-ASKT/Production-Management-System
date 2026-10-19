@extends('layouts.supplier')

@section('title', 'Product Details - ' . $product->name)
@section('page-title', 'Product Details')

@section('content')
<style>
    .clickable-photo {
        cursor: zoom-in !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }
    .clickable-photo:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18) !important;
        filter: brightness(1.03);
    }
    .thumb-item {
        width: 72px;
        height: 72px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .thumb-item:hover {
        border-color: #2b5288;
        transform: translateY(-2px);
    }
    .thumb-item.active {
        border-color: #2b5288;
        box-shadow: 0 0 0 2px rgba(43, 82, 136, 0.25);
    }
    .attr-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
    }
    .attr-row:nth-child(even) {
        background-color: #f8fafc;
    }
    .attr-row:last-child {
        border-bottom: none;
    }
    .attr-label {
        font-weight: 600;
        color: #475569;
        min-width: 140px;
    }
    .attr-value {
        color: #0f172a;
        font-weight: 500;
        text-align: right;
    }
    .metric-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="m-0 fw-bold text-dark">
            {{ $product->name }}
        </h4>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="text-muted small font-monospace">
                <i class="bi bi-upc me-1"></i>{{ $product->product_sku ?: ('PRD-' . $product->sno) }}
            </span>
            <span class="badge {{ $product->is_integrated ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}">
                <i class="bi {{ $product->is_integrated ? 'bi-link-45deg' : 'bi-pencil-square' }} me-1"></i>
                {{ $product->is_integrated ? 'Integrated Ready List' : 'Manual Entry' }}
            </span>
            @if($product->status === 'sent_to_photo_section' || $product->status_done === 'sent_to_photo_section')
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="bi bi-camera me-1"></i> Sent to Photo Section
                </span>
            @endif
            <span class="badge bg-light text-dark border">
                <i class="bi bi-calendar3 me-1"></i> Added {{ $product->created_at ? $product->created_at->format('d M, Y') : '-' }}
            </span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Review Selection
        </a>
        @if($product->status !== 'sent_to_photo_section' && $product->status_done !== 'sent_to_photo_section')
            <form id="formSendPhoto" action="{{ route('supplier.products.send-single-to-photo-section', $product->sno) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" id="btnSendPhoto" class="btn btn-primary">
                    <i class="bi bi-camera me-1"></i> Send to Photo Section
                </button>
            </form>
        @else
            <span class="badge bg-success px-3 py-2 fs-6">
                <i class="bi bi-check-circle me-1"></i> Sent to Photo Section
            </span>
        @endif
        <form id="formDeleteProduct" action="{{ route('supplier.products.destroy', $product->sno) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="button" id="btnDeleteProduct" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: IMAGES & GALLERY --}}
    <div class="col-lg-5 col-xl-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 1;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <strong class="text-dark">
                    <i class="bi bi-images me-1 text-primary"></i> Product Media
                </strong>
                @php
                    $totalImages = ($product->main_image_url ? 1 : 0) + count($subImages);
                @endphp
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    {{ $totalImages }} {{ $totalImages === 1 ? 'Image' : 'Images' }}
                </span>
            </div>
            <div class="card-body p-3">
                {{-- MAIN PREVIEW BOX --}}
                <div class="position-relative bg-light rounded-3 border overflow-hidden d-flex align-items-center justify-content-center p-2 mb-3" style="min-height: 320px; max-height: 420px;">
                    @if($product->main_image_url)
                        <img id="activeMainPreview"
                             src="{{ $product->main_image_url }}"
                             alt="{{ $product->name }}"
                             class="img-fluid rounded clickable-photo"
                             style="max-height: 400px; max-width: 100%; object-fit: contain;"
                             title="Click to view big photo">
                        
                        <button type="button"
                                class="btn btn-sm btn-light border position-absolute top-0 end-0 m-2 rounded-circle shadow-sm"
                                id="btnZoomActiveMain"
                                title="Enlarge photo"
                                style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-arrows-fullscreen text-secondary"></i>
                        </button>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-image" style="font-size: 48px;"></i>
                            <div class="mt-2 fw-semibold">No Main Image Available</div>
                        </div>
                    @endif
                </div>

                <div class="text-center mb-3">
                    <small class="text-muted"><i class="bi bi-zoom-in me-1"></i>Click image to view big photo in modal</small>
                </div>

                {{-- THUMBNAILS GALLERY --}}
                @if($totalImages > 0)
                    <div class="border-top pt-3">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Gallery Thumbnails</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($product->main_image_url)
                                <div class="thumb-item active" data-src="{{ $product->main_image_url }}" title="Main Image">
                                    <img src="{{ $product->main_image_url }}" alt="Main Image" class="w-100 h-100 object-fit-cover">
                                </div>
                            @endif

                            @foreach($subImages as $idx => $sImg)
                                <div class="thumb-item" data-src="{{ $sImg }}" title="Additional Image {{ $idx + 1 }}">
                                    <img src="{{ $sImg }}" alt="Sub Image {{ $idx + 1 }}" class="w-100 h-100 object-fit-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: PRODUCT SPECIFICATIONS & DETAILS --}}
    <div class="col-lg-7 col-xl-8">
        {{-- PRICE & STOCK CARDS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <strong class="text-dark">
                    <i class="bi bi-cash-stack me-1 text-success"></i> Pricing & Stock Information
                </strong>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="metric-card text-center">
                            <span class="small text-muted d-block mb-1">Regular Price</span>
                            <span class="fs-5 fw-bold text-dark">
                                {{ $product->price !== null && $product->price !== '' ? '₹' . number_format($product->price, 2) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card text-center">
                            <span class="small text-muted d-block mb-1">Sale Price</span>
                            <span class="fs-5 fw-bold text-success">
                                {{ $product->sale_price !== null && $product->sale_price !== '' ? '₹' . number_format($product->sale_price, 2) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card text-center">
                            <span class="small text-muted d-block mb-1">Min Price</span>
                            <span class="fs-5 fw-bold text-secondary">
                                {{ $product->min_price !== null && $product->min_price !== '' ? '₹' . number_format($product->min_price, 2) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card text-center bg-primary-subtle border-primary-subtle">
                            <span class="small text-primary d-block mb-1 fw-semibold">Current Stock</span>
                            <span class="fs-5 fw-bold text-primary">
                                {{ $product->stock ?? 0 }} <small class="fs-7 fw-normal">units</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DESCRIPTION --}}
        @if(!empty($product->description))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <strong class="text-dark">
                    <i class="bi bi-file-text me-1 text-primary"></i> Description
                </strong>
            </div>
            <div class="card-body p-3 p-md-4">
                <p class="mb-0 text-secondary" style="white-space: pre-line; line-height: 1.6;">{{ $product->description }}</p>
            </div>
        </div>
        @endif

        {{-- PRODUCT ATTRIBUTES & MASTER SPECIFICATIONS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <strong class="text-dark">
                    <i class="bi bi-card-checklist me-1 text-primary"></i> Master & Technical Attributes
                </strong>
                <span class="badge bg-light text-muted border">Garment Details</span>
            </div>
            <div class="card-body p-0">
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-tag me-1 text-muted"></i> Product Name</span>
                    <span class="attr-value fw-bold">{{ $product->name }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-upc-scan me-1 text-muted"></i> SKU</span>
                    <span class="attr-value font-monospace">{{ $product->product_sku ?: '-' }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-boxes me-1 text-muted"></i> Item Type</span>
                    <span class="attr-value">{{ ($masterNames['item_type'] ?? null) ?: ($product->item_type ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-bookmark me-1 text-muted"></i> Item Name</span>
                    <span class="attr-value">{{ ($masterNames['item_name'] ?? null) ?: ($product->item_name ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-palette me-1 text-muted"></i> Colour</span>
                    <span class="attr-value">{{ ($masterNames['colour'] ?? null) ?: ($product->colour ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-aspect-ratio me-1 text-muted"></i> Size</span>
                    <span class="attr-value">{{ ($masterNames['size'] ?? null) ?: ($product->size ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-gender-ambiguous me-1 text-muted"></i> Gender</span>
                    <span class="attr-value">{{ ($masterNames['gender'] ?? null) ?: ($product->gender ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-layers me-1 text-muted"></i> Composition</span>
                    <span class="attr-value">{{ ($masterNames['composition'] ?? null) ?: ($product->composition ?: ($product->compositions ?: '-')) }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-person-badge me-1 text-muted"></i> Designer</span>
                    <span class="attr-value">{{ ($masterNames['designer'] ?? null) ?: ($product->designer ?: ($product->designers ?: '-')) }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-flower1 me-1 text-muted"></i> Yarn</span>
                    <span class="attr-value">{{ ($masterNames['yarn'] ?? null) ?: ($product->yarn ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-gem me-1 text-muted"></i> Embellishment</span>
                    <span class="attr-value">{{ ($masterNames['embellishment'] ?? null) ?: ($product->embellishment ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-gear me-1 text-muted"></i> Manufacturing Process</span>
                    <span class="attr-value">{{ ($masterNames['manufacturing_process'] ?? null) ?: ($product->manufacturing_process ?: ($product->mfg_processes ?: '-')) }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-tools me-1 text-muted"></i> Craftsman</span>
                    <span class="attr-value">{{ ($masterNames['craftsman'] ?? null) ?: ($product->craftsman ?: ($product->craftsmen ?: '-')) }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-building me-1 text-muted"></i> Manufacture</span>
                    <span class="attr-value">{{ ($masterNames['manufacture'] ?? null) ?: ($product->manufacture ?: '-') }}</span>
                </div>
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-collection me-1 text-muted"></i> Collection</span>
                    <span class="attr-value">{{ $product->collection ?: '-' }}</span>
                </div>
                @if(!empty($product->design_names))
                <div class="attr-row">
                    <span class="attr-label"><i class="bi bi-stars me-1 text-muted"></i> Design Names</span>
                    <span class="attr-value">{{ $product->design_names }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- RECORD INFORMATION CARD --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <strong class="text-dark">
                    <i class="bi bi-info-circle me-1 text-primary"></i> System Information
                </strong>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 small text-muted">
                    <div class="col-md-6">
                        <div><strong>Supplier:</strong> {{ $supplier->name ?? 'Supplier' }}</div>
                        <div><strong>Shop / Company:</strong> {{ $supplier->shop_name ?? ($supplier->name ?? '-') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>Created:</strong> {{ $product->created_at ? $product->created_at->format('d-m-Y H:i A') : '-' }}</div>
                        <div><strong>Last Updated:</strong> {{ $product->updated_at ? $product->updated_at->format('d-m-Y H:i A') : '-' }}</div>
                    </div>
                </div>
            </div>
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
                    <h6 class="modal-title fw-bold text-dark mb-0" id="imagePreviewModalLabel">Product Photo Preview</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 d-flex align-items-center justify-content-center" style="background-color: #0b1120; min-height: 350px; max-height: 80vh; overflow: auto;">
                <img id="imagePreviewModalImg" src="" alt="Full Preview" class="img-fluid rounded shadow" style="max-height: 75vh; max-width: 100%; object-fit: contain;">
            </div>
            <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small fw-medium" id="imagePreviewModalCaption"></span>
                <div class="d-flex gap-2">
                    <a id="imagePreviewModalNewTab" href="#" target="_blank" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                        <i class="bi bi-arrows-fullscreen"></i>
                        <span>Open Full Size</span>
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal zoom handler
        function showImageInModal(src, title, caption) {
            if (!src) return;
            const modalEl = document.getElementById('imagePreviewModal');
            if (!modalEl) return;

            const modalImg = document.getElementById('imagePreviewModalImg');
            const modalTitle = document.getElementById('imagePreviewModalLabel');
            const modalCaption = document.getElementById('imagePreviewModalCaption');
            const newTabBtn = document.getElementById('imagePreviewModalNewTab');

            if (modalImg) modalImg.src = src;
            if (modalTitle) modalTitle.textContent = title || 'Product Photo Preview';
            if (modalCaption) modalCaption.textContent = caption || '';

            if (newTabBtn) {
                newTabBtn.href = src;
                newTabBtn.target = '_blank';
            }

            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }

        const activeMainPreview = document.getElementById('activeMainPreview');
        const btnZoomActiveMain = document.getElementById('btnZoomActiveMain');
        const productName = @json($product->name);

        if (activeMainPreview) {
            activeMainPreview.addEventListener('click', function() {
                showImageInModal(this.src, 'Product Photo Preview', productName);
            });
        }

        if (btnZoomActiveMain && activeMainPreview) {
            btnZoomActiveMain.addEventListener('click', function(e) {
                e.stopPropagation();
                showImageInModal(activeMainPreview.src, 'Product Photo Preview', productName);
            });
        }

        // Thumbnails clicking
        document.querySelectorAll('.thumb-item').forEach(thumb => {
            thumb.addEventListener('click', function() {
                document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const src = this.getAttribute('data-src');
                if (src && activeMainPreview) {
                    activeMainPreview.src = src;
                }
            });
        });

        // Send to Photo Section with SweetAlert2
        const btnSendPhoto = document.getElementById('btnSendPhoto');
        const formSendPhoto = document.getElementById('formSendPhoto');
        if (btnSendPhoto && formSendPhoto) {
            btnSendPhoto.addEventListener('click', function() {
                Swal.fire({
                    title: 'Send to Photo Section?',
                    html: `Are you sure you want to send <b>${productName}</b> to the Photo Section?<br><small class="text-muted">It will be moved out of your Review Selection.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b5288',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-camera me-1"></i> Yes, Send Now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formSendPhoto.submit();
                    }
                });
            });
        }

        // Delete Product with SweetAlert2
        const btnDeleteProduct = document.getElementById('btnDeleteProduct');
        const formDeleteProduct = document.getElementById('formDeleteProduct');
        if (btnDeleteProduct && formDeleteProduct) {
            btnDeleteProduct.addEventListener('click', function() {
                Swal.fire({
                    title: 'Delete Product?',
                    html: `Are you sure you want to delete <b>${productName}</b>?<br><small class="text-danger">This action cannot be undone.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formDeleteProduct.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
