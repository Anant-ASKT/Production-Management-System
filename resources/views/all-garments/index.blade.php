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

        PROJECT FILTER

        Project = Supplier

    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row g-3 align-items-end">

                <div class="col-md-6">

                    <label

                        for="garmentProject"

                        class="form-label fw-semibold"

                    >

                        Suppliers

                    </label>

                    <select

                        id="garmentProject"

                        class="form-select"

                    >

                        <option value="">

                            All Suppliers

                        </option>

                        @foreach($projects as $project)

                            <option

                                value="{{ $project->projectid }}"

                                data-company-id="{{ $project->companyid }}"

                                data-subcompany-id="{{ $project->subcompanyid }}"

                            >

                                {{ $project->projectname }}

                            </option>

                        @endforeach

                    </select>

                </div>



                <div class="col-md-auto">

                    <button

                        type="button"

                        id="btnApplyGarmentFilter"

                        class="btn btn-primary"

                    >

                        <i class="bi bi-funnel me-1"></i>

                        Apply Filter

                    </button>



                    <button

                        type="button"

                        id="btnClearGarmentFilter"

                        class="btn btn-outline-secondary"

                    >

                        Clear

                    </button>

                </div>

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


@endsection



@push('scripts')

<script>

document.addEventListener(

    'DOMContentLoaded',

    function () {



        /*

        |--------------------------------------------------------------------------

        | ELEMENTS

        |--------------------------------------------------------------------------

        */

        const projectSelect =

            document.getElementById(

                'garmentProject'

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

        function getImageUrl(image) {

            if (

                !image ||

                image === null ||

                image === undefined

            ) {

                return '';

            }



            image =

                String(image).trim();



            /*

            |--------------------------------------------------------------------------

            | JSON ARRAY

            |--------------------------------------------------------------------------

            */

            if (

                image.startsWith('[') &&

                image.endsWith(']')

            ) {

                try {

                    const parsed =

                        JSON.parse(image);



                    if (

                        Array.isArray(parsed) &&

                        parsed.length > 0

                    ) {

                        image =

                            parsed[0];

                    }

                }

                catch (error) {

                    console.warn(

                        'Image JSON parse error:',

                        error

                    );

                }

            }



            image =

                String(image).trim();



            if (!image) {

                return '';

            }



            /*

            |--------------------------------------------------------------------------

            | FULL URL

            |--------------------------------------------------------------------------

            */

            if (

                image.startsWith('http://') ||

                image.startsWith('https://') ||

                image.startsWith('data:')

            ) {

                return image;

            }



            /*

            |--------------------------------------------------------------------------

            | CLEAN PATH

            |--------------------------------------------------------------------------

            */

            image =

                image.replace(

                    /^\/+/,

                    ''

                );



            image =

                image.replace(

                    /^public\/+/i,

                    ''

                );



            /*

            |--------------------------------------------------------------------------

            | LARAVEL PUBLIC PATH

            |--------------------------------------------------------------------------

            */

            return "{{ asset('') }}" + image;

        }



        /*

        |--------------------------------------------------------------------------

        | GET SELECTED PROJECT DATA

        |--------------------------------------------------------------------------

        */

        function getSelectedProject() {

            if (!projectSelect) {

                return {

                    projectId: '',

                    companyId: '',

                    subCompanyId: ''

                };

            }



            const projectId =

                projectSelect.value;



            const selectedOption =

                projectSelect.options[

                    projectSelect.selectedIndex

                ];



            if (

                !projectId ||

                !selectedOption

            ) {

                return {

                    projectId: '',

                    companyId: '',

                    subCompanyId: ''

                };

            }



            return {

                projectId:

                    projectId,

                companyId:

                    selectedOption.dataset.companyId ||

                    '',

                subCompanyId:

                    selectedOption.dataset.subcompanyId ||

                    ''

            };

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



            /*

            |--------------------------------------------------------------------------

            | PROJECT

            |--------------------------------------------------------------------------

            |

            | Selected Project provides:

            |

            | project_id

            | company_id

            | subcompany_id

            |

            */

            const selectedProject =

                getSelectedProject();



            console.log(

                'Selected Project:',

                selectedProject

            );



            /*

            |--------------------------------------------------------------------------

            | SEND PROJECT IDs

            |--------------------------------------------------------------------------

            */

            if (

                selectedProject.projectId !== '' &&

                selectedProject.companyId !== '' &&

                selectedProject.subCompanyId !== ''

            ) {

                params.set(

                    'project_id',

                    selectedProject.projectId

                );



                params.set(

                    'company_id',

                    selectedProject.companyId

                );



                params.set(

                    'subcompany_id',

                    selectedProject.subCompanyId

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

                                No garments available

                                ${

                                    selectedProject.projectId

                                        ? 'for the selected project.'

                                        : '.'

                                }

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
                        'col-md-6 col-lg-4 col-xl-3';


                    /*
                    |--------------------------------------------------------------------------
                    | IMAGE
                    |--------------------------------------------------------------------------
                    */

                    let image =
                        garment.img_path ||
                        garment.image ||
                        garment.image_path ||
                        garment.main_image ||
                        garment.design_image ||
                        garment.oc_main_img ||
                        '';

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
                        'Pending';

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
                            class="card h-100 border-0 shadow-sm specification-card"
                        >

                            <div class="card-body p-0">

                                <div
                                    class="specification-image-wrapper"
                                    style="
                                        height:240px;
                                        overflow:hidden;
                                        background:#f5f6f8;
                                        border-radius:8px 8px 0 0;
                                    "
                                >

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
                                        class="specification-actions mt-3"
                                    >

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary btn-view-spec"
                                            data-id="${escapeHtml(specificationId)}"
                                        >

                                            <i class="bi bi-eye me-1"></i>

                                            View

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    `;


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
        | VIEW GARMENT MODAL
        |--------------------------------------------------------------------------
        */

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
                    'Pending'
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

                const image =
                    garment.img_path ||
                    garment.image ||
                    garment.image_path ||
                    garment.main_image ||
                    garment.design_image ||
                    garment.oc_main_img ||
                    '';

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

                    if (projectSelect) {

                        projectSelect.value =

                            '';

                    }



                    if (searchInput) {

                        searchInput.value =

                            '';

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

        | OPTIONAL:

        | APPLY WHEN PROJECT CHANGES

        |--------------------------------------------------------------------------

        |

        | If you want the list to change immediately after selecting

        | a project, without clicking Apply Filter, keep this.

        |

        */

        if (projectSelect) {

            projectSelect.addEventListener(

                'change',

                function () {

                    loadGarments(

                        1

                    );

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

        loadGarments(

            1

        );

    }

);

</script>

@endpush