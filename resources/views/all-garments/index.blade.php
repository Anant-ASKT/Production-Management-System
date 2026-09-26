@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- =========================================================

        PAGE HEADER

    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">

                All Garments

            </h4>

            <div class="text-muted">

                View all garment design specifications

            </div>

        </div>

    </div>



    {{-- =========================================================
        GARMENT FILTERS
        Product Type + Product Name + Composition + Gender + AI Enhancer
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-3 align-items-end">

                <div class="col-md-6 col-xl">
                    <label for="garmentItemType" class="form-label fw-semibold">
                        Product Type
                    </label>
                    <select id="garmentItemType" class="form-select garment-filter-select">
                        <option value="">All Product Types</option>
                        @isset($itemTypes)
                            @foreach($itemTypes as $itemType)
                                <option value="{{ $itemType->id }}">
                                    {{ $itemType->itemtype }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl">
                    <label for="garmentItemName" class="form-label fw-semibold">
                        Product Name
                    </label>
                    <select id="garmentItemName" class="form-select garment-filter-select">
                        <option value="">All Product Names</option>
                        @isset($itemNames)
                            @foreach($itemNames as $itemName)
                                <option value="{{ $itemName->id }}">
                                    {{ $itemName->itemname }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl">
                    <label for="garmentComposition" class="form-label fw-semibold">
                        Composition
                    </label>
                    <select id="garmentComposition" class="form-select garment-filter-select">
                        <option value="">All Compositions</option>
                        @isset($compositions)
                            @foreach($compositions as $composition)
                                <option value="{{ $composition->id }}">
                                    {{ $composition->composition_details }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl">
                    <label for="garmentGender" class="form-label fw-semibold">
                        Gender Type
                    </label>
                    <select id="garmentGender" class="form-select garment-filter-select">
                        <option value="">All Gender Types</option>
                        @isset($genders)
                            @foreach($genders as $gender)
                                <option value="{{ $gender->id }}">
                                    {{ $gender->name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl">
                    <label for="garmentAiSent" class="form-label fw-semibold">
                        AI Enhancer
                    </label>
                    <select id="garmentAiSent" class="form-select garment-filter-select">
                        <option value="yes" selected>Show Only Send Product for AI Image</option>
                        <option value="all">Show All Products</option>
                    </select>
                </div>

            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <button type="button" id="btnApplyGarmentFilter" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>
                    Apply Filter
                </button>

                <button type="button" id="btnClearGarmentFilter" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Clear
                </button>
            </div>

        </div>
    </div>

    {{-- =========================================================

        SEARCH

    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="input-group">

                <span class="input-group-text">

                    <i class="bi bi-search"></i>

                </span>

                <input

                    type="text"

                    id="garmentSearch"

                    class="form-control"

                    placeholder="Search garments..."

                >

            </div>

        </div>

    </div>



    {{-- =========================================================
        IMAGE DOWNLOAD TOOLBAR
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">

            <div>
                <strong>
                    <i class="bi bi-images me-1"></i>
                    Product Image Download
                </strong>

                <div id="garmentSelectedImageInfo" class="small text-muted mt-1">
                    0 products selected
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button
                    type="button"
                    id="btnDownloadSelectedGarmentImages"
                    class="btn btn-success"
                    disabled
                >
                    <i class="bi bi-download me-1"></i>
                    Download Selected Images
                </button>

                <button
                    type="button"
                    id="btnClearSelectedGarmentImages"
                    class="btn btn-outline-secondary"
                    disabled
                >
                    <i class="bi bi-x-circle me-1"></i>
                    Clear Selected
                </button>
            </div>

        </div>
    </div>


    {{-- =========================================================

        LOADING

    ========================================================== --}}

    <div

        id="garmentLoading"

        class="text-center py-4"

        style="display:none;"

    >

        <div

            class="spinner-border text-primary"

            role="status"

        >

            <span class="visually-hidden">

                Loading...

            </span>

        </div>

        <div class="mt-2 text-muted">

            Loading garments...

        </div>

    </div>



    {{-- =========================================================

        EMPTY

    ========================================================== --}}

    <div

        id="garmentEmpty"

        class="alert alert-info text-center"

        style="display:none;"

    >

        No garments available.

    </div>



    {{-- =========================================================

        CARDS

    ========================================================== --}}

    <div

        id="garmentCards"

        class="row g-4"

    >

    </div>



    {{-- =========================================================

        PAGINATION

    ========================================================== --}}

    <div

        id="garmentPagination"

        class="mt-4 pb-4"

    >

    </div>

</div>


    {{-- =========================================================
        ALL GARMENT VIEW MODAL
    ========================================================== --}}

    <div
        class="modal fade"
        id="allGarmentViewModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered modal-xl">

            <div class="modal-content">

                {{-- HEADER --}}

                <div class="modal-header">

                    <div>

                        <h5 class="modal-title mb-1">

                            <i class="bi bi-eye me-2"></i>

                            Garment Details

                        </h5>

                        <small class="text-muted">
                            Complete garment specification
                        </small>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                {{-- BODY --}}

                <div class="modal-body">

                    {{-- =================================================
                         PRODUCT BASIC INFORMATION
                    ================================================== --}}

                    <div class="row g-4 mb-4">

                        <div class="col-md-4">

                            {{-- ORIGINAL PRODUCT IMAGE --}}
                            <div
                                id="allGarmentViewImage"
                                class="rounded border d-flex align-items-center justify-content-center"
                                style="
                                    height:300px;
                                    background:#f5f6f8;
                                    overflow:hidden;
                                "
                            >

                                <div class="text-center text-muted">

                                    <i
                                        class="bi bi-image"
                                        style="font-size:50px;"
                                    ></i>

                                    <div>
                                        No Image
                                    </div>

                                </div>

                            </div>

                            {{-- AI UPLOADED IMAGES --}}
                            <div
                                id="allGarmentViewAIImagesSection"
                                class="card border mt-3"
                                style="display:none;"
                            >
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="small">
                                            <i class="bi bi-stars me-1 text-success"></i>
                                            AI Uploaded Images
                                        </strong>
                                        <span
                                            id="allGarmentViewAIImagesCount"
                                            class="badge bg-success"
                                        >
                                            0
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body p-2">
                                    <div
                                        id="allGarmentViewAIImages"
                                        class="row g-2"
                                    ></div>

                                    <div
                                        id="allGarmentViewAIImagesEmpty"
                                        class="text-center text-muted small py-2"
                                    >
                                        No AI images uploaded.
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="col-md-8">

                            <div class="mb-3">

                                <div class="text-muted small">
                                    SKU
                                </div>

                                <h4
                                    id="allGarmentViewSku"
                                    class="mb-0"
                                >
                                    -
                                </h4>

                            </div>


                            <div class="mb-3">

                                <div class="text-muted small">
                                    Barcode
                                </div>

                                <strong
                                    id="allGarmentViewBarcode"
                                >
                                    -
                                </strong>

                            </div>


                            <div class="mb-3">

                                <div class="text-muted small">
                                    Item Name
                                </div>

                                <strong
                                    id="allGarmentViewItemName"
                                >
                                    -
                                </strong>

                            </div>


                            <div
                                id="allGarmentViewStatus"
                            ></div>

                        </div>

                    </div>


                    {{-- =================================================
                         DESIGN INFORMATION
                    ================================================== --}}

                    <div class="card border mb-4">

                        <div class="card-header bg-light">

                            <strong>

                                <i class="bi bi-info-circle me-2"></i>

                                Design Information

                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row g-3">


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Designer
                                    </div>

                                    <strong
                                        id="allGarmentViewDesigner"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Item Type
                                    </div>

                                    <strong
                                        id="allGarmentViewItemType"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Gender
                                    </div>

                                    <strong
                                        id="allGarmentViewGender"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Item Name
                                    </div>

                                    <strong
                                        id="allGarmentViewItemName2"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Composition
                                    </div>

                                    <strong
                                        id="allGarmentViewComposition"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Colour
                                    </div>

                                    <strong
                                        id="allGarmentViewColour"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Size
                                    </div>

                                    <strong
                                        id="allGarmentViewSize"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Embellishment
                                    </div>

                                    <strong
                                        id="allGarmentViewEmbellishment"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Manufacturing Process
                                    </div>

                                    <strong
                                        id="allGarmentViewManufacturingProcess"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Craftsman
                                    </div>

                                    <strong
                                        id="allGarmentViewCraftsman"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Craftsman Code
                                    </div>

                                    <strong
                                        id="allGarmentViewCraftsmanCode"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Manufacturer
                                    </div>

                                    <strong
                                        id="allGarmentViewManufacture"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Client
                                    </div>

                                    <strong
                                        id="allGarmentViewClient"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-8">

                                    <div class="text-muted small">
                                        Client Reference
                                    </div>

                                    <strong
                                        id="allGarmentViewClientReference"
                                    >
                                        -
                                    </strong>

                                </div>


                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         COMPANY CONTEXT
                    ================================================== --}}

                    <div class="card border mb-4">

                        <div class="card-header bg-light">

                            <strong>

                                <i class="bi bi-building me-2"></i>

                                Company Context

                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Company
                                    </div>

                                    <strong
                                        id="allGarmentViewCompany"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Sub Company
                                    </div>

                                    <strong
                                        id="allGarmentViewSubCompany"
                                    >
                                        -
                                    </strong>

                                </div>


                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Project
                                    </div>

                                    <strong
                                        id="allGarmentViewProject"
                                    >
                                        -
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         AI INFORMATION
                    ================================================== --}}

                    <div
                        class="card border mb-4"
                        id="allGarmentViewAISection"
                        style="display:none;"
                    >

                        <div class="card-header bg-light">

                            <strong>

                                <i class="bi bi-stars me-2"></i>

                                AI Product Information

                            </strong>

                        </div>


                        <div class="card-body">

                            <div class="row g-3">


                                <div class="col-12">

                                    <div class="text-muted small">
                                        Product Name
                                    </div>

                                    <div
                                        id="allGarmentViewAIProductName"
                                    >
                                        -
                                    </div>

                                </div>


                                <div class="col-12">

                                    <div class="text-muted small">
                                        Product Description
                                    </div>

                                    <div
                                        id="allGarmentViewAIProductDescription"
                                        style="white-space:pre-wrap;"
                                    >
                                        -
                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="text-muted small">
                                        Meta Title
                                    </div>

                                    <div
                                        id="allGarmentViewAIMetaTitle"
                                    >
                                        -
                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="text-muted small">
                                        Meta Keywords
                                    </div>

                                    <div
                                        id="allGarmentViewAIMetaKeywords"
                                    >
                                        -
                                    </div>

                                </div>


                                <div class="col-12">

                                    <div class="text-muted small">
                                        Meta Description
                                    </div>

                                    <div
                                        id="allGarmentViewAIMetaDescription"
                                        style="white-space:pre-wrap;"
                                    >
                                        -
                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="text-muted small">
                                        Product Tags
                                    </div>

                                    <div
                                        id="allGarmentViewAIProductTags"
                                    >
                                        -
                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="text-muted small">
                                        Image Alt Text
                                    </div>

                                    <div
                                        id="allGarmentViewAIImageAltText"
                                    >
                                        -
                                    </div>

                                </div>


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

                        <i class="bi bi-x-lg me-1"></i>

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
         AI IMAGE UPLOAD MODAL
         Main AI Image + Multiple Sub AI Images
    ========================================================== --}}

    <style>
        .ai-images-modal-dialog {
            width: 96vw;
            max-width: 1650px;
            margin: 1.25rem auto;
        }

        .ai-images-modal-content {
            min-height: 90vh;
            border: 0;
            border-radius: 16px;
            overflow: hidden;
        }

        .ai-images-modal-body {
            background: #f5f7fa;
            max-height: calc(90vh - 135px);
            overflow-y: auto;
        }

        .ai-original-panel,
        .ai-main-panel,
        .ai-sub-panel,
        .ai-saved-panel {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #fff;
        }

        .ai-original-preview {
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: #f8fafc;
            border-radius: 0 0 14px 14px;
        }

        .ai-original-preview img {
            max-width: 100%;
            max-height: 500px;
            object-fit: contain;
            cursor: pointer;
        }

        .ai-upload-dropzone {
            min-height: 150px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            cursor: pointer;
            transition: .2s ease;
        }

        .ai-upload-dropzone:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .ai-preview-image-box,
        .ai-saved-image-box {
            height: 165px;
            background: #f1f5f9;
            overflow: hidden;
            position: relative;
        }

        .ai-preview-image-box img,
        .ai-saved-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ai-section-title {
            padding: 13px 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 14px 14px 0 0;
        }

        .ai-uploaded-product-card {
            border: 2px solid #198754 !important;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, .10), 0 8px 24px rgba(25, 135, 84, .12) !important;
        }

        .ai-uploaded-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 5;
            font-size: 11px;
            padding: 6px 9px;
            border-radius: 999px;
        }

        .ai-saved-image-box {
            cursor: zoom-in;
        }

        .ai-saved-image-box img,
        .ai-original-preview img {
            cursor: zoom-in;
        }

        @media (max-width: 1199.98px) {
            .ai-images-modal-dialog {
                width: 98vw;
                max-width: 98vw;
            }

            .ai-images-modal-content {
                min-height: 94vh;
            }

            .ai-images-modal-body {
                max-height: calc(94vh - 135px);
            }
        }

        @media (max-width: 767.98px) {
            .ai-images-modal-dialog {
                width: 100%;
                max-width: 100%;
                margin: 0;
            }

            .ai-images-modal-content {
                min-height: 100vh;
                border-radius: 0;
            }

            .ai-images-modal-body {
                max-height: calc(100vh - 135px);
            }

            .ai-original-preview {
                min-height: 300px;
            }

            .ai-preview-image-box,
            .ai-saved-image-box {
                height: 140px;
            }
        }
    </style>

    <div
        class="modal fade"
        id="uploadAiImagesModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered ai-images-modal-dialog">
            <div class="modal-content ai-images-modal-content shadow-lg">

                <div class="modal-header px-4 py-3">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="bi bi-stars me-2"></i>
                            Upload AI Images
                        </h5>
                        <div class="small text-muted">
                            Barcode:
                            <strong id="aiUploadBarcode">-</strong>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body ai-images-modal-body p-3 p-lg-4">

                    <input type="hidden" id="aiUploadSpecificationId">
                    <input type="hidden" id="aiUploadBarcodeValue">

                    <div class="row g-4">

                        {{-- ORIGINAL IMAGE --}}
                        <div class="col-xl-5">
                            <div class="ai-original-panel h-100 shadow-sm">
                                <div class="ai-section-title d-flex justify-content-between align-items-center">
                                    <strong>
                                        <i class="bi bi-image me-2"></i>
                                        Original Image
                                    </strong>
                                    <span class="badge text-bg-secondary">
                                        Product Image
                                    </span>
                                </div>

                                <div id="aiOriginalImageBox" class="ai-original-preview">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image fs-1"></i>
                                        <div class="mt-2">No Original Image</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- AI UPLOAD AREA --}}
                        <div class="col-xl-7">

                            {{-- MAIN AI IMAGE --}}
                            <div class="ai-main-panel shadow-sm mb-4">
                                <div class="ai-section-title d-flex justify-content-between align-items-center">
                                    <strong>
                                        <i class="bi bi-stars me-2"></i>
                                        Main AI Image
                                    </strong>
                                    <span class="badge text-bg-primary">
                                        1 image
                                    </span>
                                </div>

                                <div class="p-3">
                                    <label
                                        for="aiMainImageInput"
                                        class="ai-upload-dropzone d-flex flex-column align-items-center justify-content-center text-center p-4"
                                    >
                                        <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                                        <strong class="mt-2">
                                            Select Main AI Image
                                        </strong>
                                        <span class="small text-muted">
                                            Select one image. Uploading a new Main AI Image replaces the existing Main AI Image.
                                        </span>
                                        <span class="small text-muted mt-1">
                                            JPG, JPEG, PNG, WEBP or HEIC • Maximum 10 MB
                                        </span>
                                    </label>

                                    <input
                                        type="file"
                                        id="aiMainImageInput"
                                        class="d-none"
                                        accept="image/jpeg,image/png,image/webp,image/heic,.heic"
                                    >

                                    <div id="aiMainImagePreview" class="mt-3"></div>
                                </div>
                            </div>

                            {{-- SUB AI IMAGES --}}
                            <div class="ai-sub-panel shadow-sm">
                                <div class="ai-section-title d-flex justify-content-between align-items-center">
                                    <strong>
                                        <i class="bi bi-images me-2"></i>
                                        Sub AI Images
                                    </strong>
                                    <span id="aiSelectedSubCount" class="badge text-bg-primary">
                                        0 selected
                                    </span>
                                </div>

                                <div class="p-3">
                                    <label
                                        for="aiSubImagesInput"
                                        class="ai-upload-dropzone d-flex flex-column align-items-center justify-content-center text-center p-4"
                                    >
                                        <i class="bi bi-images fs-1 text-primary"></i>
                                        <strong class="mt-2">
                                            Select Sub AI Images
                                        </strong>
                                        <span class="small text-muted">
                                            You can select multiple images at once.
                                        </span>
                                        <span class="small text-muted mt-1">
                                            JPG, JPEG, PNG, WEBP or HEIC • Maximum 10 MB each
                                        </span>
                                    </label>

                                    <input
                                        type="file"
                                        id="aiSubImagesInput"
                                        class="d-none"
                                        accept="image/jpeg,image/png,image/webp,image/heic,.heic"
                                        multiple
                                    >

                                    <div id="aiSubUploadPreview" class="row g-3 mt-1"></div>

                                    <div
                                        id="aiSubUploadEmpty"
                                        class="text-center text-muted py-3"
                                    >
                                        No new Sub AI Images selected.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- SAVED AI IMAGES --}}
                    <div class="ai-saved-panel shadow-sm mt-4">
                        <div class="ai-section-title d-flex justify-content-between align-items-center">
                            <strong>
                                <i class="bi bi-check2-circle me-2"></i>
                                Saved AI Images
                            </strong>
                            <span id="aiExistingImageCount" class="small text-muted">
                                0 images
                            </span>
                        </div>

                        <div class="p-3">
                            <div id="aiExistingImages" class="row g-3"></div>

                            <div
                                id="aiExistingEmpty"
                                class="text-center text-muted py-4"
                            >
                                No AI images saved for this product.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer px-4 py-3">
                    <div
                        class="me-auto small text-muted"
                        id="aiUploadStatus"
                    ></div>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btnSaveAiImages"
                    >
                        <i class="bi bi-save me-1"></i>
                        Save AI Images
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection



@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>

document.addEventListener(

    'DOMContentLoaded',

    function () {



        /*

        |--------------------------------------------------------------------------

        | ELEMENTS

        |--------------------------------------------------------------------------

        */
        const itemTypeSelect =
            document.getElementById(
                'garmentItemType'
            );


        const itemNameSelect =
            document.getElementById(
                'garmentItemName'
            );


        const compositionSelect =
            document.getElementById(
                'garmentComposition'
            );


        const genderSelect =
            document.getElementById(
                'garmentGender'
            );


        const aiSentSelect =
            document.getElementById(
                'garmentAiSent'
            );



        const searchInput =

            document.getElementById(

                'garmentSearch'

            );



        const applyButton =

            document.getElementById(

                'btnApplyGarmentFilter'

            );



        const clearButton =

            document.getElementById(

                'btnClearGarmentFilter'

            );



        const cardsContainer =

            document.getElementById(

                'garmentCards'

            );



        const emptyContainer =

            document.getElementById(

                'garmentEmpty'

            );



        const loadingContainer =

            document.getElementById(

                'garmentLoading'

            );



        const paginationContainer =

            document.getElementById(

                'garmentPagination'

            );


        const downloadSelectedButton =
            document.getElementById(
                'btnDownloadSelectedGarmentImages'
            );


        const clearSelectedButton =
            document.getElementById(
                'btnClearSelectedGarmentImages'
            );


        const selectedImageInfo =
            document.getElementById(
                'garmentSelectedImageInfo'
            );


        const selectedGarments = new Map();



        /*

        |--------------------------------------------------------------------------

        | HTML ESCAPE

        |--------------------------------------------------------------------------

        */

        function escapeHtml(value) {

            if (

                value === null ||

                value === undefined

            ) {

                return '';

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

        | IMAGE URL

        |--------------------------------------------------------------------------

        */

        function parseImageValues(value) {

            if (
                value === null ||
                value === undefined ||
                value === ''
            ) {
                return [];
            }

            if (Array.isArray(value)) {
                return value.flatMap(function (item) {
                    return parseImageValues(item);
                });
            }

            if (typeof value === 'object') {
                return parseImageValues(
                    value.path ||
                    value.url ||
                    value.image ||
                    ''
                );
            }

            let text = String(value).trim();

            if (!text) {
                return [];
            }

            if (
                text.startsWith('[') &&
                text.endsWith(']')
            ) {
                try {
                    const parsed = JSON.parse(text);

                    if (Array.isArray(parsed)) {
                        return parsed.flatMap(function (item) {
                            return parseImageValues(item);
                        });
                    }
                } catch (error) {
                    console.warn(
                        'Image JSON parse error:',
                        error
                    );
                }
            }

            return [text];
        }



        /*
        |--------------------------------------------------------------------------
        | IMAGE PATH HANDLER
        |--------------------------------------------------------------------------
        | Supports BOTH existing database formats:
        |
        | 1. Legacy:
        |    ../../ItemsDesigner_Masterwithbarcode/1479293301111/
        |
        | 2. New:
        |    ["ItemsDesigner_Masterwithbarcode/91350782350210/I1L-IDI-2109-02pEWKwn9i.jpeg"]
        |
        | IMPORTANT:
        | A legacy value can be a DIRECTORY only. The browser cannot discover
        | filenames inside a directory. Therefore image_urls (resolved by the
        | controller) is always checked first. New JSON file paths are used
        | directly.
        |--------------------------------------------------------------------------
        */

        function getImageUrl(image) {

            if (
                image === null ||
                image === undefined ||
                image === ''
            ) {
                return '';
            }

            if (Array.isArray(image)) {
                const values = image.flatMap(function (item) {
                    return parseImageValues(item);
                });

                /*
                | Prefer a real file, never a directory-only value.
                */
                const firstFile = values.find(function (item) {
                    const value = String(item || '').trim();

                    return (
                        value !== '' &&
                        /\.(jpe?g|png|webp|gif|bmp|svg|avif|heic)(?:[?#].*)?$/i.test(value)
                    );
                });

                return firstFile
                    ? getImageUrl(firstFile)
                    : '';
            }

            let text = String(image).trim();

            if (!text) {
                return '';
            }

            /*
            | JSON string:
            | ["ItemsDesigner_Masterwithbarcode/.../file.jpeg"]
            */
            if (
                text.startsWith('[') &&
                text.endsWith(']')
            ) {
                const values = parseImageValues(text);

                const firstFile = values.find(function (item) {
                    const value = String(item || '').trim();

                    return (
                        value !== '' &&
                        /\.(jpe?g|png|webp|gif|bmp|svg|avif|heic)(?:[?#].*)?$/i.test(value)
                    );
                });

                return firstFile
                    ? getImageUrl(firstFile)
                    : '';
            }

            if (
                text.startsWith('http://') ||
                text.startsWith('https://') ||
                text.startsWith('data:') ||
                text.startsWith('blob:')
            ) {
                return text;
            }

            text = text
                .replace(/\\/g, '/')
                .replace(/^\s*["']+|["']+\s*$/g, '');

            /*
            | Keep the complete public path after ItemsDesigner_Masterwithbarcode.
            | This handles both:
            | ../../ItemsDesigner_Masterwithbarcode/...
            | ItemsDesigner_Masterwithbarcode/...
            */
            const marker = 'ItemsDesigner_Masterwithbarcode/';

            const markerPosition = text.indexOf(marker);

            if (markerPosition !== -1) {
                const publicPath = text.substring(markerPosition);

                /*
                | Do NOT return a directory-only path as an image.
                | It must have a real image filename.
                */
                if (
                    !/\.(jpe?g|png|webp|gif|bmp|svg|avif|heic)(?:[?#].*)?$/i.test(
                        publicPath
                    )
                ) {
                    return '';
                }

                return "{{ asset('') }}" + publicPath;
            }

            text = text
                .replace(/^(?:\.\.\/|\.\/)+/g, '')
                .replace(/^\/+/, '')
                .replace(/^public\/+/i, '');

            if (
                !/\.(jpe?g|png|webp|gif|bmp|svg|avif|heic)(?:[?#].*)?$/i.test(text)
            ) {
                return '';
            }

            return "{{ asset('') }}" + text;
        }



        function getGarmentImages(garment) {

            if (!garment) {
                return [];
            }

            /*
            | IMPORTANT:
            | image_urls is controller-resolved and MUST come first.
            | This is what allows the legacy directory format to work when
            | the database contains only:
            | ../../ItemsDesigner_Masterwithbarcode/1479293301111/
            */
            const candidates = [
                garment.image_urls,
                garment.image_url,

                garment.img_path,
                garment.subimg_path,
                garment.image,
                garment.image_path,
                garment.main_image,
                garment.design_image,
                garment.oc_main_img,
                garment.sub_images,
                garment.sub_images_path,
                garment.images
            ];

            const rawImages = [];

            candidates.forEach(function (value) {
                parseImageValues(value).forEach(function (image) {

                    const clean = String(image || '').trim();

                    if (!clean) {
                        return;
                    }

                    /*
                    | Keep real image files.
                    | Keep legacy directory values only as a fallback; the
                    | controller-resolved image_urls will be selected first.
                    */
                    rawImages.push(clean);
                });
            });

            const seen = new Set();
            const result = [];

            rawImages.forEach(function (image) {

                const key = image.toLowerCase();

                if (!seen.has(key)) {
                    seen.add(key);
                    result.push(image);
                }
            });

            return result;
        }


        function getFileExtension(url) {

            try {
                const cleanUrl =
                    String(url || '')
                        .split('?')[0]
                        .split('#')[0];

                const match =
                    cleanUrl.match(
                        /\.([a-zA-Z0-9]{2,5})$/
                    );

                if (match) {
                    return match[1].toLowerCase();
                }
            } catch (error) {
                // JPG fallback.
            }

            return 'jpg';
        }



        function sanitizeFileName(value) {

            const name =
                String(value || 'garment')
                    .trim()
                    .replace(
                        /[<>:"\\/|?*\x00-\x1F]/g,
                        '_'
                    )
                    .replace(
                        /\s+/g,
                        '_'
                    );

            return name || 'garment';
        }


        /*

        |--------------------------------------------------------------------------

        | LOAD GARMENTS

        |--------------------------------------------------------------------------

        */

        function loadGarments(

            page = 1

        ) {



            /*

            |--------------------------------------------------------------------------

            | IMPORTANT:

            | CLEAR OLD DATA BEFORE EVERY REQUEST

            |--------------------------------------------------------------------------

            |

            | This prevents old project's garments from remaining visible

            | while a new project is being loaded.

            |

            */

            if (cardsContainer) {

                cardsContainer.innerHTML = '';

            }



            if (paginationContainer) {

                paginationContainer.innerHTML = '';

            }



            if (emptyContainer) {

                emptyContainer.style.display =

                    'none';

            }



            if (loadingContainer) {

                loadingContainer.style.display =

                    'block';

            }



            /*

            |--------------------------------------------------------------------------

            | PARAMETERS

            |--------------------------------------------------------------------------

            */

            const params =

                new URLSearchParams();



            params.set(

                'page',

                page

            );



            params.set(

                'per_page',

                20

            );



            /*

            |--------------------------------------------------------------------------

            | SEARCH

            |--------------------------------------------------------------------------

            */

            const search =

                searchInput

                    ? searchInput.value.trim()

                    : '';



            if (search !== '') {

                params.set(

                    'search',

                    search

                );

            }


            if (
                itemTypeSelect &&
                itemTypeSelect.value
            ) {

                params.set(
                    'item_type',
                    itemTypeSelect.value
                );

            }


            if (
                itemNameSelect &&
                itemNameSelect.value
            ) {

                params.set(
                    'item_name',
                    itemNameSelect.value
                );

            }


            if (
                compositionSelect &&
                compositionSelect.value
            ) {

                params.set(
                    'composition',
                    compositionSelect.value
                );

            }


            if (
                genderSelect &&
                genderSelect.value
            ) {

                params.set(
                    'gender',
                    genderSelect.value
                );

            }


            if (
                aiSentSelect &&
                aiSentSelect.value
            ) {

                params.set(
                    'ai_sent',
                    aiSentSelect.value
                );

            }



            /*

            |--------------------------------------------------------------------------

            | FINAL URL

            |--------------------------------------------------------------------------

            */

            const url =

                "{{ route('all-garments.data') }}" +

                '?' +

                params.toString();



            console.log(

                'All Garments Request:',

                url

            );



            /*

            |--------------------------------------------------------------------------

            | AJAX

            |--------------------------------------------------------------------------

            */

            fetch(

                url,

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

            .then(

                function (response) {

                    if (!response.ok) {

                        throw new Error(

                            'Unable to load garments.'

                        );

                    }



                    return response.json();

                }

            )

            .then(

                function (response) {

                    console.log(

                        'All Garments Response:',

                        response

                    );



                    if (!response.success) {

                        throw new Error(

                            response.message ||

                            'Unable to load garments.'

                        );

                    }



                    /*

                    |--------------------------------------------------------------------------

                    | ALWAYS CLEAR OLD DATA

                    |--------------------------------------------------------------------------

                    */

                    if (cardsContainer) {

                        cardsContainer.innerHTML =

                            '';

                    }



                    /*

                    |--------------------------------------------------------------------------

                    | NO DATA

                    |--------------------------------------------------------------------------

                    */

                    if (

                        !response.data ||

                        !Array.isArray(response.data) ||

                        response.data.length === 0

                    ) {

                        if (emptyContainer) {

                            emptyContainer.style.display =

                                'block';

                            emptyContainer.innerHTML = `

                                <i class="bi bi-info-circle me-2"></i>

                                No garments available.

                            `;

                        }



                        if (paginationContainer) {

                            paginationContainer.innerHTML =

                                '';

                        }



                        return;

                    }



                    /*

                    |--------------------------------------------------------------------------

                    | RENDER

                    |--------------------------------------------------------------------------

                    */

                    if (emptyContainer) {

                        emptyContainer.style.display =

                            'none';

                    }



                    renderGarments(

                        response.data,

                        response

                    );

                }

            )

            .catch(

                function (error) {

                    console.error(

                        'All Garments Error:',

                        error

                    );



                    /*

                    |--------------------------------------------------------------------------

                    | CLEAR OLD CARDS

                    |--------------------------------------------------------------------------

                    */

                    if (cardsContainer) {

                        cardsContainer.innerHTML =

                            '';

                    }



                    if (paginationContainer) {

                        paginationContainer.innerHTML =

                            '';

                    }



                    /*

                    |--------------------------------------------------------------------------

                    | ERROR

                    |--------------------------------------------------------------------------

                    */

                    if (emptyContainer) {

                        emptyContainer.style.display =

                            'block';

                        emptyContainer.className =

                            'alert alert-danger text-center';

                        emptyContainer.innerHTML = `

                            <i class="bi bi-exclamation-triangle me-2"></i>

                            ${escapeHtml(

                                error.message

                            )}

                        `;

                    }

                }

            )

            .finally(

                function () {

                    if (loadingContainer) {

                        loadingContainer.style.display =

                            'none';

                    }

                }

            );

        }



        /*

        |--------------------------------------------------------------------------

        | RENDER GARMENTS

        |--------------------------------------------------------------------------

        */

        function renderGarments(
            garments,
            meta
        ) {

            if (!cardsContainer) {
                return;
            }

            cardsContainer.innerHTML = '';

            if (
                !Array.isArray(garments) ||
                garments.length === 0
            ) {

                if (emptyContainer) {
                    emptyContainer.style.display = 'block';
                }

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | RENDER EACH GARMENT
            |--------------------------------------------------------------------------
            */

            garments.forEach(
                function (garment) {

                    const col =
                        document.createElement('div');

                    col.className =
                        'col-md-6 col-lg-4';

                    const hasAiImages =
                        String(garment.ai_images_uploaded ?? '0') === '1' ||
                        garment.ai_images_uploaded === true ||
                        Number(garment.ai_images_uploaded) > 0;


                    /*
                    |--------------------------------------------------------------------------
                    | IMAGE
                    |--------------------------------------------------------------------------
                    */

                    /*
                    |--------------------------------------------------------------------------
                    | IMAGE
                    |--------------------------------------------------------------------------
                    | Always use getGarmentImages() here because img_path may be:
                    |
                    | 1. JSON array:
                    |    ["ItemsDesigner_Masterwithbarcode/.../SKU.jpeg"]
                    |
                    | 2. Old relative barcode-folder path:
                    |    ../../ItemsDesigner_Masterwithbarcode/147.../
                    |
                    | The helper normalizes both formats.
                    |--------------------------------------------------------------------------
                    */

                    const garmentImages =
                        getGarmentImages(garment);

                    const image =
                        garmentImages.length
                            ? garmentImages[0]
                            : '';

                    const imageUrl =
                        getImageUrl(image);


                    /*
                    |--------------------------------------------------------------------------
                    | DISPLAY VALUES
                    |--------------------------------------------------------------------------
                    |
                    | The controller supplies both internal IDs and readable
                    | master values. Cards should show the readable values.
                    |
                    */

                    const sku =
                        garment.sku || '';

                    const barcode =
                        garment.barcode || '';

                    const productName =
                        garment.item_name_text ||
                        garment.productname ||
                        garment.product_name ||
                        garment.name ||
                        garment.AI_product_name ||
                        'Garment';

                    const itemType =
                        garment.item_type_text ||
                        garment.item_type ||
                        '-';

                    const gender =
                        garment.gender_text ||
                        garment.gender ||
                        '-';


                    const composition =
                        garment.composition_text ||
                        garment.composition ||
                        '-';

                    const designer =
                        garment.designer_name_text ||
                        garment.designer_name ||
                        '-';

                    const colour =
                        garment.colour_text ||
                        garment.colour ||
                        '-';

                    const size =
                        garment.size_text ||
                        garment.sizes ||
                        '-';

                    const companyName =
                        garment.company_name ||
                        '-';

                    const subCompanyName =
                        garment.subcompany_name ||
                        '-';

                    const projectName =
                        garment.project_name ||
                        '-';

                    const status =
                        garment.status ||
                        '';

                    const specificationId =
                        garment.sno ||
                        garment.id ||
                        '';


                    /*
                    |--------------------------------------------------------------------------
                    | CARD
                    |--------------------------------------------------------------------------
                    */

                    col.innerHTML = `

                        <div
                            class="card h-100 border-0 shadow-sm specification-card ${hasAiImages ? 'ai-uploaded-product-card' : ''}"
                        >

                            <div class="card-body p-0">

                                <div
                                    class="specification-image-wrapper position-relative"
                                    style="
                                        height:240px;
                                        overflow:hidden;
                                        background:#f5f6f8;
                                        border-radius:8px 8px 0 0;
                                    "
                                >

                                    ${
                                        hasAiImages
                                            ? `
                                                <span class="badge bg-success ai-uploaded-badge">
                                                    <i class="bi bi-stars me-1"></i>
                                                    AI Images Uploaded
                                                </span>
                                              `
                                            : ''
                                    }

                                    ${
                                        imageUrl
                                            ?
                                            `
                                                <img
                                                    src="${escapeHtml(imageUrl)}"
                                                    class="w-100 h-100 specification-image-click"
                                                    data-image="${escapeHtml(imageUrl)}"
                                                    alt="${escapeHtml(productName)}"
                                                    style="
                                                        object-fit:cover;
                                                        cursor:pointer;
                                                    "
                                                >
                                            `
                                            :
                                            `
                                                <div
                                                    class="d-flex align-items-center justify-content-center h-100 text-muted"
                                                >
                                                    <div class="text-center">
                                                        <i
                                                            class="bi bi-image"
                                                            style="font-size:40px;"
                                                        ></i>
                                                        <div class="small mt-2">
                                                            No Image
                                                        </div>
                                                    </div>
                                                </div>
                                            `
                                    }

                                </div>


                                <div class="p-3">

                                    <h6
                                        class="mb-2"
                                        title="${escapeHtml(productName)}"
                                        style="
                                            white-space:nowrap;
                                            overflow:hidden;
                                            text-overflow:ellipsis;
                                        "
                                    >
                                        ${escapeHtml(productName)}
                                    </h6>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            SKU:
                                        </strong>

                                        ${escapeHtml(sku)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Item Type:
                                        </strong>

                                        ${escapeHtml(itemType)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Composition:
                                        </strong>

                                        ${escapeHtml(composition)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Designer:
                                        </strong>

                                        ${escapeHtml(designer)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Gender:
                                        </strong>

                                        ${escapeHtml(gender)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Colour:
                                        </strong>

                                        ${escapeHtml(colour)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Size:
                                        </strong>

                                        ${escapeHtml(size)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Company:
                                        </strong>

                                        ${escapeHtml(companyName)}

                                    </div>


                                    <div class="small text-muted mb-1">

                                        <strong>
                                            Sub Company:
                                        </strong>

                                        ${escapeHtml(subCompanyName)}

                                    </div>


                                    <div class="small text-muted">

                                        <strong>
                                            Project:
                                        </strong>

                                        ${escapeHtml(projectName)}

                                    </div>


                                    <div class="mt-2">

                                        <span
                                            class="badge ${
                                                String(status).toLowerCase() === 'done'
                                                    ? 'bg-success'
                                                    : 'bg-warning text-dark'
                                            }"
                                        >
                                            ${escapeHtml(status)}
                                        </span>

                                    </div>


                                    

                                    <div
                                        class="specification-actions mt-3 d-flex flex-wrap align-items-center gap-2"
                                    >

                                        ${
                                            hasAiImages
                                                ? `
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-2">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        AI Uploaded
                                                    </span>
                                                  `
                                                : ''
                                        }

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary btn-view-spec"
                                            data-id="${escapeHtml(specificationId)}"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            View
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary btn-upload-ai-images"
                                            data-id="${escapeHtml(specificationId)}"
                                            title="Upload AI Images"
                                        >
                                            <i class="bi bi-stars me-1"></i>
                                            Upload AI Images
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-dark btn-download-ai-images"
                                            data-id="${escapeHtml(specificationId)}"
                                            title="Download AI Images"
                                        >
                                            <i class="bi bi-cloud-download me-1"></i>
                                            Download AI Images
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-success btn-download-garment-image"
                                            data-id="${escapeHtml(specificationId)}"
                                            title="Download product image"
                                            ${
                                                imageUrl
                                                    ? ''
                                                    : 'disabled'
                                            }
                                        >
                                            <i class="bi bi-download me-1"></i>
                                            Download
                                        </button>


                                        <label
                                            class="btn btn-sm btn-outline-secondary mb-0 d-inline-flex align-items-center gap-1"
                                            title="Select this product for multiple image download"
                                            style="cursor:pointer;"
                                        >
                                            <input
                                                class="form-check-input garment-image-checkbox m-0"
                                                type="checkbox"
                                                data-id="${escapeHtml(specificationId)}"
                                                ${
                                                    selectedGarments.has(
                                                        String(
                                                            specificationId
                                                        )
                                                    )
                                                        ? 'checked'
                                                        : ''
                                                }
                                            >
                                            <span>Select</span>
                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                    `;

                    const garmentCard =
                        col.querySelector(
                            '.specification-card'
                        );


                    if (garmentCard) {

                        garmentCard.__garmentData =
                            garment;

                    }



                    /*
                    |--------------------------------------------------------------------------
                    | IMAGE CLICK
                    |--------------------------------------------------------------------------
                    */

                    const imageElement =
                        col.querySelector(
                            '.specification-image-click'
                        );


                    if (imageElement) {

                        imageElement.addEventListener(
                            'click',
                            function () {

                                if (
                                    typeof openLargeImage ===
                                    'function'
                                ) {

                                    openLargeImage(
                                        this.dataset.image
                                    );

                                }

                            }
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VIEW BUTTON
                    |--------------------------------------------------------------------------
                    */

                    const viewButton =
                        col.querySelector(
                            '.btn-view-spec'
                        );


                    if (viewButton) {

                        viewButton.addEventListener(
                            'click',
                            function () {

                                openGarmentViewModal(
                                    garment
                                );

                            }
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | APPEND
                    |--------------------------------------------------------------------------
                    */

                    cardsContainer.appendChild(
                        col
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            renderGarmentPagination(
                meta
            );

        }


        /*
        |--------------------------------------------------------------------------
                /*
        |--------------------------------------------------------------------------
        | SELECTED IMAGE DOWNLOAD HELPERS
        |--------------------------------------------------------------------------
        */

        function updateSelectedImageControls() {

            const count = selectedGarments.size;

            if (selectedImageInfo) {
                selectedImageInfo.textContent =
                    count +
                    (
                        count === 1
                            ? ' product selected'
                            : ' products selected'
                    );
            }

            if (downloadSelectedButton) {
                downloadSelectedButton.disabled =
                    count === 0;
            }

            if (clearSelectedButton) {
                clearSelectedButton.disabled =
                    count === 0;
            }
        }


        async function downloadSingleGarmentImage(garment) {

            const images = getGarmentImages(garment);

            if (!images.length) {

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Image',
                        text: 'No image is available for this product.',
                        confirmButtonText: 'OK'
                    });
                }

                return;
            }

            const sku = sanitizeFileName(
                garment.sku ||
                garment.barcode ||
                'garment'
            );

            const rawImage = images[0];
            const imageUrl = getImageUrl(rawImage);

            if (!imageUrl) {
                return;
            }

            const extension = getFileExtension(rawImage);

            try {

                const response = await fetch(
                    imageUrl,
                    {
                        method: 'GET',
                        credentials: 'same-origin'
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'Unable to download image.'
                    );
                }

                const blob = await response.blob();
                const blobUrl = URL.createObjectURL(blob);

                const anchor =
                    document.createElement('a');

                anchor.href = blobUrl;
                anchor.download =
                    sku + '.' + extension;

                document.body.appendChild(anchor);
                anchor.click();
                anchor.remove();

                setTimeout(function () {
                    URL.revokeObjectURL(blobUrl);
                }, 1000);

            } catch (error) {

                console.error(
                    'Single image download error:',
                    error
                );

                /*
                |--------------------------------------------------------------------------
                | FALLBACK
                |--------------------------------------------------------------------------
                */

                const anchor =
                    document.createElement('a');

                anchor.href = imageUrl;
                anchor.download =
                    sku + '.' + extension;

                document.body.appendChild(anchor);
                anchor.click();
                anchor.remove();
            }
        }


        async function downloadSelectedGarmentImages() {

            const garments =
                Array.from(
                    selectedGarments.values()
                );

            if (!garments.length) {
                return;
            }

            if (typeof JSZip === 'undefined') {

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Library Missing',
                        text:
                            'JSZip could not be loaded. Please refresh the page and try again.',
                        confirmButtonText: 'OK'
                    });
                }

                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Preparing Images',
                    text:
                        'Please wait while the selected images are prepared.',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });
            }

            try {

                const zip = new JSZip();
                const usedNames = new Set();

                let downloadedCount = 0;

                for (
                    let productIndex = 0;
                    productIndex < garments.length;
                    productIndex++
                ) {

                    const garment =
                        garments[productIndex];

                    const images =
                        getGarmentImages(garment);

                    if (!images.length) {
                        continue;
                    }

                    const sku =
                        sanitizeFileName(
                            garment.sku ||
                            garment.barcode ||
                            'garment_' +
                            (productIndex + 1)
                        );

                    for (
                        let imageIndex = 0;
                        imageIndex < images.length;
                        imageIndex++
                    ) {

                        const rawImage =
                            images[imageIndex];

                        const imageUrl =
                            getImageUrl(rawImage);

                        if (!imageUrl) {
                            continue;
                        }

                        try {

                            const response =
                                await fetch(
                                    imageUrl,
                                    {
                                        method: 'GET',
                                        credentials: 'same-origin'
                                    }
                                );

                            if (!response.ok) {
                                throw new Error(
                                    'HTTP ' +
                                    response.status
                                );
                            }

                            const blob =
                                await response.blob();

                            const extension =
                                getFileExtension(
                                    rawImage
                                );

                            let fileName =
                                sku +
                                (
                                    images.length > 1
                                        ? '_' +
                                          (imageIndex + 1)
                                        : ''
                                ) +
                                '.' +
                                extension;

                            let baseName =
                                fileName.replace(
                                    /\.[^.]+$/,
                                    ''
                                );

                            let counter = 2;

                            while (
                                usedNames.has(
                                    fileName.toLowerCase()
                                )
                            ) {

                                fileName =
                                    baseName +
                                    '_' +
                                    counter +
                                    '.' +
                                    extension;

                                counter++;
                            }

                            usedNames.add(
                                fileName.toLowerCase()
                            );

                            zip.file(
                                fileName,
                                blob
                            );

                            downloadedCount++;

                        } catch (imageError) {

                            console.warn(
                                'Unable to add image to ZIP:',
                                imageUrl,
                                imageError
                            );
                        }
                    }
                }

                if (!downloadedCount) {
                    throw new Error(
                        'No downloadable images were found for the selected products.'
                    );
                }

                const zipBlob =
                    await zip.generateAsync({
                        type: 'blob'
                    });

                const zipUrl =
                    URL.createObjectURL(zipBlob);

                const anchor =
                    document.createElement('a');

                anchor.href = zipUrl;
                anchor.download =
                    'garment-images.zip';

                document.body.appendChild(anchor);
                anchor.click();
                anchor.remove();

                setTimeout(function () {
                    URL.revokeObjectURL(zipUrl);
                }, 2000);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Download Ready',
                        text:
                            downloadedCount +
                            (
                                downloadedCount === 1
                                    ? ' image'
                                    : ' images'
                            ) +
                            ' added to garment-images.zip.',
                        confirmButtonText: 'OK'
                    });
                }

            } catch (error) {

                console.error(
                    'Selected image download error:',
                    error
                );

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text:
                            error.message ||
                            'Unable to download selected images.',
                        confirmButtonText: 'OK'
                    });
                }
            }
        }


        if (downloadSelectedButton) {

            downloadSelectedButton.addEventListener(
                'click',
                function () {
                    downloadSelectedGarmentImages();
                }
            );

        }


        if (clearSelectedButton) {

            clearSelectedButton.addEventListener(
                'click',
                function () {

                    selectedGarments.clear();

                    document
                        .querySelectorAll(
                            '.garment-image-checkbox'
                        )
                        .forEach(function (checkbox) {
                            checkbox.checked = false;
                        });

                    updateSelectedImageControls();
                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CHECKBOX / DOWNLOAD EVENTS
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'change',
            function (event) {

                const checkbox =
                    event.target.closest(
                        '.garment-image-checkbox'
                    );

                if (!checkbox) {
                    return;
                }

                const id =
                    String(
                        checkbox.dataset.id || ''
                    ).trim();

                if (!id) {
                    return;
                }

                const card =
                    checkbox.closest(
                        '.specification-card'
                    );

                const garment =
                    card
                        ? card.__garmentData
                        : null;

                if (checkbox.checked) {

                    if (garment) {
                        selectedGarments.set(
                            id,
                            garment
                        );
                    }

                } else {

                    selectedGarments.delete(id);

                }

                updateSelectedImageControls();
            }
        );


        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.btn-download-garment-image'
                    );

                if (!button) {
                    return;
                }

                event.preventDefault();

                const card =
                    button.closest(
                        '.specification-card'
                    );

                if (!card) {
                    return;
                }

                const garment =
                    card.__garmentData;

                if (!garment) {
                    return;
                }

                downloadSingleGarmentImage(
                    garment
                );
            }
        );


        /*
        | VIEW GARMENT MODAL
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | LOAD AI UPLOADED IMAGES FOR VIEW MODAL
        |--------------------------------------------------------------------------
        | Uses the same all-garments.ai-images endpoint used by the
        | Upload / Download AI Images functionality.
        */
        async function loadViewModalAiImages(specificationId) {

            const section =
                document.getElementById(
                    'allGarmentViewAIImagesSection'
                );

            const container =
                document.getElementById(
                    'allGarmentViewAIImages'
                );

            const empty =
                document.getElementById(
                    'allGarmentViewAIImagesEmpty'
                );

            const count =
                document.getElementById(
                    'allGarmentViewAIImagesCount'
                );

            if (!section || !container || !empty) {
                return;
            }

            section.style.display = 'none';
            container.innerHTML = '';
            empty.style.display = 'block';

            if (count) {
                count.textContent = '0';
            }

            if (!specificationId) {
                return;
            }

            try {

                const response =
                    await fetch(
                        "{{ route('all-garments.ai-images') }}?specification_id=" +
                        encodeURIComponent(specificationId),
                        {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
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
                        'Unable to load AI images.'
                    );
                }

                const images =
                    Array.isArray(result.images)
                        ? result.images
                        : [];

                if (!images.length) {
                    return;
                }

                section.style.display = '';

                empty.style.display = 'none';

                if (count) {
                    count.textContent = String(images.length);
                }

                images.forEach(
                    function (image, index) {

                        if (
                            !image ||
                            !image.url
                        ) {
                            return;
                        }

                        const col =
                            document.createElement('div');

                        col.className =
                            'col-6';

                        const title =
                            image.type === 'main'
                                ? 'Main AI Image'
                                : 'AI Image ' + (index + 1);

                        col.innerHTML = `
                            <div
                                class="border rounded overflow-hidden bg-light"
                                style="
                                    height:105px;
                                    position:relative;
                                "
                            >
                                <img
                                    src="${escapeHtml(image.url)}"
                                    alt="${escapeHtml(title)}"
                                    title="Click to view"
                                    class="w-100 h-100"
                                    style="
                                        object-fit:cover;
                                        cursor:zoom-in;
                                    "
                                >

                                <span
                                    class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white small px-2 py-1 text-truncate"
                                >
                                    ${escapeHtml(title)}
                                </span>
                            </div>
                        `;

                        container.appendChild(col);

                        const imageElement =
                            col.querySelector('img');

                        if (imageElement) {

                            imageElement.addEventListener(
                                'click',
                                function () {

                                    if (
                                        typeof openLargeImage ===
                                        'function'
                                    ) {
                                        openLargeImage(
                                            this.src
                                        );
                                    }

                                }
                            );

                        }

                    }
                );

                if (!container.children.length) {
                    section.style.display = 'none';
                    empty.style.display = 'block';

                    if (count) {
                        count.textContent = '0';
                    }
                }

            } catch (error) {

                console.error(
                    'VIEW MODAL AI IMAGE ERROR:',
                    error
                );

                section.style.display = 'none';
                container.innerHTML = '';
                empty.style.display = 'block';

                if (count) {
                    count.textContent = '0';
                }

            }
        }


        function openGarmentViewModal(garment) {

            if (!garment) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | SAFE VALUE
            |--------------------------------------------------------------------------
            */

            function displayValue(
                value,
                fallback = '-'
            ) {

                if (
                    value === null ||
                    value === undefined ||
                    String(value).trim() === ''
                ) {

                    return fallback;

                }

                return String(value);

            }


            /*
            |--------------------------------------------------------------------------
            | BASIC INFORMATION
            |--------------------------------------------------------------------------
            */

            setViewField(
                'allGarmentViewSku',
                displayValue(garment.sku)
            );

            setViewField(
                'allGarmentViewBarcode',
                displayValue(garment.barcode)
            );

            setViewField(
                'allGarmentViewItemName',
                displayValue(
                    garment.item_name_text ||
                    garment.productname ||
                    garment.product_name ||
                    garment.name
                )
            );


            /*
            |--------------------------------------------------------------------------
            | DESIGN INFORMATION
            |--------------------------------------------------------------------------
            */

            setViewField(
                'allGarmentViewDesigner',
                displayValue(
                    garment.designer_name_text ||
                    garment.designer_name
                )
            );

            setViewField(
                'allGarmentViewItemType',
                displayValue(
                    garment.item_type_text ||
                    garment.item_type
                )
            );

            setViewField(
                'allGarmentViewGender',
                displayValue(
                    garment.gender_text ||
                    garment.gender
                )
            );

            setViewField(
                'allGarmentViewItemName2',
                displayValue(
                    garment.item_name_text ||
                    garment.productname ||
                    garment.product_name ||
                    garment.name
                )
            );

            setViewField(
                'allGarmentViewComposition',
                displayValue(
                    garment.composition_text ||
                    garment.composition
                )
            );

            setViewField(
                'allGarmentViewColour',
                displayValue(
                    garment.colour_text ||
                    garment.colour
                )
            );

            setViewField(
                'allGarmentViewSize',
                displayValue(
                    garment.size_text ||
                    garment.sizes
                )
            );

            setViewField(
                'allGarmentViewEmbellishment',
                displayValue(
                    garment.embellishment_text ||
                    garment.embellishment
                )
            );

            setViewField(
                'allGarmentViewManufacturingProcess',
                displayValue(
                    garment.manufacturing_process_text ||
                    garment.manufacturing_process
                )
            );

            setViewField(
                'allGarmentViewCraftsman',
                displayValue(
                    garment.craftsman_text ||
                    garment.craftsman
                )
            );

            setViewField(
                'allGarmentViewCraftsmanCode',
                displayValue(
                    garment.craftsman_code
                )
            );

            setViewField(
                'allGarmentViewManufacture',
                displayValue(
                    garment.manufacture_text ||
                    garment.manufecture
                )
            );

            setViewField(
                'allGarmentViewClient',
                displayValue(
                    garment.client_text ||
                    garment.client
                )
            );

            setViewField(
                'allGarmentViewClientReference',
                displayValue(
                    garment.clientreference
                )
            );


            /*
            |--------------------------------------------------------------------------
            | COMPANY CONTEXT
            |--------------------------------------------------------------------------
            */

            setViewField(
                'allGarmentViewCompany',
                displayValue(
                    garment.company_name
                )
            );

            setViewField(
                'allGarmentViewSubCompany',
                displayValue(
                    garment.subcompany_name
                )
            );

            setViewField(
                'allGarmentViewProject',
                displayValue(
                    garment.project_name
                )
            );


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            const status =
                displayValue(
                    garment.status,
                    ''
                );

            const statusElement =
                document.getElementById(
                    'allGarmentViewStatus'
                );

            if (statusElement) {

                statusElement.innerHTML = `

                    <span
                        class="badge ${
                            status.toLowerCase() === 'done'
                                ? 'bg-success'
                                : 'bg-warning text-dark'
                        }"
                    >
                        ${escapeHtml(status)}
                    </span>

                `;

            }


            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            const imageContainer =
                document.getElementById(
                    'allGarmentViewImage'
                );

            if (imageContainer) {

                const garmentImages =
                    getGarmentImages(garment);

                const image =
                    garmentImages.length
                        ? garmentImages[0]
                        : '';

                const imageUrl =
                    getImageUrl(image);


                if (imageUrl) {

                    imageContainer.innerHTML = `

                        <img
                            src="${escapeHtml(imageUrl)}"
                            alt="${escapeHtml(
                                displayValue(
                                    garment.item_name_text ||
                                    garment.productname ||
                                    garment.product_name ||
                                    garment.name,
                                    'Garment'
                                )
                            )}"
                            style="
                                width:100%;
                                height:100%;
                                object-fit:contain;
                                cursor:pointer;
                            "
                            id="allGarmentViewMainImage"
                        >

                    `;


                    const modalImage =
                        document.getElementById(
                            'allGarmentViewMainImage'
                        );


                    if (modalImage) {

                        modalImage.addEventListener(
                            'click',
                            function () {

                                if (
                                    typeof openLargeImage ===
                                    'function'
                                ) {

                                    openLargeImage(
                                        this.src
                                    );

                                }

                            }
                        );

                    }

                }
                else {

                    imageContainer.innerHTML = `

                        <div
                            class="text-center text-muted"
                        >

                            <i
                                class="bi bi-image"
                                style="font-size:50px;"
                            ></i>

                            <div>
                                No Image
                            </div>

                        </div>

                    `;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | AI UPLOADED IMAGES
            |--------------------------------------------------------------------------
            | Load the saved AI images for this garment and show them
            | together with the original product image.
            */
            loadViewModalAiImages(
                garment.sno ||
                garment.id ||
                ''
            );


            /*
            |--------------------------------------------------------------------------
            | AI INFORMATION
            |--------------------------------------------------------------------------
            */

            const aiFields = [

                'AI_product_name',
                'AI_product_description',
                'AI_Metatitle',
                'AI_Metakeywards',
                'AI_Metadescription',
                'AI_Producttag',
                'AI_Imagealttext'

            ];


            let hasAIData = false;


            aiFields.forEach(
                function (field) {

                    if (
                        garment[field] !== null &&
                        garment[field] !== undefined &&
                        String(garment[field]).trim() !== ''
                    ) {

                        hasAIData = true;

                    }

                }
            );


            setViewField(
                'allGarmentViewAIProductName',
                displayValue(
                    garment.AI_product_name
                )
            );

            setViewField(
                'allGarmentViewAIProductDescription',
                displayValue(
                    garment.AI_product_description
                )
            );

            setViewField(
                'allGarmentViewAIMetaTitle',
                displayValue(
                    garment.AI_Metatitle
                )
            );

            setViewField(
                'allGarmentViewAIMetaKeywords',
                displayValue(
                    garment.AI_Metakeywards
                )
            );

            setViewField(
                'allGarmentViewAIMetaDescription',
                displayValue(
                    garment.AI_Metadescription
                )
            );

            setViewField(
                'allGarmentViewAIProductTags',
                displayValue(
                    garment.AI_Producttag
                )
            );

            setViewField(
                'allGarmentViewAIImageAltText',
                displayValue(
                    garment.AI_Imagealttext
                )
            );


            const aiSection =
                document.getElementById(
                    'allGarmentViewAISection'
                );

            if (aiSection) {

                aiSection.style.display =
                    hasAIData
                        ? ''
                        : 'none';

            }


            /*
            |--------------------------------------------------------------------------
            | OPEN MODAL
            |--------------------------------------------------------------------------
            */

            const modalElement =
                document.getElementById(
                    'allGarmentViewModal'
                );

            if (!modalElement) {
                return;
            }


            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );

            modal.show();

        }


        /*
        |--------------------------------------------------------------------------
        | VIEW FIELD HELPER
        |--------------------------------------------------------------------------
        */

        function setViewField(
            id,
            value
        ) {

            const element =
                document.getElementById(id);

            if (element) {

                element.textContent =
                    value;

            }

        }


        function renderGarmentPagination(

            meta

        ) {

            if (!paginationContainer) {

                return;

            }



            paginationContainer.innerHTML =

                '';



            const currentPage =

                Number(

                    meta.current_page || 1

                );



            const lastPage =

                Number(

                    meta.last_page || 1

                );



            if (lastPage <= 1) {

                return;

            }



            /*

            |--------------------------------------------------------------------------

            | CREATE PAGE LIST

            |--------------------------------------------------------------------------

            */

            const pages = [];



            function addPage(page) {

                if (

                    page >= 1 &&

                    page <= lastPage &&

                    !pages.includes(page)

                ) {

                    pages.push(page);

                }

            }



            /*

            |--------------------------------------------------------------------------

            | FIRST PAGE

            |--------------------------------------------------------------------------

            */

            addPage(1);



            /*

            |--------------------------------------------------------------------------

            | CURRENT PAGE AREA

            |--------------------------------------------------------------------------

            */

            for (

                let page = currentPage - 2;

                page <= currentPage + 2;

                page++

            ) {

                addPage(page);

            }



            /*

            |--------------------------------------------------------------------------

            | LAST PAGE

            |--------------------------------------------------------------------------

            */

            addPage(lastPage);



            /*

            |--------------------------------------------------------------------------

            | HTML

            |--------------------------------------------------------------------------

            */

            let html = `

                <div class="d-flex justify-content-center">

                    <nav

                        aria-label="Garment pagination"

                    >

                        <ul

                            class="pagination mb-0"

                        >

            `;



            /*

            |--------------------------------------------------------------------------

            | PREVIOUS

            |--------------------------------------------------------------------------

            */

            html += `

                <li

                    class="page-item ${

                        currentPage <= 1

                            ? 'disabled'

                            : ''

                    }"

                >

                    <button

                        type="button"

                        class="page-link garment-page-btn"

                        data-page="${currentPage - 1}"

                        ${

                            currentPage <= 1

                                ? 'disabled'

                                : ''

                        }

                    >

                        <i class="bi bi-chevron-left"></i>

                        Previous

                    </button>

                </li>

            `;



            /*

            |--------------------------------------------------------------------------

            | PAGE NUMBERS + ELLIPSIS

            |--------------------------------------------------------------------------

            */

            let previousPage = null;



            pages.forEach(

                function (page) {

                    if (

                        previousPage !== null &&

                        page - previousPage > 1

                    ) {

                        html += `

                            <li

                                class="page-item disabled"

                            >

                                <span

                                    class="page-link"

                                >

                                    ...

                                </span>

                            </li>

                        `;

                    }



                    html += `

                        <li

                            class="page-item ${

                                page === currentPage

                                    ? 'active'

                                    : ''

                            }"

                        >

                            <button

                                type="button"

                                class="page-link garment-page-btn"

                                data-page="${page}"

                            >

                                ${page}

                            </button>

                        </li>

                    `;



                    previousPage =

                        page;

                }

            );



            /*

            |--------------------------------------------------------------------------

            | NEXT

            |--------------------------------------------------------------------------

            */

            html += `

                <li

                    class="page-item ${

                        currentPage >= lastPage

                            ? 'disabled'

                            : ''

                    }"

                >

                    <button

                        type="button"

                        class="page-link garment-page-btn"

                        data-page="${currentPage + 1}"

                        ${

                            currentPage >= lastPage

                                ? 'disabled'

                                : ''

                        }

                    >

                        Next

                        <i class="bi bi-chevron-right"></i>

                    </button>

                </li>

            `;



            html += `

                        </ul>

                    </nav>

                </div>

            `;



            paginationContainer.innerHTML =

                html;



            /*

            |--------------------------------------------------------------------------

            | PAGE CLICK

            |--------------------------------------------------------------------------

            */

            paginationContainer

                .querySelectorAll(

                    '.garment-page-btn'

                )

                .forEach(

                    function (button) {

                        button.addEventListener(

                            'click',

                            function () {

                                const page =

                                    Number(

                                        this.dataset.page

                                    );



                                if (

                                    page >= 1 &&

                                    page <= lastPage &&

                                    page !== currentPage

                                ) {

                                    loadGarments(

                                        page

                                    );



                                    /*

                                    |--------------------------------------------------------------------------

                                    | SCROLL TO CARDS

                                    |--------------------------------------------------------------------------

                                    */

                                    if (cardsContainer) {

                                        cardsContainer.scrollIntoView({

                                            behavior: 'smooth',

                                            block: 'start'

                                        });

                                    }

                                }

                            }

                        );

                    }

                );

        }



        /*

        |--------------------------------------------------------------------------

        | APPLY FILTER

        |--------------------------------------------------------------------------

        */

        if (applyButton) {

            applyButton.addEventListener(

                'click',

                function () {

                    loadGarments(

                        1

                    );

                }

            );

        }



        /*

        |--------------------------------------------------------------------------

        | CLEAR FILTER

        |--------------------------------------------------------------------------

        */

        if (clearButton) {

            clearButton.addEventListener(

                'click',

                function () {

                    if (searchInput) {

                        searchInput.value =

                            '';

                    }


                    if (itemTypeSelect) {
                        itemTypeSelect.value = '';
                    }

                    if (itemNameSelect) {
                        itemNameSelect.value = '';
                    }

                    if (compositionSelect) {
                        compositionSelect.value = '';
                    }

                    if (genderSelect) {
                        genderSelect.value = '';
                    }

                    if (aiSentSelect) {
                        aiSentSelect.value = 'all';
                    }


                    if (
                        typeof jQuery !== 'undefined' &&
                        jQuery.fn.select2
                    ) {

                        jQuery(
                            '#garmentItemType, #garmentItemName, #garmentComposition, #garmentGender'
                        )
                        .val('')
                        .trigger('change');

                        jQuery('#garmentAiSent')
                            .val('all')
                            .trigger('change');

                    }



                    loadGarments(

                        1

                    );

                }

            );

        }



        /*

        |--------------------------------------------------------------------------

        | SEARCH

        |--------------------------------------------------------------------------

        */

        if (searchInput) {

            searchInput.addEventListener(

                'keyup',

                function (event) {

                    if (

                        event.key === 'Enter'

                    ) {

                        loadGarments(

                            1

                        );

                    }

                }

            );

        }



        /*

        |--------------------------------------------------------------------------

        | INITIAL LOAD

        |--------------------------------------------------------------------------

        |

        | No project selected.

        |

        | Therefore controller returns ALL garments from ALL projects.

        |

        */

        /*
        |--------------------------------------------------------------------------
        | SELECT2 FILTERS
        |--------------------------------------------------------------------------
        */

        if (
            typeof jQuery !== 'undefined' &&
            jQuery.fn.select2
        ) {

            jQuery(
                '#garmentItemType, #garmentItemName, #garmentComposition, #garmentGender, #garmentAiSent'
            ).select2({
                width: '100%',
                allowClear: true
            });

        }



        /*
        |--------------------------------------------------------------------------
        | AI IMAGE UPLOAD / DOWNLOAD
        |--------------------------------------------------------------------------
        */

        const aiModalEl =
            document.getElementById('uploadAiImagesModal');

        const aiSpecIdEl =
            document.getElementById('aiUploadSpecificationId');

        const aiBarcodeEl =
            document.getElementById('aiUploadBarcodeValue');

        const aiBarcodeText =
            document.getElementById('aiUploadBarcode');

        const aiOriginalBox =
            document.getElementById('aiOriginalImageBox');

        const aiMainInput =
            document.getElementById('aiMainImageInput');

        const aiMainPreview =
            document.getElementById('aiMainImagePreview');

        const aiSubInput =
            document.getElementById('aiSubImagesInput');

        const aiSubPreview =
            document.getElementById('aiSubUploadPreview');

        const aiSubEmpty =
            document.getElementById('aiSubUploadEmpty');

        const aiSubCount =
            document.getElementById('aiSelectedSubCount');

        const aiExisting =
            document.getElementById('aiExistingImages');

        const aiExistingEmpty =
            document.getElementById('aiExistingEmpty');

        const aiExistingCount =
            document.getElementById('aiExistingImageCount');

        const aiStatus =
            document.getElementById('aiUploadStatus');

        const aiSave =
            document.getElementById('btnSaveAiImages');

        let aiModal = null;
        let aiMainFile = null;
        let aiSubFiles = [];


        function getAiModal() {

            if (!aiModalEl) {
                return null;
            }

            if (
                typeof bootstrap === 'undefined' ||
                !bootstrap.Modal
            ) {
                return null;
            }

            if (!aiModal) {
                aiModal =
                    bootstrap.Modal.getOrCreateInstance(
                        aiModalEl
                    );
            }

            return aiModal;
        }


        function clearAiUploadSelections() {

            aiMainFile = null;
            aiSubFiles = [];

            if (aiMainInput) {
                aiMainInput.value = '';
            }

            if (aiSubInput) {
                aiSubInput.value = '';
            }

            renderAiMainPreview();
            renderAiSubPreview();
        }


        function renderAiMainPreview() {

            if (!aiMainPreview) {
                return;
            }

            aiMainPreview.innerHTML = '';

            if (!aiMainFile) {
                return;
            }

            const url =
                URL.createObjectURL(aiMainFile);

            const wrapper =
                document.createElement('div');

            wrapper.className =
                'card border overflow-hidden';

            wrapper.innerHTML = `
                <div class="row g-0 align-items-center">
                    <div class="col-md-5">
                        <div style="height:190px;background:#f1f5f9;overflow:hidden;">
                            <img
                                src="${escapeHtml(url)}"
                                alt="Main AI Image"
                                class="w-100 h-100"
                                style="object-fit:cover;"
                            >
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="p-3">
                            <div class="fw-semibold mb-1">
                                New Main AI Image
                            </div>
                            <div class="small text-muted text-break">
                                ${escapeHtml(aiMainFile.name)}
                            </div>
                            <div class="small text-muted mt-1">
                                ${formatFileSize(aiMainFile.size)}
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger mt-3"
                                id="btnRemoveAiMainPreview"
                            >
                                <i class="bi bi-trash me-1"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;

            aiMainPreview.appendChild(wrapper);
        }


        function renderAiSubPreview() {

            if (!aiSubPreview) {
                return;
            }

            aiSubPreview.innerHTML = '';

            if (aiSubEmpty) {
                aiSubEmpty.style.display =
                    aiSubFiles.length
                        ? 'none'
                        : 'block';
            }

            if (aiSubCount) {
                aiSubCount.textContent =
                    aiSubFiles.length +
                    (
                        aiSubFiles.length === 1
                            ? ' selected'
                            : ' selected'
                    );
            }

            aiSubFiles.forEach(
                function (file, index) {

                    const url =
                        URL.createObjectURL(file);

                    const col =
                        document.createElement('div');

                    col.className =
                        'col-6 col-md-4 col-xl-3';

                    col.innerHTML = `
                        <div class="card border h-100">
                            <div class="ai-preview-image-box">
                                <img
                                    src="${escapeHtml(url)}"
                                    alt="Sub AI Image"
                                >
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-remove-ai-sub-preview"
                                    data-index="${index}"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="card-body p-2">
                                <div class="small fw-semibold text-truncate">
                                    ${escapeHtml(file.name)}
                                </div>
                                <div class="small text-muted">
                                    ${formatFileSize(file.size)}
                                </div>
                            </div>
                        </div>
                    `;

                    aiSubPreview.appendChild(col);
                }
            );
        }


        function formatFileSize(bytes) {

            const size = Number(bytes || 0);

            if (size < 1024) {
                return size + ' B';
            }

            if (size < 1024 * 1024) {
                return (
                    size / 1024
                ).toFixed(1) + ' KB';
            }

            return (
                size / (1024 * 1024)
            ).toFixed(1) + ' MB';
        }


        function renderSavedAiImages(images) {

            if (!aiExisting) {
                return;
            }

            aiExisting.innerHTML = '';

            const list =
                Array.isArray(images)
                    ? images
                    : [];

            if (aiExistingCount) {
                aiExistingCount.textContent =
                    list.length +
                    (
                        list.length === 1
                            ? ' image'
                            : ' images'
                    );
            }

            if (aiExistingEmpty) {
                aiExistingEmpty.style.display =
                    list.length
                        ? 'none'
                        : 'block';
            }

            list.forEach(
                function (image, index) {

                    const col =
                        document.createElement('div');

                    col.className =
                        'col-6 col-md-4 col-lg-3 col-xl-2';

                    const title =
                        image.type === 'main'
                            ? 'Main AI Image'
                            : 'Sub AI Image ' + index;

                    col.innerHTML = `
                        <div class="card border h-100 shadow-sm">
                            <div class="ai-saved-image-box">
                                <img
                                    src="${escapeHtml(image.url)}"
                                    alt="${escapeHtml(title)}"
                                    title="Click to view"
                                >
                            </div>
                            <div class="card-body p-2">
                                <div class="small fw-semibold">
                                    ${escapeHtml(title)}
                                </div>
                                <div class="small text-muted text-truncate">
                                    ${escapeHtml(image.filename || '')}
                                </div>
                            </div>
                        </div>
                    `;

                    aiExisting.appendChild(col);

                    const imageEl =
                        col.querySelector('img');

                    if (imageEl) {
                        imageEl.addEventListener(
                            'click',
                            function () {
                                if (
                                    typeof openLargeImage ===
                                    'function'
                                ) {
                                    openLargeImage(
                                        this.src
                                    );
                                }
                            }
                        );
                    }
                }
            );
        }


        async function getSavedAiImages(specificationId) {

            const response =
                await fetch(
                    "{{ route('all-garments.ai-images') }}?specification_id=" +
                    encodeURIComponent(specificationId),
                    {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
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
                    'Unable to load AI images.'
                );
            }

            renderSavedAiImages(
                result.images || []
            );

            return result.images || [];
        }


        function openAiImages(garment) {

            const specificationId =
                garment &&
                (
                    garment.sno ||
                    garment.id
                );

            const barcode =
                garment &&
                garment.barcode;

            if (!specificationId || !barcode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Product Information Missing',
                    text: 'Specification ID or barcode is missing.'
                });
                return;
            }

            if (!getAiModal()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Bootstrap Error',
                    text: 'Bootstrap JavaScript is not loaded.'
                });
                return;
            }

            aiSpecIdEl.value = specificationId;
            aiBarcodeEl.value = barcode;
            aiBarcodeText.textContent = barcode;
            aiStatus.textContent = '';

            clearAiUploadSelections();

            const originalImages =
                getGarmentImages(garment);

            const originalUrl =
                originalImages.length
                    ? getImageUrl(originalImages[0])
                    : '';

            if (aiOriginalBox) {
                aiOriginalBox.innerHTML =
                    originalUrl
                        ? `
                            <img
                                src="${escapeHtml(originalUrl)}"
                                alt="Original Product Image"
                                class="img-fluid rounded"
                            >
                          `
                        : `
                            <div class="text-center text-muted">
                                <i class="bi bi-image fs-1"></i>
                                <div class="mt-2">
                                    No Original Image
                                </div>
                            </div>
                          `;

                const originalImage =
                    aiOriginalBox.querySelector('img');

                if (originalImage) {
                    originalImage.addEventListener(
                        'click',
                        function () {
                            if (
                                typeof openLargeImage ===
                                'function'
                            ) {
                                openLargeImage(
                                    this.src
                                );
                            }
                        }
                    );
                }
            }

            getAiModal().show();

            getSavedAiImages(
                specificationId
            ).catch(
                function (error) {
                    console.error(error);
                    aiStatus.textContent =
                        error.message;
                }
            );
        }


        document.addEventListener(
            'click',
            function (event) {

                const removeMain =
                    event.target.closest(
                        '#btnRemoveAiMainPreview'
                    );

                if (removeMain) {
                    aiMainFile = null;

                    if (aiMainInput) {
                        aiMainInput.value = '';
                    }

                    renderAiMainPreview();
                    return;
                }

                const removeSub =
                    event.target.closest(
                        '.btn-remove-ai-sub-preview'
                    );

                if (removeSub) {
                    const index = Number(
                        removeSub.dataset.index
                    );

                    if (!Number.isNaN(index)) {
                        aiSubFiles.splice(
                            index,
                            1
                        );
                    }

                    renderAiSubPreview();
                    return;
                }

                const uploadButton =
                    event.target.closest(
                        '.btn-upload-ai-images'
                    );

                if (uploadButton) {
                    event.preventDefault();

                    const card =
                        uploadButton.closest(
                            '.specification-card'
                        );

                    openAiImages(
                        card &&
                        card.__garmentData
                    );

                    return;
                }

                const downloadButton =
                    event.target.closest(
                        '.btn-download-ai-images'
                    );

                if (downloadButton) {
                    event.preventDefault();

                    const card =
                        downloadButton.closest(
                            '.specification-card'
                        );

                    downloadAiImages(
                        card &&
                        card.__garmentData
                    );
                }
            }
        );


        if (aiMainInput) {
            aiMainInput.addEventListener(
                'change',
                function () {

                    const file =
                        this.files &&
                        this.files[0];

                    if (!file) {
                        return;
                    }

                    if (!isValidAiImage(file)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Image',
                            text: 'Please select JPG, JPEG, PNG, WEBP or HEIC image up to 10 MB.'
                        });

                        this.value = '';
                        return;
                    }

                    aiMainFile = file;
                    renderAiMainPreview();
                }
            );
        }


        if (aiSubInput) {
            aiSubInput.addEventListener(
                'change',
                function () {

                    const selected =
                        Array.from(
                            this.files || []
                        );

                    selected.forEach(
                        function (file) {
                            if (
                                isValidAiImage(file)
                            ) {
                                aiSubFiles.push(file);
                            }
                        }
                    );

                    this.value = '';
                    renderAiSubPreview();
                }
            );
        }


        function isValidAiImage(file) {

            if (!file) {
                return false;
            }

            const extension =
                String(
                    file.name || ''
                )
                .split('.')
                .pop()
                .toLowerCase();

            const allowed = [
                'jpg',
                'jpeg',
                'png',
                'webp',
                'heic'
            ];

            return (
                allowed.includes(extension) &&
                Number(file.size || 0) <=
                    10 * 1024 * 1024
            );
        }


        if (aiSave) {
            aiSave.addEventListener(
                'click',
                async function () {

                    if (
                        !aiMainFile &&
                        !aiSubFiles.length
                    ) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select Images',
                            text: 'Please select a Main AI Image or at least one Sub AI Image.'
                        });
                        return;
                    }

                    const formData =
                        new FormData();

                    formData.append(
                        'specification_id',
                        aiSpecIdEl.value
                    );

                    formData.append(
                        'barcode',
                        aiBarcodeEl.value
                    );

                    if (aiMainFile) {
                        formData.append(
                            'ai_main_image',
                            aiMainFile
                        );
                    }

                    aiSubFiles.forEach(
                        function (file) {
                            formData.append(
                                'ai_sub_images[]',
                                file
                            );
                        }
                    );

                    const oldHtml =
                        this.innerHTML;

                    this.disabled = true;
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                    aiStatus.textContent =
                        'Uploading AI images...';

                    try {

                        const response =
                            await fetch(
                                "{{ route('all-garments.ai-images.save') }}",
                                {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN':
                                            "{{ csrf_token() }}",
                                        'X-Requested-With':
                                            'XMLHttpRequest',
                                        'Accept':
                                            'application/json'
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
                                'Unable to save AI images.'
                            );
                        }

                        aiMainFile = null;
                        aiSubFiles = [];

                        if (aiMainInput) {
                            aiMainInput.value = '';
                        }

                        if (aiSubInput) {
                            aiSubInput.value = '';
                        }

                        renderAiMainPreview();
                        renderAiSubPreview();
                        renderSavedAiImages(
                            result.images || []
                        );

                        aiStatus.textContent =
                            result.message ||
                            'AI images saved successfully.';

                        Swal.fire({
                            icon: 'success',
                            title: 'Saved',
                            text:
                                result.message ||
                                'AI images saved successfully.'
                        });

                    } catch (error) {

                        console.error(
                            'AI IMAGE SAVE ERROR:',
                            error
                        );

                        aiStatus.textContent =
                            error.message;

                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: error.message
                        });

                    } finally {

                        this.disabled = false;
                        this.innerHTML = oldHtml;
                    }
                }
            );
        }


        async function downloadAiImages(garment) {

            const specificationId =
                garment &&
                (
                    garment.sno ||
                    garment.id
                );

            const barcode =
                garment &&
                (
                    garment.barcode ||
                    garment.sku
                ) ||
                'AI-Images';

            if (!specificationId) {
                return;
            }

            if (typeof JSZip === 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Download Library Missing',
                    text: 'JSZip could not be loaded.'
                });
                return;
            }

            try {
                Swal.fire({
                    title: 'Preparing AI Images',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

                const response =
                    await fetch(
                        "{{ route('all-garments.ai-images') }}?specification_id=" +
                        encodeURIComponent(specificationId),
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
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
                        'Unable to load AI images.'
                    );
                }

                if (
                    !result.images ||
                    !result.images.length
                ) {
                    throw new Error(
                        'No AI images have been uploaded for this product.'
                    );
                }

                const zip = new JSZip();
                let count = 0;
                const usedNames = new Set();

                for (
                    let i = 0;
                    i < result.images.length;
                    i++
                ) {
                    const image =
                        result.images[i];

                    try {
                        const imageResponse =
                            await fetch(
                                image.url,
                                {
                                    credentials:
                                        'same-origin'
                                }
                            );

                        if (!imageResponse.ok) {
                            continue;
                        }

                        const blob =
                            await imageResponse.blob();

                        let extension =
                            String(
                                image.filename ||
                                'jpg'
                            )
                            .split('.')
                            .pop()
                            .toLowerCase();

                        if (extension.length > 5) {
                            extension = 'jpg';
                        }

                        let name =
                            sanitizeFileName(
                                barcode
                            ) +
                            '_' +
                            (
                                i + 1
                            ) +
                            '.' +
                            extension;

                        const base =
                            name.replace(
                                /\.[^.]+$/,
                                ''
                            );

                        let number = 2;

                        while (
                            usedNames.has(
                                name.toLowerCase()
                            )
                        ) {
                            name =
                                base +
                                '_' +
                                number +
                                '.' +
                                extension;

                            number++;
                        }

                        usedNames.add(
                            name.toLowerCase()
                        );

                        zip.file(
                            name,
                            blob
                        );

                        count++;

                    } catch (error) {
                        console.warn(
                            'AI IMAGE DOWNLOAD ERROR:',
                            error
                        );
                    }
                }

                if (!count) {
                    throw new Error(
                        'No AI image files could be downloaded.'
                    );
                }

                const blob =
                    await zip.generateAsync({
                        type: 'blob'
                    });

                const url =
                    URL.createObjectURL(blob);

                const anchor =
                    document.createElement('a');

                anchor.href = url;
                anchor.download =
                    sanitizeFileName(
                        barcode
                    ) +
                    '-AI-Images.zip';

                document.body.appendChild(
                    anchor
                );

                anchor.click();
                anchor.remove();

                setTimeout(
                    function () {
                        URL.revokeObjectURL(url);
                    },
                    2000
                );

                Swal.fire({
                    icon: 'success',
                    title: 'Download Ready',
                    text:
                        count +
                        ' AI images downloaded.'
                });

            } catch (error) {

                Swal.fire({
                    icon: 'warning',
                    title: 'No AI Images',
                    text: error.message
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INITIAL LOAD
        |--------------------------------------------------------------------------
        */

        updateSelectedImageControls();


        loadGarments(

            1

        );

    }

);

</script>

@endpush
