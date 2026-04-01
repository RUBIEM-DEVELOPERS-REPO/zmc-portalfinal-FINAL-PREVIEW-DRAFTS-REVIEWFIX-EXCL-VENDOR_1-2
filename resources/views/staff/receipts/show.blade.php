@extends('layouts.portal')
@section('title', 'Receipt Details')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Receipt Details</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        View receipt information and verification details.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('receipts.index') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i>Back to Receipts
      </a>
    </div>
  </div>

  {{-- Receipt Header --}}
  <div class="zmc-card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="text-center">
            <img src="{{ asset('images/zmc-logo.png') }}" alt="ZMC Logo" style="width: 80px; height: 80px;">
            <div class="mt-2">
              <h5 class="fw-bold text-primary mb-0">{{ $receipt->receipt_number }}</h5>
              <div class="small text-muted">Official Receipt</div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-center">
            <div class="mb-2">
              <span class="badge bg-{{ $receipt->status_color }}">
                {{ $receipt->status_label }}
              </span>
            </div>
            <div class="small text-muted">
              Generated: {{ $receipt->created_at->format('d M Y H:i') }}
            </div>
            @if($receipt->isVerified())
              <div class="small text-success">
                Verified: {{ $receipt->verified_at->format('d M Y H:i') }}
              </div>
            @endif
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-center">
            <div class="verified-badge">
              <i class="ri-shield-check-line me-1"></i>
              VERIFIED PAYMENT
            </div>
            <div class="small text-muted mt-1">Official Receipt</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Application Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-primary text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-list-3-line me-2"></i>
        Application Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Application Reference:</span>
            <span class="info-value fw-bold">{{ $receipt->application?->reference ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Application Type:</span>
            <span class="info-value">{{ ucfirst($receipt->application?->application_type ?? '—') }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Request Type:</span>
            <span class="info-value">{{ ucfirst($receipt->application?->request_type ?? '—') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Submission Date:</span>
            <span class="info-value">{{ $receipt->application?->submitted_at->format('d M Y') ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Application Status:</span>
            <span class="info-value">
              @if($receipt->application)
                <span class="badge bg-{{ $receipt->application->statusColor() }}">
                  {{ $receipt->application->statusLabel() }}
                </span>
              @else
                —
              @endif
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Applicant Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-success text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-user-line me-2"></i>
        Applicant Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Full Name:</span>
            <span class="info-value">{{ $receipt->applicant?->name ?? 'Unknown' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $receipt->applicant?->email ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Phone Number:</span>
            <span class="info-value">{{ $receipt->applicant?->phone ?? '—' }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">ID Number:</span>
            <span class="info-value">{{ $receipt->applicant?->id_number ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Address:</span>
            <span class="info-value">{{ $receipt->applicant?->address ?? '—' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Payment Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-info text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-money-dollar-circle-line me-2"></i>
        Payment Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Payment Reference:</span>
            <span class="info-value fw-bold text-primary">{{ $receipt->payment_reference }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Payment Method:</span>
            <span class="info-value">
              <span class="badge bg-primary">{{ $receipt->payment_method_label }}</span>
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Transaction ID:</span>
            <span class="info-value">{{ $receipt->transaction_id ?? '—' }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Amount Paid:</span>
            <span class="info-value fw-bold text-success fs-5">${{ number_format($receipt->amount, 2) }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Payment Date:</span>
            <span class="info-value">{{ $receipt->payment_date->format('d M Y H:i') }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Verification Status:</span>
            <span class="info-value">
              <span class="badge bg-{{ $receipt->status_color }}">
                {{ $receipt->status_label }}
              </span>
            </span>
          </div>
        </div>
      </div>

      @if($receipt->notes)
        <div class="mt-3 pt-3 border-top">
          <div class="info-item">
            <span class="info-label">Notes:</span>
            <span class="info-value">{{ $receipt->notes }}</span>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Verification Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-warning text-dark">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-shield-check-line me-2"></i>
        Verification Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Generated By:</span>
            <span class="info-value">{{ $receipt->generator?->name ?? 'System' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Generated At:</span>
            <span class="info-value">{{ $receipt->created_at->format('d M Y H:i:s') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-item">
            <span class="info-label">Verified By:</span>
            <span class="info-value">{{ $receipt->verifier?->name ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Verified At:</span>
            <span class="info-value">{{ $receipt->verified_at?->format('d M Y H:i:s') ?? '—' }}</span>
          </div>
        </div>
      </div>

      <div class="mt-3 pt-3 border-top">
        <div class="text-center">
          <div class="small text-muted">
            <i class="ri-information-line me-1"></i>
            This receipt is digitally signed and verifiable.
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Actions --}}
  <div class="zmc-card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div class="small text-muted">
          <i class="ri-information-line me-1"></i>
          This receipt is an official payment record.
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('receipts.view', $receipt->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="ri-eye-line me-1"></i>View Receipt
          </a>
          <a href="{{ route('receipts.download', $receipt->id) }}" class="btn btn-success btn-sm">
            <i class="ri-download-line me-1"></i>Download PDF
          </a>
          @if($receipt->isPending())
            <button type="button" class="btn btn-warning btn-sm" onclick="verifyReceipt({{ $receipt->id }})">
              <i class="ri-check-line me-1"></i>Verify
            </button>
          @endif
        </div>
      </div>
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

<script>
function verifyReceipt(id) {
  if (confirm('Are you sure you want to verify this receipt?')) {
    fetch(`/staff/receipts/${id}/verify`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      });
  }
}
</script>
@endsection
