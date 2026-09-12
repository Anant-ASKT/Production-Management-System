@extends('layouts.supplier')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<style>
    .filter-card,
    .result-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background: #fff;
    }

    .filter-card .card-header,
    .result-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 12px 16px;
    }

    .filter-card .card-body {
        padding: 16px;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .product-table-search {
        padding: 12px 16px;
        border-bottom: 1px solid #dee2e6;
        background: #fff;
    }

    .product-table-search input {
        min-height: 38px;
    }

    .product-table-search .btn {
        min-height: 38px;
    }

    .product-image-cell {
        width: 85px;
        text-align: center;
    }

    .product-table-image {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }

    .no-product-image {
        width: 52px;
        height: 52px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #adb5bd;
        background: #f8f9fa;
        font-size: 16px;
        margin: 0 auto;
    }

    .no-product-image span {
        font-size: 8px;
        margin-top: 2px;
    }

    .stock-qty-badge {
        font-weight: 700;
        font-size: 12px;
    }

    .spec-row-selected {
        background-color: #f0f7ff !important;
    }
</style>
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
    <!-- Direct Big Selection Buttons (Different Colors) -->
    <div class="d-flex flex-wrap gap-3 mb-4">
        <div>
            <input type="radio" class="btn-check" name="catalog_mode_switch" id="modeIntegrated" value="integrated" autocomplete="off" {{ old('catalog_mode', $product->is_integrated ? 'integrated' : 'manual') === 'integrated' ? 'checked' : '' }}>
            <label class="btn btn-outline-primary btn-lg px-4 py-3 fw-bold d-flex align-items-center gap-3 shadow-sm rounded-3 fs-5" for="modeIntegrated" style="cursor: pointer; border-width: 2px;">
                <i class="bi bi-card-checklist fs-3"></i>
                <span>Choose from Ready List</span>
            </label>
        </div>
        <div>
            <input type="radio" class="btn-check" name="catalog_mode_switch" id="modeManual" value="manual" autocomplete="off" {{ old('catalog_mode', $product->is_integrated ? 'integrated' : 'manual') === 'manual' ? 'checked' : '' }}>
            <label class="btn btn-outline-success btn-lg px-4 py-3 fw-bold d-flex align-items-center gap-3 shadow-sm rounded-3 fs-5" for="modeManual" style="cursor: pointer; border-width: 2px;">
                <i class="bi bi-plus-circle fs-3"></i>
                <span>Add New Product</span>
            </label>
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
            {{-- CHOOSE SKU / SPECIFICATION AUTO-FILL & FILTERS --}}
            @php
                $currentSku = old('product_sku', $product->product_sku ?? $product->intregated_sku);
            @endphp
            <div id="chooseSkuTopCard" class="mb-4">
                <!-- Hidden input to store chosen SKU for form submission -->
                <input type="hidden" name="selected_vendor_sku" id="select_vendor_sku" value="{{ $currentSku }}">

                {{-- FILTER CARD --}}
                <div class="card shadow-sm filter-card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>
                            <i class="bi bi-funnel me-1"></i>
                            Filters
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            {{-- ITEM TYPE --}}
                            <div class="col-md-3">
                                <label for="filterItemType" class="form-label filter-label">
                                    Item Type
                                </label>
                                <select id="filterItemType" class="form-select select2-master">
                                    <option value="">All Item Types</option>
                                    @if(isset($masters['itemTypes']))
                                        @foreach($masters['itemTypes'] as $itemType)
                                            <option value="{{ $itemType->id }}">
                                                {{ $itemType->itemtype }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- ITEM NAME --}}
                            <div class="col-md-3">
                                <label for="filterItemName" class="form-label filter-label">
                                    Item Name
                                </label>
                                <select id="filterItemName" class="form-select select2-master">
                                    <option value="">All Item Names</option>
                                    @if(isset($masters['itemNames']))
                                        @foreach($masters['itemNames'] as $itemName)
                                            <option value="{{ $itemName->id }}">
                                                {{ $itemName->itemname }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- COMPOSITION --}}
                            <div class="col-md-3">
                                <label for="filterComposition" class="form-label filter-label">
                                    Composition
                                </label>
                                <select id="filterComposition" class="form-select select2-master">
                                    <option value="">All Compositions</option>
                                    @if(isset($masters['compositions']))
                                        @foreach($masters['compositions'] as $composition)
                                            <option value="{{ $composition->id }}">
                                                {{ $composition->composition_details }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- GENDER --}}
                            <div class="col-md-3">
                                <label for="filterGender" class="form-label filter-label">
                                    Gender Type
                                </label>
                                <select id="filterGender" class="form-select select2-master">
                                    <option value="">All Gender Types</option>
                                    @if(isset($masters['genders']))
                                        @foreach($masters['genders'] as $gender)
                                            <option value="{{ $gender->id }}">
                                                {{ $gender->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="btnApplyFilters" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Apply Filters
                            </button>

                            <button type="button" id="btnClearFilters" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RESULT CARD --}}
                <div class="card shadow-sm result-card mb-4" id="specTableCard">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>
                            <i class="bi bi-list-ul me-1"></i>
                            Ready Products List
                        </strong>

                        <span id="totalProducts" class="badge bg-primary">0</span>
                    </div>

                    <div class="card-body p-0">
                        {{-- TABLE SEARCH --}}
                        <div class="product-table-search">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text"
                                               id="productTableSearch"
                                               class="form-control"
                                               placeholder="Search by name, SKU, color, size, type..."
                                               autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-auto">
                                    <button type="button"
                                            id="btnResetTableSearch"
                                            class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                    </button>
                                </div>

                                <div class="col-md-auto">
                                    <small id="productTableSearchInfo" class="text-muted"></small>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                            <table class="table table-bordered table-hover mb-0 align-middle" id="productSpecificationTable">
                                <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 2;">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th class="text-center" style="width: 85px;">Image</th>
                                        <th>Item Type</th>
                                        <th>Item Name</th>
                                        <th>Composition</th>
                                        <th>Gender Type</th>
                                        <th class="text-end" style="width: 110px;">Stock Qty</th>
                                        <th class="text-center" style="width: 110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productSpecificationTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading specifications...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="productFormFieldsStart"></div>
            <div id="sku_auto_fill_alert" class="alert alert-success alert-dismissible fade show d-none align-items-center mb-4 py-2" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="sku_auto_fill_msg">Specification auto-filled to product form.</span>
                <button type="button" class="btn-close ms-auto py-2" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <h5 class="text-primary border-bottom pb-2 mt-3 mb-3">
                        <i class="bi bi-collection me-1"></i> Master Attributes
                    </h5>

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
                    <h5 class="text-primary border-bottom pb-2 mt-3 mb-3">
                        <i class="bi bi-pencil-square me-1"></i> Product Attributes
                    </h5>

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

            // =========================================================================
            // PRODUCT SPECIFICATION MASTER - FILTERS, TABLE SEARCH & AUTO-FILL
            // =========================================================================
            const filterItemType = $('#filterItemType');
            const filterItemName = $('#filterItemName');
            const filterComposition = $('#filterComposition');
            const filterGender = $('#filterGender');
            const btnApplyFilters = $('#btnApplyFilters');
            const btnClearFilters = $('#btnClearFilters');
            const productTableSearch = $('#productTableSearch');
            const btnResetTableSearch = $('#btnResetTableSearch');
            const productTableSearchInfo = $('#productTableSearchInfo');
            const tableBody = $('#productSpecificationTableBody');
            const totalProductsBadge = $('#totalProducts');
            const hiddenSkuInput = $('#select_vendor_sku');
            const skuAutoFillAlert = $('#sku_auto_fill_alert');
            const skuAutoFillMsg = $('#sku_auto_fill_msg');

            let loadedSpecifications = [];
            let currentSelectedSku = hiddenSkuInput.val() || '';

            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

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

            function renderSpecificationTable(items) {
                tableBody.empty();
                totalProductsBadge.text(items ? items.length : 0);

                if (!items || items.length === 0) {
                    tableBody.html(`
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam" style="font-size: 32px;"></i>
                                <div class="mt-2 fw-semibold">No product specification stock found.</div>
                                <small class="text-muted">Try adjusting your filters above.</small>
                            </td>
                        </tr>
                    `);
                    productTableSearchInfo.text('');
                    return;
                }

                items.forEach((item, index) => {
                    const isSelected = currentSelectedSku && item.sku === currentSelectedSku;
                    const availStock = (item.available_stock !== undefined && item.available_stock !== null)
                        ? parseInt(item.available_stock, 10)
                        : (parseInt(item.total_stock, 10) || 0);

                    let imageHtml = '';
                    if (item.image_url) {
                        imageHtml = `
                            <img src="${escapeHtml(item.image_url)}"
                                 alt="Product Image"
                                 class="product-table-image"
                                 loading="lazy"
                                 onerror="this.outerHTML='<div class=&quot;no-product-image&quot;><i class=&quot;bi bi-image&quot;></i><span>Unavailable</span></div>';">
                        `;
                    } else {
                        imageHtml = `
                            <div class="no-product-image">
                                <i class="bi bi-image"></i>
                                <span>No Image</span>
                            </div>
                        `;
                    }

                    const actionHtml = isSelected
                        ? `<button type="button" class="btn btn-sm btn-success disabled">
                               <i class="bi bi-check-lg me-1"></i> Selected
                           </button>`
                        : `<button type="button" class="btn btn-sm btn-primary js-choose-spec" data-index="${index}">
                               <i class="bi bi-check2-circle me-1"></i> Choose
                           </button>`;

                    const tr = $(`
                        <tr class="${isSelected ? 'table-primary spec-row-selected' : ''}" data-spec-id="${item.spec_id}">
                            <td>${index + 1}</td>
                            <td class="product-image-cell">${imageHtml}</td>
                            <td>${escapeHtml(item.item_type_text || '-')}</td>
                            <td>
                                <div class="fw-bold text-dark">${escapeHtml(item.item_name_text || '-')}</div>
                                <div class="small text-muted font-monospace">${escapeHtml(item.sku || '')}</div>
                            </td>
                            <td>${escapeHtml(item.composition_text || '-')}</td>
                            <td>${escapeHtml(item.gender_text || '-')}</td>
                            <td class="text-end">
                                <span class="badge ${availStock > 0 ? 'bg-success' : 'bg-secondary'} stock-qty-badge">
                                    ${availStock}
                                </span>
                            </td>
                            <td class="text-center">${actionHtml}</td>
                        </tr>
                    `);

                    tableBody.append(tr);
                });

                applyClientTableSearch();
            }

            function fetchSpecifications() {
                tableBody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Loading product specifications...
                        </td>
                    </tr>
                `);

                const params = {
                    item_type: filterItemType.val() || '',
                    item_name: filterItemName.val() || '',
                    composition: filterComposition.val() || '',
                    gender: filterGender.val() || '',
                    q: productTableSearch.val().trim()
                };

                $.ajax({
                    url: '{{ route("supplier.products.search-specifications") }}',
                    method: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(response) {
                        loadedSpecifications = Array.isArray(response) ? response : (response.data || []);
                        renderSpecificationTable(loadedSpecifications);
                    },
                    error: function(err) {
                        console.error('Error fetching specifications:', err);
                        tableBody.html(`
                            <tr>
                                <td colspan="8" class="text-center text-danger py-4">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Failed to load product specifications. Please try again.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            function applyClientTableSearch() {
                const query = productTableSearch.val().trim().toLowerCase();
                const rows = tableBody.find('tr').filter(function() {
                    return $(this).find('td').length === 8;
                });

                let visibleCount = 0;
                rows.each(function() {
                    const rowText = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                    const match = !query || rowText.indexOf(query) !== -1;
                    $(this).toggle(match);
                    if (match) visibleCount++;
                });

                tableBody.find('.table-search-no-result').remove();
                if (query && rows.length > 0 && visibleCount === 0) {
                    tableBody.append(`
                        <tr class="table-search-no-result">
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-search" style="font-size: 24px;"></i>
                                <div class="mt-2">No matching product specification found for "${escapeHtml(query)}".</div>
                            </td>
                        </tr>
                    `);
                }

                if (query) {
                    productTableSearchInfo.text(`${visibleCount} matching on this page`);
                } else {
                    productTableSearchInfo.text('');
                }
            }

            function applySpecification(spec) {
                if (!spec) return;

                currentSelectedSku = spec.sku || '';
                hiddenSkuInput.val(currentSelectedSku);

                let availStock = (spec.available_stock !== undefined && spec.available_stock !== null)
                    ? parseInt(spec.available_stock, 10)
                    : (parseInt(spec.total_stock, 10) || 0);

                // Auto-fill form inputs
                if (spec.sku) {
                    $('#product_sku_input').val(spec.sku);
                }
                if (spec.item_name_text) {
                    $('#product_name_input').val(spec.item_name_text);
                } else if (spec.sku) {
                    $('#product_name_input').val(spec.sku);
                }

                // Auto-select Master dropdowns
                if (spec.item_name) setSelect2Val($('#master_item_name'), spec.item_name, spec.item_name_text);
                if (spec.item_type) setSelect2Val($('#master_item_type'), spec.item_type, spec.item_type_text);
                if (spec.designer) setSelect2Val($('#master_designer'), spec.designer);
                if (spec.gender) setSelect2Val($('#master_gender'), spec.gender, spec.gender_text);
                if (spec.composition) setSelect2Val($('#master_composition'), spec.composition);
                if (spec.colour) setSelect2Val($('#master_colour'), spec.colour, spec.colour_text);
                if (spec.yarn) setSelect2Val($('#master_yarn'), spec.yarn);
                if (spec.size) setSelect2Val($('#master_size'), spec.size, spec.size_text);
                if (spec.embellishment) setSelect2Val($('#master_embellishment'), spec.embellishment);
                if (spec.manufacturing_process) setSelect2Val($('#master_manufacturing_process'), spec.manufacturing_process);
                if (spec.craftsman !== undefined && spec.craftsman !== null) setSelect2Val($('#master_craftsman'), spec.craftsman);
                if (spec.manufacture) setSelect2Val($('#master_manufacture'), spec.manufacture);

                // Auto-fill Prices & Stock
                if (spec.price) $('input[name="price"]').val(spec.price);
                if (spec.sale_price) $('input[name="sale_price"]').val(spec.sale_price);
                if (spec.min_price) $('input[name="min_price"]').val(spec.min_price);

                $('#total_available_stock_count').text(availStock);
                $('input[name="stock"]').val(availStock > 0 ? availStock : 1);

                // Auto-fill Image
                if (spec.image_url) {
                    $('#mainImagePreview').attr('src', spec.image_url);
                    $('#mainImagePreviewContainer').removeClass('d-none');
                    $('#existing_spec_image_input').val(spec.img_path || spec.image_url);
                }

                // Show confirmation alert
                skuAutoFillMsg.html(`Specification <strong>${escapeHtml(spec.sku)}</strong> selected and applied to product form below.`);
                skuAutoFillAlert.removeClass('d-none').addClass('d-flex');

                // Re-render table to mark active selection
                renderSpecificationTable(loadedSpecifications);

                // Smooth scroll down to the product form
                const target = document.getElementById('productFormFieldsStart');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Click Choose button on a table row
            tableBody.on('click', '.js-choose-spec', function() {
                const idx = $(this).data('index');
                const selectedItem = loadedSpecifications[idx];
                if (selectedItem) {
                    applySpecification(selectedItem);
                }
            });

            // Filter actions
            btnApplyFilters.on('click', function() {
                fetchSpecifications();
            });

            btnClearFilters.on('click', function() {
                filterItemType.val('').trigger('change');
                filterItemName.val('').trigger('change');
                filterComposition.val('').trigger('change');
                filterGender.val('').trigger('change');
                productTableSearch.val('');
                fetchSpecifications();
            });

            // Table search with input event (client filter) and debounce for server query
            let searchDebounceTimer = null;
            productTableSearch.on('input', function() {
                applyClientTableSearch();
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    const q = productTableSearch.val().trim();
                    if (q.length >= 2) {
                        fetchSpecifications();
                    }
                }, 400);
            });

            btnResetTableSearch.on('click', function() {
                productTableSearch.val('');
                applyClientTableSearch();
            });

            // Initial load of specifications
            fetchSpecifications();

            // Set initial state
            updateMode('{{ $product->is_integrated ? "integrated" : "manual" }}');
        @endif
    });
</script>
@endpush
@endsection
