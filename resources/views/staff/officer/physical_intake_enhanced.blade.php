@extends('layouts.portal')
@section('title', 'Physical Intake')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Physical Intake</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Record walk-in applications, renewals, and link to existing records for card generation.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-dashboard-3-line me-1"></i>Back to Dashboard
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Intake Type Selection --}}
  <div class="zmc-card shadow-sm mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0"><i class="ri-user-add-line me-2"></i>Intake Type</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label small fw-bold">Application Type <span class="text-danger">*</span></label>
          <select name="application_type" id="application_type" class="form-select" required>
            <option value="">-- Select Application Type --</option>
            <option value="accreditation">Accreditation (Media Practitioner)</option>
            <option value="registration">Registration (Media House)</option>
          </select>
        </div>
        
        <div class="col-md-12">
          <label class="form-label small fw-bold">Request Type <span class="text-danger">*</span></label>
          <select name="request_type" id="request_type" class="form-select" required>
            <option value="">-- Select Request Type --</option>
            <option value="new">New Application</option>
            <option value="renewal">Renewal</option>
            <option value="replacement">Replacement</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- New Application Form --}}
  <div id="newApplicationForm" class="card shadow-sm mb-4" style="display: none;">
    <div class="card-header bg-primary text-white">
      <h6 class="fw-bold m-0"><i class="ri-user-add-line me-2"></i>New Application Details</h6>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="ri-information-line me-2"></i>
        <strong>New Application:</strong> Complete applicant details for walk-in applications. Accreditation number will be auto-generated after submission.
      </div>
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
          <input type="text" name="first_name" class="form-control" placeholder="First name">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Surname <span class="text-danger">*</span></label>
          <input type="text" name="surname" class="form-control" placeholder="Surname">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">ID Number <span class="text-danger">*</span></label>
          <input type="text" name="id_number" class="form-control" placeholder="National ID number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
          <select name="category" class="form-select">
            <option value="">-- Select Category --</option>
            @if(request('application_type') === 'accreditation')
              <option value="journalist">Journalist</option>
              <option value="photographer">Photographer</option>
              <option value="camera_operator">Camera Operator</option>
              <option value="editor">Editor</option>
              <option value="producer">Producer</option>
              <option value="presenter">Presenter</option>
              <option value="correspondent">Correspondent</option>
              <option value="researcher">Researcher</option>
              <option value="other_media_practitioner">Other Media Practitioner</option>
            @else
              <option value="newspaper">Newspaper</option>
              <option value="magazine">Magazine</option>
              <option value="digital">Digital Platform</option>
              <option value="newsletter">Newsletter</option>
              <option value="agency">News Agency</option>
              <option value="production">Production House</option>
              <option value="advertising">Advertising</option>
              <option value="broadcasting">Broadcasting</option>
              <option value="other_media_house">Other Media House</option>
            @endif
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Receipt Number <span class="text-danger">*</span></label>
          <input type="text" name="receipt_number" class="form-control" placeholder="Receipt number" required>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="Email address">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label>
          <input type="tel" name="phone" class="form-control" placeholder="Phone number">
        </div>
        
        <div class="col-12">
          <label class="form-label small fw-bold">Physical Address <span class="text-danger">*</span></label>
          <input type="text" name="physical_address" class="form-control" placeholder="Full physical address">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">City/Town <span class="text-danger">*</span></label>
          <input type="text" name="city" class="form-control" placeholder="City or town">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Province <span class="text-danger">*</span></label>
          <select name="province" class="form-select">
            <option value="">-- Select Province --</option>
            <option value="harare">Harare</option>
            <option value="bulawayo">Bulawayo</option>
            <option value="manicaland">Manicaland</option>
            <option value="mashonaland_central">Mashonaland Central</option>
            <option value="mashonaland_east">Mashonaland East</option>
            <option value="mashonaland_west">Mashonaland West</option>
            <option value="masvingo">Masvingo</option>
            <option value="matabeleland_north">Matabeleland North</option>
            <option value="matabeleland_south">Matabeleland South</option>
            <option value="midlands">Midlands</option>
          </select>
        </div>
        
        <div class="col-12">
          <label class="form-label small fw-bold">Applicant Photo <span class="text-danger">*</span></label>
          <div class="row g-3">
            <div class="col-md-8">
              <div class="d-flex gap-3">
                <div class="flex-grow-1">
                  <input type="file" name="applicant_photo" id="applicant_photo" class="form-control" accept="image/*" onchange="previewPhoto(this)">
                  <div class="form-text">Upload applicant photo (JPG, PNG - Max 5MB)</div>
                </div>
                <div class="text-center">
                  <img id="photo_preview" src="{{ asset('images/default-avatar.png') }}" 
                       alt="Photo Preview" class="rounded" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #dee2e6;">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <button type="button" class="btn btn-outline-primary w-100" onclick="openCamera()">
                <i class="ri-camera-line me-2"></i>Take Photo
              </button>
              <div class="form-text small">Use device camera to capture photo</div>
            </div>
          </div>
        </div>
        
        <div class="col-12">
          <div class="alert alert-warning">
            <i class="ri-information-line me-2"></i>
            <strong>Note:</strong> Accreditation number will be automatically generated after submission. You can edit the application later to add more details to match the full application form.
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Existing Application Form --}}
  <div id="existingApplicationForm" class="card shadow-sm mb-4" style="display: none;">
    <div class="card-header bg-success text-white">
      <h6 class="fw-bold m-0"><i class="ri-refresh-line me-2"></i>Existing Application Details</h6>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="ri-information-line me-2"></i>
        <strong>Renewal/Replacement:</strong> Link to existing application record using accreditation/registration number.
      </div>
      
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label small fw-bold">
            <span id="lookup_label">Accreditation / Registration Number</span> 
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <input type="text" name="lookup_number" id="lookup_number" class="form-control" 
                   placeholder="e.g. J12345678E or MC00001234">
            <button type="button" class="btn btn-outline-secondary" onclick="lookupApplication()">
              <i class="ri-search-line"></i> Lookup
            </button>
          </div>
          <div class="small text-muted mt-1">
            Enter the existing accreditation/registration number for renewal or replacement
          </div>
        </div>
        
        <div id="existing_applicant_info" class="mt-3" style="display: none;">
          <div class="alert alert-success">
            <i class="ri-user-line me-2"></i>
            <strong>Applicant Found:</strong> <span id="found_applicant_name"></span>
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Applicant Name (Optional)</label>
          <input type="text" name="applicant_name" class="form-control" 
                 placeholder="Confirm applicant name" id="existing_applicant_name">
        </div>
      </div>
    </div>
  </div>

  {{-- Payment Information --}}
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
      <h6 class="fw-bold m-0"><i class="ri-money-dollar-circle-line me-2"></i>Payment Information</h6>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="ri-information-line me-2"></i>
        <strong>Receipt Number:</strong> Required for payments already made at Accounts. Leave blank if no payment yet.
      </div>
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold">Receipt Number <span class="text-danger">*</span></label>
          <input type="text" name="receipt_number" class="form-control" required 
                 placeholder="Enter receipt number">
          <div class="small text-muted mt-1">
            Official receipt number for payment verification
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Payment Method</label>
          <select name="payment_method" class="form-select">
            <option value="cash">Cash</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cheque">Cheque</option>
            <option value="paynow">PayNow</option>
            <option value="pop">Proof of Payment</option>
            <option value="waiver">Waiver</option>
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Amount Paid</label>
          <input type="number" name="amount_paid" class="form-control" step="0.01" 
                 placeholder="Amount paid">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Payment Date</label>
          <input type="date" name="payment_date" class="form-control" 
                 value="{{ now()->format('Y-m-d') }}">
        </div>
      </div>
    </div>
  </div>

  {{-- Card Generation Fields --}}
  <div id="card_fields" class="card shadow-sm mb-4" style="display: none;">
    <div class="card-header bg-info text-white">
      <h6 class="fw-bold m-0"><i class="ri-id-card-line me-2"></i>Card Generation Information</h6>
    </div>
    <div class="card-body">
      <div class="alert alert-warning">
        <i class="ri-information-line me-2"></i>
        <strong>Important:</strong> Fields below will be used for card generation. 
        For accreditation: photo, expiry date, and card type. 
        For registration: trading name, address, and contact person.
      </div>
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold">Card Type</label>
          <select name="card_type" class="form-select">
            <option value="standard">Standard Card</option>
            <option value="premium">Premium Card</option>
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Photo Required</label>
          <select name="photo_required" class="form-select">
            <option value="yes">Yes - Use existing photo</option>
            <option value="no">No - Take new photo</option>
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Expiry Date</label>
          <input type="date" name="expiry_date" class="form-control" 
                 placeholder="Card expiry date">
        </div>
      </div>
      
      @if(request('application_type') === 'registration')
      <div class="row g-3 mt-3">
        <div class="col-md-12">
          <label class="form-label small fw-bold">Trading Name</label>
          <input type="text" name="trading_name" class="form-control" 
                 placeholder="Official trading name">
        </div>
        
        <div class="col-md-12">
          <label class="form-label small fw-bold">Business Address</label>
          <input type="text" name="business_address" class="form-control" 
                 placeholder="Business physical address">
        </div>
        
        <div class="col-md-12">
          <label class="form-label small fw-bold">Contact Person</label>
          <input type="text" name="contact_person" class="form-control" 
                 placeholder="Authorized contact person">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Contact Phone</label>
          <input type="tel" name="contact_phone" class="form-control" 
                 placeholder="Contact phone number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Contact Email</label>
          <input type="email" name="contact_email" class="form-control" 
                 placeholder="Contact email address">
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Additional Information --}}
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0"><i class="ri-file-text-line me-2"></i>Additional Information</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label small fw-bold">Notes</label>
          <textarea name="notes" class="form-control" rows="4" 
                    placeholder="Any additional notes or special instructions..."></textarea>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Journalist / Practitioner Category <span class="text-danger">*</span></label>
          <select name="applicant_category" class="form-select" required>
            <option value="" disabled selected>-- Select Category --</option>
            <option value="JE">JE — Full-time employed media practitioner</option>
            <option value="JF">JF — Freelance journalist (local)</option>
            <option value="JO">JO — Local journalist running office for foreign media</option>
            <option value="JS">JS — Local journalist stringing for foreign media</option>
            <option value="JM">JM — Journalist reporting locally and abroad</option>
            <option value="JP">JP — Content creator / photographer / PR / digital media</option>
            <option value="JD">JD — Digital social media practitioner</option>
            <option value="JT">JT — Foreign journalist on temporary permit</option>
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Applicant Photo</label>
          <input type="file" name="applicant_photo" class="form-control" 
                 accept="image/*" capture="environment">
          <div class="small text-muted mt-1">
            Take a clear photo of the applicant for ID verification
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">ID Copy</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="documents[]" value="id_copy" id="doc_id">
            <label class="form-check-label" for="doc_id">ID Copy</label>
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Passport Photos</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="documents[]" value="photos" id="doc_photos">
            <label class="form-check-label" for="doc_photos">Passport Photos</label>
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Proof of Address</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="documents[]" value="proof" id="doc_proof">
            <label class="form-check-label" for="doc_proof">Proof of Address</label>
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Other Documents</label>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="documents[]" value="other" id="doc_other">
            <label class="form-check-label" for="doc_other">Other Documents</label>
          </div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Priority Level</label>
          <select name="priority_level" class="form-select">
            <option value="normal">Normal</option>
            <option value="urgent">Urgent</option>
            <option value="expedited">Expedited</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  {{-- Submit Button --}}
  <div class="zmc-card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div class="small text-muted">
          <i class="ri-information-line me-1"></i>
          All required fields must be completed before submission.
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-secondary" onclick="resetForm()">
            <i class="ri-refresh-line me-1"></i>Reset Form
          </button>
          <button type="submit" form="intakeForm" class="btn btn-primary btn-lg">
            <i class="ri-check-line me-1"></i>Record Intake & Add to Production Queue
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestType = document.getElementById('request_type');
    const applicationType = document.getElementById('application_type');
    const newAppForm = document.getElementById('newApplicationForm');
    const existingAppForm = document.getElementById('existingApplicationForm');
    const lookupLabel = document.getElementById('lookup_label');
    const cardFields = document.getElementById('card_fields');
    const intakeForm = document.getElementById('intakeForm');
    const existingApplicantInfo = document.getElementById('existing_applicant_info');
    const foundApplicantName = document.getElementById('found_applicant_name');
    const existingApplicantNameField = document.getElementById('existing_applicant_name');
    
    function updateFormVisibility() {
        const requestValue = requestType.value;
        const appValue = applicationType.value;
        
        console.log('updateFormVisibility called:', { requestValue, appValue });
        
        // Reset all forms
        newAppForm.style.display = 'none';
        existingAppForm.style.display = 'none';
        cardFields.style.display = 'none';
        existingApplicantInfo.style.display = 'none';
        
        // Remove all required attributes first
        const newAppFields = ['first_name', 'surname', 'id_number', 'category', 'email', 'phone', 'physical_address', 'city', 'province', 'applicant_photo'];
        newAppFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.required = false;
            }
        });
        
        if (requestValue === 'new') {
            console.log('Showing new application form');
            newAppForm.style.display = 'block';
            
            // Make new application fields required
            document.querySelector('[name="first_name"]').required = true;
            document.querySelector('[name="surname"]').required = true;
            document.querySelector('[name="id_number"]').required = true;
            document.querySelector('[name="category"]').required = true;
            document.querySelector('[name="email"]').required = true;
            document.querySelector('[name="phone"]').required = true;
            document.querySelector('[name="physical_address"]').required = true;
            document.querySelector('[name="city"]').required = true;
            document.querySelector('[name="province"]').required = true;
            document.querySelector('[name="applicant_photo"]').required = true;
            
            // Make lookup fields optional
            document.querySelector('[name="lookup_number"]').required = false;
        } else if (requestValue === 'renewal' || requestValue === 'replacement') {
            console.log('Showing existing application form');
            existingAppForm.style.display = 'block';
            
            // Make lookup field required
            document.querySelector('[name="lookup_number"]').required = true;
            
            // Hide payment fields initially
            document.querySelector('[name="receipt_number"]').required = false;
        } else {
            console.log('Default case - hiding forms');
            existingAppForm.style.display = 'block';
            
            // Make lookup field required
            document.querySelector('[name="lookup_number"]').required = true;
        }
        
        // Update lookup label based on application type
        if (appValue === 'accreditation') {
            lookupLabel.textContent = 'Accreditation Number';
        } else if (appValue === 'registration') {
            lookupLabel.textContent = 'Registration Number';
        }
        
        // Show card fields for renewals and new applications
        if (requestValue === 'renewal' || requestValue === 'new') {
            cardFields.style.display = 'block';
        }
    }
    
    function lookupApplication() {
        const lookupNumber = document.getElementById('lookup_number').value;
        const applicationType = document.getElementById('application_type').value;
        
        if (!lookupNumber) {
            alert('Please enter an accreditation/registration number to lookup.');
            return;
        }
        
        // Show loading state
        existingApplicantInfo.style.display = 'block';
        foundApplicantName.textContent = 'Searching...';
        
        // Make AJAX request to lookup application
        fetch(`/staff/officer/lookup-application/${lookupNumber}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                foundApplicantName.textContent = data.applicant_name;
                existingApplicantNameField.value = data.applicant_name;
                
                // Show payment fields if payment exists
                if (data.payment_status === 'paid') {
                    document.querySelector('[name="receipt_number"]').required = true;
                    document.querySelector('[name="receipt_number"]').value = data.receipt_number || '';
                }
                
                // Show card fields for renewals
                if (data.request_type === 'renewal') {
                    cardFields.style.display = 'block';
                    // Pre-fill card fields
                    if (data.expiry_date) {
                        document.querySelector('[name="expiry_date"]').value = data.expiry_date;
                    }
                }
            } else {
                alert('Application not found. Please check the number and try again.');
                foundApplicantName.textContent = 'Not found';
                existingApplicantNameField.value = '';
                existingApplicantInfo.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error looking up application. Please try again.');
            existingApplicantInfo.style.display = 'none';
        });
    }
    
    function moveAllFieldsToForm() {
        // Move all form fields to the main form for submission
        const allInputs = document.querySelectorAll('input, select, textarea');
        const mainForm = document.getElementById('intakeForm');
        
        allInputs.forEach(input => {
            if (!mainForm.contains(input) && input.type !== 'submit') {
                mainForm.appendChild(input.cloneNode(true));
            }
        });
    }
    
    function resetForm() {
        // Reset all form fields and hide optional sections
        document.getElementById('intakeForm').reset();
        existingApplicantInfo.style.display = 'none';
        document.querySelector('[name="receipt_number"]').required = false;
        cardFields.style.display = 'none';
    }
    
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo_preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function openCamera() {
        // Check if device has camera support
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    // Create camera modal
                    const modal = document.createElement('div');
                    modal.className = 'modal fade';
                    modal.innerHTML = `
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Capture Photo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <video id="cameraVideo" autoplay style="width: 100%; max-width: 400px;"></video>
                                    <canvas id="cameraCanvas" style="display: none;"></canvas>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary" onclick="capturePhoto()">
                                            <i class="ri-camera-line me-2"></i>Capture Photo
                                        </button>
                                        <button type="button" class="btn btn-secondary ms-2" onclick="closeCamera()">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(modal);
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                    
                    // Setup video stream
                    const video = document.getElementById('cameraVideo');
                    video.srcObject = stream;
                    
                    // Store stream for cleanup
                    window.currentStream = stream;
                    
                    // Setup capture function
                    window.capturePhoto = function() {
                        const canvas = document.getElementById('cameraCanvas');
                        const context = canvas.getContext('2d');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        context.drawImage(video, 0, 0);
                        
                        // Convert to blob and set to file input
                        canvas.toBlob(function(blob) {
                            const file = new File([blob], 'camera_photo.jpg', { type: 'image/jpeg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            
                            const fileInput = document.getElementById('applicant_photo');
                            fileInput.files = dataTransfer.files;
                            
                            // Trigger preview
                            previewPhoto(fileInput);
                            
                            // Close modal
                            bsModal.hide();
                            
                            // Cleanup camera
                            if (window.currentStream) {
                                window.currentStream.getTracks().forEach(track => track.stop());
                            }
                        }, 'image/jpeg', 0.95);
                    };
                    
                    window.closeCamera = function() {
                        bsModal.hide();
                        if (window.currentStream) {
                            window.currentStream.getTracks().forEach(track => track.stop());
                        }
                    };
                    
                    // Cleanup on modal close
                    modal.addEventListener('hidden.bs.modal', function() {
                        if (window.currentStream) {
                            window.currentStream.getTracks().forEach(track => track.stop());
                        }
                        document.body.removeChild(modal);
                    });
                    
                })
                .catch(function(err) {
                    console.error('Camera access denied:', err);
                    alert('Unable to access camera. Please use file upload instead.');
                });
        } else {
            alert('Camera not supported on this device. Please use file upload instead.');
        }
    }
    
    // Initialize form visibility
    console.log('Initializing form visibility');
    console.log('Elements found:', {
        requestType: !!requestType,
        applicationType: !!applicationType,
        newAppForm: !!newAppForm,
        existingAppForm: !!existingAppForm
    });
    
    if (requestType && applicationType) {
        requestType.addEventListener('change', updateFormVisibility);
        applicationType.addEventListener('change', updateFormVisibility);
        updateFormVisibility();
    } else {
        console.error('Required elements not found');
    }
});
</script>
@endsection
