@extends('layouts.app')

@section('content')

<style>
    .product-master-page {
        padding: 18px;
    }

    .product-master-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 18px;
    }

    .product-master-title {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }

    .product-master-subtitle {
        margin: 4px 0 0;
        color: #6c757d;
        font-size: 13px;
    }

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

    #productSpecificationTable {
        min-width: 850px;
    }

    #productSpecificationTable th {
        white-space: nowrap;
        font-size: 13px;
        vertical-align: middle;
    }

    #productSpecificationTable td {
        font-size: 13px;
        vertical-align: middle;
    }

    .product-image-cell {
        width: 85px;
        text-align: center;
    }

    .product-table-image {
        width: 58px;
        height: 58px;
        object-fit: cover;
        border-radius: 7px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        cursor: pointer;
    }

    .no-product-image {
        width: 58px;
        height: 58px;
        border: 1px solid #dee2e6;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #adb5bd;
        background: #f8f9fa;
        font-size: 18px;
    }

    .no-product-image span {
        font-size: 8px;
        margin-top: 2px;
    }

    .stock-qty-badge {
        font-weight: 700;
        font-size: 13px;
    }

    .view-product-btn {
        white-space: nowrap;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        padding: 13px 16px;
        border-top: 1px solid #dee2e6;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 13px;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-controls .pagination {
        margin-bottom: 0;
    }

    .pagination-controls .page-link {
        cursor: pointer;
    }

    .pagination-controls .page-item.disabled .page-link {
        cursor: not-allowed;
    }

    /* Product details modal */
    .product-details-modal .modal-dialog {
        max-width: 1200px;
    }

    .product-details-modal .modal-body {
        max-height: calc(100vh - 160px);
        overflow-y: auto;
    }

    .product-details-top {
        display: grid;
        grid-template-columns: 220px minmax(0, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .modal-product-image-wrap {
        min-height: 220px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }

    .modal-product-image {
        width: 100%;
        max-height: 260px;
        object-fit: contain;
        border-radius: 8px;
        cursor: pointer;
    }

    .modal-no-image {
        color: #adb5bd;
        text-align: center;
    }

    .modal-no-image i {
        font-size: 45px;
        display: block;
        margin-bottom: 5px;
    }

    .product-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 9px;
    }

    .product-info-box {
        border: 1px solid #dee2e6;
        border-radius: 7px;
        overflow: hidden;
        min-width: 0;
    }

    .product-info-label {
        display: block;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 5px 8px;
        color: #6c757d;
        font-size: 10px;
        font-weight: 600;
    }

    .product-info-value {
        display: block;
        padding: 7px 8px;
        min-height: 34px;
        font-size: 12px;
        font-weight: 500;
        word-break: break-word;
    }

    .stock-details-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin: 5px 0 10px;
        font-size: 15px;
        font-weight: 700;
    }

    .stock-row-image,
    .stock-row-no-image {
        width: 52px;
        height: 52px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        object-fit: cover;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #adb5bd;
        cursor: pointer;
    }

    .stock-row-image:hover {
        opacity: 0.85;
    }

    .edit-barcode-top { display: grid; grid-template-columns: 280px minmax(0, 1fr); gap: 20px; align-items: start; }
    .edit-barcode-image-wrap { min-height: 320px; border: 1px solid #dee2e6; border-radius: 10px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .edit-barcode-image { width: 100%; height: 320px; object-fit: contain; cursor: zoom-in; }
    .edit-barcode-info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }

    .stock-details-table {
        min-width: 1200px;
    }

    .stock-details-table th,
    .stock-details-table td {
        font-size: 11px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .stock-table-wrapper {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: auto;
    }

    .large-image-modal .modal-dialog {
        max-width: 1100px;
    }

    .large-image-modal .modal-body {
        text-align: center;
        background: #111;
        padding: 15px;
    }

    .product-sub-images {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .product-sub-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        background: #f8f9fa;
    }

    .product-sub-image:hover {
        opacity: 0.85;
    }

    #largeProductImage {
        max-width: 100%;
        max-height: 75vh;
        object-fit: contain;
    }

    @media (max-width: 900px) {
        .edit-barcode-top { grid-template-columns: 1fr; }
        .edit-barcode-info-grid { grid-template-columns: 1fr; }
        .product-details-top {
            grid-template-columns: 1fr;
        }

        .product-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .product-master-page {
            padding: 10px;
        }

        .product-master-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .product-info-grid {
            grid-template-columns: 1fr;
        }

        .pagination-wrapper {
            align-items: flex-start;
            flex-direction: column;
        }
    }


    .barcode-edit-form-modal .modal-dialog { max-width: 1200px; }
    .barcode-edit-form-modal .modal-body { max-height: 78vh; overflow-y: auto; }
    .barcode-edit-section { border:1px solid #dee2e6; border-radius:10px; padding:18px; margin-bottom:18px; background:#fff; }
    .barcode-edit-section-title { font-weight:700; font-size:16px; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
    .barcode-edit-field label { font-size:12px; color:#6c757d; margin-bottom:5px; font-weight:600; }
    .barcode-edit-field .form-control, .barcode-edit-field .form-select { min-height:40px; }
    .barcode-edit-readonly { background:#f8f9fa !important; }
    .barcode-edit-images { display:flex; gap:10px; flex-wrap:wrap; }
    .barcode-edit-images img { width:90px; height:90px; object-fit:cover; border:1px solid #dee2e6; border-radius:8px; cursor:pointer; }

</style>

<div class="container-fluid product-master-page">

    {{-- PAGE HEADER --}}
    <div class="product-master-header">
        <div>
            <h4 class="product-master-title">
                <i class="bi bi-box-seam me-1"></i>
                Show All Product Specification Master
            </h4>

            <p class="product-master-subtitle">
                Available Product Specification Stock
            </p>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card shadow-sm filter-card mb-3">

        <div class="card-header">
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

                        @foreach($itemTypes as $itemType)
                            <option value="{{ $itemType->id }}">
                                {{ $itemType->itemtype }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ITEM NAME --}}
                <div class="col-md-3">
                    <label for="filterItemName" class="form-label filter-label">
                        Item Name
                    </label>

                    <select id="filterItemName" class="form-select select2-master">
                        <option value="">All Item Names</option>

                        @foreach($itemNames as $itemName)
                            <option value="{{ $itemName->id }}">
                                {{ $itemName->itemname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- COMPOSITION --}}
                <div class="col-md-3">
                    <label for="filterComposition" class="form-label filter-label">
                        Composition
                    </label>

                    <select id="filterComposition" class="form-select select2-master">
                        <option value="">All Compositions</option>

                        @foreach($compositions as $composition)
                            <option value="{{ $composition->id }}">
                                {{ $composition->composition_details }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- GENDER --}}
                <div class="col-md-3">
                    <label for="filterGender" class="form-label filter-label">
                        Gender Type
                    </label>

                    <select id="filterGender" class="form-select select2-master">
                        <option value="">All Gender Types</option>

                        @foreach($genders as $gender)
                            <option value="{{ $gender->id }}">
                                {{ $gender->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-3">
                <button
                    type="button"
                    id="btnApplyFilters"
                    class="btn btn-primary"
                >
                    <i class="bi bi-search me-1"></i>
                    Apply Filters
                </button>

                <button
                    type="button"
                    id="btnClearFilters"
                    class="btn btn-secondary"
                >
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Clear
                </button>
            </div>

        </div>
    </div>

    {{-- RESULT CARD --}}
    <div class="card shadow-sm result-card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>
                <i class="bi bi-list-ul me-1"></i>
                Product Specification Master
            </strong>

            <span
                id="totalProducts"
                class="badge bg-primary"
            >
                0
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table
                    class="table table-bordered table-hover mb-0"
                    id="productSpecificationTable"
                >
                    <thead class="table-light">
                        <tr>
                            <th style="width:55px;">#</th>

                            <th
                                class="text-center"
                                style="width:85px;"
                            >
                                Image
                            </th>

                            <th>Item Type</th>

                            <th>Item Name</th>

                            <th>Composition</th>

                            <th>Gender Type</th>

                            <th
                                class="text-end"
                                style="width:120px;"
                            >
                                Stock Qty
                            </th>

                            <th
                                class="text-center"
                                style="width:90px;"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody id="productSpecificationTableBody">
                        <tr>
                            <td
                                colspan="8"
                                class="text-center py-4 text-muted"
                            >
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div
                id="specificationPagination"
                class="pagination-wrapper"
                style="display:none;"
            >
                <div
                    id="paginationInfo"
                    class="pagination-info"
                ></div>

                <div class="pagination-controls">

                    <div class="d-flex align-items-center gap-1">
                        <label
                            for="specificationPerPage"
                            class="small text-muted mb-0"
                        >
                            Show
                        </label>

                        <select
                            id="specificationPerPage"
                            class="form-select form-select-sm"
                            style="width:75px;"
                        >
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <nav aria-label="Product specification pagination">
                        <ul
                            class="pagination pagination-sm mb-0"
                            id="specificationPaginationList"
                        ></ul>
                    </nav>

                </div>
            </div>

        </div>
    </div>

</div>

{{-- PRODUCT DETAILS MODAL --}}
<div
    class="modal fade product-details-modal"
    id="productDetailsModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam me-1"></i>
                    Product & Stock Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body">

                <div id="productDetailsLoading" class="text-center py-5">
                    <div
                        class="spinner-border"
                        role="status"
                    ></div>

                    <div class="mt-2 text-muted">
                        Loading product details...
                    </div>
                </div>

                <div
                    id="productDetailsContent"
                    style="display:none;"
                >

                    <div class="product-details-top">

                        <div>

                            <div
                                id="modalProductImageContainer"
                                class="modal-product-image-wrap"
                            ></div>

                            <div
                                id="modalProductSubImages"
                                class="product-sub-images"
                            ></div>

                        </div>

                        <div>
                            <div
                                id="modalProductInfo"
                                class="product-info-grid"
                            ></div>
                        </div>

                    </div>

                    <div class="stock-details-title">
                        <span>
                            <i class="bi bi-boxes me-1"></i>
                            Stock Details
                        </span>

                        <span
                            id="modalStockCount"
                            class="badge bg-primary"
                        >
                            0
                        </span>
                    </div>

                    <div class="stock-table-wrapper">
                        <table class="table table-bordered table-hover mb-0 stock-details-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Barcode</th>
                                    <th>Available Qty</th>
                                    <th>Stock Date</th>
                                    <th>Box No</th>
                                    <th>Warehouse</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="modalStockTableBody"></tbody>
                        </table>
                    </div>

                </div>

                <div
                    id="productDetailsEmpty"
                    class="text-center text-muted py-5"
                    style="display:none;"
                >
                    No stock details found.
                </div>

            </div>

        </div>
    </div>
</div>

{{-- EDIT BARCODE DETAILS MODAL --}}
<div class="modal fade edit-barcode-modal" id="editBarcodeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Barcode Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"><div id="editBarcodeDetailsContent"></div></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Close</button>
                <button type="button" class="btn btn-primary" id="btnGoForEditBarcode"><i class="bi bi-pencil-square me-1"></i> Go For Edit Barcode</button>
            </div>
        </div>
    </div>
</div>

{{-- BARCODE EDIT FORM MODAL --}}
<div class="modal fade barcode-edit-form-modal" id="barcodeEditFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><i class="bi bi-upc-scan me-2"></i>Edit Barcode Product</h5>
                    <small class="text-muted">All specification values are loaded from their master records.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="barcodeEditCsrfToken" value="{{ csrf_token() }}">
                <div class="barcode-edit-section">
                    <div class="barcode-edit-section-title"><i class="bi bi-image"></i>Product Images</div>
                    <div id="barcodeEditImages" class="barcode-edit-images"></div>
                </div>

                <div class="barcode-edit-section">
                    <div class="barcode-edit-section-title"><i class="bi bi-upc-scan"></i>Barcode Information</div>
                    <div class="row g-3">
                        <div class="col-md-6 barcode-edit-field">
                            <label>Existing Barcode</label>
                            <input type="text" id="editModalBarcode" class="form-control barcode-edit-readonly" readonly>
                        </div>
                        <div class="col-md-6 barcode-edit-field">
                            <label>Stock ID</label>
                            <input type="text" id="editModalStockId" class="form-control barcode-edit-readonly" readonly>
                        </div>
                    </div>
                </div>

                <div class="barcode-edit-section">
                    <div class="barcode-edit-section-title"><i class="bi bi-info-circle"></i>Design Information</div>
                    <div class="row g-3">
                        <div class="col-md-6 barcode-edit-field"><label>Item Name <span class="text-danger">*</span></label><select id="editModalItemName" class="form-select edit-modal-master-select" required><option value="">Select Item Name</option>@foreach($itemNames as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->itemname }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Item Type <span class="text-danger">*</span></label><select id="editModalItemType" class="form-select edit-modal-master-select" required><option value="">Select Item Type</option>@foreach($itemTypes as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->itemtype }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Designer <span class="text-danger">*</span></label><select id="editModalDesigner" class="form-select edit-modal-master-select" required><option value="">Select Designer</option>@foreach($designers as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->designername }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Gender Type <span class="text-danger">*</span></label><select id="editModalGender" class="form-select edit-modal-master-select" required><option value="">Select Gender</option>@foreach($genders as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->name }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Composition <span class="text-danger">*</span></label><select id="editModalComposition" class="form-select edit-modal-master-select" required><option value="">Select Composition</option>@foreach($compositions as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->composition_details }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Yarn Name</label><select id="editModalYarn" class="form-select edit-modal-master-select"><option value="">Select Yarn</option>@foreach($yarns as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->yarnname }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Colour <span class="text-danger">*</span></label><select id="editModalColour" class="form-select edit-modal-master-select" required><option value="">Select Colour</option>@foreach($colours as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->colourname }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Size <span class="text-danger">*</span></label><select id="editModalSize" class="form-select edit-modal-master-select" required><option value="">Select Size</option>@foreach($sizes as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->size }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Embellishment</label><select id="editModalEmbellishment" class="form-select edit-modal-master-select"><option value="">Select Embellishment</option>@foreach($embellishments as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->embellishmentname }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Manufacturing Process</label><select id="editModalManufacturing" class="form-select edit-modal-master-select"><option value="">Select Manufacturing Process</option>@foreach($manufacturingProcesses as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->manufacturing_process }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Craftsman</label><select id="editModalCraftsman" class="form-select edit-modal-master-select"><option value="">Select Craftsman</option>@foreach($craftsmen as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->name }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Manufacture</label><select id="editModalManufacture" class="form-select edit-modal-master-select"><option value="">Select Manufacture</option>@foreach($manufactures as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->name }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                        <div class="col-md-6 barcode-edit-field"><label>Collection / Client</label><select id="editModalClient" class="form-select edit-modal-master-select"><option value="">Select Collection</option>@foreach($clients as $x)<option value="{{ $x->id }}" data-code="{{ $x->code ?? '' }}">{{ $x->name }}@if(!empty($x->code)) ({{ $x->code }}) @endif</option>@endforeach</select></div>
                    </div>
                </div>

                <div class="barcode-edit-section">
                    <div class="barcode-edit-section-title"><i class="bi bi-tag"></i>Other Product Details</div>
                    <div class="row g-3">
                        <div class="col-md-4 barcode-edit-field"><label>SKU</label><input id="editModalSku" class="form-control barcode-edit-readonly" readonly></div>
                        <div class="col-md-4 barcode-edit-field"><label>Product Price</label><input type="number" id="editModalPrice" class="form-control"></div>
                        <div class="col-md-4 barcode-edit-field"><label>Sale Price</label><input type="number" id="editModalSalePrice" class="form-control"></div>
                        <div class="col-md-4 barcode-edit-field"><label>Min Price</label><input type="number" id="editModalMinPrice" class="form-control"></div>
                        <div class="col-md-8 barcode-edit-field"><label>Client Reference / Description</label><textarea id="editModalClientReference" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>Close</button>
                <button type="button" class="btn btn-primary" id="btnProceedBarcodeEdit"><i class="bi bi-check2-circle me-1"></i>Edit & Generate New Barcode</button>
            </div>
        </div>
    </div>
</div>

{{-- LARGE IMAGE MODAL --}}
<div
    class="modal fade large-image-modal"
    id="largeProductImageModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Product Image
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>

            <div class="modal-body">
                <img
                    id="largeProductImage"
                    src=""
                    alt="Product Image"
                >
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const itemType =
        document.getElementById('filterItemType');

    const itemName =
        document.getElementById('filterItemName');

    const composition =
        document.getElementById('filterComposition');

    const gender =
        document.getElementById('filterGender');

    const applyButton =
        document.getElementById('btnApplyFilters');

    const clearButton =
        document.getElementById('btnClearFilters');

    const tableBody =
        document.getElementById('productSpecificationTableBody');

    const totalProducts =
        document.getElementById('totalProducts');

    const pagination =
        document.getElementById('specificationPagination');

    const paginationInfo =
        document.getElementById('paginationInfo');

    const paginationList =
        document.getElementById('specificationPaginationList');

    const perPageSelect =
        document.getElementById('specificationPerPage');

    const detailsModalElement =
        document.getElementById('productDetailsModal');

    const detailsLoading =
        document.getElementById('productDetailsLoading');

    const detailsContent =
        document.getElementById('productDetailsContent');

    const detailsEmpty =
        document.getElementById('productDetailsEmpty');

    const modalProductImageContainer =
        document.getElementById('modalProductImageContainer');

    const modalProductInfo =
        document.getElementById('modalProductInfo');

    const modalStockTableBody =
        document.getElementById('modalStockTableBody');

    const modalStockCount =
        document.getElementById('modalStockCount');

    const largeImageModalElement =
        document.getElementById('largeProductImageModal');

    const largeProductImage =
        document.getElementById('largeProductImage');

    const editBarcodeModalElement = document.getElementById('editBarcodeDetailsModal');
    const editBarcodeDetailsContent = document.getElementById('editBarcodeDetailsContent');
    const btnGoForEditBarcode = document.getElementById('btnGoForEditBarcode');
    const barcodeEditFormModalElement = document.getElementById('barcodeEditFormModal');
    const btnProceedBarcodeEdit = document.getElementById('btnProceedBarcodeEdit');
    let selectedEditStock = null;


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let currentPage = 1;

    let currentPerPage =
        Number(perPageSelect?.value || 20);

    let currentPagination = null;


    /*
    |--------------------------------------------------------------------------
    | BOOTSTRAP MODAL HELPERS
    |--------------------------------------------------------------------------
    */

    function getBootstrapModal(element) {

        if (
            !element ||
            typeof bootstrap === 'undefined' ||
            !bootstrap.Modal
        ) {
            return null;
        }

        return bootstrap.Modal.getOrCreateInstance(
            element
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HTML ESCAPE
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    /*
    |--------------------------------------------------------------------------
    | QUANTITY FORMAT
    |--------------------------------------------------------------------------
    */

    function formatQuantity(value) {

        const number =
            Number(value || 0);

        if (Number.isInteger(number)) {
            return number.toLocaleString();
        }

        return number.toLocaleString(
            undefined,
            {
                maximumFractionDigits: 2
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DATE / VALUE FORMAT
    |--------------------------------------------------------------------------
    */

    function displayValue(value) {

        if (
            value === null ||
            value === undefined ||
            String(value).trim() === ''
        ) {
            return '-';
        }

        return String(value);
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD PRODUCTS
    |--------------------------------------------------------------------------
    */

    function loadProducts(page = 1) {

        currentPage = Math.max(
            1,
            Number(page || 1)
        );

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="8"
                    class="text-center py-4"
                >
                    <div
                        class="spinner-border spinner-border-sm"
                        role="status"
                    ></div>

                    <span class="ms-2">
                        Loading products...
                    </span>
                </td>
            </tr>
        `;

        pagination.style.display = 'none';

        const params =
            new URLSearchParams();


        /*
        |--------------------------------------------------------------------------
        | FILTER VALUES
        |--------------------------------------------------------------------------
        */

        if (itemType.value) {
            params.append(
                'item_type',
                itemType.value
            );
        }

        if (itemName.value) {
            params.append(
                'item_name',
                itemName.value
            );
        }

        if (composition.value) {
            params.append(
                'composition',
                composition.value
            );
        }

        if (gender.value) {
            params.append(
                'gender',
                gender.value
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION VALUES
        |--------------------------------------------------------------------------
        */

        params.append(
            'page',
            currentPage
        );

        params.append(
            'per_page',
            currentPerPage
        );


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        fetch(
            "{{ route('product-specification-masters.data') }}" +
            '?' +
            params.toString(),
            {
                method: 'GET',

                headers: {
                    'X-Requested-With':
                        'XMLHttpRequest',

                    'Accept':
                        'application/json'
                }
            }
        )
        .then(function (response) {

            if (!response.ok) {
                throw new Error(
                    'Unable to load product data.'
                );
            }

            return response.json();
        })
        .then(function (result) {

            if (
                !result ||
                result.success !== true
            ) {
                throw new Error(
                    result?.message ||
                    'Unable to load product data.'
                );
            }

            currentPagination =
                result.pagination || {
                    current_page: currentPage,
                    per_page: currentPerPage,
                    total: (result.data || []).length,
                    last_page: 1,
                    from: 0,
                    to: 0
                };

            renderProducts(
                result.data || [],
                currentPagination
            );

            renderPagination(
                currentPagination
            );
        })
        .catch(function (error) {

            console.error(
                'Product Specification Master:',
                error
            );

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="8"
                        class="text-center text-danger py-4"
                    >
                        ${escapeHtml(
                            error.message ||
                            'Something went wrong.'
                        )}
                    </td>
                </tr>
            `;

            totalProducts.textContent = '0';
            pagination.style.display = 'none';
        });
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER PRODUCTS
    |--------------------------------------------------------------------------
    */

    function renderProducts(
        products,
        meta
    ) {

        tableBody.innerHTML = '';

        totalProducts.textContent =
            formatQuantity(
                meta?.total ?? products.length
            );


        if (!products.length) {

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="8"
                        class="text-center py-5 text-muted"
                    >
                        <i
                            class="bi bi-box-seam"
                            style="font-size:32px;"
                        ></i>

                        <div class="mt-2">
                            No product specification stock found.
                        </div>
                    </td>
                </tr>
            `;

            return;
        }


        products.forEach(
            function (product, index) {

                const row =
                    document.createElement('tr');

                const rowNumber =
                    Number(meta?.from || 0) +
                    index;

                const imageUrl =
                    product.first_image || '';

                let imageHtml = '';

                if (imageUrl) {

                    imageHtml = `
                        <img
                            src="${escapeHtml(imageUrl)}"
                            alt="Product Image"
                            class="product-table-image js-product-image"
                            data-image="${escapeHtml(imageUrl)}"
                            loading="lazy"
                            onerror="
                                this.outerHTML =
                                '<div class=&quot;no-product-image&quot;><i class=&quot;bi bi-image&quot;></i><span>Unavailable</span></div>';
                            "
                        >
                    `;

                } else {

                    imageHtml = `
                        <div class="no-product-image">
                            <i class="bi bi-image"></i>
                            <span>No Image</span>
                        </div>
                    `;
                }


                row.innerHTML = `

                    <td>
                        ${rowNumber}
                    </td>

                    <td class="product-image-cell">
                        ${imageHtml}
                    </td>

                    <td>
                        ${escapeHtml(
                            product.item_type_text || '-'
                        )}
                    </td>

                    <td>
                        <strong>
                            ${escapeHtml(
                                product.item_name_text || '-'
                            )}
                        </strong>
                    </td>

                    <td>
                        ${escapeHtml(
                            product.composition_text || '-'
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            product.gender_text || '-'
                        )}
                    </td>

                    <td class="text-end">
                        <span class="badge bg-success stock-qty-badge">
                            ${formatQuantity(
                                product.stock_qty
                            )}
                        </span>
                    </td>

                    <td class="text-center">
                        <button
                            type="button"
                            class="btn btn-sm btn-primary view-product-btn js-view-product"
                            data-item-type="${escapeHtml(
                                product.item_type || ''
                            )}"
                            data-item-name="${escapeHtml(
                                product.item_name || ''
                            )}"
                            data-composition="${escapeHtml(
                                product.composition || ''
                            )}"
                            data-gender="${escapeHtml(
                                product.gender || ''
                            )}"
                        >
                            <i class="bi bi-eye me-1"></i>
                            View
                        </button>
                    </td>

                `;

                tableBody.appendChild(row);
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    function renderPagination(meta) {

        if (
            !meta ||
            Number(meta.total || 0) <= 0 ||
            Number(meta.last_page || 1) <= 1
        ) {
            pagination.style.display =
                Number(meta?.total || 0) > 0
                    ? 'flex'
                    : 'none';

            paginationInfo.textContent =
                Number(meta?.total || 0) > 0
                    ? `Showing ${meta.from} to ${meta.to} of ${meta.total} products`
                    : '';

            paginationList.innerHTML = '';

            return;
        }


        pagination.style.display = 'flex';


        paginationInfo.textContent =
            `Showing ${meta.from} to ${meta.to} of ${meta.total} products`;


        paginationList.innerHTML = '';


        const current =
            Number(meta.current_page || 1);

        const last =
            Number(meta.last_page || 1);


        /*
        |--------------------------------------------------------------------------
        | PREVIOUS
        |--------------------------------------------------------------------------
        */

        addPaginationItem(
            '‹',
            current - 1,
            current <= 1,
            'Previous'
        );


        /*
        |--------------------------------------------------------------------------
        | PAGE NUMBERS
        |--------------------------------------------------------------------------
        */

        const pages =
            getPaginationPages(
                current,
                last
            );

        pages.forEach(
            function (page) {

                if (page === '...') {

                    const li =
                        document.createElement('li');

                    li.className =
                        'page-item disabled';

                    li.innerHTML = `
                        <span class="page-link">
                            …
                        </span>
                    `;

                    paginationList.appendChild(li);

                    return;
                }

                addPaginationItem(
                    String(page),
                    page,
                    false,
                    `Page ${page}`,
                    page === current
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | NEXT
        |--------------------------------------------------------------------------
        */

        addPaginationItem(
            '›',
            current + 1,
            current >= last,
            'Next'
        );
    }


    function addPaginationItem(
        label,
        page,
        disabled,
        ariaLabel,
        active = false
    ) {

        const li =
            document.createElement('li');

        li.className =
            'page-item' +
            (disabled ? ' disabled' : '') +
            (active ? ' active' : '');

        const button =
            document.createElement('button');

        button.type = 'button';

        button.className =
            'page-link';

        button.innerHTML =
            label;

        button.setAttribute(
            'aria-label',
            ariaLabel
        );

        if (active) {
            button.setAttribute(
                'aria-current',
                'page'
            );
        }

        if (disabled) {
            button.disabled = true;
        } else {
            button.addEventListener(
                'click',
                function () {
                    loadProducts(page);
                }
            );
        }

        li.appendChild(button);

        paginationList.appendChild(li);
    }


    function getPaginationPages(
        current,
        last
    ) {

        if (last <= 7) {
            return Array.from(
                {
                    length: last
                },
                function (_, i) {
                    return i + 1;
                }
            );
        }


        const pages = [1];


        if (current > 4) {
            pages.push('...');
        }


        const start =
            Math.max(
                2,
                current - 1
            );

        const end =
            Math.min(
                last - 1,
                current + 1
            );


        for (
            let page = start;
            page <= end;
            page++
        ) {
            pages.push(page);
        }


        if (current < last - 3) {
            pages.push('...');
        }


        pages.push(last);

        return pages;
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN LARGE IMAGE
    |--------------------------------------------------------------------------
    */

    function openLargeImage(imageUrl) {

        if (!imageUrl) {
            return;
        }

        largeProductImage.src =
            imageUrl;

        const modal =
            getBootstrapModal(
                largeImageModalElement
            );

        if (modal) {
            modal.show();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAILS
    |--------------------------------------------------------------------------
    */

    function openProductDetails(product) {

        resetDetailsModal();

        const modal =
            getBootstrapModal(
                detailsModalElement
            );

        if (modal) {
            modal.show();
        }

        const params =
            new URLSearchParams();

        params.append(
            'item_type',
            product.item_type || ''
        );

        params.append(
            'item_name',
            product.item_name || ''
        );

        params.append(
            'composition',
            product.composition || ''
        );

        params.append(
            'gender',
            product.gender || ''
        );


        fetch(
            "{{ route('product-specification-masters.details') }}" +
            '?' +
            params.toString(),
            {
                method: 'GET',

                headers: {
                    'X-Requested-With':
                        'XMLHttpRequest',

                    'Accept':
                        'application/json'
                }
            }
        )
        .then(function (response) {

            if (!response.ok) {

                return response
                    .json()
                    .catch(function () {
                        return {};
                    })
                    .then(function (errorResult) {

                        throw new Error(
                            errorResult?.message ||
                            'Unable to load product details.'
                        );
                    });
            }

            return response.json();
        })
        .then(function (result) {

            if (
                !result ||
                result.success !== true
            ) {
                throw new Error(
                    result?.message ||
                    'Unable to load product details.'
                );
            }

            if (!result.product) {

                detailsLoading.style.display =
                    'none';

                detailsContent.style.display =
                    'none';

                detailsEmpty.style.display =
                    'block';

                return;
            }


            renderProductDetails(
                result.product,
                result.stock || []
            );
        })
        .catch(function (error) {

            console.error(
                'Product Details:',
                error
            );

            detailsLoading.style.display =
                'none';

            detailsContent.style.display =
                'none';

            detailsEmpty.style.display =
                'block';

            detailsEmpty.innerHTML = `
                <i class="bi bi-exclamation-triangle text-danger"
                   style="font-size:30px;"></i>

                <div class="mt-2 text-danger">
                    ${escapeHtml(
                        error.message ||
                        'Unable to load product details.'
                    )}
                </div>
            `;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | RESET DETAILS MODAL
    |--------------------------------------------------------------------------
    */

    function resetDetailsModal() {

        detailsLoading.style.display =
            'block';

        detailsContent.style.display =
            'none';

        detailsEmpty.style.display =
            'none';

        detailsEmpty.innerHTML =
            'No stock details found.';

        modalProductImageContainer.innerHTML =
            '';

        const modalProductSubImages =
            document.getElementById(
                'modalProductSubImages'
            );

        if (modalProductSubImages) {
            modalProductSubImages.innerHTML = '';
        }

        modalProductInfo.innerHTML =
            '';

        modalStockTableBody.innerHTML =
            '';

        modalStockCount.textContent =
            '0';
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER PRODUCT DETAILS
    |--------------------------------------------------------------------------
    */

    function renderProductDetails(
        product,
        stock
    ) {

        window.__productStockDetails = stock || [];

        detailsLoading.style.display =
            'none';

        detailsContent.style.display =
            'block';

        detailsEmpty.style.display =
            'none';


        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGE
        |--------------------------------------------------------------------------
        */

        if (product.first_image) {

            modalProductImageContainer.innerHTML = `
                <img
                    src="${escapeHtml(
                        product.first_image
                    )}"
                    alt="Product Image"
                    class="modal-product-image"
                    id="modalProductImage"
                    onerror="
                        this.parentElement.innerHTML =
                        '<div class=&quot;modal-no-image&quot;><i class=&quot;bi bi-image&quot;></i><div>Image unavailable</div></div>';
                    "
                >
            `;

            const modalImage =
                document.getElementById(
                    'modalProductImage'
                );

            if (modalImage) {

                modalImage.addEventListener(
                    'click',
                    function () {
                        openLargeImage(
                            this.src
                        );
                    }
                );
            }

        } else {

            modalProductImageContainer.innerHTML = `
                <div class="modal-no-image">
                    <i class="bi bi-image"></i>
                    <div>No Image</div>
                </div>
            `;
        }


        /*
        |--------------------------------------------------------------------------
        | SUB IMAGES
        |--------------------------------------------------------------------------
        */

        const subImages = Array.isArray(product.sub_images)
            ? product.sub_images
            : [];

        const subImagesWrapper = document.getElementById(
            'modalProductSubImages'
        );

        if (subImagesWrapper) {

            if (subImages.length) {

                subImagesWrapper.innerHTML = subImages
                    .map(function (image) {
                        return `
                            <img
                                src="${escapeHtml(image)}"
                                alt="Product Sub Image"
                                class="product-sub-image js-product-sub-image"
                                data-image="${escapeHtml(image)}"
                                loading="lazy"
                                onerror="this.style.display='none';"
                            >
                        `;
                    })
                    .join('');

                subImagesWrapper
                    .querySelectorAll('.js-product-sub-image')
                    .forEach(function (imageElement) {

                        imageElement.addEventListener(
                            'click',
                            function () {
                                openLargeImage(
                                    this.dataset.image
                                );
                            }
                        );
                    });

            } else {
                subImagesWrapper.innerHTML = '';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT INFORMATION
        |--------------------------------------------------------------------------
        */

        const infoRows = [

            [
                'Designer',
                product.designer_name
            ],

            [
                'Item Type',
                product.item_type
            ],

            [
                'Item Name',
                product.item_name
            ],

            [
                'Composition',
                product.composition
            ],

            [
                'Gender Type',
                product.gender
            ],

            [
                'Colour',
                product.colour
            ],

            [
                'Sizes',
                product.sizes
            ],

            [
                'Embellishment',
                product.embellishment
            ],

            [
                'Manufacturing Process',
                product.manufacturing_process
            ],

            [
                'Client Reference',
                product.clientreference
            ],

            [
                'Stock Qty',
                formatQuantity(
                    product.stock_qty
                )
            ]

        ];


        modalProductInfo.innerHTML =
            infoRows.map(
                function (row) {

                    return `
                        <div class="product-info-box">

                            <span class="product-info-label">
                                ${escapeHtml(
                                    row[0]
                                )}
                            </span>

                            <span class="product-info-value">
                                ${escapeHtml(
                                    displayValue(
                                        row[1]
                                    )
                                )}
                            </span>

                        </div>
                    `;
                }
            ).join('');


        /*
        |--------------------------------------------------------------------------
        | STOCK DETAILS
        |--------------------------------------------------------------------------
        */

        modalStockCount.textContent =
            formatQuantity(
                stock.length
            );

        modalStockTableBody.innerHTML =
            '';


        if (!stock.length) {

            modalStockTableBody.innerHTML = `
                <tr>
                    <td
                        colspan="9"
                        class="text-center text-muted py-4"
                    >
                        No stock details found.
                    </td>
                </tr>
            `;

            return;
        }


        stock.forEach(
            function (row, index) {

                const tr =
                    document.createElement('tr');

                const stockImage = row.stock_image || '';

                let stockImageHtml = '';

                if (stockImage) {
                    stockImageHtml = `
                        <img
                            src="${escapeHtml(stockImage)}"
                            alt="Product Image"
                            class="stock-row-image js-stock-row-image"
                            data-image="${escapeHtml(stockImage)}"
                            loading="lazy"
                        >
                    `;
                } else {
                    stockImageHtml = `
                        <div class="stock-row-no-image"><i class="bi bi-image"></i></div>
                    `;
                }

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td class="text-center">${stockImageHtml}</td>
                    <td>${escapeHtml(displayValue(row.barcode))}</td>
                    <td class="text-end fw-bold">${formatQuantity(row.available_qty)}</td>
                    <td>${escapeHtml(displayValue(row.stock_date))}</td>
                    <td>${escapeHtml(displayValue(row.boxno))}</td>
                    <td>${escapeHtml(displayValue(row.warehousename))}</td>
                    <td>${escapeHtml(displayValue(row.locationname))}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-warning js-edit-stock-product" data-stock-id="${escapeHtml(row.stock_id || '')}" data-barcode="${escapeHtml(row.barcode || '')}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                    </td>
                `;

                modalStockTableBody.appendChild(
                    tr
                );

                const rowImage = tr.querySelector('.js-stock-row-image');
                if (rowImage) {
                    rowImage.addEventListener('error', function () {
                        const fallback = document.createElement('div');
                        fallback.className = 'stock-row-no-image';
                        fallback.innerHTML = '<i class="bi bi-image"></i>';
                        this.replaceWith(fallback);
                    }, { once: true });
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT BARCODE DETAILS
    |--------------------------------------------------------------------------
    */

    function renderEditBarcodeDetails(row) {
        const imageHtml = row.stock_image
            ? `<div class="edit-barcode-image-wrap"><img src="${escapeHtml(row.stock_image)}" alt="Product Image" class="edit-barcode-image"></div>`
            : `<div class="edit-barcode-image-wrap"><div class="modal-no-image"><i class="bi bi-image"></i><div>No Image</div></div></div>`;

        const details = [
            ['Barcode', row.barcode], ['SKU', row.sku], ['Designer', row.designer_name],
            ['Item Type', row.item_type], ['Item Name', row.item_name], ['Composition', row.composition],
            ['Gender Type', row.gender], ['Composition', row.composition], ['Yarn Name', row.yarn],
            ['Colour', row.colour], ['Sizes', row.sizes], ['Embellishment', row.embellishment],
            ['Manufacturing Process', row.manufacturing_process], ['Craftsman', row.craftsman],
            ['Manufacture', row.manufacture], ['Collection / Client', row.client],
            ['Client Reference', row.clientreference], ['Available Qty', formatQuantity(row.available_qty)],
            ['Stock Date', row.stock_date], ['Box No', row.boxno], ['Warehouse', row.warehousename],
            ['Location', row.locationname]
        ];

        editBarcodeDetailsContent.innerHTML = `<div class="edit-barcode-top">${imageHtml}<div class="edit-barcode-info-grid">${details.map(function (detail) {
            return `<div class="product-info-box"><span class="product-info-label">${escapeHtml(detail[0])}</span><span class="product-info-value">${escapeHtml(displayValue(detail[1]))}</span></div>`;
        }).join('')}</div></div>`;

        const editImage = editBarcodeDetailsContent.querySelector('.edit-barcode-image');
        if (editImage) {
            editImage.addEventListener('click', function () { openLargeImage(this.src); });
            editImage.addEventListener('error', function () {
                this.parentElement.innerHTML = `<div class="modal-no-image"><i class="bi bi-image"></i><div>Image unavailable</div></div>`;
            }, { once: true });
        }
    }

    function setEditModalSelect(id, value) {
        const element = document.getElementById(id);
        if (!element) return;
        const normalized = value === null || value === undefined ? '' : String(value);
        element.value = normalized;
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2 && jQuery(element).hasClass('select2-hidden-accessible')) {
            jQuery(element).val(normalized).trigger('change');
        }
    }

    function setEditModalInput(id, value) {
        const element = document.getElementById(id);
        if (element) element.value = value === null || value === undefined ? '' : value;
    }

    function renderBarcodeEditForm(row) {
        if (!row) return;

        setEditModalInput('editModalBarcode', row.barcode);
        setEditModalInput('editModalStockId', row.stock_id);

        setEditModalSelect('editModalItemName', row.item_name_id);
        setEditModalSelect('editModalItemType', row.item_type_id);
        setEditModalSelect('editModalDesigner', row.designer_id);
        setEditModalSelect('editModalGender', row.gender_id);
        setEditModalSelect('editModalComposition', row.composition_id);
        setEditModalSelect('editModalYarn', row.yarn_id);
        setEditModalSelect('editModalColour', row.colour_id);
        setEditModalSelect('editModalSize', row.sizes_id);
        setEditModalSelect('editModalEmbellishment', row.embellishment_id);
        setEditModalSelect('editModalManufacturing', row.manufacturing_process_id);
        setEditModalSelect('editModalCraftsman', row.craftsman_id);
        setEditModalSelect('editModalManufacture', row.manufacture_id);
        setEditModalSelect('editModalClient', row.client_id);

        setEditModalInput('editModalSku', row.sku);
        setEditModalInput('editModalPrice', row.price);
        setEditModalInput('editModalSalePrice', row.sale_price);
        setEditModalInput('editModalMinPrice', row.min_price);
        setEditModalInput('editModalClientReference', row.clientreference);

        const images = document.getElementById('barcodeEditImages');
        if (images) {
            images.innerHTML = '';
            const allImages = [];
            if (row.stock_image) allImages.push(row.stock_image);
            if (Array.isArray(row.sub_images)) row.sub_images.forEach(function (image) { if (image) allImages.push(image); });
            if (!allImages.length) {
                images.innerHTML = '<div class="text-muted py-3">No image available.</div>';
            } else {
                allImages.forEach(function (image) {
                    const img = document.createElement('img');
                    img.src = image;
                    img.alt = 'Product Image';
                    img.addEventListener('click', function () { openLargeImage(this.src); });
                    img.addEventListener('error', function () { this.remove(); }, { once: true });
                    images.appendChild(img);
                });
            }
        }
    }

    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery('.edit-modal-master-select').select2({
            width: '100%',
            dropdownParent: jQuery('#barcodeEditFormModal')
        });
    }

    if (btnGoForEditBarcode) {
        btnGoForEditBarcode.addEventListener('click', function () {
            if (!selectedEditStock) return;
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to edit this barcode?',
                html: '<div style="font-size:16px;line-height:1.7;">If you edit this product, its specification details may change.<br><strong>This may change the existing barcode and create a new barcode.</strong></div>',
                showCancelButton: true,
                confirmButtonText: 'Yes, Go For Edit Barcode',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                width: '600px'
            }).then(function (result) {
                if (result.isConfirmed) {
                    renderBarcodeEditForm(selectedEditStock);
                    const oldModal = getBootstrapModal(editBarcodeModalElement);
                    if (oldModal) oldModal.hide();
                    const editFormModal = getBootstrapModal(barcodeEditFormModalElement);
                    if (editFormModal) editFormModal.show();
                }
            });
        });
    }



    /*
    |--------------------------------------------------------------------------
    | SAVE BARCODE EDIT
    |--------------------------------------------------------------------------
    */

    if (btnProceedBarcodeEdit) {
        btnProceedBarcodeEdit.addEventListener('click', function () {

            if (!selectedEditStock) {
                return;
            }

            const requiredFields = [
                ['editModalItemName', 'Item Name'],
                ['editModalItemType', 'Item Type'],
                ['editModalDesigner', 'Designer'],
                ['editModalGender', 'Gender Type'],
                ['editModalComposition', 'Composition'],
                ['editModalColour', 'Colour'],
                ['editModalSize', 'Size']
            ];

            /*
            |--------------------------------------------------------------------------
            | REQUIRED FIELD VALIDATION
            |--------------------------------------------------------------------------
            | All required fields only need a selected value.
            */
            for (const field of requiredFields) {

                const element =
                    document.getElementById(field[0]);

                if (
                    !element ||
                    !String(element.value || '').trim()
                ) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Please check',
                        text:
                            'Please select ' +
                            field[1] +
                            '.'
                    }).then(function () {

                        if (!element) {
                            return;
                        }

                        if (
                            typeof jQuery !== 'undefined' &&
                            jQuery.fn.select2 &&
                            jQuery(element).hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            jQuery(element).select2('open');
                        } else {
                            element.focus();
                        }

                    });

                    return;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | ITEM NAME CODE VALIDATION ONLY
            |--------------------------------------------------------------------------
            | Only Item Name must have a Code.
            */
            const itemNameElement =
                document.getElementById('editModalItemName');

            const itemNameOption =
                itemNameElement?.options[
                    itemNameElement.selectedIndex
                ];

            const itemNameCode =
                itemNameOption?.getAttribute('data-code') || '';

            if (!itemNameCode.trim()) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text:
                        'Selected Item Name does not have a Code. Please update the Item Name master and add a Code.'
                }).then(function () {

                    if (
                        typeof jQuery !== 'undefined' &&
                        jQuery.fn.select2 &&
                        jQuery(itemNameElement).hasClass(
                            'select2-hidden-accessible'
                        )
                    ) {
                        jQuery(itemNameElement).select2('open');
                    } else if (itemNameElement) {
                        itemNameElement.focus();
                    }

                });

                return;
            }

            const specificationSno =
                selectedEditStock.specification_sno ||
                selectedEditStock.specification_id ||
                '';

            if (!specificationSno) {
                Swal.fire({
                    icon: 'error',
                    title: 'Specification not found',
                    text:
                        'Unable to identify the specification record.'
                });

                return;
            }

            const formData =
                new FormData();

            formData.append(
                '_token',
                document.getElementById(
                    'barcodeEditCsrfToken'
                )?.value || ''
            );

            const appendValue = function (
                fieldName,
                elementId
            ) {
                const element =
                    document.getElementById(elementId);

                formData.append(
                    fieldName,
                    element?.value || ''
                );
            };

            appendValue(
                'item_name',
                'editModalItemName'
            );

            appendValue(
                'item_type',
                'editModalItemType'
            );

            appendValue(
                'designer_name',
                'editModalDesigner'
            );

            appendValue(
                'gender',
                'editModalGender'
            );

            appendValue(
                'composition',
                'editModalComposition'
            );

            appendValue(
                'yarn',
                'editModalYarn'
            );

            appendValue(
                'colour',
                'editModalColour'
            );

            appendValue(
                'sizes',
                'editModalSize'
            );

            appendValue(
                'embellishment',
                'editModalEmbellishment'
            );

            appendValue(
                'manufacturing_process',
                'editModalManufacturing'
            );

            appendValue(
                'craftsman',
                'editModalCraftsman'
            );

            appendValue(
                'manufecture',
                'editModalManufacture'
            );

            appendValue(
                'client',
                'editModalClient'
            );

            appendValue(
                'price',
                'editModalPrice'
            );

            appendValue(
                'saleprice',
                'editModalSalePrice'
            );

            appendValue(
                'minprice',
                'editModalMinPrice'
            );

            appendValue(
                'clientreference',
                'editModalClientReference'
            );

            const codeFields = [
                ['item_name_code', 'editModalItemName'],
                ['item_type_code', 'editModalItemType'],
                ['designer_code', 'editModalDesigner'],
                ['gender_code', 'editModalGender'],
                ['composition_code', 'editModalComposition'],
                ['colour_code', 'editModalColour'],
                ['size_code', 'editModalSize']
            ];

            codeFields.forEach(function (item) {

                const element =
                    document.getElementById(item[1]);

                const option =
                    element?.options[
                        element.selectedIndex
                    ];

                formData.append(
                    item[0],
                    option?.getAttribute('data-code') || ''
                );
            });

            const craftsmanElement =
                document.getElementById(
                    'editModalCraftsman'
                );

            const craftsmanOption =
                craftsmanElement?.options[
                    craftsmanElement.selectedIndex
                ];

            formData.append(
                'craftsman_code',
                craftsmanOption?.getAttribute(
                    'data-code'
                ) ||
                selectedEditStock.craftsman_code ||
                ''
            );

            const oldBarcode =
                selectedEditStock.barcode || '';

            Swal.fire({
                title: 'Save Changes?',
                html:
                    'The selected master values will be used to generate the barcode.' +
                    '<br><strong>The old barcode will be kept in the barcode relation table.</strong>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText:
                    'Yes, Edit & Save',
                cancelButtonText:
                    'Cancel',
                reverseButtons: true,
                width: '600px'
            }).then(function (confirmResult) {

                if (!confirmResult.isConfirmed) {
                    return;
                }

                Swal.fire({
                    title: 'Saving...',
                    text:
                        'Generating the new barcode and saving the specification.',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

                fetch(
                    "{{ url('/admin/product-specification-masters') }}/" +
                    encodeURIComponent(
                        specificationSno
                    ),
                    {
                        method: 'POST',

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'
                        },

                        body: formData
                    }
                )
                .then(function (response) {

                    return response
                        .json()
                        .then(function (result) {

                            if (!response.ok) {

                                let message =
                                    result?.message ||
                                    'Unable to update product specification.';

                                if (
                                    result?.errors
                                ) {
                                    message =
                                        Object
                                            .values(
                                                result.errors
                                            )
                                            .flat()
                                            .join(' ');
                                }

                                throw new Error(
                                    message
                                );
                            }

                            return result;
                        });
                })
                .then(function (result) {

                    if (
                        !result ||
                        result.success !== true
                    ) {
                        throw new Error(
                            result?.message ||
                            'Unable to update product specification.'
                        );
                    }

                    const editFormModal =
                        getBootstrapModal(
                            barcodeEditFormModalElement
                        );

                    if (editFormModal) {
                        editFormModal.hide();
                    }

                    const detailsModal =
                        getBootstrapModal(detailsModalElement);

                    if (detailsModal) {
                        detailsModal.hide();
                    }

                    const editDetailsModal =
                        getBootstrapModal(editBarcodeModalElement);

                    if (editDetailsModal) {
                        editDetailsModal.hide();
                    }

                    const largeImageModal =
                        getBootstrapModal(largeImageModalElement);

                    if (largeImageModal) {
                        largeImageModal.hide();
                    }

                    selectedEditStock = null;


                    /*
                    |--------------------------------------------------------------------------
                    | REFRESH ALL PRODUCT DATA
                    |--------------------------------------------------------------------------
                    */

                    const newBarcode =
                        result.barcode || '';

                    let successHtml =
                        escapeHtml(
                            result.message ||
                            'Product specification updated successfully.'
                        );

                    if (result.barcode_changed) {

                        successHtml +=
                            '<br><br><strong>Old Barcode:</strong> ' +
                            escapeHtml(
                                result.old_barcode ||
                                oldBarcode
                            ) +
                            '<br><strong>New Barcode:</strong> ' +
                            escapeHtml(
                                newBarcode
                            );

                    } else {

                        successHtml +=
                            '<br><br><strong>Barcode:</strong> ' +
                            escapeHtml(
                                newBarcode ||
                                oldBarcode
                            );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS MESSAGE
                    |--------------------------------------------------------------------------
                    */

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved Successfully',
                        html: successHtml,
                        confirmButtonText: 'OK'
                    }).then(function () {

                        /*
                        |--------------------------------------------------------------------------
                        | LOAD FRESH DATA
                        |--------------------------------------------------------------------------
                        */

                        selectedEditStock = null;

                       

                        loadProducts(currentPage);
                    });

                })
                .catch(function (error) {

                    console.error(
                        'Product Specification Edit:',
                        error
                    );

                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to Save',
                        text:
                            error.message ||
                            'Something went wrong.'
                    });

                });

            });

        });
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT DELEGATION
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            /*
            | Image
            */

            const image =
                event.target.closest(
                    '.js-product-image'
                );

            if (image) {

                event.preventDefault();
                event.stopPropagation();

                openLargeImage(
                    image.dataset.image
                );

                return;
            }


            /*
            | Stock Row Image
            */

            const stockImage = event.target.closest('.js-stock-row-image');

            if (stockImage) {
                event.preventDefault();
                event.stopPropagation();
                openLargeImage(stockImage.dataset.image);
                return;
            }

            /*
            | Edit Stock Product
            */

            const editButton = event.target.closest('.js-edit-stock-product');

            if (editButton) {
                event.preventDefault();
                event.stopPropagation();

                const stockId = editButton.dataset.stockId || '';
                const barcode = editButton.dataset.barcode || '';

                selectedEditStock = (window.__productStockDetails || []).find(function (row) {
                    return String(row.stock_id || '') === String(stockId) && String(row.barcode || '') === String(barcode);
                }) || (window.__productStockDetails || []).find(function (row) {
                    return String(row.stock_id || '') === String(stockId);
                }) || null;

                if (!selectedEditStock) {
                    Swal.fire({ icon: 'error', title: 'Details not found', text: 'Unable to load the selected barcode details.' });
                    return;
                }

                renderEditBarcodeDetails(selectedEditStock);
                const editModal = getBootstrapModal(editBarcodeModalElement);
                if (editModal) editModal.show();
                return;
            }

            /*
            | View Product
            */

            const button =
                event.target.closest(
                    '.js-view-product'
                );

            if (!button) {
                return;
            }

            openProductDetails({
                item_type:
                    button.dataset.itemType,

                item_name:
                    button.dataset.itemName,

                composition:
                    button.dataset.composition,

                gender:
                    button.dataset.gender
            });
        }
    );


    /*
    |--------------------------------------------------------------------------
    | APPLY FILTERS
    |--------------------------------------------------------------------------
    */

    if (applyButton) {

        applyButton.addEventListener(
            'click',
            function () {

                currentPage = 1;

                loadProducts(1);
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAR FILTERS
    |--------------------------------------------------------------------------
    */

    if (clearButton) {

        clearButton.addEventListener(
            'click',
            function () {

                itemType.value = '';
                itemName.value = '';
                composition.value = '';
                gender.value = '';

                if (
                    typeof jQuery !== 'undefined' &&
                    jQuery.fn.select2
                ) {
                    jQuery(itemType).val('').trigger('change');
                    jQuery(itemName).val('').trigger('change');
                    jQuery(composition).val('').trigger('change');
                    jQuery(gender).val('').trigger('change');
                }

                currentPage = 1;

                loadProducts(1);
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PER PAGE
    |--------------------------------------------------------------------------
    */

    if (perPageSelect) {

        perPageSelect.addEventListener(
            'change',
            function () {

                currentPerPage =
                    Number(
                        this.value || 20
                    );

                currentPage = 1;

                loadProducts(1);
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RESET LARGE IMAGE
    |--------------------------------------------------------------------------
    */

    if (largeImageModalElement) {

        largeImageModalElement.addEventListener(
            'hidden.bs.modal',
            function () {

                largeProductImage.src =
                    '';
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    |
    | First load ALL available products.
    | Filters are applied only when selected.
    |
    */

    if (
        typeof jQuery !== 'undefined' &&
        jQuery.fn.select2
    ) {
        jQuery(
            '#filterItemType, #filterItemName, #filterComposition, #filterGender'
        ).select2({
            width: '100%',
            allowClear: true
        });
    }

    loadProducts(1);

});
</script>

@endsection