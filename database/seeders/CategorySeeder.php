<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一级分类
        $electronics = Category::create([
            'name' => '数码电器',
            'parent_id' => null,
            'sort' => 1,
            'is_show' => true,
        ]);

        $clothing = Category::create([
            'name' => '服装鞋帽',
            'parent_id' => null,
            'sort' => 2,
            'is_show' => true,
        ]);

        $home = Category::create([
            'name' => '家居生活',
            'parent_id' => null,
            'sort' => 3,
            'is_show' => true,
        ]);

        $food = Category::create([
            'name' => '食品生鲜',
            'parent_id' => null,
            'sort' => 4,
            'is_show' => true,
        ]);

        // 二级分类 - 数码电器
        Category::create(['name' => '手机通讯', 'parent_id' => $electronics->id, 'sort' => 1]);
        Category::create(['name' => '电脑办公', 'parent_id' => $electronics->id, 'sort' => 2]);
        Category::create(['name' => "摄影摄像", 'parent_id' => $electronics->id, 'sort' => 3]);

        // 二级分类 - 服装鞋帽
        Category::create(['name' => '男装', 'parent_id' => $clothing->id, 'sort' => 1]);
        Category::create(['name' => '女装', 'parent_id' => $clothing->id, 'sort' => 2]);
        Category::create(['name' => '运动鞋服', 'parent_id' => $clothing->id, 'sort' => 3]);

        // 二级分类 - 家居生活
        Category::create(['name' => '家具家私', 'parent_id' => $home->id, 'sort' => 1]);
        Category::create(['name' => '厨具餐具', 'parent_id' => $home->id, 'sort' => 2]);
        Category::create(['name' => '家纺床品', 'parent_id' => $home->id, 'sort' => 3]);
    }
}
