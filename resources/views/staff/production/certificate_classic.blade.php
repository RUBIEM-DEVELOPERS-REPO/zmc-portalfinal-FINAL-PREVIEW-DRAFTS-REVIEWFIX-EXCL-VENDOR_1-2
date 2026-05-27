<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Classic Certificate - {{ $application->reference }}</title>
  <style>
    @page { size: A4 {{ $orientation ?? 'portrait' }}; margin: 0; }
    body {
      font-family: 'Georgia', 'Times New Roman', serif;
      color: #3e2723;
      margin: 0;
      padding: 0;
      background: #fff;
    }

    .certificate-wrap {
      width: {{ ($orientation ?? 'portrait') === 'landscape' ? '297mm' : '210mm' }};
      height: {{ ($orientation ?? 'portrait') === 'landscape' ? '210mm' : '297mm' }};
      padding: 15mm;
      box-sizing: border-box;
      background: #fff0f5; /* Faded Pink (Lavender Blush) */
      position: relative;
    }

    @if(($orientation ?? 'portrait') === 'landscape')
    .border-inner {
        padding: 10mm 20mm;
    }
    .cert-title {
        font-size: 48px;
    }
    .details {
        margin-top: 20px;
    }
    .validity {
        margin-top: 20px;
    }
    @endif

    .border-outer {
      border: 8px double #8d6e63;
      height: 100%;
      box-sizing: border-box;
      padding: 10mm;
      position: relative;
    }

    .border-inner {
      border: 1px solid #8d6e63;
      height: 100%;
      box-sizing: border-box;
      padding: 20mm;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .corner {
      position: absolute;
      width: 40px;
      height: 40px;
      border: 4px solid #8d6e63;
      z-index: 5;
    }
    .tl { top: -4px; left: -4px; border-right: 0; border-bottom: 0; }
    .tr { top: -4px; right: -4px; border-left: 0; border-bottom: 0; }
    .bl { bottom: -4px; left: -4px; border-right: 0; border-top: 0; }
    .br { bottom: -4px; right: -4px; border-left: 0; border-top: 0; }

    .header-box {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
      width: 100%;
      justify-content: center;
    }

    .header-logo {
      width: 100px;
    }

    .org-name {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: #4e342e;
      text-align: center;
    }

    .divider {
      width: 100%;
      height: 2px;
      background: #d4a574;
      margin-bottom: 1.5rem;
    }

    .org-name .zimbabwe { color: #000; }
    .org-name .media { color: #f5c518; }
    .org-name .commission { color: #000; }

    .cert-title {
      font-size: 72px;
      font-weight: normal;
      font-style: italic;
      color: #795548;
      margin-bottom: 1.5rem;
    }

    .sub-title {
      font-size: 20px;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 1.5rem;
    }

    .cert-statement {
      font-size: 15px;
      line-height: 1.5;
      color: #3e2723;
      max-width: 80%;
      margin: 1.5rem auto;
      text-align: center;
    }

    .issued-text {
      font-size: 18px;
      margin-bottom: 1.5rem;
    }

    .entity-name {
      font-size: 42px;
      font-weight: 700;
      color: #3e2723;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid #8d6e63;
      padding-bottom: 5px;
      min-width: 400px;
      text-align: center;
    }

    .details {
      margin-top: 1.5rem;
      text-align: center;
      line-height: 2;
      margin-bottom: 1.5rem;
      font-size: 18px;
    }

    .details b { color: #5d4037; }

    .validity {
      margin-top: 1.5rem;
      display: flex;
      gap: 50px;
      margin-bottom: 1.5rem;
    }

    .validity-box {
      text-align: center;
    }

    .val-label { font-size: 13px; text-transform: uppercase; color: #8d6e63; }
    .val-date { font-size: 18px; font-weight: 700; }

    .signatures {
      margin-top: 10px;
      width: 100%;
      display: flex;
      justify-content: center;
    }

    .sig-block {
      text-align: center;
      width: 180px;
    }

    .sig-line {
      border-bottom: 1px solid #3e2723;
      height: 35px;
      margin-bottom: 5px;
    }

    .sig-title { font-size: 11px; font-weight: 700; }

    .footer {
      position: absolute;
      bottom: 25mm;
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 45mm;
      box-sizing: border-box;
    }

    .qr-container {
      width: 80px;
      height: 80px;
      border: 1px solid #8d6e63;
      padding: 5px;
      background: #fff;
    }

    @media print {
      body { -webkit-print-color-adjust: exact; }
    }
  </style>
</head>
<body>
  <div class="certificate-wrap">
    <div class="border-outer">
      <div class="tl corner"></div><div class="tr corner"></div>
      <div class="bl corner"></div><div class="br corner"></div>

      <div class="border-inner">
        <div class="header-box">
          <img src="{{ asset('zmc_logo.png') }}" class="header-logo" alt="ZMC">
          <div class="org-name">
            <span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span>
          </div>
        </div>
        <div class="divider"></div>

        <div class="cert-title">{{ $payload['certificate_title'] ?? 'Certificate' }}</div>
        <div class="sub-title">{{ $payload['certificate_sub'] ?? 'of Mass Media Registration' }}</div>

        <div class="issued-text">This is to certify that</div>
        <div class="entity-name">{{ $payload['org_name'] ?? '—' }}</div>

        <div class="cert-statement">
          {{ $payload['certification_statement'] ?? 'This is to certify that the above-named entity is duly registered with the Zimbabwe Media Commission and is recognized in accordance with the applicable laws and regulations governing media operations in Zimbabwe.' }}
        </div>

        <div class="details">
          is duly registered as a <b>{{ $payload['category'] ?? 'Mass Media Service' }}</b><br>
          under Registration Number: <b>{{ $payload['reg_no'] ?? '—' }}</b>
        </div>

        <div class="validity">
          <div class="validity-box">
            <div class="val-label">Registration Date</div>
            <div class="val-date">{{ $payload['issue_date'] ?? '—' }}</div>
          </div>
          <div class="validity-box">
            <div class="val-label">Expiry Date</div>
            <div class="val-date">{{ $payload['valid_until'] ?? '—' }}</div>
          </div>
        </div>

        <div class="signatures">
          {{-- Signatures moved to footer area --}}
        </div>

        <div class="footer">
          <div class="sig-block">
            <div class="sig-line"></div>
            <div class="sig-title">{{ $payload['left_sign_title'] ?? 'Chief Executive Officer' }}</div>
          </div>
          <a href="{{ $payload['qr_value'] ?? route('public.verify','invalid') }}" target="_blank" rel="noopener" title="Open verification page">
            <div class="qr-container" id="certQr"></div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    window.addEventListener('load', () => {
      try {
        new QRCode(document.getElementById('certQr'), {
          text: @json($payload['qr_value'] ?? route('public.verify', 'invalid')),
          width: 80,
          height: 80,
          correctLevel: QRCode.CorrectLevel.M
        });
      } catch(e) {}
      setTimeout(() => window.print(), 500);
    });
  </script>
</body>
</html>
