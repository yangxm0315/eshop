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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 32)->unique(); // 订单号
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->constrained()->onDelete('cascade'); // 收货地址
            $table->integer('total_amount'); // 订单总金额（分）
            $table->integer('pay_amount')->default(0); // 实付金额（分）
            $table->tinyInteger('status')->default(0); // 订单状态：0-待支付，1-待发货，2-已发货，3-已完成，4-已取消
            $table->string('remark', 500)->nullable(); // 订单备注
            $table->timestamp('paid_at')->nullable(); // 支付时间
            $table->timestamp('shipped_at')->nullable(); // 发货时间
            $table->timestamp('completed_at')->nullable(); // 完成时间
            $table->timestamp('cancelled_at')->nullable(); // 取消时间
            $table->string('cancel_reason', 200)->nullable(); // 取消原因
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
