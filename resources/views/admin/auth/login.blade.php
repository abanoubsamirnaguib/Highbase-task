<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · Highbase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #0f172a, #020617 55%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 1.5rem;
            background: radial-gradient(circle at top right, rgba(251,191,36,.12), transparent 55%),
                        #020617;
            box-shadow: 0 20px 80px rgba(15,23,42,.75);
            border: 1px solid rgba(148,163,184,.35);
        }
        .form-control {
            border-radius: .9rem;
            border-color: rgba(148,163,184,.5);
            background-color: rgba(15,23,42,.85);
            color: #e5e7eb;
        }
        .form-control:focus {
            border-color: #facc15;
            box-shadow: 0 0 0 .15rem rgba(250,204,21,.35);
        }
        .btn-amber {
            background: linear-gradient(135deg, #facc15, #f97316);
            border: none;
            color: #0f172a;
            font-weight: 600;
        }
        .btn-amber:hover {
            filter: brightness(.95);
        }
        .text-amber {
            color: #facc15;
        }
        .badge-pill {
            border-radius: 999px;
            font-size: .65rem;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="login-card p-4 p-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <span
                class="rounded-circle bg-amber d-inline-flex align-items-center justify-content-center me-2"
                style="width: 36px;height: 36px;background: radial-gradient(circle at 30% 0, #facc15, #f97316);color:#0f172a;"
            >
                <i class="bi bi-shield-lock-fill"></i>
            </span>
            <div>
                <div class="text-amber fw-semibold small text-uppercase">Highbase</div>
                <div class="text-secondary small">Admin access</div>
            </div>
        </div>
        <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-box-arrow-left me-1"></i> Back to shop
        </a>
    </div>

    <h1 class="h4 text-white fw-semibold mb-2">
        Sign in to dashboard
    </h1>
    <p class="text-secondary small mb-4">
        
    </p>

    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}" class="mb-3">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label text-light small">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', 'admin@highbase.com') }}"
                required
                autofocus
                class="form-control form-control-sm @error('email') is-invalid @enderror"
                placeholder="admin@highbase.com"
            >
        </div>

        <div class="mb-2">
            <label for="password" class="form-label text-light small d-flex justify-content-between">
                <span>Password</span>
                <span class="text-secondary">Default: <code class="text-amber">123456</code></span>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="form-control form-control-sm @error('password') is-invalid @enderror"
                placeholder="••••••"
            >
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check form-check-sm">
                <input
                    class="form-check-input"
                    type="checkbox"
                    value="1"
                    id="remember"
                    name="remember"
                >
                <label class="form-check-label text-secondary small" for="remember">
                    Remember me
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-amber w-100 rounded-pill">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

