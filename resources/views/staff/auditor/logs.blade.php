@extends('layouts.portal')
@section('title', 'Audit Logs')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Audit Logs (Immutable)</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Full read-only trail of user/system actions. These logs cannot be edited or deleted.
      </div>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="{{ route('staff.auditor.logs.csv', request()->query()) }}">
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
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-md-3">
        <label class="form-label small text-muted">Search</label>
        <input class="form-control form-control-sm" name="q" value="{{ $q }}" placeholder="action / model / id / ip">
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
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-search-line me-1"></i>Apply</button>
        <a class="btn btn-white border btn-sm" href="{{ route('staff.auditor.logs') }}">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-file-list-3-line me-2" style="color:var(--zmc-accent)"></i>Audit logs</h6>
      <div class="small text-muted">Showing {{ $logs->count() }} of {{ $logs->total() }}</div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th style="width:170px;">Time</th>
            <th>Action</th>
            <th style="width:140px;">Actor</th>
            <th>Model</th>
            <th style="width:90px;">ID</th>
            <th style="width:120px;">IP</th>
            <th>Meta</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $l)
            <tr>
              <td class="small text-muted">{{ optional($l->created_at)->format('d M Y H:i') }}</td>
              <td class="fw-bold text-dark">{{ $l->action }}</td>
              @php
                $actor = $l->actor;
              @endphp
              <td class="small">
                @if($actor)
                  <div class="fw-bold">{{ $actor->name }}</div>
                  <div class="text-muted" style="font-size: var(--font-size-sm);">{{ $actor->email }}</div>
                @else
                  {{ $l->actor_user_id ?? '—' }}
                @endif
              </td>
              <td class="small">{{ $l->model_type ? class_basename($l->model_type) : '—' }}</td>
              <td class="small">{{ $l->model_id ?? '—' }}</td>
              <td class="small">{{ $l->ip ?? '—' }}</td>
              <td class="small text-muted" style="max-width:420px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $l->meta ? json_encode($l->meta) : '—' }}
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center py-5 text-muted">No audit logs yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">{{ $logs->links() }}</div>
</div>
@endsection
