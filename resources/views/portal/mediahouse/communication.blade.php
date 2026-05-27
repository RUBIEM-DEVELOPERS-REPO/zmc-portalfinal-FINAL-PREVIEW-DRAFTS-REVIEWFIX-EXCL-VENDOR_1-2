@extends('layouts.portal')

@section('title', 'Message Center')

@section('content')

<div id="communication-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Communication</h4>
    <div class="d-flex gap-2">
      <button class="btn btn-secondary" id="clearCommLog">
        <i class="ri-delete-bin-6-line me-1"></i>Clear Log
      </button>
      <button class="btn btn-primary" id="sendSupportEmailBtn">
        <i class="ri-mail-send-line me-2"></i>Compose Email to ZMC
      </button>
    </div>
  </div>

  <div class="comm-grid">
    <div class="form-container">
      <div class="form-header">
        <h5 class="m-0"><i class="ri-mail-line me-2"></i>Email Composer (Embedded)</h5>
        <p class="mt-2">This composer logs messages locally (demo) and can also open your default mail client.</p>
      </div>
      <div class="form-steps-container">
        <div class="form-field">
          <label class="form-label required">To</label>
          <input type="email" class="form-control" id="emailTo" value="zmcaccreditation@gmail.com" required>
        </div>
        <div class="form-field">
          <label class="form-label required">Subject</label>
          <input type="text" class="form-control" id="emailSubject" placeholder="e.g. AP1 Submission Follow-up" required>
        </div>
        <div class="form-field">
          <label class="form-label required">Message</label>
          <textarea class="form-control" id="emailBody" rows="7" placeholder="Type your message..." required></textarea>
          <div class="form-hint">Tip: Include your reference number (e.g. ZMC-AP1-YYYY-XXX).</div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-primary" id="logEmailBtn">
            <i class="ri-save-line me-2"></i>Save to Communication Log
          </button>
          <button class="btn btn-secondary" id="openMailClientBtn">
            <i class="ri-external-link-line me-2"></i>Open Mail Client
          </button>
        </div>

        <div class="alert alert-light border mt-3 mb-0">
          <div class="fw-bold"><i class="ri-lock-2-line me-2"></i>Note</div>
          <div class="text-muted">In production: connect this to your email/ticketing backend (SMTP/API) and store threads per application.</div>
        </div>
      </div>
    </div>

    <div class="form-container">
      <div class="form-header">
        <h5 class="m-0"><i class="ri-chat-3-line me-2"></i>Communication Log</h5>
        <p class="mt-2">Saved messages (demo) — stored in your browser (localStorage).</p>
      </div>
      <div class="form-steps-container">
        <div id="commLog"></div>
      </div>
    </div>
  </div>
</div>

@endsection
