@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-box-seam"></i> Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Add Product
    </a>
</div>

@if($products->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox display-4 d-block mb-3"></i>
        No products yet. <a href="{{ route('admin.products.create') }}">Add the first one.</a>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td class="text-muted small">{{ $product->id }}</td>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}" class="fw-semibold text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-secondary">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>L.E {{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @php
                                $statusColors = ['active' => 'success', 'inactive' => 'secondary', 'draft' => 'warning'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$product->status] ?? 'secondary' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this product?')">
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

    <div class="mt-3">{{ $products->links() }}</div>
@endif
@endsection
