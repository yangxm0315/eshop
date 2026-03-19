<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'price',
        'quantity',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
        'total_price' => 'integer',
    ];

    /**
     * 格式化单价
     */
    public function getFormattedPriceAttribute(): string
    {
        return '¥' . number_format($this->price / 100, 2);
    }

    /**
     * 格式化总价
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '¥' . number_format($this->total_price / 100, 2);
    }

    /**
     * 所属订单
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 商品
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
