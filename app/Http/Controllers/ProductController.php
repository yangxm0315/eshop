<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 商品列表页
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_show', true);

        // 分类筛选
        if ($request->filled('category')) {
            $category = Category::find($request->category);
            if ($category) {
                $categoryIds = array_merge([$category->id], $category->allChildren());
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 搜索
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 价格区间
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price * 100);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price * 100);
        }

        // 排序
        $sort = $request->get('sort', 'default');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'sales':
                $query->orderBy('sales', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('sort')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // 获取所有一级分类用于筛选
        $categories = Category::whereNull('parent_id')
            ->where('is_show', true)
            ->orderBy('sort')
            ->with('children')
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * 商品详情页
     */
    public function show(Product $product)
    {
        if (!$product->is_show) {
            abort(404);
        }

        // 获取同分类下的其他商品
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_show', true)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
