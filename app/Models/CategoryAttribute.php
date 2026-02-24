<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryAttribute extends Model
{
    protected $fillable = ['category_id', 'name', 'type', 'is_required', 'options'];

    protected $casts = [
        'is_required' => 'boolean',
        'options'     => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
