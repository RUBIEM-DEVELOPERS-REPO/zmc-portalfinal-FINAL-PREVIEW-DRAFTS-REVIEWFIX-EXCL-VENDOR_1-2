@extends('layouts.portal')
@section('title', 'Edit Request Details')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Edit Request Details</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Review the requested changes and approve or reject the edit request.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.edit-requests.index') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i>Back to Requests
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

  {{-- Request Summary --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-primary text-white">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-file-list-3-line me-2"></i>
          Request Summary
        </h6>
        <span class="badge bg-light text-dark">
          Request #{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}
        </span>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Record Type</label>
          <div class="h6">
            <span class="badge bg-info">{{ $request->record_type_label }}</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Status</label>
          <div class="h6">
            <span class="badge bg-{{ $request->status_color }}">
              {{ $request->status_label }}
            </span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Submitted</label>
          <div class="h6">{{ $request->created_at->format('d M Y H:i') }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Requested By</label>
          <div class="h6">{{ $request->requester?->name ?? 'Unknown' }}</div>
          <div class="small text-muted">{{ $request->requester?->email ?? '' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Processed By</label>
          <div class="h6">{{ $request->approver?->name ?? 'Not processed yet' }}</div>
          <div class="small text-muted">{{ $request->approver?->email ?? '' }}</div>
        </div>
      </div>
      
      <div class="mt-3 pt-3 border-top">
        <label class="form-label small fw-bold text-muted">Edit Reason</label>
        <div class="p-3 bg-light rounded">
          {{ $request->edit_reason }}
        </div>
      </div>
    </div>
  </div>

  {{-- Record Information --}}
  @if($request->record_type === 'accreditation' && $request->accreditationRecord)
    <div class="zmc-card mb-4">
      <div class="card-header bg-light">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-award-line me-2"></i>
          Accreditation Record
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Certificate Number</label>
            <div class="h6 text-primary">{{ $request->accreditationRecord->certificate_no }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Applicant Name</label>
            <div class="h6">{{ $request->accreditationRecord->holder?->name ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Organization</label>
            <div class="h6">{{ $request->accreditationRecord->application->form_data['organization'] ?? '—' }}</div>
          </div>
        </div>
      </div>
    </div>
  @elseif($request->record_type === 'registration' && $request->registrationRecord)
    <div class="zmc-card mb-4">
      <div class="card-header bg-light">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-building-line me-2"></i>
          Registration Record
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Registration Number</label>
            <div class="h6 text-success">{{ $request->registrationRecord->registration_no }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Entity Name</label>
            <div class="h6">{{ $request->registrationRecord->application->form_data['entity_name'] ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Business Type</label>
            <div class="h6">{{ ucfirst($request->registrationRecord->application->form_data['business_type'] ?? '—') }}</div>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- Changes Summary --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-warning text-dark">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-diff-line me-2"></i>
        Requested Changes
      </h6>
    </div>
    <div class="card-body">
      @php
        $changes = $request->changes_summary;
      @endphp
      
      @if(empty($changes))
        <div class="text-muted text-center py-3">
          <i class="ri-information-line me-2"></i>
          No changes detected in this request.
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Field</th>
                <th>Current Value</th>
                <th>New Value</th>
              </tr>
            </thead>
            <tbody>
              @foreach($changes as $change)
                <tr>
                  <td class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $change['field'])) }}</td>
                  <td>
                    <span class="text-muted">{{ $change['old_value'] ?? '—' }}</span>
                  </td>
                  <td>
                    <span class="text-primary fw-semibold">{{ $change['new_value'] ?? '—' }}</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  {{-- Processing Information --}}
  @if($request->isApproved() || $request->isRejected())
    <div class="zmc-card mb-4">
      <div class="card-header bg-light">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-information-line me-2"></i>
          Processing Information
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold text-muted">Processed By</label>
            <div class="h6">{{ $request->approver?->name }}</div>
            <div class="small text-muted">{{ $request->approver?->email }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold text-muted">Processed At</label>
            <div class="h6">{{ $request->approved_at->format('d M Y H:i') }}</div>
          </div>
        </div>
        
        @if($request->rejection_reason)
          <div class="mt-3 pt-3 border-top">
            <label class="form-label small fw-bold text-muted">Rejection Reason</label>
            <div class="p-3 bg-light rounded">
              {{ $request->rejection_reason }}
            </div>
          </div>
        @endif
        
        @if($request->notes)
          <div class="mt-3 pt-3 border-top">
            <label class="form-label small fw-bold text-muted">Processing Notes</label>
            <div class="p-3 bg-light rounded">
              {{ $request->notes }}
            </div>
          </div>
        @endif
      </div>
    </div>
  @endif

  {{-- Action Buttons --}}
  @if($request->isPending())
    <div class="d-flex justify-content-between align-items-center">
      <div class="small text-muted">
        <i class="ri-information-line me-1"></i>
        Review the requested changes carefully before approving or rejecting this request.
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" onclick="approveRequest({{ $request->id }})">
          <i class="ri-check-line me-1"></i>Approve Request
        </button>
        <button type="button" class="btn btn-danger" onclick="rejectRequest({{ $request->id }})">
          <i class="ri-close-line me-1"></i>Reject Request
        </button>
      </div>
    </div>
  @endif
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('staff.officer.edit-requests.approve', $request->id) }}">
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
      <form method="POST" action="{{ route('staff.officer.edit-requests.reject', $request->id) }}">
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
  new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function rejectRequest(id) {
  new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
