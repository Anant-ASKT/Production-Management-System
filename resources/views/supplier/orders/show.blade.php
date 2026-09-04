@extends('layouts.supplier')

@section('title', 'Order #' . $orderData['order_number'])
@section('page-title', 'Order Details')

@section('content')
<div class="container-fluid p-0">

    {{-- TOP ACTION BAR --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 top-action-bar">
        <a href="{{ route('supplier.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-xs">
            <i class="bi bi-arrow-left me-1"></i> Back to Orders
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
            <i class="bi bi-pencil-square me-1"></i> Update Status & Shipping
        </button>
    </div>

    {{-- FLASH ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ORDER HEADER BANNER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-4 bg-primary-subtle text-primary">
                            <i class="bi bi-bag-check-fill fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Order Number</div>
                            <h2 class="fw-bold text-dark mb-0">#{{ $orderData['order_number'] }}</h2>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-calendar-event me-1"></i> Ordered on: <strong>{{ $orderData['date_created'] }}</strong>
                                <span class="mx-2">•</span>
                                <i class="bi bi-shop me-1"></i> Store: <strong>{{ $orderData['selling_store_name'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 text-md-end">
                    <div class="text-muted small mb-1">Order Status:</div>
                    @php
                        $s = strtolower($orderData['status'] ?? 'pending');
                        $badgeClass = 'bg-secondary text-white';
                        $statusText = 'Pending';
                        if ($s === 'processing') { $badgeClass = 'bg-primary text-white'; $statusText = 'Processing (Packing)'; }
                        elseif ($s === 'shipped') { $badgeClass = 'bg-info text-dark'; $statusText = 'Shipped / In Transit'; }
                        elseif ($s === 'completed') { $badgeClass = 'bg-success text-white'; $statusText = 'Delivered / Completed'; }
                        elseif ($s === 'on-hold') { $badgeClass = 'bg-warning text-dark'; $statusText = 'On Hold'; }
                        elseif ($s === 'cancelled') { $badgeClass = 'bg-danger text-white'; $statusText = 'Cancelled'; }
                        elseif ($s === 'refunded') { $badgeClass = 'bg-dark text-white'; $statusText = 'Refunded'; }
                    @endphp
                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-circle-fill fs-8 me-1"></i> {{ $statusText }}
                    </span>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="bi bi-pencil me-1"></i> Change Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 1. CUSTOMER DETAILS & SHIPPING DETAILS --}}
    <div class="row g-4 mb-4">
        {{-- Customer Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="text-primary fw-bold text-uppercase fs-7 d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle fs-5"></i> Customer Details
                    </div>
                    <span class="badge bg-light text-secondary border">Buyer Info</span>
                </div>

                @php
                    $b = $orderData['billing'] ?? [];
                    $s = $orderData['shipping'] ?? [];
                    $custName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?: trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) ?: 'Customer';
                    $phone = $b['phone'] ?? ($s['phone'] ?? null);
                    $email = $b['email'] ?? null;
                @endphp

                <h4 class="fw-bold text-dark mb-3">{{ $custName }}</h4>

                <div class="d-flex flex-column gap-2 mb-3">
                    @if(!empty($phone))
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border">
                            <div class="d-flex align-items-center gap-2 text-dark">
                                <i class="bi bi-telephone-fill text-success fs-6"></i>
                                <span class="fw-semibold">{{ $phone }}</span>
                            </div>
                            <a href="tel:{{ $phone }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold">
                                <i class="bi bi-telephone me-1"></i> Call
                            </a>
                        </div>
                    @else
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i> Phone: <em>Not provided</em></div>
                    @endif

                    @if(!empty($email))
                        <div class="d-flex align-items-center gap-2 text-secondary p-2 rounded-3 bg-light border">
                            <i class="bi bi-envelope-fill text-primary fs-6"></i>
                            <span class="small">{{ $email }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
                    <span><i class="bi bi-credit-card me-1"></i> Payment: <strong>{{ $orderData['payment_method'] }}</strong></span>
                </div>

                @if(!empty($orderData['customer_note']))
                    <div class="mt-3 p-3 rounded-3 bg-warning-subtle text-dark border border-warning-subtle small">
                        <strong><i class="bi bi-chat-dots-fill me-1 text-warning"></i> Customer Note:</strong>
                        <div class="mt-1">{{ $orderData['customer_note'] }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Shipping & Delivery Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="text-primary fw-bold text-uppercase fs-7 d-flex align-items-center gap-2">
                        <i class="bi bi-truck fs-5"></i> Shipping & Delivery Details
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                        <i class="bi bi-pencil me-1"></i> Edit Shipping
                    </button>
                </div>

                @php
                    $address1 = $s['address_1'] ?? ($b['address_1'] ?? '');
                    $address2 = $s['address_2'] ?? ($b['address_2'] ?? '');
                    $city = $s['city'] ?? ($b['city'] ?? '');
                    $state = $s['state'] ?? ($b['state'] ?? '');
                    $postcode = $s['postcode'] ?? ($b['postcode'] ?? '');
                    $country = $s['country'] ?? ($b['country'] ?? '');
                @endphp

                {{-- Delivery Address --}}
                <div class="mb-3">
                    <div class="text-muted small fw-semibold mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Delivery Address:</div>
                    @if(!empty($address1) || !empty($city))
                        <div class="text-dark fs-6 lh-base bg-light p-3 rounded-3 border">
                            {{ $address1 }} {{ $address2 }}<br>
                            <strong>{{ $city }}, {{ $state }} {{ $postcode }}</strong><br>
                            <span class="text-muted">{{ $country ?: 'India' }}</span>
                        </div>
                    @else
                        <div class="text-muted bg-light p-3 rounded-3 border">Location: <strong>{{ $city ?: 'India' }}</strong></div>
                    @endif
                </div>

                {{-- Courier & Tracking ID --}}
                <div class="mt-auto pt-2">
                    @if(!empty($orderData['tracking_id']) || !empty($orderData['courier_name']))
                        <div class="p-3 rounded-3 bg-primary-subtle border border-primary-subtle text-dark">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="badge bg-primary text-white text-uppercase px-2 py-1">
                                        {{ $orderData['courier_name'] ?: 'Courier' }}
                                    </span>
                                    <div class="fw-bold fs-6 font-monospace mt-1 text-dark">
                                        AWB: {{ $orderData['tracking_id'] }}
                                    </div>
                                    @if(!empty($orderData['shipped_at_formatted']))
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-clock me-1"></i> Dispatched: {{ $orderData['shipped_at_formatted'] }}
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-light border" onclick="copyTrackingId('{{ $orderData['tracking_id'] }}')" title="Copy Tracking ID">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                    @if(!empty($orderData['tracking_url']))
                                        <a href="{{ $orderData['tracking_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary" title="Track Live Shipment">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Track
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($orderData['shipping_notes']))
                                <div class="text-muted small mt-2 pt-2 border-top border-primary-subtle">
                                    <i class="bi bi-sticky me-1"></i> {{ $orderData['shipping_notes'] }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-3 rounded-3 bg-light border text-muted small d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-info-circle me-1"></i> No tracking ID added yet.</span>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                + Add Tracking
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2. ORDER DETAILS (PRODUCTS & PAYABLE TOTAL) --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-box-seam text-primary me-2"></i> Order Details & Products ({{ count($orderData['supplier_items']) }})
            </h5>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-bold">
                Total Products: {{ count($orderData['supplier_items']) }}
            </span>
        </div>

        <div class="card-body p-4">
            @forelse($orderData['supplier_items'] as $item)
                <div class="d-flex flex-wrap flex-md-nowrap gap-4 p-3 rounded-4 bg-light mb-3 align-items-center">
                    {{-- Image --}}
                    <div class="flex-shrink-0">
                        @if(!empty($item['image']))
                            <img src="{{ $item['image'] }}" class="rounded-4 border shadow-xs" style="width: 100px; height: 100px; object-fit: cover;" alt="Product">
                        @else
                            <div class="rounded-4 border bg-white d-flex align-items-center justify-content-center text-muted" style="width: 100px; height: 100px;">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="flex-grow-1">
                        <h5 class="fw-bold text-dark mb-1">{{ $item['name'] }}</h5>

                        <div class="d-flex flex-wrap gap-2 my-2 align-items-center">
                            <span class="badge bg-dark text-white px-3 py-1.5 rounded-pill font-monospace fs-7">
                                <i class="bi bi-upc me-1"></i> SKU: {{ $item['sku'] }}
                            </span>
                            @if(!empty($item['size']))
                                <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 rounded-pill">
                                    Size: <strong>{{ $item['size'] }}</strong>
                                </span>
                            @endif
                            @if(!empty($item['colour']))
                                <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 rounded-pill">
                                    Color: <strong>{{ $item['colour'] }}</strong>
                                </span>
                            @endif
                            @if(!empty($item['product_type']))
                                <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 rounded-pill">
                                    Type: <strong>{{ $item['product_type'] }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap gap-4 mt-3 pt-2 border-top">
                            <div>
                                <div class="text-muted small">Quantity:</div>
                                <span class="badge bg-primary fs-6 px-3 py-1 rounded-pill fw-bold">
                                    {{ $item['quantity'] }} Piece{{ $item['quantity'] > 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div>
                                <div class="text-muted small">Unit Price:</div>
                                <div class="fw-bold text-dark fs-6">{{ $orderData['currency_symbol'] }}{{ number_format($item['price'], 2) }}</div>
                            </div>

                            <div>
                                <div class="text-muted small">Item Total:</div>
                                <div class="fw-bold text-primary fs-5">{{ $orderData['currency_symbol'] }}{{ number_format($item['total'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    No products found for your account in this order.
                </div>
            @endforelse
        </div>

        {{-- TOTAL PAYABLE FOOTER --}}
        <div class="card-footer bg-light p-4 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold text-dark fs-5">Total Payable for Your Products:</span>
                    <div class="text-muted small">Calculated from supplier product line items</div>
                </div>
                <span class="fw-bold text-primary fs-2">{{ $orderData['supplier_total_formatted'] }}</span>
            </div>
        </div>
    </div>

    {{-- 3. ORDER STATUS & AUDIT LOG HISTORY --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-clock-history text-primary me-2"></i> Order Status History & Activity Log
            </h5>
            <span class="badge bg-secondary-subtle text-dark border rounded-pill px-3 py-1">
                {{ count($orderData['histories'] ?? []) }} Entries
            </span>
        </div>

        <div class="card-body p-4">
            <div class="timeline">
                @forelse($orderData['histories'] ?? [] as $history)
                    @php
                        $hStatus = strtolower($history->to_status ?? '');
                        $iconClass = 'bi-arrow-repeat bg-primary';
                        if ($hStatus === 'completed') { $iconClass = 'bi-check-circle-fill bg-success'; }
                        elseif ($hStatus === 'shipped') { $iconClass = 'bi-truck bg-info'; }
                        elseif ($hStatus === 'cancelled') { $iconClass = 'bi-x-circle-fill bg-danger'; }
                        elseif ($hStatus === 'on-hold') { $iconClass = 'bi-pause-circle-fill bg-warning'; }
                        elseif ($hStatus === 'processing') { $iconClass = 'bi-box-seam bg-primary'; }
                    @endphp

                    <div class="timeline-item d-flex gap-3 mb-4 position-relative">
                        <div class="timeline-icon text-white rounded-circle d-flex align-items-center justify-content-center shadow-xs flex-shrink-0 {{ $iconClass }}" style="width: 38px; height: 38px; font-size: 16px;">
                            <i class="bi {{ explode(' ', $iconClass)[0] }}"></i>
                        </div>
                        <div class="timeline-content bg-light p-3 rounded-4 flex-grow-1 border">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-1 gap-2">
                                <h6 class="fw-bold text-dark mb-0">
                                    {{ $history->action }}
                                    @if(!empty($history->to_status))
                                        <span class="badge bg-secondary-subtle text-dark border ms-1 px-2.5 py-0.5 rounded-pill font-monospace">
                                            {{ ucfirst($history->from_status ?: 'initial') }} &rarr; {{ ucfirst($history->to_status) }}
                                        </span>
                                    @endif
                                </h6>
                                <span class="text-muted small">
                                    <i class="bi bi-clock me-1"></i> {{ $history->created_at ? $history->created_at->format('d M Y, h:i A') : '—' }}
                                </span>
                            </div>

                            @if(!empty($history->comment))
                                <p class="text-secondary small mb-1 mt-1">{{ $history->comment }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-3 mt-2 pt-2 border-top text-muted small">
                                @if(!empty($history->user_name))
                                    <div><i class="bi bi-person-fill me-1"></i> By: <strong>{{ $history->user_name }}</strong></div>
                                @endif
                                @if(!empty($history->courier_name))
                                    <div><i class="bi bi-truck me-1"></i> Carrier: <strong>{{ $history->courier_name }}</strong></div>
                                @endif
                                @if(!empty($history->tracking_id))
                                    <div><i class="bi bi-upc me-1"></i> AWB: <strong>{{ $history->tracking_id }}</strong></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="timeline-item d-flex gap-3 mb-2 position-relative">
                        <div class="timeline-icon text-white bg-success rounded-circle d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 38px; height: 38px; font-size: 16px;">
                            <i class="bi bi-bag-check-fill"></i>
                        </div>
                        <div class="timeline-content bg-light p-3 rounded-4 flex-grow-1 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold text-dark mb-0">Order Placed & Received</h6>
                                <span class="text-muted small">{{ $orderData['date_created'] }}</span>
                            </div>
                            <p class="text-secondary small mb-0">Order received from {{ $orderData['selling_store_name'] }}. Initial status: {{ ucfirst($orderData['status']) }}.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- SIMPLE UPDATE STATUS & SHIPPING MODAL --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('supplier.orders.update-status', $orderData['record_id']) }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-bold" id="updateStatusModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> Update Order Status & Shipping
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Status Selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Order Status *</label>
                        <select name="status" id="modalOrderStatus" class="form-select rounded-3 py-2 fw-semibold" required>
                            <option value="pending" {{ strtolower($orderData['status']) === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="processing" {{ strtolower($orderData['status']) === 'processing' ? 'selected' : '' }}>📦 Processing (Pack Order)</option>
                            <option value="shipped" {{ strtolower($orderData['status']) === 'shipped' ? 'selected' : '' }}>🚚 Shipped / In Transit</option>
                            <option value="completed" {{ strtolower($orderData['status']) === 'completed' ? 'selected' : '' }}>✅ Delivered / Completed</option>
                            <option value="on-hold" {{ strtolower($orderData['status']) === 'on-hold' ? 'selected' : '' }}>⏸️ On Hold</option>
                            <option value="cancelled" {{ strtolower($orderData['status']) === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            <option value="refunded" {{ strtolower($orderData['status']) === 'refunded' ? 'selected' : '' }}>🔄 Refunded</option>
                        </select>
                    </div>

                    {{-- Courier / Shipping Partner --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Courier / Shipping Partner</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-truck"></i></span>
                            <input type="text" name="courier_name" id="modalCourierName" list="courierList" class="form-control rounded-end-3 py-2" value="{{ old('courier_name', $orderData['courier_name']) }}" placeholder="e.g. Blue Dart, Delhivery, DTDC..." oninput="autoSuggestTrackingUrlModal()">
                        </div>
                        <datalist id="courierList">
                            <option value="Delhivery">
                            <option value="Blue Dart">
                            <option value="DTDC">
                            <option value="India Post">
                            <option value="Ekart Logistics">
                            <option value="Shadowfax">
                            <option value="Xpressbees">
                            <option value="Ecom Express">
                            <option value="Shiprocket">
                            <option value="FedEx">
                            <option value="DHL Express">
                        </datalist>
                    </div>

                    {{-- Tracking / AWB Number --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Tracking ID / AWB Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="tracking_id" id="modalTrackingId" class="form-control rounded-end-3 py-2 font-monospace" value="{{ old('tracking_id', $orderData['tracking_id']) }}" placeholder="e.g. 1234567890" oninput="autoSuggestTrackingUrlModal()">
                        </div>
                    </div>

                    {{-- Tracking URL (Optional) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Tracking Link / URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="tracking_url" id="modalTrackingUrl" class="form-control rounded-end-3 py-2 small" value="{{ old('tracking_url', $orderData['tracking_url']) }}" placeholder="Auto-generated or custom tracking URL">
                        </div>
                        <small class="text-muted" style="font-size: 11px;">Auto-generated for major Indian couriers if left empty.</small>
                    </div>

                    {{-- Dispatch / Shipped Date --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Dispatch / Shipped Date</label>
                        <input type="datetime-local" name="shipped_at" class="form-control rounded-3 py-2" value="{{ old('shipped_at', $orderData['shipped_at'] ?? now()->format('Y-m-d\TH:i')) }}">
                    </div>

                    {{-- Shipping Notes --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Shipping / Packing Note</label>
                        <textarea name="shipping_notes" rows="2" class="form-control rounded-3" placeholder="e.g. Packed in box 2 with bubble wrap">{{ old('shipping_notes', $orderData['shipping_notes']) }}</textarea>
                    </div>

                    {{-- History Log Remark --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">History Log Remark</label>
                        <input type="text" name="comment" class="form-control rounded-3 py-2" placeholder="Optional remark for timeline history">
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs">
                        <i class="bi bi-check2-circle me-1"></i> Save & Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 5px;
    }
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        top: 38px;
        bottom: -20px;
        left: 19px;
        width: 2px;
        background: #e2e8f0;
        z-index: 1;
    }
    .timeline-icon {
        z-index: 2;
    }
    @media print {
        .app-sidebar, .app-header, .btn, .top-action-bar, .modal, form button {
            display: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function copyTrackingId(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            alert('Tracking AWB Number copied to clipboard: ' + text);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }

    function autoSuggestTrackingUrlModal() {
        const courier = (document.getElementById('modalCourierName')?.value || '').toLowerCase().trim();
        const tracking = (document.getElementById('modalTrackingId')?.value || '').trim();
        const urlInput = document.getElementById('modalTrackingUrl');

        if (!urlInput || !tracking) return;

        let autoUrl = '';
        if (courier.includes('delhivery')) {
            autoUrl = `https://www.delhivery.com/track/package/${tracking}`;
        } else if (courier.includes('bluedart') || courier.includes('blue dart')) {
            autoUrl = `https://www.bluedart.com/tracking?track=${tracking}`;
        } else if (courier.includes('dtdc')) {
            autoUrl = `https://www.dtdc.in/tracking/shipment-tracking.asp?trackingNo=${tracking}`;
        } else if (courier.includes('india post') || courier.includes('postal')) {
            autoUrl = `https://www.indiapost.gov.in/_layouts/15/dpt.cptc.va/trackconsignment.aspx`;
        } else if (courier.includes('fedex')) {
            autoUrl = `https://www.fedex.com/fedextrack/?trknbr=${tracking}`;
        } else if (courier.includes('dhl')) {
            autoUrl = `https://www.dhl.com/en/express/tracking.html?AWB=${tracking}`;
        } else if (courier.includes('ekart')) {
            autoUrl = `https://ekartlogistics.com/shipmenttrack/${tracking}`;
        } else if (courier.includes('shadowfax')) {
            autoUrl = `https://tracker.shadowfax.in/#/track/${tracking}`;
        } else if (courier.includes('xpressbees')) {
            autoUrl = `https://www.xpressbees.com/track?isawb=Yes&trackid=${tracking}`;
        } else if (courier.includes('ecom express')) {
            autoUrl = `https://ecomexpress.in/tracking/?awb_number=${tracking}`;
        } else if (courier.includes('shiprocket')) {
            autoUrl = `https://shiprocket.co/tracking/${tracking}`;
        }

        if (autoUrl && (!urlInput.value || urlInput.value.includes('track') || urlInput.value.includes('http'))) {
            urlInput.value = autoUrl;
        }
    }
</script>
@endpush
@endsection
