<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'status'       => ['required', Rule::in(['active', 'inactive', 'draft'])],
            'attributes'   => ['nullable', 'array'],
            'attributes.*' => ['nullable', 'string', 'max:1000'],
        ];

        return $rules;
    }

    /**
     * Additional validation after base rules pass: check required attributes.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $categoryId = $this->input('category_id');

            if (! $categoryId) {
                return;
            }

            $category = Category::with('parent.attributes', 'attributes')->find($categoryId);

            if (! $category) {
                return;
            }

            $requiredAttributes = $category->getAllRequiredAttributes();
            $submittedAttributes = $this->input('attributes', []);

            foreach ($requiredAttributes as $attribute) {
                $value = $submittedAttributes[$attribute->id] ?? null;

                if ($value === null || trim((string) $value) === '') {
                    $validator->errors()->add(
                        "attributes.{$attribute->id}",
                        "The \"{$attribute->name}\" attribute is required for this category."
                    );
                }
            }
        });
    }
}
