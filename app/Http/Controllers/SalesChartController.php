<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesChartController extends Controller
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

        // Query to get the top products based on quantity sold
        // $products = OrderItem::select('product_id', OrderItem::raw('SUM(quantity) as total_quantity'))
        //     ->groupBy('product_id')
        //     ->orderByDesc('total_quantity')
        //     ->limit(5) // Get top 5 products
        //     ->get();

        $products = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name as product_name', OrderItem::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();


        // Pass the sales data to the view
        return view('pages.chart.index', compact('sales', 'dates', 'products'));
    }
}
