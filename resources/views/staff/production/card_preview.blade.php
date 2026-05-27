@extends('layouts.portal')
@section('title', 'Card Preview')

@section('content')
@php
  $fd = is_array($application->form_data) ? $application->form_data : (json_decode($application->form_data ?? '[]', true) ?: []);
  $fullName = trim(($fd['first_name'] ?? '') . ' ' . ($fd['surname'] ?? ''));
  if ($fullName === '') $fullName = $application->applicant?->name ?? '—';

  $passportPhoto = $application->documents->where('doc_type', 'passport_photo')->first();
  $photoUrl = $passportPhoto ? $passportPhoto->url : null;

  /**
   * PaymentTypeLetter:
   *  - waiver => C
   *  - paynow/proof/anything else => P
   */
  $paymentLetter = 'P';
  try {
    // If you have a payments relation, use it. Otherwise default P.
    $lastPayment = method_exists($application, 'payments') ? $application->payments()->latest()->first() : null;
    if ($lastPayment && strtolower((string)$lastPayment->method) === 'waiver') $paymentLetter = 'C';
  } catch (\Throwable $e) {}

  /**
   * Media Practitioner Type Initials:
   * Example given: Videographer => JV
   * Rule implemented:
   *  - Always starts with J
   *  - Second letter = first meaningful word initial of designation/type
   */
  $designation = (string)($fd['designation'] ?? 'Media Practitioner');
  $designationClean = trim(preg_replace('/\s+/', ' ', $designation));
  $words = array_values(array_filter(explode(' ', strtoupper($designationClean))));

  $second = 'J';
  if (count($words) >= 1) {
    // If the designation includes PRACTITIONER, use next word (PHOTO PRACTITIONER => JP etc)
    $idxPractitioner = array_search('PRACTITIONER', $words);
    if ($idxPractitioner !== false && isset($words[$idxPractitioner + 1])) {
      $second = substr($words[$idxPractitioner + 1], 0, 1);
    } else {
      // Otherwise use first word (VIDEOGRAPHER => V)
      $second = substr($words[0], 0, 1);
      // if first word is MEDIA and no next word, fallback to J
      if ($second === 'M' && !isset($words[1])) $second = 'J';
    }
  }
  $typeInitials = 'J' . $second; // e.g. JV

  // Number format: DDMM + Initials + YYYY + PaymentLetter
  // Update: Using model-driven category-based numbering if available, else fallback
  $ddmm = now()->format('dm');
  $yyyy = now()->format('Y');
  $generatedAccreditationId = $application->accreditationRecord->certificate_no ?? $application->generateFormattedNumber();

  $defaults = [
    'name'        => $fullName,
    // reference still exists for internal use, but we DO NOT SHOW it in preview anymore
    'ref'         => $application->reference ?? ('APP-' . $application->id),
    'designation' => $designationClean ?: 'Media Practitioner',
    'organisation'=> $fd['employer_name'] ?? '—',
    'scope'       => strtoupper((string)($application->journalist_scope ?? 'local')),
    'region'      => strtoupper((string)($application->collection_region ?? '—')),
    'valid_from'  => now()->format('Y-m-d'),
    'valid_to'    => now()->addYear()->format('Y-m-d'),

    // Accreditation ID fields/components (store separately too)
    'accreditation_id' => $generatedAccreditationId,
    'acc_ddmm'         => $ddmm,
    'acc_initials'     => $typeInitials,
    'acc_year'         => $yyyy,
    'acc_pay_letter'   => $paymentLetter,
  ];

  $payload = array_merge($defaults, $edits ?? []);

  $template = $application->form_data['card_template'] ?? 'default';

  $isForeign = strtoupper((string)($payload['scope'] ?? 'LOCAL')) === 'FOREIGN';

  // Use dynamic verification link for QR
  $qrValue = isset($application->accreditationRecord) && $application->accreditationRecord?->qr_token
    ? route('public.verify', $application->accreditationRecord->qr_token)
    : route('public.verify', 'invalid');
@endphp

<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Card Preview</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Preview & edit the accreditation card before printing.
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      {{-- Removed reference pill as requested --}}
      <a href="{{ route('staff.production.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3" title="Back">
        <i class="ri-arrow-left-line me-1"></i> Back
      </a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-5">
      <div class="zmc-card">
        <h6 class="fw-bold mb-3"><i class="ri-edit-2-line me-2" style="color:var(--zmc-accent-dark)"></i> Edit fields</h6>

        <form method="POST" action="{{ route('staff.production.applications.card.print', $application) }}" target="_blank" id="cardForm">
          @csrf

          <input type="hidden" name="card_payload[ref]" id="p_ref" value="{{ e($payload['ref']) }}">
          <input type="hidden" name="card_payload[acc_ddmm]" id="p_acc_ddmm" value="{{ e($payload['acc_ddmm']) }}">
          <input type="hidden" name="card_payload[acc_initials]" id="p_acc_initials" value="{{ e($payload['acc_initials']) }}">
          <input type="hidden" name="card_payload[acc_year]" id="p_acc_year" value="{{ e($payload['acc_year']) }}">
          <input type="hidden" name="card_payload[acc_pay_letter]" id="p_acc_pay_letter" value="{{ e($payload['acc_pay_letter']) }}">
          <input type="hidden" name="card_payload[qr_value]" id="p_qr_value" value="{{ e($qrValue) }}">

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label zmc-lbl">Name</label>
              <input class="form-control zmc-input" value="{{ $payload['name'] }}" data-bind="name" name="card_payload[name]">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label zmc-lbl">Scope</label>
              <select class="form-control zmc-input" data-bind="scope" name="card_payload[scope]">
                <option value="LOCAL" {{ $payload['scope'] == 'LOCAL' ? 'selected' : '' }}>LOCAL</option>
                <option value="FOREIGN" {{ $payload['scope'] == 'FOREIGN' ? 'selected' : '' }}>FOREIGN</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label zmc-lbl">Accreditation ID (ID No.)</label>
              <input class="form-control zmc-input" value="{{ $payload['accreditation_id'] }}" data-bind="accreditation_id" name="card_payload[accreditation_id]">
              <div class="form-text">Auto-generated: DDMM + Initials + YYYY + C/P</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label zmc-lbl">Designation</label>
              <input class="form-control zmc-input" value="{{ $payload['designation'] }}" data-bind="designation" name="card_payload[designation]">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label zmc-lbl">Region</label>
              <input class="form-control zmc-input" value="{{ $payload['region'] }}" data-bind="region" name="card_payload[region]">
            </div>

            <div class="col-12">
              <label class="form-label zmc-lbl">Organisation</label>
              <input class="form-control zmc-input" value="{{ $payload['organisation'] }}" data-bind="organisation" name="card_payload[organisation]">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label zmc-lbl">Valid from</label>
              <input type="date" class="form-control zmc-input" value="{{ $payload['valid_from'] }}" data-bind="valid_from" name="card_payload[valid_from]">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label zmc-lbl">Valid to</label>
              <input type="date" class="form-control zmc-input" value="{{ $payload['valid_to'] }}" data-bind="valid_to" name="card_payload[valid_to]">
            </div>

            <div class="col-12">
              <label class="form-label zmc-lbl">Card Template</label>
              <select name="template" class="form-select zmc-input" onchange="updateCardTemplate(this.value)">
                <option value="default" {{ $template == 'default' ? 'selected' : '' }}>Standard ZMC (Default)</option>
                <option value="modern_dark" {{ $template == 'modern_dark' ? 'selected' : '' }}>Modern Charcoal & Amber</option>
                <option value="eco_green" {{ $template == 'eco_green' ? 'selected' : '' }}>Eco Forest Green</option>
                <option value="royal_gold" {{ $template == 'royal_gold' ? 'selected' : '' }}>Royal Purple & Gold</option>
                <option value="ocean_blue" {{ $template == 'ocean_blue' ? 'selected' : '' }}>Ocean Blue Professional</option>
                <option value="crimson_pro" {{ $template == 'crimson_pro' ? 'selected' : '' }}>Crimson Professional</option>
              </select>
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-dark fw-bold px-4">
              <i class="ri-printer-line me-1"></i> Print Front
            </button>
            <button type="submit" class="btn btn-outline-dark fw-bold px-4" formaction="{{ route('staff.production.applications.card.print_back', $application) }}" formtarget="_blank">
              <i class="ri-qr-code-line me-1"></i> Print Back
            </button>
            <button type="button" class="btn btn-light fw-bold" id="btnReset">Reset</button>
          </div>
          <div class="form-text mt-2">Tip: You can also click the preview fields to edit inline.</div>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-7">
      <div class="zmc-card">
        <h6 class="fw-bold mb-3"><i class="ri-eye-line me-2" style="color:var(--zmc-accent-dark)"></i> Live preview (3D View)</h6>

        <div class="d-flex flex-column align-items-center mb-4">
          <div class="zmc-3d-card-wrap">
            <div class="zmc-3d-card" id="mainCard3D">
              {{-- FRONT --}}
              <div id="cardPreview" class="zmc-3d-front zmc-card-preview {{ $isForeign ? 'is-foreign' : 'is-local' }}">
                <div class="card-bg-watermark">
                  <img src="{{ asset('zmc_logo.png') }}" alt="">
                </div>
                <div class="card-bg-wave"></div>
                <div class="card-header-row">
                  <img src="{{ asset('zmc_logo.png') }}" class="logo-small" alt="ZMC">
                  <div class="header-title">
                    <div class="main">
                      <div style="color:#000;">ZIMBABWE</div>
                      <div class="highlight">MEDIA</div>
                      <div style="color:#000;">COMMISSION</div>
                    </div>
                    <div class="sub">{{ $isForeign ? 'FOREIGN ACCREDITATION CARD' : 'LOCAL ACCREDITATION CARD' }}</div>
                  </div>
                </div>
                <div class="card-body-content">
                  <div class="photo-frame">
                    @if($photoUrl)
                      <img src="{{ $photoUrl }}" alt="Photo" class="photo-img">
                    @else
                      <div class="photo-placeholder">PASSPORT<br>PHOTO</div>
                    @endif
                  </div>
                  <div class="card-details">
                    <div class="detail-line">
                      <span class="k">NAME</span>
                      <span class="v" contenteditable="true" data-edit="name">{{ $payload['name'] }}</span>
                    </div>
                    <div class="detail-line">
                      <span class="k">ACC NO.</span>
                      <span class="v" contenteditable="true" data-edit="accreditation_id">{{ $payload['accreditation_id'] }}</span>
                    </div>
                    <div class="detail-line">
                      <span class="k">ID NO.</span>
                      <span class="v" contenteditable="true" data-edit="national_id">{{ $fd['national_id'] ?? '—' }}</span>
                    </div>
                    <div class="detail-line">
                      <span class="k">ORG</span>
                      <span class="v" contenteditable="true" data-edit="organisation">{{ $payload['organisation'] }}</span>
                    </div>
                    <div class="detail-line">
                      <span class="k">CAT</span>
                      <span class="v" contenteditable="true" data-edit="category_code">{{ $application->accreditation_category_code ?? '—' }}</span>
                    </div>
                    <div class="detail-line">
                      <span class="k">VALID</span>
                      <span class="v">
                        <span contenteditable="true" data-edit="valid_from">{{ $payload['valid_from'] }}</span>
                        -
                        <span contenteditable="true" data-edit="valid_to">{{ $payload['valid_to'] }}</span>
                      </span>
                    </div>
                  </div>
                </div>
                <div id="scopeBanner" class="scope-banner">{{ $payload['scope'] }} MEDIA</div>
              </div>

              {{-- BACK --}}
              <div id="cardBackPreview" class="zmc-3d-back zmc-card-preview zmc-card-back-preview {{ $isForeign ? 'is-foreign' : 'is-local' }}">
                <div class="card-bg-watermark">
                  <img src="{{ asset('zmc_logo.png') }}" alt="">
                </div>
                <div class="card-bg-wave"></div>
                <div class="back-header">
                  <img src="{{ asset('zmc_logo.png') }}" class="logo-small" alt="ZMC">
                  <div style="text-align: right;">
                    <div class="org-name">
                      <div style="color:#000;">ZIMBABWE</div>
                      <div style="color:#228B22;">MEDIA</div>
                      <div style="color:#000;">COMMISSION</div>
                    </div>
                    <div class="org-sub">Accreditation Card (Back)</div>
                  </div>
                </div>
                <div class="back-content">
                  <div class="back-info">
                    <div class="info-line"><b>Address:</b> 108 Swan Drive, Alexandra Park, Harare</div>
                    <div class="info-line"><b>Tel:</b> 253509/10 or 253572/5/6</div>
                    <div class="info-line"><b>Email:</b> zmcaccreditation@gmail.com</div>
                    <div class="info-line"><b>Designation:</b> <span contenteditable="true" data-edit="designation">{{ $payload['designation'] }}</span></div>
                    <div class="notice-box" style="font-size: 6px;">This card remains the property of ZMC. If found, please return to ZMC.</div>
                  </div>
                  <div class="qr-section">
                    <div id="cardBackQr"></div>
                    <div class="qr-hint">Scan to verify</div>
                  </div>
                </div>
                <div class="back-footer">
                  <span>www.zmc.org.zw</span>
                  <span>Tel: 253509/10</span>
                </div>
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-outline-dark btn-sm mt-3 px-4" onclick="flipCard()">
            <i class="ri-refresh-line me-1"></i> Flip Card View
          </button>
        </div>

        <div class="text-muted small mt-3">
          Printing uses your browser print dialog. Use "More settings" to set margins to "None" if needed.
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  :root{
    --zmc-local:#1a237e;
    --zmc-foreign:#1a5f2a;
    --zmc-local-lite: rgba(26,35,126,.14);
    --zmc-foreign-lite: rgba(26,95,42,.14);
  }

  /* 3D Card Wrap */
  .zmc-3d-card-wrap {
    perspective: 1200px;
    width: 440px; /* Increased from 340px */
    height: 280px; /* Increased from 215px */
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .zmc-3d-card {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transform-style: preserve-3d;
    cursor: pointer;
    transform-origin: center center;
  }
  .zmc-3d-card.is-flipped {
    transform: rotateY(180deg);
  }
  .zmc-3d-card.is-flipped .zmc-3d-front {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.4s, visibility 0.4s;
  }
  .zmc-3d-card:not(.is-flipped) .zmc-3d-back {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.4s, visibility 0.4s;
  }
  .zmc-3d-front, .zmc-3d-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    margin: 0 !important;
    border-radius: 12px;
    overflow: hidden;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
  }
  .zmc-3d-back {
    transform: rotateY(180deg);
  }

  .zmc-card-preview {
    width: 100% !important;
    height: 100% !important;
    border: 2px solid rgba(15,23,42,.18);
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 40%, #ffffff 100%);
    position: relative;
    box-shadow: 0 18px 45px rgba(0,0,0,.15);
    overflow: hidden; /* ensure content stays inside */
  }

  .zmc-card-preview.is-local { --accent: var(--zmc-local); --accentLite: var(--zmc-local-lite); }
  .zmc-card-preview.is-foreign { --accent: var(--zmc-foreign); --accentLite: var(--zmc-foreign-lite); }

  /* Template Variations */
  .template-modern_dark { --accent: #212121; --accentLite: rgba(33,33,33,0.15); }
  .template-eco_green { --accent: #1a1a1a; --accentLite: rgba(245,197,24,0.12); }
  .template-royal_gold { --accent: #4a148c; --accentLite: rgba(74,20,140,0.15); }
  .template-ocean_blue { --accent: #01579b; --accentLite: rgba(1,87,155,0.15); }
  .template-crimson_pro { --accent: #b71c1c; --accentLite: rgba(183,28,28,0.15); }

  .card-bg-wave {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60%;
    background: radial-gradient(circle at 20% 80%, var(--accentLite), transparent 55%),
                radial-gradient(circle at 80% 90%, var(--accentLite), transparent 55%);
    opacity: 1;
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

  .card-header-row {
    display: flex;
    align-items: center;
    justify-content: center; /* Center-align the header content */
    gap: 14px; /* Increased gap for better spacing with centered logo */
    padding: 8px 12px;
    position: relative;
    z-index: 2;
    min-height: 44px;
  }

  .logo-small { width: 32px; height: 32px; object-fit: contain; display: block; }

  .header-title {
    flex: none; /* Don't grow, keep it centered */
  }

  .header-title .main {
    font-size: 11px; /* Increased from 8.5px */
    font-weight: 900;
    color: #000;
    letter-spacing: 0.1px;
    line-height: 1.0;
    text-align: center;
  }

  .header-title .highlight { color: #228B22; }

  .header-title .sub{
    font-size: 10px; /* Increased from 7.5px */
    font-weight: 800;
    color: rgba(15,23,42,.65);
    margin-top: 1px;
    letter-spacing: .4px;
    text-align: center;
  }

  .card-body-content {
    display: flex;
    gap: 10px;
    padding: 10px 12px;
    position: relative;
    z-index: 2;
  }

  .photo-frame {
    width: 85px; /* Increased from 65px */
    height: 105px; /* Increased from 80px */
    border: 2px solid var(--accent);
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .photo-placeholder {
    font-size: 8px;
    color: #64748b;
    font-weight: 700;
    text-align: center;
  }

  .photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .card-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  }

  .detail-line {
    display: flex;
    gap: 6px;
    align-items: baseline;
    font-size: 10.5px; /* Increased from 8px */
  }

  .detail-line .k {
    min-width: 64px;
    font-weight: 900;
    color: #64748b;
  }

  .detail-line .v {
    flex: 1;
    font-weight: 900;
    color: #0f172a;
    outline: none;
    padding: 1px 3px;
    border-radius: 4px;
  }

  .detail-line .v:focus {
    box-shadow: 0 0 0 2px rgba(245,158,11,.25);
    background: #fff7ed;
  }

  .scope-banner {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--accent);
    color: #fff;
    font-size: 7px;
    font-weight: 900;
    letter-spacing: 2px;
    text-align: center;
    padding: 3px 0;
    z-index: 3;
  }

  /* Card Back Preview */
  .zmc-card-back-preview {
    padding: 10px;
    box-sizing: border-box;
  }

  .back-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 8px 12px 6px;
    border-bottom: 1px solid rgba(15,23,42,0.12);
    position: relative;
    z-index: 2;
  }

  .back-header .org-name {
    font-size: 11px; /* Increased from 8.5px */
    font-weight: 900;
    color: #000;
    text-align: center;
    line-height: 1.0;
  }

  .back-header .org-sub {
    font-size: 10px; /* Increased from 8px */
    color: #64748b;
    text-align: center;
  }

  .back-content {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 0;
    position: relative;
    z-index: 2;
  }

  .back-info {
    flex: 1;
  }

  .back-info .info-line {
    font-size: 10px; /* Increased from 8px */
    margin-bottom: 4px;
    color: #334155;
  }

  .back-info .info-line b { color: var(--accent); }

  .notice-box {
    font-size: 7px;
    color: #64748b;
    margin-top: 8px;
    padding: 5px;
    background: rgba(255,255,255,0.85);
    border: 1px dashed rgba(15,23,42,.25);
    border-radius: 4px;
    line-height: 1.3;
  }

  .qr-section {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .qr-section #cardBackQr {
    width: 85px; /* Increased from 65px */
    height: 85px; /* Increased from 65px */
    border: 2px solid var(--accent);
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

  .back-footer {
    display: flex;
    justify-content: space-between;
    font-size: 10px; /* Increased from 8px */
    color: var(--accent);
    font-weight: 700;
    border-top: 1px solid rgba(15,23,42,0.12);
    padding-top: 4px;
    position: relative;
    z-index: 2;
  }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  (function(){
    const bindToHidden = {
      qr_value: 'p_qr_value',
    };

    function setHidden(key, val){
      const id = bindToHidden[key];
      if(!id) return;
      const el = document.getElementById(id);
      if(el) el.value = String(val ?? '');
    }

    function updateScopeBanner(scope) {
      const banner = document.getElementById('scopeBanner');
      if (banner) banner.textContent = scope + ' MEDIA';

      const front = document.getElementById('cardPreview');
      const back  = document.getElementById('cardBackPreview');
      const isForeign = String(scope || '').toUpperCase() === 'FOREIGN';

      if(front){
        front.classList.toggle('is-foreign', isForeign);
        front.classList.toggle('is-local', !isForeign);
      }
      if(back){
        back.classList.toggle('is-foreign', isForeign);
        back.classList.toggle('is-local', !isForeign);
      }
    }

    function updateCardTemplate(template) {
      const front = document.getElementById('cardPreview');
      const back = document.getElementById('cardBackPreview');

      // Remove all template classes
      const templates = ['template-default', 'template-modern_dark', 'template-eco_green', 'template-royal_gold', 'template-ocean_blue', 'template-crimson_pro'];

      [front, back].forEach(el => {
        if (!el) return;
        templates.forEach(t => el.classList.remove(t));
        el.classList.add('template-' + template);
      });
    }

    document.querySelectorAll('[data-bind]').forEach(inp => {
      const key = inp.getAttribute('data-bind');
      const handler = () => {
        const v = inp.value;
        setHidden(key, v);
        document.querySelectorAll('[data-edit="'+key+'"]').forEach(tgt => tgt.textContent = v);
        if (key === 'scope') updateScopeBanner(v);
        renderBackQr();
      };
      inp.addEventListener('input', handler);
      inp.addEventListener('change', handler);
    });

    document.querySelectorAll('[data-edit]').forEach(sp => {
      const key = sp.getAttribute('data-edit');
      sp.addEventListener('input', () => {
        setHidden(key, sp.textContent);
        const inp = document.querySelector('[data-bind="'+key+'"]');
        if(inp && inp.value !== sp.textContent) inp.value = sp.textContent;
        renderBackQr();
      });
    });

    document.getElementById('btnReset')?.addEventListener('click', () => {
      window.location.reload();
    });

    function renderBackQr(){
      const host = document.getElementById('cardBackQr');
      if(!host || typeof QRCode === 'undefined') return;
      host.innerHTML='';
      const v = document.getElementById('p_qr_value')?.value || '';
      try{
        new QRCode(host, {text:String(v), width:60, height:60, correctLevel: QRCode.CorrectLevel.M});
      }catch(e){}
    }

    function flipCard() {
      document.getElementById('mainCard3D').classList.toggle('is-flipped');
    }

    window.addEventListener('load', () => {
      // initialize QR on back
      refreshQr();
    });

    function refreshQr() {
      const qrEl = document.getElementById('cardBackQr');
      if(!qrEl) return;
      qrEl.innerHTML = '';
      new QRCode(qrEl, {
        text: document.getElementById('p_qr_value').value,
        width: 60,
        height: 60
      });
    }

    // Attach flipCard to window so it's globally callable if needed via onclick
    window.flipCard = flipCard;
    window.refreshQr = refreshQr;

    updateScopeBanner(document.querySelector('[data-bind="scope"]')?.value || 'LOCAL');
    updateCardTemplate('{{ $template }}');
    renderBackQr();
  })();
</script>
@endpush

@endsection
