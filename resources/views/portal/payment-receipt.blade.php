<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Receipt — {{ $application->reference }}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background:#f3f4f6; color:#1f2937; }
    .page { max-width:680px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.1); }
    .receipt-header { background:linear-gradient(135deg,#1e7e34,#28a745); padding:32px 40px; color:#fff; }
    .receipt-header .logo-row { display:flex; align-items:center; gap:16px; margin-bottom:20px; }
    .receipt-header .logo-box { width:56px; height:56px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:28px; }
    .receipt-header h1 { font-size:22px; font-weight:700; }
    .receipt-header p { opacity:.85; font-size:13px; margin-top:2px; }
    .stamp-row { display:flex; justify-content:space-between; align-items:center; margin-top:16px; }
    .stamp { background:rgba(255,255,255,.15); border:2px solid rgba(255,255,255,.4); border-radius:8px; padding:6px 16px; font-size:13px; font-weight:700; letter-spacing:1px; }
    .receipt-body { padding:32px 40px; }
    .section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#6b7280; margin-bottom:12px; padding-bottom:6px; border-bottom:1px solid #e5e7eb; }
    .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 24px; margin-bottom:24px; }
    .info-item label { font-size:11px; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.5px; display:block; margin-bottom:2px; }
    .info-item span { font-size:14px; color:#1f2937; font-weight:500; }
    .amount-box { background:#f0fdf4; border:1px solid #d1fae5; border-radius:12px; padding:20px 24px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; }
    .amount-box .label { font-size:13px; color:#6b7280; }
    .amount-box .amount { font-size:28px; font-weight:700; color:#1e7e34; }
    .status-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
    .status-paid { background:#d1fae5; color:#065f46; }
    .status-pending { background:#fef3c7; color:#92400e; }
    .receipt-footer { background:#f9fafb; border-top:1px solid #e5e7eb; padding:20px 40px; text-align:center; }
    .receipt-footer p { font-size:12px; color:#9ca3af; line-height:1.6; }
    .digital-stamp { display:inline-block; border:2px solid #1e7e34; border-radius:8px; padding:8px 20px; color:#1e7e34; font-weight:700; font-size:13px; letter-spacing:1px; margin-bottom:12px; }
    @media print {
      body { background:#fff; }
      .page { box-shadow:none; margin:0; border-radius:0; }
      .no-print { display:none !important; }
    }
  </style>
</head>
<body>

<div class="page">
  <div class="receipt-header">
    <div class="logo-row">
      <div class="logo-box">🏛️</div>
      <div>
        <h1>Zimbabwe Media Commission</h1>
        <p>Official Payment Receipt</p>
      </div>
    </div>
    <div class="stamp-row">
      <div>
        <div style="font-size:12px;opacity:.8;">Receipt No.</div>
        <div style="font-size:18px;font-weight:700;">{{ $application->receipt_number ?? 'ZMC-' . strtoupper(substr(md5($application->id), 0, 8)) }}</div>
      </div>
      <div class="stamp">✓ DIGITALLY STAMPED</div>
    </div>
  </div>

  <div class="receipt-body">

    <div class="amount-box">
      <div>
        <div class="label">Amount Paid</div>
        <div class="amount">
          {{ $application->proof_amount_paid ? 'USD ' . number_format($application->proof_amount_paid, 2) : 'USD —' }}
        </div>
      </div>
      <div>
        @if($application->payment_status === 'paid' || $application->proof_status === 'approved')
          <span class="status-badge status-paid">✓ PAID</span>
        @else
          <span class="status-badge status-pending">PENDING</span>
        @endif
      </div>
    </div>

    <div class="section-title">Application Details</div>
    <div class="info-grid">
      <div class="info-item">
        <label>Reference</label>
        <span>{{ $application->reference }}</span>
      </div>
      <div class="info-item">
        <label>Application Type</label>
        <span>{{ ucfirst($application->application_type) }} — {{ ucfirst($application->request_type) }}</span>
      </div>
      <div class="info-item">
        <label>Submitted</label>
        <span>{{ $application->submitted_at?->format('d M Y') ?? '—' }}</span>
      </div>
      <div class="info-item">
        <label>Payment Date</label>
        <span>
          @if($application->paynow_confirmed_at)
            {{ $application->paynow_confirmed_at->format('d M Y H:i') }}
          @elseif($application->proof_payment_date)
            {{ \Carbon\Carbon::parse($application->proof_payment_date)->format('d M Y') }}
          @else
            —
          @endif
        </span>
      </div>
    </div>

    <div class="section-title">Applicant Details</div>
    <div class="info-grid">
      <div class="info-item">
        <label>Name</label>
        <span>
          {{ $application->proof_payer_first_name ?? ($application->form_data['first_name'] ?? '') }}
          {{ $application->proof_payer_last_name ?? ($application->form_data['surname'] ?? '') }}
        </span>
      </div>
      <div class="info-item">
        <label>Email</label>
        <span>{{ $application->applicant?->email ?? '—' }}</span>
      </div>
    </div>

    <div class="section-title">Payment Details</div>
    <div class="info-grid">
      <div class="info-item">
        <label>Payment Method</label>
        <span>
          @if($application->paynow_confirmed_at)
            PayNow (Online)
          @elseif($application->proof_bank_name)
            Bank Transfer — {{ $application->proof_bank_name }}
          @else
            —
          @endif
        </span>
      </div>
      <div class="info-item">
        <label>PayNow / Bank Ref</label>
        <span>{{ $application->paynow_reference ?? $application->paynow_ref_submitted ?? '—' }}</span>
      </div>
    </div>

    <div style="text-align:center;margin-top:24px;">
      <div class="digital-stamp">ZIMBABWE MEDIA COMMISSION</div>
      <p style="font-size:11px;color:#9ca3af;">Generated: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

  </div>

  <div class="receipt-footer">
    <p>This is an official digitally stamped receipt issued by the Zimbabwe Media Commission.<br>
    For queries, contact ZMC at <strong>info@zmc.co.zw</strong> | Tel: +263 (0)4 123456</p>
    <div class="no-print" style="margin-top:16px;">
      <button onclick="window.print()" style="background:#1e7e34;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;cursor:pointer;margin-right:8px;">
        🖨️ Print Receipt
      </button>
      <a href="javascript:history.back()" style="background:#f3f4f6;color:#374151;border:none;padding:10px 24px;border-radius:8px;font-weight:600;cursor:pointer;text-decoration:none;">
        ← Back
      </a>
    </div>
  </div>
</div>

</body>
</html>
