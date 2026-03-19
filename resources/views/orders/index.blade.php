@extends('layouts.shop')

@section('title', '我的订单')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">我的订单</h1>

    <!-- 订单状态筛选 -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="flex border-b">
            <a href="{{ route('orders.index') }}"
               class="px-6 py-4 text-sm font-medium {{ !request('status') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                全部订单
            </a>
            <a href="{{ route('orders.index', ['status' => 0]) }}"
               class="px-6 py-4 text-sm font-medium {{ request('status') == '0' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                待支付
            </a>
            <a href="{{ route('orders.index', ['status' => 1]) }}"
               class="px-6 py-4 text-sm font-medium {{ request('status') == '1' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                待发货
            </a>
            <a href="{{ route('orders.index', ['status' => 2]) }}"
               class="px-6 py-4 text-sm font-medium {{ request('status') == '2' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                待收货
            </a>
            <a href="{{ route('orders.index', ['status' => 3]) }}"
               class="px-6 py-4 text-sm font-medium {{ request('status') == '3' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                已完成
            </a>
            <a href="{{ route('orders.index', ['status' => 4]) }}"
               class="px-6 py-4 text-sm font-medium {{ request('status') == '4' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                已取消
            </a>
        </div>
    </div>

    <!-- 订单列表 -->
    @if($orders->count() > 0)
    <div class="space-y-6">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- 订单头部 -->
            <div class="bg-gray-50 px-6 py-3 flex justify-between items-center border-b">
                <div class="text-sm">
                    <span class="text-gray-500">订单号：</span>
                    <span class="font-medium">{{ $order->order_no }}</span>
                    <span class="mx-4 text-gray-300">|</span>
                    <span class="text-gray-500">下单时间：</span>
                    <span>{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <span class="px-3 py-1 rounded-full text-sm
                    @if($order->status == 0) bg-yellow-100 text-yellow-800
                    @elseif($order->status == 1) bg-blue-100 text-blue-800
                    @elseif($order->status == 2) bg-purple-100 text-purple-800
                    @elseif($order->status == 3) bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $order->status_text }}
                </span>
            </div>

            <!-- 订单商品 -->
            <div class="p-6">
                @foreach($order->items as $item)
                <div class="flex items-center py-4 border-b last:border-0">
                    <div class="w-20 h-20 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                        @if($item->product_image)
                        <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-medium">{{ $item->product_name }}</p>
                        <p class="text-sm text-gray-500 mt-1">¥{{ number_format($item->price / 100, 2) }} × {{ $item->quantity }}</p>
                    </div>
                    <p class="text-red-600 font-bold">¥{{ number_format($item->total_price / 100, 2) }}</p>
                </div>
                @endforeach
            </div>

            <!-- 订单底部 -->
            <div class="px-6 py-4 bg-gray-50 flex justify-between items-center">
                <div>
                    <span class="text-gray-500">共 {{ $order->items->sum('quantity') }} 件商品</span>
                    <span class="mx-4 text-gray-300">|</span>
                    <span class="text-gray-500">收货人：{{ $order->address->name }}</span>
                    <span class="text-gray-500 ml-4">{{ $order->address->phone }}</span>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 text-sm">实付金额</p>
                    <p class="text-red-600 text-xl font-bold">{{ $order->formatted_pay_amount }}</p>
                </div>
            </div>

            <!-- 订单操作 -->
            <div class="px-6 py-3 flex justify-end space-x-3 border-t">
                <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition text-sm">
                    查看详情
                </a>
                @if($order->canCancel())
                <button onclick="showCancelModal({{ $order->id }})" class="px-4 py-2 border border-red-300 text-red-600 rounded hover:bg-red-50 transition text-sm">
                    取消订单
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- 分页 -->
    <div class="mt-8">
        {{ $orders->links() }}
    </div>

    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500 text-lg mb-4">暂无订单</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition">
            去购物
        </a>
    </div>
    @endif
</div>

<!-- 取消订单模态框 -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">取消订单</h3>
        <form id="cancelForm" method="POST">
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
function showCancelModal(orderId) {
    document.getElementById('cancelForm').action = '/orders/' + orderId + '/cancel';
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}
</script>
@endpush
@endsection
