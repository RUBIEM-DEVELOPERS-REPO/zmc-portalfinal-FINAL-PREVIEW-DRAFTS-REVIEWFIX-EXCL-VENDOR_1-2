@extends('layouts.portal')
@section('title', 'Full Registration Record')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Complete Registration Record</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Full registration record with all business, operational, and administrative details.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
        <i class="ri-printer-line me-1"></i>Print Record
      </button>
      <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadDocuments({{ $mediahouse->id }})">
        <i class="ri-download-2-line me-1"></i>Download Documents
      </button>
      <button type="button" class="btn btn-outline-warning btn-sm" onclick="editRecord({{ $mediahouse->id }})">
        <i class="ri-edit-line me-1"></i>Edit Record
      </button>
      <button type="button" class="btn btn-white border shadow-sm btn-sm" onclick="window.close()">
        <i class="ri-close-line me-1"></i>Close
      </button>
    </div>
  </div>

  {{-- Header Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-success text-white">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-building-line me-2"></i>
          Registration Certificate Information
        </h6>
        <div class="d-flex align-items-center gap-3">
          <span class="badge bg-light text-dark">
            <i class="ri-calendar-line me-1"></i>
            Year: {{ $mediahouse->year ?? optional($mediahouse->issued_at)->format('Y') }}
          </span>
          <span class="badge bg-light text-dark">
            <i class="ri-shield-check-line me-1"></i>
            Status: {{ ucfirst($mediahouse->status ?? 'active') }}
          </span>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Registration Number</label>
          <div class="h5 fw-bold text-success">{{ $mediahouse->registration_no ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Issued Date</label>
          <div class="h5">{{ optional($mediahouse->issued_at)->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Expiry Date</label>
          <div class="h5">{{ optional($mediahouse->expires_at)->format('d M Y') ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Business Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-briefcase-line me-2"></i>
        Business Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Entity Name</label>
          <div class="h6">{{ $formData['entity_name'] ?? $formData['company_name'] ?? '—' }}</div>
          @if($contact && $contact->email)
            <div class="text-muted small">{{ $contact->email }}</div>
          @endif
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Trading Name</label>
          <div class="h6">{{ $formData['trading_name'] ?? $formData['trading_as'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Business Type</label>
          <div class="h6">
            <span class="badge bg-info">{{ ucfirst($formData['business_type'] ?? '—') }}</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Media Type</label>
          <div class="h6">{{ ucfirst($formData['media_type'] ?? '—') }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Ownership Type</label>
          <div class="h6">
            <span class="badge bg-{{ $formData['ownership_type'] === 'foreign' ? 'warning' : 'primary' }}">
              {{ ucfirst($formData['ownership_type'] ?? '—') }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Business Registration --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-list-3-line me-2"></i>
        Business Registration Details
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Business Registration Number</label>
          <div class="h6">{{ $formData['business_registration'] ?? $formData['company_registration'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Tax/BVR Number</label>
          <div class="h6">{{ $formData['tax_number'] ?? $formData['bvr_number'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Shareholding Percentage</label>
          <div class="h6">{{ $formData['shareholding_percentage'] ?? $formData['local_ownership'] ?? '—' }}%</div>
          @if($formData['ownership_type'] === 'foreign')
            <div class="small text-muted">Local ownership percentage</div>
          @endif
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Collection Status</label>
          <div class="h6">
            @if($mediahouse->collected_at)
              <span class="badge bg-success">
                <i class="ri-check-double-line me-1"></i>
                Collected on {{ $mediahouse->collected_at->format('d M Y') }}
              </span>
            @else
              <span class="badge bg-warning">
                <i class="ri-time-line me-1"></i>
                Not Collected
              </span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Contact Person --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-user-line me-2"></i>
        Contact Person Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Title</label>
          <div class="h6">{{ $formData['contact_person_title'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Full Name</label>
          <div class="h6">{{ $contact?->name ?? $formData['contact_person_name'] ?? '—' }}</div>
          @if($formData['contact_person_surname'])
            <div class="small text-muted">{{ $formData['contact_person_surname'] }}</div>
          @endif
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">ID Number</label>
          <div class="h6">{{ $formData['contact_person_id_number'] ?? $contact?->id_number ?? '—' }}</div>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted">Email</label>
          <div class="h6">{{ $contact?->email ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Contact Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-phone-line me-2"></i>
        Contact Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Physical Address</label>
          <div class="h6">{{ $formData['physical_address'] ?? $formData['address'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Postal Address</label>
          <div class="h6">{{ $formData['postal_address'] ?? $formData['mailing_address'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Phone</label>
          <div class="h6">{{ $contact?->phone ?? $formData['phone_number'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Mobile</label>
          <div class="h6">{{ $contact?->phone ?? $formData['mobile_number'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Email</label>
          <div class="h6">{{ $contact?->email ?? $formData['email_address'] ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Application Details --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-text-line me-2"></i>
        Application Details
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Application Reference</label>
          <div class="h6">{{ $mediahouse->application->reference ?? '—' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Application Type</label>
          <div class="h6">{{ ucfirst($mediahouse->application->application_type ?? '—') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Request Type</label>
          <div class="h6">{{ ucfirst($mediahouse->application->request_type ?? '—') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Submitted Date</label>
          <div class="h6">{{ optional($mediahouse->application->submitted_at)->format('d M Y H:i') ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Documents List --}}
  @if($mediahouse->application && $mediahouse->application->documents->count() > 0)
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-list-3-line me-2"></i>
        Uploaded Documents ({{ $mediahouse->application->documents->count() }})
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        @foreach($mediahouse->application->documents as $document)
          <div class="col-md-6">
            <div class="border rounded p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="fw-semibold">{{ $document->doc_type_label }}</div>
                  <div class="small text-muted">{{ $document->original_filename }}</div>
                  <div class="small text-muted">{{ $document->file_size_formatted }}</div>
                </div>
                <a href="{{ asset('storage/' . $document->file_path) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-primary">
                  <i class="ri-download-line"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  {{-- Action Buttons --}}
  <div class="d-flex justify-content-between align-items-center">
    <div class="small text-muted">
      <i class="ri-information-line me-1"></i>
      This record was created on {{ optional($mediahouse->created_at)->format('d M Y H:i') }} and last updated on {{ optional($mediahouse->updated_at)->format('d M Y H:i') }}.
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary" onclick="window.print()">
        <i class="ri-printer-line me-1"></i>Print
      </button>
      <button type="button" class="btn btn-outline-success" onclick="downloadDocuments({{ $mediahouse->id }})">
        <i class="ri-download-2-line me-1"></i>Download All Documents
      </button>
      <button type="button" class="btn btn-outline-warning" onclick="editRecord({{ $mediahouse->id }})">
        <i class="ri-edit-line me-1"></i>Edit Record
      </button>
    </div>
  </div>
</div>

<script>
function downloadDocuments(id) {
  window.location.href = `/staff/officer/records/registered-mediahouses/${id}/download-documents`;
}

function editRecord(id) {
  window.location.href = `/staff/officer/records/registered-mediahouses/${id}/edit`;
}

function window.print() {
  window.print();
}
</script>

@push('styles')
<style>
@media print {
  .no-print {
    display: none !important;
  }
  
  .zmc-card {
    break-inside: avoid;
    page-break-inside: avoid;
  }
  
  body {
    font-size: 12px;
  }
}
</style>
@endpush
@endsection
