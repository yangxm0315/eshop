<?php

namespace Models;

use Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
    ];

    public function isAdmin(): bool
    {
        return $this->attributes['role'] === 1;
    }
}
