<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Highbase Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at top left, #fef3c7 0, #f9fafb 40%, #e5e7eb 100%);
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: .06em;
        }
        .hero-title {
            font-weight: 800;
            letter-spacing: .04em;
        }
        .product-card {
            border: none;
            border-radius: 1.25rem;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
            transition: transform .2s ease, box-shadow .2s ease, translate .2s ease;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 65px rgba(15, 23, 42, 0.25);
        }
        .product-image-placeholder {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }
        .badge-pill {
            border-radius: 999px;
            font-size: .70rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-transparent py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('shop.index') }}">
            <span
                class="rounded-circle bg-dark text-warning d-inline-flex align-items-center justify-content-center me-2"
                style="width: 34px;height: 34px;"
            >
                <i class="bi bi-bag-heart-fill"></i>
            </span>
            Highbase <span class="text-muted ms-1">Market</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#shopNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="shopNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item me-lg-3">
                    <a class="nav-link fw-semibold {{ request()->routeIs('shop.index') ? 'text-dark' : '' }}"
                       href="{{ route('shop.index') }}">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm rounded-pill">
                        <i class="bi bi-speedometer2 me-1"></i> Admin Panel
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container pb-5">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

