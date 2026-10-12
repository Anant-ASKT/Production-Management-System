@extends('layouts.app')

@section('title', $supplier->name . ' - Store Products & Categories')
@section('page-title', $supplier->name . ' - Store Manager')

@section('content')
<div class="container-fluid px-3 py-2">

    <!-- Top Simple Header Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="mb-0 fw-bold text-dark">{{ $supplier->name }}</h4>
                @if($connection['success'])
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i> Website Online
                    </span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        <i class="bi bi-x-circle-fill me-1"></i> Offline / Error
                    </span>
                @endif
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                    {{ ucfirst($supplier->type ?? 'Supplier') }}
                </span>
                @if(!empty($supplier->store_url))
                    <a href="{{ $supplier->store_url }}" target="_blank" rel="noopener noreferrer" class="small text-primary text-decoration-none ms-1">
                        <i class="bi bi-globe me-1"></i>{{ preg_replace('#^https?://#i', '', rtrim($supplier->store_url, '/')) }}
                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(!empty($supplier->store_url))
                <a href="{{ $supplier->store_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Visit Website
                </a>
            @endif
            <a href="{{ route('admin.suppliers.edit', $supplier->sno) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-gear me-1"></i> Settings
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(!$hasCredentials)
        <div class="alert alert-warning p-3 rounded mb-3">
            <h5 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Website Credentials Missing</h5>
            <p class="mb-2">Please configure WordPress Store URL, Consumer Key, and Consumer Secret for this supplier to connect.</p>
            <a href="{{ route('admin.suppliers.edit', $supplier->sno) }}" class="btn btn-sm btn-warning fw-bold">Configure Now</a>
        </div>
    @else

        <!-- Top 2 Simple Metric Cards: Total Products and Total Categories -->
        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <div class="card shadow-sm border-0 p-3 bg-primary text-white rounded-3 d-flex flex-row align-items-center justify-content-between" style="cursor: pointer;" onclick="document.getElementById('products-tab').click();">
                    <div>
                        <div class="text-white-50 small fw-bold text-uppercase">Total Products on Website</div>
                        <div class="fs-2 fw-bold" id="stat-total-products">{{ $connection['total_products'] ?? 0 }}</div>
                        <div class="small text-white-50">Click to view products</div>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-75"></i>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card shadow-sm border-0 p-3 bg-dark text-white rounded-3 d-flex flex-row align-items-center justify-content-between" style="cursor: pointer;" onclick="document.getElementById('categories-tab').click();">
                    <div>
                        <div class="text-white-50 small fw-bold text-uppercase">Total Categories on Website</div>
                        <div class="fs-2 fw-bold" id="stat-total-categories">{{ $localCategoriesCount }}</div>
                        <div class="small text-white-50">Click to view categories</div>
                    </div>
                    <i class="bi bi-tags fs-1 opacity-75"></i>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-3" id="storeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-2" id="products-tab" data-bs-toggle="pill" data-bs-target="#products-tab-pane" type="button" role="tab">
                    <i class="bi bi-box-seam me-1"></i> Products (<span id="badge-products-count">-</span>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-2" id="categories-tab" data-bs-toggle="pill" data-bs-target="#categories-tab-pane" type="button" role="tab">
                    <i class="bi bi-tags me-1"></i> Categories (<span id="badge-categories-count">-</span>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="storeTabsContent">
            
            <!-- ========================================== -->
            <!-- TAB 1: PRODUCTS LISTING                   -->
            <!-- ========================================== -->
            <div class="tab-pane fade show active" id="products-tab-pane" role="tabpanel">
                
                <!-- Filters Bar -->
                <div class="card shadow-sm border-0 mb-3 bg-light">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            
                            <!-- Search -->
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="filter-search" class="form-control" placeholder="Search product name or SKU...">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-3 col-sm-6">
                                <select id="filter-status" class="form-select">
                                    <option value="all">All Statuses</option>
                                    <option value="publish" selected>🟢 Live on Website</option>
                                    <option value="draft">⏸️ Hidden / Withdrawn</option>
                                    <option value="private">🔒 Private</option>
                                    <option value="trash">🗑️ In Trash</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div class="col-md-3 col-sm-6">
                                <select id="filter-category" class="form-select">
                                    <option value="">All Categories</option>
                                </select>
                            </div>

                            <!-- Filter & Reset Buttons -->
                            <div class="col-md-2 d-flex gap-2">
                                <button type="button" id="btn-apply-filters" class="btn btn-primary fw-bold flex-grow-1">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                                <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary" title="Reset Filters">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Product Listing Card & Table -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-6" id="products-sub-count">
                            <i class="bi bi-list-task me-1 text-primary"></i> Product List
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btn-refresh-products">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">Photo</th>
                                    <th>Product Name</th>
                                    <th style="width: 140px;">SKU</th>
                                    <th style="width: 140px;">Category</th>
                                    <th style="width: 130px;">Price</th>
                                    <th style="width: 130px;">Stock</th>
                                    <th style="width: 130px;">Status</th>
                                    <th style="width: 180px;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="spinner-border text-primary mb-2"></div>
                                        <div class="fw-bold text-dark">Loading products from website...</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="text-muted small fw-semibold" id="products-page-info">Showing 0 of 0 products</div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0" id="products-pagination-ul">
                                <!-- Populated via JS -->
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- TAB 2: CATEGORIES LISTING                 -->
            <!-- ========================================== -->
            <div class="tab-pane fade" id="categories-tab-pane" role="tabpanel">
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-6">
                                <i class="bi bi-tags me-1 text-primary"></i> Categories List
                            </span>
                            <input type="text" id="categories-search" class="form-control form-control-sm" style="max-width: 200px;" placeholder="Search category...">
                            <button type="button" class="btn btn-sm btn-light border text-secondary" id="btn-refresh-categories">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success fw-bold px-3" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                <i class="bi bi-plus-lg me-1"></i> Add Category
                            </button>
                            <button type="button" class="btn btn-sm btn-primary fw-bold px-3" id="btn-sync-categories">
                                <i class="bi bi-arrow-down-circle me-1"></i> Sync to Our System
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Category Name</th>
                                    <th>Slug</th>
                                    <th>Parent Category</th>
                                    <th class="text-center" style="width: 140px;">Products Count</th>
                                    <th style="width: 120px;" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="categories-tbody">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                        Loading categories from website...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    @endif

</div>

<!-- Modal: Add New Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-folder-plus text-success me-1"></i> Add Category to Website
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sarees, Kurtas, Shirts" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parent Category (Optional)</label>
                        <select name="parent" id="new-category-parent" class="form-select">
                            <option value="0">None (Main Category)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Short description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btn-save-category">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="save-category-spinner"></span>
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete Product Confirmation -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-trash-fill me-1"></i> Delete Product from Website?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark mb-2">Are you sure you want to delete this product from the website?</p>
                <div class="p-2 border rounded bg-light mb-3">
                    <div class="fw-bold text-dark" id="delete-modal-product-title">-</div>
                    <div class="small text-muted">Product ID: <strong id="delete-modal-product-id">-</strong></div>
                </div>

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="delete-force-permanent">
                    <label class="form-check-label fw-bold text-danger" for="delete-force-permanent">
                        Permanently Delete (Cannot be recovered)
                    </label>
                </div>
                <div class="small text-muted">
                    If unchecked, it goes to the Website <strong>Trash</strong> where it can be restored later.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger fw-bold" id="btn-confirm-delete-product">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="delete-product-spinner"></span>
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Message -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="liveToast" class="toast align-items-center text-white border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="toastMessage">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <span class="fw-bold">Done!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const supplierId = {{ $supplier->sno }};
    const hasCredentials = {{ $hasCredentials ? 'true' : 'false' }};

    if (!hasCredentials) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toastEl = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });

    function showToast(message, isSuccess = true) {
        const toastMessageEl = document.getElementById('toastMessage');
        toastEl.className = `toast align-items-center text-white border-0 shadow bg-${isSuccess ? 'success' : 'danger'}`;
        toastMessageEl.innerHTML = `
            <i class="bi ${isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} fs-5"></i>
            <span class="fw-bold">${escapeHtml(message)}</span>
        `;
        toast.show();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    function cleanText(str) {
        if (!str) return '';
        const txt = document.createElement('textarea');
        txt.innerHTML = str;
        return txt.value;
    }

    let currentProductsPage = 1;
    let storeCategoriesList = [];
    let pendingDeleteProductId = null;

    // Load Categories
    function loadCategories() {
        const tbody = document.getElementById('categories-tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    <span class="spinner-border spinner-border-sm me-2 text-primary"></span>
                    Loading categories from website...
                </td>
            </tr>
        `;

        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/categories?per_page=100`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.categories)) {
                storeCategoriesList = data.categories;
                renderCategories(storeCategoriesList);
                populateCategoryDropdowns(storeCategoriesList);
                document.getElementById('badge-categories-count').innerText = storeCategoriesList.length;
                document.getElementById('stat-total-categories').innerText = storeCategoriesList.length;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4 text-danger">
                            Failed to load categories: ${escapeHtml(data.message || 'Unknown error')}
                        </td>
                    </tr>
                `;
            }
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        Error fetching categories: ${escapeHtml(err.message)}
                    </td>
                </tr>
            `;
        });
    }

    function renderCategories(cats) {
        const tbody = document.getElementById('categories-tbody');
        if (cats.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No categories found on website.
                    </td>
                </tr>
            `;
            return;
        }

        const catMap = {};
        cats.forEach(c => catMap[c.id] = cleanText(c.name));

        let html = '';
        cats.forEach(c => {
            const cleanName = cleanText(c.name);
            const hasParent = Boolean(c.parent && catMap[c.parent]);
            const parentName = hasParent 
                ? `<span class="badge bg-light text-dark border">↳ ${escapeHtml(catMap[c.parent])}</span>`
                : `<span class="text-muted small">Main Category</span>`;

            html += `
                <tr>
                    <td class="text-muted fw-bold">#${c.id}</td>
                    <td>
                        <div class="fw-bold text-dark">
                            ${hasParent ? '<span class="text-muted me-1">↳</span>' : '<i class="bi bi-folder-fill text-warning me-1"></i>'}
                            ${escapeHtml(cleanName)}
                        </div>
                    </td>
                    <td><code class="text-secondary">${escapeHtml(c.slug)}</code></td>
                    <td>${parentName}</td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">${c.count || 0} Products</span>
                    </td>
                    <td class="text-end">
                        ${(c.count && c.count > 0) ? `
                            <button type="button" class="btn btn-sm btn-light border text-muted px-2 py-1" style="cursor: not-allowed; opacity: 0.75;" title="Cannot delete: This category contains ${c.count} products." onclick="alert('Cannot delete category &quot;${escapeHtml(cleanName)}&quot; because it has ${c.count} products inside it on the website. Please move or delete those products first.')">
                                <i class="bi bi-lock-fill text-secondary me-1"></i> Has ${c.count} items
                            </button>
                        ` : `
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-category" data-id="${c.id}" data-name="${escapeHtml(cleanName)}" title="Delete category from store">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        `}
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;

        tbody.querySelectorAll('.btn-delete-category').forEach(btn => {
            btn.addEventListener('click', function () {
                const catId = this.getAttribute('data-id');
                const catName = this.getAttribute('data-name');
                if (confirm(`Do you want to delete category "${catName}" from the website?`)) {
                    deleteCategory(catId);
                }
            });
        });
    }

    function populateCategoryDropdowns(cats) {
        const filterDropdown = document.getElementById('filter-category');
        const parentDropdown = document.getElementById('new-category-parent');

        let filterHtml = '<option value="">All Categories</option>';
        let parentHtml = '<option value="0">None (Main Category)</option>';

        cats.forEach(c => {
            const cleanName = cleanText(c.name);
            filterHtml += `<option value="${c.id}">${escapeHtml(cleanName)} (${c.count || 0})</option>`;
            parentHtml += `<option value="${c.id}">${escapeHtml(cleanName)}</option>`;
        });

        filterDropdown.innerHTML = filterHtml;
        parentDropdown.innerHTML = parentHtml;
    }

    function deleteCategory(catId) {
        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/categories/${catId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Category deleted successfully.', true);
                loadCategories();
            } else {
                showToast(data.message || 'Failed to delete category.', false);
            }
        })
        .catch(err => {
            showToast('Error: ' + err.message, false);
        });
    }

    // Load Products
    function loadProducts(page = 1) {
        currentProductsPage = page;
        const tbody = document.getElementById('products-tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-primary mb-2"></div>
                    <div class="fw-bold text-dark">Fetching live products from website...</div>
                </td>
            </tr>
        `;

        const search = document.getElementById('filter-search').value.trim();
        const status = document.getElementById('filter-status').value;
        const category = document.getElementById('filter-category').value;

        const params = new URLSearchParams({
            page: page,
            per_page: 20,
            status: status
        });

        if (search) params.append('search', search);
        if (category) params.append('category', category);

        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/products?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.products)) {
                renderProducts(data.products);
                renderPagination(data.total, data.total_pages, data.page, data.per_page);
                document.getElementById('badge-products-count').innerText = data.total;
                document.getElementById('stat-total-products').innerText = data.total;
                document.getElementById('products-sub-count').innerHTML = `
                    <i class="bi bi-list-task me-1 text-primary"></i> Total <strong>${data.total}</strong> products on store
                `;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-circle fs-4 d-block mb-1"></i>
                            ${escapeHtml(data.message || 'Failed to load products from store.')}
                        </td>
                    </tr>
                `;
                document.getElementById('products-page-info').innerText = 'Could not load products.';
                document.getElementById('products-pagination-ul').innerHTML = '';
            }
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                        Connection error: ${escapeHtml(err.message)}
                    </td>
                </tr>
            `;
        });
    }

    function renderProducts(products) {
        const tbody = document.getElementById('products-tbody');
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        <h6 class="fw-bold text-dark">No Products Found</h6>
                        <p class="small text-muted mb-0">Try clearing filters or search keyword.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        products.forEach(p => {
            const imgHtml = p.featured_image 
                ? `<img src="${p.featured_image}" alt="" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">`
                : `<div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;"><i class="bi bi-image fs-5"></i></div>`;

            // Status Badge
            let statusBadge = '';
            if (p.status === 'publish') {
                statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i> Live</span>';
            } else if (p.status === 'draft') {
                statusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="bi bi-eye-slash me-1"></i> Hidden</span>';
            } else if (p.status === 'trash') {
                statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-trash me-1"></i> Trash</span>';
            } else {
                statusBadge = `<span class="badge bg-secondary">${escapeHtml(p.status)}</span>`;
            }

            // Stock
            let stockBadge = '';
            if (p.stock_status === 'instock') {
                stockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2 me-1"></i> In Stock ${p.stock_quantity !== null ? `(${p.stock_quantity})` : ''}</span>`;
            } else {
                stockBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i> Out of Stock</span>';
            }

            // Price
            let priceHtml = '';
            if (p.sale_price && p.regular_price) {
                priceHtml = `
                    <div class="fw-bold text-success">₹${p.sale_price}</div>
                    <div class="small text-muted text-decoration-line-through">₹${p.regular_price}</div>
                `;
            } else if (p.price) {
                priceHtml = `<div class="fw-bold text-dark">₹${p.price}</div>`;
            } else {
                priceHtml = '<div class="text-muted small">-</div>';
            }

            // Withdraw / Show action
            const isPublished = p.status === 'publish';
            const actionBtn = isPublished 
                ? `
                    <button type="button" class="btn btn-sm btn-outline-warning text-dark btn-toggle-status" 
                        data-id="${p.id}" data-name="${escapeHtml(p.name)}" data-next-status="draft"
                        title="Hide product from website">
                        <i class="bi bi-eye-slash me-1"></i> Hide
                    </button>
                  `
                : `
                    <button type="button" class="btn btn-sm btn-outline-success btn-toggle-status" 
                        data-id="${p.id}" data-name="${escapeHtml(p.name)}" data-next-status="publish"
                        title="Make product live on website">
                        <i class="bi bi-eye me-1"></i> Show
                    </button>
                  `;

            html += `
                <tr data-product-id="${p.id}">
                    <td>${imgHtml}</td>
                    <td>
                        <div class="fw-bold text-dark text-truncate" style="max-width: 260px;" title="${escapeHtml(p.name)}">
                            ${escapeHtml(p.name)}
                        </div>
                        <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                            <span>ID: <strong>${p.id}</strong></span>
                            ${p.permalink ? `
                                <a href="${p.permalink}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none" title="Open on website">
                                    <i class="bi bi-box-arrow-up-right"></i> Open Link
                                </a>
                            ` : ''}
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace">${escapeHtml(p.sku || '-')}</span>
                    </td>
                    <td>
                        <span class="small text-dark text-truncate d-inline-block" style="max-width: 130px;" title="${escapeHtml(p.category_names || 'None')}">
                            ${escapeHtml(p.category_names || 'None')}
                        </span>
                    </td>
                    <td>${priceHtml}</td>
                    <td>${stockBadge}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end align-items-center gap-1">
                            ${actionBtn}
                            <button type="button" class="btn btn-sm btn-outline-danger btn-open-delete-modal"
                                data-id="${p.id}" data-name="${escapeHtml(p.name)}"
                                title="Delete product">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        // Attach Hide / Show Listeners
        tbody.querySelectorAll('.btn-toggle-status').forEach(btn => {
            btn.addEventListener('click', function () {
                const pId = this.getAttribute('data-id');
                const pName = this.getAttribute('data-name');
                const nextStatus = this.getAttribute('data-next-status');
                const actionText = nextStatus === 'draft' ? 'HIDE this product from the website' : 'SHOW this product live on the website';

                if (confirm(`Do you want to ${actionText}?\n\n"${pName}"`)) {
                    updateProductStatus(pId, nextStatus, this);
                }
            });
        });

        // Attach Delete Modal Listeners
        tbody.querySelectorAll('.btn-open-delete-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                pendingDeleteProductId = this.getAttribute('data-id');
                const pName = this.getAttribute('data-name');
                document.getElementById('delete-modal-product-title').innerText = pName;
                document.getElementById('delete-modal-product-id').innerText = pendingDeleteProductId;
                document.getElementById('delete-force-permanent').checked = false;

                const deleteModal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
                deleteModal.show();
            });
        });
    }

    function renderPagination(total, totalPages, page, perPage) {
        const infoEl = document.getElementById('products-page-info');
        const ul = document.getElementById('products-pagination-ul');

        const start = total === 0 ? 0 : (page - 1) * perPage + 1;
        const end = Math.min(page * perPage, total);
        infoEl.innerHTML = `Showing <strong>${start}</strong> to <strong>${end}</strong> of <strong>${total}</strong> products (Page ${page} of ${totalPages || 1})`;

        if (totalPages <= 1) {
            ul.innerHTML = '';
            return;
        }

        let html = '';

        html += `
            <li class="page-item ${page <= 1 ? 'disabled' : ''}">
                <button class="page-link" data-page="${page - 1}"><i class="bi bi-chevron-left"></i> Prev</button>
            </li>
        `;

        const windowSize = 2;
        const startPage = Math.max(1, page - windowSize);
        const endPage = Math.min(totalPages, page + windowSize);

        if (startPage > 1) {
            html += `<li class="page-item"><button class="page-link" data-page="1">1</button></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === page ? 'active' : ''}">
                    <button class="page-link" data-page="${i}">${i}</button>
                </li>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><button class="page-link" data-page="${totalPages}">${totalPages}</button></li>`;
        }

        html += `
            <li class="page-item ${page >= totalPages ? 'disabled' : ''}">
                <button class="page-link" data-page="${page + 1}">Next <i class="bi bi-chevron-right"></i></button>
            </li>
        `;

        ul.innerHTML = html;

        ul.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function () {
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (!isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages) {
                    loadProducts(targetPage);
                }
            });
        });
    }

    function updateProductStatus(productId, newStatus, buttonEl) {
        const originalContent = buttonEl.innerHTML;
        buttonEl.disabled = true;
        buttonEl.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/products/${productId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            buttonEl.disabled = false;
            buttonEl.innerHTML = originalContent;
            if (data.success) {
                showToast(newStatus === 'draft' ? 'Product hidden from website.' : 'Product is now live on website.', true);
                loadProducts(currentProductsPage);
            } else {
                showToast(data.message || 'Failed to update status.', false);
            }
        })
        .catch(err => {
            buttonEl.disabled = false;
            buttonEl.innerHTML = originalContent;
            showToast('Error: ' + err.message, false);
        });
    }

    // Confirm Delete Product
    document.getElementById('btn-confirm-delete-product').addEventListener('click', function () {
        if (!pendingDeleteProductId) return;

        const force = document.getElementById('delete-force-permanent').checked;
        const spinner = document.getElementById('delete-product-spinner');
        const btn = this;

        btn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/products/${pendingDeleteProductId}?force=${force ? 1 : 0}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            const modalEl = document.getElementById('deleteProductModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            if (data.success) {
                showToast('Product removed from website.', true);
                loadProducts(currentProductsPage);
            } else {
                showToast(data.message || 'Failed to delete product.', false);
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            showToast('Error: ' + err.message, false);
        });
    });

    // Create Category Form
    document.getElementById('createCategoryForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-category');
        const spinner = document.getElementById('save-category-spinner');

        btn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/categories`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: formData.get('name'),
                parent: formData.get('parent') || 0,
                description: formData.get('description') || ''
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');

            if (data.success) {
                showToast('Category created on website successfully!', true);
                form.reset();
                const modalEl = document.getElementById('createCategoryModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                loadCategories();
            } else {
                showToast(data.message || 'Failed to create category.', false);
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            showToast('Error: ' + err.message, false);
        });
    });

    // Sync Categories
    document.getElementById('btn-sync-categories').addEventListener('click', function () {
        const btn = this;
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Syncing...`;

        fetch(`{{ url('admin/suppliers') }}/${supplierId}/website/categories/sync`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalContent;

            if (data.success) {
                showToast(data.message || 'Categories synced successfully!', true);
                if (data.synced_count !== undefined) {
                    document.getElementById('stat-total-categories').innerText = data.synced_count;
                }
            } else {
                showToast(data.message || 'Failed to sync categories.', false);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalContent;
            showToast('Error: ' + err.message, false);
        });
    });

    // Filters
    document.getElementById('btn-apply-filters').addEventListener('click', () => loadProducts(1));
    document.getElementById('filter-search').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') loadProducts(1);
    });
    document.getElementById('filter-status').addEventListener('change', () => loadProducts(1));
    document.getElementById('filter-category').addEventListener('change', () => loadProducts(1));

    document.getElementById('btn-reset-filters').addEventListener('click', function () {
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-status').value = 'all';
        document.getElementById('filter-category').value = '';
        loadProducts(1);
    });

    document.getElementById('btn-refresh-products').addEventListener('click', () => loadProducts(currentProductsPage));
    document.getElementById('btn-refresh-categories').addEventListener('click', () => loadCategories());

    // Search in categories
    document.getElementById('categories-search').addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#categories-tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // Lazy load categories tab
    document.getElementById('categories-tab').addEventListener('shown.bs.tab', function () {
        if (storeCategoriesList.length === 0) {
            loadCategories();
        }
    });

    // Start load
    loadCategories();
    loadProducts(1);
});
</script>
@endpush
@endsection
