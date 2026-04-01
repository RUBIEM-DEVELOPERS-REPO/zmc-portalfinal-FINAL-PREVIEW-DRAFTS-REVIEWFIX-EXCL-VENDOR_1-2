@extends('layouts.portal')
@section('title', 'Paid Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Paid Applications</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Applications with confirmed payment (PayNow confirmation, approved proof, or marked paid).</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-shield-check-line me-2" style="color:var(--zmc-accent)"></i> Paid</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant</th>
            <th>Application #</th>
            <th>Payment method</th>
            <th>Ready for Registrar</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $method = ($app->waiver_status ?? 'none') === 'approved' ? 'Waiver' : 'PayNow/Proof';
              $ready = in_array(($app->status ?? ''), ['registrar_review','paid_confirmed'], true) ? 'Yes' : '—';
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td class="text-muted">{{ $method }}</td>
              <td class="text-muted">{{ $ready }}</td>
              <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}"><i class="ri-eye-line"></i></a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted p-4">No paid applications.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
