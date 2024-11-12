@extends('layouts.app')

@section('title', 'Reports')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Reports Transaction Detail</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Reports</a></div>
                    <div class="breadcrumb-item">All Reports</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Datas</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-left">
                                    <!-- Date Selection and Report Generation Form -->
                                    <form action="{{ route('details.report.generatedetails') }}" method="POST" class="d-inline">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="start_date">Start Date:</label>
                                                <input type="date" name="start_date" class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="end_date">End Date:</label>
                                                <input type="date" name="end_date" class="form-control" required>
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary">Generate Report</button>
                                            </div>
                                        </div>
                                    </form>
                            
                                    <!-- Export to PDF Button, Parallel to Generate Report -->
                                    @if(isset($details))
                                    <form action="{{ route('details.report.exportpdf') }}" method="POST" target="_blank" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                                        <button type="submit" class="btn btn-success w-100">Export to PDF</button>
                                    </form>
                                    @endif
                                </div>
                            
                                <div class="clearfix mb-3"></div>
                            
                                <!-- Table Section -->
                                <div class="table-responsive">
                                    @if(isset($details))
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Detail</th>
                                                <th>ID</th>
                                                <th>Transaction Date</th>
                                                <th>Cashier</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details as $sale)
                                                @php
                                                    // Calculate order total
                                                    $orderTotal = $sale->orderDetails->sum(function($detail) {
                                                        return $detail->quantity * $detail->product->price;
                                                    });
                                                @endphp
                            
                                                <tr>
                                                    <td>
                                                        <button class="btn btn-icon btn-sm btn-info" data-toggle="collapse" data-target="#details-{{ $sale->id }}">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </td>
                                                    <td>{{ $sale->id }}</td>
                                                    <td>{{ $sale->transaction_time }}</td>
                                                    <td>{{ $sale->nama_kasir }}</td>
                                                    <td>{{ number_format($orderTotal, 2) }}</td>
                                                </tr>
                            
                                                <tr class="collapse" id="details-{{ $sale->id }}">
                                                    <td colspan="5">
                                                        <table class="table table-sm">
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
                                                                        <td>{{ number_format($detail->quantity * $detail->product->price, 2) }}</td>
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
                                    </table>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
