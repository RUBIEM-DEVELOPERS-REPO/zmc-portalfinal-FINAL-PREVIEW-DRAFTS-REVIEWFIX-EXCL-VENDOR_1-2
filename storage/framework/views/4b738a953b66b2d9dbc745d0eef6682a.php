<?php $__env->startSection('title', 'Application Audits'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Application & Accreditation Audits</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Verify decisions, consistency and workflow compliance. Flag irregularities for follow-up.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="<?php echo e(route('staff.auditor.applications.csv', request()->query())); ?>">
        <i class="ri-file-excel-line me-1"></i>Export CSV
      </a>
      <button class="btn btn-outline-dark btn-sm" onclick="window.print()">
        <i class="ri-printer-line me-1"></i>Print PDF
      </button>
      <a class="btn btn-white border btn-sm" href="<?php echo e(route('staff.auditor.dashboard')); ?>">
        <i class="ri-arrow-left-line me-1"></i>Back
      </a>
    </div>
  </div>

  <div class="zmc-card shadow-sm border-0 p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-2">
        <label class="form-label small text-muted">Application Type</label>
        <select class="form-select form-select-sm" name="application_type">
          <option value="">All</option>
          <?php $__currentLoopData = \App\Models\Application::bucketLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php if(request('application_type')===$k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Status</label>
        <input class="form-control form-control-sm" name="status" value="<?php echo e(request('status')); ?>" placeholder="e.g. officer_review">
      </div>

      <div class="col-md-2">
        <label class="form-label small text-muted">From</label>
        <input type="date" class="form-control form-control-sm" name="date_from" value="<?php echo e(request('date_from', request('from'))); ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">To</label>
        <input type="date" class="form-control form-control-sm" name="date_to" value="<?php echo e(request('date_to', request('to'))); ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label small text-muted">Search</label>
        <input class="form-control form-control-sm" name="search" value="<?php echo e(request('search')); ?>" placeholder="Ref / applicant / email / PayNow ref">
      </div>
      <div class="col-md-8 d-flex gap-2">
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-filter-3-line me-1"></i>Filter</button>
        <a class="btn btn-white border btn-sm" href="<?php echo e(route('staff.auditor.applications')); ?>">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card shadow-sm border-0 p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-file-search-line me-2" style="color:var(--zmc-accent)"></i>Applications</h6>
      <div class="small text-muted">Showing <?php echo e($applications->count()); ?> of <?php echo e($applications->total()); ?></div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Applicant</th>
            <th style="font-size: 0.75rem;">Type</th>
            <th style="font-size: 0.75rem;">Request</th>
            <th style="font-size: 0.75rem;">Scope</th>
            <th>Status</th>
            <th>Payment</th>
            <th style="width:240px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
              $hasPayment = !empty($a->paynow_confirmed_at) || ($a->proof_status === 'approved') || ($a->waiver_status === 'approved');
              $isApprovedStage = in_array($a->status, [\App\Models\Application::PAID_CONFIRMED, \App\Models\Application::PRODUCTION_QUEUE, \App\Models\Application::CARD_GENERATED, \App\Models\Application::CERT_GENERATED, \App\Models\Application::PRINTED, \App\Models\Application::ISSUED], true);
              $irregular = $isApprovedStage && !$hasPayment;
            ?>
            <tr <?php if($irregular): ?> style="background:#fff7ed;" <?php endif; ?>>
              <td class="fw-bold"><?php echo e($a->reference); ?></td>
              <td>
                <div class="fw-semibold"><?php echo e($a->applicant->name ?? '—'); ?></div>
                <div class="small text-muted"><?php echo e($a->applicant->email ?? '—'); ?></div>
              </td>
              <td style="font-size: 0.75rem;"><?php echo e($a->application_type); ?></td>
              <td style="font-size: 0.75rem;"><?php echo e($a->request_type); ?></td>
              <td style="font-size: 0.75rem;"><?php echo e($a->journalist_scope ?? '—'); ?></td>
              <td class="small fw-bold"><?php echo e($a->status); ?></td>
              <td class="small">
                <?php if(!empty($a->paynow_confirmed_at)): ?>
                  <span class="badge bg-success">PayNow</span>
                <?php elseif($a->proof_status==='approved'): ?>
                  <span class="badge bg-success">Proof</span>
                <?php elseif($a->waiver_status==='approved'): ?>
                  <span class="badge bg-success">Waiver</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Unverified</span>
                <?php endif; ?>
                <?php if($irregular): ?>
                  <div class="small" style="color:#dc2626;">Irregular approved stage</div>
                <?php endif; ?>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <button type="button" class="btn btn-sm btn-outline-primary js-view-more" data-app-id="<?php echo e($a->id); ?>" title="View Details">
                    <i class="ri-eye-line"></i>
                  </button>
                  <form method="POST" action="<?php echo e(route('staff.auditor.flag')); ?>" class="d-flex gap-2">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="entity_type" value="application">
                  <input type="hidden" name="entity_id" value="<?php echo e($a->id); ?>">
                  <select name="severity" class="form-select form-select-sm" style="max-width:120px;">
                    <option value="medium">MED</option>
                    <option value="low">LOW</option>
                    <option value="high">HIGH</option>
                  </select>
                  <input class="form-control form-control-sm" name="reason" placeholder="Flag reason" required>
                  <button class="btn btn-sm btn-outline-danger" type="submit"><i class="ri-flag-2-line"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center py-5 text-muted">No applications found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3"><?php echo e($applications->links()); ?></div>
</div>


<div class="modal fade" id="appDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark text-white border-0 py-3">
        <div>
          <h5 class="modal-title fw-bold m-0" id="appDetailsModalLabel">
            <i class="ri-file-list-3-line me-2"></i> Application Audit Review
          </h5>
          <div class="small opacity-75 mt-1" id="mdl_meta">—</div>
        </div>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4" style="background: #f8fafc;">
        <div id="mdl_loading" class="text-center py-5">
          <div class="spinner-border text-primary"></div>
          <div class="text-muted mt-2">Fetching application data...</div>
        </div>

        <div id="mdl_error" class="alert alert-danger d-none"></div>

        <div id="mdl_content_area" class="d-none">
          
        </div>
      </div>

      <div class="modal-footer bg-light border-0 py-3">
        <button type="button" class="btn btn-dark fw-bold px-4" data-bs-dismiss="modal">Close Audit View</button>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  const ZMC_DETAILS_URL = <?php echo json_encode(route('staff.applications.details', ['application' => '__ID__']), 512) ?>;

  function zmcFmt(v){ return (v === null || v === undefined) ? '—' : String(v).trim() || '—'; }

  function zmcBlock(titleHtml, bodyHtml){
    return `
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-3 border-bottom-0">
          <div style="font-size: 0.9rem; color: #1e293b; border-left: 3px solid #3b82f6; padding-left: 10px;">
            ${titleHtml}
          </div>
        </div>
        <div class="card-body pt-0">${bodyHtml}</div>
      </div>
    `;
  }

  function zmcField(label, value, col = 4){
    return `
      <div class="col-md-${col} mb-3">
        <label class="form-label small fw-bold text-muted mb-1 d-block text-uppercase" style="font-size: 0.65rem;">${label}</label>
        <div class="p-2 bg-light border-0 rounded small text-dark">${zmcFmt(value)}</div>
      </div>
    `;
  }

  async function loadApplicationDetails(appId) {
    const loader = document.getElementById('mdl_loading');
    const area   = document.getElementById('mdl_content_area');
    const meta   = document.getElementById('mdl_meta');
    const errBox = document.getElementById('mdl_error');

    loader.classList.remove('d-none');
    area.classList.add('d-none'); area.innerHTML = '';
    meta.textContent = '—';
    if(errBox) { errBox.classList.add('d-none'); errBox.textContent = ''; }

    try {
      const url = ZMC_DETAILS_URL.replace('__ID__', appId);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Failed to load details');

      const app = data.application || {};
      meta.innerHTML = `
        <span class="badge bg-white text-dark me-2">${zmcFmt(app.application_type).toUpperCase()}</span>
        REF: <span class="fw-bold">${zmcFmt(app.reference)}</span> •
        STATUS: <span class="fw-bold text-uppercase">${zmcFmt(app.status)}</span>
      `;

      let html = '';
      
      // Applicant Box
      let applicantHtml = `
        <div class="row g-2">
          ${zmcField('Title', app.title)}
          ${zmcField('First Name', app.first_name)}
          ${zmcField('Surname', app.surname)}
          ${zmcField('Gender', app.sex)}
          ${zmcField('Nationality', app.nationality)}
          ${zmcField('ID/Passport', app.id_passport_number)}
          ${zmcField('Phone', app.phone)}
          ${zmcField('Email', app.email)}
          ${zmcField('Employer', app.employer_name)}
        </div>
      `;
      html += zmcBlock('Media Practitioner Information', applicantHtml);

      // Previous Applications history
      const prevApps = Array.isArray(data.previous_applications) ? data.previous_applications : [];
      if (prevApps.length > 0) {
        let rows = prevApps.map(pa => `
          <tr>
            <td class="small fw-bold">${zmcFmt(pa.reference)}</td>
            <td class="small text-capitalize">${zmcFmt(pa.type)}</td>
            <td class="small"><span class="badge bg-light text-dark border">${zmcFmt(pa.status)}</span></td>
            <td class="small text-muted">${zmcFmt(pa.date)}</td>
          </tr>
        `).join('');
        html += zmcBlock('Application History', `
          <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Ref</th><th>Type</th><th>Status</th><th>Date</th></tr></thead><tbody>${rows}</tbody></table></div>
        `);
      }

      // Payment history
      const prevPays = Array.isArray(data.previous_payments) ? data.previous_payments : [];
      if (prevPays.length > 0) {
        let payRows = prevPays.map(p => `
          <tr>
            <td class="small fw-bold">${zmcFmt(p.reference)}</td>
            <td class="small">${zmcFmt(p.amount)} ${zmcFmt(p.currency)}</td>
            <td class="small text-capitalize">${zmcFmt(p.method)}</td>
            <td class="small"><span class="badge bg-light text-dark border text-capitalize">${zmcFmt(p.status)}</span></td>
            <td class="small text-muted">${zmcFmt(p.date)}</td>
          </tr>
        `).join('');
        html += zmcBlock('Payment History', `
          <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Ref</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead><tbody>${payRows}</tbody></table></div>
        `);
      }

      // Documents
      const docs = Array.isArray(data.documents) ? data.documents : [];
      if (docs.length > 0) {
        let dRows = docs.map(d => `
          <tr>
            <td class="small fw-semibold">${zmcFmt(d.document_type || d.doc_type)}</td>
            <td class="small text-truncate" style="max-width:200px;">${zmcFmt(d.original_name)}</td>
            <td class="text-end"><a href="${d.url}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 small">View</a></td>
          </tr>
        `).join('');
        html += zmcBlock('Uploaded Documents', `
          <div class="table-responsive"><table class="table table-sm align-middle mb-0"><tbody>${dRows}</tbody></table></div>
        `);
      }

      area.innerHTML = html;
      area.classList.remove('d-none');
    } catch (e) {
      if(errBox) { errBox.textContent = e.message; errBox.classList.remove('d-none'); }
    } finally {
      loader.classList.add('d-none');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', e => {
      const btn = e.target.closest('.js-view-more');
      if (!btn) return;
      const appId = btn.dataset.appId;
      const modal = new bootstrap.Modal(document.getElementById('appDetailsModal'));
      modal.show();
      loadApplicationDetails(appId);
    });
  });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/staff/auditor/applications.blade.php ENDPATH**/ ?>