{{--
    KPI Card Partial Component
    
    Parameters:
    - title: Card title
    - value: Main value to display
    - icon: Remix icon class
    - color: Bootstrap color (primary, success, info, warning, danger, secondary)
    - subtitle: Optional subtitle text
    - trend: Optional trend indicator (e.g., '+12%')
    - progress: Optional progress bar value (0-100)
    - drilldown: Optional URL for drill-down navigation
--}}

<div class="col-12 col-md-3">
    <div class="kpi-card {{ isset($drilldown) ? 'clickable' : '' }}" 
         @if(isset($drilldown)) onclick="window.location='{{ $drilldown }}'" style="cursor: pointer;" @endif>
        <div class="kpi-icon bg-{{ $color }}-subtle text-{{ $color }}">
            <i class="{{ $icon }}"></i>
        </div>
        <div class="kpi-content">
            <div class="kpi-title">{{ $title }}</div>
            <div class="kpi-value">{{ $value }}</div>
            @if(isset($subtitle))
                <div class="kpi-subtitle">{{ $subtitle }}</div>
            @endif
            @if(isset($trend))
                <div class="kpi-trend {{ str_starts_with($trend, '+') ? 'text-success' : 'text-danger' }}">
                    <i class="{{ str_starts_with($trend, '+') ? 'ri-arrow-up-line' : 'ri-arrow-down-line' }}"></i> {{ $trend }}
                </div>
            @endif
            @if(isset($progress))
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $progress }}%"></div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.kpi-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s;
    display: flex;
    gap: 1rem;
    height: 100%;
}

.kpi-card.clickable:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.kpi-content {
    flex: 1;
}

.kpi-title {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.kpi-value {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.kpi-subtitle {
    font-size: 0.75rem;
    color: #9ca3af;
}

.kpi-trend {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.5rem;
}

.kpi-trend i {
    font-size: 1rem;
}
</style>
