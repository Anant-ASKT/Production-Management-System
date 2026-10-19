@extends('layouts.supplier')

@section('title', 'Create Product')
@section('page-title', 'Add New Product')

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

    .clickable-photo {
        cursor: zoom-in !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .clickable-photo:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
        filter: brightness(1.05);
    }

    .locked-master-fields .select2-container {
        pointer-events: none !important;
    }
    .locked-master-fields .select2-selection {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        cursor: not-allowed !important;
        color: #495057 !important;
    }
    .locked-master-fields .select2-selection__clear {
        display: none !important;
    }
    .locked-master-fields input {
        background-color: #f8f9fa !important;
        pointer-events: none !important;
        cursor: not-allowed !important;
    }
    .input-with-add {
        display: flex;
        align-items: stretch;
        width: 100%;
    }
    .input-with-add .select2-container {
        flex: 1 1 auto;
        width: 1% !important;
    }
    .input-with-add .select2-container .select2-selection--single {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        height: 38px !important;
        display: flex;
        align-items: center;
    }
    .input-with-add .btn-add-master {
        flex: 0 0 42px;
        width: 42px;
        min-width: 42px;
        height: 38px;
        border: 1px solid #ced4da;
        border-left: 0;
        border-radius: 0 6px 6px 0;
        background: #f8fafc;
        color: #0d6efd;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s ease;
        font-size: 16px;
    }
    .input-with-add .btn-add-master:hover {
        background: #e7f1ff;
        color: #0a58ca;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="m-0">New Product</h4>
        <small class="text-muted">Add a new garment product to your catalog</small>
    </div>
    <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill fs-4 me-2 text-success"></i>
        <div class="fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2 text-danger"></i>
        <div class="fw-semibold">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{--
@if(!empty($isIntegrated))
    <!-- Direct Big Selection Buttons (Different Colors) -->
    <div class="d-flex flex-wrap gap-3 mb-4">
        <div>
            <input type="radio" class="btn-check" name="catalog_mode_switch" id="modeIntegrated" value="integrated" autocomplete="off" checked>
            <label class="btn btn-outline-primary btn-lg px-4 py-3 fw-bold d-flex align-items-center gap-3 shadow-sm rounded-3 fs-5" for="modeIntegrated" style="cursor: pointer; border-width: 2px;">
                <i class="bi bi-card-checklist fs-3"></i>
                <span>Choose from Ready List</span>
            </label>
        </div>
        <div>
            <input type="radio" class="btn-check" name="catalog_mode_switch" id="modeManual" value="manual" autocomplete="off">
            <label class="btn btn-outline-success btn-lg px-4 py-3 fw-bold d-flex align-items-center gap-3 shadow-sm rounded-3 fs-5" for="modeManual" style="cursor: pointer; border-width: 2px;">
                <i class="bi bi-plus-circle fs-3"></i>
                <span>Add New Product</span>
            </label>
        </div>
    </div>
@endif
--}}

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            <input type="hidden" name="catalog_mode" id="catalog_mode_input" value="{{ !empty($isIntegrated) ? 'integrated' : 'manual' }}">
            
            @if(!empty($isIntegrated))
            {{-- CHOOSE SKU / SPECIFICATION AUTO-FILL & FILTERS --}}
            <div id="chooseSkuTopCard" class="mb-4">
                <!-- Hidden inputs to store chosen SKU, action type and filters for submission/retention -->
                <input type="hidden" name="selected_vendor_sku" id="select_vendor_sku" value="{{ old('product_sku') }}">
                <input type="hidden" name="action_type" id="form_action_type" value="save_next">
                <input type="hidden" name="selected_spec_id" id="selected_spec_id" value="{{ old('selected_spec_id', request('selected_spec_id')) }}">
                <input type="hidden" name="filter_item_type" id="hidden_filter_item_type" value="{{ request('item_type', request('filter_item_type')) }}">
                <input type="hidden" name="filter_item_name" id="hidden_filter_item_name" value="{{ request('item_name', request('filter_item_name')) }}">
                <input type="hidden" name="filter_composition" id="hidden_filter_composition" value="{{ request('composition', request('filter_composition')) }}">
                <input type="hidden" name="filter_gender" id="hidden_filter_gender" value="{{ request('gender', request('filter_gender')) }}">
                <input type="hidden" name="filter_stock_qty" id="hidden_filter_stock_qty" value="{{ request('stock_qty', request('filter_stock_qty')) }}">

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
                                <select id="filterItemType" class="form-select select2-filter">
                                    <option value="">All</option>
                                    @if(isset($masters['itemTypes']))
                                        @foreach($masters['itemTypes'] as $itemType)
                                            <option value="{{ $itemType->id }}" {{ (string)request('item_type', request('filter_item_type')) === (string)$itemType->id ? 'selected' : '' }}>
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
                                <select id="filterItemName" class="form-select select2-filter">
                                    <option value="">All</option>
                                    @if(isset($masters['itemNames']))
                                        @foreach($masters['itemNames'] as $itemName)
                                            <option value="{{ $itemName->id }}" {{ (string)request('item_name', request('filter_item_name')) === (string)$itemName->id ? 'selected' : '' }}>
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
                                <select id="filterComposition" class="form-select select2-filter">
                                    <option value="">All</option>
                                    @if(isset($masters['compositions']))
                                        @foreach($masters['compositions'] as $composition)
                                            <option value="{{ $composition->id }}" {{ (string)request('composition', request('filter_composition')) === (string)$composition->id ? 'selected' : '' }}>
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
                                <select id="filterGender" class="form-select select2-filter">
                                    <option value="">All</option>
                                    @if(isset($masters['genders']))
                                        @foreach($masters['genders'] as $gender)
                                            <option value="{{ $gender->id }}" {{ (string)request('gender', request('filter_gender')) === (string)$gender->id ? 'selected' : '' }}>
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
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <strong>
                                <i class="bi bi-list-ul me-1"></i>
                                Ready Products List
                            </strong>

                            <span id="totalProducts" class="badge bg-primary">0</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label for="filterStockQty" class="form-label mb-0 small fw-bold text-muted text-nowrap">
                                <i class="bi bi-funnel me-1"></i>Stock:
                            </label>
                            <select id="filterStockQty" class="form-select form-select-sm" style="min-width: 160px;">
                                <option value="">All Stock</option>
                                <option value="1" {{ (string)request('stock_qty', request('filter_stock_qty')) === '1' ? 'selected' : '' }}>1</option>
                                <option value="less_than_5" {{ (string)request('stock_qty', request('filter_stock_qty')) === 'less_than_5' ? 'selected' : '' }}>Less than 5</option>
                                <option value="5_10" {{ (string)request('stock_qty', request('filter_stock_qty')) === '5_10' ? 'selected' : '' }}>5 - 10</option>
                                <option value="10_above" {{ (string)request('stock_qty', request('filter_stock_qty')) === '10_above' ? 'selected' : '' }}>10 Above</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body p-0">

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
                    <input type="file" name="main_image" class="form-control" accept="image/*" id="mainImageInput">
                    <div id="mainImageNotice" class="small mt-1"></div>
                    <div class="mt-2 d-none" id="mainImagePreviewContainer">
                        <div class="position-relative d-inline-block">
                            <img src="" id="mainImagePreview" class="rounded border shadow-sm clickable-photo" style="height: 150px; object-fit: cover;" title="Click to view big photo">
                        </div>
                        <div class="small text-muted mt-1"><i class="bi bi-zoom-in me-1"></i>Click image to view big photo</div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Additional Images</label>
                    <input type="file" name="sub_images[]" class="form-control" accept="image/*" multiple id="subImagesInput">
                    <div id="subImagesNotice" class="small mt-1"></div>
                    <div class="mt-2 d-flex gap-2 flex-wrap d-none" id="subImagesPreviewContainer">
                        <!-- Preview images will be appended here -->
                    </div>
                    <div class="small text-muted mt-1 d-none" id="subImagesHint"><i class="bi bi-zoom-in me-1"></i>Click image to view big photo</div>
                </div>

                @if(!empty($isIntegrated))
                <!-- Action Buttons: Save & Next / Save & Exit -->
                <div class="col-md-12 mb-3 mt-2" id="photoSectionActionCard">
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="submit" name="action_type" value="save_next" class="btn btn-primary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm btn-submit-action" id="btnSaveNextTop">
                            <i class="bi bi-arrow-right-circle fs-5"></i>
                            <span>Save &amp; Next</span>
                        </button>
                        <button type="submit" name="action_type" value="save_exit" class="btn btn-success px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm btn-submit-action" id="btnSaveExitTop">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>Save &amp; Exit</span>
                        </button>
                    </div>
                </div>
                @endif

                <!-- Basic Details section -->
                <div class="col-md-12 mb-3 mt-3">
                    <h5 class="text-primary border-bottom pb-2">
                        <i class="bi bi-info-circle me-1"></i> Basic Details
                    </h5>
                </div>
                
                @if(!empty($isIntegrated))
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        Item Name *
                        <span class="badge bg-secondary-subtle text-secondary border ms-1 d-none" id="product_name_locked_badge"><i class="bi bi-lock-fill me-1"></i>From Specification</span>
                    </label>
                    <div class="input-with-add">
                        <select name="item_name" id="supplier_item_name_select" class="form-select select2-master" required>
                            <option value="">Select an option</option>
                            @foreach($masters['itemNames'] ?? [] as $m)
                                <option value="{{ $m->id }}" data-name="{{ $m->itemname }}" data-code="{{ $m->code }}" {{ (old('item_name') == $m->id) ? 'selected' : '' }}>
                                    {{ $m->itemname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn-add-master" id="btnSupplierAddItemName" title="Add Item Name">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <input type="hidden" name="name" id="product_name_input" value="{{ old('name') }}">
                    <small class="text-muted">Select an item name or click + to add a new one.</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        Product SKU
                        <span class="badge bg-success-subtle text-success border ms-1" id="product_sku_badge"><i class="bi bi-magic me-1"></i>Auto Generated</span>
                        <span class="badge bg-secondary-subtle text-secondary border ms-1 d-none" id="product_sku_locked_badge"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-upc"></i></span>
                        <input type="text" name="product_sku" id="product_sku_input" class="form-control font-monospace" value="{{ old('product_sku') }}" placeholder="Auto-generated on Item Name select">
                    </div>
                    <small class="text-muted" id="sku_help_text">Auto-generated based on Item Name &amp; Supplier code, or enter manually.</small>
                </div>
                @else
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="product_name_input" class="form-control form-control-lg" value="{{ old('name') }}" placeholder="e.g. Cotton T-Shirt, Polo, Formal Shirt" required>
                </div>
                @endif
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" placeholder="0.00">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price') }}" placeholder="0.00">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Minimum Price</label>
                    <input type="number" step="0.01" name="min_price" class="form-control" value="{{ old('min_price') }}" placeholder="0.00">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">
                        Stock
                        @if(!empty($isIntegrated))
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" id="total_available_stock_badge">Total Available: <span id="total_available_stock_count">0</span></span>
                        @endif
                    </label>
                    <input type="number" name="stock" id="stock_input" class="form-control" value="{{ old('stock', 1) }}" min="1">
                    @if(!empty($isIntegrated))
                        <small class="text-danger d-none" id="stock_max_warning"><i class="bi bi-exclamation-triangle-fill me-1"></i>Cannot add more than actual stock (<span id="stock_max_val">0</span>)</small>
                    @endif
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Detailed product description...">{{ old('description') }}</textarea>
                </div>

                <input type="hidden" name="existing_spec_image" id="existing_spec_image_input" value="">

                @if(!empty($isIntegrated))
                <!-- =========================================================
                     INTEGRATED MASTER ATTRIBUTES SECTION
                ========================================================= -->
                <div id="integratedAttributesSection" class="col-md-12 mb-3">
                    <div class="card border rounded shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3" 
                             style="cursor: pointer; user-select: none;" 
                             id="toggleMasterAttributesHeader"
                             data-bs-toggle="collapse" 
                             data-bs-target="#masterAttributesCollapse" 
                             aria-expanded="false" 
                             aria-controls="masterAttributesCollapse">
                            <span class="fw-bold text-primary d-flex align-items-center gap-2">
                                <i class="bi bi-collection"></i>
                                <span>Master Attributes</span>
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary border small fw-normal">
                                    <i class="bi bi-lock-fill me-1"></i> Read-only from Specification
                                </span>
                                <span class="badge bg-primary text-white d-flex align-items-center gap-1 px-2 py-1">
                                    <span id="masterAttrToggleText">Click to View Attributes</span>
                                    <i class="bi bi-chevron-down" id="masterAttrToggleIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="collapse" id="masterAttributesCollapse">
                            <div class="card-body border-top p-3 bg-white locked-master-fields">
                                <div class="row">
                                    {{-- Item Name --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Item Name</label>
                                        <select name="item_name" id="master_item_name" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['itemNames'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->itemname }}" data-code="{{ $m->code }}" {{ old('item_name') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->itemname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Item Type --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Item Type</label>
                                        <select name="item_type" id="master_item_type" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['itemTypes'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->itemtype }}" data-code="{{ $m->code }}" {{ old('item_type') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->itemtype }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Designer --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Designer</label>
                                        <select name="designer" id="master_designer" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['designers'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->designername }}" data-code="{{ $m->code }}" {{ old('designer') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->designername }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Gender --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Gender</label>
                                        <select name="gender" id="master_gender" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['genders'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-code="{{ $m->code }}" {{ old('gender') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->name }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Composition --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Composition</label>
                                        <select name="composition" id="master_composition" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['compositions'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->composition_details }}" data-code="{{ $m->code }}" {{ old('composition') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->composition_details }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Colour --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Colour</label>
                                        <select name="colour" id="master_colour" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['colours'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->colourname }}" data-code="{{ $m->code }}" {{ old('colour') == $m->id ? 'selected' : '' }}>
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
                                                <option value="{{ $m->id }}" data-name="{{ $m->yarnname }}" data-code="{{ $m->code }}" {{ old('yarn') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->yarnname }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Size --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Size</label>
                                        <select name="size" id="master_size" class="form-select select2-master">
                                            <option value="">Select an option</option>
                                            @foreach($masters['sizes'] ?? [] as $m)
                                                <option value="{{ $m->id }}" data-name="{{ $m->size }}" data-code="{{ $m->code }}" {{ old('size') == $m->id ? 'selected' : '' }}>
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
                                                <option value="{{ $m->id }}" data-name="{{ $m->embellishmentname }}" data-code="{{ $m->code }}" {{ old('embellishment') == $m->id ? 'selected' : '' }}>
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
                                                <option value="{{ $m->id }}" data-name="{{ $m->manufacturing_process }}" data-code="{{ $m->code }}" {{ old('manufacturing_process') == $m->id ? 'selected' : '' }}>
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
                                                <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-code="{{ $m->code }}" {{ old('craftsman') == $m->id ? 'selected' : '' }}>
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
                                                <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-code="{{ $m->code }}" {{ old('manufacture') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->name }} @if(!empty($m->code)) ({{ $m->code }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Collection --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Collection</label>
                                        <input type="text" name="collection" class="form-control" value="{{ old('collection') }}" placeholder="e.g. Summer 2026">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- =========================================================
                     MANUAL ATTRIBUTES SECTION (Simple Text)
                ========================================================= -->
                <div id="manualAttributesSection" class="col-md-12 {{ !empty($isIntegrated) ? 'd-none' : '' }}">
                    <h5 class="text-primary border-bottom pb-2 mt-3 mb-3">
                        <i class="bi bi-pencil-square me-1"></i> Product Attributes
                    </h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Colour</label>
                            <input type="text" name="colour" class="form-control manual-input" value="{{ old('colour') }}" placeholder="e.g. Red, Blue">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Size</label>
                            <input type="text" name="size" class="form-control manual-input" value="{{ old('size') }}" placeholder="e.g. S, M, L">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Item Type</label>
                            <input type="text" name="item_type" class="form-control manual-input" value="{{ old('item_type') }}" placeholder="e.g. Shirt, Jeans">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <input type="text" name="gender" class="form-control manual-input" value="{{ old('gender') }}" placeholder="e.g. Men, Women, Unisex">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Composition</label>
                            <input type="text" name="composition" class="form-control manual-input" value="{{ old('composition') }}" placeholder="e.g. 100% Cotton">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Compositions (Misc)</label>
                            <input type="text" name="compositions" class="form-control manual-input" value="{{ old('compositions') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Designer</label>
                            <input type="text" name="designer" class="form-control manual-input" value="{{ old('designer') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Designers (Misc)</label>
                            <input type="text" name="designers" class="form-control manual-input" value="{{ old('designers') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Design Names</label>
                            <input type="text" name="design_names" class="form-control manual-input" value="{{ old('design_names') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Collection</label>
                            <input type="text" name="collection" class="form-control manual-input" value="{{ old('collection') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Embellishment</label>
                            <input type="text" name="embellishment" class="form-control manual-input" value="{{ old('embellishment') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Manufacture</label>
                            <input type="text" name="manufacture" class="form-control manual-input" value="{{ old('manufacture') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Manufacturing Process</label>
                            <input type="text" name="manufacturing_process" class="form-control manual-input" value="{{ old('manufacturing_process') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Mfg Processes (Misc)</label>
                            <input type="text" name="mfg_processes" class="form-control manual-input" value="{{ old('mfg_processes') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Craftsman</label>
                            <input type="text" name="craftsman" class="form-control manual-input" value="{{ old('craftsman') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Craftsmen (Misc)</label>
                            <input type="text" name="craftsmen" class="form-control manual-input" value="{{ old('craftsmen') }}">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                <a href="{{ route('supplier.products.index') }}" class="btn btn-light border">Cancel</a>
                @if(!empty($isIntegrated))
                    <div id="integratedBottomActionButtons" class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="submit" name="action_type" value="save_next" class="btn btn-primary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm btn-submit-action" id="btnSaveNextBottom">
                            <i class="bi bi-arrow-right-circle fs-5"></i>
                            <span>Save &amp; Next</span>
                        </button>
                        <button type="submit" name="action_type" value="save_exit" class="btn btn-success px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm btn-submit-action" id="btnSaveExitBottom">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>Save &amp; Exit</span>
                        </button>
                    </div>
                    <div id="manualBottomActionButtons" class="d-none">
                        <button type="submit" name="action_type" value="save" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Product
                        </button>
                    </div>
                @else
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Product
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-2 px-3 bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-zoom-in text-primary fs-5"></i>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="imagePreviewModalLabel">Image Preview</h6>
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

@if(!empty($isIntegrated))
<!-- Modal to Add New Item Name -->
<div class="modal fade" id="supplierItemNameModal" tabindex="-1" aria-labelledby="supplierItemNameModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0 rounded-3">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="supplierItemNameModalLabel">
                    <i class="bi bi-plus-circle me-1"></i> Add New Item Name
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="newItemNameInput" class="form-label fw-bold">Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="newItemNameInput" placeholder="e.g. Cotton T-Shirt, Polo, Formal Shirt">
                    <div class="text-danger small mt-1 d-none" id="newItemNameError"></div>
                </div>

                <div class="mb-2">
                    <label for="newItemCodeInput" class="form-label fw-bold d-flex justify-content-between align-items-center">
                        <span>Item Code <span class="text-muted fw-normal small">(3 Letters)</span></span>
                        <span class="badge bg-light text-primary border" id="codeAutoBadge" style="cursor: pointer;" title="Auto generate code from Item Name">
                            <i class="bi bi-magic me-1"></i>Auto Generate
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg text-uppercase font-monospace" id="newItemCodeInput" maxlength="4" placeholder="e.g. COT (Leave blank to auto-generate)">
                        <button class="btn btn-outline-primary px-3" type="button" id="btnAutoGenerateCode" title="Auto generate code from name">
                            <i class="bi bi-arrow-repeat me-1"></i>Auto
                        </button>
                    </div>
                    <div class="form-text text-muted mt-2">
                        Enter a 3-letter code or leave blank to automatically generate a unique code.
                    </div>
                    <div class="text-danger small mt-1 d-none" id="newItemCodeError"></div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" id="btnSaveSupplierItemName">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="saveItemNameSpinner" role="status" aria-hidden="true"></span>
                    <i class="bi bi-check2 me-1" id="saveItemNameIcon"></i> Save Item Name
                </button>
            </div>
        </div>
    </div>
</div>
@endif

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
        // Image preview in modal helper
        function showImageInModal(src, title, caption) {
            if (!src) return;
            const modalEl = document.getElementById('imagePreviewModal');
            if (!modalEl) return;

            const modalImg = document.getElementById('imagePreviewModalImg');
            const modalTitle = document.getElementById('imagePreviewModalLabel');
            const modalCaption = document.getElementById('imagePreviewModalCaption');
            const newTabBtn = document.getElementById('imagePreviewModalNewTab');

            if (modalImg) modalImg.src = src;
            if (modalTitle) modalTitle.textContent = title || 'Image Preview';
            if (modalCaption) modalCaption.textContent = caption || '';

            if (newTabBtn) {
                if (src.startsWith('data:')) {
                    newTabBtn.onclick = function(e) {
                        e.preventDefault();
                        const win = window.open();
                        if (win) {
                            win.document.write('<iframe src="' + src + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
                        }
                    };
                    newTabBtn.removeAttribute('href');
                    newTabBtn.removeAttribute('target');
                } else {
                    newTabBtn.onclick = null;
                    newTabBtn.href = src;
                    newTabBtn.target = '_blank';
                }
            }

            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }

        // Image preview logic
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
                    const existingSpecImg = $('#existing_spec_image_input').val();
                    if (existingSpecImg) {
                        mainPreview.src = existingSpecImg;
                    } else {
                        mainPreviewContainer.classList.add('d-none');
                    }
                }
            });
        }

        // Click on main image to view in modal (auto-selected or chosen)
        if (mainPreview) {
            mainPreview.addEventListener('click', function() {
                if (this.src && this.src.trim() !== '') {
                    const name = $('#product_name_input').val() || 'Main Product Image';
                    showImageInModal(this.src, 'Main Image Preview', name);
                }
            });
        }

        const subInput = document.getElementById('subImagesInput');
        const subPreviewContainer = document.getElementById('subImagesPreviewContainer');
        const subNotice = document.getElementById('subImagesNotice');
        const subHint = document.getElementById('subImagesHint');

        if (subInput) {
            subInput.addEventListener('change', function(e) {
                subPreviewContainer.innerHTML = '';
                
                if (e.target.files.length > 0) {
                    subPreviewContainer.classList.remove('d-none');
                    if (subHint) subHint.classList.remove('d-none');
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
                            img.className = 'rounded border shadow-sm clickable-photo';
                            img.style.height = '100px';
                            img.style.width = '100px';
                            img.style.objectFit = 'cover';
                            img.title = 'Click to view big photo';
                            subPreviewContainer.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                } else {
                    if (subNotice) subNotice.innerHTML = '';
                    subPreviewContainer.classList.add('d-none');
                    if (subHint) subHint.classList.add('d-none');
                }
            });
        }

        // Click on any additional image preview to view in modal
        if (subPreviewContainer) {
            subPreviewContainer.addEventListener('click', function(e) {
                const targetImg = e.target.closest('img');
                if (targetImg && targetImg.src) {
                    const name = $('#product_name_input').val() || 'Additional Image';
                    showImageInModal(targetImg.src, 'Additional Image Preview', name);
                }
            });
        }

        // Initialize Select2 on master selects and filter selects if jQuery is available
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

                $('.select2-filter').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%'
                        });
                    }
                });
            }
        }

        initSelect2();

        @if(!empty($isIntegrated))
        // Handle Item Name change in Add Product
        const supplierItemNameSelect = $('#supplier_item_name_select');
        const productNameHidden = $('#product_name_input');
        const productSkuInput = $('#product_sku_input');

        supplierItemNameSelect.on('change', function() {
            const selectedOpt = $(this).find('option:selected');
            const val = $(this).val();
            if (val) {
                const name = selectedOpt.data('name') || selectedOpt.text().trim();
                productNameHidden.val(name);

                // Auto-generate SKU
                $.ajax({
                    url: "{{ route('supplier.products.generate-sku') }}",
                    type: "GET",
                    data: { item_name_id: val },
                    dataType: "json",
                    success: function(res) {
                        if (res && res.sku) {
                            productSkuInput.val(res.sku);
                        }
                    }
                });
            }
        });

        // Helper to derive a 3-letter candidate code from an item name
        function deriveItemCodeCandidate(name) {
            if (!name) return '';
            const clean = name.toUpperCase().replace(/[^A-Z0-9 ]/g, '').trim();
            if (!clean) return '';
            const words = clean.split(/\s+/).filter(w => w.length > 0);
            let code = '';
            for (let i = 0; i < words.length && code.length < 3; i++) {
                code += words[i].charAt(0);
            }
            const lettersOnly = clean.replace(/[^A-Z0-9]/g, '');
            for (let i = 0; i < lettersOnly.length && code.length < 3; i++) {
                const ch = lettersOnly.charAt(i);
                if (code.indexOf(ch) === -1) {
                    code += ch;
                }
            }
            while (code.length < 3) {
                code += 'X';
            }
            return code.substring(0, 3);
        }

        let userTouchedCode = false;

        // Add Item Name Modal open
        $('#btnSupplierAddItemName').on('click', function(e) {
            e.preventDefault();
            userTouchedCode = false;
            $('#newItemNameInput').val('').removeClass('is-invalid');
            $('#newItemCodeInput').val('').removeClass('is-invalid').attr('placeholder', 'e.g. COT (Leave blank to auto-generate)');
            $('#newItemNameError').addClass('d-none').text('');
            $('#newItemCodeError').addClass('d-none').text('');
            const modalEl = document.getElementById('supplierItemNameModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
                setTimeout(() => $('#newItemNameInput').focus(), 400);
            }
        });

        // Dynamic code suggestion as user types Item Name
        $('#newItemNameInput').on('input keyup paste', function() {
            const nameVal = $(this).val().trim();
            $('#newItemNameError').addClass('d-none').text('');
            $(this).removeClass('is-invalid');

            if (!userTouchedCode) {
                const candidate = deriveItemCodeCandidate(nameVal);
                if (candidate) {
                    $('#newItemCodeInput').attr('placeholder', `e.g. ${candidate} (Leave blank to auto-generate)`);
                } else {
                    $('#newItemCodeInput').attr('placeholder', 'e.g. COT (Leave blank to auto-generate)');
                }
            }
        });

        // User manually types in Item Code
        $('#newItemCodeInput').on('input keyup', function() {
            let val = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 4);
            $(this).val(val);
            userTouchedCode = (val.trim().length > 0);
            $('#newItemCodeError').addClass('d-none').text('');
            $(this).removeClass('is-invalid');
        });

        // Auto Generate button / badge click
        $('#btnAutoGenerateCode, #codeAutoBadge').on('click', function(e) {
            e.preventDefault();
            const nameVal = $('#newItemNameInput').val().trim();
            const candidate = deriveItemCodeCandidate(nameVal) || 'ITM';
            $('#newItemCodeInput').val(candidate).removeClass('is-invalid');
            $('#newItemCodeError').addClass('d-none').text('');
            userTouchedCode = true;
        });

        // Save New Item Name via AJAX
        $('#btnSaveSupplierItemName').on('click', function() {
            const name = $('#newItemNameInput').val().trim();
            const code = $('#newItemCodeInput').val().trim().toUpperCase();

            $('#newItemNameError').addClass('d-none').text('');
            $('#newItemCodeError').addClass('d-none').text('');
            $('#newItemNameInput').removeClass('is-invalid');
            $('#newItemCodeInput').removeClass('is-invalid');

            if (!name) {
                $('#newItemNameInput').addClass('is-invalid');
                $('#newItemNameError').removeClass('d-none').text('Please enter an item name.');
                return;
            }

            const btn = $(this);
            const spinner = $('#saveItemNameSpinner');
            const icon = $('#saveItemNameIcon');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');

            $.ajax({
                url: "{{ route('supplier.masters.item-name.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    code: code
                },
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');

                    if (res && res.success) {
                        const codeText = res.code ? ` (${res.code})` : '';
                        const displayText = `${res.name}${codeText}`;
                        
                        let existingOpt = supplierItemNameSelect.find(`option[value="${res.id}"]`);
                        if (existingOpt.length === 0) {
                            const newOption = new Option(displayText, res.id, true, true);
                            $(newOption).attr('data-name', res.name).attr('data-code', res.code || '');
                            supplierItemNameSelect.append(newOption);
                        } else {
                            existingOpt.prop('selected', true);
                        }

                        supplierItemNameSelect.val(res.id).trigger('change');

                        const modalEl = document.getElementById('supplierItemNameModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                    } else {
                        $('#newItemNameInput').addClass('is-invalid');
                        $('#newItemNameError').removeClass('d-none').text(res.message || 'Error saving item name.');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');

                    let errMsg = 'Failed to save item name.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.code) {
                            $('#newItemCodeInput').addClass('is-invalid');
                            $('#newItemCodeError').removeClass('d-none').text(xhr.responseJSON.errors.code[0]);
                            return;
                        }
                        if (xhr.responseJSON.errors.name) {
                            $('#newItemNameInput').addClass('is-invalid');
                            $('#newItemNameError').removeClass('d-none').text(xhr.responseJSON.errors.name[0]);
                            return;
                        }
                    }

                    if (errMsg.toLowerCase().includes('code')) {
                        $('#newItemCodeInput').addClass('is-invalid');
                        $('#newItemCodeError').removeClass('d-none').text(errMsg);
                    } else {
                        $('#newItemNameInput').addClass('is-invalid');
                        $('#newItemNameError').removeClass('d-none').text(errMsg);
                    }
                }
            });
        });

            // Handle Master Attributes collapsible dropdown toggle text, icon & Select2 resize
            $('#masterAttributesCollapse').on('show.bs.collapse', function () {
                $('#masterAttrToggleText').text('Click to Hide Attributes');
                $('#masterAttrToggleIcon').removeClass('bi-chevron-down').addClass('bi-chevron-up');
            }).on('shown.bs.collapse', function () {
                $('.select2-master').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: 'Select an option',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
            }).on('hide.bs.collapse', function () {
                $('#masterAttrToggleText').text('Click to View Attributes');
                $('#masterAttrToggleIcon').removeClass('bi-chevron-up').addClass('bi-chevron-down');
            });

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

            // =========================================================================
            // PRODUCT SPECIFICATION MASTER - FILTERS, TABLE SEARCH & AUTO-FILL
            // =========================================================================
            const filterItemType = $('#filterItemType');
            const filterItemName = $('#filterItemName');
            const filterComposition = $('#filterComposition');
            const filterGender = $('#filterGender');
            const filterStockQty = $('#filterStockQty');
            const btnApplyFilters = $('#btnApplyFilters');
            const btnClearFilters = $('#btnClearFilters');
            const tableBody = $('#productSpecificationTableBody');
            const totalProductsBadge = $('#totalProducts');
            const hiddenSkuInput = $('#select_vendor_sku');
            const skuAutoFillAlert = $('#sku_auto_fill_alert');
            const skuAutoFillMsg = $('#sku_auto_fill_msg');

            let loadedSpecifications = [];
            let currentSelectedSku = hiddenSkuInput.val() || '';

            // Helper to blank/reset photo and media details completely
            function clearPhotoDetails() {
                $('#mainImageInput').val('');
                if (mainNotice) mainNotice.innerHTML = '';
                $('#subImagesInput').val('');
                if (subNotice) subNotice.innerHTML = '';

                if (subPreviewContainer) {
                    subPreviewContainer.innerHTML = '';
                    subPreviewContainer.classList.add('d-none');
                }
                if (subHint) subHint.classList.add('d-none');

                if (mainPreview) mainPreview.src = '';
                if (mainPreviewContainer) mainPreviewContainer.classList.add('d-none');
                $('#existing_spec_image_input').val('');
            }

            // Helper to clean all product details completely
            function clearAllProductDetails() {
                currentSelectedSku = '';
                hiddenSkuInput.val('');
                $('#selected_spec_id').val('');

                // Clear Product Name & SKU
                userTypedName = false;
                $('#product_name_input').val('').prop('readonly', false).removeClass('bg-light');
                $('#product_name_locked_badge').addClass('d-none');
                $('#product_sku_input').val('').prop('readonly', false).removeClass('bg-light');
                $('#product_sku_locked_badge').addClass('d-none');

                // Clear Prices & Stock
                $('input[name="price"]').val('');
                $('input[name="sale_price"]').val('');
                $('input[name="min_price"]').val('');
                $('#stock_input').val('1').removeAttr('max').removeAttr('min');
                $('#total_available_stock_count').text('0');
                $('#stock_max_val').text('0');
                $('#stock_max_warning').addClass('d-none');

                // Clear Description
                $('textarea[name="description"]').val('');

                // Blank all photo and media details
                clearPhotoDetails();

                // Clear Master Dropdowns
                $('#master_item_name').val('').trigger('change');
                $('#master_item_type').val('').trigger('change');
                $('#master_designer').val('').trigger('change');
                $('#master_gender').val('').trigger('change');
                $('#master_composition').val('').trigger('change');
                $('#master_colour').val('').trigger('change');
                $('#master_yarn').val('').trigger('change');
                $('#master_size').val('').trigger('change');
                $('#master_embellishment').val('').trigger('change');
                $('#master_manufacturing_process').val('').trigger('change');
                $('#master_craftsman').val('').trigger('change');
                $('#master_manufacture').val('').trigger('change');

                // Clear Manual Inputs
                $('.manual-input').val('');

                // Clear confirmation alert
                skuAutoFillAlert.addClass('d-none').removeClass('d-flex');



                // Re-render table to unselect rows
                if (typeof renderSpecificationTable === 'function') {
                    renderSpecificationTable(loadedSpecifications);
                }
            }

            // Toggle between Integrated Masters and Manual Entry modes
            const modeRadios = document.querySelectorAll('input[name="catalog_mode_switch"]');
            const integratedSection = document.getElementById('integratedAttributesSection');
            const manualSection = document.getElementById('manualAttributesSection');

            function updateMode(mode, isUserClick = false) {
                $('#catalog_mode_input').val(mode);
                if (mode === 'integrated') {
                    $('#form_action_type').val('save_next');
                    $('#chooseSkuTopCard').removeClass('d-none');
                    if (integratedSection) integratedSection.classList.remove('d-none');
                    if (manualSection) manualSection.classList.add('d-none');
                    $('#photoSectionActionCard').removeClass('d-none');
                    $('#integratedBottomActionButtons').removeClass('d-none');
                    $('#manualBottomActionButtons').addClass('d-none');

                    // Enable integrated inputs, disable manual inputs so they don't conflict
                    $('#integratedAttributesSection select, #integratedAttributesSection input').prop('disabled', false);
                    $('#select_vendor_sku').prop('disabled', false);
                    $('#manualAttributesSection input').prop('disabled', true);
                    initSelect2();

                    if (currentSelectedSku) {
                        $('#product_name_input').prop('readonly', true).addClass('bg-light');
                        $('#product_name_locked_badge').removeClass('d-none');
                        $('#product_sku_input').prop('readonly', true).addClass('bg-light');
                        $('#product_sku_locked_badge').removeClass('d-none');
                    } else if (isUserClick) {
                        clearAllProductDetails();
                    }
                } else {
                    $('#form_action_type').val('save');
                    $('#chooseSkuTopCard').addClass('d-none');
                    if (integratedSection) integratedSection.classList.add('d-none');
                    if (manualSection) manualSection.classList.remove('d-none');
                    $('#photoSectionActionCard').addClass('d-none');
                    $('#integratedBottomActionButtons').addClass('d-none');
                    $('#manualBottomActionButtons').removeClass('d-none');

                    // Disable integrated inputs, enable manual inputs
                    $('#integratedAttributesSection select, #integratedAttributesSection input').prop('disabled', true);
                    $('#select_vendor_sku').prop('disabled', true);
                    $('#manualAttributesSection input').prop('disabled', false);

                    // When clicking "Add New Product", clean all details completely
                    if (isUserClick) {
                        clearAllProductDetails();
                    } else {
                        $('#product_name_input').prop('readonly', false).removeClass('bg-light');
                        $('#product_name_locked_badge').addClass('d-none');
                        $('#product_sku_input').prop('readonly', false).removeClass('bg-light');
                        $('#product_sku_locked_badge').addClass('d-none');
                        $('#stock_input').removeAttr('max');
                        $('#stock_max_warning').addClass('d-none');
                    }
                }
            }

            modeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateMode(this.value, true);
                });
            });

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
                const stockFilterVal = filterStockQty.val();

                // Filter out any items with 0 stock and match selected stock quantity
                items = (items || []).filter(item => {
                    const stock = (item.available_stock !== undefined && item.available_stock !== null)
                        ? parseInt(item.available_stock, 10)
                        : (parseInt(item.total_stock, 10) || 0);

                    if (stock <= 0) return false;

                    if (stockFilterVal === '1') {
                        return stock === 1;
                    } else if (stockFilterVal === 'less_than_5') {
                        return stock > 1 && stock < 5;
                    } else if (stockFilterVal === '5_10') {
                        return stock >= 5 && stock <= 10;
                    } else if (stockFilterVal === '10_above') {
                        return stock >= 10;
                    }

                    return true;
                });

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
                                 class="product-table-image clickable-photo"
                                 title="Click to view big photo"
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
                    stock_filter: filterStockQty.val() || ''
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

            function applySpecification(spec) {
                if (!spec) return;

                // When choosing another product, blank previous photo/media details and description
                clearPhotoDetails();
                $('textarea[name="description"]').val('');

                currentSelectedSku = spec.sku || '';
                hiddenSkuInput.val(currentSelectedSku);
                $('#selected_spec_id').val(spec.spec_id || spec.id || '');

                let availStock = (spec.available_stock !== undefined && spec.available_stock !== null)
                    ? parseInt(spec.available_stock, 10)
                    : (parseInt(spec.total_stock, 10) || 0);

                // Auto-fill Product SKU and lock it
                if (spec.sku) {
                    $('#product_sku_input').val(spec.sku).prop('readonly', true).addClass('bg-light');
                    $('#product_sku_locked_badge').removeClass('d-none');
                }

                // Item Name as Product Name and lock it
                const itemName = spec.item_name_text || spec.sku || '';
                userTypedName = false;
                $('#product_name_input').val(itemName).prop('readonly', true).addClass('bg-light');
                $('#product_name_locked_badge').removeClass('d-none');

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

                // Ensure product name is preserved as item name
                $('#product_name_input').val(itemName);

                // Auto-fill Prices & Stock (only prices, image, sub-images, description can be changed; stock capped at availStock)
                $('input[name="price"]').val(spec.price || '');
                $('input[name="sale_price"]').val(spec.sale_price || '');
                $('input[name="min_price"]').val(spec.min_price || '');

                const maxStock = availStock > 0 ? availStock : 1;
                $('#total_available_stock_count').text(availStock);
                $('#stock_max_val').text(maxStock);
                $('#stock_input').attr('max', maxStock).attr('min', 1).val(maxStock);
                $('#stock_max_warning').addClass('d-none');

                // Auto-fill Image for this specific product
                if (spec.image_url) {
                    $('#mainImagePreview').attr('src', spec.image_url);
                    $('#mainImagePreviewContainer').removeClass('d-none');
                    $('#existing_spec_image_input').val(spec.img_path || spec.image_url);
                } else {
                    $('#mainImagePreview').attr('src', '');
                    $('#mainImagePreviewContainer').addClass('d-none');
                    $('#existing_spec_image_input').val('');
                }

                // Show confirmation alert
                skuAutoFillMsg.html(`Specification <strong>${escapeHtml(spec.sku)}</strong> selected. Item Name <strong>${escapeHtml(itemName)}</strong> applied as Product Name. Only prices, images, description, and stock (up to max ${maxStock}) can be changed.`);
                skuAutoFillAlert.removeClass('d-none').addClass('d-flex');

                // Re-render table to mark active selection
                renderSpecificationTable(loadedSpecifications);

                // Smooth scroll down to the product form
                const target = document.getElementById('productFormFieldsStart');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Realtime clamp on stock input so it cannot exceed actual stock
            $('#stock_input').on('input change keyup paste', function() {
                const max = parseInt($(this).attr('max'), 10);
                const current = parseInt($(this).val(), 10);
                if (!isNaN(max) && max > 0) {
                    if (current > max) {
                        $(this).val(max);
                        $('#stock_max_warning').removeClass('d-none');
                    } else if (current < 1 && $(this).val() !== '') {
                        $(this).val(1);
                        $('#stock_max_warning').addClass('d-none');
                    } else {
                        $('#stock_max_warning').addClass('d-none');
                    }
                }
            });

            function syncFilterHiddenInputs() {
                $('#hidden_filter_item_type').val(filterItemType.val() || '');
                $('#hidden_filter_item_name').val(filterItemName.val() || '');
                $('#hidden_filter_composition').val(filterComposition.val() || '');
                $('#hidden_filter_gender').val(filterGender.val() || '');
                $('#hidden_filter_stock_qty').val(filterStockQty.val() || '');
            }

            // Sync hidden inputs whenever filters change
            filterItemType.on('change', syncFilterHiddenInputs);
            filterItemName.on('change', syncFilterHiddenInputs);
            filterComposition.on('change', syncFilterHiddenInputs);
            filterGender.on('change', syncFilterHiddenInputs);
            filterStockQty.on('change', syncFilterHiddenInputs);

            // Handle clicking any submit action button (Save & Next, Save & Exit, etc.)
            $(document).on('click', '.btn-submit-action', function() {
                const action = $(this).val() || $(this).data('action') || 'save_next';
                $('#form_action_type').val(action);
                syncFilterHiddenInputs();
            });

            // Prevent form submit if stock exceeds actual available stock or if ready product not chosen
            $('#productForm').on('submit', function(e) {
                syncFilterHiddenInputs();

                const mode = $('#catalog_mode_input').val();
                if (mode === 'integrated' && !currentSelectedSku) {
                    e.preventDefault();
                    alert('Please choose a product from the Ready Products List first.');
                    const card = document.getElementById('specTableCard');
                    if (card) card.scrollIntoView({ behavior: 'smooth' });
                    return false;
                }

                const max = parseInt($('#stock_input').attr('max'), 10);
                const current = parseInt($('#stock_input').val(), 10);
                if (!isNaN(max) && max > 0 && current > max) {
                    e.preventDefault();
                    $('#stock_input').val(max).focus();
                    $('#stock_max_warning').removeClass('d-none');
                    alert('Stock quantity cannot be greater than the actual available stock (' + max + ').');
                    return false;
                }
            });

            // Click Choose button on a table row
            tableBody.on('click', '.js-choose-spec', function() {
                const idx = $(this).data('index');
                const selectedItem = loadedSpecifications[idx];
                if (selectedItem) {
                    applySpecification(selectedItem);
                }
            });

            // Click on specification table row image to preview big photo
            tableBody.on('click', '.product-table-image', function(e) {
                e.stopPropagation();
                const src = $(this).attr('src');
                const tr = $(this).closest('tr');
                const itemName = tr.find('td:nth-child(4) div.fw-bold').text().trim() || 'Specification Product Image';
                const itemSku = tr.find('td:nth-child(4) div.small').text().trim();
                if (src) {
                    showImageInModal(src, 'Specification Product Image', itemName + (itemSku ? ` (${itemSku})` : ''));
                }
            });

            // Filter actions
            filterStockQty.on('change', function() {
                fetchSpecifications();
            });

            btnApplyFilters.on('click', function() {
                fetchSpecifications();
            });

            btnClearFilters.on('click', function() {
                filterItemType.val('').trigger('change');
                filterItemName.val('').trigger('change');
                filterComposition.val('').trigger('change');
                filterGender.val('').trigger('change');
                filterStockQty.val('');
                syncFilterHiddenInputs();
                fetchSpecifications();
            });

            // Initial sync of filter inputs
            syncFilterHiddenInputs();

            // Initial load of specifications
            fetchSpecifications();

            // Set initial state
            updateMode('integrated');
        @endif
    });
</script>
@endpush
@endsection
