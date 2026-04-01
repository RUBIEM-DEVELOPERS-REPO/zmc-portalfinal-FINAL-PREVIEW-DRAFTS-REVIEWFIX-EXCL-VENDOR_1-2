@extends('layouts.portal')

@section('title', 'New Registration (AP1)')

@section('content')
<div id="new-registration-page">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">
      New Registration — Mass Media Service (AP1) — Digital Form
    </h4>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP1 — Application for Registration of a Mass Media Service</h1>
      <p class="m-0">
        Complete this digital AP1 form and upload required annexures. Ensure all details are accurate.
      </p>
    </div>

    <div class="form-steps-container">
      {{-- PROGRESS --}}
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Registration Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Contact & Organization</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Directors & Shareholding</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Management</div>
          </div>
          <div class="step" data-step="5">
            <div class="step-number">5</div>
            <div class="step-label">Questions</div>
          </div>
          <div class="step" data-step="6">
            <div class="step-number">6</div>
            <div class="step-label">Uploads</div>
          </div>
          <div class="step" data-step="7">
            <div class="step-number">7</div>
            <div class="step-label">Declaration</div>
          </div>
        </div>
      </div>

      <form id="ap1Form" onsubmit="return false;" enctype="multipart/form-data">
        {{-- STEP 1 --}}
        <div class="step-content active" id="ap1-step-1">
          <h3 class="step-title">Choose Registration Type</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Select whether you are registering a Local Media House or a Foreign Media House.
          </div>

          <div class="app-type-container">
            <div class="app-type-cards">
              <div class="app-type-card" data-type="local">
                <i class="ri-building-2-line"></i>
                <h4>Local Media House</h4>
                <p>Zimbabwe-based media service registration.</p>
              </div>

              <div class="app-type-card" data-type="foreign">
                <i class="ri-global-line"></i>
                <h4>Foreign Media House</h4>
                <p>Foreign media service registering local operations.</p>
              </div>
            </div>
          </div>

          <input type="hidden" name="registration_scope" id="ap1_registration_scope" required>
        </div>

        {{-- STEP 2 --}}
        <div class="step-content" id="ap1-step-2">
          <h3 class="step-title">Contact Person Details</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Provide the Contact Person details and Organization details.
          </div>

          {{-- CONTACT PERSON --}}
          <div class="section-card">
            <div class="section-title"><i class="ri-user-3-line me-2"></i>Contact Person Details</div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Name</label>
                <input type="text" class="form-control" name="contact_name" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Surname</label>
                <input type="text" class="form-control" name="contact_surname" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Physical Address</label>
                <textarea class="form-control" name="contact_address" rows="3" required></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Phone No.</label>
                <div class="d-flex gap-2">
                  <select class="form-control" name="contact_phone_country_code" style="max-width:160px;" required>
                    @include('partials.country-codes-options')
                  </select>
                  <input type="text" class="form-control" name="contact_phone" placeholder="e.g. 771234567" required>
                </div>
              </div>
              <div class="form-field">
                <label class="form-label required">Email Address</label>
                <input type="email" class="form-control" name="contact_email" required>
              </div>
            </div>

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
                <div class="form-hint"><i class="ri-map-pin-line"></i> Choose where you will collect your certificate after approval.</div>
              </div>
            </div>
          </div>

          {{-- ORGANIZATION --}}
          <div class="section-card mt-3">
            <div class="section-title"><i class="ri-building-4-line me-2"></i>Organization Details</div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Mass Media Category</label>
                <select class="form-control" name="mass_media_category" required>
                  <option value="">Select</option>
                  <option value="MC">MC — Community Media</option>
                  <option value="MA">MA — Advertising Agency as Media Service</option>
                  <option value="MF">MF — Local Office for Foreign Media Service</option>
                  <option value="MN">MN — National Media Service Publishing Newspaper</option>
                  <option value="DG">DG — Internet Based Media Service</option>
                  <option value="MP">MP — Production House as Media Service</option>
                  <option value="MS">MS — Media Service Fitting Multiple Categories</option>
                  <option value="MV">MV — Media Service in Film and Video Production</option>
                </select>
              </div>

              {{-- Local Activity Type --}}
              <div class="form-field registration-local-only">
                <label class="form-label required">Mass Media Type Activity</label>
                <select class="form-control" name="mass_media_activity" id="mass_media_activity">
                  <option value="">Select</option>
                  <option>Television</option>
                  <option>Radio</option>
                  <option>Newspaper</option>
                  <option>Magazine</option>
                  <option>Filming & Cinema</option>
                  <option>Billboards</option>
                  <option>Books</option>
                  <option>Online Publication</option>
                  <option>Podcasting</option>
                  <option>Other</option>
                </select>
              </div>

              {{-- Foreign Media Type (Electronic/Print/Both) --}}
              <div class="form-field registration-foreign-only" style="display:none;">
                <label class="form-label required">Media House Type</label>
                <div class="d-flex gap-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="foreign_media_type" value="Electronic" id="fmt_electronic">
                    <label class="form-check-label" for="fmt_electronic">Electronic</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="foreign_media_type" value="Print" id="fmt_print">
                    <label class="form-check-label" for="fmt_print">Print</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="foreign_media_type" value="Both" id="fmt_both">
                    <label class="form-check-label" for="fmt_both">Both</label>
                  </div>
                </div>
              </div>
            </div>

            {{-- Foreign Organization Details --}}
            <div class="registration-foreign-only section-subcard mt-3" style="display:none;">
              <div class="subcard-title"><i class="ri-global-line me-2"></i>Organization Details (Foreign)</div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Organization Name</label>
                  <input type="text" class="form-control" name="org_name" id="org_name">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Address of Head Office</label>
                  <textarea class="form-control" name="org_head_office" id="org_head_office" rows="2"></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Mailing Address</label>
                  <textarea class="form-control" name="org_mailing_address" id="org_mailing_address" rows="2"></textarea>
                </div>
              </div>
            </div>

            {{-- Foreign Representative Office Details --}}
            <div class="registration-foreign-only section-card mt-3" style="display:none;">
              <div class="section-title"><i class="ri-hotel-line me-2"></i>Representative Office Details</div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Name of Representative Office</label>
                  <input type="text" class="form-control" name="rep_office_name" id="rep_office_name">
                </div>
                <div class="form-field">
                  <label class="form-label required">Email Address</label>
                  <input type="email" class="form-control" name="rep_office_email" id="rep_office_email">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Physical Address</label>
                  <textarea class="form-control" name="rep_office_address" id="rep_office_address" rows="2"></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Activities Description</label>
                  <textarea class="form-control" name="rep_office_activities" id="rep_office_activities" rows="3"></textarea>
                </div>
              </div>

              <div class="q-block mt-3">
                <label class="form-label required">
                  Will the proposed representative office be owned wholly by the applicant?
                </label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input wholly-owned-toggle" type="radio" name="rep_office_wholly_owned" value="yes" id="row_yes">
                    <label class="form-check-label" for="row_yes">Yes</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input wholly-owned-toggle" type="radio" name="rep_office_wholly_owned" value="no" id="row_no">
                    <label class="form-check-label" for="row_no">No</label>
                  </div>
                </div>

                <div id="repOfficeShareholdersSection" class="mt-3" style="display:none;">
                  <label class="form-label mb-2">Shareholders / Members holding 10% or more</label>
                  <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th style="width:200px;">Share Capital / Voting Rights %</th>
                          <th style="width:70px;">Action</th>
                        </tr>
                      </thead>
                      <tbody id="repOfficeShareholdersRows">
                        {{-- rows injected by JS --}}
                      </tbody>
                    </table>
                  </div>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="addRepShareholderBtn">
                    <i class="ri-add-line me-1"></i> Add Shareholder
                  </button>
                </div>
              </div>
            </div>

            {{-- Radio extra fields --}}
            <div id="radioFields" class="section-subcard" style="display:none;">
              <div class="subcard-title"><i class="ri-radio-line me-2"></i>Radio Details</div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Titles Published</label>
                  <input type="text" class="form-control" name="radio_titles_published" id="radio_titles_published">
                </div>
                <div class="form-field">
                  <label class="form-label required">Frequency</label>
                  <input type="text" class="form-control" name="radio_frequency" id="radio_frequency">
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Circulation Figures</label>
                  <input type="text" class="form-control" name="radio_circulation_figures" id="radio_circulation_figures">
                </div>
                <div class="form-field">
                  <label class="form-label required">General News</label>
                  <select class="form-control" name="radio_general_news" id="radio_general_news">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Specialized Information</label>
                  <select class="form-control" name="radio_specialized_info" id="radio_specialized_info">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>
                <div class="form-field">
                  <label class="form-label">Give Details</label>
                  <input type="text" class="form-control" name="radio_specialized_details" id="radio_specialized_details" placeholder="Provide details (if applicable)">
                </div>
              </div>
            </div>

            {{-- Other activity field --}}
            <div id="otherActivityField" class="section-subcard" style="display:none;">
              <div class="subcard-title"><i class="ri-edit-2-line me-2"></i>Other Activity</div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label required">Specify Other Activity</label>
                  <input type="text" class="form-control" name="mass_media_activity_other" id="mass_media_activity_other">
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 3 --}}
        <div class="step-content" id="ap1-step-3">
          <h3 class="step-title">Directors and Shareholding</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Add each Director / Shareholder row. Use “Add Director” if there are multiple.
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead>
                <tr>
                  <th style="min-width:140px;">Name</th>
                  <th style="min-width:140px;">Surname</th>
                  <th style="min-width:220px;">Address</th>
                  <th style="min-width:160px;">Occupation</th>
                  <th style="min-width:160px;">Nationality</th>
                  <th style="min-width:140px;">Shareholding %</th>

                  <th style="min-width:180px;">Other Directorships?</th>
                  <th style="min-width:240px;">Companies Concerned</th>

                  <th style="min-width:200px;">Public/Political Office?</th>
                  <th style="min-width:240px;">Public/Political Details</th>

                  <th style="min-width:180px;">Other Shareholdings?</th>
                  <th style="min-width:240px;">Other Shareholdings (Specify)</th>

                  <th style="min-width:260px;">Owns/licensed under Broadcasting Act?</th>
                  <th style="min-width:260px;">Owns/licensed under Postal & Telecom Act?</th>
                  <th style="min-width:220px;">Owns/Shareholding in Advertising Agency?</th>

                  <th style="width:70px;">Action</th>
                </tr>
              </thead>
              <tbody id="directorsRows">
                {{-- rows injected by JS --}}
              </tbody>
            </table>
          </div>

          <button type="button" class="btn btn-outline-secondary btn-sm" id="addDirectorRowBtn">
            <i class="ri-add-line me-1"></i> Add Director
          </button>
        </div>

        {{-- STEP 4 --}}
        <div class="step-content" id="ap1-step-4">
          <h3 class="step-title">Management</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Provide the Chief Executive Officer details and add Senior Manager rows if needed.
          </div>

          <div class="section-card">
            <div class="section-title"><i class="ri-user-star-line me-2"></i>Chief Executive Officer</div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Name</label>
                <input type="text" class="form-control" name="ceo_name" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Surname</label>
                <input type="text" class="form-control" name="ceo_surname" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Nationality</label>
                <input type="text" class="form-control" name="ceo_nationality" required>
              </div>
              <div class="form-field">
                <label class="form-label required">Qualifications</label>
                <input type="text" class="form-control" name="ceo_qualifications" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Experience</label>
                <textarea class="form-control" name="ceo_experience" rows="3" required></textarea>
              </div>
            </div>
          </div>

          <div class="section-card mt-3">
            <div class="section-title"><i class="ri-team-line me-2"></i>Senior Managers</div>

            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead>
                  <tr>
                    <th style="min-width:160px;">Name</th>
                    <th style="min-width:160px;">Surname</th>
                    <th style="min-width:160px;">Nationality</th>
                    <th style="min-width:240px;">Qualifications</th>
                    <th style="width:70px;">Action</th>
                  </tr>
                </thead>
                <tbody id="seniorManagersRows">
                  {{-- rows injected by JS --}}
                </tbody>
              </table>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm" id="addSeniorManagerBtn">
              <i class="ri-add-line me-1"></i> Add Senior Manager
            </button>
          </div>
        </div>

        {{-- STEP 5 --}}
        <div class="step-content" id="ap1-step-5">
          <h3 class="step-title">Questions</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Answer all questions. Provide details if you select “Yes”.
          </div>

          <div class="section-card">
            <div class="section-title"><i class="ri-questionnaire-line me-2"></i>Compliance Questions</div>

            <div class="q-block">
              <label class="form-label required">
                Has the applicant or any of its directors ever been convicted of any offence within or outside Zimbabwe?
              </label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="q_convicted" value="yes" required>
                  <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="q_convicted" value="no" required>
                  <label class="form-check-label">No</label>
                </div>
              </div>
              <div class="mt-2">
                <label class="form-label">If yes, provide details</label>
                <textarea class="form-control" name="q_convicted_details" rows="2"></textarea>
              </div>
            </div>

            <hr>

            <div class="q-block">
              <label class="form-label required">
                Has applicant or any of its directors failed to satisfy within one year any judgment debt issued in Zimbabwe or elsewhere?
              </label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="q_judgment_debt" value="yes" required>
                  <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="q_judgment_debt" value="no" required>
                  <label class="form-check-label">No</label>
                </div>
              </div>
              <div class="mt-2">
                <label class="form-label">If yes, provide details</label>
                <textarea class="form-control" name="q_judgment_debt_details" rows="2"></textarea>
              </div>
            </div>

            <hr>

            <div class="q-block">
              <label class="form-label required">Has applicant or any of its directors ever:</label>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label required">(a) been adjudged insolvent by a court, in Zimbabwe or elsewhere?</label>
                  <select class="form-control" name="q_insolvent_a" required>
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label required">(b) been served with an insolvency petition within the last 10 years?</label>
                  <select class="form-control" name="q_insolvent_b" required>
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label required">(c) made any compromise with his/her creditors?</label>
                  <select class="form-control" name="q_insolvent_c" required>
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label required">(d) been declared insolvent?</label>
                  <select class="form-control" name="q_insolvent_d" required>
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>
              </div>

              <div class="mt-3">
                <label class="form-label">If the answer to any of these questions is yes, provide details</label>
                <textarea class="form-control" name="q_insolvent_details" rows="3"></textarea>
              </div>
            </div>

            <hr>

            <div class="q-block">
              <label class="form-label">Provide any other information relevant to evaluating the merits of the application</label>
              <textarea class="form-control" name="other_relevant_info" rows="4" placeholder="Optional details..."></textarea>
            </div>

          </div>
        </div>

        {{-- STEP 6 --}}
        <div class="step-content" id="ap1-step-6">
          <h3 class="step-title">Uploads / Annexures</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Upload all required annexures. Ensure scans are clear and readable.
          </div>

          <div class="row g-3">
            @php
              $docs = [
                ['key'=>'directors_ids', 'label'=>'1. Certified copy of a national identity card for each director listed', 'required'=>true, 'hint'=>'Combine into one PDF if possible'],
                ['key'=>'mission_statement', 'label'=>'2. Mission statement', 'required'=>true],
                ['key'=>'code_of_ethics', 'label'=>'3. In-house code of ethics', 'required'=>true],
                ['key'=>'code_of_conduct', 'label'=>'4. In-house code of conduct for employees', 'required'=>true],
                ['key'=>'style_book', 'label'=>'5. In-house style book', 'required'=>true],
                ['key'=>'certificate_of_incorporation', 'label'=>'6. Certificate of incorporation', 'required'=>true],
                ['key'=>'cr7_cr14', 'label'=>'7. CR7/CR14', 'required'=>true],
                ['key'=>'editorial_charter', 'label'=>'8. Editorial Charter', 'required'=>true],
                ['key'=>'balance_sheet_3yr', 'label'=>'9. Balance sheet for 3 years', 'required'=>true],
                ['key'=>'memorandum_of_association', 'label'=>'10. Memorandum of Association', 'required'=>true],
                ['key'=>'market_analysis', 'label'=>'11. Market Analysis', 'required'=>true],
                ['key'=>'cashflow_projection_3yr', 'label'=>'12. Cashflow Projection for the next 3 years', 'required'=>true],
                ['key'=>'journalist_list', 'label'=>'13. List of names and addresses of journalist employed in the representative office', 'required'=>false, 'hint'=>'Required for Foreign Media Houses'],
              ];
            @endphp

            @foreach($docs as $d)
              <div class="col-12 col-lg-6">
                <div class="form-field">
                  <label class="form-label {{ $d['required'] ? 'required' : '' }}">{{ $d['label'] }}</label>
                  <div class="upload-area">
                    <i class="ri-folder-upload-line"></i>
                    <h5>Upload File</h5>
                    <p class="m-0">{{ $d['hint'] ?? 'PDF/JPG/PNG/ZIP' }}</p>
                    <input
                      type="file"
                      name="documents[{{ $d['key'] }}]"
                      data-doc-type="{{ $d['key'] }}"
                      accept=".pdf,.jpg,.jpeg,.png,.zip"
                      style="display:none;"
                      {{ $d['required'] ? 'required' : '' }}
                    >
                    <button type="button" class="upload-btn">Choose File</button>
                  </div>
                  <div class="uploaded-files"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- STEP 7 --}}
        <div class="step-content" id="ap1-step-7">
          <h3 class="step-title">Declaration</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Tick the declaration to confirm the information is true and complete.
          </div>

          <div class="alert alert-warning mt-3">
            <h6 class="mb-2"><i class="ri-file-text-line me-2"></i>DECLARATION</h6>
            <p class="mb-2">I declare that all the information given above, to the best of my knowledge is true and complete.</p>

            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" id="ap1Agree" name="declaration_agreed" required>
              <label class="form-check-label fw-semibold" for="ap1Agree">
                I agree to the declaration.
              </label>
            </div>
          </div>

          <div class="section-card mt-3">
            <div class="form-field">
              <label class="form-label required">Date &amp; Time</label>
              <input type="text" class="form-control" name="declaration_date" id="ap1_declaration_date" readonly required>
              <small class="text-muted">Automatically set when you submit the application</small>
            </div>
          </div>
        </div>

        {{-- BUTTONS --}}
        <div class="form-buttons">
          <div>
            <button type="button" class="btn btn-secondary" id="ap1PrevBtn">
              <i class="ri-arrow-left-line"></i> Previous
            </button>

            <button type="button" class="btn btn-outline-secondary ms-2" id="ap1SaveDraftBtn">
              <i class="ri-save-line"></i> Save Draft
            </button>
          </div>

          <button type="button" class="btn btn-primary" id="ap1NextBtn">
            Next <i class="ri-arrow-right-line"></i>
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- REVIEW MODAL --}}
  <div class="modal fade" id="ap1ReviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
        <div class="modal-header border-0" style="background:linear-gradient(135deg,#1e7e34,#28a745);padding:20px 24px;">
          <div class="d-flex align-items-center gap-3">
            <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
              <i class="ri-file-search-line text-white" style="font-size:20px;"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold text-white m-0">Review Your Registration</h5>
              <p class="m-0 text-white" style="opacity:.8;font-size:12px;">Please verify all details before submitting</p>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="ap1ReviewContent"></div>
        <div class="modal-body border-top pt-3" id="ap1AppFeeSection">
          <div class="alert alert-warning mb-3">
            <div class="fw-bold mb-1"><i class="ri-money-dollar-circle-line me-1"></i> Application Fee Required</div>
            <div class="small">An application fee must be paid before submission. Please provide your PayNow reference or upload proof of payment.</div>
          </div>
          <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="appfee-tab-ref" data-bs-toggle="pill" data-bs-target="#appfee-pane-ref" type="button" role="tab">
                <i class="ri-flashlight-line me-1"></i> PayNow Reference
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="appfee-tab-proof" data-bs-toggle="pill" data-bs-target="#appfee-pane-proof" type="button" role="tab">
                <i class="ri-file-upload-line me-1"></i> Upload Proof
              </button>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="appfee-pane-ref" role="tabpanel">
              <div class="mb-3">
                <label class="form-label small fw-bold">PayNow Reference Number</label>
                <input type="text" class="form-control" id="appfee_paynow_ref" placeholder="e.g. PN1234567890">
              </div>
            </div>
            <div class="tab-pane fade" id="appfee-pane-proof" role="tabpanel">
              <div class="row g-2">
                <div class="col-12 col-md-6">
                  <label class="form-label small">Name</label>
                  <input type="text" class="form-control" id="appfee_first_name">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small">Surname</label>
                  <input type="text" class="form-control" id="appfee_last_name">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small">Payment date</label>
                  <input type="date" class="form-control" id="appfee_payment_date">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small">Amount paid</label>
                  <input type="number" step="0.01" min="0" class="form-control" id="appfee_amount_paid">
                </div>
                <div class="col-12">
                  <label class="form-label small">Bank used</label>
                  <input type="text" class="form-control" id="appfee_bank_name" placeholder="e.g. CBZ / Steward / Stanbic">
                </div>
                <div class="col-12">
                  <label class="form-label small">Upload proof (PDF/JPG/PNG)</label>
                  <input type="file" class="form-control" id="appfee_proof_file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Edit Application</button>
          <button type="button" class="btn btn-primary" id="ap1ConfirmSubmitBtn">
            <i class="ri-send-plane-line me-2"></i>Confirm & Submit
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Payment Modal (shown after successful submission) --}}
  <input type="hidden" id="ap1PayModalAppId" value="">
  <div class="modal fade" id="ap1PaymentModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
      <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
        <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1e7e34,#28a745);padding:24px 28px 20px;">
          <div class="d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
              <i class="ri-checkbox-circle-line text-white" style="font-size:22px;"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold text-white m-0">Application Submitted!</h5>
              <p class="m-0 text-white" style="opacity:.8;font-size:13px;">Ref: <span id="ap1PayModalRef"></span></p>
            </div>
          </div>
        </div>
        <div class="modal-body p-0">
          <div style="padding:20px 28px 0;">
            <div class="alert alert-success mb-3" style="border-radius:10px;font-size:13px;">
              <i class="ri-checkbox-circle-line me-2"></i>
              Your application has been submitted. Please proceed to pay the registration fee to complete the process.
            </div>
            <div class="d-flex gap-2 mb-4" id="ap1PayTabs">
              <button type="button" class="pay-tab-btn active" data-tab="paynow"
                style="flex:1;padding:10px;border:2px solid #28a745;border-radius:10px;background:#f0fdf4;color:#1e7e34;font-weight:600;font-size:13px;cursor:pointer;">
                <i class="ri-smartphone-line me-1"></i>PayNow
              </button>
              <button type="button" class="pay-tab-btn" data-tab="proof"
                style="flex:1;padding:10px;border:2px solid #e5e7eb;border-radius:10px;background:#fff;color:#6b7280;font-weight:600;font-size:13px;cursor:pointer;">
                <i class="ri-file-upload-line me-1"></i>Upload Proof
              </button>
            </div>
          </div>
          <div class="pay-tab-content" id="ap1PaymentModal-tab-paynow" style="padding:0 28px 24px;">
            <div class="alert alert-info mb-3" style="border-radius:10px;font-size:13px;">
              <i class="ri-information-line me-2"></i>
              You will be redirected to PayNow. Once payment is confirmed, your portal updates automatically.
            </div>
            <button type="button" class="btn btn-success w-100 py-2 mb-3" id="ap1PayNowWebBtn" style="border-radius:10px;font-weight:600;">
              <i class="ri-external-link-line me-2"></i>Pay with PayNow (Card / ZimSwitch)
            </button>
            <div class="d-flex align-items-center gap-2 mb-3">
              <hr style="flex:1;margin:0;"><span class="text-muted" style="font-size:12px;">or mobile money</span><hr style="flex:1;margin:0;">
            </div>
            <div class="d-flex gap-2 mb-2">
              <input type="tel" class="form-control" id="ap1MobilePhone" placeholder="07XXXXXXXX" maxlength="10" style="border-radius:8px;font-size:13px;">
              <select class="form-select" id="ap1MobileMethod" style="max-width:130px;border-radius:8px;font-size:13px;">
                <option value="ecocash">EcoCash</option>
                <option value="onemoney">OneMoney</option>
              </select>
            </div>
            <button type="button" class="btn btn-outline-success w-100 py-2 mb-3" id="ap1PayNowMobileBtn" style="border-radius:10px;font-weight:600;font-size:13px;">
              <i class="ri-smartphone-line me-2"></i>Send Mobile Payment Request
            </button>
            <div id="ap1PollArea" style="display:none;" class="alert alert-warning">
              <div class="d-flex align-items-center gap-2">
                <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                <span id="ap1PollMsg">Waiting for payment confirmation...</span>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="ap1PollCheckBtn">
                <i class="ri-refresh-line me-1"></i>Check Status
              </button>
            </div>
          </div>
          <div class="pay-tab-content" id="ap1PaymentModal-tab-proof" style="display:none;padding:0 28px 24px;">
            <div class="alert alert-warning mb-3" style="border-radius:10px;font-size:13px;">
              <i class="ri-alert-line me-2"></i>
              An Accounts Officer will verify your proof and generate a digitally stamped receipt.
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:600;">First Name *</label>
                <input type="text" class="form-control form-control-sm" id="ap1ProofFname" style="border-radius:8px;">
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:600;">Surname *</label>
                <input type="text" class="form-control form-control-sm" id="ap1ProofLname" style="border-radius:8px;">
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:600;">Payment Date *</label>
                <input type="date" class="form-control form-control-sm" id="ap1ProofDate" style="border-radius:8px;">
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:12px;font-weight:600;">Amount Paid (USD) *</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="ap1ProofAmount" style="border-radius:8px;">
              </div>
              <div class="col-12">
                <label class="form-label" style="font-size:12px;font-weight:600;">Bank / Payment Method *</label>
                <input type="text" class="form-control form-control-sm" id="ap1ProofBank" placeholder="e.g. CBZ / EcoCash" style="border-radius:8px;">
              </div>
              <div class="col-12">
                <label class="form-label" style="font-size:12px;font-weight:600;">Upload Proof (PDF/JPG/PNG) *</label>
                <input type="file" class="form-control form-control-sm" id="ap1ProofFile" accept=".pdf,.jpg,.jpeg,.png" style="border-radius:8px;">
              </div>
            </div>
            <button type="button" class="btn btn-warning w-100 py-2 mt-3" id="ap1ProofSubmitBtn" style="border-radius:10px;font-weight:600;color:#fff;">
              <i class="ri-upload-cloud-line me-2"></i>Submit Proof of Payment
            </button>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 d-flex justify-content-between" style="padding:0 28px 20px;">
          <p class="text-muted m-0" style="font-size:11px;"><i class="ri-shield-check-line me-1 text-success"></i>Secured by PayNow Zimbabwe</p>
          <a href="{{ route('mediahouse.portal') }}" class="btn btn-sm btn-outline-secondary">Pay Later</a>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  let ap1Step = 1;
  const csrfToken = '{{ csrf_token() }}';

  const ap1SelectedDocs = {};
  const ap1ObjectUrls = [];

  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // ===== Step UI =====
  function ap1UpdateSteps() {
    const steps = document.querySelectorAll('#new-registration-page .step');
    const contents = document.querySelectorAll('#new-registration-page .step-content');

    steps.forEach(s => {
      const n = parseInt(s.dataset.step, 10);
      s.classList.remove('active','completed');
      const num = s.querySelector('.step-number');

      if (n < ap1Step) {
        s.classList.add('completed');
        if (num) num.innerHTML = '<i class="ri-check-line"></i>';
      } else {
        if (num) num.textContent = String(n);
      }

      if (n === ap1Step) s.classList.add('active');
    });

    contents.forEach(c => c.classList.remove('active'));
    document.getElementById('ap1-step-' + ap1Step)?.classList.add('active');

    const prev = document.getElementById('ap1PrevBtn');
    const next = document.getElementById('ap1NextBtn');

    if (prev) prev.style.display = (ap1Step === 1) ? 'none' : 'inline-block';
    if (next) next.innerHTML = (ap1Step === 7)
      ? 'Review & Submit <i class="ri-file-search-line"></i>'
      : 'Next <i class="ri-arrow-right-line"></i>';
  }

  function ap1ValidateStep() {
    const active = document.getElementById('ap1-step-' + ap1Step);
    if (!active) return true;

    // Step 1 must have scope
    if (ap1Step === 1) {
      const scope = document.getElementById('ap1_registration_scope')?.value;
      if (!scope) { alert('Please choose the Registration Type (Local or Foreign).'); return false; }
    }

    // validate required fields inside visible step
    const required = active.querySelectorAll('[required]');
    for (const el of required) {
      if (el.type === 'file') continue;
      if (el.type === 'checkbox' && !el.checked) { alert('Please agree to the declaration.'); el.focus(); return false; }
      if (!String(el.value || '').trim()) { alert('Please fill in all required fields.'); el.focus(); return false; }
    }

    // files required on Step 6
    if (ap1Step === 6) {
      const scope = document.getElementById('ap1_registration_scope')?.value;
      const files = active.querySelectorAll('input[type="file"][required]');
      for (const f of files) {
        if (!f.files || !f.files[0]) { alert('Please upload all required annexures.'); return false; }
      }
      
      // Media practitioner list is required for Foreign Media
      if (scope === 'foreign') {
        const jList = document.querySelector('input[name="documents[journalist_list]"]');
        if (jList && (!jList.files || !jList.files[0])) {
          alert('Foreign Media Houses must upload the list of journalists.');
          return false;
        }
      }
    }

    return true;
  }

  // ===== Dynamic Activity (Radio + Other) =====
  function applyActivityVisibility() {
    const activity = document.getElementById('mass_media_activity')?.value;

    const radioWrap = document.getElementById('radioFields');
    const otherWrap = document.getElementById('otherActivityField');

    const radioRequiredIds = [
      'radio_titles_published','radio_frequency','radio_circulation_figures','radio_general_news','radio_specialized_info'
    ];

    if (radioWrap) radioWrap.style.display = (activity === 'Radio') ? '' : 'none';
    if (otherWrap) otherWrap.style.display = (activity === 'Other') ? '' : 'none';

    // Required toggles for radio
    radioRequiredIds.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      if (activity === 'Radio') el.setAttribute('required','required');
      else el.removeAttribute('required');
    });

    // Required toggle for other
    const otherEl = document.getElementById('mass_media_activity_other');
    if (otherEl) {
      if (activity === 'Other') otherEl.setAttribute('required','required');
      else otherEl.removeAttribute('required');
    }
  }

  // ===== Foreign Media Scope Toggles =====
  function applyScopeVisibility() {
    const scope = document.getElementById('ap1_registration_scope')?.value;
    const localOnly = document.querySelectorAll('.registration-local-only');
    const foreignOnly = document.querySelectorAll('.registration-foreign-only');

    if (scope === 'foreign') {
      localOnly.forEach(el => el.style.display = 'none');
      foreignOnly.forEach(el => el.style.display = 'block');
      
      // Update required attributes
      document.getElementById('mass_media_activity')?.removeAttribute('required');
      document.getElementById('org_name')?.setAttribute('required', 'required');
      document.getElementById('org_head_office')?.setAttribute('required', 'required');
      document.getElementById('org_mailing_address')?.setAttribute('required', 'required');
      document.getElementById('rep_office_name')?.setAttribute('required', 'required');
      document.getElementById('rep_office_email')?.setAttribute('required', 'required');
      document.getElementById('rep_office_address')?.setAttribute('required', 'required');
      document.getElementById('rep_office_activities')?.setAttribute('required', 'required');
      
      // Radio for foreign media type
      document.querySelectorAll('input[name="foreign_media_type"]').forEach(r => r.setAttribute('required', 'required'));
      document.querySelectorAll('input[name="rep_office_wholly_owned"]').forEach(r => r.setAttribute('required', 'required'));

    } else {
      localOnly.forEach(el => el.style.display = 'block');
      foreignOnly.forEach(el => el.style.display = 'none');

      // Update required attributes
      document.getElementById('mass_media_activity')?.setAttribute('required', 'required');
      document.getElementById('org_name')?.removeAttribute('required');
      document.getElementById('org_head_office')?.removeAttribute('required');
      document.getElementById('org_mailing_address')?.removeAttribute('required');
      document.getElementById('rep_office_name')?.removeAttribute('required');
      document.getElementById('rep_office_email')?.removeAttribute('required');
      document.getElementById('rep_office_address')?.removeAttribute('required');
      document.getElementById('rep_office_activities')?.removeAttribute('required');

      document.querySelectorAll('input[name="foreign_media_type"]').forEach(r => r.removeAttribute('required'));
      document.querySelectorAll('input[name="rep_office_wholly_owned"]').forEach(r => r.removeAttribute('required'));
    }
  }

  function applyWhollyOwnedVisibility() {
    const val = document.querySelector('input[name="rep_office_wholly_owned"]:checked')?.value;
    const section = document.getElementById('repOfficeShareholdersSection');
    if (section) section.style.display = (val === 'no') ? 'block' : 'none';
    
    // Manage required for repeater if visible
    const inputs = section?.querySelectorAll('input');
    inputs?.forEach(input => {
        if (val === 'no') input.setAttribute('required', 'required');
        else input.removeAttribute('required');
    });
  }

  // ===== Repeaters =====
  let directorIdx = 0;
  function directorRowHtml(i) {
    return `
      <tr data-row="${i}">
        <td><input class="form-control" name="directors[name][]" required></td>
        <td><input class="form-control" name="directors[surname][]" required></td>
        <td><input class="form-control" name="directors[address][]" required></td>
        <td><input class="form-control" name="directors[occupation][]" required></td>
        <td><input class="form-control" name="directors[nationality][]" required></td>
        <td><input class="form-control" type="number" name="directors[shareholding_percent][]" min="0" max="100" step="0.01" required></td>

        <td>
          <select class="form-control director-toggle" data-target="directorships_${i}" name="directors[other_directorships][]" required>
            <option value="">Select</option>
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </td>
        <td>
          <input class="form-control director-dependent" id="directorships_${i}" name="directors[companies_concerned][]" placeholder="If yes, specify">
        </td>

        <td>
          <select class="form-control director-toggle" data-target="publicoffice_${i}" name="directors[public_political_office][]" required>
            <option value="">Select</option>
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </td>
        <td>
          <input class="form-control director-dependent" id="publicoffice_${i}" name="directors[public_political_details][]" placeholder="If yes, provide details">
        </td>

        <td>
          <select class="form-control director-toggle" data-target="othershare_${i}" name="directors[other_shareholdings][]" required>
            <option value="">Select</option>
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </td>
        <td>
          <input class="form-control director-dependent" id="othershare_${i}" name="directors[other_shareholdings_details][]" placeholder="If yes, specify">
        </td>

        <td>
          <select class="form-control" name="directors[broadcasting_act_shareholding][]" required>
            <option value="">Select</option>
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </td>
        <td>
          <select class="form-control" name="directors[postal_telecom_act_shareholding][]" required>
            <option value="">Select</option>
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </td>
        <td>
          <select class="form-control" name="directors[advertising_agency_shareholding][]" required>
            <option value="">Select</option>
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </td>

        <td class="text-center">
          <button type="button" class="btn btn-light btn-sm remove-row"><i class="ri-close-line"></i></button>
        </td>
      </tr>
    `;
  }

  function addDirectorRow() {
    const tbody = document.getElementById('directorsRows');
    if (!tbody) return;
    directorIdx++;
    tbody.insertAdjacentHTML('beforeend', directorRowHtml(directorIdx));
    wireRowActions();
    applyDirectorDependencies(); // keep consistent
  }

  let smIdx = 0;
  function seniorManagerRowHtml(i) {
    return `
      <tr data-row="${i}">
        <td><input class="form-control" name="senior_managers[name][]" required></td>
        <td><input class="form-control" name="senior_managers[surname][]" required></td>
        <td><input class="form-control" name="senior_managers[nationality][]" required></td>
        <td><input class="form-control" name="senior_managers[qualifications][]" required></td>
        <td class="text-center">
          <button type="button" class="btn btn-light btn-sm remove-row"><i class="ri-close-line"></i></button>
        </td>
      </tr>
    `;
  }

  function addSeniorManagerRow() {
    const tbody = document.getElementById('seniorManagersRows');
    if (!tbody) return;
    smIdx++;
    tbody.insertAdjacentHTML('beforeend', seniorManagerRowHtml(smIdx));
    wireRowActions();
  }

  let repShareIdx = 0;
  function repShareholderRowHtml(i) {
    return `
      <tr data-row="${i}">
        <td><input class="form-control" name="rep_office_shareholders[name][]" required></td>
        <td><input class="form-control" type="number" name="rep_office_shareholders[percent][]" min="0" max="100" step="0.01" required></td>
        <td class="text-center">
          <button type="button" class="btn btn-light btn-sm remove-row"><i class="ri-close-line"></i></button>
        </td>
      </tr>
    `;
  }

  function addRepShareholderRow() {
    const tbody = document.getElementById('repOfficeShareholdersRows');
    if (!tbody) return;
    repShareIdx++;
    tbody.insertAdjacentHTML('beforeend', repShareholderRowHtml(repShareIdx));
    wireRowActions();
  }

  function wireRowActions() {
    document.querySelectorAll('.remove-row').forEach(btn => {
      btn.onclick = (e) => {
        const tr = e.currentTarget.closest('tr');
        if (tr) tr.remove();
      };
    });

    document.querySelectorAll('.director-toggle').forEach(sel => {
      sel.onchange = applyDirectorDependencies;
    });
  }

  function applyDirectorDependencies() {
    document.querySelectorAll('.director-toggle').forEach(sel => {
      const targetId = sel.dataset.target;
      const target = document.getElementById(targetId);
      if (!target) return;

      if (sel.value === 'yes') {
        target.removeAttribute('disabled');
      } else {
        target.value = '';
        target.setAttribute('disabled', 'disabled');
      }
    });
  }

  // ===== Upload UI + Preview storage =====
  function ap1SetupUploads() {
    document.querySelectorAll('#new-registration-page .upload-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input[type="file"]');
        if (input) input.click();
      });
    });

    document.querySelectorAll('#new-registration-page .upload-area input[type="file"]').forEach(input => {
      input.addEventListener('change', function() {
        const file = this.files && this.files[0];
        const docType = this.dataset.docType;

        if (docType) ap1SelectedDocs[docType] = file || null;

        const area = this.closest('.upload-area');
        const list = area?.parentElement?.querySelector('.uploaded-files');

        if (area) {
          if (file) {
            area.style.borderColor = '#10b981';
            area.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
          } else {
            area.style.borderColor = '';
            area.style.backgroundColor = '';
          }
        }

        if (list) {
          if (!file) { list.innerHTML = ''; return; }

          const fileName = file.name.length > 32 ? file.name.slice(0, 32) + '...' : file.name;
          const size = (file.size / 1024).toFixed(1) + ' KB';

          list.innerHTML = `
            <div class="uploaded-file d-flex align-items-center justify-content-between p-2 border rounded mb-2">
              <div class="d-flex align-items-center gap-2">
                <i class="ri-file-text-line file-icon"></i>
                <div>
                  <div class="file-name fw-semibold" style="font-size:13px;">${escapeHtml(fileName)}</div>
                  <div class="file-size text-muted" style="font-size:11px;">${escapeHtml(size)}</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-light">Remove</button>
            </div>
          `;

          list.querySelector('button')?.addEventListener('click', () => {
            input.value = '';
            if (docType) ap1SelectedDocs[docType] = null;
            list.innerHTML = '';
            if (area) { area.style.borderColor = ''; area.style.backgroundColor = ''; }
          });
        }
      });
    });
  }

  // ===== Collect Form Data (Text only) =====
  function getFormData() {
    const formData = {};
    const inputs = document.querySelectorAll('#ap1Form input, #ap1Form select, #ap1Form textarea');

    inputs.forEach(input => {
      if (input.type === 'file') return;
      if (!input.name && !input.id) return;

      const key = input.name || input.id;

      // radio handling
      if (input.type === 'radio') {
        if (input.checked) formData[key] = input.value;
        return;
      }

      // checkbox
      if (input.type === 'checkbox') {
        formData[key] = input.checked;
        return;
      }

      formData[key] = input.value;
    });

    // Foreign specific: get radio values manually for safety
    formData.foreign_media_type = document.querySelector('input[name="foreign_media_type"]:checked')?.value || null;
    formData.rep_office_wholly_owned = document.querySelector('input[name="rep_office_wholly_owned"]:checked')?.value || null;

    // Directors table -> structured array
    formData.directors = [];
    const directorRows = document.querySelectorAll('#directorsRows tr');
    directorRows.forEach(tr => {
      const row = {
        name: tr.querySelector('[name="directors[name][]"]')?.value || '',
        surname: tr.querySelector('[name="directors[surname][]"]')?.value || '',
        address: tr.querySelector('[name="directors[address][]"]')?.value || '',
        occupation: tr.querySelector('[name="directors[occupation][]"]')?.value || '',
        nationality: tr.querySelector('[name="directors[nationality][]"]')?.value || '',
        shareholding_percent: tr.querySelector('[name="directors[shareholding_percent][]"]')?.value || '',

        other_directorships: tr.querySelector('[name="directors[other_directorships][]"]')?.value || '',
        companies_concerned: tr.querySelector('[name="directors[companies_concerned][]"]')?.value || '',

        public_political_office: tr.querySelector('[name="directors[public_political_office][]"]')?.value || '',
        public_political_details: tr.querySelector('[name="directors[public_political_details][]"]')?.value || '',

        other_shareholdings: tr.querySelector('[name="directors[other_shareholdings][]"]')?.value || '',
        other_shareholdings_details: tr.querySelector('[name="directors[other_shareholdings_details][]"]')?.value || '',

        broadcasting_act_shareholding: tr.querySelector('[name="directors[broadcasting_act_shareholding][]"]')?.value || '',
        postal_telecom_act_shareholding: tr.querySelector('[name="directors[postal_telecom_act_shareholding][]"]')?.value || '',
        advertising_agency_shareholding: tr.querySelector('[name="directors[advertising_agency_shareholding][]"]')?.value || '',
      };

      const hasAny = Object.values(row).some(v => String(v).trim() !== '');
      if (hasAny) formData.directors.push(row);
    });

    // Senior managers -> structured array
    formData.senior_managers = [];
    const smRows = document.querySelectorAll('#seniorManagersRows tr');
    smRows.forEach(tr => {
      const row = {
        name: tr.querySelector('[name="senior_managers[name][]"]')?.value || '',
        surname: tr.querySelector('[name="senior_managers[surname][]"]')?.value || '',
        nationality: tr.querySelector('[name="senior_managers[nationality][]"]')?.value || '',
        qualifications: tr.querySelector('[name="senior_managers[qualifications][]"]')?.value || '',
      };
      const hasAny = Object.values(row).some(v => String(v).trim() !== '');
      if (hasAny) formData.senior_managers.push(row);
    });

    // Representative Office Shareholders
    formData.rep_office_shareholders = [];
    const repRows = document.querySelectorAll('#repOfficeShareholdersRows tr');
    repRows.forEach(tr => {
      const row = {
        name: tr.querySelector('[name="rep_office_shareholders[name][]"]')?.value || '',
        percent: tr.querySelector('[name="rep_office_shareholders[percent][]"]')?.value || '',
      };
      if (row.name || row.percent) formData.rep_office_shareholders.push(row);
    });

    return formData;
  }

  // ===== Review (with file previews) =====
  function buildDocsPreviewHtml() {
    const entries = Object.keys(ap1SelectedDocs).map(docType => {
      const file = ap1SelectedDocs[docType];
      if (!file) return '';

      const safeName = escapeHtml(file.name);
      const ext = (file.name.split('.').pop() || '').toLowerCase();
      const mime = (file.type || '').toLowerCase();

      let preview = `<div class="text-muted small">Preview not available</div>`;
      if (ext === 'pdf' || mime.includes('pdf')) {
        const url = URL.createObjectURL(file);
        ap1ObjectUrls.push(url);
        preview = `
          <div class="border rounded overflow-hidden" style="height:420px;">
            <iframe src="${url}" style="width:100%;height:100%;border:0;"></iframe>
          </div>
        `;
      } else if (mime.startsWith('image/')) {
        const url = URL.createObjectURL(file);
        ap1ObjectUrls.push(url);
        preview = `
          <div class="border rounded p-2">
            <img src="${url}" alt="${safeName}" style="max-width:100%;height:auto;">
          </div>
        `;
      } else if (ext === 'zip') {
        preview = `<div class="alert alert-light border mb-0">ZIP file attached (cannot preview). (${safeName})</div>`;
      }

      return `
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">${escapeHtml(docType.replaceAll('_',' ').toUpperCase())}</div>
            <div class="small text-muted">${safeName}</div>
          </div>
          ${preview}
        </div>
      `;
    }).join('');

    if (!entries.trim()) {
      return `<div class="alert alert-danger border mb-0"><i class="ri-error-warning-line me-2"></i>No documents have been uploaded.</div>`;
    }

    return entries;
  }

  function showReviewModal() {
    const fd = getFormData();
    const scope = (fd.registration_scope === 'foreign') ? 'Foreign Media House' : 'Local Media House';
    const regionText = document.querySelector('select[name="collection_region"]')?.selectedOptions?.[0]?.text || '-';

    const directorsHtml = (fd.directors || []).length ? `
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle" style="font-size:12px;">
          <thead class="table-light">
            <tr>
              <th>#</th><th>Name</th><th>Surname</th><th>Nationality</th><th>Occupation</th><th>%</th>
              <th>Other Directorships</th><th>Public/Political Office</th><th>Other Shareholdings</th>
              <th>Broadcasting Act</th><th>Postal/Telecom Act</th><th>Advertising Agency</th>
            </tr>
          </thead>
          <tbody>
            ${(fd.directors || []).map((d,i)=>`
              <tr>
                <td>${i+1}</td>
                <td>${escapeHtml(d.name||'-')}</td>
                <td>${escapeHtml(d.surname||'-')}</td>
                <td>${escapeHtml(d.nationality||'-')}</td>
                <td>${escapeHtml(d.occupation||'-')}</td>
                <td>${escapeHtml(d.shareholding_percent||'-')}</td>
                <td>${escapeHtml(d.other_directorships||'-')}${d.companies_concerned ? ' — '+escapeHtml(d.companies_concerned) : ''}</td>
                <td>${escapeHtml(d.public_political_office||'-')}${d.public_political_details ? ' — '+escapeHtml(d.public_political_details) : ''}</td>
                <td>${escapeHtml(d.other_shareholdings||'-')}${d.other_shareholdings_details ? ' — '+escapeHtml(d.other_shareholdings_details) : ''}</td>
                <td>${escapeHtml(d.broadcasting_act_shareholding||'-')}</td>
                <td>${escapeHtml(d.postal_telecom_act_shareholding||'-')}</td>
                <td>${escapeHtml(d.advertising_agency_shareholding||'-')}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    ` : `<div class="alert alert-light border mb-0 small">No directors/shareholding rows provided.</div>`;

    const smHtml = (fd.senior_managers || []).length ? `
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle" style="font-size:12px;">
          <thead class="table-light"><tr><th>#</th><th>Name</th><th>Surname</th><th>Nationality</th><th>Qualifications</th></tr></thead>
          <tbody>
            ${(fd.senior_managers || []).map((m,i)=>`
              <tr>
                <td>${i+1}</td>
                <td>${escapeHtml(m.name||'-')}</td>
                <td>${escapeHtml(m.surname||'-')}</td>
                <td>${escapeHtml(m.nationality||'-')}</td>
                <td>${escapeHtml(m.qualifications||'-')}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    ` : `<div class="alert alert-light border mb-0 small">No senior manager rows provided.</div>`;

    const radioBlock = (fd.mass_media_activity === 'Radio') ? `
      <div class="rv-grid mt-2">
        <div class="rv-field"><span class="rv-label">Titles Published</span><span class="rv-value">${escapeHtml(fd.radio_titles_published||'-')}</span></div>
        <div class="rv-field"><span class="rv-label">Frequency</span><span class="rv-value">${escapeHtml(fd.radio_frequency||'-')}</span></div>
        <div class="rv-field"><span class="rv-label">Circulation Figures</span><span class="rv-value">${escapeHtml(fd.radio_circulation_figures||'-')}</span></div>
        <div class="rv-field"><span class="rv-label">General News</span><span class="rv-value">${escapeHtml(fd.radio_general_news||'-')}</span></div>
        <div class="rv-field"><span class="rv-label">Specialized Info</span><span class="rv-value">${escapeHtml(fd.radio_specialized_info||'-')}</span></div>
        <div class="rv-field"><span class="rv-label">Details</span><span class="rv-value">${escapeHtml(fd.radio_specialized_details||'-')}</span></div>
      </div>
    ` : '';

    const reviewHtml = `
      <style>
        .rv-section { background: white; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; }
        .rv-section-title { display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #1e7e34; margin-bottom: 1rem; padding-bottom: 0.6rem; border-bottom: 2px solid #f1f5f9; }
        .rv-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem 1.5rem; }
        .rv-grid.cols-1 { grid-template-columns: 1fr; }
        .rv-field { display: flex; flex-direction: column; padding: 0.35rem 0; }
        .rv-label { font-size: 0.72rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.15rem; }
        .rv-value { font-size: 0.88rem; color: #1e293b; font-weight: 500; }
        .rv-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #dcfce7; color: #166534; }
        .rv-declaration { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 1rem; font-size: 0.85rem; color: #166534; }
      </style>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-building-2-line"></i> Registration Type</div>
        <div class="rv-grid">
          <div class="rv-field"><span class="rv-label">Type</span><span class="rv-value"><span class="rv-badge">${escapeHtml(scope)}</span></span></div>
          <div class="rv-field"><span class="rv-label">Collection Office</span><span class="rv-value">${escapeHtml(regionText)}</span></div>
        </div>
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-user-3-line"></i> Contact Person</div>
        <div class="rv-grid">
          <div class="rv-field"><span class="rv-label">Name</span><span class="rv-value">${escapeHtml(fd.contact_name||'-')}</span></div>
          <div class="rv-field"><span class="rv-label">Surname</span><span class="rv-value">${escapeHtml(fd.contact_surname||'-')}</span></div>
          <div class="rv-field"><span class="rv-label">Phone</span><span class="rv-value">${escapeHtml((fd.contact_phone_country_code||'')+' '+(fd.contact_phone||'-'))}</span></div>
          <div class="rv-field"><span class="rv-label">Email</span><span class="rv-value">${escapeHtml(fd.contact_email||'-')}</span></div>
          <div class="rv-field cols-1"><span class="rv-label">Address</span><span class="rv-value">${escapeHtml(fd.contact_address||'-')}</span></div>
        </div>
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-organization-chart"></i> Organization Details</div>
        <div class="rv-grid">
          <div class="rv-field"><span class="rv-label">Mass Media Category</span><span class="rv-value">${escapeHtml(fd.mass_media_category||'-')}</span></div>
          ${fd.registration_scope !== 'foreign' ? `
            <div class="rv-field"><span class="rv-label">Mass Media Activity</span><span class="rv-value">${escapeHtml(fd.mass_media_activity||'-')}${fd.mass_media_activity==='Other' ? ' — '+escapeHtml(fd.mass_media_activity_other||'') : ''}</span></div>
          ` : `
            <div class="rv-field"><span class="rv-label">Media House Type</span><span class="rv-value">${escapeHtml(fd.foreign_media_type||'-')}</span></div>
            <div class="rv-field"><span class="rv-label">Organization Name</span><span class="rv-value">${escapeHtml(fd.org_name||'-')}</span></div>
            <div class="rv-field"><span class="rv-label">Head Office</span><span class="rv-value">${escapeHtml(fd.org_head_office||'-')}</span></div>
            <div class="rv-field"><span class="rv-label">Mailing Address</span><span class="rv-value">${escapeHtml(fd.org_mailing_address||'-')}</span></div>
          `}
        </div>
        ${radioBlock}
        ${fd.registration_scope === 'foreign' ? `
          <div class="mt-3 p-3 border rounded" style="background:#f8fafc;">
            <div class="rv-section-title" style="font-size:0.78rem;"><i class="ri-hotel-line"></i> Representative Office</div>
            <div class="rv-grid">
              <div class="rv-field"><span class="rv-label">Office Name</span><span class="rv-value">${escapeHtml(fd.rep_office_name||'-')}</span></div>
              <div class="rv-field"><span class="rv-label">Email</span><span class="rv-value">${escapeHtml(fd.rep_office_email||'-')}</span></div>
              <div class="rv-field"><span class="rv-label">Physical Address</span><span class="rv-value">${escapeHtml(fd.rep_office_address||'-')}</span></div>
              <div class="rv-field"><span class="rv-label">Wholly Owned</span><span class="rv-value">${escapeHtml(fd.rep_office_wholly_owned||'-')}</span></div>
              <div class="rv-field cols-1"><span class="rv-label">Activities</span><span class="rv-value">${escapeHtml(fd.rep_office_activities||'-')}</span></div>
            </div>
          </div>
        ` : ''}
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-team-line"></i> Directors &amp; Shareholding</div>
        ${directorsHtml}
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-user-star-line"></i> Chief Executive Officer</div>
        <div class="rv-grid">
          <div class="rv-field"><span class="rv-label">Name</span><span class="rv-value">${escapeHtml(fd.ceo_name||'-')}</span></div>
          <div class="rv-field"><span class="rv-label">Surname</span><span class="rv-value">${escapeHtml(fd.ceo_surname||'-')}</span></div>
          <div class="rv-field"><span class="rv-label">Nationality</span><span class="rv-value">${escapeHtml(fd.ceo_nationality||'-')}</span></div>
          <div class="rv-field"><span class="rv-label">Qualifications</span><span class="rv-value">${escapeHtml(fd.ceo_qualifications||'-')}</span></div>
          <div class="rv-field cols-1"><span class="rv-label">Experience</span><span class="rv-value">${escapeHtml(fd.ceo_experience||'-')}</span></div>
        </div>
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-team-line"></i> Senior Managers</div>
        ${smHtml}
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-questionnaire-line"></i> Compliance Questions</div>
        <div class="rv-grid">
          <div class="rv-field"><span class="rv-label">Convicted Offence</span><span class="rv-value">${escapeHtml(fd.q_convicted||'-')}${fd.q_convicted_details ? ' — '+escapeHtml(fd.q_convicted_details) : ''}</span></div>
          <div class="rv-field"><span class="rv-label">Judgment Debt</span><span class="rv-value">${escapeHtml(fd.q_judgment_debt||'-')}${fd.q_judgment_debt_details ? ' — '+escapeHtml(fd.q_judgment_debt_details) : ''}</span></div>
          <div class="rv-field"><span class="rv-label">Insolvency (a/b/c/d)</span><span class="rv-value">${escapeHtml(fd.q_insolvent_a||'-')} / ${escapeHtml(fd.q_insolvent_b||'-')} / ${escapeHtml(fd.q_insolvent_c||'-')} / ${escapeHtml(fd.q_insolvent_d||'-')}</span></div>
          ${fd.q_insolvent_details ? `<div class="rv-field"><span class="rv-label">Insolvency Details</span><span class="rv-value">${escapeHtml(fd.q_insolvent_details)}</span></div>` : ''}
          ${fd.other_relevant_info ? `<div class="rv-field cols-1"><span class="rv-label">Other Relevant Info</span><span class="rv-value">${escapeHtml(fd.other_relevant_info)}</span></div>` : ''}
        </div>
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-attachment-2"></i> Uploaded Documents</div>
        ${buildDocsPreviewHtml()}
      </div>

      <div class="rv-section">
        <div class="rv-section-title"><i class="ri-shield-check-line"></i> Declaration</div>
        <div class="rv-declaration">
          <i class="ri-double-quotes-l" style="color:#1e7e34;font-size:1.25rem;"></i>
          <p class="mb-2 mt-1">I declare that all the information given above, to the best of my knowledge is true and complete.</p>
          <div class="rv-field"><span class="rv-label">Agreed</span><span class="rv-value">${fd.declaration_agreed ? '<span class="rv-badge">Yes — Confirmed</span>' : '<span style="color:#dc2626;">Not confirmed</span>'}</span></div>
          <div class="rv-field mt-1"><span class="rv-label">Date &amp; Time Submitted</span><span class="rv-value">${escapeHtml(fd.ap1_declaration_date || fd.declaration_date || '-')}</span></div>
        </div>
      </div>
    `;

    document.getElementById('ap1ReviewContent').innerHTML = reviewHtml;

    const modalEl = document.getElementById('ap1ReviewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    modalEl.addEventListener('hidden.bs.modal', function cleanup() {
      ap1ObjectUrls.forEach(u => { try { URL.revokeObjectURL(u); } catch(e){} });
      ap1ObjectUrls.length = 0;
      modalEl.removeEventListener('hidden.bs.modal', cleanup);
    });
  }

  // ===== Save Draft (multipart + files) =====
  async function saveDraft() {
    const data = getFormData();
    const region = document.querySelector('select[name="collection_region"]')?.value || '';

    const btn = document.getElementById('ap1SaveDraftBtn');
    try {
      btn.disabled = true;
      btn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';

      const fd = new FormData();
      fd.append('collection_region', region);
      fd.append('form_data', JSON.stringify(data));

      document.querySelectorAll('#ap1Form input[type="file"][name^="documents"]').forEach(input => {
        if (input.files && input.files[0]) fd.append(input.name, input.files[0]);
      });

      const res = await fetch('{{ route("mediahouse.saveDraft") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: fd,
      });

      if (!res.ok) {
        const errText = await res.text();
        console.error('Draft save failed:', errText);
        throw new Error('Server returned an error (' + res.status + ').');
      }

      const json = await res.json();
      if (json.success) {
        alert('Draft saved successfully (including uploads).');
      } else {
        alert(json.message || 'Failed to save draft.');
      }
    } catch (e) {
      console.error(e);
      alert('An error occurred while saving draft.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="ri-save-line"></i> Save Draft';
    }
  }

  function getAppFeeData() {
    const refTab = document.getElementById('appfee-pane-ref');
    const isRefActive = refTab && refTab.classList.contains('show');

    if (isRefActive) {
      const ref = document.getElementById('appfee_paynow_ref')?.value?.trim();
      if (!ref) return null;
      return { type: 'paynow_ref', paynow_reference: ref };
    } else {
      const firstName = document.getElementById('appfee_first_name')?.value?.trim();
      const lastName = document.getElementById('appfee_last_name')?.value?.trim();
      const paymentDate = document.getElementById('appfee_payment_date')?.value?.trim();
      const amountPaid = document.getElementById('appfee_amount_paid')?.value?.trim();
      const bankName = document.getElementById('appfee_bank_name')?.value?.trim();
      const proofFile = document.getElementById('appfee_proof_file')?.files?.[0];

      if (!firstName || !lastName || !paymentDate || !amountPaid || !bankName || !proofFile) return null;

      return {
        type: 'proof',
        first_name: firstName,
        last_name: lastName,
        payment_date: paymentDate,
        amount_paid: amountPaid,
        bank_name: bankName,
        proof_file: proofFile,
      };
    }
  }

  async function submitApplication() {
    // Auto-populate declaration date and time
    const now = new Date();
    const dateTimeString = now.toLocaleString('en-GB', {
      day: '2-digit', month: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit',
      hour12: false
    });
    const declField = document.getElementById('ap1_declaration_date');
    if (declField) declField.value = dateTimeString;

    const data = getFormData();
    const region = document.querySelector('select[name="collection_region"]')?.value || '';
    const btn = document.getElementById('ap1ConfirmSubmitBtn');

    const isCorrection = @json(isset($draft) && !$draft->is_draft && ($draft->status ?? null) === \App\Models\Application::CORRECTION_REQUESTED);

    const appFeeData = getAppFeeData();
    if (!isCorrection && !appFeeData) {
      alert('Application fee is required. Please provide a PayNow reference or upload proof of payment.');
      return;
    }

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 120000); // 2 min timeout

    try {
      btn.disabled = true;
      btn.innerHTML = '<i class="ri-loader-4-line"></i> Submitting...';

      const fd = new FormData();
      fd.append('collection_region', region);
      fd.append('form_data', JSON.stringify(data));

      if (!isCorrection && appFeeData) {
        fd.append('app_fee_type', appFeeData.type);
        if (appFeeData.type === 'paynow_ref') {
          fd.append('app_fee_paynow_ref', appFeeData.paynow_reference);
        } else {
          fd.append('app_fee_first_name', appFeeData.first_name);
          fd.append('app_fee_last_name', appFeeData.last_name);
          fd.append('app_fee_payment_date', appFeeData.payment_date);
          fd.append('app_fee_amount_paid', appFeeData.amount_paid);
          fd.append('app_fee_bank_name', appFeeData.bank_name);
          fd.append('app_fee_proof_file', appFeeData.proof_file);
        }
      }

      document.querySelectorAll('#ap1Form input[type="file"][name^="documents"]').forEach(input => {
        if (input.files && input.files[0]) fd.append(input.name, input.files[0]);
      });

      const submitUrl = isCorrection
        ? @json(isset($draft) && !$draft->is_draft ? route('mediahouse.applications.resubmit', $draft ?? 0) : route('mediahouse.submit'))
        : @json(route('mediahouse.submit'));

      const res = await fetch(submitUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: fd,
        signal: controller.signal,
      });

      if (!res.ok) {
        const errText = await res.text();
        console.error('Submission failed:', errText);
        throw new Error('Server returned an error (' + res.status + ').');
      }

      const json = await res.json();
      if (json.success) {
        bootstrap.Modal.getInstance(document.getElementById('ap1ReviewModal'))?.hide();

        // Show payment modal
        const appId = json.application_id;
        if (appId) {
          document.getElementById('ap1PayModalAppId').value = appId;
          document.getElementById('ap1PayModalRef').textContent = json.reference;
          const payModal = new bootstrap.Modal(document.getElementById('ap1PaymentModal'));
          payModal.show();
        } else {
          alert('Application submitted successfully! Reference: ' + json.reference);
          window.location.href = "{{ route('mediahouse.portal') }}";
        }
      } else {
        alert(json.message || 'Failed to submit.');
      }
    } catch (e) {
      if (e.name === 'AbortError') {
        alert('Submission timed out after 2 minutes. Please check your connection and try again.');
      } else {
        console.error(e);
        alert('An error occurred while submitting: ' + e.message);
      }
    } finally {
      clearTimeout(timeoutId);
      btn.disabled = false;
      btn.innerHTML = '<i class="ri-send-plane-line me-2"></i>Confirm & Submit';
    }
  }

  // ===== Scope selection cards =====
  function bindScopeCards() {
    document.querySelectorAll('#new-registration-page .app-type-card').forEach(card => {
      card.addEventListener('click', () => {
        document.querySelectorAll('#new-registration-page .app-type-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        document.getElementById('ap1_registration_scope').value = card.dataset.type;
        applyScopeVisibility();
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    ap1UpdateSteps();
    ap1SetupUploads();
    bindScopeCards();

    // default rows
    addDirectorRow();
    addSeniorManagerRow();

    // activity dynamic
    document.getElementById('mass_media_activity')?.addEventListener('change', applyActivityVisibility);
    applyActivityVisibility();

    // nav
    document.getElementById('ap1PrevBtn')?.addEventListener('click', () => {
      ap1Step = Math.max(1, ap1Step - 1);
      ap1UpdateSteps();
    });

    document.getElementById('ap1NextBtn')?.addEventListener('click', () => {
      if (!ap1ValidateStep()) return;
      if (ap1Step === 7) { showReviewModal(); return; }
      ap1Step = Math.min(7, ap1Step + 1);
      ap1UpdateSteps();
    });

    document.getElementById('ap1SaveDraftBtn')?.addEventListener('click', saveDraft);
    document.getElementById('ap1ConfirmSubmitBtn')?.addEventListener('click', submitApplication);

    document.getElementById('addDirectorRowBtn')?.addEventListener('click', addDirectorRow);
    document.getElementById('addSeniorManagerBtn')?.addEventListener('click', addSeniorManagerRow);

    // Draft restore
    @if(isset($draft) && $draft)
      const draftData = @json($draft->form_data ?? []);
      if (draftData && typeof draftData === 'object') {
        // fill plain fields
        Object.keys(draftData).forEach(key => {
          if (key === 'directors' || key === 'senior_managers') return;

          const el = document.querySelector(`[name="${key}"]`) || document.getElementById(key);
          if (!el) return;

          if (el.type === 'radio') {
            const r = document.querySelector(`[name="${key}"][value="${draftData[key]}"]`);
            if (r) r.checked = true;
          } else if (el.type === 'checkbox') {
            el.checked = !!draftData[key];
          } else {
            el.value = draftData[key];
          }
        });

        // scope card highlight
        if (draftData.registration_scope) {
          const scopeCard = document.querySelector(`.app-type-card[data-type="${draftData.registration_scope}"]`);
          if (scopeCard) scopeCard.click();
        }

        // directors
        if (Array.isArray(draftData.directors)) {
          const tbody = document.getElementById('directorsRows');
          if (tbody) tbody.innerHTML = '';
          directorIdx = 0;
          draftData.directors.forEach(() => addDirectorRow());

          const rows = document.querySelectorAll('#directorsRows tr');
          draftData.directors.forEach((d, i) => {
            const tr = rows[i];
            if (!tr) return;

            tr.querySelector('[name="directors[name][]"]').value = d.name || '';
            tr.querySelector('[name="directors[surname][]"]').value = d.surname || '';
            tr.querySelector('[name="directors[address][]"]').value = d.address || '';
            tr.querySelector('[name="directors[occupation][]"]').value = d.occupation || '';
            tr.querySelector('[name="directors[nationality][]"]').value = d.nationality || '';
            tr.querySelector('[name="directors[shareholding_percent][]"]').value = d.shareholding_percent || '';

            tr.querySelector('[name="directors[other_directorships][]"]').value = d.other_directorships || '';
            tr.querySelector('[name="directors[companies_concerned][]"]').value = d.companies_concerned || '';

            tr.querySelector('[name="directors[public_political_office][]"]').value = d.public_political_office || '';
            tr.querySelector('[name="directors[public_political_details][]"]').value = d.public_political_details || '';

            tr.querySelector('[name="directors[other_shareholdings][]"]').value = d.other_shareholdings || '';
            tr.querySelector('[name="directors[other_shareholdings_details][]"]').value = d.other_shareholdings_details || '';

            tr.querySelector('[name="directors[broadcasting_act_shareholding][]"]').value = d.broadcasting_act_shareholding || '';
            tr.querySelector('[name="directors[postal_telecom_act_shareholding][]"]').value = d.postal_telecom_act_shareholding || '';
            tr.querySelector('[name="directors[advertising_agency_shareholding][]"]').value = d.advertising_agency_shareholding || '';
          });

          applyDirectorDependencies();
        }

        // senior managers
        if (Array.isArray(draftData.senior_managers)) {
          const tbody = document.getElementById('seniorManagersRows');
          if (tbody) tbody.innerHTML = '';
          smIdx = 0;
          draftData.senior_managers.forEach(() => addSeniorManagerRow());

          const rows = document.querySelectorAll('#seniorManagersRows tr');
          draftData.senior_managers.forEach((m, i) => {
            const tr = rows[i];
            if (!tr) return;
            tr.querySelector('[name="senior_managers[name][]"]').value = m.name || '';
            tr.querySelector('[name="senior_managers[surname][]"]').value = m.surname || '';
            tr.querySelector('[name="senior_managers[nationality][]"]').value = m.nationality || '';
            tr.querySelector('[name="senior_managers[qualifications][]"]').value = m.qualifications || '';
          });
        }

        // representative office shareholders
        if (Array.isArray(draftData.rep_office_shareholders)) {
          const tbody = document.getElementById('repOfficeShareholdersRows');
          if (tbody) tbody.innerHTML = '';
          repShareIdx = 0;
          draftData.rep_office_shareholders.forEach(() => addRepShareholderRow());

          const rows = document.querySelectorAll('#repOfficeShareholdersRows tr');
          draftData.rep_office_shareholders.forEach((s, i) => {
            const tr = rows[i];
            if (!tr) return;
            tr.querySelector('[name="rep_office_shareholders[name][]"]').value = s.name || '';
            tr.querySelector('[name="rep_office_shareholders[percent][]"]').value = s.percent || '';
          });
        }

        // activity visibility
        applyActivityVisibility();
        applyScopeVisibility();
        applyWhollyOwnedVisibility();
      }
    @endif

    // Bind wholly owned toggle
    document.querySelectorAll('.wholly-owned-toggle').forEach(r => {
        r.addEventListener('change', applyWhollyOwnedVisibility);
    });
    document.getElementById('addRepShareholderBtn')?.addEventListener('click', addRepShareholderRow);

  });
</script>

<style>
  .section-card{
    background:#fff;
    border:1px solid #eef2f7;
    border-radius:14px;
    padding:14px;
  }
  .section-title{
    font-weight:800;
    color:#0f172a;
    margin-bottom:10px;
    display:flex;
    align-items:center;
  }
  .section-subcard{
    margin-top:12px;
    padding:12px;
    border:1px dashed #cbd5e1;
    border-radius:12px;
    background:#f8fafc;
  }
  .subcard-title{font-weight:800;margin-bottom:10px;color:#334155;display:flex;align-items:center;}

  .app-type-container{margin-top:10px;}
  .app-type-cards{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
  @media(max-width:768px){.app-type-cards{grid-template-columns:1fr;}}
  .app-type-card{
    border:1px solid #e5e7eb;border-radius:14px;padding:14px;
    cursor:pointer;background:#fff;transition:.15s;
  }
  .app-type-card:hover{transform:translateY(-1px);box-shadow:0 .25rem .8rem rgba(0,0,0,.06);}
  .app-type-card.selected{border-color:#2563eb;background:rgba(37,99,235,.06);}
  .app-type-card i{font-size:26px;color:#2563eb;}
  .app-type-card h4{margin:8px 0 4px;font-weight:900;color:#0f172a;}
  .app-type-card p{margin:0;color:#64748b;font-size:13px;}

  .q-block{padding:10px;border:1px solid #eef2f7;border-radius:12px;background:#fff;}

  .upload-area{
    border:2px dashed #cbd5e1;border-radius:14px;padding:12px;text-align:center;background:#fff;
  }
  .upload-area i{font-size:22px;color:#2563eb;}
  .upload-area h5{font-size:14px;font-weight:800;margin:6px 0 0;}
  .upload-area p{font-size:12px;color:#64748b;margin:4px 0 10px;}
  .upload-btn{border:1px solid #cbd5e1;background:#fff;border-radius:10px;padding:8px 12px;font-weight:700;}
</style>

<script>
// ===== AP1 Payment Modal JS =====
(function(){
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
  let ap1PollInterval = null;

  document.querySelectorAll('#ap1PayTabs .pay-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#ap1PayTabs .pay-tab-btn').forEach(b => {
        b.style.borderColor = '#e5e7eb'; b.style.background = '#fff'; b.style.color = '#6b7280';
      });
      btn.style.borderColor = '#28a745'; btn.style.background = '#f0fdf4'; btn.style.color = '#1e7e34';
      document.querySelectorAll('#ap1PaymentModal .pay-tab-content').forEach(c => c.style.display = 'none');
      const tab = document.getElementById('ap1PaymentModal-tab-' + btn.dataset.tab);
      if (tab) tab.style.display = 'block';
    });
  });

  function getAppId() { return document.getElementById('ap1PayModalAppId')?.value; }

  document.getElementById('ap1PayNowWebBtn')?.addEventListener('click', async () => {
    const appId = getAppId(); if (!appId) return;
    const btn = document.getElementById('ap1PayNowWebBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
    try {
      const res = await fetch('/payments/' + appId + '/initiate', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({}),
      });
      const data = await res.json().catch(() => ({}));
      if (data.success && data.redirect_url) {
        window.location.href = data.redirect_url;
      } else {
        alert(data.message || 'PayNow initiation failed. Please try again or use Upload Proof.');
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-external-link-line me-2"></i>Pay with PayNow (Card / ZimSwitch)';
      }
    } catch(e) {
      alert('Network error. Please try again.');
      btn.disabled = false;
      btn.innerHTML = '<i class="ri-external-link-line me-2"></i>Pay with PayNow (Card / ZimSwitch)';
    }
  });

  document.getElementById('ap1PayNowMobileBtn')?.addEventListener('click', async () => {
    const appId = getAppId();
    const phone = document.getElementById('ap1MobilePhone')?.value?.trim();
    const method = document.getElementById('ap1MobileMethod')?.value;
    if (!phone || !/^0[7][0-9]{8}$/.test(phone)) {
      alert('Please enter a valid Zimbabwean mobile number (07XXXXXXXX).');
      return;
    }
    const btn = document.getElementById('ap1PayNowMobileBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    try {
      const res = await fetch('/payments/' + appId + '/initiate-mobile', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone, method }),
      });
      const data = await res.json().catch(() => ({}));
      if (data.success) {
        document.getElementById('ap1PollArea').style.display = 'block';
        ap1PollInterval = setInterval(() => ap1CheckStatus(), 5000);
      } else {
        alert(data.message || 'Mobile payment failed. Please try again.');
      }
    } catch(e) { alert('Network error.'); }
    finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="ri-smartphone-line me-2"></i>Send Mobile Payment Request';
    }
  });

  document.getElementById('ap1PollCheckBtn')?.addEventListener('click', () => ap1CheckStatus());

  async function ap1CheckStatus() {
    const appId = getAppId(); if (!appId) return;
    try {
      const res = await fetch('/payments/' + appId + '/status', { headers: { 'Accept': 'application/json' } });
      const data = await res.json().catch(() => ({}));
      const msg = document.getElementById('ap1PollMsg');
      if (data.status === 'paid') {
        if (ap1PollInterval) clearInterval(ap1PollInterval);
        if (msg) msg.textContent = 'Payment confirmed! Redirecting...';
        setTimeout(() => window.location.href = "{{ route('mediahouse.portal') }}", 1500);
      } else if (data.status === 'failed') {
        if (ap1PollInterval) clearInterval(ap1PollInterval);
        if (msg) msg.textContent = 'Payment failed. Please try again.';
      } else {
        if (msg) msg.textContent = 'Waiting for confirmation... (' + (data.status || 'pending') + ')';
      }
    } catch(e) { /* silent */ }
  }

  document.getElementById('ap1ProofSubmitBtn')?.addEventListener('click', async () => {
    const appId  = getAppId();
    const fname  = document.getElementById('ap1ProofFname')?.value?.trim();
    const lname  = document.getElementById('ap1ProofLname')?.value?.trim();
    const date   = document.getElementById('ap1ProofDate')?.value;
    const amount = document.getElementById('ap1ProofAmount')?.value;
    const bank   = document.getElementById('ap1ProofBank')?.value?.trim();
    const file   = document.getElementById('ap1ProofFile')?.files?.[0];
    if (!fname || !lname || !date || !amount || !bank || !file) {
      alert('Please fill in all required fields and upload your proof of payment.');
      return;
    }
    const fd = new FormData();
    fd.append('proof_first_name', fname);
    fd.append('proof_last_name', lname);
    fd.append('proof_payment_date', date);
    fd.append('proof_amount_paid', amount);
    fd.append('proof_bank_name', bank);
    fd.append('proof_file', file);
    const btn = document.getElementById('ap1ProofSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    try {
      const res = await fetch('/payments/' + appId + '/upload-proof', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: fd,
      });
      const data = await res.json().catch(() => ({}));
      if (data.ok) {
        bootstrap.Modal.getInstance(document.getElementById('ap1PaymentModal'))?.hide();
        alert('Proof submitted! An Accounts Officer will verify it and generate your receipt.');
        window.location.href = "{{ route('mediahouse.portal') }}";
      } else {
        alert(data.message || 'Upload failed. Please try again.');
      }
    } catch(e) { alert('Network error.'); }
    finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="ri-upload-cloud-line me-2"></i>Submit Proof of Payment';
    }
  });
})();
</script>

@endpush
