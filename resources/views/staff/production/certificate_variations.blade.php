<!doctype html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Certificate - {{ $application->reference }}</title>
  <style>
    @php
        $orientation = $template_data['orientation'] ?? 'portrait';
        $primaryColor = $template_data['primary_color'] ?? '#1a237e';
        $secondaryColor = $template_data['secondary_color'] ?? '#d4a574';
        $fontFamily = $template_data['font_family'] ?? "'Times New Roman', serif";
        $bgType = $template_data['bg_type'] ?? 'solid'; // solid, gradient, pattern
        $borderStyle = $template_data['border_style'] ?? 'solid'; // solid, double, ornate
        $titleFont = $template_data['title_font'] ?? $fontFamily;
        $titleStyle = $template_data['title_style'] ?? 'normal'; // normal, italic
    @endphp

    @page {
        size: A4 {{ $orientation }};
        margin: 0;
    }

    body {
      font-family: {!! $fontFamily !!};
      color: #2c1810;
      margin: 0;
      padding: 0;
      background: #fff;
      -webkit-print-color-adjust: exact;
    }

    .certificate-container {
      width: {{ $orientation === 'portrait' ? '210mm' : '297mm' }};
      height: {{ $orientation === 'portrait' ? '297mm' : '210mm' }};
      box-sizing: border-box;
      position: relative;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 10mm;
      @if($bgType === 'gradient')
        background: linear-gradient(135deg, #fff5f7 0%, #ffe9ef 100%); /* Faded Pink Gradient */
      @elseif($bgType === 'pattern')
        background: #fff0f5; /* Faded Pink base */
        background-image: radial-gradient(rgba(212, 165, 186, 0.35) 0.8px, transparent 0.8px);
        background-size: 18px 18px;
      @else
        background: #fff0f5; /* Faded Pink solid */
      @endif
    }

    .border-frame {
      width: 100%;
      height: 100%;
      border: 5mm {{ $borderStyle }} {{ $primaryColor }};
      box-sizing: border-box;
      padding: 5mm;
      position: relative;
      display: flex;
      flex-direction: column;
    }

    @if($borderStyle === 'ornate')
    .border-frame {
        border: 1mm solid {{ $primaryColor }};
        padding: 10mm;
    }
    .border-frame::before {
        content: "";
        position: absolute;
        top: 2mm; left: 2mm; right: 2mm; bottom: 2mm;
        border: 0.5mm solid {{ $secondaryColor }};
        pointer-events: none;
    }
    .corner-ornament {
        position: absolute;
        width: 30mm;
        height: 30mm;
        border: 2mm solid {{ $primaryColor }};
        z-index: 2;
    }
    .top-left { top: 0; left: 0; border-right: 0; border-bottom: 0; }
    .top-right { top: 0; right: 0; border-left: 0; border-bottom: 0; }
    .bottom-left { bottom: 0; left: 0; border-right: 0; border-top: 0; }
    .bottom-right { bottom: 0; right: 0; border-left: 0; border-top: 0; }
    @endif

    .inner-content {
      flex: 1;
      border: 1px solid {{ $secondaryColor }};
      padding: 15mm;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      opacity: 0.05;
      z-index: 0;
      width: 50%;
    }

    .header-section {
        display: flex;
        align-items: center;
        gap: 20mm;
        margin-bottom: 5mm;
        width: 100%;
        justify-content: center;
    }

    .org-header .zimbabwe { color: #000; }
    .org-header .media { color: #f5c518; }
    .org-header .commission { color: #000; }

    .zmc-logo {
        width: 45mm;
        margin-bottom: 0;
    }

    .org-header {
        font-size: 32pt;
        font-weight: bold;
        color: {{ $primaryColor }};
        letter-spacing: 2pt;
        text-transform: uppercase;
        margin-bottom: 0;
        text-align: center;
    }

    .ornamental-line {
        width: 100%;
        height: 1mm;
        background: linear-gradient(to right, transparent, {{ $secondaryColor }}, transparent);
        margin: 5mm auto 1.5rem;
    }

    .cert-title {
        font-family: {!! $titleFont !!};
        font-size: 52pt;
        color: {{ $primaryColor }};
        margin: 5mm 0 1.5rem;
        text-transform: uppercase;
        font-style: {{ $titleStyle }};
    }

    .cert-subtitle {
        font-size: 20pt;
        margin-bottom: 1.5rem;
        color: #444;
    }

    .cert-body {
        font-size: 16pt;
        line-height: 1.4;
        width: 90%;
        margin: 0 auto 1.5rem;
    }

    .cert-statement {
        font-size: 13pt;
        line-height: 1.4;
        color: #333;
        max-width: 85%;
        margin: 1.5rem auto;
    }

    .recipient-name {
        font-size: 36pt;
        font-weight: bold;
        color: #000;
        margin: 3mm 0;
        border-bottom: 1pt solid {{ $secondaryColor }};
        display: inline-block;
        min-width: 70%;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem 15mm;
        margin-top: 1.5rem;
        width: 90%;
        margin-bottom: 1.5rem;
    }

    @if($orientation === 'landscape')
    .inner-content {
        padding: 8mm 15mm;
    }
    .cert-title {
        font-size: 42pt;
        margin: 3mm 0;
    }
    .cert-subtitle {
        margin-bottom: 5mm;
        font-size: 18pt;
    }
    .details-grid {
        grid-template-columns: repeat(4, 1fr);
        width: 100%;
        gap: 3mm;
        margin-top: 5mm;
    }
    .signatures-section {
        padding-top: 5mm;
    }
    @endif

    .detail-item {
        text-align: left;
        border-bottom: 0.5pt solid #eee;
        padding-bottom: 2mm;
    }

    .detail-label {
        font-size: 12pt;
        text-transform: uppercase;
        color: #777;
        font-weight: bold;
    }

    .detail-value {
        font-size: 14pt;
        font-weight: bold;
        color: #222;
    }

    .signatures-section {
        margin-top: 10mm;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .sig-block {
        width: 60mm;
        text-align: center;
    }

    .sig-line {
        border-top: 1pt solid #000;
        margin-bottom: 2mm;
    }

    .sig-title {
        font-size: 11pt;
        font-weight: bold;
    }

    .footer-section {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: auto;
        padding-bottom: 5mm;
    }

    .qr-box {
        width: 25mm;
        height: 25mm;
        background: #fff;
        padding: 1mm;
        border: 0.5pt solid {{ $secondaryColor }};
    }

    .flag-img {
        width: 30mm;
    }
  </style>
</head>
<body>
    <div class="certificate-container">
        <div class="border-frame">
            @if($borderStyle === 'ornate')
                <div class="corner-ornament top-left"></div>
                <div class="corner-ornament top-right"></div>
                <div class="corner-ornament bottom-left"></div>
                <div class="corner-ornament bottom-right"></div>
            @endif

            <div class="inner-content">
                <img src="{{ asset('zmc_logo.png') }}" class="watermark" alt="">

                <div class="header-section">
                    <img src="{{ asset('zmc_logo.png') }}" class="zmc-logo" alt="ZMC Logo">
                    <div class="org-header">
                        <span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span>
                    </div>
                </div>
                <div class="ornamental-line"></div>

                <div class="cert-title">{{ $payload['certificate_title'] ?? 'Certificate' }}</div>
                <div class="cert-subtitle">{{ $payload['certificate_sub'] ?? 'Of Registration For A Mass Media Service' }}</div>

                <div class="cert-body">
                    This is to certify that
                    <div class="recipient-name">{{ $payload['org_name'] ?? '—' }}</div>
                </div>

                <div class="cert-statement">
                    {{ $payload['certification_statement'] ?? 'This is to certify that the above-named entity is duly registered with the Zimbabwe Media Commission and is recognized in accordance with the applicable laws and regulations governing media operations in Zimbabwe.' }}
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Registration Number</div>
                        <div class="detail-value">{{ $payload['reg_no'] ?? '—' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Issued On</div>
                        <div class="detail-value">{{ $payload['issue_date'] ?? '—' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Expiry Date</div>
                        <div class="detail-value">{{ $payload['valid_until'] ?? '—' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Place of Issue</div>
                        <div class="detail-value">Harare, Zimbabwe</div>
                    </div>
                </div>

                <div class="signatures-section">
                    {{-- Signatures moved to footer-section --}}
                </div>

                <div class="footer-section">
                    <div class="sig-block">
                        <div class="sig-line"></div>
                        <div class="sig-title">{{ $payload['left_sign_title'] ?? 'Chief Executive Officer' }}</div>
                    </div>
                    <div class="qr-box" id="certQr"></div>
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
                    width: 90,
                    height: 90,
                    correctLevel: QRCode.CorrectLevel.M
                });
            } catch(e) {}
            setTimeout(() => window.print(), 500);
        });
    </script>
</body>
</html>
