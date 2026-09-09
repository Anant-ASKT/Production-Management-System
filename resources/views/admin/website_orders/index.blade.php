@extends('layouts.app')

@section('title', 'Website Orders')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                    <i class="bi bi-cart-check fs-5"></i>
                </span>
                <h4 class="fw-bold text-dark mb-0">Website Orders</h4>
            </div>
            <p class="text-muted small mb-0">
                Track and manage customer orders received from connected WooCommerce stores.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-7 fw-semibold border border-success-subtle shadow-2xs">
                <i class="bi bi-receipt me-1"></i> <span id="statTotalOrdersTop">{{ $totalOrders }}</span> Orders
            </span>
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-2xs" id="btnRefresh" title="Refresh Orders">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- TOP KPI STAT CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Total Orders --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 border-top border-3 border-secondary shadow-sm rounded-3 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Total Orders</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0" id="statTotalOrders">{{ $totalOrders }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-light text-secondary">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Confirmed --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 border-top border-3 border-primary shadow-sm rounded-3 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-primary small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Order Confirmed</span>
                        <h3 class="fw-bold text-primary mt-1 mb-0" id="statConfirmedOrders">{{ $confirmedOrders }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-primary-subtle text-primary">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shipped --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 border-top border-3 border-info shadow-sm rounded-3 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-info-emphasis small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Shipped</span>
                        <h3 class="fw-bold text-info-emphasis mt-1 mb-0" id="statShippedOrders">{{ $shippedOrders }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-info-subtle text-info-emphasis">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivered --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 border-top border-3 border-success shadow-sm rounded-3 p-3 bg-white h-100 stat-hover-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-success small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Delivered</span>
                        <h3 class="fw-bold text-success mt-1 mb-0" id="statDeliveredOrders">{{ $deliveredOrders }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-success-subtle text-success">
                        <i class="bi bi-box2-heart fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER TOOLBAR --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                {{-- Search --}}
                <div class="col-12 col-lg-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            id="orderSearch"
                            class="form-control form-control-sm border-start-0 ps-2"
                            placeholder="Search Order #, Customer, Phone, SKU..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Supplier Filter --}}
                <div class="col-6 col-sm-4 col-lg-2">
                    <select id="supplierFilter" class="form-select form-select-sm rounded-2">
                        <option value="all">All Suppliers</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->sno }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-6 col-sm-4 col-lg-2">
                    <select id="statusFilter" class="form-select form-select-sm rounded-2">
                        <option value="all">All Statuses</option>
                        <option value="Order confirmed">Order confirmed</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>

                {{-- Date Filter --}}
                <div class="col-6 col-sm-4 col-lg-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar3"></i></span>
                        <input type="date" id="dateFilter" class="form-control form-control-sm" title="Filter by date">
                        <button class="btn btn-outline-secondary" type="button" id="btnClearDate" title="Clear Date">&times;</button>
                    </div>
                </div>

                {{-- Per page --}}
                <div class="col-6 col-lg-2 d-flex justify-content-end align-items-center gap-2">
                    <label for="perPageSelect" class="small text-muted text-nowrap fw-semibold">Show:</label>
                    <select id="perPageSelect" class="form-select form-select-sm w-auto rounded-2">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ORDERS TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="ordersTable">
                    <thead class="table-light text-secondary text-uppercase border-bottom" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="text-center py-3 text-nowrap" style="width: 45px;">#</th>
                            <th class="py-3 text-nowrap" style="min-width: 100px;">Order #</th>
                            <th class="py-3 text-nowrap" style="min-width: 140px;">Order Date</th>
                            <th class="py-3 text-nowrap" style="min-width: 160px;">Customer</th>
                            <th class="py-3 text-nowrap" style="min-width: 130px;">Supplier / Store</th>
                            <th class="py-3 text-nowrap" style="min-width: 180px;">Items</th>
                            <th class="py-3 text-nowrap" style="min-width: 110px;">Payment</th>
                            <th class="py-3 text-nowrap text-end" style="min-width: 100px;">Total</th>
                            <th class="py-3 text-nowrap text-center" style="min-width: 130px;">Status</th>
                            <th class="py-3 text-nowrap text-end pe-3" style="width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
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
                    <!-- Pagination links generated by JS -->
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
        box-shadow: 0 8px 24px rgba(15,23,42,.08) !important;
    }
    #ordersTable thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
    }
    #ordersTable tbody tr {
        transition: background-color .15s ease;
    }
    #ordersTable tbody tr:hover {
        background-color: #f8fafc;
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
    const supplierFilter = document.getElementById('supplierFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const btnClearDate = document.getElementById('btnClearDate');
    const perPageSelect = document.getElementById('perPageSelect');
    const btnRefresh = document.getElementById('btnRefresh');

    // Status Badge generator
    function getStatusBadge(status) {
        const s = (status || 'pending').toLowerCase();
        let bgClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
        let icon = 'bi-circle';
        let text = status || 'Pending';

        switch (s) {
            case 'order confirmed':
            case 'order_confirmed':
            case 'processing':
                bgClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                icon = 'bi-check2-circle';
                text = 'Order confirmed';
                break;
            case 'shipped':
                bgClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                icon = 'bi-truck';
                text = 'Shipped';
                break;
            case 'delivered':
            case 'completed':
                bgClass = 'bg-success-subtle text-success border border-success-subtle';
                icon = 'bi-box2-heart';
                text = 'Delivered';
                break;
            case 'on-hold':
                bgClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                icon = 'bi-pause-circle';
                text = 'On-Hold';
                break;
            case 'pending':
                bgClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                icon = 'bi-hourglass-split';
                text = 'Pending';
                break;
            case 'cancelled':
                bgClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                icon = 'bi-x-circle';
                text = 'Cancelled';
                break;
            case 'refunded':
                bgClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                icon = 'bi-arrow-counterclockwise';
                text = 'Refunded';
                break;
            case 'failed':
                bgClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                icon = 'bi-exclamation-triangle';
                text = 'Failed';
                break;
        }

        return `<span class="badge ${bgClass} rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                    <i class="bi ${icon}"></i> <span>${text}</span>
                </span>`;
    }

    // Load Orders Data
    function loadOrders(page = 1) {
        currentPage = page;
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading website orders...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: page,
            per_page: perPageSelect.value,
            search: searchInput.value.trim(),
            supplier_id: supplierFilter ? supplierFilter.value : 'all',
            status: statusFilter.value,
            date_from: dateFilter.value,
            date_to: dateFilter.value
        });

        fetch(`{{ route('admin.website-orders.data') }}?${params.toString()}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    tableBody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-danger">${response.message || 'Error loading data.'}</td></tr>`;
                    return;
                }

                // Update Stats if returned
                if (response.stats) {
                    const elTotal = document.getElementById('statTotalOrders');
                    const elTotalTop = document.getElementById('statTotalOrdersTop');
                    const elConfirmed = document.getElementById('statConfirmedOrders');
                    const elShipped = document.getElementById('statShippedOrders');
                    const elDelivered = document.getElementById('statDeliveredOrders');

                    if (elTotal) elTotal.textContent = response.stats.total_orders ?? 0;
                    if (elTotalTop) elTotalTop.textContent = response.stats.total_orders ?? 0;
                    if (elConfirmed) elConfirmed.textContent = response.stats.confirmed_orders ?? 0;
                    if (elShipped) elShipped.textContent = response.stats.shipped_orders ?? 0;
                    if (elDelivered) elDelivered.textContent = response.stats.delivered_orders ?? 0;
                }

                renderTable(response.data, response.per_page, response.current_page);
                renderPagination(response);
            })
            .catch(err => {
                console.error('Error fetching orders:', err);
                tableBody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-danger">Failed to connect to server.</td></tr>`;
            });
    }

    function renderTable(items, perPage = 20, curPage = 1) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <div class="mb-2"><i class="bi bi-inbox fs-2 text-secondary"></i></div>
                        <h6 class="fw-semibold text-secondary">No Website Orders Found</h6>
                        <p class="small text-muted mb-0">No orders match your active search or filters.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach((order, idx) => {
            // Serial Number
            const srNo = ((curPage - 1) * perPage) + (idx + 1);

            // Clean items preview
            const count = order.items_count || 1;
            const firstItemName = (order.items_summary && order.items_summary.length > 0) ? order.items_summary[0].name : 'Product';
            const itemsHtml = `
                <div class="d-flex align-items-center gap-1.5">
                    <span class="badge bg-light text-dark border fw-semibold text-nowrap">${count} ${count > 1 ? 'Items' : 'Item'}</span>
                    <span class="small text-muted text-truncate d-inline-block align-middle" style="max-width: 170px;" title="${firstItemName}">${firstItemName}</span>
                </div>
            `;

            // Clean store badge
            const storeName = order.selling_supplier_name || order.source_store || 'Direct Store';
            const storeBadge = `
                <span class="badge bg-light text-dark border fw-normal py-1 px-2 text-truncate d-inline-block" style="max-width: 150px;" title="${storeName}">
                    <i class="bi bi-shop me-1 text-primary"></i>${storeName}
                </span>
            `;

            // Customer info
            const customerSub = order.customer_city ? `<i class="bi bi-geo-alt me-1"></i>${order.customer_city}` : (order.customer_email || order.customer_phone || '—');

            html += `
                <tr>
                    <td class="text-center text-muted fw-semibold">${srNo}</td>
                    <td>
                        <a href="{{ url('/admin/website-orders') }}/${order.id}" class="fw-bold font-monospace text-primary text-decoration-none fs-6">
                            #${order.order_number}
                        </a>
                    </td>
                    <td class="text-nowrap">
                        <div class="small fw-semibold text-dark">${order.order_date}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="${order.customer_name}">${order.customer_name}</div>
                        <div class="small text-muted text-truncate" style="max-width: 180px;" title="${customerSub}">${customerSub}</div>
                    </td>
                    <td>
                        ${storeBadge}
                    </td>
                    <td>
                        ${itemsHtml}
                    </td>
                    <td class="text-nowrap">
                        <span class="badge bg-light text-secondary border fw-medium px-2 py-1">${order.payment_method || 'COD'}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <div class="fw-bold text-dark fs-6">${order.total_formatted}</div>
                    </td>
                    <td class="text-center text-nowrap">
                        ${getStatusBadge(order.status)}
                    </td>
                    <td class="text-end pe-3 text-nowrap">
                        <a href="{{ url('/admin/website-orders') }}/${order.id}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-2xs d-inline-flex align-items-center gap-1">
                            <span>View</span> <i class="bi bi-arrow-right"></i>
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

    // Event listeners for filters
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadOrders(1), 350);
    });

    if (supplierFilter) {
        supplierFilter.addEventListener('change', () => loadOrders(1));
    }
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
