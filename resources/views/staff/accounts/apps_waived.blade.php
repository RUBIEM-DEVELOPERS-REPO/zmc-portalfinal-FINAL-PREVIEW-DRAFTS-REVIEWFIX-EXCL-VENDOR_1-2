@extends('layouts.portal')
@section('title', 'Waived Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Waived Applications</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Applications approved under waiver (audit tagged).</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-price-tag-3-line me-2" style="color:var(--zmc-accent)"></i> Waived applications</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant</th>
            <th>Application #</th>
            <th>Type</th>
            <th>Waiver</th>
            <th>Reviewed</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $waiverType = data_get($app->form_data, 'waiver_type') ?? 'Full/Partial';
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td class="text-capitalize">{{ $app->application_type ?? '—' }}</td>
              <td class="text-muted">{{ $waiverType }}</td>
              <td class="small text-muted">{{ $app->waiver_reviewed_at ? \Carbon\Carbon::parse($app->waiver_reviewed_at)->format('d M Y H:i') : '—' }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}"><i class="ri-eye-line"></i></a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted p-4">No waived applications.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
