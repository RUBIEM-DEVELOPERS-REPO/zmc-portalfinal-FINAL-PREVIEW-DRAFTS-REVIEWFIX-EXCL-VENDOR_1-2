@extends('layouts.portal')
@section('title', 'Incoming Queue')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Incoming Queue</h4>
            <div class="text-muted small">Confirmed by Accreditation Officer & Payment Cleared</div>
        </div>
        <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-light border btn-sm">
            <i class="ri-arrow-left-line"></i> Back to Dashboard
        </a>
    </div>

    <div class="zmc-card p-0 shadow-sm border-0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0"><i class="ri-list-check-2 me-2" style="color:#2563eb"></i> Awaiting Review</h6>
            <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Type</th>
                        <th>Officer</th>
                        <th>Payment</th>
                        <th>Cleared At</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="fw-bold">{{ $app->reference }}</td>
                            <td>
                                <div>{{ $app->applicant?->name }}</div>
                                <div class="small text-muted">{{ $app->journalist_scope }} | {{ $app->residency_type }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ strtoupper($app->application_type) }}</span></td>
                            <td class="small">{{ $app->assignedOfficer?->name ?? 'System' }}</td>
                            <td>
                                @php
                                    $p = $app->payments->last();
                                @endphp
                                @if($p)
                                    <span class="badge bg-success-subtle text-success border-success">
                                        {{ strtoupper($p->method) }} ({{ $p->amount }} {{ $p->currency }})
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border-secondary">N/A</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $app->last_action_at?->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                @php
                                    $canApproveForPayment = $app->payment_status !== 'paid' && !$app->registrar_reviewed_at;
                                @endphp
                                <div class="d-flex gap-1 justify-content-end">
                                    {{-- Reassign Category --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning rounded-circle p-2 js-open-modal"
                                        data-target="#reassignModal{{ $app->id }}"
                                        data-bs-toggle="tooltip"
                                        title="Reassign Category"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                    >
                                        <i class="fa-solid fa-award"></i>
                                    </button>

                                    {{-- View --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary rounded-circle p-2 js-view-more"
                                        data-app-id="{{ $app->id }}"
                                        data-bs-toggle="tooltip"
                                        title="View Details"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                    {{-- Approve (shows appropriate modal based on payment status) --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success rounded-circle p-2 js-open-modal"
                                        data-target="{{ $canApproveForPayment ? '#approveForPaymentModal' . $app->id : '#approveModal' . $app->id }}"
                                        data-bs-toggle="tooltip"
                                        title="{{ $canApproveForPayment ? 'Approve for Payment' : 'Final Approval' }}"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                    >
                                        <i class="fa-solid fa-check"></i>
                                    </button>

                                    {{-- Return --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-dark rounded-circle p-2 js-open-modal"
                                        data-target="#returnModal{{ $app->id }}"
                                        data-bs-toggle="tooltip"
                                        title="Return to Accounts"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                    >
                                        <i class="fa-regular fa-comment-dots"></i>
                                    </button>

                                    {{-- Reject --}}
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger rounded-circle p-2 js-open-modal"
                                        data-target="#rejectModal{{ $app->id }}"
                                        data-bs-toggle="tooltip"
                                        title="Reject"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                    >
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No applications awaiting review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
        <div class="p-3 border-top">
            {{ $applications->links() }}
        </div>
        @endif
    </div>

    {{-- Action Modals for each application --}}
    @foreach($applications as $app)
        @php
            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
            $isRegistration = ($app->application_type ?? '') === 'registration';
            $cats = $isRegistration ? \App\Models\Application::massMediaCategories() : \App\Models\Application::accreditationCategories();
            $label = $isRegistration ? 'Mass Media Category' : 'Accreditation Category';
            $currentCat = $isRegistration ? $app->media_house_category_code : $app->accreditation_category_code;
        @endphp

        {{-- Reassign Category Modal --}}
        <div class="modal fade" id="reassignModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.reassign-category', $app) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-award me-2"></i>
                            Reassign Category
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning small">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <strong>{{ $ref }}</strong> - Change the assigned category for this application.
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Current Category</label>
                            <input type="text" class="form-control" value="{{ $currentCat ?? 'NOT ASSIGNED' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">New {{ $label }} <span class="text-danger">*</span></label>
                            <select name="category_code" class="form-select" required>
                                <option value="">-- Select new category --</option>
                                @foreach($cats as $code => $name)
                                    <option value="{{ $code }}" {{ $currentCat == $code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Reason for Reassignment <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="State why the category is being changed..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fa-solid fa-rotate me-1"></i>Reassign Category
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Approve For Payment Modal --}}
        <div class="modal fade" id="approveForPaymentModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.approve-for-payment', $app) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ri-money-dollar-circle-line me-2"></i>
                            Approve for Payment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            <i class="ri-information-line me-1"></i>
                            <strong>{{ $ref }}</strong> - This will forward the application to Accounts for payment processing.
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Notes for Accounts (Optional)</label>
                            <textarea name="decision_notes" class="form-control" rows="3" placeholder="Add any notes for Accounts..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-check-line me-1"></i>Forward to Accounts
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Approve Modal (Final Approval) --}}
        <div class="modal fade" id="approveModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.approve', $app) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ri-check-double-line me-2"></i>
                            Final Approval
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success small">
                            <i class="ri-information-line me-1"></i>
                            <strong>{{ $ref }}</strong> - This will move the application to Production for card/certificate generation.
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ $label }} <span class="text-danger">*</span></label>
                            <select name="category_code" class="form-select" required>
                                <option value="">-- Select category --</option>
                                @foreach($cats as $code => $name)
                                    <option value="{{ $code }}" {{ $currentCat == $code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Notes (Optional)</label>
                            <textarea name="decision_notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-check-line me-1"></i>Approve & Send to Production
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Return Modal --}}
        <div class="modal fade" id="returnModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.return', $app) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ri-arrow-go-back-line me-2"></i>
                            Return to Accounts
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning small">
                            <i class="ri-alert-line me-1"></i>
                            <strong>{{ $ref }}</strong> - This will send the application back to Accounts/Payments.
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Return Reason <span class="text-danger">*</span></label>
                            <textarea name="decision_notes" class="form-control" rows="4" required placeholder="Specify what needs to be corrected or verified..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-arrow-go-back-line me-1"></i>Return to Accounts
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.reject', $app) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ri-close-circle-line me-2"></i>
                            Reject Application
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger small">
                            <i class="ri-error-warning-line me-1"></i>
                            <strong>{{ $ref }}</strong> - This will permanently reject the application. The applicant will be notified.
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="decision_notes" class="form-control" rows="4" required placeholder="Provide a clear reason for the applicant..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-close-line me-1"></i>Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
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
@endsection

@push('scripts')
<script>
  const ZMC_DETAILS_URL = '{{ route('staff.applications.details', ['application' => '__ID__']) }}';

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
          `
          <div class="row g-3">
            ${zmcInput('Category', s.category)}
            ${zmcInput('Service name', s.service_name)}
            ${zmcInput('Operating model', s.operating_model)}
            ${zmcInput('Organisation', s.org_name)}
            ${zmcInput('Reg no', s.reg_no)}
            ${zmcInput('Website', s.website)}
            ${zmcTextarea('Head office', s.head_office, 6)}
            ${zmcTextarea('Postal address', s.postal_address, 6)}
          </div>
          `
        );

        html += zmcBlock(
          `<i class="fa-regular fa-address-book"></i> Contact`,
          `
          <div class="row g-3">
            ${zmcInput('Contact person', s.contact_person, 4)}
            ${zmcInput('Contact email', s.contact_email, 4)}
            ${zmcInput('Contact phone', s.contact_phone, 4)}
          </div>
          `
        );

        const directors = Array.isArray(data.directors) ? data.directors : [];
        const managers  = Array.isArray(data.managers) ? data.managers : [];

        let dRows = directors.length ? '' : `<tr><td colspan="5" class="text-muted text-center">—</td></tr>`;
        directors.forEach(d => {
          dRows += `
            <tr>
              <td>${zmcFmt(d.full_name || d.name || (d.first_name ? (d.first_name + ' ' + (d.surname||'')) : ''))}</td>
              <td>${zmcFmt(d.id_passport || d.id || d.id_number)}</td>
              <td>${zmcFmt(d.nationality)}</td>
              <td>${zmcFmt(d.role || d.occupation)}</td>
              <td>${zmcFmt(d.shareholding || d.shares)}</td>
            </tr>
          `;
        });

        html += zmcBlock(
          `<i class="fa-solid fa-people-group"></i> Directors`,
          `
          <div class="table-responsive">
            <table class="table table-sm align-middle zmc-table-lite">
              <thead>
                <tr>
                  <th>Full name</th><th>ID / Passport</th><th>Nationality</th><th>Role</th><th>Shareholding</th>
                </tr>
              </thead>
              <tbody>${dRows}</tbody>
            </table>
          </div>
          `
        );

        let mRows = managers.length ? '' : `<tr><td colspan="4" class="text-muted text-center">—</td></tr>`;
        managers.forEach(m => {
          mRows += `
            <tr>
              <td>${zmcFmt(m.full_name || m.name)}</td>
              <td>${zmcFmt(m.position)}</td>
              <td>${zmcFmt(m.qualification)}</td>
              <td>${zmcFmt(m.experience)}</td>
            </tr>
          `;
        });

        html += zmcBlock(
          `<i class="fa-solid fa-user-gear"></i> Managers`,
          `
          <div class="table-responsive">
            <table class="table table-sm align-middle zmc-table-lite">
              <thead>
                <tr>
                  <th>Full name</th><th>Position</th><th>Qualification</th><th>Experience</th>
                </tr>
              </thead>
              <tbody>${mRows}</tbody>
            </table>
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
  });
</script>
@endpush
