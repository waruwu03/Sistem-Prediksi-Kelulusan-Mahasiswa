<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Prediksi Kelulusan Mahasiswa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --app-bg: #f4f7fb;
            --app-panel: #ffffff;
            --app-border: #d9e1eb;
            --app-text: #15202b;
            --app-muted: #667085;
            --app-primary: #0f766e;
            --app-primary-strong: #115e59;
            --app-accent: #2563eb;
            --app-warning: #d97706;
        }

        [data-bs-theme="dark"] {
            --app-bg: #101418;
            --app-panel: #171d22;
            --app-border: #2a333c;
            --app-text: #edf2f7;
            --app-muted: #aab4bf;
            --app-primary: #2dd4bf;
            --app-primary-strong: #5eead4;
            --app-accent: #60a5fa;
            --app-warning: #fbbf24;
        }

        body {
            background: var(--app-bg);
            color: var(--app-text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr;
        }

        .sidebar {
            background: var(--app-panel);
            border-right: 1px solid var(--app-border);
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            color: #fff;
            background: var(--app-primary);
            border-radius: 8px;
        }

        .nav-link {
            color: var(--app-muted);
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
        }

        .nav-link.active,
        .nav-link:hover {
            color: var(--app-primary-strong);
            background: rgba(15, 118, 110, .11);
        }

        .content-wrap {
            padding: 28px;
        }

        .topbar,
        .panel,
        .metric-card,
        .table-panel {
            background: var(--app-panel);
            border: 1px solid var(--app-border);
            border-radius: 8px;
        }

        .topbar {
            padding: 18px 22px;
            margin-bottom: 22px;
        }

        .metric-card {
            padding: 20px;
            min-height: 138px;
        }

        .metric-value {
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 750;
        }

        .panel,
        .table-panel {
            padding: 22px;
        }

        .muted {
            color: var(--app-muted);
        }

        .btn-primary {
            --bs-btn-bg: var(--app-primary);
            --bs-btn-border-color: var(--app-primary);
            --bs-btn-hover-bg: var(--app-primary-strong);
            --bs-btn-hover-border-color: var(--app-primary-strong);
        }

        .form-control,
        .form-select {
            border-color: var(--app-border);
            border-radius: 8px;
            min-height: 42px;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            display: inline-grid;
            place-items: center;
            padding: 0;
        }

        .status-pill {
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 700;
            font-size: .8rem;
        }

        .chart-box {
            min-height: 280px;
        }

        @media (max-width: 991px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--app-border);
            }

            .content-wrap {
                padding: 18px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-mark"><i data-lucide="graduation-cap"></i></div>
            <div>
                <div class="fw-bold">SPKM</div>
                <div class="small muted">Dashboard Akademik</div>
            </div>
        </div>

        <nav class="nav flex-column gap-1">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
            </a>
            <a class="nav-link {{ request()->routeIs('predictions.*') ? 'active' : '' }}" href="{{ route('predictions.index') }}">
                <i data-lucide="brain-circuit"></i><span>Prediksi</span>
            </a>
            <a class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}" href="{{ route('mahasiswa.index') }}">
                <i data-lucide="users"></i><span>Data Mahasiswa</span>
            </a>
            <a class="nav-link {{ request()->routeIs('evaluation.*') ? 'active' : '' }}" href="{{ route('evaluation.index') }}">
                <i data-lucide="chart-no-axes-combined"></i><span>Evaluasi Model</span>
            </a>
        </nav>

        <div class="mt-4 pt-4 border-top">
            <button class="btn btn-outline-secondary icon-btn me-2" id="themeToggle" type="button" title="Dark mode">
                <i data-lucide="moon"></i>
            </button>
            <form class="d-inline" action="{{ route('logout') }}" method="post">
                @csrf
                <button class="btn btn-outline-danger icon-btn" type="submit" title="Logout">
                    <i data-lucide="log-out"></i>
                </button>
            </form>
        </div>
    </aside>

    <main class="content-wrap">
        <div class="topbar d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Sistem Prediksi Kelulusan Mahasiswa</h1>
                <div class="muted">Implementasi Metode Klasifikasi Machine Learning</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge text-bg-light border">{{ auth()->user()->name ?? 'Admin' }}</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    const storedTheme = localStorage.getItem('spkm-theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', storedTheme);
    document.getElementById('themeToggle')?.addEventListener('click', () => {
        const nextTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', nextTheme);
        localStorage.setItem('spkm-theme', nextTheme);
    });
    lucide.createIcons();
</script>
@stack('scripts')
</body>
</html>
