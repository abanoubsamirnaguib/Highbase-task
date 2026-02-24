<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryAttributeController extends Controller
{
    public function index(Category $category): View
    {
        $category->load('attributes');

        return view('admin.attributes.index', compact('category'));
    }

    public function create(Category $category): View
    {
        return view('admin.attributes.create', compact('category'));
    }

    public function store(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:text,number,select,boolean'],
            'is_required' => ['boolean'],
            'options'     => ['nullable', 'string'],
        ]);

        $options = null;
        if ($validated['type'] === 'select' && ! empty($validated['options'])) {
            $options = array_filter(
                array_map('trim', explode(',', $validated['options']))
            );
        }

        $category->attributes()->create([
            'name'        => $validated['name'],
            'type'        => $validated['type'],
            'is_required' => $request->boolean('is_required'),
            'options'     => $options ? array_values($options) : null,
        ]);

        return redirect()->route('admin.categories.attributes.index', $category)
            ->with('success', "Attribute \"{$validated['name']}\" added to {$category->name}.");
    }

    public function edit(Category $category, CategoryAttribute $attribute): View
    {
        return view('admin.attributes.edit', compact('category', 'attribute'));
    }

    public function update(Request $request, Category $category, CategoryAttribute $attribute): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:text,number,select,boolean'],
            'is_required' => ['boolean'],
            'options'     => ['nullable', 'string'],
        ]);

        $options = null;
        if ($validated['type'] === 'select' && ! empty($validated['options'])) {
            $options = array_filter(
                array_map('trim', explode(',', $validated['options']))
            );
        }

        $attribute->update([
            'name'        => $validated['name'],
            'type'        => $validated['type'],
            'is_required' => $request->boolean('is_required'),
            'options'     => $options ? array_values($options) : null,
        ]);

        return redirect()->route('admin.categories.attributes.index', $category)
            ->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Category $category, CategoryAttribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('admin.categories.attributes.index', $category)
            ->with('success', 'Attribute deleted successfully.');
    }
}
