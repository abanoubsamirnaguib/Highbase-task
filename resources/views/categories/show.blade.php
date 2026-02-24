@extends('layouts.app')

@section('title', $category->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
        <li class="breadcrumb-item active">{{ $category->name }}</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $category->name }}</h5>
                    @if($category->parent)
                        <small class="text-muted">{{ $category->breadcrumb }}</small>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.attributes.index', $category) }}"
                       class="btn btn-sm btn-outline-info">
                        <i class="bi bi-list-check"></i> Attributes
                    </a>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                          onsubmit="return confirm('Delete this category?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $category->description ?? 'No description.' }}</p>
            </div>
        </div>

        {{-- Sub-categories --}}
        @if($category->children->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-diagram-3"></i> Sub-categories</h6>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($category->children as $child)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.categories.show', $child) }}" class="text-decoration-none fw-semibold">
                        {{ $child->name }}
                    </a>
                    <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-secondary">
                        View
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Products --}}
        @if($category->products->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header bg-white d-flex justify-content-between">
                <h6 class="mb-0"><i class="bi bi-box-seam"></i> Products in this category</h6>
                <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-plus"></i> Add Product
                </a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($category->products->take(10) as $product)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.products.show', $product) }}" class="text-decoration-none">
                        {{ $product->name }}
                    </a>
                    <span class="text-muted">L.E {{ number_format($product->price, 2) }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Attributes --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list-check"></i> Attributes</h6>
                <a href="{{ route('admin.categories.attributes.create', $category) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus"></i>
                </a>
            </div>
            @if($category->attributes->isEmpty())
                <div class="card-body text-muted small">No attributes defined.</div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($category->attributes as $attr)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold">{{ $attr->name }}</span>
                                <div class="small text-muted">Type: {{ $attr->type }}</div>
                            </div>
                            @if($attr->is_required)
                                <span class="badge bg-danger">Required</span>
                            @else
                                <span class="badge bg-secondary">Optional</span>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
