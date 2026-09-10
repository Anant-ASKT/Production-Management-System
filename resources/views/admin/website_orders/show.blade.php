@extends('layouts.app')

@section('title', 'Order #' . $orderData['order_number'])

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

    {{-- TOP ACTION & NAVIGATION HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.website-orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-2">
                <i class="bi bi-arrow-left me-1"></i> Back to Orders
            </a>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold text-dark mb-0">Order #{{ $orderData['order_number'] }}</h4>
                @php
                    $s = strtolower(trim($orderData['status'] ?? 'order confirmed'));
                    $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                    $icon = 'bi-circle';
                    $statusLabel = $orderData['status'];

                    if (in_array($s, ['order confirmed', 'order_confirmed', 'processing'])) {
                        $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                        $icon = 'bi-check2-circle';
                        $statusLabel = 'Order confirmed';
                    } elseif ($s === 'shipped') {
                        $badgeClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                        $icon = 'bi-truck';
                        $statusLabel = 'Shipped';
                    } elseif (in_array($s, ['delivered', 'completed'])) {
                        $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                        $icon = 'bi-box2-heart';
                        $statusLabel = 'Delivered';
                    } elseif ($s === 'cancelled' || $s === 'failed') {
                        $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                        $icon = 'bi-x-circle';
                    }
                @endphp
                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                    <i class="bi {{ $icon }}"></i> {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-primary rounded-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                <i class="bi bi-pencil-square me-1"></i> Update Status & Shipping
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#emailSupplierModal">
                <i class="bi bi-envelope me-1"></i> Email Supplier
            </button>
            <button class="btn btn-sm btn-outline-secondary rounded-2" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-2 py-2 px-3 mb-3 small" role="alert">
            <i class="bi bi-check2-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-2 py-2 px-3 mb-3 small" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $b = $orderData['billing'] ?? [];
        $s = $orderData['shipping'] ?? [];
        $custName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) 
            ?: trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) 
            ?: 'Customer';
        $phone = $b['phone'] ?? ($s['phone'] ?? '—');
        $email = $b['email'] ?? ($orderData['customer_email'] ?? '—');

        $shipName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?: $custName;
        $address1 = $s['address_1'] ?? ($b['address_1'] ?? '');
        $address2 = $s['address_2'] ?? ($b['address_2'] ?? '');
        $city = $s['city'] ?? ($b['city'] ?? '');
        $state = $s['state'] ?? ($b['state'] ?? '');
        $postcode = $s['postcode'] ?? ($b['postcode'] ?? '');
        $country = $s['country'] ?? ($b['country'] ?? '');
        $fullAddress = trim("{$address1} {$address2}, {$city}, {$state} {$postcode}, {$country}", " ,");
    @endphp

    {{-- 3-COLUMN SUMMARY CARD (ONE VIEW AT A GLANCE) --}}
    <div class="row g-3 mb-4">
        {{-- 1. ORDER & CUSTOMER INFO --}}
        <div class="col-md-4">
            <div class="card border rounded-3 bg-white h-100 shadow-2xs">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-person me-1.5 text-primary"></i> Customer Details
                    </span>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted ps-0" style="width: 100px;">Name:</td>
                            <td class="fw-semibold text-dark">{{ $custName }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Phone:</td>
                            <td class="text-dark">{{ $phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Email:</td>
                            <td class="text-dark text-break">{{ $email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Payment:</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $orderData['payment_method'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Store:</td>
                            <td class="text-dark fw-medium">{{ $orderData['selling_supplier_name'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Order Date:</td>
                            <td class="text-dark">{{ $orderData['date_created'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- 2. DELIVERY ADDRESS --}}
        <div class="col-md-4">
            <div class="card border rounded-3 bg-white h-100 shadow-2xs">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-geo-alt me-1.5 text-primary"></i> Delivery Address
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="fw-semibold text-dark mb-1">{{ $shipName }}</div>
                    <div class="text-secondary small lh-base mb-2">
                        @if(!empty($address1)) <div>{{ $address1 }}</div> @endif
                        @if(!empty($address2)) <div>{{ $address2 }}</div> @endif
                        <div>{{ $city }}@if(!empty($city) && !empty($state)), @endif{{ $state }} {{ $postcode }}</div>
                        <div class="text-muted">{{ $country ?: 'India' }}</div>
                    </div>

                    @if(!empty($orderData['customer_note']))
                        <div class="mt-2 pt-2 border-top small">
                            <span class="text-muted d-block fw-semibold">Note:</span>
                            <span class="text-dark">{{ $orderData['customer_note'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3. SHIPPING & TRACKING DETAILS (ADMIN FILLED) --}}
        <div class="col-md-4">
            <div class="card border rounded-3 bg-white h-100 shadow-2xs">
                @php
                    $isOrderDelivered = strtolower(trim($orderData['status'])) === 'delivered';
                    $isOrderConfirmed = in_array(strtolower(trim($orderData['status'])), ['order confirmed', 'order_confirmed', 'processing']);
                    $hasTracking = !empty($orderData['tracking_id']) || !empty($orderData['courier_name']);
                @endphp
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi {{ $isOrderDelivered ? 'bi-check2-circle text-success' : 'bi-truck text-primary' }} me-1.5"></i>
                        {{ $isOrderDelivered ? 'Delivery & Shipment' : 'Shipping & Tracking' }}
                    </span>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                        Edit
                    </button>
                </div>
                <div class="card-body p-3">
                    @if($isOrderConfirmed && !$hasTracking)
                        <div class="py-2 text-center text-muted small">
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mb-2 px-2 py-1">
                                <i class="bi bi-hourglass-split me-1"></i> Awaiting Dispatch
                            </span>
                            <p class="mb-0 text-secondary" style="font-size: 0.8rem;">Courier partner and tracking ID can be assigned when the order is shipped.</p>
                        </div>
                    @else
                        @if($isOrderDelivered)
                            <div class="mb-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Delivered Successfully
                                </span>
                            </div>
                        @endif
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0" style="width: 110px;">Courier:</td>
                                <td class="fw-semibold text-dark">
                                    {{ $orderData['courier_name'] ?: ($isOrderDelivered ? 'Direct / In-person' : 'Not assigned') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">AWB / Tracking:</td>
                                <td>
                                    @if(!empty($orderData['tracking_id']))
                                        <span class="fw-bold font-monospace text-dark">{{ $orderData['tracking_id'] }}</span>
                                        @if(!empty($orderData['tracking_url']))
                                            <a href="{{ $orderData['tracking_url'] }}" target="_blank" class="ms-1 text-primary text-decoration-none">
                                                <i class="bi bi-box-arrow-up-right"></i> Track
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-muted fst-italic">{{ $isOrderDelivered ? 'Completed' : 'Not added' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">{{ $isOrderDelivered ? 'Dispatched:' : 'Shipped Date:' }}</td>
                                <td class="text-dark">
                                    {{ $orderData['shipped_at_formatted'] ?: '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Shipping Note:</td>
                                <td class="text-dark">
                                    {{ $orderData['shipping_notes'] ?: '—' }}
                                </td>
                            </tr>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ORDERED ITEMS TABLE --}}
    <div class="card border rounded-3 bg-white mb-4 shadow-2xs">
        <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-box-seam me-1.5 text-primary"></i> Ordered Items
            </span>
            <span class="badge bg-white text-secondary border">
                {{ count($orderData['line_items']) }} {{ count($orderData['line_items']) === 1 ? 'Item' : 'Items' }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-secondary" style="font-size: 0.72rem;">
                        <tr>
                            <th class="ps-3" style="width: 40px;">#</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Manufacturer / Origin</th>
                            <th class="text-center" style="width: 100px;">Price</th>
                            <th class="text-center" style="width: 80px;">Qty</th>
                            <th class="text-end pe-3" style="width: 120px;">Total</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($orderData['line_items'] as $idx => $item)
                            <tr>
                                <td class="ps-3 text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item['name'] }}</div>
                                    @php
                                        $attrs = [];
                                        if(!empty($item['colour'])) $attrs[] = $item['colour'];
                                        if(!empty($item['size'])) $attrs[] = 'Size: ' . $item['size'];
                                    @endphp
                                    @if(count($attrs) > 0)
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ implode(' • ', $attrs) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-monospace text-dark">{{ $item['resolved_sku'] ?: ($item['sku'] ?: '—') }}</span>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $item['origin_supplier_name'] ?: 'In-house' }}</span>
                                    @if(!empty($item['spec_id']))
                                        <a href="{{ route('admin.publish-products.show', $item['spec_id']) }}" target="_blank" class="ms-1 text-primary text-decoration-none" title="View Specification">
                                            <i class="bi bi-box-arrow-up-right" style="font-size: 0.75rem;"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center font-monospace">
                                    {{ $orderData['currency_symbol'] }}{{ number_format($item['price'] ?? 0, 2) }}
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $item['quantity'] ?? 1 }}
                                </td>
                                <td class="text-end pe-3 font-monospace fw-bold text-dark">
                                    {{ $orderData['currency_symbol'] }}{{ number_format($item['total'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No items in this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TOTALS SUMMARY --}}
            <div class="d-flex justify-content-end p-3 border-top bg-light-subtle">
                <div style="min-width: 260px;" class="small">
                    <div class="d-flex justify-content-between text-muted mb-1">
                        <span>Subtotal:</span>
                        <span class="font-monospace text-dark fw-medium">
                            {{ $orderData['currency_symbol'] }}{{ number_format($orderData['subtotal'], 2) }}
                        </span>
                    </div>

                    @if($orderData['discount_total'] > 0)
                        <div class="d-flex justify-content-between text-success mb-1">
                            <span>Discount:</span>
                            <span class="font-monospace fw-medium">
                                -{{ $orderData['currency_symbol'] }}{{ number_format($orderData['discount_total'], 2) }}
                            </span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between text-muted mb-1">
                        <span>Shipping:</span>
                        <span class="font-monospace text-dark fw-medium">
                            {{ $orderData['shipping_total'] > 0 ? ($orderData['currency_symbol'] . number_format($orderData['shipping_total'], 2)) : 'Free' }}
                        </span>
                    </div>

                    @if($orderData['total_tax'] > 0)
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>Tax:</span>
                            <span class="font-monospace text-dark fw-medium">
                                {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total_tax'], 2) }}
                            </span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center pt-2 mt-1 border-top">
                        <span class="fw-bold text-dark fs-6">Grand Total:</span>
                        <span class="fw-bold text-primary fs-5 font-monospace">
                            {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total'], 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTIVITY & AUDIT LOG (SIMPLE TABLE) --}}
    @if(isset($orderData['histories']) && count($orderData['histories']) > 0)
    <div class="card border rounded-3 bg-white mb-4 shadow-2xs">
        <div class="card-header bg-light py-2 px-3 border-bottom">
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-clock-history me-1.5 text-primary"></i> Order Activity & History Log
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary text-uppercase" style="font-size: 0.72rem;">
                        <tr>
                            <th class="ps-3" style="width: 170px;">Date & Time</th>
                            <th style="width: 180px;">Action / Status</th>
                            <th style="width: 160px;">Updated By</th>
                            <th>Remark / Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderData['histories'] as $history)
                            <tr>
                                <td class="ps-3 text-muted">
                                    {{ $history->created_at ? $history->created_at->format('d M Y, h:i A') : '—' }}
                                </td>
                                <td class="fw-semibold text-dark">
                                    {{ $history->action ?: 'Order Updated' }}
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $history->user_name }}</span>
                                </td>
                                <td class="text-dark">
                                    {{ $history->comment ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- RAW WEBHOOK PAYLOAD COLLAPSED (OPTIONAL) --}}
    <div class="mb-3">
        <a class="text-muted small text-decoration-none" data-bs-toggle="collapse" href="#collapseRawJson" role="button" aria-expanded="false">
            <i class="bi bi-chevron-right me-1"></i> View Raw Webhook JSON
        </a>
        <div class="collapse mt-2" id="collapseRawJson">
            <pre class="bg-dark text-white p-3 rounded-2 small mb-0 font-monospace" style="max-height: 220px; overflow-y: auto;"><code>{{ json_encode($orderData['raw_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </div>
    </div>

</div>

{{-- MODAL: UPDATE STATUS & SHIPPING --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-3 overflow-hidden">
            <form action="{{ route('admin.website-orders.update-status', $orderData['record_id']) }}" method="POST">
                @csrf

                <div class="modal-header bg-light py-2 px-3 border-bottom">
                    <h6 class="modal-title fw-bold text-dark" id="updateStatusModalLabel">
                        <i class="bi bi-truck me-1.5 text-primary"></i> Update Order Status & Shipping
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    @php
                        $currStatus = strtolower(trim($orderData['status'] ?? ''));
                    @endphp
                    {{-- 1. Status --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Order Status *</label>
                        <select name="status" id="modalOrderStatus" class="form-select form-select-sm rounded-2 fw-semibold" required>
                            <option value="Order confirmed" {{ in_array($currStatus, ['order confirmed', 'order_confirmed', 'processing']) ? 'selected' : '' }}>
                                Order confirmed
                            </option>
                            <option value="Shipped" {{ $currStatus === 'shipped' ? 'selected' : '' }}>
                                Shipped
                            </option>
                            <option value="Delivered" {{ in_array($currStatus, ['delivered', 'completed']) ? 'selected' : '' }}>
                                Delivered
                            </option>
                        </select>
                    </div>

                    {{-- 2-6. Courier & Shipping Details (Shown when status is Shipped) --}}
                    <div id="shippingFieldsContainer">
                        <div class="row g-2 mb-3">
                            {{-- 2. Courier --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Courier / Shipping Partner</label>
                                <input type="text" name="courier_name" id="modalCourierName" list="courierList" class="form-control form-control-sm rounded-2" value="{{ old('courier_name', $orderData['courier_name']) }}" placeholder="e.g. Delhivery, Blue Dart, DTDC..." oninput="autoSuggestTrackingUrl()">
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

                            {{-- 3. AWB --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Tracking ID / AWB Number</label>
                                <input type="text" name="tracking_id" id="modalTrackingId" class="form-control form-control-sm rounded-2 font-monospace" value="{{ old('tracking_id', $orderData['tracking_id']) }}" placeholder="e.g. DEL123456789" oninput="autoSuggestTrackingUrl()">
                            </div>
                        </div>

                        {{-- 4. Tracking URL --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Tracking Link / URL</label>
                            <input type="url" name="tracking_url" id="modalTrackingUrl" class="form-control form-control-sm rounded-2" value="{{ old('tracking_url', $orderData['tracking_url']) }}" placeholder="https://...">
                            <small class="text-muted" style="font-size: 0.72rem;">Auto-generated for Delhivery, Blue Dart, etc., or enter custom tracking URL.</small>
                        </div>

                        {{-- 5. Shipped Date --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Shipped Date</label>
                            <input type="datetime-local" name="shipped_at" class="form-control form-control-sm rounded-2" value="{{ old('shipped_at', $orderData['shipped_at'] ?? now()->format('Y-m-d\TH:i')) }}">
                        </div>

                        {{-- 6. Shipping Note --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Shipping / Packing Note</label>
                            <textarea name="shipping_notes" rows="2" class="form-control form-control-sm rounded-2" placeholder="e.g. Packed in polybag with dispatch invoice...">{{ old('shipping_notes', $orderData['shipping_notes']) }}</textarea>
                        </div>
                    </div>

                    {{-- 7. History Remark --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">History Log Remark</label>
                        <input type="text" name="comment" class="form-control form-control-sm rounded-2" placeholder="e.g. Handed over to courier partner / Marked delivered">
                    </div>

                    {{-- 8. Email Notification Switches (Default Enabled) --}}
                    @php
                        $customerEmail = $orderData['billing']['email'] ?? ($orderData['shipping']['email'] ?? null);
                        $supplierEmail = $orderData['default_supplier_email'] ?? null;
                    @endphp
                    <div class="p-3 bg-light rounded-2 border mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                            <span class="small fw-bold text-dark">
                                <i class="bi bi-envelope-check me-1.5 text-primary"></i> Email Notifications
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">Default Enabled</span>
                        </div>

                        {{-- Customer Notification --}}
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="notify_customer" id="notifyCustomer" value="1" checked>
                            <label class="form-check-label small fw-semibold text-dark" for="notifyCustomer">
                                Send update email to Customer
                                @if(!empty($customerEmail))
                                    <span class="text-muted fw-normal">({{ $customerEmail }})</span>
                                @else
                                    <span class="text-warning fw-normal">(No email on file)</span>
                                @endif
                            </label>
                        </div>

                        {{-- Supplier Notification --}}
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="notify_supplier" id="notifySupplier" value="1" checked>
                            <label class="form-check-label small fw-semibold text-dark" for="notifySupplier">
                                Send update email to Supplier
                                @if(!empty($supplierEmail))
                                    <span class="text-muted fw-normal">({{ $supplierEmail }})</span>
                                @else
                                    <span class="text-muted fw-normal">({{ $orderData['selling_supplier_name'] ?? 'Supplier' }})</span>
                                @endif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: EMAIL TO SUPPLIER (WITH LIVE EMAIL PREVIEW) --}}
<div class="modal fade" id="emailSupplierModal" tabindex="-1" aria-labelledby="emailSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header bg-light py-2 px-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-1.5 rounded-2">
                        <i class="bi bi-envelope-paper fs-6"></i>
                    </span>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="emailSupplierModalLabel">Email Order Details to Supplier</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Review recipient, instructions, and preview the email before sending.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3">
                <div id="emailAlertContainer" class="d-none"></div>

                <form id="sendSupplierEmailForm" onsubmit="handleSendEmail(event)">
                    <div class="row g-3">
                        {{-- LEFT COLUMN: EMAIL FORM INPUTS --}}
                        <div class="col-lg-5">
                            <div class="card border rounded-3 p-3 bg-white h-100 shadow-2xs">
                                <span class="fw-bold text-dark small text-uppercase mb-3 pb-2 border-bottom d-block" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                                    <i class="bi bi-sliders me-1 text-primary"></i> Email Configuration
                                </span>

                                {{-- Recipient Email --}}
                                <div class="mb-3">
                                    <label for="recipientEmail" class="form-label small fw-bold text-dark mb-1">
                                        Supplier Email <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="recipientEmail"
                                        name="recipient_email"
                                        class="form-control form-control-sm rounded-2"
                                        value="{{ $orderData['default_supplier_email'] ?? '' }}"
                                        placeholder="supplier@example.com"
                                        oninput="updateEmailPreview()"
                                        required
                                    >
                                    @if(!empty($orderData['candidate_suppliers']) && count($orderData['candidate_suppliers']) > 0)
                                        <div class="mt-1.5">
                                            <div class="text-muted" style="font-size: 0.7rem;">Suggested:</div>
                                            <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                                @foreach($orderData['candidate_suppliers'] as $cEmail => $cLabel)
                                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setRecipientEmail('{{ $cEmail }}')">
                                                        {{ $cLabel }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Subject --}}
                                <div class="mb-3">
                                    <label for="emailSubject" class="form-label small fw-bold text-dark mb-1">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="emailSubject"
                                        name="subject"
                                        class="form-control form-control-sm rounded-2"
                                        value="Order #{{ $orderData['order_number'] }} Details - {{ $orderData['selling_supplier_name'] }}"
                                        oninput="updateEmailPreview()"
                                        required
                                    >
                                </div>

                                {{-- Custom Instructions / Note --}}
                                <div class="mb-3">
                                    <label for="customMessage" class="form-label small fw-bold text-dark mb-1">
                                        Admin Instructions / Note (Optional)
                                    </label>
                                    <textarea
                                        id="customMessage"
                                        name="custom_message"
                                        rows="3"
                                        class="form-control form-control-sm rounded-2"
                                        placeholder="Add packing or dispatch instructions for the supplier..."
                                        oninput="updateEmailPreview()"
                                    ></textarea>
                                    <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                        This note will appear inside a highlighted blue banner in the email.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-2 border-top mt-auto">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="btnSendEmail" class="btn btn-sm btn-primary rounded-2 fw-semibold shadow-2xs">
                                        <span class="spinner-border spinner-border-sm me-1 d-none" id="sendEmailSpinner" role="status"></span>
                                        <i class="bi bi-send me-1" id="sendEmailIcon"></i> Send Email
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: LIVE EMAIL PREVIEW --}}
                        <div class="col-lg-7">
                            <div class="border rounded-3 bg-light h-100 d-flex flex-column overflow-hidden shadow-2xs">
                                <div class="bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-uppercase text-secondary" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                                        <i class="bi bi-eye text-primary me-1"></i> Live Email Template Preview
                                    </span>
                                    <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Live</span>
                                </div>

                                {{-- Envelope Header --}}
                                <div class="p-2.5 bg-white border-bottom small">
                                    <div class="d-flex text-muted mb-1" style="font-size: 0.75rem;">
                                        <span style="width: 65px;" class="fw-semibold">To:</span>
                                        <span id="previewToEmail" class="text-dark font-monospace">{{ $orderData['default_supplier_email'] ?: 'supplier@example.com' }}</span>
                                    </div>
                                    <div class="d-flex text-muted" style="font-size: 0.75rem;">
                                        <span style="width: 65px;" class="fw-semibold">Subject:</span>
                                        <span id="previewSubject" class="text-dark fw-medium">Order #{{ $orderData['order_number'] }} Details - {{ $orderData['selling_supplier_name'] }}</span>
                                    </div>
                                </div>

                                {{-- Scrollable Email Body --}}
                                <div class="p-3 overflow-y-auto flex-grow-1" style="max-height: 480px; background-color: #f4f6f9;">
                                    <div class="bg-white rounded-2 border shadow-xs overflow-hidden mx-auto" style="max-width: 580px;">
                                        {{-- Header Banner --}}
                                        <div class="p-3 text-white" style="background-color: #2b5288;">
                                            <h6 class="fw-bold mb-0 text-white">Order Details #{{ $orderData['order_number'] }}</h6>
                                            <div class="opacity-75" style="font-size: 0.72rem;">Store: {{ $orderData['selling_supplier_name'] }} • Placed: {{ $orderData['date_created'] }}</div>
                                        </div>

                                        <div class="p-3 small text-dark">
                                            {{-- Live Admin Note Banner --}}
                                            <div id="previewNoteBanner" class="p-2.5 bg-primary-subtle border-start border-3 border-primary rounded-2 mb-3 d-none">
                                                <strong class="d-block text-primary mb-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i> Admin Instructions:</strong>
                                                <div id="previewNoteText" class="text-dark small" style="white-space: pre-wrap;"></div>
                                            </div>

                                            {{-- Customer & Shipping Box --}}
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="p-2 bg-light rounded border h-100">
                                                        <div class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Customer Info</div>
                                                        <div class="fw-semibold text-truncate">{{ $custName }}</div>
                                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $email }}</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $phone }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 bg-light rounded border h-100">
                                                        <div class="text-muted fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Delivery Address</div>
                                                        <div class="fw-semibold text-truncate">{{ $shipName }}</div>
                                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $fullAddress ?: ($city ?: 'India') }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Line Items Table --}}
                                            <div class="table-responsive mb-2">
                                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.72rem;">
                                                    <thead class="table-light text-secondary text-uppercase" style="font-size: 0.68rem;">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>SKU</th>
                                                            <th class="text-center" style="width: 45px;">Qty</th>
                                                            <th class="text-end" style="width: 75px;">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($orderData['line_items'] as $it)
                                                            <tr>
                                                                <td>
                                                                    <div class="fw-semibold text-truncate" style="max-width: 160px;" title="{{ $it['name'] }}">{{ $it['name'] }}</div>
                                                                </td>
                                                                <td class="font-monospace text-muted">{{ $it['resolved_sku'] ?: ($it['sku'] ?: '—') }}</td>
                                                                <td class="text-center fw-bold">{{ $it['quantity'] ?? 1 }}</td>
                                                                <td class="text-end font-monospace">{{ $orderData['currency_symbol'] }}{{ number_format($it['total'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Grand Total --}}
                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <span class="text-muted" style="font-size: 0.75rem;">Payment: <strong class="text-dark">{{ $orderData['payment_method'] }}</strong></span>
                                                <div class="text-end">
                                                    <span class="text-muted" style="font-size: 0.75rem;">Grand Total: </span>
                                                    <strong class="text-primary font-monospace fs-6">{{ $orderData['currency_symbol'] }}{{ number_format($orderData['total'], 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setRecipientEmail(email) {
    document.getElementById('recipientEmail').value = email;
    updateEmailPreview();
}

function updateEmailPreview() {
    const to = document.getElementById('recipientEmail')?.value || 'supplier@example.com';
    const sub = document.getElementById('emailSubject')?.value || 'Order Details';
    const note = (document.getElementById('customMessage')?.value || '').trim();

    const prevTo = document.getElementById('previewToEmail');
    const prevSub = document.getElementById('previewSubject');
    const prevBanner = document.getElementById('previewNoteBanner');
    const prevNoteText = document.getElementById('previewNoteText');

    if (prevTo) prevTo.textContent = to;
    if (prevSub) prevSub.textContent = sub;

    if (prevBanner && prevNoteText) {
        if (note.length > 0) {
            prevNoteText.textContent = note;
            prevBanner.classList.remove('d-none');
        } else {
            prevBanner.classList.add('d-none');
        }
    }
}

// Dynamic shipping fields toggle based on selected status
function toggleShippingFields() {
    const statusSelect = document.getElementById('modalOrderStatus');
    if (!statusSelect) return;
    const status = statusSelect.value.toLowerCase().trim();
    const shippingContainer = document.getElementById('shippingFieldsContainer');

    if (shippingContainer) {
        shippingContainer.style.display = (status === 'shipped') ? 'block' : 'none';
    }
}

document.getElementById('modalOrderStatus')?.addEventListener('change', toggleShippingFields);
document.getElementById('updateStatusModal')?.addEventListener('show.bs.modal', toggleShippingFields);
document.addEventListener('DOMContentLoaded', toggleShippingFields);

function autoSuggestTrackingUrl() {
    const courier = (document.getElementById('modalCourierName')?.value || '').toLowerCase().trim();
    const trackingId = (document.getElementById('modalTrackingId')?.value || '').trim();
    const urlInput = document.getElementById('modalTrackingUrl');

    if (!trackingId || !urlInput) return;

    if (courier.includes('delhivery')) {
        urlInput.value = 'https://www.delhivery.com/track/package/' + encodeURIComponent(trackingId);
    } else if (courier.includes('blue dart') || courier.includes('bluedart')) {
        urlInput.value = 'https://www.bluedart.com/tracking';
    } else if (courier.includes('dtdc')) {
        urlInput.value = 'https://www.dtdc.in/tracking/tracking_results.asp?action=profile&strCnno=' + encodeURIComponent(trackingId);
    } else if (courier.includes('india post') || courier.includes('speed post')) {
        urlInput.value = 'https://www.indiapost.gov.in/_layouts/15/dpt.cpt.tracking/trackconsignment.aspx';
    } else if (courier.includes('ekart')) {
        urlInput.value = 'https://ekartlogistics.com/shipmenttrack/' + encodeURIComponent(trackingId);
    } else if (courier.includes('shadowfax')) {
        urlInput.value = 'https://tracker.shadowfax.in/#/track/' + encodeURIComponent(trackingId);
    } else if (courier.includes('xpressbees')) {
        urlInput.value = 'https://www.xpressbees.com/shipment/tracking?awb=' + encodeURIComponent(trackingId);
    } else if (courier.includes('ecom express') || courier.includes('ecomexpress')) {
        urlInput.value = 'https://ecomexpress.in/tracking/?awb_field=' + encodeURIComponent(trackingId);
    } else if (courier.includes('shiprocket')) {
        urlInput.value = 'https://shiprocket.co/tracking/' + encodeURIComponent(trackingId);
    } else if (courier.includes('fedex')) {
        urlInput.value = 'https://www.fedex.com/fedextrack/?trknbr=' + encodeURIComponent(trackingId);
    } else if (courier.includes('dhl')) {
        urlInput.value = 'https://www.dhl.com/en/express/tracking.html?AWB=' + encodeURIComponent(trackingId) + '&brand=DHL';
    }
}

function handleSendEmail(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSendEmail');
    const spinner = document.getElementById('sendEmailSpinner');
    const icon = document.getElementById('sendEmailIcon');
    const alertBox = document.getElementById('emailAlertContainer');

    btn.disabled = true;
    spinner.classList.remove('d-none');
    icon.classList.add('d-none');
    alertBox.className = 'd-none';

    const formData = new FormData(document.getElementById('sendSupplierEmailForm'));

    fetch("{{ route('admin.website-orders.send-email', $orderData['record_id']) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        spinner.classList.add('d-none');
        icon.classList.remove('d-none');

        if (data.success) {
            alertBox.className = 'alert alert-success alert-dismissible fade show rounded-2 p-2 mb-3 small';
            alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${data.message}`;
        } else {
            alertBox.className = 'alert alert-danger alert-dismissible fade show rounded-2 p-2 mb-3 small';
            alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message || 'Failed to send email.'}`;
        }
    })
    .catch(err => {
        console.error('Error sending email:', err);
        btn.disabled = false;
        spinner.classList.add('d-none');
        icon.classList.remove('d-none');

        alertBox.className = 'alert alert-danger alert-dismissible fade show rounded-2 p-2 mb-3 small';
        alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> An unexpected network error occurred.`;
    });
}
</script>
@endsection
