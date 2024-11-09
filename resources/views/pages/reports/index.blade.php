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
                <h1>Reports Transaction</h1>
                {{-- <div class="section-header-button">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">Add New</a>
                </div> --}}
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
                                    <form action="{{ route('sales.report.generate') }}" method="POST">
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
                                </div>

                                <div class="clearfix mb-3"></div>

                                <div class="table-responsive">
                                    @if(isset($sales))
                                    {{-- <h2 class="mt-4">Sales Report from {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}</h2> --}}
                                    <table class="table-striped table">
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Total Item</th>
                                            <th>Tax</th>
                                            <th>Discount</th>
                                            <th>Subtotal</th>
                                            <th>Cashier</th>
                                            
                                        </tr>
                                        <tbody>
                                            @forelse($sales as $sale)
                                                <tr>
                                                    <td>{{ $sale->id }}</td>
                                                    {{-- <td>{{ $sale->transaction_time->format('Y-m-d') }}</td> --}}
                                                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $sale->total_item }}</td>
                                                    <td>{{ number_format($sale->tax) }}</td>
                                                    <td>{{ $sale->discount }}</td>
                                                    <td>{{ number_format($sale->sub_total) }}</td>
                                                    <td>{{ $sale->nama_kasir }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No sales found for the selected period.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                                                <td><strong>Rp.{{ number_format($totalAmount, 0) }}</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    @endif
                                    {{-- <div class="float-right">
                                        {{ $reports->withQueryString()->links() }}
                                    </div> --}}
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
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
