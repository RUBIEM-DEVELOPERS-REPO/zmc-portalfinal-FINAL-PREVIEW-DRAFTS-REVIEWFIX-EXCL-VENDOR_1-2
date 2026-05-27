<?php $__env->startSection('title', 'User Logins'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">User Logins</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-login-box-line me-1"></i>Login events with user name and recent activity actions.</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-success btn-sm" href="<?php echo e(route('staff.auditor.logins.csv', request()->query())); ?>">
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
      <div class="col-md-3">
        <label class="form-label small text-muted">Search</label>
        <input class="form-control form-control-sm" name="q" value="<?php echo e(request('q')); ?>" placeholder="Name, email, IP, action">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">From</label>
        <input type="date" class="form-control form-control-sm" name="date_from" value="<?php echo e(request('date_from', request('from'))); ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">To</label>
        <input type="date" class="form-control form-control-sm" name="date_to" value="<?php echo e(request('date_to', request('to'))); ?>">
      </div>
      <div class="col-md-5 d-flex gap-2">
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-filter-3-line me-1"></i>Filter</button>
        <a class="btn btn-white border btn-sm" href="<?php echo e(route('staff.auditor.logins')); ?>">Reset</a>
      </div>
    </form>
  </div>

  <div class="zmc-card shadow-sm border-0 p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-history-line me-2" style="color:var(--zmc-accent)"></i>Login Events</h6>
      <div class="small text-muted">Read-only</div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr class="text-muted small">
            <th style="width:170px;">Time</th>
            <th style="width:220px;">User</th>
            <th style="width:220px;">Action</th>
            <th style="width:160px;">IP</th>
            <th>Recent user activity</th>
          </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $user = $log->actor;
            $userLabel = $user ? ($user->name . ' (' . $user->email . ')') : ('User #' . (int)($log->actor_user_id ?? 0));
            $recent = $recentByUser[(int)($log->actor_user_id ?? 0)] ?? [];
          ?>
          <tr>
            <td class="small text-muted"><?php echo e(optional($log->created_at)->format('d M Y H:i')); ?></td>
            <td class="small fw-bold"><?php echo e($userLabel); ?></td>
            <td class="small"><?php echo e(str_replace('_',' ', (string)$log->action)); ?></td>
            <td class="small text-muted"><?php echo e($log->ip ?? '—'); ?></td>
            <td class="small">
              <?php if(!empty($recent)): ?>
                <ul class="mb-0 ps-3">
                  <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="text-muted" style="margin-bottom:2px;">
                      <?php echo e(optional($r->created_at)->format('d M H:i')); ?> - <?php echo e(str_replace('_',' ', (string)$r->action)); ?>

                    </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
              <?php else: ?>
                <span class="text-muted">No recent actions found.</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="text-muted small">No login events found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="p-3">
      <?php echo e($logs->links()); ?>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/staff/auditor/logins.blade.php ENDPATH**/ ?>