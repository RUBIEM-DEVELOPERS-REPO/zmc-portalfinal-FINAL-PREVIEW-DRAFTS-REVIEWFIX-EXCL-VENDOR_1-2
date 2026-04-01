@extends('layouts.portal')

@section('title', 'Renewal of Registration (AP5)')

@section('content')
<div id="renewal-page">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Renewal of Registration (AP5)</h4>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Renewal</h1>
      <p class="m-0">
        Complete this digital AP5 form to renew your registration.
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
            <div class="step-label">Registration Lookup</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Documents</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Payment & Submit</div>
          </div>
        </div>
      </div>

      <form id="ap5Form" onsubmit="return false;" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="has_changes" id="ap5_has_changes" value="no">
        <input type="hidden" name="changes_data" id="ap5_changes_data" value="">
        <input type="hidden" name="current_step" id="ap5_current_step" value="1">

        {{-- STEP 1: APPLICATION TYPE --}}
        <div class="step-content active" id="ap5-step-1">
          <h3 class="step-title">Renewal Application</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            This is for annual renewal of your Mass Media Service registration.
          </div>

          <div class="alert alert-info mt-3">
            <i class="ri-information-line me-2"></i>
            This form is for <strong>Renewal</strong> of your registration only. If you need a <strong>Replacement</strong> for a lost, damaged, or stolen certificate, please use the separate Replacement link in the sidebar.
          </div>

          <input type="hidden" id="ap5AppType" name="request_type" value="renewal">
        </div>

        {{-- STEP 2: REGISTRATION NUMBER LOOKUP --}}
        <div class="step-content" id="ap5-step-2">
          <h3 class="step-title">Registration Number Lookup</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Enter your registration number to retrieve your record.
          </div>

          <div class="form-row">
            <div class="form-field">
              <label class="form-label required">Registration Number</label>
              <div class="d-flex gap-2">
                <input type="text" class="form-control" name="registration_number" id="ap5RegNumber" placeholder="e.g. ZMC-AP1-2023-031" required>
                <button type="button" class="btn btn-primary" id="ap5LookupBtn" style="white-space:nowrap;">
                  <i class="ri-search-line me-1"></i>Look Up
                </button>
              </div>
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
                <h6 class="mb-0 fw-bold"><i class="ri-building-line me-2"></i>Your Registration Record</h6>
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
                  <label class="form-label">New Entity / Service Name</label>
                  <input type="text" class="form-control change-field" data-field="entity_name" placeholder="Leave blank if unchanged">
                </div>
                <div class="form-field">
                  <label class="form-label">New Head Office Address</label>
                  <textarea class="form-control change-field" data-field="head_office" rows="2" placeholder="Leave blank if unchanged"></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">New Postal Address</label>
                  <textarea class="form-control change-field" data-field="postal_address" rows="2" placeholder="Leave blank if unchanged"></textarea>
                </div>
                <div class="form-field">
                  <label class="form-label">New Contact Name</label>
                  <input type="text" class="form-control change-field" data-field="contact_name" placeholder="Leave blank if unchanged">
                </div>
              </div>
              <div class="form-row">
                <div class="form-field">
                  <label class="form-label">New Contact Phone</label>
                  <input type="text" class="form-control change-field" data-field="contact_phone" placeholder="Leave blank if unchanged">
                </div>
                <div class="form-field">
                  <label class="form-label">New Contact Email</label>
                  <input type="email" class="form-control change-field" data-field="contact_email" placeholder="Leave blank if unchanged">
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
            Upload all required documents.
          </div>

          {{-- RENEWAL DOCS --}}
          <div id="ap5-renewal-docs" style="display:none;">
            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Previous Certificate</label>
                <div class="upload-area">
                  <i class="ri-file-shield-2-line"></i>
                  <h5>Upload Previous Certificate</h5>
                  <p>PDF / JPG / PNG</p>
                  <input type="file" name="previous_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
                  <button type="button" class="upload-btn">Choose File</button>
                </div>
                <div class="uploaded-files"></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Official Letter Requesting Renewal</label>
                <div class="upload-area">
                  <i class="ri-file-text-line"></i>
                  <h5>Upload Official Letter</h5>
                  <p>PDF / JPG / PNG</p>
                  <input type="file" name="official_letter" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" required>
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
              Affidavit required for replacement. Police report required if stolen.
            </div>

            <div class="form-row">
              <div class="form-field">
                <label class="form-label required">Affidavit for Lost/Stolen</label>
                <div class="upload-area">
                  <i class="ri-scales-3-line"></i>
                  <h5>Upload Affidavit</h5>
                  <p>PDF / JPG / PNG</p>
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
                  <p>PDF / JPG / PNG</p>
                  <input type="file" name="police_report" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" id="ap5PoliceInput">
                  <button type="button" class="upload-btn">Choose File</button>
                </div>
                <div class="uploaded-files"></div>
              </div>
            </div>
          </div>

          <div class="form-row mt-3">
            <div class="form-field">
              <label class="form-label">Regional Office for Collection</label>
              <select class="form-control" id="ap5Office" name="collection_region">
                <option value="">Select office</option>
                <option value="harare">Harare - 108 Swan Drive, Alexandra Park</option>
                <option value="bulawayo">Bulawayo - Main Office</option>
                <option value="mutare">Mutare - Regional Office</option>
                <option value="masvingo">Masvingo - Regional Office</option>
              </select>
            </div>
          </div>
        </div>

        {{-- STEP 4: DECLARATION & SUBMIT --}}
        <div class="step-content" id="ap5-step-4">
          <h3 class="step-title">Declaration & Submit</h3>
          <div class="current-step-info">
            <i class="ri-information-line me-2"></i>
            Review and confirm your application before submitting.
          </div>

          <div class="alert alert-info mb-3">
            <i class="ri-bank-card-line me-2"></i>
            After submitting, you will be prompted to pay the renewal fee via <strong>PayNow</strong> or by uploading <strong>proof of payment</strong>.
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
  description="Registration Renewal Fee"
  currency="USD"
/>
@endsection

@push('scripts')
<script>
  let ap5Step = 1;
  const TOTAL_STEPS = 4;
  let ap5LookupData = null;
  let ap5LookupDone = false;

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
    if (next) next.innerHTML = (ap5Step === TOTAL_STEPS)
      ? 'Submit Application <i class="ri-send-plane-line"></i>'
      : 'Next <i class="ri-arrow-right-line"></i>';

    if (ap5Step === 2) {
      if (next) next.style.display = ap5LookupDone ? 'inline-block' : 'none';
    } else {
      if (next) next.style.display = 'inline-block';
    }

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

    if (reasonWrap) reasonWrap.style.display = (type === 'replacement') ? 'block' : 'none';
    const renewalDocs = document.getElementById('ap5-renewal-docs');
    const replacementDocs = document.getElementById('ap5-replacement-docs');
    if (renewalDocs) renewalDocs.style.display = (type === 'replacement') ? 'none' : 'block';
    if (replacementDocs) replacementDocs.style.display = (type === 'replacement') ? 'block' : 'none';

    if (type !== 'replacement') {
      document.querySelectorAll('input[name="replacement_reason"]').forEach(r => r.checked = false);
    }

    ap5SyncPoliceRequirement();
  }

  function ap5SyncPoliceRequirement() {
    const chosenType = document.getElementById('ap5AppType')?.value || '';
    const chosenReason = document.querySelector('input[name="replacement_reason"]:checked')?.value || '';
    const label = document.getElementById('ap5PoliceLabel');
    const policeInput = document.getElementById('ap5PoliceInput');

    if (!label || !policeInput) return;

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

    if (ap5Step === 1) {
      // Step 1 is now just informational, always valid
      return true;
    }

    if (ap5Step === 2) {
      const num = document.getElementById('ap5RegNumber')?.value?.trim();
      if (!num) {
        alert('Please enter your registration number.');
        return false;
      }
      if (!ap5LookupDone) {
        alert('Please look up your registration number first.');
        return false;
      }
    }

    if (ap5Step === 3) {
      const files = active.querySelectorAll('input[type="file"][required]');
      for (const f of files) {
        const section = f.closest('#ap5-renewal-docs, #ap5-replacement-docs');
        if (section && section.style.display === 'none') continue;
        if (!f.files || !f.files.length) {
          alert('Please upload all required documents before continuing.');
          return false;
        }
      }
    }

    const required = active.querySelectorAll('[required]');
    for (const el of required) {
      if (el.type === 'checkbox' && !el.checked) {
        alert('Please agree to the declaration.');
        el.focus();
        return false;
      }
      if (el.type === 'file') continue;
      if (!el.value || !String(el.value).trim()) {
        alert('Please fill in all required fields.');
        el.focus();
        return false;
      }
    }

    return true;
  }

  async function ap5DoLookup(){
    const num = document.getElementById('ap5RegNumber')?.value?.trim();
    if(!num){ alert('Please enter a registration number.'); return; }

    const loading = document.getElementById('ap5-lookup-loading');
    const errorDiv = document.getElementById('ap5-lookup-error');
    const resultDiv = document.getElementById('ap5-lookup-result');
    const nextBtn = document.getElementById('ap5NextBtn');

    loading.style.display = 'block';
    errorDiv.style.display = 'none';
    resultDiv.style.display = 'none';
    ap5LookupDone = false;
    if (nextBtn) nextBtn.style.display = 'none';

    try {
      const url = "{{ url('/media-house/registration/lookup-number') }}/" + encodeURIComponent(num);
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

      ap5LookupData = data.record;

      const tbody = document.querySelector('#ap5-record-table tbody');
      const fields = [
        ['Reference', data.record.reference],
        ['Entity Name', data.record.entity_name],
        ['Head Office', data.record.head_office],
        ['Postal Address', data.record.postal_address],
        ['Contact Name', data.record.contact_name],
        ['Contact Phone', data.record.contact_phone],
        ['Contact Email', data.record.contact_email],
        ['Collection Region', data.record.collection_region],
        ['Status', data.record.status],
      ];

      tbody.innerHTML = fields.map(([label, val]) =>
        `<tr><th style="width:35%">${label}</th><td>${val || '-'}</td></tr>`
      ).join('');

      resultDiv.style.display = 'block';
      document.getElementById('ap5-changes-buttons').style.display = 'flex';
      document.getElementById('ap5-changes-form').style.display = 'none';
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
      const val = (el.value || '').trim();
      if(val) changes[el.dataset.field] = val;
    });
    return changes;
  }

  async function submitAp5Registration(){
    const type = document.getElementById('ap5AppType')?.value || '';
    if(!type){
      alert('Please select an application type.');
      return;
    }

    const fd = new FormData(document.getElementById('ap5Form'));

    if(!document.getElementById('ap5Agree').checked){
      alert('Please agree to the declaration.');
      return;
    }

    const btn = document.getElementById('ap5ConfirmSubmitBtn');
    const old = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...'; }

    try{
      const res = await fetch("{{ route('mediahouse.ap5.submit') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json",
        },
        body: fd
      });

      const data = await res.json().catch(() => ({}));

      if(!res.ok){
        alert(data.message || 'Failed to submit AP5.');
        if (btn) { btn.disabled = false; btn.innerHTML = old; }
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
        alert('AP5 submitted successfully!\nReference: ' + (data.reference || '—'));
        window.location.href = "{{ route('mediahouse.portal') }}";
      }
    }catch(e){
      console.error(e);
      alert('Network/server error while submitting.');
      if (btn) { btn.disabled = false; btn.innerHTML = old; }
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
      ['Registration Number', document.getElementById('ap5RegNumber')?.value || '-'],
      ['Changes', document.getElementById('ap5_has_changes')?.value === 'yes' ? 'Yes' : 'No'],
      ['Collection Office', document.getElementById('ap5Office')?.value || '-'],
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
      alert('Please select an application type first.');
      return;
    }

    const fd = new FormData(document.getElementById('ap5Form'));

    try{
      const res = await fetch('{{ route("mediahouse.ap5.saveDraft") }}', {
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

  document.addEventListener('DOMContentLoaded', function() {
    ap5UpdateSteps();
    ap5SetupUploads();

    document.getElementById('ap5LookupBtn')?.addEventListener('click', () => { ap5DoLookup(); });

    document.getElementById('ap5NoChangesBtn')?.addEventListener('click', () => {
      document.getElementById('ap5_has_changes').value = 'no';
      document.getElementById('ap5_changes_data').value = '';
      document.getElementById('ap5-changes-form').style.display = 'none';
      ap5LookupDone = true;
      ap5Step = 3;
      ap5UpdateSteps();
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
      ap5Step = 3;
      ap5UpdateSteps();
    });

    document.querySelectorAll('input[name="replacement_reason"]').forEach(r => {
      r.addEventListener('change', () => ap5SyncPoliceRequirement());
    });

    @if(isset($draft) && $draft)
      const draftData = @json($draft->form_data ?? []);
      if (draftData) {
        if (draftData.request_type) {
          ap5SetType(draftData.request_type);
        }

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

        const s = parseInt(draftData.current_step || 1, 10);
        if (s && !isNaN(s)) {
          ap5Step = Math.min(TOTAL_STEPS, Math.max(1, s));
        }
        ap5UpdateSteps();
        ap5SyncPoliceRequirement();
      }
    @endif

    document.querySelectorAll('#renewal-page .app-type-card[data-ap5-type]').forEach(card => {
      card.addEventListener('click', () => ap5SetType(card.dataset.ap5Type));
      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          ap5SetType(card.dataset.ap5Type);
        }
      });
    });

    document.querySelectorAll('input[name="replacement_reason"]').forEach(r => {
      r.addEventListener('change', () => ap5SyncPoliceRequirement());
    });

    document.getElementById('ap5PrevBtn')?.addEventListener('click', function() {
      ap5Step = Math.max(1, ap5Step - 1);
      ap5UpdateSteps();
    });

    document.getElementById('ap5NextBtn')?.addEventListener('click', function() {
      if (!ap5ValidateStep()) return;

      if (ap5Step === TOTAL_STEPS) {
        const body = document.getElementById('ap5ReviewBody');
        if (body) body.innerHTML = buildReviewHtml();
        const modalEl = document.getElementById('ap5ReviewModal');
        if (modalEl && window.bootstrap) {
          const m = bootstrap.Modal.getOrCreateInstance(modalEl);
          m.show();
        } else {
          submitAp5Registration();
        }
        return;
      }

      ap5Step = Math.min(TOTAL_STEPS, ap5Step + 1);
      ap5UpdateSteps();

      if (ap5Step === 3) ap5SyncPoliceRequirement();
    });

    document.getElementById('ap5SaveDraftBtn')?.addEventListener('click', function() {
      saveDraftAp5Registration();
    });

    document.getElementById('ap5ConfirmSubmitBtn')?.addEventListener('click', function() {
      submitAp5Registration();
    });
  });
</script>
@endpush
