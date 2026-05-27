@extends('layouts.portal')

@section('title', 'My Renewals & Replacements (AP5)')

@section('content')
<div id="mediahouse-renewals-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">My Renewals & Replacements (AP5)</h4>
    <a class="btn btn-secondary" href="{{ route('mediahouse.portal') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Dashboard
    </a>
  </div>

  <div class="mb-4">
    <a href="{{ route('mediahouse.renewals.start','renewal') }}" class="btn btn-primary">
      <i class="ri-add-line me-1"></i>Start New Renewal (AP5)
    </a>
    <a href="{{ route('mediahouse.renewals.start','replacement') }}" class="btn btn-outline-dark ms-2">
      <i class="ri-add-line me-1"></i>Start New Replacement (AP5)
    </a>
  </div>

  @if($renewals->count() > 0)
    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="ri-file-list-3-line me-1"></i>My Renewal Applications</h6>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Type</th>
                <th>Registration Number</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($renewals as $renewal)
                <tr>
                  <td class="text-capitalize">{{ $renewal->request_type ?? 'Renewal' }}</td>
                  <td class="fw-semibold"><code>{{ $renewal->original_number ?? 'N/A' }}</code></td>
                  <td>
                    <span class="badge 
                      @if($renewal->status === 'renewal_produced_ready_for_collection') bg-success
                      @elseif($renewal->status === 'renewal_payment_rejected') bg-danger
                      @elseif($renewal->status === 'renewal_payment_verified') bg-info
                      @elseif(str_contains($renewal->status, 'awaiting')) bg-warning
                      @else bg-secondary
                      @endif">
                      {{ ucwords(str_replace('_', ' ', $renewal->status)) }}
                    </span>
                  </td>
                  <td class="text-muted">{{ $renewal->created_at->format('d M Y, H:i') }}</td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-primary" href="{{ route('mediahouse.renewals.show', $renewal) }}">
                      <i class="ri-eye-line me-1"></i>View
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @if($renewals->hasPages())
      <div class="mt-3">
        {{ $renewals->links() }}
      </div>
    @endif
  @else
    <div class="card shadow-sm">
      <div class="card-body text-center py-5">
        <i class="ri-file-list-3-line" style="font-size:48px; opacity:0.3;"></i>
        <h5 class="mt-3 mb-2">No Renewals Yet</h5>
        <p class="text-muted mb-4">You haven't started any renewal or replacement applications.</p>
        <a href="{{ route('mediahouse.renewals.select-type') }}" class="btn btn-primary">
          <i class="ri-add-line me-1"></i>Start Your First Renewal
        </a>
      </div>
    </div>
  @endif
</div>
@endsection
