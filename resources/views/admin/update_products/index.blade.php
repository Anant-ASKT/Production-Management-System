@extends('layouts.app')

@section('title', 'Update Product - Stock & Price Sync')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center text-dark">
                <span class="badge bg-primary-subtle text-primary p-2 rounded-3 me-2">
                    <i class="bi bi-pencil-square fs-5"></i>
                </span>
                Update Product (Stock & Price)
            </h4>
            <p class="text-muted small mb-0">
                Update stock levels and pricing requested by suppliers via WhatsApp, call, or email, syncing automatically to both WordPress and ERP.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.published-products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-bag-check me-1"></i> Published Products
            </a>
            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs" id="btnRefreshAll" title="Refresh List">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- STATS METRIC CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Published Products</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1" id="statTotalPublished">{{ $totalPublished }}</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Updates Logged</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1" id="statTotalLogs">{{ $totalLogs }}</h3>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">WhatsApp Requests</div>
                        <h3 class="fw-bold text-success mb-0 mt-1" id="statWhatsappLogs">{{ $whatsappLogs }}</h3>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Phone Call Requests</div>
                        <h3 class="fw-bold text-warning mb-0 mt-1" id="statPhoneLogs">{{ $phoneLogs }}</h3>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-telephone fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS NAVIGATION --}}
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm" id="updateTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-semibold" id="tab-products-btn" data-bs-toggle="pill" data-bs-target="#tab-products" type="button" role="tab">
                <i class="bi bi-grid-3x3-gap me-1"></i> Published Products List
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-semibold" id="tab-logs-btn" data-bs-toggle="pill" data-bs-target="#tab-logs" type="button" role="tab">
                <i class="bi bi-clock-history me-1"></i> Update History & Logs
            </button>
        </li>
    </ul>

    <div class="tab-content" id="updateTabsContent">

        {{-- ============================================================ --}}
        {{-- TAB 1: PUBLISHED PRODUCTS (QUICK UPDATE) --}}
        {{-- ============================================================ --}}
        <div class="tab-pane fade show active" id="tab-products" role="tabpanel">

            {{-- FILTERS CARD --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        {{-- Search --}}
                        <div class="col-md-4 col-lg-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    type="text"
                                    id="productSearchInput"
                                    class="form-control border-start-0 ps-2"
                                    placeholder="Search by SKU, barcode, title, supplier..."
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        {{-- Target Store Filter --}}
                        <div class="col-md-3 col-lg-3">
                            <select id="targetSupplierFilter" class="form-select form-select-sm rounded-3">
                                <option value="">-- All Target Stores --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->sno }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Stock Status Filter --}}
                        <div class="col-md-3 col-lg-2">
                            <select id="stockFilter" class="form-select form-select-sm rounded-3">
                                <option value="all">All Stock Status</option>
                                <option value="in_stock">In Stock (> 0)</option>
                                <option value="low_stock">Low Stock (1-5)</option>
                                <option value="out_of_stock">Out of Stock (0)</option>
                            </select>
                        </div>

                        {{-- Per Page --}}
                        <div class="col-md-2 col-lg-2 d-flex align-items-center justify-content-md-end gap-2">
                            <label for="perPageSelect" class="small text-muted text-nowrap fw-semibold">Show:</label>
                            <select id="perPageSelect" class="form-select form-select-sm w-auto rounded-3">
                                <option value="10">10</option>
                                <option value="15" selected>15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PRODUCTS TABLE CARD --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="productsTable">
                            <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                                <tr>
                                    <th style="width: 50px;" class="ps-3 text-center">#</th>
                                    <th style="width: 80px;" class="text-center">Image</th>
                                    <th style="min-width: 240px;">Product & Details</th>
                                    <th style="min-width: 150px;">Store / Supplier</th>
                                    <th style="min-width: 140px;">SKU & Barcode</th>
                                    <th style="min-width: 130px;">Price (₹)</th>
                                    <th style="min-width: 110px;" class="text-center">Stock</th>
                                    <th style="min-width: 160px;">Last Update</th>
                                    <th style="min-width: 170px;" class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        Loading published products...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination Footer --}}
                <div class="card-footer bg-white border-top d-flex flex-wrap align-items-center justify-content-between p-3 gap-2" id="productsPaginationFooter" style="display: none !important;">
                    <div class="text-muted small" id="productsPaginationInfo">Showing 0 to 0 of 0 items</div>
                    <nav><ul class="pagination pagination-sm mb-0 rounded-pill" id="productsPaginationNav"></ul></nav>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB 2: AUDIT LOGS & UPDATE HISTORY --}}
        {{-- ============================================================ --}}
        <div class="tab-pane fade" id="tab-logs" role="tabpanel">

            {{-- LOGS FILTERS CARD --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    type="text"
                                    id="logsSearchInput"
                                    class="form-control border-start-0 ps-2"
                                    placeholder="Search logs by SKU, barcode, admin or remarks..."
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        <div class="col-md-4">
                            <select id="logsChannelFilter" class="form-select form-select-sm rounded-3">
                                <option value="">-- All Channels --</option>
                                <option value="whatsapp">💬 WhatsApp</option>
                                <option value="phone_call">📞 Phone Call</option>
                                <option value="email">✉️ Email</option>
                                <option value="other">📝 Other / In-Person</option>
                            </select>
                        </div>

                        <div class="col-md-3 text-md-end">
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btnRefreshLogs">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LOGS TABLE CARD --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="logsTable">
                            <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                                <tr>
                                    <th style="width: 50px;" class="ps-3 text-center">#</th>
                                    <th style="min-width: 140px;">Date & Time</th>
                                    <th style="min-width: 140px;">SKU / Barcode</th>
                                    <th style="min-width: 110px;">Channel</th>
                                    <th style="min-width: 140px;">Price Change</th>
                                    <th style="min-width: 130px;">Stock Change</th>
                                    <th style="min-width: 200px;">Supplier Notes / Remark</th>
                                    <th style="min-width: 120px;">WP Sync</th>
                                    <th style="min-width: 110px;" class="pe-3">Admin</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        Loading update logs...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Logs Pagination Footer --}}
                <div class="card-footer bg-white border-top d-flex flex-wrap align-items-center justify-content-between p-3 gap-2" id="logsPaginationFooter" style="display: none !important;">
                    <div class="text-muted small" id="logsPaginationInfo">Showing 0 to 0 of 0 logs</div>
                    <nav><ul class="pagination pagination-sm mb-0 rounded-pill" id="logsPaginationNav"></ul></nav>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ============================================================ --}}
{{-- MODAL: QUICK UPDATE PRODUCT (STOCK & PRICE) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-light px-4 py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="updateProductModalLabel">Update Product Stock & Price</h5>
                        <small class="text-muted">Sync changes to WordPress Store and ERP Inventory</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="updateProductForm">
                <input type="hidden" id="modalPublishedId" name="published_id">

                <div class="modal-body p-4">
                    {{-- PRODUCT HEADER BANNER --}}
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 mb-4 border">
                        <div id="modalProductImageContainer" style="width: 70px; height: 70px; flex-shrink: 0;" class="bg-white rounded-3 d-flex align-items-center justify-content-center overflow-hidden border">
                            <img id="modalProductImage" src="" alt="Product" class="w-100 h-100 object-fit-cover" style="display: none;">
                            <i id="modalProductNoImage" class="bi bi-image text-muted fs-3"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="fw-bold text-dark text-truncate mb-1" id="modalProductName">—</h6>
                            <div class="d-flex flex-wrap align-items-center gap-2 small">
                                <span class="badge bg-secondary-subtle text-dark border">
                                    SKU: <strong id="modalProductSku">—</strong>
                                </span>
                                <span class="badge bg-secondary-subtle text-dark border">
                                    Barcode: <strong id="modalProductBarcode">—</strong>
                                </span>
                                <span class="badge bg-info-subtle text-info border" id="modalProductStore">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- PRICING & STOCK INPUTS --}}
                    <div class="row g-3 mb-3">
                        {{-- Regular Price --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Regular Price (₹) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control fw-bold" id="modalRegularPrice" name="regular_price" required placeholder="e.g. 4000.00">
                            </div>
                            <div class="form-text small text-muted">Current: <span id="currentRegularPriceDisplay">₹0</span></div>
                        </div>

                        {{-- Sale Price --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Sale Price (₹) <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="modalSalePrice" name="sale_price" placeholder="Leave empty if none">
                            </div>
                            <div class="form-text small text-muted">Current: <span id="currentSalePriceDisplay">—</span></div>
                        </div>

                        {{-- Stock Quantity --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Stock Quantity <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-boxes text-muted"></i></span>
                                <input type="number" step="1" min="0" class="form-control fw-bold" id="modalStockQuantity" name="stock_quantity" required placeholder="e.g. 10">
                            </div>
                            <div class="form-text small text-muted">Current ERP Stock: <strong id="currentStockDisplay" class="text-primary">0</strong> units</div>
                        </div>
                    </div>

                    {{-- COMMUNICATION CHANNEL --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Supplier Communication Channel <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            <div class="col-6 col-sm-3">
                                <input type="radio" class="btn-check" name="source_channel" id="channelWhatsapp" value="whatsapp" checked>
                                <label class="btn btn-outline-success w-100 rounded-3 py-2 text-nowrap d-flex align-items-center justify-content-center gap-1" for="channelWhatsapp">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </label>
                            </div>
                            <div class="col-6 col-sm-3">
                                <input type="radio" class="btn-check" name="source_channel" id="channelPhone" value="phone_call">
                                <label class="btn btn-outline-warning w-100 rounded-3 py-2 text-nowrap d-flex align-items-center justify-content-center gap-1" for="channelPhone">
                                    <i class="bi bi-telephone"></i> Phone Call
                                </label>
                            </div>
                            <div class="col-6 col-sm-3">
                                <input type="radio" class="btn-check" name="source_channel" id="channelEmail" value="email">
                                <label class="btn btn-outline-info w-100 rounded-3 py-2 text-nowrap d-flex align-items-center justify-content-center gap-1" for="channelEmail">
                                    <i class="bi bi-envelope"></i> Email
                                </label>
                            </div>
                            <div class="col-6 col-sm-3">
                                <input type="radio" class="btn-check" name="source_channel" id="channelOther" value="other">
                                <label class="btn btn-outline-secondary w-100 rounded-3 py-2 text-nowrap d-flex align-items-center justify-content-center gap-1" for="channelOther">
                                    <i class="bi bi-chat-left-text"></i> Other
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- SUPPLIER REQUEST / REASON REMARKS --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Supplier Remarks / Reason for Update <span class="text-muted fw-normal">(Log Record)</span>
                        </label>
                        <textarea
                            class="form-control rounded-3"
                            id="modalReasonNotes"
                            name="reason_notes"
                            rows="2"
                            placeholder="e.g., Supplier Ramesh asked on WhatsApp to set stock to 15 after fresh shipment arrival..."
                        ></textarea>
                    </div>

                    {{-- SYNC TO WORDPRESS TOGGLE --}}
                    <div class="form-check form-switch p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <label class="form-check-label fw-bold text-dark d-block" for="modalSyncWc">
                                <i class="bi bi-wordpress text-primary me-1"></i> Sync immediately to WordPress WooCommerce Store
                            </label>
                            <small class="text-muted">Updates WooCommerce REST API stock & price automatically</small>
                        </div>
                        <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" id="modalSyncWc" name="sync_woocommerce" value="1" checked>
                    </div>

                    {{-- STATUS NOTIFICATION ALERT --}}
                    <div id="modalAlertBox" class="mt-3" style="display: none;"></div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs d-flex align-items-center gap-2" id="btnSubmitUpdate">
                        <span id="btnSubmitSpinner" class="spinner-border spinner-border-sm" style="display: none;" role="status"></span>
                        <i class="bi bi-cloud-arrow-up" id="btnSubmitIcon"></i>
                        <span>Save & Sync to WordPress</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: SINGLE PRODUCT AUDIT HISTORY --}}
{{-- ============================================================ --}}
<div class="modal fade" id="productHistoryModal" tabindex="-1" aria-labelledby="productHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-light px-4 py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info-subtle text-info p-2 rounded-3">
                        <i class="bi bi-clock-history fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="productHistoryModalLabel">Update History</h5>
                        <small class="text-muted" id="historyModalSubtitle">Audit trail of stock & price adjustments</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small text-uppercase" style="font-size: 0.72rem;">
                            <tr>
                                <th style="width: 140px;">Date & Time</th>
                                <th style="width: 110px;">Channel</th>
                                <th style="width: 130px;">Price (₹)</th>
                                <th style="width: 120px;">Stock</th>
                                <th>Remarks / Notes</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Admin</th>
                            </tr>
                        </thead>
                        <tbody id="productHistoryTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Loading history...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-2 border-top">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .shadow-xs { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
    .nav-pills .nav-link { color: #64748b; background: transparent; transition: all 0.2s ease; }
    .nav-pills .nav-link.active { background-color: #0d6efd; color: #fff; box-shadow: 0 2px 6px rgba(13, 110, 253, 0.35); }
    .product-thumb { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .channel-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 11px; }
    .channel-whatsapp { background: #dcfce7; color: #166534; }
    .channel-phone { background: #fef9c3; color: #854d0e; }
    .channel-email { background: #e0f2fe; color: #075985; }
    .channel-other { background: #f1f5f9; color: #475569; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let logsPage = 1;
    let productsDataStore = {};

    const productsTableBody = document.getElementById('productsTableBody');
    const logsTableBody = document.getElementById('logsTableBody');
    const updateModal = new bootstrap.Modal(document.getElementById('updateProductModal'));
    const historyModal = new bootstrap.Modal(document.getElementById('productHistoryModal'));

    // Load initial products
    loadProducts(1);

    // Filters event listeners
    let searchTimeout = null;
    document.getElementById('productSearchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadProducts(1), 300);
    });

    document.getElementById('targetSupplierFilter').addEventListener('change', () => loadProducts(1));
    document.getElementById('stockFilter').addEventListener('change', () => loadProducts(1));
    document.getElementById('perPageSelect').addEventListener('change', () => loadProducts(1));
    document.getElementById('btnRefreshAll').addEventListener('click', function() {
        loadProducts(currentPage);
        loadLogs(logsPage);
    });

    // Logs Tab event listeners
    document.getElementById('tab-logs-btn').addEventListener('shown.bs.tab', function() {
        loadLogs(1);
    });
    document.getElementById('logsSearchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadLogs(1), 300);
    });
    document.getElementById('logsChannelFilter').addEventListener('change', () => loadLogs(1));
    document.getElementById('btnRefreshLogs').addEventListener('click', () => loadLogs(logsPage));

    // ============================================================
    // LOAD PRODUCTS DATA
    // ============================================================
    function loadProducts(page = 1) {
        currentPage = page;
        productsTableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading published products...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            search: document.getElementById('productSearchInput').value.trim(),
            target_supplier_id: document.getElementById('targetSupplierFilter').value,
            stock_filter: document.getElementById('stockFilter').value
        });

        fetch(`{{ route('admin.update-products.data') }}?${params.toString()}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                productsTableBody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">${data.message || 'Failed to load products.'}</td></tr>`;
                return;
            }

            renderProductsTable(data.data, data.from);
            renderProductsPagination(data);
        })
        .catch(err => {
            console.error(err);
            productsTableBody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">Error loading products. Please retry.</td></tr>`;
        });
    }

    function renderProductsTable(items, startIndex = 1) {
        if (!items || items.length === 0) {
            productsTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                        No published products found matching your search.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        productsDataStore = {};

        items.forEach((item, index) => {
            productsDataStore[item.published_id] = item;
            const srNo = startIndex ? (startIndex + index) : (index + 1);
            const regularPriceFormatted = item.current_regular_price ? '₹' + Number(item.current_regular_price).toLocaleString('en-IN') : '—';
            const salePriceFormatted = item.current_sale_price ? '₹' + Number(item.current_sale_price).toLocaleString('en-IN') : null;

            // Stock badge
            let stockBadge = '';
            if (item.current_stock > 5) {
                stockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-7 fw-bold">${item.current_stock} in stock</span>`;
            } else if (item.current_stock > 0) {
                stockBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-7 fw-bold">${item.current_stock} low stock</span>`;
            } else {
                stockBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-7 fw-bold">0 out of stock</span>`;
            }

            // Image
            let imgHtml = item.image_url 
                ? `<img src="${item.image_url}" alt="Product" class="product-thumb" onerror="this.outerHTML='<div class=\\'product-thumb d-flex align-items-center justify-content-center bg-light text-muted\\'><i class=\\'bi bi-image\\'></i></div>'">`
                : `<div class="product-thumb d-flex align-items-center justify-content-center bg-light text-muted"><i class="bi bi-image"></i></div>`;

            // Last Log Snippet
            let lastUpdateHtml = '<span class="text-muted small">No updates yet</span>';
            if (item.last_log) {
                let channelClass = 'channel-other';
                let channelIcon = 'bi-chat-dots';
                if (item.last_log.channel === 'whatsapp') { channelClass = 'channel-whatsapp'; channelIcon = 'bi-whatsapp'; }
                else if (item.last_log.channel === 'phone_call') { channelClass = 'channel-phone'; channelIcon = 'bi-telephone'; }
                else if (item.last_log.channel === 'email') { channelClass = 'channel-email'; channelIcon = 'bi-envelope'; }

                lastUpdateHtml = `
                    <div class="small">
                        <span class="channel-badge ${channelClass} mb-1">
                            <i class="bi ${channelIcon}"></i> ${item.last_log.channel}
                        </span>
                        <div class="text-muted text-nowrap" style="font-size: 11px;">${item.last_log.updated_at}</div>
                    </div>
                `;
            }

            html += `
                <tr>
                    <td class="ps-3 text-center text-muted fw-bold">${srNo}</td>
                    <td class="text-center">${imgHtml}</td>
                    <td>
                        <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="${escapeHtml(item.product_name)}">
                            ${escapeHtml(item.product_name)}
                        </div>
                        <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-light text-secondary border">${escapeHtml(item.product_type || 'Garment')}</span>
                            ${item.published_category_name ? `<span class="badge bg-light text-secondary border">${escapeHtml(item.published_category_name)}</span>` : ''}
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">${escapeHtml(item.target_supplier_name || 'Store')}</div>
                        ${item.target_store_url ? `<a href="${item.target_store_url}" target="_blank" class="text-muted small text-decoration-none"><i class="bi bi-link-45deg"></i> Store link</a>` : ''}
                    </td>
                    <td>
                        <div class="fw-bold text-primary">${escapeHtml(item.sku || '—')}</div>
                        <div class="small text-muted">${escapeHtml(item.barcode || '—')}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">${regularPriceFormatted}</div>
                        ${salePriceFormatted ? `<div class="small text-success fw-semibold"><i class="bi bi-tag-fill me-1"></i>Sale: ${salePriceFormatted}</div>` : ''}
                    </td>
                    <td class="text-center">
                        ${stockBadge}
                    </td>
                    <td>${lastUpdateHtml}</td>
                    <td class="text-end pe-3 text-nowrap">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs me-1 btn-open-update" data-id="${item.published_id}">
                            <i class="bi bi-pencil-square me-1"></i> Update
                        </button>
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-2 btn-open-history" data-id="${item.published_id}" title="View History Log">
                            <i class="bi bi-clock-history"></i>
                        </button>
                        ${item.permalink ? `
                            <a href="${item.permalink}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-2 ms-1" title="View on WordPress Website">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        ` : ''}
                    </td>
                </tr>
            `;
        });

        productsTableBody.innerHTML = html;

        // Attach buttons handlers
        document.querySelectorAll('.btn-open-update').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                openUpdateModal(id);
            });
        });

        document.querySelectorAll('.btn-open-history').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                openProductHistoryModal(id);
            });
        });
    }

    function renderProductsPagination(data) {
        const footer = document.getElementById('productsPaginationFooter');
        const info = document.getElementById('productsPaginationInfo');
        const nav = document.getElementById('productsPaginationNav');

        if (!data.total || data.total <= 0) {
            footer.style.setProperty('display', 'none', 'important');
            return;
        }

        footer.style.removeProperty('display');
        info.textContent = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} items`;

        let navHtml = '';
        if (data.current_page > 1) {
            navHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}">Previous</a></li>`;
        } else {
            navHtml += `<li class="page-item disabled"><span class="page-link">Previous</span></li>`;
        }

        for (let p = 1; p <= data.last_page; p++) {
            if (p === 1 || p === data.last_page || (p >= data.current_page - 2 && p <= data.current_page + 2)) {
                navHtml += `<li class="page-item ${p === data.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            } else if (p === data.current_page - 3 || p === data.current_page + 3) {
                navHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        if (data.current_page < data.last_page) {
            navHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next</a></li>`;
        } else {
            navHtml += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
        }

        nav.innerHTML = navHtml;
        nav.querySelectorAll('a.page-link').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage) loadProducts(targetPage);
            });
        });
    }

    // ============================================================
    // OPEN UPDATE MODAL & SUBMIT
    // ============================================================
    function openUpdateModal(publishedId) {
        const item = productsDataStore[publishedId];
        if (!item) return;

        document.getElementById('modalPublishedId').value = publishedId;
        document.getElementById('modalProductName').textContent = item.product_name || 'Product';
        document.getElementById('modalProductSku').textContent = item.sku || '—';
        document.getElementById('modalProductBarcode').textContent = item.barcode || '—';
        document.getElementById('modalProductStore').textContent = item.target_supplier_name || 'Store';

        // Pre-fill pricing & stock
        document.getElementById('modalRegularPrice').value = item.current_regular_price || '';
        document.getElementById('modalSalePrice').value = item.current_sale_price || '';
        document.getElementById('modalStockQuantity').value = item.current_stock ?? 0;

        document.getElementById('currentRegularPriceDisplay').textContent = item.current_regular_price ? '₹' + Number(item.current_regular_price).toLocaleString('en-IN') : '₹0';
        document.getElementById('currentSalePriceDisplay').textContent = item.current_sale_price ? '₹' + Number(item.current_sale_price).toLocaleString('en-IN') : 'None';
        document.getElementById('currentStockDisplay').textContent = item.current_stock ?? 0;

        // Reset notes and channel
        document.getElementById('modalReasonNotes').value = '';
        document.getElementById('channelWhatsapp').checked = true;
        document.getElementById('modalSyncWc').checked = true;

        // Alert box
        const alertBox = document.getElementById('modalAlertBox');
        alertBox.style.display = 'none';
        alertBox.innerHTML = '';

        // Image
        const img = document.getElementById('modalProductImage');
        const noImg = document.getElementById('modalProductNoImage');
        if (item.image_url) {
            img.src = item.image_url;
            img.style.display = 'block';
            noImg.style.display = 'none';
        } else {
            img.src = '';
            img.style.display = 'none';
            noImg.style.display = 'block';
        }

        updateModal.show();
    }

    // Submit Update Form
    document.getElementById('updateProductForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const publishedId = document.getElementById('modalPublishedId').value;
        const btnSubmit = document.getElementById('btnSubmitUpdate');
        const spinner = document.getElementById('btnSubmitSpinner');
        const icon = document.getElementById('btnSubmitIcon');
        const alertBox = document.getElementById('modalAlertBox');

        btnSubmit.disabled = true;
        spinner.style.display = 'inline-block';
        icon.style.display = 'none';
        alertBox.style.display = 'none';

        const formData = new FormData(this);

        fetch(`{{ url('/admin/update-products') }}/${publishedId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            spinner.style.display = 'none';
            icon.style.display = 'inline-block';

            if (!data.success) {
                alertBox.className = 'alert alert-danger rounded-3 small mt-3';
                alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message || 'Update failed.'}`;
                alertBox.style.display = 'block';
                return;
            }

            alertBox.className = 'alert alert-success rounded-3 small mt-3';
            alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${data.message}`;
            alertBox.style.display = 'block';

            // Refresh products table in background
            loadProducts(currentPage);

            // Increment stat counts
            const statTotalLogs = document.getElementById('statTotalLogs');
            if (statTotalLogs) statTotalLogs.textContent = parseInt(statTotalLogs.textContent || 0) + 1;

            const selectedChannel = document.querySelector('input[name="source_channel"]:checked')?.value;
            if (selectedChannel === 'whatsapp') {
                const statWhatsappLogs = document.getElementById('statWhatsappLogs');
                if (statWhatsappLogs) statWhatsappLogs.textContent = parseInt(statWhatsappLogs.textContent || 0) + 1;
            } else if (selectedChannel === 'phone_call') {
                const statPhoneLogs = document.getElementById('statPhoneLogs');
                if (statPhoneLogs) statPhoneLogs.textContent = parseInt(statPhoneLogs.textContent || 0) + 1;
            }

            setTimeout(() => {
                updateModal.hide();
            }, 1200);
        })
        .catch(err => {
            btnSubmit.disabled = false;
            spinner.style.display = 'none';
            icon.style.display = 'inline-block';
            console.error(err);
            alertBox.className = 'alert alert-danger rounded-3 small mt-3';
            alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> An unexpected error occurred. Please retry.';
            alertBox.style.display = 'block';
        });
    });

    // ============================================================
    // LOAD AUDIT LOGS
    // ============================================================
    function loadLogs(page = 1) {
        logsPage = page;
        logsTableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading update logs...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: page,
            per_page: 20,
            search: document.getElementById('logsSearchInput').value.trim(),
            channel: document.getElementById('logsChannelFilter').value
        });

        fetch(`{{ route('admin.update-products.logs') }}?${params.toString()}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                logsTableBody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">${data.message || 'Failed to load logs.'}</td></tr>`;
                return;
            }

            renderLogsTable(data.data);
            renderLogsPagination(data);
        })
        .catch(err => {
            console.error(err);
            logsTableBody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-danger">Error loading update logs.</td></tr>`;
        });
    }

    function renderLogsTable(logs) {
        if (!logs || logs.length === 0) {
            logsTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-3 d-block mb-2 text-secondary"></i>
                        No update logs recorded yet.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        logs.forEach((log, index) => {
            let channelClass = 'channel-other';
            let channelIcon = 'bi-chat-dots';
            if (log.source_channel === 'whatsapp') { channelClass = 'channel-whatsapp'; channelIcon = 'bi-whatsapp'; }
            else if (log.source_channel === 'phone_call') { channelClass = 'channel-phone'; channelIcon = 'bi-telephone'; }
            else if (log.source_channel === 'email') { channelClass = 'channel-email'; channelIcon = 'bi-envelope'; }

            // Price diff
            const oldP = log.old_regular_price ? '₹' + Number(log.old_regular_price).toLocaleString('en-IN') : '—';
            const newP = log.new_regular_price ? '₹' + Number(log.new_regular_price).toLocaleString('en-IN') : '—';
            const priceHtml = `<div class="small fw-semibold text-dark">${oldP} &rarr; <span class="text-primary">${newP}</span></div>`;

            // Stock diff
            const oldS = log.old_stock ?? 0;
            const newS = log.new_stock ?? 0;
            const diff = newS - oldS;
            let diffBadge = '';
            if (diff > 0) {
                diffBadge = `<span class="badge bg-success-subtle text-success ms-1">+${diff}</span>`;
            } else if (diff < 0) {
                diffBadge = `<span class="badge bg-danger-subtle text-danger ms-1">${diff}</span>`;
            }
            const stockHtml = `<div class="small fw-semibold text-dark">${oldS} &rarr; <span class="text-primary">${newS}</span> ${diffBadge}</div>`;

            // Sync status
            let syncHtml = '';
            if (log.sync_status === 'success') {
                syncHtml = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i> Synced</span>';
            } else if (log.sync_status === 'failed') {
                syncHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" title="${escapeHtml(log.sync_error || 'Sync error')}"><i class="bi bi-x-circle me-1"></i> Failed</span>`;
            } else {
                syncHtml = '<span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Local Only</span>';
            }

            html += `
                <tr>
                    <td class="ps-3 text-center text-muted fw-bold">${log.sno}</td>
                    <td class="small text-nowrap">${log.created_at}</td>
                    <td>
                        <div class="fw-bold text-dark">${escapeHtml(log.sku || '—')}</div>
                        <div class="small text-muted">${escapeHtml(log.barcode || '—')}</div>
                    </td>
                    <td>
                        <span class="channel-badge ${channelClass}">
                            <i class="bi ${channelIcon}"></i> ${log.source_channel}
                        </span>
                    </td>
                    <td>${priceHtml}</td>
                    <td>${stockHtml}</td>
                    <td>
                        <div class="small text-dark" style="max-width: 260px;">
                            ${escapeHtml(log.reason_notes || '—')}
                        </div>
                    </td>
                    <td>${syncHtml}</td>
                    <td class="small fw-semibold text-muted pe-3">${escapeHtml(log.updated_by_name || 'Admin')}</td>
                </tr>
            `;
        });

        logsTableBody.innerHTML = html;
    }

    function renderLogsPagination(data) {
        const footer = document.getElementById('logsPaginationFooter');
        const info = document.getElementById('logsPaginationInfo');
        const nav = document.getElementById('logsPaginationNav');

        if (!data.total || data.total <= 0) {
            footer.style.setProperty('display', 'none', 'important');
            return;
        }

        footer.style.removeProperty('display');
        info.textContent = `Showing page ${data.current_page} of ${data.last_page} (${data.total} total logs)`;

        let navHtml = '';
        if (data.current_page > 1) {
            navHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}">Previous</a></li>`;
        } else {
            navHtml += `<li class="page-item disabled"><span class="page-link">Previous</span></li>`;
        }

        for (let p = 1; p <= data.last_page; p++) {
            if (p === 1 || p === data.last_page || (p >= data.current_page - 2 && p <= data.current_page + 2)) {
                navHtml += `<li class="page-item ${p === data.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            }
        }

        if (data.current_page < data.last_page) {
            navHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next</a></li>`;
        } else {
            navHtml += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
        }

        nav.innerHTML = navHtml;
        nav.querySelectorAll('a.page-link').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage) loadLogs(targetPage);
            });
        });
    }

    // ============================================================
    // OPEN PRODUCT HISTORY MODAL
    // ============================================================
    function openProductHistoryModal(publishedId) {
        const item = productsDataStore[publishedId];
        const tableBody = document.getElementById('productHistoryTableBody');
        const subtitle = document.getElementById('historyModalSubtitle');

        subtitle.textContent = item ? `History for SKU: ${item.sku} (${item.product_name})` : 'Audit trail of adjustments';
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading history...</td></tr>`;

        historyModal.show();

        fetch(`{{ url('/admin/update-products') }}/${publishedId}/logs`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.data || data.data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> No past updates found for this product.</td></tr>`;
                return;
            }

            let html = '';
            data.data.forEach(log => {
                let channelClass = 'channel-other';
                let channelIcon = 'bi-chat-dots';
                if (log.source_channel === 'whatsapp') { channelClass = 'channel-whatsapp'; channelIcon = 'bi-whatsapp'; }
                else if (log.source_channel === 'phone_call') { channelClass = 'channel-phone'; channelIcon = 'bi-telephone'; }
                else if (log.source_channel === 'email') { channelClass = 'channel-email'; channelIcon = 'bi-envelope'; }

                const oldP = log.old_regular_price ? '₹' + Number(log.old_regular_price).toLocaleString('en-IN') : '—';
                const newP = log.new_regular_price ? '₹' + Number(log.new_regular_price).toLocaleString('en-IN') : '—';

                const oldS = log.old_stock ?? 0;
                const newS = log.new_stock ?? 0;
                const diff = newS - oldS;
                const diffBadge = diff > 0 ? `<span class="badge bg-success-subtle text-success">+${diff}</span>` : (diff < 0 ? `<span class="badge bg-danger-subtle text-danger">${diff}</span>` : '');

                const syncBadge = log.sync_status === 'success' 
                    ? '<span class="badge bg-success-subtle text-success">Synced</span>' 
                    : (log.sync_status === 'failed' ? '<span class="badge bg-danger-subtle text-danger">Failed</span>' : '<span class="badge bg-secondary-subtle text-secondary">Local</span>');

                html += `
                    <tr>
                        <td class="small text-nowrap">${log.created_at}</td>
                        <td>
                            <span class="channel-badge ${channelClass}">
                                <i class="bi ${channelIcon}"></i> ${log.source_channel}
                            </span>
                        </td>
                        <td class="small">${oldP} &rarr; <strong class="text-primary">${newP}</strong></td>
                        <td class="small">${oldS} &rarr; <strong class="text-primary">${newS}</strong> ${diffBadge}</td>
                        <td class="small">${escapeHtml(log.reason_notes || '—')}</td>
                        <td>${syncBadge}</td>
                        <td class="small text-muted">${escapeHtml(log.updated_by_name || 'Admin')}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load product history.</td></tr>`;
        });
    }

    function escapeHtml(value) {
        if (value == null) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
@endpush
@endsection
