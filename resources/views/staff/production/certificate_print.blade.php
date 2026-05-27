<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Print Certificate - {{ $application->reference }}</title>
  <style>
    @page { size: A4 {{ $orientation ?? 'portrait' }}; margin: 12mm; }
    body {
      font-family: 'Times New Roman', Georgia, serif;
      color: #2c1810;
      margin: 0;
      padding: 20px;
      background: #fff;
    }

    .certificate {
      width: 100%;
      max-width: {{ ($orientation ?? 'portrait') === 'landscape' ? '1000px' : '700px' }};
      margin: 0 auto;
      padding: 30px 40px;
      position: relative;
      background: #fff0f5; /* Faded Pink (Lavender Blush) */
      border: 2px solid #d4a574;
    }

    @if(($orientation ?? 'portrait') === 'landscape')
    .details-box {
      max-width: 800px;
      display: flex;
      justify-content: space-around;
      gap: 20px;
    }
    .detail-row {
        border-bottom: none;
        flex-direction: column;
        align-items: center;
    }
    @endif

    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      opacity: 0.08;
      z-index: 0;
      pointer-events: none;
    }

    .watermark img {
      width: 350px;
      height: auto;
    }

    .watermark-text {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
      opacity: 0.04;
      z-index: 0;
      font-size: 10px;
      font-weight: 700;
      color: #1a237e;
      line-height: 1.8;
      word-wrap: break-word;
    }

    .content {
      position: relative;
      z-index: 1;
    }

    .header {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 30px;
      justify-content: center;
    }

    .header-logo-img {
      width: 100px;
      height: auto;
    }

    .org-name {
      font-size: 22px;
      font-weight: 700;
      letter-spacing: 3px;
      text-align: center;
    }

    .org-name .zimbabwe { color: #000; }
    .org-name .media { color: #f5c518; }
    .org-name .commission { color: #000; }

    .ornament {
      width: 80%;
      max-width: 400px;
      height: 30px;
      margin: 15px auto 1.5rem;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 30"><path fill="%23d4a574" d="M0,15 Q50,5 100,15 T200,15 T300,15 T400,15" stroke="%23c9a86c" stroke-width="2"/><circle cx="200" cy="15" r="8" fill="%23d4a574"/><circle cx="100" cy="15" r="4" fill="%23c9a86c"/><circle cx="300" cy="15" r="4" fill="%23c9a86c"/></svg>') no-repeat center;
      background-size: contain;
    }

    .cert-title {
      font-size: 46px;
      font-weight: 700;
      font-style: italic;
      color: #8b6914;
      margin: 10px 0 1.5rem;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
      font-family: 'Georgia', serif;
      text-align: center;
    }

    .cert-subtitle {
      font-size: 18px;
      font-style: italic;
      color: #4a4a4a;
      margin-bottom: 1.5rem;
      text-align: center;
    }

    .cert-statement {
      font-size: 14px;
      line-height: 1.5;
      color: #333;
      max-width: 85%;
      margin: 1.5rem auto;
      text-align: center;
    }

    .issued-to {
      font-size: 16px;
      color: #4a4a4a;
      margin-bottom: 5px;
      text-align: center;
    }

    .recipient-name {
      font-size: 32px;
      font-weight: 700;
      color: #1a237e;
      border-bottom: 2px dotted #d4a574;
      padding-bottom: 5px;
      margin-bottom: 1.5rem;
      display: block;
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
      min-width: 300px;
      text-align: center;
    }

    .details-box {
      margin: 1.5rem auto;
      max-width: 500px;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      border-bottom: 1px dotted #e8d5d5;
      padding-bottom: 5px;
    }

    .detail-label {
      font-size: 14px;
      font-weight: 700;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .detail-value {
      font-size: 16px;
      font-weight: 700;
      color: #2c1810;
    }

    .validity-section {
      display: flex;
      justify-content: center;
      gap: 50px;
      margin: 1.5rem 0;
    }

    .validity-item {
      text-align: center;
    }

    .validity-label {
      font-size: 12px;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .validity-value {
      font-size: 16px;
      font-weight: 700;
      color: #1a237e;
      border-bottom: 1px dotted #d4a574;
      padding: 5px 15px;
    }

    .signatures {
      margin-top: 20px;
      display: flex;
      justify-content: center;
    }

    .signature-block {
      text-align: center;
      width: 180px;
    }

    .signature-line {
      border-bottom: 1px solid #2c1810;
      margin-bottom: 4px;
      height: 30px;
    }

    .signature-title {
      font-size: 10px;
      font-weight: 700;
      color: #2c1810;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .signature-date {
      font-size: 9px;
      color: #666;
      margin-top: 2px;
    }

    .footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 30px;
      padding-top: 15px;
      border-top: 1px solid #e8d5d5;
    }

    .footer-flag {
      width: 80px;
    }

    .footer-flag img {
      width: 100%;
      height: auto;
    }

    .footer-logo {
      text-align: right;
    }

    .footer-logo img {
      width: 70px;
      height: 70px;
      object-fit: contain;
    }

    .qr-section {
      text-align: center;
    }

    .qr-section #certQr {
      width: 70px;
      height: 70px;
      display: inline-block;
    }

    .qr-hint {
      font-size: 8px;
      color: #666;
      margin-top: 4px;
    }

    @media print {
      body { background: #fff; }
      .certificate { box-shadow: none; }
    }
  </style>
</head>
<body>
  <div class="certificate">
    <div class="watermark">
      <img src="{{ asset('zmc_logo.png') }}" alt="">
    </div>
    <div class="watermark-text">
      @for($i = 0; $i < 100; $i++)
        ZIMBABWE MEDIA COMMISSION &nbsp;&nbsp;
      @endfor
    </div>

    <div class="content">
      <div class="header">
        <img src="{{ asset('zmc_logo.png') }}" class="header-logo-img" alt="ZMC Logo">
        <div class="org-name">
          <span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span>
        </div>
      </div>
      <div class="ornament"></div>
      <div class="header" style="display: block; text-align: center;">
        <div class="cert-title">{{ $payload['certificate_title'] ?? 'CERTIFICATE' }}</div>
        <div class="cert-subtitle">{{ $payload['certificate_sub'] ?? 'Of Registration For A Mass Media Service' }}</div>
      </div>

      <div style="text-align: center;">
        <div class="issued-to">Issued To</div>
        <div class="recipient-name">{{ $payload['org_name'] ?? '—' }}</div>
      </div>

      <div class="cert-statement">
        {{ $payload['certification_statement'] ?? 'This is to certify that the above-named entity is duly registered with the Zimbabwe Media Commission and is recognized in accordance with the applicable laws and regulations governing media operations in Zimbabwe.' }}
      </div>

      <div class="details-box">
        <div class="detail-row">
          <span class="detail-label">Category:</span>
          <span class="detail-value">{{ $payload['category'] ?? '—' }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Registration No:</span>
          <span class="detail-value">{{ $payload['reg_no'] ?? '—' }}</span>
        </div>
      </div>

      <div class="validity-section">
        <div class="validity-item">
          <div class="validity-label">Valid From</div>
          <div class="validity-value">{{ $payload['issue_date'] ?? '—' }}</div>
        </div>
        <div class="validity-item">
          <div class="validity-label">To</div>
          <div class="validity-value">{{ $payload['valid_until'] ?? '—' }}</div>
        </div>
      </div>

      <div class="signatures">
        {{-- Signatures moved to footer area --}}
      </div>

      <div class="footer">
        <div class="signature-block">
          <div class="signature-line"></div>
          <div class="signature-title">{{ $payload['left_sign_title'] ?? 'Chief Executive Officer' }}</div>
          <div class="signature-date">DATE: {{ $payload['issue_date'] ?? '................' }}</div>
        </div>
        <div class="qr-section">
          <a href="{{ $payload['qr_value'] ?? route('public.verify','invalid') }}" target="_blank" rel="noopener" title="Open verification page">
            <div id="certQr"></div>
          </a>
          <div class="qr-hint">Scan to verify</div>
        </div>
        <div class="footer-logo">
          <img src="{{ asset('zmc_logo.png') }}" alt="ZMC Logo">
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
          width: 70,
          height: 70,
          correctLevel: QRCode.CorrectLevel.M
        });
      } catch(e) {}
      setTimeout(() => window.print(), 250);
    });
  </script>
</body>
</html>
