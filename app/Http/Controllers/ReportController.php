<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
//use App\Models\Report as ModelsReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $totalAmount = $sales->sum('sub_total');

        return view('pages.reports.index', compact('sales', 'startDate', 'endDate', 'totalAmount'));
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
        $totalAmount = $details->sum('sub_total');


        return view('pages.reports.detail', compact('details', 'totalAmount'));
    }

    public function generateReportDetail(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Retrieve orders with their products and calculate the total for each order
        $orders = Order::with('products')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('created_at', '<=', $endDate);
            })
            ->get();

        // Calculate total amount for each order
        foreach ($orders as $order) {
            $order->total_amount = $order->products->sum(function ($product) {
                return $product->pivot->quantity * $product->pivot->price;
            });
        }

        return view('pages.reports.detail', compact('orders', 'startDate', 'endDate'));
    }
}
