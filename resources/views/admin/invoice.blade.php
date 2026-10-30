<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->order_number }} - Anandam Sweets</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; }
        .border-b { border-bottom: 1px dashed #000; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .my-2 { margin-top: 8px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; text-align: left; }
        @media print {
            body { width: 100%; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 16px; background: #d97706; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Print Receipt / Packing Slip
        </button>
    </div>

    <div class="text-center">
        <h2 style="margin: 0; font-size: 16px;">{{ $settings->shop_name }}</h2>
        <p style="margin: 2px 0; font-size: 10px;">{{ $settings->address }}</p>
        <p style="margin: 2px 0; font-size: 10px;">Phone: {{ $settings->phone }}</p>
        <p style="margin: 2px 0; font-size: 10px;">FSSAI Lic: {{ $settings->fssai_license }}</p>
    </div>

    <div class="border-t border-b py-1 my-2">
        <div><strong>Order:</strong> {{ $order->order_number }}</div>
        <div><strong>Date:</strong> {{ $order->created_at->format('d/m/Y h:i A') }}</div>
        <div><strong>Customer:</strong> {{ $order->customer_name }} ({{ $order->customer_phone }})</div>
        <div><strong>Type:</strong> {{ strtoupper($order->fulfilment_type) }}</div>
        @if($order->fulfilment_type === 'pickup')
            <div><strong>Pickup Slot:</strong> {{ $order->pickup_slot }}</div>
        @elseif(!empty($order->delivery_address))
            <div><strong>Address:</strong> {{ $order->delivery_address['address_line1'] ?? '' }}, {{ $order->delivery_address['city'] ?? '' }} - {{ $order->delivery_address['pincode'] ?? '' }}</div>
        @endif
        @if($order->delivery_notes)
            <div><strong>Note:</strong> {{ $order->delivery_notes }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr class="border-b">
                <th>Item & Weight</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}<br>
                        <small style="color: #444;">({{ $item->variant_weight }})</small>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->total_price_paise / 100, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-t my-2" style="padding-top: 6px;">
        <table style="width: 100%;">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">₹{{ number_format($order->subtotal_paise / 100, 2) }}</td>
            </tr>
            @if($order->discount_paise > 0)
                <tr>
                    <td>Discount ({{ $order->coupon_code }}):</td>
                    <td class="text-right">-₹{{ number_format($order->discount_paise / 100, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>Delivery Fee:</td>
                <td class="text-right">{{ $order->delivery_fee_paise == 0 ? 'FREE' : '₹' . number_format($order->delivery_fee_paise / 100, 2) }}</td>
            </tr>
            <tr class="font-bold border-t">
                <td style="font-size: 14px; padding-top: 4px;">TOTAL:</td>
                <td class="text-right" style="font-size: 14px; padding-top: 4px;">₹{{ number_format($order->total_paise / 100, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="border-t py-1 text-center" style="margin-top: 10px;">
        <p style="margin: 2px 0;"><strong>Payment:</strong> {{ strtoupper($order->payment_method) }} ({{ strtoupper($order->payment_status) }})</p>
        <p style="margin: 4px 0 0; font-size: 10px;">Thank you for celebrating with Anandam Sweets!</p>
        <p style="margin: 2px 0; font-size: 9px;">100% Pure Cow Desi Ghee Guarantee</p>
    </div>

</body>
</html>
