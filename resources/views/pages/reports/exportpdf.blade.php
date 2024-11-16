<!DOCTYPE html>
<html>
<head>
    <title>Report Details</title>
    <style>
        /* Add styles for PDF formatting if necessary */
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Report Transaction Details</h1>
    <p>Report Period: {{ $startDate }} to {{ $endDate }}</p>
    @php
    $grandTotal = 0; // Initialize grand total
    @endphp
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Transaction Date</th>
                <th>Discount</th>
                <th>Cashier</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $sale)
                @php
                    $orderTotal = $sale->orderDetails->sum(function($detail) {
                        return $detail->quantity * $detail->product->price;
                    });
                    $grandTotal += $sale->total;
                @endphp

                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->transaction_time }}</td>
                    <td>{{ $sale->discount }}</td>
                    <td>{{ $sale->nama_kasir }}</td>
                    <td>{{ number_format($sale->total, 0) }}</td>
                </tr>

                <tr>
                    <td colspan="4">
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->orderDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ number_format($detail->product->price, 2) }}</td>
                                        <td>{{ number_format($detail->quantity * $detail->product->price, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total for Order {{ $sale->id }}:</strong></td>
                                    <td><strong>Rp. {{ number_format($orderTotal, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Grand Total:</strong></td>
                <td><strong>Rp. {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
