@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-tags"></i> Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Add Category
    </a>
</div>

@if($categories->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-tags display-4 d-block mb-3"></i>
        No categories yet. <a href="{{ route('admin.categories.create') }}">Add the first one.</a>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Products</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td class="text-muted small">{{ $category->id }}</td>
                        <td>
                            <a href="{{ route('admin.categories.show', $category) }}" class="fw-semibold text-decoration-none">
                                {{ $category->name }}
                            </a>
                            @if($category->description)
                                <div class="small text-muted">{{ Str::limit($category->description, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($category->parent)
                                <a href="{{ route('admin.categories.show', $category->parent) }}"
                                   class="badge bg-light text-dark text-decoration-none border">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="text-muted small">Root</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $category->products_count }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.attributes.index', $category) }}"
                               class="btn btn-sm btn-outline-info" title="Manage Attributes">
                                <i class="bi bi-list-check"></i>
                            </a>
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this category? Products in this category cannot be deleted.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
