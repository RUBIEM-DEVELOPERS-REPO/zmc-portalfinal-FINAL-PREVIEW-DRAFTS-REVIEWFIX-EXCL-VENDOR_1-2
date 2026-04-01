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

    <style>
        :root {
            --bg-dark: #2e7d32;
            --bg-dark-2: #1b5e20;
            --zmc-green: #388e3c;
            --zmc-green-light: #4caf50;
            --zmc-yellow: #c9a227;
            --zmc-orange: #ea580c;
            --card-bg: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.15);
            --text-main: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
        }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            /* Fallback background */
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Background image using img element for better control */
        .bg-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            opacity: 0.9;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, 
                rgba(46, 125, 50, 0.25) 0%, 
                rgba(27, 94, 32, 0.35) 30%, 
                rgba(46, 125, 50, 0.45) 70%, 
                rgba(27, 94, 32, 0.55) 100%),
                radial-gradient(ellipse at bottom center, 
                transparent 0%, 
                rgba(0, 0, 0, 0.15) 50%, 
                rgba(0, 0, 0, 0.3) 100%);
            z-index: -1;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand img {
            height: 110px;
            width: 110px;
            margin-bottom: 25px;
            padding: 0;
            background: none;
            border-radius: 0;
            object-fit: contain;
            display: block;
            position: relative;
            z-index: 10;
            transition: all 0.3s ease;
        }
        .logo-circle-home {
            width: 135px;
            height: 135px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logo-circle-home:hover {
            transform: scale(1.05);
        }


        .welcome-hero { margin-bottom: 35px; text-align: right; }

        .welcome-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--zmc-yellow);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 5px;
            display: block;
        }

        .header-title {
            font-size: 36px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            margin: 0;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3), 
                         0 0 20px rgba(255, 255, 255, 0.1);
        }

        .header-title span.zimbabwe { 
            color: #fff; 
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }
        .header-title span.media { 
            color: #4caf50; 
            text-shadow: 0 2px 10px rgba(76, 175, 80, 0.3);
        }
        .header-title span.commission { 
            color: #fff; 
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .instruction {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            width: 100%;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .portal-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 45px 35px;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            backdrop-filter: blur(30px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            width: 100%;
            cursor: pointer;
            text-align: left;
            position: relative;
            overflow: hidden;
        }

        .portal-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.05) 0%, 
                transparent 50%, 
                rgba(255, 255, 255, 0.02) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .portal-card:hover::before {
            opacity: 1;
        }

        .portal-card:hover {
            border-color: rgba(201, 162, 39, 0.8);
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3), 
                        0 0 0 1px rgba(201, 162, 39, 0.2);
        }

        .portal-card h2 {
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 0 0 12px;
            letter-spacing: 1.5px;
            color: var(--zmc-yellow);
        }

        .portal-card p {
            color: var(--text-muted);
            font-size: 13.5px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .portal-type {
            font-size: 36px;
            font-weight: 900;
            margin: 15px 0;
            color: var(--text-main);
            letter-spacing: -1px;
        }

        .portal-type span {
            font-size: 10px;
            color: var(--zmc-yellow);
            text-transform: uppercase;
            display: block;
            letter-spacing: 4px;
            margin-bottom: -5px;
            font-weight: 700;
        }

        .btn {
            padding: 16px 32px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            border-radius: 6px;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            letter-spacing: 2px;
            display: inline-block;
            border: 2px solid var(--zmc-yellow);
            background: var(--zmc-yellow);
            color: #2e7d32;
            width: fit-content;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: "";
            position: absolute;
            top: 0; left: -100%; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .portal-card:hover .btn {
            background: transparent;
            color: var(--zmc-yellow);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(201, 162, 39, 0.3);
        }

        .portal-card:hover .btn::before {
            left: 100%;
        }

        .feature-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            flex-wrap: wrap;
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .feature-bar span {
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .feature-bar span:hover {
            color: var(--zmc-yellow);
            transform: translateY(-2px);
        }

        .feature-bar span i {
            color: var(--zmc-green);
            margin-right: 8px;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .feature-bar span:hover i {
            color: var(--zmc-yellow);
            transform: scale(1.2);
        }

        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            .header-title { font-size: 28px; }
            .container { padding-top: 60px; }
        }
    </style>
</head>
<body>
<img src="{{ asset('zmc_building.png') }}" alt="ZMC Building" class="bg-image">

<div class="container">
    <div class="brand">
        <div class="logo-circle-home">
            <img src="{{ asset('zmc_logo_circular.png') }}" alt="ZMC Logo" style="width:100%;height:100%;object-fit:contain;mix-blend-mode:multiply;">
        </div>
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
