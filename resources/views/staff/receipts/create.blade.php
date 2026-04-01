@extends('layouts.portal')
@section('title', 'Generate Receipt')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Generate Payment Receipt</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Create official receipt for confirmed payment.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('applications.show', $application->id) }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i>Back to Application
      </a>
    </div>
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

  {{-- Application Details --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-primary text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-list-3-line me-2"></i>
        Application Details
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Reference Number:</span>
            <span class="info-value fw-bold">{{ $application->reference }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Applicant Name:</span>
            <span class="info-value">{{ $application->applicant->name }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Application Type:</span>
            <span class="info-value">{{ ucfirst($application->application_type) }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Request Type:</span>
            <span class="info-value">{{ ucfirst($application->request_type) }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $application->applicant->email }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Phone:</span>
            <span class="info-value">{{ $application->applicant->phone }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Payment Details --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-success text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-money-dollar-circle-line me-2"></i>
        Payment Details
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Payment Status:</span>
            <span class="info-value">
              <span class="badge bg-success">Confirmed</span>
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Amount:</span>
            <span class="info-value fw-bold text-success">${{ number_format($application->payment->amount, 2) }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Confirmed Date:</span>
            <span class="info-value">{{ $application->payment->confirmed_at->format('d M Y H:i') }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Confirmed By:</span>
            <span class="info-value">{{ $application->payment->confirmed_by ? $application->payment->confirmedBy->name : 'System' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Receipt Generation Form --}}
  <div class="zmc-card">
    <div class="card-header bg-info text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-text-line me-2"></i>
        Receipt Generation
      </h6>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('receipts.store') }}">
        @csrf
        <input type="hidden" name="application_id" value="{{ $application->id }}">
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Payment Method *</label>
            <select name="payment_method" class="form-select" required>
              <option value="">Select Payment Method</option>
              @foreach($paymentMethods as $method => $label)
                <option value="{{ $method }}">{{ $label }}</option>
              @endforeach
            </select>
            <div class="form-text">Select the payment method used by the applicant</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Transaction ID</label>
            <input type="text" name="transaction_id" class="form-control" 
                   placeholder="Enter transaction ID (required for PayNow)">
            <div class="form-text">Required for PayNow payments, optional for others</div>
          </div>
          
          <div class="col-12">
            <label class="form-label fw-bold">Notes</label>
            <textarea name="notes" class="form-control" rows="3" 
                      placeholder="Add any additional notes about this payment..."></textarea>
            <div class="form-text">Optional notes about the payment or receipt</div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
          <div class="small text-muted">
            <i class="ri-information-line me-1"></i>
            Receipt will be generated with QR code for verification
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary" onclick="history.back()">
              <i class="ri-close-line me-1"></i>Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="ri-file-text-line me-1"></i>Generate Receipt
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.info-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dotted #e0e0e0;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.info-value {
    color: #333;
}
</style>
@endsection
