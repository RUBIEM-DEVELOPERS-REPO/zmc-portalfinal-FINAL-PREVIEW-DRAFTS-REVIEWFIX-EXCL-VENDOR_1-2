<?php $__env->startSection('title', $pageTitle ?? 'Production'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        <?php echo e($pageTitle ?? 'Production'); ?>

      </h4>
      <?php if(!empty($pageNote)): ?>
        <div class="text-muted mt-1" style="font-size:13px;">
          <i class="ri-information-line me-1"></i> <?php echo $pageNote; ?>

        </div>
      <?php endif; ?>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span class="zmc-pill zmc-pill-dark">
        <i class="ri-map-pin-user-line"></i>
        <span>Region: <?php echo e(auth()->user()->region ?? 'NOT SET'); ?></span>
      </span>
      <a href="<?php echo e(url()->current()); ?>" class="btn btn-white border shadow-sm btn-sm px-3" title="Refresh">
        <i class="ri-refresh-line me-1"></i> Refresh
      </a>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div><?php echo e(session('success')); ?></div>
    </div>
  <?php endif; ?>

  <?php
    $detailsUrlTemplate = route('staff.applications.details', ['application' => '__ID__']);
  ?>

  <div class="card shadow-sm mb-3 border-0">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Search</label>
          <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="Ref / name / email" />
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Request Type</label>
          <select class="form-select" name="request_type">
            <option value="">All</option>
            <option value="new" <?php if(request('request_type')==='new'): echo 'selected'; endif; ?>>New</option>
            <option value="renewal" <?php if(request('request_type')==='renewal'): echo 'selected'; endif; ?>>Renewal</option>
            <option value="replacement" <?php if(request('request_type')==='replacement'): echo 'selected'; endif; ?>>Replacement</option>
          </select>
        </div>
        <div class="col-12 col-md-2">
          <label class="form-label small fw-bold">Scope</label>
          <select class="form-select" name="scope">
            <option value="">Both</option>
            <option value="local" <?php if(request('scope')==='local'): echo 'selected'; endif; ?>>Local</option>
            <option value="foreign" <?php if(request('scope')==='foreign'): echo 'selected'; endif; ?>>Foreign</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small fw-bold">From</label>
          <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-control" />
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label small fw-bold">To</label>
          <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-control" />
        </div>
        <div class="col-12 col-md-3 mt-3 d-flex gap-2">
          <button class="btn btn-dark w-100"><i class="ri-filter-3-line me-1"></i>Apply</button>
          <a class="btn btn-outline-secondary w-100" href="<?php echo e(url()->current()); ?>">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h6 class="fw-bold m-0">
        <i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i>
        <?php echo e($pageTitle ?? 'Items'); ?>

      </h6>

      <?php if(($mode ?? '') === 'printing'): ?>
        <form id="batchForm" method="POST" action="<?php echo e(route('staff.production.batch.print')); ?>" class="d-flex align-items-center gap-2">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="tooltip" title="Marks selected items as printed">
            <i class="ri-printer-line me-1"></i> Batch Print (selected)
          </button>
        </form>
      <?php endif; ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <?php if(($mode ?? '') === 'printing'): ?>
              <th style="width:40px;"></th>
            <?php endif; ?>
            <th style="width:60px;">#</th>
            <th><i class="ri-hashtag me-1"></i> Ref</th>
            <th><i class="ri-user-line me-1"></i> Applicant</th>
            <th><i class="ri-map-pin-line me-1"></i> Region</th>
            <th><i class="ri-flag-line me-1"></i> Status</th>
            <th class="text-end" style="min-width:220px;">Action</th>
          </tr>
        </thead>

        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $status = strtolower((string)($app->status ?? ''));
            $badge = match($status) {
              'issued' => 'success',
              'printed' => 'secondary',
              'card_generated','certificate_generated' => 'info',
              default => 'warning',
            };

            $rowNo = method_exists($applications,'firstItem') && $applications->firstItem()
              ? ($applications->firstItem() + $i)
              : ($i + 1);

            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));

            $isAccred = ($app->application_type ?? '') === 'accreditation';
            $isReg    = ($app->application_type ?? '') === 'registration';

            $canBatch = in_array($status, ['production_queue','card_generated','certificate_generated'], true);
          ?>

          <?php
            $optName = $app->applicant?->name 
                       ?? ($app->form_data['physical_applicant_name'] ?? null) 
                       ?? ($app->form_data['media_house_name'] ?? null) 
                       ?? '—';
          ?>
          <tr>
            <?php if(($mode ?? '') === 'printing'): ?>
              <td>
                <input class="form-check-input" type="checkbox" name="application_ids[]" value="<?php echo e($app->id); ?>" form="batchForm" <?php if(!$canBatch): ?> disabled <?php endif; ?>>
              </td>
            <?php endif; ?>
            <td class="text-muted small"><?php echo e($rowNo); ?></td>
            <td class="fw-bold text-dark"><?php echo e($ref); ?></td>
            <td><?php echo e(((string)$optName !== '') ? $optName : '—'); ?></td>
            <td class="small text-uppercase"><?php echo e($app->collection_region ?? '—'); ?></td>
            <td>
              <span class="badge rounded-pill bg-<?php echo e($badge); ?> px-3">
                <?php echo e(ucwords(str_replace('_',' ', $status ?: '—'))); ?>

              </span>
            </td>

            <td class="text-end">
              <div class="zmc-action-strip">

                
                <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-primary js-view-more" data-app-id="<?php echo e($app->id); ?>" data-bs-toggle="tooltip" title="View application">
                  <i class="fa-regular fa-eye"></i>
                </button>

                
                <?php if($isAccred): ?>
                  <a href="<?php echo e(route('staff.production.applications.card.preview', $app)); ?>" target="_blank" class="btn btn-sm zmc-icon-btn btn-outline-success" data-bs-toggle="tooltip" title="Preview / edit card (then print)">
                    <i class="ri-id-card-line"></i>
                  </a>
                <?php else: ?>
                  <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-success" disabled data-bs-toggle="tooltip" title="Card applies to accreditation only">
                    <i class="ri-id-card-line"></i>
                  </button>
                <?php endif; ?>

                
                <?php if($isReg): ?>
                  <a href="<?php echo e(route('staff.production.applications.certificate.preview', $app)); ?>" target="_blank" class="btn btn-sm zmc-icon-btn btn-outline-success" data-bs-toggle="tooltip" title="Preview / edit certificate (then print)">
                    <i class="ri-award-line"></i>
                  </a>
                <?php else: ?>
                  <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-success" disabled data-bs-toggle="tooltip" title="Certificate applies to registration only">
                    <i class="ri-award-line"></i>
                  </button>
                <?php endif; ?>

                
                <form class="d-inline" method="POST" action="<?php echo e(route('staff.production.applications.print_single', $app)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm zmc-icon-btn btn-outline-dark" data-bs-toggle="tooltip" title="Mark printed" <?php if(in_array($status, ['issued'], true)): ?> disabled <?php endif; ?>>
                    <i class="ri-printer-line"></i>
                  </button>
                </form>

                
                <form class="d-inline" method="POST" action="<?php echo e(route('staff.production.applications.issue', $app)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm zmc-icon-btn btn-outline-success" data-bs-toggle="tooltip" title="Mark issued" <?php if($status === 'issued'): ?> disabled <?php endif; ?>>
                    <i class="ri-checkbox-circle-line"></i>
                  </button>
                </form>

              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="<?php echo e(($mode ?? '') === 'printing' ? 7 : 6); ?>" class="text-center py-5 text-muted">No applications found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    <?php echo e($applications->links()); ?>

  </div>
</div>


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
          
        </div>
      </div>

      <div class="modal-footer zmc-modal-footer">
        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  const ZMC_DETAILS_URL = <?php echo json_encode($detailsUrlTemplate, 15, 512) ?>;

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
        html += zmcBlock(`<i class="fa-regular fa-id-card"></i> Applicant details`, body);
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
      const btn = e.target.closest('.js-view-more');
      if(!btn) return;
      const appId = btn.getAttribute('data-app-id');
      if(!appId) return;

      zmcOpenModal('#appDetailsModal');
      loadApplicationDetails(appId);
    });
  });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/staff/production/list.blade.php ENDPATH**/ ?>