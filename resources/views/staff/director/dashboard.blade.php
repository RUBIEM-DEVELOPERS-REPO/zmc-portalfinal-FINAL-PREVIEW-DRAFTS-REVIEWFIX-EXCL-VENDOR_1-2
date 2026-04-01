@extends('layouts.portal')
@section('title', 'Director Media Development & Governance Dashboard')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
    
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Director Media Development and Governance Dashboard</h4>
            <div class="text-muted mt-1" style="font-size:13px;">
                <i class="ri-shield-user-line me-1"></i> 
                Director MDG Strategic Oversight &bull; Strictly Read-Only &bull; Last Updated: {{ now()->format('d M Y, H:i') }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <div class="bg-white border p-2 rounded-4 px-3 d-flex align-items-center shadow-sm">
                <div class="text-end me-3">
                    <div class="text-muted fw-bold" style="font-size:10px; text-transform: uppercase;">System Status</div>
                    <div class="text-success fw-bold small">
                        <i class="ri-checkbox-circle-fill me-1"></i>Operational
                    </div>
                </div>
                <div class="spinner-grow text-success" style="width: 8px; height: 8px;" role="status"></div>
            </div>
            <a href="{{ route('staff.director.reports.downloads') }}" class="btn btn-dark rounded-4 px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="ri-download-cloud-line"></i> Export Reports
            </a>
        </div>
    </div>

    {{-- Section 1: Executive KPIs --}}
    @include('staff.director.partials.executive')

    {{-- Section 6: Risk Panel --}}
    @include('staff.director.partials.risk_panel')

    {{-- Trends Analytics (Redistributed from IT) --}}
    @include('partials.analytics.trends')

    {{-- Tabbed Detailed Strategic Oversight --}}
    <div class="zmc-card bg-white shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <ul class="nav nav-tabs border-0 bg-light p-2 gap-2" id="directorTabs" role="tablist">
            <li class="nav-item flex-grow-1" role="presentation">
                <button class="nav-link {{ $activeTab === 'perf' ? 'active' : '' }} w-100 rounded-3 border-0 fw-bold py-3" id="perf-tab" data-bs-toggle="tab" data-bs-target="#perf" type="button">
                    <i class="ri-line-chart-line me-2"></i> Accreditation Performance
                </button>
            </li>
            <li class="nav-item flex-grow-1" role="presentation">
                <button class="nav-link {{ $activeTab === 'fin' ? 'active' : '' }} w-100 rounded-3 border-0 fw-bold py-3" id="fin-tab" data-bs-toggle="tab" data-bs-target="#fin" type="button">
                    <i class="ri-bank-card-line me-2"></i> Financial Health
                </button>
            </li>
            <li class="nav-item flex-grow-1" role="presentation">
                <button class="nav-link {{ $activeTab === 'comp' ? 'active' : '' }} w-100 rounded-3 border-0 fw-bold py-3" id="comp-tab" data-bs-toggle="tab" data-bs-target="#comp" type="button">
                    <i class="ri-shield-keyhole-line me-2"></i> Compliance & Audit
                </button>
            </li>
            <li class="nav-item flex-grow-1" role="presentation">
                <button class="nav-link {{ $activeTab === 'ops' ? 'active' : '' }} w-100 rounded-3 border-0 fw-bold py-3" id="ops-tab" data-bs-toggle="tab" data-bs-target="#ops" type="button">
                    <i class="ri-organization-chart me-2"></i> Entity & Issuance
                </button>
            </li>
        </ul>
        <div class="tab-content" id="directorTabsContent">
            {{-- Performance Tab --}}
            <div class="tab-pane fade {{ $activeTab === 'perf' ? 'show active' : '' }} p-4" id="perf" role="tabpanel">
                @include('staff.director.partials.performance')
            </div>
            {{-- Financial Tab --}}
            <div class="tab-pane fade {{ $activeTab === 'fin' ? 'show active' : '' }} p-4" id="fin" role="tabpanel">
                @include('staff.director.partials.financial')
            </div>
            {{-- Compliance Tab --}}
            <div class="tab-pane fade {{ $activeTab === 'comp' ? 'show active' : '' }} p-4" id="comp" role="tabpanel">
                @include('staff.director.partials.compliance')
            </div>
            {{-- Ops Tab --}}
            <div class="tab-pane fade {{ $activeTab === 'ops' ? 'show active' : '' }} p-4" id="ops" role="tabpanel">
                <h5 class="fw-bold mb-4">Media House & Registration Oversight</h5>
                @include('staff.director.partials.media_oversight')
                
                <hr class="my-4 opacity-5">
                
                <h5 class="fw-bold mb-4">Issuance & Printing Integrity</h5>
                @include('staff.director.partials.issuance')
            </div>
        </div>
    </div>

    {{-- Floating Strategic Oversight Footer --}}
    <div class="alert bg-white border shadow-sm rounded-4 p-4 d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4 align-items-center">
            <div class="text-center px-4 border-end">
                <div class="text-muted smaller fw-bold">SYSTEM STAMP</div>
                <div class="fw-black text-indigo" style="color: #6366f1;">ZMC-INT-V2.1</div>
            </div>
            <div class="smaller text-muted max-width-600">
                This dashboard is generated in real-time. All data is cross-referenced with the primary transaction ledger and encrypted audit logs. No operational changes can be made from this view.
            </div>
        </div>
        <div class="fw-bold text-dark opacity-50 smaller">
            <i class="ri-lock-2-line me-1"></i> DIRECTOR MDG AUTHENTICATED ACCESS ONLY
        </div>
    </div>

</div>

<script>
// Ensure tabs work properly
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded, initializing tabs...');
    
    // Initialize Bootstrap tabs manually if needed
    const triggerTabList = document.querySelectorAll('#directorTabs button[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
            console.log('Tab clicked:', triggerEl.id);
        });
    });
    
    // Log active tab
    const activeTab = document.querySelector('#directorTabs .nav-link.active');
    if (activeTab) {
        console.log('Active tab on load:', activeTab.id);
    }
});
</script>

<style>
/* Modern Typography & Transitions */

.fw-black { font-weight: 900; }
.smaller { font-size: 0.75rem; }
.tracking-wider { letter-spacing: 0.1em; }

/* Gradient Backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.nav-tabs .nav-link {
    color: #64748b;
    transition: all 0.2s ease;
}
.nav-tabs .nav-link.active {
    background-color: #fff !important;
    color: #0f172a !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.nav-tabs .nav-link:hover:not(.active) {
    background-color: #f1f5f9;
}

.zmc-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.zmc-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

/* Quick Action Cards Hover Effect */
a .zmc-card:hover {
    transform: translateY(-4px);
}
</style>
@endsection
