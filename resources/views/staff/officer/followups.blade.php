@extends('layouts.portal')
@section('title', $title ?? 'Follow-up Queue')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">{{ $title ?? 'Officer Follow-up Queue' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Auto-generated follow-ups for expiring items and compliance flags.</div>
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
              <th>Type</th>
              <th>Reference</th>
              <th>Due</th>
              <th>Status</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
          @forelse($items as $it)
            <tr>
              <td class="fw-semibold">{{ $it->task_type }}</td>
              <td>{{ $it->reference ?? ('#'.$it->id) }}</td>
              <td>{{ optional($it->due_at)->format('Y-m-d') ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $it->status }}</span></td>
              <td>{{ optional($it->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No follow-ups in the queue.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($items, 'links'))
        <div class="mt-3">{{ $items->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
