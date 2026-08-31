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
                                Barcode
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


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

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
        | IMAGE URL
        |--------------------------------------------------------------------------
        */

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


            /*
            |--------------------------------------------------------------------------
            | Convert to string
            |--------------------------------------------------------------------------
            */

            path = String(path).trim();


            /*
            |--------------------------------------------------------------------------
            | Full URL
            |--------------------------------------------------------------------------
            */

            if (
                path.startsWith('http://') ||
                path.startsWith('https://') ||
                path.startsWith('data:')
            ) {
                return path;
            }


            /*
            |--------------------------------------------------------------------------
            | JSON ARRAY PATH
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | ["ItemsDesigner_Masterwithbarcode/123/abc.webp"]
            |
            */

            if (
                path.startsWith('[') &&
                path.endsWith(']')
            ) {

                try {

                    const images = JSON.parse(path);

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


            /*
            |--------------------------------------------------------------------------
            | Remove old ../../ prefix
            |--------------------------------------------------------------------------
            */

            path =
                path.replace(
                    /^(\.\.\/)+/,
                    ''
                );


            /*
            |--------------------------------------------------------------------------
            | Remove leading slash
            |--------------------------------------------------------------------------
            */

            path =
                path.replace(
                    /^\/+/,
                    ''
                );


            /*
            |--------------------------------------------------------------------------
            | Return public path
            |--------------------------------------------------------------------------
            */

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
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        async function loadAiDescriptionData(
            page = 1
        ) {

            tableBody.innerHTML = `

                <tr>

                    <td
                        colspan="6"
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

                const search =
                    searchInput.value.trim();


                const params =
                    new URLSearchParams({

                        page: page,

                        per_page: 20,

                        search: search

                    });


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


                const result =
                    await response.json();


                if (
                    !response.ok ||
                    !result.success
                ) {

                    throw new Error(
                        result.message ||
                        'Unable to load products.'
                    );

                }


                currentPage =
                    result.current_page;


                renderTable(
                    result.data || []
                );


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
                            colspan="6"
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

        function renderTable(
            products
        ) {

            tableBody.innerHTML = '';


            if (
                !products.length
            ) {

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


            products.forEach(
                function (product) {

                    const mainImage =
                        getImageUrl(
                            product.img_path
                        );


                    const aiImage =
                        getImageUrl(
                            product.ai_approved_image
                        );


                    const mainImageHtml =
                        mainImage
                            ?
                            `
                                <img
                                    src="${escapeHtml(mainImage)}"
                                    alt="Main Image"
                                    style="
                                        width:90px;
                                        height:90px;
                                        object-fit:cover;
                                        border-radius:8px;
                                    "
                                >
                            `
                            :
                            `
                                <div
                                    class="d-flex align-items-center justify-content-center bg-light rounded"
                                    style="
                                        width:90px;
                                        height:90px;
                                    "
                                >
                                    <i
                                        class="bi bi-image text-muted"
                                    ></i>
                                </div>
                            `;


                    const aiImageHtml =
                        aiImage
                            ?
                            `
                                <img
                                    src="${escapeHtml(aiImage)}"
                                    alt="AI Approved Image"
                                    style="
                                        width:90px;
                                        height:90px;
                                        object-fit:cover;
                                        border-radius:8px;
                                    "
                                >
                            `
                            :
                            `
                                <div
                                    class="d-flex align-items-center justify-content-center bg-light rounded"
                                    style="
                                        width:90px;
                                        height:90px;
                                    "
                                >
                                    <i
                                        class="bi bi-stars text-muted"
                                    ></i>
                                </div>
                            `;


                    const row =
                        document.createElement(
                            'tr'
                        );


                    row.innerHTML = `

                        <td>
                            ${mainImageHtml}
                        </td>


                        <td>
                            ${aiImageHtml}
                        </td>


                        <td>

                            <strong>
                                ${escapeHtml(
                                     product.product_name ||
                                    '-'
                                )}
                            </strong>

                        </td>


                        <td>

                            ${escapeHtml(
                                 product.product_type ||
                                '-'
                            )}

                        </td>


                        <td>

                            <span
                                class="badge bg-light text-dark border"
                            >
                                ${escapeHtml(
                                    product.sku ||
                                    '-'
                                )}
                            </span>

                        </td>


                        <td>

                            ${escapeHtml(
                                product.barcode ||
                                '-'
                            )}

                        </td>

                    `;


                    tableBody.appendChild(
                        row
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        function renderPagination(
            result
        ) {

            pagination.innerHTML = '';


            if (
                result.last_page <= 1
            ) {

                return;

            }


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


            if (
                result.current_page > 1
            ) {

                html += `

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm"
                        data-page="${result.current_page - 1}"
                    >

                        <i class="bi bi-chevron-left"></i>

                    </button>

                `;

            }


            html += `

                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                >

                    ${result.current_page}
                    /
                    ${result.last_page}

                </button>

            `;


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

                        <i class="bi bi-chevron-right"></i>

                    </button>

                `;

            }


            html += `

                    </div>

                </div>

            `;


            pagination.innerHTML =
                html;


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

</script>

@endsection