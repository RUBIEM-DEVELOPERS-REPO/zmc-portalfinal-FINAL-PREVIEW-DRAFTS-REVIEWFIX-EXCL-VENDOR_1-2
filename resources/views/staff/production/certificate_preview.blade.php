@extends('layouts.portal')
@section('title', 'Certificate Preview')

@section('content')
@php
  $fd = is_array($application->form_data)
      ? $application->form_data
      : (json_decode($application->form_data ?? '[]', true) ?: []);

  $ap1 = isset($fd['ap1']) && is_array($fd['ap1']) ? $fd['ap1'] : $fd;

  $issuedTo = $ap1['ap1_org_name'] ?? $application->applicant?->name ?? '—';
  $category = $ap1['ap1_category'] ?? 'Mass Media';

  $validFrom  = now()->format('Y-m-d');
  $validUntil = now()->addYears(5)->format('Y-m-d');

  // 🔐 Auto-generated certificate number (Now using Registration Number format)
  $certificateNo = $application->generateFormattedNumber();

  $payload = [
    'top_heading'       => 'ZIMBABWE MEDIA COMMISSION',
    'certificate_title' => 'CERTIFICATE',
    'certificate_sub'   => 'Of Registration For A Mass Media Service',

    'certificate_no'    => $certificateNo,
    'org_name'          => $issuedTo,
    'category'          => $category,

    'valid_from'        => $validFrom,
    'valid_until'       => $validUntil,

    'certification_statement' => 'This is to certify that the above-named entity is duly registered with the Zimbabwe Media Commission and is recognized in accordance with the applicable laws and regulations governing media operations in Zimbabwe.',

    'left_sign_title'   => 'CHIEF EXECUTIVE OFFICER',
    'right_sign_title'  => '',

    'left_sign_date'    => now()->format('Y-m-d'),
    'right_sign_date'   => '',

    'qr_value'          => (isset($application->registrationRecord) && $application->registrationRecord?->qr_token)
                              ? route('public.verify', $application->registrationRecord->qr_token)
                              : route('public.verify', 'invalid'),
  ];
@endphp

<div class="zmc-dashboard-wrapper">
  <div class="row g-3">
    <div class="col-12 col-lg-8 mx-auto">

      <div class="zmc-card mb-4">
        <h6 class="fw-bold mb-3"><i class="ri-palette-line me-2"></i> Design & Content Settings</h6>
        <form method="POST" action="{{ route('staff.production.applications.certificate.print', $application) }}" target="_blank">
          @csrf

          <div class="row g-3 mb-4">
            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Organization / Recipient Name</label>
                <input type="text" name="certificate_payload[org_name]" class="form-control zmc-input" value="{{ $payload['org_name'] }}" oninput="updateContent('org_name', this.value)">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Category</label>
                <input type="text" name="certificate_payload[category]" class="form-control zmc-input" value="{{ $payload['category'] }}" oninput="updateContent('category', this.value)">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Registration / Certificate No</label>
                <input type="text" name="certificate_payload[reg_no]" class="form-control zmc-input" value="{{ $payload['certificate_no'] }}" oninput="updateContent('reg_no', this.value)">
            </div>
            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Certification Statement</label>
                <textarea name="certificate_payload[certification_statement]" class="form-control zmc-input" rows="2" oninput="updateContent('certification_statement', this.value)">{{ $payload['certification_statement'] }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Issue Date</label>
                <input type="date" name="certificate_payload[issue_date]" class="form-control zmc-input" value="{{ $payload['valid_from'] }}" oninput="updateContent('issue_date', this.value)">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Valid Until</label>
                <input type="date" name="certificate_payload[valid_until]" class="form-control zmc-input" value="{{ $payload['valid_until'] }}" oninput="updateContent('valid_until', this.value)">
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Signatory Title (CEO)</label>
                <input type="text" name="certificate_payload[left_sign_title]" class="form-control zmc-input" value="{{ $payload['left_sign_title'] }}" oninput="updateContent('left_sign_title', this.value)">
                <input type="hidden" name="certificate_payload[right_sign_title]" value="">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Certificate Heading</label>
                <input type="text" name="certificate_payload[top_heading]" class="form-control zmc-input" value="{{ $payload['top_heading'] }}" oninput="updateContent('top_heading', this.value)">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Certificate Main Title</label>
                <input type="text" name="certificate_payload[certificate_title]" class="form-control zmc-input" value="{{ $payload['certificate_title'] }}" oninput="updateContent('certificate_title', this.value)">
            </div>
            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Certificate Subtitle</label>
                <input type="text" name="certificate_payload[certificate_sub]" class="form-control zmc-input" value="{{ $payload['certificate_sub'] }}" oninput="updateContent('certificate_sub', this.value)">
            </div>
          </div>

          <div class="row align-items-end g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-muted">Certificate Template</label>
              <select name="template" class="form-select zmc-input" onchange="updateDesignPreview(this.value)">
                <option value="modern">Modern Professional (Default)</option>
                <option value="classic">Classic Ornate (Gold/Brown)</option>
                <optgroup label="Portrait Variations">
                  <option value="var_1">Portrait: Navy & Gold (Solid)</option>
                  <option value="var_2">Portrait: Forest Green (Double Border)</option>
                  <option value="var_3">Portrait: Crimson Gradient</option>
                  <option value="var_4">Portrait: Royal Purple (Ornate)</option>
                  <option value="var_8">Portrait: Charcoal Professional</option>
                  <option value="var_9">Portrait: Burnt Orange (Italic Title)</option>
                  <option value="var_11">Portrait: Olive Garden (Ornate)</option>
                  <option value="var_13">Portrait: Midnight Pattern</option>
                  <option value="var_15">Portrait: Magenta Elegance</option>
                  <option value="var_17">Portrait: Dark Slate Gradient (Ornate)</option>
                  <option value="var_19">Portrait: Amber Solid</option>
                  <option value="var_21">Portrait: Cyan Pattern (Double Border)</option>
                  <option value="var_23">Portrait: Red Wine Gradient (Double)</option>
                  <option value="var_25">Portrait: Indigo (Classic Double)</option>
                </optgroup>
                <optgroup label="Landscape Variations">
                  <option value="var_5">Landscape: Navy & Gold (Solid)</option>
                  <option value="var_6">Landscape: Deep Violet Gradient</option>
                  <option value="var_7">Landscape: Teal Pattern (Ornate)</option>
                  <option value="var_10">Landscape: Sky Blue Gradient</option>
                  <option value="var_12">Landscape: Sunset Orange (Double)</option>
                  <option value="var_14">Landscape: Royal Blue Gradient (Ornate)</option>
                  <option value="var_16">Landscape: Earth Brown (Solid)</option>
                  <option value="var_18">Landscape: Ocean Blue Pattern (Double)</option>
                  <option value="var_20">Landscape: Emerald Gradient (Ornate)</option>
                  <option value="var_22">Landscape: Amethyst Solid</option>
                  <option value="var_24">Landscape: Slate Gray (Ornate)</option>
                </optgroup>
              </select>
            </div>
            <div class="col-md-6">
              <button type="submit" class="btn btn-dark fw-bold w-100">
                <i class="ri-printer-line me-1"></i> Generate & Print Certificate
              </button>
            </div>
          </div>
        </form>
      </div>

      <div class="cert-paper" id="certPreview">
        <div class="cert-inner">

          {{-- Watermark --}}
          <img src="{{ asset('zmc_logo.png') }}" class="cert-watermark">

          {{-- HEADER: Logo + Text --}}
          <div class="cert-header">
            <img src="{{ asset('zmc_logo.png') }}" class="cert-header-logo" alt="ZMC Logo">
            <div class="cert-header-text" id="view_top_heading">
              <span style="color:#000;">ZIMBABWE</span> <span style="color:#2e7d32;">MEDIA</span> <span style="color:#000;">COMMISSION</span>
            </div>
          </div>

          {{-- Ornament --}}
          <div class="cert-ornament"></div>

          {{-- Title --}}
          <div class="cert-title" id="view_certificate_title">{{ $payload['certificate_title'] }}</div>
          <div class="cert-subtitle" id="view_certificate_sub">{{ $payload['certificate_sub'] }}</div>

          {{-- Certificate Number --}}
          <div class="cert-number">
            Certificate No:
            <span id="view_reg_no">{{ $payload['certificate_no'] }}</span>
          </div>

          {{-- Issued To --}}
          <div class="cert-section">
            <div class="cert-label">Issued To</div>
            <div class="cert-value cert-strong" id="view_org_name">{{ $payload['org_name'] }}</div>
          </div>

          {{-- Category --}}
          <div class="cert-section">
            <div class="cert-label">Category</div>
            <div class="cert-value" id="view_category">{{ $payload['category'] }}</div>
          </div>

          {{-- Certification Statement --}}
          <div class="cert-statement" id="view_certification_statement">
            {{ $payload['certification_statement'] }}
          </div>

          {{-- Validity --}}
          <div class="cert-validity">
            <div><strong>Valid From:</strong> <span id="view_issue_date">{{ $payload['valid_from'] }}</span></div>
            <div><strong>To:</strong> <span id="view_valid_until">{{ $payload['valid_until'] }}</span></div>
          </div>

          {{-- Signatures --}}
          <div class="cert-signatures">
            {{-- Signatures moved to footer area --}}
          </div>

          {{-- Footer --}}
          <div class="cert-footer">
            <div class="cert-sign-block">
              <div class="cert-sign-line"></div>
              <div class="cert-sign-title" id="view_left_sign_title">{{ $payload['left_sign_title'] }}</div>
              <div class="cert-sign-date">{{ $payload['left_sign_date'] }}</div>
            </div>
            <a id="certPreviewQrLink" href="{{ $payload['qr_value'] }}" target="_blank" rel="noopener" class="cert-qr" title="Open verification page">
              <div id="certPreviewQr"></div>
            </a>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<style>
/* Paper */
.cert-paper{
  background:#fff0f5; /* Faded Pink (Lavender Blush) */
  border:3px solid #9c7c38;
  box-shadow:0 20px 50px rgba(0,0,0,.15);
  /* Screen preview should look like A4 and not grow excessively */
  width: 100%;
  max-width: 860px;
  aspect-ratio: 210 / 297;
  overflow: hidden;
  margin: 0 auto;
}
.cert-inner{
  padding: 42px;
  height: 100%;
  box-sizing: border-box;
  position:relative;
}

/* Watermark */
.cert-watermark{
  position:absolute;
  width:520px;
  opacity:.07;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  pointer-events:none;
}

/* Header */
.cert-header{
  display:flex;
  align-items:center;
  gap:14px;
  justify-content: center;
}
.cert-header-logo{
  width:100px;
  height:auto;
}
.cert-header-text{
  font-weight:900;
  letter-spacing:.6px;
  font-size: var(--font-size-2xl);
  text-align: center;
}

/* Ornament */
.cert-ornament{
  height:2px;
  width:60%;
  margin:40px auto;
  background:linear-gradient(to right, transparent, #b08a3c, transparent);
}

/* Titles */
.cert-title{
  text-align:center;
  font-size:64px;
  font-family:Georgia, serif;
  color:#6b7280;
  margin-bottom: 1.5rem;
}
.cert-subtitle{
  text-align:center;
  margin-top:8px;
  font-size:18px;
  margin-bottom: 1.5rem;
}

/* Certificate Number */
.cert-number{
  text-align:center;
  margin-top:14px;
  font-size:16px;
  font-weight:700;
  margin-bottom: 1.5rem;
}
.cert-number span{
  padding:6px 12px;
  border:1px solid rgba(0,0,0,.2);
  border-radius:999px;
  margin-left:6px;
}

/* Sections */
.cert-section{
  margin-top:14px;
  text-align:center;
  margin-bottom: 1.5rem;
}
.cert-statement{
  margin: 15px auto 1.5rem;
  max-width: 80%;
  text-align: center;
  font-size: 14px;
  line-height: 1.4;
  color: #333;
}
.cert-label{
  font-size:14px;
  font-weight:700;
  text-transform:uppercase;
}
.cert-value{
  margin-top:6px;
  font-size:24px;
}
.cert-strong{ font-weight:900; }

/* Validity */
.cert-validity{
  margin-top:14px;
  display:flex;
  justify-content:center;
  gap:60px;
  margin-bottom: 2rem;
}

/* Signatures */
.cert-signatures{
  margin-top: 20px;
  display:flex;
  justify-content:center;
}
.cert-sign-block{
  width:200px;
  text-align:center;
}
.cert-sign-line{
  height:1px;
  background:#000;
  margin-bottom:4px;
}
.cert-sign-title{
  font-size:11px;
  font-weight:700;
}
.cert-sign-date{
  font-size:10px;
}

/* Footer */
.cert-footer{
  position:absolute;
  bottom:35px;
  left:42px;
  right:42px;
  display:flex;
  justify-content:space-between;
  align-items:flex-end;
}
.cert-flag{ width:120px; }
.cert-qr{
  width:96px;
  height:96px;
}

/* Print */
@media print{
  body *{ visibility:hidden; }
  #certPreview, #certPreview *{ visibility:visible; }
  #certPreview{
    position:absolute;
    left:0; top:0;
    width:210mm; height:297mm;
  }
}
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  function updateContent(field, val) {
    const el = document.getElementById('view_' + field);
    if (el) el.textContent = val;
  }

  function updateDesignPreview(template) {
    const certPaper = document.getElementById('certPreview');
    if (template === 'classic') {
      certPaper.style.background = '#faf7f2';
      certPaper.style.border = '6px double #8d6e63';
      certPaper.style.fontFamily = "'Georgia', serif";
      certPaper.style.aspectRatio = "210 / 297";
      document.querySelector('.cert-title').style.color = '#795548';
      document.querySelector('.cert-title').style.fontStyle = 'italic';
    } else if (template.startsWith('var_')) {
        // Simple visual hint in preview
        certPaper.style.background = '#f0f0f0';
        certPaper.style.border = '5px dashed #ccc';
        certPaper.style.fontFamily = 'inherit';

        // Handle orientation in preview
        const landscapeVars = ['var_5', 'var_6', 'var_7', 'var_10', 'var_12', 'var_14', 'var_16', 'var_18', 'var_20', 'var_22', 'var_24'];
        if (landscapeVars.includes(template)) {
            certPaper.style.aspectRatio = "297 / 210";
            certPaper.style.maxWidth = "100%";
        } else {
            certPaper.style.aspectRatio = "210 / 297";
            certPaper.style.maxWidth = "860px";
        }
    } else {
      certPaper.style.background = '#f7f4ec';
      certPaper.style.border = '3px solid #9c7c38';
      certPaper.style.fontFamily = 'inherit';
      certPaper.style.aspectRatio = "210 / 297";
      document.querySelector('.cert-title').style.color = '#6b7280';
      document.querySelector('.cert-title').style.fontStyle = 'normal';
    }
  }

  new QRCode(document.getElementById("certPreviewQr"), {
    text: "{{ $payload['qr_value'] }}",
    width: 96,
    height: 96
  });
</script>
@endpush

@endsection
