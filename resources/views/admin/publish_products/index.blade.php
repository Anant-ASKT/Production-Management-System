@extends('layouts.app')

@section('title', 'Publish Products')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center text-dark">
                <span class="badge bg-primary-subtle text-primary p-2 rounded-3 me-2">
                    <i class="bi bi-send-check fs-5"></i>
                </span>
                Publish Products
            </h4>
            <p class="text-muted small mb-0">
                Displaying products with completed AI descriptions and verified approved enhanced images.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-7 fw-semibold border border-success-subtle" id="totalReadyBadge">
                <i class="bi bi-check2-circle me-1"></i> <span id="totalCountDisplay">0</span> Products Ready
            </span>
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" id="btnRefresh" title="Refresh List">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
        </div>
    </div>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-8 col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            id="publishSearch"
                            class="form-control border-start-0 ps-2"
                            placeholder="Search by AI product name, SKU, barcode, tags or product type..."
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 d-flex align-items-center justify-content-md-end gap-2">
                    <label for="perPageSelect" class="small text-muted text-nowrap fw-semibold">Show:</label>
                    <select id="perPageSelect" class="form-select form-select-sm w-auto rounded-3">
                        <option value="10">10 per page</option>
                        <option value="20" selected>20 per page</option>
                        <option value="30">30 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="publishTable">
                    <thead class="table-light text-secondary small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                        <tr>
                            <th style="width: 60px;" class="ps-3 text-center">Sr No</th>
                            <th style="width: 85px;" class="text-center">AI Image</th>
                            <th style="min-width: 200px;">Product Name</th>
                            <th style="min-width: 120px;">Supplier</th>
                            <th style="min-width: 130px;">Product Type</th>
                            <th style="min-width: 110px;">Colour</th>
                            <th style="min-width: 90px;">Size</th>
                            <th style="min-width: 100px;">Gender</th>
                            <th style="min-width: 130px;">Composition</th>
                            <th style="min-width: 150px;">SKU</th>
                            <th style="min-width: 130px;">Barcode</th>
                            <th style="width: 100px;" class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="publishTableBody">
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Loading ready-to-publish products...
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

{{-- ===================================================
     PRODUCT DETAIL & PREVIEW MODAL
==================================================== --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold border border-success-subtle">
                        <i class="bi bi-check-circle-fill me-1"></i> Ready to Publish
                    </span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalProductName">—</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" id="modalContent">
                <div class="row g-4">
                    
                    {{-- LEFT: AI APPROVED IMAGES GALLERY --}}
                    <div class="col-lg-5">
                        <div class="card border rounded-4 shadow-xs overflow-hidden h-100 bg-light">
                            <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                    <i class="bi bi-camera-fill text-primary me-1"></i> Approved AI Images
                                </span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5" id="modalImageCountBadge">0 Images</span>
                            </div>
                            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                                {{-- Main Active Image Preview --}}
                                <div class="w-100 bg-white rounded-3 border p-2 mb-3 d-flex align-items-center justify-content-center" style="height: 340px;">
                                    <a id="modalMainImgLink" href="#" target="_blank" rel="noopener noreferrer">
                                        <img id="modalMainImg" src="" class="img-fluid rounded" style="max-height: 320px; object-fit: contain;" alt="Approved AI Image">
                                    </a>
                                </div>
                                {{-- Thumbnails List --}}
                                <div class="d-flex gap-2 flex-wrap justify-content-center w-100" id="modalThumbsContainer">
                                    {{-- Thumbnails rendered via JS --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: AI CONTENT & SPECS --}}
                    <div class="col-lg-7">
                        <div class="d-flex flex-column gap-3">
                            
                            {{-- SPECIFICATIONS BADGES --}}
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em;">Product Attributes</div>
                                <div class="d-flex flex-wrap gap-2" id="modalAttributeBadges">
                                    {{-- Badges rendered via JS --}}
                                </div>
                            </div>

                            {{-- AI DESCRIPTION BOX --}}
                            <div class="card border rounded-4 shadow-xs">
                                <div class="card-header bg-white border-bottom py-2.5 px-3">
                                    <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                        <i class="bi bi-stars text-warning me-1"></i> AI Product Description
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="text-secondary small lh-base" id="modalAiDescription" style="white-space: pre-line; max-height: 180px; overflow-y: auto;">
                                        —
                                    </div>
                                </div>
                            </div>

                            {{-- SEO & METADATA SECTION --}}
                            <div class="card border rounded-4 shadow-xs">
                                <div class="card-header bg-white border-bottom py-2.5 px-3">
                                    <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                        <i class="bi bi-globe text-info me-1"></i> SEO & Meta Information
                                    </span>
                                </div>
                                <div class="card-body p-3 small">
                                    <div class="row g-2">
                                        <div class="col-sm-4 text-muted fw-semibold">Meta Title:</div>
                                        <div class="col-sm-8 fw-medium text-dark" id="modalMetaTitle">—</div>

                                        <div class="col-sm-4 text-muted fw-semibold">Meta Description:</div>
                                        <div class="col-sm-8 text-secondary" id="modalMetaDescription">—</div>

                                        <div class="col-sm-4 text-muted fw-semibold">Meta Keywords:</div>
                                        <div class="col-sm-8 text-secondary" id="modalMetaKeywords">—</div>

                                        <div class="col-sm-4 text-muted fw-semibold">Product Tags:</div>
                                        <div class="col-sm-8" id="modalProductTags">—</div>

                                        <div class="col-sm-4 text-muted fw-semibold">Image Alt Text:</div>
                                        <div class="col-sm-8 text-secondary" id="modalImageAltText">—</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-between">
                <div class="small text-muted">
                    <span id="modalSkuFooter" class="fw-semibold text-dark"></span>
                </div>
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- STYLES & JAVASCRIPT --}}
<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .fs-7 { font-size: 0.8rem; }
    .hover-elevate { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .hover-elevate:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.06); }
    .thumb-btn { border: 2px solid transparent; transition: all 0.2s ease; }
    .thumb-btn.active { border-color: var(--bs-primary); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentSearch = '';
    let currentPerPage = 20;
    let debounceTimer = null;

    const tableBody = document.getElementById('publishTableBody');
    const searchInput = document.getElementById('publishSearch');
    const perPageSelect = document.getElementById('perPageSelect');
    const paginationSummary = document.getElementById('paginationSummary');
    const paginationControls = document.getElementById('paginationControls');
    const totalCountDisplay = document.getElementById('totalCountDisplay');
    const btnRefresh = document.getElementById('btnRefresh');

    // Helper: Normalize image path
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

    // Helper: Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Fetch Table Data
    function loadProducts(page = 1) {
        currentPage = page;
        tableBody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading ready-to-publish products...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            page: currentPage,
            per_page: currentPerPage,
            search: currentSearch
        });

        fetch(`{{ route('admin.publish-products.data') }}?${params.toString()}`, {
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
                        <td colspan="12" class="text-center py-5 text-danger">
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
            console.error('Error fetching publish products data:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="12" class="text-center py-5 text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i> An error occurred while fetching data.
                    </td>
                </tr>
            `;
        });
    }

    // Render Table Rows
    function renderTable(items) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="12" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                        No qualified products ready to publish.
                        <p class="small text-muted mb-0 mt-1">Make sure the product has a saved AI description and at least one approved AI image.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach((item, index) => {
            const srNo = ((currentPage - 1) * currentPerPage) + (index + 1);
            const approvedImgUrl = formatImgUrl(item.ai_main_image);
            const totalImgs = item.total_approved_images || 0;
            const productName = item.clean_product_name || item.AI_product_name || item.product_name || 'Untitled Product';

            html += `
                <tr>
                    {{-- 1. Sr No --}}
                    <td class="ps-3 text-center text-muted fw-semibold font-monospace" style="font-size: 0.8rem;">
                        ${srNo}
                    </td>

                    {{-- 2. AI Approved Image --}}
                    <td class="text-center">
                        <div class="position-relative d-inline-block rounded-3 overflow-hidden border shadow-xs bg-light" style="width: 68px; height: 68px;">
                            <img src="${approvedImgUrl}" 
                                 class="w-100 h-100 object-fit-contain p-1" 
                                 alt="${escapeHtml(productName)}"
                                 onerror="this.src='/assets/images/placeholder.png';">
                            ${totalImgs > 1 ? `
                                <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75 text-white rounded-start-pill py-0.5 px-1.5" style="font-size: 0.6rem;">
                                    <i class="bi bi-images"></i> ${totalImgs}
                                </span>
                            ` : ''}
                        </div>
                    </td>

                    {{-- 3. Product Name --}}
                    <td>
                        <div class="fw-bold text-dark fs-7">${escapeHtml(productName)}</div>
                        ${item.AI_Producttag ? `
                            <div class="text-muted text-truncate" style="font-size: 0.7rem; max-width: 240px;">
                                <i class="bi bi-tags me-1 text-primary"></i>${escapeHtml(item.AI_Producttag.split(',').slice(0, 2).join(', '))}
                            </div>
                        ` : ''}
                    </td>

                    {{-- 4. Supplier --}}
                    <td>
                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-truck me-1"></i>${escapeHtml(item.supplier_name || '—')}
                        </span>
                    </td>

                    {{-- 5. Product Type --}}
                    <td>
                        <span class="text-dark small">${escapeHtml(item.product_type || '—')}</span>
                    </td>

                    {{-- 6. Colour --}}
                    <td>
                        <span class="badge bg-light text-dark border py-1 px-2 font-monospace" style="font-size: 0.72rem;">
                            ${escapeHtml(item.colour_name || '—')}
                        </span>
                    </td>

                    {{-- 7. Size --}}
                    <td>
                        <span class="fw-semibold text-dark small">${escapeHtml(item.size_name || '—')}</span>
                    </td>

                    {{-- 8. Gender --}}
                    <td>
                        <span class="text-secondary small">${escapeHtml(item.gender_name || '—')}</span>
                    </td>

                    {{-- 9. Composition --}}
                    <td>
                        <span class="text-secondary small">${escapeHtml(item.composition_name || '—')}</span>
                    </td>

                    {{-- 10. SKU --}}
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 font-monospace" style="font-size: 0.72rem;">
                            ${escapeHtml(item.sku || '—')}
                        </span>
                    </td>

                    {{-- 11. Barcode --}}
                    <td>
                        <span class="text-muted small font-monospace">
                            <i class="bi bi-upc-scan me-1"></i>${escapeHtml(item.barcode || '—')}
                        </span>
                    </td>

                    {{-- 12. Action --}}
                    <td class="text-end pe-3">
                        <a href="{{ url('/admin/publish-products') }}/${item.spec_id}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs hover-elevate" title="View Full Product">
                            <i class="bi bi-eye me-1"></i> View
                        </a>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
    }



    // Render Pagination Controls
    function renderPagination(res) {
        paginationSummary.textContent = `Showing ${res.from || 0} to ${res.to || 0} of ${res.total} products`;

        if (res.last_page <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let html = '';

        // Previous
        html += `
            <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${res.current_page - 1}">&laquo;</a>
            </li>
        `;

        // Page Numbers
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

        // Next
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
                    loadProducts(page);
                }
            });
        });
    }

    // Open Modal and Load Full Product Details
    function openProductDetailModal(specId) {
        const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
        
        document.getElementById('modalProductName').textContent = 'Loading product...';
        document.getElementById('modalMainImg').src = '/assets/images/placeholder.png';
        document.getElementById('modalThumbsContainer').innerHTML = '';
        document.getElementById('modalAttributeBadges').innerHTML = '';
        document.getElementById('modalAiDescription').textContent = 'Loading...';

        modal.show();

        fetch(`{{ url('/admin/publish-products') }}/${specId}/detail`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(res => {
            if (!res.success || !res.data) {
                alert('Unable to load product details.');
                return;
            }

            const p = res.data;
            const images = res.approved_images || [];

            // Title & Header
            document.getElementById('modalProductName').textContent = p.AI_product_name || p.master_product_name || 'Product Detail';
            document.getElementById('modalSkuFooter').textContent = `SKU: ${p.sku || '—'} | Barcode: ${p.barcode || '—'}`;

            // Images Gallery Setup
            document.getElementById('modalImageCountBadge').textContent = `${images.length} Approved Image${images.length !== 1 ? 's' : ''}`;
            
            const firstImg = images.length > 0 ? formatImgUrl(images[0].enhanced_image_path) : '/assets/images/placeholder.png';
            document.getElementById('modalMainImg').src = firstImg;
            document.getElementById('modalMainImgLink').href = firstImg;

            let thumbsHtml = '';
            images.forEach((img, idx) => {
                const imgUrl = formatImgUrl(img.enhanced_image_path);
                thumbsHtml += `
                    <button type="button" class="thumb-btn p-0.5 rounded-2 bg-white ${idx === 0 ? 'active' : ''}" data-url="${imgUrl}" style="width: 56px; height: 56px; overflow: hidden;">
                        <img src="${imgUrl}" class="w-100 h-100 object-fit-contain rounded" alt="Thumb">
                    </button>
                `;
            });
            document.getElementById('modalThumbsContainer').innerHTML = thumbsHtml;

            // Thumbnail Click Handler
            document.querySelectorAll('.thumb-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const url = this.getAttribute('data-url');
                    document.getElementById('modalMainImg').src = url;
                    document.getElementById('modalMainImgLink').href = url;
                });
            });

            // Attributes Badges
            const attrs = [
                { label: 'Type', val: p.product_type },
                { label: 'Gender', val: p.gender_name },
                { label: 'Colour', val: p.colour_name },
                { label: 'Size', val: p.size_name },
                { label: 'Composition', val: p.composition_name },
                { label: 'Designer', val: p.designer_name },
                { label: 'Supplier', val: p.supplier_name },
                { label: 'Process', val: p.manufacturing_process_name },
                { label: 'Embellishment', val: p.embellishment_name }
            ];

            let attrHtml = '';
            attrs.forEach(a => {
                if (a.val) {
                    attrHtml += `<span class="badge bg-white text-dark border py-1.5 px-3 rounded-pill fs-7 fw-medium"><span class="text-muted me-1">${escapeHtml(a.label)}:</span>${escapeHtml(a.val)}</span>`;
                }
            });
            document.getElementById('modalAttributeBadges').innerHTML = attrHtml || '<span class="text-muted small">No specifications recorded</span>';

            // AI Description
            document.getElementById('modalAiDescription').textContent = p.AI_product_description || 'No AI description available.';

            // SEO Metadata
            document.getElementById('modalMetaTitle').textContent = p.AI_Metatitle || '—';
            document.getElementById('modalMetaDescription').textContent = p.AI_Metadescription || '—';
            document.getElementById('modalMetaKeywords').textContent = p.AI_Metakeywards || '—';
            document.getElementById('modalImageAltText').textContent = p.AI_Imagealttext || '—';

            if (p.AI_Producttag) {
                const tags = p.AI_Producttag.split(',').map(t => `<span class="badge bg-light text-primary border rounded-pill py-1 px-2 me-1 mb-1">#${escapeHtml(t.trim())}</span>`).join('');
                document.getElementById('modalProductTags').innerHTML = tags;
            } else {
                document.getElementById('modalProductTags').textContent = '—';
            }
        })
        .catch(err => {
            console.error('Error fetching detail modal data:', err);
            alert('Failed to load product details.');
        });
    }

    // Search Input with Debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentSearch = this.value.trim();
            loadProducts(1);
        }, 350);
    });

    // Per Page Select Handler
    perPageSelect.addEventListener('change', function() {
        currentPerPage = parseInt(this.value);
        loadProducts(1);
    });

    // Refresh Button Handler
    btnRefresh.addEventListener('click', function() {
        loadProducts(currentPage);
    });

    // Initial Load
    loadProducts(1);
});
</script>
@endsection
