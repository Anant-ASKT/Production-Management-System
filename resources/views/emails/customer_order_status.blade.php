<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $orderData['order_number'] }} Status Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #212529;
            line-height: 1.5;
        }
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 24px;
            text-align: left;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .status-delivered { background-color: #22c55e; color: #ffffff; }
        .status-shipped { background-color: #0284c7; color: #ffffff; }
        .status-confirmed { background-color: #f59e0b; color: #ffffff; }
        .status-default { background-color: #64748b; color: #ffffff; }

        .content {
            padding: 24px;
        }
        .welcome-text {
            font-size: 15px;
            color: #1e293b;
            margin-bottom: 20px;
        }
        .tracking-card {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .tracking-card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #166534;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .track-btn {
            display: inline-block;
            background-color: #16a34a;
            color: #ffffff !important;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
        }
        .custom-note {
            background-color: #eff6ff;
            border-left: 4px solid #1e3a8a;
            padding: 14px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .grid-row {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .grid-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 12px;
        }
        .grid-col:last-child {
            padding-right: 0;
            padding-left: 12px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            font-size: 13px;
        }
        .info-box strong {
            display: block;
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
        }
        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-table {
            width: 250px;
            margin-left: auto;
            font-size: 13px;
        }
        .totals-table td {
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .grand-total td {
            font-size: 16px;
            font-weight: 700;
            color: #1e3a8a;
            border-top: 2px solid #cbd5e1;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            background-color: #e2e8f0;
            color: #334155;
            margin-top: 4px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    @php
        $statusStr = strtolower(trim($orderData['status'] ?? ''));
        $badgeClass = 'status-default';
        if (str_contains($statusStr, 'deliver')) {
            $badgeClass = 'status-delivered';
        } elseif (str_contains($statusStr, 'ship')) {
            $badgeClass = 'status-shipped';
        } elseif (str_contains($statusStr, 'confirm') || str_contains($statusStr, 'process')) {
            $badgeClass = 'status-confirmed';
        }

        $b = $orderData['billing'] ?? [];
        $s = $orderData['shipping'] ?? [];
        $custName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) 
            ?: trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) 
            ?: 'Valued Customer';
        $shipName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?: $custName;
        $address1 = $s['address_1'] ?? ($b['address_1'] ?? '');
        $address2 = $s['address_2'] ?? ($b['address_2'] ?? '');
        $city = $s['city'] ?? ($b['city'] ?? '');
        $state = $s['state'] ?? ($b['state'] ?? '');
        $postcode = $s['postcode'] ?? ($b['postcode'] ?? '');
        $country = $s['country'] ?? ($b['country'] ?? '');
    @endphp

    <div class="email-container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ $orderData['selling_supplier_name'] ?? 'Order Status Update' }}</h1>
            <p>Order #{{ $orderData['order_number'] }}</p>
            <span class="status-badge {{ $badgeClass }}">Status: {{ $orderData['status'] }}</span>
        </div>

        <div class="content">
            {{-- Welcome / Intro --}}
            <div class="welcome-text">
                Hello <strong>{{ $custName }}</strong>,<br>
                @if(str_contains($statusStr, 'deliver'))
                    Your order <strong>#{{ $orderData['order_number'] }}</strong> has been delivered. Thank you for shopping with us!
                @elseif(str_contains($statusStr, 'ship'))
                    Great news! Your order <strong>#{{ $orderData['order_number'] }}</strong> has been dispatched and is on its way to you.
                @else
                    Your order <strong>#{{ $orderData['order_number'] }}</strong> status has been updated to <strong>{{ $orderData['status'] }}</strong>.
                @endif
            </div>

            {{-- Optional Admin Custom Note --}}
            @if(!empty($customMessage))
                <div class="custom-note">
                    <strong style="color: #1e3a8a;">Update Note:</strong>
                    <div style="margin-top: 4px;">{{ nl2br(e($customMessage)) }}</div>
                </div>
            @endif

            {{-- Shipping & Tracking Box (Only when courier or tracking ID is present) --}}
            @if(!empty($orderData['courier_name']) || !empty($orderData['tracking_id']))
                <div class="tracking-card">
                    <h3><i style="font-style: normal;">🚚</i> Shipment & Tracking Details</h3>
                    @if(!empty($orderData['courier_name']))
                        <div style="margin-bottom: 4px;"><strong>Courier / Partner:</strong> {{ $orderData['courier_name'] }}</div>
                    @endif
                    @if(!empty($orderData['tracking_id']))
                        <div style="margin-bottom: 4px;"><strong>Tracking ID / AWB:</strong> <span style="font-family: monospace; font-weight: 700;">{{ $orderData['tracking_id'] }}</span></div>
                    @endif
                    @if(!empty($orderData['shipped_at_formatted']) || !empty($orderData['shipped_at']))
                        <div style="margin-bottom: 4px; color: #64748b; font-size: 12px;">Dispatched On: {{ $orderData['shipped_at_formatted'] ?? $orderData['shipped_at'] }}</div>
                    @endif
                    @if(!empty($orderData['shipping_notes']))
                        <div style="margin-top: 4px; color: #334155; font-size: 12px;">Note: {{ $orderData['shipping_notes'] }}</div>
                    @endif

                    @if(!empty($orderData['tracking_url']))
                        <div>
                            <a href="{{ $orderData['tracking_url'] }}" target="_blank" class="track-btn">Track Your Package &rarr;</a>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Delivery & Order Info --}}
            <div class="grid-row">
                <div class="grid-col">
                    <div class="section-title">Delivery Address</div>
                    <div class="info-box">
                        <strong>{{ $shipName }}</strong>
                        @if(!empty($address1)) <div>{{ $address1 }}</div> @endif
                        @if(!empty($address2)) <div>{{ $address2 }}</div> @endif
                        <div>{{ $city }}@if(!empty($city) && !empty($state)), @endif{{ $state }} {{ $postcode }}</div>
                        <div>{{ $country ?: 'India' }}</div>
                    </div>
                </div>

                <div class="grid-col">
                    <div class="section-title">Order Information</div>
                    <div class="info-box">
                        <div><strong>Order Date:</strong> {{ $orderData['date_created'] }}</div>
                        <div style="margin-top: 4px;"><strong>Payment Method:</strong> {{ $orderData['payment_method'] }}</div>
                        <div style="margin-top: 4px;"><strong>Status:</strong> {{ $orderData['status'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Ordered Items Table --}}
            <div class="section-title">Items in Your Order ({{ count($orderData['line_items']) }})</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center" style="width: 70px;">Price</th>
                        <th class="text-center" style="width: 50px;">Qty</th>
                        <th class="text-right" style="width: 90px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderData['line_items'] as $item)
                        <tr>
                            <td>
                                <strong style="color: #1e293b;">{{ $item['name'] }}</strong>
                                <div style="margin-top: 2px;">
                                    @if(!empty($item['resolved_sku']) && $item['resolved_sku'] !== '—')
                                        <span class="badge">SKU: {{ $item['resolved_sku'] }}</span>
                                    @endif
                                    @if(!empty($item['colour']))
                                        <span class="badge">{{ $item['colour'] }}</span>
                                    @endif
                                    @if(!empty($item['size']))
                                        <span class="badge">{{ $item['size'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                {{ $orderData['currency_symbol'] }}{{ number_format($item['price'] ?? 0, 2) }}
                            </td>
                            <td class="text-center" style="font-weight: 700;">
                                {{ $item['quantity'] ?? 1 }}
                            </td>
                            <td class="text-right" style="font-weight: 600;">
                                {{ $orderData['currency_symbol'] }}{{ number_format($item['total'] ?? 0, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals Summary --}}
            <table class="totals-table">
                <tr>
                    <td style="color: #64748b;">Subtotal:</td>
                    <td class="text-right" style="font-weight: 500;">
                        {{ $orderData['currency_symbol'] }}{{ number_format($orderData['subtotal'], 2) }}
                    </td>
                </tr>
                @if($orderData['discount_total'] > 0)
                    <tr>
                        <td style="color: #16a34a;">Discount:</td>
                        <td class="text-right" style="color: #16a34a; font-weight: 500;">
                            -{{ $orderData['currency_symbol'] }}{{ number_format($orderData['discount_total'], 2) }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="color: #64748b;">Shipping:</td>
                    <td class="text-right" style="font-weight: 500;">
                        {{ $orderData['shipping_total'] > 0 ? ($orderData['currency_symbol'] . number_format($orderData['shipping_total'], 2)) : 'Free' }}
                    </td>
                </tr>
                @if($orderData['total_tax'] > 0)
                    <tr>
                        <td style="color: #64748b;">Tax:</td>
                        <td class="text-right" style="font-weight: 500;">
                            {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total_tax'], 2) }}
                        </td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td>Grand Total:</td>
                    <td class="text-right">
                        {{ $orderData['currency_symbol'] }}{{ number_format($orderData['total'], 2) }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            Thank you for your order! If you have any questions, please contact our support team.<br>
            &copy; {{ date('Y') }} {{ $orderData['selling_supplier_name'] ?? 'Production Management System' }}. All rights reserved.
        </div>
    </div>
</body>
</html>
