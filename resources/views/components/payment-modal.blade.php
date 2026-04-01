{{--
  Payment Modal Component
  Usage: <x-payment-modal :application="$application" />
  Or with a JS variable: pass application-id and fee via data attributes
--}}
@props([
    'modalId'       => 'paymentModal',
    'applicationId' => null,
    'fee'           => null,
    'currency'      => 'USD',
    'description'   => 'Application Fee',
    'initiateUrl'   => null,
    'proofUrl'      => null,
    'statusUrl'     => null,
])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

      {{-- Header --}}
      <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1e7e34,#28a745);padding:24px 28px 20px;">
        <div class="d-flex align-items-center gap-3">
          <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <i class="ri-bank-card-line text-white" style="font-size:22px;"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-white m-0">Payment</h5>
            <p class="m-0 text-white" style="opacity:.8;font-size:13px;">{{ $description }}</p>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- Fee Banner --}}
      @if($fee)
      <div style="background:#f0fdf4;border-bottom:1px solid #d1fae5;padding:14px 28px;">
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted" style="font-size:13px;">Amount Due</span>
          <span class="fw-bold" style="font-size:22px;color:#1e7e34;">{{ $currency }} {{ number_format($fee, 2) }}</span>
        </div>
      </div>
      @endif

      <div class="modal-body p-0">

        {{-- Method Tabs --}}
        <div style="padding:20px 28px 0;">
          <div class="d-flex gap-2 mb-4" id="{{ $modalId }}-tabs">
            <button type="button" class="pay-tab-btn active" data-tab="paynow"
              style="flex:1;padding:10px;border:2px solid #28a745;border-radius:10px;background:#f0fdf4;color:#1e7e34;font-weight:600;font-size:13px;cursor:pointer;transition:all .2s;">
              <i class="ri-smartphone-line me-1"></i>PayNow
            </button>
            <button type="button" class="pay-tab-btn" data-tab="proof"
              style="flex:1;padding:10px;border:2px solid #e5e7eb;border-radius:10px;background:#fff;color:#6b7280;font-weight:600;font-size:13px;cursor:pointer;transition:all .2s;">
              <i class="ri-file-upload-line me-1"></i>Upload Proof
            </button>
          </div>
        </div>

        {{-- PayNow Tab --}}
        <div class="pay-tab-content" id="{{ $modalId }}-tab-paynow" style="padding:0 28px 24px;">
          <div class="alert alert-info mb-3" style="border-radius:10px;font-size:13px;">
            <i class="ri-information-line me-2"></i>
            You will be redirected to the PayNow secure payment page. Once payment is confirmed, your application status updates automatically.
          </div>

          {{-- Web / Card --}}
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px;">Pay via Web (Card / ZimSwitch)</label>
            <button type="button" class="btn btn-success w-100 py-2" id="{{ $modalId }}-paynow-web-btn"
              style="border-radius:10px;font-weight:600;">
              <i class="ri-external-link-line me-2"></i>Pay with PayNow
            </button>
          </div>

          <div class="d-flex align-items-center gap-2 mb-3">
            <hr style="flex:1;margin:0;"><span class="text-muted" style="font-size:12px;">or mobile money</span><hr style="flex:1;margin:0;">
          </div>

          {{-- Mobile --}}
          <div class="mb-2">
            <label class="form-label fw-semibold" style="font-size:13px;">EcoCash / OneMoney</label>
            <div class="d-flex gap-2">
              <input type="tel" class="form-control" id="{{ $modalId }}-mobile-phone"
                placeholder="07XXXXXXXX" maxlength="10"
                style="border-radius:8px;font-size:13px;">
              <select class="form-select" id="{{ $modalId }}-mobile-method" style="max-width:130px;border-radius:8px;font-size:13px;">
                <option value="ecocash">EcoCash</option>
                <option value="onemoney">OneMoney</option>
              </select>
            </div>
          </div>
          <button type="button" class="btn btn-outline-success w-100 py-2 mb-3" id="{{ $modalId }}-paynow-mobile-btn"
            style="border-radius:10px;font-weight:600;font-size:13px;">
            <i class="ri-smartphone-line me-2"></i>Send Mobile Payment Request
          </button>

          {{-- Status polling area --}}
          <div id="{{ $modalId }}-poll-area" style="display:none;" class="alert alert-warning" style="border-radius:10px;font-size:13px;">
            <div class="d-flex align-items-center gap-2">
              <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
              <span id="{{ $modalId }}-poll-msg">Waiting for payment confirmation...</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="{{ $modalId }}-poll-check-btn">
              <i class="ri-refresh-line me-1"></i>Check Status
            </button>
          </div>
        </div>

        {{-- Proof Upload Tab --}}
        <div class="pay-tab-content" id="{{ $modalId }}-tab-proof" style="display:none;padding:0 28px 24px;">
          <div class="alert alert-warning mb-3" style="border-radius:10px;font-size:13px;">
            <i class="ri-alert-line me-2"></i>
            Upload your bank transfer or deposit slip. An Accounts Officer will verify and approve it. A digitally stamped receipt will be generated once approved.
          </div>

          <div class="row g-2">
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;">First Name *</label>
              <input type="text" class="form-control form-control-sm" id="{{ $modalId }}-proof-fname" style="border-radius:8px;">
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;">Surname *</label>
              <input type="text" class="form-control form-control-sm" id="{{ $modalId }}-proof-lname" style="border-radius:8px;">
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;">Payment Date *</label>
              <input type="date" class="form-control form-control-sm" id="{{ $modalId }}-proof-date" style="border-radius:8px;">
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;">Amount Paid ({{ $currency }}) *</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="{{ $modalId }}-proof-amount"
                value="{{ $fee ?? '' }}" style="border-radius:8px;">
            </div>
            <div class="col-12">
              <label class="form-label" style="font-size:12px;font-weight:600;">Bank / Payment Method *</label>
              <input type="text" class="form-control form-control-sm" id="{{ $modalId }}-proof-bank"
                placeholder="e.g. CBZ / Steward / EcoCash" style="border-radius:8px;">
            </div>
            <div class="col-12">
              <label class="form-label" style="font-size:12px;font-weight:600;">Upload Proof (PDF/JPG/PNG) *</label>
              <input type="file" class="form-control form-control-sm" id="{{ $modalId }}-proof-file"
                accept=".pdf,.jpg,.jpeg,.png" style="border-radius:8px;">
            </div>
          </div>

          <button type="button" class="btn btn-warning w-100 py-2 mt-3" id="{{ $modalId }}-proof-submit-btn"
            style="border-radius:10px;font-weight:600;color:#fff;">
            <i class="ri-upload-cloud-line me-2"></i>Submit Proof of Payment
          </button>
        </div>

      </div>

      {{-- Footer --}}
      <div class="modal-footer border-0 pt-0" style="padding:0 28px 20px;">
        <p class="text-muted m-0" style="font-size:11px;">
          <i class="ri-shield-check-line me-1 text-success"></i>
          Payments are secured by PayNow Zimbabwe. ZMC does not store card details.
        </p>
      </div>

    </div>
  </div>
</div>

@once
@push('scripts')
<script>
(function(){
  // Generic payment modal initialiser — call initPaymentModal(modalId, applicationId, urls) after DOM ready
  window.initPaymentModal = function(modalId, applicationId, urls) {
    const el = document.getElementById(modalId);
    if (!el) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Tab switching
    el.querySelectorAll('.pay-tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        el.querySelectorAll('.pay-tab-btn').forEach(b => {
          b.style.borderColor = '#e5e7eb';
          b.style.background = '#fff';
          b.style.color = '#6b7280';
        });
        btn.style.borderColor = '#28a745';
        btn.style.background = '#f0fdf4';
        btn.style.color = '#1e7e34';

        el.querySelectorAll('.pay-tab-content').forEach(c => c.style.display = 'none');
        const tab = el.querySelector('#' + modalId + '-tab-' + btn.dataset.tab);
        if (tab) tab.style.display = 'block';
      });
    });

    // PayNow Web
    el.querySelector('#' + modalId + '-paynow-web-btn')?.addEventListener('click', async () => {
      const btn = el.querySelector('#' + modalId + '-paynow-web-btn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting to PayNow...';
      try {
        const res = await fetch(urls.initiate, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify({ application_id: applicationId }),
        });
        const data = await res.json().catch(() => ({}));
        if (data.success && data.redirect_url) {
          window.location.href = data.redirect_url;
        } else {
          alert(data.message || 'PayNow initiation failed. Please try again.');
          btn.disabled = false;
          btn.innerHTML = '<i class="ri-external-link-line me-2"></i>Pay with PayNow';
        }
      } catch(e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-external-link-line me-2"></i>Pay with PayNow';
      }
    });

    // PayNow Mobile
    el.querySelector('#' + modalId + '-paynow-mobile-btn')?.addEventListener('click', async () => {
      const phone = el.querySelector('#' + modalId + '-mobile-phone')?.value?.trim();
      const method = el.querySelector('#' + modalId + '-mobile-method')?.value;
      if (!phone || !/^0[7][0-9]{8}$/.test(phone)) {
        alert('Please enter a valid Zimbabwean mobile number (07XXXXXXXX).');
        return;
      }
      const btn = el.querySelector('#' + modalId + '-paynow-mobile-btn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending request...';
      try {
        const res = await fetch(urls.initiateMobile, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify({ phone, method }),
        });
        const data = await res.json().catch(() => ({}));
        if (data.success) {
          el.querySelector('#' + modalId + '-poll-area').style.display = 'block';
          startPolling(data.poll_url, urls.status);
        } else {
          alert(data.message || 'Mobile payment failed. Please try again.');
        }
      } catch(e) {
        alert('Network error. Please try again.');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-smartphone-line me-2"></i>Send Mobile Payment Request';
      }
    });

    // Manual status check
    el.querySelector('#' + modalId + '-poll-check-btn')?.addEventListener('click', () => {
      checkPaymentStatus(urls.status);
    });

    // Proof upload
    el.querySelector('#' + modalId + '-proof-submit-btn')?.addEventListener('click', async () => {
      const fname = el.querySelector('#' + modalId + '-proof-fname')?.value?.trim();
      const lname = el.querySelector('#' + modalId + '-proof-lname')?.value?.trim();
      const date  = el.querySelector('#' + modalId + '-proof-date')?.value;
      const amount = el.querySelector('#' + modalId + '-proof-amount')?.value;
      const bank  = el.querySelector('#' + modalId + '-proof-bank')?.value?.trim();
      const file  = el.querySelector('#' + modalId + '-proof-file')?.files?.[0];

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

      const btn = el.querySelector('#' + modalId + '-proof-submit-btn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

      try {
        const res = await fetch(urls.proof, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
          body: fd,
        });
        const data = await res.json().catch(() => ({}));
        if (data.ok) {
          bootstrap.Modal.getInstance(el)?.hide();
          alert('Proof of payment submitted successfully. An Accounts Officer will verify it and generate your receipt.');
          window.location.reload();
        } else {
          alert(data.message || 'Upload failed. Please try again.');
        }
      } catch(e) {
        alert('Network error. Please try again.');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-upload-cloud-line me-2"></i>Submit Proof of Payment';
      }
    });

    let pollInterval = null;

    function startPolling(pollUrl, statusUrl) {
      if (pollInterval) clearInterval(pollInterval);
      pollInterval = setInterval(() => checkPaymentStatus(statusUrl), 5000);
    }

    async function checkPaymentStatus(statusUrl) {
      try {
        const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
        const data = await res.json().catch(() => ({}));
        const msgEl = el.querySelector('#' + modalId + '-poll-msg');
        if (data.status === 'paid') {
          if (pollInterval) clearInterval(pollInterval);
          if (msgEl) msgEl.textContent = 'Payment confirmed! Redirecting...';
          setTimeout(() => window.location.reload(), 1500);
        } else if (data.status === 'failed') {
          if (pollInterval) clearInterval(pollInterval);
          if (msgEl) msgEl.textContent = 'Payment failed. Please try again.';
        } else {
          if (msgEl) msgEl.textContent = 'Waiting for payment confirmation... (' + (data.status || 'pending') + ')';
        }
      } catch(e) { /* silent */ }
    }
  };
})();
</script>
@endpush
@endonce
