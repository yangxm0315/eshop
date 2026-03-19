<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * 商品列表
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * 创建商品页面
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * 保存商品
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'main_image' => 'nullable|image|max:2048',
            'content' => 'nullable|string',
        ]);

        $data = $request->only(['category_id', 'name', 'description', 'price', 'stock', 'content']);

        // 处理主图上传
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', '商品创建成功');
    }

    /**
     * 编辑商品页面
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * 更新商品
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'main_image' => 'nullable|image|max:2048',
            'content' => 'nullable|string',
        ]);

        $data = $request->only(['category_id', 'name', 'description', 'price', 'stock', 'content']);

        // 处理主图上传
        if ($request->hasFile('main_image')) {
            // 删除旧图片
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', '商品更新成功');
    }

    /**
     * 删除商品
     */
    public function destroy(Product $product)
    {
        // 删除主图
        if ($product->main_image) {
            Storage::disk('public')->delete($product->main_image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', '商品删除成功');
    }

    /**
     * 上架/下架商品
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_show' => !$product->is_show]);
        return back()->with('success', '商品状态已更新');
    }
}
