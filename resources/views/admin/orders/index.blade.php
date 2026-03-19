@extends('layouts.admin')

@section('title', '订单管理')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold">订单列表</h2>
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center space-x-2">
                <input type="text" name="order_no" placeholder="订单号" value="{{ request('order_no') }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">全部状态</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>待支付</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>待发货</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>已发货</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>已完成</option>
                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>已取消</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">搜索</button>
            </form>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">订单号</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">用户</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">商品</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">金额</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">时间</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                    <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_no }}</a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    @foreach($order->items->take(2) as $item)
                    <div>{{ $item->product_name }} x{{ $item->quantity }}</div>
                    @endforeach
                    @if($order->items->count() > 2)
                    <div class="text-xs text-gray-400">+{{ $order->items->count() - 2 }} 件商品...</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">¥{{ number_format($order->pay_amount / 100, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($order->status == 0) bg-yellow-100 text-yellow-800
                        @elseif($order->status == 1) bg-blue-100 text-blue-800
                        @elseif($order->status == 2) bg-purple-100 text-purple-800
                        @elseif($order->status == 3) bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $order->status_text }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('m-d H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">详情</a>
                    @if($order->status == 1)
                    <form action="{{ route('admin.orders.ship', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="ml-3 text-blue-600 hover:text-blue-900">发货</button>
                    </form>
                    @endif
                    @if($order->status == 2)
                    <form action="{{ route('admin.orders.complete', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="ml-3 text-green-600 hover:text-green-900">完成</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">暂无订单</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($orders->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
