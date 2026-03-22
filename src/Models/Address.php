<?php

namespace Models;

use Core\Model;

class Address extends Model
{
    protected string $table = 'addresses';
    protected array $fillable = [
        'user_id',
        'name',
        'phone',
        'province',
        'city',
        'district',
        'detail',
        'is_default',
    ];
}
