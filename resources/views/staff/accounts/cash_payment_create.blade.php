@extends('layouts.portal')
@section('title', 'Record Cash Payment')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        Record Cash Payment
      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Record a cash payment received for an application. This record is immutable and auditable.
      </div>
    </div>

    <a href="{{ route('staff.accounts.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
      <i class="ri-arrow-left-line me-1"></i> Back to Dashboard
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-start gap-2">
      <i class="ri-error-warning-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="zmc-card shadow-sm border-0">
    <form method="POST" action="{{ route('staff.accounts.payments.offline.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-12">
          <label class="form-label zmc-lbl">Application <span class="text-danger">*</span></label>
          <select name="application_id" class="form-select zmc-input" required>
            <option value="">-- Select application --</option>
            @foreach($applications as $app)
              <option value="{{ $app->id }}" {{ old('application_id') == $app->id ? 'selected' : '' }}>
                {{ $app->reference ?? 'APP-' . str_pad($app->id, 5, '0', STR_PAD_LEFT) }}
                — {{ $app->applicant?->name ?? 'Unknown' }}
                ({{ ucwords(str_replace('_', ' ', $app->status)) }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label zmc-lbl">Receipt Number <span class="text-danger">*</span></label>
          <input type="text" name="receipt_number" class="form-control zmc-input" value="{{ old('receipt_number') }}" required placeholder="e.g. RCT-2025-001">
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label zmc-lbl">Amount (USD) <span class="text-danger">*</span></label>
          <input type="number" name="amount" class="form-control zmc-input" step="0.01" min="0.01" value="{{ old('amount') }}" required placeholder="0.00">
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label zmc-lbl">Payment Date <span class="text-danger">*</span></label>
          <input type="date" name="payment_date" class="form-control zmc-input" value="{{ old('payment_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
        </div>

        <div class="col-12">
          <label class="form-label zmc-lbl">Notes (optional)</label>
          <textarea name="notes" class="form-control zmc-input" rows="3" placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
        </div>
      </div>

      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('staff.accounts.dashboard') }}" class="btn btn-light fw-bold px-4">Cancel</a>
        <button type="submit" class="btn btn-dark fw-bold px-4">
          <i class="ri-save-line me-1"></i> Record Cash Payment
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
