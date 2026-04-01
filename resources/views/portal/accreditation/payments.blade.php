@extends('layouts.portal')

@section('title', 'Payment History')
@section('page_title', 'PAYMENT HISTORY')

@section('content')
<div id="payment-history-page">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Payment History</h4>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="ri-error-warning-line me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  <div class="form-container">
    <div class="form-header">
      <h5 class="m-0"><i class="ri-bank-card-line me-2"></i>Transaction History</h5>
    </div>

    <div class="form-steps-container">
      @if(isset($applications) && $applications->count())
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($applications as $app)
                @php
                  $isPaid = $app->payment_status === 'paid' || $app->proof_status === 'approved' || $app->paynow_confirmed_at;
                  $isPending = in_array($app->proof_status, ['submitted']) || $app->paynow_ref_submitted;
                  $isRejected = $app->proof_status === 'rejected' || $app->payment_status === 'failed';
                  $amount = $app->proof_amount_paid ?? null;
                  $method = $app->paynow_confirmed_at ? 'PayNow' : ($app->proof_bank_name ? 'Bank Transfer' : ($app->paynow_ref_submitted ? 'PayNow Ref' : '—'));
                  $desc = ucfirst($app->application_type) . ' — ' . ucfirst($app->request_type ?? 'new');
                  $date = $app->paynow_confirmed_at ?? $app->proof_payment_date ?? $app->payment_proof_uploaded_at ?? $app->submitted_at;
                @endphp
                <tr>
                  <td class="fw-semibold">{{ $app->reference }}</td>
                  <td class="text-muted" style="font-size:13px;">{{ $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '—' }}</td>
                  <td>{{ $desc }}</td>
                  <td>{{ $amount ? 'USD ' . number_format($amount, 2) : '—' }}</td>
                  <td style="font-size:13px;">{{ $method }}</td>
                  <td>
                    @if($isPaid)
                      <span class="badge bg-success">Paid</span>
                    @elseif($isPending)
                      <span class="badge bg-warning text-dark">Pending Verification</span>
                    @elseif($isRejected)
                      <span class="badge bg-danger">Rejected</span>
                    @else
                      <span class="badge bg-secondary">Awaiting Payment</span>
                    @endif
                  </td>
                  <td>
                    @if($isPaid)
                      <a href="{{ route('payments.receipt', $app) }}" target="_blank" class="btn btn-sm btn-success" style="border-radius:6px;">
                        <i class="ri-download-line me-1"></i>Receipt
                      </a>
                    @else
                      <button class="btn btn-sm btn-primary pay-now-btn" style="border-radius:6px;"
                        data-app-id="{{ $app->id }}"
                        data-initiate-url="{{ route('paynow.initiate', $app) }}"
                        data-mobile-url="{{ route('paynow.initiate.mobile', $app) }}"
                        data-status-url="{{ route('paynow.status', $app) }}"
                        data-proof-url="{{ route('payments.upload_proof', $app) }}"
                        data-modal-id="payModal{{ $app->id }}">
                        <i class="ri-bank-card-line me-1"></i>Pay Now
                      </button>
                    @endif
                  </td>
                </tr>

                {{-- Per-application payment modal --}}
                <x-payment-modal
                  :modal-id="'payModal' . $app->id"
                  :application-id="$app->id"
                  :description="ucfirst($app->application_type) . ' — ' . ucfirst($app->request_type ?? 'new')"
                  currency="USD"
                />
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-5">
          <i class="ri-bank-card-line" style="font-size:48px;color:#d1d5db;"></i>
          <p class="text-muted mt-3">No payment records yet. Payments will appear here once you submit an application.</p>
        </div>
      @endif
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.pay-now-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const modalId = btn.dataset.modalId;
      const appId   = btn.dataset.appId;
      initPaymentModal(modalId, appId, {
        initiate:      btn.dataset.initiateUrl,
        initiateMobile: btn.dataset.mobileUrl,
        status:        btn.dataset.statusUrl,
        proof:         btn.dataset.proofUrl,
      });
      const modal = new bootstrap.Modal(document.getElementById(modalId));
      modal.show();
    });
  });
});
</script>
@endpush

@endsection
