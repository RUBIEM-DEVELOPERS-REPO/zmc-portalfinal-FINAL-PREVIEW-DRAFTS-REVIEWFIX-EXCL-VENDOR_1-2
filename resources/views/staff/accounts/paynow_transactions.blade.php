@extends('layouts.portal')
@section('title', 'PayNow Transactions')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">PayNow Transactions</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        Real-time feed built from application payment fields (PayNow ref/status). When PayNow webhooks are enabled, this becomes your operational queue.
      </div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-12 col-md-2">
        <label class="form-label small text-muted">From</label>
        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label small text-muted">To</label>
        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label small text-muted">Payment status</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          @foreach(['paid'=>'Paid','pending'=>'Pending','failed'=>'Failed','reversed'=>'Reversed'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label small text-muted">Application type</label>
        <select name="type" class="form-select">
          <option value="">All</option>
          <option value="accreditation" @selected(request('type')==='accreditation')>Accreditation</option>
          <option value="registration" @selected(request('type')==='registration')>Registration</option>
        </select>
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label small text-muted">Amount</label>
        <input type="text" name="amount" class="form-control" value="{{ request('amount') }}" placeholder="(placeholder)">
      </div>
      <div class="col-12 col-md-2 d-flex gap-2">
        <button class="btn btn-primary w-100"><i class="ri-filter-3-line me-1"></i> Filter</button>
        <a class="btn btn-outline-secondary w-100" href="{{ route('staff.accounts.paynow.transactions') }}">Clear</a>
      </div>
    </form>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <div class="fw-bold"><i class="ri-bank-card-line me-2" style="color:var(--zmc-accent)"></i> Transactions</div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-dark" disabled title="Export will be wired to CSV/PDF"><i class="ri-download-2-line me-1"></i> Export CSV</button>
        <button type="button" class="btn btn-sm btn-outline-dark" disabled><i class="ri-file-pdf-2-line me-1"></i> Export PDF</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant / Media House</th>
            <th>Application #</th>
            <th>Fee type</th>
            <th>Amount</th>
            <th>PayNow ref</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $status = strtolower((string)($app->payment_status ?? 'pending'));
              $badge = match($status) {
                'paid' => 'success',
                'failed' => 'danger',
                'reversed' => 'warning',
                default => 'info',
              };
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td class="text-capitalize">{{ $app->application_type ?? '—' }}</td>
              <td class="text-muted">—</td>
              <td class="text-muted">{{ $app->paynow_reference ?? '—' }}</td>
              <td><span class="badge rounded-pill bg-{{ $badge }} px-3">{{ ucfirst($status) }}</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}"><i class="ri-eye-line"></i></a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted p-4">No PayNow-linked transactions found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
