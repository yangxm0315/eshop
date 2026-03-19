@extends('layouts.shop')

@section('title', '商品列表')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- 左侧筛选栏 -->
        <div class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                <h3 class="font-bold text-lg mb-4">筛选条件</h3>

                <form action="{{ route('products.index') }}" method="GET">
                    <!-- 分类筛选 -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">商品分类</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="text-indigo-600">
                                <span class="ml-2 text-sm text-gray-600">全部分类</span>
                            </label>
                            @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="radio" name="category" value="{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }} class="text-indigo-600">
                                <span class="ml-2 text-sm text-gray-600">{{ $category->name }}</span>
                            </label>
                            @if($category->children->count() > 0)
                                @foreach($category->children as $child)
                                <label class="flex items-center pl-6">
                                    <input type="radio" name="category" value="{{ $child->id }}" {{ request('category') == $child->id ? 'checked' : '' }} class="text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-500">{{ $child->name }}</span>
                                </label>
                                @endforeach
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- 价格区间 -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">价格区间</h4>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="min_price" placeholder="最低价" value="{{ request('min_price') }}"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            <span class="text-gray-500">-</span>
                            <input type="number" name="max_price" placeholder="最高价" value="{{ request('max_price') }}"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                    </div>

                    <!-- 搜索词 -->
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                        应用筛选
                    </button>

                    @if(request()->anyFilled(['category', 'min_price', 'max_price', 'search']))
                    <a href="{{ route('products.index') }}" class="block mt-2 text-center text-sm text-indigo-600 hover:text-indigo-800">
                        清除筛选
                    </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- 右侧商品列表 -->
        <div class="flex-1">
            <!-- 排序栏 -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    共 <span class="font-medium text-gray-800">{{ $products->total() }}</span> 件商品
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">排序：</span>
                    <a href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'default'])) }}"
                       class="text-sm {{ request('sort') == 'default' ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        默认
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'sales'])) }}"
                       class="text-sm {{ request('sort') == 'sales' ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        销量
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}"
                       class="text-sm {{ request('sort') == 'price_asc' ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        价格↑
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}"
                       class="text-sm {{ request('sort') == 'price_desc' ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        价格↓
                    </a>
                    <a href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}"
                       class="text-sm {{ request('sort') == 'newest' ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        最新
                    </a>
                </div>
            </div>

            <!-- 商品网格 -->
            @if($products->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <a href="{{ route('products.show', $product) }}">
                        <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                            @if($product->main_image)
                            <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition">
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

            <!-- 分页 -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500 text-lg">暂无商品</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
