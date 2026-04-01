<?php $__env->startSection('title', 'Auditor Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">Auditor Dashboard</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Read-only oversight across applications, payments, logs, and reports. Auditors can <b>flag anomalies</b> but cannot approve/reject.
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo e(url()->current()); ?>" class="btn btn-white border shadow-sm btn-sm px-3" title="Refresh">
        <i class="ri-refresh-line me-1"></i> Refresh
      </a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Total Applications</div>
        <div class="fw-bold" style="font-size: var(--font-size-3xl);"><?php echo e(number_format($totalApplications)); ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Approved (all stages)</div>
        <div class="fw-bold" style="font-size: var(--font-size-3xl);"><?php echo e(number_format($approvedCount)); ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Rejected</div>
        <div class="fw-bold" style="font-size: var(--font-size-3xl);"><?php echo e(number_format($rejectedCount)); ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Irregular: Approved w/o Pay/Proof/Waiver</div>
        <div class="fw-bold" style="font-size: var(--font-size-3xl); color:#dc2626;"><?php echo e(number_format($irregularApprovedWithoutPayment)); ?></div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">PayNow Confirmed</div>
        <div class="fw-bold" style="font-size: var(--font-size-2xl);"><?php echo e(number_format($paynowConfirmed)); ?></div>
        <div class="mt-2">
          <a class="btn btn-sm btn-white border" href="<?php echo e(route('staff.auditor.paynow')); ?>"><i class="ri-eye-line me-1"></i>View PayNow Audit</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Payment Proofs Approved</div>
        <div class="fw-bold" style="font-size: var(--font-size-2xl);"><?php echo e(number_format($proofsApproved)); ?></div>
        <div class="mt-2">
          <a class="btn btn-sm btn-white border" href="<?php echo e(route('staff.auditor.proofs')); ?>"><i class="ri-file-search-line me-1"></i>View Proofs</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Waivers Approved</div>
        <div class="fw-bold" style="font-size: var(--font-size-2xl);"><?php echo e(number_format($waiversApproved)); ?></div>
        <div class="mt-2">
          <a class="btn btn-sm btn-white border" href="<?php echo e(route('staff.auditor.waivers')); ?>"><i class="ri-file-shield-2-line me-1"></i>View Waivers</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="zmc-card shadow-sm border-0 p-0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <h6 class="fw-bold m-0"><i class="ri-briefcase-4-line me-2" style="color:var(--zmc-accent)"></i>Quick Links</h6>
        </div>
        <div class="p-3">
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-sm btn-dark" href="<?php echo e(route('staff.auditor.applications')); ?>"><i class="ri-folder-open-line me-1"></i>Application Audits</a>
            <a class="btn btn-sm btn-white border" href="<?php echo e(route('staff.auditor.logs')); ?>"><i class="ri-file-list-3-line me-1"></i>Audit Logs</a>
            <a class="btn btn-sm btn-white border" href="<?php echo e(route('staff.auditor.reports')); ?>"><i class="ri-bar-chart-2-line me-1"></i>Audit Reports</a>
            <a class="btn btn-sm btn-white border" href="<?php echo e(route('staff.auditor.security')); ?>"><i class="ri-shield-line me-1"></i>Security Oversight</a>
          </div>
          <div class="mt-3 text-muted small">
            Tip: Use <b>Flag</b> on any record to log an anomaly (read-only workflows are enforced).
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="zmc-card shadow-sm border-0 p-0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <h6 class="fw-bold m-0"><i class="ri-flag-2-line me-2" style="color:#dc2626"></i>Recent Flags</h6>
          <a class="small" href="<?php echo e(route('staff.auditor.logs')); ?>">View logs</a>
        </div>
        <div class="p-3">
          <?php if($recentFlags->isEmpty()): ?>
            <div class="text-muted">No anomalies flagged yet.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>ID</th>
                    <th>Severity</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $recentFlags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td class="small"><?php echo e($f->entity_type); ?></td>
                      <td class="small"><?php echo e($f->entity_id); ?></td>
                      <td class="small fw-bold"><?php echo e(strtoupper($f->severity)); ?></td>
                      <td class="small text-muted"><?php echo e(optional($f->created_at)->format('d M Y H:i')); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  
  <div class="zmc-card mb-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
      <div>
        <div class="fw-bold"><i class="ri-pulse-line me-1"></i> Activity Feed</div>
        <div class="small text-muted">Latest actions across the system</div>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-success btn-sm" href="<?php echo e(route('staff.auditor.activity.csv')); ?>">
          <i class="ri-file-excel-line me-1"></i>Export CSV
        </a>
        <button class="btn btn-outline-dark btn-sm" onclick="window.print()">
          <i class="ri-printer-line me-1"></i>Print PDF
        </button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr class="text-muted small">
            <th style="width:170px;">Time</th>
            <th style="width:200px;">Actor</th>
            <th style="width:220px;">Action</th>
            <th>Reference</th>
            <th style="width:200px;">From → To</th>
          </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = ($activity ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $actor = optional($log->user);
            $actorLabel = $actor?->name ? ($actor->name . ' (' . ($log->user_role ?? 'role') . ')') : ('User #' . (int)($log->user_id ?? 0));
            $ref = null;
            try { $ref = optional($log->entity)->reference; } catch (\Throwable $e) {}
            $ref = $ref ?: ($log->entity_type ? class_basename($log->entity_type) . '-' . (int)($log->entity_id ?? 0) : ('Entity-' . (int)($log->entity_id ?? 0)));
          ?>
          <tr>
            <td class="small text-muted"><?php echo e(\Carbon\Carbon::parse($log->created_at)->format('d M Y H:i')); ?></td>
            <td class="small fw-bold"><?php echo e($actorLabel); ?></td>
            <td class="small"><?php echo e(str_replace('_',' ', (string)$log->action)); ?></td>
            <td class="small"><?php echo e($ref); ?></td>
            <td class="small text-muted"><?php echo e(($log->from_status ?? '—')); ?> → <?php echo e(($log->to_status ?? '—')); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="text-muted small">No recent activity.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/staff/auditor/dashboard.blade.php ENDPATH**/ ?>