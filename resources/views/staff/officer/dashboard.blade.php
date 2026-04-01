@extends('layouts.portal')
@section('title', 'Accreditation Officer Dashboard')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Accreditation Officer Dashboard</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Review new submissions and request corrections. Approved items go to <b>Registrar</b>.
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
      <i class="ri-checkbox-circle-line" style="font-size: var(--font-size-lg); line-height: 1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @php
    $items = collect(method_exists($applications, 'items') ? $applications->items() : $applications);
    $summaryTotal = $kpis['total_applications'] ?? (method_exists($applications, 'total') ? $applications->total() : $items->count());

    $summaryPending = $kpis['pending_applications'] ?? $items->filter(fn($x) => in_array(strtolower((string)($x->status ?? '')), [
      'submitted', 'officer_review', 'under_officer_review', 'needs_correction', 'returned_from_payments', 'returned_from_registrar'
    ], true))->count();

    $summaryCorrections = $kpis['corrections'] ?? $items->filter(fn($x) => in_array(strtolower((string)($x->status ?? '')), ['correction_requested','corrections_requested','needs_correction'], true))->count();

    $detailsUrlTemplate = route('staff.applications.details', ['application' => '__ID__']);
    $approveUrl = fn($id) => route('staff.officer.applications.approve', $id);
    $correctionUrl = fn($id) => route('staff.officer.applications.requestCorrection', $id);
  @endphp

  {{-- Summary cards --}}
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Total Applications</div>
            <div class="h3 fw-black mb-0">{{ $summaryTotal }}</div>
          </div>
          <div class="icon-box text-primary"><i class="ri-folders-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Pending action</div>
            <div class="h3 fw-black mb-0">{{ $summaryPending }}</div>
          </div>
          <div class="icon-box" style="background:transparent;border-left:3px solid var(--zmc-accent);border-radius:0;">
            <i class="ri-time-line" style="color:var(--zmc-accent)"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Corrections</div>
            <div class="h3 fw-black mb-0">{{ $summaryCorrections }}</div>
          </div>
          <div class="icon-box text-warning"><i class="ri-chat-check-line"></i></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Records Summary Section --}}
  <div class="row g-3 mb-4">
    <div class="col-12">
      <h6 class="fw-bold mb-3">
        <i class="ri-file-list-3-line me-2" style="color:var(--zmc-accent)"></i> Records Management
      </h6>
    </div>
    <div class="col-12 col-md-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-bold m-0">Media Practitioners</h6>
            <div class="text-muted small">Compare total media practitioners in system vs accredited</div>
          </div>
          <div class="icon-box text-primary"><i class="ri-user-star-line"></i></div>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="border rounded p-2 text-center">
              <div class="h4 fw-bold mb-0">{{ $kpis['media_practitioners_total'] ?? 0 }}</div>
              <div class="small text-muted">Total</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded p-2 text-center">
              <div class="h4 fw-bold mb-0 text-success">{{ $kpis['media_practitioners_accredited'] ?? 0 }}</div>
              <div class="small text-muted">Accredited</div>
            </div>
          </div>
        </div>
        <a href="{{ route('staff.officer.records.accredited-journalists') }}" class="btn btn-outline-primary w-100">
          <i class="ri-eye-line me-1"></i> View All Media Practitioners
        </a>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h6 class="fw-bold m-0">Media Houses</h6>
            <div class="text-muted small">Compare total media houses in system vs registered</div>
          </div>
          <div class="icon-box text-success"><i class="ri-building-line"></i></div>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="border rounded p-2 text-center">
              <div class="h4 fw-bold mb-0">{{ $kpis['media_houses_total'] ?? 0 }}</div>
              <div class="small text-muted">Total</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded p-2 text-center">
              <div class="h4 fw-bold mb-0 text-success">{{ $kpis['media_houses_registered'] ?? 0 }}</div>
              <div class="small text-muted">Registered</div>
            </div>
          </div>
        </div>
        <a href="{{ route('staff.officer.records.registered-mediahouses') }}" class="btn btn-outline-success w-100">
          <i class="ri-eye-line me-1"></i> View All Media Houses
        </a>
      </div>
    </div>
  </div>

  {{-- Renewals Due Section --}}
  @if(($expiringJournalists && $expiringJournalists->count() > 0) || ($expiringMediaHouses && $expiringMediaHouses->count() > 0))
    <div class="zmc-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0">
          <i class="ri-calendar-todo-line me-2" style="color:var(--zmc-accent)"></i> Renewals Due (Next 90 Days)
        </h6>
        <a href="{{ route('staff.officer.renewals.expiring') }}" class="btn btn-sm btn-outline-primary">
          <i class="ri-eye-line me-1"></i> View All
        </a>
      </div>

      <div class="row g-3">
        @if($expiringJournalists && $expiringJournalists->count() > 0)
          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold small text-muted">Media Practitioners ({{ $expiringJournalists->count() }})</div>
                <form method="POST" action="{{ route('staff.officer.renewals.send-reminders') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="record_type" value="accreditation">
                  <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="ri-mail-send-line me-1"></i> Send All Reminders
                  </button>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th>Name</th>
                      <th>Certificate</th>
                      <th>Expires</th>
                      <th class="text-end">Days</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($expiringJournalists as $rec)
                      @php
                        $days = $rec->expires_at ? now()->startOfDay()->diffInDays($rec->expires_at->startOfDay(), false) : null;
                        $urgency = $days !== null && $days <= 30 ? 'danger' : ($days <= 60 ? 'warning' : 'info');
                      @endphp
                      <tr>
                        <td class="small">{{ $rec->holder->name ?? '—' }}</td>
                        <td class="small">{{ $rec->certificate_no ?? '—' }}</td>
                        <td class="small">{{ optional($rec->expires_at)->format('d M Y') ?? '—' }}</td>
                        <td class="text-end">
                          <span class="badge bg-{{ $urgency }}-subtle text-{{ $urgency }} border border-{{ $urgency }}-subtle">
                            {{ $days ?? '—' }} days
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endif

        @if($expiringMediaHouses && $expiringMediaHouses->count() > 0)
          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold small text-muted">Media Houses ({{ $expiringMediaHouses->count() }})</div>
                <form method="POST" action="{{ route('staff.officer.renewals.send-reminders') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="record_type" value="registration">
                  <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="ri-mail-send-line me-1"></i> Send All Reminders
                  </button>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th>Entity</th>
                      <th>Reg No</th>
                      <th>Expires</th>
                      <th class="text-end">Days</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($expiringMediaHouses as $rec)
                      @php
                        $days = $rec->expires_at ? now()->startOfDay()->diffInDays($rec->expires_at->startOfDay(), false) : null;
                        $urgency = $days !== null && $days <= 30 ? 'danger' : ($days <= 60 ? 'warning' : 'info');
                      @endphp
                      <tr>
                        <td class="small">{{ $rec->entity_name ?? '—' }}</td>
                        <td class="small">{{ $rec->registration_no ?? '—' }}</td>
                        <td class="small">{{ optional($rec->expires_at)->format('d M Y') ?? '—' }}</td>
                        <td class="text-end">
                          <span class="badge bg-{{ $urgency }}-subtle text-{{ $urgency }} border border-{{ $urgency }}-subtle">
                            {{ $days ?? '—' }} days
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  @endif

  {{-- Inactive Journalists Section --}}
  @if($inactiveJournalists && $inactiveJournalists->count() > 0)
    <div class="zmc-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0">
          <i class="ri-user-unfollow-line me-2" style="color:#dc3545"></i> Inactive Media Practitioners (2-3 Years)
        </h6>
        <span class="badge bg-danger">{{ $inactiveJournalists->count() }} inactive</span>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th>Name</th>
              <th>Certificate No</th>
              <th>Last Login</th>
              <th class="text-end">Days Inactive</th>
            </tr>
          </thead>
          <tbody>
            @foreach($inactiveJournalists as $rec)
              @php
                $lastActivity = $rec->last_activity ? \Carbon\Carbon::parse($rec->last_activity) : null;
                $daysInactive = $lastActivity ? now()->startOfDay()->diffInDays($lastActivity->startOfDay()) : null;
              @endphp
              <tr>
                <td class="small">{{ $rec->holder->name ?? '—' }}</td>
                <td class="small">{{ $rec->certificate_no ?? '—' }}</td>
                <td class="small">{{ $lastActivity ? $lastActivity->format('d M Y') : 'Never' }}</td>
                <td class="text-end">
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                    {{ $daysInactive ?? '—' }} days
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3 text-muted small">
        <i class="ri-information-line me-1"></i> Showing media practitioners who haven't logged in for 2-3 years. Consider sending reactivation reminders.
      </div>
    </div>
  @endif


  {{-- Table --}}
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0">
        <i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i> Incoming applications
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
            <th><i class="ri-map-pin-line me-1"></i> Collection Region</th>
            <th><i class="ri-calendar-line me-1"></i> Date & Time</th>
            <th style="font-size: var(--font-size-dense);"><i class="ri-flag-line me-1"></i> New or Renewal</th>
            <th style="font-size: var(--font-size-dense);"><i class="ri-global-line me-1"></i> Foreign or Local</th>
          </tr>
        </thead>

        <tbody>
        @forelse($applications as $i => $app)
          @php
            $status = strtolower((string)($app->status ?? ''));
            $badge = match($status) {
              'officer_rejected','rejected' => 'danger',
              'correction_requested','corrections_requested','needs_correction' => 'warning',
              'draft' => 'secondary',
              'accounts_review' => 'info',
              default => 'info',
            };

            $canDecide = in_array($status, ['submitted','officer_review','under_officer_review','returned_from_payments','returned_from_registrar'], true);

            $rowNo = method_exists($applications,'firstItem') && $applications->firstItem() ? ($applications->firstItem() + $i) : ($i + 1);
            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
            
            // Determine New, Renewal or Replacement
            $requestType = strtolower((string)($app->request_type ?? 'new'));
            $newOrRenewal = match($requestType) {
              'renewal' => 'Renewal',
              'replacement' => 'Replacement',
              default => 'New'
            };
            
            // Determine Foreign or Local
            $scope = strtolower((string)($app->journalist_scope ?? $app->residency_type ?? $app->form_data['journalist_scope'] ?? $app->form_data['residency_type'] ?? 'local'));
            $foreignOrLocal = ($scope === 'foreign' || $scope === 'non-resident') ? 'Foreign' : 'Local';
          @endphp

          <tr>
            <td class="text-muted small">{{ $rowNo }}</td>
            <td class="fw-bold text-dark">{{ $ref }}</td>
            <td>{{ $app->applicant?->name ?? '—' }}</td>
            <td class="text-capitalize">{{ $app->collection_region ?? '—' }}</td>
            <td class="small">{{ !empty($app->created_at) ? \Carbon\Carbon::parse($app->created_at)->format('d M Y H:i') : '—' }}</td>
            <td>
              <span class="badge rounded-pill bg-{{ $newOrRenewal === 'Renewal' ? 'info' : ($newOrRenewal === 'Replacement' ? 'warning' : 'primary') }} px-3" style="font-size: var(--font-size-dense-sm);">{{ $newOrRenewal }}</span>
            </td>
            <td>
              <span class="badge rounded-pill bg-{{ $foreignOrLocal === 'Foreign' ? 'warning' : 'success' }} px-3" style="font-size: var(--font-size-dense-sm);">{{ $foreignOrLocal }}</span>
            </td>
          </tr>

          @push('zmc_modals')
            {{-- Correction Modal --}}
            <div class="modal fade zmc-chat-modal" id="corrModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <form class="modal-content" method="POST" action="{{ $correctionUrl($app->id) }}">
                  @csrf

                  <div class="modal-header zmc-modal-header">
                    <div class="d-flex align-items-center gap-2">
                      <div class="zmc-avatar"><i class="fa-solid fa-user"></i></div>
                      <div>
                        <div class="zmc-modal-title">Request correction <span class="ms-2 text-muted" style="font-weight:800;font-size: var(--font-size-sm);">{{ $ref }}</span></div>
                        <div class="zmc-modal-sub">Applicant: <span class="fw-bold">{{ $app->applicant?->name ?? '—' }}</span></div>
                      </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <label class="form-label zmc-lbl mb-2">Message / corrections <span class="text-danger">*</span></label>
                    <textarea name="notes" class="form-control zmc-chat-input" rows="4" required placeholder="State what needs to be corrected / provided…"></textarea>
                    <div class="form-text mt-2">This note is visible to the applicant.</div>
                  </div>

                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark fw-bold">
                      <i class="fa-solid fa-paper-plane me-1"></i>Send
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Approve Modal --}}
            <div class="modal fade" id="approveModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ $approveUrl($app->id) }}">
                  @csrf
                  <div class="modal-header zmc-modal-header">
                    <div>
                      <div class="zmc-modal-title"><i class="fa-solid fa-check me-2" style="color:var(--zmc-accent-dark)"></i>Approve application <span class="ms-2 text-muted" style="font-weight:800;font-size: var(--font-size-sm);">{{ $ref }}</span></div>
                      <div class="zmc-modal-sub">Choose the appropriate category, confirm, and send to Registrar.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    @php
                      $isRegistration = ($app->application_type ?? '') === 'registration';
                      $cats = $isRegistration ? \App\Models\Application::massMediaCategories() : \App\Models\Application::accreditationCategories();
                      $label = $isRegistration ? 'Mass Media Category' : 'Accreditation Category';
                    @endphp

                    <label class="form-label zmc-lbl">{{ $label }} <span class="text-danger">*</span></label>
                    <select name="category_code" class="form-select zmc-input" required>
                      <option value="">-- Select category --</option>
                      @foreach($cats as $code => $name)
                        <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
                      @endforeach
                    </select>

                    <div class="mt-3">
                      <label class="form-label zmc-lbl">Notes (optional)</label>
                      <textarea name="decision_notes" class="form-control zmc-input" rows="3" placeholder="Add any notes (optional)"></textarea>
                    </div>
                  </div>
                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold"><i class="fa-solid fa-check me-1"></i>Confirm & Send</button>
                  </div>
                </form>
              </div>
            </div>

          @endpush

        @empty
          <tr><td colspan="7" class="text-center py-5 text-muted">No applications found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>

  @stack('zmc_modals')
</div>

{{-- Global Details Modal --}}
<div class="modal fade zmc-modal-pop" id="appDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <div class="zmc-modal-title"><i class="fa-regular fa-file-lines me-2" style="color:var(--zmc-accent-dark)"></i>Application Review</div>
          <div class="zmc-modal-sub" id="mdl_meta">—</div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="mdl_loading" class="d-none text-center py-5">
          <div class="spinner-border" style="color:var(--zmc-accent-dark)"></div>
          <div class="text-muted mt-2" style="font-size: var(--font-size-sm);">Loading…</div>
        </div>

        <div id="mdl_error" class="alert alert-danger d-none"></div>
        <div id="mdl_content_area" class="d-none"></div>
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
    return `<div class="zmc-mdl-block"><div class="zmc-mdl-title">${titleHtml}</div>${bodyHtml}</div>`;
  }

  function zmcInput(label, value, col = 4){
    return `<div class="col-12 col-md-${col}"><label class="form-label zmc-lbl">${label}</label><input type="text" class="form-control zmc-input" value="${zmcFmt(value)}" readonly></div>`;
  }

  function zmcTextarea(label, value, col = 12){
    return `<div class="col-12 col-md-${col}"><label class="form-label zmc-lbl">${label}</label><textarea class="form-control zmc-input" rows="3" readonly>${zmcFmt(value)}</textarea></div>`;
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
        meta.innerHTML = `<span class="badge bg-light text-dark border me-2">${zmcFmt(formCode || 'Form')}</span>Ref: <span class="fw-bold">${zmcFmt(ref)}</span> • Status: <span class="fw-bold" style="color:var(--zmc-accent-dark)">${zmcFmt(status)}</span>`;
      }

      let html = '';

      if (formCode === 'AP3') {
        const body = `
          <div class="row g-3">
            ${zmcInput('Title', app.title)}
            ${zmcInput('First name', app.first_name)}
            ${zmcInput('Surname', app.surname)}
            ${zmcInput('Other names', app.other_names)}
            ${zmcInput('Date of birth', app.dob)}
            ${zmcInput('Sex', app.sex)}
            ${zmcInput('Birth place', app.birth_place)}
            ${zmcInput('Origin', app.origin)}
            ${zmcInput('Nationality', app.nationality)}
            ${zmcInput('ID / Passport', app.id_passport_number)}
            ${zmcInput('Employer', app.employer_name)}
            ${zmcInput('Medium type', app.medium_type)}
            ${zmcInput('Designation', app.designation)}
            ${zmcTextarea('Assignment brief', app.assignment_brief)}
            ${zmcInput('Arrival date', app.arrival_date)}
            ${zmcInput('Departure date', app.departure_date)}
            ${zmcInput('Port of entry', app.port_entry)}
            ${zmcTextarea('Local address', (app.zim_local_address || app.zim_address))}
          </div>
        `;
        html += zmcBlock(`<i class="fa-regular fa-id-card"></i> Media practitioner details`, body);
      }

      if (formCode === 'AP1') {
        const s = data.ap1 || {};

        html += zmcBlock(
          `<i class="fa-regular fa-building"></i> Organisation details`,
          `<div class="row g-3">
            ${zmcInput('Category', s.category)}
            ${zmcInput('Service name', s.service_name)}
            ${zmcInput('Operating model', s.operating_model)}
            ${zmcInput('Organisation', s.org_name)}
            ${zmcInput('Reg no', s.reg_no)}
            ${zmcInput('Website', s.website)}
            ${zmcTextarea('Head office', s.head_office, 6)}
            ${zmcTextarea('Postal address', s.postal_address, 6)}
          </div>`
        );

        html += zmcBlock(
          `<i class="fa-regular fa-address-book"></i> Contact`,
          `<div class="row g-3">
            ${zmcInput('Contact person', s.contact_person, 4)}
            ${zmcInput('Contact email', s.contact_email, 4)}
            ${zmcInput('Contact phone', s.contact_phone, 4)}
          </div>`
        );

        const directors = Array.isArray(data.directors) ? data.directors : [];
        const managers  = Array.isArray(data.managers) ? data.managers : [];

        let dRows = directors.length ? '' : `<tr><td colspan="5" class="text-muted text-center">—</td></tr>`;
        directors.forEach(d => {
          dRows += `<tr><td>${zmcFmt(d.full_name || (d.name ? (d.name + ' ' + (d.surname||'')) : ''))}</td><td>${zmcFmt(d.id_passport)}</td><td>${zmcFmt(d.nationality)}</td><td>${zmcFmt(d.role || d.occupation)}</td><td>${zmcFmt(d.shareholding)}</td></tr>`;
        });

        html += zmcBlock(
          `<i class="fa-solid fa-people-group"></i> Directors`,
          `<div class="table-responsive"><table class="table table-sm align-middle zmc-table-lite"><thead><tr><th>Full name</th><th>ID / Passport</th><th>Nationality</th><th>Role</th><th>Shareholding</th></tr></thead><tbody>${dRows}</tbody></table></div>`
        );

        let mRows = managers.length ? '' : `<tr><td colspan="4" class="text-muted text-center">—</td></tr>`;
        managers.forEach(m => {
          mRows += `<tr><td>${zmcFmt(m.full_name)}</td><td>${zmcFmt(m.position)}</td><td>${zmcFmt(m.qualification)}</td><td>${zmcFmt(m.experience)}</td></tr>`;
        });

        html += zmcBlock(
          `<i class="fa-solid fa-user-gear"></i> Managers`,
          `<div class="table-responsive"><table class="table table-sm align-middle zmc-table-lite"><thead><tr><th>Full name</th><th>Position</th><th>Qualification</th><th>Experience</th></tr></thead><tbody>${mRows}</tbody></table></div>`
        );
      }

      // Previous Applications Block
      const prevApps = Array.isArray(data.previous_applications) ? data.previous_applications : [];
      if (prevApps.length > 0) {
        let prevRows = prevApps.map(pa => `
          <tr>
            <td>${zmcFmt(pa.reference)}</td>
            <td class="text-capitalize">${zmcFmt(pa.type)}</td>
            <td><span class="badge bg-light text-dark border">${zmcFmt(pa.status)}</span></td>
            <td>${zmcFmt(pa.date)}</td>
          </tr>
        `).join('');

        html += zmcBlock(
          `<i class="fa-solid fa-history"></i> Previous Applications`,
          `<div class="table-responsive"><table class="table table-sm align-middle zmc-table-lite"><thead><tr><th>Reference</th><th>Type</th><th>Status</th><th>Date</th></tr></thead><tbody>${prevRows}</tbody></table></div>`
        );
      }

      // Previous Payments Block
      const prevPays = Array.isArray(data.previous_payments) ? data.previous_payments : [];
      if (prevPays.length > 0) {
        let payRows = prevPays.map(p => `
          <tr>
            <td>${zmcFmt(p.reference)}</td>
            <td>${zmcFmt(p.amount)} ${zmcFmt(p.currency)}</td>
            <td class="text-capitalize">${zmcFmt(p.method)}</td>
            <td><span class="badge bg-light text-dark border text-capitalize">${zmcFmt(p.status)}</span></td>
            <td>${zmcFmt(p.date)}</td>
          </tr>
        `).join('');

        html += zmcBlock(
          `<i class="fa-solid fa-money-bill-transfer"></i> Payment History`,
          `<div class="table-responsive"><table class="table table-sm align-middle zmc-table-lite"><thead><tr><th>Reference</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead><tbody>${payRows}</tbody></table></div>`
        );
      }

      const docs = Array.isArray(data.documents) ? data.documents : [];
      let docRows = docs.length ? '' : `<tr><td colspan="3" class="text-muted text-center">—</td></tr>`;
      docs.forEach(doc => {
        const open = doc.url
          ? `<a href="${doc.url}" target="_blank" class="btn btn-sm btn-outline-primary" title="Open document"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>`
          : `<span class="text-muted">—</span>`;
        docRows += `<tr><td class="fw-bold">${zmcFmt(doc.document_type)}</td><td>${zmcFmt(doc.original_name || doc.file_name)}</td><td class="text-end">${open}</td></tr>`;
      });

      html += zmcBlock(
        `<i class="fa-regular fa-folder-open"></i> Attachments`,
        `<div class="table-responsive"><table class="table table-sm align-middle zmc-table-lite"><thead><tr><th>Type</th><th>File</th><th class="text-end">Open</th></tr></thead><tbody>${docRows}</tbody></table></div>`
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
  });
</script>
@endpush
@endsection
