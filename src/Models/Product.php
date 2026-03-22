<?php

namespace Models;

use Core\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'sales',
        'main_image',
        'content',
        'is_show',
        'sort',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return '¥' . number_format($this->attributes['price'] / 100, 2);
    }

    public function decreaseStock(int $quantity): bool
    {
        if ($this->attributes['stock'] < $quantity) {
            return false;
        }
        $this->attributes['stock'] -= $quantity;
        return $this->save();
    }
}
