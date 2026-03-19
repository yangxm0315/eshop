<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 订单列表
     */
    public function index(Request $request)
    {
        $query = Auth::user()->orders()->with(['items.product', 'address']);

        // 按状态筛选
        $status = $request->get('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * 订单详情
     */
    public function show(Order $order)
    {
        // 权限检查：只能查看自己的订单
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'address']);

        return view('orders.show', compact('order'));
    }

    /**
     * 确认订单页面（下单前）
     */
    public function checkout()
    {
        // 获取购物车商品
        $carts = Auth::user()->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '购物车为空');
        }

        // 计算总价
        $totalAmount = $carts->sum(function($cart) {
            return $cart->product->price * $cart->quantity;
        });

        // 获取用户地址
        $addresses = Auth::user()->addresses;
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        return view('orders.checkout', compact('carts', 'totalAmount', 'addresses', 'defaultAddress'));
    }

    /**
     * 提交订单
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'remark' => 'nullable|string|max:500',
        ]);

        // 获取购物车商品
        $carts = Auth::user()->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '购物车为空');
        }

        // 检查库存
        foreach ($carts as $cart) {
            if ($cart->product->stock < $cart->quantity) {
                return back()->with('error', "商品「{$cart->product->name}」库存不足");
            }
        }

        DB::beginTransaction();
        try {
            // 计算总价
            $totalAmount = $carts->sum(function($cart) {
                return $cart->product->price * $cart->quantity;
            });

            // 创建订单
            $order = Order::create([
                'order_no' => Order::generateOrderNo(),
                'user_id' => Auth::id(),
                'address_id' => $request->address_id,
                'total_amount' => $totalAmount,
                'pay_amount' => $totalAmount,
                'status' => Order::STATUS_PENDING,
                'remark' => $request->remark,
            ]);

            // 创建订单项
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name,
                    'product_image' => $cart->product->main_image,
                    'price' => $cart->product->price,
                    'quantity' => $cart->quantity,
                    'total_price' => $cart->product->price * $cart->quantity,
                ]);

                // 减少库存，增加销量
                $cart->product->decreaseStock($cart->quantity);
                $cart->product->increaseSales($cart->quantity);
            }

            // 清空购物车
            Auth::user()->carts()->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', '订单提交成功');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '订单提交失败：' . $e->getMessage());
        }
    }

    /**
     * 取消订单
     */
    public function cancel(Request $request, Order $order)
    {
        // 权限检查
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:200',
        ]);

        if (!$order->canCancel()) {
            return back()->with('error', '该订单无法取消');
        }

        DB::beginTransaction();
        try {
            // 取消订单
            $order->cancel($request->cancel_reason);

            // 恢复库存
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            DB::commit();

            return back()->with('success', '订单已取消');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '订单取消失败：' . $e->getMessage());
        }
    }
}
