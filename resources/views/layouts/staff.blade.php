<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'ZMC Staff Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 (if you already load via Vite/app, remove these 2 lines) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Icons (optional) --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    {{-- Green Theme CSS --}}
    <link href="{{ asset('css/green-theme.css') }}" rel="stylesheet">

    <style>
        :root{
            --zmc-primary:#1a1a1a;
            --zmc-primary-dark:#111111;
            --zmc-accent:#facc15;
            --zmc-accent-dark:#eab308;
            
            /* Typography System */
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-size-xs: 9px;
            --font-size-sm: 10px;
            --font-size-base: 13px;
            --font-size-lg: 15px;
            --font-size-xl: 18px;
            --font-size-2xl: 22px;
            --font-size-3xl: 28px;
            --font-weight-normal: 400;
            --font-weight-medium: 500;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --font-weight-black: 900;
            --line-height-tight: 1.2;
            --line-height-normal: 1.5;
            --line-height-relaxed: 1.75;
        }
        body {
            font-family: var(--font-primary);
            font-size: var(--font-size-base);
            line-height: var(--line-height-normal);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-primary); line-height: var(--line-height-tight); }
        .fw-black { font-weight: var(--font-weight-black) !important; }
        .fw-bold { font-weight: var(--font-weight-bold) !important; }
        .fw-semibold { font-weight: var(--font-weight-semibold) !important; }
        .btn-primary, .btn-success{
            background: var(--zmc-primary) !important;
            border-color: var(--zmc-accent) !important;
            color: var(--zmc-accent) !important;
        }
        .btn-primary:hover, .btn-success:hover{
            background: var(--zmc-accent) !important;
            border-color: var(--zmc-primary) !important;
            color: var(--zmc-primary) !important;
        }
        .badge.bg-success-subtle{ background: rgba(250,204,21,.15) !important; color: #78350f !important; }
        .text-success{ color: var(--zmc-accent-dark) !important; }
        .bg-success{ background-color: var(--zmc-accent) !important; }
        .border-success{ border-color: var(--zmc-accent) !important; }
        .bg-success-subtle{ background-color: rgba(250,204,21,.15) !important; }
        .border-success-subtle{ border-color: rgba(250,204,21,.3) !important; }
        
        .zmc-topbar{
            background: url('/zmc_building.png') center center / cover no-repeat;
            position: relative;
        }
        .zmc-topbar::before{
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.72), rgba(0, 0, 0, 0.78));
            z-index: 0;
        }
        .zmc-topbar > *{
            position: relative;
            z-index: 1;
        }
        .zmc-badge{
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.25);
            color:#fff;
        }
        .zmc-btn-outline{
            border-color: rgba(255,255,255,.5);
            color:#fff;
        }
        .zmc-btn-outline:hover{
            background: rgba(255,255,255,.12);
            border-color:#fff;
            color:#fff;
        }
        body{ 
            background: #f3f4f6 !important;
        }
        body::before{
            display: none;
        }
    </style>

    @stack('head')
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark zmc-topbar">
    <div class="container-fluid px-3">
        <span class="navbar-brand fw-bold d-flex align-items-center gap-2">
            <img src="{{ asset('zmc_logo.png') }}" alt="ZMC" style="height:34px">
            ZMC Staff Portal
        </span>

        <div class="d-flex align-items-center gap-2">
            @if(session('active_staff_role'))
                <span class="badge rounded-pill zmc-badge text-uppercase">
                    {{ str_replace('_',' ', session('active_staff_role')) }}
                </span>
            @endif

            <span class="text-white small d-none d-md-inline">
                {{ auth()->user()->name ?? auth()->user()->email }}
            </span>

            {{-- Switch role goes back to /staff landing --}}
            <a href="{{ route('staff.entry') }}" class="btn btn-sm zmc-btn-outline">
                <i class="ri-shuffle-line"></i> Switch Role
            </a>

            {{-- Staff logout --}}
            <form method="POST" action="{{ route('staff.logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="ri-logout-box-r-line"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<main class="container-fluid py-3 px-3">
    @yield('content')
</main>

</body>
</html>
