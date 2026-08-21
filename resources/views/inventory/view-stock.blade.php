@extends('layouts.app')

@section('content')

<style>

    .stock-page {
        width: 100%;
        max-width: 100%;
    }

    .stock-card {
        border: 1px solid #e3e8ef;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
    }

    .stock-header {
        padding: 20px 22px;
        border-bottom: 1px solid #e3e8ef;
    }

    .stock-header h4 {
        margin: 0;
        font-weight: 700;
    }

    .stock-filter {
        padding: 18px;
        background: #f8fafc;
        border-bottom: 1px solid #e3e8ef;
    }

    .stock-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .stock-table {
        width: 100%;
        min-width: 1500px;
        margin: 0;
        vertical-align: middle;
    }

    .stock-table th {
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        padding: 14px 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .stock-table td {
        padding: 12px;
        font-size: 13px;
        white-space: nowrap;
        border-bottom: 1px solid #edf0f3;
    }

    .product-image-box {
        width: 70px;
        height: 70px;
        border: 1px solid #dbe3ec;
        border-radius: 9px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .product-image-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .no-image {
        color: #94a3b8;
        font-size: 12px;
        text-align: center;
    }

    .product-name {
        font-weight: 700;
        color: #172033;
    }

    .sku-text {
        font-size: 11px;
        color: #64748b;
        margin-top: 3px;
    }

    .barcode-text {
        color: #2563eb;
        font-weight: 600;
    }

    .box-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 7px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 600;
    }

    .qty-badge {
        display: inline-block;
        min-width: 42px;
        padding: 6px 10px;
        border-radius: 7px;
        background: #dcfce7;
        color: #166534;
        text-align: center;
        font-weight: 700;
    }
    /* ============================================================
   PRODUCT STOCK MODAL
============================================================ */

.product-stock-modal {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
}


.product-stock-modal .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 18px 22px;
}


.product-stock-modal .modal-title {
    font-weight: 700;
    color: #172033;
}


/* PRODUCT TOP */

.product-detail-top {
    display: flex;
    gap: 24px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #fff;
}


.product-detail-image {
    width: 220px;
    min-width: 220px;
    height: 240px;
    border: 1px solid #dbe3ec;
    border-radius: 12px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}


.product-detail-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 10px;
}


.modal-no-image {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    gap: 8px;
}


.modal-no-image i {
    font-size: 42px;
}


/* PRODUCT INFORMATION */

.product-detail-info {
    flex: 1;
    min-width: 0;
}


.product-detail-name {
    margin-bottom: 16px;
}


.product-detail-name span {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 4px;
}


.product-detail-name strong {
    display: block;
    font-size: 22px;
    color: #172033;
}


.product-info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}


.product-info-item {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 8px;
    padding: 10px 12px;
}


.product-info-item span {
    display: block;
    font-size: 11px;
    color: #64748b;
    margin-bottom: 3px;
}


.product-info-item strong {
    display: block;
    font-size: 13px;
    color: #172033;
    word-break: break-word;
}


/* SUMMARY */

.stock-summary-card {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 18px;
}


.stock-summary-card > div {
    padding: 16px;
    border: 1px solid #dbe3ec;
    border-radius: 10px;
    background: #f8fafc;
}


.stock-summary-card span {
    display: block;
    font-size: 12px;
    color: #64748b;
}


.stock-summary-card strong {
    display: block;
    font-size: 25px;
    color: #2563eb;
    margin-top: 3px;
}


/* DISTRIBUTION */

.stock-distribution-section {
    margin-top: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}


.stock-distribution-title {
    padding: 14px 16px;
    font-weight: 700;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}


.stock-distribution-table {
    margin: 0;
    vertical-align: middle;
}


.stock-distribution-table th {
    white-space: nowrap;
    font-size: 12px;
    background: #f8fafc;
}


.stock-distribution-table td {
    font-size: 13px;
    white-space: nowrap;
}


.stock-qty-available {
    display: inline-block;
    min-width: 45px;
    padding: 5px 9px;
    border-radius: 6px;
    background: #dcfce7;
    color: #166534;
    font-weight: 700;
    text-align: center;
}


.stock-box-name {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #334155;
    font-weight: 600;
}


/* CLICKABLE IMAGE */

.product-image-box {
    cursor: pointer;
    transition: 0.2s;
}


.product-image-box:hover {
    transform: scale(1.04);
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

/* ============================================================
   STOCK TABLE
============================================================ */

.stock-table-wrapper {
    width: 100%;
    max-width: 100%;
}


.stock-table-wrapper .table-responsive {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}


/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.stock-table {
    width: 100%;
    min-width: 1250px;
    margin-bottom: 0;
}


.stock-table th {
    white-space: nowrap;
    vertical-align: middle;
}


.stock-table td {
    vertical-align: middle;
    white-space: nowrap;
}


/*
|--------------------------------------------------------------------------
| PRODUCT NAME
|--------------------------------------------------------------------------
*/

.stock-table td:nth-child(3) {
    min-width: 230px;
    white-space: normal;
}


/*
|--------------------------------------------------------------------------
| IMAGE
|--------------------------------------------------------------------------
*/

.stock-table .product-image-box {
    width: 70px;
    height: 70px;

    border: 1px solid #dbe3ec;
    border-radius: 10px;

    background: #f8fafc;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    cursor: pointer;

    transition: all 0.2s ease;
}


.stock-table .product-image-box:hover {
    transform: scale(1.04);
    border-color: #2563eb;
}


.stock-table .product-image-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 5px;
}


.stock-table .no-image {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #94a3b8;
}


/*
|--------------------------------------------------------------------------
| BOX
|--------------------------------------------------------------------------
*/

.stock-box-name {
    display: inline-block;

    padding: 5px 10px;

    background: #f1f5f9;

    border-radius: 6px;

    color: #334155;

    font-weight: 600;
}


/* ============================================================
   PAGINATION
============================================================ */

.stock-pagination-wrapper {

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding: 18px 4px 5px;

    flex-wrap: wrap;

}


/*
|--------------------------------------------------------------------------
| INFORMATION
|--------------------------------------------------------------------------
*/

.stock-pagination-info {

    color: #64748b;

    font-size: 14px;

    white-space: nowrap;

}


.stock-pagination-info strong {

    color: #172033;

    font-weight: 600;

}


/*
|--------------------------------------------------------------------------
| PAGINATION BUTTON AREA
|--------------------------------------------------------------------------
*/

.stock-pagination {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 5px;

    flex-wrap: wrap;

}


/*
|--------------------------------------------------------------------------
| PREVIOUS / NEXT
|--------------------------------------------------------------------------
*/

.pagination-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    min-height: 38px;

    padding: 0 13px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #334155;

    text-decoration: none;

    font-size: 14px;

    font-weight: 500;

    white-space: nowrap;

    transition: all 0.2s ease;

}


.pagination-btn:hover {

    background: #2563eb;

    border-color: #2563eb;

    color: #ffffff;

}


.pagination-btn.disabled {

    color: #94a3b8;

    background: #f8fafc;

    border-color: #e2e8f0;

    cursor: not-allowed;

}


/*
|--------------------------------------------------------------------------
| PAGE NUMBER
|--------------------------------------------------------------------------
*/

.pagination-number {

    width: 38px;

    height: 38px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 7px;

    border: 1px solid #d1d5db;

    background: #ffffff;

    color: #334155;

    text-decoration: none;

    font-size: 14px;

    font-weight: 500;

}


.pagination-number:hover {

    background: #eff6ff;

    border-color: #2563eb;

    color: #2563eb;

}


.pagination-number.active {

    background: #2563eb;

    border-color: #2563eb;

    color: #ffffff;

}


/*
|--------------------------------------------------------------------------
| ...
|--------------------------------------------------------------------------
*/

.pagination-dots {

    width: 30px;

    height: 38px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    color: #64748b;

}


/* ============================================================
   MOBILE
============================================================ */

@media (max-width: 768px) {

    .stock-pagination-wrapper {

        flex-direction: column;

        align-items: stretch;

        gap: 12px;

    }


    .stock-pagination-info {

        text-align: center;

        width: 100%;

    }


    .stock-pagination {

        width: 100%;

        justify-content: center;

    }

}


@media (max-width: 480px) {

    .pagination-btn {

        padding: 0 10px;

    }


    .pagination-number {

        width: 34px;

        height: 34px;

    }


    .pagination-dots {

        width: 22px;

    }

}


/* MOBILE */

@media (max-width: 768px) {

    .product-detail-top {
        flex-direction: column;
    }


    .product-detail-image {
        width: 100%;
        min-width: 0;
        height: 260px;
    }


    .product-info-grid {
        grid-template-columns: 1fr 1fr;
    }


    .stock-summary-card {
        grid-template-columns: 1fr;
    }

}

/* ============================================================
   BIG PRODUCT IMAGE
============================================================ */

#modalProductImage {
    cursor: zoom-in;
}


.big-image-modal-content {
    background: #111827;
    border: none;
    border-radius: 12px;
    overflow: hidden;
}


.big-image-modal-content .modal-header {
    border: none;
    padding: 10px 14px;
    background: #111827;
}


.big-image-modal-content .btn-close {
    filter: invert(1);
    opacity: 1;
}


.big-image-modal-body {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}


#bigProductImage {
    max-width: 100%;
    max-height: 80vh;
    width: auto;
    height: auto;
    object-fit: contain;
    cursor: zoom-out;
}


@media (max-width: 480px) {

    .product-info-grid {
        grid-template-columns: 1fr;
    }

}

    @media (max-width: 768px) {

        .stock-header {
            padding: 15px;
        }

        .stock-filter {
            padding: 12px;
        }

        .stock-table {
            min-width: 1450px;
        }

    }

</style>


<div class="container-fluid py-4 stock-page">

    <div class="stock-card">

        {{-- HEADER --}}
        <div class="stock-header">

            <div class="d-flex align-items-center justify-content-between">

                <div>

                    <h4>
                        <i class="bi bi-boxes me-2"></i>
                        View Stock
                    </h4>

                    <div class="text-muted small mt-1">
                        Ready to Sell Stock
                    </div>

                </div>

                <span class="badge bg-primary">
                    {{ $stocks->total() }} Records
                </span>

            </div>

        </div>


        {{-- FILTER --}}
        <div class="stock-filter">

            <form
                method="GET"
                action="{{ route('inventory.ready-to-sell-stock.view-stock') }}"
            >

                <div class="row g-2 align-items-center">

                    <div class="col-lg-5 col-md-5">

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Enter barcode, SKU, product name, box..."
                        >

                    </div>


                    <div class="col-lg-3 col-md-3">

                        <select
                            name="warehouse_id"
                            class="form-select"
                        >

                            <option value="">
                                All Warehouses
                            </option>

                            @foreach($warehouses as $warehouse)

                                <option
                                    value="{{ $warehouse->sno }}"
                                    {{ request('warehouse_id') == $warehouse->sno ? 'selected' : '' }}
                                >
                                    {{ $warehouse->warehousename }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-lg-2 col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >

                            <i class="bi bi-search me-1"></i>
                            Search

                        </button>

                    </div>


                    <div class="col-lg-2 col-md-2">

                        <a
                            href="{{ route('inventory.ready-to-sell-stock.view-stock') }}"
                            class="btn btn-outline-secondary w-100"
                        >

                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Clear

                        </a>

                    </div>

                </div>

            </form>

        </div>


       
        {{-- ============================================================
            STOCK TABLE
        ============================================================ --}}

        <div class="stock-table-wrapper">

            <div class="table-responsive">

                <table class="table stock-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Product Name</th>
                            <th>Type</th>
                            <th>Gender</th>
                            <th>Composition</th>
                            <th>Colour</th>
                            <th>Size</th>
                            <th>Warehouse</th>
                            <th>Location</th>
                            <th>Box No.</th>
                            <th>Qty</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($stocks as $index => $stock)

                            <tr>

                                <td>
                                    {{ $stocks->firstItem() + $index }}
                                </td>

                                <td>

                                    <div
                                        class="product-image-box stock-product-image"
                                        data-barcode="{{ $stock->barcode }}"
                                        title="Click to view product stock details"
                                    >

                                        @if(!empty($stock->image_url))

                                            <img
                                                src="{{ $stock->image_url }}"
                                                alt="Product"
                                                loading="lazy"
                                                onerror="
                                                    this.style.display='none';
                                                    this.nextElementSibling.style.display='flex';
                                                "
                                            >

                                            <div
                                                class="no-image"
                                                style="display:none;"
                                            >
                                                <i class="bi bi-image"></i>
                                            </div>

                                        @else

                                            <div class="no-image">
                                                <i class="bi bi-image"></i>
                                            </div>

                                        @endif

                                    </div>

                                </td>

                                <td>
                                    <strong>
                                        {{ $stock->product_name ?? '-' }}
                                    </strong>

                                    <small class="d-block text-muted">
                                        SKU:
                                        {{ $stock->sku ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $stock->item_type_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $stock->gender_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $stock->composition_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $stock->colour_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $stock->size_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $stock->warehousename ?? '-' }}
                                </td>

                                <td>
                                    {{ $stock->locationname ?? '-' }}
                                </td>

                                <td>

                                    <span class="stock-box-name">
                                        {{ $stock->boxno ?? '-' }}
                                    </span>

                                </td>

                                <td>
                                    {{-- {{ $stock->available_qty ?? 0 }} --}}
                                    <span class="qty-badge">

                                        {{ $stock->total_received }}

                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="12"
                                    class="text-center py-5"
                                >

                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                    No stock found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGINATION --}}
        {{-- @if($stocks->hasPages())

            <div class="p-3 border-top">

                {{ $stocks->links() }}

            </div>

        @endif --}}

        {{-- ============================================================
     PAGINATION
============================================================ --}}

@if($stocks->total() > 0)

    <div class="stock-pagination-wrapper">

        <div class="stock-pagination-info">

            Showing

            <strong>
                {{ $stocks->firstItem() }}
            </strong>

            to

            <strong>
                {{ $stocks->lastItem() }}
            </strong>

            of

            <strong>
                {{ $stocks->total() }}
            </strong>

            results

        </div>


        <div class="stock-pagination">

            @if($stocks->onFirstPage())

                <span class="pagination-btn disabled">
                    <i class="bi bi-chevron-left"></i>
                    Previous
                </span>

            @else

                <a
                    href="{{ $stocks->previousPageUrl() }}"
                    class="pagination-btn"
                >

                    <i class="bi bi-chevron-left"></i>
                    Previous

                </a>

            @endif


            {{-- PAGE NUMBERS --}}

            @php

                $currentPage =
                    $stocks->currentPage();

                $lastPage =
                    $stocks->lastPage();

                $startPage =
                    max(
                        1,
                        $currentPage - 2
                    );

                $endPage =
                    min(
                        $lastPage,
                        $currentPage + 2
                    );

            @endphp


            @if($startPage > 1)

                <a
                    href="{{ $stocks->url(1) }}"
                    class="pagination-number"
                >
                    1
                </a>


                @if($startPage > 2)

                    <span class="pagination-dots">
                        ...
                    </span>

                @endif

            @endif


            @for(
                $page = $startPage;
                $page <= $endPage;
                $page++
            )

                @if($page == $currentPage)

                    <span class="pagination-number active">
                        {{ $page }}
                    </span>

                @else

                    <a
                        href="{{ $stocks->url($page) }}"
                        class="pagination-number"
                    >
                        {{ $page }}
                    </a>

                @endif

            @endfor


            @if($endPage < $lastPage)

                @if($endPage < $lastPage - 1)

                    <span class="pagination-dots">
                        ...
                    </span>

                @endif


                <a
                    href="{{ $stocks->url($lastPage) }}"
                    class="pagination-number"
                >
                    {{ $lastPage }}
                </a>

            @endif


            {{-- NEXT --}}

            @if($stocks->hasMorePages())

                <a
                    href="{{ $stocks->nextPageUrl() }}"
                    class="pagination-btn"
                >

                    Next
                    <i class="bi bi-chevron-right"></i>

                </a>

            @else

                <span class="pagination-btn disabled">

                    Next
                    <i class="bi bi-chevron-right"></i>

                </span>

            @endif

        </div>

    </div>

@endif

    </div>

</div>

{{-- ============================================================
     PRODUCT STOCK DETAILS MODAL
============================================================ --}}

<div
    class="modal fade"
    id="productStockDetailsModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
    >

        <div class="modal-content product-stock-modal">


            {{-- HEADER --}}
            <div class="modal-header">

                <div>

                    <h5 class="modal-title">

                        <i class="bi bi-box-seam me-2"></i>

                        Product Stock Details

                    </h5>

                    <small class="text-muted">

                        Complete stock distribution

                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            {{-- BODY --}}
            <div class="modal-body">


                {{-- LOADER --}}
                <div
                    id="productStockLoading"
                    class="text-center py-5"
                >

                    <div
                        class="spinner-border text-primary"
                        role="status"
                    ></div>

                    <div class="mt-2 text-muted">

                        Loading product details...

                    </div>

                </div>


                {{-- CONTENT --}}
                <div
                    id="productStockContent"
                    style="display:none;"
                >


                    {{-- PRODUCT INFORMATION --}}
                    <div class="product-detail-top">


                        {{-- IMAGE --}}
                        <div class="product-detail-image">

                            <img
                                id="modalProductImage"
                                src=""
                                alt="Product"
                            >

                            <div
                                id="modalProductNoImage"
                                class="modal-no-image"
                                style="display:none;"
                            >

                                <i class="bi bi-image"></i>

                                <span>
                                    No Image
                                </span>

                            </div>

                        </div>


                        {{-- INFORMATION --}}
                        <div class="product-detail-info">

                            <div class="product-detail-name">

                                <span>
                                    Product Name
                                </span>

                                <strong
                                    id="modalProductName"
                                >
                                    -
                                </strong>

                            </div>


                            <div class="product-info-grid">


                                <div class="product-info-item">

                                    <span>Barcode</span>

                                    <strong
                                        id="modalProductBarcode"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>SKU</span>

                                    <strong
                                        id="modalProductSku"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Type</span>

                                    <strong
                                        id="modalProductType"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Gender</span>

                                    <strong
                                        id="modalProductGender"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Composition</span>

                                    <strong
                                        id="modalProductComposition"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Colour</span>

                                    <strong
                                        id="modalProductColour"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Size</span>

                                    <strong
                                        id="modalProductSize"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Designer</span>

                                    <strong
                                        id="modalProductDesigner"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="product-info-item">

                                    <span>Manufacturing</span>

                                    <strong
                                        id="modalProductManufacturing"
                                    >
                                        -
                                    </strong>

                                </div>


                            </div>

                        </div>

                    </div>


                    {{-- STOCK SUMMARY --}}
                    <div class="stock-summary-card">

                        <div>

                            <span>
                                Total Stock Available
                            </span>

                            <strong
                                id="modalTotalStock"
                            >
                                0
                            </strong>

                        </div>


                        <div>

                            <span>
                                Stock Locations
                            </span>

                            <strong
                                id="modalStockLocationCount"
                            >
                                0
                            </strong>

                        </div>

                    </div>


                    {{-- STOCK DISTRIBUTION --}}
                    <div class="stock-distribution-section">

                        <div class="stock-distribution-title">

                            <i class="bi bi-boxes me-2"></i>

                            Stock Distribution

                        </div>


                        <div class="table-responsive">

                            <table
                                class="table table-bordered stock-distribution-table"
                            >

                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Warehouse</th>

                                        <th>Location</th>

                                        <th>Box No.</th>

                                        <th>Received</th>

                                        <th>Sent</th>

                                        <th>Available Qty</th>

                                    </tr>

                                </thead>


                                <tbody
                                    id="modalStockTableBody"
                                >

                                </tbody>


                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

{{-- ============================================================
     BIG PRODUCT IMAGE MODAL
============================================================ --}}

<div
    class="modal fade"
    id="bigProductImageModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content big-image-modal-content">

            <div class="modal-header">

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>

            <div class="modal-body big-image-modal-body">

                <img
                    id="bigProductImage"
                    src=""
                    alt="Product Image"
                >

            </div>

        </div>

    </div>

</div>
<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | IMAGE CLICK
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function (event) {

                const imageBox =
                    event.target.closest(
                        '.stock-product-image'
                    );


                if (!imageBox) {
                    return;
                }


                const barcode =
                    imageBox.dataset.barcode;


                if (!barcode) {

                    alert(
                        'Barcode is not available.'
                    );

                    return;

                }


                loadProductStockDetails(
                    barcode
                );

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| LOAD PRODUCT DETAILS
|--------------------------------------------------------------------------
*/

function loadProductStockDetails(
    barcode
) {

    const modalElement =
        document.getElementById(
            'productStockDetailsModal'
        );


    const loading =
        document.getElementById(
            'productStockLoading'
        );


    const content =
        document.getElementById(
            'productStockContent'
        );


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    loading.style.display =
        'block';

    content.style.display =
        'none';


    /*
    |--------------------------------------------------------------------------
    | OPEN MODAL
    |--------------------------------------------------------------------------
    */

    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    modal.show();


    /*
    |--------------------------------------------------------------------------
    | CLEAR OLD DATA
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'modalStockTableBody'
    ).innerHTML = '';


    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    */

    const url =
        "{{ route('inventory.ready-to-sell-stock.product-details', ['barcode' => '__BARCODE__']) }}"
            .replace(
                '__BARCODE__',
                encodeURIComponent(barcode)
            );


    /*
    |--------------------------------------------------------------------------
    | AJAX
    |--------------------------------------------------------------------------
    */

    fetch(url, {

        method: 'GET',

        headers: {

            'Accept':
                'application/json',

            'X-Requested-With':
                'XMLHttpRequest'

        }

    })


    .then(
        function (response) {

            if (!response.ok) {

                throw new Error(
                    'Unable to load product details.'
                );

            }

            return response.json();

        }
    )


    .then(
        function (data) {

            if (!data.success) {

                throw new Error(
                    data.message ||
                    'Product details not found.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */

            const product =
                data.product;


            document.getElementById(
                'modalProductName'
            ).textContent =
                product.product_name || '-';


            document.getElementById(
                'modalProductBarcode'
            ).textContent =
                product.barcode || '-';


            document.getElementById(
                'modalProductSku'
            ).textContent =
                product.sku || '-';


            document.getElementById(
                'modalProductType'
            ).textContent =
                product.item_type_name || '-';


            document.getElementById(
                'modalProductGender'
            ).textContent =
                product.gender_name || '-';


            document.getElementById(
                'modalProductComposition'
            ).textContent =
                product.composition_name || '-';


            document.getElementById(
                'modalProductColour'
            ).textContent =
                product.colour_name || '-';


            document.getElementById(
                'modalProductSize'
            ).textContent =
                product.size_name || '-';


            document.getElementById(
                'modalProductDesigner'
            ).textContent =
                product.designer_name || '-';


            document.getElementById(
                'modalProductManufacturing'
            ).textContent =
                product.manufacturing_process_name || '-';


            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            const modalImage =
                document.getElementById(
                    'modalProductImage'
                );


            const noImage =
                document.getElementById(
                    'modalProductNoImage'
                );


            if (
                product.image_url
            ) {

                modalImage.src =
                    product.image_url;

                modalImage.style.display =
                    'block';

                noImage.style.display =
                    'none';


                modalImage.onerror =
                    function () {

                        modalImage.style.display =
                            'none';

                        noImage.style.display =
                            'flex';

                    };

            } else {

                modalImage.src =
                    '';

                modalImage.style.display =
                    'none';

                noImage.style.display =
                    'flex';

            }


            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                'modalTotalStock'
            ).textContent =
                data.total_stock || 0;


            document.getElementById(
                'modalStockLocationCount'
            ).textContent =
                data.stock.length;


            /*
            |--------------------------------------------------------------------------
            | STOCK TABLE
            |--------------------------------------------------------------------------
            */

            renderProductStockTable(
                data.stock
            );


            /*
            |--------------------------------------------------------------------------
            | SHOW CONTENT
            |--------------------------------------------------------------------------
            */

            loading.style.display =
                'none';

            content.style.display =
                'block';

        }
    )


    .catch(
        function (error) {

            console.error(
                error
            );


            loading.style.display =
                'none';


            content.style.display =
                'block';


            document.getElementById(
                'modalStockTableBody'
            ).innerHTML = `

                <tr>

                    <td
                        colspan="7"
                        class="text-center text-danger py-4"
                    >

                        <i class="bi bi-exclamation-triangle me-1"></i>

                        ${escapeHtml(
                            error.message ||
                            'Unable to load stock details.'
                        )}

                    </td>

                </tr>

            `;

        }
    );

}


/*
|--------------------------------------------------------------------------
| RENDER STOCK TABLE
|--------------------------------------------------------------------------
*/

function renderProductStockTable(
    stockList
) {

    const tbody =
        document.getElementById(
            'modalStockTableBody'
        );


    tbody.innerHTML = '';


    if (
        !stockList ||
        stockList.length === 0
    ) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    class="text-center text-muted py-4"
                >

                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>

                    No stock available.

                </td>

            </tr>

        `;

        return;

    }


    stockList.forEach(
        function (stock, index) {

            const row =
                document.createElement(
                    'tr'
                );


            row.innerHTML = `

                <td>
                    ${index + 1}
                </td>

                <td>
                    ${escapeHtml(
                        stock.warehousename || '-'
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        stock.locationname || '-'
                    )}
                </td>

                <td>

                    <span class="stock-box-name">

                        ${escapeHtml(
                            stock.boxno || '-'
                        )}

                    </span>

                </td>

                <td>
                    ${Number(
                        stock.total_received || 0
                    )}
                </td>

                <td>
                    ${Number(
                        stock.total_sent || 0
                    )}
                </td>

                <td>

                    <span class="stock-qty-available">

                        ${Number(
                            stock.total_available || 0
                        )}

                    </span>

                </td>

            `;


            tbody.appendChild(
                row
            );

        }
    );

}


/*
|--------------------------------------------------------------------------
| HTML ESCAPE
|--------------------------------------------------------------------------
*/

function escapeHtml(
    value
) {

    const div =
        document.createElement(
            'div'
        );

    div.textContent =
        value == null
            ? ''
            : String(value);

    return div.innerHTML;

}

/*
|--------------------------------------------------------------------------
| CLICK PRODUCT IMAGE INSIDE DETAILS MODAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    function (event) {

        const productImage =
            event.target.closest(
                '#modalProductImage'
            );


        if (!productImage) {
            return;
        }


        const imageUrl =
            productImage.getAttribute(
                'src'
            );


        if (
            !imageUrl ||
            imageUrl.trim() === ''
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SET BIG IMAGE
        |--------------------------------------------------------------------------
        */

        const bigImage =
            document.getElementById(
                'bigProductImage'
            );


        bigImage.src =
            imageUrl;


        /*
        |--------------------------------------------------------------------------
        | OPEN BIG IMAGE MODAL
        |--------------------------------------------------------------------------
        */

        const bigModalElement =
            document.getElementById(
                'bigProductImageModal'
            );


        const bigModal =
            bootstrap.Modal.getOrCreateInstance(
                bigModalElement
            );


        bigModal.show();

    }
);

</script>

@endsection