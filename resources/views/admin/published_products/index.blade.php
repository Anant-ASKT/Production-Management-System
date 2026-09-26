@extends('layouts.app')

@section('title', 'Published Products Across Stores')

@section('content')
<div class="container-fluid py-4 px-md-4">

    {{-- PAGE HEADER & ACTION BAR --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success p-2 rounded-3 border border-success-subtle">
                    <i class="bi bi-cloud-check-fill fs-5"></i>
                </span>
                <h4 class="fw-bold mb-0 text-dark">Published Products</h4>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold border border-primary-subtle ms-2" id="totalRecordsBadge">
                    <span id="totalCountDisplay">{{ $totalPublished }}</span> Live Publications
                </span>
            </div>
            <p class="text-muted small mb-0">
                Centralized registry of products published across multiple supplier store websites and WooCommerce domains.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.publish-products.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs hover-elevate">
                <i class="bi bi-send-plus me-1.5"></i> Publish New Product
            </a>
            <a href="{{ route('admin.update-products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs hover-elevate">
                <i class="bi bi-arrow-repeat me-1.5"></i> Stock & Price Sync
            </a>
            <button class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-xs hover-elevate" id="btnRefresh" title="Refresh Table">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- METRIC SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 hover-elevate border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Publications</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $totalPublished }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Across all connected stores</div>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-4">
                        <i class="bi bi-shop-window fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 hover-elevate border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Unique Garment SKUs</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $totalUniqueProducts }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Distinct design specifications</div>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-4">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 hover-elevate border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Connected Stores</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $totalStores }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Active supplier websites</div>
                    </div>
                    <div class="p-3 bg-info-subtle text-info rounded-4">
                        <i class="bi bi-globe2 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 hover-elevate border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Multi-Store Publishing</div>
                        <div class="fs-4 fw-bold text-dark mt-1">Multi-Domain</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Cross-supplier syndication</div>
                    </div>
                    <div class="p-3 bg-warning-subtle text-warning rounded-4">
                        <i class="bi bi-diagram-3-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TARGET SUPPLIER STORE TABS --}}
    <div class="d-flex align-items-center gap-2 mb-3 overflow-x-auto pb-1" id="storeTabsContainer" style="scrollbar-width: thin;">
        <button type="button" class="btn btn-sm rounded-pill px-3.5 py-1.5 fw-semibold store-tab-btn active shadow-xs" data-target-store="">
            <i class="bi bi-grid-fill me-1.5"></i> All Stores 
            <span class="badge bg-white text-primary rounded-pill ms-1.5 border">{{ $totalPublished }}</span>
        </button>

        @foreach($targetStores as $store)
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5 py-1.5 fw-semibold store-tab-btn bg-white shadow-xs" data-target-store="{{ $store->sno }}">
                <i class="bi bi-shop me-1.5 text-primary"></i> {{ $store->name }}
                <span class="badge bg-primary-subtle text-primary rounded-pill ms-1.5">{{ $store->total_published }}</span>
            </button>
        @endforeach
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            id="publishedSearch"
                            class="form-control border-start-0 ps-2"
                            placeholder="Search by title, SKU, barcode, store domain, WooCommerce ID..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Target Supplier Filter --}}
                <div class="col-md-3 col-lg-3">
                    <select id="targetSupplierFilter" class="form-select form-select-sm rounded-3">
                        <option value="">-- All Target Stores --</option>
                        @foreach($suppliers as $s)
                            @if(!empty($s->store_url))
                                <option value="{{ $s->sno }}">{{ $s->name }} ({{ parse_url($s->store_url, PHP_URL_HOST) ?: $s->store_url }})</option>
                            @else
                                <option value="{{ $s->sno }}">{{ $s->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Origin Supplier Filter --}}
                <div class="col-md-2 col-lg-2">
                    <select id="originSupplierFilter" class="form-select form-select-sm rounded-3">
                        <option value="">-- Origin Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->sno }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-lg-2 d-flex align-items-center justify-content-md-end gap-2">
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

    {{-- MAIN PUBLISHED PRODUCTS TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="publishedTable">
                    <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.73rem;">
                        <tr>
                            <th style="width: 55px;" class="ps-3 text-center">#</th>
                            <th style="width: 75px;" class="text-center">Photo</th>
                            <th style="min-width: 240px;">Product Name & Master Type</th>
                            <th style="min-width: 170px;">Target Website Store</th>
                            <th style="min-width: 130px;">Category</th>
                            <th style="min-width: 140px;">SKU / Barcode</th>
                            <th style="min-width: 120px;">Price & Sale</th>
                            <th style="min-width: 110px;">WooCommerce ID</th>
                            <th style="min-width: 120px;">Multi-Store Sync</th>
                            <th style="min-width: 110px;">Date</th>
                            <th style="width: 160px;" class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="publishedTableBody">
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Loading published products across stores...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CARD FOOTER / PAGINATION --}}
        <div class="card-footer bg-white border-top py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="small text-muted" id="paginationSummary">
                Showing 0 to 0 of 0 products
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0 rounded-pill" id="paginationControls">
                    {{-- Generated via JS --}}
                </ul>
            </nav>
        </div>
    </div>

</div>

{{-- UNPUBLISH CONFIRMATION MODAL --}}
<div class="modal fade" id="unpublishModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="bi bi-trash3-fill fs-1"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Remove from Published?</h6>
                <p class="text-muted small mb-3">
                    This will remove the publication record from the ERP registry for this store.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-3 btn-sm" id="btnConfirmDelete">Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .fs-7 { font-size: 0.8rem; }
    .fs-8 { font-size: 0.72rem; }
    .hover-elevate { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .hover-elevate:hover { transform: translateY(-1.5px); box-shadow: 0 5px 12px rgba(0,0,0,0.08); }
    .store-tab-btn.active {
        background-color: #2b5288 !important;
        color: #ffffff !important;
        border-color: #2b5288 !important;
    }
    .store-tab-btn.active .badge {
        background-color: #ffffff !important;
        color: #2b5288 !important;
    }
</style>

{{-- JAVASCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentSearch = '';
    let currentTargetSupplier = '';
    let currentOriginSupplier = '';
    let currentPerPage = 20;
    let debounceTimer = null;
    let pendingDeleteId = null;

    const tableBody = document.getElementById('publishedTableBody');
    const searchInput = document.getElementById('publishedSearch');
    const targetSupplierFilter = document.getElementById('targetSupplierFilter');
    const originSupplierFilter = document.getElementById('originSupplierFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const paginationSummary = document.getElementById('paginationSummary');
    const paginationControls = document.getElementById('paginationControls');
    const totalCountDisplay = document.getElementById('totalCountDisplay');
    const btnRefresh = document.getElementById('btnRefresh');
    const storeTabButtons = document.querySelectorAll('.store-tab-btn');
    const unpublishModalEl = document.getElementById('unpublishModal');
    const unpublishModal = unpublishModalEl ? new bootstrap.Modal(unpublishModalEl) : null;
    const btnConfirmDelete = document.getElementById('btnConfirmDelete');

    function formatImgUrl(path) {
        if (!path) return '/assets/images/placeholder.png';
        path = path.trim().replace(/\\/g, '/');
        if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('data:')) {
            return path;
        }
        if (path.startsWith('/')) {
            return path;
        }
        return '/' + path;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCurrency(val) {
        if (!val || isNaN(val)) return '—';
        return '₹' + parseFloat(val).toLocaleString('en-IN');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function loadPublishedProducts(page = 1) {
        currentPage = page;
        tableBody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading published products...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: currentPage,
            per_page: currentPerPage,
            search: currentSearch,
            target_supplier_id: currentTargetSupplier,
            origin_supplier_id: currentOriginSupplier
        });

        fetch(`{{ route('admin.published-products.data') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(res => {
            if (!res.success) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="11" class="text-center py-5 text-danger">
                            <i class="bi bi-exclamation-circle me-1"></i> ${escapeHtml(res.message || 'Failed to load data.')}
                        </td>
                    </tr>
                `;
                return;
            }

            renderTable(res.data);
            renderPagination(res);
            if (totalCountDisplay) {
                totalCountDisplay.textContent = res.total;
            }
        })
        .catch(err => {
            console.error('Error fetching published products data:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-5 text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i> An error occurred while fetching data.
                    </td>
                </tr>
            `;
        });
    }

    function renderTable(items) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        No published products found matching criteria.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach((item, index) => {
            const srNo = ((currentPage - 1) * currentPerPage) + (index + 1);
            const imgUrl = formatImgUrl(item.main_image);
            const productName = item.clean_title || item.AI_product_name || item.product_name || 'Untitled Product';
            const categoryName = item.category_name || item.published_category_name || item.product_type || 'General';
            const detailUrl = `{{ url('/admin/published-products') }}/${item.published_id}`;
            const publishUrl = `{{ url('/admin/publish-products') }}/${item.specification_id}`;

            // Multi-store indicator badges
            let multiStoreBadge = '';
            if (item.total_stores_count > 1) {
                const otherCount = item.total_stores_count - 1;
                const otherListHtml = (item.other_stores || []).map(os => `
                    <div class="small py-1 border-bottom text-start">
                        <i class="bi bi-shop me-1 text-primary"></i><strong>${escapeHtml(os.store_name)}</strong><br>
                        <span class="text-muted fs-8 font-monospace">WC ID: #${os.woocommerce_product_id || '—'}</span>
                    </div>
                `).join('');

                multiStoreBadge = `
                    <div class="d-inline-block">
                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2.5 py-1 fw-semibold" 
                              style="background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff; font-size: 0.72rem; cursor: pointer;"
                              title="Published to ${item.total_stores_count} stores">
                            <i class="bi bi-diagram-3-fill me-1"></i>${item.total_stores_count} Stores Live
                        </span>
                    </div>
                `;
            } else {
                multiStoreBadge = `
                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-0.5 fs-8">
                        Single Store
                    </span>
                `;
            }

            html += `
                <tr>
                    {{-- 1. Sr No --}}
                    <td class="ps-3 text-center text-muted fw-semibold font-monospace fs-8">
                        ${srNo}
                    </td>

                    {{-- 2. Image --}}
                    <td class="text-center">
                        <a href="${detailUrl}" class="d-inline-block text-decoration-none position-relative">
                            <div class="rounded-3 overflow-hidden border shadow-xs bg-light position-relative" style="width: 58px; height: 58px;">
                                <img src="${imgUrl}" 
                                     class="w-100 h-100 object-fit-contain p-1" 
                                     alt="${escapeHtml(productName)}"
                                     onerror="this.src='/assets/images/placeholder.png';">
                                ${item.total_images > 1 ? `
                                    <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75 text-white p-0.5 px-1 rounded-1" style="font-size: 0.6rem;">
                                        ${item.total_images}
                                    </span>
                                ` : ''}
                            </div>
                        </a>
                    </td>

                    {{-- 3. Product Name & Type --}}
                    <td>
                        <a href="${detailUrl}" class="fw-bold text-dark text-decoration-none fs-7 hover-primary d-block mb-1">
                            ${escapeHtml(productName)}
                        </a>
                        <div class="d-flex align-items-center gap-2 text-muted fs-8">
                            <span><i class="bi bi-tag text-muted me-1"></i>${escapeHtml(item.product_type || 'Garment')}</span>
                            ${item.origin_supplier_name ? `<span>• Origin: <strong class="text-secondary">${escapeHtml(item.origin_supplier_name)}</strong></span>` : ''}
                        </div>
                    </td>

                    {{-- 4. Target Website Store --}}
                    <td>
                        <div class="d-flex align-items-center gap-1.5 mb-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold fs-8">
                                <i class="bi bi-shop me-1"></i>${escapeHtml(item.target_supplier_name || 'Store')}
                            </span>
                        </div>
                        <div class="font-monospace text-muted fs-8 text-truncate" style="max-width: 170px;" title="${escapeHtml(item.target_store_url || '')}">
                            <i class="bi bi-link-45deg me-0.5"></i>${escapeHtml(item.target_domain || '—')}
                        </div>
                    </td>

                    {{-- 5. Store Category --}}
                    <td>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fs-8">
                            ${escapeHtml(categoryName)}
                        </span>
                    </td>

                    {{-- 6. SKU / Barcode --}}
                    <td>
                        <div class="font-monospace fw-bold text-dark fs-7">${escapeHtml(item.sku || '—')}</div>
                        ${item.barcode ? `<div class="font-monospace text-muted fs-8"><i class="bi bi-upc-scan me-1"></i>${escapeHtml(item.barcode)}</div>` : ''}
                    </td>

                    {{-- 7. Price & Sale Price --}}
                    <td>
                        <div class="fw-bold text-success fs-7">
                            ${formatCurrency(item.sale_price || item.price)}
                        </div>
                        ${(item.sale_price && item.price && item.price > item.sale_price) ? `
                            <div class="text-muted text-decoration-line-through fs-8">${formatCurrency(item.price)}</div>
                        ` : ''}
                    </td>

                    {{-- 8. WooCommerce ID --}}
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 font-monospace fs-8">
                            <i class="bi bi-check2-circle me-0.5"></i>#${escapeHtml(item.woocommerce_product_id || '—')}
                        </span>
                    </td>

                    {{-- 9. Multi-Store Sync --}}
                    <td>
                        ${multiStoreBadge}
                    </td>

                    {{-- 10. Date --}}
                    <td>
                        <small class="text-muted fs-8">${formatDate(item.last_updated_at || item.published_at)}</small>
                    </td>

                    {{-- 11. Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- View Details --}}
                            <a href="${detailUrl}" class="btn btn-sm btn-dark rounded-pill px-2.5 py-1 shadow-xs hover-elevate" title="View Full Publication Details">
                                <i class="bi bi-eye me-1"></i> Details
                            </a>

                            {{-- Live Link on Store --}}
                            ${item.permalink ? `
                                <a href="${item.permalink}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 shadow-xs hover-elevate" title="Open Live WooCommerce Store Page">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            ` : ''}

                            {{-- Publish to Another Store / Manage --}}
                            <a href="${publishUrl}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 shadow-xs hover-elevate" title="Publish to Another Store">
                                <i class="bi bi-send-plus"></i>
                            </a>

                            {{-- Remove publication --}}
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 shadow-xs hover-elevate btn-delete-publication" 
                                    data-id="${item.published_id}" title="Remove publication record">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Attach delete listeners
        document.querySelectorAll('.btn-delete-publication').forEach(btn => {
            btn.addEventListener('click', function() {
                pendingDeleteId = this.getAttribute('data-id');
                if (unpublishModal) {
                    unpublishModal.show();
                }
            });
        });
    }

    function renderPagination(res) {
        paginationSummary.textContent = `Showing ${res.from || 0} to ${res.to || 0} of ${res.total} products`;

        if (res.last_page <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let html = '';

        html += `
            <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${res.current_page - 1}">&laquo;</a>
            </li>
        `;

        for (let i = 1; i <= res.last_page; i++) {
            if (i === 1 || i === res.last_page || (i >= res.current_page - 1 && i <= res.current_page + 1)) {
                html += `
                    <li class="page-item ${i === res.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === res.current_page - 2 || i === res.current_page + 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        html += `
            <li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${res.current_page + 1}">&raquo;</a>
            </li>
        `;

        paginationControls.innerHTML = html;

        paginationControls.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    loadPublishedProducts(page);
                }
            });
        });
    }

    // Store Tab Buttons
    storeTabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            storeTabButtons.forEach(b => {
                b.classList.remove('active');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.add('active');
            this.classList.remove('btn-outline-secondary');

            const storeId = this.getAttribute('data-target-store');
            currentTargetSupplier = storeId;
            targetSupplierFilter.value = storeId;
            loadPublishedProducts(1);
        });
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentSearch = this.value.trim();
            loadPublishedProducts(1);
        }, 350);
    });

    targetSupplierFilter.addEventListener('change', function() {
        currentTargetSupplier = this.value;
        // Update tabs active state
        storeTabButtons.forEach(b => {
            if (b.getAttribute('data-target-store') === currentTargetSupplier) {
                b.classList.add('active');
                b.classList.remove('btn-outline-secondary');
            } else {
                b.classList.remove('active');
                b.classList.add('btn-outline-secondary');
            }
        });
        loadPublishedProducts(1);
    });

    originSupplierFilter.addEventListener('change', function() {
        currentOriginSupplier = this.value;
        loadPublishedProducts(1);
    });

    perPageSelect.addEventListener('change', function() {
        currentPerPage = parseInt(this.value);
        loadPublishedProducts(1);
    });

    btnRefresh.addEventListener('click', function() {
        loadPublishedProducts(currentPage);
    });

    // Confirm Delete / Unpublish
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function() {
            if (!pendingDeleteId) return;

            btnConfirmDelete.disabled = true;
            btnConfirmDelete.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Removing...`;

            fetch(`{{ url('/admin/published-products') }}/${pendingDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                btnConfirmDelete.disabled = false;
                btnConfirmDelete.textContent = 'Remove';
                if (unpublishModal) {
                    unpublishModal.hide();
                }

                if (res.success) {
                    loadPublishedProducts(currentPage);
                } else {
                    alert(res.message || 'Failed to remove record.');
                }
            })
            .catch(err => {
                console.error(err);
                btnConfirmDelete.disabled = false;
                btnConfirmDelete.textContent = 'Remove';
                if (unpublishModal) {
                    unpublishModal.hide();
                }
                alert('An error occurred.');
            });
        });
    }

    // Initial Load
    loadPublishedProducts(1);
});
</script>
@endsection
