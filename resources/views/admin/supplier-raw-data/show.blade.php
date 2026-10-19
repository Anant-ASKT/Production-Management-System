@extends('layouts.app')

@section('title', ($product->name ?: 'Supplier Product #' . $product->sno) . ' - Supplier Raw Data Details')

@section('content')
<style>
    .show-raw-page {
        padding: 24px 20px 40px;
        max-width: 1600px;
        margin: 0 auto;
    }
    .info-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .info-card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .info-card-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-card-body {
        padding: 20px;
    }
    .spec-row {
        padding: 10px 14px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        height: 100%;
        transition: all 0.15s ease;
    }
    .spec-row:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }
    .spec-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }
    .spec-val-filled {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        text-align: right;
        word-break: break-word;
    }
    .spec-val-blank {
        font-size: 11px;
        font-family: monospace;
        font-style: italic;
        padding: 2px 8px;
        border-radius: 4px;
        background: #e2e8f0;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }
    .main-img-box {
        background: #0f172a;
        border-radius: 10px;
        min-height: 320px;
        max-height: 440px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }
    .main-img-box img {
        max-height: 420px;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.25s ease;
    }
    .main-img-box:hover img {
        transform: scale(1.03);
    }
    .sub-img-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .sub-img-thumb:hover {
        border-color: #0284c7;
        transform: scale(1.06);
    }
    .kpi-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 18px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        height: 100%;
    }
    .kpi-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .kpi-value {
        font-size: 22px;
        font-weight: 800;
    }
</style>

<div class="show-raw-page">

    {{-- Top Navigation & Actions Bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.supplier-raw-data.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-arrow-left"></i>
                <span>Back to Supplier Raw Data</span>
            </a>
            <div class="vr my-1 d-none d-sm-block"></div>
            <div>
                <span class="text-muted small">Viewing Supplier Sent Product</span>
                <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span>{{ $product->name ?: 'Product #' . $product->sno }}</span>
                    <span class="badge bg-secondary font-monospace" style="font-size: 12px;">ID: #{{ $product->sno }}</span>
                </h4>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('design-specifications.index', ['supplier_product_id' => $product->sno]) }}" 
               class="btn btn-primary btn-lg px-4 d-inline-flex align-items-center gap-2 shadow fw-bold">
                <i class="bi bi-palette2"></i>
                <span>Make Design Specification</span>
            </a>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4">

        {{-- LEFT COLUMN: Images & Supplier Profile (5 cols) --}}
        <div class="col-lg-5">

            {{-- Main Image Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h6 class="info-card-title">
                        <i class="bi bi-image text-primary"></i> Main Product Image
                    </h6>
                    @if($product->image_url)
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Image Available</span>
                    @else
                        <span class="badge bg-light text-muted border fst-italic">Blank - No Image</span>
                    @endif
                </div>
                <div class="info-card-body p-3">
                    @if($product->image_url)
                        <div class="main-img-box" onclick="zoomImage('{{ $product->image_url }}', '{{ addslashes($product->name) }}')">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            <div class="position-absolute bottom-0 end-0 m-3">
                                <button type="button" class="btn btn-sm btn-dark bg-opacity-75 text-white shadow">
                                    <i class="bi bi-zoom-in me-1"></i> Zoom Full Photo
                                </button>
                            </div>
                        </div>
                        <div class="text-center text-muted small mt-2">
                            <i class="bi bi-info-circle me-1"></i> Click photo to zoom full size
                        </div>
                    @else
                        <div class="p-5 text-center bg-light rounded text-muted">
                            <i class="bi bi-card-image fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <span class="fw-semibold fst-italic">No main image sent by supplier</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Additional Images Gallery Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h6 class="info-card-title">
                        <i class="bi bi-images text-primary"></i> Additional Images Gallery
                    </h6>
                    <span class="badge bg-light text-secondary border font-monospace">
                        {{ is_array($product->sub_image_urls) ? count($product->sub_image_urls) : 0 }} Images
                    </span>
                </div>
                <div class="info-card-body p-3">
                    @if(is_array($product->sub_image_urls) && count($product->sub_image_urls) > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->sub_image_urls as $subUrl)
                                <img src="{{ $subUrl }}" alt="Sub Image" class="sub-img-thumb" onclick="zoomImage('{{ $subUrl }}', '{{ addslashes($product->name) }} (Additional Image)')">
                            @endforeach
                        </div>
                        <div class="text-muted small mt-2">
                            <i class="bi bi-info-circle me-1"></i> Click any thumbnail to preview
                        </div>
                    @else
                        <div class="p-3 text-center bg-light rounded text-muted small fst-italic">
                            <span class="spec-val-blank">Blank - No additional images sent by supplier</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Supplier Profile Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h6 class="info-card-title">
                        <i class="bi bi-building text-primary"></i> Supplier Profile
                    </h6>
                    @if(!empty($product->supplier_id))
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">ID: #{{ $product->supplier_id }}</span>
                    @endif
                </div>
                <div class="info-card-body">
                    <div class="mb-3 pb-2 border-bottom">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Company / Supplier Name</span>
                        <div class="fw-bold text-dark fs-6">{{ $product->supplier_name ?: 'Unknown' }}</div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Supplier Code / Nickname</span>
                        <div>
                            @if(!empty($product->supplier_nickname))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">{{ $product->supplier_nickname }}</span>
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Contact Person</span>
                        <div class="fw-semibold text-dark">
                            @if(!empty($product->supplier_user_name))
                                <i class="bi bi-person me-1 text-primary"></i>{{ $product->supplier_user_name }}
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 pb-2 border-bottom">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Phone Number</span>
                        <div>
                            @if(!empty($product->supplier_user_phone))
                                <a href="tel:{{ $product->supplier_user_phone }}" class="text-decoration-none text-dark fw-bold">
                                    <i class="bi bi-telephone me-1 text-success"></i>{{ $product->supplier_user_phone }}
                                </a>
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Email Address</span>
                        <div>
                            @if(!empty($product->supplier_user_email))
                                <a href="mailto:{{ $product->supplier_user_email }}" class="text-decoration-none text-dark">
                                    <i class="bi bi-envelope me-1 text-info"></i>{{ $product->supplier_user_email }}
                                </a>
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Stock, Pricing, Garment Specifications & Description (7 cols) --}}
        <div class="col-lg-7">

            {{-- Status & Summary Banner --}}
            <div class="info-card mb-3">
                <div class="info-card-body p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Product Name Sent by Supplier</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $product->name ?: '-' }}</h3>
                        </div>
                        <div class="text-end">
                            @if($product->status_done === 'Done')
                                <span class="badge bg-success text-white px-3 py-2 fs-6">
                                    <i class="bi bi-check2-circle me-1"></i> Specified (Done)
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                    <i class="bi bi-clock me-1"></i> Pending Specification
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-muted small border-top pt-2 mt-2">
                        <span><strong>Integration:</strong> 
                            @if($product->is_integrated)
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Integrated Supplier</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Manual (Non-Integrated)</span>
                            @endif
                        </span>
                        <span>•</span>
                        <span><strong>Workflow Status:</strong> <span class="badge bg-light text-dark border">{{ $product->status ?: 'draft' }}</span></span>
                        <span>•</span>
                        <span><strong>Submitted:</strong> {{ $product->created_at ? \Carbon\Carbon::parse($product->created_at)->format('d-M-Y h:i A') : '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- 4 KPI Commercials & Stock Cards --}}
            <div class="row g-3 mb-3">
                <div class="col-sm-3 col-6">
                    <div class="kpi-box">
                        <div class="kpi-title text-success"><i class="bi bi-boxes me-1"></i> Available Stock</div>
                        <div class="kpi-value text-success">{{ $product->stock ?? 0 }}</div>
                        <span class="text-muted small">Units / Pcs</span>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="kpi-box">
                        <div class="kpi-title text-dark"><i class="bi bi-tag me-1"></i> Base Price</div>
                        <div class="kpi-value text-dark">
                            @if($product->price !== null && $product->price !== '')
                                ₹{{ number_format($product->price, 2) }}
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                        <span class="text-muted small">Standard Rate</span>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="kpi-box">
                        <div class="kpi-title text-primary"><i class="bi bi-percent me-1"></i> Sale Price</div>
                        <div class="kpi-value text-primary">
                            @if($product->sale_price !== null && $product->sale_price !== '')
                                ₹{{ number_format($product->sale_price, 2) }}
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                        <span class="text-muted small">Special Offer</span>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="kpi-box">
                        <div class="kpi-title text-muted"><i class="bi bi-shield-shaded me-1"></i> Min Price</div>
                        <div class="kpi-value text-muted">
                            @if($product->min_price !== null && $product->min_price !== '')
                                ₹{{ number_format($product->min_price, 2) }}
                            @else
                                <span class="spec-val-blank">Blank</span>
                            @endif
                        </div>
                        <span class="text-muted small">Floor Price</span>
                    </div>
                </div>
            </div>

            {{-- ALL Garment & Design Specifications (Filled or Blank) --}}
            @php
                $allSpecs = [
                    ['label' => 'Item Name / Number', 'val' => $product->item_name],
                    ['label' => 'Item Type', 'val' => $product->item_type],
                    ['label' => 'Gender', 'val' => $product->gender],
                    ['label' => 'Composition / Fabric', 'val' => $product->composition ?: $product->compositions],
                    ['label' => 'Colour', 'val' => $product->colour],
                    ['label' => 'Size', 'val' => $product->size],
                    ['label' => 'Yarn', 'val' => $product->yarn],
                    ['label' => 'Embellishment', 'val' => $product->embellishment],
                    ['label' => 'Manufacturing Process', 'val' => $product->manufacturing_process ?: $product->mfg_processes],
                    ['label' => 'Craftsman', 'val' => $product->craftsman ?: $product->craftsmen],
                    ['label' => 'Manufacture / Factory', 'val' => $product->manufacture],
                    ['label' => 'Designer', 'val' => $product->designer ?: $product->designers],
                    ['label' => 'Collection', 'val' => $product->collection],
                    ['label' => 'Variations', 'val' => $product->variations],
                    ['label' => 'Supplier SKU', 'val' => $product->product_sku],
                ];
            @endphp

            <div class="info-card">
                <div class="info-card-header">
                    <h6 class="info-card-title">
                        <i class="bi bi-card-checklist text-primary"></i> Garment Specifications Sent by Supplier
                    </h6>
                    <span class="badge bg-light text-muted border font-monospace">All Attributes (Filled or Blank)</span>
                </div>
                <div class="info-card-body">
                    <div class="row g-2">
                        @foreach($allSpecs as $spec)
                            <div class="col-md-6">
                                <div class="spec-row">
                                    <span class="spec-label">{{ $spec['label'] }}:</span>
                                    <div>
                                        @if($spec['val'] !== null && trim($spec['val']) !== '' && trim($spec['val']) !== '-' && strtolower(trim($spec['val'])) !== 'null')
                                            <span class="spec-val-filled">{{ $spec['val'] }}</span>
                                        @else
                                            <span class="spec-val-blank">Blank</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Description Sent by Supplier Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <h6 class="info-card-title">
                        <i class="bi bi-file-text text-primary"></i> Description Sent by Supplier
                    </h6>
                </div>
                <div class="info-card-body">
                    @if(!empty(trim($product->description)))
                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;">{{ $product->description }}</div>
                    @else
                        <div class="p-3 text-center bg-light rounded text-muted small fst-italic">
                            <span class="spec-val-blank">Blank - No description provided by supplier</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bottom Sticky Actions --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2">
                <a href="{{ route('admin.supplier-raw-data.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Listing
                </a>
                <a href="{{ route('design-specifications.index', ['supplier_product_id' => $product->sno]) }}" class="btn btn-primary btn-lg px-5 fw-bold shadow">
                    <i class="bi bi-palette2 me-1"></i> Make Design Specification
                </a>
            </div>

        </div>

    </div>

</div>

{{-- Image Zoom Modal --}}
<div class="modal fade" id="imageZoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="background: transparent;">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="imageZoomModalImg" src="" alt="Full Preview" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
                <div class="text-white text-center mt-2 small" id="imageZoomModalTitle"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function zoomImage(src, title) {
    if (!src) return;
    const modalEl = document.getElementById('imageZoomModal');
    const modalImg = document.getElementById('imageZoomModalImg');
    const modalTitle = document.getElementById('imageZoomModalTitle');
    if (modalEl && modalImg) {
        modalImg.src = src;
        if (modalTitle) modalTitle.textContent = title || '';
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}
</script>
@endpush
@endsection
