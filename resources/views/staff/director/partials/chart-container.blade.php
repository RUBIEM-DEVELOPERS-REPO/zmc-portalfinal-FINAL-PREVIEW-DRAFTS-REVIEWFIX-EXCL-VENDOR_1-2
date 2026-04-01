{{--
    Chart Container Partial Component
    
    Parameters:
    - chartId: Unique ID for the canvas element
    - chartType: Type of chart (line, bar, pie, doughnut, etc.)
    - title: Chart title
    - height: Optional canvas height (default: 300)
    - chartData: Optional chart data (for initialization)
    - drilldown: Optional URL for drill-down navigation
--}}

@php
    $height = $height ?? 300;
@endphp

<div class="chart-container-wrapper">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ri-bar-chart-line me-2"></i>{{ $title }}
            </h5>
            @if(isset($drilldown))
                <a href="{{ $drilldown }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-external-link-line"></i> Details
                </a>
            @endif
        </div>
        <div class="card-body">
            <div class="chart-canvas-wrapper" style="position: relative; height: {{ $height }}px;">
                <canvas id="{{ $chartId }}" data-chart-type="{{ $chartType }}"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.chart-container-wrapper {
    height: 100%;
}

.chart-container-wrapper .card {
    height: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
}

.chart-container-wrapper .card-header {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
}

.chart-container-wrapper .card-header h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.chart-container-wrapper .card-body {
    padding: 1.25rem;
}

.chart-canvas-wrapper {
    width: 100%;
}

.chart-canvas-wrapper canvas {
    max-height: 100%;
}
</style>

@if(isset($chartData))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('{{ $chartId }}');
            if (ctx) {
                const chartData = @json($chartData);
                const chartType = '{{ $chartType }}';
                
                // Initialize chart with provided data
                new Chart(ctx, {
                    type: chartType,
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        @if($chartType === 'line' || $chartType === 'bar')
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                        @endif
                    }
                });
            }
        });
    </script>
    @endpush
@endif
