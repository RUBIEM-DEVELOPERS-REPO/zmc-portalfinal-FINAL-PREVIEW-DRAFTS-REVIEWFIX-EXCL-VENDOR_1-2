@extends('layouts.portal')

@section('title', 'Media House Portal Dashboard')

@section('content')
@php
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Str;

  $detailsUrlTemplate = Route::has('portal.applications.details')
    ? route('portal.applications.details', ['application' => '__ID__'])
    : url('/portal/applications/__ID__/details');

  $apps = $recentApplications ?? [];
@endphp

<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Media House Registration Dashboard</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Track your AP1 (New Registration) and AP5 (Renewal/Replacement) submissions.
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('mediahouse.renewals') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-refresh-line me-1"></i> Renew / Replace
      </a>
      <a href="{{ route('mediahouse.new') }}" class="btn btn-dark btn-sm px-3">
        <i class="ri-file-add-line me-1"></i> New Registration (AP1)
      </a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Drafts</div>
            <div class="h3 fw-black mb-0">{{ $stats['drafts'] ?? 0 }}</div>
          </div>
          <div class="icon-box text-secondary"><i class="ri-draft-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Active</div>
            <div class="h3 fw-black mb-0">{{ $stats['active'] ?? 0 }}</div>
          </div>
          <div class="icon-box text-primary"><i class="ri-folders-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Approved</div>
            <div class="h3 fw-black mb-0">{{ $stats['approved'] ?? 0 }}</div>
          </div>
          <div class="icon-box text-success"><i class="ri-check-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Pending review</div>
            <div class="h3 fw-black mb-0">{{ $stats['pending'] ?? 0 }}</div>
          </div>
          <div class="icon-box" style="background:transparent;border-left:3px solid var(--zmc-accent);border-radius:0;">
            <i class="ri-time-line" style="color:var(--zmc-accent)"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold m-0"><i class="ri-shield-check-line me-2" style="color:var(--zmc-accent)"></i>License Status</h6>
        </div>
        @php
          $record = $latestRecord ?? null;
          $isActive = false;
          $daysRemaining = 0;
          if ($record && $record->expires_at) {
            $now = now();
            $isActive = $record->expires_at->isFuture();
            $daysRemaining = $isActive ? $now->diffInDays($record->expires_at) : 0;
          }
        @endphp

        @if($record)
          <div class="d-flex align-items-center gap-3 mb-3">
            @if($isActive)
              <span class="badge bg-success px-3 py-2" style="font-size:13px;">
                <i class="ri-checkbox-circle-line me-1"></i> Active
              </span>
            @else
              <span class="badge bg-danger px-3 py-2" style="font-size:13px;">
                <i class="ri-close-circle-line me-1"></i> Expired
              </span>
            @endif
          </div>

          <div class="small">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted fw-bold">Registration No:</span>
              <span class="fw-bold">{{ $record->registration_no ?? $record->record_number ?? '—' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted fw-bold">Issued:</span>
              <span>{{ $record->issued_at ? $record->issued_at->format('d M Y') : '—' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted fw-bold">Expires:</span>
              <span>{{ $record->expires_at ? $record->expires_at->format('d M Y') : '—' }}</span>
            </div>
            @if($isActive)
              <div class="d-flex justify-content-between">
                <span class="text-muted fw-bold">Time Remaining:</span>
                <span class="fw-bold {{ $daysRemaining <= 60 ? 'text-warning' : 'text-success' }}">
                  {{ $daysRemaining }} days
                </span>
              </div>
              @if($daysRemaining <= 60)
                <div class="alert alert-warning mt-2 mb-0 py-2 px-3" style="font-size:12px;">
                  <i class="ri-error-warning-line me-1"></i> Your license expires soon. Consider submitting a renewal (AP5).
                </div>
              @endif
            @else
              <div class="alert alert-danger mt-2 mb-0 py-2 px-3" style="font-size:12px;">
                <i class="ri-error-warning-line me-1"></i> Your license has expired. Please submit a renewal application.
              </div>
            @endif
          </div>
        @else
          <div class="text-muted small">No license record found. Submit a registration application to get started.</div>
        @endif
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold m-0"><i class="ri-alarm-warning-line me-2" style="color:var(--zmc-accent)"></i>Levy Reminders</h6>
        </div>

        <div class="small">
          @forelse(($levyReminders ?? collect()) as $reminder)
            <div class="d-flex gap-2 mb-2 p-2 border rounded" style="font-size:12px;">
              <i class="ri-error-warning-fill text-warning" style="font-size:16px; margin-top:2px;"></i>
              <div>
                <div class="fw-bold">{{ $reminder->reminder_type ?? 'Levy Reminder' }}</div>
                <div class="text-muted">{{ $reminder->message }}</div>
                <div class="text-muted" style="font-size:10px;">{{ $reminder->created_at?->diffForHumans() }}</div>
              </div>
            </div>
          @empty
            <div class="text-muted">No outstanding levy reminders.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-megaphone-line me-2" style="color:var(--zmc-accent)"></i>Notices</h6>
          <a href="{{ route('mediahouse.notices') }}" class="btn btn-sm btn-outline-dark">View all</a>
        </div>

        <div class="small text-muted">
          @forelse(($notices ?? collect())->take(4) as $n)
            <div class="d-flex gap-2 mb-2">
              <i class="ri-checkbox-blank-circle-fill" style="font-size:9px; margin-top:5px; color:var(--zmc-accent-dark)"></i>
              <div>
                <div class="fw-bold" style="font-size:12px;">{{ $n->title }}</div>
                <div class="text-muted" style="font-size:11px">{{ Str::limit(strip_tags($n->body), 90) }}</div>
              </div>
            </div>
          @empty
            <div class="text-muted">No notices yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-calendar-event-line me-2" style="color:var(--zmc-accent)"></i>Events</h6>
          <a href="{{ route('mediahouse.notices') }}" class="btn btn-sm btn-outline-dark">View all</a>
        </div>

        <div class="small text-muted">
          @forelse(($events ?? collect())->take(4) as $e)
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-bold" style="font-size:12px">{{ $e->title }}</div>
                <div class="text-muted" style="font-size:11px">
                  {{ optional($e->starts_at)->format('d M Y') ?? 'TBA' }}
                  {{ $e->location ? (' • ' . $e->location) : '' }}
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted">No events yet.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold m-0"><i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i>My Applications</h6>
        
        <ul class="nav nav-pills" role="tablist" style="font-size: 13px;">
          <li class="nav-item" role="presentation">
            <button class="nav-link active px-3 py-1" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-pane" type="button" role="tab">
              <i class="ri-folders-line me-1"></i> All
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link px-3 py-1" id="drafts-tab" data-bs-toggle="tab" data-bs-target="#drafts-pane" type="button" role="tab">
              <i class="ri-draft-line me-1"></i> Drafts <span class="badge bg-secondary ms-1">{{ $stats['drafts'] ?? 0 }}</span>
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link px-3 py-1" id="submitted-tab" data-bs-toggle="tab" data-bs-target="#submitted-pane" type="button" role="tab">
              <i class="ri-send-plane-line me-1"></i> Submitted
            </button>
          </li>
        </ul>
      </div>
    </div>

    <div class="tab-content">
      {{-- ALL APPLICATIONS TAB --}}
      <div class="tab-pane fade show active" id="all-pane" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th><i class="ri-hashtag me-1"></i> Ref</th>
                <th><i class="ri-file-text-line me-1"></i> Form</th>
                <th><i class="ri-calendar-line me-1"></i> Date</th>
                <th><i class="ri-flag-line me-1"></i> Status</th>
                <th class="text-end" style="min-width:140px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($apps as $app)
                @php
                  $status = strtolower((string)($app->status ?? ''));
                  $badge = match(true) {
                    (bool)($app->is_draft) => 'secondary',
                    in_array($status, ['payment_rejected'], true) => 'danger',
                    str_contains($status, 'rejected') => 'danger',
                    in_array($status, ['approved_awaiting_payment','registrar_approved_pending_reg_fee'], true) => 'warning',
                    in_array($status, ['awaiting_accounts_verification'], true) => 'info',
                    in_array($status, ['payment_verified','submitted_with_app_fee'], true) => 'success',
                    str_contains($status, 'approved') || $status === 'issued' => 'success',
                    in_array($status, ['needs_correction','correction_requested'], true) => 'warning',
                    in_array($status, ['submitted','officer_review','registrar_review','accounts_review'], true) => 'info',
                    default => 'warning',
                  };

                  $date = $app->is_draft
                    ? 'Draft'
                    : (($app->submitted_at?->format('d M Y')) ?? ($app->created_at?->format('d M Y') ?? '—'));

                  $form = $app->request_type === 'new'
                    ? 'AP1 - New Registration'
                    : ('AP5 - ' . ucfirst($app->request_type ?? 'renewal'));
                @endphp

                <tr>
                  <td class="fw-bold text-dark">{{ $app->reference ?? ('APP-' . $app->id) }}</td>
                  <td>{{ $form }}</td>
                  <td class="small text-muted">{{ $date }}</td>
                  <td>
                    <span class="badge rounded-pill bg-{{ $badge }} px-3">
                      {{ ucwords(str_replace('_',' ', $status ?: ($app->is_draft ? 'draft' : 'processing'))) }}
                    </span>
                  </td>
                  <td class="text-end">
                    @include('portal.partials.application_actions', ['app' => $app, 'status' => $status])
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">No applications found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- DRAFTS TAB --}}
      <div class="tab-pane fade" id="drafts-pane" role="tabpanel">
        <div class="alert alert-info border-0 shadow-sm mx-3 mt-3 mb-2" style="border-left: 4px solid #0ea5e9 !important;">
          <div class="d-flex align-items-center">
            <i class="ri-information-line fs-4 me-3 text-info"></i>
            <div>
              <div class="fw-bold text-dark" style="font-size:14px;">Draft Expiry Policy</div>
              <div class="small text-slate-600">Applications in draft status are automatically deleted after <strong>14 days</strong> of inactivity. Please ensure you submit your application within this period to avoid losing your progress.</div>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th><i class="ri-hashtag me-1"></i> Ref</th>
                <th><i class="ri-file-text-line me-1"></i> Form</th>
                <th><i class="ri-calendar-line me-1"></i> Last Updated</th>
                <th class="text-end" style="min-width:140px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $drafts = collect($apps)->filter(fn($app) => $app->is_draft);
              @endphp
              @forelse($drafts as $app)
                @php
                  $form = $app->request_type === 'new'
                    ? 'AP1 - New Registration'
                    : ('AP5 - ' . ucfirst($app->request_type ?? 'renewal'));
                @endphp

                <tr>
                  <td class="fw-bold text-dark">{{ $app->reference ?? ('APP-' . $app->id) }}</td>
                  <td>{{ $form }}</td>
                  <td class="small text-muted">{{ $app->updated_at?->format('d M Y, H:i') ?? '—' }}</td>
                  <td class="text-end">
                    <div class="zmc-action-strip">
                      <a class="btn btn-sm btn-primary" href="{{ route('mediahouse.new') }}" title="Continue Editing">
                        <i class="ri-edit-line me-1"></i> Continue
                      </a>
                      <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-danger js-delete-draft" data-app-id="{{ $app->id }}" title="Delete Draft">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-5 text-muted">
                    <i class="ri-draft-line" style="font-size: 3rem; opacity: 0.3;"></i>
                    <div class="mt-2">No drafts found.</div>
                    <div class="small">Start a new registration to create a draft.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- SUBMITTED TAB --}}
      <div class="tab-pane fade" id="submitted-pane" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th><i class="ri-hashtag me-1"></i> Ref</th>
                <th><i class="ri-file-text-line me-1"></i> Form</th>
                <th><i class="ri-calendar-line me-1"></i> Submitted</th>
                <th><i class="ri-flag-line me-1"></i> Status</th>
                <th class="text-end" style="min-width:140px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @php
                $submitted = collect($apps)->filter(fn($app) => !$app->is_draft);
              @endphp
              @forelse($submitted as $app)
                @php
                  $status = strtolower((string)($app->status ?? ''));
                  $badge = match(true) {
                    in_array($status, ['payment_rejected'], true) => 'danger',
                    str_contains($status, 'rejected') => 'danger',
                    in_array($status, ['approved_awaiting_payment','registrar_approved_pending_reg_fee'], true) => 'warning',
                    in_array($status, ['awaiting_accounts_verification'], true) => 'info',
                    in_array($status, ['payment_verified','submitted_with_app_fee'], true) => 'success',
                    str_contains($status, 'approved') || $status === 'issued' => 'success',
                    in_array($status, ['needs_correction','correction_requested'], true) => 'warning',
                    in_array($status, ['submitted','officer_review','registrar_review','accounts_review'], true) => 'info',
                    default => 'warning',
                  };

                  $form = $app->request_type === 'new'
                    ? 'AP1 - New Registration'
                    : ('AP5 - ' . ucfirst($app->request_type ?? 'renewal'));
                @endphp

                <tr>
                  <td class="fw-bold text-dark">{{ $app->reference ?? ('APP-' . $app->id) }}</td>
                  <td>{{ $form }}</td>
                  <td class="small text-muted">{{ $app->submitted_at?->format('d M Y') ?? '—' }}</td>
                  <td>
                    <span class="badge rounded-pill bg-{{ $badge }} px-3">
                      {{ ucwords(str_replace('_',' ', $status ?: 'processing')) }}
                    </span>
                  </td>
                  <td class="text-end">
                    @include('portal.partials.application_actions', ['app' => $app, 'status' => $status])
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="ri-send-plane-line" style="font-size: 3rem; opacity: 0.3;"></i>
                    <div class="mt-2">No submitted applications found.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- Global Details Modal (View) --}}
<div class="modal fade zmc-modal-pop" id="appDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <div class="zmc-modal-title">
            <i class="fa-regular fa-file-lines me-2" style="color:var(--zmc-accent-dark)"></i>
            Application Details
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
        <div id="mdl_content_area" class="d-none"></div>
      </div>

      <div class="modal-footer zmc-modal-footer">
        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@include('portal.partials.payment_modal')

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
      const ref = app.reference || app.application_number || ('APP-' + (app.id || appId));
      const status = app.status || '—';

      if (meta) {
        meta.innerHTML = `<span class="badge bg-light text-dark border me-2">${zmcFmt(formCode || 'Form')}</span>Ref: <span class="fw-bold">${zmcFmt(ref)}</span> • Status: <span class="fw-bold" style="color:var(--zmc-accent-dark)">${zmcFmt(status)}</span>`;
      }

      let html = '';

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
          dRows += `<tr><td>${zmcFmt(d.full_name)}</td><td>${zmcFmt(d.id_passport)}</td><td>${zmcFmt(d.nationality)}</td><td>${zmcFmt(d.role)}</td><td>${zmcFmt(d.shareholding)}</td></tr>`;
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
    document.addEventListener('click', async function(e){
      const btn = e.target.closest('.js-view-more');
      if(btn){
        const appId = btn.getAttribute('data-app-id');
        if(!appId) return;
        zmcOpenModal('#appDetailsModal');
        loadApplicationDetails(appId);
        return;
      }

      // Delete Draft
      const delBtn = e.target.closest('.js-delete-draft');
      if(delBtn){
        const appId = delBtn.getAttribute('data-app-id');
        if(!confirm('Are you sure you want to delete this draft? This action cannot be undone.')) return;
        
        try {
          const res = await fetch(`/portal/mediahouse/applications/${appId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          });
          const data = await res.json();
          if(data.success){
            window.location.reload();
          } else {
            alert(data.message || 'Error deleting draft.');
          }
        } catch(err) {
          alert('Failed to connect to server.');
        }
        return;
      }

      // Withdraw Application
      const withBtn = e.target.closest('.js-withdraw-app');
      if(withBtn){
        const appId = withBtn.getAttribute('data-app-id');
        if(!confirm('Are you sure you want to withdraw this application? It will be moved back to your drafts.')) return;

        try {
          const res = await fetch(`/portal/mediahouse/applications/${appId}/withdraw`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            }
          });
          const data = await res.json();
          if(data.success){
            window.location.reload();
          } else {
            alert(data.message || 'Error withdrawing application.');
          }
        } catch(err) {
          alert('Failed to connect to server.');
        }
      }
    });
  });
</script>
@endpush
@endsection
