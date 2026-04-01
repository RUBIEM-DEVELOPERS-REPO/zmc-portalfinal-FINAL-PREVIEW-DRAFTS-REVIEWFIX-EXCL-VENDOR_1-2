@extends('layouts.portal')
@section('title', 'Payment Exception Reports')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Payment Exception Reports</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Rejected proofs, failed transactions, and unmatched items.</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Rejected proofs</div>
            <div class="h3 fw-black mb-0">{{ $rejectedProofs }}</div>
          </div>
          <div class="icon-box" style="color:#dc2626"><i class="ri-file-warning-line"></i></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Failed PayNow</div>
            <div class="h3 fw-black mb-0">{{ $failedPaynow }}</div>
          </div>
          <div class="icon-box text-warning"><i class="ri-close-circle-line"></i></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Unmatched / missing ref</div>
            <div class="h3 fw-black mb-0">{{ $unmatched }}</div>
          </div>
          <div class="icon-box text-muted"><i class="ri-link-unlink-m"></i></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
