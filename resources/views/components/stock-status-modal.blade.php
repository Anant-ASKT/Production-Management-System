{{-- =========================================================
     STOCK STATUS MODAL
     Report source:
     published_product
          -> specification_id
     auto_designer_specification_master
          -> barcode
     vendor_stock
          -> barcode
========================================================= --}}

<div
    class="modal fade"
    id="stockStatusModal"
    tabindex="-1"
    aria-labelledby="stockStatusModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">

        <div class="modal-content border-0 shadow-lg">

            {{-- =====================================================
                 HEADER
            ====================================================== --}}
            <div
                class="modal-header text-white"
                style="background:linear-gradient(135deg,#2b5288,#1f3f6b);"
            >

                <div>

                    <h5
                        class="modal-title mb-1"
                        id="stockStatusModalLabel"
                    >
                        <i class="bi bi-box-seam me-2"></i>
                        Stock Status
                    </h5>

                    <small class="opacity-75">
                        Published Product → Specification → Barcode → Vendor Stock
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            {{-- =====================================================
                 BODY
            ====================================================== --}}
            <div class="modal-body bg-light">

                {{-- =================================================
                     FILTER
                ================================================== --}}
                <div class="card border-0 shadow-sm mb-3">

                    <div class="card-header bg-white">

                        <strong>
                            <i class="bi bi-funnel me-2"></i>
                            Stock Status Filters
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="row g-3">

                            {{-- SEARCH --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusSearch"
                                >
                                    Search
                                </label>

                                <input
                                    type="text"
                                    id="stockStatusSearch"
                                    class="form-control"
                                    placeholder="SKU / Barcode / Product"
                                >

                            </div>


                            {{-- SKU --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusSku"
                                >
                                    SKU
                                </label>

                                <input
                                    type="text"
                                    id="stockStatusSku"
                                    class="form-control"
                                    placeholder="Enter SKU"
                                >

                            </div>


                            {{-- BARCODE --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusBarcode"
                                >
                                    Barcode
                                </label>

                                <input
                                    type="text"
                                    id="stockStatusBarcode"
                                    class="form-control"
                                    placeholder="Enter barcode"
                                >

                            </div>


                            {{-- SUPPLIER --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusSupplier"
                                >
                                    Supplier
                                </label>

                                <select
                                    id="stockStatusSupplier"
                                    class="form-select"
                                >
                                    <option value="">
                                        All Suppliers
                                    </option>
                                </select>

                            </div>


                            {{-- PRODUCT TYPE --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusProductType"
                                >
                                    Product Type
                                </label>

                                <select
                                    id="stockStatusProductType"
                                    class="form-select"
                                >
                                    <option value="">
                                        All Product Types
                                    </option>
                                </select>

                            </div>


                            {{-- PRODUCT NAME --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusProductName"
                                >
                                    Product Name
                                </label>

                                <select
                                    id="stockStatusProductName"
                                    class="form-select"
                                >
                                    <option value="">
                                        All Product Names
                                    </option>
                                </select>

                            </div>


                            {{-- COMPOSITION --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusComposition"
                                >
                                    Composition
                                </label>

                                <select
                                    id="stockStatusComposition"
                                    class="form-select"
                                >
                                    <option value="">
                                        All Compositions
                                    </option>
                                </select>

                            </div>


                            {{-- GENDER --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusGender"
                                >
                                    Gender Type
                                </label>

                                <select
                                    id="stockStatusGender"
                                    class="form-select"
                                >
                                    <option value="">
                                        All Gender Types
                                    </option>
                                </select>

                            </div>


                            {{-- WAREHOUSE --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusWarehouse"
                                >
                                    Warehouse
                                </label>

                                <select
                                    id="stockStatusWarehouse"
                                    class="form-select"
                                >
                                    <option value="">
                                        All Warehouses
                                    </option>
                                </select>

                            </div>


                            {{-- STOCK STATUS --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusStockStatus"
                                >
                                    Stock Status
                                </label>

                                <select
                                    id="stockStatusStockStatus"
                                    class="form-select"
                                >

                                    <option value="">
                                        All
                                    </option>

                                    <option value="in_stock">
                                        In Stock
                                    </option>

                                    <option value="out_of_stock">
                                        Out of Stock
                                    </option>

                                </select>

                            </div>


                            {{-- FROM DATE --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusFromDate"
                                >
                                    From Date
                                </label>

                                <input
                                    type="date"
                                    id="stockStatusFromDate"
                                    class="form-control"
                                >

                            </div>


                            {{-- TO DATE --}}
                            <div class="col-md-6 col-lg-3">

                                <label
                                    class="form-label small fw-semibold"
                                    for="stockStatusToDate"
                                >
                                    To Date
                                </label>

                                <input
                                    type="date"
                                    id="stockStatusToDate"
                                    class="form-control"
                                >

                            </div>

                        </div>


                        {{-- BUTTONS --}}
                        <div class="d-flex flex-wrap gap-2 mt-3">

                            <button
                                type="button"
                                id="btnGenerateStockStatus"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-search me-1"></i>
                                Generate Report
                            </button>

                            <button
                                type="button"
                                id="btnResetStockStatus"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Reset
                            </button>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     SUMMARY
                ================================================== --}}
                <div class="row g-3 mb-3">

                    {{-- TOTAL PRODUCTS --}}
                    <div class="col-6 col-lg-3">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body">

                                <div class="small text-muted">
                                    Product Rows
                                </div>

                                <div
                                    class="fs-4 fw-bold"
                                    id="stockStatusTotalProducts"
                                >
                                    0
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- RECEIVED --}}
                    <div class="col-6 col-lg-3">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body">

                                <div class="small text-muted">
                                    Received Qty
                                </div>

                                <div
                                    class="fs-4 fw-bold"
                                    id="stockStatusQuantityReceived"
                                >
                                    0
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- SENT --}}
                    <div class="col-6 col-lg-3">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body">

                                <div class="small text-muted">
                                    Sent Qty
                                </div>

                                <div
                                    class="fs-4 fw-bold"
                                    id="stockStatusSendQty"
                                >
                                    0
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- AVAILABLE --}}
                    <div class="col-6 col-lg-3">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-body">

                                <div class="small text-muted">
                                    Available Qty
                                </div>

                                <div
                                    class="fs-4 fw-bold text-success"
                                    id="stockStatusAvailableQty"
                                >
                                    0
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     REPORT
                ================================================== --}}
                <div class="card border-0 shadow-sm">

                    <div
                        class="card-header bg-white d-flex justify-content-between align-items-center gap-2"
                    >

                        <strong>
                            <i class="bi bi-table me-2"></i>
                            Stock Status Report
                        </strong>

                        <span
                            class="small text-muted"
                            id="stockStatusReportCount"
                        >
                            0 records
                        </span>

                    </div>


                    <div class="card-body p-0">

                        {{-- LOADING --}}
                        <div
                            id="stockStatusLoading"
                            class="text-center py-5"
                            style="display:none;"
                        >

                            <div
                                class="spinner-border text-primary"
                                role="status"
                            ></div>

                            <div class="small text-muted mt-2">
                                Loading stock report...
                            </div>

                        </div>


                        {{-- EMPTY --}}
                        <div
                            id="stockStatusEmpty"
                            class="text-center text-muted py-5"
                        >
                            Click "Generate Report" to load stock status.
                        </div>


                        {{-- TABLE --}}
                        <div
                            id="stockStatusTableWrapper"
                            class="table-responsive"
                            style="display:none;"
                        >

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>#</th>

                                        <th>SKU</th>

                                        <th>Barcode</th>

                                        <th>Product</th>

                                        <th>Type</th>

                                        <th>Composition</th>

                                        <th>Gender</th>

                                        <th>Warehouse</th>

                                        <th class="text-end">
                                            Received
                                        </th>

                                        <th class="text-end">
                                            Sent
                                        </th>

                                        <th class="text-end">
                                            Available
                                        </th>

                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody id="stockStatusTableBody"></tbody>

                            </table>

                        </div>

                    </div>


                    {{-- PAGINATION --}}
                    <div
                        class="card-footer bg-white"
                        id="stockStatusPagination"
                    ></div>

                </div>

            </div>


            {{-- =================================================
                 FOOTER
            ================================================== --}}
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


<script>
(function () {

    /*
    |--------------------------------------------------------------------------
    | STOCK STATUS
    |--------------------------------------------------------------------------
    */

    function initStockStatus() {

        const modalElement =
            document.getElementById(
                'stockStatusModal'
            );

        if (!modalElement) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate initialization
        |--------------------------------------------------------------------------
        */

        if (
            modalElement.dataset
                .stockStatusInitialized === '1'
        ) {
            return;
        }


        modalElement.dataset
            .stockStatusInitialized = '1';


        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById(
                'stockStatusSearch'
            );


        const skuInput =
            document.getElementById(
                'stockStatusSku'
            );


        const barcodeInput =
            document.getElementById(
                'stockStatusBarcode'
            );


        const supplierSelect =
            document.getElementById(
                'stockStatusSupplier'
            );


        const productTypeSelect =
            document.getElementById(
                'stockStatusProductType'
            );


        const productNameSelect =
            document.getElementById(
                'stockStatusProductName'
            );


        const compositionSelect =
            document.getElementById(
                'stockStatusComposition'
            );


        const genderSelect =
            document.getElementById(
                'stockStatusGender'
            );


        const warehouseSelect =
            document.getElementById(
                'stockStatusWarehouse'
            );


        const stockStatusSelect =
            document.getElementById(
                'stockStatusStockStatus'
            );


        const fromDateInput =
            document.getElementById(
                'stockStatusFromDate'
            );


        const toDateInput =
            document.getElementById(
                'stockStatusToDate'
            );


        const tableWrapper =
            document.getElementById(
                'stockStatusTableWrapper'
            );


        const tableBody =
            document.getElementById(
                'stockStatusTableBody'
            );


        const emptyBox =
            document.getElementById(
                'stockStatusEmpty'
            );


        const loadingBox =
            document.getElementById(
                'stockStatusLoading'
            );


        const pagination =
            document.getElementById(
                'stockStatusPagination'
            );


        const reportCount =
            document.getElementById(
                'stockStatusReportCount'
            );


        let currentPage = 1;


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            return String(
                value ?? ''
            )
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
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        function numberFormat(value) {

            const number =
                Number(value || 0);

            return number.toLocaleString(
                'en-IN',
                {
                    maximumFractionDigits: 2
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SET SELECT OPTIONS
        |--------------------------------------------------------------------------
        */

        function setOptions(
            select,
            rows,
            defaultText
        ) {

            if (!select) {
                return;
            }


            select.innerHTML =
                '<option value="">' +
                escapeHtml(
                    defaultText
                ) +
                '</option>';


            (rows || []).forEach(
                function (row) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        row.id ?? '';


                    option.textContent =
                        row.name ??
                        row.id ??
                        '';


                    select.appendChild(
                        option
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD FILTERS
        |--------------------------------------------------------------------------
        */

        async function loadStockStatusFilters() {

            try {

                const response =
                    await fetch(
                        '{{ route("stock-status.filters") }}',
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json'
                            },

                            credentials: 'same-origin'
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
                        'Unable to load filters.'
                    );
                }


                const data =
                    result.data || {};


                setOptions(
                    supplierSelect,
                    data.suppliers,
                    'All Suppliers'
                );


                setOptions(
                    productTypeSelect,
                    data.product_types,
                    'All Product Types'
                );


                setOptions(
                    productNameSelect,
                    data.product_names,
                    'All Product Names'
                );


                setOptions(
                    compositionSelect,
                    data.compositions,
                    'All Compositions'
                );


                setOptions(
                    genderSelect,
                    data.genders,
                    'All Gender Types'
                );


                setOptions(
                    warehouseSelect,
                    data.warehouses,
                    'All Warehouses'
                );


            } catch (error) {

                console.error(
                    'STOCK STATUS FILTER ERROR:',
                    error
                );

            }
        }


        /*
        |--------------------------------------------------------------------------
        | BUILD REPORT URL
        |--------------------------------------------------------------------------
        */

        function buildReportUrl(page) {

            const params =
                new URLSearchParams();


            params.set(
                'page',
                page || 1
            );


            params.set(
                'per_page',
                20
            );


            params.set(
                'search',
                searchInput?.value.trim() || ''
            );


            params.set(
                'sku',
                skuInput?.value.trim() || ''
            );


            params.set(
                'barcode',
                barcodeInput?.value.trim() || ''
            );


            params.set(
                'supplier_id',
                supplierSelect?.value || ''
            );


            params.set(
                'product_type',
                productTypeSelect?.value || ''
            );


            params.set(
                'product_name',
                productNameSelect?.value || ''
            );


            params.set(
                'composition',
                compositionSelect?.value || ''
            );


            params.set(
                'gender',
                genderSelect?.value || ''
            );


            params.set(
                'warehouse_id',
                warehouseSelect?.value || ''
            );


            params.set(
                'stock_status',
                stockStatusSelect?.value || ''
            );


            params.set(
                'from_date',
                fromDateInput?.value || ''
            );


            params.set(
                'to_date',
                toDateInput?.value || ''
            );


            return (
                '{{ route("stock-status.report") }}' +
                '?' +
                params.toString()
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD REPORT
        |--------------------------------------------------------------------------
        */

        async function loadStockStatusReport(
            page
        ) {

            currentPage =
                page || 1;


            if (loadingBox) {

                loadingBox.style.display =
                    'block';
            }


            if (emptyBox) {

                emptyBox.style.display =
                    'none';
            }


            if (tableWrapper) {

                tableWrapper.style.display =
                    'none';
            }


            if (pagination) {

                pagination.innerHTML =
                    '';
            }


            try {

                const response =
                    await fetch(
                        buildReportUrl(
                            currentPage
                        ),
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json'
                            },

                            credentials:
                                'same-origin'
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
                        'Unable to load stock report.'
                    );
                }


                renderSummary(
                    result.summary || {}
                );


                renderRows(
                    result.data || [],
                    result
                );


            } catch (error) {

                console.error(
                    'STOCK STATUS REPORT ERROR:',
                    error
                );


                if (emptyBox) {

                    emptyBox.style.display =
                        'block';


                    emptyBox.className =
                        'alert alert-danger m-3 text-center';


                    emptyBox.textContent =
                        error.message;
                }


            } finally {

                if (loadingBox) {

                    loadingBox.style.display =
                        'none';
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        function renderSummary(
            summary
        ) {

            const totalProducts =
                document.getElementById(
                    'stockStatusTotalProducts'
                );


            const quantityReceived =
                document.getElementById(
                    'stockStatusQuantityReceived'
                );


            const sendQty =
                document.getElementById(
                    'stockStatusSendQty'
                );


            const availableQty =
                document.getElementById(
                    'stockStatusAvailableQty'
                );


            if (totalProducts) {

                totalProducts.textContent =
                    numberFormat(
                        summary.total_products
                    );
            }


            if (quantityReceived) {

                quantityReceived.textContent =
                    numberFormat(
                        summary.quantity_received
                    );
            }


            if (sendQty) {

                sendQty.textContent =
                    numberFormat(
                        summary.send_qty
                    );
            }


            if (availableQty) {

                availableQty.textContent =
                    numberFormat(
                        summary.available_qty
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RENDER ROWS
        |--------------------------------------------------------------------------
        */

        function renderRows(
            rows,
            meta
        ) {

            if (!tableBody) {
                return;
            }


            tableBody.innerHTML =
                '';


            if (
                !Array.isArray(rows) ||
                rows.length === 0
            ) {

                if (emptyBox) {

                    emptyBox.style.display =
                        'block';


                    emptyBox.className =
                        'text-center text-muted py-5';


                    emptyBox.innerHTML =
                        '<i class="bi bi-inbox fs-2 d-block mb-2"></i>' +
                        'No stock records found.';
                }


                if (reportCount) {

                    reportCount.textContent =
                        '0 records';
                }


                return;
            }


            if (emptyBox) {

                emptyBox.style.display =
                    'none';
            }


            if (tableWrapper) {

                tableWrapper.style.display =
                    'block';
            }


            if (reportCount) {

                reportCount.textContent =
                    numberFormat(
                        meta.total
                    ) +
                    ' records';
            }


            const start =
                Number(
                    meta.from || 0
                );


            rows.forEach(
                function (
                    row,
                    index
                ) {

                    const available =
                        Number(
                            row.available_qty || 0
                        );


                    const status =
                        available > 0
                            ? 'In Stock'
                            : 'Out of Stock';


                    const statusBadge =
                        available > 0
                            ? 'bg-success'
                            : 'bg-danger';


                    const tr =
                        document.createElement(
                            'tr'
                        );


                    tr.innerHTML = `

                        <td>
                            ${start + index}
                        </td>

                        <td>
                            <strong>
                                ${escapeHtml(
                                    row.sku || '-'
                                )}
                            </strong>

                            ${
                                row.sku_supplier
                                    ? `
                                        <div class="small text-muted">
                                            ${escapeHtml(
                                                row.sku_supplier
                                            )}
                                        </div>
                                      `
                                    : ''
                            }
                        </td>

                        <td>
                            <span class="font-monospace">
                                ${escapeHtml(
                                    row.barcode || '-'
                                )}
                            </span>
                        </td>

                        <td>
                            ${escapeHtml(
                                row.item_name_name ||
                                row.category_name ||
                                '-'
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                row.item_type_name ||
                                '-'
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                row.composition_name ||
                                '-'
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                row.gender_name ||
                                '-'
                            )}
                        </td>

                        <td>

                            <strong>
                                ${escapeHtml(
                                    row.warehouse_id ||
                                    '-'
                                )}
                            </strong>

                            ${
                                row.warehouse_location
                                    ? `
                                        <div class="small text-muted">
                                            ${escapeHtml(
                                                row.warehouse_location
                                            )}
                                        </div>
                                      `
                                    : ''
                            }

                        </td>

                        <td class="text-end">
                            ${numberFormat(
                                row.quantity_received
                            )}
                        </td>

                        <td class="text-end">
                            ${numberFormat(
                                row.send_qty
                            )}
                        </td>

                        <td class="text-end fw-bold">
                            ${numberFormat(
                                row.available_qty
                            )}
                        </td>

                        <td>
                            <span class="badge ${statusBadge}">
                                ${status}
                            </span>
                        </td>

                    `;


                    tableBody.appendChild(
                        tr
                    );

                }
            );


            renderPagination(
                meta
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        function renderPagination(
            meta
        ) {

            if (!pagination) {
                return;
            }


            const current =
                Number(
                    meta.current_page || 1
                );


            const last =
                Number(
                    meta.last_page || 1
                );


            if (last <= 1) {

                pagination.innerHTML =
                    '';

                return;
            }


            let html =
                '<div class="d-flex flex-wrap justify-content-between align-items-center gap-2">';


            html +=
                '<div class="small text-muted">' +
                'Page ' +
                current +
                ' of ' +
                last +
                '</div>';


            html +=
                '<div class="btn-group">';


            html +=
                '<button type="button" ' +
                'class="btn btn-sm btn-outline-primary stock-page-btn" ' +
                'data-page="' +
                Math.max(
                    1,
                    current - 1
                ) +
                '"' +
                (
                    current <= 1
                        ? ' disabled'
                        : ''
                ) +
                '>' +
                '&laquo;' +
                '</button>';


            const start =
                Math.max(
                    1,
                    current - 2
                );


            const end =
                Math.min(
                    last,
                    current + 2
                );


            for (
                let page = start;
                page <= end;
                page++
            ) {

                html +=
                    '<button type="button" ' +
                    'class="btn btn-sm ' +
                    (
                        page === current
                            ? 'btn-primary'
                            : 'btn-outline-primary'
                    ) +
                    ' stock-page-btn" ' +
                    'data-page="' +
                    page +
                    '">' +
                    page +
                    '</button>';
            }


            html +=
                '<button type="button" ' +
                'class="btn btn-sm btn-outline-primary stock-page-btn" ' +
                'data-page="' +
                Math.min(
                    last,
                    current + 1
                ) +
                '"' +
                (
                    current >= last
                        ? ' disabled'
                        : ''
                ) +
                '>' +
                '&raquo;' +
                '</button>';


            html +=
                '</div></div>';


            pagination.innerHTML =
                html;


            pagination
                .querySelectorAll(
                    '.stock-page-btn'
                )
                .forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                if (
                                    this.disabled
                                ) {
                                    return;
                                }


                                loadStockStatusReport(
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
        | RESET
        |--------------------------------------------------------------------------
        */

        function resetStockStatus() {

            [
                searchInput,
                skuInput,
                barcodeInput,
                supplierSelect,
                productTypeSelect,
                productNameSelect,
                compositionSelect,
                genderSelect,
                warehouseSelect,
                stockStatusSelect,
                fromDateInput,
                toDateInput
            ]
            .forEach(
                function (element) {

                    if (element) {

                        element.value =
                            '';
                    }

                }
            );


            loadStockStatusReport(
                1
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATE BUTTON
        |--------------------------------------------------------------------------
        */

        const generateButton =
            document.getElementById(
                'btnGenerateStockStatus'
            );


        if (generateButton) {

            generateButton.addEventListener(
                'click',
                function () {

                    loadStockStatusReport(
                        1
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESET BUTTON
        |--------------------------------------------------------------------------
        */

        const resetButton =
            document.getElementById(
                'btnResetStockStatus'
            );


        if (resetButton) {

            resetButton.addEventListener(
                'click',
                resetStockStatus
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ENTER SEARCH
        |--------------------------------------------------------------------------
        */

        [
            searchInput,
            skuInput,
            barcodeInput
        ]
        .forEach(
            function (element) {

                if (!element) {
                    return;
                }


                element.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Enter'
                        ) {

                            event.preventDefault();


                            loadStockStatusReport(
                                1
                            );
                        }

                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA WHEN MODAL OPENS
        |--------------------------------------------------------------------------
        */

        modalElement.addEventListener(
            'shown.bs.modal',
            function () {

                loadStockStatusFilters();

                loadStockStatusReport(
                    1
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | OPEN STOCK STATUS MODAL
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Sidebar can be dynamically loaded/replaced.
        | Therefore use delegated click event.
        |
        */

        if (
            !document.body.dataset
                .stockStatusClickHandler
        ) {

            document.body.dataset
                .stockStatusClickHandler = '1';


            document.addEventListener(
                'click',
                function (event) {

                    const button =
                        event.target.closest(
                            '#btnOpenStockStatus'
                        );


                    if (!button) {
                        return;
                    }


                    event.preventDefault();

                    event.stopPropagation();


                    const modal =
                        document.getElementById(
                            'stockStatusModal'
                        );


                    if (!modal) {

                        console.error(
                            'Stock Status modal not found.'
                        );

                        return;
                    }


                    if (
                        typeof bootstrap ===
                            'undefined' ||
                        !bootstrap.Modal
                    ) {

                        console.error(
                            'Bootstrap 5 Modal is not loaded.'
                        );

                        return;
                    }


                    const stockModal =
                        bootstrap.Modal
                            .getOrCreateInstance(
                                modal
                            );


                    stockModal.show();

                },
                true
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    if (
        document.readyState ===
        'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initStockStatus,
            {
                once: true
            }
        );

    } else {

        initStockStatus();
    }

})();
</script>