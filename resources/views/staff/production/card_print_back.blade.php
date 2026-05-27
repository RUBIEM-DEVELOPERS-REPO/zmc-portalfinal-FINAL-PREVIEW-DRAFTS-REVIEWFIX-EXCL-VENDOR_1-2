<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Print Card (Back) - {{ $application->reference }}</title>
  <style>
    @php
        $primaryColor = $template_data['primary_color'] ?? '#1a237e';
        $secondaryColor = $template_data['secondary_color'] ?? '#f5c518';
        $bgStyle = $template_data['bg_style'] ?? 'gradient';
    @endphp

    @page { size: 85.6mm 53.98mm; margin: 0; }
    html, body { height: 100%; margin: 0; }
    body { display:flex; align-items:center; justify-content:center; background:#fff; font-family: Arial, sans-serif; }

    .card{
      width: 85.6mm;
      height: 53.98mm;
      position: relative;
      overflow: hidden;
      @if($bgStyle === 'solid')
        background: #fff;
      @elseif($bgStyle === 'pattern')
        background: #fff;
        background-image: radial-gradient({{ $secondaryColor }}22 0.8px, transparent 0.8px);
        background-size: 10px 10px;
      @else
        background: linear-gradient(135deg, #f8f0ff 0%, #fff5f8 30%, #ffffff 100%);
      @endif
      padding: 10px;
      box-sizing: border-box;
    }

    .card-bg-wave {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 60%;
      background: radial-gradient(circle at 20% 80%, {{ $primaryColor }}22, transparent 55%),
                  radial-gradient(circle at 80% 90%, {{ $primaryColor }}22, transparent 55%);
      opacity: 0.7;
      z-index: 0;
    }

    .card-bg-watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 65%;
      opacity: 0.08;
      z-index: 1;
      pointer-events: none;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card-bg-watermark img {
      width: 100%;
      height: auto;
      filter: grayscale(100%);
    }

    .content {
      position: relative;
      z-index: 1;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: center; /* Center-align the back header content */
      padding: 8px 12px 6px;
      gap: 14px; /* Consistent with front */
      border-bottom: 1px solid {{ $primaryColor }}33;
    }

    .header-text {
      text-align: center;
      flex: none; /* Keep it centered */
    }

    .logo {
      width: 30px;
      height: 30px;
      object-fit: contain;
    }

    .header-text .org {
      font-size: 10px;
      font-weight: 900;
      color: #000;
      letter-spacing: 0.5px;
      text-align: center;
      line-height: 1.0;
    }

    .header-text .org span.media {
      color: #228B22;
    }

    .header-text .sub {
      font-size: 9px;
      font-weight: 700;
      color: #64748b;
      text-align: center;
    }

    .body-content {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex: 1;
      padding: 6px 0;
      gap: 10px;
    }

    .left-info {
      flex: 1;
    }

    .info-line {
      font-size: 9px;
      margin-bottom: 3px;
      color: #334155;
    }

    .info-line b {
      color: {{ $primaryColor }};
    }

    .notice {
      font-size: 7.5px;
      color: #64748b;
      margin-top: 6px;
      line-height: 1.4;
      padding: 5px;
      background: rgba(255,255,255,0.8);
      border: 1px dashed {{ $secondaryColor }};
      border-radius: 4px;
    }

    .qr-section {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .qr-box {
      width: 70px;
      height: 70px;
      border: 2px solid {{ $primaryColor }};
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .qr-hint {
      font-size: 7px;
      color: #64748b;
      font-weight: 700;
      margin-top: 3px;
    }

    .footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 9px;
      color: {{ $primaryColor }};
      font-weight: 700;
      border-top: 1px solid {{ $primaryColor }}33;
      padding-top: 4px;
    }

    @media print {
      body { background: #fff; }
      .card { box-shadow: none; }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-bg-watermark">
      <img src="{{ asset('zmc_logo.png') }}" alt="">
    </div>
    <div class="card-bg-wave"></div>

    <div class="content">
      <div class="header">
        <img src="{{ asset('zmc_logo.png') }}" class="logo" alt="ZMC">
        <div class="header-text">
          <div class="org">
            <div style="color:#000;">ZIMBABWE</div>
            <div class="media" style="color: #228B22;">MEDIA</div>
            <div style="color:#000;">COMMISSION</div>
          </div>
          <div class="sub">Accreditation Card (Back)</div>
        </div>
      </div>

      <div class="body-content">
        <div class="left-info">
          <div class="info-line"><b>Address:</b> 108 Swan Drive, Alexandra Park, Harare</div>
          <div class="info-line"><b>Tel:</b> 253509/10 or 253572/5/6</div>
          <div class="info-line"><b>Email:</b> zmcaccreditation@gmail.com</div>
          <div class="info-line"><b>Designation:</b> {{ $payload['designation'] ?? 'Media Practitioner' }}</div>
          <div class="info-line"><b>Accreditation No:</b> {{ $payload['accreditation_number'] ?? ($payload['reg_no'] ?? ($payload['ref'] ?? '—')) }}</div>
          <div class="info-line"><b>Valid:</b> {{ $payload['valid_from'] ?? '—' }} to {{ $payload['valid_to'] ?? '—' }}</div>

          <div class="notice">
            This card remains the property of the Zimbabwe Media Commission.
            If found, please return to ZMC.
          </div>
        </div>

        <div class="qr-section">
          <div class="qr-box">
            <a href="{{ $payload['qr_value'] ?? route('public.verify','invalid') }}" target="_blank" rel="noopener" title="Open verification page">
              <div id="cardBackQr"></div>
            </a>
          </div>
          <div class="qr-hint">Scan to verify</div>
        </div>
      </div>

      <div class="footer">
        <div>www.zmc.org.zw</div>
        <div>Tel: 253509/10</div>
        <div>{{ $payload['ref'] ?? $application->reference }}</div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    (function(){
      const value = @json($payload['qr_value'] ?? ($application->reference ?? ''));
      try {
        new QRCode(document.getElementById('cardBackQr'), {
          text: String(value),
          width: 65,
          height: 65,
          correctLevel: QRCode.CorrectLevel.M
        });
      } catch(e) {}
      window.addEventListener('load', () => setTimeout(() => window.print(), 200));
    })();
  </script>
</body>
</html>
