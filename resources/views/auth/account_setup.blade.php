<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Setup | ZMC Online Portal</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@700;800;900&display=swap" rel="stylesheet">

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --bg: #fafafa;
            --card: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --text: #0f172a;
            --primary: #000000;
            --primary-hover: #1a1a1a;
            --accent: #facc15;
            --accent-dark: #eab308;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --radius: 16px;
        }

        *{box-sizing:border-box}
        body{
            margin:0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: #000 url('/zmc_building.png') no-repeat center center;
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
            background: rgba(0, 0, 0, 0.45);
            pointer-events: none;
        }

        .wrap{ width: 100%; max-width: 520px; position: relative; z-index: 1; }

        .brand{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            text-decoration: none;
        }
        .brand img { height: 70px; width: 70px; background: white; padding: 6px; border-radius: 50%; object-fit: contain; box-shadow: 0 2px 12px rgba(0,0,0,0.2); }
        .brand span{
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 900;
            font-size: 20px;
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
            background: rgba(0, 0, 0, 0.08);
            color: var(--primary);
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
        .input.is-invalid{
            border-color: #ef4444;
            background: #fef2f2;
        }
        .input:focus{
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.15);
        }

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

        .error-message{
            color: #ef4444;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .info-box{
            background: rgba(250, 204, 21, 0.1);
            border-left: 3px solid var(--accent);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            color: #78350f;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box i{ font-size: 18px; flex-shrink: 0; }

        .btn{
            width: 100%;
            height: 52px;
            background-color: var(--primary);
            color: var(--accent);
            border: 2px solid var(--accent);
            border-radius: 12px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            transition: all 0.2s ease;
        }
        .btn:hover { 
            background-color: var(--accent);
            color: var(--primary);
            border-color: var(--primary);
        }
        .btn:active{ transform: translateY(1px); }

        .footer-text{
            text-align: center;
            font-size: 14px;
            color: var(--muted);
            margin-top: 20px;
        }
        .footer-text a{
            color: var(--primary);
            font-weight: 900;
            text-decoration: none;
        }
        .footer-text a:hover{ 
            color: var(--accent-dark);
            text-decoration: underline; 
        }

        @media (max-width: 480px){
            .card { padding: 30px 20px; }
        }
    </style>
</head>

<body class="landing-page">

<div class="wrap">
    {{-- Brand with Logo --}}
    <a href="{{ url('/') }}" class="brand">
        <img src="{{ asset('zimbabwe_media_commission_transparent_edges.png') }}" alt="ZMC Logo">
        <span>ZMC Portal</span>
    </a>

    <div class="card">
        <div class="header">
            <div class="eyebrow">
                <i class="ri-shield-user-line" aria-hidden="true"></i>
                Account Setup
            </div>

            <h1 class="title">Complete Your Setup</h1>

            <p class="subtitle">
                Set up your credentials to access the ZMC system
            </p>
        </div>

        <form method="POST" action="{{ route('account.setup.update', $token) }}">
            @csrf

            <div class="field">
                <label for="name">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="input @error('name') is-invalid @enderror"
                    placeholder="Enter your full name"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                >
                @error('name')
                    <div class="error-message">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="field">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="input @error('email') is-invalid @enderror"
                    placeholder="yourname@example.com"
                    value="{{ old('email', $user->email) }}"
                    required
                >
                @error('email')
                    <div class="error-message">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="field">
                <label for="password">New Password <span style="font-weight:500;color:var(--muted);">(minimum 6 characters)</span></label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input @error('password') is-invalid @enderror"
                        placeholder="Create a strong password"
                        required
                    >
                    <button type="button" class="toggle" id="togglePwd" aria-label="Toggle password visibility">
                        <i id="eyeIcon" class="ri-eye-off-line" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="input"
                        placeholder="Retype your password"
                        required
                    >
                    <button type="button" class="toggle" id="togglePwdConfirm" aria-label="Toggle password visibility">
                        <i id="eyeIconConfirm" class="ri-eye-off-line" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="info-box">
                <i class="ri-information-line"></i>
                <span>Your existing data and applications will be preserved after setup.</span>
            </div>

            <button type="submit" class="btn">
                <i class="ri-checkbox-circle-line" aria-hidden="true"></i>
                Complete Account Setup
            </button>

            <div class="footer-text">
                <a href="{{ route('login') }}">
                    <i class="ri-arrow-left-line"></i>
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        // Toggle password visibility
        const btn = document.getElementById('togglePwd');
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (btn && input && icon) {
            btn.addEventListener('click', function () {
                const isPassword = input.type === "password";
                input.type = isPassword ? "text" : "password";
                icon.classList.toggle('ri-eye-off-line', !isPassword);
                icon.classList.toggle('ri-eye-line', isPassword);
            });
        }

        // Toggle password confirmation visibility
        const btnConfirm = document.getElementById('togglePwdConfirm');
        const inputConfirm = document.getElementById('password_confirmation');
        const iconConfirm = document.getElementById('eyeIconConfirm');
        if (btnConfirm && inputConfirm && iconConfirm) {
            btnConfirm.addEventListener('click', function () {
                const isPassword = inputConfirm.type === "password";
                inputConfirm.type = isPassword ? "text" : "password";
                iconConfirm.classList.toggle('ri-eye-off-line', !isPassword);
                iconConfirm.classList.toggle('ri-eye-line', isPassword);
            });
        }
    })();
</script>

</body>
</html>
