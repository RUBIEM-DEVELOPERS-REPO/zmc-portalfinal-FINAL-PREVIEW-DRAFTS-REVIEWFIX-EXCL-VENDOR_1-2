@extends('layouts.portal')
@section('title', 'Compliance & Risk Monitoring')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <a href="{{ route('staff.director.dashboard') }}" class="btn btn-sm btn-link p-0 text-muted mb-2 text-decoration-none">
            <i class="ri-arrow-left-line"></i> Back to Overview
        </a>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Compliance & Risk Monitoring</h4>
    </div>

    <div class="row g-4">
        <!-- Category Reassignments -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-exchange-line text-warning me-2"></i>Category Reassignments
                </h6>
                <div class="alert alert-warning alert-sm mb-3">
                    <strong>{{ $categoryReassignments['total'] }}</strong> reassignments this month
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryReassignments['by_staff'] as $staff)
                            <tr>
                                <td>{{ $staff->user_name }}</td>
                                <td class="text-end fw-bold">{{ $staff->count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reopened Applications -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-refresh-line text-info me-2"></i>Reopened Applications
                </h6>
                <div class="alert alert-info alert-sm mb-3">
                    <strong>{{ $reopenedApplications['total'] }}</strong> applications reopened
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reopenedApplications['by_staff'] as $staff)
                            <tr>
                                <td>{{ $staff->user_name }}</td>
                                <td class="text-end fw-bold">{{ $staff->count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manual Overrides -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-shield-keyhole-line text-danger me-2"></i>Manual Overrides
                </h6>
                <div class="alert alert-danger alert-sm mb-3">
                    <strong>{{ $manualOverrides['total'] }}</strong> manual overrides recorded
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manualOverrides['by_staff'] as $staff)
                            <tr>
                                <td>{{ $staff->user_name }}</td>
                                <td class="text-end fw-bold">{{ $staff->count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Certificate Edits -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-edit-line text-warning me-2"></i>Certificate Edits After Approval
                </h6>
                <div class="alert alert-warning alert-sm mb-3">
                    <strong>{{ $certificateEdits['total'] }}</strong> post-approval edits
                </div>
                <h6 class="fw-bold mt-3 mb-2 small">Most Edited Fields</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Field Name</th>
                                <th class="text-end">Edit Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificateEdits['most_edited_fields'] as $field)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $field->field_name)) }}</td>
                                <td class="text-end fw-bold">{{ $field->count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Print Statistics -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-printer-line text-primary me-2"></i>Print vs Reprint Statistics
                </h6>
                <div style="height: 200px;">
                    <canvas id="printStatsChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Prints</span>
                        <span class="fw-bold">{{ number_format($printStatistics['total_prints']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Reprints</span>
                        <span class="fw-bold text-warning">{{ number_format($printStatistics['total_reprints']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Excessive Reprints -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-alert-line text-danger me-2"></i>Excessive Reprints
                </h6>
                <h6 class="fw-bold mt-3 mb-2 small">By Applicant (Top 10)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Applicant</th>
                                <th class="text-end">Reprints</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($excessiveReprints['by_applicant']->take(10) as $reprint)
                            <tr>
                                <td>{{ $reprint->applicant_name }}</td>
                                <td class="text-end fw-bold text-danger">{{ $reprint->reprint_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Suspicious Activity Alerts -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-alarm-warning-line text-danger me-2"></i>Suspicious Activity Alerts
                </h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center {{ $suspiciousActivity['failed_logins'] > 10 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' }}">
                            <div class="h3 fw-bold mb-1 {{ $suspiciousActivity['failed_logins'] > 10 ? 'text-danger' : 'text-muted' }}">
                                {{ $suspiciousActivity['failed_logins'] }}
                            </div>
                            <div class="text-muted small">Failed Login Attempts</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center {{ $suspiciousActivity['repeated_reassignments'] > 5 ? 'border-warning bg-warning bg-opacity-10' : 'bg-light' }}">
                            <div class="h3 fw-bold mb-1 {{ $suspiciousActivity['repeated_reassignments'] > 5 ? 'text-warning' : 'text-muted' }}">
                                {{ $suspiciousActivity['repeated_reassignments'] }}
                            </div>
                            <div class="text-muted small">Repeated Reassignments</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center {{ $suspiciousActivity['high_waiver_frequency'] > 5 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' }}">
                            <div class="h3 fw-bold mb-1 {{ $suspiciousActivity['high_waiver_frequency'] > 5 ? 'text-danger' : 'text-muted' }}">
                                {{ $suspiciousActivity['high_waiver_frequency'] }}
                            </div>
                            <div class="text-muted small">High Waiver Frequency</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center {{ $suspiciousActivity['system_overrides'] > 3 ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' }}">
                            <div class="h3 fw-bold mb-1 {{ $suspiciousActivity['system_overrides'] > 3 ? 'text-danger' : 'text-muted' }}">
                                {{ $suspiciousActivity['system_overrides'] }}
                            </div>
                            <div class="text-muted small">System Overrides</div>
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

        // Print Statistics Chart
        const printStatsCanvas = document.getElementById('printStatsChart');
        if (printStatsCanvas) {
            new Chart(printStatsCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Initial Prints', 'Reprints'],
                    datasets: [{
                        data: [
                            {{ $printStatistics['total_prints'] }},
                            {{ $printStatistics['total_reprints'] }}
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
