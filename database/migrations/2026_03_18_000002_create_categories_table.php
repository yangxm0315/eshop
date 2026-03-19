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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // 分类名称
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade'); // 父分类 ID
            $table->string('icon', 255)->nullable(); // 分类图标
            $table->integer('sort')->default(0); // 排序
            $table->boolean('is_show')->default(true); // 是否显示
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
