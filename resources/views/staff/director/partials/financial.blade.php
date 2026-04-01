<div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-md-8 border-end">
            <h5 class="fw-bold mb-4">Revenue Trends (12 Months)</h5>
            <div id="revenueTrendChart" style="min-height: 350px;">
                @if(!isset($revenueTrend) || $revenueTrend->isEmpty())
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="ri-line-chart-line" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="mt-3">No revenue data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <h5 class="fw-bold mb-4 px-3">Receivables Ageing</h5>
            <div class="px-3">
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-4">
                    <span class="text-muted small fw-bold">0 - 30 Days</span>
                    <span class="h5 fw-black mb-0 text-success">{{ number_format($aging['0_30'] ?? 0) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-4 border-start border-4 border-warning">
                    <span class="text-muted small fw-bold">31 - 60 Days</span>
                    <span class="h5 fw-black mb-0 text-warning">{{ number_format($aging['30_60'] ?? 0) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-4 border-start border-4 border-danger">
                    <span class="text-muted small fw-bold">60+ Days</span>
                    <span class="h5 fw-black mb-0 text-danger">{{ number_format($aging['60_plus'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4">Revenue Breakdown (Service)</h5>
            <div id="revenueServiceChart" style="min-height: 250px;">
                @if(!isset($breakdown['service']) || $breakdown['service']->isEmpty())
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="ri-pie-chart-line" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="mt-3">No service breakdown data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4">Waiver Analytics</h5>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="text-center flex-grow-1 border-end">
                    <div class="text-muted small fw-bold text-uppercase">Count (All Time)</div>
                    <div class="h2 fw-black mb-0">{{ number_format($waivers['count'] ?? 0) }}</div>
                </div>
                <div class="text-center flex-grow-1">
                    <div class="text-muted small fw-bold text-uppercase">Total Prov. Value</div>
                    <div class="h2 fw-black mb-1 text-info">${{ number_format($waivers['total_value'] ?? 0) }}</div>
                    <div class="small text-muted opacity-75">Approx. revenue waived</div>
                </div>
            </div>
            
            <div class="mt-4">
                <h6 class="small fw-bold text-muted text-uppercase mb-3">Top Approving Staff</h6>
                @if(isset($waivers['by_approver']) && $waivers['by_approver']->isNotEmpty())
                    @foreach($waivers['by_approver'] as $approver)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 mt-1">
                            <span class="smaller fw-bold">User #{{ $approver->waiver_approved_by }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $approver->waiver_count }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-3">
                        <small>No waiver approvals recorded</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Financial tab: Initializing charts...');
    
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts not loaded');
        return;
    }

    // Revenue Trend Line
    const revenueTrendEl = document.querySelector("#revenueTrendChart");
    if (revenueTrendEl) {
        try {
            const revenueData = @json($revenueTrend ?? collect([]));
            console.log('Revenue trend data:', revenueData);
            
            if (revenueData && revenueData.length > 0) {
                const chartData = revenueData.map(item => parseFloat(item.total_revenue || 0));
                const chartLabels = revenueData.map(item => item.month || '');
                
                console.log('Chart data:', chartData);
                console.log('Chart labels:', chartLabels);
                
                new ApexCharts(revenueTrendEl, {
                    series: [{
                        name: 'Total Revenue',
                        data: chartData
                    }],
                    chart: { 
                        height: 350, 
                        type: 'line', 
                        toolbar: { show: false },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    stroke: { width: 4, curve: 'smooth' },
                    colors: ['#facc15'],
                    xaxis: { 
                        categories: chartLabels,
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    },
                    markers: { 
                        size: 5, 
                        strokeColors: "#fff", 
                        strokeWidth: 3,
                        hover: {
                            size: 7
                        }
                    },
                    grid: { borderColor: '#f1f5f9' },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }).render();
                console.log('Revenue trend chart rendered successfully');
            } else {
                console.warn('No revenue trend data available');
            }
        } catch (error) {
            console.error('Error rendering revenue trend chart:', error);
        }
    }

    // Service Breakdown Donut
    const revenueServiceEl = document.querySelector("#revenueServiceChart");
    if (revenueServiceEl) {
        try {
            const serviceData = @json($breakdown['service'] ?? collect([]));
            console.log('Service breakdown data:', serviceData);
            
            if (serviceData && serviceData.length > 0) {
                const chartData = serviceData.map(item => parseFloat(item.total_revenue || 0));
                const chartLabels = serviceData.map(item => item.service_type || 'Unknown');
                
                console.log('Service chart data:', chartData);
                console.log('Service chart labels:', chartLabels);
                
                new ApexCharts(revenueServiceEl, {
                    series: chartData,
                    chart: { 
                        type: 'donut', 
                        height: 250,
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    labels: chartLabels,
                    colors: ['#000000', '#facc15', '#eab308', '#6366f1', '#ec4899', '#8b5cf6'],
                    legend: { 
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    stroke: { width: 0 },
                    plotOptions: { 
                        pie: { 
                            donut: { 
                                size: '70%', 
                                labels: { 
                                    show: true, 
                                    total: { 
                                        show: true, 
                                        label: 'TOTAL',
                                        formatter: function(w) {
                                            const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            return '$' + total.toFixed(0);
                                        }
                                    } 
                                } 
                            } 
                        } 
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }).render();
                console.log('Service breakdown chart rendered successfully');
            } else {
                console.warn('No service breakdown data available');
            }
        } catch (error) {
            console.error('Error rendering service breakdown chart:', error);
        }
    }
});
</script>
