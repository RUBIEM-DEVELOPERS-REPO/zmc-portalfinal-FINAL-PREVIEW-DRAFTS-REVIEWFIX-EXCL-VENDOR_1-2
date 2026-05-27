<?php $__env->startSection('title', $title ?? 'All Applications'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;"><?php echo e($title ?? 'All Applications'); ?></h4>
      <div class="text-muted mt-1" style="font-size:13px;"><i class="ri-information-line me-1"></i>Manage and filter all submitted applications.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo e(route('staff.officer.dashboard')); ?>" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div><?php echo e(session('success')); ?></div>
    </div>
  <?php endif; ?>

  
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0"><i class="ri-filter-3-line me-2"></i>Filters</h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
          <i class="ri-refresh-line me-1"></i>Clear All
        </button>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" action="<?php echo e(request()->url()); ?>" id="filterForm">
        <div class="row g-3">
          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Applicant Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo e(request('name')); ?>" placeholder="Search by name...">
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Application Type</label>
            <select name="application_type" class="form-select">
              <option value="">All Types</option>
              <option value="accreditation" <?php echo e(request('application_type') == 'accreditation' ? 'selected' : ''); ?>>Accreditation</option>
              <option value="registration" <?php echo e(request('application_type') == 'registration' ? 'selected' : ''); ?>>Registration</option>
            </select>
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Request Type</label>
            <select name="request_type" class="form-select">
              <option value="">All Request Types</option>
              <option value="new" <?php echo e(request('request_type') == 'new' ? 'selected' : ''); ?>>New</option>
              <option value="renewal" <?php echo e(request('request_type') == 'renewal' ? 'selected' : ''); ?>>Renewal</option>
              <option value="replacement" <?php echo e(request('request_type') == 'replacement' ? 'selected' : ''); ?>>Replacement</option>
            </select>
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Local/Foreign</label>
            <select name="local_foreign" class="form-select">
              <option value="">All</option>
              <option value="local" <?php echo e(request('local_foreign') == 'local' ? 'selected' : ''); ?>>Local</option>
              <option value="foreign" <?php echo e(request('local_foreign') == 'foreign' ? 'selected' : ''); ?>>Foreign</option>
            </select>
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?php echo e(request('email')); ?>" placeholder="Search by email...">
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Reference Number</label>
            <input type="text" name="reference" class="form-control" value="<?php echo e(request('reference')); ?>" placeholder="Search by reference...">
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Date</label>
            <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Month</label>
            <select name="month" class="form-select">
              <option value="">All Months</option>
              <?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>>
                  <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                </option>
              <?php endfor; ?>
            </select>
          </div>

          
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Year</label>
            <select name="year" class="form-select">
              <option value="">All Years</option>
              <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?php echo e($y); ?>" <?php echo e(request('year') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
              <?php endfor; ?>
            </select>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-sm">
              <i class="ri-search-line me-1"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  
  <?php if(($list ?? '') !== 'new' && ($list ?? '') !== 'pending'): ?>
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="fw-bold m-0"><i class="ri-download-2-line me-2"></i>Export Data</h6>
          <small class="text-muted">Export filtered results (database records only)</small>
        </div>
        <div class="d-flex gap-2">
          <a href="<?php echo e(route('staff.officer.export.csv')); ?>?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-sm btn-outline-success">
            <i class="ri-file-excel-line me-1"></i>Export CSV
          </a>
          <a href="<?php echo e(route('staff.officer.export.pdf')); ?>?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-sm btn-outline-danger">
            <i class="ri-file-pdf-line me-1"></i>Export PDF
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th><i class="ri-hashtag me-1"></i>Reference</th>
              <th><i class="ri-user-line me-1"></i>Applicant Details</th>
              <th><i class="ri-file-list-line me-1"></i>Type</th>
              <th><i class="ri-calendar-line me-1"></i>Submission Date & Time</th>
              <th class="text-end"><i class="ri-settings-3-line me-1"></i>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="fw-semibold"><?php echo e($app->reference ?? ('#'.$app->id)); ?></td>
              <td>
                <div class="fw-semibold"><?php echo e($app->applicant->name ?? '—'); ?></div>
                <?php if($app->applicant && $app->applicant->email): ?>
                  <div class="small text-muted"><?php echo e($app->applicant->email); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-semibold"><?php echo e($app->applicationTypeLabel()); ?></div>
                <?php if($app->request_type): ?>
                  <div class="small text-muted"><?php echo e(ucfirst($app->request_type)); ?></div>
                <?php endif; ?>
                <?php if($app->application_type === 'registration' && isset($app->form_data['ownership_type'])): ?>
                  <div class="small text-muted"><?php echo e(ucfirst($app->form_data['ownership_type'])); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <div><?php echo e(optional($app->submitted_at)->format('M d, Y') ?? optional($app->created_at)->format('M d, Y')); ?></div>
                <div class="small text-muted"><?php echo e(optional($app->submitted_at)->format('H:i') ?? optional($app->created_at)->format('H:i')); ?></div>
              </td>
              <td class="text-end">
                <div class="zmc-action-strip justify-content-end">
                  
                  <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-warning" 
                          onclick="seekGuidance(<?php echo e($app->id); ?>)" 
                          title="Seek Guidance from Registrar">
                    <i class="ri-question-line"></i>
                  </button>
                  
                  
                  <a href="<?php echo e(route('staff.officer.applications.show', $app)); ?>" class="btn btn-sm zmc-icon-btn btn-outline-primary" title="View application">
                    <i class="fa-regular fa-eye"></i>
                  </a>
                  
                  
                  <?php if(in_array($app->status, [\App\Models\Application::SUBMITTED, \App\Models\Application::SUBMITTED_WITH_APP_FEE, \App\Models\Application::OFFICER_REVIEW])): ?>
                  <a href="<?php echo e(route('staff.officer.applications.show', $app)); ?>#correction" class="btn btn-sm zmc-icon-btn btn-outline-dark" title="Request correction">
                    <i class="fa-regular fa-comment-dots"></i>
                  </a>
                  <?php endif; ?>
                  
                  
                  <?php if(in_array($app->status, [\App\Models\Application::SUBMITTED, \App\Models\Application::SUBMITTED_WITH_APP_FEE, \App\Models\Application::OFFICER_REVIEW])): ?>
                  <a href="<?php echo e(route('staff.officer.applications.show', $app)); ?>#approve" class="btn btn-sm zmc-icon-btn btn-outline-success" title="Approve">
                    <i class="fa-solid fa-check"></i>
                  </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No applications found.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if(method_exists($applications, 'links')): ?>
        <div class="mt-3"><?php echo e($applications->links()); ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>


<div class="modal fade" id="guidanceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="<?php echo e(route('staff.officer.seek-guidance')); ?>" id="guidanceForm">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="application_id" id="guidanceApplicationId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="ri-question-line me-2 text-warning"></i>
            Seek Guidance from Registrar
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            Use this when an application is complicated and needs the Registrar's perusal and approval.
          </div>
          <label class="form-label">Reason for seeking guidance *</label>
          <textarea name="guidance_reason" class="form-control" rows="4" required 
                    placeholder="Please explain why this application needs Registrar's guidance..."></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-send-plane-line me-1"></i>Send to Registrar
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function clearFilters() {
  document.getElementById('filterForm').reset();
  window.location.href = '<?php echo e(request()->url()); ?>';
}

function seekGuidance(applicationId) {
  document.getElementById('guidanceApplicationId').value = applicationId;
  var modal = new bootstrap.Modal(document.getElementById('guidanceModal'));
  modal.show();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/staff/officer/applications_list.blade.php ENDPATH**/ ?>