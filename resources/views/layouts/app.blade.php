<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Highbase Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: .5px; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .breadcrumb-item + .breadcrumb-item::before { content: ">"; }
        .badge-required { font-size: .7rem; vertical-align: middle; }
        .attr-input-group { background: #f0f4ff; border-radius: 8px; padding: 12px 16px; margin-bottom: 10px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.products.index') }}">
            <i class="bi bi-shop"></i> Highbase Shop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                       href="{{ route('admin.products.index') }}">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                       href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Category Attributes
                    </a>
                    <ul class="dropdown-menu">
                        <!-- <li><h6 class="dropdown-header">Category Attributes</h6></li> -->
                        @foreach(\App\Models\Category::orderBy('name')->get() as $cat)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.categories.attributes.index', $cat) }}">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('shop.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-bag-heart"></i> View Shop
                </a>
                @auth
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
