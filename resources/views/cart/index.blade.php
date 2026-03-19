@extends('layouts.shop')

@section('title', '购物车')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">购物车</h1>

    @if($carts->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 购物车商品列表 -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($carts as $cart)
                    <div class="p-6 flex items-center space-x-4">
                        <!-- 商品图片 -->
                        <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                            @if($cart->product->main_image)
                            <img src="{{ asset('storage/' . $cart->product->main_image) }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>

                        <!-- 商品信息 -->
                        <div class="flex-1">
                            <a href="{{ route('products.show', $cart->product) }}" class="font-medium text-gray-800 hover:text-indigo-600">
                                {{ $cart->product->name }}
                            </a>
                            <p class="text-sm text-gray-500 mt-1">单价：{{ $cart->product->formatted_price }}</p>

                            <!-- 数量调整 -->
                            @auth
                            <form action="{{ route('cart.update', $cart) }}" method="POST" class="mt-2 flex items-center">
                                @csrf
                                @method('PUT')
                                <div class="flex items-center border border-gray-300 rounded">
                                    <button type="submit" name="quantity" value="{{ max(1, $cart->quantity - 1) }}"
                                            class="px-3 py-1 text-gray-600 hover:bg-gray-100">-</button>
                                    <input type="text" value="{{ $cart->quantity }}" class="w-12 text-center border-0" readonly>
                                    <button type="submit" name="quantity" value="{{ min($cart->product->stock, $cart->quantity + 1) }}"
                                            class="px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                                </div>
                            </form>
                            @else
                            <p class="text-sm text-gray-500 mt-2">数量：{{ $cart->quantity }}</p>
                            @endauth
                        </div>

                        <!-- 商品总价 -->
                        <div class="text-right">
                            <p class="text-red-600 font-bold text-lg">{{ $cart->formatted_total_price }}</p>
                            @if($cart->product->stock < $cart->quantity)
                            <p class="text-xs text-red-500 mt-1">库存不足</p>
                            @endif

                            @auth
                            <form action="{{ route('cart.destroy', $cart) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-gray-400 hover:text-red-600">移除</button>
                            </form>
                            @endauth
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 清空购物车 -->
            @auth
            <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-gray-500 hover:text-red-600">清空购物车</button>
            </form>
            @endauth
        </div>

        <!-- 结算栏 -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                <h3 class="font-bold text-lg mb-4">订单摘要</h3>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">商品数量</span>
                        <span>{{ $carts->sum('quantity') }} 件</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">商品总额</span>
                        <span>¥{{ number_format($carts->sum('total_price') / 100, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">运费</span>
                        <span class="text-green-600">免运费</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between font-bold text-lg">
                        <span>应付总额</span>
                        <span class="text-red-600">¥{{ number_format($carts->sum('total_price') / 100, 2) }}</span>
                    </div>
                </div>

                @auth
                <a href="{{ route('orders.checkout') }}"
                   class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    去结算
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                    登录去结算
                </a>
                @endauth

                <a href="{{ route('products.index') }}"
                   class="block w-full text-center mt-3 text-indigo-600 hover:text-indigo-800 text-sm">
                    继续购物
                </a>
            </div>
        </div>
    </div>

    @else
    <!-- 空购物车 -->
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-32 h-32 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="text-gray-500 text-lg mb-4">购物车还是空的</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition">
            去逛逛
        </a>
    </div>
    @endif
</div>
@endsection
