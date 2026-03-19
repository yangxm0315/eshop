<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => '智能手机 5G 全网通',
                'description' => '高性能处理器，超长续航，拍照清晰',
                'price' => 299900,
                'stock' => 100,
                'sales' => 500,
                'is_show' => true,
                'sort' => 1,
            ],
            [
                'category_id' => 2,
                'name' => '轻薄笔记本电脑',
                'description' => '14 英寸高清屏，办公娱乐两不误',
                'price' => 499900,
                'stock' => 50,
                'sales' => 200,
                'is_show' => true,
                'sort' => 2,
            ],
            [
                'category_id' => 4,
                'name' => '男士休闲 T 恤',
                'description' => '纯棉面料，舒适透气',
                'price' => 9900,
                'stock' => 500,
                'sales' => 1000,
                'is_show' => true,
                'sort' => 1,
            ],
            [
                'category_id' => 5,
                'name' => '女士连衣裙',
                'description' => '时尚百搭，优雅大方',
                'price' => 19900,
                'stock' => 200,
                'sales' => 800,
                'is_show' => true,
                'sort' => 2,
            ],
            [
                'category_id' => 7,
                'name' => '实木餐桌椅套装',
                'description' => '环保材质，坚固耐用',
                'price' => 159900,
                'stock' => 30,
                'sales' => 150,
                'is_show' => true,
                'sort' => 1,
            ],
            [
                'category_id' => 8,
                'name' => '不锈钢锅具套装',
                'description' => '健康材质，导热均匀',
                'price' => 29900,
                'stock' => 100,
                'sales' => 300,
                'is_show' => true,
                'sort' => 2,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
