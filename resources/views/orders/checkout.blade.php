@extends('layouts.shop')

@section('title', '确认订单')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">确认订单</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <!-- 收货地址 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">收货地址</h2>
            <div class="space-y-3">
                @forelse($addresses as $address)
                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-indigo-500 transition {{ $defaultAddress?->id == $address->id ? 'border-indigo-500 bg-indigo-50' : '' }}">
                    <input type="radio" name="address_id" value="{{ $address->id }}"
                           class="mt-1 text-indigo-600" {{ $defaultAddress?->id == $address->id ? 'checked' : '' }}>
                    <div class="ml-3">
                        <p class="font-medium">{{ $address->name }} <span class="text-gray-500 font-normal">{{ $address->phone }}</span></p>
                        <p class="text-sm text-gray-500 mt-1">{{ $address->full_address }}</p>
                        @if($address->is_default)
                        <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-100 text-indigo-600 text-xs rounded">默认地址</span>
                        @endif
                    </div>
                </label>
                @empty
                <p class="text-gray-500 text-center py-8">
                    暂无收货地址，请先到 <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:underline">个人中心</a> 添加
                </p>
                @endforelse
            </div>
        </div>

        <!-- 商品列表 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">商品清单</h2>
            <div class="divide-y divide-gray-200">
                @foreach($carts as $cart)
                <div class="py-4 flex items-center">
                    <div class="w-20 h-20 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                        @if($cart->product->main_image)
                        <img src="{{ asset('storage/' . $cart->product->main_image) }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-medium">{{ $cart->product->name }}</p>
                        <p class="text-sm text-gray-500 mt-1">单价：{{ $cart->product->formatted_price }} × {{ $cart->quantity }}</p>
                    </div>
                    <p class="text-red-600 font-bold">{{ $cart->formatted_total_price }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 订单备注 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">订单备注</h2>
            <textarea name="remark" rows="3" placeholder="选填：对本订单的说明（如配送时间要求等）"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
        </div>

        <!-- 金额汇总 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500">商品总额</span>
                <span>¥{{ number_format($totalAmount / 100, 2) }}</span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500">运费</span>
                <span class="text-green-600">免运费</span>
            </div>
            <div class="border-t pt-4 flex justify-between items-center text-lg font-bold">
                <span>应付总额</span>
                <span class="text-red-600">¥{{ number_format($totalAmount / 100, 2) }}</span>
            </div>
        </div>

        <!-- 提交按钮 -->
        <div class="flex justify-end">
            <a href="{{ route('cart.index') }}" class="mr-4 px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                返回购物车
            </a>
            <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                提交订单
            </button>
        </div>
    </form>
</div>
@endsection
