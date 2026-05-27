@extends('layouts.portal')

@section('title', 'Renewal / Replacement of Registration (AP5)')

@section('content')
<div id="renewal-page">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Renewal / Replacement of Registration (AP5)</h4>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Renewal / Replacement</h1>
      <p class="m-0">
        Complete this digital AP5 form to renew your registration or request replacement of a certificate/document.
      </p>
    </div>

    <div class="form-steps-container">
    @if(isset($drafts) && $drafts->count())
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="ri-draft-line me-1"></i>My Saved Drafts</h6>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Reference</th>
                  <th>Type</th>
                  <th>Saved</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($drafts as $d)
                  <tr>
                    <td class="fw-semibold">{{ $d->reference }}</td>
                    <td class="text-capitalize">{{ $d->request_type }}</td>
                    <td class="text-muted">{{ $d->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="text-end">
                      <a class="btn btn-sm btn-primary" href="{{ route('mediahouse.renewals', ['draft' => $d->reference]) }}">
                        <i class="ri-edit-line me-1"></i>Continue Editing
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="text-muted mt-2" style="font-size:12px;">Drafts remain here until you submit. Once submitted, they disappear from this list.</div>
        </div>
      </div>
    @endif



      {{-- STEP PROGRESS --}}
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Application Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Contact Details</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Registration Info</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Documents</div>
          </div>
          <div class="step" data-step="5">
            <div class="step-number">5</div>
            <div class="step-label">Declaration</div>
          </div>
        </div>
      </div>

      {{-- STEP 1: APPLICATION TYPE (CARDS STYLE) --}}
      <div class="step-content active" id="ap5-step-1">
        <h3 class="step-title">Select Application Type</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Choose whether you are renewing your registration or requesting a replacement. If replacement, choose a reason.
        </div>

        <div class="app-type-container">
          <div class="app-type-cards">
            <div class="app-type-card" data-ap5-type="renewal" role="button" tabindex="0">
              <i class="ri-refresh-line"></i>
              <h4>Renewal of Registration</h4>
              <p>For annual renewal of your Mass Media Service registration.</p>
              <div style="margin-top:15px;">
                <span class="badge bg-light text-dark">Typical Processing: 14 Days</span>
                <span class="badge bg-light text-dark">Updates Allowed</span>
              </div>
            </div>

            <div class="app-type-card" data-ap5-type="replacement" role="button" tabindex="0">
              <i class="ri-exchange-line"></i>
              <h4>Replacement of Registration Certificate</h4>
              <p>For lost, damaged, or stolen registration certificate/document.</p>
              <div style="margin-top:15px;">
                <span class="badge bg-light text-dark">Affidavit Required</span>
                <span class="badge bg-light text-dark">Police Report (If Stolen)</span>
              </div>
            </div>
          </div>
        </div>

        {{-- This is what we submit to backend --}}
        <input type="hidden" id="ap5AppType" name="request_type" value="">

        {{-- Replacement Reason (Conditional) --}}
        <div id="ap5-replacement-reason" style="display:none; margin-top:22px;">
          <label class="form-label required">Reason for Replacement</label>
          <div class="checkbox-group">
            <div class="checkbox-item">
              <input type="radio" id="ap5-reason-lost" name="replacement_reason" value="lost">
              <label for="ap5-reason-lost">Lost</label>
            </div>
            <div class="checkbox-item">
              <input type="radio" id="ap5-reason-damaged" name="replacement_reason" value="damaged">
              <label for="ap5-reason-damaged">Damaged</label>
            </div>
            <div class="checkbox-item">
              <input type="radio" id="ap5-reason-stolen" name="replacement_reason" value="stolen">
              <label for="ap5-reason-stolen">Stolen</label>
            </div>
          </div>

          <div class="alert alert-warning mt-3 mb-0">
            <i class="ri-alert-line me-2"></i>
            <strong>Important:</strong> For stolen certificates, a police report is required.
          </div>
        </div>
      </div>

      {{-- STEP 2: CONTACT DETAILS --}}
      <div class="step-content" id="ap5-step-2">
        <h3 class="step-title">Contact Information</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Provide your organization’s communication details for follow-up.
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Contact Name</label>
            <input type="text" class="form-control" id="ap5ContactName" name="contact_name" placeholder="Full name" required>
          </div>
          <div class="form-field">
            <label class="form-label required">Contact Phone</label>
            <input type="text" class="form-control" id="ap5ContactPhone" name="contact_phone" placeholder="+263 77 000 0000" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Contact Address</label>
            <textarea class="form-control" id="ap5ContactAddress" name="contact_address" rows="3" required></textarea>
          </div>
          <div class="form-field">
            <label class="form-label required">Email Address</label>
            <input type="email" class="form-control" id="ap5ContactEmail" name="contact_email" placeholder="email@example.com" required>
            <div class="form-hint">Notifications and invoices will be sent here.</div>
          </div>
        </div>
      </div>

      {{-- STEP 3: REGISTRATION INFO --}}
      <div class="step-content" id="ap5-step-3">
        <h3 class="step-title">Registration Information</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Provide your previous registration details and indicate any changes.
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Registered Entity / Service Name</label>
            <input type="text" class="form-control" id="ap5EntityName" name="entity_name" placeholder="Organization / Service name" required>
          </div>
          <div class="form-field">
            <label class="form-label required">Previous Registration Reference</label>
            <input type="text" class="form-control" id="ap5PrevRef" name="previous_reference" placeholder="e.g. ZMC-AP1-2023-031" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Head Office Address</label>
            <textarea class="form-control" id="ap5HeadOffice" name="head_office" rows="3" required></textarea>
          </div>
          <div class="form-field">
            <label class="form-label required">Postal Address</label>
            <textarea class="form-control" id="ap5Postal" name="postal_address" rows="3" required></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Any changes to particulars since last registration?</label>
            <select class="form-control" id="ap5Changes" name="changes" required>
              <option value="no">No</option>
              <option value="yes">Yes</option>
            </select>
          </div>
          <div class="form-field" id="ap5ChangesDetailsWrap" style="display:none;">
            <label class="form-label required">Details of Changes</label>
            <textarea class="form-control" id="ap5ChangesDetails" name="changes_details" rows="3" placeholder="Describe changes"></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Regional Office for Collection</label>
            <select class="form-control" id="ap5Office" name="collection_region" required>
              <option value="">Select office</option>
              <option value="harare">Harare - 108 Swan Drive, Alexandra Park</option>
              <option value="bulawayo">Bulawayo - Main Office</option>
              <option value="mutare">Mutare - Regional Office</option>
              <option value="masvingo">Masvingo - Regional Office</option>
            </select>
            <div class="form-hint">Your document/certificate will be available at the selected office.</div>
          </div>
        </div>
      </div>

      {{-- STEP 4: DOCUMENTS --}}
      <div class="step-content" id="ap5-step-4">
        <h3 class="step-title">Required Documents</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Upload all required documents. Requirements depend on Renewal vs Replacement.
        </div>

        {{-- COMMON --}}
        <div class="form-row">
          <div class="form-field">
            <label class="form-label required">Proof of Payment / Payment Advice (if applicable)</label>
            <div class="upload-area">
              <i class="ri-bank-card-line"></i>
              <h5>Upload Proof of Payment</h5>
              <p>PDF/JPG/PNG</p>
              {{-- ✅ IMPORTANT: name attribute --}}
              <input type="file" name="proof_of_payment" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
              <button type="button" class="upload-btn">Choose File</button>
            </div>
            <div class="uploaded-files"></div>
          </div>
        </div>

        {{-- RENEWAL DOCS --}}
        <div id="ap5-renewal-docs">
            <div class="form-field">
              <label class="form-label required">Current Registration Certificate / Document</label>
              <div class="upload-area">
                <i class="ri-file-shield-2-line"></i>
                <h5>Upload Current Certificate</h5>
                <p>Front/back scan if applicable</p>
                <input type="file" name="current_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                <button type="button" class="upload-btn">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Official Request Letter for Renewal</label>
              <div class="upload-area">
                <i class="ri-mail-send-line"></i>
                <h5>Upload Request Letter</h5>
                <p>Signed letter on company letterhead</p>
                <input type="file" name="official_request_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                <button type="button" class="upload-btn">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label">Updated Supporting Documents (if changes were declared)</label>
              <div class="upload-area">
                <i class="ri-attachment-2"></i>
                <h5>Upload Supporting Docs</h5>
                <p>Optional (only if changes)</p>
                {{-- ✅ IMPORTANT: name attribute --}}
                <input type="file" name="supporting_docs" accept=".pdf,.jpg,.jpeg,.png,.zip" style="display:none;">
                <button type="button" class="upload-btn">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
          </div>
        </div>

        {{-- REPLACEMENT DOCS --}}
        <div id="ap5-replacement-docs" style="display:none;">
          <div class="alert alert-warning mb-3">
            <i class="ri-alert-line me-2"></i>
            <strong>Replacement Requirements:</strong> Affidavit required for lost/stolen. Police report required for stolen.
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Affidavit for Lost/Stolen</label>
              <div class="upload-area">
                <i class="ri-scales-3-line"></i>
                <h5>Upload Affidavit</h5>
                <p>PDF/JPG/PNG</p>
                {{-- ✅ IMPORTANT: name attribute --}}
                <input type="file" name="affidavit" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                <button type="button" class="upload-btn">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label" id="ap5PoliceLabel">Police Report (Required if stolen)</label>
              <div class="upload-area">
                <i class="ri-shield-check-line"></i>
                <h5>Upload Police Report</h5>
                <p>PDF/JPG/PNG</p>
                {{-- ✅ IMPORTANT: name attribute --}}
                <input type="file" name="police_report" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" id="ap5PoliceInput">
                <button type="button" class="upload-btn">Choose File</button>
              </div>
              <div class="uploaded-files"></div>
              <div class="form-hint" id="ap5PoliceHint">If the reason is "Stolen", this becomes mandatory.</div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label">Damaged Certificate Return</label>
              <div class="checkbox-group">
                <div class="checkbox-item">
                  <input type="checkbox" id="ap5ReturnDamaged" name="return_damaged">
                  <label for="ap5ReturnDamaged">I will return the damaged certificate/document to ZMC offices</label>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      {{-- STEP 5: DECLARATION --}}
      <div class="step-content" id="ap5-step-5">
        <h3 class="step-title">Declaration & Submission</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Confirm that all details are correct before submitting.
        </div>

        <div class="alert alert-warning">
          <h6 class="mb-2"><i class="ri-file-text-line me-2"></i>Declaration</h6>
          <p class="mb-2">I declare that the information provided in this AP5 application is true and complete.</p>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="ap5Agree" name="declaration_confirmed" value="1" required>
            <label class="form-check-label" for="ap5Agree">
              I agree to the declaration and terms.
            </label>
          </div>
        </div>

        <div class="alert alert-info mb-0">
          <i class="ri-information-line me-2"></i>
          Digital signature is not required. You will review your information before submitting.
        </div>
      </div>

      {{-- NAV --}}
      <div class="form-buttons">
        <button type="button" class="btn btn-secondary" id="ap5PrevBtn">
          <i class="ri-arrow-left-line"></i> Previous
        </button>
        <button type="button" class="btn btn-outline-primary" id="ap5SaveDraftBtn">
          <i class="ri-save-3-line"></i> Save as Draft
        </button>
        <button type="button" class="btn btn-primary" id="ap5NextBtn">
          Next <i class="ri-arrow-right-line"></i>
        </button>
      </div>

      {{-- Review Modal --}}
      <div class="modal fade" id="ap5ReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold">Review Before Submitting</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div id="ap5ReviewBody"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Edit</button>
              <button type="button" class="btn btn-primary" id="ap5ConfirmSubmitBtn">
                <i class="ri-check-line"></i> Confirm & Submit
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  let ap5Step = 1;

  function ap5SetDefaultDate() {
    // Digital signature/date removed; keep as no-op for backward compatibility
    return;
  }

  function ap5UpdateSteps() {
    const steps = document.querySelectorAll('#renewal-page .step');
    const contents = document.querySelectorAll('#renewal-page .step-content');

    steps.forEach(s => {
      const n = parseInt(s.dataset.step, 10);
      s.classList.remove('active','completed');
      const num = s.querySelector('.step-number');

      if (n < ap5Step) {
        s.classList.add('completed');
        if (num) num.innerHTML = '<i class="ri-check-line"></i>';
      } else {
        if (num) num.textContent = String(n);
      }

      if (n === ap5Step) s.classList.add('active');
    });

    contents.forEach(c => c.classList.remove('active'));
    document.getElementById('ap5-step-' + ap5Step)?.classList.add('active');

    const prev = document.getElementById('ap5PrevBtn');
    const next = document.getElementById('ap5NextBtn');

    if (prev) prev.style.display = (ap5Step === 1) ? 'none' : 'inline-block';
    if (next) next.innerHTML = (ap5Step === 5)
      ? 'Submit Application <i class="ri-send-plane-line"></i>'
      : 'Next <i class="ri-arrow-right-line"></i>';
    // track current step for draft saves
    const stepField = document.getElementById('ap5_current_step');
    if (stepField) stepField.value = String(ap5Step);
  }

  function ap5SetupUploads() {
    document.querySelectorAll('#renewal-page .upload-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input[type="file"]');
        if (input) input.click();
      });
    });

    document.querySelectorAll('#renewal-page .upload-area input[type="file"]').forEach(input => {
      input.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;

        const area = this.closest('.upload-area');
        if (area) {
          area.style.borderColor = '#10b981';
          area.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
        }

        const list = area?.parentElement?.querySelector('.uploaded-files');
        if (list) {
          const fileName = file.name.length > 28 ? file.name.slice(0, 28) + '...' : file.name;
          const size = (file.size / 1024).toFixed(1) + ' KB';

          list.innerHTML = `
            <div class="uploaded-file">
              <div class="file-info">
                <i class="ri-file-text-line file-icon"></i>
                <div>
                  <div class="file-name">${fileName}</div>
                  <div class="file-size">${size}</div>
                </div>
              </div>
              <button type="button" class="file-remove"><i class="ri-close-line"></i></button>
            </div>
          `;

          list.querySelector('.file-remove')?.addEventListener('click', function() {
            list.innerHTML = '';
            if (area) { area.style.borderColor = ''; area.style.backgroundColor = ''; }
            input.value = '';
          });
        }
      });
    });
  }

  function ap5SetType(type) {
    const cards = document.querySelectorAll('#renewal-page .app-type-card[data-ap5-type]');
    const typeInput = document.getElementById('ap5AppType');
    const reasonWrap = document.getElementById('ap5-replacement-reason');

    cards.forEach(c => c.classList.remove('selected'));
    const selected = document.querySelector(`#renewal-page .app-type-card[data-ap5-type="${type}"]`);
    if (selected) selected.classList.add('selected');

    if (typeInput) typeInput.value = type;

    // Toggle reason block + docs
    if (reasonWrap) reasonWrap.style.display = (type === 'replacement') ? 'block' : 'none';
    const renewalDocs = document.getElementById('ap5-renewal-docs');
    const replacementDocs = document.getElementById('ap5-replacement-docs');
    if (renewalDocs) renewalDocs.style.display = (type === 'replacement') ? 'none' : 'block';
    if (replacementDocs) replacementDocs.style.display = (type === 'replacement') ? 'block' : 'none';

    // Clear reason if switching away
    if (type !== 'replacement') {
      document.querySelectorAll('input[name="replacement_reason"]').forEach(r => r.checked = false);
    }

    ap5SyncPoliceRequirement();
  }

  function checkRequestSize(formData) {
    let totalSize = 0;
    for (let pair of formData.entries()) {
      if (pair[1] instanceof File) {
        totalSize += pair[1].size;
      } else {
        totalSize += (pair[1].length || 0);
      }
    }
    const maxPostSize = 8 * 1024 * 1024; // 8MB limit from system check
    if (totalSize > maxPostSize) {
      alert(`The total size of your files (${(totalSize / 1024 / 1024).toFixed(2)}MB) exceeds the server limit (approx 8MB). Please reduce file sizes or upload fewer documents at a time.`);
      return false;
    }
    return true;
  }

  function ap5SyncPoliceRequirement() {
    const chosenType = document.getElementById('ap5AppType')?.value || '';
    const chosenReason = document.querySelector('input[name="replacement_reason"]:checked')?.value || '';
    const label = document.getElementById('ap5PoliceLabel');
    const policeInput = document.getElementById('ap5PoliceInput');

    if (!label || !policeInput) return;

    // Only relevant for replacement
    if (chosenType !== 'replacement') {
      label.textContent = 'Police Report (Optional)';
      label.classList.remove('required');
      policeInput.removeAttribute('required');
      return;
    }

    if (chosenReason === 'stolen') {
      label.textContent = 'Police Report (Required for stolen)';
      label.classList.add('required');
      policeInput.setAttribute('required', 'required');
    } else {
      label.textContent = 'Police Report (Optional)';
      label.classList.remove('required');
      policeInput.removeAttribute('required');
    }
  }

  function ap5ValidateStep() {
    const active = document.getElementById('ap5-step-' + ap5Step);
    if (!active) return true;

    // Step 1: enforce type + reason (if replacement)
    if (ap5Step === 1) {
      const chosenType = document.getElementById('ap5AppType')?.value || '';
      if (!chosenType) {
        alert('Please select an application type (Renewal or Replacement).');
        return false;
      }
      if (chosenType === 'replacement') {
        const chosenReason = document.querySelector('input[name="replacement_reason"]:checked');
        if (!chosenReason) {
          alert('Please select a reason for replacement.');
          return false;
        }
      }
    }

    // general required validation
    const required = active.querySelectorAll('[required]');
    for (const el of required) {
      if (el.type === 'checkbox' && !el.checked) {
        alert('Please agree to the declaration.');
        el.focus();
        return false;
      }
      if (el.type === 'file') continue; // handled below
      if (!el.value || !String(el.value).trim()) {
        alert('Please fill in all required fields.');
        el.focus();
        return false;
      }
    }

    // Step 3: if changes=yes enforce details
    if (ap5Step === 3) {
      const changes = document.getElementById('ap5Changes')?.value || 'no';
      if (changes === 'yes') {
        const details = document.getElementById('ap5ChangesDetails');
        if (!details || !details.value.trim()) {
          alert('Please provide details of changes.');
          details?.focus();
          return false;
        }
      }
    }

    // Step 4: validate ONLY visible required file inputs
    if (ap5Step === 4) {
      // required file inputs inside step
      const files = active.querySelectorAll('input[type="file"][required]');
      for (const f of files) {
        // If required file is inside a hidden section, ignore it
        const section = f.closest('#ap5-renewal-docs, #ap5-replacement-docs');
        if (section && section.style.display === 'none') continue;

        if (!f.files || !f.files.length) {
          alert('Please upload all required documents before continuing.');
          return false;
        }
      }
    }

    return true;
  }

  async function submitAp5Registration(){
    const type = document.getElementById('ap5AppType')?.value || '';
    if(!type){
      alert('Please select an application type (Renewal or Replacement).');
      return;
    }

    const fd = new FormData();

    // core fields
    fd.append('request_type', type);

    fd.append('contact_name', document.getElementById('ap5ContactName').value);
    fd.append('contact_phone', document.getElementById('ap5ContactPhone').value);
    fd.append('contact_address', document.getElementById('ap5ContactAddress').value);
    fd.append('contact_email', document.getElementById('ap5ContactEmail').value);

    fd.append('entity_name', document.getElementById('ap5EntityName').value);
    fd.append('previous_reference', document.getElementById('ap5PrevRef').value);
    fd.append('head_office', document.getElementById('ap5HeadOffice').value);
    fd.append('postal_address', document.getElementById('ap5Postal').value);

    fd.append('changes', document.getElementById('ap5Changes').value);
    fd.append('changes_details', document.getElementById('ap5ChangesDetails')?.value || '');
    fd.append('collection_region', document.getElementById('ap5Office').value);

    // replacement reason
    if(type === 'replacement'){
      const reason = document.querySelector('input[name="replacement_reason"]:checked')?.value || '';
      fd.append('replacement_reason', reason);
    }

    // files (names must match backend validate)
    const proof = document.querySelector('input[name="proof_of_payment"]')?.files?.[0];
    if (proof) fd.append('proof_of_payment', proof);

    if(type === 'renewal'){
      const cert = document.querySelector('input[name="current_certificate"]')?.files?.[0];
      if (cert) fd.append('current_certificate', cert);

      const letter = document.querySelector('input[name="official_request_letter"]')?.files?.[0];
      if (letter) fd.append('official_request_letter', letter);

      const supp = document.querySelector('input[name="supporting_docs"]')?.files?.[0];
      if (supp) fd.append('supporting_docs', supp);
    } else {
      const aff = document.querySelector('input[name="affidavit"]')?.files?.[0];
      if (aff) fd.append('affidavit', aff);

      const pol = document.querySelector('input[name="police_report"]')?.files?.[0];
      if (pol) fd.append('police_report', pol);
    }

    if (!checkRequestSize(fd)) {
      btn.disabled = false;
      btn.innerHTML = old;
      return;
    }

    if(!document.getElementById('ap5Agree').checked){
      alert('Please agree to the declaration.');
      return;
    }

    const btn = document.getElementById('ap5NextBtn');
    const old = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Submitting... <i class="ri-loader-4-line"></i>';

    try{
      const res = await fetch("{{ route('mediahouse.renewals.submit-changes') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json",
        },
        body: fd
      });

      const data = await res.json().catch(() => ({}));

      if(!res.ok){
        let errMsg = 'Failed to submit AP5 (' + res.status + ').';
        if (data.message) errMsg += '\nDetails: ' + data.message;

        if (res.status === 413) {
          errMsg = 'Payload Too Large (413). Your files are too large for the server to process. Please reduce file sizes.';
        }
        alert(errMsg);
        return;
      }

      alert('AP5 submitted successfully!\nReference: ' + (data.reference || '—'));
      window.location.href = "{{ route('mediahouse.portal') }}";
    }catch(e){
      console.error(e);
      alert('Network/server error while submitting.');
    }finally{
      btn.disabled = false;
      btn.innerHTML = old;
    }
  }

  function listSelectedFiles(){
    const items = [];
    document.querySelectorAll('#renewal-page input[type="file"]').forEach(inp => {
      if (inp.files && inp.files[0]) {
        const f = inp.files[0];
        items.push({
          label: (inp.getAttribute('name') || 'document').replaceAll('_',' '),
          name: f.name,
          size: (f.size/1024).toFixed(1) + ' KB'
        });
      }
    });
    return items;
  }

  function buildReviewHtml(){
    const type = document.getElementById('ap5AppType')?.value || '-';
    const rows = [
      ['Application Type', type],
      ['Entity Name', document.getElementById('ap5EntityName')?.value || '-'],
      ['Previous Reference', document.getElementById('ap5PrevRef')?.value || '-'],
      ['Collection Office', document.getElementById('ap5Office')?.value || '-'],
      ['Contact Name', document.getElementById('ap5ContactName')?.value || '-'],
      ['Contact Phone', document.getElementById('ap5ContactPhone')?.value || '-'],
      ['Contact Email', document.getElementById('ap5ContactEmail')?.value || '-'],
    ];

    const table = `
      <div class="table-responsive">
        <table class="table table-sm">
          <tbody>
            ${rows.map(r=>`<tr><th style="width:38%">${r[0]}</th><td>${(r[1]||'-')}</td></tr>`).join('')}
          </tbody>
        </table>
      </div>`;

    const files = listSelectedFiles();
    const filesHtml = files.length ? `
      <div class="mt-3">
        <h6 class="fw-bold mb-2">Uploaded Documents</h6>
        <div class="list-group">
          ${files.map(f=>`
            <div class="list-group-item d-flex align-items-center">
              <i class="ri-file-text-line text-primary me-3 fs-5"></i>
              <div>
                <div class="fw-semibold">${f.label}</div>
                <small class="text-muted">${f.name} (${f.size})</small>
              </div>
              <i class="ri-checkbox-circle-fill text-success ms-auto"></i>
            </div>
          `).join('')}
        </div>
      </div>` : `<div class="alert alert-danger mt-3 mb-0">No documents selected.</div>`;

    return table + filesHtml;
  }

  async function saveDraftAp5Registration(){
    const type = document.getElementById('ap5AppType')?.value || '';
    if(!type){
      alert('Please select an application type (Renewal or Replacement) first.');
      return;
    }

    // Reuse existing submit builder but allow empty fields
    const fd = new FormData();
    fd.append('request_type', type);
    fd.append('contact_name', document.getElementById('ap5ContactName')?.value || '');
    fd.append('contact_phone', document.getElementById('ap5ContactPhone')?.value || '');
    fd.append('contact_address', document.getElementById('ap5ContactAddress')?.value || '');
    fd.append('contact_email', document.getElementById('ap5ContactEmail')?.value || '');
    fd.append('entity_name', document.getElementById('ap5EntityName')?.value || '');
    fd.append('previous_reference', document.getElementById('ap5PrevRef')?.value || '');
    fd.append('head_office', document.getElementById('ap5HeadOffice')?.value || '');
    fd.append('postal_address', document.getElementById('ap5Postal')?.value || '');
    fd.append('changes', document.getElementById('ap5Changes')?.value || '');
    fd.append('changes_details', document.getElementById('ap5ChangesDetails')?.value || '');
    fd.append('collection_region', document.getElementById('ap5Office')?.value || 'harare');

    if(type === 'replacement'){
      const reason = document.querySelector('input[name="replacement_reason"]:checked')?.value || '';
      fd.append('replacement_reason', reason);
    }

    const proof = document.querySelector('input[name="proof_of_payment"]')?.files?.[0];
    if (proof) fd.append('proof_of_payment', proof);
    if(type === 'renewal'){
      const cert = document.querySelector('input[name="current_certificate"]')?.files?.[0];
      if (cert) fd.append('current_certificate', cert);
      const supp = document.querySelector('input[name="supporting_docs"]')?.files?.[0];
      if (supp) fd.append('supporting_docs', supp);
    } else {
      const aff = document.querySelector('input[name="affidavit"]')?.files?.[0];
      if (aff) fd.append('affidavit', aff);
      const pol = document.querySelector('input[name="police_report"]')?.files?.[0];
      if (pol) fd.append('police_report', pol);
    }

    if (!checkRequestSize(fd)) {
      return;
    }

    try{
      const res = await fetch('{{ route("mediahouse.ap5.saveDraft") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: fd
      });
      if(!res.ok){
        let errMsg = 'Draft save failed (' + res.status + ').';
        try {
          const errJson = await res.json();
          if (errJson.message) errMsg += '\nDetails: ' + errJson.message;
        } catch (e) {}

        if (res.status === 413) {
          errMsg = 'Payload Too Large (413). Your files are too large for the server to process. Please reduce file sizes.';
        }
        alert(errMsg);
        return;
      }
      const data = await res.json().catch(()=>({}));
      alert('Draft saved! Reference: ' + (data.reference || '—'));
    }catch(e){
      console.error(e);
      alert('Network error saving draft.');
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    ap5SetDefaultDate();
    ap5UpdateSteps();
    ap5SetupUploads();

    @if(isset($draft) && $draft)
      const draftData = @json($draft->form_data ?? []);
      if (draftData) {
        // restore type selection
        if (draftData.request_type) {
          ap5SetType(draftData.request_type);
        }

        // restore values
        Object.keys(draftData).forEach(key => {
          const el = document.querySelector(`#renewal-page [name="${key}"]`);
          if (!el) return;

          if (el.type === 'radio') {
            const r = document.querySelector(`#renewal-page [name="${key}"][value="${draftData[key]}"]`);
            if (r) r.checked = true;
          } else if (el.type === 'checkbox') {
            el.checked = String(draftData[key]) === '1' || draftData[key] === true;
          } else {
            el.value = draftData[key];
          }
        });

        // restore step
        const s = parseInt(draftData.current_step || 1, 10);
        if (s && !isNaN(s)) {
          ap5Step = Math.min(5, Math.max(1, s));
        }
        ap5UpdateSteps();
        ap5SyncPoliceRequirement();

        // toggle changes details
        document.getElementById('ap5Changes')?.dispatchEvent(new Event('change'));
      }
    @endif


    // Card selection
    document.querySelectorAll('#renewal-page .app-type-card[data-ap5-type]').forEach(card => {
      card.addEventListener('click', () => ap5SetType(card.dataset.ap5Type));
      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          ap5SetType(card.dataset.ap5Type);
        }
      });
    });

    // Replacement reason updates police requirement
    document.querySelectorAll('input[name="replacement_reason"]').forEach(r => {
      r.addEventListener('change', () => ap5SyncPoliceRequirement());
    });

    // Changes toggle
    document.getElementById('ap5Changes')?.addEventListener('change', function() {
      const wrap = document.getElementById('ap5ChangesDetailsWrap');
      const details = document.getElementById('ap5ChangesDetails');

      if (this.value === 'yes') {
        wrap.style.display = 'block';
        details.setAttribute('required', 'required');
      } else {
        wrap.style.display = 'none';
        details.value = '';
        details.removeAttribute('required');
      }
    });

    // Navigation
    document.getElementById('ap5PrevBtn')?.addEventListener('click', function() {
      ap5Step = Math.max(1, ap5Step - 1);
      ap5UpdateSteps();
    });

    document.getElementById('ap5NextBtn')?.addEventListener('click', function() {
      if (!ap5ValidateStep()) return;

      if (ap5Step === 5) {
        // Show review modal before submitting
        const body = document.getElementById('ap5ReviewBody');
        if (body) body.innerHTML = buildReviewHtml();
        const modalEl = document.getElementById('ap5ReviewModal');
        if (modalEl && window.bootstrap) {
          const m = bootstrap.Modal.getOrCreateInstance(modalEl);
          m.show();
        } else {
          // fallback
          submitAp5Registration();
        }
        return;
      }

      ap5Step = Math.min(5, ap5Step + 1);
      ap5UpdateSteps();

      if (ap5Step === 4) ap5SyncPoliceRequirement();
    });

    // Save draft (multiple drafts allowed)
    document.getElementById('ap5SaveDraftBtn')?.addEventListener('click', function() {
      saveDraftAp5Registration();
    });

    // Confirm submit from review modal
    document.getElementById('ap5ConfirmSubmitBtn')?.addEventListener('click', function() {
      submitAp5Registration();
    });
  });
</script>
@endpush
