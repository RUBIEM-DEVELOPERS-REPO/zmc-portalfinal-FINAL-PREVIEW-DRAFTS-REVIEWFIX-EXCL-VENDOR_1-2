@extends('layouts.portal')
@section('title', $title ?? 'Records')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">{{ $title ?? 'Records' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Issued records (active, expired, etc.).</div>
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
              <th>Number</th>
              <th>Holder / Contact</th>
              <th>Status</th>
              <th>Issued</th>
              <th>Expires</th>
              <th class="text-end">QR</th>
            </tr>
          </thead>
          <tbody>
          @forelse($rows as $row)
            <tr>
              <td class="fw-semibold">{{ $row->record_number ?? $row->certificate_no ?? ('#'.$row->id) }}</td>
              <td>{{ $row->holder->name ?? $row->contact->name ?? $row->contact_name ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $row->status ?? '—' }}</span></td>
              <td>{{ optional($row->issued_at)->format('Y-m-d') ?? '—' }}</td>
              <td>
                @php
                  $expires = optional($row->expires_at);
                @endphp
                {{ $expires?->format('Y-m-d') ?? '—' }}
              </td>
              <td class="text-end">
                @if(!empty($row->qr_token))
                  <code>{{ $row->qr_token }}</code>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No records found.</td></tr>
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
