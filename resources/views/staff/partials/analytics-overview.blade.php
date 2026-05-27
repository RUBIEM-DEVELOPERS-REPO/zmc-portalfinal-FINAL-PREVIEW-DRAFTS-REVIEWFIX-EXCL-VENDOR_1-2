@php
  // Initial safe defaults (will be overwritten by AJAX)
  $analyticsTotals = $analyticsTotals ?? [
    'public_users' => 0,
    'staff_users' => 0,
    'accreditation' => 0,
    'registration' => 0,
  ];
@endphp

<div class="analytics-section mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0"><i class="ri-bar-chart-box-line me-2"></i>Analytics Overview</h5>
    <a href="{{ route('admin.analytics') }}" class="btn btn-sm btn-outline-primary">
      <i class="ri-line-chart-line me-1"></i>Full Analytics
    </a>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="card-body text-center py-3">
          <div class="text-muted small mb-1">Public Users</div>
          <div class="fs-3 fw-bold text-primary" id="analytics-total-public">{{ number_format($analyticsTotals['public_users'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);">
        <div class="card-body text-center py-3">
          <div class="text-muted small mb-1">Staff Users</div>
          <div class="fs-3 fw-bold" style="color: #92400e;" id="analytics-total-staff">{{ number_format($analyticsTotals['staff_users'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
        <div class="card-body text-center py-3">
          <div class="text-muted small mb-1">Accreditation Apps</div>
          <div class="fs-3 fw-bold text-info" id="analytics-total-accreditation">{{ number_format($analyticsTotals['accreditation'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
        <div class="card-body text-center py-3">
          <div class="text-muted small mb-1">Media House Regs</div>
          <div class="fs-3 fw-bold" style="color: #c2410c;" id="analytics-total-registration">{{ number_format($analyticsTotals['registration'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts Row --}}
  <div class="row g-4">
    {{-- Daily Trend Chart --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <div class="fw-bold"><i class="ri-line-chart-line me-2"></i>Daily Trend (30 days)</div>
        </div>
        <div class="card-body">
          <canvas id="trendChart" height="110"></canvas>
          <div class="text-muted small mt-2">Includes accreditation, media house registrations, and new public users per day.</div>
        </div>
      </div>
    </div>

    {{-- Status Breakdown Chart --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 py-3">
          <div class="fw-bold"><i class="ri-pie-chart-line me-2"></i>Applications by Status</div>
        </div>
        <div class="card-body">
          <canvas id="statusChart" height="240"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  let trendChart, statusChart;

  function initCharts(data) {
    // Daily Trend Chart
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
      if (trendChart) {
          trendChart.destroy();
      }
      trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: data ? data.labels : [],
          datasets: [
            {
              label: 'Accreditation',
              data: data ? data.datasets.accreditation : [],
              borderColor: '#3b82f6',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              fill: true,
              tension: 0.4
            },
            {
              label: 'Media House',
              data: data ? data.datasets.registration : [],
              borderColor: '#ec4899',
              backgroundColor: 'rgba(236, 72, 153, 0.1)',
              fill: true,
              tension: 0.4
            },
            {
              label: 'New Public Users',
              data: data ? data.datasets.public_users : [],
              borderColor: '#f59e0b',
              backgroundColor: 'rgba(245, 158, 11, 0.1)',
              fill: true,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: { usePointStyle: true, padding: 15 }
            }
          },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
          }
        }
      });
    }

    // Status Breakdown Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
      if (statusChart) {
          statusChart.destroy();
      }
      statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: data ? data.status_breakdown.labels : [],
          datasets: [{
            data: data ? data.status_breakdown.counts : [],
            backgroundColor: [
              '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
              '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { usePointStyle: true, padding: 10, font: { size: 11 } }
            }
          }
        }
      });
    }
  }

  function updateCharts(data) {
    if (!trendChart || !statusChart) {
      initCharts(data);
      return;
    }

    // Update Trend Chart
    trendChart.data.labels = data.labels;
    trendChart.data.datasets[0].data = data.datasets.accreditation;
    trendChart.data.datasets[1].data = data.datasets.registration;
    trendChart.data.datasets[2].data = data.datasets.public_users;
    trendChart.update();

    // Update Status Chart
    statusChart.data.labels = data.status_breakdown.labels;
    statusChart.data.datasets[0].data = data.status_breakdown.counts;
    statusChart.update();

    // Update summary numbers in cards
    const totalPub = document.getElementById('analytics-total-public');
    if (totalPub) {
        const sum = data.datasets.public_users.reduce((a, b) => a + b, 0);
        // Note: The cards expected total counts, not just 30-day counts. 
        // But the dashboard refresh script already updates the top counters.
        // We can just leave them as they are or sync them if needed.
    }
  }

  // Listen for the custom event from the main dashboard script
  window.addEventListener('zmc:dashboard-data-updated', function(e) {
    updateCharts(e.detail);
  });

  // Initial placeholder init if no data yet
  initCharts();

})();
</script>
