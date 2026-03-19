<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_id',
        'icon',
        'sort',
        'is_show',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_show' => 'boolean',
    ];

    /**
     * 父分类
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * 子分类
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * 分类下的商品
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * 获取所有子孙分类
     */
    public function allChildren(): array
    {
        $children = [];
        foreach ($this->children as $child) {
            $children[] = $child->id;
            $children = array_merge($children, $child->allChildren());
        }
        return $children;
    }
}
