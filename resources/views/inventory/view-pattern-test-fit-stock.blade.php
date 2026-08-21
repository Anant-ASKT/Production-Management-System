@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="card shadow-sm">

        <div class="card-body">

            {{-- =========================================================
                 HEADER
            ========================================================== --}}

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>

                    <h4 class="mb-1">

                        <i class="bi bi-boxes me-2"></i>

                        View Pattern / Test Fit Stock

                    </h4>

                    <p class="text-muted mb-0">

                        View Pattern and Test Fit stock assigned to warehouse,
                        location and box.

                    </p>

                </div>

                <div>

                    <span
                        id="totalStockCount"
                        class="badge bg-secondary fs-6"
                    >
                        0 Stock
                    </span>

                </div>

            </div>


            {{-- =========================================================
                 FILTER
            ========================================================== --}}

            <div class="row g-2 mt-4">

                <div class="col-md-4">

                    <label class="form-label fw-semibold">
                        Search
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-search"></i>

                        </span>

                        <input
                            type="text"
                            id="ptfSearch"
                            class="form-control"
                            placeholder="Product, barcode..."
                        >

                    </div>

                </div>


                <div class="col-md-3">

                    <label class="form-label fw-semibold">
                        Stock Type
                    </label>

                    <select
                        id="ptfStockType"
                        class="form-select"
                    >

                        <option value="all">
                            All
                        </option>

                        <option value="pattern">
                            Pattern
                        </option>

                        <option value="testfit">
                            Test Fit
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <button
                        type="button"
                        id="ptfRefreshBtn"
                        class="btn btn-primary w-100"
                    >

                        <i class="bi bi-arrow-clockwise me-1"></i>

                        Refresh

                    </button>

                </div>

            </div>


            {{-- =========================================================
                 LOADING
            ========================================================== --}}

            <div
                id="ptfLoading"
                class="text-center py-5"
                style="display:none;"
            >

                <div
                    class="spinner-border text-primary"
                    role="status"
                ></div>

                <div class="mt-2 text-muted">

                    Loading stock...

                </div>

            </div>


            {{-- =========================================================
                 ERROR
            ========================================================== --}}

            <div
                id="ptfError"
                class="alert alert-danger mt-4"
                style="display:none;"
            ></div>


            {{-- =========================================================
                 EMPTY
            ========================================================== --}}

            <div
                id="ptfEmpty"
                class="text-center py-5 text-muted"
                style="display:none;"
            >

                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>

                No Pattern / Test Fit stock found.

            </div>


            {{-- =========================================================
                 STOCK GRID
            ========================================================== --}}

            <div
                id="ptfStockGrid"
                class="row g-3 mt-3"
            ></div>


            {{-- =========================================================
                 PAGINATION
            ========================================================== --}}

            <div
                id="ptfPagination"
                class="d-flex justify-content-center mt-4"
            ></div>

        </div>

    </div>

</div>


{{-- ================================================================
     PRODUCT DETAILS MODAL
================================================================ --}}

<div
    class="modal fade"
    id="ptfProductModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-scrollable"
    >

        <div class="modal-content">


            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="ptfProductModalTitle"
                >
                    Product Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="row g-4">


                    {{-- IMAGE --}}

                    <div class="col-lg-5">

                        <div
                            class="ptf-modal-image-wrapper"
                        >

                            <img
                                id="ptfModalImage"
                                src=""
                                alt="Product"
                                class="ptf-modal-image"
                                style="display:none;"
                            >

                            <div
                                id="ptfModalNoImage"
                                class="text-muted text-center py-5"
                            >

                                <i class="bi bi-image fs-1 d-block"></i>

                                No Image

                            </div>

                        </div>

                    </div>


                    {{-- DETAILS --}}

                    <div class="col-lg-7">

                        <div
                            id="ptfModalDetails"
                            class="row g-2"
                        ></div>

                    </div>

                </div>


                <hr class="my-4">


                {{-- STOCK DETAILS --}}

                <h5 class="mb-3">

                    <i class="bi bi-boxes me-2"></i>

                    Stock Assignment

                </h5>


                <div
                    id="ptfModalStockDetails"
                ></div>

            </div>


        </div>

    </div>

</div>


{{-- ================================================================
     IMAGE ONLY MODAL
================================================================ --}}

<div
    class="modal fade"
    id="ptfImageModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-dialog-centered modal-xl"
    >

        <div class="modal-content bg-dark">

            <div class="modal-header border-0">

                <h5
                    class="modal-title text-white"
                >
                    Product Image
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body text-center">

                <img
                    id="ptfBigImage"
                    src=""
                    alt="Product"
                    style="
                        max-width:100%;
                        max-height:80vh;
                        object-fit:contain;
                    "
                >

            </div>

        </div>

    </div>

</div>


<style>

/* ================================================================
   STOCK CARD
================================================================ */

.ptf-stock-card
{
    height:100%;
    border:1px solid #e5e7eb;
    border-radius:12px;
    overflow:hidden;
    background:#fff;
    transition:all .2s ease;
}

.ptf-stock-card:hover
{
    box-shadow:0 5px 20px rgba(0,0,0,.10);
    transform:translateY(-2px);
}


/* ================================================================
   IMAGE
================================================================ */

.ptf-stock-image-wrapper
{
    height:240px;
    background:#f8f9fa;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
    cursor:pointer;
}

.ptf-stock-image
{
    width:100%;
    height:100%;
    object-fit:contain;
    transition:transform .2s ease;
}

.ptf-stock-image:hover
{
    transform:scale(1.04);
}

.ptf-no-image
{
    color:#9ca3af;
    text-align:center;
}


/* ================================================================
   CARD BODY
================================================================ */

.ptf-stock-body
{
    padding:15px;
}

.ptf-stock-title
{
    font-size:17px;
    font-weight:600;
    margin-bottom:8px;
}

.ptf-stock-detail
{
    display:flex;
    justify-content:space-between;
    gap:10px;
    padding:4px 0;
    border-bottom:1px dashed #eee;
}

.ptf-stock-label
{
    color:#6b7280;
    font-size:13px;
}

.ptf-stock-value
{
    font-size:13px;
    font-weight:500;
    text-align:right;
}


/* ================================================================
   TYPE BADGE
================================================================ */

.ptf-type-badge
{
    display:inline-block;
    padding:4px 9px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.ptf-type-pattern
{
    background:#ede9fe;
    color:#6d28d9;
}

.ptf-type-testfit
{
    background:#dbeafe;
    color:#1d4ed8;
}


/* ================================================================
   MODAL IMAGE
================================================================ */

.ptf-modal-image-wrapper
{
    min-height:350px;
    background:#f8f9fa;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
    cursor:pointer;
}

.ptf-modal-image
{
    max-width:100%;
    max-height:500px;
    object-fit:contain;
}


/* ================================================================
   MODAL DETAIL
================================================================ */

.ptf-detail-box
{
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:10px;
    height:100%;
}

.ptf-detail-label
{
    font-size:12px;
    color:#6b7280;
    display:block;
    margin-bottom:3px;
}

.ptf-detail-value
{
    font-size:14px;
    font-weight:500;
    word-break:break-word;
}


/* ================================================================
   STOCK ASSIGNMENT
================================================================ */

.ptf-assignment
{
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:15px;
    margin-bottom:12px;
}

.ptf-assignment-header
{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
}

.ptf-assignment-grid
{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
}

.ptf-assignment-item
{
    background:#f8f9fa;
    border-radius:7px;
    padding:10px;
}

.ptf-assignment-label
{
    display:block;
    color:#6b7280;
    font-size:12px;
    margin-bottom:3px;
}

.ptf-assignment-value
{
    font-size:14px;
    font-weight:500;
    word-break:break-word;
}


/* ================================================================
   MOBILE
================================================================ */

@media(max-width:768px)
{
    .ptf-stock-image-wrapper
    {
        height:200px;
    }

    .ptf-assignment-grid
    {
        grid-template-columns:1fr;
    }

    .ptf-modal-image-wrapper
    {
        min-height:250px;
    }
}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function()
    {

        let ptfAllData = [];

        let ptfCurrentPage = 1;

        const ptfPerPage = 12;


        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById(
                'ptfSearch'
            );

        const typeSelect =
            document.getElementById(
                'ptfStockType'
            );

        const refreshBtn =
            document.getElementById(
                'ptfRefreshBtn'
            );

        const grid =
            document.getElementById(
                'ptfStockGrid'
            );

        const pagination =
            document.getElementById(
                'ptfPagination'
            );

        const loading =
            document.getElementById(
                'ptfLoading'
            );

        const empty =
            document.getElementById(
                'ptfEmpty'
            );

        const error =
            document.getElementById(
                'ptfError'
            );

        const totalCount =
            document.getElementById(
                'totalStockCount'
            );


        /*
        |--------------------------------------------------------------------------
        | LOAD STOCK
        |--------------------------------------------------------------------------
        */

       function loadPatternTestFitStock()
{
    /*
    |--------------------------------------------------------------------------
    | SHOW LOADING
    |--------------------------------------------------------------------------
    */

    loading.style.display = 'block';

    grid.innerHTML = '';

    empty.style.display = 'none';

    error.style.display = 'none';


    /*
    |--------------------------------------------------------------------------
    | GET FILTER VALUES
    |--------------------------------------------------------------------------
    */

    const type =
        typeSelect.value;

    const search =
        searchInput.value.trim();


    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    */

    const url =
        "{{ route('inventory.pattern-test-fit-stock.data') }}" +
        "?type=" +
        encodeURIComponent(type) +
        "&search=" +
        encodeURIComponent(search);


    console.log(
        'Pattern/Test Fit URL:',
        url
    );


    /*
    |--------------------------------------------------------------------------
    | FETCH
    |--------------------------------------------------------------------------
    */

    fetch(
        url,
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


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    .then(
        function(response)
        {

            if (!response.ok)
            {

                throw new Error(
                    'Server returned HTTP ' +
                    response.status
                );

            }

            return response.json();

        }
    )


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    .then(
        function(result)
        {

            console.log(
                'Pattern/Test Fit API Response:',
                result
            );


            loading.style.display =
                'none';


            /*
            |--------------------------------------------------------------------------
            | CHECK SUCCESS
            |--------------------------------------------------------------------------
            */

            if (!result.success)
            {

                throw new Error(
                    result.message ||
                    'Unable to load stock.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PATTERN DATA
            |--------------------------------------------------------------------------
            */

            const patternData =
                Array.isArray(
                    result.pattern
                )
                ? result.pattern
                : [];


            /*
            |--------------------------------------------------------------------------
            | TEST FIT DATA
            |--------------------------------------------------------------------------
            */

            const testFitData =
                Array.isArray(
                    result.testfit
                )
                ? result.testfit
                : [];


            console.log(
                'Pattern records:',
                patternData.length
            );


            console.log(
                'Test Fit records:',
                testFitData.length
            );


            /*
            |--------------------------------------------------------------------------
            | COMBINE BOTH
            |--------------------------------------------------------------------------
            */

            ptfAllData =
                patternData.concat(
                    testFitData
                );


            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */

            totalCount.textContent =
                ptfAllData.length +
                ' Stock';


            /*
            |--------------------------------------------------------------------------
            | RESET PAGE
            |--------------------------------------------------------------------------
            */

            ptfCurrentPage =
                1;


            /*
            |--------------------------------------------------------------------------
            | NO DATA
            |--------------------------------------------------------------------------
            */

            if (
                ptfAllData.length === 0
            )
            {

                empty.style.display =
                    'block';

                pagination.innerHTML =
                    '';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | RENDER
            |--------------------------------------------------------------------------
            */

            renderPage();

        }
    )


    /*
    |--------------------------------------------------------------------------
    | ERROR
    |--------------------------------------------------------------------------
    */

    .catch(
        function(err)
        {

            console.error(
                'Pattern/Test Fit stock:',
                err
            );


            loading.style.display =
                'none';


            grid.innerHTML =
                '';


            error.textContent =
                err.message ||
                'Unable to load stock.';


            error.style.display =
                'block';

        }
    );

}


        /*
        |--------------------------------------------------------------------------
        | RENDER PAGE
        |--------------------------------------------------------------------------
        */

        function renderPage()
        {

            grid.innerHTML =
                '';

            pagination.innerHTML =
                '';


            if (!ptfAllData.length)
            {

                empty.style.display =
                    'block';

                return;

            }


            empty.style.display =
                'none';


            const start =
                (ptfCurrentPage - 1) *
                ptfPerPage;


            const end =
                start +
                ptfPerPage;


            const pageData =
                ptfAllData.slice(
                    start,
                    end
                );


            pageData.forEach(
                function(row)
                {

                    grid.insertAdjacentHTML(
                        'beforeend',
                        createStockCard(row)
                    );

                }
            );


            renderPagination();

        }


        /*
        |--------------------------------------------------------------------------
        | CREATE STOCK CARD
        |--------------------------------------------------------------------------
        */

        function createStockCard(row)
        {

            const imageUrl =
                getImageUrl(row);


            const productName =
                row.item_name ||
                'Unknown Product';


            const stockType =
                row.stock_type ||
                '-';


            const typeClass =
                stockType === 'Pattern'
                    ? 'ptf-type-pattern'
                    : 'ptf-type-testfit';


            const index =
                ptfAllData.indexOf(row);


            return `

                <div class="col-xl-3 col-lg-4 col-md-6">

                    <div class="ptf-stock-card">


                        <div
                            class="ptf-stock-image-wrapper"
                            onclick="openPtfProduct(${index})"
                        >

                            ${
                                imageUrl

                                ?

                                `
                                <img
                                    src="${escapePtf(imageUrl)}"
                                    class="ptf-stock-image"
                                    alt="Product"
                                    onclick="event.stopPropagation(); openPtfBigImage('${escapePtf(imageUrl)}')"
                                >
                                `

                                :

                                `
                                <div class="ptf-no-image">

                                    <i class="bi bi-image fs-1"></i>

                                    <div>
                                        No Image
                                    </div>

                                </div>
                                `
                            }

                        </div>


                        <div class="ptf-stock-body">


                            <div class="mb-2">

                                <span
                                    class="ptf-type-badge ${typeClass}"
                                >
                                    ${escapePtf(stockType)}
                                </span>

                            </div>


                            <div class="ptf-stock-title">

                                ${escapePtf(productName)}

                            </div>


                            ${ptfDetail(
                                'Gender',
                                row.gender
                            )}

                            ${ptfDetail(
                                'Composition',
                                row.composition
                            )}

                            ${ptfDetail(
                                'Colour',
                                row.colour
                            )}

                            ${ptfDetail(
                                'Size',
                                row.sizes
                            )}

                            ${ptfDetail(
                                'Designer',
                                row.designer_name
                            )}

                            ${ptfDetail(
                                'Warehouse',
                                row.warehouse_name
                            )}

                            ${ptfDetail(
                                'Location',
                                row.location_name
                            )}

                            ${ptfDetail(
                                'Box No',
                                row.box_no
                            )}

                            ${ptfDetail(
                                'Images',
                                row.qty_img
                            )}

                        </div>

                    </div>

                </div>

            `;

        }


        /*
        |--------------------------------------------------------------------------
        | DETAIL ROW
        |--------------------------------------------------------------------------
        */

        function ptfDetail(
            label,
            value
        )
        {

            return `

                <div class="ptf-stock-detail">

                    <span class="ptf-stock-label">
                        ${escapePtf(label)}
                    </span>

                    <span class="ptf-stock-value">
                        ${escapePtf(
                            value === null ||
                            value === undefined ||
                            value === ''
                                ? '-'
                                : value
                        )}
                    </span>

                </div>

            `;

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        function renderPagination()
        {

            const totalPages =
                Math.ceil(
                    ptfAllData.length /
                    ptfPerPage
                );


            if (totalPages <= 1)
            {
                return;
            }


            let html = '';


            html += `

                <button
                    type="button"
                    class="btn btn-sm btn-outline-primary me-1"
                    ${ptfCurrentPage === 1 ? 'disabled' : ''}
                    onclick="changePtfPage(${ptfCurrentPage - 1})"
                >
                    Previous
                </button>

            `;


            for (
                let i = 1;
                i <= totalPages;
                i++
            )
            {

                html += `

                    <button
                        type="button"
                        class="btn btn-sm ${
                            i === ptfCurrentPage
                                ? 'btn-primary'
                                : 'btn-outline-primary'
                        } me-1"
                        onclick="changePtfPage(${i})"
                    >
                        ${i}
                    </button>

                `;

            }


            html += `

                <button
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    ${
                        ptfCurrentPage === totalPages
                            ? 'disabled'
                            : ''
                    }
                    onclick="changePtfPage(${ptfCurrentPage + 1})"
                >
                    Next
                </button>

            `;


            pagination.innerHTML =
                html;

        }


        /*
        |--------------------------------------------------------------------------
        | GLOBAL PAGE FUNCTION
        |--------------------------------------------------------------------------
        */

        window.changePtfPage =
            function(page)
            {

                const totalPages =
                    Math.ceil(
                        ptfAllData.length /
                        ptfPerPage
                    );


                if (
                    page < 1 ||
                    page > totalPages
                )
                {
                    return;
                }


                ptfCurrentPage =
                    page;


                renderPage();


                window.scrollTo({
                    top:0,
                    behavior:'smooth'
                });

            };


        /*
        |--------------------------------------------------------------------------
        | OPEN PRODUCT
        |--------------------------------------------------------------------------
        */

        window.openPtfProduct =
            function(index)
            {

                const row =
                    ptfAllData[index];


                if (!row)
                {
                    return;
                }


                const modalTitle =
                    document.getElementById(
                        'ptfProductModalTitle'
                    );


                modalTitle.textContent =
                    row.item_name ||
                    'Product Details';


                /*
                |--------------------------------------------------------------------------
                | IMAGE
                |--------------------------------------------------------------------------
                */

                const image =
                    document.getElementById(
                        'ptfModalImage'
                    );

                const noImage =
                    document.getElementById(
                        'ptfModalNoImage'
                    );


                const imageUrl =
                    getImageUrl(row);


                if (imageUrl)
                {

                    image.src =
                        imageUrl;

                    image.style.display =
                        'block';

                    noImage.style.display =
                        'none';

                }
                else
                {

                    image.removeAttribute(
                        'src'
                    );

                    image.style.display =
                        'none';

                    noImage.style.display =
                        'block';

                }


                /*
                |--------------------------------------------------------------------------
                | DETAILS
                |--------------------------------------------------------------------------
                */

                document.getElementById(
                    'ptfModalDetails'
                ).innerHTML = `

                    ${modalDetail(
                        'Product Name',
                        row.item_name
                    )}

                    ${modalDetail(
                        'Stock Type',
                        row.stock_type
                    )}

                    ${modalDetail(
                        'Main Barcode',
                        row.barcode
                    )}

                    ${modalDetail(
                        'Design Barcode',
                        row.barcodeofdesign
                    )}

                    ${modalDetail(
                        'Designer',
                        row.designer_name
                    )}

                    ${modalDetail(
                        'Item Type',
                        row.item_type
                    )}

                    ${modalDetail(
                        'Gender',
                        row.gender
                    )}

                    ${modalDetail(
                        'Composition',
                        row.composition
                    )}

                    ${modalDetail(
                        'Colour',
                        row.colour
                    )}

                    ${modalDetail(
                        'Size',
                        row.sizes
                    )}

                    ${modalDetail(
                        'Warehouse',
                        row.warehouse_name
                    )}

                    ${modalDetail(
                        'Location',
                        row.location_name
                    )}

                    ${modalDetail(
                        'Box No',
                        row.box_no
                    )}

                    ${modalDetail(
                        'Image Quantity',
                        row.qty_img
                    )}

                `;


                /*
                |--------------------------------------------------------------------------
                | STOCK ASSIGNMENT
                |--------------------------------------------------------------------------
                */

                const stockDetails =
                    document.getElementById(
                        'ptfModalStockDetails'
                    );


                stockDetails.innerHTML = `

                    <div class="ptf-assignment">

                        <div class="ptf-assignment-header">

                            <strong>
                                ${escapePtf(
                                    row.stock_type
                                )}
                            </strong>

                            <span class="badge bg-secondary">
                                ${escapePtf(
                                    row.qty_img || 0
                                )} Images
                            </span>

                        </div>


                        <div class="ptf-assignment-grid">

                            ${assignmentDetail(
                                'Warehouse',
                                row.warehouse_name
                            )}

                            ${assignmentDetail(
                                'Location',
                                row.location_name
                            )}

                            ${assignmentDetail(
                                'Box No',
                                row.box_no
                            )}

                            ${assignmentDetail(
                                'Main Barcode',
                                row.barcode
                            )}

                            ${assignmentDetail(
                                'Design Barcode',
                                row.barcodeofdesign
                            )}

                            ${assignmentDetail(
                                'Image Quantity',
                                row.qty_img
                            )}

                        </div>

                    </div>

                `;


                /*
                |--------------------------------------------------------------------------
                | SHOW MODAL
                |--------------------------------------------------------------------------
                */

                bootstrap.Modal
                    .getOrCreateInstance(
                        document.getElementById(
                            'ptfProductModal'
                        )
                    )
                    .show();

            };


        /*
        |--------------------------------------------------------------------------
        | OPEN BIG IMAGE
        |--------------------------------------------------------------------------
        */

        window.openPtfBigImage =
            function(url)
            {

                if (!url)
                {
                    return;
                }


                document.getElementById(
                    'ptfBigImage'
                ).src =
                    url;


                bootstrap.Modal
                    .getOrCreateInstance(
                        document.getElementById(
                            'ptfImageModal'
                        )
                    )
                    .show();

            };


        /*
        |--------------------------------------------------------------------------
        | MODAL DETAIL
        |--------------------------------------------------------------------------
        */

        function modalDetail(
            label,
            value
        )
        {

            return `

                <div class="col-md-6">

                    <div class="ptf-detail-box">

                        <span class="ptf-detail-label">

                            ${escapePtf(label)}

                        </span>

                        <span class="ptf-detail-value">

                            ${escapePtf(
                                value === null ||
                                value === undefined ||
                                value === ''
                                    ? '-'
                                    : value
                            )}

                        </span>

                    </div>

                </div>

            `;

        }


        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT DETAIL
        |--------------------------------------------------------------------------
        */

        function assignmentDetail(
            label,
            value
        )
        {

            return `

                <div class="ptf-assignment-item">

                    <span class="ptf-assignment-label">

                        ${escapePtf(label)}

                    </span>

                    <span class="ptf-assignment-value">

                        ${escapePtf(
                            value === null ||
                            value === undefined ||
                            value === ''
                                ? '-'
                                : value
                        )}

                    </span>

                </div>

            `;

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE PATH
        |--------------------------------------------------------------------------
        */

        function getImageUrl(row)
        {

            if (
                !row.img_path ||
                row.img_path === 'null'
            )
            {
                return '';
            }


            let path =
                String(row.img_path)
                    .trim();


            /*
            |--------------------------------------------------------------------------
            | JSON IMAGE PATH
            |--------------------------------------------------------------------------
            */

            try
            {

                if (
                    path.startsWith('[')
                )
                {

                    const images =
                        JSON.parse(path);


                    if (
                        Array.isArray(images) &&
                        images.length > 0
                    )
                    {

                        path =
                            images[0];

                    }

                }

            }
            catch(e)
            {
                // normal path
            }


            path =
                String(path)
                    .replace(/^["']|["']$/g, '')
                    .trim();


            if (!path)
            {
                return '';
            }


            /*
            |--------------------------------------------------------------------------
            | ALREADY FULL URL
            |--------------------------------------------------------------------------
            */

            if (
                path.startsWith('http://') ||
                path.startsWith('https://') ||
                path.startsWith('/')
            )
            {

                return path;

            }


            /*
            |--------------------------------------------------------------------------
            | YOUR PROJECT IMAGE LOCATION
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | ItemsDesigner_Masterwithbarcode/009101.../image.jpg
            |
            */

            return "{{ asset('') }}" +
                path.replace(/^\/+/, '');

        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE
        |--------------------------------------------------------------------------
        */

        function escapePtf(value)
        {

            if (
                value === null ||
                value === undefined
            )
            {
                return '-';
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
        | EVENTS
        |--------------------------------------------------------------------------
        */

        refreshBtn.addEventListener(
            'click',
            function()
            {
                loadPatternTestFitStock();
            }
        );


        typeSelect.addEventListener(
            'change',
            function()
            {
                loadPatternTestFitStock();
            }
        );


        let searchTimer = null;


        searchInput.addEventListener(
            'input',
            function()
            {

                clearTimeout(
                    searchTimer
                );


                searchTimer =
                    setTimeout(
                        function()
                        {
                            loadPatternTestFitStock();
                        },
                        400
                    );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | MODAL IMAGE CLICK
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'ptfModalImage'
        ).addEventListener(
            'click',
            function()
            {

                if (!this.src)
                {
                    return;
                }


                openPtfBigImage(
                    this.src
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | FIRST LOAD
        |--------------------------------------------------------------------------
        */

        loadPatternTestFitStock();

    }
);

</script>

@endsection