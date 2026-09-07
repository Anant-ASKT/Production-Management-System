<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #{{ $orderData['order_number'] }}</title>
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
            background-color: #2b5288;
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
        .content {
            padding: 24px;
        }
        .custom-note {
            background-color: #eff6ff;
            border-left: 4px solid #2b5288;
            padding: 14px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 14px;
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
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
            color: #2b5288;
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
    <div class="email-container">
        {{-- Header --}}
        <div class="header">
            <h1>Order Notification: #{{ $orderData['order_number'] }}</h1>
            <p>Store: {{ $orderData['selling_supplier_name'] }} | Date: {{ $orderData['date_created'] }}</p>
        </div>

        <div class="content">
            {{-- Optional Admin Custom Note --}}
            @if(!empty($customMessage))
                <div class="custom-note">
                    <strong style="color: #2b5288;">Message from Admin:</strong>
                    <div style="margin-top: 4px;">{{ nl2br(e($customMessage)) }}</div>
                </div>
            @endif

            {{-- Customer & Delivery Details --}}
            @php
                $b = $orderData['billing'] ?? [];
                $s = $orderData['shipping'] ?? [];
                $custName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) 
                    ?: trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) 
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

            <div class="grid-row">
                <div class="grid-col">
                    <div class="section-title">Customer Information</div>
                    <div class="info-box">
                        <strong>{{ $custName }}</strong>
                        <div>Email: {{ $email }}</div>
                        <div>Phone: {{ $phone }}</div>
                        <div>Payment: {{ $orderData['payment_method'] }}</div>
                        @if(!empty($orderData['customer_note']))
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #cbd5e1; color: #b45309;">
                                <strong>Customer Note:</strong> {{ $orderData['customer_note'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid-col">
                    <div class="section-title">Shipping Address</div>
                    <div class="info-box">
                        <strong>{{ $shipName }}</strong>
                        @if(!empty($address1)) <div>{{ $address1 }}</div> @endif
                        @if(!empty($address2)) <div>{{ $address2 }}</div> @endif
                        <div>{{ $city }}@if(!empty($city) && !empty($state)), @endif{{ $state }} {{ $postcode }}</div>
                        <div>{{ $country ?: 'India' }}</div>
                        <div style="margin-top: 6px; color: #64748b;">
                            Method: {{ $orderData['shipping_lines'][0]['method_title'] ?? 'Standard Delivery' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items Table --}}
            <div class="section-title">Ordered Items ({{ count($orderData['line_items']) }})</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product & SKU</th>
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
                                    @if(!empty($item['spec_id']))
                                        <span class="badge">Spec #{{ $item['spec_id'] }}</span>
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
                            <td class="text-center font-weight-bold">
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
            This is an automated notification from Production Management System.
        </div>
    </div>
</body>
</html>
