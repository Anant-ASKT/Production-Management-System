{{-- If your authenticated layout differs, replace layouts.app. --}}

@extends('layouts.app')

@section('content')

<style>

.physical-shop-product-image{display:block;width:86px;height:100px;object-fit:contain;padding:4px;background:#fff;border:1px solid #dee2e6;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.06)}

.physical-shop-no-image{width:86px;height:100px;display:flex;align-items:center;justify-content:center;text-align:center;padding:5px;color:#6c757d;background:#f1f3f5;border:1px dashed #adb5bd;border-radius:8px;font-size:11px}

</style>

<div class="container-fluid py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">

        <div><h3 class="mb-1">Physical Shop Order</h3><div class="text-muted small">Draft baskets do not reserve or deduct stock. Stock is updated only when the order is finalized.</div></div>

        <a class="btn btn-outline-primary" href="{{ route('inventory.physical-shop-orders.orders.index') }}">Confirmed Orders</a>

    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <div class="card shadow-sm mb-3"><div class="card-body">

        <form method="POST" action="{{ route('inventory.physical-shop-orders.baskets.store') }}" class="row g-2 align-items-end">

            @csrf

            <div class="col-md-5"><label class="form-label">New Basket Name</label><input class="form-control" name="basket_name" maxlength="150" value="{{ old('basket_name') }}" placeholder="e.g. Walk-in Customer 01" required></div>

            <div class="col-md-auto"><button class="btn btn-primary">+ Create Basket</button></div>

        </form>

       {{-- Combined order: place inside the existing basket card, after the basket navigation and before its closing </div></div>. --}}
@if($baskets->where('status', 'draft')->isNotEmpty())
    <div class="border rounded p-3 mt-3">
        <h6 class="mb-2">Combine Draft Baskets into One Order</h6>
        <p class="small text-muted">Select all baskets that should belong to the same physical shop order.</p>

        <form method="POST"
              action="{{ route('inventory.physical-shop-orders.baskets.confirm-combined') }}"
              onsubmit="return confirm('Finalize the selected baskets as ONE order? Stock will be deducted now.');">
            @csrf

            <div class="row g-2 mb-3">
                @foreach($baskets->where('status', 'draft') as $draftBasket)
                    <div class="col-md-4 col-lg-3">
                        <label class="border rounded p-2 d-flex gap-2 align-items-start h-100">
                            <input type="checkbox"
                                   name="basket_ids[]"
                                   value="{{ $draftBasket->id }}"
                                   class="form-check-input mt-1"
                                   {{ (string) request('basket') === (string) $draftBasket->id ? 'checked' : '' }}>
                            <span>
                                <strong>{{ $draftBasket->basket_name }}</strong><br>
                                <small class="text-muted">Basket #{{ $draftBasket->id }}</small>
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Physical Shop</label>
                    <select name="shopid" class="form-select" required>
                        <option value="">Select shop</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" @selected(old('shopid') == $shop->id)>
                                {{ $shop->shop_name }}{{ $shop->address ? ' — '.$shop->address : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Box Number</label>
                    <input type="text" name="box_no" class="form-control"
                           maxlength="200" value="{{ old('box_no') }}"
                           placeholder="Enter packing box number" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">Finalize Combined Order</button>
                </div>
            </div>
            <div class="small text-muted mt-2">
                Only selected draft baskets are included. Their items will be combined by barcode, checked against available stock,
                saved under one order number, and deducted from stock once.
            </div>
        </form>
    </div>
@endif


    </div></div>

    @if(!$activeBasket)

        <div class="alert alert-info">Create a basket or select an existing basket above to start adding garments.</div>

    @else

        <div class="card shadow-sm mb-3"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">

            <div>

                <h5 class="mb-1">{{ $activeBasket->basket_name }}</h5>

                <span class="badge {{ $activeBasket->status === 'confirmed' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($activeBasket->status) }}</span>

                @if($activeBasket->order_number)<span class="ms-2">Order: <strong>{{ $activeBasket->order_number }}</strong></span>@endif

            </div>

            @if($activeBasket->status === 'draft')

                <form method="POST" action="{{ route('inventory.physical-shop-orders.baskets.save', $activeBasket->id) }}">@csrf<button class="btn btn-outline-secondary">Save Draft</button></form>

            @endif

        </div></div>

        @if($activeBasket->status === 'draft')

            <div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Find Ready-to-Sell Garments</div><div class="card-body">

                <form id="filterForm" class="row g-2 mb-3">

                    <div class="col-sm-6 col-lg"><label class="form-label">Item Type</label><select class="form-select" name="item_type"><option value="">All types</option>@foreach($itemTypes as $option)<option value="{{ $option->id }}">{{ $option->itemtype }}</option>@endforeach</select></div>

                    <div class="col-sm-6 col-lg"><label class="form-label">Item Name</label><select class="form-select" name="item_name"><option value="">All items</option>@foreach($itemNames as $option)<option value="{{ $option->id }}">{{ $option->itemname }}</option>@endforeach</select></div>

                    <div class="col-sm-6 col-lg"><label class="form-label">Gender</label><select class="form-select" name="gender"><option value="">All genders</option>@foreach($genders as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach</select></div>

                    <div class="col-sm-6 col-lg"><label class="form-label">Composition</label><select class="form-select" name="composition"><option value="">All compositions</option>@foreach($compositions as $option)<option value="{{ $option->id }}">{{ $option->composition_details }}</option>@endforeach</select></div>

                    <div class="col-sm-6 col-lg"><label class="form-label">Size</label><select class="form-select" name="size"><option value="">All sizes</option>@foreach($sizes as $option)<option value="{{ $option->id }}">{{ $option->size }}</option>@endforeach</select></div>

                    <div class="col-sm-8 col-lg-3"><label class="form-label">Search</label><input class="form-control" name="search" placeholder="Barcode, SKU, item name..."></div>

                    <div class="col-auto d-flex align-items-end"><button class="btn btn-primary" type="submit">Search</button></div>

                </form>

                <div class="table-responsive"><table class="table table-hover align-middle">

                    <thead><tr><th style="width:90px">Image</th><th>Product</th><th>Barcode / SKU</th><th>Type</th><th>Gender</th><th>Composition</th><th>Size</th><th>Available</th><th style="min-width:230px">Add to Basket</th></tr></thead>

                    <tbody id="productRows"><tr><td colspan="9" class="text-muted text-center">Loading available stock...</td></tr></tbody>

                </table></div>

                <div id="productPagination" class="d-flex gap-2 flex-wrap"></div>

            </div></div>

        @endif

        <div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Basket Items</div><div class="card-body"><div class="table-responsive">

            <table class="table table-striped align-middle">

                <thead><tr><th>Image</th><th>Barcode</th><th>SKU</th><th>Product</th><th>Type</th><th>Gender</th><th>Composition</th><th>Size</th><th>Qty</th>@if($activeBasket->status === 'draft')<th>Actions</th>@endif</tr></thead>

                <tbody>

                @forelse($items as $item)

                    <tr>

                        <td>

                            @if($item->image_path)

                                <img src="" data-basket-image="{{ e($item->image_path) }}" alt="{{ $item->product_name }}" class="physical-shop-product-image" data-image-candidates="[]" data-image-index="0" >

                                <div class="physical-shop-no-image" style="display:none">No image</div>

                            @else

                                <div class="physical-shop-no-image">No image</div>

                            @endif

                        </td>

                        <td>{{ $item->barcode }}</td><td>{{ $item->sku }}</td><td>{{ $item->product_name }}</td><td>{{ $item->item_type_name }}</td><td>{{ $item->gender_name }}</td><td>{{ $item->composition_name }}</td><td>{{ $item->size_name }}</td>

                        <td>

                            @if($activeBasket->status === 'draft')

                                <form method="POST" action="{{ route('inventory.physical-shop-orders.baskets.items.update', [$activeBasket->id, $item->id]) }}" class="d-flex gap-1">@csrf @method('PUT')<input type="number" min="1" name="quantity" value="{{ $item->quantity }}" class="form-control form-control-sm" style="width:85px" required><button class="btn btn-sm btn-outline-primary">Update</button></form>

                            @else{{ $item->quantity }}@endif

                        </td>

                        @if($activeBasket->status === 'draft')

                            <td><form method="POST" action="{{ route('inventory.physical-shop-orders.baskets.items.destroy', [$activeBasket->id, $item->id]) }}" onsubmit="return confirm('Remove this item?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></td>

                        @endif

                    </tr>

                @empty

                    <tr><td colspan="{{ $activeBasket->status === 'draft' ? 10 : 9 }}" class="text-center text-muted py-4">No items in this basket yet.</td></tr>

                @endforelse

                </tbody>

            </table>

        </div></div></div>

        @if($activeBasket->status === 'draft')

            <div class="card shadow-sm"><div class="card-header fw-semibold">Finalize Physical Shop Order</div><div class="card-body">

                <form method="POST" action="{{ route('inventory.physical-shop-orders.baskets.confirm', $activeBasket->id) }}" class="row g-3" onsubmit="return confirm('Finalize this order? Stock will be deducted now.');">

                    @csrf

                    <div class="col-md-5"><label class="form-label">Physical Shop</label>

                        <select name="shopid" class="form-select" required>

                            <option value="">Select shop</option>

                            @foreach($shops as $shop)

                                <option value="{{ $shop->id }}" @selected(old('shopid') == $shop->id)>{{ $shop->shop_name }}{{ $shop->address ? ' — '.$shop->address : '' }}</option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-4"><label class="form-label">Box Number</label><input type="text" name="box_no" class="form-control" maxlength="200" value="{{ old('box_no') }}" placeholder="Enter packing box number" required></div>

                    <div class="col-md-3 d-flex align-items-end"><button class="btn btn-success w-100" {{ $items->isEmpty() ? 'disabled' : '' }}>Finalize Order</button></div>

                    <div class="col-12 small text-muted">Finalization saves the order, records the box number in order-detail remarks, and updates stock. Draft baskets do not deduct stock.</div>

                </form>

            </div></div>

        @endif

    @endif

</div>

@if(isset($activeBasket) && $activeBasket && $activeBasket->status === 'draft')

<script>

(function () {

    'use strict';

    const form = document.getElementById('filterForm');

    const tbody = document.getElementById('productRows');

    const pagination = document.getElementById('productPagination');

    if (!form || !tbody || !pagination) {

        console.error('Physical Shop Order: required HTML elements not found.', {

            filterForm: !!form,

            productRows: !!tbody,

            productPagination: !!pagination

        });

        return;

    }

    const productsUrl = @json(route('inventory.physical-shop-orders.products'));

    const addUrl = @json(route('inventory.physical-shop-orders.baskets.items.store', $activeBasket->id));

    const csrf = @json(csrf_token());

    function esc(value) {

        return String(value ?? '').replace(/[&<>"']/g, function (ch) {

            return {

                '&': '&amp;',

                '<': '&lt;',

                '>': '&gt;',

                '"': '&quot;',

                "'": '&#039;'

            }[ch];

        });

    }

    function showMessage(message, className = 'text-muted') {

        tbody.innerHTML =

            '<tr><td colspan="9" class="text-center py-4 ' +

            className + '">' + esc(message) + '</td></tr>';

    }

    function imageCell(value, name) {

        let path = String(value || '').trim();

        try {

            const parsed = JSON.parse(path);

            if (Array.isArray(parsed)) {

                path = parsed.find(function (item) {

                    return /\.(jpe?g|png|gif|webp|bmp)(\?.*)?$/i.test(String(item));

                }) || '';

            }

        } catch (e) {

            // Legacy image paths may not be JSON.

        }

        if (!path || !/\.(jpe?g|png|gif|webp|bmp)(\?.*)?$/i.test(path)) {

            return '<td><div class="physical-shop-no-image">No image</div></td>';

        }

        if (/^https?:\/\//i.test(path)) {

            // Keep an absolute image URL as supplied.

        } else {

            path = path.replace(/^(?:\.\.\/|\.\/)+/, '').replace(/^\/+/, '');

            path = @json(asset('')) + path;

        }

        return '<td>' +

            '<img src="' + esc(path) + '" ' +

            'alt="' + esc(name || 'Product') + '" ' +

            'class="physical-shop-product-image" ' +



            '<div class="physical-shop-no-image" style="display:none">No image</div>' +

            '</td>';

    }

    function renderPagination(data) {

        pagination.innerHTML = '';

        (data.links || []).forEach(function (link) {

            if (!link.url || !link.label) return;

            const button = document.createElement('button');

            button.type = 'button';

            button.className = 'btn btn-sm ' +

                (link.active ? 'btn-primary' : 'btn-outline-secondary');

            button.innerHTML = link.label;

            button.disabled = Boolean(link.active);

            button.addEventListener('click', function () {

                load(link.url, false);

            });

            pagination.appendChild(button);

        });

    }

    async function load(url, includeFilters = true) {

        showMessage('Loading available stock...');

        try {

            let target = url || productsUrl;

            if (includeFilters) {

                const params = new URLSearchParams(new FormData(form));

                const query = params.toString();

                if (query) {

                    target += (target.includes('?') ? '&' : '?') + query;

                }

            }

            console.log('Loading Physical Shop products from:', target);

            const response = await fetch(target, {

                method: 'GET',

                headers: {

                    'Accept': 'application/json',

                    'X-Requested-With': 'XMLHttpRequest'

                },

                credentials: 'same-origin'

            });

            if (!response.ok) {

                const responseText = await response.text();

                console.error('Products request failed:', response.status, responseText);

                throw new Error('Product request failed: HTTP ' + response.status);

            }

            const data = await response.json();

            console.log('Physical Shop API response:', data);

            const rows = Array.isArray(data.data) ? data.data : [];

            if (!rows.length) {

                showMessage('No available garments found.');

                renderPagination(data);

                return;

            }

            tbody.innerHTML = rows.map(function (p) {

                const barcode = p.barcode || '';

                const qty = Math.max(0, Number(p.available_qty || 0));

                return '<tr>' +

                    imageCell(p.image_path || p.img_path, p.product_name) +

                    '<td>' + esc(p.product_name) + '</td>' +

                    '<td>' + esc(barcode) + '<br><small>' + esc(p.sku) + '</small></td>' +

                    '<td>' + esc(p.item_type_name) + '</td>' +

                    '<td>' + esc(p.gender_name) + '</td>' +

                    '<td>' + esc(p.composition_name) + '</td>' +

                    '<td>' + esc(p.size_name) + '</td>' +

                    '<td><span class="badge text-bg-success">' + esc(qty) + '</span></td>' +

                    '<td>' +

                        '<form method="POST" action="' + esc(addUrl) + '" class="d-flex gap-2 align-items-center">' +

                            '<input type="hidden" name="_token" value="' + esc(csrf) + '">' +

                            '<input type="hidden" name="barcode" value="' + esc(barcode) + '">' +

                            '<input type="number" name="quantity" min="1" max="' + esc(qty) + '" value="1" required class="form-control form-control-sm" style="width:80px" ' + (qty < 1 ? 'disabled' : '') + '>' +

                            '<button type="submit" class="btn btn-sm btn-primary" ' + (qty < 1 ? 'disabled' : '') + '>Add</button>' +

                        '</form>' +

                    '</td>' +

                '</tr>';

            }).join('');

            renderPagination(data);

            console.log('Products rendered:', rows.length);

        } catch (error) {

            console.error('Physical Shop stock loading error:', error);

            showMessage(error.message || 'Unable to load stock. Check the browser console.', 'text-danger');

        }

    }

    form.addEventListener('submit', function (event) {

        event.preventDefault();

        load(productsUrl, true);

    });

    load(productsUrl, true);

})();

</script>

@endif

@endsection
