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

        .wrap{ width: 100%; max-width: 520px; }

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
        .input.is-invalid{
            border-color: #ef4444;
            background: #fef2f2;
        }
        .input:focus{
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
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
            background: rgba(46, 125, 50, 0.05);
            border-left: 3px solid var(--green);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            color: var(--green);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box i{ font-size: 18px; flex-shrink: 0; }

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
                <label>Phone Number <span style="font-weight:500;color:var(--muted);">(with country code)</span></label>
                <div class="row">
                    <div class="col-md-5">
                        <select name="phone_country_code" class="input" required>
                            <option value="">Country Code</option>
                            @foreach(\App\Models\Country::where('is_active', true)->orderBy('name')->get() as $country)
                                <option value="{{ $country->code }}" {{ old('phone_country_code') == $country->code ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->phone_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input
                            type="tel"
                            name="phone_number"
                            class="input @error('phone_number') is-invalid @enderror"
                            placeholder="Phone Number"
                            value="{{ old('phone_number') }}"
                            required
                        >
                    </div>
                </div>
                @error('phone_number')
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
                <a href="{{ route('staff.login') }}">
                    <i class="ri-arrow-left-line"></i>
                    Back to Staff Login
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
