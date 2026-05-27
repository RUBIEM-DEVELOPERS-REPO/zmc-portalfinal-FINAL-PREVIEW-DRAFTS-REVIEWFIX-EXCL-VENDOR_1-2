@extends('layouts.portal')

@section('title', 'Media Practitioner Portal Dashboard')

@section('content')
@php
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Str;
  use App\Models\Application;

  $detailsUrlTemplate = Route::has('portal.applications.details')
    ? route('portal.applications.details', ['application' => '__ID__'])
    : url('/portal/applications/__ID__/details');

  $apps = $recentApplications ?? [];
@endphp

<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Media Practitioner Accreditation Dashboard</h4>
      <div class="text-muted mt-1" style="font-size:14px;">
        <i class="ri-information-line me-1"></i>
        Track your AP3 (New Accreditation) and AP5 (Renewal/Replacement) submissions.
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-outline-success border shadow-sm btn-sm px-3" onclick="showRequirementsModal()">
        <i class="ri-file-list-3-line me-1"></i> View Requirements
      </button>
      <a href="{{ route('accreditation.renewal') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-refresh-line me-1"></i> Renew
      </a>
      <a href="{{ route('accreditation.replacement') }}" class="btn btn-outline-warning border shadow-sm btn-sm px-3">
        <i class="ri-exchange-line me-1"></i> Replace
      </a>
      <a href="{{ route('accreditation.new') }}" class="btn btn-dark btn-sm px-3">
        <i class="ri-file-add-line me-1"></i> New Accreditation (AP3)
      </a>
    </div>
  </div>

  @if(isset($reminders) && $reminders->count())
    <div class="mb-4">
      @foreach($reminders as $reminder)
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-start gap-2 shadow-sm" role="alert" id="reminder-{{ $reminder->id }}">
          <i class="ri-alarm-warning-line" style="font-size:20px; color:#b45309;"></i>
          <div class="flex-grow-1">
            <strong>{{ ucfirst(str_replace('_', ' ', $reminder->reminder_type)) }}</strong>
            <div class="small mt-1">{{ $reminder->message }}</div>
            <div class="text-muted smaller mt-1">From: {{ $reminder->creator?->name ?? 'ZMC' }} &bull; {{ $reminder->created_at?->diffForHumans() }}</div>
          </div>
          <form method="POST" action="{{ route('accreditation.reminders.acknowledge', $reminder->id) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
              <i class="ri-check-line"></i> Acknowledge
            </button>
          </form>
        </div>
      @endforeach
    </div>
  @endif

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Drafts</div>
            <div class="h3 fw-black mb-0">{{ $stats['drafts'] ?? 0 }}</div>
            <div class="text-muted smaller mt-1" style="font-size:11px;">Drafts expire after 14 days</div>
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
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold m-0"><i class="ri-megaphone-line me-2" style="color:var(--zmc-accent)"></i>Notices</h6>
          <a href="{{ url('/portal/notices-events') }}" class="btn btn-sm btn-outline-dark">View all</a>
        </div>

        <div class="small text-muted">
          @forelse(($notices ?? collect())->take(4) as $n)
            <div class="d-flex gap-2 mb-2">
              <i class="ri-checkbox-blank-circle-fill" style="font-size:9px; margin-top:5px; color:var(--zmc-accent-dark)"></i>
              <div>
                <div class="fw-bold" style="font-size:14px; text-transform: none !important;">{{ $n->title }}</div>
                <div class="text-muted" style="font-size:12px; text-transform: none !important;">{{ Str::limit(strip_tags($n->body), 90) }}</div>
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
          <a href="{{ url('/portal/notices-events') }}" class="btn btn-sm btn-outline-dark">View all</a>
        </div>

        <div class="small text-muted">
          @forelse(($events ?? collect())->take(4) as $e)
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <div class="fw-bold" style="font-size:14px">{{ $e->title }}</div>
                <div class="text-muted" style="font-size:12px">
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
      <ul class="nav nav-tabs mb-0" id="appTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active fw-bold" id="drafts-tab" data-bs-toggle="tab" data-bs-target="#drafts-pane" type="button" role="tab">
            <i class="ri-draft-line me-1"></i>Drafts
            @if(isset($drafts) && $drafts->count())
              <span class="badge bg-warning text-dark ms-1">{{ $drafts->count() }}</span>
            @endif
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-bold" id="all-apps-tab" data-bs-toggle="tab" data-bs-target="#all-apps-pane" type="button" role="tab">
            <i class="ri-list-check-2 me-1"></i>All Applications
          </button>
        </li>
      </ul>
    </div>

    <div class="tab-content">
      <div class="tab-pane fade show active" id="drafts-pane" role="tabpanel">
        @if(isset($drafts) && $drafts->count())
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 zmc-mini-table">
              <thead>
                <tr>
                  <th><i class="ri-hashtag me-1"></i> Ref</th>
                  <th><i class="ri-file-text-line me-1"></i> Type</th>
                  <th><i class="ri-time-line me-1"></i> Last Updated</th>
                  <th><i class="ri-percent-line me-1"></i> Progress</th>
                  <th class="text-end" style="min-width:140px;">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($drafts as $draft)
                  @php
                    $draftType = $draft->request_type === 'new'
                      ? 'New Accreditation (AP3)'
                      : ($draft->request_type === 'renewal' ? 'Renewal (AP5)' : 'Replacement (AP5)');

                    $formData = $draft->form_data ?? [];
                    $filledFields = collect($formData)->filter(fn($v) => !empty($v) && $v !== 'N/A')->count();
                    $totalExpected = $draft->request_type === 'new' ? 25 : 15;
                    $progress = min(100, round(($filledFields / max(1, $totalExpected)) * 100));

                    $continueRoute = $draft->request_type === 'new'
                      ? route('accreditation.new')
                      : ($draft->request_type === 'replacement'
                        ? route('accreditation.replacement', ['draft' => $draft->reference])
                        : route('accreditation.renewal', ['draft' => $draft->reference]));
                  @endphp
                  <tr>
                    <td class="fw-bold text-dark">{{ $draft->reference }}</td>
                    <td>
                      <span class="badge bg-light text-dark border">{{ $draftType }}</span>
                    </td>
                    <td class="small text-muted">{{ $draft->updated_at?->diffForHumans() ?? '—' }}</td>
                    <td style="min-width:120px;">
                      <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:6px;">
                          <div class="progress-bar bg-{{ $progress >= 75 ? 'success' : ($progress >= 40 ? 'warning' : 'secondary') }}" style="width:{{ $progress }}%"></div>
                        </div>
                        <span class="small text-muted">{{ $progress }}%</span>
                      </div>
                    </td>
                    <td class="text-end">
                      <div class="d-flex justify-content-end gap-1">
                        <a class="btn btn-sm btn-outline-primary" href="{{ $continueRoute }}" title="Continue Editing">
                          <i class="ri-edit-line me-1"></i>Continue
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger js-delete-draft" data-app-id="{{ $draft->id }}" title="Delete Draft">
                          <i class="ri-delete-bin-line"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-5">
            <i class="ri-draft-line" style="font-size:48px; color:#cbd5e1;"></i>
            <h6 class="mt-3 text-muted">No drafts in progress</h6>
            <p class="text-muted small">Start a new application and save as draft to see it here.</p>
            <a href="{{ route('accreditation.new') }}" class="btn btn-dark btn-sm mt-2">
              <i class="ri-file-add-line me-1"></i>New Accreditation (AP3)
            </a>
          </div>
        @endif
      </div>

      <div class="tab-pane fade" id="all-apps-pane" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th><i class="ri-hashtag me-1"></i> Ref</th>
                <th><i class="ri-file-text-line me-1"></i> Type</th>
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
                str_contains($status, 'rejected') => 'danger',
                in_array($status, [
                    'approved_by_officer_awaiting_payment_and_registrar_master',
                    'approved_by_accreditation_officer_awaiting_payment',
                    'approved_by_ao_awaiting_payment_and_registrar_master'
                ], true) => 'info',
                in_array($status, [
                    'submitted',
                    'submitted_to_accreditation_officer',
                    'officer_review',
                    'registrar_review',
                    'accounts_review',
                    'awaiting_accounts_verification',
                    'registrar_raised_fix_request',
                    'returned_to_officer'
                ], true) => 'info',
                default => 'warning',
              };

              $date = $app->is_draft
                ? 'Draft'
                : (($app->submitted_at?->format('d M Y')) ?? ($app->created_at?->format('d M Y') ?? '—'));

              $typeLabel = $app->request_type === 'new'
                ? 'New Accreditation'
                : ($app->request_type === 'renewal' ? 'Renewal' : 'Replacement');
            @endphp

            <tr>
              <td class="fw-bold text-dark">{{ $app->reference ?? ('APP-' . $app->id) }}</td>
              <td>{{ $typeLabel }}</td>
              <td class="small text-muted">{{ $date }}</td>
              <td>
                <span class="badge rounded-pill bg-{{ $badge }} px-3">
                  {{ ucwords(str_replace('_',' ', $status ?: ($app->is_draft ? 'draft' : 'processing'))) }}
                </span>
              </td>
              <td class="text-end">
                <div class="zmc-action-strip">
                  @if($app->is_draft)
                    <a class="btn btn-sm zmc-icon-btn btn-outline-secondary" href="{{ route('accreditation.new') }}" title="Continue">
                      <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-danger js-delete-draft" data-app-id="{{ $app->id }}" title="Delete Draft">
                      <i class="fa-regular fa-trash-can"></i>
                    </button>
                  @else
                    <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-primary js-view-more" data-app-id="{{ $app->id }}" title="View">
                      <i class="fa-regular fa-eye"></i>
                    </button>
                    @if($status === 'correction_requested')
                      <a href="{{ route('accreditation.applications.edit', $app) }}" class="btn btn-sm btn-warning fw-bold" title="Edit & Resubmit">
                        <i class="ri-edit-2-line me-1"></i> Edit
                      </a>
                    @endif
                    @php
                      $canPay = in_array($app->status, [
                        'accounts_review',
                        'approved_by_officer_awaiting_payment_and_registrar_master',
                        'approved_by_accreditation_officer_awaiting_payment',
                        'approved_by_ao_awaiting_payment_and_registrar_master'
                      ], true);
                    @endphp
                    @if($canPay)
                      <button type="button" class="btn btn-sm btn-success fw-bold js-pay-now" 
                              data-app-id="{{ $app->id }}" 
                              data-app-ref="{{ $app->reference }}"
                              title="Pay Now">
                        <i class="ri-bank-card-line me-1"></i> Pay
                      </button>
                    @endif
                    @if(in_array($status, ['submitted','officer_review','registrar_review','accounts_review']))
                      <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-danger js-withdraw-app" data-app-id="{{ $app->id }}" title="Withdraw Application">
                        <i class="ri-arrow-go-back-line"></i>
                      </button>
                    @endif
                  @endif
                </div>
              </td>
            </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">No submitted applications yet.</td>
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

      const fd = data.form_data || {};
      const labels = data.labels || {};
      const exclude = ['current_step', 'registration_scope', 'journalist_scope', 'directors', 'managers', 'directors_rows', 'managers_rows', 'ap1'];

      let fieldsHtml = '';
      for (const [key, val] of Object.entries(fd)) {
        if (exclude.includes(key) || !val) continue;
        const label = labels[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        if (typeof val === 'object' && val !== null) {
          fieldsHtml += zmcTextarea(label, JSON.stringify(val, null, 2));
        } else {
          fieldsHtml += zmcInput(label, val);
        }
      }

      if (fieldsHtml) {
        html += zmcBlock(`<i class="fa-regular fa-id-card"></i> Application details`, `<div class="row g-3">${fieldsHtml}</div>`);
      }

      const docs = Array.isArray(data.documents) ? data.documents : [];
      let docRows = docs.length ? '' : `<tr><td colspan="3" class="text-muted text-center">—</td></tr>`;
      docs.forEach(doc => {
        const open = doc.url
          ? `<a href="${doc.url}" target="_blank" class="btn btn-sm btn-outline-primary" title="Open document"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>`
          : `<span class="text-muted">—</span>`;
        docRows += `<tr><td class="fw-bold">${zmcFmt(doc.document_type)}</td><td>${zmcFmt(doc.original_name || doc.file_name)}</td><td class="text-end">${open}</td></tr>`;
      });

      html += zmcBlock(`<i class="fa-regular fa-folder-open"></i> Attachments`, `<div class="table-responsive"><table class="table table-sm align-middle zmc-table-lite"><thead><tr><th>Type</th><th>File</th><th class="text-end">Open</th></tr></thead><tbody>${docRows}</tbody></table></div>`);

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
          const res = await fetch(`/portal/accreditation/applications/${appId}`, {
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
          const res = await fetch(`/portal/accreditation/applications/${appId}/withdraw`, {
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

    // Show welcome modal on every page load
    const modalEl = document.getElementById('welcomeRequirementsModal');
    if (modalEl) {
      setTimeout(() => {
        const m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
      }, 800);
    }
  });

  // Function to manually show requirements modal
  function showRequirementsModal() {
    const modalEl = document.getElementById('welcomeRequirementsModal');
    if (modalEl) {
      const m = bootstrap.Modal.getOrCreateInstance(modalEl);
      m.show();
    }
  }
</script>
@endpush
</div>

{{-- Media Practitioner Requirements Welcome Modal - Updated with Green Theme --}}
<div class="modal fade" id="welcomeRequirementsModal" tabindex="-1" aria-labelledby="practitionerWelcomeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 24px;">
      <div class="modal-body p-0">
        <div class="row g-0">
          <div class="col-md-4 d-none d-md-block position-relative" style="background: url('/zmc_building.png') center center / cover no-repeat;">
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(0, 0, 0, 0.82), rgba(0, 0, 0, 0.88)); z-index: 1;"></div>
            <div class="h-100 d-flex flex-column justify-content-center p-5 text-white position-relative" style="z-index: 2;">
              <i class="ri-user-star-line mb-4" style="font-size: 80px; color: #ffffff;"></i>
              <h2 class="fw-black mb-3" style="color: #ffffff;">Welcome, Practitioner!</h2>
              <p style="color: rgba(255,255,255,0.9);">Getting accredited is an important step in your media career. Ensure you have all necessary documents ready for a smooth AP3 application process.</p>
            </div>
          </div>
          <div class="col-md-8">
            <div class="p-4 p-md-5">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-black m-0" id="practitionerWelcomeModalLabel" style="color: #1e293b;">Media Practitioner Accreditation Requirements</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="row g-4 mb-4">
                <div class="col-md-6">
                  <div class="fw-bold mb-3" style="color: #1a1a1a;"><i class="ri-user-line me-2"></i>Local Applicants</div>
                  <ul class="list-unstyled small mb-0">
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>National ID</li>
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Education Certificate</li>
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Letter of Employment (if employed)</li>
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Passport Sized Photo</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <div class="fw-bold mb-3" style="color: #1a1a1a;"><i class="ri-global-line me-2"></i>Foreign Applicants</div>
                  <ul class="list-unstyled small mb-0">
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Passport</li>
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Education Certificate</li>
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Passport Sized Photo</li>
                    <li class="mb-2" style="color: #334155;"><i class="ri-checkbox-circle-fill me-2" style="color: #1a1a1a;"></i>Clearance Letter</li>
                  </ul>
                </div>
              </div>

              <div class="alert mb-4" style="background: rgba(245, 197, 24, 0.1); border-left: 4px solid #1a1a1a;">
                <div class="d-flex align-items-start gap-2">
                  <i class="ri-information-line" style="color: #1a1a1a; font-size: 1.2rem; margin-top: 2px;"></i>
                  <div class="small" style="color: #334155;">
                    <strong style="color: #1a1a1a;">Important:</strong> All documents must be clear, legible, and in acceptable formats (PDF, JPG, PNG). Ensure your education certificates are certified copies where required.
                  </div>
                </div>
              </div>

              <div class="d-grid">
                <button type="button" class="btn btn-lg py-3 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal" style="background: #1a1a1a; border-color: #1a1a1a; color: #ffffff;">
                  I Understand, Let's Begin <i class="ri-arrow-right-line ms-2"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .fw-black { font-weight: 900 !important; }
</style>

@endsection
