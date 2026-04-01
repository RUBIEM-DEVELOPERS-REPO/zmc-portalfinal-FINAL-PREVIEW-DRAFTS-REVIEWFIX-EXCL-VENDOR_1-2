@extends('layouts.portal')
@section('title', 'PayNow & Fees Audit')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">PayNow & Fees Audit</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Read-only view of PayNow-linked payments (derived from application payment fields). Duplicates and missing confirmations are highlighted.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="{{ route('staff.auditor.paynow.csv', request()->query()) }}">
        <i class="ri-file-excel-line me-1"></i>Export CSV
      </a>
      <button class="btn btn-outline-dark btn-sm" onclick="window.print()">
        <i class="ri-printer-line me-1"></i>Print PDF
      </button>
      <a class="btn btn-white border btn-sm" href="{{ route('staff.auditor.dashboard') }}">
        <i class="ri-arrow-left-line me-1"></i>Back
      </a>
    </div>
  </div>

  <div class="zmc-card shadow-sm border-0 p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small text-muted">Payment Status</label>
        <input class="form-control form-control-sm" name="status" value="{{ request('status') }}" placeholder="e.g. paid, pending">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">From</label>
        <input type="date" class="form-control form-control-sm" name="from" value="{{ request('from') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">To</label>
        <input type="date" class="form-control form-control-sm" name="to" value="{{ request('to') }}">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-filter-3-line me-1"></i>Filter</button>
        <a class="btn btn-white border btn-sm" href="{{ route('staff.auditor.paynow') }}">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card shadow-sm border-0 p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-exchange-dollar-line me-2" style="color:var(--zmc-accent)"></i>Transactions</h6>
      <div class="small text-muted">Showing {{ $applications->count() }} of {{ $applications->total() }}</div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Applicant</th>
            <th>PayNow Ref</th>
            <th>Status</th>
            <th>Confirmed</th>
            <th style="width:260px;">Flag</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $a)
            @php
              $dup = $a->paynow_reference && in_array($a->paynow_reference, $duplicateRefs, true);
              $missing = $a->paynow_reference && empty($a->paynow_confirmed_at) && ($a->payment_status === 'success' || $a->payment_status === 'paid');
            @endphp
            <tr @if($dup || $missing) style="background:#fff7ed;" @endif>
              <td class="fw-bold">{{ $a->reference }}</td>
              <td>
                <div class="fw-semibold">{{ $a->applicant->name ?? '—' }}</div>
                <div class="small text-muted">{{ $a->applicant->email ?? '—' }}</div>
              </td>
              <td class="small">
                {{ $a->paynow_reference ?? '—' }}
                @if($dup)
                  <div class="small" style="color:#dc2626;">Duplicate reference</div>
                @endif
              </td>
              <td class="small fw-bold">{{ $a->payment_status ?? '—' }}</td>
              <td class="small">
                {{ $a->paynow_confirmed_at ? $a->paynow_confirmed_at->format('d M Y H:i') : '—' }}
                @if($missing)
                  <div class="small" style="color:#dc2626;">Status suggests paid but not confirmed</div>
                @endif
              </td>
              <td>
                <form method="POST" action="{{ route('staff.auditor.flag') }}" class="d-flex gap-2">
                  @csrf
                  <input type="hidden" name="entity_type" value="paynow">
                  <input type="hidden" name="entity_id" value="{{ $a->id }}">
                  <select name="severity" class="form-select form-select-sm" style="max-width:120px;">
                    <option value="medium">MED</option>
                    <option value="low">LOW</option>
                    <option value="high">HIGH</option>
                  </select>
                  <input class="form-control form-control-sm" name="reason" placeholder="Flag reason" required>
                  <button class="btn btn-sm btn-outline-danger" type="submit"><i class="ri-flag-2-line"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-5 text-muted">No transactions found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
</div>
@endsection
