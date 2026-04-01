@extends('layouts.portal')
@section('title', 'Incoming Queue')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
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
                                     {{-- Reviewed Toggle --}}
                                     <form method="POST" action="{{ route('staff.registrar.applications.toggle-reviewed', $app) }}">
                                         @csrf
                                         <button type="submit"
                                             class="btn btn-sm {{ $app->registrar_reviewed_at ? 'btn-success' : 'btn-outline-secondary' }} rounded-circle p-2"
                                             data-bs-toggle="tooltip"
                                             title="{{ $app->registrar_reviewed_at ? 'Reviewed ✓ (Click to unmark)' : 'Mark as Reviewed' }}"
                                             style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                             <i class="fa-solid fa-check-double"></i>
                                         </button>
                                     </form>

                                     {{-- View Details --}}
                                     <button type="button"
                                         class="btn btn-sm btn-outline-primary rounded-circle p-2 js-view-more"
                                         data-app-id="{{ $app->id }}"
                                         data-bs-toggle="tooltip" title="View Details"
                                         style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                         <i class="fa-regular fa-eye"></i>
                                     </button>

                                     {{-- Flag Anomaly --}}
                                     <button type="button"
                                         class="btn btn-sm {{ $app->is_flagged ? 'btn-danger' : 'btn-outline-danger' }} rounded-circle p-2 js-open-modal"
                                         data-target="#flagAnomalyModal{{ $app->id }}"
                                         data-bs-toggle="tooltip" title="{{ $app->is_flagged ? 'Flagged' : 'Flag Anomaly' }}"
                                         style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                         <i class="fa-solid fa-flag"></i>
                                     </button>

                                     {{-- Message Officer --}}
                                     <button type="button"
                                         class="btn btn-sm btn-outline-info rounded-circle p-2 js-open-modal"
                                         data-target="#msgOfficerModal{{ $app->id }}"
                                         data-bs-toggle="tooltip" title="Message Accreditation Officer"
                                         style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                         <i class="fa-regular fa-comment-dots"></i>
                                     </button>

                                     {{-- Reassign Officer --}}
                                     <button type="button"
                                         class="btn btn-sm btn-outline-warning rounded-circle p-2 js-open-modal"
                                         data-target="#reassignOfficerModal{{ $app->id }}"
                                         data-bs-toggle="tooltip" title="Reassign to Officer"
                                         style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                         <i class="fa-solid fa-user-gear"></i>
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

     {{-- Supervisory Modals for each application --}}
     @foreach($applications as $app)
         @php $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT)); @endphp

         {{-- Flag Anomaly Modal --}}
         <div class="modal fade" id="flagAnomalyModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                 <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.flag-anomaly', $app) }}">
                     @csrf
                     <div class="modal-header bg-danger text-white">
                         <h5 class="modal-title"><i class="fa-solid fa-flag me-2"></i> Flag Anomaly — {{ $ref }}</h5>
                         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                     </div>
                     <div class="modal-body">
                         <label class="form-label small fw-bold">Nature of Anomaly / Concern <span class="text-danger">*</span></label>
                         <textarea name="flag_notes" class="form-control" rows="4" required
                             placeholder="Describe the issue clearly…">{{ $app->flag_notes }}</textarea>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-danger"><i class="fa-solid fa-flag me-1"></i>Submit Flag</button>
                     </div>
                 </form>
             </div>
         </div>

         {{-- Message Officer Modal --}}
         <div class="modal fade" id="msgOfficerModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                 <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.message-officer', $app) }}">
                     @csrf
                     <div class="modal-header bg-info text-white">
                         <h5 class="modal-title"><i class="fa-regular fa-comment-dots me-2"></i> Message Officer — {{ $ref }}</h5>
                         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                     </div>
                     <div class="modal-body">
                         <label class="form-label small fw-bold">Message / Guidance <span class="text-danger">*</span></label>
                         <textarea name="message" class="form-control" rows="4" required
                             placeholder="Type your guidance or note for the officer…"></textarea>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-info text-white"><i class="fa-solid fa-paper-plane me-1"></i>Send Message</button>
                     </div>
                 </form>
             </div>
         </div>

         {{-- Reassign Officer Modal --}}
         <div class="modal fade" id="reassignOfficerModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                 <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.reassign-category', $app) }}">
                     @csrf
                     <div class="modal-header">
                         <h5 class="modal-title"><i class="fa-solid fa-user-gear me-2"></i> Reassign to Officer — {{ $ref }}</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                     </div>
                     <div class="modal-body">
                         <div class="mb-3">
                             <label class="form-label small fw-bold">Select Accreditation Officer <span class="text-danger">*</span></label>
                             <select name="officer_id" class="form-select" required>
                                 <option value="">-- Select Officer --</option>
                                 @foreach($officers as $off)
                                     <option value="{{ $off->id }}" {{ $app->assigned_officer_id == $off->id ? 'selected' : '' }}>
                                         {{ $off->name }}{{ $off->region ? ' ('.$off->region.')' : '' }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                         <div class="mb-3">
                             <label class="form-label small fw-bold">Reason for Reassignment</label>
                             <textarea name="reason" class="form-control" rows="3" placeholder="Why is this being reassigned?"></textarea>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-warning"><i class="fa-solid fa-arrows-rotate me-1"></i>Reassign</button>
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
        html += zmcBlock(`<i class="fa-regular fa-id-card"></i> Media Practitioner details`, body);
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
