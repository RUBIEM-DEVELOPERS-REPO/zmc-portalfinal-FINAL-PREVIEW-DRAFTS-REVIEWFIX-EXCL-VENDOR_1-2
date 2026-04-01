@extends('layouts.portal')
@section('title', 'Operational Reports - Registrar')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Operational Reports</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Officer activities and accreditation/registration performance metrics.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm px-3" onclick="exportReports()">
        <i class="ri-download-2-line me-1"></i>Export Reports
      </button>
    </div>
  </div>

  {{-- Date Range Filter --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-calendar-line me-2"></i>
        Date Range Filter
      </h6>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('registrar.reports') }}">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
              <i class="ri-search-line me-1"></i>Apply Filter
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Officer Activities Statistics --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-primary text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-user-star-line me-2"></i>
        Officer Activities Statistics
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="bg-light">
            <tr>
              <th>Officer Name</th>
              <th class="text-center">Total Activities</th>
              <th class="text-center">Approved</th>
              <th class="text-center">Rejected</th>
              <th class="text-center">Corrections</th>
              <th class="text-center">Guidance Requests</th>
            </tr>
          </thead>
          <tbody>
            @forelse($officerStats as $officer)
              <tr>
                <td class="fw-semibold">{{ $officer->officer_name }}</td>
                <td class="text-center">
                  <span class="badge bg-primary">{{ $officer->total_activities }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-success">{{ $officer->approved_count }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-danger">{{ $officer->rejected_count }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-warning">{{ $officer->correction_count }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-info">{{ $officer->guidance_count }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  <i class="ri-user-line me-2"></i>
                  No officer activities found in the selected period.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Application Processing Times --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-success text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-timer-line me-2"></i>
        Application Processing Times
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="bg-light">
            <tr>
              <th>Reference</th>
              <th>Type</th>
              <th>Submitted Date</th>
              <th>Approved Date</th>
              <th class="text-center">Processing Days</th>
            </tr>
          </thead>
          <tbody>
            @forelse($processingTimes as $processing)
              <tr>
                <td class="fw-bold">{{ $processing['reference'] }}</td>
                <td>
                  <span class="badge bg-{{ $processing['type'] === 'accreditation' ? 'primary' : 'success' }}">
                    {{ ucfirst($processing['type']) }}
                  </span>
                </td>
                <td>{{ $processing['submitted_at']->format('d M Y H:i') }}</td>
                <td>{{ $processing['approved_at']->format('d M Y H:i') }}</td>
                <td class="text-center">
                  <span class="badge bg-{{ $processing['processing_days'] <= 7 ? 'success' : ($processing['processing_days'] <= 14 ? 'warning' : 'danger') }}">
                    {{ $processing['processing_days'] }} days
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                  <i class="ri-timer-line me-2"></i>
                  No processing data found in the selected period.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Monthly Trends --}}
  <div class="zmc-card">
    <div class="card-header bg-info text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-bar-chart-line me-2"></i>
        Monthly Trends - Accreditation & Registration
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="bg-light">
            <tr>
              <th>Month</th>
              <th class="text-center">Total Applications</th>
              <th class="text-center">Accreditation</th>
              <th class="text-center">Registration</th>
              <th class="text-center">Approved</th>
              <th class="text-center">Approval Rate</th>
            </tr>
          </thead>
          <tbody>
            @forelse($monthlyTrends as $trend)
              <tr>
                <td class="fw-semibold">{{ date('F Y', strtotime($trend->month . '-01')) }}</td>
                <td class="text-center">
                  <span class="badge bg-primary">{{ $trend->total }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-primary">{{ $trend->accreditation_count }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-success">{{ $trend->registration_count }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-success">{{ $trend->approved_count }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-{{ $trend->approved_count / $trend->total >= 0.8 ? 'success' : ($trend->approved_count / $trend->total >= 0.6 ? 'warning' : 'danger') }}">
                    {{ round(($trend->approved_count / $trend->total) * 100, 1) }}%
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  <i class="ri-bar-chart-line me-2"></i>
                  No trend data found in the selected period.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
function exportReports() {
  const params = new URLSearchParams(window.location.search);
  window.location.href = '/staff/registrar/reports/export?' + params.toString();
}
</script>
@endsection
      </div>
      <div class="col-md-2">
        <label class="small fw-bold text-muted">Date To</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to', $dateTo->toDateString()) }}">
      </div>
      <div class="col-md-2">
        <label class="small fw-bold text-muted">Application Type</label>
        <select name="app_type" class="form-select form-select-sm">
          <option value="">All Types</option>
          <option value="accreditation" {{ $appType === 'accreditation' ? 'selected' : '' }}>Accreditation</option>
          <option value="registration"  {{ $appType === 'registration'  ? 'selected' : '' }}>Registration</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="small fw-bold text-muted">Officer</label>
        <select name="officer_id" class="form-select form-select-sm">
          <option value="">All Officers</option>
          @foreach($allOfficers as $off)
            <option value="{{ $off->id }}" {{ (string)$officerId === (string)$off->id ? 'selected' : '' }}>{{ $off->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-dark btn-sm w-100">Apply Filters</button>
      </div>
      <div class="col-md-1">
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
      </div>
    </div>
  </form>

  {{-- KPI Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-2-4" style="flex:0 0 20%; max-width:20%;">
      <div class="zmc-card text-center py-3 px-2">
        <div class="small text-muted fw-bold text-uppercase mb-1">Officer Reviews</div>
        <div class="h2 fw-bold text-success mb-0">{{ $officerApproved }}</div>
        <div class="small text-muted">Moved forward for Registrar</div>
      </div>
    </div>
    <div class="col-6 col-md-2-4" style="flex:0 0 20%; max-width:20%;">
      <div class="zmc-card text-center py-3 px-2">
        <div class="small text-muted fw-bold text-uppercase mb-1">Returned to Officer</div>
        <div class="h2 fw-bold text-warning mb-0">{{ $officerReturned }}</div>
        <div class="small text-muted">Pending correction</div>
      </div>
    </div>
    <div class="col-6 col-md-2-4" style="flex:0 0 20%; max-width:20%;">
      <div class="zmc-card text-center py-3 px-2">
        <div class="small text-muted fw-bold text-uppercase mb-1">Returned for Correction</div>
        <div class="h2 fw-bold text-danger mb-0">{{ $officerRejected }}</div>
        <div class="small text-muted">By Officer</div>
      </div>
    </div>
    <div class="col-6 col-md-2-4" style="flex:0 0 20%; max-width:20%;">
      <div class="zmc-card text-center py-3 px-2">
        <div class="small text-muted fw-bold text-uppercase mb-1">Pending Review</div>
        <div class="h2 fw-bold text-info mb-0">{{ $pendingOfficer }}</div>
        <div class="small text-muted">In Officer queue</div>
      </div>
    </div>
    <div class="col-6 col-md-2-4" style="flex:0 0 20%; max-width:20%;">
      <div class="zmc-card text-center py-3 px-2">
        <div class="small text-muted fw-bold text-uppercase mb-1">Flagged by Registrar</div>
        <div class="h2 fw-bold text-danger mb-0">{{ $flaggedByRegistrar }}</div>
        <div class="small text-muted">Anomalies</div>
      </div>
    </div>
  </div>

  {{-- Chart --}}
  <div class="row g-4 mb-4">
    <div class="col-md-8">
      <div class="zmc-card h-100 p-3">
        <h6 class="fw-bold mb-3"><i class="ri-line-chart-line me-2" style="color:var(--zmc-accent)"></i>Monthly Officer Activity (12 Months)</h6>
        <canvas id="officerTrendChart" height="100"></canvas>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card h-100 p-3">
        <h6 class="fw-bold mb-3"><i class="ri-pie-chart-line me-2" style="color:var(--zmc-accent)"></i>Outcome Breakdown</h6>
        <canvas id="outcomeDonutChart"></canvas>
        <div class="d-flex justify-content-around mt-3 small">
          <span><span class="badge bg-success me-1">&nbsp;</span>Approved</span>
          <span><span class="badge bg-warning text-dark me-1">&nbsp;</span>Returned</span>
          <span><span class="badge bg-danger me-1">&nbsp;</span>Returned for Correction</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Officer Breakdown Table + Activity Feed --}}
  <div class="row g-4">
    <div class="col-md-6">
      <div class="zmc-card p-0">
        <div class="p-3 border-bottom fw-bold">
          <i class="ri-group-line me-2" style="color:var(--zmc-accent)"></i>Officer Performance Breakdown
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th>Officer</th>
                <th class="text-center text-success">Reviews</th>
                <th class="text-center text-warning">Returned for Correction</th>
                <th class="text-center text-danger">Returned for Correction</th>
                <th class="text-center text-info">Pending</th>
              </tr>
            </thead>
            <tbody>
            @forelse($officers as $off)
              <tr>
                <td><i class="ri-user-3-line me-1 text-muted"></i>{{ $off['name'] }}</td>
                <td class="text-center fw-bold text-success">{{ $off['approved'] }}</td>
                <td class="text-center fw-bold text-warning">{{ $off['returned'] }}</td>
                <td class="text-center fw-bold text-danger">{{ $off['rejected'] }}</td>
                <td class="text-center fw-bold text-info">{{ $off['pending'] }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-3">No officers found.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="zmc-card p-0">
        <div class="p-3 border-bottom fw-bold d-flex justify-content-between align-items-center">
          <span><i class="ri-history-line me-2" style="color:var(--zmc-accent)"></i>Recent Officer Actions</span>
          <span class="badge bg-secondary">Last 20</span>
        </div>
        <div class="table-responsive" style="max-height:380px; overflow-y:auto;">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Officer</th>
                <th>Action</th>
                <th>Ref</th>
              </tr>
            </thead>
            <tbody>
            @forelse($recentActivity as $log)
              @php
                $actionBadge = match($log->action) {
                  'officer_approve'        => 'success',
                  'officer_reject'         => 'danger',
                  'officer_return'         => 'warning',
                  'accounts_confirm_paid'  => 'info',
                  'accounts_reject_paid'   => 'secondary',
                  default                  => 'light',
                };
                $actionLabel = match($log->action) {
                  'officer_approve'        => 'Reviews',
                  'officer_reject'         => 'Returned for Correction',
                  'officer_return'         => 'Returned for Correction',
                  'accounts_confirm_paid'  => 'Payment OK',
                  'accounts_reject_paid'   => 'Payment Rejected',
                  default                  => ucfirst(str_replace('_',' ',$log->action)),
                };
              @endphp
              <tr>
                <td class="small text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d M H:i') }}</td>
                <td class="small">{{ $log->user?->name ?? '—' }}</td>
                <td><span class="badge bg-{{ $actionBadge }}">{{ $actionLabel }}</span></td>
                <td class="small fw-bold">{{ $log->entity?->reference ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted py-3">No activity in selected range.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const months         = @json($months);
  const approvedTrend  = @json($approvedTrend);
  const returnedTrend  = @json($returnedTrend);

  // Line Chart — Monthly Trend
  new Chart(document.getElementById('officerTrendChart'), {
    type: 'line',
    data: {
      labels: months,
      datasets: [
        {
          label: 'Approved',
          data: approvedTrend,
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,.1)',
          fill: true,
          tension: 0.4,
        },
        {
          label: 'Returned',
          data: returnedTrend,
          borderColor: '#d97706',
          backgroundColor: 'rgba(217,119,6,.1)',
          fill: true,
          tension: 0.4,
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } },
      scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
  });

  // Donut — Outcome breakdown
  new Chart(document.getElementById('outcomeDonutChart'), {
    type: 'doughnut',
    data: {
      labels: ['Reviews', 'Returned for Correction', 'Returned for Correction'],
      datasets: [{
        data: [{{ $officerApproved }}, {{ $officerReturned }}, {{ $officerRejected }}],
        backgroundColor: ['#16a34a', '#d97706', '#dc2626'],
        borderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      cutout: '65%',
      plugins: { legend: { display: false } }
    }
  });
});
</script>
@endpush
@endsection
