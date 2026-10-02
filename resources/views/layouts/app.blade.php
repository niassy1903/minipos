<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mini POS') · {{ config('app.name', 'Mini POS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --ink: #17202d; --muted: #6f7b8a; --navy: #132238; --mint: #2fb58a; --cream: #f6f8f7; --line: #e7eceb; }
        body { background: var(--cream); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, .brand { font-family: 'Space Grotesk', sans-serif; }
        .app-shell { min-height: 100vh; }
        .sidebar { background: var(--navy); color: #dbe6eb; min-height: 100vh; width: 258px; }
        .brand { color: #fff; font-weight: 700; font-size: 1.3rem; letter-spacing: -.04em; }
        .brand-mark { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 10px; background: var(--mint); color: #092c25; margin-right: .6rem; }
        .sidebar .nav-link { color: #9dafbd; border-radius: 11px; margin: .15rem 0; padding: .72rem .85rem; font-weight: 500; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-link i { width: 22px; }
        .content { width: calc(100% - 258px); }
        .topbar { background: rgba(255,255,255,.82); border-bottom: 1px solid var(--line); backdrop-filter: blur(10px); }
        .page-title { letter-spacing: -.045em; }
        .card { border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 8px 28px rgba(23,32,45,.04); }
        .stat-card { border: 0; background: #fff; }
        .stat-icon { width: 43px; height: 43px; border-radius: 13px; display: grid; place-items: center; color: #fff; background: var(--navy); }
        .stat-icon.mint { background: #d9f5eb; color: #137d5d; }
        .stat-icon.orange { background: #fff0d9; color: #b36805; }
        .stat-icon.blue { background: #e4efff; color: #2865b5; }
        .text-muted { color: var(--muted) !important; }
        .btn-primary { --bs-btn-bg: var(--navy); --bs-btn-border-color: var(--navy); --bs-btn-hover-bg: #1d3552; --bs-btn-hover-border-color: #1d3552; }
        .btn-success { --bs-btn-bg: var(--mint); --bs-btn-border-color: var(--mint); --bs-btn-hover-bg: #249774; --bs-btn-hover-border-color: #249774; }
        .badge-soft { background: #e5f7ef; color: #157452; }
        .badge-low { background: #fff0d9; color: #aa6508; }
        .table > :not(caption) > * > * { padding: 1rem .75rem; border-bottom-color: var(--line); }
        .table thead th { color: var(--muted); font-size: .73rem; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
        .form-control, .form-select { border-color: #dce5e3; border-radius: 10px; padding: .68rem .85rem; }
        .form-control:focus, .form-select:focus { border-color: var(--mint); box-shadow: 0 0 0 .2rem rgba(47,181,138,.14); }
        .auth-page { min-height: 100vh; background: radial-gradient(circle at 15% 10%, #d8f4e9 0, transparent 28%), var(--navy); }
        .auth-card { max-width: 460px; border: 0; }
        .empty-state { border: 1px dashed #cddbd8; background: rgba(255,255,255,.6); }
        @media (max-width: 991.98px) {
            .sidebar { width: 76px; }
            .sidebar .brand span, .sidebar .nav-link span, .sidebar .small-label { display: none; }
            .sidebar .nav-link { text-align: center; }
            .sidebar .nav-link i { width: auto; }
            .content { width: calc(100% - 76px); }
        }
        @media (max-width: 575.98px) {
            .sidebar { display: none; }
            .content { width: 100%; }
        }
    </style>
</head>
<body>
@auth
<div class="d-flex app-shell">
    <aside class="sidebar flex-shrink-0 p-3 d-flex flex-column">
        <a href="{{ route('dashboard') }}" class="brand text-decoration-none mb-4">
            <span class="brand-mark"><i class="bi bi-grid-1x2-fill"></i></span><span>Mini POS</span>
        </a>
        @php($currentShop = request()->route('shop'))
        @if($currentShop instanceof \App\Models\Shop)
            <div class="small-label text-uppercase small fw-bold text-white-50 mb-2">Boutique active</div>
            <div class="rounded-3 p-2 mb-3" style="background:rgba(255,255,255,.08)">
                <div class="fw-semibold text-white text-truncate">{{ $currentShop->name }}</div>
                <a href="{{ route('shops.index') }}" class="small text-white-50 text-decoration-none">Changer de boutique</a>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('shops.dashboard') ? 'active' : '' }}" href="{{ route('shops.dashboard', $currentShop) }}"><i class="bi bi-speedometer2"></i><span>Vue d'ensemble</span></a>
                <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index', $currentShop) }}"><i class="bi bi-receipt"></i><span>Ventes</span></a>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index', $currentShop) }}"><i class="bi bi-box-seam"></i><span>Produits</span></a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index', $currentShop) }}"><i class="bi bi-people"></i><span>Clients</span></a>
            </nav>
        @endif
        <div class="mt-auto">
            <a class="nav-link" href="{{ route('shops.index') }}"><i class="bi bi-shop"></i><span>Mes boutiques</span></a>
            <hr class="border-secondary opacity-25">
            <div class="d-flex align-items-center gap-2 px-2 mb-2">
                <div class="rounded-circle bg-success-subtle text-success fw-bold d-grid place-items-center" style="width:32px;height:32px">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="small text-truncate"><div class="text-white fw-semibold">{{ auth()->user()->name }}</div><div class="text-white-50">Administrateur</div></div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link border-0 bg-transparent w-100 text-start" type="submit"><i class="bi bi-box-arrow-right"></i><span>Se déconnecter</span></button></form>
        </div>
    </aside>
    <main class="content">
        <header class="topbar px-4 py-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</div>
            @if($currentShop instanceof \App\Models\Shop)
                <a href="{{ route('sales.create', $currentShop) }}" class="btn btn-success btn-sm px-3"><i class="bi bi-plus-lg me-1"></i> Nouvelle vente</a>
            @endif
        </header>
        <div class="p-4 p-lg-5">
            @if(session('success')) <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> @endif
            @if($errors->any()) <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i><strong>Vérifiez les informations saisies.</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
            @yield('content')
        </div>
    </main>
</div>
@else
    @yield('guest-content')
@endauth
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>