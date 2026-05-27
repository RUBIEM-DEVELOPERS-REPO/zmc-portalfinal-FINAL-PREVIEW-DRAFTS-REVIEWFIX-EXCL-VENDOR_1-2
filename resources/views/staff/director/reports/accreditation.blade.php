@extends('layouts.portal')
@section('title', 'Accreditation Performance')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <a href="{{ auth()->user()->hasRole('director') ? route('staff.director.dashboard') : (auth()->user()->hasRole('registrar') ? route('staff.registrar.dashboard') : url()->previous()) }}" class="btn btn-sm btn-link p-0 text-muted mb-2 text-decoration-none">
            <i class="ri-arrow-left-line"></i> Back to Overview
        </a>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Accreditation & Operational Performance</h4>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="zmc-card h-100">
                <h6 class="fw-bold mb-3">Applications Trend (Last 12 Months)</h6>
                <div style="height: 300px;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
<<<<<<< HEAD
        <div class="col-md-4">
            <div class="zmc-card h-100">
                <h6 class="fw-bold mb-3">Processing Efficiency (Avg. Hours)</h6>
                <div class="d-flex flex-column gap-4 mt-4">
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Accreditation Officer</span>
                            <span class="fw-bold">{{ round($efficiency['officer'], 1) }}h</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 70%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Registrar Reviews</span>
                            <span class="fw-bold">{{ round($efficiency['registrar'], 1) }}h</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-auto pt-4 text-center">
                    <div class="text-muted smaller">Operational Target: <span class="fw-bold">48 Hours</span></div>
                </div>
            </div>
        </div>
=======
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b

        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Accreditation Category Distribution</h6>
                <div style="height: 250px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Application Type Overview</h6>
                <div class="row g-3 mt-2">
                    @foreach($categories as $cat)
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center h-100 bg-light">
                                <div class="text-muted small fw-bold text-uppercase mb-1">{{ $cat->accreditation_category_code }}</div>
                                <div class="h4 fw-black mb-0 text-primary">{{ number_format($cat->count) }}</div>
                                <div class="text-muted smaller">Accreditations</div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <div class="p-3 border rounded-3 text-center bg-info bg-opacity-10 border-info">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Media House Registrations</div>
                            <div class="h3 fw-black mb-0 text-info">{{ number_format($mediaHouseRegistrations) }}</div>
                            <div class="text-muted smaller">Total Registrations</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Recent Performance Stats</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Month</th>
                                <th>Submitted</th>
<<<<<<< HEAD
                                <th>Issued/Reviewed</th>
=======
                                <th>Approved</th>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
                                <th>Returned for Correction</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyTrends as $trend)
                            <tr>
                                <td class="fw-bold">{{ $trend->month }}</td>
                                <td>{{ $trend->total_submitted }}</td>
                                <td class="text-success">{{ $trend->total_approved }}</td>
                                <td class="text-danger">{{ $trend->total_returned }}</td>
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
// Wait for both DOM and Chart.js to be ready
(function() {
    function initCharts() {
        console.log('=== ACCREDITATION REPORT DEBUG ===');
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');
        console.log('Chart version:', typeof Chart !== 'undefined' ? Chart.version : 'N/A');
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded, retrying in 100ms...');
            setTimeout(initCharts, 100);
            return;
        }
        
        const months = {!! json_encode($months->toArray()) !!};
        const accreditationSubmitted = {!! json_encode($accreditationTrends->pluck('total_submitted')->toArray()) !!};
        const accreditationApproved = {!! json_encode($accreditationTrends->pluck('total_approved')->toArray()) !!};
        const registrationSubmitted = {!! json_encode($registrationTrends->pluck('total_submitted')->toArray()) !!};
        const registrationApproved = {!! json_encode($registrationTrends->pluck('total_approved')->toArray()) !!};
        
        console.log('Months:', months);
        console.log('Accreditation Submitted:', accreditationSubmitted);
        console.log('Accreditation Issued/Reviewed:', accreditationApproved);
        console.log('Registration Submitted:', registrationSubmitted);
        console.log('Registration Issued/Reviewed:', registrationApproved);
        
        // Check if canvas exists
        const trendsCanvas = document.getElementById('trendsChart');
        console.log('Trends canvas found:', trendsCanvas !== null);
        
        if (trendsCanvas) {
            try {
                const chart = new Chart(trendsCanvas, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Accreditations (Submitted)',
                                data: accreditationSubmitted,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Accreditations (Issued/Reviewed)',
                                data: accreditationApproved,
                                borderColor: '#facc15',
                                backgroundColor: 'rgba(250, 204, 21, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Registrations (Submitted)',
                                data: registrationSubmitted,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.3,
                                fill: true,
                                borderDash: [5, 5]
                            },
                            {
                                label: 'Registrations (Issued/Reviewed)',
                                data: registrationApproved,
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
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
                            legend: { 
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
                console.log('Chart created successfully:', chart);
            } catch (error) {
                console.error('Error creating chart:', error);
            }
        } else {
            console.error('Canvas element #trendsChart not found in DOM');
        }

        // Category Chart
        console.log('=== CATEGORY CHART DEBUG ===');
        const categoryCanvas = document.getElementById('categoryChart');
        console.log('Category canvas found:', categoryCanvas !== null);
        
        const categoryLabels = {!! json_encode($categories->pluck('accreditation_category_code')->toArray()) !!};
        const categoryCounts = {!! json_encode($categories->pluck('count')->toArray()) !!};
        
        console.log('Category labels:', categoryLabels);
        console.log('Category counts:', categoryCounts);
        
        if (categoryCanvas) {
            try {
                const categoryChart = new Chart(categoryCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryCounts,
                            backgroundColor: ['#000000', '#facc15', '#eab308', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { 
                                position: 'right',
                                labels: {
                                    padding: 10,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Category chart created successfully:', categoryChart);
            } catch (error) {
                console.error('Error creating category chart:', error);
            }
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
