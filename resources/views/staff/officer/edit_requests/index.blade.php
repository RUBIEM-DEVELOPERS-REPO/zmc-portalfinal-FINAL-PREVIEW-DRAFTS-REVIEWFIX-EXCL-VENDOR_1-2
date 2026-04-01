@extends('layouts.portal')
@section('title', 'Edit Requests - Registrar Approval')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Edit Requests</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Review and approve/reject edit requests from accreditation officers.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i>Back to Dashboard
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-start gap-2">
      <i class="ri-error-warning-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  {{-- Filter Tabs --}}
  <div class="zmc-card mb-4">
    <div class="card-body">
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('staff.officer.edit-requests.index') }}" 
           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
          <i class="ri-list-check me-1"></i>All Requests
        </a>
        <a href="{{ route('staff.officer.edit-requests.index', ['status' => 'pending']) }}" 
           class="btn {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm">
          <i class="ri-time-line me-1"></i>Pending
          @php $pendingCount = \App\Models\EditRequest::pending()->count(); @endphp
          @if($pendingCount > 0)
            <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
          @endif
        </a>
        <a href="{{ route('staff.officer.edit-requests.index', ['status' => 'approved']) }}" 
           class="btn {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }} btn-sm">
          <i class="ri-check-line me-1"></i>Approved
        </a>
        <a href="{{ route('staff.officer.edit-requests.index', ['status' => 'rejected']) }}" 
           class="btn {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} btn-sm">
          <i class="ri-close-line me-1"></i>Rejected
        </a>
      </div>
    </div>
  </div>

  {{-- Requests List --}}
  <div class="zmc-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th>Request ID</th>
            <th>Record Type</th>
            <th>Requested By</th>
            <th>Edit Reason</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Processed</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($requests as $editRequest)
            <tr>
              <td class="fw-bold">#{{ str_pad($editRequest->id, 6, '0', STR_PAD_LEFT) }}</td>
              <td>
                <span class="badge bg-info">{{ $editRequest->record_type_label }}</span>
              </td>
              <td>
                <div class="fw-semibold">{{ $editRequest->requester?->name ?? 'Unknown' }}</div>
                <div class="small text-muted">{{ $editRequest->requester?->email ?? '' }}</div>
              </td>
              <td>
                <div class="text-truncate" style="max-width: 200px;" title="{{ $editRequest->edit_reason }}">
                  {{ Str::limit($editRequest->edit_reason, 80) }}
                </div>
              </td>
              <td>
                <span class="badge bg-{{ $editRequest->status_color }}">
                  {{ $editRequest->status_label }}
                </span>
              </td>
              <td>
                <div>{{ $editRequest->created_at->format('d M Y') }}</div>
                <div class="small text-muted">{{ $editRequest->created_at->format('H:i') }}</div>
              </td>
              <td>
                @if($editRequest->approved_at)
                  <div>{{ $editRequest->approved_at->format('d M Y') }}</div>
                  <div class="small text-muted">{{ $editRequest->approved_at->format('H:i') }}</div>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Actions
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('staff.officer.edit-requests.view', $editRequest->id) }}">
                      <i class="ri-eye-line me-1"></i>View Details
                    </a></li>
                    
                    @if($editRequest->isPending())
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-success" href="#" onclick="approveRequest({{ $editRequest->id }})">
                        <i class="ri-check-line me-1"></i>Approve Request
                      </a></li>
                      <li><a class="dropdown-item text-danger" href="#" onclick="rejectRequest({{ $editRequest->id }})">
                        <i class="ri-close-line me-1"></i>Reject Request
                      </a></li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">
                <i class="ri-inbox-line me-2"></i>
                No edit requests found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-3">{{ $requests->links() }}</div>
  </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="#" id="approveForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Approve Edit Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to approve this edit request? The changes will be applied to the record immediately.</p>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Approval Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="ri-check-line me-1"></i>Approve Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="#" id="rejectForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Reject Edit Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Rejection Reason (Required)</label>
            <textarea name="rejection_reason" class="form-control" rows="3" required
                      placeholder="Please explain why this request is being rejected..."></textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Additional Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Add any additional notes..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">
            <i class="ri-close-line me-1"></i>Reject Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function approveRequest(id) {
  const form = document.getElementById('approveForm');
  form.action = `/staff/officer/edit-requests/${id}/approve`;
  new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectRequest(id) {
  const form = document.getElementById('rejectForm');
  form.action = `/staff/officer/edit-requests/${id}/reject`;
  new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
