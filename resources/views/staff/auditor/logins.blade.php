@extends('layouts.portal')
@section('title', 'User Logins')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">User Logins</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-login-box-line me-1"></i>Login events with user name and recent activity actions.</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="{{ route('staff.auditor.logins.csv', request()->query()) }}">
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
        <label class="form-label small text-muted">Search</label>
        <input class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="Name, email, IP, action">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">From</label>
        <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from', request('from')) }}">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">To</label>
        <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to', request('to')) }}">
      </div>
      <div class="col-md-5 d-flex gap-2">
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-filter-3-line me-1"></i>Filter</button>
        <a class="btn btn-white border btn-sm" href="{{ route('staff.auditor.logins') }}">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card shadow-sm border-0 p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-history-line me-2" style="color:var(--zmc-accent)"></i>Login Events</h6>
      <div class="small text-muted">Read-only</div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr class="text-muted small">
            <th style="width:170px;">Time</th>
            <th style="width:220px;">User</th>
            <th style="width:220px;">Action</th>
            <th style="width:160px;">IP</th>
            <th>Recent user activity</th>
          </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
          @php
            $user = $log->actor;
            $userLabel = $user ? ($user->name . ' (' . $user->email . ')') : ('User #' . (int)($log->actor_user_id ?? 0));
            $recent = $recentByUser[(int)($log->actor_user_id ?? 0)] ?? [];
          @endphp
          <tr>
            <td class="small text-muted">{{ optional($log->created_at)->format('d M Y H:i') }}</td>
            <td class="small fw-bold">{{ $userLabel }}</td>
            <td class="small">{{ str_replace('_',' ', (string)$log->action) }}</td>
            <td class="small text-muted">{{ $log->ip ?? '—' }}</td>
            <td class="small">
              @if(!empty($recent))
                <ul class="mb-0 ps-3">
                  @foreach($recent as $r)
                    <li class="text-muted" style="margin-bottom:2px;">
                      {{ optional($r->created_at)->format('d M H:i') }} - {{ str_replace('_',' ', (string)$r->action) }}
                    </li>
                  @endforeach
                </ul>
              @else
                <span class="text-muted">No recent actions found.</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-muted small">No login events found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-3">
      {{ $logs->links() }}
    </div>
  </div>
</div>
@endsection
