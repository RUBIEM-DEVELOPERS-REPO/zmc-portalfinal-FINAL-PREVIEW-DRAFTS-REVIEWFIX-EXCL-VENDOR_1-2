@extends('layouts.portal')

@section('title', 'Confirm Your Information')

@section('content')
<div id="renewals-confirm-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Confirm Your Information</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.renewals.index') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Renewals
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Confirm Changes</h1>
      <p>Review your information and indicate if any changes are needed</p>
    </div>

    <div class="form-steps-container">
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Select Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Number Lookup</div>
          </div>
          <div class="step active" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Confirm Changes</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Payment</div>
          </div>
        </div>
      </div>

      <div class="step-content active">
        <h3 class="step-title">Current Record Information</h3>
        
        @if($renewal->originalApplication)
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="zmc-lbl">Accreditation Number</div>
                  <div class="fw-semibold">{{ $renewal->original_number }}</div>
                </div>
                <div class="col-md-6">
                  <div class="zmc-lbl">Type</div>
                  <div class="fw-semibold">{{ ucfirst($renewal->originalApplication->application_type) }}</div>
                </div>
                <div class="col-md-6">
                  <div class="zmc-lbl">Full Name</div>
                  <div class="fw-semibold">{{ $renewal->originalApplication->applicant->name }}</div>
                </div>
                <div class="col-md-6">
                  <div class="zmc-lbl">Email Address</div>
                  <div class="fw-semibold">{{ $renewal->originalApplication->applicant->email }}</div>
                </div>
              </div>
            </div>
          </div>
        @endif

        <h3 class="step-title mt-4">Are there any changes to your information?</h3>
        <div class="current-step-info mb-3">
          <i class="ri-information-line me-2"></i>
          Choose one option below to proceed with your renewal.
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <form method="POST" action="{{ route('accreditation.renewals.confirm-no-changes', $renewal) }}">
              @csrf
              <button type="submit" class="w-100 btn btn-success" style="height:auto; padding:16px; text-align:left;">
                <div class="d-flex align-items-start">
                  <i class="ri-checkbox-circle-line me-3" style="font-size:24px;"></i>
                  <div>
                    <div class="fw-bold mb-1">No Changes</div>
                    <div style="font-size:12px; opacity:0.9;">All information is correct as shown above</div>
                  </div>
                </div>
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <button type="button" onclick="showChangesForm()" class="w-100 btn btn-warning" style="height:auto; padding:16px; text-align:left;">
              <div class="d-flex align-items-start">
                <i class="ri-edit-line me-3" style="font-size:24px;"></i>
                <div>
                  <div class="fw-bold mb-1">There Are Changes</div>
                  <div style="font-size:12px; opacity:0.9;">I need to update some information</div>
                </div>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Changes Form (Hidden by default) -->
      <div id="changesFormContainer" class="step-content" style="display:none;">
        <h3 class="step-title">Specify Changes</h3>
        <div class="current-step-info mb-3">
          <i class="ri-information-line me-2"></i>
          List all fields that need to be updated with supporting documents if required.
        </div>

        <form method="POST" action="{{ route('accreditation.renewals.submit-changes', $renewal) }}" enctype="multipart/form-data">
          @csrf
          
          <div id="changesContainer">
            <div class="card shadow-sm mb-3 change-item">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Field Name</label>
                    <input type="text" name="changes[0][field_name]" class="form-control zmc-input" placeholder="e.g., Phone Number" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Old Value</label>
                    <input type="text" name="changes[0][old_value]" class="form-control zmc-input" placeholder="Current value" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">New Value</label>
                    <input type="text" name="changes[0][new_value]" class="form-control zmc-input" placeholder="New value" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Supporting Document (Optional)</label>
                    <input type="file" name="changes[0][supporting_document]" class="form-control zmc-input" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="text-muted mt-1" style="font-size:11px;">PDF, JPG, or PNG (max 5MB)</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="button" onclick="addChange()" class="btn btn-sm btn-outline-primary mb-3">
            <i class="ri-add-line me-1"></i>Add Another Change
          </button>

          <div class="form-buttons">
            <button type="button" onclick="hideChangesForm()" class="btn btn-secondary">
              <i class="ri-arrow-left-line"></i> Back
            </button>
            <button type="submit" class="btn btn-primary">
              Submit Changes <i class="ri-arrow-right-line"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let changeIndex = 1;

function showChangesForm() {
  document.querySelector('.step-content.active').style.display = 'none';
  document.getElementById('changesFormContainer').style.display = 'block';
}

function hideChangesForm() {
  document.getElementById('changesFormContainer').style.display = 'none';
  document.querySelector('.step-content.active').style.display = 'block';
}

function addChange() {
  const container = document.getElementById('changesContainer');
  const newChange = `
    <div class="card shadow-sm mb-3 change-item">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label required">Field Name</label>
            <input type="text" name="changes[${changeIndex}][field_name]" class="form-control zmc-input" placeholder="e.g., Phone Number" required>
          </div>
          <div class="col-md-4">
            <label class="form-label required">Old Value</label>
            <input type="text" name="changes[${changeIndex}][old_value]" class="form-control zmc-input" placeholder="Current value" required>
          </div>
          <div class="col-md-4">
            <label class="form-label required">New Value</label>
            <input type="text" name="changes[${changeIndex}][new_value]" class="form-control zmc-input" placeholder="New value" required>
          </div>
          <div class="col-12">
            <label class="form-label">Supporting Document (Optional)</label>
            <input type="file" name="changes[${changeIndex}][supporting_document]" class="form-control zmc-input" accept=".pdf,.jpg,.jpeg,.png">
            <div class="text-muted mt-1" style="font-size:11px;">PDF, JPG, or PNG (max 5MB)</div>
          </div>
        </div>
      </div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', newChange);
  changeIndex++;
}
</script>
@endpush
@endsection
