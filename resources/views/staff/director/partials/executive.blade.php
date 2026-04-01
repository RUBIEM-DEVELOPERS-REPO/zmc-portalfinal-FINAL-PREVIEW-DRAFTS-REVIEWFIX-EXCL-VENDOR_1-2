<div class="row g-3 mb-4">
    {{-- Issued This Month / Year --}}
    <div class="col-12 col-md-4">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Issued (MTD / YTD)</div>
                    <div class="h2 fw-black mb-0 mt-1 text-dark">
                        {{ number_format($kpis['issued_this_month']) }} <span class="text-muted h4 fw-medium">/ {{ number_format($kpis['issued_this_year']) }}</span>
                    </div>
                </div>
                <div class="icon-box bg-success-subtle text-success rounded-circle p-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="ri-calendar-event-line h4 mb-0"></i>
                </div>
            </div>
            <div class="mt-3 smaller text-success fw-bold"><i class="ri-arrow-up-line me-1"></i> Strategic growth trend</div>
        </div>
    </div>

    {{-- Revenue Collected (MTD / YTD) --}}
    <div class="col-12 col-md-4">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Revenue (MTD / YTD)</div>
                    <div class="h3 fw-black mb-0 mt-1 text-dark">
                        ${{ number_format($kpis['revenue_mtd']) }} <span class="text-muted h5 fw-medium">/ ${{ number_format($kpis['revenue_ytd']) }}</span>
                    </div>
                </div>
                <div class="icon-box bg-info-subtle text-info rounded-circle p-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="ri-money-dollar-circle-line h4 mb-0"></i>
                </div>
            </div>
            <div class="mt-3 smaller text-info fw-bold">Confirmed payments only</div>
        </div>
    </div>

    {{-- Outstanding Revenue --}}
    <div class="col-12 col-md-4">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Outstanding Revenue</div>
                    <div class="h2 fw-black mb-0 mt-1 text-dark">{{ number_format($kpis['outstanding_revenue']) }}</div>
                </div>
                <div class="icon-box bg-warning-subtle text-warning rounded-circle p-2" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="ri-time-line h4 mb-0"></i>
                </div>
            </div>
            <div class="mt-3 smaller text-muted">Awaiting accounts/verification</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Pipeline Volume --}}
    <div class="col-12 col-md-3">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Applications in Pipeline</div>
            <div class="h2 fw-black mb-0 mt-1 text-dark">{{ number_format($kpis['applications_in_pipeline']) }}</div>
            <div class="mt-3 smaller text-muted">Awaiting final decision</div>
        </div>
    </div>

    {{-- Avg Processing Time --}}
    <div class="col-12 col-md-3">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Avg. Processing Time</div>
            <div class="h2 fw-black mb-0 mt-1 text-dark">{{ $kpis['avg_processing_time'] }} <span class="h4 text-muted">days</span></div>
            <div class="mt-3 smaller text-muted">Submission to issuance</div>
        </div>
    </div>

    {{-- Approval Rate --}}
    <div class="col-12 col-md-3">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4">
                <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Approval Rate</div>
                <div class="h2 fw-black mb-0 mt-1 text-dark">{{ $kpis['approval_rate'] }}%</div>
                <div class="progress mt-3" style="height: 6px; background-color: #f1f5f9;">
                    <div class="progress-bar bg-success" style="width: {{ $kpis['approval_rate'] }}%"></div>
                </div>
        </div>
    </div>

    {{-- Media Houses --}}
    <div class="col-12 col-md-3">
        <div class="zmc-card h-100 bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="text-muted small fw-bold text-uppercase tracking-wider" style="font-size: var(--font-size-sm);">Media Houses Registered</div>
            <div class="h2 fw-black mb-0 mt-1 text-dark">{{ number_format($kpis['total_media_houses']) }}</div>
            <div class="mt-3 smaller text-muted">Active entities</div>
        </div>
    </div>
</div>
