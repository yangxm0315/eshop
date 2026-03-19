@extends('layouts.shop')

@section('title', '首页')

@section('content')
<!-- 轮播图区域 -->
<div class="bg-indigo-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-4">欢迎来到网上商城</h1>
        <p class="text-indigo-200 mb-8">优质商品，限时优惠，立即选购！</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
            立即选购
        </a>
    </div>
</div>

<!-- 分类导航 -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold mb-8 text-center">商品分类</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('products.index', ['category' => $category->id]) }}"
           class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto mb-4 bg-indigo-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
            @if($category->children->count() > 0)
            <p class="text-sm text-gray-500 mt-2">{{ $category->children->count() }} 个子分类</p>
            @endif
        </a>
        @endforeach
    </div>
</div>

<!-- 推荐商品 -->
<div class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold">热销商品</h2>
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">查看更多 &rarr;</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('products.show', $product) }}">
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        @if($product->main_image)
                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium text-gray-800 truncate">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1 truncate">{{ $product->description }}</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-red-600 font-bold">{{ $product->formatted_price }}</span>
                            <span class="text-xs text-gray-400">销量 {{ $product->sales }}</span>
                        </div>
                    </div>
                </a>
                <form action="{{ route('cart.add', $product) }}" method="POST" class="px-4 pb-4">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
                        加入购物车
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 最新商品 -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold">新品上市</h2>
        <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">查看更多 &rarr;</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($newProducts as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <a href="{{ route('products.show', $product) }}">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    @if($product->main_image)
                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-medium text-gray-800 truncate">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1 truncate">{{ $product->description }}</p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-red-600 font-bold">{{ $product->formatted_price }}</span>
                        <span class="text-xs text-gray-400">新品</span>
                    </div>
                </div>
            </a>
            <form action="{{ route('cart.add', $product) }}" method="POST" class="px-4 pb-4">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
                    加入购物车
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
