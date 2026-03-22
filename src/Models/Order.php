<?php

namespace Models;

use Core\Model;

class Order extends Model
{
    protected string $table = 'orders';
    protected array $fillable = [
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

    const STATUS_PENDING = 0;
    const STATUS_TO_SHIP = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELLED = 4;

    public function getStatusTextAttribute(): string
    {
        return match($this->attributes['status']) {
            self::STATUS_PENDING => '待支付',
            self::STATUS_TO_SHIP => '待发货',
            self::STATUS_SHIPPED => '已发货',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消',
            default => '未知',
        };
    }

    public static function generateOrderNo(): string
    {
        return 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }
}
