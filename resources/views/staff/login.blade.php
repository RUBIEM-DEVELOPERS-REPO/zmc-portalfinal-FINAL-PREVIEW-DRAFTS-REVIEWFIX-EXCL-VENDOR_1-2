<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Login | ZMC Online Portal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --bg: #fafafa;
            --card: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --text: #0f172a;
            --primary: #1a1a1a;
            --primary-hover: #111111;
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
            background: #000 url('/zmc_building.png') no-repeat center center;
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
            background: rgba(0, 0, 0, 0.55);
            pointer-events: none;
        }
        .wrap{ width:100%; max-width:480px; position: relative; z-index: 1; }
        .brand{
            display:flex; justify-content:center; align-items:center; gap:12px;
            margin-bottom:25px; text-decoration:none;
        }
<<<<<<< HEAD
        .brand img{ width: 100%; height: 100%; object-fit: contain; display: block; margin: 0; mix-blend-mode: multiply; }
        .logo-circle {
            width: 110px;
            height: 110px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .brand span{
            font-weight:900; font-size:28px; color:var(--text);
=======
        .brand img{ height:70px; width:70px; background:white; padding:6px; border-radius:50%; object-fit:contain; box-shadow:0 2px 12px rgba(0,0,0,0.2); }
        .brand span{
            font-family:'Montserrat','Inter',sans-serif;
            font-weight:800; font-size:17px; color:var(--text);
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
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
            background: rgba(245, 197, 24, 0.08);
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

        .error-box{
            margin-top: 14px;
            padding: 12px 14px;
            border: 1px solid #fecaca;
            background: #fff1f2;
            color: #991b1b;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            text-align: left;
        }

        .field{ margin-bottom: 20px; }
        label{ display:block; font-size: var(--font-size-base); font-weight:900; margin-bottom:8px; color:#111827; }
        .input-group{ position:relative; }
        .input{
            width:100%;
            height:48px;
            padding:0 14px;
            border-radius:10px;
            border:1px solid var(--border);
            font-size:14px;
            outline:none;
            transition: all .2s ease;
            background:#fff;
        }
        .input:focus{
            border-color:var(--accent);
            box-shadow:0 0 0 4px rgba(250, 204, 21, 0.15);
        }
        .pwd-row{
            display:flex; align-items:center; justify-content:space-between;
            margin-bottom:8px; gap:10px;
        }
        .forgot{
            font-size: var(--font-size-sm); font-weight:900; color:var(--primary);
            text-decoration:none; white-space:nowrap;
        }
        .forgot:hover{ 
            color: var(--accent-dark);
            text-decoration: underline; 
        }
        .forgot:hover{ text-decoration:underline; }
        .toggle{
            position:absolute; right:12px; top:50%;
            transform:translateY(-50%);
            background:none; border:none; color:var(--muted);
            cursor:pointer; padding:0;
            display:flex; align-items:center; justify-content:center;
            height:42px; width:42px;
        }
        .toggle i{ font-size:18px; }
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
        .footer-text{
            text-align:center;
            font-size: 13px;
            color: var(--muted);
            margin-top: 18px;
            font-weight: 700;
        }
        @media (max-width:480px){
            .card{ padding:30px 20px; }
        }
    </style>
</head>

<body class="landing-page">
<div class="wrap">
    <a href="{{ url('/') }}" class="brand">
        <div class="logo-circle">
            <img src="{{ asset('zmc_logo_circular.png') }}" alt="ZMC Logo">
        </div>
        <span>ZMC Portal</span>
    </a>

    <div class="card">
        <div class="header">
            <div class="eyebrow">
                <i class="ri-shield-user-line" aria-hidden="true"></i> Staff Access
            </div>
            <h1 class="title">Staff Login</h1>
            <p class="subtitle">Sign in to access staff dashboards.</p>

            @if ($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            @if (session('success'))
                <div class="error-box" style="border-color:#bbf7d0;background:#f0fdf4;color:#166534;">{{ session('success') }}</div>
            @endif
        </div>

        <form method="POST" action="{{ route('staff.login.store') }}">
            @csrf

            <div class="field">
                <label for="email">Email / Username</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    class="input"
                    placeholder="e.g. staff@zmc.co.zw"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="field">
                <div class="pwd-row">
                    <label for="password" style="margin:0;">Password</label>
                    <a class="forgot" href="#">Forgot Password?</a>
                </div>

                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="toggle" id="togglePwd" aria-label="Toggle password visibility">
                        <i id="eyeIcon" class="ri-eye-off-line" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="field" style="margin-bottom: 10px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:var(--muted);font-weight:700;">
                    <input type="checkbox" name="remember" style="width:16px;height:16px;accent-color:var(--primary);">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn">
                <i class="ri-login-box-line" aria-hidden="true"></i> Sign In (Staff)
            </button>

            <div class="footer-text">
                Staff accounts are created by the administrator.
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const btn = document.getElementById('togglePwd');
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (!btn || !input || !icon) return;

        btn.addEventListener('click', function () {
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            icon.classList.toggle('ri-eye-off-line', !isPassword);
            icon.classList.toggle('ri-eye-line', isPassword);
        });
    })();
</script>
</body>
</html>
