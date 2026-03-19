<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateCartCount
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 每次请求时更新购物车计数
        if (Auth::check()) {
            $count = Auth::user()->carts()->sum('quantity');
        } else {
            $count = array_sum(session('cart', []));
        }
        session(['cart_count' => $count]);

        return $next($request);
    }
}
