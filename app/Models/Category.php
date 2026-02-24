<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * All descendants (recursive), loaded eagerly.
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Collect required attributes from this category and all ancestors.
     */
    public function getAllRequiredAttributes(): \Illuminate\Support\Collection
    {
        $attributes = $this->attributes()->where('is_required', true)->get();

        if ($this->parent_id) {
            $attributes = $attributes->merge(
                $this->parent->getAllRequiredAttributes()
            );
        }

        return $attributes;
    }

    /**
     * Collect all attributes (required + optional) from this category and all ancestors.
     */
    public function getAllAttributes(): \Illuminate\Support\Collection
    {
        $attributes = $this->attributes()->get();

        if ($this->parent_id && $this->relationLoaded('parent')) {
            $attributes = $attributes->merge($this->parent->getAllAttributes());
        } elseif ($this->parent_id) {
            $this->load('parent.attributes');
            $attributes = $attributes->merge($this->parent->getAllAttributes());
        }

        return $attributes;
    }

    /**
     * Returns the full breadcrumb path: Food > Vegetables > Leaf
     */
    public function getBreadcrumbAttribute(): string
    {
        $parts = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($parts, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $parts);
    }
}
