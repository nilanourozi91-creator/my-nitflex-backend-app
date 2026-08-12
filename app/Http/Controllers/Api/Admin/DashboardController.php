<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index(): JsonResponse
    {
        $totalSales = Payment::where('status', 'paid')
            ->sum('amount');

        $totalOrders = Order::count();

        $totalCustomers = User::where('role', 'customer')
            ->count();

        $totalProducts = products::count();

        $pendingOrders = Order::where('status', 'pending')
            ->count();

        $lowStockProducts = products::whereHas(
            'inventory',
            function ($query) {
                $query->whereColumn(
                    'quantity',
                    '<=',
                    'low_stock_limit'
                );
            }
        )->count();

        $outOfStockProducts = products::whereHas(
            'inventory',
            function ($query) {
                $query->where('quantity', 0);
            }
        )->count();

        return response()->json([
            'success' => true,

            'data' => [
                'statistics' => [
                    'total_sales' => $totalSales,
                    'total_orders' => $totalOrders,
                    'total_customers' => $totalCustomers,
                    'total_products' => $totalProducts,
                    'pending_orders' => $pendingOrders,
                    'low_stock_products' => $lowStockProducts,
                    'out_of_stock_products' => $outOfStockProducts,
                ],
            ],
        ]);
    }
    public function sales()
{
    $sales = Payment::query()
        ->where('status', 'paid')
        ->selectRaw(
            'DATE(created_at) as date, SUM(amount) as total'
        )
        ->groupBy('date')
        ->orderBy('date')
        ->limit(30)
        ->get();

    return response()->json([
        'success' => true,
        'data' => $sales,
    ]);
}
public function recentOrders(): JsonResponse
{
    $orders = Order::with([
        'user:id,name,email',
        'items.product:id,name',
        'payment',
    ])
        ->latest()
        ->limit(10)
        ->get();

    return response()->json([
        'success' => true,
        'data' => $orders,
    ]);
}
public function lowStock(): JsonResponse
{
    $products = products::with([
        'inventory',
        'category',
    ])
        ->whereHas(
            'inventory',
            function ($query) {
                $query->whereColumn(
                    'quantity',
                    '<=',
                    'low_stock_limit'
                );
            }
        )
        ->orderBy(
            'id',
            'desc'
        )
        ->limit(10)
        ->get();

    return response()->json([
        'success' => true,
        'data' => $products,
    ]);
}
}
