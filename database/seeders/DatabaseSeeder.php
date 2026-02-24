<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@highbase.com'],
            [
                'name'     => 'Bahrian Admin',
                'password' => '123456',
            ]
        );

        // ---- Hierarchy: Food > Vegetables > Leaf ----
        $food = Category::create(['name' => 'Food', 'slug' => 'food', 'description' => 'Edible items']);
        $vegetables = Category::create(['name' => 'Vegetables', 'slug' => 'vegetables', 'parent_id' => $food->id]);
        $leaf = Category::create(['name' => 'Leaf', 'slug' => 'leaf', 'parent_id' => $vegetables->id]);

        // Food-level required attribute (inherited by sub-categories)
        $expiryAttr = CategoryAttribute::create([
            'category_id' => $food->id,
            'name'        => 'Expiry Date',
            'type'        => 'text',
            'is_required' => true,
        ]);

        CategoryAttribute::create([
            'category_id' => $food->id,
            'name'        => 'Organic',
            'type'        => 'boolean',
            'is_required' => false,
        ]);

        // Vegetables-level attribute
        $weightAttr = CategoryAttribute::create([
            'category_id' => $vegetables->id,
            'name'        => 'Weight (kg)',
            'type'        => 'number',
            'is_required' => true,
        ]);

        // ---- Hierarchy: Clothes ----
        $clothes = Category::create(['name' => 'Clothes', 'slug' => 'clothes', 'description' => 'Apparel']);
        $shirts = Category::create(['name' => 'Shirts', 'slug' => 'shirts', 'parent_id' => $clothes->id]);

        $sizeAttr = CategoryAttribute::create([
            'category_id' => $clothes->id,
            'name'        => 'Size',
            'type'        => 'select',
            'is_required' => true,
            'options'     => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        ]);

        $colorAttr = CategoryAttribute::create([
            'category_id' => $clothes->id,
            'name'        => 'Color',
            'type'        => 'text',
            'is_required' => true,
        ]);

        // ---- Sample Products ----
        $spinach = Product::create([
            'category_id' => $leaf->id,
            'name'        => 'Fresh Spinach',
            'slug'        => 'fresh-spinach',
            'description' => 'Freshly harvested spinach leaves.',
            'price'       => 1.99,
            'stock'       => 100,
            'status'      => 'active',
        ]);

        $spinach->attributeValues()->createMany([
            ['category_attribute_id' => $expiryAttr->id,  'value' => '2025-03-15'],
            ['category_attribute_id' => $weightAttr->id,  'value' => '0.5'],
        ]);

        $tshirt = Product::create([
            'category_id' => $shirts->id,
            'name'        => 'Classic Cotton T-Shirt',
            'slug'        => 'classic-cotton-tshirt',
            'description' => 'Comfortable everyday T-shirt.',
            'price'       => 24.99,
            'stock'       => 50,
            'status'      => 'active',
        ]);

        $tshirt->attributeValues()->createMany([
            ['category_attribute_id' => $sizeAttr->id,  'value' => 'M'],
            ['category_attribute_id' => $colorAttr->id, 'value' => 'Navy Blue'],
        ]);
    }
}
