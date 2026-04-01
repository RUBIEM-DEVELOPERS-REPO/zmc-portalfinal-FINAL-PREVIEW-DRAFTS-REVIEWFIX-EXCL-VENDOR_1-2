@extends('layouts.portal')

@section('title', 'Batch Payment - Media House Portal')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
      <a href="{{ route('mediahouse.batch.index') }}" class="text-muted text-decoration-none small">
        <i class="ri-arrow-left-line me-1"></i> Back to Batches
      </a>
      <h4 class="fw-bold m-0 mt-2" style="font-size:22px; color:#1e293b;">Batch: {{ $batch->reference }}</h4>
    </div>
    <span class="badge rounded-pill px-3 py-2
      @if($batch->status === 'paid') bg-success
      @elseif($batch->status === 'pending_verification') bg-info
      @else bg-warning text-dark
      @endif">
      {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
    </span>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="zmc-card p-4 shadow-sm border-0 mb-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3">Payment Instructions</h6>
        <p>Please make a payment of <strong>{{ number_format($batch->amount, 2) }} USD</strong> to the following bank account:</p>
        
        <div class="p-3 bg-light rounded mb-4" style="border: 1px dashed #cbd5e1;">
          <div class="row">
            <div class="col-sm-4 text-muted small">Bank Name:</div>
            <div class="col-sm-8 fw-bold">Commercial Bank of Zimbabwe (CBZ)</div>
            <div class="col-sm-4 text-muted small">Account Name:</div>
            <div class="col-sm-8 fw-bold">Zimbabwe Media Commission</div>
            <div class="col-sm-4 text-muted small">Account Number:</div>
            <div class="col-sm-8 fw-bold">011223344556677</div>
            <div class="col-sm-4 text-muted small">Branch:</div>
            <div class="col-sm-8 fw-bold">Kwame Nkrumah</div>
            <div class="col-sm-4 text-muted small">Reference:</div>
            <div class="col-sm-8 fw-bold text-primary">{{ $batch->reference }}</div>
          </div>
        </div>

        @if($batch->status === 'pending')
          <form action="{{ route('mediahouse.batch.payment', $batch) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="payment_method" value="proof">
            
            <div class="mb-3">
              <label class="form-label fw-bold">Upload Proof of Payment (POP)</label>
              <input type="file" name="proof_file" class="form-control zmc-input" accept="image/*,.pdf" required>
              <div class="form-text mt-1">Accepted formats: PDF, JPG, PNG. Max size: 5MB.</div>
            </div>

            <button type="submit" class="btn btn-dark fw-bold px-4 py-2">
              <i class="ri-upload-cloud-2-line me-1"></i> Submit Payment Proof
            </button>
          </form>
        @else
          <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
            <i class="ri-checkbox-circle-line me-2 fs-4"></i>
            <div>
              @if($batch->status === 'pending_verification')
                Payment proof has been submitted and is currently being verified by the Accounts department.
              @else
                This batch has been successfully paid and processed.
              @endif
            </div>
          </div>
          @if($batch->proof_path)
             <a href="{{ Storage::url($batch->proof_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
               <i class="ri-file-search-line me-1"></i> View Submitted Proof
             </a>
          @endif
        @endif
      </div>

      <div class="zmc-card p-0 shadow-sm border-0">
        <div class="p-3 border-bottom">
          <h6 class="fw-bold m-0">Included Journalists</h6>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($journalists as $journalist)
                <tr>
                  <td class="fw-bold text-dark">{{ $journalist->name }}</td>
                  <td>{{ $journalist->email }}</td>
                  <td>
                    @php
                       $hasApp = $batch->applications()->where('applicant_user_id', $journalist->id)->exists();
                    @endphp
                    @if($hasApp)
                      <span class="text-success small"><i class="ri-checkbox-circle-fill me-1"></i> Application Created</span>
                    @else
                      <span class="text-muted small">Pending Application</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="zmc-card shadow-sm border-0">
        <h6 class="fw-bold mb-3">Financial Recap</h6>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Item:</span>
          <span class="small">Practitioner Renewals</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Quantity:</span>
          <span>{{ count($journalists) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-4 border-top pt-2">
          <span class="text-muted fw-bold">Amount Due:</span>
          <span class="fw-bold text-dark fs-5">{{ number_format($batch->amount, 2) }} USD</span>
        </div>
        
        <div class="p-3 bg-light rounded text-center">
           <i class="ri-shield-check-line text-success fs-3 mb-2 d-block"></i>
           <span class="small text-muted">Secure transaction via ZMC Portal</span>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
