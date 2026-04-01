@extends('layouts.portal')
@section('title', 'Financial Performance')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <a href="{{ route('staff.director.dashboard') }}" class="btn btn-sm btn-link p-0 text-muted mb-2 text-decoration-none">
            <i class="ri-arrow-left-line"></i> Back to Overview
        </a>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Financial Performance & Revenue Analytics</h4>
    </div>

    <div class="row g-4">
        <!-- Monthly Revenue Trend -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Monthly Revenue Trend (Last 12 Months)</h6>
                <div style="height: 300px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Service Type -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Revenue by Service Type</h6>
                <div style="height: 250px;">
                    <canvas id="serviceTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Applicant Type -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Revenue by Applicant Category</h6>
                <div style="height: 250px;">
                    <canvas id="applicantTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Residency -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Revenue by Residency Type</h6>
                <div style="height: 250px;">
                    <canvas id="residencyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Payment Method -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Revenue by Payment Method</h6>
                <div style="height: 250px;">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Waiver Statistics -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Waiver Statistics</h6>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">Total Waivers Granted</td>
                                <td class="text-end fw-bold">{{ number_format($waiverStatistics['count']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Waiver Value</td>
                                <td class="text-end fw-bold text-danger">${{ number_format($waiverStatistics['total_value'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h6 class="fw-bold mt-4 mb-2 small">By Approver</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Staff Member</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($waiverStatistics['by_approver'] as $waiver)
                            <tr>
                                <td>{{ $waiver->approver_name ?? 'Unknown' }}</td>
                                <td class="text-end">{{ $waiver->count }}</td>
                                <td class="text-end">${{ number_format($waiver->total_value, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Outstanding Payments Aging -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Outstanding Payments Aging</h6>
                <div style="height: 250px;">
                    <canvas id="agingChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">0-30 Days</span>
                        <span class="fw-bold">${{ number_format($outstandingPaymentsAging['0_30'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">30-60 Days</span>
                        <span class="fw-bold text-warning">${{ number_format($outstandingPaymentsAging['30_60'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">60+ Days</span>
                        <span class="fw-bold text-danger">${{ number_format($outstandingPaymentsAging['60_plus'], 2) }}</span>
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

        // Revenue Trend Chart
        const revenueTrendCanvas = document.getElementById('revenueTrendChart');
        if (revenueTrendCanvas) {
            new Chart(revenueTrendCanvas, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyRevenueTrend['months']) !!},
                    datasets: [
                        {
                            label: 'Current Year',
                            data: {!! json_encode($monthlyRevenueTrend['current_year']) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Previous Year',
                            data: {!! json_encode($monthlyRevenueTrend['previous_year']) !!},
                            borderColor: '#9ca3af',
                            backgroundColor: 'rgba(156, 163, 175, 0.1)',
                            tension: 0.3,
                            fill: true,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Service Type Chart
        const serviceTypeCanvas = document.getElementById('serviceTypeChart');
        if (serviceTypeCanvas) {
            new Chart(serviceTypeCanvas, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($revenueByServiceType->pluck('service_type')->map(fn($t) => ucwords(str_replace('_', ' ', $t)))->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($revenueByServiceType->pluck('total')->toArray()) !!},
                        backgroundColor: ['#3b82f6', '#facc15', '#10b981', '#f59e0b', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': $' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Applicant Type Chart
        const applicantTypeCanvas = document.getElementById('applicantTypeChart');
        if (applicantTypeCanvas) {
            new Chart(applicantTypeCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($revenueByApplicantType->pluck('applicant_category')->map(fn($t) => ucwords(str_replace('_', ' ', $t)))->toArray()) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($revenueByApplicantType->pluck('total')->toArray()) !!},
                        backgroundColor: '#3b82f6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Residency Chart
        const residencyCanvas = document.getElementById('residencyChart');
        if (residencyCanvas) {
            new Chart(residencyCanvas, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($revenueByResidency->pluck('residency_type')->map(fn($t) => ucwords($t))->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($revenueByResidency->pluck('total')->toArray()) !!},
                        backgroundColor: ['#10b981', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': $' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Payment Method Chart
        const paymentMethodCanvas = document.getElementById('paymentMethodChart');
        if (paymentMethodCanvas) {
            new Chart(paymentMethodCanvas, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($revenueByPaymentMethod->pluck('payment_method')->map(fn($t) => ucwords(str_replace('_', ' ', $t)))->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($revenueByPaymentMethod->pluck('total')->toArray()) !!},
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': $' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Aging Chart
        const agingCanvas = document.getElementById('agingChart');
        if (agingCanvas) {
            new Chart(agingCanvas, {
                type: 'bar',
                data: {
                    labels: ['0-30 Days', '30-60 Days', '60+ Days'],
                    datasets: [{
                        label: 'Outstanding Amount',
                        data: [
                            {{ $outstandingPaymentsAging['0_30'] }},
                            {{ $outstandingPaymentsAging['30_60'] }},
                            {{ $outstandingPaymentsAging['60_plus'] }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Amount: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
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
