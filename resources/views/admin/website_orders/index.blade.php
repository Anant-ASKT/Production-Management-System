@extends('layouts.app')

@section('title', 'Website Orders')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center text-dark">
                <span class="badge bg-primary-subtle text-primary p-2 rounded-3 me-2">
                    <i class="bi bi-cart-check fs-5"></i>
                </span>
                Website Orders
            </h4>
            <p class="text-muted small mb-0">
                Track and manage orders received across multiple supplier stores with matched SKU and origin manufacturer details.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-7 fw-semibold border border-success-subtle" id="totalOrdersBadge">
                <i class="bi bi-receipt me-1"></i> <span id="statTotalOrdersTop">{{ $totalOrders }}</span> Total Orders
            </span>
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" id="btnRefresh" title="Refresh Orders">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- TOP STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Orders</div>
                        <h3 class="fw-bold text-dark mt-1 mb-0" id="statTotalOrders">{{ $totalOrders }}</h3>
                    </div>
                    <div class="p-3 rounded-4 bg-primary-subtle text-primary">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Processing</div>
                        <h3 class="fw-bold text-primary mt-1 mb-0" id="statProcessingOrders">{{ $processingOrders }}</h3>
                    </div>
                    <div class="p-3 rounded-4 bg-info-subtle text-info-emphasis">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Completed</div>
                        <h3 class="fw-bold text-success mt-1 mb-0" id="statCompletedOrders">{{ $completedOrders }}</h3>
                    </div>
                    <div class="p-3 rounded-4 bg-success-subtle text-success">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Pending / On-Hold</div>
                        <h3 class="fw-bold text-warning-emphasis mt-1 mb-0" id="statPendingOrders">{{ $pendingOrders }}</h3>
                    </div>
                    <div class="p-3 rounded-4 bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-pause-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                {{-- Search --}}
                <div class="col-12 col-md-4 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            id="orderSearch"
                            class="form-control border-start-0 ps-2"
                            placeholder="Search by Order #, SKU, Customer Name, Email, Phone..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <select id="statusFilter" class="form-select rounded-3">
                        <option value="all">All Statuses</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="on-hold">On-Hold</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                {{-- Date Filter --}}
                <div class="col-6 col-md-3 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent text-muted"><i class="bi bi-calendar3"></i></span>
                        <input type="date" id="dateFilter" class="form-control" title="Filter by date">
                        <button class="btn btn-outline-secondary" type="button" id="btnClearDate" title="Clear Date">&times;</button>
                    </div>
                </div>

                {{-- Per page --}}
                <div class="col-12 col-md-2 col-lg-2 d-flex justify-content-md-end align-items-center gap-2">
                    <label for="perPageSelect" class="small text-muted text-nowrap fw-semibold">Show:</label>
                    <select id="perPageSelect" class="form-select form-select-sm w-auto rounded-3">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ORDERS TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="ordersTable">
                    <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                        <tr>
                            <th style="width: 130px;" class="ps-3">Order</th>
                            <th style="min-width: 170px;">Date & Selling Store</th>
                            <th style="min-width: 190px;">Customer</th>
                            <th style="min-width: 320px;">Ordered Items & SKU Details</th>
                            <th style="min-width: 120px;">Payment</th>
                            <th style="min-width: 110px;">Status</th>
                            <th style="min-width: 110px;" class="text-end">Total</th>
                            <th style="width: 100px;" class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Loading website orders...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CARD FOOTER / PAGINATION --}}
        <div class="card-footer bg-white border-top py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="small text-muted" id="paginationSummary">
                Showing 0 to 0 of 0 orders
            </div>
            <nav aria-label="Order pagination">
                <ul class="pagination pagination-sm mb-0 gap-1" id="paginationNav">
                    <!-- Pagination items will be generated by JS -->
                </ul>
            </nav>
        </div>
    </div>

</div>

{{-- ORDER DETAIL MODAL --}}
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="bi bi-receipt-cutoff fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="orderDetailModalLabel">Order Details</h5>
                        <div class="small text-muted" id="modalOrderSubtitle">Loading...</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-body-tertiary">
                <div id="modalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Loading full order details...</p>
                </div>

                <div id="modalContent" class="d-none">

                    {{-- NAV TABS (Order Summary vs Raw Webhook Payload) --}}
                    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="orderModalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 fw-semibold" id="tab-overview" data-bs-toggle="pill" data-bs-target="#pane-overview" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i> Order Overview & SKU Mapping
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 fw-semibold" id="tab-raw" data-bs-toggle="pill" data-bs-target="#pane-raw" type="button" role="tab">
                                <i class="bi bi-code-square me-1"></i> Raw Webhook JSON
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="orderModalTabContent">
                        
                        {{-- TAB 1: OVERVIEW --}}
                        <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">

                            {{-- SELLING STORE BANNER --}}
                            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4 border-start border-4 border-primary">
                                <div class="row align-items-center g-2">
                                    <div class="col-md-6">
                                        <div class="text-muted small text-uppercase fw-semibold">Selling Store / Supplier Website:</div>
                                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                            <i class="bi bi-shop text-primary"></i>
                                            <span id="modalSellingStoreName">—</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <div class="text-muted small">Store URL:</div>
                                        <div class="small font-monospace" id="modalSellingStoreUrl">—</div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3-CARD ROW: Customer, Shipping, Payment --}}
                            <div class="row g-3 mb-4">
                                {{-- Billing Details --}}
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                                        <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small text-uppercase">
                                            <i class="bi bi-person-circle fs-6"></i> Billing Customer
                                        </div>
                                        <div class="fw-bold text-dark fs-6" id="modalBillingName">—</div>
                                        <div class="small text-muted mt-1" id="modalBillingAddress">—</div>
                                        <div class="small text-secondary mt-2">
                                            <div><i class="bi bi-envelope me-1 text-muted"></i> <span id="modalBillingEmail">—</span></div>
                                            <div><i class="bi bi-telephone me-1 text-muted"></i> <span id="modalBillingPhone">—</span></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Shipping Details --}}
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                                        <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small text-uppercase">
                                            <i class="bi bi-truck fs-6"></i> Shipping Address
                                        </div>
                                        <div class="fw-bold text-dark fs-6" id="modalShippingName">—</div>
                                        <div class="small text-muted mt-1" id="modalShippingAddress">—</div>
                                        <div class="small text-secondary mt-2">
                                            <div><i class="bi bi-geo-alt me-1 text-muted"></i> <span id="modalShippingLocation">—</span></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment & Status --}}
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                                        <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small text-uppercase">
                                            <i class="bi bi-credit-card-2-front fs-6"></i> Order & Payment
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small text-muted">Status:</span>
                                            <span id="modalStatusBadge" class="badge rounded-pill">—</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small text-muted">Payment:</span>
                                            <span class="small fw-bold text-dark" id="modalPaymentMethod">—</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small text-muted">Date Created:</span>
                                            <span class="small text-secondary" id="modalDateCreated">—</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small text-muted">Customer IP:</span>
                                            <span class="small font-monospace text-muted" id="modalCustomerIp">—</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- LINE ITEMS TABLE --}}
                            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 overflow-hidden">
                                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                        <i class="bi bi-boxes text-primary me-2"></i> Ordered Items with SKU & Supplier Details (<span id="modalItemCount">0</span>)
                                    </h6>
                                    <span class="small text-muted">Auto-matched with ERP Specification Master</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light small text-uppercase text-secondary">
                                            <tr>
                                                <th style="width: 70px;" class="ps-4">Item</th>
                                                <th style="min-width: 220px;">Product & SKU</th>
                                                <th style="min-width: 180px;">Origin Supplier / Specs</th>
                                                <th style="width: 120px;" class="text-end">Sold Price</th>
                                                <th style="width: 80px;" class="text-center">Qty</th>
                                                <th style="width: 130px;" class="text-end pe-4">Line Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalLineItemsBody">
                                            <!-- Injected by JS -->
                                        </tbody>
                                    </table>
                                </div>

                                {{-- ORDER TOTALS SUMMARY --}}
                                <div class="card-footer bg-light p-4 border-top">
                                    <div class="row justify-content-end">
                                        <div class="col-md-5 col-lg-4">
                                            <div class="d-flex justify-content-between py-1 small text-muted">
                                                <span>Items Subtotal:</span>
                                                <span class="fw-semibold text-dark" id="modalSubtotal">&#8377;0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small text-muted" id="modalShippingRow">
                                                <span>Shipping:</span>
                                                <span class="fw-semibold text-dark" id="modalShippingTotal">&#8377;0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small text-muted" id="modalDiscountRow">
                                                <span>Discount:</span>
                                                <span class="fw-semibold text-danger" id="modalDiscountTotal">-&#8377;0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small text-muted" id="modalTaxRow">
                                                <span>Total Tax:</span>
                                                <span class="fw-semibold text-dark" id="modalTaxTotal">&#8377;0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2 border-top border-2 mt-2">
                                                <span class="fw-bold text-dark fs-6">Grand Total:</span>
                                                <span class="fw-bold text-primary fs-5" id="modalGrandTotal">&#8377;0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CUSTOMER NOTE (if any) --}}
                            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 d-none mb-3" id="modalCustomerNoteCard">
                                <div class="fw-bold text-dark small mb-1"><i class="bi bi-chat-left-quote me-1 text-primary"></i> Customer Note:</div>
                                <div class="text-muted small" id="modalCustomerNoteText"></div>
                            </div>

                        </div>

                        {{-- TAB 2: RAW JSON PAYLOAD --}}
                        <div class="tab-pane fade" id="pane-raw" role="tabpanel">
                            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold small text-dark"><i class="bi bi-file-code me-1 text-primary"></i> Webhook Raw JSON Payload</span>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnCopyJson">
                                        <i class="bi bi-clipboard me-1"></i> Copy JSON
                                    </button>
                                </div>
                                <pre class="bg-dark text-light p-3 rounded-3 small mb-0" style="max-height: 480px; overflow-y: auto;" id="modalRawJson"></pre>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="modal-footer bg-white border-top px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stat-hover-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15,23,42,.08) !important;
    }
    .order-img-thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let searchTimer = null;

    const tableBody = document.getElementById('ordersTableBody');
    const paginationNav = document.getElementById('paginationNav');
    const paginationSummary = document.getElementById('paginationSummary');
    const searchInput = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const btnClearDate = document.getElementById('btnClearDate');
    const perPageSelect = document.getElementById('perPageSelect');
    const btnRefresh = document.getElementById('btnRefresh');

    // Status Badge generator
    function getStatusBadge(status) {
        const s = (status || 'pending').toLowerCase();
        let bgClass = 'bg-secondary-subtle text-secondary border-secondary-subtle';
        let icon = 'bi-circle';

        switch(s) {
            case 'completed':
                bgClass = 'bg-success-subtle text-success border border-success-subtle';
                icon = 'bi-check2-circle';
                break;
            case 'processing':
                bgClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                icon = 'bi-arrow-repeat';
                break;
            case 'on-hold':
                bgClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                icon = 'bi-pause-circle';
                break;
            case 'pending':
                bgClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                icon = 'bi-hourglass-split';
                break;
            case 'cancelled':
                bgClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                icon = 'bi-x-circle';
                break;
            case 'refunded':
                bgClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                icon = 'bi-arrow-counterclockwise';
                break;
            case 'failed':
                bgClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                icon = 'bi-exclamation-triangle';
                break;
        }

        return `<span class="badge ${bgClass} rounded-pill px-2.5 py-1 text-capitalize fw-semibold">
                    <i class="bi ${icon} me-1"></i>${s}
                </span>`;
    }

    // Load Orders Data
    function loadOrders(page = 1) {
        currentPage = page;
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading website orders...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: page,
            per_page: perPageSelect.value,
            search: searchInput.value.trim(),
            status: statusFilter.value,
            date_from: dateFilter.value,
            date_to: dateFilter.value
        });

        fetch(`{{ route('admin.website-orders.data') }}?${params.toString()}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">${response.message || 'Error loading data.'}</td></tr>`;
                    return;
                }

                // Update Stats if returned
                if (response.stats) {
                    document.getElementById('statTotalOrders').textContent = response.stats.total_orders;
                    document.getElementById('statTotalOrdersTop').textContent = response.stats.total_orders;
                    document.getElementById('statProcessingOrders').textContent = response.stats.processing_orders;
                    document.getElementById('statCompletedOrders').textContent = response.stats.completed_orders;
                    document.getElementById('statPendingOrders').textContent = response.stats.pending_orders;
                }

                renderTable(response.data);
                renderPagination(response);
            })
            .catch(err => {
                console.error('Error fetching orders:', err);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Failed to connect to server.</td></tr>`;
            });
    }

    function renderTable(items) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <div class="mb-2"><i class="bi bi-inbox fs-2 text-secondary"></i></div>
                        <h6 class="fw-semibold text-secondary">No Website Orders Found</h6>
                        <p class="small text-muted mb-0">No webhook order payloads match your active filters.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach(order => {
            // Render items summary preview with SKU and Origin details
            let itemsHtml = '';
            if (order.items_summary && order.items_summary.length > 0) {
                const firstItem = order.items_summary[0];
                const thumb = firstItem.image ? `<img src="${firstItem.image}" class="order-img-thumb me-2" alt="Product">` : `<div class="order-img-thumb d-flex align-items-center justify-content-center me-2 text-muted"><i class="bi bi-bag"></i></div>`;
                const moreCount = order.items_count > 1 ? `<span class="badge bg-light text-secondary border ms-1">+${order.items_count - 1} more item${order.items_count > 2 ? 's' : ''}</span>` : '';

                const skuBadge = firstItem.sku && firstItem.sku !== '—' 
                    ? `<span class="badge bg-dark-subtle text-dark border font-monospace me-1"><i class="bi bi-upc me-1"></i>${firstItem.sku}</span>` 
                    : '';

                const originSupplierBadge = firstItem.origin_supplier_name && firstItem.origin_supplier_name !== '—'
                    ? `<span class="badge bg-light text-secondary border me-1"><i class="bi bi-truck me-1"></i>Origin: ${firstItem.origin_supplier_name}</span>`
                    : '';

                itemsHtml = `
                    <div class="d-flex align-items-start">
                        ${thumb}
                        <div class="lh-sm">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 250px;" title="${firstItem.name}">${firstItem.name}</div>
                            <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                ${skuBadge}
                                ${originSupplierBadge}
                            </div>
                            <small class="text-muted d-block mt-1">Qty: ${firstItem.quantity} &times; ${order.currency_symbol}${parseFloat(firstItem.price).toFixed(2)} ${moreCount}</small>
                        </div>
                    </div>
                `;
            } else {
                itemsHtml = `<span class="text-muted small">No items detailed</span>`;
            }

            const storeBadge = order.selling_supplier_name 
                ? `<div class="badge bg-primary-subtle text-primary border border-primary-subtle text-truncate mt-1" style="max-width: 160px;" title="${order.selling_supplier_name}">
                    <i class="bi bi-shop me-1"></i>${order.selling_supplier_name}
                   </div>` 
                : (order.source_store ? `<div class="badge bg-light text-muted border text-truncate mt-1" style="max-width: 140px;" title="${order.source_store}"><i class="bi bi-shop me-1"></i>${order.source_store}</div>` : '');

            html += `
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-dark font-monospace fs-6">#${order.order_number}</div>
                        <small class="text-muted font-monospace">${order.order_key ? order.order_key.substring(0, 14) + '...' : ''}</small>
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark">${order.order_date}</div>
                        ${storeBadge}
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">${order.customer_name}</div>
                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i>${order.customer_email}</div>
                        ${order.customer_phone && order.customer_phone !== '—' ? `<div class="small text-muted"><i class="bi bi-telephone me-1"></i>${order.customer_phone}</div>` : ''}
                    </td>
                    <td>
                        ${itemsHtml}
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark">${order.payment_method}</div>
                    </td>
                    <td>
                        ${getStatusBadge(order.status)}
                    </td>
                    <td class="text-end">
                        <div class="fw-bold text-dark fs-6">${order.total_formatted}</div>
                    </td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs view-order-btn" data-id="${order.id}">
                            <i class="bi bi-eye me-1"></i> View
                        </button>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Attach modal click listeners
        document.querySelectorAll('.view-order-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const orderId = this.dataset.id;
                openOrderDetailModal(orderId);
            });
        });
    }

    function renderPagination(meta) {
        paginationSummary.textContent = `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total || 0} orders`;

        if (meta.last_page <= 1) {
            paginationNav.innerHTML = '';
            return;
        }

        let navHtml = '';

        // Previous
        navHtml += `
            <li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link rounded-2" href="javascript:void(0)" data-page="${meta.current_page - 1}">
                    &laquo;
                </a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                navHtml += `
                    <li class="page-item ${i === meta.current_page ? 'active' : ''}">
                        <a class="page-link rounded-2" href="javascript:void(0)" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
                navHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        navHtml += `
            <li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
                <a class="page-link rounded-2" href="javascript:void(0)" data-page="${meta.current_page + 1}">
                    &raquo;
                </a>
            </li>
        `;

        paginationNav.innerHTML = navHtml;

        paginationNav.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function () {
                const p = parseInt(this.dataset.page);
                if (p && p !== meta.current_page && p >= 1 && p <= meta.last_page) {
                    loadOrders(p);
                }
            });
        });
    }

    // Modal Handling
    const modalElement = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    const modalLoading = document.getElementById('modalLoading');
    const modalContent = document.getElementById('modalContent');

    function openOrderDetailModal(id) {
        modalLoading.classList.remove('d-none');
        modalContent.classList.add('d-none');
        modalElement.show();

        // Switch to Overview tab by default
        const tabTrigger = new bootstrap.Tab(document.getElementById('tab-overview'));
        tabTrigger.show();

        fetch(`{{ url('/admin/website-orders') }}/${id}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    alert(res.message || 'Error loading order details.');
                    modalElement.hide();
                    return;
                }

                const d = res.data;
                const sym = d.currency_symbol || '₹';

                document.getElementById('orderDetailModalLabel').textContent = `Order #${d.order_number}`;
                document.getElementById('modalOrderSubtitle').textContent = `Placed on ${d.date_created} • Order Key: ${d.order_key}`;

                // Selling store banner
                document.getElementById('modalSellingStoreName').textContent = d.selling_supplier_name || 'Website Store';
                document.getElementById('modalSellingStoreUrl').textContent = d.selling_supplier_url || d.source_store || '—';

                // Billing
                const b = d.billing || {};
                document.getElementById('modalBillingName').textContent = `${b.first_name || ''} ${b.last_name || ''}`.trim() || '—';
                document.getElementById('modalBillingAddress').innerHTML = `${b.address_1 || ''} ${b.address_2 || ''}<br>${b.city || ''}, ${b.state || ''} ${b.postcode || ''} ${b.country || ''}`.trim();
                document.getElementById('modalBillingEmail').textContent = b.email || '—';
                document.getElementById('modalBillingPhone').textContent = b.phone || '—';

                // Shipping
                const s = d.shipping || {};
                document.getElementById('modalShippingName').textContent = `${s.first_name || ''} ${s.last_name || ''}`.trim() || '—';
                document.getElementById('modalShippingAddress').innerHTML = `${s.address_1 || ''} ${s.address_2 || ''}`.trim() || 'Same as billing address';
                document.getElementById('modalShippingLocation').textContent = `${s.city || ''} ${s.state || ''} ${s.postcode || ''} ${s.country || ''}`.trim() || '—';

                // Payment & Status
                document.getElementById('modalStatusBadge').innerHTML = getStatusBadge(d.status);
                document.getElementById('modalPaymentMethod').textContent = d.payment_method;
                document.getElementById('modalDateCreated').textContent = d.date_created;
                document.getElementById('modalCustomerIp').textContent = d.customer_ip_address;

                // Line items with SKU, Manufacturer/Origin Supplier and Specification details
                const itemsBody = document.getElementById('modalLineItemsBody');
                document.getElementById('modalItemCount').textContent = d.line_items ? d.line_items.length : 0;
                let itemsHtml = '';
                let computedSubtotal = 0;

                (d.line_items || []).forEach(item => {
                    const price = parseFloat(item.price || 0);
                    const qty = parseInt(item.quantity || 1);
                    const lineTotal = parseFloat(item.total || (price * qty));
                    computedSubtotal += lineTotal;

                    const imgSrc = item.resolved_image || (item.image && item.image.src ? item.image.src : null);
                    const imgTag = imgSrc ? `<img src="${imgSrc}" class="order-img-thumb" alt="Product">` : `<div class="order-img-thumb d-flex align-items-center justify-content-center text-muted"><i class="bi bi-bag"></i></div>`;

                    const skuVal = item.resolved_sku || item.sku || '—';
                    const originSupName = item.origin_supplier_name || 'Global';

                    // Spec link button if matched
                    const specButton = item.spec_id 
                        ? `<a href="${item.spec_url}" target="_blank" class="badge bg-primary text-white text-decoration-none px-2 py-1"><i class="bi bi-box-arrow-up-right me-1"></i>Spec #${item.spec_id}</a>` 
                        : '';

                    // Attribute tags
                    let attrTags = [];
                    if (item.product_type) attrTags.push(`Type: ${item.product_type}`);
                    if (item.colour) attrTags.push(`Color: ${item.colour}`);
                    if (item.size) attrTags.push(`Size: ${item.size}`);
                    if (item.composition) attrTags.push(`Fabric: ${item.composition}`);
                    const attrHtml = attrTags.length > 0 ? `<div class="small text-muted mt-1">${attrTags.join(' • ')}</div>` : '';

                    itemsHtml += `
                        <tr>
                            <td class="ps-4">${imgTag}</td>
                            <td>
                                <div class="fw-bold text-dark">${item.name || 'Product'}</div>
                                <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                    <span class="badge bg-dark-subtle text-dark border font-monospace"><i class="bi bi-upc me-1"></i>SKU: ${skuVal}</span>
                                    ${specButton}
                                </div>
                                ${attrHtml}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="bi bi-truck text-primary me-1"></i>${originSupName}</div>
                                ${item.barcode ? `<div class="small text-muted font-monospace"><i class="bi bi-upc-scan me-1"></i>${item.barcode}</div>` : ''}
                                ${item.spec_price ? `<div class="small text-muted">Spec Base: ${sym}${parseFloat(item.spec_price).toFixed(2)}</div>` : ''}
                            </td>
                            <td class="text-end fw-semibold text-dark">${sym}${price.toFixed(2)}</td>
                            <td class="text-center fw-bold">${qty}</td>
                            <td class="text-end pe-4 fw-bold text-dark">${sym}${lineTotal.toFixed(2)}</td>
                        </tr>
                    `;
                });
                itemsBody.innerHTML = itemsHtml || `<tr><td colspan="6" class="text-center py-3 text-muted">No line items in this order payload.</td></tr>`;

                // Totals
                document.getElementById('modalSubtotal').textContent = `${sym}${computedSubtotal.toFixed(2)}`;
                document.getElementById('modalShippingTotal').textContent = `${sym}${parseFloat(d.shipping_total || 0).toFixed(2)}`;
                document.getElementById('modalDiscountTotal').textContent = `-${sym}${parseFloat(d.discount_total || 0).toFixed(2)}`;
                document.getElementById('modalTaxTotal').textContent = `${sym}${parseFloat(d.total_tax || 0).toFixed(2)}`;
                document.getElementById('modalGrandTotal').textContent = `${sym}${parseFloat(d.total || 0).toFixed(2)}`;

                // Customer Note
                const noteCard = document.getElementById('modalCustomerNoteCard');
                if (d.customer_note && d.customer_note.trim() !== '') {
                    noteCard.classList.remove('d-none');
                    document.getElementById('modalCustomerNoteText').textContent = d.customer_note;
                } else {
                    noteCard.classList.add('d-none');
                }

                // Raw JSON
                document.getElementById('modalRawJson').textContent = JSON.stringify(d.raw_payload, null, 2);

                modalLoading.classList.add('d-none');
                modalContent.classList.remove('d-none');
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load order details.');
                modalElement.hide();
            });
    }

    // Copy JSON button
    document.getElementById('btnCopyJson').addEventListener('click', function () {
        const jsonText = document.getElementById('modalRawJson').textContent;
        navigator.clipboard.writeText(jsonText).then(() => {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check2 me-1"></i> Copied!';
            this.classList.replace('btn-outline-secondary', 'btn-success');
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.replace('btn-success', 'btn-outline-secondary');
            }, 2000);
        });
    });

    // Event listeners for filters
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadOrders(1), 350);
    });

    statusFilter.addEventListener('change', () => loadOrders(1));
    dateFilter.addEventListener('change', () => loadOrders(1));
    perPageSelect.addEventListener('change', () => loadOrders(1));
    btnRefresh.addEventListener('click', () => loadOrders(currentPage));

    btnClearDate.addEventListener('click', function () {
        dateFilter.value = '';
        loadOrders(1);
    });

    // Initial load
    loadOrders(1);
});
</script>
@endpush
@endsection
