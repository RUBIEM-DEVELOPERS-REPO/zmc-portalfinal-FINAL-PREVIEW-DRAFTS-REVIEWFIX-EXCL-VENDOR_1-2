<?php $__env->startSection('title', 'Security Oversight'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Security & Data Integrity Oversight</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Review failed logins, password/OTP events, and privilege changes. Read-only.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="<?php echo e(route('staff.auditor.security.csv', request()->query())); ?>">
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
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-md-6">
        <label class="form-label small text-muted">Search</label>
        <input class="form-control form-control-sm" name="q" value="<?php echo e($q); ?>" placeholder="login_failed / otp / password_reset / role_change / ip">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-search-line me-1"></i>Apply</button>
        <a class="btn btn-white border btn-sm" href="<?php echo e(route('staff.auditor.security')); ?>">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-shield-line me-2" style="color:var(--zmc-accent)"></i>Security-related logs</h6>
      <div class="small text-muted">Showing <?php echo e($logs->count()); ?> of <?php echo e($logs->total()); ?></div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width:170px;">Time</th>
            <th>Action</th>
            <th style="width:140px;">Actor</th>
            <th style="width:120px;">IP</th>
            <th>User Agent</th>
            <th>Meta</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="small text-muted"><?php echo e(optional($l->created_at)->format('d M Y H:i')); ?></td>
              <td class="fw-bold"><?php echo e($l->action); ?></td>
              <td class="small"><?php echo e($l->actor_user_id ?? '—'); ?></td>
              <td class="small"><?php echo e($l->ip ?? '—'); ?></td>
              <td class="small" style="max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo e($l->user_agent ?? '—'); ?></td>
              <td class="small text-muted" style="max-width:360px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo e($l->meta ? json_encode($l->meta) : '—'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="text-center py-5 text-muted">No security logs found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3"><?php echo e($logs->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/staff/auditor/security.blade.php ENDPATH**/ ?>