@extends('layouts.portal')

@section('title', 'My Batches - Media House Portal')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Batch Processing</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Manage and track bulk practitioner renewals.
      </div>
    </div>
    <a href="{{ route('mediahouse.staff.index') }}" class="btn btn-dark btn-sm px-3">
      <i class="ri-user-add-line me-1"></i> New Batch
    </a>
  </div>

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom">
      <h6 class="fw-bold m-0"><i class="ri-history-line me-2" style="color:var(--zmc-accent)"></i>Batch History</h6>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Amount</th>
            <th>Staff Count</th>
            <th>Status</th>
            <th>Date</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($batches as $batch)
            <tr>
              <td class="fw-bold">{{ $batch->reference }}</td>
              <td>{{ number_format($batch->amount, 2) }} USD</td>
              <td>{{ count($batch->metadata['journalist_ids'] ?? []) }}</td>
              <td>
                <span class="badge rounded-pill
                  @if($batch->status === 'paid') bg-success
                  @elseif($batch->status === 'pending_verification') bg-info
                  @elseif($batch->status === 'rejected') bg-danger
                  @else bg-warning text-dark
                  @endif">
                  {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                </span>
              </td>
              <td class="small text-muted">{{ $batch->created_at->format('d M Y') }}</td>
              <td class="text-end">
                <a href="{{ route('mediahouse.batch.show', $batch) }}" class="btn btn-sm btn-outline-dark zmc-icon-btn" title="View Details">
                  <i class="ri-eye-line"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">No batches found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    @if($batches->hasPages())
      <div class="p-3 border-top">
        {{ $batches->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
