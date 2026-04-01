@extends('layouts.portal')
@section('title', 'Full Accreditation Record')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Complete Accreditation Record</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Full accreditation record with all personal, professional, and administrative details.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
        <i class="ri-printer-line me-1"></i>Print Record
      </button>
      <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadDocuments({{ $journalist->id }})">
        <i class="ri-download-2-line me-1"></i>Download Documents
      </button>
      <button type="button" class="btn btn-outline-warning btn-sm" onclick="editRecord({{ $journalist->id }})">
        <i class="ri-edit-line me-1"></i>Edit Record
      </button>
      <button type="button" class="btn btn-white border shadow-sm btn-sm" onclick="window.close()">
        <i class="ri-close-line me-1"></i>Close
      </button>
    </div>
  </div>

  {{-- Header Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-primary text-white">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-award-line me-2"></i>
          Accreditation Certificate Information
        </h6>
        <div class="d-flex align-items-center gap-3">
          <span class="badge bg-light text-dark">
            <i class="ri-calendar-line me-1"></i>
            Year: {{ $journalist->year ?? optional($journalist->issued_at)->format('Y') }}
          </span>
          <span class="badge bg-light text-dark">
            <i class="ri-shield-check-line me-1"></i>
            Status: {{ ucfirst($journalist->status ?? 'active') }}
          </span>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Certificate Number</label>
          <div class="h5 fw-bold text-primary">{{ $journalist->certificate_no ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Issued Date</label>
          <div class="h5">{{ optional($journalist->issued_at)->format('d M Y') ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Expiry Date</label>
          <div class="h5">{{ optional($journalist->expires_at)->format('d M Y') ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Personal Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-user-line me-2"></i>
        Personal Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Full Name</label>
          <div class="h6">{{ $holder?->name ?? ($formData['first_name'] ?? '' . ' ' . $formData['surname'] ?? '') }}</div>
          @if($holder && $holder->email)
            <div class="text-muted small">{{ $holder->email }}</div>
          @endif
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Sex/Gender</label>
          <div class="h6">{{ ucfirst($formData['sex'] ?? $formData['gender'] ?? '—') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Marital Status</label>
          <div class="h6">{{ ucfirst($formData['marital_status'] ?? '—') }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Date of Birth</label>
          <div class="h6">{{ $formData['date_of_birth'] ? \Carbon\Carbon::parse($formData['date_of_birth'])->format('d M Y') : '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Place of Birth</label>
          <div class="h6">{{ $formData['place_of_birth'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Nationality</label>
          <div class="h6">{{ $formData['nationality'] ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Identification --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-id-card-line me-2"></i>
        Identification
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">ID Number</label>
          <div class="h6">{{ $formData['id_number'] ?? $formData['national_id'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Passport Photo</label>
          <div class="d-flex align-items-center">
            @if($journalist->application && $journalist->application->documents)
              @php
                $photo = $journalist->application->documents->where('doc_type', 'passport_photo')->first();
              @endphp
              @if($photo)
                <img src="{{ asset('storage/' . $photo->file_path) }}" 
                     alt="Passport Photo" class="rounded border" style="width: 80px; height: 100px; object-fit: cover;">
                <div class="ms-3">
                  <div class="small text-muted">Photo on file</div>
                  <div class="small text-muted">{{ $photo->original_filename }}</div>
                </div>
              @else
                <div class="text-muted">
                  <i class="ri-image-line" style="font-size: 48px;"></i>
                  <div class="small mt-2">No photo available</div>
                </div>
              @endif
            @else
              <div class="text-muted">
                <i class="ri-image-line" style="font-size: 48px;"></i>
                <div class="small mt-2">No photo available</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Professional Information --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-briefcase-line me-2"></i>
        Professional Information
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Organization</label>
          <div class="h6">{{ $formData['organization'] ?? $formData['employer'] ?? '—' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Category</label>
          <div class="h6">
            <span class="badge bg-primary">{{ $journalist->application?->categoryLabel() ?? '—' }}</span>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Medium</label>
          <div class="h6">{{ ucfirst($formData['medium'] ?? '—') }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Designation</label>
          <div class="h6">{{ $formData['designation'] ?? $formData['job_title'] ?? '—' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted">Collection Status</label>
          <div class="h6">
            @if($journalist->collected_at)
              <span class="badge bg-success">
                <i class="ri-check-double-line me-1"></i>
                Collected on {{ $journalist->collected_at->format('d M Y') }}
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
          <label class="form-label small fw-bold text-muted">Home Address</label>
          <div class="h6">{{ $formData['home_address'] ?? $formData['address'] ?? '—' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Town/City</label>
          <div class="h6">{{ $formData['town'] ?? $formData['city'] ?? '—' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Province</label>
          <div class="h6">{{ ucfirst($formData['province'] ?? '—') }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Phone</label>
          <div class="h6">{{ $holder?->phone ?? $formData['phone_number'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Mobile</label>
          <div class="h6">{{ $holder?->phone ?? $formData['cell_number'] ?? '—' }}</div>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted">Email</label>
          <div class="h6">{{ $holder?->email ?? '—' }}</div>
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
          <div class="h6">{{ $journalist->application->reference ?? '—' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Application Type</label>
          <div class="h6">{{ ucfirst($journalist->application->application_type ?? '—') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Request Type</label>
          <div class="h6">{{ ucfirst($journalist->application->request_type ?? '—') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Submitted Date</label>
          <div class="h6">{{ optional($journalist->application->submitted_at)->format('d M Y H:i') ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Documents List --}}
  @if($journalist->application && $journalist->application->documents->count() > 0)
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-file-list-3-line me-2"></i>
        Uploaded Documents ({{ $journalist->application->documents->count() }})
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        @foreach($journalist->application->documents as $document)
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
      This record was created on {{ optional($journalist->created_at)->format('d M Y H:i') }} and last updated on {{ optional($journalist->updated_at)->format('d M Y H:i') }}.
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary" onclick="window.print()">
        <i class="ri-printer-line me-1"></i>Print
      </button>
      <button type="button" class="btn btn-outline-success" onclick="downloadDocuments({{ $journalist->id }})">
        <i class="ri-download-2-line me-1"></i>Download All Documents
      </button>
      <button type="button" class="btn btn-outline-warning" onclick="editRecord({{ $journalist->id }})">
        <i class="ri-edit-line me-1"></i>Edit Record
      </button>
    </div>
  </div>
</div>

<script>
function downloadDocuments(id) {
  window.location.href = `/staff/officer/records/accredited-journalists/${id}/download-documents`;
}

function editRecord(id) {
  window.location.href = `/staff/officer/records/accredited-journalists/${id}/edit`;
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
