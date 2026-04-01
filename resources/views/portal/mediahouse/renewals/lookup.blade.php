@extends('layouts.portal')

@section('title', 'Registration Number Lookup')

@section('content')
<div id="mediahouse-renewal-lookup">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Registration Number Lookup</h4>
    <a class="btn btn-secondary" href="{{ route('mediahouse.renewals.index') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Renewals
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Registration Lookup</h1>
      <p>Enter your registration number to retrieve your record</p>
    </div>

    <div class="form-steps-container">
      <!-- Step Progress Indicator -->
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Select Type</div>
          </div>
          <div class="step active" data-step="2">
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

      @if(session('error'))
        <div class="alert alert-danger mb-3">
          <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
        </div>
      @endif

      <div class="step-content active">
        <h3 class="step-title">Enter Your Registration Number</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Enter the exact registration number as it appears on your certificate.
        </div>

        <form method="POST" action="{{ route('mediahouse.renewals.perform-lookup', $renewal) }}">
          @csrf

          <div class="form-field mb-3">
            <label class="form-label required">Registration Number</label>
            <input type="text" 
                   name="registration_number" 
                   id="registration_number" 
                   class="form-control zmc-input"
                   placeholder="Enter your registration number"
                   value="{{ old('registration_number') }}"
                   required>
            @error('registration_number')
              <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
            @enderror
          </div>

          <div class="alert alert-warning" style="font-size:12px;">
            <i class="ri-alert-line me-1"></i>
            <strong>Important:</strong> Make sure to enter the number exactly as shown on your registration certificate.
          </div>

          <div class="form-buttons">
            <a href="{{ route('mediahouse.renewals.index') }}" class="btn btn-secondary">
              <i class="ri-arrow-left-line"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
              Search <i class="ri-search-line"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
