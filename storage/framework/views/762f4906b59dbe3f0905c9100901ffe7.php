<!doctype html>
<html lang="<?php echo e(str_replace("_", "-", app()->getLocale())); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $__env->yieldContent('title'); ?> | ZMC Media Portal</title>

  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@latest/css/all.min.css" rel="stylesheet">

  
  <style>
    :root{
      --accent:#c9a227; --accent-dark:#b8931f;
      --bg:#f3f4f6; --card:#ffffff; --border:#e5e7eb;
      --text:#111827; --muted:#6b7280;
      --sidebar:#2e7d32; --sidebar-2:#1b5e20; --sidebar-text:#e8f5e9;
      --shadow:0 14px 40px rgba(0,0,0,.08); --radius:14px;
      --zmc-accent: #fbbf24; --zmc-accent-dark: #f59e0b;
      --zmc-border: #e2e8f0; --zmc-soft: #f8fafc;
      --zmc-text: #0f172a; --zmc-muted: #64748b;
    }

    /* ====== Layout (sidebar + main + topbar) ====== */
    html, body{ height: 100%; margin: 0; }
    body{ font-family:'Inter', sans-serif; background: var(--bg); color: var(--text); }
    .app{ display:flex; min-height:100vh; }
    .main{ flex:1; min-width:0; display: flex; flex-direction: column; }
    .content{ padding: 18px 18px 40px; flex: 1; }

    /* Sidebar */
    .vertical-menu{ width: 270px; background: url('<?php echo e(asset("zmc_building.png")); ?>') center center / cover no-repeat; color:var(--sidebar-text); position: fixed; top:0; left: 0; bottom: 0; height: 100%; display:flex; flex-direction:column; overflow:hidden; z-index: 1000; }
    .vertical-menu::before{ content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(180deg, rgba(46, 125, 50, 0.82), rgba(27, 94, 32, 0.85)); z-index: 0; pointer-events: none; }
    .vertical-menu > *{ position: relative; z-index: 1; }
    .main{ margin-left: 270px; }
    .navbar-brand-box{ padding: 18px 16px; display:flex; gap:10px; align-items:center; border-bottom: 1px solid rgba(255,255,255,.08); justify-content: space-between; }
    .navbar-brand-box img{ 
      width: 100%;
      height: 100%;
      object-fit: contain; 
      display: block;
      margin: 0;
      mix-blend-mode: multiply;
    }
    .navbar-brand-circle {
      width: 85px;
      height: 85px;
      flex-shrink: 0;
      border-radius: 50%;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.12);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .navbar-brand-circle:hover { transform: scale(1.05); }
    .logo-text{ display:block; font-weight: 900; font-size: 13px; letter-spacing:.4px; text-align: right; }
    .logo-text span.zimbabwe { color: #000; }
    .logo-text span.media { color: #2e7d32; }
    .logo-text span.commission { color: #000; }
    .logo-sub{ display:block; font-size: 11px; opacity:.75; margin-top:2px; text-align: right; }

    .sidebar-menu{ list-style:none; padding: 12px; margin:0; flex:1; overflow-y:auto; }
    .sidebar-menu li a{ display:flex; align-items:center; gap:10px; padding: 10px 12px; border-radius: 12px; color: rgba(255,255,255,.86); text-decoration:none; font-weight: 800; font-size: 12px; }
    .sidebar-menu li a i{ font-size: 16px; }
    .sidebar-menu li.active a, .sidebar-menu li a:hover{ background: rgba(255,255,255,.10); color:#fff; }
    .sidebar-user{ padding: 14px 14px; border-top: 1px solid rgba(255,255,255,.08); display:flex; gap:10px; align-items:center; margin-top:auto; background: rgba(0,0,0,.10); }
    .sidebar-user img{ width: 38px; height:38px; border-radius: 999px; }

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
    .icon-badge{ position:absolute; top: 6px; right: 6px; font-size: 10px; padding: 2px 6px; border-radius: 999px; color:#fff; font-weight: 900; }
    .badge-blue{ background:#2563eb; }
    .badge-red{ background:#ef4444; }
    .hide-caret::after{ display:none !important; }
    .logout-pill{ height: 40px; border-radius: 999px; border: 1px solid var(--border); background: #111827; color:#fff; font-weight: 900; font-size: 12px; padding: 0 14px; display:inline-flex; align-items:center; gap: 8px; }

    /* ====== ZMC dashboard components (cards, tables, modals) ====== */
    .fw-black { font-weight: 900; }
    .zmc-card,
    .modal-content{ border-radius: 0 !important; }
    .form-control,
    .btn:not(.zmc-icon-btn){ border-radius: 8px; }
    .zmc-card{ background:#ffffff !important; border:1px solid var(--zmc-border); padding:18px; transition:.2s; }
    .icon-box{ width:40px;height:40px; display:flex;align-items:center;justify-content:center; font-size:20px; }
    .zmc-mini-table thead th{ background: var(--zmc-soft); color: #475569; font-size: 11px; font-weight: 900; padding: 12px 12px; border-bottom: 2px solid #edf2f7; white-space: nowrap; }
    .zmc-mini-table tbody td{ padding: 12px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }
    .zmc-pill{ padding:5px 12px; font-size:12px; font-weight:800; display:inline-flex; align-items:center; gap:8px; border:1px solid var(--zmc-border); background:#fff; white-space:nowrap; border-radius: 999px; }
    .zmc-pill-dark{ background:#111827; color:#fff; border-color:#111827; }
    .zmc-action-strip{ display:flex; gap:3px; justify-content:flex-end; }
    .zmc-icon-btn{ width:32px; height:32px; padding:0; border-radius:999px !important; display:inline-flex; align-items:center; justify-content:center; border-width:1px; background:#fff; line-height:1; }
    .zmc-icon-btn i{ font-size:15px; line-height:1; }
    .zmc-icon-btn:hover{ border-color: var(--zmc-accent-dark) !important; color: var(--zmc-accent-dark) !important; }
    .zmc-modal-header{ position:relative; background:#ffffff !important; border-bottom: 1px solid rgba(15,23,42,.10) !important; padding: 14px 16px; }
    .zmc-modal-header::before{ content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background: var(--zmc-accent-dark); }
    .zmc-modal-title{ font-weight: 900; color: var(--zmc-text); font-size: 15px; line-height: 1.2; }
    .zmc-modal-sub{ font-size: 12px; font-weight: 700; color: var(--zmc-muted); margin-top: 2px; }
    .zmc-modal-footer{ border-top: 1px solid rgba(15,23,42,.08) !important; }
    .zmc-lbl{ font-size: 11px; font-weight: 900; color: #64748b; margin-bottom: 6px; }
    .zmc-input{ font-size: 12px !important; padding: 7px 10px !important; line-height: 1.25 !important; }
    .zmc-input[readonly]{ background:#fff !important; }
    .zmc-mdl-block{ padding:0; margin-bottom: 16px; }
    .zmc-mdl-title{ display:flex; align-items:center; gap:10px; font-size:12px; font-weight:900; color: var(--zmc-text); padding: 10px 0 10px; border-bottom: 1px solid rgba(15,23,42,.10); margin-bottom: 12px; }
    .zmc-mdl-title i{ color: var(--zmc-accent-dark); }
    .zmc-table-lite thead th{ background: rgba(248,250,252,.95); font-size: 11px; font-weight: 800; padding: 10px 10px; border-bottom: 2px solid rgba(15,23,42,.08) !important; border-top: none !important; }
    .zmc-table-lite tbody td{ font-size: 12px; padding: 10px 10px; border-top: none !important; border-bottom: 1px solid rgba(15,23,42,.06) !important; vertical-align: middle; }
    .zmc-table-lite{ margin-bottom:0; }


    /* ====== Applicant Forms ====== */
    .form-container{background:#fff;border:1px solid var(--border);box-shadow:var(--shadow);border-radius:16px;overflow:hidden;}
    .form-header{padding:18px 18px;background:linear-gradient(180deg, rgba(250,204,21,.18), rgba(255,255,255,0));border-bottom:1px solid var(--border);}
    .form-header h1{font-size:16px;font-weight:900;margin:0 0 6px;color:var(--text);}
    .form-header p{margin:0;font-size:12px;color:var(--muted);font-weight:700;}
    .form-steps-container{padding:16px 18px 18px;}
    .step-progress{margin-bottom:14px;}
    .step-progress-bar{display:flex;gap:10px;flex-wrap:wrap;}
    .step{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--border);border-radius:14px;background:#fff;cursor:pointer;user-select:none;}
    .step .step-number{width:28px;height:28px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:900;background:rgba(250,204,21,.22);color:#92400e;}
    .step .step-label{font-size:12px;font-weight:900;color:#334155;}
    .step.active{border-color:rgba(234,179,8,.65);box-shadow:0 10px 25px rgba(234,179,8,.12);}
    .step.active .step-number{background:var(--accent);color:#111827;}
    .step-content{display:none;}
    .step-content.active{display:block;}
    .step-title{font-size:14px;font-weight:900;color:#0f172a;margin:6px 0 10px;}
    .current-step-info{font-size:12px;font-weight:700;color:var(--muted);padding:10px 12px;border:1px dashed rgba(15,23,42,.18);background:rgba(248,250,252,.9);border-radius:14px;margin-bottom:12px;}
    .form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:12px;}
    .form-row .form-field{min-width:0;}
    @media (max-width:768px){.form-row{grid-template-columns:1fr;}.step{flex:1 1 auto;}.app-type-cards{grid-template-columns:1fr;}}
    .form-label{font-size:11px;font-weight:900;color:#475569;}
    .form-label.required::after{content:' *';color:#ef4444;font-weight:900;}
    .checkbox-group{display:flex;gap:10px;flex-wrap:wrap;}
    .checkbox-item{border:1px solid var(--border);border-radius:12px;padding:10px 12px;background:#fff;display:flex;gap:8px;align-items:center;}
    .app-type-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
    .app-type-card{border:1px solid var(--border);border-radius:16px;padding:14px;background:#fff;cursor:pointer;transition:.15s;}
    .app-type-card:hover{transform:translateY(-1px);box-shadow:0 10px 25px rgba(15,23,42,.08);}
    .app-type-card.selected{border-color:rgba(234,179,8,.7);box-shadow:0 10px 25px rgba(234,179,8,.15);}
    .app-type-card i{font-size:22px;color:#111827;}
    .app-type-card h4{font-size:13px;font-weight:900;margin:8px 0 6px;}
    .app-type-card p{font-size:12px;color:var(--muted);font-weight:700;margin:0;}
    .form-buttons{display:flex;justify-content:space-between;gap:10px;margin-top:14px;}
    .upload-area{border:1px dashed rgba(15,23,42,.18);border-radius:16px;padding:14px;background:rgba(248,250,252,.8);text-align:left;}
    .upload-area h5{font-size:12px;font-weight:900;margin:8px 0 3px;}
    .upload-area p{font-size:11px;color:var(--muted);font-weight:700;margin:0 0 8px;}
    .upload-btn{border:1px solid var(--border);background:#fff;border-radius:12px;padding:8px 10px;font-size:12px;font-weight:900;}

  </style>



  <style>
    :root{
      --zmc-green:#388e3c;
      --zmc-green-dark:#2e7d32;
    }
    .btn-primary, .btn-success{
      background: var(--zmc-green) !important;
      border-color: var(--zmc-green) !important;
    }
    .btn-primary:hover, .btn-success:hover{
      background: var(--zmc-green-dark) !important;
      border-color: var(--zmc-green-dark) !important;
    }
    .badge.bg-success-subtle{ background: rgba(45,90,39,.15) !important; }
    .text-success{ color: var(--zmc-green) !important; }
    body{
      background: url('<?php echo e(asset("zmc_building.png")); ?>') no-repeat center center fixed !important;
      background-size: cover !important;
    }
    body::before{
      content: "";
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: linear-gradient(135deg, rgba(240, 247, 240, 0.88) 0%, rgba(220, 237, 220, 0.92) 100%);
      z-index: -1;
    }

    .topbar{ background: url('<?php echo e(asset("zmc_building.png")); ?>') center center / cover no-repeat !important; border-bottom: none !important; position: sticky; top: 0; z-index: 1030; }
    .topbar::before{ content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(46, 125, 50, 0.82), rgba(27, 94, 32, 0.85)); z-index: 0; }
    .topbar > *{ position: relative; z-index: 1; }
    .topbar .icon-btn{ background: rgba(255,255,255,.1) !important; border-color: rgba(255,255,255,.2) !important; }
    .topbar .icon-btn i{ color: #fff !important; }
    .topbar .logout-pill{ background: rgba(255,255,255,.15) !important; border-color: rgba(255,255,255,.25) !important; }
    .topbar-toggle{ background: rgba(255,255,255,.1) !important; border-color: rgba(255,255,255,.2) !important; }
    .topbar-toggle i{ color: #fff !important; }

    /* Dark theme overrides */
    body.theme-dark{ color-scheme: dark; }
    body.theme-dark::before{ background: rgba(6, 12, 8, 0.92) !important; }
    body.theme-dark .content{ color: #e2e8f0; }
    body.theme-dark .topbar{ border-bottom: 1px solid rgba(255,255,255,0.1) !important; }
    body.theme-dark .card, body.theme-dark .zmc-card{ background: rgba(15, 23, 42, 0.95) !important; color: #e2e8f0; border-color: rgba(148,163,184,0.15) !important; }
    body.theme-dark .card-header, body.theme-dark .card-footer{ background: rgba(30, 41, 59, 0.5) !important; border-color: rgba(148,163,184,0.15) !important; }
    body.theme-dark .card .text-muted, body.theme-dark .zmc-card .text-muted, body.theme-dark .text-muted{ color: #94a3b8 !important; }
    body.theme-dark .table{ color: #e2e8f0; border-color: rgba(148,163,184,0.1) !important; }
    body.theme-dark .table thead th{ background: rgba(30,41,59,1) !important; color: #cbd5e1 !important; border-bottom: 2px solid rgba(148,163,184,0.2) !important; }
    body.theme-dark .table tbody td{ border-color: rgba(148,163,184,0.1) !important; }
    body.theme-dark .bg-light, body.theme-dark .bg-white{ background: rgba(30, 41, 59, 0.9) !important; }
    body.theme-dark .dropdown-menu{ background: #1e293b; color: #e2e8f0; border: 1px solid rgba(148,163,184,0.2); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    body.theme-dark .dropdown-item{ color: #e2e8f0; }
    body.theme-dark .dropdown-item:hover{ background: rgba(148,163,184,0.1); color: #fff; }
    body.theme-dark .dropdown-divider{ border-top-color: rgba(148,163,184,0.1); }
    body.theme-dark .form-container{ background: rgba(15, 23, 42, 0.95) !important; border-color: rgba(148,163,184,0.15) !important; }
    body.theme-dark .form-header{ background: linear-gradient(180deg, rgba(250,204,21,.05), rgba(255,255,255,0)) !important; border-color: rgba(148,163,184,0.15) !important; }
    body.theme-dark .form-control, body.theme-dark .form-select, body.theme-dark .form-check-input{ background: rgba(30,41,59,0.8) !important; color: #f1f5f9 !important; border-color: rgba(148,163,184,0.2) !important; }
    body.theme-dark .form-control:focus, body.theme-dark .form-select:focus{ border-color: var(--zmc-accent) !important; box-shadow: 0 0 0 0.25rem rgba(251, 191, 36, 0.1) !important; }
    body.theme-dark .form-label, body.theme-dark .form-check-label{ color: #cbd5e1 !important; }
    body.theme-dark .modal-content{ background: #111827 !important; color: #e2e8f0 !important; border: 1px solid rgba(148,163,184,0.2); }
    body.theme-dark .modal-header, body.theme-dark .modal-footer{ border-color: rgba(148,163,184,0.1) !important; }
    body.theme-dark h1, body.theme-dark h2, body.theme-dark h3, body.theme-dark h4, body.theme-dark h5, body.theme-dark h6{ color: #f8fafc !important; }
    body.theme-dark .text-dark, body.theme-dark .text-black, body.theme-dark .fw-bold{ color: #f1f5f9 !important; }
    body.theme-dark .alert{ background: rgba(30,41,59,0.9) !important; border-color: rgba(148,163,184,0.1) !important; color: #cbd5e1 !important; }
    body.theme-dark .list-group-item{ background: rgba(30, 41, 59, 0.4) !important; color: #e2e8f0 !important; border-color: rgba(148,163,184,0.1) !important; }
    body.theme-dark .list-group-item-action:hover{ background: rgba(148, 163, 184, 0.1) !important; color: #fff !important; }
    body.theme-dark .list-group-item.active{ background: var(--zmc-accent) !important; border-color: var(--zmc-accent) !important; color: #000 !important; }
    body.theme-dark hr{ border-top-color: rgba(148,163,184,0.1); opacity: 0.5; }
    body.theme-dark .placeholder-light::placeholder{ color: rgba(255,255,255,0.4) !important; }
    body.theme-dark .bg-primary-subtle, body.theme-dark .bg-info-subtle, body.theme-dark .bg-warning-subtle, body.theme-dark .bg-danger-subtle, body.theme-dark .bg-success-subtle{ background-opacity: 0.1 !important; }

  </style>

  
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

  <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<?php
  $theme = auth()->check() ? (auth()->user()->theme ?? 'light') : 'light';
?>

<body class="theme-<?php echo e($theme); ?>">
  <div class="app">
    
    <?php
      $user = auth()->user();
      $isStaffRoute = request()->is('staff/*') || request()->routeIs('staff.*') || request()->is('admin/*') || request()->routeIs('admin.*');
      $showStaffSidebar = $isStaffRoute || ($user && ($user->hasRole('accreditation_officer') || $user->hasRole('accounts_payments') || $user->hasRole('registrar') || $user->hasRole('production') || $user->hasRole('auditor') || $user->hasRole('super_admin') || $user->hasRole('it_admin') || $user->hasRole('director')));
    ?>

    <?php if($showStaffSidebar): ?>
      <?php echo $__env->make('layouts.sidebar_staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
      <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <main class="main">
      <?php echo $__env->make('layouts.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <section class="content">
        <?php echo $__env->yieldContent('content'); ?>
      </section>

      
      <?php if(!request()->is('staff/*') && !request()->is('admin/*')): ?>
        <?php echo $__env->make('layouts.chatbot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php endif; ?>
    </main>
  </div>

  
  <?php echo $__env->yieldPushContent('scripts'); ?>


  
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Toggle handling
      const btn = document.getElementById('sidebarToggle');
      if(btn) {
        btn.addEventListener('click', function(){
          document.querySelector('.app')?.classList.toggle('sidebar-collapsed');
        });
      }

      // Scroll persistence
      const sidebarMenu = document.querySelector('.sidebar-menu');
      if (sidebarMenu) {
        // Restore position
        const savedScroll = sessionStorage.getItem('sidebar-scroll');
        if (savedScroll) {
          sidebarMenu.scrollTop = parseInt(savedScroll, 10);
        }

        // Save position on scroll
        sidebarMenu.addEventListener('scroll', function() {
          sessionStorage.setItem('sidebar-scroll', sidebarMenu.scrollTop);
        });
      }
    });
  </script>
  <script>
  (function(){
    var p='_auth_token', u=new URLSearchParams(window.location.search), t=u.get(p);
    if(t) localStorage.setItem(p,t);
    var s=localStorage.getItem(p);
    if(!s) return;
    document.addEventListener('click',function(e){
      var a=e.target.closest('a');
      if(!a||!a.href) return;
      try{
        var l=new URL(a.href);
        if(l.origin===window.location.origin && !l.searchParams.has(p)){
          l.searchParams.set(p,s);
          a.href=l.toString();
        }
      }catch(x){}
    },true);
    document.addEventListener('submit',function(e){
      var f=e.target;
      if(f.tagName==='FORM' && !f.querySelector('input[name="'+p+'"]')){
        var i=document.createElement('input');
        i.type='hidden'; i.name=p; i.value=s;
        f.appendChild(i);
      }
    },true);
    var origFetch=window.fetch;
    window.fetch=function(url,opts){
      if(typeof url==='string'){
        try{
          var u2=new URL(url,window.location.origin);
          if((u2.origin===window.location.origin||url.startsWith('/'))&&!u2.searchParams.has(p)){
            u2.searchParams.set(p,s);
            url=u2.toString();
          }
        }catch(x){
          if(url.startsWith('/')){var sep=url.indexOf('?')>=0?'&':'?';url+=sep+p+'='+s;}
        }
      }
      return origFetch.call(this,url,opts);
    };
    var origOpen=XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open=function(method,url){
      if(typeof url==='string'){
        try{
          var u3=new URL(url,window.location.origin);
          if((u3.origin===window.location.origin||url.startsWith('/'))&&!u3.searchParams.has(p)){
            u3.searchParams.set(p,s);
            url=u3.toString();
          }
        }catch(x){
          if(url.startsWith('/')){var sep=url.indexOf('?')>=0?'&':'?';url+=sep+p+'='+s;}
        }
      }
      var args=Array.prototype.slice.call(arguments);args[1]=url;
      return origOpen.apply(this,args);
    };
  })();
  </script>
</body>
</html>

<?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/layouts/portal.blade.php ENDPATH**/ ?>