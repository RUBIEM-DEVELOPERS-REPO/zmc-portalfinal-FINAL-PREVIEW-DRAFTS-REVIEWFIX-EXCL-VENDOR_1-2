@extends('layouts.portal')
@section('title', 'Registered Media Houses')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">
        Registered Media Houses
      </h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        View and manage all registered media houses. Filter by entity name, registration number, collection status, or expiry.
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      @if(Route::has('staff.officer.records.export'))
      <a href="{{ route('staff.officer.records.export', ['type' => 'mediahouses']) }}?{{ http_build_query(request()->only(['search','collection_status','expiry_status'])) }}" class="btn btn-outline-success border shadow-sm btn-sm px-3">
        <i class="ri-file-excel-2-line me-1"></i> Export CSV
      </a>
      @endif
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i> Back to Dashboard
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size: var(--font-size-lg); line-height: 1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  {{-- Analytics Summary --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="zmc-card text-center p-3">
        <div class="display-6 fw-bold" style="color:#1a1a1a;">{{ $mhStats['total'] ?? 0 }}</div>
        <div class="small text-muted">Total Registered</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="zmc-card text-center p-3">
        <div class="display-6 fw-bold text-success">{{ $mhStats['collected'] ?? 0 }}</div>
        <div class="small text-muted">Collected</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="zmc-card text-center p-3">
        <div class="display-6 fw-bold text-warning">{{ $mhStats['uncollected'] ?? 0 }}</div>
        <div class="small text-muted">Uncollected</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="zmc-card text-center p-3">
        <div class="display-6 fw-bold text-danger">{{ $mhStats['expired'] ?? 0 }}</div>
        <div class="small text-muted">Expired</div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="zmc-card mb-4">
    <h6 class="fw-bold mb-3"><i class="ri-filter-3-line me-2"></i> Filters</h6>
    <form method="GET" action="{{ route('staff.officer.records.registered-mediahouses') }}">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label class="form-label small fw-bold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Entity Name or Reg No" value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Collection Status</label>
          <select name="collection_status" class="form-select">
            <option value="">All</option>
            <option value="collected" {{ request('collection_status') === 'collected' ? 'selected' : '' }}>Collected</option>
            <option value="uncollected" {{ request('collection_status') === 'uncollected' ? 'selected' : '' }}>Uncollected</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Expiry Status</label>
          <select name="expiry_status" class="form-select">
            <option value="">All</option>
            <option value="expiring_soon" {{ request('expiry_status') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (90 days)</option>
            <option value="expired" {{ request('expiry_status') === 'expired' ? 'selected' : '' }}>Expired</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">
            <i class="ri-search-line me-1"></i> Filter
          </button>
        </div>
      </div>
      @if(request()->hasAny(['search', 'collection_status', 'expiry_status']))
        <div class="mt-3">
          <a href="{{ route('staff.officer.records.registered-mediahouses') }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-close-line me-1"></i> Clear Filters
          </a>
        </div>
      @endif
    </form>
  </div>

  {{-- Bulk Actions --}}
  @if($mediaHouses->where('collected_at', null)->count() > 0)
    <div class="mb-3">
      <form method="POST" action="{{ route('staff.officer.records.send-collection-notification') }}" class="d-inline">
        @csrf
        <input type="hidden" name="record_type" value="registration">
        <button type="submit" class="btn btn-success shadow-sm">
          <i class="ri-mail-send-line me-1"></i> Send Collection Reminders (Uncollected Only)
        </button>
      </form>
    </div>
  @endif

  {{-- Table --}}
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th>Entity Name</th>
            <th>Registration No</th>
            <th>Issued Date</th>
            <th>Expiry Date</th>
            <th>Collection Status</th>
            <th class="text-end">Days to Expiry</th>
          </tr>
        </thead>
        <tbody>
          @forelse($mediaHouses as $mediaHouse)
            @php
              $daysToExpiry = $mediaHouse->expires_at ? now()->startOfDay()->diffInDays($mediaHouse->expires_at->startOfDay(), false) : null;
              $urgency = $daysToExpiry !== null && $daysToExpiry <= 30 ? 'danger' : ($daysToExpiry <= 90 ? 'warning' : 'success');
            @endphp
            <tr>
              <td class="small">{{ $mediaHouse->entity_name ?? '—' }}</td>
              <td class="small fw-bold">{{ $mediaHouse->registration_no ?? '—' }}</td>
              <td class="small">{{ optional($mediaHouse->issued_at)->format('d M Y') ?? '—' }}</td>
              <td class="small">{{ optional($mediaHouse->expires_at)->format('d M Y') ?? '—' }}</td>
              <td>
                @if($mediaHouse->collected_at)
                  <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="ri-check-line me-1"></i> Collected {{ $mediaHouse->collected_at->format('d M Y') }}
                  </span>
                @else
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                    <i class="ri-time-line me-1"></i> Uncollected
                  </span>
                @endif
              </td>
              <td class="text-end">
                @if($daysToExpiry !== null)
                  <span class="badge bg-{{ $urgency }}-subtle text-{{ $urgency }} border border-{{ $urgency }}-subtle">
                    {{ $daysToExpiry }} days
                  </span>
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                <i class="ri-building-line" style="font-size:48px;opacity:0.3;"></i>
                <div class="mt-2">No registered media houses found</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($mediaHouses->hasPages())
      <div class="p-3 border-top">
        {{ $mediaHouses->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
