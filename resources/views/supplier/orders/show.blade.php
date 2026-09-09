@extends('layouts.supplier')

@section('title', 'Order #' . $orderData['order_number'])
@section('page-title', 'Order Details')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

    {{-- TOP ACTION & NAVIGATION HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('supplier.orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-2">
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
            <button class="btn btn-sm btn-outline-secondary rounded-2" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Order
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
        $custName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) 
            ?: trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) 
            ?: 'Customer';
        $phone = $b['phone'] ?? ($s['phone'] ?? '—');
        $email = $b['email'] ?? '—';

        $shipName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?: $custName;
        $address1 = $s['address_1'] ?? ($b['address_1'] ?? '');
        $address2 = $s['address_2'] ?? ($b['address_2'] ?? '');
        $city = $s['city'] ?? ($b['city'] ?? '');
        $state = $s['state'] ?? ($b['state'] ?? '');
        $postcode = $s['postcode'] ?? ($b['postcode'] ?? '');
        $country = $s['country'] ?? ($b['country'] ?? '');
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
                            <td class="text-dark">
                                @if(!empty($phone) && $phone !== '—')
                                    <a href="tel:{{ $phone }}" class="text-decoration-none text-dark fw-medium">
                                        {{ $phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
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
                            <td class="text-dark fw-medium">{{ $orderData['selling_store_name'] }}</td>
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

        {{-- 3. SHIPPING & TRACKING (READ-ONLY FOR SUPPLIER) --}}
        <div class="col-md-4">
            <div class="card border rounded-3 bg-white h-100 shadow-2xs">
                <div class="card-header bg-light py-2 px-3 border-bottom">
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                        <i class="bi bi-truck me-1.5 text-primary"></i> Shipping & Tracking
                    </span>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted ps-0" style="width: 110px;">Courier:</td>
                            <td class="fw-semibold text-dark">
                                {{ $orderData['courier_name'] ?: 'Pending dispatch' }}
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
                                    <span class="text-muted fst-italic">Not added yet</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Shipped Date:</td>
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
                </div>
            </div>
        </div>
    </div>

    {{-- ORDERED ITEMS TABLE --}}
    <div class="card border rounded-3 bg-white mb-4 shadow-2xs">
        <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-box-seam me-1.5 text-primary"></i> Your Products in this Order
            </span>
            <span class="badge bg-white text-secondary border">
                {{ count($orderData['supplier_items']) }} {{ count($orderData['supplier_items']) === 1 ? 'Item' : 'Items' }}
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
                            <th class="text-center" style="width: 110px;">Unit Price</th>
                            <th class="text-center" style="width: 80px;">Qty</th>
                            <th class="text-end pe-3" style="width: 130px;">Total</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($orderData['supplier_items'] as $idx => $item)
                            <tr>
                                <td class="ps-3 text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item['name'] }}</div>
                                    @php
                                        $attrs = [];
                                        if(!empty($item['colour'])) $attrs[] = $item['colour'];
                                        if(!empty($item['size'])) $attrs[] = 'Size: ' . $item['size'];
                                        if(!empty($item['product_type'])) $attrs[] = 'Type: ' . $item['product_type'];
                                    @endphp
                                    @if(count($attrs) > 0)
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ implode(' • ', $attrs) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-monospace text-dark">{{ $item['sku'] ?: '—' }}</span>
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
                                <td colspan="6" class="text-center py-3 text-muted">No products found for your supplier account in this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TOTAL PAYABLE FOOTER --}}
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light-subtle">
                <div>
                    <span class="fw-bold text-dark fs-6">Total Payable for Your Products:</span>
                    <div class="text-muted small">Calculated from your supplier items</div>
                </div>
                <span class="fw-bold text-primary fs-5 font-monospace">
                    {{ $orderData['supplier_total_formatted'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- ACTIVITY & AUDIT LOG (SIMPLE TABLE) --}}
    @if(isset($orderData['histories']) && count($orderData['histories']) > 0)
    <div class="card border rounded-3 bg-white mb-4 shadow-2xs">
        <div class="card-header bg-light py-2 px-3 border-bottom">
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.05em;">
                <i class="bi bi-clock-history me-1.5 text-primary"></i> Order Status & Activity History
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
                                    @if(!empty($history->to_status))
                                        <span class="badge bg-secondary-subtle text-dark border ms-1 px-2 py-0.5 rounded-pill font-monospace" style="font-size: 0.7rem;">
                                            {{ ucfirst($history->from_status ?: 'initial') }} &rarr; {{ ucfirst($history->to_status) }}
                                        </span>
                                    @endif
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

</div>
@endsection
