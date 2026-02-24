<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function shop(): View
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        return view('shop.index', compact('products'));
    }

    public function shopShow(Product $product): View
    {
        $product->load(['category.parent', 'attributeValues.categoryAttribute']);

        return view('shop.show', compact('product'));
    }

    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::with('parent')->orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->safe()->except('attributes'));

        $this->syncAttributes($product, $request->input('attributes', []));

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category.parent', 'attributeValues.categoryAttribute']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'attributeValues']);
        $categories = Category::with('parent')->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->safe()->except('attributes'));

        // When category changes, remove attribute values that no longer belong
        // to the product's (possibly new) category hierarchy.
        $product->load('category');
        if ($product->category) {
            $validAttributeIds = $product->category
                ->getAllAttributes()
                ->pluck('id')
                ->all();

            if (empty($validAttributeIds)) {
                ProductAttributeValue::where('product_id', $product->id)->delete();
            } else {
                ProductAttributeValue::where('product_id', $product->id)
                    ->whereNotIn('category_attribute_id', $validAttributeIds)
                    ->delete();
            }
        } else {
            // No category: drop all attribute values
            ProductAttributeValue::where('product_id', $product->id)->delete();
        }

        $this->syncAttributes($product, $request->input('attributes', []));

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Sync attribute values for a product.
     *
     * @param array<int|string, string> $attributes  keyed by category_attribute_id
     */
    private function syncAttributes(Product $product, array $attributes): void
    {
        foreach ($attributes as $attributeId => $value) {
            if ($value === null || trim((string) $value) === '') {
                ProductAttributeValue::where('product_id', $product->id)
                    ->where('category_attribute_id', $attributeId)
                    ->delete();
                continue;
            }

            ProductAttributeValue::updateOrCreate(
                [
                    'product_id'           => $product->id,
                    'category_attribute_id' => $attributeId,
                ],
                ['value' => $value]
            );
        }
    }
}
