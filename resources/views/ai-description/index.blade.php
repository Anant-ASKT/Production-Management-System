@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                <i class="bi bi-stars me-2"></i>
                Make AI Description
            </h4>

            <div class="text-muted">
                Create AI descriptions for your garments
            </div>

        </div>

    </div>


    {{-- SEARCH --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>

                <input
                    type="text"
                    id="aiDescriptionSearch"
                    class="form-control"
                    placeholder="Search by product, SKU or barcode..."
                >

            </div>

        </div>

    </div>


    {{-- TABLE --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-hover align-middle mb-0"
                >

                    <thead class="table-light">

                        <tr>

                        <th style="width:130px;">
                            Main Image
                        </th>

                        <th style="width:130px;">
                            AI Approved Image
                        </th>

                        <th style="width:110px;">
                            Select
                        </th>

                        <th>
                            Product Name
                        </th>

                        <th>
                            Product Type
                        </th>

                        <th>
                            SKU
                        </th>

                        <th>
                            QR Code
                        </th>

                    </tr>

                    </thead>


                    <tbody id="aiDescriptionTableBody">

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5 text-muted"
                            >

                                Loading...

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- PAGINATION --}}

    <div
        id="aiDescriptionPagination"
        class="mt-4"
    ></div>

</div>

{{-- =========================================================
     APPROVED AI IMAGES MODAL
========================================================= --}}
{{-- =========================================================
     APPROVED AI IMAGES / PRODUCT DETAILS MODAL
========================================================= --}}

<div
    class="modal fade"
    id="approvedImagesModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content border-0 shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header">

                <div>
                    <h5 class="modal-title mb-1">
                        <i class="bi bi-stars me-2"></i>
                        AI Approved Product
                    </h5>

                    <small class="text-muted">
                        AI approved image and product details
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

    {{-- =========================================================
         LOADING
    ========================================================== --}}

    <div
        id="approvedImagesLoading"
        class="text-center py-5"
    >

        <div class="spinner-border text-primary"></div>

        <div class="mt-2 text-muted">
            Loading product...
        </div>

    </div>


    {{-- =========================================================
         CONTENT
    ========================================================== --}}

    <div
        id="approvedImagesContent"
        style="display:none;"
    >


        {{-- =====================================================
             IMAGES - SIDE BY SIDE
        ====================================================== --}}

        <div class="row g-3 mb-3">

            {{-- AI APPROVED MAIN IMAGE --}}

            <div class="col-lg-6">

                <div class="ai-image-card">

                    <div class="ai-image-card-header">

                        <i class="bi bi-stars me-1"></i>

                        AI Approved Main Image

                        <span class="badge bg-success float-end">
                            <i class="bi bi-check-circle me-1"></i>
                            Approved
                        </span>

                    </div>


                    <div class="ai-main-image-wrapper">

                        <img
                            id="approvedMainImage"
                            src=""
                            alt="AI Approved Main Image"
                            style="display:none;"
                        >


                        <div
                            id="approvedMainImageEmpty"
                            class="text-muted text-center"
                        >

                            <i
                                class="bi bi-stars"
                                style="font-size:35px;"
                            ></i>

                            <div class="mt-2">
                                No approved main image
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ORIGINAL MAIN IMAGE --}}

            <div class="col-lg-6">

                <div class="ai-image-card">

                    <div class="ai-image-card-header">

                        <i class="bi bi-image me-1"></i>

                        Original Main Image

                    </div>


                    <div class="ai-main-image-wrapper">

                        <img
                            id="originalMainImage"
                            src=""
                            alt="Original Main Image"
                            style="display:none;"
                        >


                        <div
                            id="originalMainImageEmpty"
                            class="text-muted text-center"
                        >

                            <i
                                class="bi bi-image"
                                style="font-size:35px;"
                            ></i>

                            <div class="mt-2">
                                No original main image
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
             APPROVED SUB IMAGES
        ====================================================== --}}

        <div
            class="ai-sub-images-card mb-3"
            id="approvedSubImagesSection"
        >

            <div class="ai-sub-images-header">

                <i class="bi bi-images me-1"></i>

                Approved Sub Images

                <span
                    class="badge bg-success float-end"
                >
                    AI Approved
                </span>

            </div>


            <div class="ai-sub-images-body">

                <div
                    id="approvedImagesGrid"
                    class="row g-2"
                ></div>


                <div
                    id="approvedSubImagesEmpty"
                    class="text-center text-muted py-3"
                    style="display:none;"
                >

                    <i
                        class="bi bi-images"
                        style="font-size:28px;"
                    ></i>

                    <div class="mt-1">
                        No approved sub images found.
                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
             PRODUCT DETAILS
        ====================================================== --}}

        <div class="ai-product-details-card mb-3">

            <div class="ai-section-header">

                <i class="bi bi-info-circle me-1"></i>

                Product Details

            </div>


            <div class="ai-product-details-body">

                <div class="row g-2">


                    {{-- PRODUCT NAME --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Product Name
                            </span>

                            <span
                                id="modalProductName"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- PRODUCT TYPE --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Product Type
                            </span>

                            <span
                                id="modalProductType"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- SKU --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                SKU
                            </span>

                            <span
                                id="modalProductSku"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- SUPPLIER SKU --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Supplier SKU
                            </span>

                            <span
                                id="modalSupplierSku"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- BARCODE --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Barcode
                            </span>

                            <span
                                id="modalProductBarcode"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- GENDER --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Gender
                            </span>

                            <span
                                id="modalProductGender"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- COMPOSITION --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Composition
                            </span>

                            <span
                                id="modalProductComposition"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- COLOUR --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Colour
                            </span>

                            <span
                                id="modalProductColour"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>


                    {{-- SIZE --}}

                    <div class="col-md-4">

                        <div class="ai-detail-item">

                            <span class="ai-detail-label">
                                Size
                            </span>

                            <span
                                id="modalProductSize"
                                class="ai-detail-value"
                            >
                                -
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- =====================================================
             AI GENERATED PRODUCT CONTENT
        ====================================================== --}}

        <div class="ai-content-card">

            {{-- HEADER --}}

            <div class="ai-content-header">

                <div class="ai-content-title">

                    <div class="ai-icon">

                        <i class="bi bi-stars"></i>

                    </div>


                    <div>

                        <h6>
                            AI Generated Product Content
                        </h6>

                        <small>
                            Content is automatically generated
                            from the selected design image.
                        </small>

                    </div>

                </div>


                <div class="ai-status">

                    <span class="ai-status-dot"></span>

                    AI Ready

                </div>

            </div>



            {{-- BODY --}}

            <div class="ai-content-body">


                {{-- PRODUCT NAME --}}

                <div class="ai-field ai-field-full">

                    <label for="txt_productName">

                        <i class="bi bi-type"></i>

                        Product Name

                    </label>


                    <input
                        type="text"
                        id="txt_productName"
                        class="form-control"
                        placeholder="AI generated product name"
                    >

                </div>



                {{-- PRODUCT DESCRIPTION --}}

                <div class="ai-field ai-field-full">

                    <label for="txt_productDescription">

                        <i class="bi bi-card-text"></i>

                        Product Description

                    </label>


                    <textarea
                        id="txt_productDescription"
                        class="form-control"
                        rows="3"
                        placeholder="AI generated product description"
                    ></textarea>

                </div>



                {{-- META TITLE --}}

                <div class="ai-field">

                    <label for="txt_metaTitle">

                        <i class="bi bi-heading"></i>

                        Meta Title

                    </label>


                    <input
                        type="text"
                        id="txt_metaTitle"
                        class="form-control"
                        placeholder="AI generated meta title"
                    >

                </div>



                {{-- META KEYWORDS --}}

                <div class="ai-field">

                    <label for="txt_metaKeywords">

                        <i class="bi bi-tags"></i>

                        Meta Keywords

                    </label>


                    <input
                        type="text"
                        id="txt_metaKeywords"
                        class="form-control"
                        placeholder="AI generated keywords"
                    >

                </div>



                {{-- META DESCRIPTION --}}

                <div class="ai-field ai-field-full">

                    <label for="txt_metaDescription">

                        <i class="bi bi-file-text"></i>

                        Meta Description

                    </label>


                    <textarea
                        id="txt_metaDescription"
                        class="form-control"
                        rows="2"
                        placeholder="AI generated meta description"
                    ></textarea>

                </div>



                {{-- PRODUCT TAGS --}}

                <div class="ai-field ai-field-full">

                    <label for="txt_productTags">

                        <i class="bi bi-bookmark"></i>

                        Product Tags

                    </label>


                    <input
                        type="text"
                        id="txt_productTags"
                        class="form-control"
                        placeholder="AI generated product tags"
                    >

                </div>



                {{-- IMAGE ALT TEXT --}}

                <div class="ai-field ai-field-full">

                    <label for="txt_image_alt_text">

                        <i class="bi bi-image-alt"></i>

                        Image Alt Text

                    </label>


                    <input
                        type="text"
                        id="txt_image_alt_text"
                        class="form-control"
                        placeholder="AI generated image alt text"
                    >

                </div>

            </div>



            {{-- ACTION --}}

            <div class="ai-description-action d-flex justify-content-end gap-2">

                <button
                    type="button"
                    id="btnSaveAiDescription"
                    class="btn btn-success"
                >

                    <i class="bi bi-save me-2"></i>

                    Save AI Description

                </button>


                <button
                    type="button"
                    id="btnGetAiDescription"
                    class="btn btn-primary"
                >

                    <i class="bi bi-stars me-2"></i>

                    Get AI Description

                </button>

            </div>

        </div>


    </div>



    {{-- =========================================================
         EMPTY
    ========================================================== --}}

    <div
        id="approvedImagesEmpty"
        class="text-center text-muted py-5"
        style="display:none;"
    >

        <i
            class="bi bi-images"
            style="font-size:40px;"
        ></i>

        <div class="mt-2">
            No approved AI images found.
        </div>

    </div>



    {{-- =========================================================
         ERROR
    ========================================================== --}}

    <div
        id="approvedImagesError"
        class="alert alert-danger"
        style="display:none;"
    ></div>

</div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >

                    <i class="bi bi-x-lg me-1"></i>

                    Close

                </button>

            </div>

        </div>

    </div>
</div>

<style>
    /* =========================================================
       AI DESCRIPTION MODAL
    ========================================================= */

    #approvedImagesModal .modal-dialog {
        max-width: 1400px;
        width: calc(100% - 30px);
        margin: 15px auto;
    }

    #approvedImagesModal .modal-content {
        height: calc(100vh - 30px);
        border-radius: 14px;
        overflow: hidden;
    }

    #approvedImagesModal .modal-header {
        padding: 14px 20px;
        min-height: 72px;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    #approvedImagesModal .modal-body {
        padding: 16px;
        overflow-y: auto;
        background: #f7f8fa;
    }

    #approvedImagesModal .modal-footer {
        padding: 10px 16px;
        border-top: 1px solid #e5e7eb;
        flex-shrink: 0;
    }


    /* =========================================================
       IMAGE AREA
    ========================================================= */

    .ai-image-card {
        background: #fff;
        border: 1px solid #e1e5ea;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }

    .ai-image-card-header {
        padding: 9px 13px;
        border-bottom: 1px solid #e5e7eb;
        background: #fafbfc;
        font-weight: 600;
        font-size: 14px;
    }

    .ai-main-image-wrapper {
        height: 285px;
        background: #f4f6f8;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }

    .ai-main-image-wrapper img {
        max-width: 100%;
        max-height: 265px;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
    }


    /* =========================================================
       SUB IMAGES
    ========================================================= */

    .ai-sub-images-card {
        background: #fff;
        border: 1px solid #e1e5ea;
        border-radius: 10px;
        overflow: hidden;
    }

    .ai-sub-images-header {
        padding: 9px 13px;
        border-bottom: 1px solid #e5e7eb;
        background: #fafbfc;
        font-weight: 600;
        font-size: 14px;
    }

    .ai-sub-images-body {
        padding: 10px;
    }

    .ai-sub-image-item {
        height: 110px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 5px;
        cursor: pointer;
        transition: .2s;
    }

    .ai-sub-image-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }

    .ai-sub-image-item img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 6px;
    }


    /* =========================================================
       PRODUCT DETAILS
    ========================================================= */

    .ai-product-details-card {
        background: #fff;
        border: 1px solid #e1e5ea;
        border-radius: 10px;
        overflow: hidden;
    }

    .ai-section-header {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        background: #fafbfc;
        font-weight: 600;
        font-size: 14px;
    }

    .ai-product-details-body {
        padding: 14px;
    }

    .ai-detail-item {
        padding: 7px 4px;
    }

    .ai-detail-label {
        display: block;
        color: #6b7280;
        font-size: 12px;
        margin-bottom: 3px;
    }

    .ai-detail-value {
        display: block;
        color: #1f2937;
        font-size: 14px;
        font-weight: 600;
        word-break: break-word;
    }


    /* =========================================================
       AI GENERATED CONTENT
    ========================================================= */

    .ai-content-card {
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 10px;
        overflow: hidden;
    }

    .ai-content-header {
        padding: 12px 15px;
        background: linear-gradient(
            135deg,
            #f8f9ff,
            #ffffff
        );
        border-bottom: 1px solid #e5e7eb;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .ai-content-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ai-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: #eef2ff;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #4f46e5;
        font-size: 18px;
        flex-shrink: 0;
    }

    .ai-content-title h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
    }

    .ai-content-title small {
        color: #6b7280;
        font-size: 11px;
    }

    .ai-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 5px 9px;
        border-radius: 20px;

        background: #ecfdf3;
        color: #15803d;

        font-size: 12px;
        font-weight: 600;

        white-space: nowrap;
    }

    .ai-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #16a34a;
    }

    .ai-content-body {
        padding: 15px;

        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .ai-field {
        min-width: 0;
    }

    .ai-field-full {
        grid-column: 1 / -1;
    }

    .ai-field label {
        display: flex;
        align-items: center;
        gap: 6px;

        font-size: 12px;
        font-weight: 600;
        color: #374151;

        margin-bottom: 5px;
    }

    .ai-field label i {
        color: #4f46e5;
    }

    .ai-field .form-control {
        border-color: #d9dee5;
        font-size: 13px;
        min-height: 38px;
    }

    .ai-field textarea.form-control {
        resize: vertical;
    }


    /* =========================================================
       AI BUTTON
    ========================================================= */

    .ai-description-action {
        padding: 14px 15px;
        border-top: 1px solid #e5e7eb;
        background: #fafbfc;
        text-align: right;
    }

    #btnGetAiDescription {
        min-width: 180px;
    }


    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 991.98px) {

        #approvedImagesModal .modal-dialog {
            width: calc(100% - 10px);
            margin: 5px auto;
        }

        #approvedImagesModal .modal-content {
            height: calc(100vh - 10px);
        }

        .ai-main-image-wrapper {
            height: 240px;
        }

        .ai-main-image-wrapper img {
            max-height: 220px;
        }

        .ai-content-body {
            grid-template-columns: 1fr;
        }

        .ai-field-full {
            grid-column: auto;
        }
    }


    @media (max-width: 575.98px) {

        #approvedImagesModal .modal-header {
            padding: 11px 13px;
        }

        #approvedImagesModal .modal-body {
            padding: 10px;
        }

        .ai-main-image-wrapper {
            height: 210px;
        }

        .ai-main-image-wrapper img {
            max-height: 190px;
        }

        .ai-content-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .ai-status {
            align-self: flex-start;
        }

        .ai-content-body {
            padding: 11px;
            gap: 11px;
        }
    }
    /*
|--------------------------------------------------------------------------
| AI DESCRIPTION SAVED PRODUCT
|--------------------------------------------------------------------------
*/

.ai-description-saved-row {
    background-color: #e8f7ee !important;
    border-left: 4px solid #198754;
}


.ai-description-saved-row td {
    background-color: #e8f7ee !important;
}


.ai-description-saved-row:hover td {
    background-color: #dff3e7 !important;
}
</style>


<script>

/*
|--------------------------------------------------------------------------
| IMAGE URL
|--------------------------------------------------------------------------
|
| Handles:
| 1. Full URL
| 2. ../../old/path
| 3. /path
| 4. JSON image array
|
*/
window.aiDescriptionProductCache = {};
let selectedAiProduct = null;

function getImageUrl(path) {

    if (
        path === null ||
        path === undefined ||
        path === '' ||
        path === 'null' ||
        path === 'undefined'
    ) {
        return '';
    }

    path = String(path).trim();


    // Full URL

    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('data:')
    ) {
        return path;
    }


    // JSON image array

    if (
        path.startsWith('[') &&
        path.endsWith(']')
    ) {

        try {

            const images =
                JSON.parse(path);

            if (
                Array.isArray(images) &&
                images.length > 0
            ) {

                path =
                    String(images[0]).trim();

            } else {

                return '';
            }

        } catch (error) {

            console.warn(
                'Invalid image JSON:',
                path
            );

            return '';
        }
    }


    // Remove ../../../../ prefix

    path = path.replace(
        /^(\.\.\/)+/,
        ''
    );


    // Remove leading /

    path = path.replace(
        /^\/+/,
        ''
    );


    // Return public URL

    return '/' + path;
}

/*
|--------------------------------------------------------------------------
| ESCAPE HTML
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
        .replace(
            /&/g,
            '&amp;'
        )
        .replace(
            /</g,
            '&lt;'
        )
        .replace(
            />/g,
            '&gt;'
        )
        .replace(
            /"/g,
            '&quot;'
        )
        .replace(
            /'/g,
            '&#039;'
        );
}


/*
|--------------------------------------------------------------------------
| PAGE INITIALIZATION
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const tableBody =
            document.getElementById(
                'aiDescriptionTableBody'
            );

        const searchInput =
            document.getElementById(
                'aiDescriptionSearch'
            );

        const pagination =
            document.getElementById(
                'aiDescriptionPagination'
            );

        let currentPage = 1;


        /*
        |--------------------------------------------------------------------------
        | CHECK ELEMENTS
        |--------------------------------------------------------------------------
        */

        if (!tableBody) {

            console.error(
                'aiDescriptionTableBody not found.'
            );

            return;
        }

        if (!searchInput) {

            console.error(
                'aiDescriptionSearch not found.'
            );

            return;
        }

        if (!pagination) {

            console.error(
                'aiDescriptionPagination not found.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        async function loadAiDescriptionData(
            page = 1
        ) {

            /*
            |--------------------------------------------------------------------------
            | LOADING
            |--------------------------------------------------------------------------
            */

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="text-center py-5"
                    >

                        <div
                            class="spinner-border spinner-border-sm me-2"
                        ></div>

                        Loading...

                    </td>
                </tr>
            `;


            try {

                /*
                |--------------------------------------------------------------------------
                | SEARCH
                |--------------------------------------------------------------------------
                */

                const search =
                    searchInput.value.trim();


                /*
                |--------------------------------------------------------------------------
                | PARAMETERS
                |--------------------------------------------------------------------------
                */

                const params =
                    new URLSearchParams({

                        page: page,

                        per_page: 20,

                        search: search

                    });


                /*
                |--------------------------------------------------------------------------
                | REQUEST
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        `{{ route('ai-description.data') }}?${params.toString()}`,
                        {
                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'

                            }
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | RESPONSE
                |--------------------------------------------------------------------------
                */

                const result =
                    await response.json();


                /*
                |--------------------------------------------------------------------------
                | ERROR
                |--------------------------------------------------------------------------
                */

                if (
                    !response.ok ||
                    !result.success
                ) {

                    throw new Error(
                        result.message ||
                        'Unable to load products.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | PAGE
                |--------------------------------------------------------------------------
                */

                currentPage =
                    result.current_page;


                /*
                |--------------------------------------------------------------------------
                | RENDER
                |--------------------------------------------------------------------------
                */

                renderTable(
                    result.data || []
                );


                /*
                |--------------------------------------------------------------------------
                | PAGINATION
                |--------------------------------------------------------------------------
                */

                renderPagination(
                    result
                );


            } catch (error) {

                console.error(
                    'AI Description Error:',
                    error
                );


                tableBody.innerHTML = `
                    <tr>
                        <td
                            colspan="7"
                            class="text-center text-danger py-5"
                        >

                            ${escapeHtml(
                                error.message
                            )}

                        </td>
                    </tr>
                `;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RENDER TABLE
        |--------------------------------------------------------------------------
        */

        function renderTable(products)
{
    tableBody.innerHTML = '';

    /*
    |--------------------------------------------------------------------------
    | STORE CURRENT PAGE PRODUCTS
    |--------------------------------------------------------------------------
    */

    window.aiDescriptionProductCache = {};

    products.forEach(function (product) {

        if (product && product.sno !== undefined) {

            window.aiDescriptionProductCache[
                String(product.sno)
            ] = product;

        }

    });


    if (!products.length) {

        tableBody.innerHTML = `
            <tr>

                <td
                    colspan="6"
                    class="text-center text-muted py-5"
                >

                    <i
                        class="bi bi-inbox"
                        style="font-size:35px;"
                    ></i>

                    <div class="mt-2">
                        No products found.
                    </div>

                </td>

            </tr>
        `;

        return;
    }


    products.forEach(function (product) {

        /*
        |--------------------------------------------------------------------------
        | YOUR EXISTING CODE CONTINUES HERE
        |--------------------------------------------------------------------------
        */

        const mainImage =
            getImageUrl(
                product.img_path
            );

        const aiImage =
            getImageUrl(
                product.ai_approved_image
            );

        // ... keep the rest of your existing render code

        /*
    |--------------------------------------------------------------------------
    | CREATE TABLE ROW
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| CREATE TABLE ROW
|--------------------------------------------------------------------------
*/

const row =
    document.createElement('tr');


/*
|--------------------------------------------------------------------------
| ROW IDENTIFIER
|--------------------------------------------------------------------------
*/

row.dataset.specificationId =
    product.sno;


/*
|--------------------------------------------------------------------------
| AI DESCRIPTION SAVED?
|--------------------------------------------------------------------------
*/

const hasAiDescription =
    Number(
        product.has_ai_description
    ) === 1;


/*
|--------------------------------------------------------------------------
| HIGHLIGHT SAVED PRODUCT
|--------------------------------------------------------------------------
*/

if (hasAiDescription) {

    row.classList.add(
        'ai-description-saved-row'
    );

}


/*
|--------------------------------------------------------------------------
| SELECT BUTTON
|--------------------------------------------------------------------------
*/

let selectButton = '';


if (aiImage) {

    if (hasAiDescription) {

        selectButton = `

            <button
                type="button"
                class="btn btn-sm btn-success btn-select-ai-product"
                data-specification-id="${escapeHtml(
                    product.sno
                )}"
            >

                <i class="bi bi-check-circle me-1"></i>

                AI Description Saved

            </button>

        `;

    } else {

        selectButton = `

            <button
                type="button"
                class="btn btn-sm btn-primary btn-select-ai-product"
                data-specification-id="${escapeHtml(
                    product.sno
                )}"
            >

                <i class="bi bi-stars me-1"></i>

                Select

            </button>

        `;

    }

}


            /*
            |--------------------------------------------------------------------------
            | MAIN IMAGE
            |--------------------------------------------------------------------------
            */

            const mainImageHtml =
                mainImage
                    ? `
                        <img
                            src="${escapeHtml(mainImage)}"
                            alt="Main Image"
                            style="
                                width:80px;
                                height:80px;
                                object-fit:contain;
                                border-radius:8px;
                            "
                        >
                    `
                    : `
                        <span class="text-muted">
                            No Image
                        </span>
                    `;


            /*
            |--------------------------------------------------------------------------
            | AI IMAGE
            |--------------------------------------------------------------------------
            */

            const aiImageHtml =
                aiImage
                    ? `
                        <img
                            src="${escapeHtml(aiImage)}"
                            alt="AI Approved"
                            style="
                                width:80px;
                                height:80px;
                                object-fit:contain;
                                border-radius:8px;
                            "
                        >
                    `
                    : `
                        <span class="text-muted">
                            -
                        </span>
                    `;


            /*
            |--------------------------------------------------------------------------
            | ROW
            |--------------------------------------------------------------------------
            */

            row.innerHTML = `

                <td>
                    ${mainImageHtml}
                </td>

                <td>
                    ${aiImageHtml}
                </td>

                <td>
                    ${selectButton}
                </td>

                <td>
                    ${escapeHtml(
                        product.product_name || '-'
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        product.product_type || '-'
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        product.sku || '-'
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        product.barcode || '-'
                    )}
                </td>

            `;


            tableBody.appendChild(row);

        }); // <-- CLOSE products.forEach()


        } // <-- CLOSE renderTable()


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        function renderPagination(
            result
        ) {

            pagination.innerHTML = '';


            /*
            |--------------------------------------------------------------------------
            | SINGLE PAGE
            |--------------------------------------------------------------------------
            */

            if (
                result.last_page <= 1
            ) {

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PAGINATION HTML
            |--------------------------------------------------------------------------
            */

            let html = `

                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-2"
                >

                    <small class="text-muted">

                        Showing

                        ${result.from || 0}

                        -

                        ${result.to || 0}

                        of

                        ${result.total || 0}

                    </small>


                    <div
                        class="btn-group"
                    >

            `;


            /*
            |--------------------------------------------------------------------------
            | PREVIOUS
            |--------------------------------------------------------------------------
            */

            if (
                result.current_page > 1
            ) {

                html += `

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm"
                        data-page="${result.current_page - 1}"
                    >

                        <i
                            class="bi bi-chevron-left"
                        ></i>

                    </button>

                `;
            }


            /*
            |--------------------------------------------------------------------------
            | CURRENT PAGE
            |--------------------------------------------------------------------------
            */

            html += `

                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    disabled
                >

                    ${result.current_page}

                    /

                    ${result.last_page}

                </button>

            `;


            /*
            |--------------------------------------------------------------------------
            | NEXT
            |--------------------------------------------------------------------------
            */

            if (
                result.current_page <
                result.last_page
            ) {

                html += `

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm"
                        data-page="${result.current_page + 1}"
                    >

                        <i
                            class="bi bi-chevron-right"
                        ></i>

                    </button>

                `;
            }


            html += `

                    </div>

                </div>

            `;


            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            pagination.innerHTML =
                html;


            /*
            |--------------------------------------------------------------------------
            | PAGINATION EVENTS
            |--------------------------------------------------------------------------
            */

            pagination
                .querySelectorAll(
                    '[data-page]'
                )
                .forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                loadAiDescriptionData(
                                    Number(
                                        this.dataset.page
                                    )
                                );

                            }
                        );

                    }
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        let searchTimer = null;


        searchInput.addEventListener(
            'input',
            function () {

                clearTimeout(
                    searchTimer
                );


                searchTimer =
                    setTimeout(
                        function () {

                            loadAiDescriptionData(
                                1
                            );

                        },
                        400
                    );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | INITIAL LOAD
        |--------------------------------------------------------------------------
        */

        loadAiDescriptionData(
            1
        );

    }
);


/*
|--------------------------------------------------------------------------
| SELECT AI PRODUCT
|--------------------------------------------------------------------------
|
| Event delegation is used because table rows
| are dynamically created.
|
*/

/*
|--------------------------------------------------------------------------
| SELECT AI PRODUCT
|--------------------------------------------------------------------------
|
| The product object comes directly from the table data.
| We store it globally so the same product is available when
| "Save AI Description" is clicked.
|
*/

document.addEventListener(
    'click',
    async function (event) {

        const button =
            event.target.closest(
                '.btn-select-ai-product'
            );


        if (!button) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SPECIFICATION ID
        |--------------------------------------------------------------------------
        */

        const specificationId =
            button.dataset.specificationId;


        if (!specificationId) {

            Swal.fire({

                icon: 'error',

                title: 'Error',

                text:
                    'Specification ID not found.'

            });

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | GET PRODUCT FROM TABLE CACHE
        |--------------------------------------------------------------------------
        */

        const product =
            window.aiDescriptionProductCache[
                String(specificationId)
            ];


        if (!product) {

            console.error(
                'Product not found in cache:',
                specificationId
            );


            Swal.fire({

                icon: 'error',

                title: 'Product Not Found',

                text:
                    'The selected product data could not be found. Please reload the page.'

            });

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Store the complete selected product.
        |
        | This will be used by:
        |
        | 1. Get AI Description
        | 2. Save AI Description
        |
        */

        selectedAiProduct =
            product;


        /*
        |--------------------------------------------------------------------------
        | ALSO STORE IT ON WINDOW
        |--------------------------------------------------------------------------
        | This makes debugging easier and protects against scope issues.
        |--------------------------------------------------------------------------
        */

        window.selectedAiProduct =
            product;


        console.log(
            'SELECTED AI PRODUCT:',
            selectedAiProduct
        );


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL
        |--------------------------------------------------------------------------
        */

        await loadApprovedImages(
            specificationId,
            product
        );

    }
);

/*
|--------------------------------------------------------------------------
| LOAD APPROVED AI IMAGES + PRODUCT DETAILS
|--------------------------------------------------------------------------
*/

async function loadApprovedImages(
    specificationId,
    product
) {
    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    const modalElement =
        document.getElementById(
            'approvedImagesModal'
        );

    if (!modalElement) {

        console.error(
            'approvedImagesModal not found.'
        );

        return;
    }


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const loading =
        document.getElementById(
            'approvedImagesLoading'
        );

    const content =
        document.getElementById(
            'approvedImagesContent'
        );

    const empty =
        document.getElementById(
            'approvedImagesEmpty'
        );

    const errorBox =
        document.getElementById(
            'approvedImagesError'
        );

    const approvedMainImage =
        document.getElementById(
            'approvedMainImage'
        );

    const approvedMainImageEmpty =
        document.getElementById(
            'approvedMainImageEmpty'
        );

    const originalMainImage =
        document.getElementById(
            'originalMainImage'
        );

    const originalMainImageEmpty =
        document.getElementById(
            'originalMainImageEmpty'
        );

    const grid =
        document.getElementById(
            'approvedImagesGrid'
        );

    const subImagesEmpty =
        document.getElementById(
            'approvedSubImagesEmpty'
        );


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAILS ELEMENTS
    |--------------------------------------------------------------------------
    */

    const modalProductName =
        document.getElementById(
            'modalProductName'
        );

    const modalProductType =
        document.getElementById(
            'modalProductType'
        );

    const modalProductSku =
        document.getElementById(
            'modalProductSku'
        );

    const modalSupplierSku =
        document.getElementById(
            'modalSupplierSku'
        );

    const modalProductBarcode =
        document.getElementById(
            'modalProductBarcode'
        );

    const modalProductGender =
        document.getElementById(
            'modalProductGender'
        );

    const modalProductComposition =
        document.getElementById(
            'modalProductComposition'
        );

    const modalProductColour =
        document.getElementById(
            'modalProductColour'
        );

    const modalProductSize =
        document.getElementById(
            'modalProductSize'
        );

    const getAiDescriptionButton =
        document.getElementById(
            'btnGetAiDescription'
        );


    /*
    |--------------------------------------------------------------------------
    | CHECK PRODUCT OBJECT
    |--------------------------------------------------------------------------
    |
    | The product object comes directly from the table data.
    | We should NOT depend on the approved-images API for these details.
    |
    */

    if (
        !product ||
        typeof product !== 'object'
    ) {

        console.error(
            'Selected product data not found.',
            product
        );

        Swal.fire({
            icon: 'error',
            title: 'Product Data Not Found',
            text:
                'The selected product information could not be found. Please reload the page.'
        });

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    loading.style.display =
        'block';

    content.style.display =
        'none';

    empty.style.display =
        'none';

    errorBox.style.display =
        'none';

    errorBox.textContent =
        '';


    /*
    |--------------------------------------------------------------------------
    | RESET APPROVED MAIN IMAGE
    |--------------------------------------------------------------------------
    */

    approvedMainImage.src =
        '';

    approvedMainImage.style.display =
        'none';

    approvedMainImageEmpty.style.display =
        'block';


    /*
    |--------------------------------------------------------------------------
    | RESET ORIGINAL MAIN IMAGE
    |--------------------------------------------------------------------------
    */

    originalMainImage.src =
        '';

    originalMainImage.style.display =
        'none';

    originalMainImageEmpty.style.display =
        'block';


    /*
    |--------------------------------------------------------------------------
    | RESET APPROVED SUB IMAGES
    |--------------------------------------------------------------------------
    */

    grid.innerHTML =
        '';

    subImagesEmpty.style.display =
        'none';


    /*
    |--------------------------------------------------------------------------
    | PRODUCT DETAILS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | These values come from "product", which is the same object
    | already used to render the table.
    |
    */

    modalProductName.textContent =
        product.product_name ||
        '-';

    modalProductType.textContent =
        product.product_type ||
        '-';

    modalProductSku.textContent =
        product.sku ||
        '-';

    modalSupplierSku.textContent =
        product.sku_supplier ||
        product.supplier_sku ||
        '-';

    modalProductBarcode.textContent =
        product.barcode ||
        '-';

    modalProductGender.textContent =
        product.gender_name ||
        product.gender ||
        '-';

    modalProductComposition.textContent =
        product.composition_name ||
        product.composition ||
        '-';

    modalProductColour.textContent =
        product.colour_name ||
        product.colour ||
        product.color_name ||
        product.color ||
        '-';

    modalProductSize.textContent =
        product.size_name ||
        product.size ||
        '-';


    /*
    |--------------------------------------------------------------------------
    | ORIGINAL PRODUCT IMAGE
    |--------------------------------------------------------------------------
    |
    | Use the same img_path that is already used in the table.
    |
    */

    const originalMainUrl =
        getImageUrl(
            product.img_path
        );

    if (originalMainUrl) {

        originalMainImage.src =
            originalMainUrl;

        originalMainImage.style.display =
            'block';

        originalMainImageEmpty.style.display =
            'none';
    }


    /*
    |--------------------------------------------------------------------------
    | AI DESCRIPTION BUTTON
    |--------------------------------------------------------------------------
    */

    if (getAiDescriptionButton) {

        getAiDescriptionButton.dataset.specificationId =
            specificationId;
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW MODAL
    |--------------------------------------------------------------------------
    */

    modal.show();


    /*
    |--------------------------------------------------------------------------
    | LOAD APPROVED AI IMAGES ONLY
    |--------------------------------------------------------------------------
    */

    try {

        const params =
            new URLSearchParams({
                specification_id:
                    specificationId
            });


        const response =
            await fetch(
                `{{ route('ai-description.approved-images') }}?${params.toString()}`,
                {
                    method: 'GET',

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
                'Unable to load approved images.'
            );
        }


        const data =
            result.data || {};


        /*
        |--------------------------------------------------------------------------
        | APPROVED IMAGES
        |--------------------------------------------------------------------------
        */

        const approvedImages =
            Array.isArray(
                data.approved_images
            )
                ? data.approved_images
                : [];


        /*
        |--------------------------------------------------------------------------
        | FIND APPROVED MAIN IMAGE
        |--------------------------------------------------------------------------
        */

        const approvedMain =
            approvedImages.find(
                function (image) {

                    return (
                        String(
                            image.image_type ||
                            ''
                        ).toLowerCase() ===
                        'main'
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | FIND APPROVED SUB IMAGES
        |--------------------------------------------------------------------------
        */

        const subImages =
            approvedImages.filter(
                function (image) {

                    return (
                        String(
                            image.image_type ||
                            ''
                        ).toLowerCase() ===
                        'subimage'
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | AI APPROVED MAIN IMAGE
        |--------------------------------------------------------------------------
        */

        if (approvedMain) {

            const approvedMainUrl =
                getImageUrl(
                    approvedMain.enhanced_image_path
                );


            if (approvedMainUrl) {

                approvedMainImage.src =
                    approvedMainUrl;

                approvedMainImage.style.display =
                    'block';

                approvedMainImageEmpty.style.display =
                    'none';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | APPROVED SUB IMAGES
        |--------------------------------------------------------------------------
        */

        if (!subImages.length) {

            subImagesEmpty.style.display =
                'block';

        } else {

            subImages.forEach(
    function (image, index) {

        const imageUrl =
            getImageUrl(
                image.enhanced_image_path
            );


        if (!imageUrl) {
            return;
        }


        const col =
            document.createElement('div');


        col.className =
            'col-6 col-sm-4 col-md-3 col-lg-2';


        col.innerHTML = `
            <div class="ai-sub-image-item">

                <img
                    src="${escapeHtml(imageUrl)}"
                    alt="Approved Sub Image"
                    class="approved-sub-image"
                    data-image-url="${escapeHtml(imageUrl)}"
                >

            </div>
        `;


        grid.appendChild(col);

    }
);


            /*
            |--------------------------------------------------------------------------
            | CLICK SUB IMAGE
            |--------------------------------------------------------------------------
            |
            | Clicking a subimage changes the large approved image.
            |
            */

            grid
                .querySelectorAll(
                    '.approved-sub-image'
                )
                .forEach(
                    function (image) {

                        image.addEventListener(
                            'click',
                            function () {

                                const imageUrl =
                                    this.dataset.imageUrl;


                                if (imageUrl) {

                                    approvedMainImage.src =
                                        imageUrl;

                                    approvedMainImage.style.display =
                                        'block';

                                    approvedMainImageEmpty.style.display =
                                        'none';
                                }

                            }
                        );

                    }
                );
        }


        /*
        |--------------------------------------------------------------------------
        | NO APPROVED IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            !approvedMain &&
            !subImages.length
        ) {

            empty.style.display =
                'block';
        }


        /*
        |--------------------------------------------------------------------------
        | FINISH
        |--------------------------------------------------------------------------
        */

        loading.style.display =
            'none';

        content.style.display =
            'block';


    } catch (error) {

        console.error(
            'Approved AI Images Error:',
            error
        );


        loading.style.display =
            'none';

        errorBox.textContent =
            error.message ||
            'Unable to load approved images.';

        errorBox.style.display =
            'block';
    }
}


/*
|--------------------------------------------------------------------------
| GET AI DESCRIPTION BUTTON
|--------------------------------------------------------------------------
|
| The button is already inside the modal Blade.
|
*/

/* ============================================================
   GET AI DESCRIPTION
   SAME AI CODE USED BY DESIGN SPECIFICATION MASTER
============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    const btnGetAiDescription =
        document.getElementById('btnGetAiDescription');

    if (!btnGetAiDescription) {
        return;
    }


   btnGetAiDescription.addEventListener(
    'click',
    async function () {

        /*
        |--------------------------------------------------------------------------
        | GET AI APPROVED IMAGE
        |--------------------------------------------------------------------------
        |
        | Instead of:
        |
        | #selectedImagePreview img
        |
        | we use:
        |
        | #approvedMainImage
        |
        */

        const approvedImage =
            document.getElementById('approvedMainImage');


        if (!approvedImage) {

            alert('Approved image element not found');

            return;

        }


        if (
            !approvedImage.src ||
            approvedImage.src === window.location.href
        ) {

            alert('AI Approved image not found');

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE URL
        |--------------------------------------------------------------------------
        */

        const imageUrl =
            await imageToBase64(
                approvedImage
            );


        console.log(
            'AI Description Image:',
            imageUrl
        );


        /*
        |--------------------------------------------------------------------------
        | LOADER
        |--------------------------------------------------------------------------
        */

        const loader =
            document.getElementById('loader');


        if (loader) {

            loader.style.display = 'flex';

        }


        /*
        |--------------------------------------------------------------------------
        | BUTTON LOADING
        |--------------------------------------------------------------------------
        */

        btnGetAiDescription.disabled = true;

        btnGetAiDescription.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>' +
            'Generating...';


        /*
        |--------------------------------------------------------------------------
        | SAME API USED BY generateBtn
        |--------------------------------------------------------------------------
        */

        fetch('/ocforms/vision-api-call.php', {

            method: 'POST',

            headers: {

                'Content-Type':
                    'application/x-www-form-urlencoded'

            },

            body:
                new URLSearchParams({

                    image_url: imageUrl

                })

        })


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        .then(function (res) {

            if (!res.ok) {

                throw new Error(
                    'API returned HTTP ' +
                    res.status
                );

            }


            return res.json();

        })


        /*
        |--------------------------------------------------------------------------
        | AI RESPONSE
        |--------------------------------------------------------------------------
        */

        .then(function (data) {

            console.log(
                'AI Description API Response:',
                data
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDATE RESPONSE
            |--------------------------------------------------------------------------
            */

            if (
                !data ||
                !data.choices ||
                !data.choices.length ||
                !data.choices[0].message
            ) {

                throw new Error(
                    'Invalid API response'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | GET AI CONTENT
            |--------------------------------------------------------------------------
            */

            const response =
                data.choices[0].message.content;


            console.log(
                'AI Response:',
                response
            );


            /*
            |--------------------------------------------------------------------------
            | EXTRACT SAME SECTIONS
            |--------------------------------------------------------------------------
            */

            const name =
                extractSection(
                    response,
                    'Meta Tag Title'
                );


            const desc =
                extractSection(
                    response,
                    'Product Description',
                    'Product Tags'
                );


            const tags =
                extractSection(
                    response,
                    'Product Tags'
                );


            const metaDesc =
                extractSection(
                    response,
                    'Meta Tag Description'
                );


            const keywords =
                extractSection(
                    response,
                    'Meta Tag Keywords'
                );


            let altText =
                extractSection(
                    response,
                    'Image Alt Text'
                );


            /*
            |--------------------------------------------------------------------------
            | ALT TEXT FALLBACK
            |--------------------------------------------------------------------------
            */

            if (!altText) {

                altText =
                    name +
                    ' ' +
                    keywords;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL PRODUCT NAME
            |--------------------------------------------------------------------------
            */

            const productName =
                document.getElementById(
                    'txt_productName'
                );


            if (productName) {

                productName.value =
                    name;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL PRODUCT DESCRIPTION
            |--------------------------------------------------------------------------
            */

            const productDescription =
                document.getElementById(
                    'txt_productDescription'
                );


            if (productDescription) {

                productDescription.value =
                    desc;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL META TITLE
            |--------------------------------------------------------------------------
            */

            const metaTitle =
                document.getElementById(
                    'txt_metaTitle'
                );


            if (metaTitle) {

                metaTitle.value =
                    name;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL META DESCRIPTION
            |--------------------------------------------------------------------------
            */

            const metaDescription =
                document.getElementById(
                    'txt_metaDescription'
                );


            if (metaDescription) {

                metaDescription.value =
                    metaDesc;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL META KEYWORDS
            |--------------------------------------------------------------------------
            */

            const metaKeywords =
                document.getElementById(
                    'txt_metaKeywords'
                );


            if (metaKeywords) {

                metaKeywords.value =
                    keywords;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL PRODUCT TAGS
            |--------------------------------------------------------------------------
            */

            const productTags =
                document.getElementById(
                    'txt_productTags'
                );


            if (productTags) {

                productTags.value =
                    tags;

            }


            /*
            |--------------------------------------------------------------------------
            | FILL IMAGE ALT TEXT
            |--------------------------------------------------------------------------
            */

            const imageAltText =
                document.getElementById(
                    'txt_image_alt_text'
                );


            if (imageAltText) {

                imageAltText.value =
                    altText;

            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            console.log(
                'Generated fields filled successfully.'
            );


            if (
                typeof Swal !== 'undefined'
            ) {

                Swal.fire({

                    icon: 'success',

                    title:
                        'AI Description Generated',

                    text:
                        'Product content has been generated successfully.',

                    confirmButtonText:
                        'OK',

                    confirmButtonColor:
                        '#2563eb',

                    timer: 1800,

                    timerProgressBar: true

                });

            }


        })


        /*
        |--------------------------------------------------------------------------
        | ERROR
        |--------------------------------------------------------------------------
        */

        .catch(function (error) {

            console.error(
                'Generate Error:',
                error
            );


            if (
                typeof Swal !== 'undefined'
            ) {

                Swal.fire({

                    icon: 'error',

                    title:
                        'Generation Failed',

                    text:
                        error.message ||
                        'Unable to generate AI description.'

                });

            } else {

                alert(
                    '❌ Generate Error\n\n' +
                    error.message
                );

            }

        })


        /*
        |--------------------------------------------------------------------------
        | FINALLY
        |--------------------------------------------------------------------------
        */

        .finally(function () {

            if (loader) {

                loader.style.display =
                    'none';

            }


            btnGetAiDescription.disabled =
                false;


            btnGetAiDescription.innerHTML =
                '<i class="bi bi-stars me-2"></i>' +
                'Get AI Description';

        });

    });

});

/* ============================================================
   SAVE AI DESCRIPTION
============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const saveButton =
            document.getElementById(
                'btnSaveAiDescription'
            );


        if (!saveButton) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | CLEAR AI FIELDS
        |--------------------------------------------------------------------------
        */

        function clearAiDescriptionFields() {

            const fields = [

                'txt_productName',

                'txt_productDescription',

                'txt_metaTitle',

                'txt_metaKeywords',

                'txt_metaDescription',

                'txt_productTags',

                'txt_image_alt_text'

            ];


            fields.forEach(
                function (id) {

                    const element =
                        document.getElementById(
                            id
                        );


                    if (element) {

                        element.value =
                            '';

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE PRODUCT MODAL
        |--------------------------------------------------------------------------
        */

        function closeAiProductModal() {

            const modalElement =
                document.getElementById(
                    'approvedImagesModal'
                );


            if (!modalElement) {

                return;

            }


            if (
                typeof bootstrap !==
                'undefined' &&
                bootstrap.Modal
            ) {

                const modal =
                    bootstrap.Modal.getInstance(
                        modalElement
                    ) ||
                    bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );


                modal.hide();

            }

        }


        /*
        |--------------------------------------------------------------------------
        | HIGHLIGHT SAVED PRODUCT IMMEDIATELY
        |--------------------------------------------------------------------------
        */

        function highlightSavedProduct(
            specificationId
        ) {

            const row =
                document.querySelector(
                    `tr[data-specification-id="${CSS.escape(
                        String(specificationId)
                    )}"]`
                );


            if (!row) {

                return;

            }


            /*
            |--------------------------------------------------------------
            | ADD HIGHLIGHT
            |--------------------------------------------------------------
            */

            row.classList.add(
                'ai-description-saved-row'
            );


            /*
            |--------------------------------------------------------------
            | CHANGE SELECT BUTTON
            |--------------------------------------------------------------
            */

            const button =
                row.querySelector(
                    '.btn-select-ai-product'
                );


            if (button) {

                button.classList.remove(
                    'btn-primary'
                );


                button.classList.add(
                    'btn-success'
                );


                button.innerHTML = `

                    <i
                        class="bi bi-check-circle me-1"
                    ></i>

                    AI Description Saved

                `;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SAVE BUTTON CLICK
        |--------------------------------------------------------------------------
        */

        saveButton.addEventListener(
            'click',
            async function () {

                /*
                |--------------------------------------------------------------------------
                | GET SELECTED PRODUCT
                |--------------------------------------------------------------------------
                */

                const product =
                    selectedAiProduct ||
                    window.selectedAiProduct ||
                    null;


                console.log(
                    'Saving AI Description for:',
                    product
                );


                /*
                |--------------------------------------------------------------------------
                | PRODUCT CHECK
                |--------------------------------------------------------------------------
                */

                if (
                    !product ||
                    !product.sno
                ) {

                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Product Not Selected',

                        text:
                            'Please select a product first.'

                    });

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | GET AI FIELDS
                |--------------------------------------------------------------------------
                */

                const productName =
                    document.getElementById(
                        'txt_productName'
                    )?.value?.trim() || '';


                const productDescription =
                    document.getElementById(
                        'txt_productDescription'
                    )?.value?.trim() || '';


                const metaTitle =
                    document.getElementById(
                        'txt_metaTitle'
                    )?.value?.trim() || '';


                const metaKeywords =
                    document.getElementById(
                        'txt_metaKeywords'
                    )?.value?.trim() || '';


                const metaDescription =
                    document.getElementById(
                        'txt_metaDescription'
                    )?.value?.trim() || '';


                const productTags =
                    document.getElementById(
                        'txt_productTags'
                    )?.value?.trim() || '';


                const imageAltText =
                    document.getElementById(
                        'txt_image_alt_text'
                    )?.value?.trim() || '';


                /*
                |--------------------------------------------------------------------------
                | CHECK CONTENT
                |--------------------------------------------------------------------------
                */

                if (
                    !productName &&
                    !productDescription &&
                    !metaTitle &&
                    !metaKeywords &&
                    !metaDescription &&
                    !productTags &&
                    !imageAltText
                ) {

                    Swal.fire({

                        icon:
                            'warning',

                        title:
                            'No AI Content',

                        text:
                            'Please generate the AI description before saving.'

                    });

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | CONFIRM SAVE
                |--------------------------------------------------------------------------
                */

                const confirmResult =
                    await Swal.fire({

                        icon:
                            'question',

                        title:
                            'Save AI Description?',

                        text:
                            'If an AI description already exists for this product, it will be updated.',

                        showCancelButton:
                            true,

                        confirmButtonText:
                            'Yes, Save',

                        cancelButtonText:
                            'Cancel',

                        confirmButtonColor:
                            '#198754'

                    });


                if (
                    !confirmResult.isConfirmed
                ) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | BUTTON LOADING
                |--------------------------------------------------------------------------
                */

                const originalHtml =
                    saveButton.innerHTML;


                saveButton.disabled =
                    true;


                saveButton.innerHTML = `

                    <span
                        class="spinner-border spinner-border-sm me-2"
                    ></span>

                    Saving...

                `;


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | CSRF
                    |--------------------------------------------------------------------------
                    */

                    const csrfToken =
                        document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            ?.getAttribute(
                                'content'
                            );


                    if (!csrfToken) {

                        throw new Error(
                            'CSRF token not found.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SAVE
                    |--------------------------------------------------------------------------
                    */

                    const response =
                        await fetch(
                            '{{ route("all-garments.ai-description.save") }}',
                            {

                                method:
                                    'POST',

                                headers: {

                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest',

                                    'X-CSRF-TOKEN':
                                        csrfToken

                                },

                                body:
                                    JSON.stringify({

                                        /*
                                        |--------------------------------------------------
                                        | PRODUCT
                                        |--------------------------------------------------
                                        */

                                        product_id:
                                            product.sno,


                                        /*
                                        |--------------------------------------------------
                                        | AI PRODUCT NAME
                                        |--------------------------------------------------
                                        */

                                        AI_product_name:
                                            productName,


                                        /*
                                        |--------------------------------------------------
                                        | AI DESCRIPTION
                                        |--------------------------------------------------
                                        */

                                        AI_product_description:
                                            productDescription,


                                        /*
                                        |--------------------------------------------------
                                        | META TITLE
                                        |--------------------------------------------------
                                        */

                                        AI_Metatitle:
                                            metaTitle,


                                        /*
                                        |--------------------------------------------------
                                        | META KEYWORDS
                                        |--------------------------------------------------
                                        */

                                        AI_Metakeywards:
                                            metaKeywords,


                                        /*
                                        |--------------------------------------------------
                                        | META DESCRIPTION
                                        |--------------------------------------------------
                                        */

                                        AI_Metadescription:
                                            metaDescription,


                                        /*
                                        |--------------------------------------------------
                                        | PRODUCT TAG
                                        |--------------------------------------------------
                                        */

                                        AI_Producttag:
                                            productTags,


                                        /*
                                        |--------------------------------------------------
                                        | IMAGE ALT
                                        |--------------------------------------------------
                                        */

                                        AI_Imagealttext:
                                            imageAltText,


                                        /*
                                        |--------------------------------------------------
                                        | COMPANY
                                        |--------------------------------------------------
                                        */

                                        company_id:
                                            product.companyid ??
                                            null,


                                        /*
                                        |--------------------------------------------------
                                        | SUB COMPANY
                                        |--------------------------------------------------
                                        */

                                        subcompany_id:
                                            product.subcompanyid ??
                                            null,


                                        /*
                                        |--------------------------------------------------
                                        | PROJECT
                                        |--------------------------------------------------
                                        */

                                        projectid:
                                            product.projectid ??
                                            null

                                    })

                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    const result =
                        await response.json();


                    console.log(
                        'Save AI Description Response:',
                        result
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !response.ok ||
                        !result.success
                    ) {

                        throw new Error(
                            result.message ||
                            'Unable to save AI description.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MARK PRODUCT AS SAVED
                    |--------------------------------------------------------------------------
                    */

                    product.has_ai_description =
                        1;


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE CACHE
                    |--------------------------------------------------------------------------
                    */

                    window
                        .aiDescriptionProductCache[
                            String(product.sno)
                        ] =
                            product;


                    /*
                    |--------------------------------------------------------------------------
                    | HIGHLIGHT CURRENT ROW
                    |--------------------------------------------------------------------------
                    */

                    highlightSavedProduct(
                        product.sno
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS ALERT
                    |--------------------------------------------------------------------------
                    */

                    await Swal.fire({

                        icon:
                            'success',

                        title:
                            result.action ===
                            'updated'
                                ? 'Updated Successfully'
                                : 'Saved Successfully',

                        text:
                            result.message ||
                            'AI description saved successfully.',

                        confirmButtonText:
                            'OK',

                        confirmButtonColor:
                            '#198754'

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | CLOSE MODAL
                    |--------------------------------------------------------------------------
                    */

                    closeAiProductModal();


                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR AI FIELDS
                    |--------------------------------------------------------------------------
                    */

                    clearAiDescriptionFields();


                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR SELECTED PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    selectedAiProduct =
                        null;


                    window.selectedAiProduct =
                        null;


                    /*
                    |--------------------------------------------------------------------------
                    | REFRESH TABLE
                    |--------------------------------------------------------------------------
                    |
                    | The controller now returns has_ai_description.
                    | Therefore the green highlight will remain after
                    | table reload.
                    |
                    */

                    if (
                        typeof loadAiDescriptionData ===
                        'function'
                    ) {

                        loadAiDescriptionData(
                            currentPage || 1
                        );

                    }


                } catch (error) {

                    console.error(
                        'Save AI Description Error:',
                        error
                    );


                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'Save Failed',

                        text:
                            error.message ||
                            'Unable to save AI description.'

                    });

                } finally {

                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    saveButton.disabled =
                        false;


                    saveButton.innerHTML =
                        originalHtml;

                }

            }
        );

    }
);

function extractSection(
    text,
    startLabel,
    endLabel = ''
) {

    if (!text) {

        return '';

    }


    text =
        String(text);


    const startIndex =
        text
            .toLowerCase()
            .indexOf(
                startLabel.toLowerCase()
            );


    if (startIndex === -1) {

        return '';

    }


    let start =
        startIndex +
        startLabel.length;


    let end =
        text.length;


    if (endLabel) {

        const endIndex =
            text
                .toLowerCase()
                .indexOf(
                    endLabel.toLowerCase(),
                    start
                );


        if (endIndex !== -1) {

            end =
                endIndex;

        }

    }


    let value =
        text.substring(
            start,
            end
        );


    return value
        .replace(
            /^[:\-–—]\s*/,
            ''
        )
        .trim();

}

async function imageToBase64(img) {

    /*
    |--------------------------------------------------------------------------
    | CREATE CANVAS
    |--------------------------------------------------------------------------
    */

    const canvas =
        document.createElement('canvas');


    const ctx =
        canvas.getContext('2d');


    /*
    |--------------------------------------------------------------------------
    | IMAGE DIMENSIONS
    |--------------------------------------------------------------------------
    */

    canvas.width =
        img.naturalWidth;

    canvas.height =
        img.naturalHeight;


    /*
    |--------------------------------------------------------------------------
    | DRAW IMAGE
    |--------------------------------------------------------------------------
    */

    ctx.drawImage(
        img,
        0,
        0
    );


    /*
    |--------------------------------------------------------------------------
    | CONVERT TO BASE64
    |--------------------------------------------------------------------------
    */

    return canvas.toDataURL(
        'image/jpeg',
        0.90
    );
}

</script>

@endsection