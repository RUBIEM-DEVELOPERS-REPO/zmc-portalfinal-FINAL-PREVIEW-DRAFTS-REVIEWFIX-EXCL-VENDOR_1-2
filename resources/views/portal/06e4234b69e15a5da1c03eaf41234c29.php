<?php $__env->startSection('title', 'Audit Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Audit Reports</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Generate time-based audit summaries with visual analytics. Export to CSV or print to PDF.
      </div>
    </div>
    <a class="btn btn-white border btn-sm" href="<?php echo e(route('staff.auditor.dashboard')); ?>"><i class="ri-arrow-left-line me-1"></i>Back</a>
  </div>

  <div class="zmc-card shadow-sm border-0 p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small text-muted fw-bold">From Date</label>
        <input type="date" class="form-control form-control-sm" name="from" value="<?php echo e(optional($from)->format('Y-m-d')); ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted fw-bold">To Date</label>
        <input type="date" class="form-control form-control-sm" name="to" value="<?php echo e(optional($to)->format('Y-m-d')); ?>">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button class="btn btn-dark btn-sm" type="submit"><i class="ri-filter-3-line me-1"></i>Generate</button>
        <a class="btn btn-white border btn-sm" href="<?php echo e(route('staff.auditor.reports')); ?>">Reset</a>
        <a class="btn btn-sm btn-success" href="<?php echo e(route('staff.auditor.reports.csv', request()->query())); ?>"><i class="ri-download-2-line me-1"></i>Export CSV</a>
        <button class="btn btn-sm btn-outline-dark" type="button" onclick="window.print()"><i class="ri-printer-line me-1"></i>Print / Save PDF</button>
      </div>
    </form>
  </div>

  <?php
    $total = $stats['total'] ?? 0;
    $approved = $stats['approved'] ?? 0;
    $rejected = $stats['rejected'] ?? 0;
    $approvalRate = $total > 0 ? round(($approved / $total) * 100, 1) : 0;
    $rejectionRate = $total > 0 ? round(($rejected / $total) * 100, 1) : 0;
  ?>

  <?php if($total === 0): ?>
    <div class="alert alert-warning border-0 rounded-4 mb-4">
      <div class="d-flex align-items-center gap-3">
        <i class="ri-information-line h3 mb-0"></i>
        <div>
          <strong>No Data Available</strong>
          <p class="mb-0 small">No applications found for the selected date range. Try adjusting the date filters or reset to view all data.</p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <div class="small mb-2 text-uppercase fw-bold" style="opacity: 0.9;">Total Applications</div>
              <div class="display-4 fw-black mb-2"><?php echo e(number_format($total)); ?></div>
              <?php if($from && $to): ?>
                <div class="small" style="opacity: 0.85;">
                  <i class="ri-calendar-line me-1"></i>
                  <?php echo e($from->format('d M Y')); ?> - <?php echo e($to->format('d M Y')); ?>

                </div>
              <?php else: ?>
                <div class="small" style="opacity: 0.85;">All time</div>
              <?php endif; ?>
            </div>
            <i class="ri-file-list-3-line" style="font-size: 52px; opacity: 0.15;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #facc15 0%, #eab308 100%); color: #000;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <div class="small mb-2 text-uppercase fw-bold" style="opacity: 0.9;">Approved</div>
              <div class="display-4 fw-black mb-2"><?php echo e(number_format($approved)); ?></div>
              <div class="small" style="opacity: 0.85;">
                <i class="ri-percent-line me-1"></i>
                <?php echo e($approvalRate); ?>% approval rate
              </div>
            </div>
            <i class="ri-checkbox-circle-line" style="font-size: 52px; opacity: 0.15;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <div class="small mb-2 text-uppercase fw-bold" style="opacity: 0.9;">Rejected</div>
              <div class="display-4 fw-black mb-2"><?php echo e(number_format($rejected)); ?></div>
              <div class="small" style="opacity: 0.85;">
                <i class="ri-percent-line me-1"></i>
                <?php echo e($rejectionRate); ?>% rejection rate
              </div>
            </div>
            <i class="ri-close-circle-line" style="font-size: 52px; opacity: 0.15;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <?php if($total > 0): ?>
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="zmc-card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-4"><i class="ri-pie-chart-line me-2 text-primary"></i>Application Status Distribution</h5>
        <div id="statusDistributionChart" style="min-height: 300px;"></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="zmc-card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-4"><i class="ri-bar-chart-line me-2 text-success"></i>Approval vs Rejection</h5>
        <div id="approvalRejectionChart" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-4 h-100">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="flex-grow-1">
            <div class="text-muted small fw-bold text-uppercase mb-2">PayNow Confirmed</div>
            <div class="fw-black" style="font-size:32px; color:#3b82f6;"><?php echo e(number_format($stats['paynow_confirmed'] ?? 0)); ?></div>
          </div>
          <div class="bg-primary bg-opacity-10 p-3 rounded-3">
            <i class="ri-bank-card-line text-primary" style="font-size: 32px;"></i>
          </div>
        </div>
        <?php
          $paynowRate = $total > 0 ? (($stats['paynow_confirmed'] ?? 0) / $total) * 100 : 0;
        ?>
        <div class="progress" style="height: 10px; border-radius: 5px;">
          <div class="progress-bar bg-primary" style="width: <?php echo e($paynowRate); ?>%"></div>
        </div>
        <div class="d-flex justify-content-between mt-2">
          <small class="text-muted"><?php echo e(round($paynowRate, 1)); ?>% of total</small>
          <?php if($total > 0): ?>
            <small class="text-muted"><?php echo e($total - ($stats['paynow_confirmed'] ?? 0)); ?> pending</small>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-4 h-100">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="flex-grow-1">
            <div class="text-muted small fw-bold text-uppercase mb-2">Proofs Approved</div>
            <div class="fw-black" style="font-size:32px; color:#ffffff;"><?php echo e(number_format($stats['proofs_approved'] ?? 0)); ?></div>
          </div>
          <div class="bg-success bg-opacity-10 p-3 rounded-3">
            <i class="ri-file-check-line text-success" style="font-size: 32px;"></i>
          </div>
        </div>
        <?php
          $proofsRate = $total > 0 ? (($stats['proofs_approved'] ?? 0) / $total) * 100 : 0;
        ?>
        <div class="progress" style="height: 10px; border-radius: 5px;">
          <div class="progress-bar bg-success" style="width: <?php echo e($proofsRate); ?>%"></div>
        </div>
        <div class="d-flex justify-content-between mt-2">
          <small class="text-muted"><?php echo e(round($proofsRate, 1)); ?>% of total</small>
          <?php if($total > 0): ?>
            <small class="text-muted"><?php echo e($total - ($stats['proofs_approved'] ?? 0)); ?> pending</small>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-4 h-100">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="flex-grow-1">
            <div class="text-muted small fw-bold text-uppercase mb-2">Waivers Approved</div>
            <div class="fw-black" style="font-size:32px; color:#f59e0b;"><?php echo e(number_format($stats['waivers_approved'] ?? 0)); ?></div>
          </div>
          <div class="bg-warning bg-opacity-10 p-3 rounded-3">
            <i class="ri-hand-coin-line text-warning" style="font-size: 32px;"></i>
          </div>
        </div>
        <?php
          $waiversRate = $total > 0 ? (($stats['waivers_approved'] ?? 0) / $total) * 100 : 0;
        ?>
        <div class="progress" style="height: 10px; border-radius: 5px;">
          <div class="progress-bar bg-warning" style="width: <?php echo e($waiversRate); ?>%"></div>
        </div>
        <div class="d-flex justify-content-between mt-2">
          <small class="text-muted"><?php echo e(round($waiversRate, 1)); ?>% of total</small>
          <?php if($total > 0): ?>
            <small class="text-muted"><?php echo e($total - ($stats['waivers_approved'] ?? 0)); ?> standard</small>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  
  <?php if(($stats['paynow_confirmed'] ?? 0) > 0 || ($stats['proofs_approved'] ?? 0) > 0 || ($stats['waivers_approved'] ?? 0) > 0): ?>
  <div class="row g-3 mb-4">
    <div class="col-md-12">
      <div class="zmc-card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-4"><i class="ri-line-chart-line me-2 text-info"></i>Payment & Processing Metrics</h5>
        <div id="paymentMetricsChart" style="min-height: 250px;"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <?php if($total > 0): ?>
  <div class="zmc-card shadow-sm border-0 p-4 mb-4">
    <h5 class="fw-bold mb-4"><i class="ri-table-line me-2 text-dark"></i>Detailed Statistics</h5>
    <div class="table-responsive">
      <table class="table table-borderless align-middle">
        <thead class="bg-light">
          <tr>
            <th class="py-3 ps-3 fw-bold">Metric</th>
            <th class="py-3 text-end fw-bold">Count</th>
            <th class="py-3 text-end pe-3 fw-bold">Percentage</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-top">
            <td class="py-3 ps-3 fw-bold"><i class="ri-file-list-line text-primary me-2"></i>Total Applications</td>
            <td class="py-3 text-end fw-bold" style="font-size: 16px;"><?php echo e(number_format($total)); ?></td>
            <td class="py-3 text-end pe-3 fw-bold">100%</td>
          </tr>
          <tr class="border-top">
            <td class="py-3 ps-3"><i class="ri-checkbox-circle-line text-success me-2"></i>Approved</td>
            <td class="py-3 text-end" style="font-size: 15px;"><?php echo e(number_format($approved)); ?></td>
            <td class="py-3 text-end pe-3"><?php echo e($approvalRate); ?>%</td>
          </tr>
          <tr>
            <td class="py-3 ps-3"><i class="ri-close-circle-line text-danger me-2"></i>Rejected</td>
            <td class="py-3 text-end" style="font-size: 15px;"><?php echo e(number_format($rejected)); ?></td>
            <td class="py-3 text-end pe-3"><?php echo e($rejectionRate); ?>%</td>
          </tr>
          <tr>
            <td class="py-3 ps-3"><i class="ri-time-line text-warning me-2"></i>In Progress</td>
            <td class="py-3 text-end" style="font-size: 15px;"><?php echo e(number_format($total - $approved - $rejected)); ?></td>
            <td class="py-3 text-end pe-3"><?php echo e(round(100 - $approvalRate - $rejectionRate, 1)); ?>%</td>
          </tr>
          <tr class="border-top bg-light">
            <td class="py-3 ps-3"><i class="ri-bank-card-line text-primary me-2"></i>PayNow Confirmed</td>
            <td class="py-3 text-end" style="font-size: 15px;"><?php echo e(number_format($stats['paynow_confirmed'] ?? 0)); ?></td>
            <td class="py-3 text-end pe-3"><?php echo e(round($paynowRate, 1)); ?>%</td>
          </tr>
          <tr class="bg-light">
            <td class="py-3 ps-3"><i class="ri-file-check-line text-success me-2"></i>Proofs Approved</td>
            <td class="py-3 text-end" style="font-size: 15px;"><?php echo e(number_format($stats['proofs_approved'] ?? 0)); ?></td>
            <td class="py-3 text-end pe-3"><?php echo e(round($proofsRate, 1)); ?>%</td>
          </tr>
          <tr class="bg-light">
            <td class="py-3 ps-3"><i class="ri-hand-coin-line text-warning me-2"></i>Waivers Approved</td>
            <td class="py-3 text-end" style="font-size: 15px;"><?php echo e(number_format($stats['waivers_approved'] ?? 0)); ?></td>
            <td class="py-3 text-end pe-3"><?php echo e(round($waiversRate, 1)); ?>%</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <div class="alert alert-info border-0 rounded-4">
    <div class="d-flex gap-3">
      <i class="ri-information-line h4 mb-0"></i>
      <div class="small">
        <strong>Notes:</strong>
        <ul class="mb-0 mt-2">
          <li>Officer performance metrics can be derived from Audit Logs (actions by actor_user_id) without disciplinary use.</li>
          <li>CSV export includes application + payment fields to support reconciliation and governance audits.</li>
          <li>Charts update automatically based on selected date range.</li>
        </ul>
      </div>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  console.log('Audit Reports: Initializing charts...');
  
  if (typeof ApexCharts === 'undefined') {
    console.error('ApexCharts not loaded');
    return;
  }

  const stats = <?php echo json_encode($stats ?? [], 15, 512) ?>;
  console.log('Stats data:', stats);

  // Status Distribution Pie Chart
  const statusDistEl = document.querySelector("#statusDistributionChart");
  if (statusDistEl && stats.total > 0) {
    const inProgress = stats.total - stats.approved - stats.rejected;
    new ApexCharts(statusDistEl, {
      series: [stats.approved, stats.rejected, inProgress],
      chart: { 
        type: 'donut', 
        height: 300,
        animations: { enabled: true, easing: 'easeinout', speed: 800 }
      },
      labels: ['Approved', 'Rejected', 'In Progress'],
      colors: ['#facc15', '#ef4444', '#000000'],
      legend: { position: 'bottom', fontSize: '13px' },
      stroke: { width: 0 },
      plotOptions: {
        pie: {
          donut: {
            size: '65%',
            labels: {
              show: true,
              total: {
                show: true,
                label: 'TOTAL',
                formatter: function(w) {
                  return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                }
              }
            }
          }
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function(val) {
          return val.toFixed(1) + '%';
        }
      }
    }).render();
  }

  // Approval vs Rejection Bar Chart
  const approvalRejectionEl = document.querySelector("#approvalRejectionChart");
  if (approvalRejectionEl && stats.total > 0) {
    new ApexCharts(approvalRejectionEl, {
      series: [{
        name: 'Count',
        data: [stats.approved, stats.rejected]
      }],
      chart: {
        type: 'bar',
        height: 300,
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '50%',
          borderRadius: 8,
          dataLabels: { position: 'top' }
        }
      },
      dataLabels: {
        enabled: true,
        offsetY: -20,
        style: { fontSize: '14px', fontWeight: 'bold', colors: ['#334155'] }
      },
      colors: ['#facc15', '#ef4444'],
      xaxis: {
        categories: ['Approved', 'Rejected'],
        labels: { style: { fontSize: '13px', fontWeight: 600 } }
      },
      yaxis: {
        labels: {
          formatter: function(value) {
            return Math.round(value);
          }
        }
      },
      grid: { borderColor: '#f1f5f9' }
    }).render();
  }

  // Payment Metrics Bar Chart
  const paymentMetricsEl = document.querySelector("#paymentMetricsChart");
  if (paymentMetricsEl) {
    new ApexCharts(paymentMetricsEl, {
      series: [{
        name: 'Applications',
        data: [stats.paynow_confirmed, stats.proofs_approved, stats.waivers_approved]
      }],
      chart: {
        type: 'bar',
        height: 250,
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          borderRadius: 6,
          dataLabels: { position: 'top' }
        }
      },
      dataLabels: {
        enabled: true,
        offsetX: 30,
        style: { fontSize: '13px', fontWeight: 'bold', colors: ['#334155'] }
      },
      colors: ['#000000', '#facc15', '#eab308'],
      xaxis: {
        categories: ['PayNow Confirmed', 'Proofs Approved', 'Waivers Approved'],
        labels: {
          formatter: function(value) {
            return Math.round(value);
          }
        }
      },
      grid: { borderColor: '#f1f5f9' }
    }).render();
  }

  console.log('All charts rendered successfully');
});
</script>

<style>
.fw-black {
  font-weight: 900 !important;
}

@media print {
  .btn, form, .alert { display: none !important; }
  .zmc-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}
.bg-gradient-success {
  background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
}
.bg-gradient-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/staff/auditor/reports.blade.php ENDPATH**/ ?>