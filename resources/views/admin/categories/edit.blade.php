@extends('layouts.admin')

@section('title', '编辑分类')

@section('content')
<div class="bg-white rounded-lg shadow max-w-md">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-bold">编辑分类</h2>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">分类名称 <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">父分类</label>
                <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">无（作为一级分类）</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">不能将自己设为父分类</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">排序</label>
                <input type="number" name="sort" value="{{ old('sort', $category->sort) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_show" value="1" {{ old('is_show', $category->is_show) ? 'checked' : '' }} class="text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">是否显示</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">取消</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">保存</button>
        </div>
    </form>
</div>
@endsection
