@extends('layouts.portal')

@section('title', 'Select Renewal Type')

@section('content')
<div id="mediahouse-renewal-select-type">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Start Renewal Application</h4>
    <a class="btn btn-secondary" href="{{ route('mediahouse.renewals.index') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to My Renewals
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Renewal / Replacement</h1>
      <p>Select whether you want to renew your registration or request a replacement</p>
    </div>

    <div class="form-steps-container">
      <!-- Step Progress Indicator -->
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Select Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Registration Lookup</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Confirm Changes</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Payment</div>
          </div>
        </div>
      </div>

      <div class="step-content active">
        <h3 class="step-title">Select Application Type</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Choose whether you are renewing your registration or requesting a replacement.
        </div>

        <form method="POST" action="{{ route('mediahouse.renewals.store-type') }}">
          @csrf

          <div class="app-type-container">
            <div class="app-type-cards">
              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="renewal" required style="display: none;">
                <i class="ri-refresh-line"></i>
                <h4>Renewal of Registration</h4>
                <p>Renew your existing media house registration for another period.</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Annual Renewal</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>

              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="replacement" required style="display: none;">
                <i class="ri-file-copy-line"></i>
                <h4>Replacement of Registration</h4>
                <p>Request a replacement for a lost, damaged, or stolen registration certificate.</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Lost/Damaged/Stolen</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>
            </div>
          </div>

          @error('renewal_type')
            <div class="alert alert-danger mt-3">{{ $message }}</div>
          @enderror

          <div class="form-buttons">
            <a href="{{ route('mediahouse.renewals.index') }}" class="btn btn-secondary">
              <i class="ri-arrow-left-line"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
              Continue <i class="ri-arrow-right-line"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.app-type-card').forEach(card => {
  card.addEventListener('click', function() {
    document.querySelectorAll('.app-type-card').forEach(c => c.classList.remove('selected'));
    this.classList.add('selected');
    this.querySelector('input[type="radio"]').checked = true;
  });
});
</script>
@endpush
@endsection
