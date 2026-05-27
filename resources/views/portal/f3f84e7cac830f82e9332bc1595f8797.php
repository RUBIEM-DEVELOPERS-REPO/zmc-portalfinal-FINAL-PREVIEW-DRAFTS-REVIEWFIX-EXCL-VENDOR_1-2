<?php $__env->startSection('title', 'Card/Certificate Templates'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        <i class="ri-layout-masonry-line me-2" style="color:var(--zmc-accent)"></i>Card &amp; Certificate Templates
      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Manage saved templates for card and certificate production.
      </div>
    </div>
    <a href="<?php echo e(route('staff.production.designer')); ?>" class="btn btn-primary btn-sm px-3 fw-bold">
      <i class="ri-add-line me-1"></i> New Template
    </a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div><?php echo e(session('success')); ?></div>
    </div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="alert alert-danger d-flex align-items-start gap-2">
      <i class="ri-error-warning-line" style="font-size:18px;line-height:1;"></i>
      <div><?php echo e(session('error')); ?></div>
    </div>
  <?php endif; ?>

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th style="width:50px;">#</th>
            <th>Name</th>
            <th>Type</th>
            <th>Year</th>
            <th>Background</th>
            <th>Fields</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Created</th>
            <th class="text-end" style="min-width:180px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $fields = is_array($tpl->layout_config) ? count($tpl->layout_config) : 0;
          ?>
          <tr>
            <td class="text-muted small"><?php echo e($i + 1); ?></td>
            <td class="fw-bold"><?php echo e($tpl->name); ?></td>
            <td>
              <span class="badge bg-<?php echo e($tpl->type === 'card' ? 'info' : 'warning'); ?> text-dark">
                <?php echo e(ucfirst($tpl->type)); ?>

              </span>
            </td>
            <td><?php echo e($tpl->year); ?></td>
            <td>
              <?php if($tpl->background_path): ?>
                <span class="text-success"><i class="ri-image-line"></i> Yes</span>
              <?php else: ?>
                <span class="text-muted">None</span>
              <?php endif; ?>
            </td>
            <td><?php echo e($fields); ?> field(s)</td>
            <td>
              <?php if($tpl->is_active): ?>
                <span class="badge bg-success">Active</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactive</span>
              <?php endif; ?>
            </td>
            <td><?php echo e($tpl->creator->name ?? '—'); ?></td>
            <td class="small text-muted"><?php echo e($tpl->created_at->format('d M Y')); ?></td>
            <td class="text-end">
              <div class="d-flex justify-content-end gap-1">
                <a href="<?php echo e(route('staff.production.designer', ['template' => $tpl->id])); ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit template">
                  <i class="ri-pencil-line"></i>
                </a>

                <form method="POST" action="<?php echo e(route('staff.production.templates.activate', $tpl)); ?>" class="d-inline">
                  <?php echo csrf_field(); ?>
                  <?php if($tpl->is_active): ?>
                    <button type="submit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Deactivate">
                      <i class="ri-toggle-line"></i>
                    </button>
                  <?php else: ?>
                    <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Activate">
                      <i class="ri-toggle-fill"></i>
                    </button>
                  <?php endif; ?>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="10" class="text-center py-5 text-muted">
              No templates found. <a href="<?php echo e(route('staff.production.designer')); ?>">Create one now</a>.
            </td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/staff/production/templates.blade.php ENDPATH**/ ?>