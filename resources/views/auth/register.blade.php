<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | ZMC Online Portal</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --bg: #fafafa;
            --card: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --text: #111827;
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: #000 url('{{ asset("zmc_building.png") }}') no-repeat center center fixed;
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
            background: rgba(45, 80, 22, 0.45);
            pointer-events: none;
        }

        .wrap{ width: 100%; max-width: 480px; position: relative; z-index: 1; }

        /* BRAND SECTION */
        .brand{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            text-decoration: none;
        }
        .brand img {
            height: 40px; /* Adjust height to fit with text */
            width: auto;
        }
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
            padding: 30px;
        }

        .header{ text-align: center; margin-bottom: 20px; }
        .title{ margin: 0 0 8px; font-size: 22px; font-weight: 800; color: #111827; }
        .subtitle{ margin: 0; font-size: 13px; color: var(--muted); }

        /* FORM STYLING */
        .grid{ 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 12px; 
            margin-bottom: 15px;
        }

        .field{ margin-bottom: 15px; }
        label{ 
            display: block; 
            font-size: 12px; 
            font-weight: 700; 
            margin-bottom: 4px; 
            color: #111827;
        }

        .input-group{ position: relative; }
        .input{
            width: 100%;
            height: 40px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
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
        }

        .btn{
            width: 100%;
            height: 44px;
            background-color: var(--primary);
            color: var(--accent);
            border: 2px solid var(--accent);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 5px;
            transition: all 0.2s ease;
        }
        .btn:hover{ 
            background-color: var(--accent);
            color: var(--primary);
            border-color: var(--primary);
        }

        .divider{
            display: flex;
            align-items: center;
            margin: 15px 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .divider::before, .divider::after{
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .divider span{ padding: 0 10px; }

        .footer-link{
            text-align: center;
            font-size: 13px;
            font-weight: 700;
        }
        .footer-link a{
            color: var(--primary);
            text-decoration: none;
        }
        .footer-link a:hover{
            color: var(--accent-dark);
            text-decoration: underline;
        }
        .footer-link a:hover{ text-decoration: underline; }

        .error{ color: var(--danger); font-size: 12px; margin-top: 5px; font-weight: 600; }

        @media (max-width: 480px){
            .grid{ grid-template-columns: 1fr; }
            .card{ padding: 25px; }
        }
    </style>
</head>

<body>
<div class="page">
    <div class="wrap">
        @php
            $selectedPortal = session('public_selected_portal', 'journalist');
            $isMediaHouse = $selectedPortal === 'mass_media';
        @endphp
        {{-- BRAND SECTION WITH IMAGE LOGO --}}
        <div class="brand">
            <img src="{{ asset('zimbabwe_media_commission_transparent_edges.png') }}" alt="ZMC Logo">
            <span>ZMC PORTAL</span>
        </div>

        <div class="card">
            <div class="header">
                <h1 class="title">Create Account</h1>
                <p class="subtitle">One account for all ZMC digital services.</p>
            </div>

            <form method="POST" action="{{ route('auth.register.store') }}">

                @csrf

                @if($isMediaHouse)
                    <div class="field">
                        <label for="organization_name">Organization's Name</label>
                        <input id="organization_name" name="organization_name" type="text" class="input" value="{{ old('organization_name') }}" required autofocus>
                        @error('organization_name') <div class="error">{{ $message }}</div> @enderror
                    </div>
                @else
                    <div class="grid">
                        <div>
                            <label for="first_name">Name</label>
                            <input id="first_name" name="first_name" type="text" class="input" value="{{ old('first_name') }}" required autofocus>
                            @error('first_name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="last_name">Surname</label>
                            <input id="last_name" name="last_name" type="text" class="input" value="{{ old('last_name') }}" required>
                            @error('last_name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endif

                <div class="field">
                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="grid">
                    <div>
                        <label for="phone_country_code">Phone Country Code</label>
                        <select id="phone_country_code" name="phone_country_code" class="input">
                            <option value="">Select</option>
                            <option value="+263" @selected(old('phone_country_code')=='+263')>🇿🇼 +263 (Zimbabwe)</option>
                            <option value="+27" @selected(old('phone_country_code')=='+27')>🇿🇦 +27 (South Africa)</option>
                            <option value="+260" @selected(old('phone_country_code')=='+260')>🇿🇲 +260 (Zambia)</option>
                            <option value="+258" @selected(old('phone_country_code')=='+258')>🇲🇿 +258 (Mozambique)</option>
                            <option value="+1" @selected(old('phone_country_code')=='+1')>🇺🇸 +1 (US/Canada)</option>
                            <option value="+44" @selected(old('phone_country_code')=='+44')>🇬🇧 +44 (UK)</option>
                        </select>
                        @error('phone_country_code') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="phone_number">Phone Number</label>
                        <input id="phone_number" name="phone_number" type="text" class="input" value="{{ old('phone_number') }}">
                        @error('phone_number') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="input" required>
                        <button type="button" class="toggle" id="togglePwd"><i class="ri-eye-off-line"></i></button>
                    </div>
                    @error('password') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-group">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input" required>
                        <button type="button" class="toggle" id="togglePwd2"><i class="ri-eye-off-line"></i></button>
                    </div>
                </div>

                <button class="btn" type="submit">
                    <i class="ri-user-add-line"></i> Create Account
                </button>

                <div class="divider">
                    <span>Already have an account?</span>
                </div>

                <div class="footer-link">
                    <a href="{{ route('login') }}">Sign in to your account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            if (!btn || !input) return;
            btn.addEventListener('click', function () {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                btn.querySelector('i').className = isPassword ? 'ri-eye-line' : 'ri-eye-off-line';
            });
        }
        setupToggle('togglePwd', 'password');
        setupToggle('togglePwd2', 'password_confirmation');
    })();
</script>
</body>
</html>