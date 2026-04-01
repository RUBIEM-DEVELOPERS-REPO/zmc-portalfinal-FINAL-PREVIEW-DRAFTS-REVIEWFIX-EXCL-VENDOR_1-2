@extends('layouts.portal')
@section('title', 'Applications - Registrar')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Applications Management</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Review all applications and provide guidance on complex cases.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm px-3" onclick="exportApplications()">
        <i class="ri-download-2-line me-1"></i>Export
      </button>
    </div>
  </div>

  {{-- Status Tabs --}}
  <div class="zmc-card mb-4">
    <div class="card-body">
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('registrar.applications') }}" 
           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
          <i class="ri-list-check me-1"></i>All Applications
          @php $totalCount = Application::count(); @endphp
          <span class="badge bg-light text-primary ms-1">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('registrar.applications', ['status' => 'awaiting_review']) }}" 
           class="btn {{ request('status') == 'awaiting_review' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm">
          <i class="ri-eye-line me-1"></i>Awaiting Review
          @php $awaitingCount = Application::where('registrar_reviewed', false)->where('status', Application::OFFICER_APPROVED)->count(); @endphp
          <span class="badge bg-light text-warning ms-1">{{ $awaitingCount }}</span>
        </a>
        <a href="{{ route('registrar.applications', ['status' => 'forwarded']) }}" 
           class="btn {{ request('status') == 'forwarded' ? 'btn-danger' : 'btn-outline-danger' }} btn-sm">
          <i class="ri-user-search-line me-1"></i>Complex Applications
          @php $forwardedCount = Application::where('status', Application::FORWARDED_TO_REGISTRAR)->count(); @endphp
          <span class="badge bg-light text-danger ms-1">{{ $forwardedCount }}</span>
        </a>
        <a href="{{ route('registrar.applications', ['status' => 'returned']) }}" 
           class="btn {{ request('status') == 'returned' ? 'btn-info' : 'btn-outline-info' }} btn-sm">
          <i class="ri-arrow-go-back-line me-1"></i>Returned for Correction
          @php $returnedCount = Application::where('status', Application::CORRECTION_REQUESTED)->count(); @endphp
          <span class="badge bg-light text-info ms-1">{{ $returnedCount }}</span>
        </a>
      </div>
    </div>
  </div>

  {{-- Advanced Filters --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-filter-3-line me-2"></i>
          Advanced Filters
        </h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
          <i class="ri-refresh-line me-1"></i>Clear Filters
        </button>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('registrar.applications') }}">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small fw-bold">Application Type</label>
            <select name="application_type" class="form-select form-select-sm">
              <option value="">All Types</option>
              <option value="accreditation" {{ request('application_type') == 'accreditation' ? 'selected' : '' }}>Accreditation</option>
              <option value="registration" {{ request('application_type') == 'registration' ? 'selected' : '' }}>Registration</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Request Type</label>
            <select name="request_type" class="form-select form-select-sm">
              <option value="">All Requests</option>
              <option value="new" {{ request('request_type') == 'new' ? 'selected' : '' }}>New Application</option>
              <option value="renewal" {{ request('request_type') == 'renewal' ? 'selected' : '' }}>Renewal</option>
              <option value="replacement" {{ request('request_type') == 'replacement' ? 'selected' : '' }}>Replacement</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Applicant Name/Email</label>
            <input type="text" name="search" class="form-control form-control-sm" 
                   value="{{ request('search') }}" placeholder="Search...">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Reference Number</label>
            <input type="text" name="reference" class="form-control form-control-sm" 
                   value="{{ request('reference') }}" placeholder="Reference...">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Date Range</label>
            <input type="date" name="date_from" class="form-control form-control-sm" 
                   value="{{ request('date_from') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">&nbsp;</label>
            <input type="date" name="date_to" class="form-control form-control-sm" 
                   value="{{ request('date_to') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Payment Status</label>
            <select name="payment_status" class="form-select form-select-sm">
              <option value="">All Status</option>
              <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">&nbsp;</label>
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="ri-search-line me-1"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Applications Table --}}
  <div class="zmc-card">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-file-list-3-line me-2"></i>
          Applications List
        </h6>
        <div class="d-flex align-items-center gap-2">
          <span class="small text-muted">{{ $applications->count() }} of {{ $applications->total() }} results</span>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            <label class="form-check-label small" for="selectAll">Select All</label>
          </div>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th width="40">
              <input type="checkbox" class="form-check-input" id="selectAllHeader" onchange="toggleSelectAll()">
            </th>
            <th>Reference</th>
            <th>Applicant</th>
            <th>Type</th>
            <th>Request</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Submitted</th>
            <th>Registrar Review</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $application)
            <tr>
              <td>
                <input type="checkbox" class="form-check-input application-checkbox" value="{{ $application->id }}"
                       onchange="updateBulkActions()">
              </td>
              <td class="fw-bold">{{ $application->reference }}</td>
              <td>
                <div class="fw-semibold">{{ $application->applicant?->name ?? 'Unknown' }}</div>
                <div class="small text-muted">{{ $application->applicant?->email ?? '' }}</div>
              </td>
              <td>
                <span class="badge bg-{{ $application->application_type === 'accreditation' ? 'primary' : 'success' }}">
                  {{ ucfirst($application->application_type) }}
                </span>
              </td>
              <td>{{ ucfirst($application->request_type) }}</td>
              <td>
                <span class="badge bg-{{ $application->statusColor() }}">
                  {{ $application->statusLabel() }}
                </span>
              </td>
              <td>
                @if($application->payment_status === 'paid')
                  <span class="badge bg-success">Paid</span>
                @else
                  <span class="badge bg-warning">Unpaid</span>
                @endif
              </td>
              <td>
                <div>{{ $application->submitted_at->format('d M Y') }}</div>
                <div class="small text-muted">{{ $application->submitted_at->format('H:i') }}</div>
              </td>
              <td>
                @if($application->registrar_reviewed)
                  <span class="badge bg-success">
                    <i class="ri-check-double-line me-1"></i>Reviewed
                  </span>
                @else
                  <span class="badge bg-warning">
                    <i class="ri-time-line me-1"></i>Pending
                  </span>
                @endif
              </td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Actions
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('staff.applications.details', $application->id) }}">
                      <i class="ri-eye-line me-1"></i>View Application
                    </a></li>
                    
                    @if($application->status === Application::FORWARDED_TO_REGISTRAR)
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-warning" href="#" onclick="sendGuidance({{ $application->id }})">
                        <i class="ri-user-search-line me-1"></i>Send Guidance
                      </a></li>
                    @endif
                    
                    @if(!$application->registrar_reviewed && $application->status === Application::OFFICER_APPROVED)
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-success" href="#" onclick="markAsReviewed({{ $application->id }})">
                        <i class="ri-check-line me-1"></i>Mark as Reviewed
                      </a></li>
                      <li><a class="dropdown-item text-danger" href="#" onclick="flagAnomaly({{ $application->id }})">
                        <i class="ri-flag-line me-1"></i>Flag Anomaly
                      </a></li>
                    @endif
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="sendMessage({{ $application->id }})">
                      <i class="ri-message-line me-1"></i>Send Message to Officer
                    </a></li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center py-4 text-muted">
                <i class="ri-inbox-line me-2"></i>
                No applications found matching the current filters.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-between align-items-center">
      <div class="small text-muted">
        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} applications
      </div>
      <div>{{ $applications->links() }}</div>
    </div>
  </div>
</div>

{{-- Bulk Actions --}}
<div class="zmc-card mt-4" id="bulkActionsCard" style="display: none;">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <span class="fw-bold" id="selectedCount">0</span> applications selected
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-sm" onclick="bulkMarkAsReviewed()">
          <i class="ri-check-double-line me-1"></i>Mark as Reviewed
        </button>
        <button type="button" class="btn btn-danger btn-sm" onclick="bulkFlagAnomaly()">
          <i class="ri-flag-line me-1"></i>Flag Anomaly
        </button>
        <button type="button" class="btn btn-info btn-sm" onclick="bulkSendMessage()">
          <i class="ri-message-line me-1"></i>Send Message
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modals --}}
<div class="modal fade" id="guidanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="#" id="guidanceForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Send Guidance to Accreditation Officer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Guidance Note</label>
            <textarea name="guidance_note" class="form-control" rows="4" required
                      placeholder="Provide detailed guidance for this complex application..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-send-plane-line me-1"></i>Send Guidance
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="anomalyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="#" id="anomalyForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Flag Application Anomaly</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Anomaly Description</label>
            <textarea name="anomaly_description" class="form-control" rows="4" required
                      placeholder="Describe the anomaly found in this application..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">
            <i class="ri-flag-line me-1"></i>Flag Anomaly
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleSelectAll() {
  const selectAll = document.getElementById('selectAllHeader');
  const checkboxes = document.querySelectorAll('.application-checkbox');
  
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAll.checked;
  });
  
  updateBulkActions();
}

function updateBulkActions() {
  const checkboxes = document.querySelectorAll('.application-checkbox:checked');
  const bulkActionsCard = document.getElementById('bulkActionsCard');
  const selectedCount = document.getElementById('selectedCount');
  
  if (checkboxes.length > 0) {
    bulkActionsCard.style.display = 'block';
    selectedCount.textContent = checkboxes.length;
  } else {
    bulkActionsCard.style.display = 'none';
  }
}

function clearFilters() {
  window.location.href = '{{ route('registrar.applications') }}';
}

function exportApplications() {
  const params = new URLSearchParams(window.location.search);
  window.location.href = '{{ route('registrar.applications.export') }}?' + params.toString();
}

function markAsReviewed(id) {
  if (confirm('Mark this application as reviewed?')) {
    fetch(`/staff/registrar/applications/${id}/mark-reviewed`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      });
  }
}

function flagAnomaly(id) {
  const form = document.getElementById('anomalyForm');
  form.action = `/staff/registrar/applications/${id}/flag-anomaly`;
  new bootstrap.Modal(document.getElementById('anomalyModal')).show();
}

function sendGuidance(id) {
  const form = document.getElementById('guidanceForm');
  form.action = `/staff/registrar/applications/${id}/send-guidance`;
  new bootstrap.Modal(document.getElementById('guidanceModal')).show();
}

function sendMessage(id) {
  const message = prompt('Enter message to send to Accreditation Officer:');
  if (message) {
    fetch(`/staff/registrar/applications/${id}/send-message`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ message: message })
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Message sent successfully');
        } else {
          alert('Error: ' + data.message);
        }
      });
  }
}

function bulkMarkAsReviewed() {
  const selectedIds = Array.from(document.querySelectorAll('.application-checkbox:checked')).map(cb => cb.value);
  if (confirm(`Mark ${selectedIds.length} applications as reviewed?`)) {
    // Implement bulk action
  }
}

function bulkFlagAnomaly() {
  const selectedIds = Array.from(document.querySelectorAll('.application-checkbox:checked')).map(cb => cb.value);
  // Implement bulk action
}

function bulkSendMessage() {
  const selectedIds = Array.from(document.querySelectorAll('.application-checkbox:checked')).map(cb => cb.value);
  // Implement bulk action
}
</script>
@endsection
