@extends('layouts.shop')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- 面包屑 -->
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">首页</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="hover:text-indigo-600">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- 商品图片 -->
        <div>
            <div class="bg-gray-100 rounded-lg overflow-hidden aspect-square flex items-center justify-center">
                @if($product->main_image)
                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                @endif
            </div>
        </div>

        <!-- 商品信息 -->
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

            <div class="flex items-center space-x-4 mb-6">
                <span class="text-red-600 text-4xl font-bold">{{ $product->formatted_price }}</span>
                <span class="text-gray-400 line-through text-lg">¥{{ number_format($product->price / 100 * 1.5, 2) }}</span>
            </div>

            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">销量</span>
                        <p class="font-medium">{{ $product->sales }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">库存</span>
                        <p class="font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->stock > 0 ? $product->stock . ' 件' : '缺货' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500">分类</span>
                        <p class="font-medium">{{ $product->category->name }}</p>
                    </div>
                </div>
            </div>

            <p class="text-gray-600 mb-8">{{ $product->description }}</p>

            <!-- 购买操作 -->
            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center space-x-4">
                @csrf
                <div class="flex items-center border border-gray-300 rounded-lg">
                    <button type="button" onclick="decrementQuantity()" class="px-4 py-3 text-gray-600 hover:bg-gray-100">-</button>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                           class="w-16 text-center border-0 focus:ring-0" readonly>
                    <button type="button" onclick="incrementQuantity()" class="px-4 py-3 text-gray-600 hover:bg-gray-100">+</button>
                </div>
                <button type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }}
                        class="flex-1 bg-indigo-600 text-white py-3 px-8 rounded-lg hover:bg-indigo-700 transition font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                    {{ $product->stock > 0 ? '加入购物车' : '暂时缺货' }}
                </button>
            </form>

            @auth
            <a href="{{ route('orders.checkout') }}"
               class="mt-4 block text-center bg-orange-500 text-white py-3 px-8 rounded-lg hover:bg-orange-600 transition font-semibold">
                立即购买
            </a>
            @else
            <p class="mt-4 text-center text-sm text-gray-500">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">登录</a> 后可以购买
            </p>
            @endauth
        </div>
    </div>

    <!-- 商品详情 -->
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-6">商品详情</h2>
        <div class="bg-white rounded-lg shadow-md p-8">
            @if($product->content)
            {!! $product->content !!}
            @else
            <p class="text-gray-500 text-center py-8">暂无商品详情</p>
            @endif
        </div>
    </div>

    <!-- 相关商品 -->
    @if($relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-6">相关商品</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('products.show', $relatedProduct) }}">
                    <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                        @if($relatedProduct->main_image)
                        <img src="{{ asset('storage/' . $relatedProduct->main_image) }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium text-gray-800 truncate">{{ $relatedProduct->name }}</h3>
                        <span class="text-red-600 font-bold mt-2 block">{{ $relatedProduct->formatted_price }}</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function incrementQuantity() {
    const input = document.getElementById('quantity');
    const max = {{ $product->stock }};
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>
@endpush
@endsection
