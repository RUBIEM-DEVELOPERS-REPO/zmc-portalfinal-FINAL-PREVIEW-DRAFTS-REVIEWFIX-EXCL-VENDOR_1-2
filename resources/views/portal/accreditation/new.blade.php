@extends('layouts.portal')

@section('title', 'New Accreditation (AP3)')

@section('content')
<div id="new-accreditation-page">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">
      New Accreditation — Media Practitioner (AP3) — Digital Form
    </h4>
    <div class="d-flex gap-2">
       <a href="{{ route('accreditation.home') }}" class="btn btn-outline-secondary btn-sm">
         <i class="ri-arrow-left-line me-1"></i> Back to Dashboard
       </a>
    </div>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP3 — Application for Accreditation as a Media Practitioner</h1>
      <p class="m-0">
        Complete this digital AP3 form and upload required documents. Ensure all details match your official identification.
      </p>
    </div>

    <div class="form-steps-container">
      {{-- PROGRESS --}}
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Applicant Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Personal Info</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Qualifications</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Professional</div>
          </div>
          <div class="step" data-step="5">
            <div class="step-number">5</div>
            <div class="step-label">Criminal Record</div>
          </div>
          <div class="step" data-step="6">
            <div class="step-number">6</div>
            <div class="step-label">Referees</div>
          </div>
          <div class="step" data-step="7">
            <div class="step-number">7</div>
            <div class="step-label">Uploads</div>
          </div>
          <div class="step" data-step="8">
            <div class="step-number">8</div>
            <div class="step-label">Declaration</div>
          </div>
        </div>
      </div>

      <form id="ap3Form" onsubmit="return false;" enctype="multipart/form-data">
        @csrf
        {{-- STEP 1: APPLICANT TYPE --}}
        <div class="step-content active" id="ap3-step-1">
          <h3 class="step-title">Choose Applicant Type</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Select whether you are applying as a Local (Zimbabwean) or Foreign Media Practitioner.
          </div>

          <div class="app-type-container">
            <div class="app-type-cards">
              <div class="app-type-card" data-type="local">
                <i class="ri-map-pin-user-line"></i>
                <h4>Local Media Practitioner</h4>
                <p>Zimbabwean citizen or permanent resident.</p>
              </div>

              <div class="app-type-card" data-type="foreign">
                <i class="ri-global-line"></i>
                <h4>Foreign Media Practitioner</h4>
                <p>Visiting journalists or foreign correspondents.</p>
              </div>
            </div>
          </div>

          <input type="hidden" name="journalist_scope" id="ap3_journalist_scope" value="{{ $draft->journalist_scope ?? ($draft->form_data['journalist_scope'] ?? '') }}" required>
        </div>

        {{-- STEP 2: PERSONAL INFO --}}
        <div class="step-content" id="ap3-step-2">
          <h3 class="step-title">Personal Identification Details</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Enter your details exactly as they appear on your National ID or Passport.
          </div>

          <div class="section-card">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Title</label>
                <select class="form-control" name="title" required>
                  <option value="">Select</option>
                  @foreach(['Mr','Mrs','Ms','Miss','Dr','Prof','Rev'] as $t)
                    <option @selected(($draft->form_data['title'] ?? '') === $t)>{{ $t }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-field">
                <label class="form-label required">Gender</label>
                <select class="form-control" name="gender" required>
                  <option value="">Select</option>
                  <option value="male" @selected(($draft->form_data['gender'] ?? '') === 'male')>Male</option>
                  <option value="female" @selected(($draft->form_data['gender'] ?? '') === 'female')>Female</option>
                  <option value="other" @selected(($draft->form_data['gender'] ?? '') === 'other')>Other</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">First Name(s)</label>
                <input type="text" class="form-control" name="first_name" value="{{ $draft->form_data['first_name'] ?? '' }}" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Surname</label>
                <input type="text" class="form-control" name="surname" value="{{ $draft->form_data['surname'] ?? '' }}" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Other Name(s)</label>
                <input type="text" class="form-control" name="other_names" value="{{ $draft->form_data['other_names'] ?? '' }}" placeholder="Middle names or aliases">
              </div>
              <div class="form-field">
                <label class="form-label">Driver's Licence Number</label>
                <input type="text" class="form-control" name="drivers_licence" value="{{ $draft->form_data['drivers_licence'] ?? '' }}" placeholder="Optional">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required" style="display:block;">Date of Birth</label>
                <div class="d-flex gap-2">
                  @php 
                    $dob = $draft->form_data['dob'] ?? '';
                    $d_y = $dob ? date('Y', strtotime($dob)) : '';
                    $d_m = $dob ? date('m', strtotime($dob)) : '';
                    $d_d = $dob ? date('d', strtotime($dob)) : '';
                  @endphp
                  <select class="form-control px-2" id="dob_date" required>
                    <option value="">DD</option>
                    @for($i=1; $i<=31; $i++) <option value="{{ sprintf('%02d', $i) }}" @selected($d_d == sprintf('%02d', $i))>{{ sprintf('%02d', $i) }}</option> @endfor
                  </select>
                  <select class="form-control px-2" id="dob_month" required>
                    <option value="">MM</option>
                    @for($i=1; $i<=12; $i++) <option value="{{ sprintf('%02d', $i) }}" @selected($d_m == sprintf('%02d', $i))>{{ date('M', mktime(0, 0, 0, $i, 1)) }}</option> @endfor
                  </select>
                  <select class="form-control px-2" id="dob_year" required>
                    <option value="">YYYY</option>
                    @for($i=date('Y'); $i>=1920; $i--) <option value="{{ $i }}" @selected($d_y == $i)>{{ $i }}</option> @endfor
                  </select>
                </div>
                <input type="hidden" name="dob" id="real_dob" value="{{ $dob }}" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Place of Birth</label>
                <input type="text" class="form-control" name="birth_place" value="{{ $draft->form_data['birth_place'] ?? '' }}" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Nationality</label>
                <input type="text" class="form-control" name="nationality" value="{{ $draft->form_data['nationality'] ?? '' }}" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Marital Status</label>
                <select class="form-control" name="marital_status" required>
                  <option value="">Select</option>
                  @foreach(['Single','Married','Divorced','Widowed'] as $m)
                    <option @selected(($draft->form_data['marital_status'] ?? '') === $m)>{{ $m }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-row local-fields">
              <div class="form-field">
                <label class="form-label required">National ID Number</label>
                <input type="text" class="form-control" name="national_reg_no" id="ap3_national_reg_no" placeholder="e.g. 63-123456A78" value="{{ $draft->form_data['national_reg_no'] ?? '' }}">
              </div>
              <div class="form-field">
                <label class="form-label">Passport Number (Optional for locals)</label>
                <input type="text" class="form-control" name="passport_number" value="{{ $draft->form_data['passport_number'] ?? '' }}">
              </div>
            </div>

            <div class="form-row foreign-fields" style="display:none;">
              <div class="form-field">
                <label class="form-label required">Passport Number</label>
                <input type="text" class="form-control" name="passport_no" id="ap3_passport_no" value="{{ $draft->form_data['passport_no'] ?? '' }}">
              </div>
              <div class="form-field">
                <label class="form-label required">Passport Issued At</label>
                <input type="text" class="form-control" name="passport_issued_at" value="{{ $draft->form_data['passport_issued_at'] ?? '' }}">
              </div>
            </div>

            <div class="form-row foreign-fields" style="display:none;">
              <div class="form-field">
                <label class="form-label required">Passport Expiry Date</label>
                <input type="date" class="form-control" name="passport_expiry" value="{{ $draft->form_data['passport_expiry'] ?? '' }}">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Physical Address</label>
                <textarea class="form-control" name="address" rows="2" required>{{ $draft->form_data['address'] ?? '' }}</textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Phone Number</label>
                <div class="d-flex gap-2">
                  <select class="form-control px-2" name="phone_country_code" style="max-width:120px;" required>
                    @include('partials.country-codes-options', ['selected' => $draft->form_data['phone_country_code'] ?? ''])
                  </select>
                  <input type="text" class="form-control" name="phone" value="{{ $draft->form_data['phone'] ?? '' }}" required>
                </div>
              </div>
              <div class="form-field">
                <label class="form-label required">Email Address</label>
                <input type="email" class="form-control" name="email" value="{{ $draft->form_data['email'] ?? '' }}" required>
              </div>
            </div>
            
            <div class="form-row local-fields">
               <div class="form-field">
                <label class="form-label required">Collection Office</label>
                <select class="form-control" name="collection_region" id="ap3_collection_region">
                  <option value="">Select collection office</option>
                  @foreach(['harare'=>'Harare Regional Office', 'bulawayo'=>'Bulawayo Regional Office', 'mutare'=>'Mutare Regional Office', 'masvingo'=>'Masvingo Regional Office', 'gweru'=>'Gweru Terminal', 'chinhoyi'=>'Chinhoyi Terminal'] as $v => $l)
                    <option value="{{ $v }}" @selected(($draft->collection_region ?? ($draft->form_data['collection_region'] ?? '')) === $v)>{{ $l }}</option>
                  @endforeach
                </select>
                <div class="form-hint small text-muted mt-1"><i class="ri-map-pin-line"></i> Choose where you will collect your card.</div>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 3: QUALIFICATIONS --}}
        <div class="step-content" id="ap3-step-3">
          <h3 class="step-title">Qualifications</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Provide details about your academic and professional qualifications.
          </div>

          <div class="section-card">
            <h5 class="mb-3"><i class="ri-graduation-cap-line me-2"></i>Academic Qualifications</h5>
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Highest Academic Qualification</label>
                <select class="form-control" name="highest_academic_qualification" required>
                  <option value="">Select</option>
                  <option value="phd" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'phd')>PhD/Doctorate</option>
                  <option value="masters" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'masters')>Master's Degree</option>
                  <option value="bachelors" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'bachelors')>Bachelor's Degree</option>
                  <option value="diploma" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'diploma')>Diploma</option>
                  <option value="certificate" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'certificate')>Certificate</option>
                  <option value="high_school" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'high_school')>High School</option>
                  <option value="other" @selected(($draft->form_data['highest_academic_qualification'] ?? '') === 'other')>Other</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label">Field of Study</label>
                <input type="text" class="form-control" name="field_of_study" value="{{ $draft->form_data['field_of_study'] ?? '' }}" placeholder="e.g. Journalism, Communications">
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Institution Name</label>
                <input type="text" class="form-control" name="institution_name" value="{{ $draft->form_data['institution_name'] ?? '' }}" placeholder="University/College name">
              </div>
              <div class="form-field">
                <label class="form-label">Year Completed</label>
                <input type="number" class="form-control" name="year_completed" value="{{ $draft->form_data['year_completed'] ?? '' }}" placeholder="e.g. 2020" min="1950" max="{{ date('Y') }}">
              </div>
            </div>
          </div>

          <div class="section-card mt-3">
            <h5 class="mb-3"><i class="ri-award-line me-2"></i>Professional Qualifications</h5>
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Professional Qualification(s)</label>
                <textarea class="form-control" name="professional_qualifications" rows="3" required placeholder="List your professional journalism/media qualifications">{{ $draft->form_data['professional_qualifications'] ?? '' }}</textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Professional Memberships</label>
                <textarea class="form-control" name="professional_memberships" rows="2" placeholder="List any professional journalism associations you belong to">{{ $draft->form_data['professional_memberships'] ?? '' }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 4: PROFESSIONAL INFO --}}
        <div class="step-content" id="ap3-step-4">
          <h3 class="step-title">Professional Details</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Provide details about your current employment and media specialization.
          </div>

          <div class="section-card">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Employment Type</label>
                <select class="form-control" name="employment_type" id="ap3_employment_type" required>
                  <option value="">Select</option>
                  <option value="employed" @selected(($draft->form_data['employment_type'] ?? '') === 'employed')>Employed</option>
                  <option value="freelance" @selected(($draft->form_data['employment_type'] ?? '') === 'freelance')>Freelance</option>
                  <option value="unemployed" @selected(($draft->form_data['employment_type'] ?? '') === 'unemployed')>Unemployed / Student</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label required">Medium Type</label>
                <select class="form-control" name="medium_type" required>
                  <option value="">Select</option>
                  <option value="news_agency" @selected(($draft->form_data['medium_type'] ?? '') === 'news_agency')>News Agency</option>
                  <option value="newspaper" @selected(($draft->form_data['medium_type'] ?? '') === 'newspaper')>Newspaper</option>
                  <option value="television" @selected(($draft->form_data['medium_type'] ?? '') === 'television')>Television</option>
                  <option value="radio" @selected(($draft->form_data['medium_type'] ?? '') === 'radio')>Radio</option>
                  <option value="magazine" @selected(($draft->form_data['medium_type'] ?? '') === 'magazine')>Magazine</option>
                  <option value="online_media" @selected(($draft->form_data['medium_type'] ?? '') === 'online_media')>Online Media</option>
                  <option value="other" @selected(($draft->form_data['medium_type'] ?? '') === 'other')>Other (Specify)</option>
                </select>
              </div>
              <div class="form-field">
                <label class="form-label required">Designation (Role)</label>
                <select class="form-control" name="designation" required>
                  <option value="">Select</option>
                  <option value="producer_editor" @selected(($draft->form_data['designation'] ?? '') === 'producer_editor')>Producer/Editor</option>
                  <option value="correspondent" @selected(($draft->form_data['designation'] ?? '') === 'correspondent')>Correspondent</option>
                  <option value="photographer" @selected(($draft->form_data['designation'] ?? '') === 'photographer')>Photographer</option>
                  <option value="freelance" @selected(($draft->form_data['designation'] ?? '') === 'freelance')>Freelance</option>
                  <option value="camera_person" @selected(($draft->form_data['designation'] ?? '') === 'camera_person')>Camera Person</option>
                  <option value="news_photo" @selected(($draft->form_data['designation'] ?? '') === 'news_photo')>News Photo</option>
                  <option value="engineer_technician" @selected(($draft->form_data['designation'] ?? '') === 'engineer_technician')>Engineer/Technician</option>
                  <option value="reporter" @selected(($draft->form_data['designation'] ?? '') === 'reporter')>Reporter</option>
                  <option value="other" @selected(($draft->form_data['designation'] ?? '') === 'other')>Other (Specify)</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Media House / Employer</label>
                <input type="text" class="form-control" name="media_house" placeholder="e.g. Zimpapers / Freelance" value="{{ $draft->form_data['media_house'] ?? '' }}" required>
              </div>
              <div class="form-field">
                <label class="form-label">Other Designation (Specify)</label>
                <input type="text" class="form-control" name="other_designation" value="{{ $draft->form_data['other_designation'] ?? '' }}" placeholder="If 'Other' selected above">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Employer Physical Address</label>
                <textarea class="form-control" name="employer_address" rows="2" required>{{ $draft->form_data['employer_address'] ?? '' }}</textarea>
              </div>
              <div class="form-field">
                <label class="form-label required">Employer Phone</label>
                <input type="text" class="form-control" name="employer_phone" value="{{ $draft->form_data['employer_phone'] ?? '' }}" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Employer Email</label>
                <input type="email" class="form-control" name="employer_email" value="{{ $draft->form_data['employer_email'] ?? '' }}" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Employer Cell Number</label>
                <input type="text" class="form-control" name="employer_cell" value="{{ $draft->form_data['employer_cell'] ?? '' }}" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Headquarters Address</label>
                <textarea class="form-control" name="headquarters_address" rows="2">{{ $draft->form_data['headquarters_address'] ?? '' }}</textarea>
              </div>
              <div class="form-field">
                <label class="form-label">Headquarters Phone</label>
                <input type="text" class="form-control" name="headquarters_phone" value="{{ $draft->form_data['headquarters_phone'] ?? '' }}">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Headquarters Email</label>
                <input type="email" class="form-control" name="headquarters_email" value="{{ $draft->form_data['headquarters_email'] ?? '' }}">
              </div>
              <div class="form-field">
                <label class="form-label">Headquarters Cell</label>
                <input type="text" class="form-control" name="headquarters_cell" value="{{ $draft->form_data['headquarters_cell'] ?? '' }}">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Name of Editor/Publisher</label>
                <input type="text" class="form-control" name="editor_name" value="{{ $draft->form_data['editor_name'] ?? '' }}" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Editor Contact Details</label>
                <input type="text" class="form-control" name="editor_contact" value="{{ $draft->form_data['editor_contact'] ?? '' }}" placeholder="Phone/Email" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Immediate Supervisor</label>
                <input type="text" class="form-control" name="supervisor_name" value="{{ $draft->form_data['supervisor_name'] ?? '' }}">
              </div>
              <div class="form-field">
                <label class="form-label">Supervisor Contact</label>
                <input type="text" class="form-control" name="supervisor_contact" value="{{ $draft->form_data['supervisor_contact'] ?? '' }}" placeholder="Phone/Email">
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label">Other organisations you string for</label>
                <textarea class="form-control" name="other_organisations" rows="2" placeholder="List other media organisations you work with">{{ $draft->form_data['other_organisations'] ?? '' }}</textarea>
              </div>
              <div class="form-field">
                <label class="form-label">Details of those organisations</label>
                <textarea class="form-control" name="other_organisations_details" rows="2" placeholder="Provide details of the organisations listed above">{{ $draft->form_data['other_organisations_details'] ?? '' }}</textarea>
              </div>
            </div>

            <div class="foreign-fields" style="display:none;">
              <hr>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Country where journalist is based</label>
                  <input type="text" class="form-control" name="journalist_based_country">
                </div>
                <div class="form-field">
                  <label class="form-label required">Is this your first time in Zimbabwe?</label>
                  <select class="form-control" name="first_time_in_zim">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Date of Arrival</label>
                  <input type="date" class="form-control" name="arrived_on">
                </div>
                <div class="form-field">
                  <label class="form-label required">Date of Departure</label>
                  <input type="date" class="form-control" name="departing_on">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Mode of Travel</label>
                  <input type="text" class="form-control" name="arrival_mode" placeholder="e.g. Air / Road">
                </div>
                <div class="form-field">
                  <label class="form-label required">Port of Entry</label>
                  <input type="text" class="form-control" name="port_of_entry" placeholder="e.g. RGM Airport / Beitbridge">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Address in Zimbabwe</label>
                  <textarea class="form-control" name="address_in_zimbabwe" rows="2"></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Special Assignment Details</label>
                  <textarea class="form-control" name="special_assignment" rows="3" placeholder="Describe the purpose of your visit"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 5: CRIMINAL RECORD --}}
        <div class="step-content" id="ap3-step-5">
          <h3 class="step-title">Criminal Record</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Please declare any criminal convictions. This information is required for accreditation purposes.
          </div>

          <div class="section-card">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Have you ever been convicted of a crime?</label>
                <select class="form-control" name="criminal_conviction" id="criminal_conviction" required>
                  <option value="">Select</option>
                  <option value="no" @selected(($draft->form_data['criminal_conviction'] ?? '') === 'no')>No</option>
                  <option value="yes" @selected(($draft->form_data['criminal_conviction'] ?? '') === 'yes')>Yes</option>
                </select>
              </div>
            </div>
            <div class="form-row" id="criminal_details_row" style="display: none;">
              <div class="form-field">
                <label class="form-label required">If yes, provide details</label>
                <textarea class="form-control" name="criminal_details" rows="4" placeholder="Please provide details of the conviction, including date, nature of offense, and sentence">{{ $draft->form_data['criminal_details'] ?? '' }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 6: REFEREES --}}
        <div class="step-content" id="ap3-step-6">
          <h3 class="step-title">Referees</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Provide details of three professional referees who can vouch for your media practitioner status.
          </div>

          @for ($i = 1; $i <= 3; $i++)
          <div class="section-card mb-3">
            <div class="section-title"><i class="ri-user-star-line me-2"></i>Referee #{{ $i }}</div>
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Full Name</label>
                <input type="text" class="form-control" name="referee_name_{{ $i }}" value="{{ $draft->form_data['referee_name_'.$i] ?? '' }}" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Phone Number</label>
                <input type="text" class="form-control" name="referee_phone_{{ $i }}" value="{{ $draft->form_data['referee_phone_'.$i] ?? '' }}" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Physical Address</label>
                <input type="text" class="form-control" name="referee_address_{{ $i }}" value="{{ $draft->form_data['referee_address_'.$i] ?? '' }}" required>
              </div>
            </div>
          </div>
          @endfor
        </div>

        {{-- STEP 7: UPLOADS --}}
        <div class="step-content" id="ap3-step-7">
          <h3 class="step-title">Document Uploads</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Upload clear scans of required documents. All files should be under 5MB (Except work samples which can be up to 20MB).
          </div>

          <div class="row g-3">
            <div class="col-12 col-lg-6">
              <div class="form-field">
                <label class="form-label required">Passport-size Photo</label>
                <div class="upload-area" id="area_passport_photo">
                  <i class="ri-user-smile-line fs-2 mb-2 text-primary"></i>
                  <h5>Passport Photo</h5>
                  <p class="mb-0 small text-muted">Recent color photo (JPG/PNG)</p>
                  <input type="file" name="passport_photo" accept="image/*" class="d-none" required>
                  <div class="d-flex gap-2 mt-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-primary pick-file">
                      <i class="ri-folder-open-line me-1"></i> Pick Photo
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" id="btn_open_camera">
                      <i class="ri-camera-lens-line me-1"></i> Take Photo
                    </button>
                  </div>
                </div>
                <div class="file-preview mt-2"></div>
              </div>
            </div>

            <div class="col-12 col-lg-6 local-fields">
              <div class="form-field">
                <label class="form-label required">National ID Scan</label>
                <div class="upload-area" id="area_id_scan">
                  <i class="ri-file-user-line fs-2 mb-2 text-primary"></i>
                  <h5>ID Card Scan</h5>
                  <p class="mb-0 small text-muted">Clear scan of both sides (PDF/JPG)</p>
                  <input type="file" name="id_scan" accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2 pick-file">Pick File</button>
                </div>
                <div class="file-preview mt-2"></div>
              </div>
            </div>

            <div class="col-12 col-lg-6 foreign-fields" style="display:none;">
              <div class="form-field">
                <label class="form-label required">Passport Bio-data Page</label>
                <div class="upload-area" id="area_passport_biodata">
                  <i class="ri-passport-line fs-2 mb-2 text-primary"></i>
                  <h5>Passport Bio-data</h5>
                  <p class="mb-0 small text-muted">Information page scan (PDF/JPG)</p>
                  <input type="file" name="passport_biodata_page" accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2 pick-file">Pick File</button>
                </div>
                <div class="file-preview mt-2"></div>
              </div>
            </div>
            
            <div class="col-12 col-lg-6">
              <div class="form-field">
                <label class="form-label required" id="label_employment_letter">Employment / Assignment Letter</label>
                <div class="upload-area" id="area_employment_letter">
                  <i class="ri-article-line fs-2 mb-2 text-primary"></i>
                  <h5>Letter from Media House</h5>
                  <p class="mb-0 small text-muted">Official assignment/confirm (PDF/JPG)</p>
                  <input type="file" name="employment_letter" accept=".pdf,.jpg,.jpeg,.png" class="d-none" required>
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2 pick-file">Pick File</button>
                </div>
                <div class="file-preview mt-2"></div>
              </div>
            </div>

            <div class="col-12 col-lg-6 foreign-fields" style="display:none;">
              <div class="form-field">
                <label class="form-label required">Clearance Letter</label>
                <div class="upload-area" id="area_clearance_letter">
                  <i class="ri-verified-badge-line fs-2 mb-2 text-primary"></i>
                  <h5>Clearance/Regulatory Letter</h5>
                  <p class="mb-0 small text-muted">From home country (PDF/JPG)</p>
                  <input type="file" name="clearance_letter" accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2 pick-file">Pick File</button>
                </div>
                <div class="file-preview mt-2"></div>
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <div class="form-field">
                <label class="form-label">Work Samples (Optional)</label>
                <div class="upload-area" id="area_work_samples">
                  <i class="ri-movie-line fs-2 mb-2 text-primary"></i>
                  <h5>Portfolio / Samples</h5>
                  <p class="mb-0 small text-muted">Combined PDF/ZIP or Video (Max 20MB)</p>
                  <input type="file" name="work_samples" accept=".pdf,.jpg,.jpeg,.png,.mp4,.mov,.avi,.doc,.docx" class="d-none">
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2 pick-file">Pick File</button>
                </div>
                <div class="file-preview mt-2"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 8: DECLARATION --}}
        <div class="step-content" id="ap3-step-8">
          <h3 class="step-title">Declaration</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Confirm that all provided information is accurate and complete.
          </div>

          <div class="alert alert-warning mt-4">
            <h6 class="fw-bold mb-2"><i class="ri-shield-check-line me-2"></i>DECLARATION BY APPLICANT</h6>
            <p class="mb-3 text-dark" style="font-size:14px;">
              I declare that all the information given above, to the best of my knowledge, is true and complete. I understand that any false statement may lead to the rejection of my application or revocation of my accreditation.
            </p>

            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" id="ap3Agree" name="declaration_agreed" required>
              <label class="form-check-label fw-bold text-dark" for="ap3Agree" style="font-size:13px; cursor:pointer;">
                I SOLEMNLY AGREE TO THE DECLARATION ABOVE.
              </label>
            </div>
          </div>

          <div class="section-card mt-3">
             <div class="form-field">
                <label class="form-label required">Declaration Date</label>
                <input type="text" class="form-control bg-light" name="declaration_date" id="ap3_declaration_date" readonly required>
             </div>
          </div>
        </div>

        {{-- FORM BUTTONS --}}
        <div class="form-buttons mt-4 pt-3 border-top">
          <div>
            <button type="button" class="btn btn-light px-4" id="ap3PrevBtn" style="display:none;">
              <i class="ri-arrow-left-line me-1"></i> Previous
            </button>
            <button type="button" class="btn btn-outline-secondary px-4 ms-2" id="ap3SaveDraftBtn" style="display:none;">
              <i class="ri-save-line me-1"></i> Save Draft
            </button>
          </div>
          <button type="button" class="btn btn-primary px-4" id="ap3NextBtn">
            Next <i class="ri-arrow-right-line ms-1"></i>
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- CAMERA MODAL --}}
  <div class="modal fade" id="cameraModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md">
      <div class="modal-content overflow-hidden" style="border-radius:16px;">
        <div class="modal-header bg-dark text-white py-3 border-0">
          <h5 class="modal-title fw-bold">Capture Passport Photo</h5>
          <button type="button" class="btn-close btn-close-white" id="btn_close_camera" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-0 position-relative bg-black" style="min-height:300px; display:flex; align-items:center; justify-content:center;">
          <video id="camera_video" autoplay playsinline class="w-100 h-100" style="object-fit:cover;"></video>
          <div class="camera-overlay" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:200px; height:250px; border:2px dashed rgba(255,255,255,0.5); border-radius:100px; pointer-events:none;"></div>
          <canvas id="camera_canvas" class="d-none"></canvas>
        </div>
        <div class="modal-footer border-top bg-light justify-content-center py-3">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success px-4" id="btn_capture_photo">
            <i class="ri-camera-line me-1"></i> Capture Photo
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- REVIEW MODAL --}}
  <div class="modal fade" id="ap3ReviewModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content overflow-hidden" style="border-radius:16px;">
        <div class="modal-header bg-success text-white py-3 border-0">
          <h5 class="modal-title fw-bold">Review Application</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" id="ap3ReviewContent"></div>
        <div class="modal-footer border-top bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back & Edit</button>
          <button type="button" class="btn btn-primary" id="ap3ConfirmSubmitBtn">
            <i class="ri-send-plane-line me-1"></i> Confirm & Submit
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- PAYMENT MODAL --}}
  <x-payment-modal 
    modal-id="ap3PaymentModal"
    description="Journalist Accreditation Application Fee"
    currency="USD"
  />

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    let currentStep = {{ $draft->form_data['current_step'] ?? 1 }};
    let scope = '{{ $draft->journalist_scope ?? ($draft->form_data['journalist_scope'] ?? '') }}';
    const totalSteps = 8;
    const form = document.getElementById('ap3Form');
    const steps = document.querySelectorAll('#new-accreditation-page .step');
    const Contents = [
      document.getElementById('ap3-step-1'),
      document.getElementById('ap3-step-2'),
      document.getElementById('ap3-step-3'),
      document.getElementById('ap3-step-4'),
      document.getElementById('ap3-step-5'),
      document.getElementById('ap3-step-6'),
      document.getElementById('ap3-step-7'),
      document.getElementById('ap3-step-8')
    ];
    const prevBtn = document.getElementById('ap3PrevBtn');
    const nextBtn = document.getElementById('ap3NextBtn');
    const saveBtn = document.getElementById('ap3SaveDraftBtn');
    
    // Initial Setup from Draft
    if (scope) {
        const card = document.querySelector(`.app-type-card[data-type="${scope}"]`);
        if (card) {
            card.classList.add('selected');
            document.querySelectorAll('.local-fields').forEach(el => el.style.display = (scope === 'local' ? '' : 'none'));
            document.querySelectorAll('.foreign-fields').forEach(el => el.style.display = (scope === 'foreign' ? '' : 'none'));
        }
    }
    showStep(currentStep);
    
    // Set auto date
    document.getElementById('ap3_declaration_date').value = new Date().toISOString().slice(0, 10);

    // DOB handling
    const updateDob = () => {
        const d = document.getElementById('dob_date').value;
        const m = document.getElementById('dob_month').value;
        const y = document.getElementById('dob_year').value;
        document.getElementById('real_dob').value = (d && m && y) ? `${y}-${m}-${d}` : '';
    };
    ['dob_date', 'dob_month', 'dob_year'].forEach(id => {
        document.getElementById(id).addEventListener('change', updateDob);
    });

    // Criminal Record field handling
    const criminalConviction = document.getElementById('criminal_conviction');
    const criminalDetailsRow = document.getElementById('criminal_details_row');
    
    if (criminalConviction && criminalDetailsRow) {
      criminalConviction.addEventListener('change', () => {
        criminalDetailsRow.style.display = criminalConviction.value === 'yes' ? 'block' : 'none';
        const criminalDetails = document.querySelector('[name="criminal_details"]');
        if (criminalDetails) {
          criminalDetails.required = criminalConviction.value === 'yes';
        }
      });
      
      // Initialize based on current value
      if (criminalConviction.value === 'yes') {
        criminalDetailsRow.style.display = 'block';
        const criminalDetails = document.querySelector('[name="criminal_details"]');
        if (criminalDetails) criminalDetails.required = true;
      }
    }

    // Pick Type Cards
    document.querySelectorAll('.app-type-card').forEach(card => {
      card.addEventListener('click', () => {
        document.querySelectorAll('.app-type-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        scope = card.dataset.type;
        document.getElementById('ap3_journalist_scope').value = scope;
        
        // Update fields visibility
        document.querySelectorAll('.local-fields').forEach(el => el.style.display = (scope === 'local' ? '' : 'none'));
        document.querySelectorAll('.foreign-fields').forEach(el => el.style.display = (scope === 'foreign' ? '' : 'none'));
        
        // Update required attributes
        document.getElementById('ap3_national_reg_no').required = (scope === 'local');
        document.getElementById('ap3_collection_region').required = (scope === 'local');
        document.getElementById('ap3_passport_no').required = (scope === 'foreign');
        
        document.querySelectorAll('.foreign-fields input, .foreign-fields select, .foreign-fields textarea').forEach(el => {
            if(el.name !== 'work_samples') el.required = (scope === 'foreign');
        });

        // Automatically move to next step when card is selected
        setTimeout(handleNext, 300);
      });
    });

    // Handle temporary application type
    const tempCard = document.querySelector('.app-type-card[data-type="temporary"]');
    if (tempCard) {
      tempCard.addEventListener('click', () => {
        // Show additional temporary fields if needed
        const tempFields = document.querySelector('.temporary-fields');
        if (tempFields) {
          tempFields.style.display = 'block';
        }
      });
    }

    // File Pickers
    document.querySelectorAll('.pick-file').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.parentElement.querySelector('input[type="file"]').click();
      });
    });

    document.querySelectorAll('input[type="file"]').forEach(input => {
      input.addEventListener('change', () => {
        const area = input.closest('.upload-area');
        const preview = area.parentElement.querySelector('.file-preview');
        if(input.files.length) {
          area.style.borderColor = '#10b981';
          area.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
          preview.innerHTML = `<span class="badge bg-success-subtle text-success p-2 rounded w-100">
            <i class="ri-check-line me-1"></i> ${input.files[0].name}
          </span>`;
        } else {
          area.style.borderColor = '';
          area.style.backgroundColor = '';
          preview.innerHTML = '';
        }
      });
    });

    // Navigation
    function showStep(s) {
      Contents.forEach((c, idx) => c.classList.toggle('active', idx === s - 1));
      steps.forEach((st, idx) => {
        st.classList.toggle('active', idx === s - 1);
        st.classList.toggle('completed', idx < s - 1);
        const num = st.querySelector('.step-number');
        if(idx < s - 1) num.innerHTML = '<i class="ri-check-line"></i>';
        else num.textContent = idx + 1;
      });
      
      prevBtn.style.display = s === 1 ? 'none' : 'block';
      saveBtn.style.display = s > 1 ? 'block' : 'none';
      nextBtn.innerHTML = s === totalSteps ? 'Review & Submit <i class="ri-send-plane-line ms-1"></i>' : 'Next <i class="ri-arrow-right-line ms-1"></i>';
    }

    function validateStep(s) {
      if(s === 1 && !scope) { alert('Please choose an applicant type.'); return false; }
      
      const current = Contents[s-1];
      const required = current.querySelectorAll('[required]');
      for(const field of required) {
        if(field.offsetParent === null) continue; // Skip visible:hidden
        if(field.type === 'file' && !field.files.length) { alert('Please upload required file: ' + field.name.replaceAll('_',' ')); return false; }
        if(field.type === 'checkbox' && !field.checked) { alert('Please agree to the declaration.'); return false; }
        if(field.type !== 'file' && field.type !== 'checkbox' && !field.value.trim()) {
          alert('Missing field: ' + field.name.replaceAll('_',' '));
          field.focus();
          return false;
        }
      }
      return true;
    }

    function handleNext() {
      if(validateStep(currentStep)) {
        if(currentStep < totalSteps) {
          currentStep++;
          showStep(currentStep);
          window.scrollTo(0, 0);
          autoSave(); // Auto save on step change
        } else {
          showReview();
        }
      }
    }

    nextBtn.addEventListener('click', handleNext);
    prevBtn.addEventListener('click', () => {
      if(currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });

    // Review
    function showReview() {
      const fd = new FormData(form);
      let html = '<div class="review-grid">';
      
      const fields = [
        ['Application Information', [
          ['Applicant Type', scope === 'local' ? 'Local Media Practitioner' : 'Foreign Media Practitioner'],
          ['Other Name(s)', fd.get('other_names') || '-'],
          ['Driver\'s Licence', fd.get('drivers_licence') || '-']
        ]],
        ['Personal Information', [
          ['Full Name', fd.get('title') + ' ' + fd.get('first_name') + ' ' + fd.get('surname')],
          ['Gender', fd.get('gender')],
          ['Date of Birth', fd.get('dob')],
          ['Place of Birth', fd.get('birth_place')],
          ['Nationality', fd.get('nationality')],
          ['Marital Status', fd.get('marital_status')],
          [scope === 'local' ? 'National ID Number' : 'Passport Number', scope === 'local' ? fd.get('national_reg_no') : fd.get('passport_no')],
          [scope === 'local' ? 'Passport Number (Optional)' : 'Passport Issued At', scope === 'local' ? (fd.get('passport_number') || '-') : fd.get('passport_issued_at')],
          [scope === 'local' ? '' : 'Passport Expiry Date', scope === 'local' ? '' : fd.get('passport_expiry')],
          ['Email', fd.get('email')],
          ['Phone', (fd.get('phone_country_code') || '') + ' ' + (fd.get('phone') || '')],
          ['Physical Address', fd.get('address')],
          [scope === 'local' ? 'Collection Office' : '', scope === 'local' ? (document.getElementById('ap3_collection_region').options[document.getElementById('ap3_collection_region').selectedIndex]?.text || '-') : '']
        ]],
        ['Qualifications', [
          ['Highest Academic Qualification', fd.get('highest_academic_qualification') || '-'],
          ['Field of Study', fd.get('field_of_study') || '-'],
          ['Institution Name', fd.get('institution_name') || '-'],
          ['Year Completed', fd.get('year_completed') || '-'],
          ['Professional Qualifications', fd.get('professional_qualifications') || '-'],
          ['Professional Memberships', fd.get('professional_memberships') || '-']
        ]],
        ['Employment Details', [
          ['Employment Type', fd.get('employment_type')],
          ['Media House / Employer', fd.get('media_house')],
          ['Designation', fd.get('designation')],
          ['Other Designation', fd.get('other_designation') || '-'],
          ['Medium Type', fd.get('medium_type')],
          ['Employer Physical Address', fd.get('employer_address') || '-'],
          ['Employer Phone', fd.get('employer_phone') || '-'],
          ['Employer Email', fd.get('employer_email') || '-'],
          ['Employer Cell', fd.get('employer_cell') || '-'],
          ['Headquarters Address', fd.get('headquarters_address') || '-'],
          ['Headquarters Phone', fd.get('headquarters_phone') || '-'],
          ['Headquarters Email', fd.get('headquarters_email') || '-'],
          ['Headquarters Cell', fd.get('headquarters_cell') || '-'],
          ['Name of Editor/Publisher', fd.get('editor_name') || '-'],
          ['Editor Contact Details', fd.get('editor_contact') || '-'],
          ['Immediate Supervisor', fd.get('supervisor_name') || '-'],
          ['Supervisor Contact', fd.get('supervisor_contact') || '-'],
          ['Other Organisations', fd.get('other_organisations') || '-'],
          ['Organisations Details', fd.get('other_organisations_details') || '-']
        ]],
        ['Foreign Applicant Details', [
          ...(scope === 'foreign' ? [
            ['Country where journalist is based', fd.get('journalist_based_country') || '-'],
            ['First time in Zimbabwe?', fd.get('first_time_in_zim') || '-'],
            ['Date of Arrival', fd.get('arrived_on') || '-'],
            ['Date of Departure', fd.get('departing_on') || '-'],
            ['Mode of Travel', fd.get('arrival_mode') || '-'],
            ['Port of Entry', fd.get('port_of_entry') || '-'],
            ['Address in Zimbabwe', fd.get('address_in_zimbabwe') || '-'],
            ['Special Assignment Details', fd.get('special_assignment') || '-']
          ] : [])
        ]],
        ['Criminal Record', [
          ['Criminal Conviction', fd.get('criminal_conviction') || '-'],
          ['Criminal Details', fd.get('criminal_details') || '-']
        ]]
      ];

      // Add Referees
      const refereeFields = [];
      for(let i=1; i<=3; i++) {
        const name = fd.get(`referee_name_${i}`);
        if(name) {
          refereeFields.push([`Referee #${i}`, `${name} (${fd.get(`referee_phone_${i}`)}) - ${fd.get(`referee_address_${i}`)}`]);
        }
      }
      if(refereeFields.length > 0) fields.push(['Referees', refereeFields]);

      fields.forEach(sect => {
        html += `<h6 class="fw-bold mt-3 mb-2 text-primary border-bottom pb-1">${sect[0]}</h6>`;
        html += `<div class="row g-2">`;
        sect[1].forEach(f => {
          html += `<div class="col-12 mb-2">
            <div class="small text-muted fw-bold">${f[0]}</div>
            <div class="text-dark">${f[1] || '-'}</div>
          </div>`;
        });
        html += `</div>`;
      });

      // Documents Section
      html += `<h6 class="fw-bold mt-4 mb-2 text-primary border-bottom pb-1">Uploaded Documents</h6>`;
      html += `<div class="row g-3">`;

      const docFields = [
        ['passport_photo', 'Passport Photo'],
        ['id_scan', 'ID / Passport Scan'],
        ['educational_certificate', 'Educational Certificate'],
        ['reference_letter', 'Reference Letter'],
        ['employment_letter', 'Employment Letter'],
        ['editorial_charter', 'Editorial Charter'],
        ['clearance_letter', 'Clearance Letter']
      ];

      docFields.forEach(df => {
        const file = fd.get(df[0]);
        if(file && file.size > 0) {
          html += `<div class="col-6 col-md-4">
            <div class="card h-100 border-dashed">
              <div class="card-body p-2 text-center">
                <div class="doc-preview mb-2" id="preview-${df[0]}" style="height:120px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:4px;">
                  <i class="ri-file-text-line fs-1 text-muted"></i>
                </div>
                <div class="small fw-bold text-truncate">${df[1]}</div>
                <div class="extra-small text-muted">${(file.size/1024).toFixed(1)} KB</div>
              </div>
            </div>
          </div>`;
          
          // Generate preview if it's an image
          if(file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
              const prev = document.getElementById(`preview-${df[0]}`);
              if(prev) prev.innerHTML = `<img src="${e.target.result}" style="max-width:100%; max-height:100%; object-fit:contain;">`;
            };
            reader.readAsDataURL(file);
          }
        }
      });

      html += `</div>`;
      html += '</div>';
      
      document.getElementById('ap3ReviewContent').innerHTML = html;
      new bootstrap.Modal(document.getElementById('ap3ReviewModal')).show();
    }

    // Submit
    document.getElementById('ap3ConfirmSubmitBtn').addEventListener('click', async function() {
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
      
      const fd = new FormData(form);
      const dataObj = {};
      fd.forEach((v, k) => {
          if(!(v instanceof File)) dataObj[k] = v;
      });
      fd.append('form_data', JSON.stringify(dataObj));

      try {
        const res = await fetch('{{ route("accreditation.submit") }}', {
          method: 'POST',
          body: fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await res.json();
        
        if(result.success) {
           bootstrap.Modal.getInstance(document.getElementById('ap3ReviewModal')).hide();
           if(result.application_id) {
              initPaymentModal('ap3PaymentModal', result.application_id, {
                  initiate: '{{ url("/payments") }}/' + result.application_id + '/initiate',
                  initiateMobile: '{{ url("/payments") }}/' + result.application_id + '/initiate-mobile',
                  status: '{{ url("/payments") }}/' + result.application_id + '/status',
                  proof: '{{ url("/payments") }}/' + result.application_id + '/upload-proof',
              });
              new bootstrap.Modal(document.getElementById('ap3PaymentModal')).show();
           } else {
              alert('Submitted successfully!');
              window.location.href = '{{ route("accreditation.home") }}';
           }
        } else {
          alert('Error: ' + result.message);
          btn.disabled = false;
          btn.innerHTML = '<i class="ri-send-plane-line me-1"></i> Confirm & Submit';
        }
      } catch(e) {
        console.error(e);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-send-plane-line me-1"></i> Confirm & Submit';
      }
    });

    // Save Draft
    async function autoSave() {
        const fd = new FormData(form);
        const dataObj = {};
        fd.forEach((v, k) => { if(!(v instanceof File)) dataObj[k] = v; });
        fd.append('form_data', JSON.stringify(dataObj));
        fd.append('current_step', currentStep);
        
        try {
            const res = await fetch('{{ route("accreditation.saveDraft") }}', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            console.log('Draft auto-saved');
        } catch(e) {
            console.error('Auto-save failed', e);
        }
    }

    // Auto save every 60 seconds if form is visible
    setInterval(() => {
        if (currentStep > 1 && currentStep < totalSteps) {
            autoSave();
        }
    }, 60000);

    saveBtn.addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        const fd = new FormData(form);
        const dataObj = {};
        fd.forEach((v, k) => { if(!(v instanceof File)) dataObj[k] = v; });
        fd.append('form_data', JSON.stringify(dataObj));
        fd.append('current_step', currentStep);
        
        try {
            const res = await fetch('{{ route("accreditation.saveDraft") }}', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
            if(result.success) alert('Draft saved successfully!');
            else alert('Draft save failed: ' + result.message);
        } catch(e) {
            console.error(e);
            alert('Draft save error.');
        } finally {
            btn.disabled = false;
        }
    });

    // Camera Handling
    const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
    const video = document.getElementById('camera_video');
    const canvas = document.getElementById('camera_canvas');
    const openCamBtn = document.getElementById('btn_open_camera');
    const captureBtn = document.getElementById('btn_capture_photo');
    let stream = null;

    openCamBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
            video.srcObject = stream;
            cameraModal.show();
        } catch (err) {
            console.error("Camera access error:", err);
            alert("Could not access camera. Please ensure you have given permission or use 'Pick Photo' instead.");
        }
    });

    const stopCamera = () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    };

    document.getElementById('cameraModal').addEventListener('hidden.bs.modal', stopCamera);

    captureBtn.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            const fileName = 'captured_photo_' + Date.now() + '.jpg';
            const file = new File([blob], fileName, { type: 'image/jpeg' });
            
            // Manual trigger of the file input logic
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            const fileInput = document.querySelector('input[name="passport_photo"]');
            fileInput.files = dataTransfer.files;
            
            // Trigger change event to update preview
            fileInput.dispatchEvent(new Event('change'));
            
            cameraModal.hide();
        }, 'image/jpeg', 0.9);
    });

  });
</script>
@endpush
