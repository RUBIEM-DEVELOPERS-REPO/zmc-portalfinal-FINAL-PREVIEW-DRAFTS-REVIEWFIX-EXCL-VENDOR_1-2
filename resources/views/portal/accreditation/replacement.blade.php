@extends('layouts.portal')

@section('title', 'Replacement (AP5)')
@section('page_title', 'REPLACEMENT (AP5)')

@section('content')
<div id="replacement-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Replacement of Accreditation Card (AP5)</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.home') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Tracker
    </a>
  </div>

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
                    <a class="btn btn-sm btn-primary" href="{{ route('accreditation.replacement', ['draft' => $d->reference]) }}">
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

  <div class="form-container">
    <div class="form-header">
      <h1>Application for Replacement of Accreditation</h1>
      <p>Statutory Instrument 169C (Registration, Accreditation and Levy) Regulations (2002)</p>
    </div>

    <div class="form-steps-container">
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1"><div class="step-number">1</div><div class="step-label">Type</div></div>
          <div class="step" data-step="2"><div class="step-number">2</div><div class="step-label">Accreditation Lookup</div></div>
          <div class="step" data-step="3"><div class="step-number">3</div><div class="step-label">Documents</div></div>
          <div class="step" data-step="4"><div class="step-number">4</div><div class="step-label">Payment & Submit</div></div>
        </div>
      </div>

      <form id="ap5Form" onsubmit="return false;" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="draft_reference" value="{{ $draft->reference ?? '' }}">
        <input type="hidden" name="current_step" id="ap5_current_step" value="{{ data_get($draft, 'form_data.current_step', 1) }}">
        <input type="hidden" name="has_changes" id="ap5_has_changes" value="no">
        <input type="hidden" name="changes_data" id="ap5_changes_data" value="">

        {{-- STEP 1: TYPE --}}
        <div class="step-content active" id="ap5-step-1">
          <h3 class="step-title">Replacement Application</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            This is for replacement of a lost, damaged, or stolen accreditation card.
          </div>

          <div class="alert alert-warning mt-3">
            <i class="ri-alert-line me-2"></i>
            This form is for <strong>Replacement</strong> only. If you need to <strong>Renew</strong> your accreditation card, please use the separate Renewal link in the sidebar.
          </div>

          <input type="hidden" id="ap5_type" name="request_type" value="replacement" required>

          <div id="ap5-replacement-reason" style="margin-top:20px;">
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
          </div>
        </div>

        {{-- STEP 2: ACCREDITATION NUMBER LOOKUP --}}
        <div class="step-content" id="ap5-step-2">
          <h3 class="step-title">Accreditation Number Lookup</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Enter your accreditation number and ID/Passport to verify your identity.
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Applicant Type</label>
              <div class="checkbox-group">
                <div class="checkbox-item">
                  <input type="radio" id="ap5-scope-local" name="journalist_scope" value="local" checked>
                  <label for="ap5-scope-local">Local (Zimbabwean)</label>
                </div>
                <div class="checkbox-item">
                  <input type="radio" id="ap5-scope-foreign" name="journalist_scope" value="foreign">
                  <label for="ap5-scope-foreign">Foreign</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Accreditation Number</label>
              <div class="d-flex gap-2">
                <input type="text" class="form-control" name="accreditation_number" id="ap5_accreditation_number" placeholder="e.g. J12345678E" required>
                <button type="button" class="btn btn-primary" id="ap5LookupBtn" style="white-space:nowrap;">
                  <i class="ri-search-line me-1"></i>Look Up
                </button>
              </div>
            </div>
          </div>

          {{-- Local: National ID --}}
          <div class="form-row" id="ap5-id-local-row">
            <div class="form-field">
              <label class="form-label required">National ID Number</label>
              <input type="text" class="form-control" name="national_id_number" id="ap5_national_id" placeholder="e.g. 63-123456A78" required>
              <small class="text-muted">Must match the ID on your original application.</small>
            </div>
          </div>

          {{-- Foreign: Passport --}}
          <div class="form-row" id="ap5-id-foreign-row" style="display:none;">
            <div class="form-field">
              <label class="form-label required">Passport Number</label>
              <input type="text" class="form-control" name="passport_number" id="ap5_passport_no" placeholder="e.g. AB1234567">
              <small class="text-muted">Must match the passport on your original application.</small>
            </div>
          </div>

          <div id="ap5-lookup-loading" style="display:none;" class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Looking up your record...</p>
          </div>

          <div id="ap5-lookup-error" style="display:none;" class="alert alert-danger mt-3"></div>

          <div id="ap5-lookup-result" style="display:none;" class="mt-3">
            <div class="card shadow-sm">
              <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold"><i class="ri-user-line me-2"></i>Your Accreditation Record</h6>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm mb-0" id="ap5-record-table">
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="mt-3 d-flex gap-2" id="ap5-changes-buttons">
              <button type="button" class="btn btn-success" id="ap5NoChangesBtn">
                <i class="ri-check-line me-1"></i>No Changes — Proceed to Documents
              </button>
              <button type="button" class="btn btn-warning" id="ap5HasChangesBtn">
                <i class="ri-edit-line me-1"></i>There are Changes
              </button>
            </div>

            <div id="ap5-changes-form" style="display:none;" class="mt-3">
              <h6 class="fw-bold mb-3">Specify Changes</h6>
              <div class="alert alert-info mb-3">
                <i class="ri-information-line me-2"></i>
                Only fill in the fields that have changed. Leave unchanged fields empty.
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">New Surname</label>
                  <input type="text" class="form-control change-field" data-field="surname" placeholder="Leave blank if unchanged">
                </div>
                <div class="form-field">
                  <label class="form-label">New First Name</label>
                  <input type="text" class="form-control change-field" data-field="first_name" placeholder="Leave blank if unchanged">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">New Gender</label>
                  <select class="form-control change-field" data-field="gender">
                    <option value="">No change</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="form-field">
                  <label class="form-label">New Nationality</label>
                  <input type="text" class="form-control change-field" data-field="nationality" placeholder="Leave blank if unchanged">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">New National ID / Passport</label>
                  <input type="text" class="form-control change-field" data-field="id_or_passport" placeholder="Leave blank if unchanged">
                </div>
                <div class="form-field">
                  <label class="form-label">New Designation</label>
                  <input type="text" class="form-control change-field" data-field="designation" placeholder="Leave blank if unchanged">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">Other Changes (describe)</label>
                  <textarea class="form-control change-field" data-field="other_changes" rows="3" placeholder="Describe any other changes"></textarea>
                </div>
              </div>
              <button type="button" class="btn btn-primary mt-2" id="ap5ConfirmChangesBtn">
                <i class="ri-check-line me-1"></i>Confirm Changes & Proceed
              </button>
            </div>
          </div>
        </div>

        {{-- STEP 3: DOCUMENTS --}}
        <div class="step-content" id="ap5-step-3">
          <h3 class="step-title">Required Documents</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Upload the required documents for your application.
          </div>

          {{-- Renewal docs --}}
          <div id="ap5-renewal-docs" style="display:none;">
            <div class="form-row mb-3">
              <div class="form-field">
                <label class="form-label required">Employment Status</label>
                <div class="checkbox-group">
                  <div class="checkbox-item">
                    <input type="radio" id="ap5-emp-freelancer" name="employment_status" value="freelancer">
                    <label for="ap5-emp-freelancer">Freelancer</label>
                  </div>
                  <div class="checkbox-item">
                    <input type="radio" id="ap5-emp-employed" name="employment_status" value="employed" checked>
                    <label for="ap5-emp-employed">Employed</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-row" id="ap5-employer-letter-row">
              <div class="form-field">
                <label class="form-label required">Employer Letter (Renewal)</label>
                <div class="upload-area">
                  <i class="ri-file-text-line"></i>
                  <h5>Upload Employer Letter</h5>
                  <p>PDF / JPG / PNG</p>
                  <input type="file" name="renewal_employer_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                  <button type="button" class="upload-btn">Choose File</button>
                </div>
                <div class="uploaded-files"></div>
              </div>
            </div>
          </div>

          {{-- Replacement docs --}}
          <div id="ap5-replacement-docs" style="display:none;">
            <div class="alert alert-warning mb-3">
              <i class="ri-alert-line me-2"></i>
              Affidavit is required for replacement. Police report is required for stolen.
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Affidavit</label>
                <div class="upload-area">
                  <i class="ri-scales-line"></i>
                  <h5>Upload Affidavit</h5>
                  <p>PDF / JPG / PNG</p>
                  <input type="file" name="replacement_affidavit" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                  <button type="button" class="upload-btn">Choose File</button>
                </div>
                <div class="uploaded-files"></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Employer Letter (Replacement)</label>
                <div class="upload-area">
                  <i class="ri-file-text-line"></i>
                  <h5>Upload Employer Letter</h5>
                  <p>PDF / JPG / PNG</p>
                  <input type="file" name="replacement_employer_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                  <button type="button" class="upload-btn">Choose File</button>
                </div>
                <div class="uploaded-files"></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label" id="ap5-police-label">Police Report (Only if stolen)</label>
                <div class="upload-area">
                  <i class="ri-shield-line"></i>
                  <h5>Upload Police Report</h5>
                  <p>Required if stolen</p>
                  <input type="file" name="replacement_police_report" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                  <button type="button" class="upload-btn">Choose File</button>
                </div>
                <div class="uploaded-files"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 4: DECLARATION & SUBMIT --}}
        <div class="step-content" id="ap5-step-4">
          <h3 class="step-title">Declaration & Submit</h3>
          <div class="current-step-info"><i class="ri-information-line me-2"></i>Review and confirm your application before submitting.</div>

          <div class="alert alert-info mb-3">
            <i class="ri-bank-card-line me-2"></i>
            After submitting, you will be prompted to pay the replacement fee via <strong>PayNow</strong> or by uploading <strong>proof of payment</strong>.
          </div>

          <div class="alert alert-warning">
            <h6><i class="ri-file-text-line me-2"></i>Declaration</h6>
            <p class="mb-2">I declare that the information given is true and complete.</p>
            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" id="ap5-declare" name="declaration_confirmed" value="1" required>
              <label class="form-check-label" for="ap5-declare">I agree to the terms and conditions</label>
            </div>
          </div>
        </div>

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
      </form>

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

{{-- Payment Modal (shown after successful submission) --}}
<x-payment-modal
  modal-id="ap5PaymentModal"
  description="Accreditation Replacement Fee"
  currency="USD"
/>
@endsection

@push('scripts')
<script>
  let ap5Step = {{ $draft->form_data['current_step'] ?? 1 }};
  let ap5Type = '{{ $draft->request_type ?? "replacement" }}';
  let ap5LookupData = null;
  let ap5LookupDone = false;

  const ap5Steps = document.querySelectorAll('#replacement-page .step');
  const ap5Contents = [
    document.getElementById('ap5-step-1'),
    document.getElementById('ap5-step-2'),
    document.getElementById('ap5-step-3'),
    document.getElementById('ap5-step-4'),
  ];

  const ap5PrevBtn = document.getElementById('ap5PrevBtn');
  const ap5NextBtn = document.getElementById('ap5NextBtn');

  const replacementReasonBlock = document.getElementById('ap5-replacement-reason');
  const renewalDocs = document.getElementById('ap5-renewal-docs');
  const replacementDocs = document.getElementById('ap5-replacement-docs');

  function isVisible(el){
    return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
  }

  async function autoSave() {
      const form = document.getElementById('ap5Form');
      const fd = new FormData(form);
      const dataObj = {};
      fd.forEach((v, k) => { if(!(v instanceof File)) dataObj[k] = v; });
      fd.append('form_data', JSON.stringify(dataObj));
      fd.append('current_step', ap5Step);
      
      try {
          await fetch('{{ route("accreditation.replacement.saveDraft") }}', {
              method: 'POST',
              body: fd,
              headers: { 
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              }
          });
          console.log('AP5 Replacement Draft auto-saved');
      } catch(e) {
          console.error('Auto-save failed', e);
      }
  }

  setInterval(() => {
      if (ap5Step > 1 && ap5Step < ap5Contents.length) {
          autoSave();
      }
  }, 60000);

  function ap5Show(step){
    ap5Step = step;
    ap5Contents.forEach((el, idx) => el.classList.toggle('active', idx === (step-1)));

    ap5Steps.forEach(s => {
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

    ap5PrevBtn.style.display = step === 1 ? 'none' : 'inline-block';
    ap5NextBtn.innerHTML = step === ap5Contents.length
      ? 'Submit Application <i class="ri-send-plane-line"></i>'
      : 'Next <i class="ri-arrow-right-line"></i>';

    if (step === 2) {
      ap5NextBtn.style.display = ap5LookupDone ? 'inline-block' : 'none';
    } else {
      ap5NextBtn.style.display = 'inline-block';
    }
  }

  function applyAp5DocRequirements(){
    document.querySelectorAll('#replacement-page input[type="file"]').forEach(f => f.required = false);

    if(ap5Type === 'renewal'){
      const empStatus = document.querySelector('input[name="employment_status"]:checked')?.value || 'employed';
      const letterRow = document.getElementById('ap5-employer-letter-row');
      const f = document.querySelector('input[name="renewal_employer_letter"]');
      if (empStatus === 'freelancer') {
        if (letterRow) letterRow.style.display = 'none';
        if (f) f.required = false;
      } else {
        if (letterRow) letterRow.style.display = '';
        if (f) f.required = true;
      }
    }

    if(ap5Type === 'replacement'){
      const aff = document.querySelector('input[name="replacement_affidavit"]');
      const emp = document.querySelector('input[name="replacement_employer_letter"]');
      if(aff) aff.required = true;
      if(emp) emp.required = true;

      const reason = document.querySelector('input[name="replacement_reason"]:checked')?.value;
      const police = document.querySelector('input[name="replacement_police_report"]');
      if(police) police.required = (reason === 'stolen');
    }
  }

  function ap5Validate(step){
    if(step === 1){
      // Check replacement reason is selected
      const reason = document.querySelector('input[name="replacement_reason"]:checked');
      if(!reason){ alert('Please select a reason for replacement'); return false; }
      return true;
    }

    if(step === 2){
      const num = document.getElementById('ap5_accreditation_number')?.value?.trim();
      if(!num){ alert('Please enter your accreditation number.'); return false; }
      const scope = document.querySelector('input[name="journalist_scope"]:checked')?.value || 'local';
      const idVal = scope === 'local'
        ? document.getElementById('ap5_national_id')?.value?.trim()
        : document.getElementById('ap5_passport_no')?.value?.trim();
      if(!idVal){
        alert(scope === 'local' ? 'Please enter your National ID Number.' : 'Please enter your Passport Number.');
        return false;
      }
      if(!ap5LookupDone){ alert('Please look up your accreditation number first.'); return false; }
      return true;
    }

    const current = ap5Contents[step-1];
    const required = current.querySelectorAll('[required]');

    for(const field of required){
      if(!isVisible(field)) continue;

      if(field.type === 'checkbox'){
        if(!field.checked){ alert('Please accept the declaration terms.'); return false; }
      } else if(field.type === 'file'){
        if(!field.files || !field.files.length){
          alert('Please upload required documents.');
          return false;
        }
      } else if(!String(field.value || '').trim()){
        alert('Please complete all required fields.');
        field.focus();
        return false;
      }
    }
    return true;
  }

  function setupUploads(root){
    root.querySelectorAll('.upload-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = btn.parentElement.querySelector('input[type="file"]');
        if(input) input.click();
      });
    });

    root.querySelectorAll('.upload-area input[type="file"]').forEach(input => {
      input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        const area = input.closest('.upload-area');
        const list = area?.parentElement?.querySelector('.uploaded-files');
        if(!file || !area || !list) return;

        area.style.borderColor = '#10b981';
        area.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';

        const fileName = file.name.length > 40 ? file.name.slice(0,40)+'...' : file.name;
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

        list.querySelector('button')?.addEventListener('click', () => {
          input.value = '';
          list.innerHTML = '';
          area.style.borderColor = '';
          area.style.backgroundColor = '';
        });
      });
    });
  }

  async function ap5DoLookup(){
    const num = document.getElementById('ap5_accreditation_number')?.value?.trim();
    if(!num){ alert('Please enter an accreditation number.'); return; }

    const scope = document.querySelector('input[name="journalist_scope"]:checked')?.value || 'local';
    const idVal = scope === 'local'
      ? document.getElementById('ap5_national_id')?.value?.trim()
      : document.getElementById('ap5_passport_no')?.value?.trim();

    if(!idVal){
      alert(scope === 'local' ? 'Please enter your National ID Number.' : 'Please enter your Passport Number.');
      return;
    }

    const loading = document.getElementById('ap5-lookup-loading');
    const errorDiv = document.getElementById('ap5-lookup-error');
    const resultDiv = document.getElementById('ap5-lookup-result');

    loading.style.display = 'block';
    errorDiv.style.display = 'none';
    resultDiv.style.display = 'none';
    ap5LookupDone = false;
    ap5NextBtn.style.display = 'none';

    try {
      const url = "{{ url('/portal/accreditation/lookup-number') }}/" + encodeURIComponent(num);
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
      const data = await res.json().catch(() => ({}));

      if(!res.ok || !data.success){
        errorDiv.textContent = data.message || 'Record not found.';
        errorDiv.style.display = 'block';
        loading.style.display = 'none';
        return;
      }

      // Verify ID/Passport matches the record
      const recordId = (data.record.id_or_passport || '').replace(/[\s\-]/g, '').toUpperCase();
      const enteredId = idVal.replace(/[\s\-]/g, '').toUpperCase();
      if(recordId && enteredId && recordId !== enteredId){
        errorDiv.textContent = scope === 'local'
          ? 'National ID Number does not match our records. Please check and try again.'
          : 'Passport Number does not match our records. Please check and try again.';
        errorDiv.style.display = 'block';
        loading.style.display = 'none';
        return;
      }

      ap5LookupData = data.record;

      const tbody = document.querySelector('#ap5-record-table tbody');
      const fields = [
        ['Accreditation Number', data.record.record_number || data.record.certificate_no],
        ['Name', data.record.holder_name],
        ['Surname', data.record.surname],
        ['First Name', data.record.first_name],
        ['Gender', data.record.gender],
        ['Date of Birth', data.record.dob],
        ['Nationality', data.record.nationality],
        [scope === 'local' ? 'National ID' : 'Passport No.', data.record.id_or_passport],
        ['Employment Type', data.record.employment_type],
        ['Status', data.record.status],
        ['Issued', data.record.issued_at],
        ['Expires', data.record.expires_at],
      ];

      tbody.innerHTML = fields.map(([label, val]) =>
        `<tr><th style="width:35%">${label}</th><td>${val || '-'}</td></tr>`
      ).join('');

      resultDiv.style.display = 'block';
      loading.style.display = 'none';

    } catch(e) {
      console.error(e);
      errorDiv.textContent = 'Network error looking up record.';
      errorDiv.style.display = 'block';
      loading.style.display = 'none';
    }
  }

  function ap5CollectChanges(){
    const changes = {};
    document.querySelectorAll('.change-field').forEach(el => {
      const val = el.value?.trim();
      if(val) changes[el.dataset.field] = val;
    });
    return changes;
  }

  async function submitAp5(){
    const form = document.getElementById('ap5Form');
    const fd = new FormData(form);
    fd.set('request_type', ap5Type);

    const btn = document.getElementById('ap5ConfirmSubmitBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...'; }

    try{
      const res = await fetch('{{ route("accreditation.replacement.submit") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: fd
      });

      const data = await res.json().catch(() => ({}));

      if(!res.ok){
        alert(data.message || 'Submission failed. Please check required fields.');
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-check-line"></i> Confirm & Submit'; }
        return;
      }

      // Hide review modal
      const reviewModal = bootstrap.Modal.getInstance(document.getElementById('ap5ReviewModal'));
      if (reviewModal) reviewModal.hide();

      // Show payment modal
      const appId = data.application_id;
      if (appId) {
        initPaymentModal('ap5PaymentModal', appId, {
          initiate: '{{ url("/payments") }}/' + appId + '/initiate',
          initiateMobile: '{{ url("/payments") }}/' + appId + '/initiate-mobile',
          status: '{{ url("/payments") }}/' + appId + '/status',
          proof: '{{ url("/payments") }}/' + appId + '/upload-proof',
        });
        const payModal = new bootstrap.Modal(document.getElementById('ap5PaymentModal'));
        payModal.show();
      } else {
        alert('Application submitted! Reference: ' + (data.reference || '—'));
        window.location.href = "{{ route('accreditation.home') }}";
      }
    }catch(e){
      console.error(e);
      alert('Network error submitting AP5.');
      if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-check-line"></i> Confirm & Submit'; }
    }
  }

  function listSelectedFiles(){
    const items = [];
    document.querySelectorAll('#ap5Form input[type="file"]').forEach(inp => {
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
    const fd = new FormData(document.getElementById('ap5Form'));
    const rows = [
      ['Application Type', ap5Type.toUpperCase()],
      ['ID / Passport', fd.get('journalist_scope') === 'local' ? fd.get('national_id_number') : fd.get('passport_number')],
      ['Accreditation Number', fd.get('accreditation_number') || '-'],
      ['Has Changes?', fd.get('has_changes') === 'yes' ? 'Yes' : 'No'],
    ];

    if(fd.get('has_changes') === 'yes' && fd.get('changes_data')) {
      try {
        const changes = JSON.parse(fd.get('changes_data'));
        Object.keys(changes).forEach(k => {
          rows.push([`Change: ${k.replaceAll('_',' ')}`, changes[k]]);
        });
      } catch(e) {}
    }

    let html = `
      <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered">
          <tbody>
            ${rows.map(r=>`<tr><th class="bg-light" style="width:40%">${r[0]}</th><td>${(r[1]||'-')}</td></tr>`).join('')}
          </tbody>
        </table>
      </div>`;

    // Add Document Previews
    html += `<h6 class="fw-bold mb-2">Uploaded Documents</h6><div class="row g-2">`;
    let hasDocs = false;
    document.querySelectorAll('#ap5Form input[type="file"]').forEach(inp => {
      if (inp.files && inp.files[0]) {
        hasDocs = true;
        const f = inp.files[0];
        const label = (inp.getAttribute('name') || 'document').replaceAll('_',' ');
        const id = 'prev-' + inp.name;
        
        html += `
          <div class="col-6">
            <div class="card p-1 text-center h-100">
              <div id="${id}" style="height:100px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:4px;" class="mb-1">
                <i class="ri-file-text-line fs-2 text-muted"></i>
              </div>
              <div class="extra-small fw-bold text-truncate">${label}</div>
            </div>
          </div>`;

        if(f.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            const el = document.getElementById(id);
            if(el) el.innerHTML = `<img src="${e.target.result}" style="max-width:100%; max-height:100%; object-fit:contain;">`;
          };
          reader.readAsDataURL(f);
        }
      }
    });

    if(!hasDocs) html += `<div class="col-12"><div class="alert alert-warning py-2 small">No new documents uploaded.</div></div>`;
    html += `</div>`;

    return html;
  }

  async function saveDraftAp5(){
    const form = document.getElementById('ap5Form');
    const fd = new FormData(form);
    fd.set('request_type', ap5Type);

    try{
      const res = await fetch('{{ route('accreditation.replacement.saveDraft') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: fd
      });
      const data = await res.json().catch(()=>({}));
      if(!res.ok){
        alert(data.message || 'Draft save failed.');
        return;
      }
      alert('Draft saved! Reference: ' + (data.reference || '—'));
    }catch(e){
      console.error(e);
      alert('Network error saving draft.');
    }
  }

  document.querySelectorAll('#replacement-page .app-type-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('#replacement-page .app-type-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');

      ap5Type = card.dataset.type;
      document.getElementById('ap5_type').value = ap5Type;

      replacementReasonBlock.style.display = (ap5Type === 'replacement') ? 'block' : 'none';
      renewalDocs.style.display = (ap5Type === 'renewal') ? 'block' : 'none';
      replacementDocs.style.display = (ap5Type === 'replacement') ? 'block' : 'none';

      applyAp5DocRequirements();
    });
  });

  document.querySelectorAll('input[name="replacement_reason"]').forEach(r => {
    r.addEventListener('change', () => { applyAp5DocRequirements(); });
  });

  document.querySelectorAll('input[name="employment_status"]').forEach(r => {
    r.addEventListener('change', () => { applyAp5DocRequirements(); });
  });

  ap5PrevBtn.addEventListener('click', () => {
    ap5Step = Math.max(1, ap5Step - 1);
    ap5Show(ap5Step);
  });

  ap5NextBtn.addEventListener('click', () => {
    applyAp5DocRequirements();

    if(!ap5Validate(ap5Step)) return;

    if(ap5Step === ap5Contents.length){
      const body = document.getElementById('ap5ReviewBody');
      if (body) body.innerHTML = buildReviewHtml();
      const modalEl = document.getElementById('ap5ReviewModal');
      if (modalEl && window.bootstrap) {
        const m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
      } else {
        if (confirm('Submit your AP5 application now?')) submitAp5();
      }
      return;
    }

    ap5Step = Math.min(ap5Contents.length, ap5Step + 1);
    ap5Show(ap5Step);
  });

  document.addEventListener('DOMContentLoaded', () => {
    ap5Show(ap5Step);
    setupUploads(document.getElementById('replacement-page'));

    // Toggle National ID / Passport field based on scope
    document.querySelectorAll('input[name="journalist_scope"]').forEach(r => {
      r.addEventListener('change', () => {
        const isLocal = r.value === 'local';
        document.getElementById('ap5-id-local-row').style.display = isLocal ? '' : 'none';
        document.getElementById('ap5-id-foreign-row').style.display = isLocal ? 'none' : '';
        document.getElementById('ap5_national_id').required = isLocal;
        document.getElementById('ap5_passport_no').required = !isLocal;
        ap5LookupDone = false;
        document.getElementById('ap5-lookup-result').style.display = 'none';
        document.getElementById('ap5-lookup-error').style.display = 'none';
        ap5NextBtn.style.display = 'none';
      });
    });

    document.getElementById('ap5LookupBtn')?.addEventListener('click', () => { ap5DoLookup(); });

    document.getElementById('ap5NoChangesBtn')?.addEventListener('click', () => {
      document.getElementById('ap5_has_changes').value = 'no';
      document.getElementById('ap5_changes_data').value = '';
      document.getElementById('ap5-changes-form').style.display = 'none';
      ap5LookupDone = true;
      ap5NextBtn.style.display = 'inline-block';
      ap5Step = 3;
      ap5Show(ap5Step);
    });

    document.getElementById('ap5HasChangesBtn')?.addEventListener('click', () => {
      document.getElementById('ap5-changes-form').style.display = 'block';
      document.getElementById('ap5-changes-buttons').style.display = 'none';
    });

    document.getElementById('ap5ConfirmChangesBtn')?.addEventListener('click', () => {
      const changes = ap5CollectChanges();
      if(Object.keys(changes).length === 0){
        alert('Please fill in at least one changed field, or click "No Changes".');
        return;
      }
      document.getElementById('ap5_has_changes').value = 'yes';
      document.getElementById('ap5_changes_data').value = JSON.stringify(changes);
      ap5LookupDone = true;
      ap5NextBtn.style.display = 'inline-block';
      ap5Step = 3;
      ap5Show(ap5Step);
    });

    @if(isset($draft) && $draft)
      const draftData = @json($draft->form_data ?? []);
      if (draftData) {
        if (draftData.request_type) {
          const card = document.querySelector(`.app-type-card[data-type="${draftData.request_type}"]`);
          if (card) card.click();
        }

        Object.keys(draftData).forEach(key => {
          const el = document.querySelector(`#replacement-page [name="${key}"]`);
          if (!el) return;

          if (el.type === 'radio') {
            const r = document.querySelector(`#replacement-page [name="${key}"][value="${draftData[key]}"]`);
            if (r) r.checked = true;
          } else if (el.type === 'checkbox') {
            el.checked = String(draftData[key]) === '1' || draftData[key] === true;
          } else {
            el.value = draftData[key];
          }
        });

        applyAp5DocRequirements();

        const s = parseInt(draftData.current_step || 1, 10);
        if (s && !isNaN(s)) ap5Show(Math.min(4, Math.max(1, s)));
      }
    @endif

    document.getElementById('ap5SaveDraftBtn')?.addEventListener('click', () => {
      applyAp5DocRequirements();
      if (!ap5Type) {
        alert('Please select Renewal or Replacement first.');
        return;
      }
      saveDraftAp5();
    });

    document.getElementById('ap5ConfirmSubmitBtn')?.addEventListener('click', () => {
      submitAp5();
    });
  });
</script>
@endpush
