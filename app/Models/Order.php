<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * 订单状态
     */
    const STATUS_PENDING = 0;      // 待支付
    const STATUS_TO_SHIP = 1;      // 待发货
    const STATUS_SHIPPED = 2;      // 已发货
    const STATUS_COMPLETED = 3;    // 已完成
    const STATUS_CANCELLED = 4;    // 已取消

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_no',
        'user_id',
        'address_id',
        'total_amount',
        'pay_amount',
        'status',
        'remark',
        'paid_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * 格式化总金额
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return '¥' . number_format($this->total_amount / 100, 2);
    }

    /**
     * 格式化实付金额
     */
    public function getFormattedPayAmountAttribute(): string
    {
        return '¥' . number_format($this->pay_amount / 100, 2);
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => '待支付',
            self::STATUS_TO_SHIP => '待发货',
            self::STATUS_SHIPPED => '已发货',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消',
            default => '未知',
        };
    }

    /**
     * 所属用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 收货地址
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * 订单商品
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 生成订单号
     */
    public static function generateOrderNo(): string
    {
        return 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    /**
     * 是否可以取消
     */
    public function canCancel(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * 取消订单
     */
    public function cancel(string $reason): bool
    {
        if (!$this->canCancel()) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->cancel_reason = $reason;
        $this->cancelled_at = now();
        return $this->save();
    }
}
