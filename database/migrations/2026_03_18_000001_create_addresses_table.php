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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 50); // 收货人姓名
            $table->string('phone', 20); // 联系电话
            $table->string('province', 50); // 省
            $table->string('city', 50); // 市
            $table->string('district', 50); // 区
            $table->string('detail', 200); // 详细地址
            $table->boolean('is_default')->default(false); // 是否默认地址
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
