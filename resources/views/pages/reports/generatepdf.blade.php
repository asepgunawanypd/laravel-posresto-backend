<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Sales Report from {{ $startDate->format('d-m-Y') }} to {{ $endDate->format('d-m-Y') }}</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Total Item</th>
                <th>Tax</th>
                <th>Discount</th>
                <th>Total</th>
                <th>Cashier</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->id }}</td>
                {{-- <td>{{ $sale->transaction_time->format('Y-m-d') }}</td> --}}
                <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                <td>{{ $sale->total_item }}</td>
                <td>{{ number_format($sale->tax) }}</td>
                <td>{{ $sale->discount }}</td>
                <td>{{ number_format($sale->total) }}</td>
                <td>{{ $sale->nama_kasir }}</td>
            </tr>    
            @endforeach
        </tbody>
    </table>
    <p class="total">Total Sales: {{ number_format($totalAmount, 0) }}</p>
</body>
</html>
