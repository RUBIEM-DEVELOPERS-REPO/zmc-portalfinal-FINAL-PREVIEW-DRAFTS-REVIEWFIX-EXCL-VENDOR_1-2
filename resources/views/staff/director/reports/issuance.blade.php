@extends('layouts.portal')
@section('title', 'Issuance & Print Oversight')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <a href="{{ route('staff.director.dashboard') }}" class="btn btn-sm btn-link p-0 text-muted mb-2 text-decoration-none">
            <i class="ri-arrow-left-line"></i> Back to Overview
        </a>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Issuance & Print Oversight</h4>
    </div>

    <div class="row g-4">
        <!-- Monthly Issuance Counts -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Monthly Issuance Trend (Last 12 Months)</h6>
                <div style="height: 300px;">
                    <canvas id="issuanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Print vs Reprint Ratio -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Print vs Reprint Ratio</h6>
                <div style="height: 250px;">
                    <canvas id="printRatioChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Initial Prints</span>
                        <span class="fw-bold text-success">{{ number_format($printRatio['total_prints']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Reprints</span>
                        <span class="fw-bold text-warning">{{ number_format($printRatio['total_reprints']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Reprint Rate</span>
                        <span class="fw-bold">
                            {{ $printRatio['total_prints'] > 0 ? number_format(($printRatio['total_reprints'] / $printRatio['total_prints']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outstanding Unprinted Approvals -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-alert-line text-danger me-2"></i>Outstanding Unprinted Approvals
                </h6>
                @if($outstandingUnprinted->count() > 0)
                <div class="alert alert-danger alert-sm mb-3">
                    <strong>{{ $outstandingUnprinted->count() }}</strong> approved applications awaiting print
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Application ID</th>
                                <th>Applicant</th>
                                <th class="text-end">Days Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outstandingUnprinted->take(10) as $app)
                            <tr>
                                <td class="fw-bold">{{ $app->application_number }}</td>
                                <td>{{ $app->applicant_name }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $app->days_pending > 7 ? 'danger' : ($app->days_pending > 3 ? 'warning' : 'info') }}">
                                        {{ $app->days_pending }} days
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-success alert-sm">
                    <i class="ri-checkbox-circle-line me-2"></i>All approved applications have been printed
                </div>
                @endif
            </div>
        </div>

        <!-- Print Actions by Staff -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Print Actions by Production Staff</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th class="text-end">Initial Prints</th>
                                <th class="text-end">Reprints</th>
                                <th class="text-end">Total Actions</th>
                                <th class="text-end">Reprint Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($printsByStaff as $staff)
                            <tr>
                                <td class="fw-bold">{{ $staff->name }}</td>
                                <td class="text-end">{{ number_format($staff->initial_prints) }}</td>
                                <td class="text-end">{{ number_format($staff->reprints) }}</td>
                                <td class="text-end fw-bold">{{ number_format($staff->total_prints) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $staff->reprint_rate > 20 ? 'danger' : ($staff->reprint_rate > 10 ? 'warning' : 'success') }}">
                                        {{ number_format($staff->reprint_rate, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Print Performance Summary -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Print Performance Summary</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-light">
                            <div class="h3 fw-bold mb-1 text-primary">
                                {{ number_format($printRatio['total_prints'] + $printRatio['total_reprints']) }}
                            </div>
                            <div class="text-muted small">Total Print Actions</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-success bg-opacity-10 border-success">
                            <div class="h3 fw-bold mb-1 text-success">
                                {{ number_format($printRatio['total_prints']) }}
                            </div>
                            <div class="text-muted small">Initial Prints</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-warning bg-opacity-10 border-warning">
                            <div class="h3 fw-bold mb-1 text-warning">
                                {{ number_format($printRatio['total_reprints']) }}
                            </div>
                            <div class="text-muted small">Reprints</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-danger bg-opacity-10 border-danger">
                            <div class="h3 fw-bold mb-1 text-danger">
                                {{ $outstandingUnprinted->count() }}
                            </div>
                            <div class="text-muted small">Pending Prints</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initCharts, 100);
            return;
        }

        // Monthly Issuance Chart
        const issuanceCanvas = document.getElementById('issuanceChart');
        if (issuanceCanvas) {
            new Chart(issuanceCanvas, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyIssuance->pluck('month')->toArray()) !!},
                    datasets: [{
                        label: 'Accreditations Issued',
                        data: {!! json_encode($monthlyIssuance->pluck('count')->toArray()) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // Print Ratio Chart
        const printRatioCanvas = document.getElementById('printRatioChart');
        if (printRatioCanvas) {
            new Chart(printRatioCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Initial Prints', 'Reprints'],
                    datasets: [{
                        data: [
                            {{ $printRatio['total_prints'] }},
                            {{ $printRatio['total_reprints'] }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }
})();
</script>
@endsection
