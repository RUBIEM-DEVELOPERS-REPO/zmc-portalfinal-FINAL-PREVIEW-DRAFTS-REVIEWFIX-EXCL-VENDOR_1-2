@extends('layouts.portal')
@section('title', 'Paid Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Approved (Paid)</h4>
      <div class="text-muted mt-1" style="font-size:13px;">Applications with confirmed payment, approved for processing.</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold text-success"><i class="ri-shield-check-line me-2"></i> Approved & Paid</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant</th>
            <th>Application #</th>
            <th>Reference/PayNow</th>
            <th>Method</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $p_ref = $app->paynow_reference ?? $app->batch?->reference ?? $app->receipt_number ?? '—';
              $method = $app->payment_method ?? '—';
              $status_label = match($app->status) {
                'paid_confirmed' => 'Paid Confirmed',
                'production_queue' => 'In Production',
                default => ucfirst(str_replace('_', ' ', $app->status))
              };
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td class="text-muted small">{{ $p_ref }}</td>
              <td class="text-capitalize text-muted">{{ $method }}</td>
              <td><span class="badge bg-soft-success text-success">{{ $status_label }}</span></td>
              <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}"><i class="ri-eye-line"></i></a></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted p-4">No applications found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
