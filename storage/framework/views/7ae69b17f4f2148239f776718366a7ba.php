<?php $__env->startSection('title', 'Accounts & Payments Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">
        Accounts & Payments Dashboard
      </h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Confirm payments and pass successful items to <b>Registrar</b>.
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <form action="<?php echo e(route('staff.accounts.dashboard')); ?>" method="GET" id="yearFilterForm" class="me-2">
        <select name="year" class="form-select border shadow-sm fw-bold bg-white btn-sm" style="height: 31px;" onchange="document.getElementById('yearFilterForm').submit()">
            <?php $__currentLoopData = $availableYears ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($y); ?>" <?php echo e((isset($year) && $year == $y) ? 'selected' : ''); ?>>
                    Year: <?php echo e($y); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </form>
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
    $items = collect(method_exists($applications, 'items') ? $applications->items() : $applications);
    $summaryTotal = method_exists($applications, 'total') ? $applications->total() : $items->count();

    $summaryPending = $items->filter(fn($x) => in_array(strtolower((string)($x->status ?? '')), [
      'accounts_review','paid_confirmed','returned_to_accounts'
    ], true))->count();

    $summaryPaid = $items->filter(fn($x) => strtolower((string)($x->status ?? '')) === 'paid_confirmed')->count();
    $summaryReturned = $items->filter(fn($x) => strtolower((string)($x->status ?? '')) === 'returned_to_accounts')->count();

    $detailsUrlTemplate = route('staff.applications.details', ['application' => '__ID__']);

    $paidUrl = fn($id) => route('staff.accounts.applications.paid', $id);
    $returnUrl = fn($id) => route('staff.accounts.applications.return', $id);
  ?>

  
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-2">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Total queue</div>
            <div class="h3 fw-black mb-0"><?php echo e($summaryTotal); ?></div>
          </div>
          <div class="icon-box text-primary"><i class="ri-folders-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-2">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Pending action</div>
            <div class="h3 fw-black mb-0"><?php echo e($summaryPending); ?></div>
          </div>
          <div class="icon-box" style="background:transparent;border-left:3px solid var(--zmc-accent);border-radius:0;">
            <i class="ri-time-line" style="color:var(--zmc-accent)"></i>
          </div>
        </div>
      </div>
    </div>

    <?php if(isset($kpis) && isset($kpis['special_cases'])): ?>
    <div class="col-12 col-md-2">
      <div class="zmc-card h-100" style="border-left: 3px solid #facc15;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Special Cases</div>
            <div class="h3 fw-black mb-0" style="color: #ffffff;"><?php echo e($kpis['special_cases']); ?></div>
          </div>
          <div class="icon-box" style="background: rgba(250, 204, 21, 0.1);">
            <i class="ri-alert-line" style="color: #ffffff;"></i>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="col-12 col-md-2">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Paid confirmed</div>
            <div class="h3 fw-black mb-0"><?php echo e($summaryPaid); ?></div>
          </div>
          <div class="icon-box text-success"><i class="ri-check-double-line"></i></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-2">
      <div class="zmc-card h-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="text-muted small fw-bold">Returned</div>
            <div class="h3 fw-black mb-0"><?php echo e($summaryReturned); ?></div>
          </div>
          <div class="icon-box text-warning"><i class="ri-arrow-go-back-line"></i></div>
        </div>
      </div>
    </div>
  </div>

  
  <?php if(isset($kpis)): ?>
  <div class="zmc-card mb-3">
    <div class="card-body">
      <form method="GET" action="<?php echo e(route('staff.accounts.dashboard')); ?>" class="row g-3 align-items-end">
        <?php if(isset($year)): ?>
          <input type="hidden" name="year" value="<?php echo e($year); ?>">
        <?php endif; ?>
        <div class="col-md-3">
          <label class="form-label small fw-bold">Payment Submission Method</label>
          <select name="submission_method" class="form-select form-select-sm">
            <option value="">All Methods</option>
            <option value="paynow_reference" <?php echo e(request('submission_method') === 'paynow_reference' ? 'selected' : ''); ?>>
              PayNow (<?php echo e($kpis['paynow_submissions'] ?? 0); ?>)
            </option>
            <option value="proof_upload" <?php echo e(request('submission_method') === 'proof_upload' ? 'selected' : ''); ?>>
              Proof Upload (<?php echo e($kpis['proof_submissions'] ?? 0); ?>)
            </option>
            <option value="waiver" <?php echo e(request('submission_method') === 'waiver' ? 'selected' : ''); ?>>
              Waiver (<?php echo e($kpis['waiver_submissions'] ?? 0); ?>)
            </option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary btn-sm w-100">
            <i class="ri-filter-line me-1"></i>Filter
          </button>
        </div>
        <div class="col-md-2">
          <a href="<?php echo e(route('staff.accounts.dashboard')); ?>" class="btn btn-light border btn-sm w-100">
            <i class="ri-refresh-line me-1"></i>Reset
          </a>
        </div>
        <div class="col-md-5 text-end">
          <div class="small text-muted">
            <span class="badge bg-light text-dark border me-2">
              <i class="ri-inbox-line me-1"></i>No Submission: <?php echo e($kpis['no_submission'] ?? 0); ?>

            </span>
            <span class="badge bg-light text-dark border">
              <i class="ri-folder-line me-1"></i>Total: <?php echo e($kpis['total_pending'] ?? 0); ?>

            </span>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0">
        <i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i> Accounts queue
      </h6>
      <?php if(method_exists($applications, 'currentPage')): ?>
        <div class="small text-muted">Page <?php echo e($applications->currentPage()); ?> of <?php echo e($applications->lastPage()); ?></div>
      <?php endif; ?>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th><i class="ri-hashtag me-1"></i> Ref</th>
            <th><i class="ri-user-line me-1"></i> Applicant</th>
            <th style="font-size: 0.75rem;"><i class="ri-file-text-line me-1"></i> Type</th>
            <th style="font-size: 0.75rem;"><i class="ri-information-line me-1"></i> Request</th>
            <th style="font-size: 0.75rem;"><i class="ri-global-line me-1"></i> Scope</th>
            <th><i class="ri-money-dollar-circle-line me-1"></i> Payment Method</th>
            <th><i class="ri-map-pin-line me-1"></i> Region</th>
            <th><i class="ri-calendar-line me-1"></i> Date</th>
            <th><i class="ri-flag-line me-1"></i> Status</th>
            <th class="text-end" style="min-width:170px;">Action</th>
          </tr>
        </thead>

        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $status = strtolower((string)($app->status ?? ''));
            $badge = match($status) {
              'returned_to_accounts' => 'warning',
              'paid_confirmed' => 'success',
              default => 'info',
            };

            $canAct = in_array($status, ['accounts_review','returned_to_accounts'], true);

            $rowNo = method_exists($applications,'firstItem') && $applications->firstItem()
              ? ($applications->firstItem() + $i)
              : ($i + 1);

            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
          ?>

          <tr>
            <td class="text-muted small"><?php echo e($rowNo); ?></td>
            <td class="fw-bold text-dark"><?php echo e($ref); ?></td>
            <td><?php echo e($app->applicant?->name ?? $app->applicant_name ?? '—'); ?></td>
            <?php
              $appType = ucwords($app->application_type ?? '—');
              $reqType = ucwords($app->request_type ?? '—');
              $scope = ucwords($app->journalist_scope ?? $app->residency_type ?? 'Local');
            ?>
            <td style="font-size: 0.75rem;"><?php echo e($appType); ?></td>
            <td style="font-size: 0.75rem;"><?php echo e($reqType); ?></td>
            <td style="font-size: 0.75rem;"><?php echo e($scope); ?></td>
            <td>
              <?php
                $methodBadges = [
                  'paynow_reference' => ['icon' => 'ri-bank-card-line', 'color' => 'primary', 'label' => 'PayNow'],
                  'proof_upload' => ['icon' => 'ri-file-upload-line', 'color' => 'info', 'label' => 'Proof'],
                  'waiver' => ['icon' => 'ri-price-tag-3-line', 'color' => 'warning', 'label' => 'Waiver'],
                ];
                $method = $app->payment_submission_method;
                $badge = $methodBadges[$method] ?? ['icon' => 'ri-question-line', 'color' => 'secondary', 'label' => 'None'];
              ?>
              <span class="badge bg-<?php echo e($badge['color']); ?>-subtle text-<?php echo e($badge['color']); ?> border border-<?php echo e($badge['color']); ?>">
                <i class="<?php echo e($badge['icon']); ?> me-1"></i><?php echo e($badge['label']); ?>

              </span>
              <?php if($app->payment_submitted_at): ?>
                <div class="text-muted" style="font-size: 10px;"><?php echo e($app->payment_submitted_at->diffForHumans()); ?></div>
              <?php endif; ?>
            </td>
            <td class="text-capitalize"><?php echo e($app->collection_region ?? '—'); ?></td>
            <td class="small"><?php echo e(!empty($app->created_at) ? \Carbon\Carbon::parse($app->created_at)->format('d M Y') : '—'); ?></td>
            <td>
              <span class="badge rounded-pill bg-<?php echo e($badge); ?> px-3">
                <?php echo e(ucwords(str_replace('_',' ', $status ?: '—'))); ?>

              </span>
              <?php if($app->status === 'pending_accounts_review_from_registrar'): ?>
                <div class="mt-1">
                  <span class="badge" style="background: rgba(250, 204, 21, 0.2); color: #000; border: 1px solid #facc15; font-size: 10px;">
                    <i class="ri-alert-line me-1"></i>SPECIAL CASE
                  </span>
                </div>
              <?php endif; ?>
            </td>

            <td class="text-end">
              <div class="zmc-action-strip">

                
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-dark js-open-modal"
                  data-target="#returnModal<?php echo e($app->id); ?>"
                  <?php if(!$canAct): ?> disabled <?php endif; ?>
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Return to Officer"
                >
                  <i class="fa-regular fa-comment-dots"></i>
                </button>

                
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-primary js-view-more"
                  data-app-id="<?php echo e($app->id); ?>"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="View application"
                >
                  <i class="fa-regular fa-eye"></i>
                </button>

                
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-success js-open-modal"
                  data-target="#paidModal<?php echo e($app->id); ?>"
                  <?php if(!$canAct): ?> disabled <?php endif; ?>
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Mark paid"
                >
                  <i class="fa-solid fa-check"></i>
                </button>

              </div>
            </td>
          </tr>

          <?php $__env->startPush('zmc_modals'); ?>
            
            <div class="modal fade" id="returnModal<?php echo e($app->id); ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <form class="modal-content" method="POST" action="<?php echo e($returnUrl($app->id)); ?>">
                  <?php echo csrf_field(); ?>

                  <div class="modal-header zmc-modal-header">
                    <div>
                      <div class="zmc-modal-title">
                        <i class="fa-regular fa-message me-2" style="color:var(--zmc-accent-dark)"></i>
                        Return to Officer
                        <span class="ms-2 text-muted" style="font-weight:800;font-size: var(--font-size-sm);"><?php echo e($ref); ?></span>
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

            
            <div class="modal fade" id="paidModal<?php echo e($app->id); ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="<?php echo e($paidUrl($app->id)); ?>">
                  <?php echo csrf_field(); ?>

                  <div class="modal-header zmc-modal-header">
                    <div>
                      <div class="zmc-modal-title">
                        <i class="fa-solid fa-check me-2" style="color:var(--zmc-green)"></i>
                        Confirm payment
                        <span class="ms-2 text-muted" style="font-weight:800;font-size: var(--font-size-sm);"><?php echo e($ref); ?></span>
                      </div>
                      <div class="zmc-modal-sub">This will move the application to Registrar review.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <label class="form-label zmc-lbl">Notes (optional)</label>
                    <textarea name="notes" class="form-control zmc-input" rows="4" placeholder="Add any notes (optional)"></textarea>
                  </div>

                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">
                      <i class="fa-solid fa-check me-1"></i>Mark paid
                    </button>
                  </div>
                </form>
              </div>
            </div>
          <?php $__env->stopPush(); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">No applications found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    <?php echo e($applications->links()); ?>

  </div>

  <?php echo $__env->yieldPushContent('zmc_modals'); ?>
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
          <div class="text-muted mt-2" style="font-size: var(--font-size-sm);">Loading…</div>
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
              <td>${zmcFmt(d.full_name || (d.name ? (d.name + ' ' + (d.surname||'')) : ''))}</td>
              <td>${zmcFmt(d.id_passport)}</td>
              <td>${zmcFmt(d.nationality)}</td>
              <td>${zmcFmt(d.role || d.occupation)}</td>
              <td>${zmcFmt(d.shareholding)}</td>
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
              <td>${zmcFmt(m.full_name)}</td>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/staff/accounts/dashboard.blade.php ENDPATH**/ ?>