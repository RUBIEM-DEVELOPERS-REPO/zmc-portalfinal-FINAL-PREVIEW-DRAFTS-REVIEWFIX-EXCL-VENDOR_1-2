<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activate Account | ZMC Staff</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --bg: #fafafa; --card: #ffffff; --border: #e2e8f0; --muted: #64748b;
            --text: #0f172a; --primary: #1a1a1a; --accent: #facc15; --accent-dark: #eab308;
            --shadow: 0 10px 30px rgba(0,0,0,0.05); --radius: 16px;
        }
        *{box-sizing:border-box}
        body{
            margin:0; font-family:'Inter',-apple-system,sans-serif; font-size:14px;
            -webkit-font-smoothing:antialiased;
            background:#000 url('/zmc_building.png') no-repeat center center;
            background-size:cover; color:var(--text);
            display:flex; align-items:center; justify-content:center;
            min-height:100vh; padding:20px; position:relative;
        }
        body::before{
            content:""; position:fixed; top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.55); pointer-events:none;
        }
        .wrap{width:100%;max-width:480px;position:relative;z-index:1;}
        .brand{display:flex;justify-content:center;align-items:center;gap:12px;margin-bottom:25px;text-decoration:none;}
        .brand img{height:70px;width:70px;background:white;padding:6px;border-radius:50%;object-fit:contain;box-shadow:0 2px 12px rgba(0,0,0,0.2);}
        .brand span{font-weight:800;font-size:17px;color:var(--text);letter-spacing:-.5px;text-transform:uppercase;}
        .card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:40px;}
        .icon-circle{width:64px;height:64px;border-radius:50%;background:rgba(250,204,21,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;}
        .icon-circle i{font-size:28px;color:var(--accent-dark);}
        .header{text-align:center;margin-bottom:28px;}
        .title{margin:0 0 8px;font-size:22px;font-weight:900;color:#111827;}
        .subtitle{margin:0;font-size:13px;color:var(--muted);line-height:1.6;}
        .subtitle strong{color:var(--text);}
        .error-box{margin-bottom:16px;padding:12px 14px;border:1px solid #fecaca;background:#fff1f2;color:#991b1b;border-radius:12px;font-size:13px;font-weight:700;text-align:left;}
        .field{margin-bottom:20px;}
        label{display:block;font-size:14px;font-weight:900;margin-bottom:8px;color:#111827;}
        .input{
            width:100%;height:48px;padding:0 14px;border-radius:10px;border:1px solid var(--border);
            font-size:14px;outline:none;transition:all .2s;background:#fff;
        }
        .input:focus{border-color:var(--accent);box-shadow:0 0 0 4px rgba(250,204,21,0.15);}
        .btn{
            width:100%;height:52px;background:var(--primary);color:var(--accent);
            border:2px solid var(--accent);border-radius:12px;font-size:15px;font-weight:900;
            cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;
            margin-top:10px;transition:background .2s,transform .08s;
        }
        .btn:hover{background:var(--accent);color:var(--primary);border-color:var(--primary);}
        .btn:active{transform:translateY(1px);}
        .info-box{margin-bottom:20px;padding:14px;border-radius:12px;background:rgba(250,204,21,0.08);border:1px solid rgba(250,204,21,0.2);}
        .info-box .label{font-size:11px;font-weight:900;text-transform:uppercase;color:var(--muted);margin-bottom:4px;}
        .info-box .value{font-size:14px;font-weight:700;color:var(--text);}
    </style>
</head>
<body>
<div class="wrap">
    <a href="{{ url('/') }}" class="brand">
        <img src="{{ asset('zimbabwe_media_commission_transparent_edges.png') }}" alt="ZMC Logo">
        <span>ZMC Portal</span>
    </a>

    <div class="card">
        <div class="icon-circle"><i class="ri-user-add-line"></i></div>
        <div class="header">
            <h1 class="title">Activate Your Account</h1>
            <p class="subtitle">Set your password to complete account activation.</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="info-box">
            <div class="label">Account</div>
            <div class="value">{{ $user->name }} ({{ $user->email }})</div>
        </div>

        <form method="POST" action="{{ route('staff.activate.store', $token) }}">
            @csrf

            <div class="field">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="input" placeholder="Minimum 8 characters" required minlength="8" autocomplete="new-password">
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="Re-enter your password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn">
                <i class="ri-check-double-line"></i> Activate Account
            </button>
        </form>
    </div>
</div>
</body>
</html>
