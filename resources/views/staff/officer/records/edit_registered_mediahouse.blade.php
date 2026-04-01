@extends('layouts.portal')
@section('title', 'Edit Registration Record')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Edit Registration Record</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Submit edit request for registrar approval. Changes will not be applied until approved.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm" onclick="window.close()">
        <i class="ri-close-line me-1"></i>Cancel
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <form method="POST" action="{{ route('staff.officer.records.registered-mediahouses.update', $mediahouse->id) }}">
    @csrf

    {{-- Edit Reason --}}
    <div class="zmc-card mb-4 border-warning">
      <div class="card-header bg-warning text-dark">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-alert-line me-2"></i>
          Edit Reason (Required)
        </h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-bold">Please explain why these changes are necessary:</label>
          <textarea name="edit_reason" class="form-control" rows="3" required
                    placeholder="Provide a detailed reason for the requested changes..."></textarea>
          <div class="form-text">This information will be reviewed by the registrar during the approval process.</div>
        </div>
      </div>
    </div>

    {{-- Current Information --}}
    <div class="zmc-card mb-4">
      <div class="card-header bg-light">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-information-line me-2"></i>
          Current Information
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Registration Number</label>
            <div class="h6 text-success">{{ $mediahouse->registration_no }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Entity Name</label>
            <div class="h6">{{ $formData['entity_name'] ?? $formData['company_name'] ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Business Type</label>
            <div class="h6">{{ ucfirst($formData['business_type'] ?? '—') }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Editable Fields --}}
    <div class="zmc-card mb-4">
      <div class="card-header bg-success text-white">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-edit-line me-2"></i>
          Editable Information
        </h6>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Trading Name</label>
            <input type="text" name="trading_name" class="form-control" 
                   value="{{ $formData['trading_name'] ?? $formData['trading_as'] ?? '' }}"
                   placeholder="Trading as name">
            <div class="form-text">Update trading name or "trading as" information</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Physical Address</label>
            <input type="text" name="physical_address" class="form-control" 
                   value="{{ $formData['physical_address'] ?? $formData['address'] ?? '' }}"
                   placeholder="Physical business address">
            <div class="form-text">Update physical business address</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Postal Address</label>
            <input type="text" name="postal_address" class="form-control" 
                   value="{{ $formData['postal_address'] ?? $formData['mailing_address'] ?? '' }}"
                   placeholder="Postal or mailing address">
            <div class="form-text">Update postal or mailing address</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Phone Number</label>
            <input type="tel" name="phone_number" class="form-control" 
                   value="{{ $contact?->phone ?? $formData['phone_number'] ?? '' }}"
                   placeholder="Primary phone number">
            <div class="form-text">Update primary phone number</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Mobile Number</label>
            <input type="tel" name="mobile_number" class="form-control" 
                   value="{{ $formData['mobile_number'] ?? '' }}"
                   placeholder="Mobile phone number">
            <div class="form-text">Update mobile phone number</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Email Address</label>
            <input type="email" name="email_address" class="form-control" 
                   value="{{ $contact?->email ?? $formData['email_address'] ?? '' }}"
                   placeholder="Email address">
            <div class="form-text">Update email address for communications</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Contact Person Name</label>
            <input type="text" name="contact_person_name" class="form-control" 
                   value="{{ $contact?->name ?? $formData['contact_person_name'] ?? '' }}"
                   placeholder="Contact person full name">
            <div class="form-text">Update contact person name</div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-bold">Contact Person Title</label>
            <select name="contact_person_title" class="form-select">
              <option value="">Select Title</option>
              <option value="mr" {{ ($formData['contact_person_title'] ?? '') === 'mr' ? 'selected' : '' }}>Mr.</option>
              <option value="mrs" {{ ($formData['contact_person_title'] ?? '') === 'mrs' ? 'selected' : '' }}>Mrs.</option>
              <option value="miss" {{ ($formData['contact_person_title'] ?? '') === 'miss' ? 'selected' : '' }}>Miss.</option>
              <option value="dr" {{ ($formData['contact_person_title'] ?? '') === 'dr' ? 'selected' : '' }}>Dr.</option>
              <option value="prof" {{ ($formData['contact_person_title'] ?? '') === 'prof' ? 'selected' : '' }}>Prof.</option>
              <option value="hon" {{ ($formData['contact_person_title'] ?? '') === 'hon' ? 'selected' : '' }}>Hon.</option>
            </select>
            <div class="form-text">Update contact person title</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Additional Notes --}}
    <div class="zmc-card mb-4">
      <div class="card-header bg-light">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-file-text-line me-2"></i>
          Additional Notes (Optional)
        </h6>
      </div>
      <div class="card-body">
        <textarea name="additional_notes" class="form-control" rows="3"
                  placeholder="Any additional information or context for the registrar..."></textarea>
        <div class="form-text">Provide any additional context that might help the registrar understand the changes.</div>
      </div>
    </div>

    {{-- Submit Button --}}
    <div class="d-flex justify-content-between align-items-center">
      <div class="small text-muted">
        <i class="ri-information-line me-1"></i>
        All edit requests are subject to registrar approval before changes are applied to the record.
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-white border shadow-sm" onclick="window.close()">
          <i class="ri-close-line me-1"></i>Cancel
        </button>
        <button type="submit" class="btn btn-warning">
          <i class="ri-send-plane-line me-1"></i>Submit Edit Request
        </button>
      </div>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Add confirmation before submission
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    const editReason = document.querySelector('textarea[name="edit_reason"]').value;
    if (!editReason || editReason.trim().length < 10) {
      e.preventDefault();
      alert('Please provide a detailed edit reason (minimum 10 characters).');
      return false;
    }
    
    if (!confirm('Are you sure you want to submit this edit request for registrar approval?')) {
      e.preventDefault();
      return false;
    }
  });
});
</script>
@endsection
