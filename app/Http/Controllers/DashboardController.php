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
        // Fetch sales data aggregated by day
        $salesData = Order::selectRaw('DATE(created_at) as date, SUM(sub_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Prepare the sales data for each day
        $dates = [];
        $sales = [];

        // Loop through the last 30 days to ensure you have a full month of data
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString(); // Get the date
            $dates[] = $date; // Store the date
            $sales[] = isset($salesData[$date]) ? $salesData[$date] : 0; // Store sales or 0
        }

        $products = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name as product_name', OrderItem::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Get the current month
        $currentMonth = Carbon::now()->month;

        // Calculate total sales for the current month
        $totalSales = DB::table('orders')
            ->whereMonth('created_at', $currentMonth)
            ->sum('sub_total'); // Adjust 'amount' to match the column name in your database
        $productSales = DB::table('order_items')
            ->whereMonth('created_at', $currentMonth)
            ->sum('quantity'); // Adjust 'amount' to match the column name in your database
        $totalItems = DB::table('products')
            ->count('id');
        $totalUsers = DB::table('users')
            ->count('id');
        // Pass the data to the view
        return view('pages.dashboard', compact('sales', 'dates', 'products', 'totalSales', 'productSales', 'totalItems', 'totalUsers'));
    }
}
