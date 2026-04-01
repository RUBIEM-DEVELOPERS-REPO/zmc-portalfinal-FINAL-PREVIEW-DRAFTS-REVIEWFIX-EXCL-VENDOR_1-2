@extends('layouts.portal')
@section('title', $title ?? 'Investigation Cases')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">{{ $title ?? 'Investigation Cases' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Track investigations and outcomes.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Case #</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Opened</th>
              <th>Assigned</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $c)
            <tr>
              <td class="fw-semibold">{{ $c->case_number ?? ('#'.$c->id) }}</td>
              <td>{{ $c->subject ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $c->status ?? '—' }}</span></td>
              <td>{{ optional($c->created_at)->format('Y-m-d') }}</td>
              <td>{{ $c->assigned_to_name ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No cases found.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($rows, 'links'))
        <div class="mt-3">{{ $rows->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
