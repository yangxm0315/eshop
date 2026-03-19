<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * 购物车列表
     */
    public function index()
    {
        $carts = $this->getCartItems();

        return view('cart.index', compact('carts'));
    }

    /**
     * 添加商品到购物车
     */
    public function add(Request $request, Product $product)
    {
        $quantity = $request->input('quantity', 1);

        if ($product->stock < $quantity) {
            return back()->with('error', '商品库存不足');
        }

        if (Auth::check()) {
            // 已登录用户：存储到数据库
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id(), 'product_id' => $product->id],
                ['quantity' => 0]
            );
            $cart->increment('quantity', $quantity);
        } else {
            // 未登录用户：存储到 Session
            $sessionCart = session('cart', []);
            $productId = $product->id;

            if (isset($sessionCart[$productId])) {
                $sessionCart[$productId] += $quantity;
            } else {
                $sessionCart[$productId] = $quantity;
            }

            session(['cart' => $sessionCart]);
            $this->updateCartCount();
        }

        return back()->with('success', '已添加到购物车');
    }

    /**
     * 更新购物车商品数量
     */
    public function update(Request $request, Cart $cart)
    {
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->destroy($cart);
        }

        if ($cart->product->stock < $quantity) {
            return back()->with('error', '商品库存不足');
        }

        $cart->update(['quantity' => $quantity]);

        return back()->with('success', '购物车已更新');
    }

    /**
     * 从购物车移除商品
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return back()->with('success', '商品已移除');
    }

    /**
     * 清空购物车
     */
    public function clear()
    {
        if (Auth::check()) {
            Auth::user()->carts()->delete();
        } else {
            session(['cart' => []]);
            $this->updateCartCount();
        }

        return back()->with('success', '购物车已清空');
    }

    /**
     * 获取购物车商品（合并登录和 Session 数据）
     */
    private function getCartItems()
    {
        if (Auth::check()) {
            return Auth::user()->carts()->with('product')->get();
        }

        // 从 Session 获取
        $sessionCart = session('cart', []);
        $carts = [];

        foreach ($sessionCart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $carts[] = new Cart([
                    'product_id' => $productId,
                    'product' => $product,
                    'quantity' => $quantity,
                ]);
            }
        }

        return collect($carts);
    }

    /**
     * 更新购物车数量计数
     */
    private function updateCartCount()
    {
        if (Auth::check()) {
            $count = Auth::user()->carts()->sum('quantity');
        } else {
            $count = array_sum(session('cart', []));
        }
        session(['cart_count' => $count]);
    }
}
