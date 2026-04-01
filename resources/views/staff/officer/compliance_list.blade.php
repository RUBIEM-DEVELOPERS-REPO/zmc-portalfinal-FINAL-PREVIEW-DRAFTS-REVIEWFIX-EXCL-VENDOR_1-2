@extends('layouts.portal')
@section('title', $title ?? 'Compliance')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">{{ $title ?? 'Compliance' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Log violations, attach evidence, and escalate for action.</div>
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
              <th>Ref</th>
              <th>Type</th>
              <th>Status</th>
              <th>Created</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $r)
            <tr>
              <td class="fw-semibold">{{ $r->reference ?? ('#'.$r->id) }}</td>
              <td>{{ $r->category ?? $r->type ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $r->status ?? '—' }}</span></td>
              <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
              <td class="text-muted">{{ \Illuminate\Support\Str::limit($r->summary ?? $r->notes ?? '', 80) }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No compliance items found.</td></tr>
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
