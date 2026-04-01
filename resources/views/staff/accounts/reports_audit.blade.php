@extends('layouts.portal')
@section('title', 'Audit Reports')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Audit Reports</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Accounts/Payments approval logs (who did what & when).</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-file-search-line me-2" style="color:var(--zmc-accent)"></i> Audit trail</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Action</th>
            <th>Model</th>
            <th>Model ID</th>
            <th>Meta</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td class="small text-muted">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') : '—' }}</td>
              <td class="fw-bold">{{ $log->action }}</td>
              <td class="text-muted small">{{ class_basename($log->model_type ?? '') ?: '—' }}</td>
              <td class="text-muted small">{{ $log->model_id ?? '—' }}</td>
              <td class="small text-muted" style="max-width:520px;">
                <div class="text-truncate" title="{{ json_encode($log->meta ?? []) }}">{{ json_encode($log->meta ?? []) }}</div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted p-4">No audit logs yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $logs->links() }}</div>
  </div>
</div>
@endsection
