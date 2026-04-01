@extends('layouts.portal')
@section('title', 'Approved Payment Proofs')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Approved Payment Proofs</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Read-only history of approved proofs, for audit and tracking.</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-checkbox-circle-line me-2" style="color:var(--zmc-accent)"></i> Approved proofs</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant</th>
            <th>Application #</th>
            <th>Proof</th>
            <th>PayNow ref</th>
            <th>Approved</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $proofPath = $app->payment_proof_path;
              if (!$proofPath) {
                $proofDoc = $app->documents?->firstWhere('doc_type', 'proof_of_payment');
                $proofPath = $proofDoc?->file_path;
              }
              $proofUrl = $proofPath ? asset('storage/' . ltrim($proofPath, '/')) : null;
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td>
                @if($proofUrl)
                  <a href="{{ $proofUrl }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="ri-attachment-2"></i> View</a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="text-muted">{{ $app->paynow_reference ?? '—' }}</td>
              <td class="small text-muted">{{ $app->proof_reviewed_at ? \Carbon\Carbon::parse($app->proof_reviewed_at)->format('d M Y H:i') : '—' }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}" title="Open application"><i class="ri-eye-line"></i></a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted p-4">No approved proofs yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@endsection
