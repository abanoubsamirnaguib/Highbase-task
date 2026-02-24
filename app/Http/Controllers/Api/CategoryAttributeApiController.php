<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryAttributeApiController extends Controller
{
    /**
     * Return all attributes (including ancestor attributes) for a given category.
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('parent.attributes', 'attributes');

        $attributes = $category->getAllAttributes()->map(fn ($attr) => [
            'id'          => $attr->id,
            'name'        => $attr->name,
            'type'        => $attr->type,
            'is_required' => $attr->is_required,
            'options'     => $attr->options,
        ]);

        return response()->json(['attributes' => $attributes->values()]);
    }
}
