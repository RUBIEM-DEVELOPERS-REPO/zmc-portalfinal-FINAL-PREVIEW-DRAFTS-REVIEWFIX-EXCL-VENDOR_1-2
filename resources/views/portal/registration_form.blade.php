<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Zimbabwe Media Commission - Mass Media Portal</title>

  <!-- Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Remix Icons -->
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
    }

    .navbar-brand-box img{height:38px;}
    .logo-text{
      color:#fff;
      font-weight:800;
      font-size:16px;
      letter-spacing:0.5px;
      text-transform:uppercase;
    }
    .logo-sub{
      font-size:11px;
      color:var(--zmc-yellow);
      font-weight:600;
      display:block;
      margin-top:2px;
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
      background:#10b981;
      border-color:#10b981;
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
      font-size:12px;color:#10b981;display:flex;align-items:center;gap:4px;
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
      width:48px;height:48px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      font-weight:900;font-size:22px;margin-right:15px;flex-shrink:0;
      box-shadow:0 4px 12px rgba(250,204,21,0.3);
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
</head>
<body>
  <!-- SIDEBAR -->
  <div class="vertical-menu">
    <div class="navbar-brand-box">
      <img src="{{ asset('zmc_logo.png') }}" alt="ZMC Logo">
      <div>
        <span class="logo-text">ZMC PORTAL</span>
        <span class="logo-sub">Mass Media Service Registration</span>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li class="active" data-page="home"><i class="ri-home-4-line"></i> Home</li>
      <li data-page="new-registration"><i class="ri-file-add-line"></i> New Registration (AP1)</li>
      <li data-page="renewal"><i class="ri-refresh-line"></i> Renewal (AP5)</li>
      <li data-page="payment-history"><i class="ri-bank-card-line"></i> Payment History</li>
      <li data-page="notices"><i class="ri-megaphone-line"></i> Notices & Events</li>
      <li data-page="how-to"><i class="ri-information-line"></i> How to Get Registered</li>
      <li data-page="org-profile"><i class="ri-building-2-line"></i> Organization Profile</li>
      <li data-page="communication"><i class="ri-mail-send-line"></i> Communication</li>
      <li data-page="settings"><i class="ri-settings-3-line"></i> Settings</li>
    </ul>

    <div class="sidebar-user">
      <img src="https://ui-avatars.com/api/?name=Media+House&background=facc15&color=000" alt="user">
      <div style="line-height:1.1;">
        <div style="font-weight:700;font-size:13px;color:#fff;">Media House Account</div>
        <div style="font-size:11px;color:rgba(255,255,255,0.7);">Applicant Dashboard</div>
      </div>
    </div>
  </div>

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="topbar-left">
      <button class="btn" type="button" id="sidebarToggle" aria-label="Toggle menu">
        <i class="ri-menu-2-line" aria-hidden="true"></i>
      </button>
      <span class="topbar-title" id="pageTitle">HOME - REGISTRATION TRACKER</span>
    </div>
    <div class="topbar-right">
      <button class="btn btn-secondary" id="quickComposeBtn" title="Compose Email">
        <i class="ri-mail-line"></i>
      </button>
      <button class="btn btn-secondary" id="openChatbotBtn" title="Chatbot">
        <i class="ri-robot-2-line"></i>
      </button>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="page-content">

      <!-- HOME PAGE -->
      <div class="page active" id="home-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Registration Tracker</h4>
          <div class="d-flex gap-2">
            <button class="btn btn-secondary" id="goRenewalHome">
              <i class="ri-refresh-line me-2"></i>Renew / Replace
            </button>
            <button class="btn btn-primary" id="newRegBtnHome">
              <i class="ri-file-add-line me-2"></i>New Registration (AP1)
            </button>
          </div>
        </div>

        <div class="dashboard-cards">
          <div class="dashboard-card">
            <h5>Active Submissions</h5>
            <div class="count" id="dashActive">2</div>
            <div class="trend"><i class="ri-arrow-up-line"></i> 1 new this month</div>
          </div>
          <div class="dashboard-card">
            <h5>Approved</h5>
            <div class="count" id="dashApproved">1</div>
            <div class="trend"><i class="ri-check-line"></i> Up to date</div>
          </div>
          <div class="dashboard-card">
            <h5>Pending Review</h5>
            <div class="count" id="dashPending">1</div>
            <div class="trend"><i class="ri-time-line"></i> Under processing</div>
          </div>
          <div class="dashboard-card">
            <h5>Renewals Due</h5>
            <div class="count" id="dashRenewals">1</div>
            <div class="trend"><i class="ri-alert-line"></i> Within 30 days</div>
          </div>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h5 class="m-0"><i class="ri-history-line me-2"></i>Recent Registrations / Renewals</h5>
            <p class="mt-2">Track your Mass Media Service registration (AP1) and renewal/replacement requests (AP5).</p>
          </div>
          <div class="form-steps-container">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Reference</th>
                    <th>Form</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="trackerTableBody">
                  <tr>
                    <td>ZMC-AP1-2023-031</td>
                    <td>AP1 - New Registration</td>
                    <td>12 Nov 2023</td>
                    <td><span class="badge bg-info">Pending Review</span></td>
                    <td><button class="btn btn-sm btn-primary" onclick="alert('Demo: Open details view')">View</button></td>
                  </tr>
                  <tr>
                    <td>ZMC-AP5-2023-077</td>
                    <td>AP5 - Renewal</td>
                    <td>04 Dec 2023</td>
                    <td><span class="badge bg-warning">Processing</span></td>
                    <td><button class="btn btn-sm btn-primary" onclick="alert('Demo: Track timeline')">Track</button></td>
                  </tr>
                  <tr>
                    <td>ZMC-AP1-2022-014</td>
                    <td>AP1 - New Registration</td>
                    <td>16 Aug 2022</td>
                    <td><span class="badge bg-success">Approved</span></td>
                    <td><button class="btn btn-sm btn-primary" onclick="alert('Demo: Download certificate')">Download</button></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="alert alert-light border mt-3">
              <div class="d-flex align-items-start gap-2">
                <i class="ri-information-line" style="font-size:18px;"></i>
                <div>
                  <div class="fw-bold">Tip</div>
                  <div class="text-muted">
                    Keep annexures ready (Certificate of Incorporation, CR6/CR14, mission statement, ethics/code, style book, certified IDs for directors),
                    plus Editorial Charter, 3-year cash flow, financial statements & balance sheet, market analysis and a dummy magazine sample.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- NEW REGISTRATION PAGE (AP1) -->
      <div class="page" id="new-registration-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">New Registration - Mass Media Service (AP1)</h4>
          <div class="d-flex gap-2">
            <button class="btn btn-secondary" id="downloadAP1">
              <i class="ri-download-line me-1"></i>Download AP1 Form
            </button>
            <button class="btn btn-secondary" id="previewAP1">
              <i class="ri-file-search-line me-1"></i>Preview AP1 (PDF)
            </button>
          </div>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h1>Application for Registration of a Mass Media Service</h1>
            <p>Statutory Instrument 169C (Registration, Accreditation and Levy) Regulations (2002)</p>
          </div>

          <div class="form-steps-container">
            <div class="step-progress">
              <div class="step-progress-bar">
                <div class="step active" data-step="1">
                  <div class="step-number">1</div>
                  <div class="step-label">Contact</div>
                </div>
                <div class="step" data-step="2">
                  <div class="step-number">2</div>
                  <div class="step-label">Applicant</div>
                </div>
                <div class="step" data-step="3">
                  <div class="step-number">3</div>
                  <div class="step-label">Titles & Offices</div>
                </div>
                <div class="step" data-step="4">
                  <div class="step-number">4</div>
                  <div class="step-label">Directors & Mgmt</div>
                </div>
                <div class="step" data-step="5">
                  <div class="step-number">5</div>
                  <div class="step-label">Ownership & Links</div>
                </div>
                <div class="step" data-step="6">
                  <div class="step-number">6</div>
                  <div class="step-label">Annexures</div>
                </div>
                <div class="step" data-step="7">
                  <div class="step-number">7</div>
                  <div class="step-label">Declaration</div>
                </div>
              </div>
            </div>

            <!-- STEP 1: CONTACT -->
            <div class="step-content active" id="ap1-step-1">
              <h3 class="step-title">Contact Person for This Application</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Provide the name, address and telephone number of the person(s) who may be contacted regarding questions in respect of this application.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Contact Full Name</label>
                  <input type="text" class="form-control" placeholder="e.g. Tendai Moyo" required>
                </div>
                <div class="form-field">
                  <label class="form-label required">Telephone</label>
                  <input type="tel" class="form-control" placeholder="+263 77 123 4567" required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Email Address</label>
                  <input type="email" class="form-control" placeholder="email@example.com" required>
                  <div class="form-hint">Notifications and invoices may be sent here.</div>
                </div>
                <div class="form-field">
                  <label class="form-label required">Contact Address</label>
                  <textarea class="form-control" rows="3" placeholder="Physical address" required></textarea>
                </div>
              </div>
            </div>

            <!-- STEP 2: APPLICANT -->
            <div class="step-content" id="ap1-step-2">
              <h3 class="step-title">Applicant Details</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Enter applicant details and the mass media activities for which registration is sought.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Name of Applicant</label>
                  <input type="text" class="form-control" placeholder="Registered entity name" required>
                  <div class="form-hint">Attach certified copies of certificate of incorporation and memorandum & articles of association.</div>
                </div>
                <div class="form-field">
                  <label class="form-label required">Type of Mass Media Activities</label>
                  <input type="text" class="form-control" placeholder="e.g. Newspaper publishing, online news, magazine, etc." required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Applicant Head Office Address</label>
                  <textarea class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label">Mailing Address</label>
                  <textarea class="form-control" rows="3" placeholder="If different from head office"></textarea>
                </div>
              </div>
            </div>

            <!-- STEP 3: TITLES & OFFICES -->
            <div class="step-content" id="ap1-step-3">
              <h3 class="step-title">Titles Published & Frequency</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                State titles published, frequency, circulation figures and whether general news or specialised information.
              </div>

              <div class="mini-table mb-3">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th style="width:26%;">Title</th>
                      <th style="width:18%;">Frequency</th>
                      <th style="width:18%;">Circulation</th>
                      <th>General / Specialised (Details)</th>
                      <th style="width:90px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="titlesTableBody">
                    <tr>
                      <td><input class="form-control" placeholder="e.g. The Daily News" required></td>
                      <td><input class="form-control" placeholder="Daily" required></td>
                      <td><input class="form-control" placeholder="50000" required></td>
                      <td><input class="form-control" placeholder="General news / specialised topic"></td>
                      <td><button type="button" class="btn btn-secondary btn-sm" onclick="removeRow(this)">Remove</button></td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <button type="button" class="btn btn-secondary" id="addTitleRow">
                <i class="ri-add-line me-1"></i>Add Another Title
              </button>
            </div>

            <!-- STEP 4: DIRECTORS & MANAGEMENT -->
            <div class="step-content" id="ap1-step-4">
              <h3 class="step-title">Directors & Senior Management</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Provide directors and senior managers details. You can add rows where necessary.
              </div>

              <h6 class="fw-bold">Directors (CR6 / CR14)</h6>
              <div class="mini-table mb-3">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Surname</th>
                      <th>Address</th>
                      <th>Occupation</th>
                      <th style="width:90px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="directorsTableBody">
                    <tr>
                      <td><input class="form-control" placeholder="Name" required></td>
                      <td><input class="form-control" placeholder="Surname" required></td>
                      <td><input class="form-control" placeholder="Address" required></td>
                      <td><input class="form-control" placeholder="Occupation" required></td>
                      <td><button type="button" class="btn btn-secondary btn-sm" onclick="removeRow(this)">Remove</button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-secondary mb-4" id="addDirectorRow">
                <i class="ri-add-line me-1"></i>Add Director
              </button>

              <h6 class="fw-bold">CEO Details</h6>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">CEO Full Name</label>
                  <input type="text" class="form-control" placeholder="CEO name" required>
                </div>
                <div class="form-field">
                  <label class="form-label required">CEO Nationality</label>
                  <input type="text" class="form-control" placeholder="e.g. Zimbabwean" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">CEO Address</label>
                  <textarea class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label required">Qualifications & Experience</label>
                  <textarea class="form-control" rows="2" placeholder="Summary" required></textarea>
                </div>
              </div>

              <h6 class="fw-bold mt-2">Senior Managers (involved in the mass media service)</h6>
              <div class="mini-table mb-3">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Full Name</th>
                      <th>Address</th>
                      <th>Nationality</th>
                      <th>Qualifications</th>
                      <th style="width:90px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="managersTableBody">
                    <tr>
                      <td><input class="form-control" placeholder="Full name"></td>
                      <td><input class="form-control" placeholder="Address"></td>
                      <td><input class="form-control" placeholder="Nationality"></td>
                      <td><input class="form-control" placeholder="Qualifications"></td>
                      <td><button type="button" class="btn btn-secondary btn-sm" onclick="removeRow(this)">Remove</button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-secondary" id="addManagerRow">
                <i class="ri-add-line me-1"></i>Add Senior Manager
              </button>
            </div>

            <!-- STEP 5: OWNERSHIP & LINKS -->
            <div class="step-content" id="ap1-step-5">
              <h3 class="step-title">Ownership, Links & Disclosures</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Complete shareholding structure and disclosure questions.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Shareholding Structure (include nationality)</label>
                  <textarea class="form-control" rows="4" placeholder="Describe shareholders and % ownership, include nationality" required></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label">Other Directorships held by directors</label>
                  <textarea class="form-control" rows="4" placeholder="List other companies and directorships"></textarea>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Public offices or political party offices held by directors</label>
                  <textarea class="form-control" rows="3" placeholder="If applicable, provide details"></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label">Shareholding in any other mass media service / news agency</label>
                  <textarea class="form-control" rows="3" placeholder="If any, provide details"></textarea>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Shareholding/ownership of broadcasting service licensee (Broadcasting Services Act)</label>
                  <textarea class="form-control" rows="3" placeholder="If any, provide details"></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label">Shareholding/ownership of postal & telecom licensee (Postal & Telecommunications Act)</label>
                  <textarea class="form-control" rows="3" placeholder="If any, provide details"></textarea>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Shareholding/ownership of any advertising agency</label>
                  <textarea class="form-control" rows="3" placeholder="If any, provide details"></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label required">Convictions / Judgment debts / Insolvency disclosures</label>
                  <textarea class="form-control" rows="3" placeholder="Answer AP1 Q16-18: YES/NO + details (where applicable)" required></textarea>
                  <div class="form-hint">If “Yes” for any item, provide details.</div>
                </div>
              </div>
            </div>

            <!-- STEP 6: ANNEXURES -->
            <div class="step-content" id="ap1-step-6">
              <h3 class="step-title">Annexures & Supporting Documents</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Upload required annexures (clear scans). Attach annexures wherever necessary.
              </div>

              <!-- Original Annexures -->
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Certificate of Incorporation (Certified)</label>
                  <div class="upload-area">
                    <i class="ri-file-text-line"></i>
                    <h5>Upload Certificate</h5>
                    <p>PDF/JPG/PNG</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label required">Memorandum & Articles (Certified)</label>
                  <div class="upload-area">
                    <i class="ri-article-line"></i>
                    <h5>Upload Memorandum & Articles</h5>
                    <p>PDF/JPG/PNG</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">CR6 / CR14 (Latest)</label>
                  <div class="upload-area">
                    <i class="ri-folder-shared-line"></i>
                    <h5>Upload CR6/CR14</h5>
                    <p>PDF/JPG/PNG</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label required">Directors’ National IDs (Certified)</label>
                  <div class="upload-area">
                    <i class="ri-id-card-line"></i>
                    <h5>Upload Directors’ IDs</h5>
                    <p>Combine into one PDF if possible</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Mission Statement</label>
                  <div class="upload-area">
                    <i class="ri-flag-line"></i>
                    <h5>Upload Mission Statement</h5>
                    <p>PDF/DOC/DOCX</p>
                    <input type="file" accept=".pdf,.doc,.docx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label required">In-house Code of Ethics</label>
                  <div class="upload-area">
                    <i class="ri-shield-check-line"></i>
                    <h5>Upload Code of Ethics</h5>
                    <p>PDF/DOC/DOCX</p>
                    <input type="file" accept=".pdf,.doc,.docx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">In-house Code of Conduct (if different)</label>
                  <div class="upload-area">
                    <i class="ri-file-shield-2-line"></i>
                    <h5>Upload Code of Conduct</h5>
                    <p>Optional if same as ethics</p>
                    <input type="file" accept=".pdf,.doc,.docx" style="display:none;">
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label required">In-house Style Book</label>
                  <div class="upload-area">
                    <i class="ri-book-open-line"></i>
                    <h5>Upload Style Book</h5>
                    <p>PDF/DOC/DOCX</p>
                    <input type="file" accept=".pdf,.doc,.docx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>

              <!-- NEW REQUIRED UPLOADS (Requested) -->
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Editorial Charter</label>
                  <div class="upload-area">
                    <i class="ri-file-shield-2-line"></i>
                    <h5>Upload Editorial Charter</h5>
                    <p>PDF/DOC/DOCX</p>
                    <input type="file" accept=".pdf,.doc,.docx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label required">Market Analysis</label>
                  <div class="upload-area">
                    <i class="ri-line-chart-line"></i>
                    <h5>Upload Market Analysis</h5>
                    <p>PDF/DOC/DOCX</p>
                    <input type="file" accept=".pdf,.doc,.docx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Projected Cash Flow (3 Years)</label>
                  <div class="upload-area">
                    <i class="ri-funds-line"></i>
                    <h5>Upload 3-Year Cash Flow Projection</h5>
                    <p>PDF/XLS/XLSX</p>
                    <input type="file" accept=".pdf,.xls,.xlsx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label required">Financial Statement & Balance Sheet</label>
                  <div class="upload-area">
                    <i class="ri-file-list-3-line"></i>
                    <h5>Upload Financials</h5>
                    <p>PDF/XLS/XLSX</p>
                    <input type="file" accept=".pdf,.xls,.xlsx" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Dummy Attachment (e.g., Magazine Sample)</label>
                  <div class="upload-area">
                    <i class="ri-book-2-line"></i>
                    <h5>Upload Sample Magazine</h5>
                    <p>PDF recommended (or JPG/PNG)</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                  <div class="form-hint">Attach a dummy/sample issue to demonstrate layout, sections, and editorial style.</div>
                </div>

                <div class="form-field">
                  <label class="form-label">Any Other Supporting Documents</label>
                  <div class="upload-area">
                    <i class="ri-attachment-2"></i>
                    <h5>Upload Additional Annexure</h5>
                    <p>Optional</p>
                    <input type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="display:none;">
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>
            </div>

            <!-- STEP 7: DECLARATION -->
            <div class="step-content" id="ap1-step-7">
              <h3 class="step-title">Declaration & Submission</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Review your application and confirm the declaration.
              </div>

              <div class="alert alert-warning">
                <h6><i class="ri-file-text-line me-2"></i>Declaration</h6>
                <p class="mb-2">I declare that all the information given above, to the best of my knowledge is true and complete.</p>
                <div class="form-check mt-3">
                  <input class="form-check-input" type="checkbox" id="ap1-declaration-agree" required>
                  <label class="form-check-label" for="ap1-declaration-agree">
                    I agree and confirm this declaration
                  </label>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Digital Signature (Applicant)</label>
                  <input type="text" class="form-control" placeholder="Type full name" required>
                </div>
                <div class="form-field">
                  <label class="form-label required">Date</label>
                  <input type="date" class="form-control" required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Official Stamp (Upload if available)</label>
                  <div class="upload-area">
                    <i class="ri-stamp-line"></i>
                    <h5>Upload Stamp</h5>
                    <p>Optional</p>
                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
                <div class="form-field">
                  <label class="form-label">Notes to ZMC (optional)</label>
                  <textarea class="form-control" rows="5" placeholder="Any additional notes"></textarea>
                </div>
              </div>
            </div>

            <!-- FORM NAVIGATION -->
            <div class="form-buttons">
              <button type="button" class="btn btn-secondary" id="ap1-prev-btn">
                <i class="ri-arrow-left-line"></i> Previous
              </button>
              <button type="button" class="btn btn-primary" id="ap1-next-btn">
                Next <i class="ri-arrow-right-line"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- AP1 PDF PREVIEW MODAL -->
        <div class="modal fade" id="ap1PreviewModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><i class="ri-file-search-line me-2"></i>AP1 Preview (PDF)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="alert alert-light border">
                  Demo preview area. In production, serve the PDF from your backend or public storage and load it in this iframe.
                </div>
                <iframe id="ap1PdfFrame" style="width:100%;height:70vh;border:1px solid var(--border-color);border-radius:8px;" title="AP1 PDF Preview"></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RENEWAL / REPLACEMENT PAGE (AP5) -->
      <div class="page" id="renewal-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Renewal / Replacement of Registration (AP5)</h4>
          <div class="d-flex gap-2">
            <button class="btn btn-secondary" id="downloadAP5">
              <i class="ri-download-line me-1"></i>Download AP5 Form
            </button>
            <button class="btn btn-secondary" id="previewAP5">
              <i class="ri-file-search-line me-1"></i>Preview AP5 (DOC)
            </button>
          </div>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h1>Application for Renewal of Registration, Accreditation or Permission</h1>
            <p>Statutory Instrument 169C (Registration, Accreditation and Levy) Regulations (2002)</p>
          </div>

          <div class="form-steps-container">
            <div class="step-progress">
              <div class="step-progress-bar">
                <div class="step active" data-step="1">
                  <div class="step-number">1</div>
                  <div class="step-label">Contact</div>
                </div>
                <div class="step" data-step="2">
                  <div class="step-number">2</div>
                  <div class="step-label">Applicant</div>
                </div>
                <div class="step" data-step="3">
                  <div class="step-number">3</div>
                  <div class="step-label">Previous No.</div>
                </div>
                <div class="step" data-step="4">
                  <div class="step-number">4</div>
                  <div class="step-label">Annexures</div>
                </div>
                <div class="step" data-step="5">
                  <div class="step-number">5</div>
                  <div class="step-label">Declaration</div>
                </div>
              </div>
            </div>

            <!-- STEP 1 -->
            <div class="step-content active" id="ap5-step-1">
              <h3 class="step-title">Contact Person</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Provide the name, address and telephone number of the person(s) who may be contacted regarding questions in respect of this application.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Contact Full Name</label>
                  <input type="text" class="form-control" placeholder="Full name" required>
                </div>
                <div class="form-field">
                  <label class="form-label required">Telephone</label>
                  <input type="tel" class="form-control" placeholder="+263 ..." required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Contact Address</label>
                  <textarea class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label required">Email Address</label>
                  <input type="email" class="form-control" placeholder="email@example.com" required>
                </div>
              </div>
            </div>

            <!-- STEP 2 -->
            <div class="step-content" id="ap5-step-2">
              <h3 class="step-title">Applicant Details</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Enter applicant name, head office address, mailing address and type of applicant.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Applicant Name</label>
                  <input type="text" class="form-control" required>
                </div>
                <div class="form-field">
                  <label class="form-label required">Type of Applicant</label>
                  <select class="form-control" required>
                    <option value="">Select type</option>
                    <option>Mass Media Service</option>
                    <option>News Agency</option>
                    <option>Other</option>
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Head Office Address</label>
                  <textarea class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label required">Mailing Address</label>
                  <textarea class="form-control" rows="3" required></textarea>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Changes to particulars since previous application?</label>
                  <div class="checkbox-group">
                    <div class="checkbox-item">
                      <input type="radio" id="ap5-changes-no" name="ap5-changes" value="no" checked>
                      <label for="ap5-changes-no">No</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="radio" id="ap5-changes-yes" name="ap5-changes" value="yes">
                      <label for="ap5-changes-yes">Yes</label>
                    </div>
                  </div>
                </div>
                <div class="form-field" id="ap5-changes-details-wrap" style="display:none;">
                  <label class="form-label required">If yes, provide details</label>
                  <textarea class="form-control" rows="3" placeholder="Describe changes"></textarea>
                </div>
              </div>
            </div>

            <!-- STEP 3 -->
            <div class="step-content" id="ap5-step-3">
              <h3 class="step-title">Previous Registration / Accreditation Number</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Provide the previous Registration or Accreditation number.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Previous Number</label>
                  <input type="text" class="form-control" placeholder="e.g. ZMC-REG-2023-001" required>
                </div>
                <div class="form-field">
                  <label class="form-label">Reason (optional)</label>
                  <input type="text" class="form-control" placeholder="Renewal / replacement explanation">
                </div>
              </div>
            </div>

            <!-- STEP 4 -->
            <div class="step-content" id="ap5-step-4">
              <h3 class="step-title">Supporting Annexures</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Attach annexures wherever necessary. ZMC may request a new application if changes are material.
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Updated CR6 / CR14 (if applicable)</label>
                  <div class="upload-area">
                    <i class="ri-folder-shared-line"></i>
                    <h5>Upload CR6/CR14</h5>
                    <p>Optional</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>

                <div class="form-field">
                  <label class="form-label">Any other supporting documents</label>
                  <div class="upload-area">
                    <i class="ri-attachment-2"></i>
                    <h5>Upload Annexure</h5>
                    <p>Optional</p>
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;">
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>
            </div>

            <!-- STEP 5 -->
            <div class="step-content" id="ap5-step-5">
              <h3 class="step-title">Declaration & Submission</h3>
              <div class="current-step-info">
                <i class="ri-information-line me-2"></i>
                Confirm the declaration and provide signatures (digital).
              </div>

              <div class="alert alert-warning">
                <h6><i class="ri-file-text-line me-2"></i>Declaration</h6>
                <p class="mb-2">I declare that all the information given above, to the best of my knowledge is true and complete.</p>
                <div class="form-check mt-3">
                  <input class="form-check-input" type="checkbox" id="ap5-declaration-agree" required>
                  <label class="form-check-label" for="ap5-declaration-agree">
                    I agree and confirm this declaration
                  </label>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Digital Signature (Applicant)</label>
                  <input type="text" class="form-control" placeholder="Type full name" required>
                </div>
                <div class="form-field">
                  <label class="form-label required">Date</label>
                  <input type="date" class="form-control" required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Digital Signature (Editor/Publisher)</label>
                  <input type="text" class="form-control" placeholder="Editor/Publisher full name (if applicable)">
                </div>
                <div class="form-field">
                  <label class="form-label">Official Stamp (Upload)</label>
                  <div class="upload-area">
                    <i class="ri-stamp-line"></i>
                    <h5>Upload Stamp</h5>
                    <p>Optional</p>
                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>
            </div>

            <!-- FORM NAVIGATION -->
            <div class="form-buttons">
              <button type="button" class="btn btn-secondary" id="ap5-prev-btn">
                <i class="ri-arrow-left-line"></i> Previous
              </button>
              <button type="button" class="btn btn-primary" id="ap5-next-btn">
                Next <i class="ri-arrow-right-line"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- AP5 PREVIEW MODAL -->
        <div class="modal fade" id="ap5PreviewModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><i class="ri-file-search-line me-2"></i>AP5 Preview (DOC)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="alert alert-light border">
                  DOC preview in-browser varies. In production, render to PDF or HTML server-side for reliable preview.
                </div>
                <div class="border rounded p-3 bg-white">
                  <h6 class="fw-bold">AP5 Summary</h6>
                  <ol class="text-muted mb-0">
                    <li>Contact person (name, address, telephone)</li>
                    <li>Applicant details (head office, mailing address, type, changes YES/NO + details)</li>
                    <li>Previous registration/accreditation number</li>
                    <li>Declaration + signatures</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PAYMENT HISTORY PAGE -->
      <div class="page" id="payment-history-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Payment History</h4>
          <button class="btn btn-primary" onclick="alert('Demo: Export to PDF/CSV')">
            <i class="ri-download-line me-2"></i>Export Records
          </button>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h5 class="m-0"><i class="ri-bank-card-line me-2"></i>Transaction History</h5>
          </div>
          <div class="form-steps-container">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Receipt</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>INV-2023-031</td>
                    <td>12 Nov 2023</td>
                    <td>AP1 Registration Processing Fee</td>
                    <td>USD 100.00</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td><button class="btn btn-sm btn-primary" onclick="alert('Demo: Download receipt')">Download</button></td>
                  </tr>
                  <tr>
                    <td>INV-2023-077</td>
                    <td>04 Dec 2023</td>
                    <td>AP5 Renewal Fee</td>
                    <td>USD 75.00</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td><button class="btn btn-sm btn-primary" disabled>Download</button></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="alert alert-info mt-3">
              <i class="ri-information-line me-2"></i>
              Payment instructions and channels should be configured by Admin. This page displays invoice status and receipts.
            </div>
          </div>
        </div>
      </div>

      <!-- NOTICES & EVENTS PAGE -->
      <div class="page" id="notices-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Notices & Events</h4>
          <button class="btn btn-primary" onclick="alert('Demo: Calendar view')">
            <i class="ri-calendar-event-line me-2"></i>View Calendar
          </button>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h5 class="m-0"><i class="ri-megaphone-line me-2"></i>Latest Announcements</h5>
            <p class="mt-2">Admin uploads will appear here (title, date, body, attachments).</p>
          </div>
          <div class="form-steps-container">
            <div class="notice-list" id="noticeList">
              <div class="notice-item">
                <div class="notice-date">1 December 2023</div>
                <h6>Mass Media Registration Renewals</h6>
                <p>Applicants are reminded to keep their registration current. Submit renewal applications ahead of expiry dates to avoid delays.</p>
              </div>
              <div class="notice-item">
                <div class="notice-date">15 November 2023</div>
                <h6>Digital Registration Portal Live</h6>
                <p>The Zimbabwe Media Commission has implemented an online portal for Mass Media registration and renewals.</p>
              </div>
              <div class="notice-item">
                <div class="notice-date">10 November 2023</div>
                <h6>Compliance Reminder: Annexures</h6>
                <p>Please ensure required documents are included for AP1 applications, including editorial and financial annexures where applicable.</p>
              </div>
            </div>

            <div class="alert alert-light border">
              <div class="fw-bold mb-1"><i class="ri-upload-cloud-2-line me-2"></i>Admin Uploads (Demo)</div>
              <div class="text-muted">In production: Admin creates notices with optional attachments; users can download them from here.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- HOW TO GET REGISTERED PAGE -->
      <div class="page" id="how-to-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">How to Get Registered</h4>
          <button class="btn btn-primary" id="startRegistrationFromGuide">
            <i class="ri-file-add-line me-2"></i>Start AP1 Now
          </button>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h5 class="m-0"><i class="ri-guide-line me-2"></i>Registration Process Guide</h5>
          </div>
          <div class="form-steps-container">
            <h4 style="color: #111827; font-weight: 700; margin-bottom: 25px; font-size: 18px;">New Registration (AP1)</h4>
            <ol style="color: #1a1a1a; font-size: 15px; line-height: 1.8; padding-left: 20px; margin: 0 0 40px 0;">
              <li style="margin-bottom: 18px;"><strong>Prepare Your Company Documents</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Gather your Certificate of Incorporation, Memorandum & Articles, CR6/CR14, certified IDs for all directors, and related company documents.</p></li>
              <li style="margin-bottom: 18px;"><strong>Compile Compliance and Governance Documents</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Prepare your mission statement, code of ethics, code of conduct, style guide, and Editorial Charter.</p></li>
              <li style="margin-bottom: 18px;"><strong>Prepare Business and Technical Documents</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Provide Market Analysis, 3-year cash flow projection, financial statements, balance sheet, and publication sample.</p></li>
              <li style="margin-bottom: 18px;"><strong>Complete the Registration Form (AP1)</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Fill all required fields with accurate information about your organization, ownership, and management.</p></li>
              <li style="margin-bottom: 18px;"><strong>Upload All Required Documents</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Upload clear, legible scans of all documents organized by category for easy verification.</p></li>
              <li style="margin-bottom: 18px;"><strong>Submit Your Application</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Review your application and submit through the portal. You will receive a reference number to track your progress.</p></li>
              <li style="margin-bottom: 18px;"><strong>Pay the Registration Fee</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Pay via Paynow (EcoCash, OneMoney) or Bank Transfer with proof of payment.</p></li>
              <li style="margin-bottom: 18px;"><strong>Await Review and Approval</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">ZMC officers review your application and materials. You may receive requests for clarifications or additional information.</p></li>
              <li style="margin-bottom: 18px;"><strong>Receive Your Certificate and Licence</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Upon approval, collect your registration certificate and operating licence from ZMC offices or request courier delivery.</p></li>
            </ol>
            <h4 style="color: #111827; font-weight: 700; margin-bottom: 25px; font-size: 18px; margin-top: 20px;">Renewal (AP5)</h4>
            <ol style="color: #1a1a1a; font-size: 15px; line-height: 1.8; padding-left: 20px; margin: 0 0 40px 0;">
              <li style="margin-bottom: 18px;"><strong>Check Your Renewal Deadline</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Log into your portal to view your renewal deadline. You can renew before your registration expires.</p></li>
              <li style="margin-bottom: 18px;"><strong>Prepare Updated Documents</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Gather updated financial statements, editorial charter, market analysis, and recent publication sample.</p></li>
              <li style="margin-bottom: 18px;"><strong>Complete the Renewal Form (AP5)</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Include all changes to your organization, management, ownership, or operations since your last registration.</p></li>
              <li style="margin-bottom: 18px;"><strong>Upload Updated Documents</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Upload updated financial statements, governance documents, and recent publication samples.</p></li>
              <li style="margin-bottom: 18px;"><strong>Submit and Pay Renewal Fee</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Submit your renewal application and pay the fee via Paynow or Bank Transfer.</p></li>
              <li style="margin-bottom: 18px;"><strong>Await Approval</strong><p style="margin: 8px 0 0 0; color: #1a1a1a;">Your renewal will be reviewed and processed. Collect your renewed certificate and licence after approval.</p></li>
            </ol>
            <h4 style="color: #111827; font-weight: 700; margin-bottom: 15px; font-size: 18px; margin-top: 20px;">Important Notes</h4>
            <ul style="color: #1a1a1a; font-size: 15px; line-height: 1.8; padding-left: 20px; margin: 0; list-style-type: disc;">
              <li style="margin-bottom: 12px;">Incomplete applications or missing attachments will delay processing. Ensure all documents are uploaded before submitting.</li>
              <li style="margin-bottom: 12px;">All documents must be clear, legible, and in PDF, JPG, or PNG format.</li>
              <li style="margin-bottom: 12px;">Payment must be verified before your application proceeds to the review stage.</li>
              <li style="margin-bottom: 12px;">Material changes to your organization or ownership may require additional review or a new application.</li>
              <li style="margin-bottom: 12px;">Track your application status using your reference number in your dashboard.</li>
              <li style="margin-bottom: 12px;">Contact ZMC support through the Help section if you have questions.</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- ORGANIZATION PROFILE PAGE -->
      <div class="page" id="org-profile-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Organization Profile</h4>
          <button class="btn btn-primary" id="editOrgProfileBtn">
            <i class="ri-edit-line me-2"></i>Edit Profile
          </button>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h5 class="m-0"><i class="ri-building-2-line me-2"></i>Registered Entity Information</h5>
            <p class="mt-2">Maintain your organization details. (In production this should sync from your database.)</p>
          </div>
          <div class="form-steps-container">
            <div class="text-center mb-4">
              <img src="https://ui-avatars.com/api/?name=Mass+Media+Service&size=120&background=facc15&color=000&bold=true"
                   class="rounded-circle" alt="Profile" width="120" height="120">
              <h5 class="mt-3 mb-1" id="orgNameHeading">Mass Media Service</h5>
              <p class="text-muted mb-1">Applicant Account</p>
              <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-light text-dark">Ref: ZMC-AP1-2023-031</span>
                <span class="badge bg-warning text-dark">Pending Review</span>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Organization Name</label>
                <input type="text" class="form-control" value="Mass Media Service" readonly>
              </div>
              <div class="form-field">
                <label class="form-label">Primary Contact Email</label>
                <input type="email" class="form-control" value="info@mediahouse.co.zw" readonly>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" value="+263 77 000 0000" readonly>
              </div>
              <div class="form-field">
                <label class="form-label">Head Office Address</label>
                <input type="text" class="form-control" value="Harare, Zimbabwe" readonly>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Type of Mass Media Activities</label>
                <input type="text" class="form-control" value="Publishing / Online News" readonly>
              </div>
              <div class="form-field">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" value="https://example.com" readonly>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- COMMUNICATION PAGE -->
      <div class="page" id="communication-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Communication</h4>
          <div class="d-flex gap-2">
            <button class="btn btn-secondary" id="clearCommLog">
              <i class="ri-delete-bin-6-line me-1"></i>Clear Log
            </button>
            <button class="btn btn-primary" id="sendSupportEmailBtn">
              <i class="ri-mail-send-line me-2"></i>Compose Email to ZMC
            </button>
          </div>
        </div>

        <div class="comm-grid">
          <div class="form-container">
            <div class="form-header">
              <h5 class="m-0"><i class="ri-mail-line me-2"></i>Email Composer (Embedded)</h5>
              <p class="mt-2">This composer logs messages locally (demo) and can also open your default mail client.</p>
            </div>
            <div class="form-steps-container">
              <div class="form-field">
                <label class="form-label required">To</label>
                <input type="email" class="form-control" id="emailTo" value="zmcaccreditation@gmail.com" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Subject</label>
                <input type="text" class="form-control" id="emailSubject" placeholder="e.g. AP1 Submission Follow-up" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Message</label>
                <textarea class="form-control" id="emailBody" rows="7" placeholder="Type your message..." required></textarea>
                <div class="form-hint">Tip: Include your reference number (e.g. ZMC-AP1-YYYY-XXX).</div>
              </div>

              <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" id="logEmailBtn">
                  <i class="ri-save-line me-2"></i>Save to Communication Log
                </button>
                <button class="btn btn-secondary" id="openMailClientBtn">
                  <i class="ri-external-link-line me-2"></i>Open Mail Client
                </button>
              </div>

              <div class="alert alert-light border mt-3 mb-0">
                <div class="fw-bold"><i class="ri-lock-2-line me-2"></i>Note</div>
                <div class="text-muted">In production: connect this to your email/ticketing backend (SMTP/API) and store threads per application.</div>
              </div>
            </div>
          </div>

          <div class="form-container">
            <div class="form-header">
              <h5 class="m-0"><i class="ri-chat-3-line me-2"></i>Communication Log</h5>
              <p class="mt-2">Saved messages (demo) — stored in your browser (localStorage).</p>
            </div>
            <div class="form-steps-container">
              <div id="commLog"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- SETTINGS PAGE -->
      <div class="page" id="settings-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Settings</h4>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h5 class="m-0"><i class="ri-settings-3-line me-2"></i>Account Settings</h5>
          </div>
          <div class="form-steps-container">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Organization Display Name</label>
                <input type="text" class="form-control" value="Mass Media Service">
              </div>
              <div class="form-field">
                <label class="form-label">Notification Email</label>
                <input type="email" class="form-control" value="info@mediahouse.co.zw">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" value="+263 77 000 0000">
              </div>
              <div class="form-field">
                <label class="form-label">Language Preference</label>
                <select class="form-control">
                  <option>English</option>
                  <option>Shona</option>
                  <option>Ndebele</option>
                </select>
              </div>
            </div>

            <div class="form-buttons">
              <button type="button" class="btn btn-primary" onclick="alert('Demo: Settings saved')">Save Changes</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- CHATBOT -->
  <button class="chatbot-fab" id="chatbotFab" aria-label="Open chatbot">
    <i class="ri-robot-2-line"></i>
  </button>

  <div class="chatbot-panel" id="chatbotPanel" aria-live="polite">
    <div class="chatbot-header">
      <div>
        <div class="title">ZMC Assistant</div>
        <div class="sub">Mass Media Registration Help</div>
      </div>
      <button class="btn btn-secondary" style="padding:6px 10px;border-radius:10px;" id="chatbotClose">
        <i class="ri-close-line"></i>
      </button>
    </div>
    <div class="chatbot-body" id="chatbotBody"></div>
    <div class="chatbot-footer">
      <input id="chatbotInput" type="text" placeholder="Ask about AP1, AP5, annexures, payments..." />
      <button id="chatbotSend"><i class="ri-send-plane-2-line"></i></button>
    </div>
  </div>

  <!-- Bootstrap JS (for modals) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
      document.getElementById('downloadAP1')?.addEventListener('click', () => alert('Connect this to your backend/static storage to serve AP1 PDF for download.'));
      document.getElementById('downloadAP5')?.addEventListener('click', () => alert('Connect this to your backend/static storage to serve AP5 DOC for download.'));

      // Preview AP1 modal
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
        window.location.href = `mailto:${to}?subject=${subject}&body=${body}`;
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
          area.style.borderColor = '#10b981';
          area.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';

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
      document.getElementById(`${pageId}-page`).classList.add('active');

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
        if(!document.getElementById('ap1-declaration-agree').checked){
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
        if(!document.getElementById('ap5-declaration-agree').checked){
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
          continue; // mostly optional on AP5 template
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
      document.getElementById('ap5-changes-no').checked = true;

      updateNavButtons();
    }

    // Generic step UI updater
    function updateStepUI(pageId, prefix, maxSteps, activeStep, reset=false){
      const page = document.getElementById(pageId);
      const steps = page.querySelectorAll('.step');
      const contents = page.querySelectorAll('.step-content');

      steps.forEach(step => {
        const n = parseInt(step.dataset.step, 10);
        step.classList.remove('active','completed');
        const stepNumEl = step.querySelector('.step-number');

        if(reset) stepNumEl.textContent = String(n);

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

    // Communication log
    function saveCommMessage(msg){
      const key = 'zmc_comm_log';
      const existing = JSON.parse(localStorage.getItem(key) || '[]');
      existing.unshift({...msg, ts: new Date().toISOString()});
      localStorage.setItem(key, JSON.stringify(existing));
    }

    function renderCommLog(){
      const container = document.getElementById('commLog');
      if(!container) return;

      const logs = JSON.parse(localStorage.getItem('zmc_comm_log') || '[]');
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
        addBotBubble("Hi! I can help with AP1 registration, AP5 renewals, annexures, and contacting ZMC. What do you need?");
        addBotBubble("Try: “AP1 annexures”, “AP1 financials”, “AP5 changes”, “how to submit”, or “contact”.");
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
        return "AP1 annexures include: Certificate of incorporation, Memorandum & Articles, CR6/CR14, certified directors’ IDs, mission statement, code of ethics, code of conduct (if different), style book, plus Editorial Charter, Market Analysis, 3-year cash flow projection, Financial statement & balance sheet, and a dummy magazine/sample.";
      }
      if(t.includes('financial') || t.includes('balance') || t.includes('cash flow')){
        return "Financial annexures: upload 3-year projected cash flow and your financial statement & balance sheet (PDF/XLS). These are required in the AP1 annexures step.";
      }
      if(t.includes('market analysis')){
        return "Market analysis should explain your target audience, competitors, pricing model, distribution channels, and demand forecasts. Upload it in AP1 Annexures.";
      }
      if(t.includes('editorial') || t.includes('charter')){
        return "Upload your Editorial Charter in AP1 Annexures. It should cover editorial independence, ethics, corrections policy, conflicts of interest, and governance.";
      }
      if(t.includes('ap5') && t.includes('change')){
        return "On AP5, indicate if there are changes since the previous application (YES/NO). If YES, describe changes; ZMC may request a new application if changes are material.";
      }
      if(t.includes('how') && (t.includes('submit') || t.includes('apply'))){
        return "Go to “New Registration (AP1)”, complete each step, upload all required annexures, confirm the declaration and submit. Track your reference on Home.";
      }
      if(t.includes('contact') || t.includes('email') || t.includes('communication')){
        return "Use the Communication page to compose an email to ZMC. Include your reference number in subject/body.";
      }
      if(t.includes('payment') || t.includes('invoice')){
        return "Check Payment History for invoice status and receipts. In production this should pull official invoice records.";
      }

      return "I can help with AP1/AP5 steps, annexures, financial docs, market analysis, and contacting ZMC. Try: “AP1 annexures” or “AP1 financials”.";
    }
  </script>
</body>
</html>
