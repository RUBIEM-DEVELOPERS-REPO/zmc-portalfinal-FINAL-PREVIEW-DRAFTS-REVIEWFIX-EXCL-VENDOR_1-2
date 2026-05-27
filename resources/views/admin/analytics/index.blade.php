@extends('layouts.portal')

@section('title', 'Analytics')

@section('content')
@php
  $labels = collect(range(0, 29))
    ->map(fn($i) => $from->copy()->addDays($i)->format('Y-m-d'))
    ->values();

  $appMap = $dailyApplications->groupBy(fn($r) => $r->d)->map(function ($rows) {
    return $rows->pluck('c', 'application_type');
  });

  $accreditationSeries = $labels->map(fn($d) => (int)($appMap[$d]['accreditation'] ?? 0));
  $registrationSeries  = $labels->map(fn($d) => (int)($appMap[$d]['registration'] ?? 0));
  $publicUsersMap      = $dailyPublicUsers->pluck('c', 'd');
  $publicUsersSeries   = $labels->map(fn($d) => (int)($publicUsersMap[$d] ?? 0));

  $statusOptions = $statusBreakdown->pluck('status')->values();
@endphp

<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div class="d-flex align-items-center gap-3">
      <div>
        <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Analytics</h4>
        <div class="text-muted mt-1" style="font-size:13px;">Trends and breakdowns across applications and users.</div>
      </div>
      <form action="{{ route('admin.analytics') }}" method="GET" id="yearFilterForm" class="ms-2">
          <select name="year" class="form-select border shadow-sm fw-bold bg-white btn-sm" style="height: 31px;" onchange="document.getElementById('yearFilterForm').submit()">
              @foreach($availableYears ?? [] as $y)
                  <option value="{{ $y }}" {{ (isset($year) && $year == $y) ? 'selected' : '' }}>
                      Year: {{ $y }}
                  </option>
              @endforeach
          </select>
      </form>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-dark">
      <i class="ri-arrow-left-line me-1"></i> Back to Dashboard
    </a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Public Users</div>
          <div class="fs-3 fw-bold">{{ number_format($totals['public_users'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Staff Users</div>
          <div class="fs-3 fw-bold">{{ number_format($totals['staff_users'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Accreditation Applications</div>
          <div class="fs-3 fw-bold">{{ number_format($totals['accreditation'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Media House Registrations</div>
          <div class="fs-3 fw-bold">{{ number_format($totals['registration'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
          <div class="fw-bold"><i class="ri-line-chart-line me-2"></i>Daily trend (30 days)</div>
        </div>
        <div class="card-body">
          <canvas id="trendChart" height="110"></canvas>
          <div class="text-muted small mt-2">Includes accreditation, media house registrations, and new public users per day.</div>
        </div>
      </div>
    </div>
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

  <div class="row g-4 mb-4">
    <div class="col-lg-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <div class="fw-bold"><i class="ri-bar-chart-fill me-2"></i>Applications by Collection Region</div>
        </div>
        <div class="card-body">
          <canvas id="regionChart" height="80"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <div class="fw-bold"><i class="ri-table-2 me-2"></i>By Status</div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 small">
              <tbody>
                @foreach($statusBreakdown as $row)
                  <tr>
                    <td class="ps-3">{{ ucwords(str_replace('_',' ', $row->status)) }}</td>
                    <td class="text-end pe-3 fw-bold">{{ number_format($row->c) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <div class="fw-bold"><i class="ri-table-2 me-2"></i>By Type</div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 small">
              <tbody>
                @foreach($typeBreakdown as $row)
                  <tr>
                    <td class="ps-3">{{ ucfirst($row->application_type ?? 'Unknown') }}</td>
                    <td class="text-end pe-3 fw-bold">{{ number_format($row->c) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <div class="fw-bold"><i class="ri-table-2 me-2"></i>By Region</div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 small">
              <tbody>
                @foreach($regionBreakdown as $row)
                  <tr>
                    <td class="ps-3">{{ ucfirst($row->collection_region ?? 'Online/Other') }}</td>
                    <td class="text-end pe-3 fw-bold">{{ number_format($row->c) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function(){
    const labels = @json($labels);
    const seriesAcc = @json($accreditationSeries);
    const seriesReg = @json($registrationSeries);
    const seriesPub = @json($publicUsersSeries);

    const statusLabels = @json($statusBreakdown->map(fn($r) => ucwords(str_replace('_',' ', $r->status))));
    const statusCounts = @json($statusBreakdown->pluck('c'));

    const ctx = document.getElementById('trendChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            { label: 'Accreditation', data: seriesAcc, tension: 0.25 },
            { label: 'Media House', data: seriesReg, tension: 0.25 },
            { label: 'New Public Users', data: seriesPub, tension: 0.25 }
          ]
        },
        options: {
          responsive: true,
          interaction: { mode: 'index', intersect: false },
          scales: {
            x: { ticks: { maxTicksLimit: 8 } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      });
    }

    const pctx = document.getElementById('statusChart');
    if (pctx) {
      new Chart(pctx, {
        type: 'doughnut',
        data: {
          labels: statusLabels,
          datasets: [{ data: statusCounts }]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' } }
        }
      });
    }

    const rctx = document.getElementById('regionChart');
    if (rctx) {
      new Chart(rctx, {
        type: 'bar',
        data: {
          labels: @json($regionBreakdown->pluck('collection_region')->map(fn($r) => ucfirst($r ?? 'Other'))),
          datasets: [{
            label: 'Applications',
            data: @json($regionBreakdown->pluck('c')),
            backgroundColor: '#1e293b'
          }]
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true } }
        }
      });
    }
  })();
</script>
@endsection
