<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\User::factory()->create([
            'email' => 'admin@highbase.com',
        ]);

        $this->actingAs(\App\Models\User::first());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeCategory(array $attrs = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ], $attrs));
    }

    private function productPayload(array $overrides = []): array
    {
        return array_merge([
            'name'        => 'Test Product',
            'price'       => '19.99',
            'stock'       => 10,
            'status'      => 'active',
            'description' => 'A great product.',
        ], $overrides);
    }

    // -------------------------------------------------------------------------
    // 1. Successful product creation (no required attributes)
    // -------------------------------------------------------------------------

    public function test_can_create_product_successfully_without_attributes(): void
    {
        $category = $this->makeCategory();

        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $category->id,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name'        => 'Test Product',
            'category_id' => $category->id,
            'price'       => '19.99',
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. Successful product creation WITH required attributes provided
    // -------------------------------------------------------------------------

    public function test_can_create_product_with_required_attributes(): void
    {
        $category = $this->makeCategory(['name' => 'Clothes', 'slug' => 'clothes']);

        $sizeAttr = CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Size',
            'type'        => 'text',
            'is_required' => true,
        ]);

        $colorAttr = CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Color',
            'type'        => 'text',
            'is_required' => true,
        ]);

        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $category->id,
            'attributes'  => [
                $sizeAttr->id  => 'Medium',
                $colorAttr->id => 'Red',
            ],
        ]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $product = Product::where('name', 'Test Product')->firstOrFail();

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id'            => $product->id,
            'category_attribute_id' => $sizeAttr->id,
            'value'                 => 'Medium',
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id'            => $product->id,
            'category_attribute_id' => $colorAttr->id,
            'value'                 => 'Red',
        ]);
    }

    // -------------------------------------------------------------------------
    // 3. Failure: basic form validation errors (missing required fields)
    // -------------------------------------------------------------------------

    public function test_cannot_create_product_with_validation_errors(): void
    {
        $category = $this->makeCategory();

        // Missing name, invalid price, invalid status
        $response = $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name'        => '',
            'price'       => '-5',
            'stock'       => 'not-a-number',
            'status'      => 'unknown-status',
        ]);

        $response->assertSessionHasErrors(['name', 'price', 'stock', 'status']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_cannot_create_product_without_category(): void
    {
        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => null,
        ]));

        $response->assertSessionHasErrors(['category_id']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_cannot_create_product_with_nonexistent_category(): void
    {
        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => 9999,
        ]));

        $response->assertSessionHasErrors(['category_id']);
        $this->assertDatabaseCount('products', 0);
    }

    // -------------------------------------------------------------------------
    // 4. Failure: missing one or more required attributes
    // -------------------------------------------------------------------------

    public function test_cannot_create_product_when_required_attributes_are_missing(): void
    {
        $category = $this->makeCategory(['name' => 'Clothes', 'slug' => 'clothes']);

        CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Size',
            'type'        => 'text',
            'is_required' => true,
        ]);

        CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Color',
            'type'        => 'text',
            'is_required' => true,
        ]);

        // Submit with NO attributes at all
        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $category->id,
        ]));

        $response->assertSessionHasErrors();
        $errors = session('errors')->toArray();

        // Both required attributes should produce errors
        $this->assertNotEmpty(
            array_filter(array_keys($errors), fn ($k) => str_starts_with($k, 'attributes.'))
        );

        $this->assertDatabaseCount('products', 0);
    }

    public function test_cannot_create_product_when_only_some_required_attributes_are_provided(): void
    {
        $category = $this->makeCategory(['name' => 'Clothes', 'slug' => 'clothes2']);

        $sizeAttr = CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Size',
            'type'        => 'text',
            'is_required' => true,
        ]);

        $colorAttr = CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Color',
            'type'        => 'text',
            'is_required' => true,
        ]);

        // Provide Size but not Color
        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $category->id,
            'attributes'  => [
                $sizeAttr->id => 'Large',
                // $colorAttr->id intentionally missing
            ],
        ]));

        $response->assertSessionHasErrors(["attributes.{$colorAttr->id}"]);
        $this->assertDatabaseCount('products', 0);
    }

    // -------------------------------------------------------------------------
    // 5. Required attributes are inherited from parent categories
    // -------------------------------------------------------------------------

    public function test_required_attributes_are_inherited_from_parent_category(): void
    {
        $parent = $this->makeCategory(['name' => 'Food', 'slug' => 'food']);

        $expiryAttr = CategoryAttribute::create([
            'category_id' => $parent->id,
            'name'        => 'Expiry Date',
            'type'        => 'text',
            'is_required' => true,
        ]);

        $child = Category::create([
            'name'      => 'Vegetables',
            'slug'      => 'vegetables',
            'parent_id' => $parent->id,
        ]);

        // Try to create a product in Vegetables without the parent's required attribute
        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $child->id,
        ]));

        $response->assertSessionHasErrors(["attributes.{$expiryAttr->id}"]);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_can_create_product_in_child_category_when_all_inherited_attributes_provided(): void
    {
        $parent = $this->makeCategory(['name' => 'Food', 'slug' => 'food2']);

        $expiryAttr = CategoryAttribute::create([
            'category_id' => $parent->id,
            'name'        => 'Expiry Date',
            'type'        => 'text',
            'is_required' => true,
        ]);

        $child = Category::create([
            'name'      => 'Vegetables',
            'slug'      => 'vegetables2',
            'parent_id' => $parent->id,
        ]);

        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $child->id,
            'attributes'  => [
                $expiryAttr->id => '2025-12-31',
            ],
        ]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('products', 1);
    }

    // -------------------------------------------------------------------------
    // 6. Optional attributes do NOT block creation
    // -------------------------------------------------------------------------

    public function test_optional_attributes_do_not_block_product_creation(): void
    {
        $category = $this->makeCategory();

        CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'Brand',
            'type'        => 'text',
            'is_required' => false,
        ]);

        // Do NOT supply Brand — should still succeed
        $response = $this->post(route('admin.products.store'), $this->productPayload([
            'category_id' => $category->id,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('products', 1);
    }
}
