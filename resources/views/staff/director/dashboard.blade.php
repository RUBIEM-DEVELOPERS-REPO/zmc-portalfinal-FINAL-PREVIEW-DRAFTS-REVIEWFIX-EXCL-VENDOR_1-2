@extends('layouts.portal')
@section('title', 'Director Strategy Dashboard')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Executive Intelligence Dashboard</h4>
            <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
                <i class="ri-shield-user-line me-1"></i> 
                Director/CEO Strategic Control Panel &bull; Strictly Read-Only Oversight &bull; Last Updated: {{ now()->format('d M Y, H:i') }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <div class="bg-white border p-2 rounded-4 px-3 d-flex align-items-center shadow-sm">
                <div class="text-end me-3">
                    <div class="text-muted fw-bold" style="font-size: var(--font-size-sm); text-transform: uppercase;">System Status</div>
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

    {{-- Section 1: Executive KPIs (11 Cards) --}}
    <div class="row g-3 mb-4">
        {{-- KPI 1: Total Active Accreditations --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Total Active Accreditations',
            'value' => number_format($kpis['total_active_accreditations']),
            'icon' => 'ri-shield-check-line',
            'color' => 'primary',
            'subtitle' => 'Currently valid credentials'
        ])

        {{-- KPI 2: Issued This Month --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Issued This Month',
            'value' => number_format($kpis['issued_this_month']),
            'icon' => 'ri-calendar-event-line',
            'color' => 'success',
            'subtitle' => 'MTD issuances',
            'trend' => $kpis['issued_this_month'] > 0 ? 'up' : null
        ])

        {{-- KPI 3: Issued This Year --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Issued This Year',
            'value' => number_format($kpis['issued_this_year']),
            'icon' => 'ri-calendar-check-line',
            'color' => 'success',
            'subtitle' => 'YTD issuances'
        ])

        {{-- KPI 4: Revenue MTD --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Revenue (MTD)',
            'value' => '$' . number_format($kpis['revenue_mtd']),
            'icon' => 'ri-money-dollar-circle-line',
            'color' => 'info',
            'subtitle' => 'Confirmed payments'
        ])
    </div>

    <div class="row g-3 mb-4">
        {{-- KPI 5: Revenue YTD --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Revenue (YTD)',
            'value' => '$' . number_format($kpis['revenue_ytd']),
            'icon' => 'ri-bank-card-line',
            'color' => 'info',
            'subtitle' => 'Year-to-date revenue'
        ])

        {{-- KPI 6: Outstanding Revenue --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Outstanding Revenue',
            'value' => number_format($kpis['outstanding_revenue']),
            'icon' => 'ri-time-line',
            'color' => 'warning',
            'subtitle' => 'Awaiting verification'
        ])

        {{-- KPI 7: Applications in Pipeline --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Applications in Pipeline',
            'value' => number_format($kpis['applications_in_pipeline']),
            'icon' => 'ri-file-list-3-line',
            'color' => 'secondary',
            'subtitle' => 'Awaiting final decision'
        ])

        {{-- KPI 8: Avg Processing Time --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Avg. Processing Time',
            'value' => $kpis['avg_processing_time'] . ' days',
            'icon' => 'ri-timer-line',
            'color' => 'primary',
            'subtitle' => 'Submission to issuance'
        ])
    </div>

    <div class="row g-3 mb-4">
        {{-- KPI 9: Approval Rate --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Approval Rate',
            'value' => $kpis['approval_rate'] . '%',
            'icon' => 'ri-checkbox-circle-line',
            'color' => 'success',
            'subtitle' => 'Overall approval ratio',
            'progress' => $kpis['approval_rate']
        ])

        {{-- KPI 10: Active Compliance Flags --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Active Compliance Flags',
            'value' => number_format($kpis['active_compliance_flags']),
            'icon' => 'ri-alert-line',
            'color' => $kpis['active_compliance_flags'] > 10 ? 'danger' : 'warning',
            'subtitle' => 'Requiring attention'
        ])

        {{-- KPI 11: Total Media Houses --}}
        @include('staff.director.partials.kpi-card', [
            'title' => 'Media Houses Registered',
            'value' => number_format($kpis['total_media_houses']),
            'icon' => 'ri-building-line',
            'color' => 'primary',
            'subtitle' => 'Active entities'
        ])
    </div>

    {{-- Section 2: Strategic Risk Indicators (7 Indicators) --}}
    <div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Strategic Risk Indicators</h5>
                <div class="text-muted small">Real-time monitoring of operational thresholds</div>
            </div>
            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                <i class="ri-error-warning-line me-1"></i> Flagged Items
            </span>
        </div>

        <div class="row g-3">
            {{-- Risk 1: Excessive Waivers --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Excessive Waivers',
                'value' => $riskIndicators['excessive_waivers']['value'],
                'level' => $riskIndicators['excessive_waivers']['level'],
                'icon' => 'ri-file-reduce-line',
                'threshold' => 15
            ])

            {{-- Risk 2: High Rejection Spike --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Rejection Spike',
                'value' => $riskIndicators['high_rejection_spike']['value'],
                'level' => $riskIndicators['high_rejection_spike']['level'],
                'icon' => 'ri-close-circle-line',
                'threshold' => 30
            ])

            {{-- Risk 3: Processing Time SLA --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Processing Time SLA',
                'value' => $riskIndicators['processing_time_sla']['value'],
                'level' => $riskIndicators['processing_time_sla']['level'],
                'icon' => 'ri-timer-flash-line',
                'threshold' => 15
            ])

            {{-- Risk 4: Revenue Drop --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Revenue Drop',
                'value' => $riskIndicators['revenue_drop']['value'],
                'level' => $riskIndicators['revenue_drop']['level'],
                'icon' => 'ri-line-chart-line',
                'threshold' => 20
            ])
        </div>

        <div class="row g-3 mt-2">
            {{-- Risk 5: Reprint Frequency --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Reprint Frequency',
                'value' => $riskIndicators['reprint_frequency']['value'],
                'level' => $riskIndicators['reprint_frequency']['level'],
                'icon' => 'ri-printer-line',
                'threshold' => 20
            ])

            {{-- Risk 6: Category Reassignments --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Category Reassignments',
                'value' => $riskIndicators['category_reassignment_trend']['value'],
                'level' => $riskIndicators['category_reassignment_trend']['level'],
                'icon' => 'ri-shuffle-line',
                'threshold' => 10
            ])

            {{-- Risk 7: Payment Verification Delays --}}
            @include('staff.director.partials.risk-indicator', [
                'title' => 'Payment Delays',
                'value' => $riskIndicators['payment_verification_delay']['value'],
                'level' => $riskIndicators['payment_verification_delay']['level'],
                'icon' => 'ri-time-line',
                'threshold' => 5
            ])
        </div>
    </div>

    {{-- Section 3: Recent High-Risk Activity --}}
    <div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Recent High-Risk Activity</h5>
                <div class="text-muted small">Last 5 flagged actions requiring oversight</div>
            </div>
            <button class="btn btn-sm btn-outline-primary rounded-3" onclick="refreshHighRiskActivity()">
                <i class="ri-refresh-line me-1"></i> Refresh
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="highRiskActivityTable">
                <thead class="bg-light">
                    <tr>
                        <th class="fw-bold small text-uppercase">Timestamp</th>
                        <th class="fw-bold small text-uppercase">Action Type</th>
                        <th class="fw-bold small text-uppercase">Staff Member</th>
                        <th class="fw-bold small text-uppercase">Application ID</th>
                        <th class="fw-bold small text-uppercase">Details</th>
                        <th class="fw-bold small text-uppercase">Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($highRiskActivity as $activity)
                    <tr>
                        <td class="small">{{ \Carbon\Carbon::parse($activity->created_at)->format('d M Y, H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $activity->action_type === 'category_reassignment' ? 'warning' : ($activity->action_type === 'manual_override' ? 'danger' : 'info') }} bg-opacity-10 text-{{ $activity->action_type === 'category_reassignment' ? 'warning' : ($activity->action_type === 'manual_override' ? 'danger' : 'info') }}">
                                {{ ucwords(str_replace('_', ' ', $activity->action_type)) }}
                            </span>
                        </td>
                        <td class="small fw-medium">{{ $activity->staff_name ?? 'System' }}</td>
                        <td class="small font-monospace">{{ $activity->application_id ?? 'N/A' }}</td>
                        <td class="small text-muted">{{ Str::limit($activity->details ?? 'No details', 50) }}</td>
                        <td>
                            <span class="badge bg-{{ $activity->risk_level === 'high' ? 'danger' : ($activity->risk_level === 'medium' ? 'warning' : 'info') }}">
                                {{ ucfirst($activity->risk_level ?? 'low') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="ri-checkbox-circle-line h3 d-block mb-2 opacity-50"></i>
                            No high-risk activity detected
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabbed Detailed Intelligence --}}
    <div class="zmc-card bg-white shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <ul class="nav nav-tabs border-0 bg-light p-2 gap-2" id="ceoTabs" role="tablist">
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
        <div class="tab-content" id="ceoTabsContent">
            {{-- Performance Tab --}}
            <div class="tab-pane fade {{ $activeTab === 'perf' ? 'show active' : '' }} p-4" id="perf" role="tabpanel">
                @include('staff.director.partials.performance')
                <hr class="my-5 opacity-5">
                @include('staff.director.partials.staff_performance')
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

    {{-- Floating Intelligence Footer --}}
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
            <i class="ri-lock-2-line me-1"></i> CEO AUTHENTICATED ACCESS ONLY
        </div>
    </div>

</div>

<script>
// Ensure tabs work properly
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded, initializing tabs...');
    
    // Initialize Bootstrap tabs manually if needed
    const triggerTabList = document.querySelectorAll('#ceoTabs button[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
            console.log('Tab clicked:', triggerEl.id);
        });
    });
    
    // Log active tab
    const activeTab = document.querySelector('#ceoTabs .nav-link.active');
    if (activeTab) {
        console.log('Active tab on load:', activeTab.id);
    }

    // Initialize AJAX polling for real-time KPI updates
    startKPIPolling();
});

// AJAX Polling for Real-Time KPI Updates
let kpiPollingInterval = null;

function startKPIPolling() {
    // Poll every 30 seconds for KPI updates
    kpiPollingInterval = setInterval(function() {
        refreshKPIs();
    }, 30000);
    
    console.log('KPI polling started (30s interval)');
}

function refreshKPIs() {
    fetch('{{ route("staff.director.dashboard") }}?ajax=1', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.kpis) {
            updateKPIValues(data.kpis);
            console.log('KPIs updated:', new Date().toLocaleTimeString());
        }
        if (data.riskIndicators) {
            updateRiskIndicators(data.riskIndicators);
        }
    })
    .catch(error => {
        console.error('Error refreshing KPIs:', error);
    });
}

function updateKPIValues(kpis) {
    // Update KPI card values dynamically
    const kpiMapping = {
        'total_active_accreditations': 'Total Active Accreditations',
        'issued_this_month': 'Issued This Month',
        'issued_this_year': 'Issued This Year',
        'revenue_mtd': 'Revenue (MTD)',
        'revenue_ytd': 'Revenue (YTD)',
        'outstanding_revenue': 'Outstanding Revenue',
        'applications_in_pipeline': 'Applications in Pipeline',
        'avg_processing_time': 'Avg. Processing Time',
        'approval_rate': 'Approval Rate',
        'active_compliance_flags': 'Active Compliance Flags',
        'total_media_houses': 'Media Houses Registered'
    };

    Object.keys(kpiMapping).forEach(key => {
        if (kpis[key] !== undefined) {
            const cards = document.querySelectorAll('.zmc-card');
            cards.forEach(card => {
                const title = card.querySelector('.text-uppercase');
                if (title && title.textContent.includes(kpiMapping[key])) {
                    const valueElement = card.querySelector('.fw-black');
                    if (valueElement) {
                        let newValue = kpis[key];
                        
                        // Format based on KPI type
                        if (key.includes('revenue')) {
                            newValue = '$' + Number(newValue).toLocaleString();
                        } else if (key === 'avg_processing_time') {
                            newValue = newValue + ' days';
                        } else if (key === 'approval_rate') {
                            newValue = newValue + '%';
                            // Update progress bar if exists
                            const progressBar = card.querySelector('.progress-bar');
                            if (progressBar) {
                                progressBar.style.width = newValue;
                            }
                        } else {
                            newValue = Number(newValue).toLocaleString();
                        }
                        
                        valueElement.textContent = newValue;
                    }
                }
            });
        }
    });
}

function updateRiskIndicators(indicators) {
    // Update risk indicator levels and values
    Object.keys(indicators).forEach(key => {
        const indicator = indicators[key];
        // Find and update risk indicator cards based on their titles
        // This would require more specific selectors based on the actual rendered HTML
        console.log('Risk indicator update:', key, indicator);
    });
}

function refreshHighRiskActivity() {
    const table = document.getElementById('highRiskActivityTable');
    const tbody = table.querySelector('tbody');
    
    // Show loading state
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';
    
    fetch('{{ route("staff.director.dashboard") }}?ajax=1&section=highRiskActivity', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.highRiskActivity) {
            updateHighRiskActivityTable(data.highRiskActivity);
        }
    })
    .catch(error => {
        console.error('Error refreshing high-risk activity:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading data</td></tr>';
    });
}

function updateHighRiskActivityTable(activities) {
    const tbody = document.querySelector('#highRiskActivityTable tbody');
    
    if (activities.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="ri-checkbox-circle-line h3 d-block mb-2 opacity-50"></i>
                    No high-risk activity detected
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    activities.forEach(activity => {
        const actionBadgeColor = activity.action_type === 'category_reassignment' ? 'warning' : 
                                 (activity.action_type === 'manual_override' ? 'danger' : 'info');
        const riskBadgeColor = activity.risk_level === 'high' ? 'danger' : 
                              (activity.risk_level === 'medium' ? 'warning' : 'info');
        
        html += `
            <tr>
                <td class="small">${formatDate(activity.created_at)}</td>
                <td>
                    <span class="badge bg-${actionBadgeColor} bg-opacity-10 text-${actionBadgeColor}">
                        ${formatActionType(activity.action_type)}
                    </span>
                </td>
                <td class="small fw-medium">${activity.staff_name || 'System'}</td>
                <td class="small font-monospace">${activity.application_id || 'N/A'}</td>
                <td class="small text-muted">${truncate(activity.details || 'No details', 50)}</td>
                <td>
                    <span class="badge bg-${riskBadgeColor}">
                        ${capitalize(activity.risk_level || 'low')}
                    </span>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Helper functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + 
           ', ' + date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
}

function formatActionType(type) {
    return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function truncate(str, length) {
    return str.length > length ? str.substring(0, length) + '...' : str;
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Stop polling when page is hidden (performance optimization)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (kpiPollingInterval) {
            clearInterval(kpiPollingInterval);
            console.log('KPI polling paused (page hidden)');
        }
    } else {
        startKPIPolling();
        console.log('KPI polling resumed (page visible)');
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
    background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
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
    border-bottom: 3px solid transparent;
}
.nav-tabs .nav-link.active {
    background-color: #fff !important;
    color: #000 !important;
    border-bottom: 3px solid #facc15 !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.nav-tabs .nav-link:hover:not(.active) {
    background-color: #f1f5f9;
    border-bottom: 3px solid #eab308;
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
