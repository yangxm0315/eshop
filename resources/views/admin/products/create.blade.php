@extends('layouts.admin')

@section('title', '添加商品')

@section('content')
<div class="bg-white rounded-lg shadow max-w-3xl">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-bold">添加商品</h2>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf

        <div class="space-y-6">
            <!-- 基本信息 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">商品分类 <span class="text-red-500">*</span></label>
                <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">请选择分类</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">商品名称 <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="200"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">商品描述</label>
                <textarea name="description" rows="2" maxlength="500"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">价格（分）<span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">例如：9900 表示 99.00 元</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">库存数量 <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock') }}" required min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">商品主图</label>
                <input type="file" name="main_image" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">支持 JPG、PNG 格式，最大 2MB</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">商品详情</label>
                <textarea name="content" rows="10"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('content') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">支持 HTML 代码</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">取消</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">保存</button>
        </div>
    </form>
</div>
@endsection
