<?php $__env->startSection('title', 'Physical Intake'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('staff.officer.physical-intake.process')); ?>" id="intakeForm" enctype="multipart/form-data">
  <?php echo csrf_field(); ?>
  <div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155; padding: 20px;">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Physical Intake</h4>
        <div class="text-muted mt-1" style="font-size:13px;">
          <i class="ri-information-line me-1"></i>
          Record walk-in applications, renewals, and link to existing records for card generation.
        </div>
      </div>
      <div class="d-flex gap-2">
        <a href="<?php echo e(route('staff.officer.dashboard')); ?>" class="btn btn-white border shadow-sm btn-sm px-3">
          <i class="ri-dashboard-3-line me-1"></i>Back to Dashboard
        </a>
      </div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success d-flex align-items-start gap-2">
        <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
        <div><?php echo e(session('success')); ?></div>
      </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($e); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>


  
  <div class="zmc-card shadow-sm mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0"><i class="ri-user-add-line me-2"></i>Intake Type</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label small fw-bold">Application Type</label>
          <select name="application_type" id="application_type" class="form-select border-light-subtle py-2 shadow-none" style="border-radius: 10px; background-color: #f8fafc;">
              <option value="accreditation" selected>Accreditation (Media Practitioner)</option>
              <option value="registration">Registration (Media House)</option>
            </select>
        </div>
        
        <div class="col-md-12">
          <label class="form-label small fw-bold">Request Type</label>
          <select name="request_type" id="request_type" class="form-select">
            <option value="">-- Select Request Type --</option>
            <option value="new">New Application</option>
            <option value="renewal">Renewal</option>
            <option value="replacement">Replacement</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  
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
            <option value="JE">JE — Local media practitioner employed on full-time basis</option>
            <option value="JF">JF — Local journalist free-lancing locally</option>
            <option value="JO">JO — Local journalist running an office for foreign media service</option>
            <option value="JS">JS — Local journalist stringing for foreign media service</option>
            <option value="JM">JM — Local journalist reporting both locally and abroad</option>
            <option value="JP">JP — Local media practitioner in content creation, photography, public relations and all forms of digital media</option>
            <option value="JD">JD — Local media practitioner in digital social media</option>
            <option value="JT">JT — Foreign journalist on temporary permit</option>
          </select>
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Receipt Number <span class="text-danger">*</span></label>
          <input type="text" name="receipt_number" class="form-control" placeholder="Receipt number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="Email address">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label>
          <input type="tel" name="phone" class="form-control" placeholder="Phone number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Physical Address <span class="text-danger">*</span></label>
          <input type="text" name="physical_address" class="form-control" placeholder="Full physical address">
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
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">City <span class="text-danger">*</span></label>
          <input type="text" name="city" class="form-control" placeholder="City or town">
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
                  <img id="photo_preview" src="<?php echo e(asset('images/default-avatar.png')); ?>" 
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
        
        
        <div id="mediaHouseFields" class="col-12" style="display: none;">
          <div class="alert alert-info">
            <i class="ri-building-line me-2"></i>
            <strong>Media House Details:</strong> Complete additional information for media house registration.
          </div>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold">Entity Name <span class="text-danger">*</span></label>
              <input type="text" name="entity_name" class="form-control" placeholder="Official registered entity name">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Trading Name <span class="text-danger">*</span></label>
              <input type="text" name="trading_name" class="form-control" placeholder="Trading as name">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Business Registration Number <span class="text-danger">*</span></label>
              <input type="text" name="business_registration" class="form-control" placeholder="Company registration number">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Tax Number <span class="text-danger">*</span></label>
              <input type="text" name="tax_number" class="form-control" placeholder="Tax/BVR number">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Business Type <span class="text-danger">*</span></label>
              <select name="business_type" class="form-select">
                <option value="">-- Select Business Type --</option>
                <option value="private_limited">Private Limited</option>
                <option value="public_limited">Public Limited</option>
                <option value="partnership">Partnership</option>
                <option value="sole_proprietor">Sole Proprietor</option>
                <option value="ngo">NGO</option>
                <option value="government">Government</option>
                <option value="other">Other</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Ownership Type <span class="text-danger">*</span></label>
              <select name="ownership_type" class="form-select">
                <option value="">-- Select Ownership --</option>
                <option value="local">Local Ownership</option>
                <option value="foreign">Foreign Ownership</option>
                <option value="mixed">Mixed Ownership</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Local Shareholding (%) <span class="text-danger">*</span></label>
              <input type="number" name="local_ownership" class="form-control" placeholder="Percentage" min="0" max="100">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Website</label>
              <input type="url" name="website" class="form-control" placeholder="https://www.example.com">
            </div>
            
            <div class="col-12">
              <label class="form-label small fw-bold">Postal Address <span class="text-danger">*</span></label>
              <input type="text" name="postal_address" class="form-control" placeholder="Postal address">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">ZMC Media Category <span class="text-danger">*</span></label>
              <select name="media_category" class="form-select">
                <option value="">-- Select Category --</option>
                <optgroup label="Mass Media Service Categories">
                  <option value="MC">MC — Community Media</option>
                  <option value="MA">MA — Advertising agency as media service</option>
                  <option value="MF">MF — Local office for foreign media service</option>
                  <option value="MN">MN — National media service publishing newspaper</option>
                  <option value="DG">DG — Internet base media service</option>
                  <option value="MP">MP — Production house as media service</option>
                  <option value="MS">MS — National media service publishing magazine</option>
                  <option value="MT">MT — Broadcasting media service free to air</option>
                  <option value="MB">MB — Satellite broadcast</option>
                  <option value="MV">MV — Video on demand</option>
                </optgroup>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Publication/Service Name <span class="text-danger">*</span></label>
              <input type="text" name="publication_name" class="form-control" placeholder="e.g., The Daily News">
            </div>
                <div class="col-md-3">
                  <label class="form-label small">Frequency</label>
                  <select name="publication_frequency" class="form-select">
                    <option value="">-- Frequency --</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="irregular">Irregular</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Editor/Manager Name <span class="text-danger">*</span></label>
              <input type="text" name="editor_name" class="form-control" placeholder="Editor or Manager name">
            </div>
            
            <div class="col-md-6">
              <label class="form-label small fw-bold">Editor Contact <span class="text-danger">*</span></label>
              <input type="text" name="editor_contact" class="form-control" placeholder="Phone or email">
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

  
  <div id="existingApplicationForm" class="card shadow-sm mb-4" style="display: none;">
    <div class="card-header bg-success text-white">
      <h6 class="fw-bold m-0"><i class="ri-refresh-line me-2"></i>Existing Application Details</h6>
    </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="ri-information-line me-2"></i>
        <strong>Renewal/Replacement:</strong> Enter applicant details to link to existing application record.
      </div>
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
          <input type="text" name="renewal_first_name" class="form-control" placeholder="First name">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Surname <span class="text-danger">*</span></label>
          <input type="text" name="renewal_surname" class="form-control" placeholder="Surname">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">ID Number <span class="text-danger">*</span></label>
          <input type="text" name="renewal_id_number" class="form-control" placeholder="National ID number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">
            <span id="lookup_label">Accreditation / Registration Number</span>
          </label>
          <input type="text" name="lookup_number" id="lookup_number" class="form-control" 
                 placeholder="e.g. J12345678E or MC00001234">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Receipt Number <span class="text-danger">*</span></label>
          <input type="text" name="renewal_receipt_number" class="form-control" placeholder="Receipt number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
          <input type="email" name="renewal_email" class="form-control" placeholder="Email address">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label>
          <input type="tel" name="renewal_phone" class="form-control" placeholder="Phone number">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Physical Address <span class="text-danger">*</span></label>
          <input type="text" name="renewal_physical_address" class="form-control" placeholder="Full physical address">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">City/Town <span class="text-danger">*</span></label>
          <input type="text" name="renewal_city" class="form-control" placeholder="City or town">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Province <span class="text-danger">*</span></label>
          <select name="renewal_province" class="form-select">
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
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Organization/Media House</label>
          <input type="text" name="renewal_organization" class="form-control" placeholder="Organization or media house name">
        </div>
        
        <div class="col-md-6">
          <label class="form-label small fw-bold">Position/Designation</label>
          <input type="text" name="renewal_position" class="form-control" placeholder="Position or designation">
        </div>
        
        <div class="col-md-12" id="existing_applicant_info" style="display: none;">
          <div class="alert alert-success">
            <i class="ri-user-check-line me-2"></i>
            <strong>Applicant Found:</strong> <span id="found_applicant_name">Loading...</span>
          </div>
          <input type="hidden" name="existing_applicant_name" id="existing_applicant_name">
        </div>
        
        <div class="col-12">
          <button type="button" class="btn btn-outline-primary" onclick="lookupApplication()">
            <i class="ri-search-line me-1"></i> Search Existing Record
          </button>
          <div class="form-text small mt-2">Search for existing accreditation/registration record using the details provided.</div>
        </div>
      </div>
    </div>
  </div>

  
  
  <div class="zmc-card shadow-sm">
    <div class="card-body">
      <div class="alert alert-info">
        <i class="ri-information-line me-1"></i>
        All required fields must be completed before submission.
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary" onclick="resetForm()">
          <i class="ri-refresh-line me-1"></i>Reset Form
        </button>
        <button type="button" class="btn btn-info" onclick="reviewApplication()">
          <i class="ri-eye-line me-1"></i>Review Application
        </button>
        <button type="submit" form="intakeForm" class="btn btn-primary btn-lg">
          <i class="ri-check-line me-1"></i>Record Intake & Add to Production Queue
        </button>
      </div>
    </div>
  </div>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestType = document.getElementById('request_type');
    const applicationType = document.getElementById('application_type');
    const newAppForm = document.getElementById('newApplicationForm');
    const existingAppForm = document.getElementById('existingApplicationForm');
    const lookupLabel = document.getElementById('lookup_label');
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
        existingApplicantInfo.style.display = 'none';
        
        // Hide media house fields initially
        const mediaHouseFields = document.getElementById('mediaHouseFields');
        if (mediaHouseFields) {
            mediaHouseFields.style.display = 'none';
        }
        
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
            
            // Show media house fields ONLY if it's a registration
            // For accreditation, hide media house fields and show only accreditation fields
            if (appValue === 'registration' && mediaHouseFields) {
                mediaHouseFields.style.display = 'block';
                console.log('Showing media house fields for registration');
            } else if (appValue === 'accreditation' && mediaHouseFields) {
                mediaHouseFields.style.display = 'none';
                console.log('Hiding media house fields for accreditation');
            }
            
            // Make new application fields required
            document.querySelector('[name="first_name"]').required = true;
            document.querySelector('[name="surname"]').required = true;
            document.querySelector('[name="id_number"]').required = true;
            document.querySelector('[name="category"]').required = true;
            document.querySelector('[name="physical_address"]').required = true;
            document.querySelector('[name="applicant_photo"]').required = true;
            
            // Make media house fields required if registration
            if (appValue === 'registration') {
                const mediaHouseRequiredFields = ['entity_name', 'trading_name', 'business_registration', 'tax_number', 'business_type', 'ownership_type', 'local_ownership', 'postal_address', 'publication_name', 'media_category', 'publication_frequency', 'editor_name', 'editor_contact'];
                mediaHouseRequiredFields.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.required = true;
                    }
                });
            }
            
            // Make lookup fields optional
            document.querySelector('[name="lookup_number"]').required = false;
        } else if (requestValue === 'renewal' || requestValue === 'replacement') {
            console.log('Showing existing application form');
            existingAppForm.style.display = 'block';
            
            // Make renewal fields required
            const renewalRequiredFields = [
                'renewal_first_name', 'renewal_surname', 'renewal_id_number', 
                'lookup_number', 'renewal_receipt_number', 'renewal_email', 
                'renewal_phone', 'renewal_physical_address', 'renewal_city', 'renewal_province'
            ];
            renewalRequiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.required = true;
                }
            });
            
            console.log('Renewal fields set as required');
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
            // Card generation functionality removed
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
    
    function reviewApplication() {
        console.log('Reviewing application before submission...');
        
        // Validate all required fields for new applications
        if (requestType.value === 'new') {
            const requiredFields = ['first_name', 'surname', 'id_number', 'category', 'email', 'phone', 'physical_address', 'city', 'province', 'applicant_photo'];
            let isValid = true;
            let missingFields = [];
            
            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field && !field.value.trim()) {
                    isValid = false;
                    missingFields.push(fieldName);
                }
            });
            
            if (!isValid) {
                alert('Please complete all required fields before reviewing:\n\n• ' + missingFields.join('\n• ') + '\n\nThese fields are required for new applications.');
                return;
            }
            
            // Show review modal with application details
            showReviewModal();
        } else {
            // For renewals/replacements, just show existing applicant info
            alert('Application ready for review. Click OK to continue.');
        }
    }
    
    function showReviewModal() {
        // Create review modal with application details
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Application Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h6>Application Details</h6>
                        <div class="mb-3">
                            <strong>Application Type:</strong> ${applicationType.value}
                        </div>
                        <div class="mb-3">
                            <strong>Request Type:</strong> ${requestType.value}
                        </div>
                        <div class="mb-3">
                            <strong>Name:</strong> ${document.querySelector('[name="first_name"]').value} ${document.querySelector('[name="surname"]').value}
                        </div>
                        <div class="mb-3">
                            <strong>ID Number:</strong> ${document.querySelector('[name="id_number"]').value}
                        </div>
                        <div class="mb-3">
                            <strong>Category:</strong> ${document.querySelector('[name="category"]').value}
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong> ${document.querySelector('[name="email"]').value}
                        </div>
                        <div class="mb-3">
                            <strong>Phone:</strong> ${document.querySelector('[name="phone"]').value}
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Handle modal close
        modal.addEventListener('hidden.bs.modal', function () {
            document.body.removeChild(modal);
        });
    }
    
    window.moveAllFieldsToForm = function() {
        const requestTypeValue = requestType.value;
        const activeSectionId = (requestTypeValue === 'new') ? 'newApplicationForm' : 'existingApplicationForm';
        const inactiveSectionId = (requestTypeValue === 'new') ? 'existingApplicationForm' : 'newApplicationForm';
        
        const activeSection = document.getElementById(activeSectionId);
        const inactiveSection = document.getElementById(inactiveSectionId);
        
        // 1. Enable all inputs in the ACTIVE section
        if (activeSection) {
            const activeInputs = activeSection.querySelectorAll('input, select, textarea');
            activeInputs.forEach(input => {
                input.disabled = false;
            });
        }
        
        // 2. Disable all inputs in the INACTIVE section so they aren't submitted
        if (inactiveSection) {
            const inactiveInputs = inactiveSection.querySelectorAll('input, select, textarea');
            inactiveInputs.forEach(input => {
                input.disabled = true;
            });
        }

        // 3. Ensure global fields are enabled
        if (applicationType) applicationType.disabled = false;
        if (requestType) requestType.disabled = false;
        const lookupNumber = document.querySelector('[name="lookup_number"]');
        if (lookupNumber) lookupNumber.disabled = (requestTypeValue === 'new');

        console.log('Form prepared for submission. Active section:', activeSectionId);
    };

    // Attach to form onsubmit
    const mainIntakeForm = document.getElementById('intakeForm');
    if (mainIntakeForm) {
        mainIntakeForm.onsubmit = window.moveAllFieldsToForm;
    }
    
    function resetForm() {
        // Reset all form fields and hide optional sections
        document.getElementById('intakeForm').reset();
        existingApplicantInfo.style.display = 'none';
        document.querySelector('[name="receipt_number"]').required = false;
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/staff/officer/physical_intake.blade.php ENDPATH**/ ?>