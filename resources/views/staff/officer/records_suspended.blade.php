@extends('layouts.portal')
@section('title', $title ?? 'Suspended / Revoked')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">{{ $title ?? 'Suspended / Revoked Records' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Suspended/revoked items across accreditation and registration.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold">Accreditation (Media Practitioners)</h6>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead><tr><th>Holder</th><th>Certificate</th><th>Status</th><th>Expires</th></tr></thead>
              <tbody>
              @forelse($accreditation ?? [] as $r)
                <tr>
                  <td>{{ $r->holder->name ?? '—' }}</td>
                  <td>{{ $r->certificate_no ?? '—' }}</td>
                  <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                  <td>{{ optional($r->expires_at)->format('Y-m-d') ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No records.</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
          @if(($accreditation ?? null) && method_exists($accreditation, 'links'))
            <div class="mt-2">{{ $accreditation->links() }}</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold">Registration (Media Houses)</h6>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead><tr><th>Entity</th><th>Reg No</th><th>Status</th><th>Expires</th></tr></thead>
              <tbody>
              @forelse($registration ?? [] as $r)
                <tr>
                  <td>{{ $r->entity_name ?? '—' }}</td>
                  <td>{{ $r->registration_no ?? '—' }}</td>
                  <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                  <td>{{ optional($r->expires_at)->format('Y-m-d') ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No records.</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
          @if(($registration ?? null) && method_exists($registration, 'links'))
            <div class="mt-2">{{ $registration->links() }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
