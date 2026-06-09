<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f4f7fb;
            font-family: Inter, system-ui, sans-serif;
            letter-spacing: 0;
        }

        .login-panel {
            width: min(420px, calc(100vw - 32px));
            background: #fff;
            border: 1px solid #d9e1eb;
            border-radius: 8px;
            padding: 30px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #fff;
            background: #0f766e;
        }
    </style>
</head>
<body>
<main class="login-panel">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-mark">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="m22 10-10-5-10 5 10 5 10-5Z"/>
                <path d="M6 12v5c3 2 9 2 12 0v-5"/>
            </svg>
        </div>
        <div>
            <h1 class="h4 fw-bold mb-1">Login Admin</h1>
            <div class="text-secondary small">Sistem Prediksi Kelulusan Mahasiswa</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" name="email" type="email" value="{{ old('email', 'admin@example.com') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input class="form-control" id="password" name="password" type="password" value="password" required>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>
        <button class="btn btn-primary w-100" type="submit">Masuk</button>
    </form>
</main>
</body>
</html>
