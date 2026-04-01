@extends('layouts.portal')
@section('title', 'Payment Proofs Audit')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Payment Proofs Audit</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Read-only view of uploaded receipts/proofs. Auditors cannot approve/reject.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="{{ route('staff.auditor.proofs.csv', request()->query()) }}">
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
        <label class="form-label small text-muted">Proof Status</label>
        <select class="form-select form-select-sm" name="status">
          <option value="">All</option>
          <option value="submitted" @selected(request('status')==='submitted')>Submitted</option>
          <option value="approved" @selected(request('status')==='approved')>Approved</option>
          <option value="rejected" @selected(request('status')==='rejected')>Returned for Correction</option>
        </select>
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
        <a class="btn btn-white border btn-sm" href="{{ route('staff.auditor.proofs') }}">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card shadow-sm border-0 p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-file-copy-2-line me-2" style="color:var(--zmc-accent)"></i>Proofs</h6>
      <div class="small text-muted">Showing {{ $applications->count() }} of {{ $applications->total() }}</div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Applicant</th>
            <th>Status</th>
            <th>Uploaded</th>
            <th>File</th>
            <th style="width:260px;">Flag</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $a)
            <tr>
              <td class="fw-bold">{{ $a->reference }}</td>
              <td>
                <div class="fw-semibold">{{ $a->applicant->name ?? '—' }}</div>
                <div class="small text-muted">{{ $a->applicant->email ?? '—' }}</div>
              </td>
              <td class="small fw-bold">{{ $a->proof_status === 'rejected' ? 'Returned for Correction' : ($a->proof_status ?? '—') }}</td>
              <td class="small">{{ $a->payment_proof_uploaded_at ? $a->payment_proof_uploaded_at->format('d M Y H:i') : '—' }}</td>
              <td class="small">
                @if($a->payment_proof_path)
                  <a href="{{ asset($a->payment_proof_path) }}" target="_blank" class="btn btn-sm btn-white border"><i class="ri-eye-line me-1"></i>View</a>
                @else
                  —
                @endif
              </td>
              <td>
                <form method="POST" action="{{ route('staff.auditor.flag') }}" class="d-flex gap-2">
                  @csrf
                  <input type="hidden" name="entity_type" value="payment_proof">
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
            <tr><td colspan="6" class="text-center py-5 text-muted">No payment proofs found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
</div>
@endsection
