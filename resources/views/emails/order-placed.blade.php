<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #0f172a; color: #fff; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .order-details { margin-top: 20px; border-collapse: collapse; width: 100%; }
        .order-details td, .order-details th { border: 1px solid #ddd; padding: 10px; }
        .order-details th { background-color: #f8fafc; text-align: left; }
        .total-row { font-weight: bold; background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $order->shipping_address['name'] }}</strong>,</p>
            <p>Your order has been placed successfully! We have received your request and it is currently pending payment verification.</p>
            
            <h3>Order Summary (#{{ $order->order_number }})</h3>
            <table class="order-details">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" align="right">Subtotal:</td>
                        <td>${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if($order->shipping_charges > 0)
                    <tr>
                        <td colspan="3" align="right">Shipping:</td>
                        <td>${{ number_format($order->shipping_charges, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="3" align="right">Total Payable:</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 30px; text-align: center;">
                <p>You can download your Proforma Invoice here:</p>
                <a href="{{ $successUrl }}" class="btn" style="color: #ffffff !important; text-decoration: none;">Download Invoice</a>
            </div>

            <div style="margin-top: 30px; border: 1px dashed #3b82f6; padding: 15px; border-radius: 5px; background: #eff6ff;">
                <h4 style="margin-top: 0; color: #1e40af;">Payment Instructions:</h4>
                <p style="margin-bottom: 5px;"><strong>Bank:</strong> {{ $order->bank_details['bank_name'] }}</p>
                <p style="margin-bottom: 5px;"><strong>Account Name:</strong> {{ $order->bank_details['account_name'] }}</p>
                <p style="margin-bottom: 5px;"><strong>Account Number:</strong> {{ $order->bank_details['account_number'] }}</p>
                <p style="margin-bottom: 0;"><strong>Reference:</strong> #{{ $order->order_number }}</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} FGX Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
