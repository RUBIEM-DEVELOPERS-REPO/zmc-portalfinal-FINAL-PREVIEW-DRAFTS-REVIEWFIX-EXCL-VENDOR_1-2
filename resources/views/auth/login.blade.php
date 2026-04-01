<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | ZMC Online Portal</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@700;800;900&display=swap" rel="stylesheet">

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    @php
        $isStaffLogin = session()->has('staff_selected_role');
        $staffRoleKey = session('staff_selected_role');
        $staffRoleLabel = match ($staffRoleKey) {
            'accreditation_officer' => 'Accreditation Officer',
            'registrar' => 'Registrar',
            'accounts_payments' => 'Payments / Accounts',
            'production' => 'Production',
            default => 'Staff',
        };
    @endphp

    <style>
        :root{
            --bg: #f0f7f0;
            --card: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --text: #0f172a;
            --green: #2e7d32;
            --green-hover: #1b5e20;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --radius: 16px;
        }

        *{box-sizing:border-box}
        body{
            margin:0;
            font-family: Roboto, Inter, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            background: url('{{ asset("zmc_building.png") }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }
        body::before{
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(240, 247, 240, 0.92) 0%, rgba(220, 237, 220, 0.95) 100%);
            z-index: -1;
        }

        .wrap{ width: 100%; max-width: 480px; }

        .brand{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            text-decoration: none;
        }
        .brand img { width: 100%; height: 100%; object-fit: contain; display: block; margin: 0; mix-blend-mode: multiply; }
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
            font-family: Roboto, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            font-weight: 900;
            font-size: 28px;
            color: var(--text);
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .card{
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 40px;
        }

        .header{ text-align: center; margin-bottom: 28px; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(46, 125, 50, 0.1);
            color: var(--green);
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .title{ margin: 0 0 8px; font-size: 26px; font-weight: 900; color: #111827; }
        .subtitle{ margin: 0; font-size: 14px; color: var(--muted); }

        .field{ margin-bottom: 20px; }
        label{
            display: block;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
            color: #111827;
        }

        .input-group{ position: relative; }
        .input{
            width: 100%;
            height: 48px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            background:#fff;
        }
        .input:focus{
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }

        .pwd-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom: 8px;
            gap:10px;
        }
        .forgot{
            font-size: 12px;
            font-weight: 900;
            color: var(--green);
            text-decoration: none;
            white-space:nowrap;
        }
        .forgot:hover{ text-decoration: underline; }

        .toggle{
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            height: 42px;
            width: 42px;
            justify-content:center;
        }
        .toggle i{ font-size: 18px; }

        .btn{
            width: 100%;
            height: 52px;
            background-color: var(--green);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            transition: background 0.2s, transform .08s ease;
        }
        .btn:hover { background-color: var(--green-hover); }
        .btn:active{ transform: translateY(1px); }

        .footer-text{
            text-align: center;
            font-size: 14px;
            color: var(--muted);
            margin-top: 20px;
        }
        .footer-text a{
            color: var(--green);
            font-weight: 900;
            text-decoration: none;
        }
        .footer-text a:hover{ text-decoration: underline; }

        .staff-note{
            text-align:center;
            font-size: 13px;
            color: var(--muted);
            margin-top: 10px;
        }
        .staff-note strong{ color: #111827; }

        @media (max-width: 480px){
            .card { padding: 30px 20px; }
        }
    </style>
</head>

<body>

<div class="wrap">
    {{-- Brand with Logo --}}
    <a href="{{ url('/') }}" class="brand">
        <div class="logo-circle">
            <img src="{{ asset('zmc_logo_circular.png') }}" alt="ZMC Logo">
        </div>
        <span>ZMC Portal</span>
    </a>

    <div class="card">
        <div class="header">
            <div class="eyebrow">
                <i class="ri-shield-user-line" aria-hidden="true"></i>
                {{ $isStaffLogin ? 'Staff Access' : 'Secure Access' }}
            </div>

            <h1 class="title">{{ $isStaffLogin ? 'Staff Login' : 'Sign In' }}</h1>

            <p class="subtitle">
                @if($isStaffLogin)
                    Sign in to access staff tools.
                @else
                    Access your ZMC portal dashboard.
                @endif
            </p>

            @if($isStaffLogin)
                <p class="staff-note">
                    Role selected: <strong>{{ $staffRoleLabel }}</strong>
                    &nbsp;•&nbsp;
                    <a href="{{ url('/staff/select-role') }}" class="forgot">Change role</a>
                </p>
            @endif
        </div>

        <form method="POST" action="{{ $isStaffLogin ? route('staff.login.store') : route('auth.login.store') }}">

            @csrf

            <div class="field">
                <label for="email">Email / Username</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    class="input"
                    placeholder="e.g. john@example.com"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="field">
                <div class="pwd-row">
                    <label for="password" style="margin:0;">Password</label>
                    <a class="forgot" href="{{ route('password.request') }}">Forgot Password?</a>
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
                    <input type="checkbox" name="remember" style="width:16px;height:16px;accent-color:var(--green);">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn">
                <i class="ri-login-box-line" aria-hidden="true"></i>
                {{ $isStaffLogin ? 'Sign In (Staff)' : 'Sign In to Account' }}
            </button>

            {{-- PUBLIC ONLY: signup --}}
            @if(!$isStaffLogin)
                <div class="footer-text">
                    Don’t have an account? <a href="{{ route('register') }}">Sign Up</a>
                </div>
            @else
                <div class="footer-text">
                    Staff accounts are created by the administrator.
                </div>
            @endif

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
