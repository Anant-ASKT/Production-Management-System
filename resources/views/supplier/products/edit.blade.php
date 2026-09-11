@extends('layouts.supplier')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0">Edit Product</h4>
        <small class="text-muted">Update garment product details</small>
    </div>
    <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(!empty($isIntegrated))
    <!-- Integrated Supplier Notice & Mode Selector -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border-left: 5px solid #2563eb !important;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                        <i class="bi bi-patch-check-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">
                            You are an Integrated Supplier
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Integrated</span>
                        </h6>
                        <p class="mb-0 text-muted small">
                            You can choose to select attributes directly from the official Master Catalogs or type them manually.
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border">
                    <span class="small fw-semibold text-secondary me-1">Entry Mode:</span>
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input" type="radio" name="catalog_mode_switch" id="modeIntegrated" value="integrated" {{ old('catalog_mode', $product->is_integrated ? 'integrated' : 'manual') === 'integrated' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-primary" for="modeIntegrated" style="cursor:pointer;">
                            <i class="bi bi-collection-fill text-primary me-1"></i> Select From Masters
                        </label>
                    </div>
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input" type="radio" name="catalog_mode_switch" id="modeManual" value="manual" {{ old('catalog_mode', $product->is_integrated ? 'integrated' : 'manual') === 'manual' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-medium text-secondary" for="modeManual" style="cursor:pointer;">
                            <i class="bi bi-pencil-square me-1"></i> Manual Entry
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('supplier.products.update', $product->sno) }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="catalog_mode" id="catalog_mode_input" value="{{ !empty($isIntegrated) ? ($product->is_integrated ? 'integrated' : 'manual') : 'manual' }}">
            <input type="hidden" name="existing_spec_image" id="existing_spec_image_input" value="">
            
            @if(!empty($isIntegrated))
            {{-- CHOOSE SKU IN STARTING (ALL SUPPLIERS) --}}
            <div id="chooseSkuTopCard" class="card border-primary border-2 shadow-sm mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                                <i class="bi bi-upc-scan fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-primary">
                                    Step 1: Choose SKU
                                </h5>
                                <small class="text-muted">
                                    Select any SKU from stock (all suppliers) to auto-fill product specifications, prices, images, and master attributes below.
                                </small>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-primary px-3 py-2 fs-6">
                                <i class="bi bi-boxes me-1"></i> {{ count($vendorStockSkus) }} SKUs Available (All Suppliers)
                            </span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="select_vendor_sku" class="form-label fw-bold text-dark">
                            Choose SKU <span class="text-danger">*</span>
                        </label>
                        <select id="select_vendor_sku" class="form-select select2-master">
                            <option value="">-- Choose SKU to Auto-Fill Specifications --</option>
                            @php
                                $currentSku = old('product_sku', $product->product_sku ?? $product->intregated_sku);
                            @endphp
                            @foreach($vendorStockSkus as $stockItem)
                                <option value="{{ $stockItem->sku }}" data-spec="{{ json_encode($stockItem) }}" {{ $currentSku == $stockItem->sku ? 'selected' : '' }}>
                                    {{ $stockItem->sku }} @if(!empty($stockItem->item_name_text)) - {{ $stockItem->item_name_text }} @endif @if(!empty($stockItem->supplier_name)) [Supplier: {{ $stockItem->supplier_name }}] @endif (Available Stock: {{ $stockItem->available_stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="sku_auto_fill_alert" class="alert alert-success mt-3 mb-0 py-2 d-none align-items-center shadow-sm">
                        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                        <div>
                            <strong>Specifications Auto-Filled!</strong>
                            <span id="sku_auto_fill_msg">Attributes and details for this SKU have been populated into the form below.</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <!-- Media section -->
                <div class="col-md-12 mb-3">
                    <h5 class="text-primary border-bottom pb-2">
                        <i class="bi bi-images me-1"></i> Media
                    </h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Main Image</label>
                    <input type="file" name="main_image" class="form-control mb-1" accept="image/*" id="mainImageInput">
                    <div id="mainImageNotice" class="small mb-1"></div>
                    <div id="mainImagePreviewContainer" class="{{ $product->main_image ? '' : 'd-none' }} position-relative d-inline-block">
                        <img src="{{ $product->main_image ? asset($product->main_image) : '' }}" id="mainImagePreview" class="rounded border shadow-sm" style="height: 150px; object-fit: cover;">
                        @if($product->main_image)
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 delete-image-btn" style="width: 24px; height: 24px; line-height: 1;" data-type="main" data-path="{{ $product->main_image }}">&times;</button>
                        @endif
                    </div>
                    <small class="text-muted d-block mt-1">Leave empty to keep existing image.</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Additional Images</label>
                    <input type="file" name="sub_images[]" class="form-control mb-1" accept="image/*" multiple id="subImagesInput">
                    <div id="subImagesNotice" class="small mb-1"></div>
                    <div id="subImagesPreviewContainer" class="d-flex gap-2 flex-wrap {{ $product->sub_images ? '' : 'd-none' }}">
                        @if($product->sub_images)
                            @php
                                $subImages = json_decode($product->sub_images, true) ?? [];
                            @endphp
                            @foreach($subImages as $img)
                                <div class="position-relative d-inline-block existing-sub-img-container">
                                    <img src="{{ asset($img) }}" alt="Sub Image" class="rounded border shadow-sm existing-sub-img" style="height: 100px; width: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 delete-image-btn" style="width: 20px; height: 20px; line-height: 1;" data-type="sub" data-path="{{ $img }}">&times;</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <small class="text-muted d-block mt-1">New images will be added to existing ones.</small>
                </div>

                <!-- Basic Details section -->
                <div class="col-md-12 mb-3 mt-3">
                    <h5 class="text-primary border-bottom pb-2">
                        <i class="bi bi-info-circle me-1"></i> Basic Details
                    </h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Product Name *</label>
                    <input type="text" name="name" id="product_name_input" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Product SKU</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-upc"></i></span>
                        <input type="text" name="product_sku" id="product_sku_input" class="form-control" value="{{ old('product_sku', $product->product_sku ?? $product->intregated_sku) }}" placeholder="e.g. SKU-12345">
                    </div>
                    <small class="text-muted">Auto-filled when selecting from Vendor Stock, or enter manually.</small>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Minimum Price</label>
                    <input type="number" step="0.01" name="min_price" class="form-control" value="{{ old('min_price', $product->min_price) }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">
                        Stock <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" id="total_available_stock_badge">Total Available: <span id="total_available_stock_count">{{ $vendorStockSkus->firstWhere('sku', old('product_sku', $product->product_sku ?? $product->intregated_sku))->available_stock ?? 0 }}</span></span>
                    </label>
                    <input type="number" name="stock" id="stock_input" class="form-control" value="{{ old('stock', $product->stock ?: 1) }}" min="1">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                @if(!empty($isIntegrated))
                <!-- =========================================================
                     INTEGRATED MASTER ATTRIBUTES SECTION
                ========================================================= -->
                <div id="integratedAttributesSection" class="col-md-12">
                    <div class="border-top pt-3 mt-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="text-primary m-0">
                                <i class="bi bi-collection me-1"></i> Attributes (Select From Official Masters)
                            </h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="bi bi-link-45deg me-1"></i> System Integrated
                            </span>
                        </div>
                        <small class="text-muted d-block mt-1">Select standardized values from the official system master catalogs.</small>
                    </div>

                    <div class="row">
                        {{-- Item Name --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Item Name *</label>
                            <select name="item_name" id="master_item_name" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['itemNames'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->itemname }}" data-code="{{ $m->code }}" {{ old('item_name', $product->item_name) == $m->id ? 'selected' : '' }}>
                                        {{ $m->itemname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Item Type --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Item Type *</label>
                            <select name="item_type" id="master_item_type" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['itemTypes'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->itemtype }}" data-code="{{ $m->code }}" {{ old('item_type', $product->item_type) == $m->id ? 'selected' : '' }}>
                                        {{ $m->itemtype }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Designer --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Designer *</label>
                            <select name="designer" id="master_designer" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['designers'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->designername }}" data-code="{{ $m->code }}" {{ old('designer', $product->designer) == $m->id ? 'selected' : '' }}>
                                        {{ $m->designername }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Gender *</label>
                            <select name="gender" id="master_gender" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['genders'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-code="{{ $m->code }}" {{ old('gender', $product->gender) == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Composition --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Composition *</label>
                            <select name="composition" id="master_composition" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['compositions'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->composition_details }}" data-code="{{ $m->code }}" {{ old('composition', $product->composition) == $m->id ? 'selected' : '' }}>
                                        {{ $m->composition_details }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Colour --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Colour *</label>
                            <select name="colour" id="master_colour" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['colours'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->colourname }}" data-code="{{ $m->code }}" {{ old('colour', $product->colour) == $m->id ? 'selected' : '' }}>
                                        {{ $m->colourname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Yarn Name --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Yarn Name</label>
                            <select name="yarn" id="master_yarn" class="form-select select2-master">
                                <option value="">Select an option</option>
                                @foreach($masters['yarns'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->yarnname }}" data-code="{{ $m->code }}" {{ old('yarn', $product->yarn) == $m->id ? 'selected' : '' }}>
                                        {{ $m->yarnname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Size --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Size *</label>
                            <select name="size" id="master_size" class="form-select select2-master" required>
                                <option value="">Select an option</option>
                                @foreach($masters['sizes'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->size }}" data-code="{{ $m->code }}" {{ old('size', $product->size) == $m->id ? 'selected' : '' }}>
                                        {{ $m->size }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Embellishment --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Embellishment</label>
                            <select name="embellishment" id="master_embellishment" class="form-select select2-master">
                                <option value="">Select an option</option>
                                @foreach($masters['embellishments'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->embellishmentname }}" data-code="{{ $m->code }}" {{ old('embellishment', $product->embellishment) == $m->id ? 'selected' : '' }}>
                                        {{ $m->embellishmentname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Manufacturing Process --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Manufacturing Process</label>
                            <select name="manufacturing_process" id="master_manufacturing_process" class="form-select select2-master">
                                <option value="">Select an option</option>
                                @foreach($masters['manufacturingProcesses'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->manufacturing_process }}" data-code="{{ $m->code }}" {{ old('manufacturing_process', $product->manufacturing_process) == $m->id ? 'selected' : '' }}>
                                        {{ $m->manufacturing_process }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Craftsman --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Craftsman</label>
                            <select name="craftsman" id="master_craftsman" class="form-select select2-master">
                                <option value="">Select an option</option>
                                @foreach($masters['craftsmen'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-code="{{ $m->code }}" {{ old('craftsman', $product->craftsman) == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Manufacture --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Manufacture</label>
                            <select name="manufacture" id="master_manufacture" class="form-select select2-master">
                                <option value="">Select an option</option>
                                @foreach($masters['manufactures'] ?? [] as $m)
                                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-code="{{ $m->code }}" {{ old('manufacture', $product->manufacture) == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Collection --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Collection</label>
                            <input type="text" name="collection" class="form-control" value="{{ old('collection', $product->collection) }}" placeholder="e.g. Summer 2026">
                        </div>
                    </div>
                </div>
                @endif

                <!-- =========================================================
                     MANUAL ATTRIBUTES SECTION (Simple Text)
                ========================================================= -->
                <div id="manualAttributesSection" class="col-md-12 {{ (!empty($isIntegrated) && $product->is_integrated) ? 'd-none' : '' }}">
                    <div class="border-top pt-3 mt-3 mb-3">
                        <h5 class="text-primary m-0">
                            <i class="bi bi-pen me-1"></i> Attributes (Simple Text)
                        </h5>
                        <small class="text-muted d-block mt-1">Please type manual text values for attributes.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Colour</label>
                            <input type="text" name="colour" class="form-control manual-input" value="{{ old('colour', $product->colour) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Size</label>
                            <input type="text" name="size" class="form-control manual-input" value="{{ old('size', $product->size) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Item Type</label>
                            <input type="text" name="item_type" class="form-control manual-input" value="{{ old('item_type', $product->item_type) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <input type="text" name="gender" class="form-control manual-input" value="{{ old('gender', $product->gender) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Composition</label>
                            <input type="text" name="composition" class="form-control manual-input" value="{{ old('composition', $product->composition) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Compositions (Misc)</label>
                            <input type="text" name="compositions" class="form-control manual-input" value="{{ old('compositions', $product->compositions) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Designer</label>
                            <input type="text" name="designer" class="form-control manual-input" value="{{ old('designer', $product->designer) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Designers (Misc)</label>
                            <input type="text" name="designers" class="form-control manual-input" value="{{ old('designers', $product->designers) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Design Names</label>
                            <input type="text" name="design_names" class="form-control manual-input" value="{{ old('design_names', $product->design_names) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Collection</label>
                            <input type="text" name="collection" class="form-control manual-input" value="{{ old('collection', $product->collection) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Embellishment</label>
                            <input type="text" name="embellishment" class="form-control manual-input" value="{{ old('embellishment', $product->embellishment) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Manufacture</label>
                            <input type="text" name="manufacture" class="form-control manual-input" value="{{ old('manufacture', $product->manufacture) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Manufacturing Process</label>
                            <input type="text" name="manufacturing_process" class="form-control manual-input" value="{{ old('manufacturing_process', $product->manufacturing_process) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Mfg Processes (Misc)</label>
                            <input type="text" name="mfg_processes" class="form-control manual-input" value="{{ old('mfg_processes', $product->mfg_processes) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Craftsman</label>
                            <input type="text" name="craftsman" class="form-control manual-input" value="{{ old('craftsman', $product->craftsman) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Craftsmen (Misc)</label>
                            <input type="text" name="craftsmen" class="form-control manual-input" value="{{ old('craftsmen', $product->craftsmen) }}">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Update Product
                </button>
                <a href="{{ route('supplier.products.index') }}" class="btn btn-light border">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 4px 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .select2-container {
        width: 100% !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainInput = document.getElementById('mainImageInput');
        const mainPreviewContainer = document.getElementById('mainImagePreviewContainer');
        const mainPreview = document.getElementById('mainImagePreview');
        const mainNotice = document.getElementById('mainImageNotice');

        if (mainInput) {
            mainInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                    if (file.size > 2 * 1024 * 1024) {
                        if (mainNotice) mainNotice.innerHTML = `<span class="text-primary fw-semibold"><i class="bi bi-arrow-down-circle me-1"></i>Selected size: ${sizeMb} MB &mdash; will be automatically compressed under 2 MB when saved.</span>`;
                    } else {
                        if (mainNotice) mainNotice.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>Selected size: ${sizeMb} MB (OK)</span>`;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        mainPreview.src = e.target.result;
                        mainPreviewContainer.classList.remove('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    if (mainNotice) mainNotice.innerHTML = '';
                    @if(!$product->main_image)
                    mainPreviewContainer.classList.add('d-none');
                    @else
                    mainPreview.src = "{{ asset($product->main_image) }}";
                    @endif
                }
            });
        }

        const subInput = document.getElementById('subImagesInput');
        const subPreviewContainer = document.getElementById('subImagesPreviewContainer');
        const subNotice = document.getElementById('subImagesNotice');

        if (subInput) {
            subInput.addEventListener('change', function(e) {
                const newPreviews = subPreviewContainer.querySelectorAll('.new-sub-img');
                newPreviews.forEach(img => img.remove());
                
                if (e.target.files.length > 0) {
                    subPreviewContainer.classList.remove('d-none');
                    let oversizedCount = 0;
                    Array.from(e.target.files).forEach(file => {
                        if (file.size > 2 * 1024 * 1024) oversizedCount++;
                    });
                    if (oversizedCount > 0) {
                        if (subNotice) subNotice.innerHTML = `<span class="text-primary fw-semibold"><i class="bi bi-arrow-down-circle me-1"></i>${oversizedCount} image(s) exceed 2 MB &mdash; will be automatically compressed under 2 MB when saved.</span>`;
                    } else {
                        if (subNotice) subNotice.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>All selected images are within 2 MB.</span>`;
                    }
                    
                    Array.from(e.target.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'rounded border shadow-sm new-sub-img';
                            img.style.height = '100px';
                            img.style.width = '100px';
                            img.style.objectFit = 'cover';
                            subPreviewContainer.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                } else {
                    if (subNotice) subNotice.innerHTML = '';
                    @if(!$product->sub_images)
                    subPreviewContainer.classList.add('d-none');
                    @endif
                }
            });
        }

        // Handle deletion of existing images
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this image?')) return;
                
                const type = this.dataset.type;
                const path = this.dataset.path;
                const container = type === 'main' ? document.getElementById('mainImagePreviewContainer') : this.closest('.existing-sub-img-container');
                const btnElement = this;
                
                fetch("{{ route('supplier.products.delete-image', $product->sno) }}", {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: type, image_path: path })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (type === 'main') {
                            container.classList.add('d-none');
                            document.getElementById('mainImagePreview').src = '';
                            btnElement.remove();
                        } else {
                            container.remove();
                        }
                    } else {
                        alert('Failed to delete image: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the image.');
                });
            });
        });

        // Initialize Select2 on master selects
        function initSelect2() {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2-master').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: 'Select an option',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
            }
        }

        @if(!empty($isIntegrated))
            initSelect2();

            // When Item Name is selected from the master dropdown, auto-fill Product Name if blank
            const masterItemName = $('#master_item_name');
            const productNameInput = $('#product_name_input');
            let userTypedName = false;

            productNameInput.on('input', function() {
                userTypedName = $(this).val().trim().length > 0;
            });

            masterItemName.on('change', function() {
                const selectedText = $(this).find('option:selected').data('name');
                if (selectedText && (!userTypedName || productNameInput.val().trim() === '')) {
                    productNameInput.val(selectedText);
                }
            });

            // Toggle between Integrated Masters and Manual Entry modes
            const modeRadios = document.querySelectorAll('input[name="catalog_mode_switch"]');
            const integratedSection = document.getElementById('integratedAttributesSection');
            const manualSection = document.getElementById('manualAttributesSection');

            function updateMode(mode) {
                $('#catalog_mode_input').val(mode);
                if (mode === 'integrated') {
                    $('#chooseSkuTopCard').removeClass('d-none');
                    if (integratedSection) integratedSection.classList.remove('d-none');
                    if (manualSection) manualSection.classList.add('d-none');

                    // Enable integrated inputs, disable manual inputs
                    $('#integratedAttributesSection select, #integratedAttributesSection input').prop('disabled', false);
                    $('#select_vendor_sku').prop('disabled', false);
                    $('#manualAttributesSection input').prop('disabled', true);
                    initSelect2();
                } else {
                    $('#chooseSkuTopCard').addClass('d-none');
                    if (integratedSection) integratedSection.classList.add('d-none');
                    if (manualSection) manualSection.classList.remove('d-none');

                    // Disable integrated inputs, enable manual inputs
                    $('#integratedAttributesSection select, #integratedAttributesSection input').prop('disabled', true);
                    $('#select_vendor_sku').prop('disabled', true);
                    $('#manualAttributesSection input').prop('disabled', false);
                }
            }

            modeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateMode(this.value);
                });
            });

            // When SKU is selected from Vendor Stock dropdown, auto-select all masters and fill fields
            $('#select_vendor_sku').on('change', function() {
                const selectedVal = $(this).val();
                if (!selectedVal) {
                    $('#sku_auto_fill_alert').removeClass('d-flex').addClass('d-none');
                    return;
                }

                const selectedOption = $(this).find('option:selected');
                const specRaw = selectedOption.attr('data-spec');
                if (!specRaw) return;

                let spec;
                try {
                    spec = JSON.parse(specRaw);
                } catch(e) {
                    console.error('Error parsing spec JSON:', e);
                    return;
                }

                // Helper to set Select2 value or add option if missing
                function setSelect2Val(selectElem, val, text) {
                    if (val === undefined || val === null || val === '') return;
                    val = String(val);
                    if (selectElem.find("option[value='" + val + "']").length) {
                        selectElem.val(val).trigger('change');
                    } else if (text) {
                        const newOpt = new Option(text, val, true, true);
                        selectElem.append(newOpt).trigger('change');
                    }
                }

                // Auto-fill SKU input
                if (spec.sku) {
                    $('#product_sku_input').val(spec.sku);
                }

                // Auto-select Master Dropdowns
                if (spec.item_name) {
                    setSelect2Val($('#master_item_name'), spec.item_name, spec.item_name_text);
                }
                if (spec.item_type) {
                    setSelect2Val($('#master_item_type'), spec.item_type);
                }
                if (spec.designer) {
                    setSelect2Val($('#master_designer'), spec.designer);
                }
                if (spec.gender) {
                    setSelect2Val($('#master_gender'), spec.gender);
                }
                if (spec.composition) {
                    setSelect2Val($('#master_composition'), spec.composition);
                }
                if (spec.colour) {
                    setSelect2Val($('#master_colour'), spec.colour);
                }
                if (spec.yarn) {
                    setSelect2Val($('#master_yarn'), spec.yarn);
                }
                if (spec.size) {
                    setSelect2Val($('#master_size'), spec.size);
                }
                if (spec.embellishment) {
                    setSelect2Val($('#master_embellishment'), spec.embellishment);
                }
                if (spec.manufacturing_process) {
                    setSelect2Val($('#master_manufacturing_process'), spec.manufacturing_process);
                }
                if (spec.craftsman !== undefined && spec.craftsman !== null) {
                    setSelect2Val($('#master_craftsman'), spec.craftsman);
                }
                if (spec.manufacture) {
                    setSelect2Val($('#master_manufacture'), spec.manufacture);
                }

                // Auto-fill Product Name
                if (spec.item_name_text) {
                    $('#product_name_input').val(spec.item_name_text);
                } else if (spec.sku) {
                    $('#product_name_input').val(spec.sku);
                }

                // Auto-fill Prices & Stock
                if (spec.price) {
                    $('input[name="price"]').val(spec.price);
                }
                if (spec.sale_price) {
                    $('input[name="sale_price"]').val(spec.sale_price);
                }
                if (spec.min_price) {
                    $('input[name="min_price"]').val(spec.min_price);
                }
                
                // Update total available stock display after label
                let availStock = 0;
                if (spec.available_stock !== undefined && spec.available_stock !== null) {
                    availStock = parseInt(spec.available_stock, 10) || 0;
                } else if (spec.total_stock) {
                    availStock = parseInt(spec.total_stock, 10) || 0;
                }
                $('#total_available_stock_count').text(availStock);

                // Set stock input to total available for this SKU, or default to 1 (not 0)
                let stockQty = availStock > 0 ? availStock : 1;
                $('input[name="stock"]').val(stockQty);

                // Auto-fill Image preview if present
                if (spec.img_path) {
                    try {
                        let imgArr = typeof spec.img_path === 'string' && spec.img_path.startsWith('[') ? JSON.parse(spec.img_path) : spec.img_path;
                        let firstImg = Array.isArray(imgArr) ? imgArr[0] : spec.img_path;
                        if (firstImg) {
                            let imgUrl = firstImg.startsWith('http') ? firstImg : ('/' + firstImg.replace(/^\/+/, ''));
                            $('#mainImagePreview').attr('src', imgUrl);
                            $('#mainImagePreviewContainer').removeClass('d-none');
                            $('#existing_spec_image_input').val(firstImg);
                        }
                    } catch(e) {
                        console.warn('Could not parse img_path', e);
                    }
                }

                // Show confirmation alert
                $('#sku_auto_fill_msg').text(`Specifications for SKU "${spec.sku}" have been auto-populated.`);
                $('#sku_auto_fill_alert').removeClass('d-none').addClass('d-flex');
            });

            // If user types in Product SKU input, sync with Vendor Stock select if available
            $('#product_sku_input').on('change', function() {
                const typedSku = $(this).val().trim();
                if (typedSku && $('#select_vendor_sku').val() !== typedSku) {
                    const match = $('#select_vendor_sku').find("option[value='" + typedSku + "']");
                    if (match.length) {
                        $('#select_vendor_sku').val(typedSku).trigger('change');
                    }
                }
            });

            // Set initial state
            updateMode('{{ $product->is_integrated ? "integrated" : "manual" }}');
        @endif
    });
</script>
@endpush
@endsection
