@extends('layouts.portal')
@section('title', $title ?? 'Expiry Tracking')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">{{ $title ?? 'Expiry Tracking' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        {{ $isExpired ? 'Items already expired.' : 'Items nearing expiry.' }}
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.renewals.expiring') }}" class="btn btn-white border shadow-sm btn-sm px-3">Expiring</a>
      <a href="{{ route('staff.officer.renewals.expired') }}" class="btn btn-white border shadow-sm btn-sm px-3">Expired</a>
      @if(!($isExpired ?? false))
        <form method="POST" action="{{ route('staff.officer.renewals.send-reminders') }}" class="d-inline">
          @csrf
          <input type="hidden" name="record_type" value="accreditation">
          <button type="submit" class="btn btn-success shadow-sm btn-sm px-3" title="Send reminders to all expiring media practitioners">
            <i class="ri-mail-send-line me-1"></i> Send All (Media Practitioners)
          </button>
        </form>
        <form method="POST" action="{{ route('staff.officer.renewals.send-reminders') }}" class="d-inline">
          @csrf
          <input type="hidden" name="record_type" value="registration">
          <button type="submit" class="btn btn-success shadow-sm btn-sm px-3" title="Send reminders to all expiring media houses">
            <i class="ri-mail-send-line me-1"></i> Send All (Media Houses)
          </button>
        </form>
      @endif
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold">Accreditation (Media Practitioners)</h6>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead><tr><th>Holder</th><th>Certificate</th><th>Status</th><th>Expires</th><th class="text-end">Days</th></tr></thead>
              <tbody>
              @forelse($accreditation ?? [] as $r)
                @php
                  $days = $r->expires_at ? now()->startOfDay()->diffInDays($r->expires_at->startOfDay(), false) : null;
                @endphp
                <tr>
                  <td>{{ $r->holder->name ?? '—' }}</td>
                  <td>{{ $r->certificate_no ?? '—' }}</td>
                  <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                  <td>{{ optional($r->expires_at)->format('Y-m-d') ?? '—' }}</td>
                  <td class="text-end">{{ is_null($days) ? '—' : $days }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No records.</td></tr>
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
              <thead><tr><th>Entity</th><th>Reg No</th><th>Status</th><th>Expires</th><th class="text-end">Days</th></tr></thead>
              <tbody>
              @forelse($registration ?? [] as $r)
                @php
                  $days = $r->expires_at ? now()->startOfDay()->diffInDays($r->expires_at->startOfDay(), false) : null;
                @endphp
                <tr>
                  <td>{{ $r->entity_name ?? '—' }}</td>
                  <td>{{ $r->registration_no ?? '—' }}</td>
                  <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                  <td>{{ optional($r->expires_at)->format('Y-m-d') ?? '—' }}</td>
                  <td class="text-end">{{ is_null($days) ? '—' : $days }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No records.</td></tr>
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
