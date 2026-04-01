<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'ZMC Portal')</title>

  <!-- Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Remix Icons -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

  {{-- IMPORTANT: This is the full CSS from the final code (unchanged). --}}
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

    body{
      font-family:'Inter',sans-serif;
      background-color:var(--bg-color);
      margin:0;
      overflow-x:hidden;
    }

    /* --- SIDEBAR --- */
    .vertical-menu{
      width:var(--sidebar-width);
      height:100vh;
      background:var(--sidebar-bg);
      position:fixed;
      bottom:0;
      top:0;
      left:0;
      z-index:1001;
      color:#ffffff;
      transition:.3s ease;
      box-shadow:2px 0 10px rgba(0,0,0,0.1);
    }

    .navbar-brand-box{
      padding:20px 20px 15px;
      text-align:left;
      display:flex;
      align-items:center;
      gap:12px;
      border-bottom:1px solid rgba(255,255,255,0.1);
      justify-content: space-between;
    }

    .navbar-brand-box img{height:38px;}
    .logo-text{
      color:#fff;
      font-weight:800;
      font-size:16px;
      letter-spacing:0.5px;
      text-transform:uppercase;
      text-align: right;
    }
    .logo-sub{
      font-size:11px;
      color:var(--zmc-yellow);
      font-weight:600;
      display:block;
      margin-top:2px;
      text-align: right;
    }

    .sidebar-menu{
      padding:15px 0;
      list-style:none;
      margin:0;
      height:calc(100vh - 180px);
      overflow-y:auto;
    }

    .sidebar-menu li{
      padding:12px 20px;
      display:flex;
      align-items:center;
      font-size:14px;
      cursor:pointer;
      transition:0.3s;
      color:#8c8c8c;
      font-weight:500;
      border-left:3px solid transparent;
    }

    .sidebar-menu li i{
      margin-right:12px;
      font-size:18px;
      width:24px;
      text-align:center;
    }
    .sidebar-menu li:hover{
      color:var(--zmc-yellow);
      background:rgba(250,204,21,0.05);
    }
    .sidebar-menu li.active{
      color:var(--zmc-yellow);
      background:rgba(250,204,21,0.08);
      border-left:3px solid var(--zmc-yellow);
    }

    .sidebar-user{
      position:absolute;
      bottom:0;
      width:100%;
      padding:15px 20px;
      background:rgba(255,255,255,0.03);
      display:flex;
      align-items:center;
      gap:12px;
    }

    .sidebar-user img{width:38px;height:38px;border-radius:50%;}

    /* --- TOPBAR --- */
    .topbar{
      height:var(--topbar-height);
      background:#fff;
      position:fixed;
      left:var(--sidebar-width);
      right:0;
      top:0;
      z-index:1000;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:0 20px;
      box-shadow:0 1px 3px rgba(0,0,0,0.05);
      transition:.3s ease;
    }

    .topbar-left{display:flex;align-items:center;gap:15px;}
    .topbar-title{
      font-weight:800;
      font-size:15px;
      letter-spacing:.5px;
      text-transform:uppercase;
      color:#111827;
      white-space:nowrap;
    }

    .topbar-right{display:flex;align-items:center;gap:12px;}

    /* --- MAIN CONTENT --- */
    .main-content{
      margin-left:var(--sidebar-width);
      padding-top:var(--topbar-height);
      min-height:100vh;
      transition:.3s ease;
    }
    .page-content{
      padding:20px;
      max-width:1200px;
      margin:0 auto;
    }

    /* --- PAGES --- */
    .page{
      display:none;
      animation:fadeIn 0.3s ease;
    }
    .page.active{display:block;}
    @keyframes fadeIn{
      from{opacity:0;transform:translateY(10px);}
      to{opacity:1;transform:translateY(0);}
    }

    /* --- FORM CONTAINER --- */
    .form-container{
      background:#fff;
      border-radius:8px;
      box-shadow:0 2px 8px rgba(0,0,0,0.08);
      padding:0;
      margin-top:20px;
    }
    .form-header{
      padding:24px 30px;
      border-bottom:1px solid var(--border-color);
      background:#fff;
    }
    .form-header h1{
      font-size:24px;
      font-weight:700;
      color:#111827;
      margin-bottom:8px;
    }
    .form-header p{
      color:var(--text-muted);
      font-size:14px;
      margin:0;
      line-height:1.5;
    }
    .form-steps-container{padding:30px;}

    /* STEP PROGRESS */
    .step-progress{margin-bottom:30px;position:relative;}
    .step-progress-bar{display:flex;justify-content:space-between;position:relative;}
    .step-progress-bar::before{
      content:'';
      position:absolute;
      top:15px;
      left:0;
      right:0;
      height:2px;
      background:var(--border-color);
      z-index:1;
    }

    .step{
      position:relative;
      z-index:2;
      text-align:center;
      background:#fff;
      padding:0 10px;
    }

    .step-number{
      width:32px;height:32px;border-radius:50%;
      background:#fff;border:2px solid var(--border-color);
      display:flex;align-items:center;justify-content:center;
      margin:0 auto 5px;font-size:14px;font-weight:600;color:var(--text-muted);
    }
    .step.active .step-number{
      background:var(--zmc-yellow);
      border-color:var(--zmc-yellow);
      color:#000;
    }
    .step.completed .step-number{
      background:#facc15;
      border-color:#facc15;
      color:#fff;
    }
    .step.completed .step-number i{font-size:16px;}
    .step-label{font-size:12px;font-weight:600;color:var(--text-muted);}
    .step.active .step-label{color:#111827;}

    /* STEP CONTENT */
    .step-content{display:none;}
    .step-content.active{display:block;}
    .step-title{
      font-size:20px;font-weight:600;color:#111827;
      margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid var(--border-color);
    }
    .current-step-info{
      font-size:14px;color:var(--text-muted);
      margin-bottom:20px;padding:10px;background:#f8f9fa;border-radius:6px;
    }

    /* FORM ELEMENTS */
    .form-row{
      display:grid;
      grid-template-columns:repeat(2, 1fr);
      gap:20px;
      margin-bottom:20px;
    }
    .form-field{margin-bottom:15px;}
    .form-label{
      display:block;font-size:14px;font-weight:600;color:#111827;margin-bottom:6px;
    }
    .form-label.required::after{content:'*';color:#ef4444;margin-left:4px;}
    .form-control{
      width:100%;padding:10px 12px;border:1px solid var(--border-color);
      border-radius:6px;font-size:14px;color:#111827;transition:all 0.2s;background:#fff;
    }
    .form-control:focus{
      outline:none;border-color:var(--zmc-yellow);
      box-shadow:0 0 0 3px rgba(250,204,21,0.15);
    }
    .form-control::placeholder{color:#9ca3af;}
    .form-hint{font-size:12px;color:var(--text-muted);margin-top:4px;}

    /* CHECKBOX AND RADIO */
    .checkbox-group{display:flex;gap:20px;margin-top:10px;flex-wrap:wrap;}
    .checkbox-item{display:flex;align-items:center;gap:8px;}
    .checkbox-item input[type="checkbox"],
    .checkbox-item input[type="radio"]{width:18px;height:18px;margin:0;cursor:pointer;}
    .checkbox-item label{font-size:14px;color:#111827;cursor:pointer;margin:0;}

    /* UPLOAD AREA */
    .upload-area{
      border:2px dashed var(--border-color);
      border-radius:6px;
      padding:22px;
      text-align:center;
      cursor:pointer;
      transition:all 0.3s;
      background:#f9fafb;
      margin-top:10px;
    }
    .upload-area:hover{border-color:var(--zmc-yellow);background:rgba(250,204,21,0.02);}
    .upload-area i{font-size:40px;color:var(--zmc-yellow);margin-bottom:10px;}
    .upload-area h5{font-size:16px;font-weight:600;color:#111827;margin-bottom:6px;}
    .upload-area p{color:var(--text-muted);font-size:13px;margin-bottom:12px;}
    .upload-btn{
      background:var(--zmc-yellow);
      color:#000;
      font-weight:600;
      padding:8px 20px;
      border-radius:4px;
      border:none;
      cursor:pointer;
      font-size:13px;
    }
    .uploaded-files{margin-top:12px;}
    .uploaded-file{
      display:flex;align-items:center;justify-content:space-between;
      padding:10px;border:1px solid var(--border-color);
      border-radius:4px;margin-bottom:8px;background:#fff;
    }
    .file-info{display:flex;align-items:center;gap:10px;}
    .file-icon{color:var(--zmc-yellow);font-size:18px;}
    .file-name{font-weight:500;color:#111827;font-size:13px;}
    .file-size{font-size:11px;color:var(--text-muted);}
    .file-remove{background:none;border:none;color:#dc2626;cursor:pointer;padding:5px;}

    /* FORM BUTTONS */
    .form-buttons{
      display:flex;justify-content:space-between;
      padding-top:30px;margin-top:30px;border-top:1px solid var(--border-color);
    }
    .btn{
      padding:10px 24px;border-radius:6px;font-weight:600;
      font-size:14px;cursor:pointer;transition:all 0.2s;border:none;
    }
    .btn-secondary{background:#f3f4f6;color:#374151;}
    .btn-secondary:hover{background:#e5e7eb;}
    .btn-primary{background:var(--zmc-yellow);color:#000;}
    .btn-primary:hover{
      background:var(--zmc-yellow-dark);
      transform:translateY(-1px);
      box-shadow:0 4px 12px rgba(250,204,21,0.2);
    }

    /* DASHBOARD CARDS */
    .dashboard-cards{
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
      gap:20px;
      margin-bottom:30px;
    }
    .dashboard-card{
      background:#fff;border-radius:8px;padding:20px;
      box-shadow:0 2px 4px rgba(0,0,0,0.05);
      border-left:4px solid var(--zmc-yellow);
    }
    .dashboard-card h5{
      font-size:14px;font-weight:600;color:#6b7280;margin-bottom:10px;
    }
    .dashboard-card .count{
      font-size:32px;font-weight:700;color:#111827;margin-bottom:5px;
    }
    .dashboard-card .trend{
      font-size:12px;color:#facc15;display:flex;align-items:center;gap:4px;
    }

    /* NOTICES */
    .notice-list{background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;}
    .notice-item{padding:15px 0;border-bottom:1px solid var(--border-color);}
    .notice-item:last-child{border-bottom:none;}
    .notice-date{font-size:12px;color:var(--text-muted);margin-bottom:5px;}

    /* HOW TO GUIDE */
    .guide-step{
      display:flex;align-items:flex-start;
      margin-bottom:25px;padding-bottom:25px;border-bottom:1px solid var(--border-color);
    }
    .step-number-large{
      background:var(--zmc-yellow);color:#000;
      width:36px;height:36px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      font-weight:800;font-size:16px;margin-right:15px;flex-shrink:0;
    }

    /* REPEATABLE TABLES */
    .mini-table{
      border:1px solid var(--border-color);
      border-radius:8px;
      overflow:hidden;
    }
    .mini-table table{margin:0;}
    .mini-table thead th{
      background:#f8f9fa;
      color:#111827;
      font-weight:700;
      font-size:13px;
      border-bottom:1px solid var(--border-color);
    }
    .mini-table td, .mini-table th{
      padding:10px;
      vertical-align:middle;
      font-size:13px;
    }

    /* CHATBOT WIDGET */
    .chatbot-fab{
      position:fixed;
      right:22px;
      bottom:22px;
      z-index:2000;
      width:56px;height:56px;border-radius:50%;
      background:var(--zmc-yellow);
      display:flex;align-items:center;justify-content:center;
      box-shadow:0 10px 25px rgba(0,0,0,0.18);
      cursor:pointer;
      border:0;
    }
    .chatbot-fab i{font-size:24px;color:#000;}
    .chatbot-panel{
      position:fixed;
      right:22px;
      bottom:90px;
      z-index:2000;
      width:360px;
      max-width:calc(100vw - 44px);
      height:520px;
      max-height:calc(100vh - 140px);
      background:#fff;
      border-radius:14px;
      box-shadow:0 18px 40px rgba(0,0,0,0.2);
      overflow:hidden;
      display:none;
      border:1px solid rgba(0,0,0,0.06);
    }
    .chatbot-panel.active{display:flex;flex-direction:column;}
    .chatbot-header{
      padding:14px 16px;
      background:#111827;
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }
    .chatbot-header .title{
      font-weight:800;
      letter-spacing:.4px;
      font-size:13px;
      text-transform:uppercase;
    }
    .chatbot-header .sub{
      font-size:12px;
      color:rgba(255,255,255,0.8);
    }
    .chatbot-body{
      padding:14px;
      background:#f8fafc;
      flex:1;
      overflow:auto;
    }
    .bubble{
      max-width:85%;
      padding:10px 12px;
      border-radius:12px;
      margin-bottom:10px;
      font-size:13px;
      line-height:1.35;
    }
    .bubble.user{background:#111827;color:#fff;margin-left:auto;border-bottom-right-radius:4px;}
    .bubble.bot{background:#fff;color:#111827;border:1px solid rgba(0,0,0,0.06);border-bottom-left-radius:4px;}
    .chatbot-footer{
      padding:10px;
      border-top:1px solid var(--border-color);
      display:flex;
      gap:8px;
      background:#fff;
    }
    .chatbot-footer input{
      flex:1;
      border:1px solid var(--border-color);
      border-radius:10px;
      padding:10px 12px;
      font-size:13px;
      outline:none;
    }
    .chatbot-footer button{
      background:var(--zmc-yellow);
      border:0;
      border-radius:10px;
      padding:10px 12px;
      font-weight:800;
      cursor:pointer;
    }
    .sidebar-menu li a{
  display:flex;
  align-items:center;
  gap:12px;
  color:inherit;
  text-decoration:none;
  width:100%;
}


    /* COMMUNICATION */
    .comm-grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:20px;
    }
    @media (max-width: 992px){
      .comm-grid{grid-template-columns:1fr;}
    }

    /* RESPONSIVE */
    @media (max-width:768px){
      .form-row{grid-template-columns:1fr;}
      .form-steps-container{padding:20px;}
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

   {{-- Sidebar + Topbar --}}
  @include('layouts.sidebar')
  @include('layouts.topbar')

  {{-- MAIN --}}
  <div class="main-content">
    <div class="page-content">
      @yield('content')
    </div>
  </div>

  {{-- Chatbot --}}
  @include('layouts.chatbot')


  <!-- Bootstrap JS (for modals) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  {{-- IMPORTANT: Full final JS, unchanged (but moved into layout). --}}
  <script>
    // Global variables
    let currentPage = 'home';

    // AP1 steps
    let ap1CurrentStep = 1;
    const AP1_MAX_STEPS = 7;

    // AP5 steps
    let ap5CurrentStep = 1;
    const AP5_MAX_STEPS = 5;

    // DOM Elements
    const pages = document.querySelectorAll('.page');
    const sidebarItems = document.querySelectorAll('.sidebar-menu li');
    const pageTitle = document.getElementById('pageTitle');

    // Page titles mapping
    const pageTitles = {
      'home': 'HOME - REGISTRATION TRACKER',
      'new-registration': 'NEW REGISTRATION (AP1)',
      'renewal': 'RENEWAL / REPLACEMENT (AP5)',
      'payment-history': 'PAYMENT HISTORY',
      'notices': 'NOTICES & EVENTS',
      'how-to': 'HOW TO GET REGISTERED',
      'org-profile': 'ORGANIZATION PROFILE',
      'communication': 'COMMUNICATION',
      'settings': 'SETTINGS'
    };

    document.addEventListener('DOMContentLoaded', function() {
      setupEventListeners();
      setDefaultDates();
      setupFileUploads();
      initChatbot();
      renderCommLog();
      updateNavButtons();
    });

    function setupEventListeners() {
      // Sidebar navigation
      sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
          navigateToPage(this.dataset.page);
        });
      });

      // Home quick actions
      document.getElementById('newRegBtnHome')?.addEventListener('click', () => navigateToPage('new-registration'));
      document.getElementById('goRenewalHome')?.addEventListener('click', () => navigateToPage('renewal'));

      // Guide CTA
      document.getElementById('startRegistrationFromGuide')?.addEventListener('click', () => navigateToPage('new-registration'));

      // Topbar quick buttons
      document.getElementById('openChatbotBtn')?.addEventListener('click', () => openChatbot(true));
      document.getElementById('quickComposeBtn')?.addEventListener('click', () => navigateToPage('communication'));

      // AP1 navigation
      document.getElementById('ap1-next-btn')?.addEventListener('click', () => {
        if (validateAP1Step()) navigateAP1Step(1);
      });
      document.getElementById('ap1-prev-btn')?.addEventListener('click', () => navigateAP1Step(-1));

      // AP5 navigation
      document.getElementById('ap5-next-btn')?.addEventListener('click', () => {
        if (validateAP5Step()) navigateAP5Step(1);
      });
      document.getElementById('ap5-prev-btn')?.addEventListener('click', () => navigateAP5Step(-1));

      // AP1 dynamic rows
      document.getElementById('addTitleRow')?.addEventListener('click', () => addTitleRow());
      document.getElementById('addDirectorRow')?.addEventListener('click', () => addDirectorRow());
      document.getElementById('addManagerRow')?.addEventListener('click', () => addManagerRow());

      // AP5 changes toggle
      document.querySelectorAll('input[name="ap5-changes"]').forEach(r => {
        r.addEventListener('change', () => {
          const wrap = document.getElementById('ap5-changes-details-wrap');
          const yes = document.getElementById('ap5-changes-yes').checked;
          wrap.style.display = yes ? 'block' : 'none';
          const textarea = wrap.querySelector('textarea');
          if (textarea) textarea.toggleAttribute('required', yes);
        });
      });

      // Download/Preview buttons (demo)
      document.getElementById('downloadAP1')?.addEventListener('click', () => alert('Use your backend/static storage to serve AP1 PDF for download.'));
      document.getElementById('downloadAP5')?.addEventListener('click', () => alert('Use your backend/static storage to serve AP5 DOC for download.'));

      // Preview AP1 modal (iframe src set here)
      document.getElementById('previewAP1')?.addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('ap1PreviewModal'));
        // In production: set iframe src to your hosted PDF URL
        document.getElementById('ap1PdfFrame').src = '';
        modal.show();
      });

      // Preview AP5 modal
      document.getElementById('previewAP5')?.addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('ap5PreviewModal'));
        modal.show();
      });

      // Org profile edit
      document.getElementById('editOrgProfileBtn')?.addEventListener('click', function() {
        const inputs = document.querySelectorAll('#org-profile-page input');
        const isReadOnly = inputs[0].readOnly;
        inputs.forEach(i => i.readOnly = !isReadOnly);
        this.innerHTML = isReadOnly ? '<i class="ri-save-line me-2"></i>Save Profile' : '<i class="ri-edit-line me-2"></i>Edit Profile';
        if (!isReadOnly) alert('Demo: Profile updated successfully!');
      });

      // Communication actions
      document.getElementById('sendSupportEmailBtn')?.addEventListener('click', () => {
        navigateToPage('communication');
        document.getElementById('emailSubject')?.focus();
      });

      document.getElementById('logEmailBtn')?.addEventListener('click', () => {
        const to = document.getElementById('emailTo').value.trim();
        const subject = document.getElementById('emailSubject').value.trim();
        const body = document.getElementById('emailBody').value.trim();
        if(!to || !subject || !body){
          alert('Please complete To, Subject, and Message.');
          return;
        }
        saveCommMessage({to, subject, body});
        document.getElementById('emailBody').value = '';
        renderCommLog();
        alert('Saved to Communication Log (demo).');
      });

      document.getElementById('openMailClientBtn')?.addEventListener('click', () => {
        const to = encodeURIComponent(document.getElementById('emailTo').value.trim());
        const subject = encodeURIComponent(document.getElementById('emailSubject').value.trim());
        const body = encodeURIComponent(document.getElementById('emailBody').value.trim());
        const link = `mailto:${to}?subject=${subject}&body=${body}`;
        window.location.href = link;
      });

      document.getElementById('clearCommLog')?.addEventListener('click', () => {
        if(confirm('Clear communication log stored in this browser?')){
          localStorage.removeItem('zmc_comm_log');
          renderCommLog();
        }
      });
    }

    function setDefaultDates() {
      const today = new Date().toISOString().split('T')[0];
      document.querySelectorAll('input[type="date"]').forEach(input => {
        if (!input.value && !input.hasAttribute('data-no-default')) input.value = today;
      });
    }

    // Upload widget
    function setupFileUploads() {
      document.querySelectorAll('.upload-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const input = this.parentElement.querySelector('input[type="file"]');
          if (input) input.click();
        });
      });

      document.querySelectorAll('.upload-area').forEach(area => {
        area.addEventListener('click', function(e) {
          if (e.target.closest('.upload-btn')) return;
          const input = this.querySelector('input[type="file"]');
          if (input) input.click();
        });
      });

      document.querySelectorAll('.upload-area input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if(!file) return;

          const area = this.closest('.upload-area');
          area.style.borderColor = '#facc15';
          area.style.backgroundColor = 'rgba(250, 204, 21, 0.05)';

          const uploadedFiles = area.parentElement.querySelector('.uploaded-files');
          if(uploadedFiles){
            const fileName = file.name.length > 28 ? file.name.substring(0, 28) + '...' : file.name;
            const fileSize = (file.size / 1024).toFixed(1) + ' KB';

            uploadedFiles.innerHTML = `
              <div class="uploaded-file">
                <div class="file-info">
                  <i class="ri-file-text-line file-icon"></i>
                  <div>
                    <div class="file-name">${fileName}</div>
                    <div class="file-size">${fileSize}</div>
                  </div>
                </div>
                <button type="button" class="file-remove" title="Remove">
                  <i class="ri-close-line"></i>
                </button>
              </div>
            `;

            uploadedFiles.querySelector('.file-remove').addEventListener('click', (ev) => {
              ev.stopPropagation();
              uploadedFiles.innerHTML = '';
              area.style.borderColor = '';
              area.style.backgroundColor = '';
              input.value = '';
            });
          }
        });
      });
    }

    // Page navigation
    function navigateToPage(pageId) {
      currentPage = pageId;

      pages.forEach(page => page.classList.remove('active'));
      document.getElementById(`${pageId}-page`)?.classList.add('active');

      sidebarItems.forEach(item => {
        item.classList.remove('active');
        if (item.dataset.page === pageId) item.classList.add('active');
      });

      pageTitle.textContent = pageTitles[pageId] || 'ZMC PORTAL';

      // Reset steps if leaving page
      if (pageId !== 'new-registration') resetAP1Steps();
      if (pageId !== 'renewal') resetAP5Steps();
    }

    // Helpers for dynamic rows
    function removeRow(btn){
      const row = btn.closest('tr');
      const tbody = row.parentElement;
      if(tbody.children.length <= 1){
        alert('At least one row is required.');
        return;
      }
      row.remove();
    }

    function addTitleRow(){
      const tbody = document.getElementById('titlesTableBody');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input class="form-control" placeholder="e.g. Title"></td>
        <td><input class="form-control" placeholder="Weekly"></td>
        <td><input class="form-control" placeholder="10000"></td>
        <td><input class="form-control" placeholder="General / specialised"></td>
        <td><button type="button" class="btn btn-secondary btn-sm" onclick="removeRow(this)">Remove</button></td>
      `;
      tbody.appendChild(tr);
    }

    function addDirectorRow(){
      const tbody = document.getElementById('directorsTableBody');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input class="form-control" placeholder="Name"></td>
        <td><input class="form-control" placeholder="Surname"></td>
        <td><input class="form-control" placeholder="Address"></td>
        <td><input class="form-control" placeholder="Occupation"></td>
        <td><button type="button" class="btn btn-secondary btn-sm" onclick="removeRow(this)">Remove</button></td>
      `;
      tbody.appendChild(tr);
    }

    function addManagerRow(){
      const tbody = document.getElementById('managersTableBody');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input class="form-control" placeholder="Full name"></td>
        <td><input class="form-control" placeholder="Address"></td>
        <td><input class="form-control" placeholder="Nationality"></td>
        <td><input class="form-control" placeholder="Qualifications"></td>
        <td><button type="button" class="btn btn-secondary btn-sm" onclick="removeRow(this)">Remove</button></td>
      `;
      tbody.appendChild(tr);
    }

    // AP1 step navigation
    function navigateAP1Step(direction){
      const newStep = ap1CurrentStep + direction;

      if(newStep < 1) return;
      if(newStep > AP1_MAX_STEPS){
        if(!document.getElementById('ap1-declaration-agree')?.checked){
          alert('Please confirm the AP1 declaration.');
          return;
        }
        if(confirm('Submit your AP1 registration application?')){
          const ref = `ZMC-AP1-${new Date().getFullYear()}-${Math.floor(Math.random()*900+100)}`;
          alert(`Application submitted successfully!\n\nReference: ${ref}`);
          navigateToPage('home');
        }
        return;
      }

      updateStepUI('new-registration-page', 'ap1', AP1_MAX_STEPS, newStep);
      ap1CurrentStep = newStep;
      updateNavButtons();
    }

    function validateAP1Step(){
      const content = document.getElementById(`ap1-step-${ap1CurrentStep}`);
      if(!content) return true;
      const requiredFields = content.querySelectorAll('[required]');
      for(const field of requiredFields){
        if(field.type === 'checkbox'){
          if(!field.checked){
            alert('Please tick the required confirmation checkbox.');
            return false;
          }
          continue;
        }
        if(field.type === 'file'){
          if(!field.files || !field.files.length){
            alert('Please upload the required document.');
            return false;
          }
          continue;
        }
        if(!String(field.value || '').trim()){
          alert('Please complete all required fields in this step.');
          field.focus?.();
          return false;
        }
      }
      return true;
    }

    function resetAP1Steps(){
      ap1CurrentStep = 1;
      updateStepUI('new-registration-page', 'ap1', AP1_MAX_STEPS, 1, true);
      updateNavButtons();
    }

    // AP5 step navigation
    function navigateAP5Step(direction){
      const newStep = ap5CurrentStep + direction;

      if(newStep < 1) return;
      if(newStep > AP5_MAX_STEPS){
        if(!document.getElementById('ap5-declaration-agree')?.checked){
          alert('Please confirm the AP5 declaration.');
          return;
        }
        if(confirm('Submit your AP5 renewal/replacement application?')){
          const ref = `ZMC-AP5-${new Date().getFullYear()}-${Math.floor(Math.random()*900+100)}`;
          alert(`Application submitted successfully!\n\nReference: ${ref}`);
          navigateToPage('home');
        }
        return;
      }

      updateStepUI('renewal-page', 'ap5', AP5_MAX_STEPS, newStep);
      ap5CurrentStep = newStep;
      updateNavButtons();
    }

    function validateAP5Step(){
      const content = document.getElementById(`ap5-step-${ap5CurrentStep}`);
      if(!content) return true;
      const requiredFields = content.querySelectorAll('[required]');
      for(const field of requiredFields){
        if(field.type === 'checkbox'){
          if(!field.checked){
            alert('Please tick the required confirmation checkbox.');
            return false;
          }
          continue;
        }
        if(field.type === 'file'){
          continue;
        }
        if(!String(field.value || '').trim()){
          alert('Please complete all required fields in this step.');
          field.focus?.();
          return false;
        }
      }
      return true;
    }

    function resetAP5Steps(){
      ap5CurrentStep = 1;
      updateStepUI('renewal-page', 'ap5', AP5_MAX_STEPS, 1, true);

      const wrap = document.getElementById('ap5-changes-details-wrap');
      if(wrap){
        wrap.style.display = 'none';
        const textarea = wrap.querySelector('textarea');
        if(textarea) textarea.removeAttribute('required');
      }
      const no = document.getElementById('ap5-changes-no');
      if(no) no.checked = true;

      updateNavButtons();
    }

    // Generic step UI updater
    function updateStepUI(pageId, prefix, maxSteps, activeStep, reset=false){
      const page = document.getElementById(pageId);
      if(!page) return;
      const steps = page.querySelectorAll('.step');
      const contents = page.querySelectorAll('.step-content');

      steps.forEach(step => {
        const n = parseInt(step.dataset.step, 10);
        step.classList.remove('active','completed');
        const stepNumEl = step.querySelector('.step-number');
        if(reset){
          stepNumEl.textContent = String(n);
        }
        if(n === activeStep){
          step.classList.add('active');
          if(!reset) stepNumEl.textContent = String(n);
        }else if(n < activeStep){
          step.classList.add('completed');
          stepNumEl.innerHTML = '<i class="ri-check-line"></i>';
        }else{
          if(!reset) stepNumEl.textContent = String(n);
        }
      });

      contents.forEach(c => c.classList.remove('active'));
      const activeContent = document.getElementById(`${prefix}-step-${activeStep}`);
      if(activeContent) activeContent.classList.add('active');
    }

    function updateNavButtons(){
      const ap1Prev = document.getElementById('ap1-prev-btn');
      const ap1Next = document.getElementById('ap1-next-btn');
      if(ap1Prev && ap1Next){
        ap1Prev.style.display = (ap1CurrentStep === 1) ? 'none' : 'block';
        ap1Next.innerHTML = (ap1CurrentStep === AP1_MAX_STEPS)
          ? 'Submit Application <i class="ri-send-plane-line"></i>'
          : 'Next <i class="ri-arrow-right-line"></i>';
      }

      const ap5Prev = document.getElementById('ap5-prev-btn');
      const ap5Next = document.getElementById('ap5-next-btn');
      if(ap5Prev && ap5Next){
        ap5Prev.style.display = (ap5CurrentStep === 1) ? 'none' : 'block';
        ap5Next.innerHTML = (ap5CurrentStep === AP5_MAX_STEPS)
          ? 'Submit Application <i class="ri-send-plane-line"></i>'
          : 'Next <i class="ri-arrow-right-line"></i>';
      }
    }

    // Communication log (localStorage demo)
    function saveCommMessage(msg){
      const key = 'zmc_comm_log';
      const existing = JSON.parse(localStorage.getItem(key) || '[]');
      existing.unshift({ ...msg, ts: new Date().toISOString() });
      localStorage.setItem(key, JSON.stringify(existing));
    }

    function renderCommLog(){
      const container = document.getElementById('commLog');
      if(!container) return;

      const key = 'zmc_comm_log';
      const logs = JSON.parse(localStorage.getItem(key) || '[]');

      if(!logs.length){
        container.innerHTML = `<div class="text-muted">No saved messages yet.</div>`;
        return;
      }

      container.innerHTML = logs.map(item => {
        const dt = new Date(item.ts);
        return `
          <div class="border rounded p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <div class="fw-bold">${escapeHtml(item.subject)}</div>
                <div class="text-muted" style="font-size:12px;">To: ${escapeHtml(item.to)} • ${dt.toLocaleString()}</div>
              </div>
              <button class="btn btn-secondary btn-sm" onclick="copyToClipboard('${encodeURIComponent(item.body)}')">
                <i class="ri-file-copy-line"></i>
              </button>
            </div>
            <div class="mt-2" style="white-space:pre-wrap;font-size:13px;">${escapeHtml(item.body)}</div>
          </div>
        `;
      }).join('');
    }

    function copyToClipboard(encodedText){
      const text = decodeURIComponent(encodedText);
      navigator.clipboard?.writeText(text).then(() => {
        alert('Message copied.');
      }).catch(() => alert('Copy failed (browser permissions).'));
    }

    function escapeHtml(str){
      return String(str || '')
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
    }

    // CHATBOT
    function initChatbot(){
      document.getElementById('chatbotFab')?.addEventListener('click', () => openChatbot(true));
      document.getElementById('chatbotClose')?.addEventListener('click', () => openChatbot(false));
      document.getElementById('chatbotSend')?.addEventListener('click', () => sendChat());
      document.getElementById('chatbotInput')?.addEventListener('keydown', (e) => {
        if(e.key === 'Enter') sendChat();
      });

      const body = document.getElementById('chatbotBody');
      if(body && !body.dataset.init){
        body.dataset.init = '1';
        addBotBubble("Hi! I can help with AP1 registration, AP5 renewals, annexures, payments, and contacting ZMC. What do you need?");
        addBotBubble("Try: “AP1 annexures”, “AP5 changes”, “how to submit”, or “contact”.");
      }
    }

    function openChatbot(open){
      const panel = document.getElementById('chatbotPanel');
      if(!panel) return;
      panel.classList.toggle('active', open);
      if(open){
        setTimeout(() => document.getElementById('chatbotInput')?.focus(), 100);
      }
    }

    function addUserBubble(text){
      const el = document.createElement('div');
      el.className = 'bubble user';
      el.textContent = text;
      document.getElementById('chatbotBody').appendChild(el);
      scrollChatBottom();
    }

    function addBotBubble(text){
      const el = document.createElement('div');
      el.className = 'bubble bot';
      el.textContent = text;
      document.getElementById('chatbotBody').appendChild(el);
      scrollChatBottom();
    }

    function scrollChatBottom(){
      const body = document.getElementById('chatbotBody');
      body.scrollTop = body.scrollHeight;
    }

    function sendChat(){
      const input = document.getElementById('chatbotInput');
      const text = (input.value || '').trim();
      if(!text) return;
      addUserBubble(text);
      input.value = '';
      const reply = chatbotReply(text);
      setTimeout(() => addBotBubble(reply), 250);
    }

    function chatbotReply(userText){
      const t = userText.toLowerCase();

      if(t.includes('ap1') && (t.includes('annex') || t.includes('documents') || t.includes('attach'))){
        return "AP1 annexures include: Certificate of incorporation (certified), Memorandum & Articles (certified), CR6/CR14, certified IDs for directors, mission statement, code of ethics, code of conduct (if different), style book, editorial charter, market analysis, projected cash flow (3 years), financial statement & balance sheet, and a dummy/sample magazine.";
      }
      if(t.includes('ap5') && t.includes('change')){
        return "On AP5, indicate whether there are changes since the previous/original application (YES/NO). If YES, describe the changes. ZMC may request a new application if the changes are material.";
      }
      if(t.includes('how') && (t.includes('submit') || t.includes('apply'))){
        return "Go to “New Registration (AP1)” and complete each step. Upload all required annexures in Step 6, then confirm the declaration and submit. You’ll receive a reference number to track in Home.";
      }
      if(t.includes('contact') || t.includes('email') || t.includes('communication')){
        return "Open the Communication page to compose an email to ZMC. Include your reference number in the subject/body for faster support.";
      }
      if(t.includes('payment') || t.includes('invoice')){
        return "Check Payment History for invoice status and receipts. In production this should reflect official invoice records.";
      }

      return "I can help with AP1 annexures, AP5 renewals, submission steps, payments, and contacting ZMC. Try: “AP1 annexures”, “AP5 changes”, “how to submit”, or “contact”.";
    }
  </script>

  @stack('scripts')
</body>
</html>
