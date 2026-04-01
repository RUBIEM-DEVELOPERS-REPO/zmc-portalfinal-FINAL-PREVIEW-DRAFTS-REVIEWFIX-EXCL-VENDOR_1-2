@extends('layouts.portal')

@section('title', 'New Accreditation (AP3)')
@section('page_title', 'NEW ACCREDITATION APPLICATION (AP3)')

@section('content')
<div id="new-application-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">New Accreditation Application (AP3)</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.home') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Tracker
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>Application for Accreditation of a Media Practitioner</h1>
      <p>Zimbabwe Media Commission Act (2020), Statutory Instrument 169C (Registration, Accreditation and Levy) Regulations (2002)</p>
    </div>

    <div class="form-steps-container">
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Applicant Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Personal Details</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Qualifications & Employment</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Uploads & Declaration</div>
          </div>
        </div>
      </div>

      <form id="ap3Form" onsubmit="return false;">
        {{-- Hidden scope --}}
        <input type="hidden" name="journalist_scope" id="ap3_scope" required>

        {{-- ===================== STEP 1: TYPE ===================== --}}
        <div class="step-content active" id="ap3-step-1">
          <h3 class="step-title">Select Applicant Type</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Select whether you are a Local or Foreign media practitioner. Different requirements apply.
          </div>

          <div class="app-type-container">
            <div class="app-type-cards">
              <div class="app-type-card" data-type="local">
                <i class="ri-user-3-line"></i>
                <h4>Local Media Practitioner</h4>
                <p>Zimbabwean citizens/residents applying for accreditation.</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">14 Days Processing</span>
                  <span class="badge bg-light text-dark">National ID Required</span>
                </div>
              </div>

              <div class="app-type-card" data-type="foreign">
                <i class="ri-global-line"></i>
                <h4>Foreign Media Practitioner</h4>
                <p>International media practitioners seeking temporary accreditation.</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">21 Days Processing</span>
                  <span class="badge bg-light text-dark">Passport Required</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- ===================== STEP 2: PERSONAL DETAILS ===================== --}}
        <div class="step-content" id="ap3-step-2">
          <h3 class="step-title">PERSONAL DETAILS</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Please provide your personal information as it appears on your official documents.
          </div>

          {{-- Title / Surname / Name / Other --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Title</label>
              <select class="form-control" name="title" required>
                <option value="">Select</option>
                <option value="Prof">Prof</option>
                <option value="Dr">Dr</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
                <option value="Miss">Miss</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="form-field">
              <label class="form-label required">Surname</label>
              <input type="text" class="form-control" name="surname" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Name</label>
              <input type="text" class="form-control" name="first_name" required>
            </div>

            <div class="form-field">
              <label class="form-label">Other</label>
              <input type="text" class="form-control" name="other_names">
            </div>
          </div>

          {{-- DOB / Place & Country of Birth --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Date of Birth</label>
              <input type="date" class="form-control" name="dob" required>
            </div>

            <div class="form-field">
              <label class="form-label required">Place and Country of Birth</label>
              <input type="text" class="form-control" name="birth_place" required>
            </div>
          </div>

          {{-- Marital Status / Sex --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Marital Status</label>
              <select class="form-control" name="marital_status" required>
                <option value="">Select</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="form-field">
              <label class="form-label required">Sex</label>
              <div class="checkbox-group">
                <div class="checkbox-item">
                  <input type="radio" id="ap3-sex-male" name="gender" value="male" required>
                  <label for="ap3-sex-male">Male</label>
                </div>
                <div class="checkbox-item">
                  <input type="radio" id="ap3-sex-female" name="gender" value="female" required>
                  <label for="ap3-sex-female">Female</label>
                </div>
              </div>
            </div>
          </div>

          {{-- National Reg No (Local) / Passport No (Foreign) --}}
          <div class="form-row">
            <div class="form-field scope-local">
              <label class="form-label required">National Reg. No</label>
              <input type="text" class="form-control" name="national_reg_no" placeholder="63-1234567-X-89" data-req="1">
            </div>

            <div class="form-field scope-foreign">
              <label class="form-label required">Passport No</label>
              <input type="text" class="form-control" name="passport_no" placeholder="Passport number" data-req="1">
            </div>

            <div class="form-field">
              <label class="form-label required">Nationality</label>
              <input type="text" class="form-control" name="nationality" required>
            </div>
          </div>

          {{-- Passport expiry / issued at (Foreign only required) --}}
          <div class="form-row scope-foreign">
            <div class="form-field">
              <label class="form-label required">Date of Expiry</label>
              <input type="date" class="form-control" name="passport_expiry" data-req="1">
            </div>
            <div class="form-field">
              <label class="form-label required">Issued at</label>
              <input type="text" class="form-control" name="passport_issued_at" placeholder="City/Country" data-req="1">
            </div>
          </div>

          {{-- Driver’s licence / (Local optional passport block not required) --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label">Driver’s Licence No</label>
              <input type="text" class="form-control" name="drivers_licence_no">
            </div>
            <div class="form-field"></div>
          </div>

          {{-- Residential Address --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Residential Address</label>
              <textarea class="form-control" rows="3" name="address" required></textarea>
            </div>
          </div>

          {{-- Foreign-only: First time in Zim / last here / Address in Zimbabwe --}}
          <div class="scope-foreign">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Is this your first time in Zimbabwe?</label>
                <div class="checkbox-group">
                  <div class="checkbox-item">
                    <input type="radio" id="ap3-first-yes" name="first_time_in_zim" value="yes" data-req="1">
                    <label for="ap3-first-yes">Yes</label>
                  </div>
                  <div class="checkbox-item">
                    <input type="radio" id="ap3-first-no" name="first_time_in_zim" value="no" data-req="1">
                    <label for="ap3-first-no">No</label>
                  </div>
                </div>
              </div>

              <div class="form-field">
                <label class="form-label">If No, indicate when you were last here</label>
                <input type="text" class="form-control" name="last_in_zim_when" placeholder="e.g. June 2024">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Address in Zimbabwe</label>
                <input type="text" class="form-control" name="address_in_zimbabwe" data-req="1">
              </div>
              <div class="form-field"></div>
            </div>
          </div>

          {{-- Phone / Email --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Phone No.</label>
              <div class="d-flex gap-2">
                <select class="form-control" name="phone_country_code" style="max-width:180px;" required>
                  <option value="">Country Code</option>
                  <option value="+263">+263 (ZW)</option>
                  <option value="+27">+27 (ZA)</option>
                  <option value="+260">+260 (ZM)</option>
                  <option value="+258">+258 (MZ)</option>
                  <option value="+1">+1 (US/CA)</option>
                  <option value="+44">+44 (UK)</option>
                </select>
                <input type="text" class="form-control" name="phone" placeholder="e.g. 771234567" required>
              </div>
            </div>

            <div class="form-field">
              <label class="form-label required">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
          </div>
        </div>

        {{-- ===================== STEP 3: QUALIFICATIONS + EMPLOYMENT + REFEREES ===================== --}}
        <div class="step-content" id="ap3-step-3">
          <h3 class="step-title">QUALIFICATIONS, EMPLOYMENT & REFEREES</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Qualifications are optional. Referees (3) are required.
          </div>

          {{-- ===== QUALIFICATIONS (Local only, optional) ===== --}}
          <div class="scope-local">
            <h6 class="mt-3 fw-bold">QUALIFICATIONS</h6>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Highest Academic Qualifications (Year / Institution / Qualification) <span class="text-muted">(Not mandatory)</span></label>
                <div id="ap3HighestAcademicRows"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="ap3AddHighestAcademicRow">
                  <i class="ri-add-line"></i> Add Row
                </button>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Professional Qualifications (Year / Institution / Qualification)</label>
                <div id="ap3ProfessionalQualRows"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="ap3AddProfessionalQualRow">
                  <i class="ri-add-line"></i> Add Row
                </button>
              </div>
            </div>
          </div>

          {{-- ===== EMPLOYMENT STATUS (Both) ===== --}}
          <h6 class="mt-4 fw-bold">EMPLOYMENT</h6>
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Applicant Type</label>
              <div class="checkbox-group">
                <div class="checkbox-item">
                  <input type="radio" id="ap3-employ-employed" name="employment_type" value="employed" required>
                  <label for="ap3-employ-employed">Employed</label>
                </div>
                <div class="checkbox-item">
                  <input type="radio" id="ap3-employ-freelancer" name="employment_type" value="freelancer" required>
                  <label for="ap3-employ-freelancer">Freelancer</label>
                </div>
              </div>
              <div class="form-hint"><i class="ri-eye-line"></i> If Freelancer, employment fields & employment letter will be hidden.</div>
            </div>
          </div>

          {{-- Type of medium / Designation (Both) --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Type of medium</label>
              <select class="form-control" name="medium_type" required>
                <option value="">Select</option>
                <option value="News Agency">News Agency</option>
                <option value="Newspaper">Newspaper</option>
                <option value="Television">Television</option>
                <option value="Radio">Radio</option>
                <option value="Magazine">Magazine</option>
                <option value="Reporter">Reporter</option>
                <option value="Engineer/Technician">Engineer/Technician</option>
                <option value="Others">Others (Specify)</option>
              </select>
            </div>

            <div class="form-field">
              <label class="form-label required">Designation</label>
              <select class="form-control" name="designation" required>
                <option value="">Select</option>
                <option value="Producer/Editor">Producer/Editor</option>
                <option value="Correspondent">Correspondent</option>
                <option value="Photographer">Photographer</option>
                <option value="Freelance">Freelance</option>
                <option value="Camera person">Camera person</option>
                <option value="News Photo">News Photo</option>
                <option value="Others">Others (Specify)</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field" id="ap3OtherMediumWrap" style="display:none;">
              <label class="form-label required">If Others, specify (Medium)</label>
              <input type="text" class="form-control" name="medium_type_other">
            </div>
            <div class="form-field" id="ap3OtherDesignationWrap" style="display:none;">
              <label class="form-label required">If Others, specify (Designation)</label>
              <input type="text" class="form-control" name="designation_other">
            </div>
          </div>

          {{-- Employment fields (hide when freelancer) --}}
          <div class="employment-only">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Media Organisation represented</label>
                <input type="text" class="form-control" name="media_org">
              </div>
              <div class="form-field">
                <label class="form-label">Physical Address</label>
                <input type="text" class="form-control" name="media_org_address">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="media_org_phone">
              </div>
              <div class="form-field">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="media_org_email">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Name of Editor/Publisher</label>
                <input type="text" class="form-control" name="editor_publisher_name">
              </div>
              <div class="form-field">
                <label class="form-label">Immediate Supervisor you report to</label>
                <input type="text" class="form-control" name="immediate_supervisor">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Which other Organisations do you string for?</label>
                <input type="text" class="form-control" name="string_for_orgs">
              </div>
              <div class="form-field">
                <label class="form-label">Give details</label>
                <textarea class="form-control" rows="2" name="string_for_details"></textarea>
              </div>
            </div>
          </div>

          {{-- Foreign-only employment extras --}}
          {{-- Foreign-only travel details (Required for ALL foreign applicants) --}}
          <div class="scope-foreign">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Country in which media practitioner is based</label>
                <input type="text" class="form-control" name="journalist_based_country">
              </div>
              <div class="form-field">
                <label class="form-label required">Arrived on</label>
                <input type="date" class="form-control" name="arrived_on" data-req="1">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">By Air/Road</label>
                <select class="form-control" name="arrival_mode" data-req="1">
                  <option value="">Select</option>
                  <option value="Air">Air</option>
                  <option value="Road">Road</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label required">Port of Entry</label>
                <input type="text" class="form-control" name="port_of_entry" data-req="1">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Departing on</label>
                <input type="date" class="form-control" name="departing_on" data-req="1">
              </div>
              <div class="form-field">
                <label class="form-label required">Special assignment to be Covered in Zimbabwe (Briefly)</label>
                <textarea class="form-control" rows="2" name="special_assignment" data-req="1"></textarea>
              </div>
            </div>
          </div>

          {{-- ===== REFEREES (3 required) ===== --}}
          <h6 class="mt-4 fw-bold">REFEREES <span class="text-muted">(3 required)</span></h6>
          <div class="form-row">
            <div class="form-field">
              <div id="ap3RefereeFixedRows"></div>
              <div class="form-hint"><i class="ri-information-line"></i> Fill all 3 referees (Name, Address, Phone).</div>
            </div>
          </div>
        </div>

        {{-- ===================== STEP 4: UPLOADS + DECLARATION ===================== --}}
        <div class="step-content" id="ap3-step-4">
          <h3 class="step-title">ANNEXURES/UPLOADS & DECLARATION</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Upload required documents. Photo should be passport size with a white background.
          </div>

          {{-- Local: Collection office required --}}
          <div class="scope-local">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Collection Office</label>
                <select class="form-control" name="collection_region" required>
                  <option value="">Select collection office</option>
                  <option value="harare">Harare Regional Office - 3rd Floor, ZMC House, 109 Rotten Row, Harare</option>
                  <option value="bulawayo">Bulawayo Regional Office - Room 12, CABS Centre, 74 Jason Moyo St, Bulawayo</option>
                  <option value="mutare">Mutare Regional Office - 2nd Floor, Old Mutual Building, Main St, Mutare</option>
                  <option value="masvingo">Masvingo Regional Office - Suite 5, TelOne Complex, Robert Mugabe Way, Masvingo</option>
                </select>
              </div>
            </div>
          </div>

          {{-- Photo (Upload or Camera) --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Photo (Passport size, white background)</label>
              <div class="upload-area">
                <i class="ri-camera-line"></i>
                <h5>Upload Photo</h5>
                <p>JPG/PNG, clear face, white background</p>
                <input type="file" name="passport_photo" accept=".jpg,.jpeg,.png" style="display:none;" required>
                <div class="d-flex gap-2 justify-content-center">
                  <button type="button" class="upload-btn btn btn-sm btn-primary">Choose File</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="ap3TakePhotoBtn">
                    <i class="ri-camera-lens-line"></i> Take Photo
                  </button>
                </div>
              </div>
              <div class="uploaded-files"></div>
            </div>

            {{-- Local: National ID scan / Foreign: Passport biodata page --}}
            <div class="form-field scope-local">
              <label class="form-label required">National ID Scan</label>
              <div class="upload-area">
                <i class="ri-id-card-line"></i>
                <h5>Upload National ID</h5>
                <p>PDF/JPG/PNG</p>
                <input type="file" name="id_scan" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                <button type="button" class="upload-btn btn btn-sm btn-primary">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>

            <div class="form-field scope-foreign">
              <label class="form-label required">Passport Bio Data Page</label>
              <div class="upload-area">
                <i class="ri-passport-line"></i>
                <h5>Upload Passport Bio Data Page</h5>
                <p>PDF/JPG/PNG</p>
                <input type="file" name="passport_biodata_page" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" data-req="1">
                <button type="button" class="upload-btn btn btn-sm btn-primary">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
          </div>

          {{-- Foreign: Clearance letter --}}
          <div class="form-row scope-foreign">
            <div class="form-field">
              <label class="form-label required">Clearance Letter</label>
              <div class="upload-area">
                <i class="ri-file-text-line"></i>
                <h5>Upload Clearance Letter</h5>
                <p>PDF/JPG/PNG</p>
                <input type="file" name="clearance_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" data-req="1">
                <button type="button" class="upload-btn btn btn-sm btn-primary">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
            <div class="form-field"></div>
          </div>

          {{-- Local: Employment letter OR Reference/Testimonial/Affidavit --}}
          <div class="form-row scope-local">
            <div class="form-field employment-letter-only">
              <label class="form-label required">Employment Letter (Employed)</label>
              <div class="upload-area">
                <i class="ri-file-text-line"></i>
                <h5>Upload Employment Letter</h5>
                <p>Required for Employed applicants</p>
                <input type="file" name="employment_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                <button type="button" class="upload-btn btn btn-sm btn-primary">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>

            <div class="form-field freelancer-only" style="display:none;">
              <label class="form-label required">Reference Letter / Affidavit (Freelancer)</label>
              <div class="upload-area">
                <i class="ri-file-paper-2-line"></i>
                <h5>Upload Reference/Testimonial/Affidavit</h5>
                <p>Required for Freelancers</p>
                <input type="file" name="reference_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                <button type="button" class="upload-btn btn btn-sm btn-primary">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
          </div>

          {{-- Local: Educational Certificate (optional) --}}
          <div class="form-row scope-local">
            <div class="form-field">
              <label class="form-label">Educational Certificate (Optional)</label>
              <div class="upload-area">
                <i class="ri-award-line"></i>
                <h5>Upload Certificate</h5>
                <p>Optional</p>
                <input type="file" name="educational_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                <button type="button" class="upload-btn btn btn-sm btn-outline-secondary">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
            <div class="form-field"></div>
          </div>

          {{-- Declaration --}}
          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Declaration</label>
              <div class="alert alert-light border">
                I declare that all the information given above, to the best of my knowledge is true and complete.
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <div class="form-check mt-1">
                <input class="form-check-input" type="checkbox" id="ap3DeclarationConfirm" name="declaration_confirmed" value="1" required>
                <label class="form-check-label" for="ap3DeclarationConfirm">
                  I confirm that I have read and agree to this declaration.
                </label>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Date</label>
              <input type="date" class="form-control" name="declaration_date" required>
            </div>
            <div class="form-field"></div>
          </div>
        </div>

        {{-- Buttons --}}
        <div class="form-buttons">
          <div>
            <button type="button" class="btn btn-secondary" id="ap3PrevBtn"><i class="ri-arrow-left-line"></i> Previous</button>
            <button type="button" class="btn btn-outline-secondary ms-2" id="ap3SaveDraftBtn"><i class="ri-save-line"></i> Save Draft</button>
          </div>
          <button type="button" class="btn btn-primary" id="ap3NextBtn">Next <i class="ri-arrow-right-line"></i></button>
        </div>
      </form>
    </div>
  </div>

  {{-- Review Modal --}}
  <div class="modal fade" id="ap3ReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="ri-file-search-line me-2"></i>Review Your Application</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="ap3ReviewContent"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Edit Application</button>
          <button type="button" class="btn btn-primary" id="ap3ConfirmSubmitBtn">
            <i class="ri-send-plane-line me-2"></i>Confirm & Submit
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Camera Modal --}}
  <div class="modal fade" id="ap3CameraModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="ri-camera-lens-line me-2"></i>Take Photo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" id="ap3CameraCloseBtn"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="ri-information-line me-1"></i>
            Stand in front of a <strong>white background</strong>. Make sure your face is well-lit.
          </div>

          <video id="ap3CamVideo" autoplay playsinline style="width:100%; border-radius:12px; background:#000;"></video>
          <canvas id="ap3CamCanvas" style="display:none;"></canvas>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-primary flex-grow-1" id="ap3CaptureBtn">
              <i class="ri-camera-line me-1"></i> Capture
            </button>
            <button type="button" class="btn btn-outline-secondary" id="ap3RetakeBtn" style="display:none;">
              <i class="ri-refresh-line me-1"></i> Retake
            </button>
          </div>

          <div id="ap3CapturePreview" class="mt-3" style="display:none;">
            <div class="fw-semibold mb-2">Preview</div>
            <img id="ap3PreviewImg" alt="Captured photo" style="width:100%; border-radius:12px; border:1px solid #e5e7eb;" />
            <button type="button" class="btn btn-success w-100 mt-3" id="ap3UsePhotoBtn">
              <i class="ri-check-line me-1"></i> Use This Photo
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  let ap3CurrentStep = 1;
  const csrfToken = '{{ csrf_token() }}';

  const ap3Steps = document.querySelectorAll('#new-application-page .step');
  const ap3StepContents = [
    document.getElementById('ap3-step-1'),
    document.getElementById('ap3-step-2'),
    document.getElementById('ap3-step-3'),
    document.getElementById('ap3-step-4'),
  ];

  const ap3PrevBtn = document.getElementById('ap3PrevBtn');
  const ap3NextBtn = document.getElementById('ap3NextBtn');
  const ap3SaveDraftBtn = document.getElementById('ap3SaveDraftBtn');

  function getFormData() {
    const form = document.getElementById('ap3Form');
    const formData = {};
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
      if (!input.name) return;
      if (input.type === 'file') return;

      if (input.type === 'radio') {
        if (input.checked) formData[input.name] = input.value;
      } else if (input.type === 'checkbox') {
        formData[input.name] = input.checked;
      } else {
        formData[input.name] = input.value;
      }
    });

    return formData;
  }

  function ap3ShowStep(step){
    ap3StepContents.forEach((el, idx) => el.classList.toggle('active', idx === (step-1)));

    ap3Steps.forEach(s => {
      const n = parseInt(s.dataset.step, 10);
      s.classList.remove('active','completed');
      if(n === step) s.classList.add('active');
      if(n < step){
        s.classList.add('completed');
        s.querySelector('.step-number').innerHTML = '<i class="ri-check-line"></i>';
      }else{
        s.querySelector('.step-number').textContent = n;
      }
    });

    ap3PrevBtn.style.display = step === 1 ? 'none' : 'inline-block';
    ap3NextBtn.innerHTML = step === 4 ? 'Review & Submit <i class="ri-file-search-line"></i>' : 'Next <i class="ri-arrow-right-line"></i>';
  }

  function setRequiredWithin(container, isRequired) {
    if (!container) return;
    container.querySelectorAll('input, select, textarea').forEach(el => {
      if (el.type === 'hidden') return;
      if (el.type === 'file') return;

      if (isRequired) {
        if (el.dataset.req === "1") el.setAttribute('required', 'required');
      } else {
        if (el.dataset.req === "1") el.removeAttribute('required');
      }
    });

    // files with data-req=1 are required for the scope
    container.querySelectorAll('input[type="file"][data-req="1"]').forEach(f => {
      if (isRequired) f.setAttribute('required','required');
      else f.removeAttribute('required');
    });
  }

  function applyScopeVisibility(scope) {
    const locals = document.querySelectorAll('.scope-local');
    const foreigners = document.querySelectorAll('.scope-foreign');

    locals.forEach(el => el.style.display = (scope === 'local') ? '' : 'none');
    foreigners.forEach(el => el.style.display = (scope === 'foreign') ? '' : 'none');

    locals.forEach(el => setRequiredWithin(el, scope === 'local'));
    foreigners.forEach(el => setRequiredWithin(el, scope === 'foreign'));
  }

  function visible(el){
    if (!el) return false;
    if (el.closest('.scope-local') && document.getElementById('ap3_scope').value !== 'local') return false;
    if (el.closest('.scope-foreign') && document.getElementById('ap3_scope').value !== 'foreign') return false;
    if (el.closest('.employment-only') && currentEmploymentType() !== 'employed') return false;
    if (el.closest('.freelancer-only') && currentEmploymentType() !== 'freelancer') return false;
    
    if (el.offsetParent === null) {
      // File inputs are often display:none but their container is visible
      if (el.type === 'file') {
        const parent = el.closest('.form-field') || el.parentElement;
        return parent ? parent.offsetParent !== null : false;
      }
      return false;
    }
    return true;
  }

  function currentEmploymentType(){
    return document.querySelector('input[name="employment_type"]:checked')?.value || '';
  }

  function validateFilesForStep4(){
    const scope = document.getElementById('ap3_scope').value;

    const passportPhoto = document.querySelector('input[name="passport_photo"]');
    if (!passportPhoto?.files?.[0]) return 'Please upload or take your passport photo.';

    if (scope === 'local') {
      const idScan = document.querySelector('input[name="id_scan"]');
      if (!idScan?.files?.[0]) return 'Please upload your National ID Scan.';

      const empType = currentEmploymentType();
      if (empType === 'employed') {
        const emp = document.querySelector('input[name="employment_letter"]');
        if (!emp?.files?.[0]) return 'Please upload Employment Letter (Employed applicants).';
      }
      if (empType === 'freelancer') {
        const ref = document.querySelector('input[name="reference_letter"]');
        if (!ref?.files?.[0]) return 'Please upload Reference/Testimonial/Affidavit (Freelancers).';
      }
    }

    if (scope === 'foreign') {
      const bio = document.querySelector('input[name="passport_biodata_page"]');
      const clr = document.querySelector('input[name="clearance_letter"]');
      if (!bio?.files?.[0]) return 'Please upload Passport Bio Data Page.';
      if (!clr?.files?.[0]) return 'Please upload Clearance Letter.';
    }

    return '';
  }

  function ap3ValidateStep(step){
    const scope = document.getElementById('ap3_scope').value;
    if(step === 1){
      if(!scope){ alert('Please select an applicant type (Local or Foreign Media Practitioner)'); return false; }
    }

    const currentContent = ap3StepContents[step-1];

    // Required (text/select/radio) validation
    const required = currentContent.querySelectorAll('[required]');
    for(const field of required){
      if (!visible(field)) continue;

      if(field.type === 'radio'){
        const group = currentContent.querySelectorAll(`input[name="${field.name}"]`);
        const anyChecked = Array.from(group).some(r => visible(r) && r.checked);
        if(!anyChecked){ alert('Please select an option.'); return false; }
      } else if (field.type !== 'file') {
        if(!String(field.value || '').trim()){
          alert('Please complete all required fields.');
          field.focus?.();
          return false;
        }
      }
    }

    // Step 3: Referees required (3 fixed rows)
    if (step === 3) {
      for (let i=1;i<=3;i++){
        const n = document.querySelector(`[name="referee_name_${i}"]`);
        const a = document.querySelector(`[name="referee_address_${i}"]`);
        const p = document.querySelector(`[name="referee_phone_${i}"]`);
        if (!n?.value?.trim() || !a?.value?.trim() || !p?.value?.trim()){
          alert('Please complete all 3 referees (Name, Address, Phone).');
          return false;
        }
      }
    }

    // Step 4: file rules
    if (step === 4) {
      const msg = validateFilesForStep4();
      if (msg) { alert(msg); return false; }
    }

    // “Others specify” rules
    if (step === 3) {
      const mt = document.querySelector('select[name="medium_type"]')?.value;
      const des = document.querySelector('select[name="designation"]')?.value;
      if (mt === 'Others' && !document.querySelector('input[name="medium_type_other"]')?.value?.trim()){
        alert('Please specify the medium type (Others).'); return false;
      }
      if (des === 'Others' && !document.querySelector('input[name="designation_other"]')?.value?.trim()){
        alert('Please specify the designation (Others).'); return false;
      }
    }

    return true;
  }

  // ===== Repeaters (Qualifications) =====
  function rowHtmlHighestAcademic(idx){
    return `
      <div class="d-flex gap-2 mb-2 ap3-row" data-row="${idx}">
        <input class="form-control" name="highest_academic_year_${idx}" placeholder="Year" style="max-width:120px;">
        <input class="form-control" name="highest_academic_institution_${idx}" placeholder="Name of Institution">
        <input class="form-control" name="highest_academic_qualification_${idx}" placeholder="Qualification">
        <button type="button" class="btn btn-light btn-sm ap3RemoveRow">Remove</button>
      </div>
    `;
  }

  function rowHtmlProfessionalQual(idx){
    return `
      <div class="d-flex gap-2 mb-2 ap3-row" data-row="${idx}">
        <input class="form-control" name="professional_year_${idx}" placeholder="Year" style="max-width:120px;">
        <input class="form-control" name="professional_institution_${idx}" placeholder="Name of Institution">
        <input class="form-control" name="professional_qualification_${idx}" placeholder="Qualification">
        <button type="button" class="btn btn-light btn-sm ap3RemoveRow">Remove</button>
      </div>
    `;
  }

  function initRepeater(containerId, addBtnId, rowBuilder){
    const container = document.getElementById(containerId);
    const btn = document.getElementById(addBtnId);
    if(!container || !btn) return null;

    let idx = 1;

    function wireRemoveButtons() {
      container.querySelectorAll('.ap3RemoveRow').forEach(rm => {
        rm.onclick = (e) => e.target.closest('.ap3-row').remove();
      });
    }

    function addRow(){
      container.insertAdjacentHTML('beforeend', rowBuilder(idx++));
      wireRemoveButtons();
    }

    btn.addEventListener('click', addRow);

    return {
      addRow,
      clear: () => { container.innerHTML = ''; idx = 1; },
      setCount: (count) => { container.innerHTML=''; idx=1; for(let i=0;i<count;i++) addRow(); },
    };
  }

  // ===== Upload UI =====
  function setupUploads(root){
    root.querySelectorAll('.upload-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = btn.closest('.upload-area')?.querySelector('input[type="file"]');
        input?.click();
      });
    });

    root.querySelectorAll('.upload-area input[type="file"]').forEach(input => {
      input.addEventListener('change', () => {
        const file = input.files[0];
        const area = input.closest('.upload-area');
        const list = area?.parentElement?.querySelector('.uploaded-files');
        if(!file || !area || !list) return;

        area.style.borderColor = '#10b981';
        area.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';

        const fileName = file.name.length > 30 ? file.name.slice(0,30)+'...' : file.name;
        const fileSize = (file.size/1024).toFixed(1)+' KB';

        list.innerHTML = `
          <div class="uploaded-file d-flex align-items-center justify-content-between p-2 border rounded mb-2">
            <div class="d-flex align-items-center gap-2">
              <i class="ri-file-text-line file-icon"></i>
              <div>
                <div class="file-name fw-semibold" style="font-size:13px;">${fileName}</div>
                <div class="file-size text-muted" style="font-size:11px;">${fileSize}</div>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-light">Remove</button>
          </div>
        `;

        list.querySelector('button').addEventListener('click', () => {
          input.value = '';
          list.innerHTML = '';
          area.style.borderColor = '';
          area.style.backgroundColor = '';
        });
      });
    });
  }

  // ===== Fixed 3 referees =====
  function buildReferees(){
    const wrap = document.getElementById('ap3RefereeFixedRows');
    if (!wrap) return;
    let html = '';
    for (let i=1;i<=3;i++){
      html += `
        <div class="d-flex gap-2 mb-2">
          <input class="form-control" name="referee_name_${i}" placeholder="Name" required>
          <input class="form-control" name="referee_address_${i}" placeholder="Address" required>
          <input class="form-control" name="referee_phone_${i}" placeholder="Phone" style="max-width:220px;" required>
        </div>
      `;
    }
    wrap.innerHTML = html;
  }

  // ===== Employment toggle show/hide =====
  function applyEmploymentVisibility() {
    const type = currentEmploymentType();
    document.querySelectorAll('.employment-only, .employment-letter-only').forEach(el => {
      el.style.display = (type === 'employed') ? '' : 'none';
    });
    document.querySelectorAll('.freelancer-only').forEach(el => {
      el.style.display = (type === 'freelancer') ? '' : 'none';
    });

    // Upload requirements on local only
    const empInput = document.querySelector('input[name="employment_letter"]');
    const refInput = document.querySelector('input[name="reference_letter"]');

    if (empInput) empInput.required = (document.getElementById('ap3_scope').value === 'local' && type === 'employed');
    if (refInput) refInput.required = (document.getElementById('ap3_scope').value === 'local' && type === 'freelancer');
  }

  // ===== Others specify toggle =====
  function applyOthersVisibility(){
    const mt = document.querySelector('select[name="medium_type"]')?.value;
    const des = document.querySelector('select[name="designation"]')?.value;

    const mtWrap = document.getElementById('ap3OtherMediumWrap');
    const desWrap = document.getElementById('ap3OtherDesignationWrap');

    if (mtWrap) mtWrap.style.display = (mt === 'Others') ? '' : 'none';
    if (desWrap) desWrap.style.display = (des === 'Others') ? '' : 'none';

    const mtOther = document.querySelector('input[name="medium_type_other"]');
    const desOther = document.querySelector('input[name="designation_other"]');
    if (mtOther) mtOther.required = (mt === 'Others');
    if (desOther) desOther.required = (des === 'Others');
  }

  // ===== Review =====
  function getUploadedFiles() {
    const files = [];
    const fileInputs = document.querySelectorAll('#ap3Form input[type="file"]');
    fileInputs.forEach(input => {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        files.push({
          name: input.name,
          fileName: file.name,
          size: (file.size / 1024).toFixed(1) + ' KB'
        });
      }
    });
    return files;
  }

  function extractRows(prefix, formData, columns) {
    const rows = [];
    const indices = new Set();
    Object.keys(formData || {}).forEach(k => {
      const m = k.match(new RegExp('^' + prefix + '[a-zA-Z_]+_([0-9]+)$'));
      if (m) indices.add(parseInt(m[1], 10));
    });
    const sorted = Array.from(indices).sort((a,b)=>a-b);
    for (const i of sorted) {
      const row = {};
      let hasAny = false;
      for (const c of columns) {
        const key = `${prefix}${c}_${i}`;
        row[c] = (formData[key] || '').trim();
        if (row[c]) hasAny = true;
      }
      if (hasAny) rows.push(row);
    }
    return rows;
  }

  function showReviewModal() {
    const formData = getFormData();
    const scope = formData.journalist_scope === 'foreign' ? 'Foreign Media Practitioner' : 'Local Media Practitioner';
    const empType = formData.employment_type || '-';

    const highest = extractRows('highest_academic_', formData, ['year','institution','qualification']);
    const prof = extractRows('professional_', formData, ['year','institution','qualification']);

    const refs = [];
    for (let i=1;i<=3;i++){
      refs.push({
        name: formData[`referee_name_${i}`] || '-',
        address: formData[`referee_address_${i}`] || '-',
        phone: formData[`referee_phone_${i}`] || '-',
      });
    }

    const files = getUploadedFiles();

    const highestHtml = highest.length ? `<ul class="mb-0">${highest.map(r=>`<li>${r.year||'-'} — ${r.institution||'-'} — ${r.qualification||'-'}</li>`).join('')}</ul>` : `<div class="text-muted">No rows provided.</div>`;
    const profHtml = prof.length ? `<ul class="mb-0">${prof.map(r=>`<li>${r.year||'-'} — ${r.institution||'-'} — ${r.qualification||'-'}</li>`).join('')}</ul>` : `<div class="text-muted">No rows provided.</div>`;

    const filesHtml = files.length ? `
      <div class="list-group">
        ${files.map(f => `
          <div class="list-group-item d-flex align-items-center">
            <i class="ri-file-text-line text-primary me-3 fs-4"></i>
            <div>
              <div class="fw-semibold">${f.name.replaceAll('_',' ')}</div>
              <small class="text-muted">${f.fileName} (${f.size})</small>
            </div>
            <i class="ri-checkbox-circle-fill text-success ms-auto"></i>
          </div>
        `).join('')}
      </div>` : `<div class="alert alert-danger mb-0">No documents uploaded.</div>`;
    // Passport photo preview (uploaded or taken)
    const photoInput = document.querySelector('input[name="passport_photo"]');
    const photoFile = (photoInput && photoInput.files && photoInput.files[0]) ? photoInput.files[0] : null;
    const photoPreviewHtml = photoFile ? (() => {
      const isImg = (photoFile.type || '').startsWith('image/');
      if (!isImg) return `<div class="text-muted">Passport photo selected (${photoFile.name})</div>`;
      const url = URL.createObjectURL(photoFile);
      return `
        <div class="d-flex align-items-start gap-3">
          <img src="${url}" alt="Passport photo preview" style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0;">
          <div>
            <div class="fw-semibold">Passport Photo</div>
            <small class="text-muted">${photoFile.name} (${formatFileSize(photoFile.size)})</small>
            <div class="text-muted mt-1">If this image looks wrong, go back and re-upload / retake.</div>
          </div>
        </div>`;
    })() : `<div class="alert alert-danger mb-0">No passport photo selected.</div>`;


    let personalExtra = '';
    if (formData.journalist_scope === 'local') {
      personalExtra = `
        <div class="col-6"><p><strong>National Reg. No:</strong> ${formData.national_reg_no || '-'}</p></div>
      `;
    } else {
      personalExtra = `
        <div class="col-6"><p><strong>Passport No:</strong> ${formData.passport_no || '-'}</p></div>
        <div class="col-6"><p><strong>Date of Expiry:</strong> ${formData.passport_expiry || '-'}</p></div>
        <div class="col-6"><p><strong>Issued at:</strong> ${formData.passport_issued_at || '-'}</p></div>
        <div class="col-6"><p><strong>First time in Zimbabwe:</strong> ${formData.first_time_in_zim || '-'}</p></div>
        <div class="col-6"><p><strong>Last here:</strong> ${formData.last_in_zim_when || '-'}</p></div>
        <div class="col-12"><p><strong>Address in Zimbabwe:</strong> ${formData.address_in_zimbabwe || '-'}</p></div>
      `;
    }

    const employmentExtraForeign = (formData.journalist_scope === 'foreign' && empType === 'employed') ? `
      <div class="col-6"><p><strong>Country based:</strong> ${formData.journalist_based_country || '-'}</p></div>
      <div class="col-6"><p><strong>Arrived on:</strong> ${formData.arrived_on || '-'}</p></div>
      <div class="col-6"><p><strong>By Air/Road:</strong> ${formData.arrival_mode || '-'}</p></div>
      <div class="col-6"><p><strong>Port of Entry:</strong> ${formData.port_of_entry || '-'}</p></div>
      <div class="col-6"><p><strong>Departing on:</strong> ${formData.departing_on || '-'}</p></div>
      <div class="col-12"><p><strong>Special assignment:</strong> ${formData.special_assignment || '-'}</p></div>
    ` : '';

    const reviewHtml = `
      <div class="review-section">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-user-line me-2"></i>Applicant Type</h6>
        <p><strong>Type:</strong> ${scope}</p>
        <p><strong>Employment Status:</strong> ${empType}</p>
      </div>

      <div class="review-section mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-profile-line me-2"></i>Personal Details</h6>
        <div class="row">
          <div class="col-6"><p><strong>Title:</strong> ${formData.title || '-'}</p></div>
          <div class="col-6"><p><strong>Surname:</strong> ${formData.surname || '-'}</p></div>
          <div class="col-6"><p><strong>Name:</strong> ${formData.first_name || '-'}</p></div>
          <div class="col-6"><p><strong>Other:</strong> ${formData.other_names || '-'}</p></div>
          <div class="col-6"><p><strong>Date of Birth:</strong> ${formData.dob || '-'}</p></div>
          <div class="col-6"><p><strong>Place/Country of Birth:</strong> ${formData.birth_place || '-'}</p></div>
          <div class="col-6"><p><strong>Marital Status:</strong> ${formData.marital_status || '-'}</p></div>
          <div class="col-6"><p><strong>Sex:</strong> ${formData.gender || '-'}</p></div>
          <div class="col-6"><p><strong>Nationality:</strong> ${formData.nationality || '-'}</p></div>
          ${personalExtra}
          <div class="col-6"><p><strong>Driver’s Licence No:</strong> ${formData.drivers_licence_no || '-'}</p></div>
          <div class="col-12"><p><strong>Residential Address:</strong> ${formData.address || '-'}</p></div>
          <div class="col-6"><p><strong>Phone:</strong> ${(formData.phone_country_code || '') + ' ' + (formData.phone || '-')}</p></div>
          <div class="col-6"><p><strong>Email:</strong> ${formData.email || '-'}</p></div>
        </div>
      </div>

      ${formData.journalist_scope === 'local' ? `
        <div class="review-section mt-4">
          <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-graduation-cap-line me-2"></i>Qualifications</h6>
          <div class="mt-2"><strong>Highest Academic:</strong>${highestHtml}</div>
          <div class="mt-2"><strong>Professional Qualifications:</strong>${profHtml}</div>
        </div>
      ` : ''}

      <div class="review-section mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-briefcase-line me-2"></i>Employment</h6>
        <div class="row">
          <div class="col-6"><p><strong>Type of Medium:</strong> ${formData.medium_type || '-'}</p></div>
          <div class="col-6"><p><strong>Designation:</strong> ${formData.designation || '-'}</p></div>
          ${(formData.medium_type === 'Others') ? `<div class="col-12"><p><strong>Medium (Other):</strong> ${formData.medium_type_other || '-'}</p></div>` : ``}
          ${(formData.designation === 'Others') ? `<div class="col-12"><p><strong>Designation (Other):</strong> ${formData.designation_other || '-'}</p></div>` : ``}

          ${empType === 'employed' ? `
            <div class="col-6"><p><strong>Media Org:</strong> ${formData.media_org || '-'}</p></div>
            <div class="col-6"><p><strong>Physical Address:</strong> ${formData.media_org_address || '-'}</p></div>
            <div class="col-6"><p><strong>Phone:</strong> ${formData.media_org_phone || '-'}</p></div>
            <div class="col-6"><p><strong>Email:</strong> ${formData.media_org_email || '-'}</p></div>
            <div class="col-6"><p><strong>Editor/Publisher:</strong> ${formData.editor_publisher_name || '-'}</p></div>
            <div class="col-6"><p><strong>Immediate Supervisor:</strong> ${formData.immediate_supervisor || '-'}</p></div>
            <div class="col-6"><p><strong>String for Orgs:</strong> ${formData.string_for_orgs || '-'}</p></div>
            <div class="col-6"><p><strong>Details:</strong> ${formData.string_for_details || '-'}</p></div>
            ${employmentExtraForeign}
          ` : `
            <div class="col-12"><div class="alert alert-light border mb-0">Freelancer selected — organisation fields were not required.</div></div>
          `}
        </div>
      </div>

      <div class="review-section mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-group-line me-2"></i>Referees</h6>
        <ul class="mb-0">
          ${refs.map((r,idx)=>`<li><strong>Referee ${idx+1}:</strong> ${r.name} — ${r.address} — ${r.phone}</li>`).join('')}
        </ul>
      </div>

      ${formData.journalist_scope === 'local' ? `
        <div class="review-section mt-4">
          <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-map-pin-line me-2"></i>Collection Office</h6>
          <p>${document.querySelector('select[name="collection_region"]')?.selectedOptions[0]?.text || '-'}</p>
        </div>
      ` : ''}

      <div class="review-section mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-file-upload-line me-2"></i>Uploaded Documents</h6>
        ${filesHtml}
      </div>

      <div class="review-section mt-4">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="ri-shield-check-line me-2"></i>Declaration</h6>
        <p>I declare that all the information given above, to the best of my knowledge is true and complete.</p>
        <p><strong>Date:</strong> ${formData.declaration_date || '-'}</p>
      </div>

      <div class="alert alert-warning mt-4">
        <i class="ri-information-line me-2"></i>
        Please review all information carefully before submitting.
      </div>
    `;

    document.getElementById('ap3ReviewContent').innerHTML = reviewHtml;
    const modalEl = document.getElementById('ap3ReviewModal');
    // Support both global bootstrap (window.bootstrap) and legacy bootstrap global.
    const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
    if (modalEl && bs?.Modal) {
      const modal = bs.Modal.getOrCreateInstance ? bs.Modal.getOrCreateInstance(modalEl) : new bs.Modal(modalEl);
      modal.show();
    } else {
      // Fallback if Bootstrap JS isn't loaded
      alert('Review is ready, but the modal could not be opened. Please refresh the page and try again.');
    }
  }

  async function saveDraft() {
  const textFormData = getFormData();
  const scope = document.getElementById('ap3_scope').value;
  const region = document.querySelector('select[name="collection_region"]')?.value || 'harare';

  try {
    ap3SaveDraftBtn.disabled = true;
    ap3SaveDraftBtn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';

    const fd = new FormData();
    fd.append('journalist_scope', scope || '');
    fd.append('collection_region', region);
    fd.append('form_data', JSON.stringify(textFormData));

    // attach files too
    document.querySelectorAll('#ap3Form input[type="file"]').forEach(input => {
      if (!visible(input)) return;
      if (input.files && input.files[0]) fd.append(input.name, input.files[0]);
    });

    const response = await fetch('{{ route("accreditation.saveDraft") }}', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken },
      body: fd,
    });

    const result = await response.json();

    if (result.success) {
      alert('Draft saved successfully! Reference: ' + (result.reference || ''));
    } else {
      alert('Failed to save draft. Please try again.');
    }
  } catch (e) {
    console.error(e);
    alert('An error occurred while saving the draft.');
  } finally {
    ap3SaveDraftBtn.disabled = false;
    ap3SaveDraftBtn.innerHTML = '<i class="ri-save-line"></i> Save Draft';
  }
}


  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  async function submitApplication() {
    const textFormData = getFormData();
    const scope = document.getElementById('ap3_scope').value;
    const region = document.querySelector('select[name="collection_region"]')?.value || 'harare';

    const confirmBtn = document.getElementById('ap3ConfirmSubmitBtn');

    const submitData = new FormData();
    submitData.append('journalist_scope', scope);
    submitData.append('collection_region', region);
    submitData.append('form_data', JSON.stringify(textFormData));

    const fileInputs = document.querySelectorAll('#ap3Form input[type="file"]');
    fileInputs.forEach(input => {
      // Use the improved visible check that handles hidden file inputs
      if (!visible(input)) return;
      if (input.files && input.files[0]) submitData.append(input.name, input.files[0]);
    });

    try {
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Submitting...';

      const submitUrl = @json(isset($draft) && !$draft->is_draft && ($draft->status ?? null) === \App\Models\Application::CORRECTION_REQUESTED
        ? route('accreditation.applications.resubmit', $draft)
        : route('accreditation.submit'));

      const response = await fetch(submitUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: submitData,
      });

      const responseText = await response.text();
      let result;
      try {
          result = JSON.parse(responseText);
      } catch (e) {
          console.error('Submission failed (raw):', responseText);
          throw new Error('Server returned an invalid response (' + response.status + ').');
      }

      if (response.ok && result.success) {
        bootstrap.Modal.getInstance(document.getElementById('ap3ReviewModal')).hide();
        alert('Application submitted successfully! Reference: ' + result.reference);
        window.location.href = "{{ route('accreditation.home') }}";
      } else {
        // Handle Laravel validation errors or manual 422s
        const msg = result.message || (result.errors ? Object.values(result.errors).flat().join('\n') : 'Please check all required fields.');
        alert('Submission failed: ' + msg);
      }
    } catch (error) {
      console.error(error);
      alert('An error occurred while submitting the application: ' + error.message);
    } finally {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="ri-send-plane-line me-2"></i>Confirm & Submit';
    }
  }

  // ===== Camera capture =====
  let ap3Stream = null;
  let ap3CapturedBlob = null;

  async function startCamera(){
    const video = document.getElementById('ap3CamVideo');
    ap3CapturedBlob = null;

    try {
      ap3Stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
      video.srcObject = ap3Stream;
    } catch (e) {
      alert('Camera access failed. Please allow camera permission or upload a photo instead.');
      throw e;
    }
  }

  function stopCamera(){
    if (ap3Stream) {
      ap3Stream.getTracks().forEach(t => t.stop());
      ap3Stream = null;
    }
  }

  function blobToFile(blob, fileName){
    return new File([blob], fileName, { type: blob.type || 'image/png' });
  }

  function setFileInput(input, file){
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
  }

  // ===== Init =====
  document.querySelectorAll('#new-application-page .app-type-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('#new-application-page .app-type-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      const type = card.dataset.type;
      document.getElementById('ap3_scope').value = type;
      applyScopeVisibility(type);
      applyEmploymentVisibility();
    });
  });

  ap3PrevBtn.addEventListener('click', () => {
    ap3CurrentStep = Math.max(1, ap3CurrentStep - 1);
    ap3ShowStep(ap3CurrentStep);
  });

  ap3NextBtn.addEventListener('click', () => {
    if(!ap3ValidateStep(ap3CurrentStep)) return;

    if(ap3CurrentStep === 4){
      showReviewModal();
      return;
    }

    ap3CurrentStep = Math.min(4, ap3CurrentStep + 1);
    ap3ShowStep(ap3CurrentStep);
  });

  ap3SaveDraftBtn.addEventListener('click', saveDraft);
  document.getElementById('ap3ConfirmSubmitBtn').addEventListener('click', submitApplication);

  document.addEventListener('DOMContentLoaded', () => {
    ap3ShowStep(1);
    setupUploads(document.getElementById('new-application-page'));
    buildReferees();

    // Default date inputs to today if empty
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('#new-application-page input[type="date"]').forEach(i => { if(!i.value) i.value = today; });

    // Qualifications repeaters (Local only)
    const highestRep = initRepeater('ap3HighestAcademicRows','ap3AddHighestAcademicRow', rowHtmlHighestAcademic);
    const profRep = initRepeater('ap3ProfessionalQualRows','ap3AddProfessionalQualRow', rowHtmlProfessionalQual);
    if (highestRep) highestRep.addRow();
    if (profRep) profRep.addRow();

    // Hide local/foreign by default until selection
    applyScopeVisibility(document.getElementById('ap3_scope').value || 'local');

    // Employment toggles
    document.querySelectorAll('input[name="employment_type"]').forEach(r => r.addEventListener('change', applyEmploymentVisibility));
    applyEmploymentVisibility();

    // Others toggles
    document.querySelector('select[name="medium_type"]')?.addEventListener('change', applyOthersVisibility);
    document.querySelector('select[name="designation"]')?.addEventListener('change', applyOthersVisibility);
    applyOthersVisibility();

    // Camera modal handlers
    const takeBtn = document.getElementById('ap3TakePhotoBtn');
    const captureBtn = document.getElementById('ap3CaptureBtn');
    const retakeBtn = document.getElementById('ap3RetakeBtn');
    const useBtn = document.getElementById('ap3UsePhotoBtn');
    const previewWrap = document.getElementById('ap3CapturePreview');
    const previewImg = document.getElementById('ap3PreviewImg');
    const video = document.getElementById('ap3CamVideo');
    const canvas = document.getElementById('ap3CamCanvas');

    takeBtn?.addEventListener('click', async () => {
      const modalEl = document.getElementById('ap3CameraModal');
      const modal = new bootstrap.Modal(modalEl);
      modal.show();

      previewWrap.style.display = 'none';
      retakeBtn.style.display = 'none';
      captureBtn.style.display = '';
      await startCamera();
    });

    document.getElementById('ap3CameraCloseBtn')?.addEventListener('click', () => {
      stopCamera();
    });

    document.getElementById('ap3CameraModal')?.addEventListener('hidden.bs.modal', () => {
      stopCamera();
    });

    captureBtn?.addEventListener('click', () => {
      const w = video.videoWidth || 640;
      const h = video.videoHeight || 480;
      canvas.width = w;
      canvas.height = h;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, w, h);

      canvas.toBlob((blob) => {
        if (!blob) return;
        ap3CapturedBlob = blob;

        previewImg.src = URL.createObjectURL(blob);
        previewWrap.style.display = '';
        retakeBtn.style.display = '';
        captureBtn.style.display = 'none';
      }, 'image/png', 0.92);
    });

    retakeBtn?.addEventListener('click', () => {
      previewWrap.style.display = 'none';
      retakeBtn.style.display = 'none';
      captureBtn.style.display = '';
      ap3CapturedBlob = null;
    });

    useBtn?.addEventListener('click', () => {
      if (!ap3CapturedBlob) return;

      const input = document.querySelector('input[name="passport_photo"]');
      const file = blobToFile(ap3CapturedBlob, 'passport_photo.png');
      setFileInput(input, file);

      const modal = bootstrap.Modal.getInstance(document.getElementById('ap3CameraModal'));
      modal.hide();
      stopCamera();
    });

    @if(isset($draft) && $draft)
      const draftData = @json($draft->form_data ?? []);
      if (draftData) {
        // restore scope
        @if($draft->journalist_scope)
          const scopeCard = document.querySelector(`.app-type-card[data-type="{{ $draft->journalist_scope }}"]`);
          if (scopeCard) scopeCard.click();
        @endif

        // restore text/select/textarea/radio
        Object.keys(draftData).forEach(key => {
          const field = document.querySelector(`[name="${key}"]`);
          if (!field) return;
          if (field.type === 'radio') {
            const radio = document.querySelector(`[name="${key}"][value="${draftData[key]}"]`);
            if (radio) radio.checked = true;
          } else {
            field.value = draftData[key];
          }
        });

        applyEmploymentVisibility();
        applyOthersVisibility();
      }
    @endif
  });
</script>
@endpush
