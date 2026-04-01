@extends('layouts.portal')
@section('title', 'Accounts & Payments Dashboard')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        Accounts & Payments Dashboard
      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Confirm payments and pass successful items to <b>Registrar</b>.
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span class="zmc-pill zmc-pill-dark">
        <i class="ri-map-pin-user-line"></i>
        <span>Region: {{ auth()->user()->region ?? 'NOT SET' }}</span>
      </span>
      <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3" title="Refresh">
        <i class="ri-refresh-line me-1"></i> Refresh
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @php
    $items = collect(method_exists($applications, 'items') ? $applications->items() : $applications);
    $summaryTotal = method_exists($applications, 'total') ? $applications->total() : $items->count();

    $summaryPending = $items->filter(fn($x) => in_array(strtolower((string)($x->status ?? '')), [
      'accounts_review','returned_to_accounts','awaiting_accounts_verification','pending_accounts_from_registrar'
    ], true))->count();

    $summaryPaid = $items->filter(fn($x) => strtolower((string)($x->status ?? '')) === 'paid_confirmed')->count();
    $summaryReturned = $items->filter(fn($x) => strtolower((string)($x->status ?? '')) === 'returned_to_accounts')->count();
    $summaryAwaitingVerification = $items->filter(fn($x) => strtolower((string)($x->status ?? '')) === 'awaiting_accounts_verification')->count();
    $summaryFromRegistrar = $items->filter(fn($x) => strtolower((string)($x->status ?? '')) === 'pending_accounts_from_registrar')->count();

    $detailsUrlTemplate = route('staff.applications.details', ['application' => '__ID__']);

    $paidUrl = fn($id) => route('staff.accounts.applications.paid', $id);
    $returnUrl = fn($id) => route('staff.accounts.applications.return', $id);
    $rejectPaymentUrl = fn($id) => route('staff.accounts.applications.payment.reject', $id);
  @endphp

  {{-- Summary cards --}}
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Total Applications</div>
            <div class="h3 fw-black mb-0">{{ $totalApplications }}</div>
            <div class="mt-2">
              <a href="#" class="btn btn-sm btn-outline-primary me-1" title="Download Applications">
                <i class="ri-download-2-line"></i>
              </a>
              <a href="#" class="btn btn-sm btn-outline-info" title="View Analytics">
                <i class="ri-bar-chart-line"></i>
              </a>
            </div>
          </div>
          <div class="icon-box text-primary"><i class="ri-folders-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Paid via Pay Now</div>
            <div class="h3 fw-black mb-0 text-success">{{ $paidViaPayNow }}</div>
            <div class="mt-2">
              <a href="#" class="btn btn-sm btn-outline-primary me-1" title="Download Pay Now Payments">
                <i class="ri-download-2-line"></i>
              </a>
              <a href="#" class="btn btn-sm btn-outline-info" title="View Analytics">
                <i class="ri-bar-chart-line"></i>
              </a>
            </div>
          </div>
          <div class="icon-box text-success"><i class="ri-bank-card-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Paid via Uploads</div>
            <div class="h3 fw-black mb-0 text-info">{{ $paidViaUploads }}</div>
            <div class="mt-2">
              <a href="#" class="btn btn-sm btn-outline-primary me-1" title="Download Upload Payments">
                <i class="ri-download-2-line"></i>
              </a>
              <a href="#" class="btn btn-sm btn-outline-info" title="View Analytics">
                <i class="ri-bar-chart-line"></i>
              </a>
            </div>
          </div>
          <div class="icon-box text-info"><i class="ri-upload-2-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Pending Action</div>
            <div class="h3 fw-black mb-0 text-warning">{{ $pendingAction }}</div>
            <div class="mt-2">
              <a href="#" class="btn btn-sm btn-outline-primary me-1" title="Download Pending Actions">
                <i class="ri-download-2-line"></i>
              </a>
              <a href="#" class="btn btn-sm btn-outline-info" title="View Analytics">
                <i class="ri-bar-chart-line"></i>
              </a>
            </div>
          </div>
          <div class="icon-box text-warning"><i class="ri-time-line"></i></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Approved (Paid)</div>
            <div class="h3 fw-black mb-0 text-success">{{ $approvedPaid }}</div>
            <div class="mt-2">
              <a href="#" class="btn btn-sm btn-outline-primary me-1" title="Download Approved Applications">
                <i class="ri-download-2-line"></i>
              </a>
              <a href="#" class="btn btn-sm btn-outline-info" title="View Analytics">
                <i class="ri-bar-chart-line"></i>
              </a>
            </div>
          </div>
          <div class="icon-box text-success"><i class="ri-check-double-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Returned for Correction</div>
            <div class="h3 fw-black mb-0 text-danger">{{ $items->filter(fn($x) => in_array(strtolower((string)($x->status ?? '')), ['payment_rejected','returned_to_accounts','returned_from_registrar','returned_to_officer','officer_rejected','registrar_rejected'], true))->count() }}</div>
          </div>
          <div class="icon-box text-danger"><i class="ri-arrow-go-back-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <a href="{{ route('staff.accounts.cash-payment.create') }}" class="text-decoration-none">
        <div class="zmc-card h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-muted small fw-bold">Record Cash Payment</div>
              <div class="small text-primary mt-1"><i class="ri-add-circle-line me-1"></i>New entry</div>
            </div>
            <div class="icon-box text-dark"><i class="ri-money-dollar-circle-line"></i></div>
          </div>
        </div>
      </a>
    </div>
  </div>

  @include('partials.analytics.financial_summary')

  <div class="row g-3 mb-4">
    <div class="col-12">
      <div class="zmc-card shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h6 class="fw-bold m-0"><i class="ri-bar-chart-box-line me-2 text-primary"></i> Monthly Revenue Overview ({{ date('Y') }})</h6>
          <div class="dropdown">
            <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
              Export Report
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-menu-item p-2 text-decoration-none d-block small" href="{{ route('staff.accounts.reports.export-ledger') }}"><i class="ri-file-excel-line me-2 text-success"></i> Excel Format</a></li>
              <li><a class="dropdown-menu-item p-2 text-decoration-none d-block small" href="#"><i class="ri-file-pdf-line me-2 text-danger"></i> PDF Format</a></li>
            </ul>
          </div>
        </div>
        <div style="height: 300px;">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Trends Analytics (Redistributed from IT) --}}
  @include('partials.analytics.trends')

  {{-- Table --}}
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0">
        <i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i> Accounts queue
      </h6>
      @if(method_exists($applications, 'currentPage'))
        <div class="small text-muted">Page {{ $applications->currentPage() }} of {{ $applications->lastPage() }}</div>
      @endif
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th><i class="ri-hashtag me-1"></i> Ref</th>
            <th><i class="ri-user-line me-1"></i> Applicant</th>
            <th>Type</th>
            <th><i class="ri-map-pin-line me-1"></i> Region</th>
            <th><i class="ri-calendar-line me-1"></i> Date</th>
            <th><i class="ri-flag-line me-1"></i> Status</th>
            <th class="text-end" style="min-width:170px;">Action</th>
          </tr>
        </thead>

        <tbody>
        @forelse($applications as $i => $app)
          @php
            $status = strtolower((string)($app->status ?? ''));
            $badge = match($status) {
              'returned_to_accounts' => 'warning',
              'paid_confirmed' => 'success',
              'awaiting_accounts_verification' => 'primary',
              'pending_accounts_from_registrar' => 'secondary',
              'payment_rejected' => 'danger',
              default => 'info',
            };

            $canAct = in_array($status, ['accounts_review','returned_to_accounts','awaiting_accounts_verification','pending_accounts_from_registrar'], true);

            $rowNo = method_exists($applications,'firstItem') && $applications->firstItem()
              ? ($applications->firstItem() + $i)
              : ($i + 1);

            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
          @endphp

          <tr>
            <td class="text-muted small">{{ $rowNo }}</td>
            <td class="fw-bold text-dark">{{ $ref }}</td>
            <td>{{ $app->applicant?->name ?? $app->applicant_name ?? '—' }}</td>
            <td>
              @php
                $reqType = $app->request_type ?? 'new';
                $reqBadge = match($reqType) { 'renewal' => 'warning', 'replacement' => 'info', default => 'success' };
              @endphp
              <span class="badge bg-{{ $reqBadge }}">{{ ucfirst($reqType) }}</span>
            </td>
            <td class="text-capitalize">{{ $app->collection_region ?? '—' }}</td>
            <td class="small">{{ !empty($app->created_at) ? \Carbon\Carbon::parse($app->created_at)->format('d M Y') : '—' }}</td>
            <td>
              <span class="badge rounded-pill bg-{{ $badge }} px-3">
                {{ str_replace('_',' ', in_array($status, ['payment_rejected','officer_rejected','registrar_rejected'], true) ? 'returned_for_correction' : ($status ?: '—')) }}
              </span>
            </td>

            <td class="text-end">
              <div class="zmc-action-strip">

                {{-- Return to Officer --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-dark js-open-modal"
                  data-target="#returnModal{{ $app->id }}"
                  @if(!$canAct) disabled @endif
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Return to Officer"
                >
                  <i class="fa-regular fa-comment-dots"></i>
                </button>

                {{-- View --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-primary js-view-more"
                  data-app-id="{{ $app->id }}"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="View application"
                >
                  <i class="fa-regular fa-eye"></i>
                </button>

                {{-- Mark Paid --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-success js-open-modal"
                  data-target="#paidModal{{ $app->id }}"
                  @if(!$canAct) disabled @endif
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Mark paid & generate receipt"
                >
                  <i class="fa-solid fa-check"></i>
                </button>

                {{-- Reject Payment --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-danger js-open-modal"
                  data-target="#rejectPaymentModal{{ $app->id }}"
                  @if(!$canAct) disabled @endif
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Reject payment"
                >
                  <i class="fa-solid fa-times"></i>
                </button>

                
              </div>
            </td>
          </tr>

          @push('zmc_modals')
            {{-- Return Modal --}}
            <div class="modal fade" id="returnModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <form class="modal-content" method="POST" action="{{ $returnUrl($app->id) }}">
                  @csrf

                  <div class="modal-header zmc-modal-header">
                    <div>
                      <div class="zmc-modal-title">
                        <i class="fa-regular fa-message me-2" style="color:var(--zmc-accent-dark)"></i>
                        Return to Officer
                        <span class="ms-2 text-muted" style="font-weight:800;font-size:12px;">{{ $ref }}</span>
                      </div>
                      <div class="zmc-modal-sub">Send a note back to the Accreditation Officer.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <label class="form-label zmc-lbl">Notes <span class="text-danger">*</span></label>
                    <textarea name="notes" class="form-control zmc-input" rows="4" required placeholder="Why is this being returned? What needs to be corrected?"></textarea>
                  </div>

                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark fw-bold">
                      <i class="fa-solid fa-paper-plane me-1"></i>Send / return
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Paid Modal --}}
            <div class="modal fade" id="paidModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ $paidUrl($app->id) }}">
                  @csrf

                  <div class="modal-header zmc-modal-header">
                    <div>
                      <div class="zmc-modal-title">
                        <i class="fa-solid fa-file-invoice me-2" style="color:var(--zmc-green)"></i>
                        Mark Paid & Generate Receipt
                        <span class="ms-2 text-muted" style="font-weight:800;font-size:12px;">{{ $ref }}</span>
                      </div>
                      <div class="zmc-modal-sub">This will confirm payment, generate receipt, and send to Production.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center gap-2">
                      <i class="fa-solid fa-info-circle"></i>
                      <div>
                        <strong>Receipt will be automatically generated and downloaded</strong><br>
                        <small>Payment status will be updated to "paid" and application will move to Production queue.</small>
                      </div>
                    </div>
                    <label class="form-label zmc-lbl">Notes (optional)</label>
                    <textarea name="decision_notes" class="form-control zmc-input" rows="4" placeholder="Add any notes (optional)"></textarea>
                  </div>

                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">
                      <i class="fa-solid fa-file-invoice me-1"></i>Mark Paid & Generate Receipt
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Reject Payment Modal --}}
            <div class="modal fade" id="rejectPaymentModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <form class="modal-content" method="POST" action="{{ $rejectPaymentUrl($app->id) }}">
                  @csrf

                  <div class="modal-header zmc-modal-header">
                    <div>
                      <div class="zmc-modal-title">
                        <i class="fa-solid fa-times me-2" style="color:#dc3545"></i>
                        Reject Payment
                        <span class="ms-2 text-muted" style="font-weight:800;font-size:12px;">{{ $ref }}</span>
                      </div>
                      <div class="zmc-modal-sub">Applicant will need to resubmit payment.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <label class="form-label zmc-lbl">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control zmc-input" rows="4" required placeholder="Provide a reason for rejecting this payment..."></textarea>
                  </div>

                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                      <i class="fa-solid fa-times me-1"></i>Reject payment
                    </button>
                  </div>
                </form>
              </div>
            </div>
          @endpush

        @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">No applications found.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $applications->links() }}
  </div>

  @stack('zmc_modals')
</div>

{{-- Global Details Modal (View) --}}
<div class="modal fade zmc-modal-pop" id="appDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <div class="zmc-modal-title">
            <i class="fa-regular fa-file-lines me-2" style="color:var(--zmc-accent-dark)"></i>
            Application Review
          </div>
          <div class="zmc-modal-sub" id="mdl_meta">—</div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="mdl_loading" class="d-none text-center py-5">
          <div class="spinner-border" style="color:var(--zmc-accent-dark)"></div>
          <div class="text-muted mt-2" style="font-size:12px;">Loading…</div>
        </div>

        <div id="mdl_error" class="alert alert-danger d-none"></div>

        <div id="mdl_content_area" class="d-none">
          {{-- filled by JS --}}
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
  const ZMC_DETAILS_URL = @json($detailsUrlTemplate);

  function zmcFmt(v){
    if(v === null || v === undefined) return '—';
    const s = String(v).trim();
    return s === '' ? '—' : s;
  }

  function zmcOpenModal(selector){
    const el = document.querySelector(selector);
    if(!el) return;
    if (window.bootstrap && typeof bootstrap.Modal === 'function') {
      bootstrap.Modal.getOrCreateInstance(el).show();
      return;
    }
  }

  function zmcBlock(titleHtml, bodyHtml){
    return `
      <div class="zmc-mdl-block">
        <div class="zmc-mdl-title">${titleHtml}</div>
        ${bodyHtml}
      </div>
    `;
  }

  function zmcInput(label, value, col = 4){
    return `
      <div class="col-12 col-md-${col}">
        <label class="form-label zmc-lbl">${label}</label>
        <input type="text" class="form-control zmc-input" value="${zmcFmt(value)}" readonly>
      </div>
    `;
  }

  function zmcTextarea(label, value, col = 12){
    return `
      <div class="col-12 col-md-${col}">
        <label class="form-label zmc-lbl">${label}</label>
        <textarea class="form-control zmc-input" rows="3" readonly>${zmcFmt(value)}</textarea>
      </div>
    `;
  }

  async function loadApplicationDetails(appId) {
    const loader = document.getElementById('mdl_loading');
    const area   = document.getElementById('mdl_content_area');
    const meta   = document.getElementById('mdl_meta');
    const errBox = document.getElementById('mdl_error');

    if (errBox){ errBox.classList.add('d-none'); errBox.textContent = ''; }
    if (loader) loader.classList.remove('d-none');
    if (area){ area.classList.add('d-none'); area.innerHTML = ''; }
    if (meta) meta.textContent = '—';

    try {
      const url = ZMC_DETAILS_URL.replace('__ID__', appId);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || data.ok === false) throw new Error(data.message || 'Failed to load details');

      const app = data.application || {};
      const formCode = String(app.form_code || '').toUpperCase();
      const ref = app.reference || ('APP-' + (app.id || appId));
      const status = app.status || '—';

      if (meta) {
        meta.innerHTML = `
          <span class="badge bg-light text-dark border me-2">${zmcFmt(formCode || 'Form')}</span>
          Ref: <span class="fw-bold">${zmcFmt(ref)}</span> •
          Status: <span class="fw-bold" style="color:var(--zmc-accent-dark)">${zmcFmt(status)}</span>
        `;
      }

      let html = '';

      // For Accounts Dashboard, show only payment details
      if (data.payment_details) {
        const pay = data.payment_details;
        
        // Payment proof file display
        let proofFileDisplay = '';
        if (pay.payment_proof_path) {
          const proofUrl = `/storage/${pay.payment_proof_path}`;
          proofFileDisplay = `
            <div class="d-flex align-items-center gap-2">
              <a href="${proofUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-file-pdf"></i> View Payment Proof
              </a>
              <span class="text-muted small">
                Uploaded: ${pay.payment_proof_uploaded_at ? new Date(pay.payment_proof_uploaded_at).toLocaleDateString() : '—'}
              </span>
            </div>
          `;
        } else {
          proofFileDisplay = '<span class="text-muted">No payment proof uploaded</span>';
        }

        // Waiver file display
        let waiverFileDisplay = '';
        if (pay.waiver_path) {
          const waiverUrl = `/storage/${pay.waiver_path}`;
          waiverFileDisplay = `
            <div class="d-flex align-items-center gap-2">
              <a href="${waiverUrl}" target="_blank" class="btn btn-sm btn-outline-info">
                <i class="fa-solid fa-file-pdf"></i> View Waiver
              </a>
              <span class="text-muted small">
                Status: <span class="badge bg-${pay.waiver_status === 'approved' ? 'success' : (pay.waiver_status === 'rejected' ? 'danger' : 'warning')}">${zmcFmt(pay.waiver_status)}</span>
              </span>
            </div>
          `;
        }

        html += zmcBlock(
          `<i class="fa-solid fa-credit-card"></i> Payment Details`,
          `
          <div class="row g-3">
            <div class="col-md-6">
              ${zmcInput('Payment Status', pay.payment_status)}
              ${zmcInput('Proof Status', pay.proof_status)}
              ${zmcInput('Amount Paid (USD)', pay.proof_amount_paid)}
              ${zmcInput('Payment Date', pay.proof_payment_date)}
              ${zmcInput('Bank Used', pay.proof_bank_name)}
            </div>
            <div class="col-md-6">
              ${zmcInput('Payer First Name', pay.proof_payer_first_name)}
              ${zmcInput('Payer Last Name', pay.proof_payer_last_name)}
              ${zmcInput('PayNow Reference', pay.paynow_reference)}
              ${zmcInput('PayNow Confirmed At', pay.paynow_confirmed_at)}
              ${zmcInput('Waiver Status', pay.waiver_status)}
            </div>
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label fw-bold">Payment Proof</label>
                ${proofFileDisplay}
              </div>
              ${pay.proof_review_notes ? `
                <div class="mb-3">
                  <label class="form-label fw-bold">Review Notes</label>
                  <div class="alert alert-info small">${zmcFmt(pay.proof_review_notes)}</div>
                </div>
              ` : ''}
            </div>
            ${waiverFileDisplay ? `
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label fw-bold">Waiver Document</label>
                  ${waiverFileDisplay}
                </div>
              </div>
            ` : ''}
          </div>
          `
        );
      }

      const docs = Array.isArray(data.documents) ? data.documents : [];
      let docRows = docs.length ? '' : `<tr><td colspan="3" class="text-muted text-center">—</td></tr>`;
      docs.forEach(doc => {
        const open = doc.url
          ? `<a href="${doc.url}" target="_blank" class="btn btn-sm btn-outline-primary" title="Open document">
               <i class="fa-solid fa-arrow-up-right-from-square"></i>
             </a>`
          : `<span class="text-muted">—</span>`;
        docRows += `
          <tr>
            <td class="fw-bold">${zmcFmt(doc.document_type)}</td>
            <td>${zmcFmt(doc.original_name || doc.file_name)}</td>
            <td class="text-end">${open}</td>
          </tr>
        `;
      });

      html += zmcBlock(
        `<i class="fa-regular fa-folder-open"></i> Attachments`,
        `
        <div class="table-responsive">
          <table class="table table-sm align-middle zmc-table-lite">
            <thead>
              <tr><th>Type</th><th>File</th><th class="text-end">Open</th></tr>
            </thead>
            <tbody>${docRows}</tbody>
          </table>
        </div>
        `
      );

      if (area) {
        area.innerHTML = html;
        area.classList.remove('d-none');
      }

    } catch (e) {
      if (errBox) {
        errBox.textContent = 'Error loading details: ' + (e.message || 'Unknown error');
        errBox.classList.remove('d-none');
      }
    } finally {
      if (loader) loader.classList.add('d-none');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.bootstrap && typeof bootstrap.Tooltip === 'function') {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }

    document.addEventListener('click', function(e){
      const btn = e.target.closest('.js-open-modal');
      if(!btn) return;
      const target = btn.getAttribute('data-target');
      if(target) zmcOpenModal(target);
    });

    document.addEventListener('click', function(e){
      const btn = e.target.closest('.js-view-more');
      if(!btn) return;
      const appId = btn.getAttribute('data-app-id');
      if(!appId) return;

      zmcOpenModal('#appDetailsModal');
      loadApplicationDetails(appId);
    });

    // --- Revenue Chart Initialization ---
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: @json($labels),
          datasets: [{
            label: 'Monthly Revenue (USD)',
            data: @json($chartData),
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#2563eb',
            pointBorderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              mode: 'index',
              intersect: false,
              callbacks: {
                label: function(context) {
                  return 'Revenue: $' + context.parsed.y.toLocaleString();
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { borderDash: [5, 5] },
              ticks: {
                callback: function(value) { return '$' + value.toLocaleString(); }
              }
            },
            x: {
              grid: { display: false }
            }
          }
        }
      });
    }
  });
</script>
@endpush
@endsection
