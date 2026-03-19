<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * 后台首页
     */
    public function index()
    {
        // 统计数据
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::where('role', 0)->count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'to_ship_orders' => Order::where('status', Order::STATUS_TO_SHIP)->count(),
        ];

        // 最近订单
        $recentOrders = Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
