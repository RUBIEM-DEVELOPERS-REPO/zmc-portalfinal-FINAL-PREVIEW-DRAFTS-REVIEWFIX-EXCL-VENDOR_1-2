<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Accreditation Applicant | ZMC')</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

  <style>
    :root{
      --bg-color:#f3f6f9;
      --sidebar-bg:#000000;
      --topbar-height:60px;
      --sidebar-width:280px;
      --zmc-yellow:#facc15;
      --zmc-yellow-dark:#eab308;
      --border-color:#e9ebec;
      --text-main:#495057;
      --text-muted:#878a99;
    }

    body{font-family:'Inter',sans-serif;background-color:var(--bg-color);margin:0;overflow-x:hidden;}

    .vertical-menu{
      width:var(--sidebar-width);height:100vh;background:var(--sidebar-bg);
      position:fixed;top:0;left:0;z-index:1001;color:#ffffff;transition:.3s ease;
      box-shadow:2px 0 10px rgba(0,0,0,0.1);
    }

    .navbar-brand-box{
      padding:20px 20px 15px;display:flex;align-items:center;gap:12px;
      border-bottom:1px solid rgba(255,255,255,0.1);
      justify-content: space-between;
    }
    .navbar-brand-box img{height:38px;}
    .logo-text{color:#fff;font-weight:800;font-size:16px;letter-spacing:0.5px;text-transform:uppercase;text-align: right;}
    .logo-text span.zimbabwe { color: #fff; }
    .logo-text span.media { color: #f5c518; }
    .logo-text span.commission { color: #fff; }
    .logo-sub{font-size:11px;color:var(--zmc-yellow);font-weight:600;display:block;margin-top:2px;text-align: right;}

    .sidebar-menu{padding:15px 0;list-style:none;margin:0;height:calc(100vh - 180px);overflow-y:auto;}
    .sidebar-menu a{
      text-decoration:none;display:flex;align-items:center;
      padding:12px 20px;font-size:14px;transition:0.3s;color:#8c8c8c;font-weight:500;
      border-left:3px solid transparent;
    }
    .sidebar-menu a i{margin-right:12px;font-size:18px;width:24px;text-align:center;}
    .sidebar-menu a:hover{color:var(--zmc-yellow);background:rgba(250,204,21,0.05);}
    .sidebar-menu a.active{color:var(--zmc-yellow);background:rgba(250,204,21,0.08);border-left:3px solid var(--zmc-yellow);}

    .sidebar-user{
      position:absolute;bottom:0;width:100%;padding:15px 20px;background:rgba(255,255,255,0.03);
      display:flex;align-items:center;gap:12px;
    }
    .sidebar-user img{width:38px;height:38px;border-radius:50%;}

    .topbar{
      height:var(--topbar-height);background:#fff;position:fixed;left:var(--sidebar-width);right:0;top:0;
      z-index:1000;display:flex;align-items:center;justify-content:space-between;padding:0 20px;
      box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:.3s ease;
    }
    .topbar-left{display:flex;align-items:center;gap:15px;}
    .topbar-title{font-weight:800;font-size:15px;letter-spacing:.5px;text-transform:uppercase;color:#111827;white-space:nowrap;}

    .main-content{margin-left:var(--sidebar-width);padding-top:var(--topbar-height);min-height:100vh;}
    .page-content{padding:20px;max-width:1200px;margin:0 auto;}

    .form-container{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:0;margin-top:20px;}
    .form-header{padding:24px 30px;border-bottom:1px solid var(--border-color);background:#fff;}
    .form-header h1{font-size:24px;font-weight:700;color:#111827;margin-bottom:8px;}
    .form-header p{color:var(--text-muted);font-size:14px;margin:0;line-height:1.5;}
    .form-steps-container{padding:30px;}

    .form-row{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:20px;}
    .form-field{margin-bottom:15px;}
    .form-label{display:block;font-size:14px;font-weight:600;color:#111827;margin-bottom:6px;}
    .form-label.required::after{content:'*';color:#ef4444;margin-left:4px;}
    .form-control{
      width:100%;padding:10px 12px;border:1px solid var(--border-color);border-radius:6px;font-size:14px;
      color:#111827;transition:all 0.2s;background:#fff;
    }
    .form-control:focus{outline:none;border-color:var(--zmc-yellow);box-shadow:0 0 0 3px rgba(250,204,21,0.15);}
    .form-hint{font-size:12px;color:var(--text-muted);margin-top:4px;}

    .step-progress{margin-bottom:30px;position:relative;}
    .step-progress-bar{display:flex;justify-content:space-between;position:relative;}
    .step-progress-bar::before{content:'';position:absolute;top:15px;left:0;right:0;height:2px;background:var(--border-color);z-index:1;}
    .step{position:relative;z-index:2;text-align:center;background:#fff;padding:0 10px;}
    .step-number{
      width:32px;height:32px;border-radius:50%;background:#fff;border:2px solid var(--border-color);
      display:flex;align-items:center;justify-content:center;margin:0 auto 5px;font-size:14px;font-weight:600;color:var(--text-muted);
    }
    .step.active .step-number{background:var(--zmc-yellow);border-color:var(--zmc-yellow);color:#000;}
    .step.completed .step-number{background:#10b981;border-color:#10b981;color:#fff;}
    .step-label{font-size:12px;font-weight:600;color:var(--text-muted);}
    .step.active .step-label{color:#111827;}

    .step-content{display:none;}
    .step-content.active{display:block;}
    .step-title{font-size:20px;font-weight:600;color:#111827;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid var(--border-color);}
    .current-step-info{font-size:14px;color:var(--text-muted);margin-bottom:20px;padding:10px;background:#f8f9fa;border-radius:6px;}

    .app-type-container{margin:30px 0;}
    .app-type-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;}
    .app-type-card{
      border:2px solid var(--border-color);border-radius:8px;padding:24px;cursor:pointer;transition:all 0.3s;text-align:center;
    }
    .app-type-card:hover{border-color:var(--zmc-yellow);transform:translateY(-2px);box-shadow:0 4px 12px rgba(250,204,21,0.1);}
    .app-type-card.selected{border-color:var(--zmc-yellow);background:rgba(250,204,21,0.08);}
    .app-type-card i{font-size:40px;color:var(--zmc-yellow);margin-bottom:15px;}

    .checkbox-group{display:flex;gap:20px;margin-top:10px;}
    .checkbox-item{display:flex;align-items:center;gap:8px;}
    .checkbox-item input{width:18px;height:18px;margin:0;cursor:pointer;}
    .checkbox-item label{font-size:14px;color:#111827;cursor:pointer;margin:0;}

    .upload-area{
      border:2px dashed var(--border-color);border-radius:6px;padding:30px;text-align:center;cursor:pointer;
      transition:all 0.3s;background:#f9fafb;margin-top:10px;
    }
    .upload-area:hover{border-color:var(--zmc-yellow);background:rgba(250,204,21,0.02);}
    .upload-area i{font-size:40px;color:var(--zmc-yellow);margin-bottom:15px;}
    .upload-btn{background:var(--zmc-yellow);color:#000;font-weight:600;padding:8px 20px;border-radius:4px;border:none;cursor:pointer;font-size:13px;}

    .form-buttons{display:flex;justify-content:space-between;padding-top:30px;margin-top:30px;border-top:1px solid var(--border-color);}

    .btn-primary{background:var(--zmc-yellow);color:#000;font-weight:600;border:none;}
    .btn-primary:hover{background:var(--zmc-yellow-dark);transform:translateY(-1px);box-shadow:0 4px 12px rgba(250,204,21,0.2);}
    .btn-secondary{background:#f3f4f6;color:#374151;font-weight:600;border:none;}
    .btn-secondary:hover{background:#e5e7eb;}

    .dashboard-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:30px;}
    .dashboard-card{background:#fff;border-radius:8px;padding:20px;box-shadow:0 2px 4px rgba(0,0,0,0.05);border-left:4px solid var(--zmc-yellow);}
    .dashboard-card h5{font-size:14px;font-weight:600;color:#6b7280;margin-bottom:10px;}
    .dashboard-card .count{font-size:32px;font-weight:700;color:#111827;margin-bottom:5px;}
    .dashboard-card .trend{font-size:12px;color:#10b981;display:flex;align-items:center;gap:4px;}

    .notice-list{background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;}
    .notice-item{padding:15px 0;border-bottom:1px solid var(--border-color);}
    .notice-item:last-child{border-bottom:none;}
    .notice-date{font-size:12px;color:var(--text-muted);margin-bottom:5px;}

    .guide-step{display:flex;align-items:flex-start;margin-bottom:25px;padding-bottom:25px;border-bottom:1px solid var(--border-color);}
    .step-number-large{
      background:var(--zmc-yellow);color:#000;width:48px;height:48px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;font-weight:900;font-size:22px;margin-right:15px;flex-shrink:0;
      box-shadow:0 4px 12px rgba(250,204,21,0.3);
    }

    @media (max-width:768px){
      .form-row{grid-template-columns:1fr;}
      .form-steps-container{padding:20px;}
      .app-type-cards{grid-template-columns:1fr;}
    }

    /* Simple floating chatbot button */
    .chatbot-fab{
      position:fixed;right:18px;bottom:18px;z-index:2000;
      width:56px;height:56px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      background:var(--zmc-yellow);color:#000;border:none;
      box-shadow:0 10px 20px rgba(0,0,0,0.15);
    }
  </style>

  @stack('styles')

  <style>
    :root{
      --zmc-primary:#000000;
      --zmc-primary-dark:#1a1a1a;
      --zmc-accent:#facc15;
      --zmc-accent-dark:#eab308;
      
      /* Typography System */
      --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --font-size-xs: 11px;
      --font-size-sm: 12px;
      --font-size-base: 14px;
      --font-size-lg: 16px;
      --font-size-xl: 18px;
      --font-size-2xl: 22px;
      --font-weight-normal: 400;
      --font-weight-medium: 500;
      --font-weight-semibold: 600;
      --font-weight-bold: 700;
      --font-weight-black: 900;
      --line-height-tight: 1.2;
      --line-height-normal: 1.5;
    }
    body {
      font-family: var(--font-primary) !important;
      font-size: var(--font-size-base);
      line-height: var(--line-height-normal);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    h1, h2, h3, h4, h5, h6 { font-family: var(--font-primary); line-height: var(--line-height-tight); }
    .fw-black { font-weight: var(--font-weight-black) !important; }
    .fw-bold { font-weight: var(--font-weight-bold) !important; }
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
    body{ background:#fafafa !important; }
  </style>

</head>
<body>
  <div class="vertical-menu">
    <div class="navbar-brand-box">
      <img src="{{ asset('zmc_logo.png') }}" alt="ZMC Logo">
      <div>
        <span class="logo-text"><span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span></span>
        <span class="logo-sub">Media Practitioner Accreditation</span>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li>
        <a class="{{ request()->routeIs('accreditation.home') ? 'active' : '' }}" href="{{ route('accreditation.home') }}">
          <i class="ri-home-4-line"></i> Home
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.new') ? 'active' : '' }}" href="{{ route('accreditation.new') }}">
          <i class="ri-file-add-line"></i> New Accreditation (AP3)
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.renewals*') ? 'active' : '' }}" href="{{ route('accreditation.renewals.index') }}">
          <i class="ri-refresh-line"></i> Renewals (AP5)
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.payments') ? 'active' : '' }}" href="{{ route('accreditation.payments') }}">
          <i class="ri-bank-card-line"></i> Payment History
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.notices') ? 'active' : '' }}" href="{{ route('accreditation.notices') }}">
          <i class="ri-megaphone-line"></i> Notices & Events
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.howto') ? 'active' : '' }}" href="{{ route('accreditation.howto') }}">
          <i class="ri-information-line"></i> How to Get Accredited
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.profile') ? 'active' : '' }}" href="{{ route('accreditation.profile') }}">
          <i class="ri-user-line"></i> Profile
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.communication') ? 'active' : '' }}" href="{{ Route::has('accreditation.communication') ? route('accreditation.communication') : '#' }}">
          <i class="ri-mail-line"></i> Communication
        </a>
      </li>
      <li>
        <a class="{{ request()->routeIs('accreditation.settings') ? 'active' : '' }}" href="{{ route('accreditation.settings') }}">
          <i class="ri-settings-3-line"></i> Settings
        </a>
      </li>
    </ul>

    <div class="sidebar-user">
      <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=facc15&color=000" alt="user">
      <div>
        <div class="fw-bold" style="font-size:13px;">{{ Auth::user()->name ?? 'User' }}</div>
        <div style="font-size:11px;color:#bbb;">Applicant</div>
      </div>
    </div>
  </div>

  <header class="topbar">
    <div class="topbar-left">
      <span class="topbar-title">@yield('page_title', 'ACCREDITATION APPLICANT')</span>
    </div>
    <div class="topbar-right">
      <a href="{{ Route::has('accreditation.communication') ? route('accreditation.communication') : '#' }}" class="btn btn-secondary btn-sm">
        <i class="ri-mail-line me-1"></i> Email
      </a>
    </div>
  </header>

  <div class="main-content">
    <div class="page-content">
      @yield('content')
    </div>
  </div>

  {{-- Chatbot floating button + modal --}}
  @include('portal.accreditation.partials.chatbot')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
