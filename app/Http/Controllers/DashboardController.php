<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index()
    {
        // Get the current year and month
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        // Fetch sales data aggregated by day for the current month
        $salesData = Order::selectRaw('DAY(created_at) as day, SUM(total) as total')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        // Prepare the sales data for each day from 1 to 31
        $dates = [];
        $sales = [];
        $daysInMonth = Carbon::now()->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dates[] = Carbon::create($year, $month, $day)->toDateString(); // Store the date in Y-m-d format
            $sales[] = isset($salesData[$day]) ? $salesData[$day] : 0; // Store sales for the day or 0 if no sales
        }

        // Fetch top 5 products by quantity sold for the current month
        $products = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->whereYear('order_items.created_at', $year)
            ->whereMonth('order_items.created_at', $month)
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Calculate total sales for the current month
        $totalSales = DB::table('orders')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total');

        // Calculate total items sold for the current month
        $productSales = DB::table('order_items')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('quantity');

        // Count total products
        $totalItems = DB::table('products')->count();

        // Count total users
        $totalUsers = DB::table('users')->count();

        // Pass the data to the view
        return view('pages.dashboard', compact('sales', 'dates', 'products', 'totalSales', 'productSales', 'totalItems', 'totalUsers'));
    }
}
