@extends('layouts.app')

@section('title', 'Published Products')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center text-dark">
                <span class="badge bg-success-subtle text-success p-2 rounded-3 me-2">
                    <i class="bi bi-bag-check fs-5"></i>
                </span>
                Published Products
            </h4>
            <p class="text-muted small mb-0">
                List of all products successfully published to WooCommerce stores across multiple suppliers.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fs-7 fw-semibold border border-primary-subtle" id="totalPublishedBadge">
                <i class="bi bi-check2-circle me-1"></i> <span id="totalCountDisplay">0</span> Published Records
            </span>
            <a href="{{ route('admin.publish-products.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-send-check me-1"></i> Ready to Publish
            </a>
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" id="btnRefresh" title="Refresh List">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-4 col-lg-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            id="publishedSearch"
                            class="form-control border-start-0 ps-2"
                            placeholder="Search by product name, SKU, barcode, supplier or category..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Target Supplier Store Filter --}}
                <div class="col-md-3 col-lg-3">
                    <select id="targetSupplierFilter" class="form-select form-select-sm rounded-3">
                        <option value="">-- All Target Stores --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->sno }}">{{ $s->name }} Store</option>
                        @endforeach
                    </select>
                </div>

                {{-- Origin Supplier Filter --}}
                <div class="col-md-3 col-lg-2">
                    <select id="originSupplierFilter" class="form-select form-select-sm rounded-3">
                        <option value="">-- All Origins --</option>
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

    {{-- MAIN TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="publishedTable">
                    <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                        <tr>
                            <th style="width: 60px;" class="ps-3 text-center">Sr No</th>
                            <th style="width: 85px;" class="text-center">Image</th>
                            <th style="min-width: 220px;">Product Name</th>
                            <th style="min-width: 140px;">Target Store</th>
                            <th style="min-width: 120px;">Category</th>
                            <th style="min-width: 120px;">Origin Supplier</th>
                            <th style="min-width: 140px;">SKU / Barcode</th>
                            <th style="min-width: 110px;">WooCommerce ID</th>
                            <th style="min-width: 120px;">Published Date</th>
                            <th style="width: 120px;" class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="publishedTableBody">
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Loading published products...
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

{{-- STYLES & JAVASCRIPT --}}
<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .fs-7 { font-size: 0.8rem; }
    .hover-elevate { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .hover-elevate:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.06); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentSearch = '';
    let currentTargetSupplier = '';
    let currentOriginSupplier = '';
    let currentPerPage = 20;
    let debounceTimer = null;

    const tableBody = document.getElementById('publishedTableBody');
    const searchInput = document.getElementById('publishedSearch');
    const targetSupplierFilter = document.getElementById('targetSupplierFilter');
    const originSupplierFilter = document.getElementById('originSupplierFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const paginationSummary = document.getElementById('paginationSummary');
    const paginationControls = document.getElementById('paginationControls');
    const totalCountDisplay = document.getElementById('totalCountDisplay');
    const btnRefresh = document.getElementById('btnRefresh');

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
                <td colspan="10" class="text-center py-5 text-muted">
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
                        <td colspan="10" class="text-center py-5 text-danger">
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
                    <td colspan="10" class="text-center py-5 text-danger">
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
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
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

            html += `
                <tr>
                    {{-- 1. Sr No --}}
                    <td class="ps-3 text-center text-muted fw-semibold font-monospace" style="font-size: 0.8rem;">
                        ${srNo}
                    </td>

                    {{-- 2. Image --}}
                    <td class="text-center">
                        <div class="position-relative d-inline-block rounded-3 overflow-hidden border shadow-xs bg-light" style="width: 62px; height: 62px;">
                            <img src="${imgUrl}" 
                                 class="w-100 h-100 object-fit-contain p-1" 
                                 alt="${escapeHtml(productName)}"
                                 onerror="this.src='/assets/images/placeholder.png';">
                        </div>
                    </td>

                    {{-- 3. Product Name --}}
                    <td>
                        <div class="fw-bold text-dark fs-7">${escapeHtml(productName)}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            Type: <span class="fw-medium">${escapeHtml(item.product_type || '—')}</span>
                        </div>
                    </td>

                    {{-- 4. Target Store --}}
                    <td>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-shop me-1"></i>${escapeHtml(item.target_supplier_name || '—')}
                        </span>
                    </td>

                    {{-- 5. Category --}}
                    <td>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-tag me-1 text-muted"></i>${escapeHtml(categoryName)}
                        </span>
                    </td>

                    {{-- 6. Origin Supplier --}}
                    <td>
                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-truck me-1"></i>${escapeHtml(item.origin_supplier_name || '—')}
                        </span>
                    </td>

                    {{-- 7. SKU / Barcode --}}
                    <td>
                        <div class="font-monospace fw-semibold text-dark small">${escapeHtml(item.sku || '—')}</div>
                        ${item.barcode ? `<div class="font-monospace text-muted" style="font-size: 0.68rem;">${escapeHtml(item.barcode)}</div>` : ''}
                    </td>

                    {{-- 8. WooCommerce ID --}}
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 font-monospace" style="font-size: 0.72rem;">
                            #${escapeHtml(item.woocommerce_product_id || '—')}
                        </span>
                    </td>

                    {{-- 9. Published Date --}}
                    <td>
                        <small class="text-muted">${formatDate(item.last_updated_at || item.published_at)}</small>
                    </td>

                    {{-- 10. Actions --}}
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            @if(true)
                            ${item.permalink ? `
                                <a href="${item.permalink}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 shadow-xs hover-elevate" title="View Live on Store">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Live
                                </a>
                            ` : ''}
                            <a href="{{ url('/admin/publish-products') }}/${item.specification_id}" class="btn btn-sm btn-primary rounded-pill px-2.5 shadow-xs hover-elevate" title="View / Re-publish Product">
                                <i class="bi bi-pencil-square me-1"></i> Manage
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
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

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentSearch = this.value.trim();
            loadPublishedProducts(1);
        }, 350);
    });

    targetSupplierFilter.addEventListener('change', function() {
        currentTargetSupplier = this.value;
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

    // Initial Load
    loadPublishedProducts(1);
});
</script>
@endsection
