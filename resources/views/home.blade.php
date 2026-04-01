<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZMC Digital Services</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <link href="{{ asset('css/green-theme.css') }}" rel="stylesheet">

    <style>
        :root {
            --text-main: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: var(--font-primary);
            font-size: var(--font-size-base);
            line-height: var(--line-height-normal);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: #000 url('{{ asset("zmc_building.png") }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(45, 80, 22, 0.45);
            pointer-events: none;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .brand img {
            height: 120px;
            width: 120px;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 20px rgba(250, 204, 21, 0.2));
            background: white;
            padding: 10px;
            border-radius: 50%;
            object-fit: contain;
        }

        .welcome-hero { margin-bottom: 35px; text-align: right; }

        .welcome-name {
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-bold);
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 5px;
            display: block;
        }

        .header-title {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-black);
            text-transform: uppercase;
            letter-spacing: -1px;
            margin: 0;
            color: #ffffff;
        }

        .header-title span.zimbabwe { color: #fff; }
        .header-title span.media { color: #4caf50; }
        .header-title span.commission { color: #facc15; }

        .instruction {
            font-size: var(--font-size-sm);
            color: var(--text-muted);
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            width: 100%;
        }

        .portal-card {
            background: rgba(45, 80, 22, 0.40);
            border: 1px solid rgba(250, 204, 21, 0.3);
            padding: 55px 45px;
            border-radius: 8px;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            backdrop-filter: blur(15px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            width: 100%;
            cursor: pointer;
            text-align: left;
            min-height: 320px;
        }

        .portal-card:hover {
            border-color: var(--accent);
            background: rgba(45, 80, 22, 0.60);
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(250, 204, 21, 0.3);
        }

        .portal-card h2 {
            font-size: var(--font-size-xl);
            font-weight: var(--font-weight-bold);
            text-transform: uppercase;
            margin: 0 0 15px;
            letter-spacing: 1.5px;
            color: var(--accent);
        }

        .portal-card p {
            color: var(--text-muted);
            font-size: var(--font-size-lg);
            line-height: var(--line-height-relaxed);
            margin-bottom: 30px;
        }

        .portal-type {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-black);
            margin: 20px 0;
            color: var(--text-main);
            letter-spacing: -1px;
        }

        .portal-type span {
            font-size: var(--font-size-xs);
            color: var(--accent);
            text-transform: uppercase;
            display: block;
            letter-spacing: 4px;
            margin-bottom: -5px;
            font-weight: var(--font-weight-bold);
        }

        .btn {
            padding: 16px 35px;
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-black);
            text-transform: uppercase;
            border-radius: 2px;
            transition: all 0.2s ease;
            letter-spacing: 2px;
            display: inline-block;
            border: 2px solid var(--accent);
            background: var(--accent);
            color: #2d5016;
            width: fit-content;
        }

        .portal-card:hover .btn {
            background: transparent;
            color: var(--accent);
        }

        .feature-bar {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 40px;
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-bold);
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .feature-bar span i {
            color: var(--accent);
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            .header-title { font-size: 28px; }
            .container { padding-top: 60px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="brand">
        <img src="{{ asset('zmc_logo.png') }}" alt="ZMC Logo">
    </div>

    <div class="welcome-hero">
        <span class="welcome-name">Public Access</span>
        <h1 class="header-title"><span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span></h1>
        <p class="instruction">Choose a service stream below to continue (you will be asked to login/signup).</p>
    </div>

    <div class="grid">
        <!-- Media Practitioner -->
        <form method="POST" action="{{ route('public.choose_portal') }}">
            @csrf
            <input type="hidden" name="portal" value="journalist">
            <button type="submit" class="portal-card" style="border:none;">
                <div>
                    <h2>Accreditation</h2>
                    <p>Apply for new press cards, renewals, replacements, and manage your media practitioner profile.</p>
                </div>
                <div class="portal-type">
                    <span>Stream</span>
                    MEDIA PRACTITIONER
                </div>
                <div class="btn">Continue</div>
            </button>
        </form>

        <!-- Mass Media -->
        <form method="POST" action="{{ route('public.choose_portal') }}">
            @csrf
            <input type="hidden" name="portal" value="mass_media">
            <button type="submit" class="portal-card" style="border:none;">
                <div>
                    <h2>Registration</h2>
                    <p>Register and manage a media house, renewals, payments, and regulatory notices.</p>
                </div>
                <div class="portal-type">
                    <span>Stream</span>
                    MEDIA HOUSE
                </div>
                <div class="btn">Continue</div>
            </button>
        </form>
    </div>

    <div class="feature-bar">
        <span><i class="ri-checkbox-circle-fill"></i> Secure</span>
        <span><i class="ri-checkbox-circle-fill"></i> Tracking</span>
        <span><i class="ri-checkbox-circle-fill"></i> Verified</span>
        <span><i class="ri-checkbox-circle-fill"></i> 24/7 Access</span>
    </div>
</div>

</body>
</html>
