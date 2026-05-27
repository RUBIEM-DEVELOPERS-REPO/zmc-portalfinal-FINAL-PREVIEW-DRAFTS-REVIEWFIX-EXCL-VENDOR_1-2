@extends('layouts.portal')
@section('title', 'Staff Performance Metrics')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <a href="{{ route('staff.director.dashboard') }}" class="btn btn-sm btn-link p-0 text-muted mb-2 text-decoration-none">
            <i class="ri-arrow-left-line"></i> Back to Overview
        </a>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Staff Performance & Productivity Metrics</h4>
    </div>

    <div class="row g-4">
        <!-- Applications Processed Per Officer -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Applications Processed by Accreditation Officers</h6>
                <div style="height: 300px;">
                    <canvas id="officerProcessedChart"></canvas>
                </div>
            </div>
        </div>


        <!-- Payment Verification Volume -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Payment Verification Volume (Accounts Staff)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th class="text-end">Payments Verified (MTD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentVerificationVolume as $staff)
                            <tr>
                                <td class="fw-bold">{{ $staff->name }}</td>
                                <td class="text-end">{{ number_format($staff->verified_count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Approval Distribution Per Officer -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Approval Distribution by Officers</h6>
                <div style="height: 300px;">
                    <canvas id="approvalDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Reassignment Frequency -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">
                    <i class="ri-exchange-line text-warning me-2"></i>Category Reassignment Frequency
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th>Role</th>
                                <th class="text-end">Reassignments</th>
                                <th class="text-end">Risk Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reassignmentFrequency as $staff)
                            <tr>
                                <td class="fw-bold">{{ $staff->name }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $staff->role)) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $staff->reassignment_count < 3 ? 'success' : ($staff->reassignment_count < 5 ? 'warning' : 'danger') }}">
                                        {{ $staff->reassignment_count }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($staff->reassignment_count < 3)
                                        <span class="badge bg-success">Low</span>
                                    @elseif($staff->reassignment_count < 5)
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-danger">High</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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

        // Applications Processed Chart
        const officerProcessedCanvas = document.getElementById('officerProcessedChart');
        if (officerProcessedCanvas) {
            new Chart(officerProcessedCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($applicationsProcessed->pluck('name')->toArray()) !!},
                    datasets: [{
                        label: 'Applications Processed',
                        data: {!! json_encode($applicationsProcessed->pluck('processed_count')->toArray()) !!},
                        backgroundColor: '#3b82f6'
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


        // Approval Distribution Chart
        const approvalDistributionCanvas = document.getElementById('approvalDistributionChart');
        if (approvalDistributionCanvas) {
            new Chart(approvalDistributionCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($approvalDistribution->pluck('name')->toArray()) !!},
                    datasets: [
                        {
                            label: 'Approved',
                            data: {!! json_encode($approvalDistribution->pluck('approved_count')->toArray()) !!},
                            backgroundColor: '#10b981'
                        },
                        {
                            label: 'Returned for Correction',
                            data: {!! json_encode($approvalDistribution->pluck('rejected_count')->toArray()) !!},
                            backgroundColor: '#ef4444'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        x: { stacked: true },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
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
