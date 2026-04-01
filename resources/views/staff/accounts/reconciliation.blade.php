@extends('layouts.portal')
@section('title', 'Payment Reconciliation')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Payment Reconciliation</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        Auto-matching in this build uses application fields (PayNow ref/confirm, proof approvals). Hook this page to bank settlements and PayNow webhooks to fully reconcile.
      </div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Matched payments</div>
            <div class="h3 fw-black mb-0">{{ $matched }}</div>
          </div>
          <div class="icon-box text-success"><i class="ri-check-double-line"></i></div>
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
          <div class="icon-box text-warning"><i class="ri-error-warning-line"></i></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Accounts queue (pending)</div>
            <div class="h3 fw-black mb-0">{{ $pending }}</div>
          </div>
          <div class="icon-box text-primary"><i class="ri-folders-line"></i></div>
        </div>
      </div>
    </div>
  </div>

  <div class="zmc-card">
    <div class="fw-bold mb-2"><i class="ri-git-merge-line me-2" style="color:var(--zmc-accent)"></i> Manual match / escalate discrepancy</div>
    <div class="text-muted small">
      This module is ready for wiring to: (1) PayNow settlement reports, (2) bank statements, and (3) unmatched PayNow references.
      Add a dedicated <code>paynow_transactions</code> table + webhook receiver for true reconciliation.
    </div>
  </div>
</div>
@endsection
