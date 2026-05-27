<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold m-0" style="color: #0f172a;">Accreditation & Registration Trends</h6>
            <p class="text-slate-600 small m-0">Monthly growth analysis for <?php echo e(date('Y')); ?></p>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-slate-100 border text-slate-600 rounded-pill px-3" data-bs-toggle="dropdown">
                <?php echo e($currentRangeLabel ?? 'Last 12 Months'); ?> <i class="ri-arrow-down-s-line"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2">
                <li><a class="dropdown-item rounded-3 small fw-bold px-3 py-2 <?php echo e(request('trend_range') == '30_days' ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['trend_range' => '30_days'])); ?>">Last 30 Days</a></li>
                <li><a class="dropdown-item rounded-3 small fw-bold px-3 py-2 <?php echo e(request('trend_range') == '90_days' ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['trend_range' => '90_days'])); ?>">Last 90 Days</a></li>
                <li><a class="dropdown-item rounded-3 small fw-bold px-3 py-2 <?php echo e(request('trend_range') == '6_months' ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['trend_range' => '6_months'])); ?>">Last 6 Months</a></li>
                <li><a class="dropdown-item rounded-3 small fw-bold px-3 py-2 <?php echo e((request('trend_range') == '12_months' || !request('trend_range')) ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['trend_range' => '12_months'])); ?>">Last 12 Months</a></li>
                <li><a class="dropdown-item rounded-3 small fw-bold px-3 py-2 <?php echo e(request('trend_range') == 'this_year' ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['trend_range' => 'this_year'])); ?>">This Year</a></li>
                <li><a class="dropdown-item rounded-3 small fw-bold px-3 py-2 <?php echo e(request('trend_range') == 'all_time' ? 'active' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['trend_range' => 'all_time'])); ?>">All Time</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="accreditationTrendsChart" style="height: 350px;"></div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var trendsOptions = {
            series: [{
                name: 'Accreditations',
                data: <?php echo json_encode($accreditationTrends ?? [], 15, 512) ?>
            }, {
                name: 'Registrations',
                data: <?php echo json_encode($registrationTrends ?? [], 15, 512) ?>
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                }
            },
            xaxis: {
                categories: <?php echo json_encode($trendLabels ?? [], 15, 512) ?>,
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { show: false },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            colors: ['#0f172a', '#3b82f6']
        };

        var trendsChart = new ApexCharts(document.querySelector("#accreditationTrendsChart"), trendsOptions);
        trendsChart.render();
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/partials/analytics/trends.blade.php ENDPATH**/ ?>