<?php

namespace Models;

use Core\Model;

class Cart extends Model
{
    protected string $table = 'cart';
    protected array $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];
}
