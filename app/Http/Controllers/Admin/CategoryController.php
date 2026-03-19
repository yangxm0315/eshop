<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 分类列表
     */
    public function index()
    {
        // 获取所有一级分类及其子分类
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * 添加分类
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'sort' => 'nullable|integer|min:0',
            'is_show' => 'boolean',
        ]);

        Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'sort' => $request->sort ?? 0,
            'is_show' => $request->is_show ?? true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', '分类添加成功');
    }

    /**
     * 编辑分类
     */
    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * 更新分类
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'sort' => 'nullable|integer|min:0',
            'is_show' => 'boolean',
        ]);

        // 不能将自己设为父分类
        if ($request->parent_id == $category->id) {
            return back()->with('error', '不能将自己设为父分类');
        }

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'sort' => $request->sort ?? 0,
            'is_show' => $request->is_show ?? true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', '分类更新成功');
    }

    /**
     * 删除分类
     */
    public function destroy(Category $category)
    {
        // 检查是否有子分类
        if ($category->children->count() > 0) {
            return back()->with('error', '无法删除：该分类下还有子分类');
        }

        // 检查是否有商品
        if ($category->products->count() > 0) {
            return back()->with('error', '无法删除：该分类下还有商品');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', '分类删除成功');
    }
}
