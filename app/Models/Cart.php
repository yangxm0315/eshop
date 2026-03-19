<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * 获取商品总价（分）
     */
    public function getTotalPriceAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }
        return $this->product->price * $this->quantity;
    }

    /**
     * 格式化总价（元）
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '¥' . number_format($this->total_price / 100, 2, '.', '');
    }

    /**
     * 所属用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 购物车商品
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
