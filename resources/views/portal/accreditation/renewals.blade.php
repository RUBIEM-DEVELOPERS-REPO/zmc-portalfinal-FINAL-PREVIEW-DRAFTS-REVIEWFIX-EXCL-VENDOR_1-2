@extends('layouts.portal')

@section('title', 'Renewal / Replacement (AP5)')
@section('page_title', 'RENEWAL / REPLACEMENT (AP5)')

@section('content')
<div id="renewal-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Renewal / Replacement of Accreditation Card (AP5)</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.home') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Tracker
    </a>
  
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
                    <a class="btn btn-sm btn-primary" href="{{ route('accreditation.renewals', ['draft' => $d->reference]) }}">
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

</div>

  <div class="form-container">
    <div class="form-header">
      <h1>Application for Renewal or Replacement of Accreditation</h1>
      <p>Zimbabwe Media Commission Act (2020), Statutory Instrument 169C (Registration, Accreditation and Levy) Regulations (2002)</p>
    </div>

    <div class="form-steps-container">
      {{-- STEPS --}}
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step active" data-step="1"><div class="step-number">1</div><div class="step-label">Type</div></div>
          <div class="step" data-step="2"><div class="step-number">2</div><div class="step-label">Personal Details</div></div>
          <div class="step" data-step="3"><div class="step-number">3</div><div class="step-label">Documents</div></div>
          <div class="step" data-step="4"><div class="step-number">4</div><div class="step-label">Declaration</div></div>
        </div>
      </div>

      <form id="ap5Form" onsubmit="return false;" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="draft_reference" value="{{ $draft->reference ?? '' }}">
        <input type="hidden" name="current_step" id="ap5_current_step" value="{{ data_get($draft, 'form_data.current_step', 1) }}">

        {{-- STEP 1: TYPE --}}
        <div class="step-content active" id="ap5-step-1">
          <h3 class="step-title">Select Application Type</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Choose Renewal or Replacement. For Replacement, select the reason.
          </div>

          <div class="app-type-container mt-3">
            <div class="app-type-cards">
              <div class="app-type-card" data-type="renewal">
                <i class="ri-refresh-line"></i>
                <h4>Renewal</h4>
                <p>Annual renewal of your accreditation card.</p>
              </div>

              <div class="app-type-card" data-type="replacement">
                <i class="ri-exchange-line"></i>
                <h4>Replacement</h4>
                <p>Lost / damaged / stolen card replacement.</p>
              </div>
            </div>
          </div>

          <input type="hidden" id="ap5_type" name="request_type" required>

          <div id="ap5-replacement-reason" style="display:none; margin-top:20px;">
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

        {{-- STEP 2: PERSONAL DETAILS (KEEP) --}}
        <div class="step-content" id="ap5-step-2">
          <h3 class="step-title">Personal Details</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Enter your personal details and previous accreditation number.
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Surname</label>
              <input type="text" class="form-control" name="surname" required>
            </div>
            <div class="form-field">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="first_name" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
               <label class="form-label required">Practitioner Type</label>
               <div class="checkbox-group">
                 <div class="checkbox-item">
                   <input type="radio" id="ap5-type-employed" name="practitioner_type" value="employed" checked required>
                   <label for="ap5-type-employed">Employed</label>
                 </div>
                 <div class="checkbox-item">
                   <input type="radio" id="ap5-type-freelancer" name="practitioner_type" value="freelancer" required>
                   <label for="ap5-type-freelancer">Freelancer</label>
                 </div>
               </div>
             </div>
            <div class="form-field">
              <label class="form-label">Other Names</label>
              <input type="text" class="form-control" name="other_names">
            </div>
            <div class="form-field">
              <label class="form-label required">Gender</label>
              <div class="checkbox-group">
                <div class="checkbox-item">
                  <input type="radio" id="ap5-gender-male" name="gender" value="male" required>
                  <label for="ap5-gender-male">Male</label>
                </div>
                <div class="checkbox-item">
                  <input type="radio" id="ap5-gender-female" name="gender" value="female" required>
                  <label for="ap5-gender-female">Female</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Date of Birth</label>
              <input type="date" class="form-control" name="dob" required>
            </div>
            <div class="form-field">
              <label class="form-label required">Nationality</label>
              <input type="text" class="form-control" name="nationality" value="Zimbabwean" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">National ID / Passport</label>
              <input type="text" class="form-control" name="id_or_passport" required>
            </div>
            <div class="form-field">
              <label class="form-label required">Previous Accreditation Number</label>
              <input type="text" class="form-control" name="accreditation_number" required>
            </div>
          </div>
        </div>

        {{-- STEP 3: DOCUMENTS (ONLY WHAT YOU REQUESTED) --}}
        <div class="step-content" id="ap5-step-3">
          <h3 class="step-title">Required Documents</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Renewal: Employer letter only. Replacement: Affidavit + Employer letter. Police report only if stolen.
          </div>

          {{-- Renewal docs --}}
          <div id="ap5-renewal-docs" style="display:none;">
            <div class="form-row">
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

        {{-- STEP 4: DECLARATION --}}
        <div class="step-content" id="ap5-step-4">
          <h3 class="step-title">Declaration</h3>
          <div class="current-step-info"><i class="ri-information-line me-2"></i>Confirm and submit.</div>

          <div class="alert alert-warning">
            <h6><i class="ri-file-text-line me-2"></i>Declaration</h6>
            <p class="mb-2">I declare that the information given is true and complete.</p>
            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" id="ap5-declare" name="declaration_confirmed" value="1" required>
              <label class="form-check-label" for="ap5-declare">I agree to the terms and conditions</label>
            </div>
          </div>

          <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            Digital signature is not required. You will review your information before submitting.
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
@endsection

@push('scripts')
<script>
  let ap5Step = 1;
  let ap5Type = '';

  const ap5Steps = document.querySelectorAll('#renewal-page .step');
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

  function setDefaultDates(){
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('#renewal-page input[type="date"]').forEach(i => {
      if(!i.value) i.value = today;
    });
  }

  function ap5Show(step){
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
  }

  function applyAp5DocRequirements(){
    // clear all required flags
    document.querySelectorAll('#renewal-page input[type="file"]').forEach(f => f.required = false);

    if(ap5Type === 'renewal'){
      const isFreelancer = document.getElementById('ap5-type-freelancer')?.checked;
      const f = document.querySelector('input[name="renewal_employer_letter"]');
      const area = f?.closest('.form-row');
      
      if(isFreelancer){
        if(area) area.style.display = 'none';
        if(f) f.required = false;
      } else {
        if(area) area.style.display = 'block';
        if(f) f.required = true;
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

      const label = document.getElementById('ap5-police-label');
      if(label){
        label.textContent = (reason === 'stolen')
          ? 'Police Report (Required for stolen cards)'
          : 'Police Report (Only if stolen)';
      }
    }
  }

  function ap5Validate(step){
    // step 1 checks selection
    if(step === 1){
      if(!ap5Type){ alert('Please select Renewal or Replacement'); return false; }
      if(ap5Type === 'replacement'){
        const reason = document.querySelector('input[name="replacement_reason"]:checked');
        if(!reason){ alert('Please select a reason for replacement'); return false; }
      }
      return true;
    }

    const current = ap5Contents[step-1];
    const required = current.querySelectorAll('[required]');

    for(const field of required){
      // IMPORTANT: ignore hidden required fields
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

  async function submitAp5(){
    const form = document.getElementById('ap5Form');
    const fd = new FormData(form);

    // Ensure request_type is present
    fd.set('request_type', ap5Type);

    // If replacement but no police report and reason != stolen, ok
    // If stolen, controller enforces police report - good.

    try{
      const res = await fetch('{{ route("accreditation.submitAp5") }}', {
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
        return;
      }

      alert('AP5 submitted successfully! Reference: ' + (data.reference || '—'));
      window.location.href = "{{ route('accreditation.home') }}";
    }catch(e){
      console.error(e);
      alert('Network error submitting AP5.');
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
    const files = listSelectedFiles();
    const rows = [
      ['Application Type', ap5Type || '-'],
      ['Surname', fd.get('surname') || '-'],
      ['First Name', fd.get('first_name') || '-'],
      ['Other Names', fd.get('other_names') || '-'],
      ['Gender', fd.get('gender') || '-'],
      ['Date of Birth', fd.get('dob') || '-'],
      ['Nationality', fd.get('nationality') || '-'],
      ['National ID / Passport', fd.get('id_or_passport') || '-'],
      ['Previous Accreditation No', fd.get('accreditation_number') || '-'],
    ];

    const table = `
      <div class="table-responsive">
        <table class="table table-sm">
          <tbody>
            ${rows.map(r=>`<tr><th style="width:38%">${r[0]}</th><td>${(r[1]||'-')}</td></tr>`).join('')}
          </tbody>
        </table>
      </div>`;

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

  async function saveDraftAp5(){
    const form = document.getElementById('ap5Form');
    const fd = new FormData(form);
    fd.set('request_type', ap5Type);

    try{
      const res = await fetch('{{ route('accreditation.renewals.saveDraft') }}', {
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

  // Type cards
  document.querySelectorAll('#renewal-page .app-type-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('#renewal-page .app-type-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');

      ap5Type = card.dataset.type;
      document.getElementById('ap5_type').value = ap5Type;

      replacementReasonBlock.style.display = (ap5Type === 'replacement') ? 'block' : 'none';
      renewalDocs.style.display = (ap5Type === 'renewal') ? 'block' : 'none';
      replacementDocs.style.display = (ap5Type === 'replacement') ? 'block' : 'none';

      applyAp5DocRequirements();
    });
  });

  // Replacement reason affects police required
  document.querySelectorAll('input[name="replacement_reason"]').forEach(r => {
    r.addEventListener('change', () => {
      applyAp5DocRequirements();
    });
  });

  // Practitioner type affects employer letter requirement
  document.querySelectorAll('input[name="practitioner_type"]').forEach(r => {
    r.addEventListener('change', () => {
      applyAp5DocRequirements();
    });
  });

  // Navigation
  ap5PrevBtn.addEventListener('click', () => {
    ap5Step = Math.max(1, ap5Step - 1);
    ap5Show(ap5Step);
  });

  ap5NextBtn.addEventListener('click', () => {
    // Always re-apply requirements before validating
    applyAp5DocRequirements();

    if(!ap5Validate(ap5Step)) return;

    if(ap5Step === ap5Contents.length){
      // Show review modal instead of submitting immediately
      const body = document.getElementById('ap5ReviewBody');
      if (body) body.innerHTML = buildReviewHtml();
      const modalEl = document.getElementById('ap5ReviewModal');
      if (modalEl && window.bootstrap) {
        const m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
      } else {
        // Fallback
        if (confirm('Submit your AP5 application now?')) submitAp5();
      }
      return;
    }

    ap5Step = Math.min(ap5Contents.length, ap5Step + 1);
    ap5Show(ap5Step);
  });

  document.addEventListener('DOMContentLoaded', () => {
    ap5Show(1);
    setupUploads(document.getElementById('renewal-page'));
    setDefaultDates();

    @if(isset($draft) && $draft)
      const draftData = @json($draft->form_data ?? []);
      if (draftData) {
        // restore type
        if (draftData.request_type) {
          const card = document.querySelector(`.app-type-card[data-type="${draftData.request_type}"]`);
          if (card) card.click();
        }

        // restore fields
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

        applyAp5DocRequirements();

        const s = parseInt(draftData.current_step || 1, 10);
        if (s && !isNaN(s)) ap5Show(Math.min(4, Math.max(1, s)));
      }
    @endif


    // Save draft (multiple drafts allowed)
    document.getElementById('ap5SaveDraftBtn')?.addEventListener('click', () => {
      applyAp5DocRequirements();
      if (!ap5Type) {
        alert('Please select Renewal or Replacement first.');
        return;
      }
      saveDraftAp5();
    });

    // Confirm submit from review modal
    document.getElementById('ap5ConfirmSubmitBtn')?.addEventListener('click', () => {
      submitAp5();
    });
  });
</script>
@endpush
