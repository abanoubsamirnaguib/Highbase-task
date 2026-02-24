<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id ?? $this->route('category');

        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', "unique:categories,slug,{$categoryId}"],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }
}
