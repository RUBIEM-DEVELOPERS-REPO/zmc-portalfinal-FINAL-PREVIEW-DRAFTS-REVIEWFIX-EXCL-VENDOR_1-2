<div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-md-6 border-end">
            <h5 class="fw-bold mb-4">Issuance Hub (MTD)</h5>
            <div class="row text-center g-3">
                <div class="col-6">
                    <div class="p-3 bg-success-subtle rounded-4 h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total Issued</div>
                        <div class="h2 fw-black mb-0 text-success">{{ number_format($issuance['total_issued'] ?? 0) }}</div>
                        <div class="smaller text-muted mt-1">This month</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 {{ ($issuance['unprinted_approvals'] ?? 0) > 0 ? 'bg-warning-subtle' : 'bg-light' }} rounded-4 h-100 border-start border-4 {{ ($issuance['unprinted_approvals'] ?? 0) > 0 ? 'border-warning' : 'border-success' }}">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Pending Print</div>
                        <div class="h2 fw-black mb-0 {{ ($issuance['unprinted_approvals'] ?? 0) > 0 ? 'text-warning' : 'text-success' }}">
                            {{ number_format($issuance['unprinted_approvals'] ?? 0) }}
                        </div>
                        <div class="smaller text-muted mt-1">
                            @if(($issuance['unprinted_approvals'] ?? 0) > 0)
                                Requires attention
                            @else
                                All printed
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if(($issuance['unprinted_approvals'] ?? 0) > 10)
                <div class="alert alert-warning border-0 rounded-4 mt-3 mb-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-alert-line h5 mb-0"></i>
                        <div class="smaller">
                            <strong>Print Queue Alert:</strong> {{ $issuance['unprinted_approvals'] }} approved applications awaiting print
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-6">
            <h5 class="fw-bold mb-4 px-3">Print Quality & Waste</h5>
            
            @php
                $totalPrints = ($issuance['prints_vs_reprints']['prints'] ?? 0);
                $totalReprints = ($issuance['prints_vs_reprints']['reprints'] ?? 0);
                $grandTotal = $totalPrints + $totalReprints;
                $reprintRate = $grandTotal > 0 ? round(($totalReprints / $grandTotal) * 100, 1) : 0;
            @endphp
            
            @if($grandTotal > 0)
                <div id="printWasteChart" style="min-height: 200px;"></div>
                
                <div class="row text-center mt-3 g-2">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <div class="smaller text-muted">First Print</div>
                            <div class="h5 fw-black mb-0 text-success">{{ number_format($totalPrints) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <div class="smaller text-muted">Reprints</div>
                            <div class="h5 fw-black mb-0 text-danger">{{ number_format($totalReprints) }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 p-3 {{ $reprintRate > 15 ? 'bg-danger-subtle' : ($reprintRate > 10 ? 'bg-warning-subtle' : 'bg-success-subtle') }} rounded-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small fw-bold {{ $reprintRate > 15 ? 'text-danger' : ($reprintRate > 10 ? 'text-warning' : 'text-success') }}">
                            Reprint Rate
                        </span>
                        <span class="h4 fw-black mb-0 {{ $reprintRate > 15 ? 'text-danger' : ($reprintRate > 10 ? 'text-warning' : 'text-success') }}">
                            {{ $reprintRate }}%
                        </span>
                    </div>
                    <div class="smaller text-muted mt-1">
                        @if($reprintRate > 15)
                            <i class="ri-error-warning-line"></i> High waste - investigation recommended
                        @elseif($reprintRate > 10)
                            <i class="ri-alert-line"></i> Moderate waste - monitor closely
                        @else
                            <i class="ri-checkbox-circle-line"></i> Within acceptable range
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="ri-printer-line" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="mt-3 mb-0">No print activity recorded this month</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Issuance tab loaded');
    console.log('Issuance data:', @json($issuance ?? []));
    
    @if($grandTotal > 0)
        if (typeof ApexCharts !== 'undefined') {
            const printWasteEl = document.querySelector("#printWasteChart");
            if (printWasteEl) {
                try {
                    const prints = {{ $totalPrints }};
                    const reprints = {{ $totalReprints }};
                    
                    console.log('Rendering print waste chart - Prints:', prints, 'Reprints:', reprints);
                    
                    new ApexCharts(printWasteEl, {
                        series: [prints, reprints],
                        chart: { 
                            type: 'donut', 
                            height: 200,
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800
                            }
                        },
                        labels: ['Standard Issue', 'Reprints/Errors'],
                        colors: ['#facc15', '#ef4444'],
                        legend: { 
                            position: 'bottom',
                            fontSize: '12px'
                        },
                        stroke: { width: 0 },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'TOTAL',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return val.toFixed(1) + '%';
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value + ' prints';
                                }
                            }
                        }
                    }).render();
                    
                    console.log('Print waste chart rendered successfully');
                } catch (error) {
                    console.error('Error rendering print waste chart:', error);
                }
            }
        } else {
            console.error('ApexCharts not loaded');
        }
    @endif
});
</script>
