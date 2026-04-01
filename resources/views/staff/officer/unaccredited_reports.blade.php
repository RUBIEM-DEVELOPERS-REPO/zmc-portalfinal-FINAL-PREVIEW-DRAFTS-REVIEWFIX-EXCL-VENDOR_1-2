@extends('layouts.portal')
@section('title', $title ?? 'Unaccredited Practice Reports')

@section('content')
<div class="container py-3" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0">{{ $title ?? 'Unaccredited Practice Reports' }}</h4>
      <div class="text-muted small mt-1"><i class="ri-information-line me-1"></i>Reports of unaccredited practice captured for follow-up.</div>
    </div>
    <div>
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Report</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $r)
            <tr>
              <td class="fw-semibold">{{ $r->reference ?? ('#'.$r->id) }}</td>
              <td>{{ $r->subject_name ?? $r->entity_name ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $r->status ?? 'open' }}</span></td>
              <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No reports found.</td></tr>
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
