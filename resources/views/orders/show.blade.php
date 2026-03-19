@extends('layouts.shop')

@section('title', '订单详情')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- 面包屑 -->
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">首页</a>
        <span class="mx-2">/</span>
        <a href="{{ route('orders.index') }}" class="hover:text-indigo-600">我的订单</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $order->order_no }}</span>
    </nav>

    <h1 class="text-3xl font-bold mb-8">订单详情</h1>

    <!-- 订单状态 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">订单号</p>
                <p class="font-medium">{{ $order->order_no }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">下单时间</p>
                <p class="font-medium">{{ $order->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div>
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($order->status == 0) bg-yellow-100 text-yellow-800
                    @elseif($order->status == 1) bg-blue-100 text-blue-800
                    @elseif($order->status == 2) bg-purple-100 text-purple-800
                    @elseif($order->status == 3) bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $order->status_text }}
                </span>
            </div>
        </div>
    </div>

    <!-- 收货地址 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">收货信息</h2>
        <p class="font-medium">{{ $order->address->name }} <span class="text-gray-500 font-normal">{{ $order->address->phone }}</span></p>
        <p class="text-gray-600 mt-2">{{ $order->address->full_address }}</p>
    </div>

    <!-- 商品列表 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">商品信息</h2>
        <div class="divide-y divide-gray-200">
            @foreach($order->items as $item)
            <div class="py-4 flex items-center">
                <div class="w-20 h-20 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                    @if($item->product_image)
                    <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="ml-4 flex-1">
                    <p class="font-medium">{{ $item->product_name }}</p>
                    <p class="text-sm text-gray-500 mt-1">单价：¥{{ number_format($item->price / 100, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 text-sm">x {{ $item->quantity }}</p>
                    <p class="text-red-600 font-bold">¥{{ number_format($item->total_price / 100, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 金额信息 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">金额信息</h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-500">商品总额</span>
                <span>¥{{ number_format($order->total_amount / 100, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">运费</span>
                <span class="text-green-600">免运费</span>
            </div>
            @if($order->status > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">支付方式</span>
                <span>在线支付</span>
            </div>
            @endif
            <div class="border-t pt-3 flex justify-between text-lg font-bold">
                <span>实付金额</span>
                <span class="text-red-600">¥{{ number_format($order->pay_amount / 100, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- 订单备注 -->
    @if($order->remark)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">订单备注</h2>
        <p class="text-gray-600">{{ $order->remark }}</p>
    </div>
    @endif

    <!-- 时间线 -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">订单时间线</h2>
        <div class="space-y-3 text-sm">
            <div class="flex items-center">
                <span class="w-20 text-gray-500">下单时间</span>
                <span>{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            @if($order->paid_at)
            <div class="flex items-center">
                <span class="w-20 text-gray-500">支付时间</span>
                <span>{{ $order->paid_at->format('Y-m-d H:i:s') }}</span>
            </div>
            @endif
            @if($order->shipped_at)
            <div class="flex items-center">
                <span class="w-20 text-gray-500">发货时间</span>
                <span>{{ $order->shipped_at->format('Y-m-d H:i:s') }}</span>
            </div>
            @endif
            @if($order->completed_at)
            <div class="flex items-center">
                <span class="w-20 text-gray-500">完成时间</span>
                <span>{{ $order->completed_at->format('Y-m-d H:i:s') }}</span>
            </div>
            @endif
            @if($order->cancelled_at)
            <div class="flex items-center">
                <span class="w-20 text-gray-500">取消时间</span>
                <span>{{ $order->cancelled_at->format('Y-m-d H:i:s') }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- 取消原因 -->
    @if($order->cancel_reason)
    <div class="bg-red-50 rounded-lg p-6 mb-6">
        <h2 class="text-lg font-bold mb-2 text-red-800">订单已取消</h2>
        <p class="text-red-600">取消原因：{{ $order->cancel_reason }}</p>
    </div>
    @endif

    <!-- 操作按钮 -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('orders.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            返回订单列表
        </a>
        @if($order->canCancel())
        <button onclick="showCancelModal()" class="px-6 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
            取消订单
        </button>
        @endif
    </div>
</div>

<!-- 取消订单模态框 -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">取消订单</h3>
        <form id="cancelForm" method="POST" action="{{ route('orders.cancel', $order) }}">
            @csrf
            @method('PUT')
            <textarea name="cancel_reason" rows="3" placeholder="请输入取消原因" required
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-4"></textarea>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideCancelModal()" class="px-4 py-2 border rounded hover:bg-gray-50">返回</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">确认取消</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}
</script>
@endpush
@endsection
