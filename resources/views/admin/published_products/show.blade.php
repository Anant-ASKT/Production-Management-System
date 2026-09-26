@extends('layouts.app')

@section('title', 'Product Details - ' . ($product->clean_title ?: $product->sku))

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
            <a href="{{ route('admin.published-products.index') }}" class="text-decoration-none text-secondary fw-medium small d-inline-flex align-items-center">
                <span class="me-1.5">&larr;</span> Back to Published Products
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
                             alt="{{ $product->clean_title }}" 
                             class="img-fluid" 
                             style="max-height: 360px; max-width: 100%; object-fit: contain;">
                        
                        {{-- Zoom / Search Icon in Top Right --}}
                        <a href="{{ $primaryImgUrl }}" target="_blank" class="position-absolute top-0 end-0 m-3 text-secondary bg-white border rounded-circle d-flex align-items-center justify-content-center shadow-xs text-decoration-none" style="width: 34px; height: 34px;">
                            <i class="bi bi-search" style="font-size: 0.8rem;"></i>
                        </a>
                    </div>

                    {{-- Thumbnails Row --}}
                    @if($approvedImages->count() > 1)
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($approvedImages as $idx => $img)
                                @php $thumbUrl = normalizeImgUrl($img->enhanced_image_path); @endphp
                                <div class="thumb-box border rounded-2 p-1 bg-white {{ $idx === 0 ? 'active border-primary border-2' : '' }}" 
                                     data-url="{{ $thumbUrl }}" 
                                     style="width: 70px; height: 70px; cursor: pointer;"
                                     onclick="selectThumbnail('{{ $thumbUrl }}', this)">
                                    <img src="{{ $thumbUrl }}" class="w-100 h-100 object-fit-contain rounded-1" alt="Thumbnail {{ $idx + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

            {{-- RIGHT COLUMN: PRODUCT INFO & STORE PUBLISH STATUS --}}
            <div class="col-lg-7 col-xl-7">
                <div class="ps-lg-2">
                    
                    {{-- Breadcrumbs --}}
                    <div class="text-muted small mb-2">
                        <span>Home</span> / 
                        <span>{{ $product->product_type ?: 'Garments' }}</span> / 
                        <span class="text-secondary">{{ $product->clean_title }}</span>
                    </div>

                    {{-- Store & Status Badges --}}
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                            <i class="bi bi-check2-circle me-1"></i> Live on {{ $targetSupplier->name ?? 'Store' }}
                        </span>
                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1">
                            <i class="bi bi-shop me-1"></i> {{ $targetSupplier->domain ?? ($targetSupplier->store_url ?? 'Website') }}
                        </span>
                        @if($allPublications->count() > 1)
                            <div class="dropdown d-inline-block">
                                <button class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 dropdown-toggle text-decoration-none" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-diagram-3-fill me-1"></i> Also on {{ $allPublications->count() - 1 }} other store{{ $allPublications->count() > 2 ? 's' : '' }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2 small">
                                    <li class="dropdown-header text-uppercase fs-8 fw-bold">Published Stores</li>
                                    @foreach($allPublications as $p)
                                        <li>
                                            <a class="dropdown-item rounded-2 py-1.5 {{ $p->sno == $published->sno ? 'active fw-bold' : '' }}" href="{{ route('admin.published-products.show', $p->sno) }}">
                                                <i class="bi bi-shop me-1.5 text-primary"></i> {{ $p->store_name }} (#{{ $p->woocommerce_product_id }})
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Main Product Title --}}
                    <h3 class="fw-bold text-dark mb-3" style="font-size: 1.65rem;">
                        {{ $product->clean_title }}
                    </h3>

                    {{-- Price Line --}}
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        @if(!empty($product->sale_price))
                            <span class="fw-bold fs-4 text-dark">&#8377;{{ number_format($product->sale_price, 2) }}</span>
                            @if(!empty($product->price) && $product->price > $product->sale_price)
                                <span class="text-muted text-decoration-line-through small">&#8377;{{ number_format($product->price, 2) }}</span>
                            @endif
                        @elseif(!empty($product->price))
                            <span class="fw-bold fs-4 text-dark">&#8377;{{ number_format($product->price, 2) }}</span>
                        @else
                            <span class="fw-bold fs-4 text-dark">&#8377;0.00</span>
                        @endif

                        @if(!empty($product->min_price))
                            <span class="badge bg-warning-subtle text-dark border border-warning px-2.5 py-1.5 rounded-pill small fw-semibold">
                                <i class="bi bi-tag-fill text-warning me-1"></i> Min Price: &#8377;{{ number_format($product->min_price, 2) }}
                            </span>
                        @endif
                        <span class="text-muted small ms-1">+ Free Shipping</span>
                    </div>

                    {{-- Short Description --}}
                    <div class="text-secondary small mb-3 lh-base" style="max-width: 580px;">
                        {{ $shortDesc }}
                    </div>

                    {{-- Total Available Stock on Store & WooCommerce ID --}}
                    <div class="mb-4 small d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-dark fw-bold">Total Available Stock:</span>
                            @if($product->current_live_stock > 0)
                                <span class="badge bg-success-subtle text-success fs-7 fw-bold px-3 py-1.5 border border-success-subtle rounded-pill" id="liveStockBadge">
                                    <i class="bi bi-box-seam me-1"></i> <span id="liveStockQty">{{ $product->current_live_stock }}</span> in stock on Store
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-7 fw-bold px-3 py-1.5 border border-danger-subtle rounded-pill" id="liveStockBadge">
                                    <i class="bi bi-x-circle me-1"></i> <span id="liveStockQty">0</span> (Out of Stock)
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-dark fw-medium">WooCommerce ID:</span>
                            <span class="badge bg-light text-dark border font-monospace px-2.5 py-1">
                                #{{ $published->woocommerce_product_id }}
                            </span>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS (CLEAN: ONLY VIEW LIVE ON STORE & WITHDRAW STOCK) --}}
                    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                        @if(!empty($published->permalink))
                            <a href="{{ $published->permalink }}" target="_blank" class="btn btn-success px-4 py-2.5 fw-bold rounded-2 shadow-xs d-inline-flex align-items-center text-decoration-none" style="min-height: 44px;">
                                <i class="bi bi-box-arrow-up-right me-2 fs-6"></i> View Live on Store
                            </a>
                        @endif

                        <button type="button" 
                                class="btn btn-danger px-4 py-2.5 fw-bold rounded-2 shadow-xs d-inline-flex align-items-center text-decoration-none" 
                                style="min-height: 44px;"
                                id="btnOpenWithdrawModal" 
                                data-bs-toggle="modal" 
                                data-bs-target="#withdrawStockModal"
                                {{ $product->current_live_stock <= 0 ? 'disabled' : '' }}>
                            <i class="bi bi-box-arrow-down me-2 fs-6"></i> Withdraw Stock from WooCommerce
                        </button>
                    </div>

                    {{-- Product Metadata Attributes List --}}
                    <div class="small text-secondary pt-3 border-top d-flex flex-column gap-1.5">
                        <div><strong class="text-dark">SKU:</strong> <span class="font-monospace text-muted">{{ $product->sku ?: '—' }}</span></div>
                        <div><strong class="text-dark">Target Store:</strong> <span class="text-primary fw-semibold">{{ $targetSupplier->name ?? '—' }}</span> <span class="text-muted font-monospace">({{ $targetSupplier->domain ?? '' }})</span></div>
                        <div><strong class="text-dark">Store Category:</strong> <span>{{ $published->category_name ?: 'Default' }}</span></div>
                        <div><strong class="text-dark">Category / Type:</strong> <span>{{ $product->product_type ?: '—' }}</span></div>
                        <div><strong class="text-dark">Origin Supplier:</strong> <span>{{ $originSupplier->name ?? ($product->origin_supplier_name ?? '—') }}</span></div>
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
                        <div><strong class="text-dark">Published Date:</strong> <span class="text-muted">{{ \Carbon\Carbon::parse($published->created_at)->format('d M Y, h:i A') }}</span></div>
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
                    <button class="nav-link fw-semibold text-secondary px-0 me-4 py-2 border-0 bg-transparent simple-tab" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-pane" type="button" role="tab">
                        SEO Metadata
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-secondary px-0 py-2 border-0 bg-transparent simple-tab" id="store-tab" data-bs-toggle="tab" data-bs-target="#store-pane" type="button" role="tab">
                        Store Publication Info
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
                                    <th class="bg-light text-secondary text-uppercase fw-semibold" style="width: 240px;">Origin Supplier</th>
                                    <td class="fw-semibold text-dark">{{ $originSupplier->name ?? ($product->origin_supplier_name ?? '—') }}</td>
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
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Composition</th>
                                    <td>{{ $product->composition_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Designer</th>
                                    <td>{{ $product->designer_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Manufacturing Process</th>
                                    <td>{{ $product->manufacturing_process_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Embellishment</th>
                                    <td>{{ $product->embellishment_name ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Craftsman</th>
                                    <td>{{ $product->craftsman_name ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: SEO METADATA --}}
                <div class="tab-pane fade" id="seo-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0 bg-white">
                            <tbody>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold" style="width: 240px;">SEO Meta Title</th>
                                    <td class="text-dark">{{ $product->AI_Metatitle ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">SEO Meta Description</th>
                                    <td class="text-dark">{{ $product->AI_Metadescription ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Focus Keywords</th>
                                    <td class="text-dark">{{ $product->AI_Metakeywards ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Product Tags</th>
                                    <td class="text-dark">{{ $product->AI_Producttag ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Image Alt Text</th>
                                    <td class="text-dark font-monospace">{{ $product->AI_Imagealttext ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 4: STORE PUBLICATION INFO --}}
                <div class="tab-pane fade" id="store-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0 bg-white">
                            <tbody>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold" style="width: 240px;">Target Store</th>
                                    <td class="fw-bold text-dark">{{ $targetSupplier->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Store Website URL</th>
                                    <td>
                                        <a href="{{ $targetSupplier->store_url ?? '#' }}" target="_blank" class="text-primary font-monospace text-decoration-none">
                                            {{ $targetSupplier->store_url ?? '—' }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">WooCommerce Product ID</th>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2 py-0.5">#{{ $published->woocommerce_product_id }}</span></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Live Store Permalink</th>
                                    <td>
                                        @if(!empty($published->permalink))
                                            <a href="{{ $published->permalink }}" target="_blank" class="text-success text-decoration-none font-monospace">
                                                {{ $published->permalink }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Store Category</th>
                                    <td>{{ $published->category_name ?: 'Default' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Published Date</th>
                                    <td>{{ \Carbon\Carbon::parse($published->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-secondary text-uppercase fw-semibold">Last Updated</th>
                                    <td>{{ \Carbon\Carbon::parse($published->updated_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                                @if($publisher)
                                    <tr>
                                        <th class="bg-light text-secondary text-uppercase fw-semibold">Published By</th>
                                        <td>{{ $publisher->name }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

{{-- WITHDRAW STOCK MODAL --}}
<div class="modal fade" id="withdrawStockModal" tabindex="-1" aria-labelledby="withdrawStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-subtle text-danger p-2 rounded-3">
                        <i class="bi bi-box-arrow-down fs-5"></i>
                    </span>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="withdrawStockModalLabel">Withdraw Stock from WooCommerce</h6>
                        <small class="text-muted">Target Store: <strong>{{ $targetSupplier->name ?? 'Store' }}</strong> ({{ $targetSupplier->domain ?? '' }})</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Current Live Stock Card --}}
                <div class="p-3 bg-light rounded-3 border mb-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted fw-semibold">Current Live Stock on Website:</div>
                        <div class="fs-4 fw-bold text-dark mt-0.5">
                            <span id="modalCurrentStockText">{{ $product->current_live_stock }}</span> unit(s)
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1.5 rounded-pill">
                        WooCommerce ID #{{ $published->woocommerce_product_id }}
                    </span>
                </div>

                {{-- Quick Preset Buttons --}}
                <div class="mb-3">
                    <label class="small text-muted fw-semibold mb-1.5 d-block">Quick Presets:</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5" id="btnPreset1" onclick="setWithdrawPreset(1)">
                            Withdraw 1 Quantity
                        </button>
                        @if($product->current_live_stock > 1)
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5" id="btnPresetAll" onclick="setWithdrawPreset({{ $product->current_live_stock }})">
                                Withdraw All Quantity ({{ $product->current_live_stock }})
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Quantity Input --}}
                <div class="mb-3">
                    <label for="withdrawQtyInput" class="form-label small fw-semibold text-dark mb-1">
                        Quantity to Withdraw: <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary px-3" type="button" onclick="stepWithdrawQty(-1)">-</button>
                        <input type="number" 
                               id="withdrawQtyInput" 
                               class="form-control text-center fw-bold fs-5" 
                               min="1" 
                               max="{{ $product->current_live_stock }}" 
                               value="1">
                        <button class="btn btn-outline-secondary px-3" type="button" onclick="stepWithdrawQty(1)">+</button>
                    </div>
                    <div class="d-flex justify-content-between mt-1 text-muted small">
                        <span>Min: 1</span>
                        <span>Remaining stock on store: <strong class="text-primary" id="previewRemainingStock">{{ max(0, $product->current_live_stock - 1) }}</strong></span>
                    </div>
                </div>

                {{-- Reason / Notes --}}
                <div class="mb-3">
                    <label for="withdrawReasonInput" class="form-label small fw-semibold text-dark mb-1">
                        Reason / Notes (Optional):
                    </label>
                    <input type="text" 
                           id="withdrawReasonInput" 
                           class="form-control form-control-sm rounded-2" 
                           placeholder="e.g. Offline order, sample withdrawn, inventory adjustment">
                </div>

                <div class="alert alert-warning py-2 px-3 small rounded-3 mb-0 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-6"></i>
                    <div>This will immediately update the live stock quantity on WooCommerce ({{ $targetSupplier->domain ?? 'Store' }}).</div>
                </div>
            </div>

            <div class="modal-footer border-top py-2.5 px-4 bg-light bg-opacity-50">
                <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 btn-sm fw-bold shadow-xs" id="btnConfirmWithdraw">
                    <i class="bi bi-box-arrow-down me-1"></i> Confirm Withdrawal
                </button>
            </div>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .fs-7 { font-size: 0.8rem; }
    .fs-8 { font-size: 0.72rem; }
    .simple-tab {
        border-bottom: 2px solid transparent !important;
        color: #6c757d !important;
        font-size: 0.95rem;
    }
    .simple-tab.active {
        color: #172033 !important;
        border-bottom: 2px solid #172033 !important;
    }
    .thumb-box {
        transition: border-color 0.15s ease;
    }
    .thumb-box:hover {
        border-color: #006699 !important;
    }
</style>

{{-- JAVASCRIPT --}}
<script>
let currentStockQty = {{ (int) $product->current_live_stock }};

function selectThumbnail(url, el) {
    const mainImg = document.getElementById('mainImagePreview');
    if (mainImg) {
        mainImg.src = url;
    }
    document.querySelectorAll('.thumb-box').forEach(b => {
        b.classList.remove('active', 'border-primary', 'border-2');
    });
    if (el) {
        el.classList.add('active', 'border-primary', 'border-2');
    }
}

function setWithdrawPreset(qty) {
    const input = document.getElementById('withdrawQtyInput');
    if (!input) return;
    qty = Math.min(Math.max(1, qty), currentStockQty);
    input.value = qty;
    updateRemainingPreview();
}

function stepWithdrawQty(delta) {
    const input = document.getElementById('withdrawQtyInput');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    val = Math.min(Math.max(1, val + delta), currentStockQty);
    input.value = val;
    updateRemainingPreview();
}

function updateRemainingPreview() {
    const input = document.getElementById('withdrawQtyInput');
    const preview = document.getElementById('previewRemainingStock');
    if (!input || !preview) return;
    let qty = parseInt(input.value) || 1;
    let remaining = Math.max(0, currentStockQty - qty);
    preview.textContent = remaining + (remaining === 0 ? ' (Will be Out of Stock)' : ' unit(s)');
    if (remaining === 0) {
        preview.className = 'text-danger fw-bold';
    } else {
        preview.className = 'text-primary fw-bold';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('withdrawQtyInput');
    if (input) {
        input.addEventListener('input', function() {
            let val = parseInt(this.value);
            if (val > currentStockQty) {
                this.value = currentStockQty;
            } else if (val < 1 && this.value !== '') {
                this.value = 1;
            }
            updateRemainingPreview();
        });
    }

    const btnConfirmWithdraw = document.getElementById('btnConfirmWithdraw');
    const withdrawModalEl = document.getElementById('withdrawStockModal');
    const withdrawModal = withdrawModalEl ? new bootstrap.Modal(withdrawModalEl) : null;

    if (btnConfirmWithdraw) {
        btnConfirmWithdraw.addEventListener('click', function() {
            const qty = parseInt(document.getElementById('withdrawQtyInput').value) || 1;
            const reason = document.getElementById('withdrawReasonInput').value.trim();

            if (qty <= 0 || qty > currentStockQty) {
                alert(`Please enter a valid quantity between 1 and ${currentStockQty}.`);
                return;
            }

            btnConfirmWithdraw.disabled = true;
            btnConfirmWithdraw.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5" role="status"></span> Withdrawing from WooCommerce...`;

            fetch(`{{ route('admin.published-products.withdraw-stock', $published->sno) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    withdraw_qty: qty,
                    reason: reason
                })
            })
            .then(res => res.json())
            .then(res => {
                btnConfirmWithdraw.disabled = false;
                btnConfirmWithdraw.innerHTML = `<i class="bi bi-box-arrow-down me-1"></i> Confirm Withdrawal`;

                if (!res.success) {
                    alert(res.message || 'Withdrawal failed.');
                    return;
                }

                // Update current stock
                currentStockQty = res.new_stock;

                // Update page badge
                const liveStockBadge = document.getElementById('liveStockBadge');
                const liveStockQty = document.getElementById('liveStockQty');
                const btnOpenWithdrawModal = document.getElementById('btnOpenWithdrawModal');

                if (liveStockQty) {
                    liveStockQty.textContent = currentStockQty;
                }

                if (currentStockQty <= 0) {
                    if (liveStockBadge) {
                        liveStockBadge.className = 'badge bg-danger-subtle text-danger fs-7 fw-bold px-3 py-1.5 border border-danger-subtle rounded-pill';
                        liveStockBadge.innerHTML = `<i class="bi bi-x-circle me-1"></i> <span id="liveStockQty">0</span> (Out of Stock)`;
                    }
                    if (btnOpenWithdrawModal) {
                        btnOpenWithdrawModal.disabled = true;
                    }
                }

                // Update modal elements
                const modalCurrentStockText = document.getElementById('modalCurrentStockText');
                if (modalCurrentStockText) {
                    modalCurrentStockText.textContent = currentStockQty;
                }
                if (input) {
                    input.max = currentStockQty;
                    input.value = Math.min(1, currentStockQty);
                }

                if (withdrawModal) {
                    withdrawModal.hide();
                }

                // Show top success alert toast
                showToastNotification(res.message || 'Stock successfully withdrawn and updated on WooCommerce.');
            })
            .catch(err => {
                console.error(err);
                btnConfirmWithdraw.disabled = false;
                btnConfirmWithdraw.innerHTML = `<i class="bi bi-box-arrow-down me-1"></i> Confirm Withdrawal`;
                alert('A network or server error occurred while withdrawing stock.');
            });
        });
    }
});

function showToastNotification(msg) {
    const alertBox = document.createElement('div');
    alertBox.className = 'position-fixed bottom-0 end-0 p-3';
    alertBox.style.zIndex = '9999';
    alertBox.innerHTML = `
        <div class="toast show align-items-center text-bg-success border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i> ${msg}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="this.closest('.position-fixed').remove()"></button>
            </div>
        </div>
    `;
    document.body.appendChild(alertBox);
    setTimeout(() => {
        alertBox.remove();
    }, 6000);
}
</script>
@endsection

