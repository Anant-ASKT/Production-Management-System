@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-stars text-primary me-2"></i>Send Image To AI Enhancer</h4>
            <div class="text-muted">Select multiple products and send their original product images to the AI Enhancer queue.</div>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6 align-self-center" id="selectedCount">0 Selected</span>
            <button type="button" class="btn btn-primary" id="btnSendToAi" disabled>
                <i class="bi bi-send me-1"></i>Send Selected To AI Enhancer
            </button>
        </div>
    </div>

    {{-- =========================================================
        FILTERS
        Same product filters as All Garments, except Supplier
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-6 col-xl-2">
                    <label for="aiEnhancerItemType" class="form-label fw-semibold">
                        Product Type
                    </label>
                    <select id="aiEnhancerItemType" class="form-select">
                        <option value="">All Product Types</option>
                        @isset($itemTypes)
                            @foreach($itemTypes as $itemType)
                                <option value="{{ $itemType->id }}">
                                    {{ $itemType->itemtype }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label for="aiEnhancerItemName" class="form-label fw-semibold">
                        Product Name
                    </label>
                    <select id="aiEnhancerItemName" class="form-select">
                        <option value="">All Product Names</option>
                        @isset($itemNames)
                            @foreach($itemNames as $itemName)
                                <option value="{{ $itemName->id }}">
                                    {{ $itemName->itemname }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label for="aiEnhancerComposition" class="form-label fw-semibold">
                        Composition
                    </label>
                    <select id="aiEnhancerComposition" class="form-select">
                        <option value="">All Compositions</option>
                        @isset($compositions)
                            @foreach($compositions as $composition)
                                <option value="{{ $composition->id }}">
                                    {{ $composition->composition_details }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label for="aiEnhancerGender" class="form-label fw-semibold">
                        Gender Type
                    </label>
                    <select id="aiEnhancerGender" class="form-select">
                        <option value="">All Gender Types</option>
                        @isset($genders)
                            @foreach($genders as $gender)
                                <option value="{{ $gender->id }}">
                                    {{ $gender->name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label for="aiEnhancerSentFilter" class="form-label fw-semibold">
                        AI Enhancer Status
                    </label>
                    <select id="aiEnhancerSentFilter" class="form-select">
                        <option value="all">All Products</option>
                        <option value="yes">Already Sent</option>
                        <option value="no">Not Sent</option>
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold">Search Product</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="aiEnhancerSearch" class="form-control" placeholder="SKU, barcode, name...">
                    </div>
                </div>

            </div>

            <div class="mt-3 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary" id="btnApplyAiFilter">
                    <i class="bi bi-funnel me-1"></i>
                    Apply Filter
                </button>

                <button type="button" class="btn btn-outline-secondary" id="btnClearAiFilter">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Clear
                </button>

                <button type="button" class="btn btn-sm btn-outline-primary ms-md-2" id="btnSelectAllPage">
                    <i class="bi bi-check2-square me-1"></i>
                    Select All On Page
                </button>

                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearSelection">
                    <i class="bi bi-x-circle me-1"></i>
                    Clear Selection
                </button>

                <span class="small text-muted align-self-center ms-auto" id="totalProductInfo"></span>
            </div>
        </div>
    </div>


    <div id="aiEnhancerLoading" class="text-center py-5" style="display:none;">
        <div class="spinner-border text-primary"></div>
        <div class="text-muted mt-2">Loading products...</div>
    </div>
    <div id="aiEnhancerEmpty" class="alert alert-info text-center" style="display:none;">No products found.</div>
    <div id="aiEnhancerCards" class="row g-4"></div>
    <div id="aiEnhancerPagination" class="mt-4 pb-4"></div>
</div>

<style>
.ai-enhancer-card { transition: .2s ease; height:100%; }
.ai-enhancer-card:hover { transform: translateY(-2px); box-shadow:0 .5rem 1rem rgba(0,0,0,.10)!important; }
.ai-enhancer-card.sent { border:2px solid #198754!important; box-shadow:0 0 0 2px rgba(25,135,84,.08); }
.ai-enhancer-image { height:220px; background:#f5f6f8; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.ai-enhancer-image img { width:100%; height:100%; object-fit:contain; }
.ai-sent-badge { position:absolute; top:10px; right:10px; z-index:2; }
.ai-select-box { position:absolute; top:10px; left:10px; z-index:3; width:22px; height:22px; cursor:pointer; }
</style>

<script>
(function () {
    'use strict';

    const cards = document.getElementById('aiEnhancerCards');
    const loading = document.getElementById('aiEnhancerLoading');
    const empty = document.getElementById('aiEnhancerEmpty');
    const pagination = document.getElementById('aiEnhancerPagination');
    const search = document.getElementById('aiEnhancerSearch');
    const itemTypeFilter = document.getElementById('aiEnhancerItemType');
    const itemNameFilter = document.getElementById('aiEnhancerItemName');
    const compositionFilter = document.getElementById('aiEnhancerComposition');
    const genderFilter = document.getElementById('aiEnhancerGender');
    const sentFilter = document.getElementById('aiEnhancerSentFilter');
    const selectedCount = document.getElementById('selectedCount');
    const btnSend = document.getElementById('btnSendToAi');
    const totalInfo = document.getElementById('totalProductInfo');
    const selectedProducts = new Map();
    let currentPage = 1;
    let searchTimer = null;

    function esc(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    function updateSelectionUi() {
        const count = selectedProducts.size;
        selectedCount.textContent = count + ' Selected';
        btnSend.disabled = count === 0;
        document.querySelectorAll('.ai-product-checkbox').forEach(function (cb) {
            cb.checked = selectedProducts.has(String(cb.dataset.id));
        });
    }

    function toggleProduct(product, checked) {
        const key = String(product.sno);
        if (checked) {
            selectedProducts.set(key, {
                garment_id: Number(product.sno),
                barcode: String(product.barcode || '')
            });
        } else {
            selectedProducts.delete(key);
        }
        updateSelectionUi();
    }

    function renderProducts(items) {
        cards.innerHTML = '';
        if (!items.length) {
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';

        items.forEach(function (p) {
            const sent = Number(p.ai_sent) === 1;
            const key = String(p.sno);
            const checked = selectedProducts.has(key);
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-lg-4 col-xl-3';
            col.innerHTML = `
                <div class="card ai-enhancer-card shadow-sm border ${sent ? 'sent' : ''}">
                    <div class="position-relative">
                        <input type="checkbox" class="form-check-input ai-select-box ai-product-checkbox" data-id="${esc(p.sno)}" ${checked ? 'checked' : ''} ${sent ? 'title="Already sent to AI Enhancer"' : ''}>
                        ${sent ? '<span class="badge bg-success ai-sent-badge"><i class="bi bi-check-circle me-1"></i>Already Sent</span>' : ''}
                        <div class="ai-enhancer-image">
                            ${p.image_url ? `<img src="${esc(p.image_url)}" alt="${esc(p.sku || p.barcode || 'Product')}" loading="lazy">` : '<div class="text-muted text-center"><i class="bi bi-image fs-1"></i><div>No Image</div></div>'}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2 mb-2">
                            <span class="badge bg-dark text-truncate">SKU: ${esc(p.sku || '-')}</span>
                            <span class="badge bg-light text-dark border">#${esc(p.sno)}</span>
                        </div>
                        <div class="fw-semibold text-truncate" title="${esc(p.item_name_text || '')}">${esc(p.item_name_text || 'Product')}</div>
                        <div class="small text-muted mt-1">Barcode: <strong>${esc(p.barcode || '-')}</strong></div>
                        <div class="small text-muted">Type: ${esc(p.item_type_text || '-')}</div>
                        <div class="small text-muted">Gender: ${esc(p.gender_text || '-')}</div>
                    </div>
                </div>`;
            const checkbox = col.querySelector('.ai-product-checkbox');
            checkbox.addEventListener('change', function () { toggleProduct(p, this.checked); });
            cards.appendChild(col);
        });
        updateSelectionUi();
    }

    function renderPagination(meta) {
        pagination.innerHTML = '';
        if (!meta || meta.last_page <= 1) return;
        const nav = document.createElement('nav');
        const ul = document.createElement('ul');
        ul.className = 'pagination justify-content-center flex-wrap';
        for (let page = 1; page <= meta.last_page; page++) {
            if (meta.last_page > 10 && page !== 1 && page !== meta.last_page && Math.abs(page - meta.current_page) > 2) {
                if (page === 2 || page === meta.last_page - 1) {
                    const li = document.createElement('li'); li.className='page-item disabled'; li.innerHTML='<span class="page-link">…</span>'; ul.appendChild(li);
                }
                continue;
            }
            const li = document.createElement('li'); li.className='page-item ' + (page === meta.current_page ? 'active' : '');
            const a = document.createElement('button'); a.type='button'; a.className='page-link'; a.textContent=page;
            a.addEventListener('click', function(){ loadProducts(page); }); li.appendChild(a); ul.appendChild(li);
        }
        nav.appendChild(ul); pagination.appendChild(nav);
    }

    async function loadProducts(page) {
        currentPage = page || 1;
        loading.style.display='block'; empty.style.display='none'; cards.innerHTML=''; pagination.innerHTML='';
        const params = new URLSearchParams({
            page: currentPage,
            per_page: 20,
            search: search.value.trim(),
            item_type: itemTypeFilter.value,
            item_name: itemNameFilter.value,
            composition: compositionFilter.value,
            gender: genderFilter.value,
            sent: sentFilter.value
        });
        try {
            const response = await fetch("{{ route('all-garments.ai-enhancer.data') }}?" + params.toString(), {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Unable to load products.');
            renderProducts(result.data || []); renderPagination(result.pagination);
            totalInfo.textContent = (result.pagination?.total || 0) + ' products found';
        } catch (e) {
            console.error(e); empty.style.display='block'; empty.className='alert alert-danger text-center'; empty.textContent=e.message || 'Unable to load products.';
        } finally { loading.style.display='none'; }
    }

    document.getElementById('btnSelectAllPage').addEventListener('click', function () {
        document.querySelectorAll('.ai-product-checkbox').forEach(function (cb) { if (!cb.checked) cb.click(); });
    });
    document.getElementById('btnClearSelection').addEventListener('click', function () {
        selectedProducts.clear(); updateSelectionUi();
    });
    document.getElementById('btnApplyAiFilter').addEventListener('click', function () {
        selectedProducts.clear();
        updateSelectionUi();
        loadProducts(1);
    });

    document.getElementById('btnClearAiFilter').addEventListener('click', function () {
        search.value = '';
        itemTypeFilter.value = '';
        itemNameFilter.value = '';
        compositionFilter.value = '';
        genderFilter.value = '';
        sentFilter.value = 'all';
        selectedProducts.clear();
        updateSelectionUi();
        loadProducts(1);
    });

    search.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            selectedProducts.clear();
            updateSelectionUi();
            loadProducts(1);
        }, 400);
    });

    btnSend.addEventListener('click', async function () {
        const products = Array.from(selectedProducts.values());
        if (!products.length) return;
        const confirmResult = await Swal.fire({
            icon:'question', title:'Send Products To AI Enhancer?',
            html:'You selected <strong>' + products.length + '</strong> product(s). Already sent products will not be inserted again.',
            showCancelButton:true, confirmButtonText:'Yes, Send', cancelButtonText:'Cancel'
        });
        if (!confirmResult.isConfirmed) return;

        btnSend.disabled=true;
        btnSend.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
        try {
            const response = await fetch("{{ route('all-garments.ai-enhancer.send') }}", {
                method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                body:JSON.stringify({products:products})
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Unable to send products.');
            await Swal.fire({icon:'success',title:'Completed',html:'<strong>'+result.inserted+'</strong> product(s) sent.<br><strong>'+result.already_sent+'</strong> already sent.'});
            selectedProducts.clear(); updateSelectionUi(); loadProducts(currentPage);
        } catch (e) {
            Swal.fire({icon:'error',title:'Send Failed',text:e.message || 'Unable to send products.'});
        } finally {
            btnSend.innerHTML='<i class="bi bi-send me-1"></i>Send Selected To AI Enhancer'; updateSelectionUi();
        }
    });

    loadProducts(1);
})();
</script>
@endsection
