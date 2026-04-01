<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') | ZMC Media Portal</title>

  {{-- Bootstrap 5 (required for dropdowns/modals in dashboards) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Chart Libraries for Director Dashboard --}}
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="{{ asset('js/director-dashboard-charts.js') }}"></script>

  {{-- Fonts & Icons --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@latest/css/all.min.css" rel="stylesheet">

  {{-- Green Theme CSS --}}
  <link href="{{ asset('css/green-theme.css') }}" rel="stylesheet">

  {{-- Base theme variables + global dashboard CSS --}}
  <style>
    :root{
      --accent:#facc15; --accent-dark:#eab308;
      --bg:#f3f4f6; --card:#ffffff; --border:#e5e7eb;
      --text:#111827; --muted:#6b7280;
      --sidebar:#000000; --sidebar-2:#1a1a1a; --sidebar-text:#ffffff;
      --shadow:0 14px 40px rgba(0,0,0,.08); --radius:14px;
      --zmc-accent: #facc15; --zmc-accent-dark: #eab308;
      --zmc-border: #e2e8f0; --zmc-soft: #f8fafc;
      --zmc-text: #0f172a; --zmc-muted: #64748b;
    }

    /* ====== Layout (sidebar + main + topbar) ====== */
    html, body{ height: 100%; margin: 0; }
    
    .app{ display:flex; min-height:100vh; background: var(--bg); }
    .main{ flex:1; min-width:0; display: flex; flex-direction: column; background: var(--bg); }
    .content{ padding: 18px 18px 40px; flex: 1; background: var(--bg); }
    
    /* Typography Utilities - Now leveraging variables from green-theme.css */
    .text-xs { font-size: var(--font-size-xs) !important; }
    .text-sm { font-size: var(--font-size-sm) !important; }
    .text-base { font-size: var(--font-size-base) !important; }
    .text-lg { font-size: var(--font-size-lg) !important; }
    .text-xl { font-size: var(--font-size-xl) !important; }
    .text-2xl { font-size: var(--font-size-2xl) !important; }
    .fw-normal { font-weight: var(--font-weight-normal) !important; }
    .fw-medium { font-weight: var(--font-weight-medium) !important; }
    .fw-semibold { font-weight: var(--font-weight-semibold) !important; }
    .fw-bold { font-weight: var(--font-weight-bold) !important; }
    .fw-black { font-weight: var(--font-weight-black) !important; }

    /* Sidebar */
    .vertical-menu{ width: 270px; background: url('{{ asset("zmc_building.png") }}') center center / cover no-repeat; color:var(--sidebar-text); position: fixed; top:0; left: 0; bottom: 0; height: 100%; display:flex; flex-direction:column; overflow:hidden; z-index: 1000; }
    .vertical-menu::before{ content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(180deg, rgba(45, 80, 22, 0.85), rgba(31, 58, 15, 0.88)); z-index: 0; pointer-events: none; }
    .vertical-menu > *{ position: relative; z-index: 1; }
    .main{ margin-left: 270px; }
    .navbar-brand-box{ padding: 24px 16px; display:flex; flex-direction:column; gap:12px; align-items:center; border-bottom: 1px solid rgba(255,255,255,.08); justify-content: center; }
    .navbar-brand-box img{ width: 80px; height: 80px; object-fit:contain; background: #ffffff; border-radius: 50%; padding: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.3s ease; }
    .navbar-brand-box img:hover{ transform: scale(1.05); }

    .logo-portal-name {
      color: #ffffff;
      font-size: 11px;
      font-weight: 700;
      text-align: center;
      margin-top: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1.2;
    }

    .logo-text{ 
      display:block; 
      font-family: var(--font-primary);
      font-weight: var(--font-weight-black); 
      font-size: 13px; 
      letter-spacing:.4px; 
      text-align: right; 
      line-height: var(--line-height-tight);
    }
    .logo-text span.zimbabwe { color: #fff; }
    .logo-text span.media { color: #4caf50; }
    .logo-text span.commission { color: #facc15; }
    .logo-sub{ 
      display:block; 
      font-family: var(--font-primary);
      font-size: var(--font-size-xs); 
      opacity:.75; 
      margin-top:2px; 
      text-align: right; 
    }

    .sidebar-menu{ list-style:none; padding: 12px; margin:0; flex:1; overflow-y:auto; }
    .sidebar-menu li a{ 
      display:flex; align-items:center; gap:10px; padding: 10px 12px; border-radius: 12px; 
      color: rgba(255,255,255,.86); text-decoration:none; 
      font-family: var(--font-primary);
      font-weight: var(--font-weight-bold); 
      font-size: 10px; 
      transition: all 0.2s ease; 
    }
    .sidebar-menu li a i{ font-size: 16px; }
    .sidebar-menu li.active a, .sidebar-menu li a:hover{ background: rgba(250, 204, 21, 0.15); color:#facc15; border-left: 3px solid #facc15; }
    .sidebar-menu li a:hover{ background: rgba(250, 204, 21, 0.08); }
    .sidebar-user{ padding: 10px 14px; border-top: 1px solid rgba(255,255,255,.08); display:flex; gap:10px; align-items:center; margin-top:auto; background: rgba(0,0,0,.10); }
    .sidebar-user img{ width: 32px; height:32px; border-radius: 999px; }

    /* Collapsed sidebar */
    .app.sidebar-collapsed .vertical-menu{ width: 78px; }
    .app.sidebar-collapsed .main{ margin-left: 78px; }
    .app.sidebar-collapsed .logo-sub, .app.sidebar-collapsed .logo-text{ display:none; }
    .app.sidebar-collapsed .sidebar-menu li a span{ display:none; }
    .app.sidebar-collapsed .sidebar-user > div{ display:none; }

    /* Topbar */
    .topbar{ position: sticky; top:0; z-index: 1030; background:#fff; border-bottom:1px solid var(--border); }
    .topbar-row--main{ display:flex; align-items:center; justify-content:space-between; padding: 10px 14px; }
    .topbar-left{ display:flex; align-items:center; gap:10px; }
    .topbar-toggle{ width:40px; height:40px; border-radius: 12px; border:1px solid var(--border); background:#fff; display:inline-flex; align-items:center; justify-content:center; }
    .topbar-toggle i{ font-size: 20px; }
    .topbar-right{ display:flex; align-items:center; gap:8px; }
    .icon-btn{ width: 40px; height: 40px; border-radius: 12px; border:1px solid var(--border); background:#fff; display:inline-flex; align-items:center; justify-content:center; position:relative; }
    .icon-btn i{ font-size: 18px; color:#0f172a; }
    .icon-badge{ position:absolute; top: 4px; right: 4px; font-size: 9px; padding: 2px 5px; min-width: 18px; border-radius: 999px; color:#fff; font-weight: 900; text-align: center; line-height: 1.2; }
    .badge-blue{ background:#2563eb; }
    .badge-red{ background:#ef4444; }
    .hide-caret::after{ display:none !important; }
    .logout-pill{ height: 34px; border-radius: 999px; border: 1px solid var(--border); background: #111827; color:#fff; font-weight: 900; font-size: 9px; padding: 0 12px; display:inline-flex; align-items:center; gap: 8px; }

    /* ====== ZMC dashboard components (cards, tables, modals) ====== */
    .zmc-card,
    .modal-content{ border-radius: 0 !important; }
    .form-control,
    .btn:not(.zmc-icon-btn){ 
      border-radius: 8px;
      font-family: var(--font-primary);
      font-size: 11px;
    }
    .dropdown-item{ font-size: 11px !important; }
    .zmc-card{ 
      background:#ffffff !important; 
      border:1px solid var(--zmc-border); 
      padding:18px; 
      transition:.2s; 
    }
    .icon-box{ width:40px;height:40px; display:flex;align-items:center;justify-content:center; font-size:20px; }
    .zmc-mini-table thead th{ 
      background: var(--zmc-soft); 
      color: #475569; 
      font-family: var(--font-primary);
      font-size: var(--font-size-xs); 
      font-weight: var(--font-weight-black); 
      padding: 12px 12px; 
      border-bottom: 2px solid #edf2f7; 
      white-space: nowrap; 
    }
    .zmc-mini-table tbody td{ 
      padding: 12px 12px; 
      border-bottom: 1px solid #f1f5f9; 
      font-family: var(--font-primary);
      font-size: var(--font-size-sm); 
      vertical-align: middle; 
    }
    .zmc-pill{ 
      padding:5px 12px; 
      font-family: var(--font-primary);
      font-size: var(--font-size-sm); 
      font-weight: var(--font-weight-bold); 
      display:inline-flex; 
      align-items:center; 
      gap:8px; 
      border:1px solid var(--zmc-border); 
      background:#fff; 
      white-space:nowrap; 
      border-radius: 999px; 
    }
    .zmc-pill-dark{ background:#111827; color:#fff; border-color:#111827; }
    .zmc-action-strip{ display:flex; gap:3px; justify-content:flex-end; }
    .zmc-icon-btn{ width:32px; height:32px; padding:0; border-radius:999px !important; display:inline-flex; align-items:center; justify-content:center; border-width:1px; background:#fff; line-height:1; }
    .zmc-icon-btn i{ font-size:15px; line-height:1; }
    .zmc-icon-btn:hover{ border-color: var(--zmc-accent-dark) !important; color: var(--zmc-accent-dark) !important; }
    .zmc-modal-header{ position:relative; background:#ffffff !important; border-bottom: 1px solid rgba(15,23,42,.10) !important; padding: 14px 16px; }
    .zmc-modal-header::before{ content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background: var(--zmc-accent-dark); }
    .zmc-modal-title{ 
      font-family: var(--font-primary);
      font-weight: var(--font-weight-black); 
      color: var(--zmc-text); 
      font-size: var(--font-size-lg); 
      line-height: var(--line-height-tight); 
    }
    .zmc-modal-sub{ 
      font-family: var(--font-primary);
      font-size: var(--font-size-sm); 
      font-weight: var(--font-weight-bold); 
      color: var(--zmc-muted); 
      margin-top: 2px; 
    }
    .zmc-modal-footer{ border-top: 1px solid rgba(15,23,42,.08) !important; }
    .zmc-lbl{ 
      font-family: var(--font-primary);
      font-size: var(--font-size-xs); 
      font-weight: var(--font-weight-black); 
      color: #64748b; 
      margin-bottom: 6px; 
    }
    .zmc-input{ 
      font-family: var(--font-primary);
      font-size: var(--font-size-sm) !important; 
      padding: 7px 10px !important; 
      line-height: 1.25 !important; 
    }
    .zmc-input[readonly]{ background:#fff !important; }
    .zmc-mdl-block{ padding:0; margin-bottom: 16px; }
    .zmc-mdl-title{ 
      display:flex; 
      align-items:center; 
      gap:10px; 
      font-family: var(--font-primary);
      font-size: var(--font-size-sm); 
      font-weight: var(--font-weight-black); 
      color: var(--zmc-text); 
      padding: 10px 0 10px; 
      border-bottom: 1px solid rgba(15,23,42,.10); 
      margin-bottom: 12px; 
    }
    .zmc-mdl-title i{ color: var(--zmc-accent-dark); }
    .zmc-table-lite thead th{ 
      background: rgba(248,250,252,.95); 
      font-family: var(--font-primary);
      font-size: var(--font-size-xs); 
      font-weight: var(--font-weight-bold); 
      padding: 10px 10px; 
      border-bottom: 2px solid rgba(15,23,42,.08) !important; 
      border-top: none !important; 
    }
    .zmc-table-lite tbody td{ 
      font-family: var(--font-primary);
      font-size: var(--font-size-sm); 
      padding: 10px 10px; 
      border-top: none !important; 
      border-bottom: 1px solid rgba(15,23,42,.06) !important; 
      vertical-align: middle; 
    }
    .zmc-table-lite{ margin-bottom:0; }


    /* ====== Applicant Forms ====== */
    .form-container{background:#fff;border:1px solid var(--border);box-shadow:var(--shadow);border-radius:16px;overflow:hidden;}
    .form-header{padding:18px 18px;background:linear-gradient(180deg, rgba(76,175,80,.12), rgba(255,255,255,0));border-bottom:1px solid rgba(76,175,80,.2);}
    .form-header h1{font-size:16px;font-weight:900;margin:0 0 6px;color:#000;}
    .form-header p{margin:0;font-size:12px;color:#666;font-weight:700;}
    .form-steps-container{padding:16px 18px 18px;}
    .step-progress{margin-bottom:14px;}
    .step-progress-bar{display:flex;gap:10px;flex-wrap:wrap;}
    .step{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #e0e0e0;border-radius:14px;background:#fff;cursor:pointer;user-select:none;transition:all 0.2s ease;}
    .step .step-number{width:28px;height:28px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:900;background:rgba(76,175,80,.15);color:#2e7d32;}
    .step .step-label{font-size:12px;font-weight:900;color:#424242;}
    .step.active{border-color:rgba(76,175,80,.5);box-shadow:0 10px 25px rgba(76,175,80,.15);background:rgba(76,175,80,.05);}
    .step.active .step-number{background:rgba(76,175,80,.75);color:#fff;}
    .step-content{display:none;}
    .step-content.active{display:block;}
    .step-title{font-size:14px;font-weight:900;color:#000;margin:6px 0 10px;}
    .current-step-info{font-size:12px;font-weight:700;color:#666;padding:10px 12px;border:1px dashed rgba(76,175,80,.3);background:rgba(76,175,80,.08);border-radius:14px;margin-bottom:12px;}
    .form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:12px;}
    .form-row .form-field{min-width:0;}
    @media (max-width:768px){.form-row{grid-template-columns:1fr;}.step{flex:1 1 auto;}.app-type-cards{grid-template-columns:1fr;}}
    .form-label{font-size:11px;font-weight:900;color:#475569;}
    .form-label.required::after{content:' *';color:#ef4444;font-weight:900;}
    .checkbox-group{display:flex;gap:10px;flex-wrap:wrap;}
    .checkbox-item{border:1px solid var(--border);border-radius:12px;padding:10px 12px;background:#fff;display:flex;gap:8px;align-items:center;}
    .app-type-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
    .app-type-card{border:1px solid #e0e0e0;border-radius:16px;padding:14px;background:#fff;cursor:pointer;transition:.15s;}
    .app-type-card:hover{transform:translateY(-1px);box-shadow:0 10px 25px rgba(76,175,80,.12);border-color:rgba(76,175,80,.3);}
    .app-type-card.selected{border-color:rgba(76,175,80,.6);box-shadow:0 10px 25px rgba(76,175,80,.2);background:rgba(76,175,80,.05);}
    .app-type-card i{font-size:22px;color:#000;}
    .app-type-card h4{font-size:13px;font-weight:900;margin:8px 0 6px;color:#000;}
    .app-type-card p{font-size:12px;color:#666;font-weight:700;margin:0;}
    .form-buttons{display:flex;justify-content:space-between;gap:10px;margin-top:14px;}
    .upload-area{border:1px dashed rgba(15,23,42,.18);border-radius:16px;padding:14px;background:rgba(248,250,252,.8);text-align:left;}
    .upload-area h5{font-size:12px;font-weight:900;margin:8px 0 3px;}
    .upload-area p{font-size:11px;color:var(--muted);font-weight:700;margin:0 0 8px;}
    .upload-btn{border:1px solid var(--border);background:#fff;border-radius:12px;padding:8px 10px;font-size:12px;font-weight:900;}

  </style>



  <style>
    :root{
      --zmc-primary:#2d5016;
      --zmc-primary-dark:#1f3a0f;
      --zmc-accent:#facc15;
      --zmc-accent-dark:#eab308;
    }
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
    
    /* Nav tabs global styling */
    .nav-tabs .nav-link.active {
      border-bottom: 3px solid var(--zmc-accent) !important;
      color: var(--zmc-primary) !important;
    }
    .nav-tabs .nav-link:hover:not(.active) {
      border-bottom: 3px solid var(--zmc-accent-dark);
    }
    
    /* Alert styling */
    .alert-success {
      background-color: rgba(250,204,21,.1) !important;
      border-color: rgba(250,204,21,.3) !important;
      color: #78350f !important;
    }
    .alert-warning {
      background-color: rgba(255,152,0,.1) !important;
      border-color: rgba(255,152,0,.3) !important;
      color: #e65100 !important;
    }
    
    body{
      background: url('{{ asset("zmc_building.png") }}') no-repeat center center fixed !important;
      background-size: cover !important;
    }
    body::before{
      content: "";
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: transparent;
      z-index: -1;
    }

    .topbar{ background: url('{{ asset("zmc_building.png") }}') center center / cover no-repeat !important; border-bottom: none !important; position: relative; }
    .topbar::before{ content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(45, 80, 22, 0.85), rgba(31, 58, 15, 0.88)); z-index: 0; }
    .topbar > *{ position: relative; z-index: 1; }
    .topbar .icon-btn{ background: rgba(255,255,255,.1) !important; border-color: rgba(255,255,255,.2) !important; }
    .topbar .icon-btn i{ color: #fff !important; }
    .topbar .logout-pill{ background: rgba(255,255,255,.15) !important; border-color: rgba(255,255,255,.25) !important; }
    .topbar-toggle{ background: rgba(255,255,255,.1) !important; border-color: rgba(255,255,255,.2) !important; }
    .topbar-toggle i{ color: #fff !important; }

    /* ====== Premium Dark Theme Overrides ====== */
     body.theme-dark {
       --bg: #0f172a;
       --card: #1e293b;
       --border: #334155;
       --text: #f1f5f9;
       --muted: #94a3b8;
       --zmc-soft: #1e293b;
       --zmc-text: #f1f5f9;
       --zmc-muted: #94a3b8;
       --zmc-border: #334155;
       background: #0f172a !important;
     }
 
     body.theme-dark .app, 
     body.theme-dark .main, 
     body.theme-dark .content { background: #0f172a !important; }
 
     body.theme-dark .zmc-card,
     body.theme-dark .card { 
       background: #1e293b !important; 
       border-color: #334155 !important; 
       box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
       color: #f1f5f9;
     }
 
     body.theme-dark .table { color: #f1f5f9; border-color: #334155; }
     body.theme-dark .table th, 
     body.theme-dark .zmc-mini-table thead th,
     body.theme-dark .zmc-table-lite thead th { 
       background: #0f172a !important; 
       color: #94a3b8; 
       border-bottom-color: #334155 !important;
     }
     body.theme-dark .table td { border-bottom-color: #1e293b !important; }
 
     body.theme-dark .form-control,
     body.theme-dark .zmc-input {
       background: #0f172a !important;
       border-color: #334155 !important;
       color: #f1f5f9 !important;
     }
     body.theme-dark .form-control:focus { border-color: var(--zmc-accent) !important; box-shadow: 0 0 0 2px rgba(250, 204, 21, 0.1); }
 
     body.theme-dark .dropdown-menu { background: #1e293b; border: 1px solid #334155; box-shadow: 0 10px 40px rgba(0,0,0,0.4); }
     body.theme-dark .dropdown-item { color: #f1f5f9; }
     body.theme-dark .dropdown-item:hover { background: #334155; }
     body.theme-dark .dropdown-divider { border-top-color: #334155; }
 
     body.theme-dark .modal-content { background: #1e293b; color: #f1f5f9; border: 1px solid #334155; }
     body.theme-dark .modal-header { border-bottom-color: #334155 !important; background: #1e293b !important; }
     body.theme-dark .modal-footer { border-top-color: #334155 !important; }
 
     body.theme-dark .bg-light { background: #0f172a !important; }
     body.theme-dark .text-muted { color: #94a3b8 !important; }
     body.theme-dark .border-bottom, 
     body.theme-dark .border-top,
     body.theme-dark .border { border-color: #334155 !important; }
 
     body.theme-dark .icon-btn,
     body.theme-dark .topbar-toggle { 
       background: #1e293b !important; 
       border-color: #334155 !important; 
     }
     body.theme-dark .icon-btn i { color: #f1f5f9 !important; }
 
     body.theme-dark .topbar::before { opacity: 0.95; }
     body.theme-dark .vertical-menu::before { opacity: 0.95; }
 
     /* Fixes for stubborn components */
     body.theme-dark .pagination .page-link { background: #1e293b; border-color: #334155; color: #f1f5f9; }
     body.theme-dark .pagination .page-item.active .page-link { background: var(--zmc-accent) !important; color: #000; }
 
     body.theme-dark .alert-success { background: rgba(250, 204, 21, 0.05) !important; border-color: rgba(250, 204, 21, 0.2) !important; color: #facc15 !important; }
     body.theme-dark .btn-outline-secondary { color: #f1f5f9; border-color: #334155; }
     body.theme-dark .btn-outline-secondary:hover { background: #334155; }


  </style>

  {{-- Global Pagination Styles - Hide large arrows, keep text buttons --}}
  <style>
    /* FORCE HIDE all SVG arrows in pagination with maximum specificity */
    .pagination svg,
    nav[role="navigation"] svg,
    .page-link svg,
    .page-item svg,
    nav svg {
      display: none !important;
      visibility: hidden !important;
      width: 0 !important;
      height: 0 !important;
      opacity: 0 !important;
    }
    
    /* Hide any arrow-like elements */
    .pagination .page-link::before,
    .pagination .page-link::after {
      display: none !important;
    }
    
    /* Style pagination to show only Previous/Next text */
    .pagination .page-link {
      border-radius: 6px !important;
      margin: 0 2px !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      padding: 6px 12px !important;
      color: var(--zmc-text) !important;
      border: 1px solid var(--zmc-border) !important;
    }
    
    .pagination .page-link:hover {
      background-color: var(--zmc-soft) !important;
      border-color: var(--zmc-accent) !important;
      color: var(--zmc-accent-dark) !important;
    }
    
    .pagination .page-item.active .page-link {
      background-color: var(--zmc-accent) !important;
      border-color: var(--zmc-accent) !important;
      color: #fff !important;
      font-weight: 600 !important;
    }
    
    .pagination .page-item.disabled .page-link {
      opacity: 0.5 !important;
      cursor: not-allowed !important;
    }
    
    /* Ensure Previous/Next buttons show text only */
    nav[role="navigation"] .page-link {
      display: inline-flex !important;
      align-items: center !important;
      gap: 4px !important;
    }
    
    /* Additional specificity for stubborn arrows */
    nav[aria-label="Pagination Navigation"] svg,
    .pagination-wrapper svg,
    [role="navigation"] [aria-label*="Previous"] svg,
    [role="navigation"] [aria-label*="Next"] svg {
      display: none !important;
    }
  </style>

</head>

@php
  $theme = auth()->check() ? (auth()->user()->theme ?? 'light') : 'light';
@endphp

<body class="theme-{{ $theme }}">
  <div class="app">
    {{--
      ✅ Super Admin uses the STAFF theme/sidebar.
      So both /staff/* and /admin/* routes load the same staff sidebar.
    --}}
    @php
      $user = auth()->user();
      $isStaffRoute = request()->is('staff/*') || request()->routeIs('staff.*') || request()->is('admin/*') || request()->routeIs('admin.*');
      $showStaffSidebar = $isStaffRoute || ($user && ($user->hasRole('accreditation_officer') || $user->hasRole('accounts_payments') || $user->hasRole('registrar') || $user->hasRole('production') || $user->hasRole('auditor') || $user->hasRole('super_admin') || $user->hasRole('it_admin') || $user->hasRole('director')));
    @endphp

    @if($showStaffSidebar)
      @include('layouts.sidebar_staff')
    @else
      @include('layouts.sidebar')
    @endif

    <main class="main">
      @include('layouts.topbar')

      <section class="content">
        @yield('content')
      </section>

      {{-- Floating Chatbot (bottom-right) --}}
      @include('layouts.chatbot')
    </main>
  </div>

  {{-- Page-specific scripts --}}
  @stack('scripts')


  {{-- Sidebar toggle, Theme Switcher & scroll persistence --}}
   <script>
     function updateThemeIcon(theme) {
       const icon = document.getElementById('themeIcon');
       if (icon) {
         icon.className = theme === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
       }
     }
 
     async function toggleZMCTheme() {
       const body = document.body;
       const isDark = body.classList.contains('theme-dark');
       const newTheme = isDark ? 'light' : 'dark';
 
       // Optimistic UI update
       body.classList.remove('theme-light', 'theme-dark');
       body.classList.add('theme-' + newTheme);
       updateThemeIcon(newTheme);
 
       try {
         const res = await fetch("{{ route('settings.theme') }}", {
           method: 'POST',
           headers: {
             'X-CSRF-TOKEN': '{{ csrf_token() }}',
             'Accept': 'application/json',
             'Content-Type': 'application/json'
           },
           body: JSON.stringify({ theme: newTheme })
         });
         
         if (!res.ok) {
           // If backend fails, fallback is handled on next reload, 
           // but we could revert here if needed.
           console.error('Failed to sync theme with server');
         }
       } catch (e) {
         console.error('Error toggling theme:', e);
       }
     }
 
     document.addEventListener('DOMContentLoaded', function(){
       // Init Theme Icon
       const currentTheme = document.body.classList.contains('theme-dark') ? 'dark' : 'light';
       updateThemeIcon(currentTheme);
 
       // Sidebar toggle handling
       const btn = document.getElementById('sidebarToggle');
       if(btn) {
         btn.addEventListener('click', function(){
           document.querySelector('.app')?.classList.toggle('sidebar-collapsed');
         });
       }
 
       // Scroll persistence
       const sidebarMenu = document.querySelector('.sidebar-menu');
       if (sidebarMenu) {
         const savedScroll = sessionStorage.getItem('sidebar-scroll');
         if (savedScroll) {
           sidebarMenu.scrollTop = parseInt(savedScroll, 10);
         }
         sidebarMenu.addEventListener('scroll', function() {
           sessionStorage.setItem('sidebar-scroll', sidebarMenu.scrollTop);
         });
       }
     });
   </script>
 </body>
</html>

