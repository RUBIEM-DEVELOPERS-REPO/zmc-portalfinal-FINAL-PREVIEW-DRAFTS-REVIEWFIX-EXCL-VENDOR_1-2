@extends('layouts.portal')
@section('title', 'Geographic Distribution')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <a href="{{ route('staff.director.dashboard') }}" class="btn btn-sm btn-link p-0 text-muted mb-2 text-decoration-none">
            <i class="ri-arrow-left-line"></i> Back to Overview
        </a>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Geographic Distribution & Regional Analysis</h4>
    </div>

    <div class="row g-4">
        <!-- Accreditations by Region -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Accreditations by Region</h6>
                <div style="height: 300px;">
                    <canvas id="accreditationsByRegionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Region -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Revenue by Region</h6>
                <div style="height: 300px;">
                    <canvas id="revenueByRegionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Processing Time by Region -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Average Processing Time by Region (Days)</h6>
                <div style="height: 300px;">
                    <canvas id="processingTimeByRegionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Media Houses by Region -->
        <div class="col-md-6">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Media Houses by Region</h6>
                <div style="height: 300px;">
                    <canvas id="mediaHousesByRegionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Regional Summary Table -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Regional Performance Summary</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="smaller text-muted">
                                <th>Region</th>
                                <th class="text-end">Accreditations</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Avg. Processing (Days)</th>
                                <th class="text-end">Media Houses</th>
                                <th class="text-end">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accreditationsByRegion as $region)
                            @php
                                $revenue = $revenueByRegion->firstWhere('region', $region->region);
                                $processingTime = $processingTimeByRegion->firstWhere('region', $region->region);
                                $mediaHouses = $mediaHousesByRegion->firstWhere('region', $region->region);
                                $avgProcessingDays = $processingTime ? $processingTime->avg_processing_days : 0;
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ ucwords($region->region ?? 'Unknown') }}</td>
                                <td class="text-end">{{ number_format($region->count) }}</td>
                                <td class="text-end">${{ number_format($revenue->total ?? 0, 2) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $avgProcessingDays < 5 ? 'success' : ($avgProcessingDays < 10 ? 'warning' : 'danger') }}">
                                        {{ number_format($avgProcessingDays, 1) }}
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($mediaHouses->count ?? 0) }}</td>
                                <td class="text-end">
                                    @if($avgProcessingDays < 5)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    @elseif($avgProcessingDays < 10)
                                        <i class="ri-error-warning-fill text-warning"></i>
                                    @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Regional Insights -->
        <div class="col-md-12">
            <div class="zmc-card">
                <h6 class="fw-bold mb-3">Regional Insights</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-light">
                            <div class="text-muted small mb-1">Highest Volume</div>
                            <div class="h5 fw-bold mb-0 text-primary">
                                {{ ucwords($accreditationsByRegion->sortByDesc('count')->first()->region ?? 'N/A') }}
                            </div>
                            <div class="text-muted smaller">{{ number_format($accreditationsByRegion->sortByDesc('count')->first()->count ?? 0) }} accreditations</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-light">
                            <div class="text-muted small mb-1">Highest Revenue</div>
                            <div class="h5 fw-bold mb-0 text-success">
                                {{ ucwords($revenueByRegion->sortByDesc('total')->first()->region ?? 'N/A') }}
                            </div>
                            <div class="text-muted smaller">${{ number_format($revenueByRegion->sortByDesc('total')->first()->total ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-light">
                            <div class="text-muted small mb-1">Fastest Processing</div>
                            <div class="h5 fw-bold mb-0 text-info">
                                {{ ucwords($processingTimeByRegion->sortBy('avg_processing_days')->first()->region ?? 'N/A') }}
                            </div>
                            <div class="text-muted smaller">{{ number_format($processingTimeByRegion->sortBy('avg_processing_days')->first()->avg_processing_days ?? 0, 1) }} days</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded-3 text-center bg-light">
                            <div class="text-muted small mb-1">Most Media Houses</div>
                            <div class="h5 fw-bold mb-0 text-warning">
                                {{ ucwords($mediaHousesByRegion->sortByDesc('count')->first()->region ?? 'N/A') }}
                            </div>
                            <div class="text-muted smaller">{{ number_format($mediaHousesByRegion->sortByDesc('count')->first()->count ?? 0) }} houses</div>
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

        // Accreditations by Region Chart
        const accreditationsCanvas = document.getElementById('accreditationsByRegionChart');
        if (accreditationsCanvas) {
            new Chart(accreditationsCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($accreditationsByRegion->pluck('region')->map(fn($r) => ucwords($r))->toArray()) !!},
                    datasets: [{
                        label: 'Accreditations',
                        data: {!! json_encode($accreditationsByRegion->pluck('count')->toArray()) !!},
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

        // Revenue by Region Chart
        const revenueCanvas = document.getElementById('revenueByRegionChart');
        if (revenueCanvas) {
            new Chart(revenueCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($revenueByRegion->pluck('region')->map(fn($r) => ucwords($r))->toArray()) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($revenueByRegion->pluck('total')->toArray()) !!},
                        backgroundColor: '#10b981'
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

        // Processing Time by Region Chart
        const processingTimeCanvas = document.getElementById('processingTimeByRegionChart');
        if (processingTimeCanvas) {
            new Chart(processingTimeCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($processingTimeByRegion->pluck('region')->map(fn($r) => ucwords($r))->toArray()) !!},
                    datasets: [{
                        label: 'Avg. Processing Days',
                        data: {!! json_encode($processingTimeByRegion->pluck('avg_processing_days')->toArray()) !!},
                        backgroundColor: '#f59e0b'
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
                            ticks: {
                                callback: function(value) {
                                    return value + ' days';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Media Houses by Region Chart
        const mediaHousesCanvas = document.getElementById('mediaHousesByRegionChart');
        if (mediaHousesCanvas) {
            new Chart(mediaHousesCanvas, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($mediaHousesByRegion->pluck('region')->map(fn($r) => ucwords($r))->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($mediaHousesByRegion->pluck('count')->toArray()) !!},
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
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
