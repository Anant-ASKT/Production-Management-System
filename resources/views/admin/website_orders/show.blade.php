@extends('layouts.app')

@section('title', 'Order #' . $orderData['order_number'])

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">

    {{-- HEADER BAR --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.website-orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs">
                <i class="bi bi-arrow-left me-1"></i> Back to Orders
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold text-dark mb-0">Order #{{ $orderData['order_number'] }}</h4>
                    @php
                        $s = strtolower($orderData['status'] ?? 'pending');
                        $badgeClass = 'bg-secondary text-white';
                        if ($s === 'processing') $badgeClass = 'bg-primary text-white';
                        elseif ($s === 'completed') $badgeClass = 'bg-success text-white';
                        elseif ($s === 'pending') $badgeClass = 'bg-info text-dark';
                        elseif ($s === 'on-hold') $badgeClass = 'bg-warning text-dark';
                        elseif ($s === 'cancelled' || $s === 'failed') $badgeClass = 'bg-danger text-white';
                        elseif ($s === 'refunded') $badgeClass = 'bg-dark text-white';
                    @endphp
                    <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1.5 text-capitalize fw-semibold fs-8">
                        {{ $orderData['status'] }}
                    </span>
                </div>
                <div class="text-muted small mt-1">
                    <span><i class="bi bi-calendar3 me-1"></i> Placed on {{ $orderData['date_created'] }}</span>
                    <span class="mx-2">•</span>
                    <span><i class="bi bi-shop me-1"></i> Store: <strong class="text-dark">{{ $orderData['selling_supplier_name'] }}</strong></span>
                    @if(!empty($orderData['selling_supplier_url']))
                        <a href="{{ $orderData['selling_supplier_url'] }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none ms-1">
                            <i class="bi bi-box-arrow-up-right fs-8"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- EMAIL TO SUPPLIER BUTTON --}}
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs fw-semibold" data-bs-toggle="modal" data-bs-target="#emailSupplierModal">
                <i class="bi bi-envelope-paper me-1.5"></i> Email to Supplier
            </button>

            <button class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-xs" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Order
            </button>
        </div>
    </div>

    @php
        $b = $orderData['billing'] ?? [];
        $s = $orderData['shipping'] ?? [];
        $custName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) 
            ?: trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) 
            ?: 'Customer';
        $phone = $b['phone'] ?? ($s['phone'] ?? null);
        $email = $b['email'] ?? null;

        $shipName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?: $custName;
        $address1 = $s['address_1'] ?? ($b['address_1'] ?? '');
        $address2 = $s['address_2'] ?? ($b['address_2'] ?? '');
        $city = $s['city'] ?? ($b['city'] ?? '');
        $state = $s['state'] ?? ($b['state'] ?? '');
        $postcode = $s['postcode'] ?? ($b['postcode'] ?? '');
        $country = $s['country'] ?? ($b['country'] ?? '');
    @endphp

    {{-- ROW 1: CUSTOMER & SHIPPING CARDS --}}
    <div class="row g-3 mb-3">
        {{-- CUSTOMER DETAILS CARD --}}
        <div class="col-md-6">
            <div class="card border shadow-xs rounded-3 bg-white h-100">
                <div class="card-body p-3.5">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center text-uppercase fs-7 text-secondary" style="letter-spacing: 0.05em;">
                        <i class="bi bi-person-circle text-primary me-2 fs-6"></i> Customer Details
                    </h6>

                    <div class="fw-bold text-dark fs-6 mb-2">{{ $custName }}</div>

                    <div class="d-flex flex-column gap-2 text-muted small">
                        <div class="d-flex align-items-center">
                            <span class="text-secondary" style="width: 120px;"><i class="bi bi-envelope me-1.5 text-muted"></i> Email:</span>
                            @if(!empty($email))
                                <a href="mailto:{{ $email }}" class="text-dark fw-medium text-decoration-none">{{ $email }}</a>
                            @else
                                <span class="text-muted fst-italic">Not provided</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center">
                            <span class="text-secondary" style="width: 120px;"><i class="bi bi-telephone me-1.5 text-muted"></i> Phone:</span>
                            @if(!empty($phone))
                                <a href="tel:{{ $phone }}" class="text-dark fw-medium text-decoration-none">{{ $phone }}</a>
                            @else
                                <span class="text-muted fst-italic">Not provided</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center">
                            <span class="text-secondary" style="width: 120px;"><i class="bi bi-credit-card me-1.5 text-muted"></i> Payment:</span>
                            <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                {{ $orderData['payment_method'] }}
                            </span>
                        </div>

                        @if(!empty($orderData['transaction_id']) && $orderData['transaction_id'] !== '—')
                            <div class="d-flex align-items-center">
                                <span class="text-secondary" style="width: 120px;"><i class="bi bi-hash me-1.5 text-muted"></i> Txn ID:</span>
                                <span class="font-monospace text-dark">{{ $orderData['transaction_id'] }}</span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($orderData['customer_note']))
                        <div class="mt-3 p-2.5 rounded-2 bg-light border small text-dark">
                            <strong class="text-secondary d-block mb-0.5"><i class="bi bi-chat-left-text me-1"></i> Customer Note:</strong>
                            {{ $orderData['customer_note'] }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- SHIPPING DETAILS CARD --}}
        <div class="col-md-6">
            <div class="card border shadow-xs rounded-3 bg-white h-100">
                <div class="card-body p-3.5">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center text-uppercase fs-7 text-secondary" style="letter-spacing: 0.05em;">
                        <i class="bi bi-truck text-primary me-2 fs-6"></i> Shipping Details
                    </h6>

                    <div class="fw-bold text-dark fs-6 mb-2">{{ $shipName }}</div>

                    <div class="text-dark small lh-base mb-3">
                        @if(!empty($address1)) <div>{{ $address1 }}</div> @endif
                        @if(!empty($address2)) <div>{{ $address2 }}</div> @endif
                        <div>
                            <strong>{{ $city }}</strong>@if(!empty($city) && !empty($state)), @endif{{ $state }} {{ $postcode }}
                        </div>
                        <div class="text-muted">{{ $country ?: 'India' }}</div>
                    </div>

                    <div class="pt-2 border-top d-flex justify-content-between align-items-center text-muted small">
                        <span><i class="bi bi-box-seam me-1 text-muted"></i> Shipping Method:</span>
                        <span class="fw-medium text-dark">
                            {{ $orderData['shipping_lines'][0]['method_title'] ?? 'Standard Delivery' }}
                            ({{ $orderData['shipping_total'] > 0 ? ($orderData['currency_symbol'] . number_format($orderData['shipping_total'], 2)) : 'Free' }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: ORDER DETAILS & PRODUCTS CARD --}}
    <div class="card border shadow-xs rounded-3 bg-white mb-4">
        <div class="card-header bg-white py-3 px-3.5 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center text-uppercase fs-7 text-secondary" style="letter-spacing: 0.05em;">
                <i class="bi bi-bag-check text-primary me-2 fs-6"></i> Order Items & Payment Details
            </h6>
            <span class="badge bg-light text-secondary border">
                {{ count($orderData['line_items']) }} {{ count($orderData['line_items']) === 1 ? 'Item' : 'Items' }}
            </span>
        </div>

        <div class="card-body p-3.5">
            {{-- PRODUCTS TABLE --}}
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Product</th>
                            <th style="min-width: 160px;">Manufacturer / Origin</th>
                            <th class="text-center" style="width: 100px;">Price</th>
                            <th class="text-center" style="width: 70px;">Qty</th>
                            <th class="text-end" style="width: 110px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderData['line_items'] as $item)
                            <tr>
                                {{-- Thumbnail --}}
                                <td>
                                    @php
                                        $imgUrl = $item['resolved_image'] ?: ($item['image']['src'] ?? null);
                                    @endphp
                                    @if(!empty($imgUrl))
                                        <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $imgUrl }}" class="rounded-2 border" style="width: 48px; height: 48px; object-fit: cover;" alt="Product">
                                        </a>
                                    @else
                                        <div class="rounded-2 border bg-light d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                                            <i class="bi bi-image fs-5"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- Product Name & Attributes --}}
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item['name'] }}</div>
                                    <div class="d-flex flex-wrap gap-1 align-items-center mt-0.5">
                                        @if(!empty($item['resolved_sku']) && $item['resolved_sku'] !== '—')
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.7rem;">
                                                SKU: {{ $item['resolved_sku'] }}
                                            </span>
                                        @endif
                                        @if(!empty($item['barcode']))
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.7rem;">
                                                Barcode: {{ $item['barcode'] }}
                                            </span>
                                        @endif
                                        @if(!empty($item['colour']))
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                                {{ $item['colour'] }}
                                            </span>
                                        @endif
                                        @if(!empty($item['size']))
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                                {{ $item['size'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Origin / Spec --}}
                                <td>
                                    <div class="text-dark small fw-medium">
                                        {{ $item['origin_supplier_name'] ?: 'Global / In-house' }}
                                    </div>
                                    @if(!empty($item['spec_id']))
                                        <a href="{{ route('admin.publish-products.show', $item['spec_id']) }}" class="text-primary text-decoration-none small" target="_blank">
                                            <i class="bi bi-link-45deg"></i> Spec #{{ $item['spec_id'] }}
                                        </a>
                                    @endif
                                </td>

                                {{-- Price --}}
                                <td class="text-center font-monospace small">
                                    {{ $orderData['currency_symbol'] }}{{ number_format($item['price'] ?? 0, 2) }}
                                </td>

                                {{-- Quantity --}}
                                <td class="text-center fw-bold small">
                                    {{ $item['quantity'] ?? 1 }}
                                </td>

                                {{-- Total --}}
                                <td class="text-end font-monospace fw-bold text-dark">
                                    {{ $orderData['currency_symbol'] }}{{ number_format($item['total'] ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TOTALS SUMMARY (Right Aligned, Crisp & Clean) --}}
            <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                <div style="min-width: 260px;">
                    <div class="d-flex justify-content-between text-muted small mb-1.5">
                        <span>Subtotal:</span>
                        <span class="font-monospace text-dark fw-medium">
                            {{ $orderData['currency_symbol'] }}{{ number_format($orderData['subtotal'], 2) }}
                        </span>
                    </div>

                    @if($orderData['discount_total'] > 0)
                        <div class="d-flex justify-content-between text-success small mb-1.5">
                            <span>Discount:</span>
                            <span class="font-monospace fw-medium">
                                -{{ $orderData['currency_symbol'] }}{{ number_format($orderData['discount_total'], 2) }}
                            </span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between text-muted small mb-1.5">
                        <span>Shipping:</span>
                        <span class="font-monospace text-dark fw-medium">
                            {{ $orderData['shipping_total'] > 0 ? ($orderData['currency_symbol'] . number_format($orderData['shipping_total'], 2)) : 'Free' }}
                        </span>
                    </div>

                    @if($orderData['total_tax'] > 0)
                        <div class="d-flex justify-content-between text-muted small mb-1.5">
                            <span>Tax:</span>
                            <span class="font-monospace text-dark fw-medium">
                                {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total_tax'], 2) }}
                            </span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                        <span class="fw-bold text-dark fs-6">Grand Total:</span>
                        <span class="fw-bold text-primary fs-5 font-monospace">
                            {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total'], 2) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- WEBHOOK PAYLOAD (COLLAPSED BY DEFAULT) --}}
    <div class="accordion accordion-flush" id="accWebhook">
        <div class="accordion-item border-0 bg-transparent">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed bg-transparent text-muted small p-0 py-2 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <i class="bi bi-code-slash me-1"></i> View Raw Webhook JSON
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accWebhook">
                <div class="accordion-body p-3 bg-white border rounded-3 mt-2">
                    <pre class="bg-dark text-white p-3 rounded-2 small mb-0 font-monospace" style="max-height: 250px; overflow-y: auto;"><code>{{ json_encode($orderData['raw_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    #emailSupplierModal .modal-dialog {
        max-width: 1120px;
        margin: 1.25rem auto;
        height: calc(100vh - 2.5rem);
        max-height: calc(100vh - 2.5rem);
        display: flex;
        align-items: center;
    }
    #emailSupplierModal .modal-content {
        height: 100%;
        max-height: 100%;
        display: flex;
        flex-direction: column;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1rem 3rem rgba(15, 23, 42, 0.2);
    }
    #emailSupplierModal .modal-header {
        flex-shrink: 0;
        padding: 0.9rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    #emailSupplierModal .modal-footer {
        flex-shrink: 0;
        padding: 0.75rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    #emailSupplierModal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        padding: 1rem 1.25rem;
        background-color: #f1f5f9;
    }
    .email-preview-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .email-preview-scrollable {
        flex: 1 1 auto;
        min-height: 0;
        max-height: 54vh;
        overflow-y: auto;
        padding: 0.85rem;
        background: #f8fafc;
    }
    @media (max-width: 991.98px) {
        #emailSupplierModal .modal-dialog {
            height: calc(100vh - 1rem);
            max-height: calc(100vh - 1rem);
            margin: 0.5rem;
        }
        .email-preview-scrollable {
            max-height: 360px;
        }
    }
</style>

{{-- EMAIL SUPPLIER MODAL --}}
<div class="modal fade" id="emailSupplierModal" tabindex="-1" aria-labelledby="emailSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            
            {{-- Fixed Modal Header --}}
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="bi bi-envelope-paper-fill fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="emailSupplierModalLabel">Email Order Details to Supplier</h5>
                        <small class="text-muted">Customize recipient, subject, or admin instructions and preview before sending.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Scrollable Modal Body with 2-Column Desktop Grid --}}
            <div class="modal-body">
                
                {{-- Alert Box --}}
                <div id="emailAlertContainer" class="d-none"></div>

                <form id="sendSupplierEmailForm" onsubmit="handleSendEmail(event)">
                    <div class="row g-3">
                        
                        {{-- LEFT COLUMN: EMAIL CONTROLS --}}
                        <div class="col-lg-5">
                            <div class="card border shadow-xs rounded-3 p-3 bg-white h-100">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center text-uppercase fs-7 text-secondary" style="letter-spacing: 0.05em;">
                                    <i class="bi bi-sliders text-primary me-2 fs-6"></i> Email Settings
                                </h6>

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
                                        required
                                    >
                                    @if(!empty($orderData['candidate_suppliers']) && count($orderData['candidate_suppliers']) > 0)
                                        <div class="mt-2">
                                            <div class="text-muted small mb-1" style="font-size: 0.72rem;">Suggested Contacts:</div>
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
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
                                        oninput="document.getElementById('previewSubject').textContent = this.value"
                                        required
                                    >
                                </div>

                                {{-- Custom Note from Admin --}}
                                <div class="mb-2">
                                    <label for="customMessage" class="form-label small fw-bold text-dark mb-1">
                                        Admin Instructions / Remarks (Optional)
                                    </label>
                                    <textarea
                                        id="customMessage"
                                        name="custom_message"
                                        rows="3"
                                        class="form-control form-control-sm rounded-2"
                                        placeholder="Add packing/dispatch notes or special requests for the supplier..."
                                        oninput="updateLivePreview()"
                                    ></textarea>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        This message will appear in a highlighted banner inside the email.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: LIVE EMAIL PREVIEW --}}
                        <div class="col-lg-7">
                            <div class="email-preview-card shadow-xs">
                                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                                    <span class="small fw-bold text-uppercase text-secondary" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                                        <i class="bi bi-eye text-primary me-1"></i> Live Email Template Preview
                                    </span>
                                    <span class="badge bg-white text-muted border small" style="font-size: 0.68rem;">HTML Output</span>
                                </div>

                                <div class="email-preview-scrollable">
                                    
                                    {{-- Simulated Email Card --}}
                                    <div class="border rounded-3 p-3 bg-white shadow-xs" style="font-size: 12.5px;">
                                        
                                        {{-- Email Banner --}}
                                        <div class="p-3 text-white rounded-2 mb-3" style="background-color: #2b5288;">
                                            <h6 class="fw-bold mb-0 text-white" id="previewSubject">
                                                Order #{{ $orderData['order_number'] }} Details - {{ $orderData['selling_supplier_name'] }}
                                            </h6>
                                            <div class="opacity-75 small mt-0.5" style="font-size: 0.75rem;">
                                                Store: {{ $orderData['selling_supplier_name'] }} | Placed: {{ $orderData['date_created'] }}
                                            </div>
                                        </div>

                                        {{-- Live Custom Message Banner --}}
                                        <div id="previewCustomNoteContainer" class="p-2.5 rounded-2 bg-primary-subtle border border-primary-subtle text-dark mb-3 d-none">
                                            <strong class="text-primary d-block small" style="font-size: 0.75rem;"><i class="bi bi-info-circle-fill me-1"></i> Message from Admin:</strong>
                                            <div id="previewCustomNoteText" class="mt-0.5 small text-dark"></div>
                                        </div>

                                        {{-- Customer & Shipping 2-col --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <div class="p-2.5 rounded-2 bg-light border h-100">
                                                    <div class="fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Customer Info</div>
                                                    <div class="fw-bold text-dark">{{ $custName }}</div>
                                                    <div class="text-muted small">Email: {{ $email ?: '—' }}</div>
                                                    <div class="text-muted small">Phone: {{ $phone ?: '—' }}</div>
                                                    <div class="text-muted small">Payment: {{ $orderData['payment_method'] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2.5 rounded-2 bg-light border h-100">
                                                    <div class="fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Shipping Address</div>
                                                    <div class="fw-bold text-dark">{{ $shipName }}</div>
                                                    <div class="text-muted small">{{ $address1 }} {{ $address2 }}</div>
                                                    <div class="text-muted small">{{ $city }}, {{ $state }} {{ $postcode }}</div>
                                                    <div class="text-muted small">{{ $country ?: 'India' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Ordered Items Table --}}
                                        <div class="fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Ordered Items ({{ count($orderData['line_items']) }})</div>
                                        <div class="table-responsive mb-2">
                                            <table class="table table-sm table-bordered mb-0 align-middle" style="font-size: 0.78rem;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Item & SKU</th>
                                                        <th class="text-center" style="width: 75px;">Price</th>
                                                        <th class="text-center" style="width: 45px;">Qty</th>
                                                        <th class="text-end" style="width: 80px;">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orderData['line_items'] as $item)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold text-dark">{{ $item['name'] }}</div>
                                                                @if(!empty($item['resolved_sku']) && $item['resolved_sku'] !== '—')
                                                                    <div class="text-muted" style="font-size: 0.7rem;">SKU: {{ $item['resolved_sku'] }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{ $orderData['currency_symbol'] }}{{ number_format($item['price'] ?? 0, 2) }}</td>
                                                            <td class="text-center fw-bold">{{ $item['quantity'] ?? 1 }}</td>
                                                            <td class="text-end font-monospace">{{ $orderData['currency_symbol'] }}{{ number_format($item['total'] ?? 0, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Summary Totals --}}
                                        <div class="d-flex justify-content-end text-end">
                                            <div style="min-width: 180px;">
                                                <div class="text-muted small" style="font-size: 0.75rem;">Subtotal: {{ $orderData['currency_symbol'] }}{{ number_format($orderData['subtotal'], 2) }}</div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">Shipping: {{ $orderData['shipping_total'] > 0 ? ($orderData['currency_symbol'] . number_format($orderData['shipping_total'], 2)) : 'Free' }}</div>
                                                <div class="fw-bold text-primary small mt-1 pt-1 border-top" style="font-size: 0.85rem;">
                                                    Grand Total: {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total'], 2) }}
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

            {{-- Fixed Modal Footer (Always Visible at Bottom) --}}
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i> Ready to send via SMTP (Aman Movement)
                </small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" form="sendSupplierEmailForm" id="btnSendEmailSubmit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-semibold">
                        <span id="btnSendEmailSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bi bi-send-fill me-1" id="btnSendEmailIcon"></i> Send Email
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function setRecipientEmail(email) {
        const input = document.getElementById('recipientEmail');
        if (input) {
            input.value = email;
            input.focus();
        }
    }

    function updateLivePreview() {
        const msg = document.getElementById('customMessage').value.trim();
        const container = document.getElementById('previewCustomNoteContainer');
        const text = document.getElementById('previewCustomNoteText');
        
        if (msg) {
            text.innerHTML = msg.replace(/\n/g, '<br>');
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    function handleSendEmail(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnSendEmailSubmit');
        const spinner = document.getElementById('btnSendEmailSpinner');
        const icon = document.getElementById('btnSendEmailIcon');
        const alertBox = document.getElementById('emailAlertContainer');

        const recipientEmail = document.getElementById('recipientEmail').value.trim();
        const subject = document.getElementById('emailSubject').value.trim();
        const customMessage = document.getElementById('customMessage').value.trim();

        if (!recipientEmail) {
            alert('Please enter a valid recipient email address.');
            return;
        }

        // Disable button & show spinner
        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');
        alertBox.className = 'd-none';

        const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        fetch(`{{ route('admin.website-orders.send-email', $orderData['record_id']) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                recipient_email: recipientEmail,
                subject: subject,
                custom_message: customMessage
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            if (data.success) {
                alertBox.className = 'alert alert-success alert-dismissible fade show rounded-3 p-3 mb-3';
                alertBox.innerHTML = `
                    <i class="bi bi-check-circle-fill me-1.5"></i> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
            } else {
                alertBox.className = 'alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-3';
                alertBox.innerHTML = `
                    <i class="bi bi-exclamation-triangle-fill me-1.5"></i> ${data.message || 'Failed to send email.'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
            }
        })
        .catch(err => {
            console.error('Error sending email:', err);
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            alertBox.className = 'alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-3';
            alertBox.innerHTML = `
                <i class="bi bi-exclamation-triangle-fill me-1.5"></i> An unexpected network error occurred. Please check mail server settings.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
        });
    }
</script>
@endsection
