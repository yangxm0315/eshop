@extends('layouts.admin')

@section('title', '订单详情')

@section('content')
<div class="max-w-4xl">
    <!-- 订单信息 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold">订单信息</h2>
            <span class="px-3 py-1 rounded-full text-sm
                @if($order->status == 0) bg-yellow-100 text-yellow-800
                @elseif($order->status == 1) bg-blue-100 text-blue-800
                @elseif($order->status == 2) bg-purple-100 text-purple-800
                @elseif($order->status == 3) bg-green-100 text-green-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $order->status_text }}
            </span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">订单号</p>
                    <p class="font-medium">{{ $order->order_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">下单时间</p>
                    <p class="font-medium">{{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">用户</p>
                    <p class="font-medium">{{ $order->user->name }} ({{ $order->user->email }})</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">用户电话</p>
                    <p class="font-medium">{{ $order->user->phone ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 收货地址 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-bold">收货地址</h2>
        </div>
        <div class="p-6">
            <p class="font-medium">{{ $order->address->name }} <span class="text-gray-500 font-normal">{{ $order->address->phone }}</span></p>
            <p class="text-gray-600 mt-2">{{ $order->address->full_address }}</p>
        </div>
    </div>

    <!-- 商品列表 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-bold">商品清单</h2>
        </div>
        <div class="p-6">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">商品</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">单价</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">数量</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">小计</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}" class="w-12 h-12 object-cover rounded mr-3">
                                @endif
                                <span>{{ $item->product_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">¥{{ number_format($item->price / 100, 2) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 font-medium">¥{{ number_format($item->total_price / 100, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-medium">订单总额：</td>
                        <td class="px-4 py-3 font-medium">¥{{ number_format($order->total_amount / 100, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-medium">实付金额：</td>
                        <td class="px-4 py-3 font-bold text-red-600">¥{{ number_format($order->pay_amount / 100, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- 订单备注 -->
    @if($order->remark)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-bold">订单备注</h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600">{{ $order->remark }}</p>
        </div>
    </div>
    @endif

    <!-- 时间线 -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-bold">订单时间线</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3 text-sm">
                <div class="flex">
                    <span class="w-32 text-gray-500">下单时间</span>
                    <span>{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @if($order->paid_at)
                <div class="flex">
                    <span class="w-32 text-gray-500">支付时间</span>
                    <span>{{ $order->paid_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
                @if($order->shipped_at)
                <div class="flex">
                    <span class="w-32 text-gray-500">发货时间</span>
                    <span>{{ $order->shipped_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
                @if($order->completed_at)
                <div class="flex">
                    <span class="w-32 text-gray-500">完成时间</span>
                    <span>{{ $order->completed_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
                @if($order->cancelled_at)
                <div class="flex">
                    <span class="w-32 text-gray-500">取消时间</span>
                    <span>{{ $order->cancelled_at->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 取消原因 -->
    @if($order->cancel_reason)
    <div class="bg-red-50 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-red-800 mb-2">订单已取消</h3>
        <p class="text-red-600">取消原因：{{ $order->cancel_reason }}</p>
    </div>
    @endif

    <!-- 操作按钮 -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">返回</a>
        @if($order->status == 1)
        <form action="{{ route('admin.orders.ship', $order) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">确认发货</button>
        </form>
        @endif
        @if($order->status == 2)
        <form action="{{ route('admin.orders.complete', $order) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">确认完成</button>
        </form>
        @endif
    </div>
</div>
@endsection
