@extends('layouts.shop')

@section('title', $product->name . ' · Highbase Shop')

@section('content')
    <div class="row g-4 align-items-start">
        <div class="col-lg-6">
            <div class="product-card mb-3">
                <div class="position-relative" style="height: 50px;">
                    <div class="position-absolute top-0 start-0 p-3">
                        @if($product->category)
                            <span class="badge bg-dark text-warning badge-pill">
                                {{ $product->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 text-end">
                        <div class="badge bg-success text-white badge-pill mb-2">
                            {{ number_format($product->price, 2) }} EGP
                        </div>
                        <div class="badge bg-light text-secondary badge-pill">
                            <i class="bi bi-box-seam me-1"></i> Stock: {{ $product->stock }}
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h1 class="h4 mb-2">{{ $product->name }}</h1>
                    @if($product->category && $product->category->parent)
                        <p class="text-muted small mb-1">
                            <i class="bi bi-diagram-3 me-1"></i>
                            {{ $product->category->parent->name }} › {{ $product->category->name }}
                        </p>
                    @endif
                    <p class="text-secondary mb-0">
                        {{ $product->description }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">
                        <i class="bi bi-sliders2-vertical me-1"></i> Attributes
                    </h2>

                    @php
                        $values = $product->attributeValues;
                    @endphp

                    @if($values->isEmpty())
                        <p class="text-secondary small mb-0">
                            No attribute values have been recorded for this product yet.
                        </p>
                    @else
                        <dl class="row mb-0">
                            @foreach($values as $value)
                                @if($value->categoryAttribute)
                                    <dt class="col-5 col-md-4 text-muted small">
                                        {{ $value->categoryAttribute->name }}
                                    </dt>
                                    <dd class="col-7 col-md-8">
                                        <span class="badge rounded-pill bg-light text-dark">
                                            {{ $value->value }}
                                        </span>
                                    </dd>
                                @endif
                            @endforeach
                        </dl>
                    @endif
                </div>
            </div>

            <a href="{{ route('shop.index') }}" class="btn btn-outline-dark rounded-pill">
                <i class="bi bi-arrow-left"></i> Back to products
            </a>
        </div>
    </div>
@endsection

