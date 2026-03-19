<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // 所属分类
            $table->string('name', 200); // 商品名称
            $table->string('description', 500)->nullable(); // 商品描述
            $table->integer('price'); // 价格（分）
            $table->integer('stock')->default(0); // 库存数量
            $table->integer('sales')->default(0); // 销量
            $table->string('main_image', 255)->nullable(); // 主图
            $table->text('content')->nullable(); // 商品详情（HTML）
            $table->boolean('is_show')->default(true); // 是否上架
            $table->integer('sort')->default(0); // 排序
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_show');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
