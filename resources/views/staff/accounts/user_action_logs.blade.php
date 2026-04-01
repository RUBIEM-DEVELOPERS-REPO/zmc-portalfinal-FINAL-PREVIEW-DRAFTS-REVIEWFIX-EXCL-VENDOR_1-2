@extends('layouts.portal')
@section('title', 'User Action Logs')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">User Action Logs</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Timestamped audit trail of approvals/rejections and other actions.</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-12 col-md-4">
        <label class="form-label small text-muted">Action</label>
        <input class="form-control" name="action" value="{{ request('action') }}" placeholder="e.g., accounts_proof_approved">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label small text-muted">User ID</label>
        <input class="form-control" name="user" value="{{ request('user') }}" placeholder="e.g., 16">
      </div>
      <div class="col-12 col-md-4 d-flex gap-2">
        <button class="btn btn-primary w-100"><i class="ri-filter-3-line me-1"></i> Filter</button>
        <a class="btn btn-outline-secondary w-100" href="{{ route('staff.accounts.tools.logs') }}">Clear</a>
      </div>
    </form>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-history-line me-2" style="color:var(--zmc-accent)"></i> Logs</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Action</th>
            <th>User</th>
            <th>Model</th>
            <th>Meta</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td class="small text-muted">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') : '—' }}</td>
              <td class="fw-bold">{{ $log->action }}</td>
              <td class="text-muted">{{ $log->actor_user_id ?? '—' }}</td>
              <td class="text-muted">{{ $log->model_type ? class_basename($log->model_type) . '#' . $log->model_id : '—' }}</td>
              <td class="text-muted" style="max-width:520px;">
                <div class="small text-truncate" title="{{ json_encode($log->meta ?? []) }}">{{ is_array($log->meta) ? json_encode($log->meta) : ($log->meta ?? '—') }}</div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted p-4">No logs found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $logs->links() }}</div>
  </div>
</div>
@endsection