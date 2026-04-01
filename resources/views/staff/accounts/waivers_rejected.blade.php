@extends('layouts.portal')
@section('title', 'Rejected Waivers')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Rejected Waivers</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Waivers rejected with reasons (useful for appeals and audit).</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-close-circle-line me-2" style="color:#dc2626"></i> Rejected waivers</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant</th>
            <th>Application #</th>
            <th>Waiver doc</th>
            <th>Reviewed</th>
            <th>Reason</th>
            <th>Appeal</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $waiverUrl = $app->waiver_path ? asset('storage/' . ltrim($app->waiver_path, '/')) : null;
              $appeal = data_get($app->form_data, 'waiver_appeal') ?? data_get($app->form_data, 'appeal') ?? null;
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td>
                @if($waiverUrl)
                  <a href="{{ $waiverUrl }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="ri-attachment-2"></i> View</a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="small text-muted">{{ $app->waiver_reviewed_at ? \Carbon\Carbon::parse($app->waiver_reviewed_at)->format('d M Y H:i') : '—' }}</td>
              <td class="text-muted" style="max-width:420px;">
                <div class="small text-truncate" title="{{ $app->waiver_review_notes ?? '' }}">{{ $app->waiver_review_notes ?? '—' }}</div>
              </td>
              <td class="text-muted small">{{ $appeal ? 'Filed' : '—' }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}" title="Open application"><i class="ri-eye-line"></i></a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted p-4">No rejected waivers.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
