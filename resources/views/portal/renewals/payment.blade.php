@extends('layouts.portal')

@section('title', 'Payment')

@section('content')
<div id="renewals-payment-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Renewal Payment</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.renewals.index') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Renewals
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Payment</h1>
      <p>Submit your renewal payment to complete the process</p>
    </div>

    <div class="form-steps-container">
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step" data-step="1">
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
          <div class="step active" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Payment</div>
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success mb-3">
          <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
        </div>
      @endif

      <div class="step-content active">
        <h3 class="step-title">Choose Payment Method</h3>
        <div class="current-step-info mb-4">
          <i class="ri-information-line me-2"></i>
          Select your preferred payment method below.
        </div>

        <div class="row g-3">
          <!-- PayNow Option -->
          <div class="col-md-6">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="fw-bold mb-2"><i class="ri-smartphone-line me-2"></i>Pay with PayNow</h5>
                <p class="text-muted mb-3" style="font-size:13px;">Complete payment online using PayNow platform</p>
                <button type="button" onclick="showPaynowModal()" class="btn btn-primary w-100">
                  <i class="ri-wallet-line me-1"></i>Pay with PayNow
                </button>
              </div>
            </div>
          </div>

          <!-- Proof Upload Option -->
          <div class="col-md-6">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="fw-bold mb-2"><i class="ri-file-upload-line me-2"></i>Upload Proof of Payment</h5>
                <p class="text-muted mb-3" style="font-size:13px;">If you've already paid, upload your proof of payment</p>
                <button type="button" onclick="showProofModal()" class="btn btn-secondary w-100">
                  <i class="ri-upload-line me-1"></i>Upload Proof
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PayNow Modal -->
<div class="modal fade" id="paynowModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <h5 class="modal-title zmc-modal-title">PayNow Reference</h5>
          <div class="zmc-modal-sub">Enter your payment reference number</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="paynowForm">
          @csrf
          <div class="mb-3">
            <label class="form-label required">PayNow Reference Number</label>
            <input type="text" id="paynow_reference" class="form-control zmc-input" placeholder="Enter reference number" required>
            <div class="text-muted mt-1" style="font-size:11px;">This is the reference number you received after completing payment</div>
          </div>
        </form>
      </div>
      <div class="modal-footer zmc-modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" onclick="submitPaynow()" class="btn btn-primary">
          <i class="ri-check-line me-1"></i>Submit Reference
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Proof Upload Modal -->
<div class="modal fade" id="proofModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <h5 class="modal-title zmc-modal-title">Upload Proof of Payment</h5>
          <div class="zmc-modal-sub">Provide payment details and upload proof</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="proofForm" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label required">Payment Date</label>
            <input type="date" name="payment_date" class="form-control zmc-input" required>
          </div>
          <div class="mb-3">
            <label class="form-label required">Amount Paid</label>
            <input type="number" name="amount" step="0.01" class="form-control zmc-input" placeholder="0.00" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Payer Name (Optional)</label>
            <input type="text" name="payer_name" class="form-control zmc-input" placeholder="Name of person who made payment">
          </div>
          <div class="mb-3">
            <label class="form-label">Reference Number (Optional)</label>
            <input type="text" name="reference" class="form-control zmc-input" placeholder="Bank reference or transaction ID">
          </div>
          <div class="mb-3">
            <label class="form-label required">Proof File</label>
            <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control zmc-input" required>
            <div class="text-muted mt-1" style="font-size:11px;">PDF, JPG, or PNG (max 5MB)</div>
          </div>
        </form>
      </div>
      <div class="modal-footer zmc-modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" onclick="submitProof()" class="btn btn-primary">
          <i class="ri-upload-line me-1"></i>Upload Proof
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let paynowModalInstance, proofModalInstance;

document.addEventListener('DOMContentLoaded', function() {
  paynowModalInstance = new bootstrap.Modal(document.getElementById('paynowModal'));
  proofModalInstance = new bootstrap.Modal(document.getElementById('proofModal'));
});

function showPaynowModal() {
  paynowModalInstance.show();
}

function showProofModal() {
  proofModalInstance.show();
}

async function submitPaynow() {
  const reference = document.getElementById('paynow_reference').value;
  
  if (!reference) {
    alert('Please enter a reference number');
    return;
  }
  
  try {
    const response = await fetch('{{ route("accreditation.renewals.payment.paynow", $renewal) }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ reference })
    });
    
    const data = await response.json();
    
    if (data.ok) {
      alert(data.message);
      window.location.href = '{{ route("accreditation.renewals.show", $renewal) }}';
    } else {
      alert(data.message || 'Error submitting payment');
    }
  } catch (error) {
    alert('Error submitting payment. Please try again.');
  }
}

async function submitProof() {
  const form = document.getElementById('proofForm');
  const formData = new FormData(form);
  
  try {
    const response = await fetch('{{ route("accreditation.renewals.payment.proof", $renewal) }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: formData
    });
    
    const data = await response.json();
    
    if (data.ok) {
      alert(data.message);
      window.location.href = '{{ route("accreditation.renewals.show", $renewal) }}';
    } else {
      alert(data.message || 'Error uploading proof');
    }
  } catch (error) {
    alert('Error uploading proof. Please try again.');
  }
}
</script>
@endpush
@endsection
