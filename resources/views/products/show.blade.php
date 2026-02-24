@extends('layouts.app')

@section('title', $product->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $product->name }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('Delete this product?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $product->description ?? 'No description provided.' }}</p>

                <div class="row g-3 mt-1">
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3 text-center">
                            <div class="small text-muted">Price</div>
                            <div class="fs-4 fw-bold text-success">L.E {{ number_format($product->price, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3 text-center">
                            <div class="small text-muted">Stock</div>
                            <div class="fs-4 fw-bold">{{ $product->stock }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3 text-center">
                            <div class="small text-muted">Status</div>
                            @php $statusColors = ['active'=>'success','inactive'=>'secondary','draft'=>'warning']; @endphp
                            <span class="badge bg-{{ $statusColors[$product->status] ?? 'secondary' }} fs-6">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($product->attributeValues->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-list-check"></i> Product Attributes</h6>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($product->attributeValues as $val)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-muted">{{ $val->categoryAttribute->name }}</span>
                    <span>{{ $val->value }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-tag"></i> Category</h6>
            </div>
            <div class="card-body">
                @if($product->category)
                    <p class="mb-1">
                        <span class="badge bg-secondary fs-6">{{ $product->category->name }}</span>
                    </p>
                    @if($product->category->parent)
                        <div class="small text-muted">
                            <i class="bi bi-arrow-up"></i> {{ $product->category->breadcrumb }}
                        </div>
                    @endif
                    <a href="{{ route('admin.categories.show', $product->category) }}" class="btn btn-sm btn-outline-secondary mt-2">
                        View Category
                    </a>
                @else
                    <span class="text-muted">No category</span>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Metadata</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted small">Slug</span>
                    <code class="small">{{ $product->slug }}</code>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted small">Created</span>
                    <span class="small">{{ $product->created_at->format('M d, Y') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted small">Updated</span>
                    <span class="small">{{ $product->updated_at->format('M d, Y') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
