<div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-md-8 border-end">
            <h5 class="fw-bold mb-4">Accreditation Trends (12 Months)</h5>
            <div id="accreditationTrendChart" style="min-height: 350px;"></div>
        </div>
        <div class="col-md-4">
            <h5 class="fw-bold mb-4 px-3">Approval Ratios</h5>
            <div class="px-3 mb-4">
                <div class="text-muted small fw-bold text-uppercase mb-1">Media Practitioner Accreditation</div>
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="h3 fw-black mb-0 text-primary">{{ $ratio['journalist'] }}%</span>
                    <span class="text-muted smaller">Approved / Decided</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 5px; background: #f1f5f9;">
                    <div class="progress-bar bg-primary" style="width: {{ $ratio['journalist'] }}%"></div>
                </div>
            </div>

            <div class="px-3">
                <div class="text-muted small fw-bold text-uppercase mb-1">Media House Registration</div>
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="h3 fw-black mb-0 text-indigo" style="color:#6366f1;">{{ $ratio['mass_media'] }}%</span>
                    <span class="text-muted smaller">Approved / Decided</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 5px; background: #f1f5f9;">
                    <div class="progress-bar" style="width: {{ $ratio['mass_media'] }}%; background-color:#6366f1;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4">
    <h5 class="fw-bold mb-4">Category Distribution</h5>
    <div class="row">
        @foreach($categories as $cat)
            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded-4 text-center h-100">
                    <div class="text-muted small fw-bold text-uppercase mb-1">{{ $cat->accreditation_category_code }}</div>
                    <div class="h3 fw-black mb-0">{{ number_format($cat->count) }}</div>
                    <div class="text-muted smaller">Valid Accreditations</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('ApexCharts loaded:', typeof ApexCharts !== 'undefined');
    console.log('Monthly trends data:', {!! json_encode($monthlyTrends) !!});
    
    const chartElement = document.querySelector("#accreditationTrendChart");
    if (chartElement && typeof ApexCharts !== 'undefined') {
        var options = {
            series: [{
                name: 'Submitted',
                data: {!! json_encode($monthlyTrends->pluck('submitted')->toArray()) !!}
            }, {
                name: 'Approved',
                data: {!! json_encode($monthlyTrends->pluck('approved')->toArray()) !!}
            }, {
                name: 'Returned for Correction',
                data: {!! json_encode($monthlyTrends->pluck('rejected')->toArray()) !!}
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#000000', '#facc15', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: {!! json_encode($monthlyTrends->pluck('month')->toArray()) !!}
            },
            tooltip: {
                x: { format: 'yyyy-MM' }
            }
        };

        var chart = new ApexCharts(chartElement, options);
        chart.render();
    } else {
        console.error('ApexCharts not loaded or element not found');
    }
});
</script>
