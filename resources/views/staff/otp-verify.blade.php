<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification | ZMC Staff</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root{
            --bg: #fafafa; --card: #ffffff; --border: #e2e8f0; --muted: #64748b;
            --text: #0f172a; --primary: #1a1a1a; --primary-hover: #111111;
            --accent: #facc15; --accent-dark: #eab308;
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
        .wrap{width:100%;max-width:440px;position:relative;z-index:1;}
        .brand{display:flex;justify-content:center;align-items:center;gap:12px;margin-bottom:25px;text-decoration:none;}
        .brand img{height:70px;width:70px;background:white;padding:6px;border-radius:50%;object-fit:contain;box-shadow:0 2px 12px rgba(0,0,0,0.2);}
        .brand span{font-weight:800;font-size:17px;color:var(--text);letter-spacing:-.5px;text-transform:uppercase;}
        .card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:40px;text-align:center;}
        .icon-circle{width:64px;height:64px;border-radius:50%;background:rgba(250,204,21,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;}
        .icon-circle i{font-size:28px;color:var(--accent-dark);}
        .title{margin:0 0 8px;font-size:22px;font-weight:900;color:#111827;}
        .subtitle{margin:0 0 24px;font-size:13px;color:var(--muted);line-height:1.6;}
        .subtitle strong{color:var(--text);}
        .error-box{margin-bottom:16px;padding:12px 14px;border:1px solid #fecaca;background:#fff1f2;color:#991b1b;border-radius:12px;font-size:13px;font-weight:700;text-align:left;}
        .success-box{margin-bottom:16px;padding:12px 14px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;border-radius:12px;font-size:13px;font-weight:700;text-align:left;}
        .otp-inputs{display:flex;gap:8px;justify-content:center;margin-bottom:24px;}
        .otp-inputs input{
            width:48px;height:56px;text-align:center;font-size:22px;font-weight:900;
            border:2px solid var(--border);border-radius:12px;outline:none;
            transition:all .2s;background:#fff;
        }
        .otp-inputs input:focus{border-color:var(--accent);box-shadow:0 0 0 4px rgba(250,204,21,0.15);}
        .btn{
            width:100%;height:50px;background:var(--primary);color:var(--accent);
            border:2px solid var(--accent);border-radius:12px;font-size:15px;font-weight:900;
            cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;
            transition:background .2s,transform .08s;
        }
        .btn:hover{background:var(--accent);color:var(--primary);border-color:var(--primary);}
        .btn:active{transform:translateY(1px);}
        .resend{margin-top:20px;font-size:13px;color:var(--muted);font-weight:700;}
        .resend a{color:var(--primary);text-decoration:none;font-weight:900;}
        .resend a:hover{text-decoration:underline;color:var(--accent-dark);}
        .back-link{display:block;margin-top:14px;font-size:13px;color:var(--muted);text-decoration:none;font-weight:700;}
        .back-link:hover{color:var(--accent-dark);text-decoration:underline;}
        input[type="hidden"]{display:none;}
    </style>
</head>
<body>
<div class="wrap">
    <a href="{{ url('/') }}" class="brand">
        <img src="{{ asset('zimbabwe_media_commission_transparent_edges.png') }}" alt="ZMC Logo">
        <span>ZMC Portal</span>
    </a>

    <div class="card">
        <div class="icon-circle"><i class="ri-shield-keyhole-line"></i></div>
        <h1 class="title">OTP Verification</h1>
        <p class="subtitle">
            A 6-digit verification code has been sent to<br>
            <strong>{{ $maskedEmail }}</strong>
        </p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        @if (session('warning'))
            <div class="error-box" style="border-color:#fed7aa;background:#fff7ed;color:#9a3412;">
                <i class="ri-alert-line"></i> {{ session('warning') }}
            </div>
        @endif

        @if (session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('staff.otp.verify') }}" id="otpForm">
            @csrf
            <input type="hidden" name="otp" id="otpHidden">

            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-digit" data-index="0" autofocus inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric" pattern="[0-9]">
                <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric" pattern="[0-9]">
            </div>

            <button type="submit" class="btn">
                <i class="ri-check-line"></i> Verify & Sign In
            </button>
        </form>

        <div class="resend">
            Didn't receive the code?
            <form method="POST" action="{{ route('staff.otp.resend') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:var(--primary);font-weight:900;font-size:13px;font-family:inherit;">Resend OTP</button>
            </form>
        </div>

        <a href="{{ route('staff.login') }}" class="back-link">
            <i class="ri-arrow-left-line"></i> Back to Login
        </a>
    </div>
</div>

<script>
(function(){
    const digits = document.querySelectorAll('.otp-digit');
    const hidden = document.getElementById('otpHidden');
    const form = document.getElementById('otpForm');

    function collectOtp(){
        let val = '';
        digits.forEach(d => val += d.value);
        hidden.value = val;
    }

    digits.forEach((input, i) => {
        input.addEventListener('input', function(){
            this.value = this.value.replace(/[^0-9]/g, '');
            if(this.value && i < digits.length - 1) digits[i+1].focus();
            collectOtp();
            if(i === digits.length - 1 && hidden.value.length === 6) form.submit();
        });

        input.addEventListener('keydown', function(e){
            if(e.key === 'Backspace' && !this.value && i > 0){
                digits[i-1].focus();
                digits[i-1].value = '';
            }
        });

        input.addEventListener('paste', function(e){
            e.preventDefault();
            const data = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g,'').slice(0,6);
            data.split('').forEach((ch, idx) => {
                if(digits[idx]) digits[idx].value = ch;
            });
            collectOtp();
            if(data.length === 6) form.submit();
        });
    });
})();
</script>
</body>
</html>
