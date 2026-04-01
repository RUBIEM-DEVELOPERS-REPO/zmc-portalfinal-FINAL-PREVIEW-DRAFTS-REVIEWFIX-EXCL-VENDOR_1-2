@extends('layouts.portal')
@section('title', $title ?? 'Audit Trail')

@section('content')
<div class="container py-3" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0">{{ $title ?? 'Audit Trail' }}</h4>
      <div class="text-muted small mt-1"><i class="ri-information-line me-1"></i>Filter by application id using <code>?application_id=...</code>.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form class="row g-2 mb-3" method="get">
        <div class="col-12 col-md-4">
          <input type="number" class="form-control" name="application_id" value="{{ $applicationId }}" placeholder="Application ID (optional)">
        </div>
        <div class="col-12 col-md-2">
          <button class="btn btn-primary w-100" type="submit">Filter</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>When</th>
              <th>Action</th>
              <th>Entity</th>
              <th>From</th>
              <th>To</th>
              <th>Actor</th>
            </tr>
          </thead>
          <tbody>
          @forelse($logs as $log)
            <tr>
              <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
              <td class="fw-semibold">{{ $log->action ?? $log->event ?? '—' }}</td>
              <td>{{ class_basename($log->entity_type ?? '') }} #{{ $log->entity_id ?? '—' }}</td>
              <td><span class="badge bg-light text-dark">{{ $log->from_status ?? '—' }}</span></td>
              <td><span class="badge bg-light text-dark">{{ $log->to_status ?? '—' }}</span></td>
              <td>{{ $log->actor_user_id ?? $log->user_id ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No logs found.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($logs, 'links'))
        <div class="mt-3">{{ $logs->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
