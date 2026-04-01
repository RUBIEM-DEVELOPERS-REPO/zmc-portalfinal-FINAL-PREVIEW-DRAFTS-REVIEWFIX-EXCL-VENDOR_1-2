@extends('layouts.portal')
@section('title', $title ?? 'Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">{{ $title ?? 'Applications' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Click a row to open the application.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size: var(--font-size-lg); line-height: 1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Ref</th>
              <th>Applicant</th>
              <th>Type</th>
              <th>Status</th>
              <th>Submitted</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
          @forelse($applications as $app)
            <tr>
              <td class="fw-semibold">{{ $app->reference ?? ('#'.$app->id) }}</td>
              <td>{{ $app->applicant->name ?? '—' }}</td>
              <td>{{ $app->application_type ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $app->status }}</span></td>
              <td>{{ optional($app->submitted_at)->format('Y-m-d H:i') ?? optional($app->created_at)->format('Y-m-d H:i') }}</td>
              <td class="text-end">
  <div class="zmc-action-strip justify-content-end">
    <a href="{{ route('staff.officer.applications.show', $app) }}#correction" class="btn btn-sm zmc-icon-btn btn-outline-dark" title="Request correction">
      <i class="fa-regular fa-comment-dots"></i>
    </a>
    <a href="{{ route('staff.officer.applications.show', $app) }}" class="btn btn-sm zmc-icon-btn btn-outline-primary" title="View application">
      <i class="fa-regular fa-eye"></i>
    </a>
    <a href="{{ route('staff.officer.applications.show', $app) }}#approve" class="btn btn-sm zmc-icon-btn btn-outline-success" title="Approve">
      <i class="fa-solid fa-check"></i>
    </a>
    <a href="{{ route('staff.officer.applications.show', $app) }}#message" class="btn btn-sm zmc-icon-btn btn-outline-secondary" title="Message">
      <i class="fa-regular fa-envelope"></i>
    </a>
  </div>
</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No applications found.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($applications, 'links'))
        <div class="mt-3">{{ $applications->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
