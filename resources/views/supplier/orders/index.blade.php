@extends('layouts.supplier')

@section('title', 'Website Orders')
@section('page-title', 'Website Orders')

@section('content')
<div class="container-fluid p-0">

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
                All customer orders received online for your products.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-7 fw-semibold border border-success-subtle">
                <i class="bi bi-receipt me-1"></i> <span id="statTotalOrdersTop">{{ $totalOrders }}</span> Total Orders
            </span>
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" id="btnRefresh" title="Refresh Orders">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- TOP STAT CARDS (SIMPLE) --}}
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
                        <div class="text-muted small fw-semibold">New / Processing</div>
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
                        <div class="text-muted small fw-semibold">Pending</div>
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
                <div class="col-12 col-md-5 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            id="orderSearch"
                            class="form-control border-start-0 ps-2"
                            placeholder="Search by Order #, SKU, Item Name, City..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="col-6 col-md-3 col-lg-3">
                    <select id="statusFilter" class="form-select rounded-3">
                        <option value="all">All Orders</option>
                        <option value="processing">Processing (To Pack)</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="on-hold">On-Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                {{-- Date Filter --}}
                <div class="col-6 col-md-4 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent text-muted"><i class="bi bi-calendar3"></i></span>
                        <input type="date" id="dateFilter" class="form-control" title="Filter by date">
                        <button class="btn btn-outline-secondary" type="button" id="btnClearDate" title="Clear Date">&times;</button>
                    </div>
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
                            <th style="width: 140px;" class="ps-3">Order Number</th>
                            <th style="min-width: 160px;">Date & Website</th>
                            <th style="min-width: 320px;">Product & SKU</th>
                            <th style="min-width: 160px;">Delivery City</th>
                            <th style="min-width: 120px;">Status</th>
                            <th style="min-width: 130px;" class="text-end">Total Amount</th>
                            <th style="width: 130px;" class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Loading orders...
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
                    <!-- Injected by JS -->
                </ul>
            </nav>
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
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
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
    const btnRefresh = document.getElementById('btnRefresh');

    function getStatusBadge(status) {
        const s = (status || 'pending').toLowerCase();
        let bgClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
        let icon = 'bi-circle';
        let text = s;

        switch(s) {
            case 'completed':
                bgClass = 'bg-success text-white';
                icon = 'bi-check2-circle';
                text = 'Completed';
                break;
            case 'shipped':
                bgClass = 'bg-info text-dark';
                icon = 'bi-truck';
                text = 'Shipped';
                break;
            case 'processing':
                bgClass = 'bg-primary text-white';
                icon = 'bi-arrow-repeat';
                text = 'Processing';
                break;
            case 'on-hold':
                bgClass = 'bg-warning text-dark';
                icon = 'bi-pause-circle';
                text = 'On-Hold';
                break;
            case 'pending':
                bgClass = 'bg-info text-dark';
                icon = 'bi-hourglass-split';
                text = 'Pending';
                break;
            case 'cancelled':
                bgClass = 'bg-danger text-white';
                icon = 'bi-x-circle';
                text = 'Cancelled';
                break;
        }

        return `<span class="badge ${bgClass} rounded-pill px-3 py-1.5 text-capitalize fw-semibold fs-7">
                    <i class="bi ${icon} me-1"></i>${text}
                </span>`;
    }

    function loadOrders(page = 1) {
        currentPage = page;
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading orders...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: page,
            search: searchInput.value.trim(),
            status: statusFilter.value,
            date_from: dateFilter.value,
            date_to: dateFilter.value
        });

        fetch(`{{ route('supplier.orders.data') }}?${params.toString()}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${response.message || 'Error loading data.'}</td></tr>`;
                    return;
                }

                renderTable(response.data);
                renderPagination(response);
            })
            .catch(err => {
                console.error(err);
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Failed to connect to server.</td></tr>`;
            });
    }

    function renderTable(items) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="mb-2"><i class="bi bi-inbox fs-2 text-secondary"></i></div>
                        <h6 class="fw-semibold text-secondary">No Orders Found</h6>
                        <p class="small text-muted mb-0">No website orders match your filter.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach(order => {
            let itemsHtml = '';
            if (order.supplier_items && order.supplier_items.length > 0) {
                const firstItem = order.supplier_items[0];
                const thumb = firstItem.image ? `<img src="${firstItem.image}" class="order-img-thumb me-2" alt="Product">` : `<div class="order-img-thumb d-flex align-items-center justify-content-center me-2 text-muted"><i class="bi bi-bag fs-4"></i></div>`;
                const moreCount = order.items_count > 1 ? `<span class="badge bg-light text-secondary border ms-1">+${order.items_count - 1} more item${order.items_count > 2 ? 's' : ''}</span>` : '';

                const skuBadge = firstItem.sku && firstItem.sku !== '—' 
                    ? `<span class="badge bg-dark-subtle text-dark border font-monospace me-1"><i class="bi bi-upc me-1"></i>${firstItem.sku}</span>` 
                    : '';

                itemsHtml = `
                    <div class="d-flex align-items-center">
                        ${thumb}
                        <div class="lh-sm">
                            <div class="fw-bold text-dark fs-6" style="max-width: 280px;" title="${firstItem.name}">${firstItem.name}</div>
                            <div class="mt-1">${skuBadge}</div>
                            <div class="text-secondary small mt-1">
                                Quantity: <strong class="text-dark">${firstItem.quantity} Piece${firstItem.quantity > 1 ? 's' : ''}</strong> ${moreCount}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                itemsHtml = `<span class="text-muted small">No items detailed</span>`;
            }

            const storeBadge = order.selling_store_name 
                ? `<div class="badge bg-light text-secondary border mt-1" title="${order.selling_store_name}">
                    <i class="bi bi-shop me-1 text-primary"></i>${order.selling_store_name}
                   </div>` 
                : '';

            const trackingBadge = order.tracking_id
                ? `<div class="mt-1">
                    <span class="badge bg-primary-subtle text-primary border font-monospace" style="font-size:11px;" title="${order.courier_name || 'Courier'}: ${order.tracking_id}">
                        <i class="bi bi-truck me-1"></i>${order.tracking_id}
                    </span>
                   </div>`
                : '';

            const showUrl = `{{ url('/supplier/orders') }}/${order.id}`;

            html += `
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-dark fs-6">#${order.order_number}</div>
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark">${order.order_date}</div>
                        ${storeBadge}
                    </td>
                    <td>
                        ${itemsHtml}
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">${order.customer_name}</div>
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>${order.customer_location || '—'}</div>
                    </td>
                    <td>
                        ${getStatusBadge(order.status)}
                        ${trackingBadge}
                    </td>
                    <td class="text-end">
                        <div class="fw-bold text-primary fs-5">${order.supplier_total_formatted}</div>
                    </td>
                    <td class="text-end pe-3">
                        <a href="${showUrl}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs">
                            <i class="bi bi-eye me-1"></i> View Order
                        </a>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
    }

    function renderPagination(meta) {
        paginationSummary.textContent = `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total || 0} orders`;

        if (meta.last_page <= 1) {
            paginationNav.innerHTML = '';
            return;
        }

        let navHtml = '';

        navHtml += `
            <li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link rounded-2" href="javascript:void(0)" data-page="${meta.current_page - 1}">&laquo;</a>
            </li>
        `;

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

        navHtml += `
            <li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
                <a class="page-link rounded-2" href="javascript:void(0)" data-page="${meta.current_page + 1}">&raquo;</a>
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

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadOrders(1), 350);
    });

    statusFilter.addEventListener('change', () => loadOrders(1));
    dateFilter.addEventListener('change', () => loadOrders(1));
    btnRefresh.addEventListener('click', () => loadOrders(currentPage));
    btnClearDate.addEventListener('click', function () {
        dateFilter.value = '';
        loadOrders(1);
    });

    loadOrders(1);
});
</script>
@endpush
@endsection
