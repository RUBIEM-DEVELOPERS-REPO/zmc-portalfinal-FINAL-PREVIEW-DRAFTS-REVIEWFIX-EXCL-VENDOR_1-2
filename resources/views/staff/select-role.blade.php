<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Staff Portal | ZMC Online Portal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    :root{
      --bg: #f0f7f0;
      --card: #ffffff;
      --border: #e2e8f0;
      --muted: #64748b;
      --text: #0f172a;
      --primary: #2d5016;
      --primary-hover: #1f3a0f;
      --accent: #facc15;
      --accent-dark: #eab308;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      --radius: 16px;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      font-size: 14px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      background: #000 url('{{ asset("zmc_building.png") }}') no-repeat center center fixed;
      background-size: cover;
      color: var(--text);
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:100vh;
      padding:20px;
      position: relative;
    }
    body::before{
      content: "";
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(45, 80, 22, 0.45);
      pointer-events: none;
    }
    .wrap{ width:100%; max-width:480px; position: relative; z-index: 1; }
    .brand{
      display:flex; justify-content:center; align-items:center; gap:12px;
      margin-bottom:25px; text-decoration:none;
    }
    .brand img{ height:50px; width:auto; }
    .brand span{
      font-weight:900; font-size:20px; color:var(--text);
      letter-spacing:-0.5px; text-transform:uppercase;
    }
    .card{
      background:var(--card);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      padding:40px;
    }
    .header{text-align:center; margin-bottom:28px;}
    .eyebrow{
      display:inline-flex; align-items:center; gap:6px;
      background: rgba(45, 80, 22, 0.08);
      color: var(--primary);
      padding:6px 14px;
      border-radius:99px;
      font-size:11px;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.5px;
      margin-bottom:15px;
    }
    .title{ margin:0 0 8px; font-size:26px; font-weight:900; color:#111827;}
    .subtitle{ margin:0; font-size:14px; color:var(--muted); }
    .field{ margin-bottom: 20px; }
    label{ display:block; font-size: var(--font-size-base); font-weight:900; margin-bottom:8px; color:#111827; }
    .select{
      width:100%;
      height:52px;
      padding:0 14px;
      border-radius:12px;
      border:1px solid var(--border);
      font-size:14px;
      outline:none;
      background:#fff;
      cursor:pointer;
      font-weight:600;
    }
    .select:focus{
      border-color:var(--accent);
      box-shadow:0 0 0 4px rgba(250, 204, 21, 0.15);
    }
    .role-desc{
      margin-top:10px;
      padding:12px 14px;
      background:rgba(26, 58, 26, 0.05);
      border-radius:10px;
      font-size: var(--font-size-base);
      color:var(--muted);
      min-height:60px;
      display:none;
    }
    .role-desc.active{ display:block; }
    .btn{
      width:100%;
      height:52px;
      background:var(--primary);
      color:var(--accent);
      border:2px solid var(--accent);
      border-radius:12px;
      font-size:15px;
      font-weight:900;
      cursor:pointer;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      margin-top:10px;
      transition: background .2s, transform .08s ease;
    }
    .btn:hover{ 
      background: var(--accent);
      color: var(--primary);
      border-color: var(--primary);
    }
    .btn:active{ transform: translateY(1px); }
    .btn:disabled{ 
      background: rgba(45, 80, 22, 0.4);
      color: rgba(250, 204, 21, 0.5);
      border-color: rgba(250, 204, 21, 0.5);
      cursor:not-allowed; 
    }
    .footer-text{
      text-align:center;
      font-size: 13px;
      color: var(--muted);
      margin-top: 18px;
    }
    .back-link{
      display:block;
      text-align:center;
      margin-top:16px;
      color:var(--primary);
      font-size: var(--font-size-base);
      font-weight:700;
      text-decoration:none;
    }
    .back-link:hover{ text-decoration:underline; }
  </style>
</head>
<body>
<div class="wrap">
  <a href="{{ url('/') }}" class="brand">
    <img src="{{ asset('zimbabwe_media_commission_transparent_edges.png') }}" alt="ZMC Logo">
    <span>ZMC Portal</span>
  </a>

  <div class="card">
    <div class="header">
      <div class="eyebrow">
        <i class="ri-shield-keyhole-line"></i> Staff Access
      </div>
      <h1 class="title">Staff Portal</h1>
      <p class="subtitle">Select your role and sign in to access your dashboard.</p>
    </div>

    <form method="POST" action="{{ route('staff.choose_role') }}">
      @csrf
      <div class="field">
        <label for="role"><i class="ri-user-settings-line"></i> Select Your Role</label>
        <select name="role" id="role" class="select" required>
          <option value="">-- Choose a role --</option>
          @foreach($roles as $role)
            <option value="{{ $role['key'] }}" data-desc="{{ $role['desc'] }}">{{ $role['title'] }}</option>
          @endforeach
        </select>
        <div class="role-desc" id="roleDesc"></div>
      </div>

      <button type="submit" class="btn" id="submitBtn" disabled>
        <i class="ri-login-box-line"></i> Continue to Login
      </button>

      <div class="footer-text">
        Internal ZMC staff only. Contact IT for access issues.
      </div>
    </form>
  </div>

  <a href="{{ url('/') }}" class="back-link">
    <i class="ri-arrow-left-line"></i> Back to Home
  </a>
</div>

<script>
  const select = document.getElementById('role');
  const desc = document.getElementById('roleDesc');
  const btn = document.getElementById('submitBtn');

  select.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const descText = option.dataset.desc || '';
    
    if (this.value) {
      desc.textContent = descText;
      desc.classList.add('active');
      btn.disabled = false;
    } else {
      desc.classList.remove('active');
      btn.disabled = true;
    }
  });
</script>
</body>
</html>
