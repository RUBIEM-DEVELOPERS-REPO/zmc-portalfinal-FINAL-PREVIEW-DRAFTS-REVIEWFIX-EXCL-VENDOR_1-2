@extends('layouts.portal')
@section('title', 'Returned for Correction')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Returned for Correction</h4>
      <div class="text-muted mt-1" style="font-size:13px;"><i class="ri-information-line me-1"></i>Applications that were returned due to corrections requested or rejected by the officer.</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  {{-- Type tabs --}}
  @php $activeType = request('application_type'); @endphp
  <div class="d-flex flex-wrap gap-2 mb-3">
    <a class="btn btn-sm {{ !$activeType ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => null]) }}">All</a>
    <a class="btn btn-sm {{ $activeType === 'accreditation' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => 'accreditation']) }}">Accreditations</a>
    <a class="btn btn-sm {{ $activeType === 'registration' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => 'registration']) }}">Media House Registrations</a>
  </div>

  {{-- Filters --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label small fw-bold">Search</label>
          <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ref / name / email" />
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small fw-bold">From</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" />
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small fw-bold">To</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" />
        </div>
        <input type="hidden" name="application_type" value="{{ request('application_type') }}" />
        <div class="col-12 col-md-3 d-flex gap-2">
          <button class="btn btn-dark w-100"><i class="ri-filter-3-line me-1"></i>Apply</button>
          <a class="btn btn-outline-secondary w-100" href="{{ url()->current() }}">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Table --}}
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
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
              @php
                $statusLabel = match($app->status) {
                  'correction_requested' => 'Correction Requested',
                  'officer_rejected'     => 'Returned for Correction',
                  'returned_to_officer'  => 'Returned to Officer',
                  default => ucwords(str_replace('_', ' ', $app->status)),
                };
                $badgeClass = match($app->status) {
                  'correction_requested' => 'bg-warning text-dark',
                  'officer_rejected'     => 'bg-danger',
                  'returned_to_officer'  => 'bg-secondary',
                  default => 'bg-secondary',
                };
              @endphp
              <tr>
                <td class="fw-bold">{{ $app->reference }}</td>
                <td>
                  <div class="fw-semibold">{{ $app->applicant?->name ?? '—' }}</div>
                  <div class="text-muted small">{{ $app->applicant?->email ?? '—' }}</div>
                </td>
                <td><span class="badge bg-dark">{{ $app->applicationTypeLabel() }}</span></td>
                <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                <td class="small">{{ optional($app->created_at)->format('d M Y, H:i') }}</td>
                <td class="text-end">
                  <a href="{{ route('staff.officer.applications.show', $app) }}"
                     class="btn btn-sm btn-outline-primary"
                     data-bs-toggle="tooltip" title="View application">
                    <i class="ri-eye-line"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-5 text-muted">No returned applications found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
</div>
@endsection
