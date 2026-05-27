<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Print Card - {{ $application->reference }}</title>
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

    .card-header {
      display: flex;
      align-items: center;
      justify-content: center; /* Center-align the header content */
      padding: 6px 12px;
      gap: 14px; /* Consistent with preview */
      position: relative;
      z-index: 2;
    }

    .header-title {
      text-align: center;
      flex: none; /* Keep it centered */
    }

    .header-title .main {
      font-size: 10px;
      font-weight: 900;
      color: #000;
      letter-spacing: 0.1px;
      text-align: center;
      line-height: 1.0;
    }

    .header-title .main .highlight {
      color: #228B22;
    }

    .header-title .sub {
      font-size: 9px;
      font-weight: 800;
      color: rgba(15,23,42,.65);
      margin-top: 1px;
      letter-spacing: .4px;
      text-align: center;
    }

    .logo-zmc {
      width: 44px;
      height: 44px;
      object-fit: contain;
    }

    .media-card-year {
      text-align: center;
      font-size: 12px;
      font-weight: 900;
      color: {{ $primaryColor }};
      margin-top: 2px;
      position: relative;
      z-index: 2;
    }

    .card-body {
      display: flex;
      padding: 6px 12px;
      gap: 10px;
      position: relative;
      z-index: 2;
    }

    .photo-container {
      position: relative;
    }

    .photo-frame {
      width: 25mm;
      height: 30mm;
      border: 2px solid {{ $primaryColor }};
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .photo-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .photo-placeholder {
      font-size: 9px;
      color: #64748b;
      font-weight: 700;
      text-align: center;
    }

    .card-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
      padding-top: 2px;
    }

    .detail-line {
      display: flex;
      gap: 4px;
      font-size: 9px;
    }

    .detail-line .label {
      color: #475569;
      font-weight: 700;
      min-width: 50px;
    }

    .detail-line .value {
      color: #0f172a;
      font-weight: 900;
    }

    .flag-container {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%) rotate(15deg);
      z-index: 1;
    }

    .flag-img {
      width: 35mm;
      height: auto;
      opacity: 0.85;
    }

    .scope-banner {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: {{ $primaryColor }};
      color: #fff;
      font-size: 8px;
      font-weight: 900;
      letter-spacing: 2px;
      text-align: center;
      padding: 3px 0;
      z-index: 3;
    }

    .scope-banner::before {
      content: '{{ strtoupper($payload['scope'] ?? 'LOCAL') }} MEDIA  ';
      animation: scroll 8s linear infinite;
    }

    @media print{
      body{background:#fff;}
      .card{box-shadow:none;}
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-bg-watermark">
      <img src="{{ asset('zmc_logo.png') }}" alt="">
    </div>
    <div class="card-bg-wave"></div>

    <div class="card-header">
      <img src="{{ asset('zmc_logo.png') }}" class="logo-zmc" alt="ZMC">
      <div class="header-title">
        <div class="main">
          <div style="color:#000;">ZIMBABWE</div>
          <div class="highlight">MEDIA</div>
          <div style="color:#000;">COMMISSION</div>
        </div>
        <div class="sub">{{ $isForeign ? 'FOREIGN ACCREDITATION CARD' : 'LOCAL ACCREDITATION CARD' }}</div>
      </div>
    </div>

    <div style="text-align:center; position:relative; z-index:2;">
      <img src="{{ asset('zmc_logo.png') }}" style="width:32px; height:32px; object-fit:contain;">
      <div class="media-card-year">MEDIA CARD {{ date('Y') }}</div>
    </div>

    <div class="card-body">
      <div class="photo-container">
        <div class="photo-frame">
          @if(!empty($payload['photo_url']))
            <img src="{{ $payload['photo_url'] }}" alt="Photo">
          @else
            <div class="photo-placeholder">PASSPORT<br>PHOTO</div>
          @endif
        </div>
      </div>

      <div class="card-details">
        <div class="detail-line">
          <span class="label">NAME:</span>
          <span class="value">{{ $payload['name'] ?? '—' }}</span>
        </div>
        <div class="detail-line">
          <span class="label">ACC NO:</span>
          <span class="value">{{ $payload['reg_no'] ?? ($payload['accreditation_id'] ?? ($payload['ref'] ?? '—')) }}</span>
        </div>
        <div class="detail-line">
          <span class="label">ID NO:</span>
          <span class="value">{{ $application->form_data['national_id'] ?? '—' }}</span>
        </div>
        <div class="detail-line">
          <span class="label">ORG:</span>
          <span class="value">{{ $payload['organisation'] ?? '—' }}</span>
        </div>
        <div class="detail-line">
          <span class="label">CAT:</span>
          <span class="value">{{ $application->accreditation_category_code ?? '—' }}</span>
        </div>
        <div class="detail-line">
          <span class="label">VALID:</span>
          <span class="value">{{ $payload['valid_from'] ?? '—' }} to {{ $payload['valid_to'] ?? '—' }}</span>
        </div>
      </div>

      <div class="qr-container" style="position: absolute; right: 10px; top: 10px; z-index: 5;">
        <a href="{{ $payload['qr_value'] ?? route('public.verify','invalid') }}" target="_blank" rel="noopener" title="Open verification page">
          <div id="cardQr"></div>
        </a>
        <div style="font-size: 6px; text-align: center; color: #666; margin-top: 2px;">Verify</div>
      </div>

      <div class="flag-container">
        <img src="{{ asset('zim_flag.png') }}" class="flag-img" alt="Zimbabwe Flag" onerror="this.style.display='none'">
      </div>
    </div>

    <div class="scope-banner"></div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    window.addEventListener('load', () => {
      try {
        new QRCode(document.getElementById('cardQr'), {
          text: @json($payload['qr_value'] ?? ($application->reference ?? '')),
          width: 50,
          height: 50,
          correctLevel: QRCode.CorrectLevel.M
        });
      } catch(e) {}
      setTimeout(() => window.print(), 300);
    });
  </script>
</body>
</html>
