@extends('layouts.app')

@section('title', 'Product Details - ' . $product->clean_product_name)

@section('content')
<div class="container-fluid py-4 px-md-4">

    {{-- PAGE TITLE --}}
    <h2 class="fw-bold text-dark mb-4" style="font-size: 1.85rem; letter-spacing: -0.02em;">
        Product Details
    </h2>

    @php
        function normalizeImgUrl($path) {
            if (!$path) return '/assets/images/placeholder.png';
            $path = trim(str_replace('\\', '/', $path), " \t\n\r\0\x0B\"'\\");
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
                return $path;
            }
            if (str_starts_with($path, '/')) {
                return $path;
            }
            return '/' . $path;
        }

        $mainImageObj = $approvedImages->firstWhere('image_type', 'main') ?? $approvedImages->first();
        $primaryImgUrl = $mainImageObj ? normalizeImgUrl($mainImageObj->enhanced_image_path) : '/assets/images/placeholder.png';

        $descRaw = trim($product->AI_product_description ?? '');
        $descClean = preg_replace('/^\*+|\*+$/', '', $descRaw);
        $paragraphs = array_filter(array_map('trim', explode("\n", $descClean)));
        $shortDesc = count($paragraphs) > 0 ? reset($paragraphs) : 'No short description provided for this product.';
    @endphp

    {{-- WHITE MAIN CARD CONTAINER --}}
    <div class="card border-0 shadow-sm rounded-3 p-4 p-lg-5 bg-white mb-4">

        {{-- BACK LINK --}}
        <div class="mb-4">
            <a href="{{ route('admin.publish-products.index') }}" class="text-decoration-none text-secondary fw-medium small d-inline-flex align-items-center">
                <span class="me-1.5">&larr;</span> Back to Products
            </a>
        </div>

        <div class="row g-4 g-lg-5">
            
            {{-- LEFT COLUMN: PRODUCT IMAGES --}}
            <div class="col-lg-5 col-xl-5">
                <div class="product-gallery">
                    
                    {{-- Main Image Box --}}
                    <div class="position-relative bg-white border rounded-2 overflow-hidden d-flex align-items-center justify-content-center mb-3" style="height: 380px;">
                        <img id="mainImagePreview" 
                             src="{{ $primaryImgUrl }}" 
                             alt="{{ $product->clean_product_name }}" 
                             class="img-fluid" 
                             style="max-height: 360px; max-width: 100%; object-fit: contain;">
                        
                        {{-- Zoom / Search Icon in Top Right --}}
                        <a href="{{ $primaryImgUrl }}" target="_blank" class="position-absolute top-0 end-0 m-3 text-secondary bg-white border rounded-circle d-flex align-items-center justify-content-center shadow-xs text-decoration-none" style="width: 34px; height: 34px;">
                            <i class="bi bi-search fs-7"></i>
                        </a>
                    </div>

                    {{-- Thumbnails Row --}}
                    @if($approvedImages->count() > 1)
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($approvedImages as $idx => $img)
                                @php $thumbUrl = normalizeImgUrl($img->enhanced_image_path); @endphp
                                <div class="thumb-box border rounded-2 p-1 bg-white {{ $idx === 0 ? 'active' : '' }}" 
                                     data-url="{{ $thumbUrl }}" 
                                     style="width: 70px; height: 70px; cursor: pointer;">
                                    <img src="{{ $thumbUrl }}" class="w-100 h-100 object-fit-contain rounded-1" alt="Thumbnail {{ $idx + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

            {{-- RIGHT COLUMN: PRODUCT INFO & PUBLISH BUTTON --}}
            <div class="col-lg-7 col-xl-7">
                <div class="ps-lg-2">
                    
                    {{-- Breadcrumbs --}}
                    <div class="text-muted small mb-2">
                        <span>Home</span> / 
                        <span>{{ $product->product_type ?: 'Garments' }}</span> / 
                        <span class="text-secondary">{{ $product->clean_product_name }}</span>
                    </div>

                    {{-- Origin Supplier Sub-label --}}
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1">
                            <i class="bi bi-truck me-1"></i> Origin: <strong>{{ $product->supplier_name ?: 'Global' }}</strong>
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                            {{ $product->product_type ?: 'Product' }}
                        </span>
                        @if($publishedRecords->count() > 0)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                <i class="bi bi-check2-circle me-1"></i> Published to {{ $publishedRecords->count() }} Store{{ $publishedRecords->count() > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- Main Product Title --}}
                    <h3 class="fw-bold text-dark mb-3" style="font-size: 1.65rem;">
                        {{ $product->clean_product_name }}
                    </h3>

                    {{-- Price Line --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        @if(!empty($product->sale_price))
                            <span class="fw-bold fs-4 text-dark">&#8377;{{ number_format($product->sale_price, 2) }}</span>
                            @if(!empty($product->regular_price) && $product->regular_price > $product->sale_price)
                                <span class="text-muted text-decoration-line-through small">&#8377;{{ number_format($product->regular_price, 2) }}</span>
                            @endif
                        @elseif(!empty($product->regular_price))
                            <span class="fw-bold fs-4 text-dark">&#8377;{{ number_format($product->regular_price, 2) }}</span>
                        @else
                            <span class="fw-bold fs-4 text-dark">&#8377;4,999.00</span>
                        @endif
                        <span class="text-muted small">+ Free Shipping</span>
                    </div>

                    {{-- Short Description --}}
                    <div class="text-secondary small mb-3 lh-base" style="max-width: 580px;">
                        {{ $shortDesc }}
                    </div>

                    {{-- Availability --}}
                    <div class="mb-4 small">
                        <span class="text-dark fw-medium">Availability:</span>
                        <span class="text-primary fw-semibold">{{ ($product->stock_qty ?? 50) > 0 ? ($product->stock_qty . ' in stock') : 'In stock' }}</span>
                    </div>

                    {{-- ====================================================
                         CLEAN ACTION BAR: OPEN PUBLISH MODAL BUTTON
                    ===================================================== --}}
                    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                        <button type="button" class="btn btn-primary px-4 py-2.5 fw-bold rounded-2 shadow-xs d-inline-flex align-items-center" style="background-color: #006699; border-color: #006699; min-height: 44px;" data-bs-toggle="modal" data-bs-target="#publishModal">
                            <i class="bi bi-cloud-arrow-up-fill me-2 fs-6"></i> Publish to WooCommerce
                        </button>

                        @if($publishedRecords->count() > 0)
                            @php $latestPublish = $publishedRecords->first(); @endphp
                            @if($latestPublish->permalink)
                                <a href="{{ $latestPublish->permalink }}" target="_blank" class="btn btn-outline-secondary px-3 py-2.5 fw-medium rounded-2 small d-inline-flex align-items-center" style="min-height: 44px;">
                                    <i class="bi bi-box-arrow-up-right me-1.5"></i> View Live ({{ $latestPublish->target_supplier_name }})
                                </a>
                            @endif
                        @endif
                    </div>

                    {{-- Product Metadata Attributes List --}}
                    <div class="small text-secondary pt-3 border-top d-flex flex-column gap-1.5">
                        <div><strong class="text-dark">SKU:</strong> <span class="font-monospace text-muted">{{ $product->sku ?: '—' }}</span></div>
                        <div><strong class="text-dark">Category / Type:</strong> <span>{{ $product->product_type ?: '—' }}</span></div>
                        <div><strong class="text-dark">Origin Supplier:</strong> <span>{{ $product->supplier_name ?: 'Handknit' }}</span></div>
                        @if($product->colour_name)
                            <div><strong class="text-dark">Colour:</strong> <span>{{ $product->colour_name }}</span></div>
                        @endif
                        @if($product->size_name)
                            <div><strong class="text-dark">Size:</strong> <span>{{ $product->size_name }}</span></div>
                        @endif
                        @if($product->composition_name)
                            <div><strong class="text-dark">Composition:</strong> <span>{{ $product->composition_name }}</span></div>
                        @endif
                        @if($product->gender_name)
                            <div><strong class="text-dark">Gender:</strong> <span>{{ $product->gender_name }}</span></div>
                        @endif
                        @if($product->barcode)
                            <div><strong class="text-dark">Barcode:</strong> <span class="font-monospace text-muted">{{ $product->barcode }}</span></div>
                        @endif
                    </div>

                </div>
            </div>

        </div>

        {{-- BOTTOM TABS SECTION --}}
        <div class="mt-5 pt-4 border-top">
            <ul class="nav nav-tabs border-bottom mb-4" id="simpleProductTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-dark px-0 me-4 py-2 border-0 bg-transparent simple-tab" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-pane" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-secondary px-0 me-4 py-2 border-0 bg-transparent simple-tab" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                        Additional information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-secondary px-0 py-2 border-0 bg-transparent simple-tab" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-pane" type="button" role="tab">
                        SEO Metadata
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="simpleProductTabsContent">
                
                {{-- TAB 1: DESCRIPTION --}}
                <div class="tab-pane fade show active" id="desc-pane" role="tabpanel">
                    @php
                        $cleanDesc = trim($product->AI_product_description ?? '');
                        $cleanDesc = preg_replace('/^\*+|\*+$/', '', $cleanDesc);
                        $cleanDesc = trim($cleanDesc);
                    @endphp
                    <div class="text-secondary small lh-lg" style="white-space: pre-line; font-size: 0.95rem;">
                        {{ $cleanDesc ?: 'No detailed description available.' }}
                    </div>
                </div>

                {{-- TAB 2: ADDITIONAL INFORMATION --}}
                <div class="tab-pane fade" id="info-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0 bg-white">
                            <tbody>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold" style="width: 240px;">Brand / Origin Supplier</th>
                                    <td class="fw-semibold text-dark">{{ $product->supplier_name ?: 'Handknit' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Garment Type</th>
                                    <td>{{ $product->product_type ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Gender</th>
                                    <td>{{ $product->gender_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Colour</th>
                                    <td>{{ $product->colour_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Size</th>
                                    <td>{{ $product->size_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Fabric / Composition</th>
                                    <td>{{ $product->composition_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Manufacturing Process</th>
                                    <td>{{ $product->manufacturing_process_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Designer</th>
                                    <td>{{ $product->designer_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Craftsman</th>
                                    <td>{{ $product->craftsman_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">SKU</th>
                                    <td class="font-monospace text-dark fw-bold">{{ $product->sku ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Barcode</th>
                                    <td class="font-monospace">{{ $product->barcode ?: '—' }}</td>
                                </tr>
                                @if(!empty($product->regular_price))
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Regular Price</th>
                                    <td class="fw-semibold text-dark">&#8377;{{ number_format($product->regular_price, 2) }}</td>
                                </tr>
                                @endif
                                @if(!empty($product->sale_price))
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Sale Price</th>
                                    <td class="fw-bold text-danger">&#8377;{{ number_format($product->sale_price, 2) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: SEO METADATA --}}
                <div class="tab-pane fade" id="seo-pane" role="tabpanel">
                    @php
                        function cleanSeoVal($val) {
                            if (empty($val)) return null;
                            $clean = trim($val);
                            if (preg_match('/^(.*?)(?:\*\*Meta|\*\*Description|\*\*Tag|Meta Tag|Meta:|\*\*Image Alt)/i', $clean, $m)) {
                                $clean = trim($m[1]);
                            }
                            $clean = preg_replace('/^\*+|\*+$/', '', $clean);
                            return trim($clean, " \t\n\r\0\x0B*:-");
                        }

                        $metaTitle = cleanSeoVal($product->AI_Metatitle);
                        $metaKeywords = cleanSeoVal($product->AI_Metakeywards);
                        $metaDesc = cleanSeoVal($product->AI_Metadescription);
                        $imageAlt = cleanSeoVal($product->AI_Imagealttext);
                    @endphp
                    <div class="p-3.5 bg-light rounded-3 border small">
                        <div class="row g-3">
                            <div class="col-md-3 text-dark fw-bold">Meta Title:</div>
                            <div class="col-md-9 text-muted">{{ $metaTitle ?: 'N/A' }}</div>

                            <div class="col-md-3 text-dark fw-bold">Meta Keywords:</div>
                            <div class="col-md-9 text-muted">{{ $metaKeywords ?: 'N/A' }}</div>

                            <div class="col-md-3 text-dark fw-bold">Meta Description:</div>
                            <div class="col-md-9 text-muted">{{ $metaDesc ?: 'No description set.' }}</div>

                            <div class="col-md-3 text-dark fw-bold">Image Alt Text:</div>
                            <div class="col-md-9 text-muted">{{ $imageAlt ?: 'N/A' }}</div>

                            <div class="col-md-3 text-dark fw-bold">Product Tags:</div>
                            <div class="col-md-9">
                                @if($product->AI_Producttag)
                                    @foreach(explode(',', $product->AI_Producttag) as $t)
                                        @php $tagClean = cleanSeoVal($t); @endphp
                                        @if($tagClean && !str_contains(strtolower($tagClean), 'meta'))
                                            <span class="badge bg-white text-secondary border rounded-pill py-1 px-2.5 me-1 mb-1">#{{ $tagClean }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

{{-- =========================================================
     MODAL: PUBLISH TO WOOCOMMERCE STORE
========================================================== --}}
<div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="bi bi-cloud-arrow-up-fill fs-6"></i>
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="publishModalLabel">Publish to WooCommerce</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                
                {{-- Product Mini Header Preview --}}
                <div class="d-flex align-items-center gap-3 p-2.5 bg-light rounded-3 border mb-4">
                    <img src="{{ $primaryImgUrl }}" class="rounded-2 border bg-white object-fit-contain" style="width: 54px; height: 54px;" alt="Preview">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold text-dark text-truncate small">{{ $product->clean_product_name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            SKU: <span class="font-monospace">{{ $product->sku ?: '—' }}</span> &bull; Origin: {{ $product->supplier_name ?: 'Global' }}
                        </div>
                    </div>
                </div>

                {{-- Form Controls --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">
                        Target Supplier / Store <span class="text-danger">*</span>
                    </label>
                    <select id="modalTargetSupplierSelect" class="form-select rounded-3">
                        @foreach($suppliers as $s)
                            @php
                                $hasCreds = !empty($s->store_url) && !empty($s->consumer_key) && !empty($s->consumer_secret);
                                $isOrigin = ($s->sno == ($product->supplier_id ?? null));
                            @endphp
                            <option value="{{ $s->sno }}" 
                                    data-has-creds="{{ $hasCreds ? '1' : '0' }}"
                                    data-store-url="{{ $s->store_url }}"
                                    data-name="{{ $s->name }}"
                                    {{ $s->sno == $defaultTargetSupplierId ? 'selected' : '' }}>
                                {{ $s->name }} {{ $isOrigin ? '(Origin Supplier)' : '' }} {{ !$hasCreds ? '⚠️ (No API Keys)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="font-size: 0.75rem;">Select which supplier's connected WooCommerce store to publish this product to.</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold small text-dark mb-0">
                            Store Category
                        </label>
                        <div id="modalCategorySpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></div>
                    </div>
                    <select id="modalTargetCategorySelect" class="form-select rounded-3">
                        <option value="">-- Default ({{ $product->product_type ?: 'Garments' }}) --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->sno }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="font-size: 0.75rem;">Category created for this supplier in ERP. Defaults to "{{ $product->product_type ?: 'Garments' }}" if none selected.</small>
                </div>

                <div class="p-3 bg-primary-subtle bg-opacity-25 rounded-3 border border-primary-subtle text-secondary" style="font-size: 0.78rem;">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    Publishing will sync the AI-generated title, description, SEO meta tags, product attributes, pricing, stock, and approved AI images directly to WooCommerce.
                </div>

            </div>

            <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                
                <button type="button" id="btnModalConfirmPublish" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center" style="background-color: #006699; border-color: #006699;" onclick="executePublishFromModal()">
                    <i class="bi bi-cloud-arrow-up-fill me-1.5"></i> <span id="modalPublishBtnText">Confirm & Publish</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .fs-7 { font-size: 0.78rem; }
    .thumb-box {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .thumb-box.active {
        border-color: #006699 !important;
        box-shadow: 0 0 0 1px #006699;
    }
    .thumb-box:hover {
        border-color: #006699;
    }
    .simple-tab {
        border-bottom: 2.5px solid transparent !important;
    }
    .simple-tab.active {
        color: #111827 !important;
        border-bottom: 2.5px solid #111827 !important;
    }
</style>

<script>
// Published records indexed by target_supplier_id for fast lookup in JS
const publishedRecordsBySupplier = @json($publishedRecords->keyBy('target_supplier_id'));
const defaultProductType = @json($product->product_type ?: 'Garments');

document.addEventListener('DOMContentLoaded', function() {
    const mainImg = document.getElementById('mainImagePreview');
    const thumbBoxes = document.querySelectorAll('.thumb-box');
    const modalSupplierSelect = document.getElementById('modalTargetSupplierSelect');

    thumbBoxes.forEach(thumb => {
        thumb.addEventListener('click', function() {
            thumbBoxes.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const url = this.getAttribute('data-url');
            if (mainImg) mainImg.src = url;
        });
    });

    if (modalSupplierSelect) {
        modalSupplierSelect.addEventListener('change', function() {
            onModalSupplierChanged(this.value);
        });

        // Initialize state
        onModalSupplierChanged(modalSupplierSelect.value);
    }
});

function openPublishModalForSupplier(supplierId, preselectedCatId = null) {
    const modalSupplierSelect = document.getElementById('modalTargetSupplierSelect');
    if (modalSupplierSelect) {
        modalSupplierSelect.value = supplierId;
        onModalSupplierChanged(supplierId, preselectedCatId);
    }
    const modal = new bootstrap.Modal(document.getElementById('publishModal'));
    modal.show();
}

function onModalSupplierChanged(supplierId, preselectedCatId = null) {
    const modalCategorySelect = document.getElementById('modalTargetCategorySelect');
    const spinner = document.getElementById('modalCategorySpinner');
    const btnText = document.getElementById('modalPublishBtnText');

    const existingPublish = publishedRecordsBySupplier[supplierId];
    const selectedOpt = document.querySelector(`#modalTargetSupplierSelect option[value="${supplierId}"]`);
    const supplierName = selectedOpt ? selectedOpt.getAttribute('data-name') : 'Store';

    if (existingPublish && existingPublish.woocommerce_product_id) {
        btnText.textContent = 'Update on ' + (existingPublish.target_supplier_name || supplierName);
    } else {
        btnText.textContent = 'Publish to ' + supplierName;
    }

    if (!supplierId) {
        modalCategorySelect.innerHTML = `<option value="">-- Default (${defaultProductType}) --</option>`;
        return;
    }

    spinner.classList.remove('d-none');
    fetch(`/admin/publish-products/categories-by-supplier/${supplierId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(res => {
        spinner.classList.add('d-none');
        if (res.success && res.data) {
            let optionsHtml = `<option value="">-- Default (${defaultProductType}) --</option>`;
            res.data.forEach(cat => {
                const isSelected = (preselectedCatId && preselectedCatId == cat.sno) || (existingPublish && existingPublish.category_id == cat.sno) ? 'selected' : '';
                optionsHtml += `<option value="${cat.sno}" ${isSelected}>${escapeHtml(cat.name)}</option>`;
            });
            modalCategorySelect.innerHTML = optionsHtml;
        }
    })
    .catch(err => {
        spinner.classList.add('d-none');
        console.error('Error fetching categories:', err);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function executePublishFromModal() {
    const btn = document.getElementById('btnModalConfirmPublish');
    const supplierSelect = document.getElementById('modalTargetSupplierSelect');
    const categorySelect = document.getElementById('modalTargetCategorySelect');

    const targetSupplierId = supplierSelect.value;
    const categoryId = categorySelect.value;

    if (!targetSupplierId) {
        alert('Please select a Target Supplier / Store.');
        return;
    }

    const selectedOpt = supplierSelect.options[supplierSelect.selectedIndex];
    const supplierName = selectedOpt.getAttribute('data-name') || 'the selected store';
    const hasCreds = selectedOpt.getAttribute('data-has-creds') === '1';

    if (!hasCreds) {
        if (!confirm(`Warning: ${supplierName} appears to be missing WooCommerce API credentials. Do you still want to attempt publishing?`)) {
            return;
        }
    }

    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5" role="status"></span> Publishing...';

    fetch(`{{ route('admin.publish-products.publish', $product->spec_id) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            target_supplier_id: targetSupplierId,
            category_id: categoryId || null
        })
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            alert(res.message || 'Product published successfully to WooCommerce!');
            window.location.reload();
        } else {
            alert('Publishing Error: ' + (res.message || 'Failed to publish product.'));
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    })
    .catch(err => {
        console.error('Publish error:', err);
        alert('An unexpected network error occurred while publishing.');
        btn.disabled = false;
        btn.innerHTML = origHtml;
    });
}
</script>
@endsection
