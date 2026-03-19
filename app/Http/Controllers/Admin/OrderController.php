<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * 订单列表
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_no')) {
            $query->where('order_no', 'like', '%' . $request->order_no . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * 订单详情
     */
    public function show(Order $order)
    {
        $order->load(['user', 'address', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * 发货
     */
    public function ship(Request $request, Order $order)
    {
        if ($order->status != Order::STATUS_TO_SHIP) {
            return back()->with('error', '该订单无法发货');
        }

        $order->update([
            'status' => Order::STATUS_SHIPPED,
            'shipped_at' => now(),
        ]);

        return back()->with('success', '订单已发货');
    }

    /**
     * 确认收货
     */
    public function complete(Order $order)
    {
        if ($order->status != Order::STATUS_SHIPPED) {
            return back()->with('error', '该订单无法确认收货');
        }

        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return back()->with('success', '订单已完成');
    }
}
