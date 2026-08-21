@extends('layouts.app')

@section('content')

<style>

/* ============================================================
   PAGE
============================================================ */

.pattern-stock-page {
    width: 100%;
}


/* ============================================================
   HEADER
============================================================ */

.pattern-stock-header {

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 14px;

    padding: 20px 22px;

    margin-bottom: 20px;

}


.pattern-stock-header h4 {

    margin: 0;

    font-weight: 700;

    color: #172033;

}


.pattern-stock-header p {

    margin: 5px 0 0;

    color: #64748b;

    font-size: 14px;

}


.pattern-stock-count {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    margin-top: 12px;

    padding: 6px 11px;

    border-radius: 7px;

    background: #eff6ff;

    color: #2563eb;

    font-size: 13px;

    font-weight: 600;

}


/* ============================================================
   PRODUCT GRID
============================================================ */

.pattern-product-grid {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 18px;

}


/* ============================================================
   PRODUCT CARD
============================================================ */

.pattern-product-card {

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 14px;

    overflow: hidden;

    cursor: pointer;

    transition:
        transform .2s ease,
        box-shadow .2s ease,
        border-color .2s ease;

}


.pattern-product-card:hover {

    transform: translateY(-3px);

    border-color: #93c5fd;

    box-shadow:
        0 10px 30px rgba(
            15,
            23,
            42,
            .10
        );

}


/* ============================================================
   IMAGE
============================================================ */

.pattern-product-image {

    height: 280px;

    width: 100%;

    background: #f8fafc;

    border-bottom: 1px solid #e2e8f0;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

}


.pattern-product-image img {

    width: 100%;

    height: 100%;

    object-fit: contain;

    padding: 12px;

    transition: transform .25s ease;

}


.pattern-product-card:hover
.pattern-product-image img {

    transform: scale(1.03);

}


.pattern-no-image {

    height: 100%;

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-direction: column;

    color: #94a3b8;

}


.pattern-no-image i {

    font-size: 45px;

    margin-bottom: 7px;

}


/* ============================================================
   BODY
============================================================ */

.pattern-product-body {

    padding: 16px;

}


.pattern-product-name {

    font-size: 17px;

    font-weight: 700;

    color: #172033;

    line-height: 1.35;

}


.pattern-product-id {

    font-size: 12px;

    color: #64748b;

    margin-top: 3px;

}


.pattern-product-sku {

    font-size: 12px;

    color: #2563eb;

    margin-top: 4px;

}


/* ============================================================
   DETAILS
============================================================ */

.pattern-details {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 8px;

    margin-top: 14px;

}


.pattern-detail {

    padding: 8px 9px;

    border: 1px solid #e2e8f0;

    border-radius: 8px;

    background: #f8fafc;

    min-width: 0;

}


.pattern-detail-label {

    display: block;

    font-size: 10px;

    color: #64748b;

    margin-bottom: 3px;

}


.pattern-detail-value {

    display: block;

    font-size: 12px;

    font-weight: 600;

    color: #172033;

    word-break: break-word;

}


/* ============================================================
   BARCODE
============================================================ */

.pattern-barcode {

    margin-top: 10px;

    padding: 9px 10px;

    border-radius: 8px;

    background: #f1f5f9;

    border: 1px solid #e2e8f0;

}


.pattern-barcode-label {

    display: block;

    font-size: 10px;

    color: #64748b;

}


.pattern-barcode-value {

    display: block;

    margin-top: 2px;

    font-size: 12px;

    font-weight: 600;

    color: #172033;

    word-break: break-all;

}


/* ============================================================
   PAGINATION
============================================================ */

.pattern-pagination-wrapper {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    flex-wrap: wrap;

    margin-top: 25px;

    padding: 16px;

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 12px;

}


.pattern-pagination-info {

    color: #64748b;

    font-size: 14px;

}


.pattern-pagination-info strong {

    color: #172033;

}


.pattern-pagination {

    display: flex;

    align-items: center;

    gap: 5px;

    flex-wrap: wrap;

}


.pattern-page-btn,
.pattern-page-number {

    min-width: 38px;

    height: 38px;

    padding: 0 11px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #334155;

    text-decoration: none;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    font-size: 13px;

    font-weight: 500;

}


.pattern-page-btn:hover,
.pattern-page-number:hover {

    color: #2563eb;

    border-color: #2563eb;

    background: #eff6ff;

}


.pattern-page-number.active {

    background: #2563eb;

    border-color: #2563eb;

    color: #ffffff;

}


.pattern-page-disabled {

    color: #94a3b8;

    background: #f8fafc;

    cursor: not-allowed;

}


.pattern-page-dots {

    min-width: 28px;

    height: 38px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    color: #64748b;

}


/* ============================================================
   EMPTY
============================================================ */

.pattern-empty {

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 14px;

    padding: 70px 20px;

    text-align: center;

    color: #64748b;

}


.pattern-empty i {

    display: block;

    font-size: 48px;

    margin-bottom: 12px;

    color: #94a3b8;

}


/* ============================================================
   PRODUCT DETAIL MODAL
============================================================ */

.pattern-detail-modal .modal-content {

    border: none;

    border-radius: 16px;

    overflow: hidden;

}


.pattern-detail-modal .modal-header {

    padding: 16px 20px;

    border-bottom: 1px solid #e2e8f0;

}


.pattern-detail-modal .modal-title {

    font-weight: 700;

    color: #172033;

}


.pattern-modal-body {

    padding: 20px;

}


.pattern-modal-image-box {

    width: 100%;

    height: 430px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 12px;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    cursor: zoom-in;

}


.pattern-modal-image-box img {

    width: 100%;

    height: 100%;

    object-fit: contain;

    padding: 12px;

}


.pattern-modal-details {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 10px;

}


.pattern-modal-detail {

    padding: 10px 12px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 8px;

}


.pattern-modal-detail-label {

    display: block;

    color: #64748b;

    font-size: 11px;

    margin-bottom: 3px;

}


.pattern-modal-detail-value {

    display: block;

    color: #172033;

    font-size: 13px;

    font-weight: 600;

    word-break: break-word;

}


/* ============================================================
   LARGE IMAGE MODAL
============================================================ */

.pattern-large-image-modal .modal-content {

    background: #111827;

    border: none;

    border-radius: 12px;

}


.pattern-large-image-modal .modal-header {

    border: none;

    padding: 8px 12px;

}


.pattern-large-image-modal .btn-close {

    filter: invert(1);

}


.pattern-large-image-body {

    min-height: 75vh;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 15px;

}


#patternLargeImage {

    max-width: 100%;

    max-height: 82vh;

    width: auto;

    height: auto;

    object-fit: contain;

}


/* ============================================================
   TABLET
============================================================ */

@media (max-width: 991px) {

    .pattern-product-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }

}


/* ============================================================
   MOBILE
============================================================ */

@media (max-width: 767px) {

    .pattern-product-grid {

        grid-template-columns: 1fr;

    }


    .pattern-pagination-wrapper {

        flex-direction: column;

        align-items: center;

    }


    .pattern-pagination-info {

        text-align: center;

    }


    .pattern-modal-image-box {

        height: 300px;

    }

}


@media (max-width: 480px) {

    .pattern-stock-header {

        padding: 16px;

    }


    .pattern-product-image {

        height: 300px;

    }


    .pattern-details {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }


    .pattern-modal-details {

        grid-template-columns: 1fr;

    }

}

.ptf-assignment-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #ffffff;
    padding: 12px;
    margin-bottom: 10px;
}

.ptf-assignment-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
}

.ptf-assignment-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    padding: 8px;
}

.ptf-assignment-label {
    display: block;
    font-size: 10px;
    color: #64748b;
    margin-bottom: 2px;
}

.ptf-assignment-value {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #172033;
    word-break: break-word;
}

.ptf-assignment-empty {
    padding: 12px;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    color: #64748b;
    background: #f8fafc;
    font-size: 13px;
}

@media (max-width: 767px) {

    .ptf-assignment-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}

</style>


<div class="container-fluid py-4 pattern-stock-page">


    {{-- ========================================================
         HEADER
    ========================================================= --}}

    <div class="pattern-stock-header">

        <h4>

            <i class="bi bi-grid-3x3-gap me-2"></i>

            Pattern & Test Fit Stock

        </h4>


        <p>

            Products available for Pattern & Test Fit.

        </p>


        <div class="pattern-stock-count">

            <i class="bi bi-box-seam"></i>

            {{ $products->total() }}

            Products

        </div>

    </div>



    {{-- ========================================================
         PRODUCTS
    ========================================================= --}}

    @if($products->count() > 0)

        <div class="pattern-product-grid">

            @foreach($products as $index => $product)

                <div
                    class="pattern-product-card"
                    data-product-index="{{ $index }}"
                    onclick="openPatternProduct({{ $index }})"
                >


                    {{-- IMAGE --}}

                    <div class="pattern-product-image">

                        @if(!empty($product->image_url))

                            <img
                                src="{{ $product->image_url }}"
                                alt="{{ $product->item_name_text ?? 'Product' }}"
                                loading="lazy"
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                "
                            >

                            <div
                                class="pattern-no-image"
                                style="display:none;"
                            >

                                <i class="bi bi-image"></i>

                                <span>
                                    Image unavailable
                                </span>

                            </div>

                        @else

                            <div class="pattern-no-image">

                                <i class="bi bi-image"></i>

                                <span>
                                    Image unavailable
                                </span>

                            </div>

                        @endif

                    </div>



                    {{-- BODY --}}

                    <div class="pattern-product-body">


                        {{-- PRODUCT NAME --}}

                        <div class="pattern-product-name">

                            {{ $product->item_name_text ?: 'Product' }}

                        </div>


                        <div class="pattern-product-id">

                            Product ID:
                            {{ $product->id ?: $product->sno }}

                        </div>


                        {{-- SKU --}}

                        <div class="pattern-product-sku">

                            SKU:
                            {{ $product->sku ?: '-' }}

                        </div>


                        {{-- DETAILS --}}

                        <div class="pattern-details">


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Designer
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->designer_name_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Item Type
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->item_type_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Gender
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->gender_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Composition
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->composition_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Colour
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->colour_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Size
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->size_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Embellishment
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->embellishment_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Manufacturing
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->manufacturing_process_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Craftsman
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->craftsman_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Manufacturer
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->manufacture_text ?: '-' }}
                                </span>

                            </div>


                            <div class="pattern-detail">

                                <span class="pattern-detail-label">
                                    Client
                                </span>

                                <span class="pattern-detail-value">
                                    {{ $product->client_text ?: '-' }}
                                </span>

                            </div>


                            @if(!empty($product->craftsman_code))

                                <div class="pattern-detail">

                                    <span class="pattern-detail-label">
                                        Craftsman Code
                                    </span>

                                    <span class="pattern-detail-value">
                                        {{ $product->craftsman_code }}
                                    </span>

                                </div>

                            @endif


                        </div>


                        {{-- BARCODE --}}

                        <div class="pattern-barcode">

                            <span class="pattern-barcode-label">
                                Barcode
                            </span>

                            <span class="pattern-barcode-value">
                                {{ $product->barcode ?: '-' }}
                            </span>

                        </div>


                    </div>

                </div>

            @endforeach

        </div>


        {{-- ====================================================
             PAGINATION
        ===================================================== --}}

        <div class="pattern-pagination-wrapper">


            <div class="pattern-pagination-info">

                Showing

                <strong>
                    {{ $products->firstItem() }}
                </strong>

                to

                <strong>
                    {{ $products->lastItem() }}
                </strong>

                of

                <strong>
                    {{ $products->total() }}
                </strong>

                products

            </div>


            <div class="pattern-pagination">


                {{-- PREVIOUS --}}

                @if($products->onFirstPage())

                    <span
                        class="pattern-page-btn pattern-page-disabled"
                    >

                        <i class="bi bi-chevron-left"></i>

                        Previous

                    </span>

                @else

                    <a
                        href="{{ $products->previousPageUrl() }}"
                        class="pattern-page-btn"
                    >

                        <i class="bi bi-chevron-left"></i>

                        Previous

                    </a>

                @endif


                @php

                    $currentPage =
                        $products->currentPage();

                    $lastPage =
                        $products->lastPage();

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


                {{-- FIRST PAGE --}}

                @if($startPage > 1)

                    <a
                        href="{{ $products->url(1) }}"
                        class="pattern-page-number"
                    >
                        1
                    </a>


                    @if($startPage > 2)

                        <span class="pattern-page-dots">
                            ...
                        </span>

                    @endif

                @endif


                {{-- PAGE NUMBERS --}}

                @for(
                    $page = $startPage;
                    $page <= $endPage;
                    $page++
                )

                    @if($page == $currentPage)

                        <span
                            class="pattern-page-number active"
                        >
                            {{ $page }}
                        </span>

                    @else

                        <a
                            href="{{ $products->url($page) }}"
                            class="pattern-page-number"
                        >
                            {{ $page }}
                        </a>

                    @endif

                @endfor


                {{-- LAST PAGE --}}

                @if($endPage < $lastPage)

                    @if($endPage < $lastPage - 1)

                        <span class="pattern-page-dots">
                            ...
                        </span>

                    @endif


                    <a
                        href="{{ $products->url($lastPage) }}"
                        class="pattern-page-number"
                    >
                        {{ $lastPage }}
                    </a>

                @endif


                {{-- NEXT --}}

                @if($products->hasMorePages())

                    <a
                        href="{{ $products->nextPageUrl() }}"
                        class="pattern-page-btn"
                    >

                        Next

                        <i class="bi bi-chevron-right"></i>

                    </a>

                @else

                    <span
                        class="pattern-page-btn pattern-page-disabled"
                    >

                        Next

                        <i class="bi bi-chevron-right"></i>

                    </span>

                @endif


            </div>

        </div>


    @else


        {{-- ====================================================
             EMPTY
        ===================================================== --}}

        <div class="pattern-empty">

            <i class="bi bi-box-seam"></i>

            <strong>
                No Pattern & Test Fit Stock Found
            </strong>

            <div class="mt-1">
                No available products were found.
            </div>

        </div>

    @endif

</div>



{{-- ============================================================
     PRODUCT DETAIL MODAL
============================================================ --}}

<div
    class="modal fade pattern-detail-modal"
    id="patternProductModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
    >

        <div class="modal-content">


            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="patternModalTitle"
                >
                    Product Details
                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body pattern-modal-body">

                <div class="row g-4">


                    {{-- IMAGE --}}

                    <div class="col-lg-5">

                        <div
                            class="pattern-modal-image-box"
                            onclick="openPatternLargeImage()"
                        >

                            <img
                                id="patternModalImage"
                                src=""
                                alt="Product"
                            >

                        </div>

                    </div>


                    {{-- DETAILS --}}

                    <div class="col-lg-7">

                        <div
                            class="pattern-modal-details"
                            id="patternModalDetails"
                        >
                        </div>

                    </div>

                    {{-- ============================================================
                        PATTERN / TEST FIT ASSIGNMENT
                    ============================================================= --}}

                    <div class="col-12 mt-3">

                        <div class="card border-0 bg-light">

                            <div class="card-header bg-white">
                                <strong>
                                    <i class="bi bi-boxes me-2"></i>
                                    Pattern / Test Fit Stock Assignment
                                </strong>
                            </div>

                            <div class="card-body">

                                <div id="patternTestFitAssignmentLoading"
                                    class="text-center py-3">

                                    <div class="spinner-border spinner-border-sm"></div>

                                    <span class="ms-2">
                                        Loading stock assignment...
                                    </span>

                                </div>


                                <div
                                    id="patternTestFitAssignmentContent"
                                    style="display:none;"
                                >

                                    {{-- PATTERN --}}
                                    <div class="mb-4">

                                        <div class="d-flex align-items-center mb-2">

                                            <span class="badge bg-primary me-2">
                                                Pattern
                                            </span>

                                            <strong id="patternAssignmentStatus">
                                                -
                                            </strong>

                                        </div>

                                        <div id="patternAssignmentList">
                                        </div>

                                    </div>


                                    {{-- TEST FIT --}}
                                    <div>

                                        <div class="d-flex align-items-center mb-2">

                                            <span class="badge bg-success me-2">
                                                Test Fit
                                            </span>

                                            <strong id="testFitAssignmentStatus">
                                                -
                                            </strong>

                                        </div>

                                        <div id="testFitAssignmentList">
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ============================================================
                        STORE PATTERN / TEST FIT BUTTON
                    ============================================================ --}}

                    <div class="mt-4 pt-3 border-top">

                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            id="btnOpenPatternTestFitStore"
                            onclick="openPatternTestFitStore()"
                        >

                            <i class="bi bi-box-arrow-in-down me-2"></i>

                            Store Pattern / Test Fit

                        </button>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>



{{-- ============================================================
     LARGE IMAGE MODAL
============================================================ --}}

<div
    class="modal fade pattern-large-image-modal"
    id="patternLargeImageModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-centered"
    >

        <div class="modal-content">


            <div class="modal-header">

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body pattern-large-image-body">

                <img
                    id="patternLargeImage"
                    src=""
                    alt="Product Image"
                >

            </div>

        </div>

    </div>

</div>

{{-- ============================================================
     STORE PATTERN / TEST FIT MODAL
============================================================ --}}

<div
    class="modal fade"
    id="patternTestFitStoreModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
    >

        <div class="modal-content">


            {{-- HEADER --}}

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">

                        <i class="bi bi-box-arrow-in-down me-2"></i>

                        Store Pattern / Test Fit

                    </h5>

                    <small class="text-muted">
                        Store selected product images in warehouse stock.
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


                <form
                    id="patternTestFitStoreForm"
                    enctype="multipart/form-data"
                >

                    @csrf


                    {{-- =================================================
                         HIDDEN PRODUCT DATA
                    ================================================== --}}

                    <input
                        type="hidden"
                        id="ptf_product_id"
                        name="product_id"
                    >

                    <input
                        type="hidden"
                        id="ptf_product_barcode"
                        name="product_barcode"
                    >


                    {{-- =================================================
                         PRODUCT INFORMATION
                    ================================================== --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                Selected Product
                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row g-3">


                                <div class="col-md-3">

                                    <div
                                        id="ptf_product_image_box"
                                        class="border rounded p-2 text-center"
                                        style="height:180px;"
                                    >

                                        <img
                                            id="ptf_product_image"
                                            src=""
                                            alt="Product"
                                            style="
                                                width:100%;
                                                height:100%;
                                                object-fit:contain;
                                            "
                                        >

                                    </div>

                                </div>


                                <div class="col-md-9">

                                    <h5
                                        id="ptf_product_name"
                                        class="mb-2"
                                    >
                                        -
                                    </h5>


                                    <div class="row g-2">


                                        <div class="col-md-6">

                                            <div class="small text-muted">
                                                Product ID
                                            </div>

                                            <strong
                                                id="ptf_product_id_text"
                                            >
                                                -
                                            </strong>

                                        </div>


                                        <div class="col-md-6">

                                            <div class="small text-muted">
                                                Barcode
                                            </div>

                                            <strong
                                                id="ptf_product_barcode_text"
                                            >
                                                -
                                            </strong>

                                        </div>


                                        <div class="col-md-6">

                                            <div class="small text-muted">
                                                Designer
                                            </div>

                                            <strong
                                                id="ptf_designer"
                                            >
                                                -
                                            </strong>

                                        </div>


                                        <div class="col-md-6">

                                            <div class="small text-muted">
                                                Item Type
                                            </div>

                                            <strong
                                                id="ptf_item_type"
                                            >
                                                -
                                            </strong>

                                        </div>


                                        <div class="col-md-6">

                                            <div class="small text-muted">
                                                Colour
                                            </div>

                                            <strong
                                                id="ptf_colour"
                                            >
                                                -
                                            </strong>

                                        </div>


                                        <div class="col-md-6">

                                            <div class="small text-muted">
                                                Size
                                            </div>

                                            <strong
                                                id="ptf_size"
                                            >
                                                -
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         PATTERN / TEST FIT
                    ================================================== --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                1. Choose Stock Type
                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row g-3">


                                <div class="col-md-6">

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="type"
                                            id="ptf_type_pattern"
                                            value="pattern"
                                        >

                                        <label
                                            class="form-check-label"
                                            for="ptf_type_pattern"
                                        >

                                            <i class="bi bi-diagram-3 me-1"></i>

                                            Pattern

                                        </label>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="type"
                                            id="ptf_type_testfit"
                                            value="testfit"
                                        >

                                        <label
                                            class="form-check-label"
                                            for="ptf_type_testfit"
                                        >

                                            <i class="bi bi-person-check me-1"></i>

                                            Test Fit

                                        </label>

                                    </div>

                                </div>


                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         IMAGE COUNT
                    ================================================== --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                2. Product Images
                            </strong>

                        </div>


                        <div class="card-body">


                            <div class="row align-items-end g-3">


                                <div class="col-md-4">

                                    <label
                                        class="form-label fw-semibold"
                                    >

                                        How many images?

                                    </label>


                                    <input
                                        type="number"
                                        class="form-control"
                                        id="ptf_image_count"
                                        name="image_count"
                                        min="1"
                                        max="50"
                                        value="1"
                                    >

                                </div>


                                <div class="col-md-8">

                                    <div
                                        class="alert alert-info mb-0"
                                    >

                                        Enter the number of images.
                                        The system will require exactly
                                        that many images.

                                    </div>

                                </div>

                            </div>


                            <div
                                id="ptf_image_inputs"
                                class="mt-3"
                            ></div>


                        </div>

                    </div>


                    {{-- =================================================
                         MAIN BARCODE
                    ================================================== --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                3. Barcode
                            </strong>

                        </div>


                        <div class="card-body">


                            <div class="alert alert-secondary mb-0">

                                <div class="small text-muted">
                                    Main Barcode
                                </div>

                                <strong
                                    id="ptf_main_barcode_preview"
                                >
                                    It will be generated during save.
                                </strong>

                                <div
                                    id="ptf_image_barcode_preview"
                                    class="mt-2"
                                ></div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         WAREHOUSE
                    ================================================== --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                4. Warehouse / Location / Box
                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row g-3">


                                {{-- WAREHOUSE --}}

                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">

                                        Warehouse

                                    </label>


                                    <select
                                        id="ptf_warehouse_id"
                                        name="warehouse_id"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Warehouse
                                        </option>

                                    </select>

                                </div>


                                {{-- LOCATION --}}

                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">

                                        Location

                                    </label>


                                    <select
                                        id="ptf_location_id"
                                        name="location_id"
                                        class="form-select"
                                        disabled
                                    >

                                        <option value="">
                                            Select Location
                                        </option>

                                    </select>

                                </div>


                                {{-- BOX --}}

                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">

                                        Box No.

                                    </label>


                                    <select
                                        id="ptf_box_id"
                                        name="box_id"
                                        class="form-select"
                                        disabled
                                    >

                                        <option value="">
                                            Select Box
                                        </option>

                                    </select>

                                </div>


                            </div>

                        </div>

                    </div>

                    {{-- ============================================================
                        DIGITAL FILES
                    ============================================================ --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                5. Digital Files
                            </strong>

                            <span class="text-muted fw-normal">
                                (Optional)
                            </span>

                        </div>


                        <div class="card-body">

                            <div class="alert alert-info">

                                <i class="bi bi-info-circle me-1"></i>

                                Digital files are optional. You can attach them now
                                or upload them later.

                            </div>


                            <label
                                class="form-label fw-semibold"
                            >

                                Attach Digital Files

                            </label>


                            <input
                                type="file"
                                class="form-control"
                                id="ptf_digital_files"
                                name="digital_files[]"
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.rar,.txt"
                            >


                            <div
                                class="form-text"
                            >

                                Allowed:
                                PDF, DOC, DOCX, XLS, XLSX, CSV, ZIP, RAR, TXT

                            </div>


                            <div
                                id="ptf_digital_file_list"
                                class="mt-3"
                            ></div>

                        </div>

                    </div>


                    {{-- =================================================
                         REMARKS
                    ================================================== --}}

                    <div class="card mb-3">

                        <div class="card-header bg-light">

                            <strong>
                                5. Remarks
                            </strong>

                        </div>


                        <div class="card-body">

                            <textarea
                                class="form-control"
                                id="ptf_remarks"
                                name="remarks"
                                rows="3"
                                placeholder="Enter remarks..."
                            ></textarea>

                        </div>

                    </div>


                    {{-- =================================================
                         SAVE
                    ================================================== --}}

                    <div class="d-flex justify-content-end gap-2">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >

                            Cancel

                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="btnSavePatternTestFit"
                        >

                            <i class="bi bi-check-circle me-1"></i>

                            Save Stock

                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>

</div>



<script>

    let ptfSelectedProduct = null;

/*
|--------------------------------------------------------------------------
| PRODUCT DATA
|--------------------------------------------------------------------------
*/

const patternProducts =
    @json(
        $products->getCollection()->values()
    );


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

/* ============================================================
   DIGITAL FILE PREVIEW
============================================================ */

document.addEventListener(
    'change',
    function (event) {

        if (
            event.target.id !==
            'ptf_digital_files'
        ) {

            return;

        }


        const files =
            event.target.files;


        const container =
            document.getElementById(
                'ptf_digital_file_list'
            );


        if (!container) {

            return;

        }


        container.innerHTML =
            '';


        if (!files || files.length === 0) {

            return;

        }


        let html = `

            <div class="small fw-semibold mb-2">

                Selected Digital Files:
                ${files.length}

            </div>

            <div class="list-group">

        `;


        Array.from(files).forEach(
            function (file, index) {

                const sizeMB =
                    (
                        file.size /
                        (
                            1024 *
                            1024
                        )
                    ).toFixed(2);


                html += `

                    <div
                        class="list-group-item
                               d-flex
                               justify-content-between
                               align-items-center"
                    >

                        <div>

                            <i
                                class="bi bi-file-earmark me-2"
                            ></i>

                            ${escapePtfHtml(
                                file.name
                            )}

                        </div>


                        <span
                            class="badge bg-secondary"
                        >

                            ${sizeMB} MB

                        </span>

                    </div>

                `;

            }
        );


        html += `
            </div>
        `;


        container.innerHTML =
            html;

    }
);

function escapePtfHtml(value)
{
    const div =
        document.createElement('div');

    div.textContent =
        value ?? '';

    return div.innerHTML;
}

function patternEscapeHtml(value)
{
    if (
        value === null ||
        value === undefined
    ) {

        return '-';

    }


    const div =
        document.createElement('div');


    div.textContent =
        String(value);


    return div.innerHTML;
}


/*
|--------------------------------------------------------------------------
| VALUE
|--------------------------------------------------------------------------
*/

function patternValue(value)
{
    if (
        value === null ||
        value === undefined ||
        String(value).trim() === ''
    ) {

        return '-';

    }


    return value;

}


/*
|--------------------------------------------------------------------------
| OPEN PRODUCT
|--------------------------------------------------------------------------
*/

function openPatternProduct(index)
{

    const product =
        patternProducts[index];


    if (!product) {
        return;
    }


    window.ptfCurrentProduct = product;


    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

    const productName =
        patternValue(
            product.item_name_text
        );


    document.getElementById(
        'patternModalTitle'
    ).textContent =
        productName;


    /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
    */

    const modalImage =
        document.getElementById(
            'patternModalImage'
        );


    if (product.image_url) {

        modalImage.src =
            product.image_url;

        modalImage.style.display =
            'block';

    }
    else {

        modalImage.removeAttribute(
            'src'
        );

        modalImage.style.display =
            'none';

    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAILS
    |--------------------------------------------------------------------------
    */

    const details =
        document.getElementById(
            'patternModalDetails'
        );


    details.innerHTML = `

        ${patternModalDetail(
            'Product ID',
            product.id || product.sno
        )}

        ${patternModalDetail(
            'Product Name',
            product.item_name_text
        )}

        ${patternModalDetail(
            'SKU',
            product.sku
        )}

        ${patternModalDetail(
            'Barcode',
            product.barcode
        )}

        ${patternModalDetail(
            'Designer',
            product.designer_name_text
        )}

        ${patternModalDetail(
            'Item Type',
            product.item_type_text
        )}

        ${patternModalDetail(
            'Gender',
            product.gender_text
        )}

        ${patternModalDetail(
            'Composition',
            product.composition_text
        )}

        ${patternModalDetail(
            'Colour',
            product.colour_text
        )}

        ${patternModalDetail(
            'Size',
            product.size_text
        )}

        ${patternModalDetail(
            'Embellishment',
            product.embellishment_text
        )}

        ${patternModalDetail(
            'Manufacturing Process',
            product.manufacturing_process_text
        )}

        ${patternModalDetail(
            'Craftsman',
            product.craftsman_text
        )}

        ${patternModalDetail(
            'Craftsman Code',
            product.craftsman_code
        )}

        ${patternModalDetail(
            'Manufacturer',
            product.manufacture_text
        )}

        ${patternModalDetail(
            'Client',
            product.client_text
        )}

        ${patternModalDetail(
            'Client Reference',
            product.clientreference
        )}

    `;


    /*
    |--------------------------------------------------------------------------
    | RESET ASSIGNMENT SECTION
    |--------------------------------------------------------------------------
    */

    const assignmentLoading =
        document.getElementById(
            'patternTestFitAssignmentLoading'
        );

    const assignmentContent =
        document.getElementById(
            'patternTestFitAssignmentContent'
        );

    const patternStatus =
        document.getElementById(
            'patternAssignmentStatus'
        );

    const patternList =
        document.getElementById(
            'patternAssignmentList'
        );

    const testFitStatus =
        document.getElementById(
            'testFitAssignmentStatus'
        );

    const testFitList =
        document.getElementById(
            'testFitAssignmentList'
        );


    if (
        assignmentLoading &&
        assignmentContent &&
        patternStatus &&
        patternList &&
        testFitStatus &&
        testFitList
    ) {

        assignmentLoading.style.display =
            'block';

        assignmentContent.style.display =
            'none';


        patternStatus.textContent =
            'Loading...';

        testFitStatus.textContent =
            'Loading...';

        patternList.innerHTML =
            '';

        testFitList.innerHTML =
            '';


        /*
        |--------------------------------------------------------------------------
        | GET PRODUCT ITEM ID
        |--------------------------------------------------------------------------
        */

        const itemId =
            product.id ||
            product.sno;


        /*
        |--------------------------------------------------------------------------
        | LOAD PATTERN / TEST FIT ASSIGNMENTS
        |--------------------------------------------------------------------------
        */

        fetch(
            "{{ route('inventory.pattern-test-fit-stock.assignments') }}" +
            "?item_id=" +
            encodeURIComponent(itemId),
            {
                method: 'GET',

                headers: {
                    'Accept':
                        'application/json',

                    'X-Requested-With':
                        'XMLHttpRequest'
                }
            }
        )

        .then(function(response) {

            if (!response.ok) {

                throw new Error(
                    'Unable to load stock assignment.'
                );

            }

            return response.json();

        })

        .then(function(data) {

            if (!data.success) {

                throw new Error(
                    data.message ||
                    'Unable to load stock assignment.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PATTERN
            |--------------------------------------------------------------------------
            */

            const pattern =
                Array.isArray(data.pattern)
                    ? data.pattern
                    : [];


            if (pattern.length > 0) {

                patternStatus.innerHTML =
                    '<span class="text-success">' +
                    '<i class="bi bi-check-circle-fill me-1"></i>' +
                    'Assigned (' +
                    pattern.length +
                    ')' +
                    '</span>';


                patternList.innerHTML =
                    pattern
                        .map(
                            function(row) {

                                return createPatternTestFitAssignmentCard(
                                    row,
                                    'Pattern'
                                );

                            }
                        )
                        .join('');

            }
            else {

                patternStatus.innerHTML =
                    '<span class="text-muted">' +
                    '<i class="bi bi-dash-circle me-1"></i>' +
                    'Not Assigned' +
                    '</span>';


                patternList.innerHTML =
                    `
                    <div class="ptf-assignment-empty">
                        Pattern stock is not assigned to this product.
                    </div>
                    `;

            }


            /*
            |--------------------------------------------------------------------------
            | TEST FIT
            |--------------------------------------------------------------------------
            */

            const testFit =
                Array.isArray(data.testfit)
                    ? data.testfit
                    : [];


            if (testFit.length > 0) {

                testFitStatus.innerHTML =
                    '<span class="text-success">' +
                    '<i class="bi bi-check-circle-fill me-1"></i>' +
                    'Assigned (' +
                    testFit.length +
                    ')' +
                    '</span>';


                testFitList.innerHTML =
                    testFit
                        .map(
                            function(row) {

                                return createPatternTestFitAssignmentCard(
                                    row,
                                    'Test Fit'
                                );

                            }
                        )
                        .join('');

            }
            else {

                testFitStatus.innerHTML =
                    '<span class="text-muted">' +
                    '<i class="bi bi-dash-circle me-1"></i>' +
                    'Not Assigned' +
                    '</span>';


                testFitList.innerHTML =
                    `
                    <div class="ptf-assignment-empty">
                        Test Fit stock is not assigned to this product.
                    </div>
                    `;

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW ASSIGNMENT SECTION
            |--------------------------------------------------------------------------
            */

            assignmentLoading.style.display =
                'none';

            assignmentContent.style.display =
                'block';

        })

        .catch(function(error) {

            console.error(
                'Pattern/Test Fit assignment error:',
                error
            );


            patternStatus.innerHTML =
                '<span class="text-danger">' +
                'Unable to load' +
                '</span>';


            testFitStatus.innerHTML =
                '<span class="text-danger">' +
                'Unable to load' +
                '</span>';


            patternList.innerHTML =
                `
                <div class="ptf-assignment-empty text-danger">
                    Unable to load Pattern stock details.
                </div>
                `;


            testFitList.innerHTML =
                `
                <div class="ptf-assignment-empty text-danger">
                    Unable to load Test Fit stock details.
                </div>
                `;


            assignmentLoading.style.display =
                'none';

            assignmentContent.style.display =
                'block';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN MODAL
    |--------------------------------------------------------------------------
    */

    const modalElement =
        document.getElementById(
            'patternProductModal'
        );


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    modal.show();

}

function createPatternTestFitAssignmentCard(row, stockType)
{
    const barcode =
        row.barcode ||
        '-';

    const barcodeOfDesign =
        row.barcodeofdesign ||
        '-';

    const warehouse =
        row.warehouse_name ||
        '-';

    const location =
        row.location_name ||
        '-';

    const boxNo =
        row.box_no ||
        '-';

    const qtyImages =
        row.qty_img !== null &&
        row.qty_img !== undefined
            ? row.qty_img
            : 0;


    return `
        <div class="ptf-assignment-card">

            <div class="d-flex justify-content-between align-items-center mb-2">

                <strong>
                    <i class="bi bi-box-seam me-1"></i>
                    ${escapeHtmlPatternValue(stockType)}
                </strong>

                <span class="badge bg-secondary">
                    ${escapeHtmlPatternValue(qtyImages)} Images
                </span>

            </div>


            <div class="ptf-assignment-grid">

                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">
                        Main Barcode
                    </span>

                    <span class="ptf-assignment-value">
                        ${escapeHtmlPatternValue(barcode)}
                    </span>

                </div>


                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">
                        Design Barcode
                    </span>

                    <span class="ptf-assignment-value">
                        ${escapeHtmlPatternValue(barcodeOfDesign)}
                    </span>

                </div>


                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">
                        Warehouse
                    </span>

                    <span class="ptf-assignment-value">
                        ${escapeHtmlPatternValue(warehouse)}
                    </span>

                </div>


                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">
                        Location
                    </span>

                    <span class="ptf-assignment-value">
                        ${escapeHtmlPatternValue(location)}
                    </span>

                </div>


                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">
                        Box No
                    </span>

                    <span class="ptf-assignment-value">
                        ${escapeHtmlPatternValue(boxNo)}
                    </span>

                </div>


                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">
                        Image Quantity
                    </span>

                    <span class="ptf-assignment-value">
                        ${escapeHtmlPatternValue(qtyImages)}
                    </span>

                </div>

            </div>

        </div>
    `;
}


function escapeHtmlPatternValue(value)
{
    if (
        value === null ||
        value === undefined
    ) {
        return '-';
    }


    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


/*
|--------------------------------------------------------------------------
| MODAL DETAIL HTML
|--------------------------------------------------------------------------
*/

function patternModalDetail(
    label,
    value
)
{

    return `

        <div class="pattern-modal-detail">

            <span
                class="pattern-modal-detail-label"
            >
                ${patternEscapeHtml(label)}
            </span>

            <span
                class="pattern-modal-detail-value"
            >
                ${patternEscapeHtml(
                    patternValue(value)
                )}
            </span>

        </div>

    `;

}


/*
|--------------------------------------------------------------------------
| OPEN LARGE IMAGE
|--------------------------------------------------------------------------
*/

function openPatternLargeImage()
{

    const image =
        document.getElementById(
            'patternModalImage'
        );


    const largeImage =
        document.getElementById(
            'patternLargeImage'
        );


    if (
        !image ||
        !image.src
    ) {

        return;

    }


    largeImage.src =
        image.src;


    const modalElement =
        document.getElementById(
            'patternLargeImageModal'
        );


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    modal.show();

}



/* ============================================================
   PATTERN / TEST FIT STORE
============================================================ */




/* ============================================================
   OPEN STORE MODAL
============================================================ */

function openPatternTestFitStore()
{

    if (!window.ptfCurrentProduct) {

        Swal.fire(
            'Error',
            'Product information not found.',
            'error'
        );

        return;

    }


    ptfSelectedProduct =
        window.ptfCurrentProduct;


    const product =
        ptfSelectedProduct;


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DATA
    |--------------------------------------------------------------------------
    */

    const productId =
        product.id || product.sno || '';


    const barcode =
        product.barcode || '';


    document.getElementById(
        'ptf_product_id'
    ).value =
        productId;


    document.getElementById(
        'ptf_product_barcode'
    ).value =
        barcode;


    document.getElementById(
        'ptf_product_id_text'
    ).textContent =
        productId || '-';


    document.getElementById(
        'ptf_product_barcode_text'
    ).textContent =
        barcode || '-';


    document.getElementById(
        'ptf_product_name'
    ).textContent =
        product.item_name_text || '-';


    document.getElementById(
        'ptf_designer'
    ).textContent =
        product.designer_name_text || '-';


    document.getElementById(
        'ptf_item_type'
    ).textContent =
        product.item_type_text || '-';


    document.getElementById(
        'ptf_colour'
    ).textContent =
        product.colour_text || '-';


    document.getElementById(
        'ptf_size'
    ).textContent =
        product.size_text || '-';


    /*
    |--------------------------------------------------------------------------
    | PRODUCT IMAGE
    |--------------------------------------------------------------------------
    */

    const image =
        document.getElementById(
            'ptf_product_image'
        );


    if (product.image_url) {

        image.src =
            product.image_url;

        image.style.display =
            'block';

    }
    else {

        image.removeAttribute('src');

        image.style.display =
            'none';

    }


    /*
    |--------------------------------------------------------------------------
    | RESET FORM
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'ptf_image_count'
    ).value =
        1;


    document.getElementById(
        'ptf_remarks'
    ).value =
        '';


    document.getElementById(
        'ptf_type_pattern'
    ).checked =
        false;


    document.getElementById(
        'ptf_type_testfit'
    ).checked =
        false;


    document.getElementById(
        'ptf_warehouse_id'
    ).value =
        '';


    document.getElementById(
        'ptf_location_id'
    ).innerHTML = `
        <option value="">
            Select Location
        </option>
    `;


    document.getElementById(
        'ptf_location_id'
    ).disabled =
        true;


    document.getElementById(
        'ptf_box_id'
    ).innerHTML = `
        <option value="">
            Select Box
        </option>
    `;


    document.getElementById(
        'ptf_box_id'
    ).disabled =
        true;


    /*
    |--------------------------------------------------------------------------
    | IMAGE INPUTS
    |--------------------------------------------------------------------------
    */

    generatePtfImageInputs();


    /*
    |--------------------------------------------------------------------------
    | LOAD WAREHOUSES
    |--------------------------------------------------------------------------
    */

    loadPtfWarehouses();


    /*
    |--------------------------------------------------------------------------
    | SHOW MODAL
    |--------------------------------------------------------------------------
    */

    const modalElement =
        document.getElementById(
            'patternTestFitStoreModal'
        );


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    modal.show();

}


/* ============================================================
   IMPORTANT:
   SAVE CURRENT PRODUCT FOR STORE BUTTON
============================================================ */

/*
   Replace the beginning of your existing
   openPatternProduct() function with this line:

       window.ptfCurrentProduct = product;

   It should be immediately after:

       const product = patternProducts[index];

*/


/* ============================================================
   IMAGE COUNT CHANGE
============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const countInput =
            document.getElementById(
                'ptf_image_count'
            );


        if (countInput) {

            countInput.addEventListener(
                'input',
                function () {

                    generatePtfImageInputs();

                }
            );

        }

    }
);


/* ============================================================
   GENERATE IMAGE INPUTS
============================================================ */

function generatePtfImageInputs()
{

    const input =
        document.getElementById(
            'ptf_image_count'
        );


    const container =
        document.getElementById(
            'ptf_image_inputs'
        );


    if (!input || !container) {

        return;

    }


    let count =
        parseInt(
            input.value,
            10
        );


    if (
        isNaN(count) ||
        count < 1
    ) {

        count = 1;

    }


    if (count > 50) {

        count = 50;

        input.value =
            50;

    }


    let html = '';


    for (
        let i = 1;
        i <= count;
        i++
    ) {

        html += `

            <div
                class="card mb-2 ptf-image-row"
                data-image-number="${i}"
            >

                <div class="card-body">

                    <div class="row align-items-center g-2">

                        <div class="col-md-2">

                            <strong>
                                Image ${i}
                            </strong>

                            <div
                                class="small text-muted"
                            >
                                ${i} / ${count}
                            </div>

                        </div>


                        <div class="col-md-7">

                            <input
                                type="file"
                                class="form-control ptf-image-input"
                                name="images[]"
                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                data-image-number="${i}"
                            >

                        </div>


                        <div class="col-md-3">

                            <div
                                class="small text-muted"
                                id="ptf_preview_barcode_${i}"
                            >
                                Image barcode will be generated
                                after saving.
                            </div>

                        </div>

                    </div>


                    <div
                        class="ptf-image-preview mt-2"
                        id="ptf_image_preview_${i}"
                    ></div>

                </div>

            </div>

        `;

    }


    container.innerHTML =
        html;


    updatePtfBarcodePreview();

}


/* ============================================================
   BARCODE PREVIEW
============================================================ */

function updatePtfBarcodePreview()
{

    const barcode =
        document.getElementById(
            'ptf_product_barcode'
        ).value;


    const countInput =
        document.getElementById(
            'ptf_image_count'
        );


    const preview =
        document.getElementById(
            'ptf_image_barcode_preview'
        );


    if (!barcode) {

        preview.innerHTML =
            'Main barcode will be generated during save.';

        return;

    }


    const count =
        parseInt(
            countInput.value,
            10
        ) || 1;


    let html = `

        <div class="small text-muted">
            Image barcode pattern
        </div>

    `;


    for (
        let i = 1;
        i <= count;
        i++
    ) {

        const number =
            String(i).padStart(
                2,
                '0'
            );


        html += `

            <span
                class="badge bg-light text-dark border me-1 mb-1"
            >
                ${barcode}-XX-${number}
            </span>

        `;

    }


    preview.innerHTML =
        html;

}


/* ============================================================
   IMAGE PREVIEW
============================================================ */

document.addEventListener(
    'change',
    function (event) {

        if (
            !event.target.classList.contains(
                'ptf-image-input'
            )
        ) {

            return;

        }


        const input =
            event.target;


        const number =
            input.dataset.imageNumber;


        const preview =
            document.getElementById(
                'ptf_image_preview_' + number
            );


        if (!preview) {

            return;

        }


        preview.innerHTML =
            '';


        if (
            !input.files ||
            !input.files[0]
        ) {

            return;

        }


        const file =
            input.files[0];


        if (
            !file.type.startsWith(
                'image/'
            )
        ) {

            input.value =
                '';

            Swal.fire(
                'Invalid File',
                'Please select an image file.',
                'warning'
            );

            return;

        }


        const reader =
            new FileReader();


        reader.onload =
            function (e) {

                preview.innerHTML = `

                    <img
                        src="${e.target.result}"
                        style="
                            width:90px;
                            height:90px;
                            object-fit:cover;
                            border-radius:8px;
                            border:1px solid #ddd;
                        "
                    >

                `;

            };


        reader.readAsDataURL(
            file
        );

    }
);


/* ============================================================
   LOAD WAREHOUSES
============================================================ */

async function loadPtfWarehouses()
{

    const select =
        document.getElementById(
            'ptf_warehouse_id'
        );


    if (!select) {

        return;

    }


    select.innerHTML = `

        <option value="">
            Loading warehouses...
        </option>

    `;


    try {

        const response =
            await fetch(
                "{{ route('inventory.ready-to-sell-stock.warehouses') }}",
                {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            );


        const result =
            await response.json();


        if (
            !result.success
        ) {

            throw new Error(
                result.message ||
                'Unable to load warehouses.'
            );

        }


        select.innerHTML = `

            <option value="">
                Select Warehouse
            </option>

        `;


        (result.data || []).forEach(
            function (warehouse) {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    warehouse.sno;


                option.textContent =
                    warehouse.warehousename;


                select.appendChild(
                    option
                );

            }
        );


    } catch (error) {

        console.error(
            'Warehouse Error:',
            error
        );


        select.innerHTML = `

            <option value="">
                Select Warehouse
            </option>

        `;


        Swal.fire(
            'Error',
            error.message ||
            'Unable to load warehouses.',
            'error'
        );

    }

}


/* ============================================================
   WAREHOUSE CHANGE
============================================================ */

document.addEventListener(
    'change',
    function (event) {

        if (
            event.target.id !==
            'ptf_warehouse_id'
        ) {

            return;

        }


        const warehouseId =
            event.target.value;


        const locationSelect =
            document.getElementById(
                'ptf_location_id'
            );


        const boxSelect =
            document.getElementById(
                'ptf_box_id'
            );


        locationSelect.innerHTML = `

            <option value="">
                Select Location
            </option>

        `;


        boxSelect.innerHTML = `

            <option value="">
                Select Box
            </option>

        `;


        boxSelect.disabled =
            true;


        if (!warehouseId) {

            locationSelect.disabled =
                true;

            return;

        }


        loadPtfLocations(
            warehouseId
        );

    }
);


/* ============================================================
   LOAD LOCATIONS
============================================================ */

async function loadPtfLocations(
    warehouseId
)
{

    const select =
        document.getElementById(
            'ptf_location_id'
        );


    select.disabled =
        true;


    select.innerHTML = `

        <option value="">
            Loading locations...
        </option>

    `;


    try {

        const url =
            "{{ route('inventory.ready-to-sell-stock.locations') }}"
            +
            '?warehouse_id='
            +
            encodeURIComponent(
                warehouseId
            );


        const response =
            await fetch(
                url,
                {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            );


        const result =
            await response.json();


        if (
            !result.success
        ) {

            throw new Error(
                result.message ||
                'Unable to load locations.'
            );

        }


        select.innerHTML = `

            <option value="">
                Select Location
            </option>

        `;


        (result.data || []).forEach(
            function (location) {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    location.sno;


                option.textContent =
                    location.locationname;


                select.appendChild(
                    option
                );

            }
        );


        select.disabled =
            false;


    } catch (error) {

        console.error(
            'Location Error:',
            error
        );


        select.innerHTML = `

            <option value="">
                Select Location
            </option>

        `;


        select.disabled =
            true;


        Swal.fire(
            'Error',
            error.message ||
            'Unable to load locations.',
            'error'
        );

    }

}


/* ============================================================
   LOCATION CHANGE
============================================================ */

document.addEventListener(
    'change',
    function (event) {

        if (
            event.target.id !==
            'ptf_location_id'
        ) {

            return;

        }


        const warehouseId =
            document.getElementById(
                'ptf_warehouse_id'
            ).value;


        const locationId =
            event.target.value;


        const boxSelect =
            document.getElementById(
                'ptf_box_id'
            );


        boxSelect.innerHTML = `

            <option value="">
                Select Box
            </option>

        `;


        boxSelect.disabled =
            true;


        if (
            !warehouseId ||
            !locationId
        ) {

            return;

        }


        loadPtfBoxes(
            warehouseId,
            locationId
        );

    }
);


/* ============================================================
   LOAD BOXES
============================================================ */

async function loadPtfBoxes(
    warehouseId,
    locationId
)
{

    const select =
        document.getElementById(
            'ptf_box_id'
        );


    select.disabled =
        true;


    select.innerHTML = `

        <option value="">
            Loading boxes...
        </option>

    `;


    try {

        const url =
            "{{ route('inventory.ready-to-sell-stock.boxes') }}"
            +
            '?warehouse_id='
            +
            encodeURIComponent(
                warehouseId
            )
            +
            '&location_id='
            +
            encodeURIComponent(
                locationId
            );


        const response =
            await fetch(
                url,
                {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            );


        const result =
            await response.json();


        if (
            !result.success
        ) {

            throw new Error(
                result.message ||
                'Unable to load boxes.'
            );

        }


        select.innerHTML = `

            <option value="">
                Select Box
            </option>

        `;


        (result.data || []).forEach(
            function (box) {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    box.sno;


                option.textContent =
                    box.boxno;


                select.appendChild(
                    option
                );

            }
        );


        select.disabled =
            false;


    } catch (error) {

        console.error(
            'Box Error:',
            error
        );


        select.innerHTML = `

            <option value="">
                Select Box
            </option>

        `;


        select.disabled =
            true;


        Swal.fire(
            'Error',
            error.message ||
            'Unable to load boxes.',
            'error'
        );

    }

}


/* ============================================================
   SAVE
============================================================ */

document.addEventListener(
    'submit',
    async function (event) {

        if (
            event.target.id !==
            'patternTestFitStoreForm'
        ) {

            return;

        }


        event.preventDefault();


        /*
        |--------------------------------------------------------------------------
        | TYPE
        |--------------------------------------------------------------------------
        */

        const typeElement =
            document.querySelector(
                'input[name="type"]:checked'
            );


        if (!typeElement) {

            Swal.fire(
                'Required',
                'Please choose Pattern or Test Fit.',
                'warning'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE COUNT
        |--------------------------------------------------------------------------
        */

        const imageCount =
            parseInt(
                document.getElementById(
                    'ptf_image_count'
                ).value,
                10
            );


        if (
            isNaN(imageCount) ||
            imageCount < 1
        ) {

            Swal.fire(
                'Required',
                'Please enter a valid image count.',
                'warning'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        const imageInputs =
            document.querySelectorAll(
                '.ptf-image-input'
            );


        let selectedImages =
            0;


        imageInputs.forEach(
            function (input) {

                if (
                    input.files &&
                    input.files.length > 0
                ) {

                    selectedImages++;

                }

            }
        );


        if (
            selectedImages !== imageCount
        ) {

            Swal.fire(
                'Images Required',
                'Please attach exactly ' +
                imageCount +
                ' images. You selected ' +
                selectedImages +
                '.',
                'warning'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE
        |--------------------------------------------------------------------------
        */

        const warehouseId =
            document.getElementById(
                'ptf_warehouse_id'
            ).value;


        if (!warehouseId) {

            Swal.fire(
                'Required',
                'Please select warehouse.',
                'warning'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        const locationId =
            document.getElementById(
                'ptf_location_id'
            ).value;


        if (!locationId) {

            Swal.fire(
                'Required',
                'Please select location.',
                'warning'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | BOX
        |--------------------------------------------------------------------------
        */

        const boxId =
            document.getElementById(
                'ptf_box_id'
            ).value;


        if (!boxId) {

            Swal.fire(
                'Required',
                'Please select box.',
                'warning'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | CONFIRM
        |--------------------------------------------------------------------------
        */

        const confirmation =
            await Swal.fire({

                title:
                    'Save Stock?',

                text:
                    'Are you sure you want to save this ' +
                    (
                        typeElement.value === 'pattern'
                            ? 'Pattern'
                            : 'Test Fit'
                    ) +
                    ' stock with ' +
                    imageCount +
                    ' images?',

                icon:
                    'question',

                showCancelButton:
                    true,

                confirmButtonText:
                    'Yes, Save',

                cancelButtonText:
                    'Cancel'

            });


        if (
            !confirmation.isConfirmed
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | BUTTON
        |--------------------------------------------------------------------------
        */

        const saveButton =
            document.getElementById(
                'btnSavePatternTestFit'
            );


        const originalButtonHtml =
            saveButton.innerHTML;


        saveButton.disabled =
            true;


        saveButton.innerHTML = `

            <span
                class="spinner-border spinner-border-sm me-1"
            ></span>

            Saving...

        `;


        /*
        |--------------------------------------------------------------------------
        | FORMDATA
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'patternTestFitStoreForm'
            );


        const formData =
            new FormData(
                form
            );


        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        try {

            const response =
                await fetch(
                    "{{ route('inventory.pattern-test-fit-stock.save') }}",
                    {
                        method:
                            'POST',

                        body:
                            formData,

                        headers: {

                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest'

                        }

                    }
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                throw new Error(
                    result.message ||
                    'Unable to save stock.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            await Swal.fire({

                title:
                    'Saved Successfully',

                html:
                    `
                    <div class="text-start">

                        <div>
                            <strong>
                                Stock:
                            </strong>
                            ${typeElement.value === 'pattern'
                                ? 'Pattern'
                                : 'Test Fit'}
                        </div>

                        <div>
                            <strong>
                                Main Barcode:
                            </strong>
                            ${result.data.main_barcode}
                        </div>

                        <div>
                            <strong>
                                Images:
                            </strong>
                            ${result.data.image_count}
                        </div>

                    </div>
                    `,

                icon:
                    'success'

            });


            /*
            |--------------------------------------------------------------------------
            | CLOSE
            |--------------------------------------------------------------------------
            */

            const modalElement =
                document.getElementById(
                    'patternTestFitStoreModal'
                );


            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );


            modal.hide();


            /*
            |--------------------------------------------------------------------------
            | RELOAD
            |--------------------------------------------------------------------------
            */

            setTimeout(
                function () {

                    window.location.reload();

                },
                500
            );


        } catch (error) {

            console.error(
                'Pattern/Test Fit Save Error:',
                error
            );


            Swal.fire(
                'Save Failed',
                error.message ||
                'Unable to save stock.',
                'error'
            );


            saveButton.disabled =
                false;


            saveButton.innerHTML =
                originalButtonHtml;

        }

    }
);


</script>

@endsection