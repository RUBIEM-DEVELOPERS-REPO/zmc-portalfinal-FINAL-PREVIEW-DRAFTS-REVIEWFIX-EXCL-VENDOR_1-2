@extends('layouts.portal')
@section('title', 'Receipts Management')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Receipts Management</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Manage and track all payment receipts.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm px-3" onclick="exportReceipts()">
        <i class="ri-download-2-line me-1"></i>Export
      </button>
    </div>
  </div>

  {{-- Filters --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-filter-3-line me-2"></i>
          Filters
        </h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
          <i class="ri-refresh-line me-1"></i>Clear
        </button>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('receipts.index') }}">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small fw-bold">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All Status</option>
              <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Payment Method</label>
            <select name="payment_method" class="form-select form-select-sm">
              <option value="">All Methods</option>
              @foreach($paymentMethods as $method => $label)
                <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" 
                   value="{{ request('date_from') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" 
                   value="{{ request('date_to') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" 
                   value="{{ request('search') }}" placeholder="Receipt number, reference, or name...">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">&nbsp;</label>
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="ri-search-line me-1"></i>Search
            </button>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">&nbsp;</label>
            <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="clearFilters()">
              <i class="ri-refresh-line me-1"></i>Reset
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Receipts Table --}}
  <div class="zmc-card">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-file-list-3-line me-2"></i>
          Receipts List
        </h6>
        <div class="small text-muted">
          {{ $receipts->count() }} of {{ $receipts->total() }} results
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th>Receipt #</th>
            <th>Payment Reference</th>
            <th>Application</th>
            <th>Applicant</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($receipts as $receipt)
            <tr>
              <td class="fw-bold">{{ $receipt->receipt_number }}</td>
              <td>
                <span class="badge bg-info">{{ $receipt->payment_reference }}</span>
              </td>
              <td>{{ $receipt->application?->reference ?? '—' }}</td>
              <td>
                <div class="fw-semibold">{{ $receipt->applicant?->name ?? 'Unknown' }}</div>
                <div class="small text-muted">{{ $receipt->applicant?->email ?? '' }}</div>
              </td>
              <td>
                <span class="badge bg-primary">{{ $receipt->payment_method_label }}</span>
              </td>
              <td class="fw-bold text-success">${{ number_format($receipt->amount, 2) }}</td>
              <td>{{ $receipt->payment_date->format('d M Y H:i') }}</td>
              <td>
                <span class="badge bg-{{ $receipt->status_color }}">
                  {{ $receipt->status_label }}
                </span>
              </td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Actions
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('receipts.show', $receipt->id) }}">
                      <i class="ri-eye-line me-1"></i>View Details
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('receipts.view', $receipt->id) }}" target="_blank">
                      <i class="ri-file-text-line me-1"></i>View Receipt
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('receipts.download', $receipt->id) }}">
                      <i class="ri-download-line me-1"></i>Download PDF
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('receipts.qr-code', $receipt->id) }}" target="_blank">
                      <i class="ri-qr-code-line me-1"></i>View QR Code
                    </a></li>
                    @if($receipt->isPending())
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-success" href="#" onclick="verifyReceipt({{ $receipt->id }})">
                        <i class="ri-check-line me-1"></i>Verify Receipt
                      </a></li>
                      <li><a class="dropdown-item text-danger" href="#" onclick="cancelReceipt({{ $receipt->id }})">
                        <i class="ri-close-line me-1"></i>Cancel Receipt
                      </a></li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">
                <i class="ri-inbox-line me-2"></i>
                No receipts found matching the current filters.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-between align-items-center">
      <div class="small text-muted">
        Showing {{ $receipts->firstItem() }} to {{ $receipts->lastItem() }} of {{ $receipts->total() }} receipts
      </div>
      <div>{{ $receipts->links() }}</div>
    </div>
  </div>
</div>

<script>
function clearFilters() {
  window.location.href = '{{ route('receipts.index') }}';
}

function exportReceipts() {
  const params = new URLSearchParams(window.location.search);
  window.location.href = '{{ route('receipts.export') }}?' + params.toString();
}

function verifyReceipt(id) {
  if (confirm('Are you sure you want to verify this receipt?')) {
    fetch(`/staff/receipts/${id}/verify`, {
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

function cancelReceipt(id) {
  if (confirm('Are you sure you want to cancel this receipt? This action cannot be undone.')) {
    fetch(`/staff/receipts/${id}/cancel`, {
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
</script>
@endsection
