<?php

namespace App\Http\Controllers;

use App\Exports\ReportDetailExport;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\PDF;
use App\Exports\SalesReportExport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    public function index()
    {
        $reports = Order::paginate(10);
        return view('pages.reports.index', compact('reports'));
    }
    public function details()
    {
        $reports = OrderItem::paginate(10);
        return view('pages.reports.detail', compact('reports'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // $sales = Order::whereBetween('transaction_time', [$startDate, $endDate])->get();

        $sales = Order::when($startDate, function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        })->when($endDate, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        })->get();

        // Calculate total amount
        $totalAmount = $sales->sum('total');

        return view('pages.reports.index', compact('sales', 'startDate', 'endDate', 'totalAmount'));
    }

    public function generatePdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // $sales = Order::whereBetween('transaction_time', [$startDate, $endDate])->get();

        $sales = Order::when($startDate, function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        })->when($endDate, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        })->get();

        // Calculate total amount
        $totalAmount = $sales->sum('total');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.reports.generatepdf', compact('sales', 'startDate', 'endDate', 'totalAmount'));

        // Download the PDF file
        return $pdf->download('report-transaction.pdf');
    }

    public function generateExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $sales = Order::when($startDate, function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        })->when($endDate, function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        })->get();

        $totalAmount = $sales->sum('total');

        return Excel::download(new SalesReportExport($sales, $totalAmount), 'Sales_Report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx');
    }


    public function generateReportDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $details = Order::with(['orderDetails.product'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->paginate(5)
            // ->appends($request->except('page'));
            ->get();

        // Calculate total amount
        // Calculate total amount based on quantity * price for each order detail
        $totalAmount = $details->flatMap(function ($order) {
            return $order->orderDetails->map(function ($detail) {
                return $detail->quantity * $detail->product->price;
            });
        })->sum();

        return view('pages.reports.detail', compact('details', 'totalAmount'));
    }

    public function generateExcelDetails(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $details = Order::with(['orderDetails.product'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->paginate(5)
            // ->appends($request->except('page'));
            ->get();

        // Calculate total amount
        // Calculate total amount based on quantity * price for each order detail
        $totalAmount = $details->flatMap(function ($order) {
            return $order->orderDetails->map(function ($detail) {
                return $detail->quantity * $detail->product->price;
            });
        })->sum();


        // Check if export is requested
        //if ($request->has('export') && $request->export === 'excel') {
        return Excel::download(new ReportDetailExport($details, $totalAmount), 'Report_Details.xlsx');
        // }
    }

    public function exportpdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $details = Order::with(['orderDetails.product'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->paginate(5)
            // ->appends($request->except('page'));
            ->get();

        // Calculate total amount
        // Calculate total amount based on quantity * price for each order detail
        $totalAmount = $details->flatMap(function ($order) {
            return $order->orderDetails->map(function ($detail) {
                return $detail->quantity * $detail->product->price;
            });
        })->sum();

        // Share data to the view for PDF
        // $pdf = PDF::loadView('pages.reports.pdf_detail', compact('details', 'totalAmount', 'startDate', 'endDate'));

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.reports.exportpdf', compact('details', 'totalAmount', 'startDate', 'endDate'));

        // Download the PDF file
        return $pdf->download('report-details.pdf');

        //return view('pages.reports.detail', compact('details', 'totalAmount'));
    }
}
