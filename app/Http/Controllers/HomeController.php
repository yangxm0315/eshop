<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        // 获取所有分类
        $categories = Category::whereNull('parent_id')
            ->where('is_show', true)
            ->orderBy('sort')
            ->with('children')
            ->get();

        // 获取推荐商品（按销量排序）
        $featuredProducts = Product::where('is_show', true)
            ->orderBy('sales', 'desc')
            ->limit(8)
            ->get();

        // 获取最新商品
        $newProducts = Product::where('is_show', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('home.index', compact('categories', 'featuredProducts', 'newProducts'));
    }
}
