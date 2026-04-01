@extends('layouts.portal')
@section('title', 'Help & Support')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Help & Support</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Quick references for PayNow payments, proofs, waivers and internal procedures.</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-md-6">
      <div class="zmc-card h-100">
        <div class="fw-bold mb-2"><i class="ri-customer-service-2-line me-2" style="color:var(--zmc-accent)"></i> PayNow support contacts</div>
        <ul class="small text-muted mb-0">
          <li>Integration / settlement issues: <span class="text-dark">(add contact details)</span></li>
          <li>Webhook failures / callback errors: <span class="text-dark">ICT Helpdesk</span></li>
        </ul>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="zmc-card h-100">
        <div class="fw-bold mb-2"><i class="ri-shield-check-line me-2" style="color:var(--zmc-accent)"></i> Internal finance procedures</div>
        <ul class="small text-muted mb-0">
          <li>Daily reconciliation (PayNow vs applications vs bank statement)</li>
          <li>Proof approval requires clear evidence + recorded notes</li>
          <li>Waiver approvals must reference policy/authority</li>
        </ul>
      </div>
    </div>

    <div class="col-12">
      <div class="zmc-card">
        <div class="fw-bold mb-2"><i class="ri-question-line me-2" style="color:var(--zmc-accent)"></i> FAQs</div>
        <div class="accordion" id="accHelp">
          <div class="accordion-item">
            <h2 class="accordion-header" id="h1">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c1">What do I do if PayNow shows paid but no proof was uploaded?</button>
            </h2>
            <div id="c1" class="accordion-collapse collapse" data-bs-parent="#accHelp">
              <div class="accordion-body small text-muted">Confirm the PayNow reference and update the application payment_status/paynow_reference, then proceed with payment confirmation workflow.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="h2">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2">What is required when rejecting a proof or waiver?</button>
            </h2>
            <div id="c2" class="accordion-collapse collapse" data-bs-parent="#accHelp">
              <div class="accordion-body small text-muted">A clear rejection reason is mandatory. Use specific items (wrong amount, missing reference, illegible document, ineligible waiver, missing authority letter, etc.).</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
