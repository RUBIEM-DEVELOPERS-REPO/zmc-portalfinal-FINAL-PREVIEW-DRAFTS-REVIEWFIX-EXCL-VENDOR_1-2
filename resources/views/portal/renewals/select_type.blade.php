@extends('layouts.portal')

@section('title', 'Select Renewal Type')

@section('content')
<div id="renewal-select-type-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Start Renewal Application</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.renewals.index') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to My Renewals
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>Select Renewal Type</h1>
      <p>Step 1 of 4: Choose the type of renewal you want to apply for</p>
    </div>

    <div class="form-steps-container">
      {{-- Step Progress Indicator --}}
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Select Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Number Lookup</div>
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

      {{-- Step Content --}}
      <div class="step-content active">
        <h3 class="step-title">Select Renewal Type</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Choose the type of renewal you want to apply for. Different requirements apply to each type.
        </div>

        <form method="POST" action="{{ route('accreditation.renewals.store-type') }}">
          @csrf

          <div class="app-type-container">
            <div class="app-type-cards">
              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="accreditation" required style="display: none;">
                <i class="ri-user-3-line"></i>
                <h4>Journalist / Media Practitioner Accreditation Renewal</h4>
                <p>Renew your individual accreditation card</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Individual</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>

              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="registration" required style="display: none;">
                <i class="ri-building-line"></i>
                <h4>Media House Registration Renewal</h4>
                <p>Renew your media house registration certificate</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Organization</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>

              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="permission" required style="display: none;">
                <i class="ri-shield-check-line"></i>
                <h4>Permission Renewal</h4>
                <p>Renew special permissions</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Special</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>
            </div>
          </div>

          @error('renewal_type')
            <div class="alert alert-danger mt-3">{{ $message }}</div>
          @enderror

          <div class="form-buttons">
            <a href="{{ route('accreditation.renewals.index') }}" class="btn btn-secondary">
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
// Add selected state to cards when radio is clicked
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
