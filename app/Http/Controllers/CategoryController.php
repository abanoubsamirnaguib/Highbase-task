<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::orderBy('name')->get();

        return view('categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category): View
    {
        $category->load(['parent', 'children', 'attributes', 'products']);

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $parents = Category::where('id', '!=', $category->id)->orderBy('name')->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
