<div class="modal fade zmc-modal-pop" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <div class="zmc-modal-title">
            <i class="ri-bank-card-line me-2" style="color:var(--zmc-green)"></i>
            Make Payment
          </div>
          <div class="zmc-modal-sub" id="pay_meta">Application: —</div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="pay_loading" class="d-none text-center py-4">
          <div class="spinner-border" style="color:var(--zmc-green)"></div>
          <div class="text-muted mt-2" style="font-size:12px;">Processing...</div>
        </div>

        <div id="pay_error" class="alert alert-danger d-none"></div>
        <div id="pay_success" class="alert alert-success d-none"></div>

        <div id="pay_options">
          <div class="alert alert-light border mb-3">
            <div class="fw-bold mb-1"><i class="ri-information-line me-1"></i> Payment Information</div>
            <div class="text-muted small">Your application is ready for payment. Choose your preferred payment method below.</div>
          </div>

          <ul class="nav nav-pills mb-3" id="payTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-paynow" data-bs-toggle="pill" data-bs-target="#pane-paynow" type="button" role="tab">
                <i class="ri-flashlight-line me-1"></i> PayNow
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-proof" data-bs-toggle="pill" data-bs-target="#pane-proof" type="button" role="tab">
                <i class="ri-file-upload-line me-1"></i> Upload Proof
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-waiver" data-bs-toggle="pill" data-bs-target="#pane-waiver" type="button" role="tab">
                <i class="ri-coupon-3-line me-1"></i> Upload Waiver
              </button>
            </li>
          </ul>

          <div class="tab-content" id="payTabContent">
            {{-- PAYNOW --}}
            <div class="tab-pane fade show active" id="pane-paynow" role="tabpanel">
              <div id="paynow_initial">
                <div class="mb-4">
                  <h6 class="fw-bold mb-3"><i class="ri-smartphone-line me-2"></i>Mobile Money (EcoCash / OneMoney)</h6>
                  <div class="row g-2">
                    <div class="col-12 col-md-7">
                      <input type="tel" class="form-control zmc-input" id="pay_phone" placeholder="e.g. 0771234567" pattern="0[7][0-9]{8}">
                    </div>
                    <div class="col-12 col-md-5">
                      <select class="form-select zmc-input" id="pay_method">
                        <option value="ecocash">EcoCash</option>
                        <option value="onemoney">OneMoney</option>
                      </select>
                    </div>
                  </div>
                  <button type="button" class="btn btn-success w-100 mt-2 fw-bold" id="btnPayMobile">
                    <i class="ri-smartphone-line me-1"></i> Pay with Mobile Money
                  </button>
                </div>

                <div class="text-center text-muted my-3">— OR —</div>

                <div>
                  <h6 class="fw-bold mb-3"><i class="ri-bank-card-2-line me-2"></i>Card / Bank Payment</h6>
                  <button type="button" class="btn btn-dark w-100 fw-bold" id="btnPayCard">
                    <i class="ri-bank-card-2-line me-1"></i> Pay with Card / Zimswitch
                  </button>
                  <div class="text-muted small mt-2 text-center">You will be redirected to Paynow to complete payment.</div>
                </div>
              </div>

              {{-- NEW: Reference Submission Step --}}
              <div id="paynow_reference_step" class="d-none">
                <div class="alert alert-info border mb-3">
                  <div class="fw-bold"><i class="ri-check-line me-1"></i> Payment Link Opened</div>
                  <div class="small">Once you have completed your payment on the PayNow gateway, click the button below to enter your reference number.</div>
                </div>
                <button type="button" class="btn btn-primary w-100 fw-bold py-3" id="btnPaymentDone">
                  <i class="ri-checkbox-circle-line me-2"></i> I Have Paid - Click to Enter Reference
                </button>
              </div>

              {{-- NEW: Reference Input Modal/Form --}}
              <div id="paynow_input_step" class="d-none">
                <div class="mb-3">
                  <label class="form-label fw-bold">Enter PayNow Reference Number</label>
                  <input type="text" class="form-control zmc-input form-control-lg" id="paynow_reference" placeholder="e.g. PN-123456789">
                  <div class="text-muted small mt-1">Enter the reference number you received after successful payment.</div>
                </div>
                <button type="button" class="btn btn-success w-100 fw-bold" id="btnSubmitRef">
                  <i class="ri-send-plane-line me-1"></i> Submit to Accounts
                </button>
                <button type="button" class="btn btn-link w-100 mt-2 text-muted small" id="btnBackToPaynow">Back to payment options</button>
              </div>
            </div>

            {{-- PROOF UPLOAD --}}
            <div class="tab-pane fade" id="pane-proof" role="tabpanel">
              <form id="proofForm" enctype="multipart/form-data">
                <div class="row g-2">
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Name</label>
                    <input type="text" class="form-control zmc-input" name="proof_first_name" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Surname</label>
                    <input type="text" class="form-control zmc-input" name="proof_last_name" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Payment date</label>
                    <input type="date" class="form-control zmc-input" name="proof_payment_date" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Amount paid</label>
                    <input type="number" step="0.01" min="0" class="form-control zmc-input" name="proof_amount_paid" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label small">Bank used</label>
                    <input type="text" class="form-control zmc-input" name="proof_bank_name" placeholder="e.g. CBZ / Steward / Stanbic" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label small">Upload proof (PDF/JPG/PNG)</label>
                    <input type="file" class="form-control zmc-input" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="text-muted small mt-1">Tip: make sure the amount, date and reference are clearly visible for audit.</div>
                  </div>
                  <div class="col-12">
                    <label class="form-label small">Other supporting documents (Optional, Multiple)</label>
                    <input type="file" class="form-control zmc-input" name="supporting_docs[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3 fw-bold" id="btnSubmitProof">
                  <i class="ri-upload-cloud-2-line me-1"></i> Submit Payment Proof
                </button>
              </form>
            </div>

            {{-- WAIVER UPLOAD --}}
            <div class="tab-pane fade" id="pane-waiver" role="tabpanel">
              <form id="waiverForm" enctype="multipart/form-data">
                <div class="row g-2">
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Name</label>
                    <input type="text" class="form-control zmc-input" name="waiver_first_name" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Surname</label>
                    <input type="text" class="form-control zmc-input" name="waiver_last_name" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Date waiver was offered</label>
                    <input type="date" class="form-control zmc-input" name="waiver_offered_date" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small">Who offered it?</label>
                    <input type="text" class="form-control zmc-input" name="waiver_offered_by" placeholder="Name / Department" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label small">Upload waiver (PDF/JPG/PNG)</label>
                    <input type="file" class="form-control zmc-input" name="waiver_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="text-muted small mt-1">Tip: include an official signature/stamp where possible for audit.</div>
                  </div>
                  <div class="col-12">
                    <label class="form-label small">Other supporting documents (Optional, Multiple)</label>
                    <input type="file" class="form-control zmc-input" name="supporting_docs[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3 fw-bold" id="btnSubmitWaiver">
                  <i class="ri-upload-cloud-2-line me-1"></i> Submit Waiver
                </button>
              </form>
            </div>
          </div>
        </div>

        <div id="pay_polling" class="d-none text-center py-4">
          <div class="spinner-border text-success" role="status"></div>
          <div class="mt-3 fw-bold">Waiting for payment confirmation...</div>
          <div class="text-muted small">Please approve the payment on your phone.</div>
          <button type="button" class="btn btn-outline-secondary btn-sm mt-3" id="btnCheckStatus">
            <i class="ri-refresh-line me-1"></i> Check Status
          </button>
        </div>
      </div>

      <div class="modal-footer zmc-modal-footer">
        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function() {
  let currentAppId = null;
  let pollingInterval = null;

  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

  function showPayError(msg) {
    const box = document.getElementById('pay_error');
    box.textContent = msg;
    box.classList.remove('d-none');
    document.getElementById('pay_loading').classList.add('d-none');
    document.getElementById('pay_options').classList.remove('d-none');
    document.getElementById('pay_polling').classList.add('d-none');
  }

  function showPaySuccess(msg) {
    const box = document.getElementById('pay_success');
    box.textContent = msg;
    box.classList.remove('d-none');
    document.getElementById('pay_loading').classList.add('d-none');
    document.getElementById('pay_options').classList.add('d-none');
    document.getElementById('pay_polling').classList.add('d-none');
    if (pollingInterval) clearInterval(pollingInterval);
  }

  function resetPayModal() {
    document.getElementById('pay_error').classList.add('d-none');
    document.getElementById('pay_success').classList.add('d-none');
    document.getElementById('pay_loading').classList.add('d-none');
    document.getElementById('pay_options').classList.remove('d-none');
    document.getElementById('pay_polling').classList.add('d-none');
    document.getElementById('paynow_initial')?.classList.remove('d-none');
    document.getElementById('paynow_reference_step')?.classList.add('d-none');
    document.getElementById('paynow_input_step')?.classList.add('d-none');
    document.getElementById('paynow_reference').value = '';

    if (pollingInterval) clearInterval(pollingInterval);

    // reset forms
    document.getElementById('proofForm')?.reset();
    document.getElementById('waiverForm')?.reset();

    // ensure PayNow tab active
    const tab = document.getElementById('tab-paynow');
    if (tab && window.bootstrap?.Tab) {
      bootstrap.Tab.getOrCreateInstance(tab).show();
    }
  }

  function setBusy(btn, busy, text) {
    if (!btn) return;
    btn.disabled = !!busy;
    if (text) btn.dataset.originalText = btn.dataset.originalText || btn.innerHTML;
    if (busy) btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span>${text || 'Submitting...'}`;
    else if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
  }

  async function postForm(url, formEl, btnEl) {
    try {
      document.getElementById('pay_error').classList.add('d-none');
      document.getElementById('pay_success').classList.add('d-none');

      const fd = new FormData(formEl);
      setBusy(btnEl, true, 'Submitting...');

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf(),
        },
        body: fd,
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok || data.ok === false) {
        // Laravel validation returns {errors:{...}}
        if (data.errors) {
          const first = Object.values(data.errors)[0];
          throw new Error(Array.isArray(first) ? first[0] : String(first));
        }
        throw new Error(data.message || 'Submission failed');
      }

      showPaySuccess(data.message || 'Submitted successfully');

      // Optional: mark current row button as "Submitted" without reloading
      const rowBtn = document.querySelector(`.js-pay-now[data-app-id="${currentAppId}"]`);
      if (rowBtn) {
        rowBtn.classList.remove('btn-success');
        rowBtn.classList.add('btn-outline-secondary');
        rowBtn.innerHTML = `<i class="ri-time-line me-1"></i> Submitted`;
        rowBtn.disabled = true;
      }

      // Close modal after short delay (no page reload)
      setTimeout(() => {
        const modalEl = document.getElementById('paymentModal');
        const inst = window.bootstrap?.Modal?.getInstance(modalEl) || window.bootstrap?.Modal?.getOrCreateInstance(modalEl);
        inst?.hide();
      }, 1500);

    } catch (e) {
      showPayError(e.message || 'Network error. Please try again.');
    } finally {
      setBusy(btnEl, false);
    }
  }

  function startPolling() {
    if (!currentAppId) return;
    pollingInterval = setInterval(async () => {
      try {
        const res = await fetch(`/payments/${currentAppId}/status`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.paid) {
          showPaySuccess('Payment confirmed! Your application is now being processed.');
          setTimeout(() => window.location.reload(), 1500);
        }
      } catch (e) {
        console.error('Polling error:', e);
      }
    }, 5000);
  }

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.js-pay-now');
    if (!btn) return;

    currentAppId = btn.getAttribute('data-app-id');
    const appRef = btn.getAttribute('data-app-ref');

    resetPayModal();
    document.getElementById('pay_meta').textContent = 'Application: ' + appRef;

    const modal = document.getElementById('paymentModal');
    if (window.bootstrap && typeof bootstrap.Modal === 'function') {
      bootstrap.Modal.getOrCreateInstance(modal).show();
    }
  });

  // PayNow - mobile
  document.getElementById('btnPayMobile')?.addEventListener('click', async function() {
    const phone = document.getElementById('pay_phone').value.trim();
    const method = document.getElementById('pay_method').value;

    if (!/^0[7][0-9]{8}$/.test(phone)) {
      showPayError('Please enter a valid phone number (e.g. 0771234567)');
      return;
    }

    document.getElementById('pay_error').classList.add('d-none');
    document.getElementById('pay_options').classList.add('d-none');
    document.getElementById('pay_loading').classList.remove('d-none');

    try {
      const res = await fetch(`/payments/${currentAppId}/initiate-mobile`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf(),
        },
        body: JSON.stringify({ phone, method })
      });

      const data = await res.json();

      if (data.success) {
        document.getElementById('pay_loading').classList.add('d-none');
        document.getElementById('pay_polling').classList.remove('d-none');
        startPolling();
      } else {
        showPayError(data.message || 'Payment failed. Please try again.');
      }
    } catch (e) {
      showPayError('Network error. Please try again.');
    }
  });

  // PayNow - card
  document.getElementById('btnPayCard')?.addEventListener('click', async function() {
    document.getElementById('pay_error').classList.add('d-none');
    document.getElementById('pay_options').classList.add('d-none');
    document.getElementById('pay_loading').classList.remove('d-none');

    try {
      const res = await fetch(`/payments/${currentAppId}/initiate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf(),
        }
      });

      const data = await res.json();

      if (data.success && data.redirect_url) {
        // Show the "Done" step and hide initial options
        document.getElementById('paynow_initial').classList.add('d-none');
        document.getElementById('paynow_reference_step').classList.remove('d-none');
        document.getElementById('pay_loading').classList.add('d-none');
        
        window.open(data.redirect_url, '_blank');
      } else {
        showPayError(data.message || 'Payment failed. Please try again.');
      }
    } catch (e) {
      showPayError('Network error. Please try again.');
    }
  });

  document.getElementById('btnPaymentDone')?.addEventListener('click', function() {
    document.getElementById('paynow_reference_step').classList.add('d-none');
    document.getElementById('paynow_input_step').classList.remove('d-none');
  });

  document.getElementById('btnBackToPaynow')?.addEventListener('click', function() {
    document.getElementById('paynow_input_step').classList.add('d-none');
    document.getElementById('paynow_initial').classList.remove('d-none');
  });

  document.getElementById('btnSubmitRef')?.addEventListener('click', async function() {
    const ref = document.getElementById('paynow_reference').value.trim();
    if (!ref) {
      alert('Please enter your PayNow reference number.');
      return;
    }

    setBusy(this, true, 'Submitting...');
    try {
      const res = await fetch(`/payments/${currentAppId}/submit-reference`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf(),
        },
        body: JSON.stringify({ reference: ref })
      });

      const data = await res.json();
      if (data.success) {
        showPaySuccess(data.message || 'Reference submitted! Awaiting accounts verification.');
        setTimeout(() => window.location.reload(), 1500);
      } else {
        alert(data.message || 'Failed to submit reference.');
      }
    } catch (e) {
      alert('Network error.');
    } finally {
      setBusy(this, false);
    }
  });

  document.getElementById('btnCheckStatus')?.addEventListener('click', async function() {
    if (!currentAppId) return;

    try {
      const res = await fetch(`/payments/${currentAppId}/status`, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();

      if (data.paid) {
        showPaySuccess('Payment confirmed! Your application is now being processed.');
        setTimeout(() => window.location.reload(), 1500);
      } else {
        alert('Payment not yet confirmed. Status: ' + (data.status || 'pending'));
      }
    } catch (e) {
      alert('Could not check status. Please try again.');
    }
  });

  // Proof / waiver AJAX
  document.getElementById('proofForm')?.addEventListener('submit', function(ev) {
    ev.preventDefault();
    if (!currentAppId) return;
    postForm(`/payments/${currentAppId}/upload-proof`, this, document.getElementById('btnSubmitProof'));
  });

  document.getElementById('waiverForm')?.addEventListener('submit', function(ev) {
    ev.preventDefault();
    if (!currentAppId) return;
    postForm(`/payments/${currentAppId}/upload-waiver`, this, document.getElementById('btnSubmitWaiver'));
  });

})();
</script>
@endpush
