@extends('layouts.shop')

@section('title', 'Highbase Shop')

@section('content')
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <h1 class="display-5 hero-title mb-3">
                Discover products<br>
                crafted for everyday life.
            </h1>

            <div class="d-flex flex-wrap gap-2">
                <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">
                    <i class="bi bi-boxes me-1"></i> {{ $products->total() }} products
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="product-card h-100 d-flex flex-column">
                    <div class="position-relative" style="height: 50px;">
                        <div class="position-absolute top-0 start-0 p-2">
                            @if($product->category)
                                <span class="badge bg-dark text-warning badge-pill">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-2">
                            <span class="badge bg-success text-white badge-pill">
                                {{ number_format($product->price, 2) }} EGP
                            </span>
                        </div>
                    </div>

                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h5 class="mb-1 text-truncate" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h5>
                        <p class="text-muted small mb-2 text-truncate" title="{{ $product->description }}">
                            {{ $product->description }}
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">
                                <i class="bi bi-box-seam me-1"></i> Stock: {{ $product->stock }}
                            </span>
                            <a href="{{ route('shop.products.show', $product) }}" class="btn btn-outline-dark btn-sm rounded-pill">
                                View details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-center py-5">
                    <h5 class="mb-2">No products yet</h5>
                    <p class="mb-0 text-muted">
                        Once products are created in the admin panel, they will appear here for visitors.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
@endsection

