<?php

namespace Models;

use Core\Model;

class Category extends Model
{
    protected string $table = 'categories';
    protected array $fillable = [
        'name',
        'description',
        'sort',
    ];
}
