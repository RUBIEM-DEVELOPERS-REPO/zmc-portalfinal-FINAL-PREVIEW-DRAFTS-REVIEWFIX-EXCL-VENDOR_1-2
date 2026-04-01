@extends('layouts.portal')

@section('title', 'IT Dashboard')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">IT Dashboard</h2>
            <p class="text-slate-600 m-0 small mt-1">Unified System Administration & Monitoring</p>
        </div>
        <div class="d-flex gap-2">
            <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-flex align-items-center">
                <span class="pulse-dot me-2"></span> System Online
            </div>
            <div class="badge bg-slate-100 text-slate-600 border border-slate-200 px-3 py-2 rounded-pill">
                v2.4.0-Onyx
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="dashboard-nav-container mb-4 overflow-x-auto">
        <div class="nav nav-pills gap-2 flex-nowrap pb-2" id="itDashboardTabs" role="tablist">
            @php
                $tabs = [
                    'overview' => ['icon' => 'ri-dashboard-3-line', 'label' => 'Overview'],
                    'drafts' => ['icon' => 'ri-draft-line', 'label' => 'Drafts'],
                    'files' => ['icon' => 'ri-file-cloud-line', 'label' => 'Files'],
                    'errors' => ['icon' => 'ri-bug-line', 'label' => 'Logs & Errors'],
                    'security' => ['icon' => 'ri-shield-flash-line', 'label' => 'Security'],
                    'backup' => ['icon' => 'ri-database-2-line', 'label' => 'Backup'],
                    'audit' => ['icon' => 'ri-history-line', 'label' => 'Audit'],
                    'system' => ['icon' => 'ri-settings-5-line', 'label' => 'System'],
                ];
                
            @foreach($tabs as $key => $tab)
            <button class="nav-link @if($key === 'overview') active @endif rounded-3 border-0 px-3 py-2 d-flex align-items-center gap-2 whitespace-nowrap" 
                    id="tab-{{ $key }}" data-bs-toggle="pill" data-bs-target="#panel-{{ $key }}" type="button" role="tab"
                    style="font-size: 13px; font-weight: 500; transition: all 0.2s ease;">
                <i class="{{ $tab['icon'] }} fs-5"></i>
                <span>{{ $tab['label'] }}</span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Content Panels -->
    <div class="tab-content border-0" id="itDashboardContent">
        
        <!-- 1. System Overview Section -->
        <div class="tab-pane fade show active" id="panel-overview" role="tabpanel">
            <div class="row g-4">
                <!-- Status Grid -->
                <div class="col-xl-9">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="stats-card p-4 rounded-4 bg-white shadow-sm border border-slate-100">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="icon-box bg-primary-subtle text-primary p-2 rounded-3"><i class="ri-user-line fs-4"></i></div>
                                    <span class="text-success small fw-bold">+12% <i class="ri-arrow-right-up-line"></i></span>
                                </div>
                                <h3 class="fw-bold mb-1" style="color: #0f172a;">{{ number_format($stats['total_users']) }}</h3>
                                <p class="text-slate-600 small m-0 fw-medium">Total Registered Users</p>
                            </div>
                        </div>
                        <!-- REMOVED: Active Submissions & Approval Efficiency (Operational) -->
                    </div>

                    <!-- REMOVED: Trends Chart (Operational - Redistributed) -->

                    {{-- Admin Management Section (Unified from Dashboard) --}}
                        <!-- REMOVED: Regions Management (Operational) -->

                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-2">
                                <div class="card-header bg-white p-4 border-0">
                                    <h6 class="fw-bold m-0">Pending Staff Approvals</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 small">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="ps-4">Requesting User</th>
                                                <th>Roles</th>
                                                <th>Created</th>
                                                <th class="text-end pe-4">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pending as $p)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-slate-700">{{ $p->name }}</div>
                                                    <div class="text-slate-600 small fw-bold">{{ $p->email }}</div>
                                                </td>
                                                <td>
                                                    @foreach($p->roles as $role)
                                                    <span class="badge bg-slate-100 text-slate-600 border rounded-pill">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="text-slate-700 fw-bold">{{ $p->created_at->diffForHumans() }}</td>
                                                <td class="text-end pe-4">
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Awaiting Approval</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-slate-700 fw-bold small">No pending staff approvals at this time.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($pending->hasPages())
                                <div class="card-footer bg-white border-0 px-4 py-3">
                                    {{ $pending->links() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-2 mb-4">
                        <!-- Key Metrics Table -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
                                <div class="card-header bg-white border-0 py-3">
                                    <h6 class="fw-bold m-0">Processing Performance</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush border-0">
                                        <!-- REMOVED: Operational Stats -->
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-slate-50 rounded-circle"><i class="ri-server-line text-slate-600"></i></div>
                                                <span class="fw-bold text-slate-700">System Uptime</span>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <span class="badge rounded-pill {{ ($health['database'] ?? false) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">DB</span>
                                                <span class="badge rounded-pill {{ ($health['storage'] ?? false) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">Disk</span>
                                                <span class="badge rounded-pill {{ ($health['queue'] ?? false) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">Jobs</span>
                                            </div>
                                        </div>
                                        <!-- REMOVED: Application Status Distribution -->
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-slate-50 rounded-circle"><i class="ri-database-line text-slate-600"></i></div>
                                                <span class="fw-bold text-slate-700">Resource Saturation</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-3" style="min-width: 120px;">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" style="width: 42%"></div>
                                                </div>
                                                <span class="small fw-bold text-slate-700">42%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- REMOVED: Payment Reconciliation (Finance Responsibility) -->
                    </div>
                </div>

                <!-- Right Sidebar Widgets -->
                <div class="col-xl-3">

                    <!-- Storage Overview -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="ri-hard-drive-2-line text-primary"></i> Upload Storage
                        </h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-slate-600">Total Document Volume</span>
                                <span class="fw-bold text-slate-800">{{ $storageUsageBytes ? number_format($storageUsageBytes/1024/1024, 1) . ' MB' : 'N/A' }}</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 10px; background: #f1f5f9;">
                                @php
                                    $storagePercent = ($storageUsage['total'] > 0) ? ($storageUsage['used'] / $storageUsage['total']) * 100 : 0;
                                @endphp
                                <div class="progress-bar rounded-pill bg-primary" style="width: {{ $storagePercent }}%"></div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach($storageByModule as $mod)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-slate-50 border border-slate-100">
                                <span class="small text-slate-600">{{ ucfirst(str_replace('_', ' ', $mod->doc_type)) }}</span>
                                <span class="small fw-bold text-slate-900">{{ number_format($mod->total/1024/1024, 1) }} MB</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Navigation Links (Unified from Dashboard) -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h6 class="fw-bold mb-3">Quick Navigation</h6>
                        <div class="d-grid gap-2">
                            {{-- REMOVED: Applicant Management (Operational Oversight) --}}
                            <a href="{{ route('admin.content.index') }}" class="btn btn-slate-50 text-slate-700 btn-sm fw-bold border text-start px-3 py-2 d-flex align-items-center gap-2">
                                <i class="ri-article-line text-purple"></i> CMS Management
                            </a>
                            <a href="{{ route('admin.audit.index') }}" class="btn btn-slate-50 text-slate-700 btn-sm fw-bold border text-start px-3 py-2 d-flex align-items-center gap-2">
                                <i class="ri-shield-check-line text-danger"></i> System Audit Logs
                            </a>
                            <!-- REMOVED: Performance Analytics (Operational) -->
                        </div>
                    </div>

                    <!-- Security Pulse -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="ri-shield-check-line text-success"></i> Security Status
                        </h6>
                        <ul class="list-unstyled m-0">
                            <li class="d-flex align-items-center gap-3 mb-3">
                                <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                                <div class="small">
                                    <div class="fw-bold text-slate-900">SSL Certificate</div>
                                    <div class="text-slate-600 font-weight-bold">Active (Verified)</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center gap-3 mb-3">
                                <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                                <div class="small">
                                    <div class="fw-bold text-slate-900">WAF Policy</div>
                                    <div class="text-slate-600 font-weight-bold">Strict Mode Enabled</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center gap-3">
                                <div class="spinner-grow spinner-grow-sm text-warning me-1"></div>
                                <div class="small">
                                    <div class="fw-bold text-slate-900">Session Monitor</div>
                                    <div class="text-slate-600 font-weight-bold">{{ count($activeSessions) }} active sessions detected</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitoring Section -->
        <div class="tab-pane fade" id="panel-monitoring" role="tabpanel">
             @include('staff.it.dashboard.partials.monitoring', ['query' => $monitoringQuery])
        </div>

        <!-- Draft Section -->
        <div class="tab-pane fade" id="panel-drafts" role="tabpanel">
             @include('staff.it.dashboard.partials.drafts', ['drafts' => $draftsQuery])
        </div>


        <!-- Errors Section -->
        <div class="tab-pane fade" id="panel-errors" role="tabpanel">
             @include('staff.it.dashboard.partials.errors', ['logs' => $errorLogs])
        </div>




                        {{-- REMOVED: Payments Panel (Finance Responsibility) --}}

        <!-- Files Section -->
        <div class="tab-pane fade" id="panel-files" role="tabpanel">
             @include('staff.it.dashboard.partials.files', ['files' => $filesQuery, 'storageStats' => $storageStats])
        </div>

        <!-- Security Section -->
        <div class="tab-pane fade" id="panel-security" role="tabpanel">
             @include('staff.it.dashboard.partials.security', ['sessions' => $activeSessions])
        </div>

        <!-- Backup Section -->
        <div class="tab-pane fade" id="panel-backup" role="tabpanel">
             @include('staff.it.dashboard.partials.backup', ['lastBackup' => $lastBackup ?? 'N/A', 'storageStats' => $storageStats])
        </div>

        <!-- Audit Section -->
        <div class="tab-pane fade" id="panel-audit" role="tabpanel">
             @include('staff.it.dashboard.partials.audit', ['logs' => $auditLogs])
        </div>

        <!-- System Section -->
        <div class="tab-pane fade" id="panel-system" role="tabpanel">
             @include('staff.it.dashboard.partials.system', ['env' => $systemEnv])
        </div>


                        {{-- REMOVED: Reports Panel (Operational Responsibility) --}}


    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    .it-dashboard .nav-pills .nav-link {
        color: #64748b;
        background: #fff;
        border: 1px solid #e2e8f0 !important;
        white-space: nowrap;
    }
    
    .it-dashboard .nav-pills .nav-link.active {
        background: #0f172a !important;
        color: #fff !important;
        border-color: #0f172a !important;
    }
    
    .it-dashboard .stats-card:hover {
        transform: translateY(-4px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-color: #cbd5e1 !important;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .list-group-item:last-child {
        border-bottom: 0 !important;
    }

    .bg-purple-subtle { background: #f3e8ff; color: #a855f7; }
    .bg-orange-subtle { background: #fff7ed; color: #f97316; }
    .bg-success-subtle { background: #f0fdf4; color: #22c55e; }
    
    .letter-spacing-1 { letter-spacing: 0.1em; }
    .uppercase { text-transform: uppercase; }

    /* Custom scrollbar for tabs */
    .dashboard-nav-container::-webkit-scrollbar {
        height: 4px;
    }
    .dashboard-nav-container::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    /* Reduce pagination arrow size */
    nav[role="navigation"] svg {
        width: 14px !important;
        height: 14px !important;
    }
    .pagination svg {
        width: 14px !important;
        height: 14px !important;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
        // mini Sparkline for payment health - REMOVED (Not required for IT)
    });
</script>
@endpush
  {{-- New Region Modal (Unified from Dashboard) --}}
  <div class="modal fade" id="addRegionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <form action="{{ route('staff.it.regions.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
        @csrf
        <div class="modal-header border-0 p-4 pb-2">
          <h5 class="modal-title fw-bold">Create New Administrative Region</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold text-slate-600">Region Name</label>
            <input type="text" name="name" class="form-control rounded-3 border-slate-200" placeholder="e.g. Harare North" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-slate-600">Internal Code</label>
            <input type="text" name="code" class="form-control rounded-3 border-slate-200" placeholder="e.g. harare_n" required>
          </div>
          <div class="mb-0">
            <label class="form-label small fw-bold text-slate-600">Expiry Date (Optional)</label>
            <input type="date" name="expires_at" class="form-control rounded-3 border-slate-200">
            <div class="form-text small">Leave blank for permanent regions.</div>
          </div>
        </div>
        <div class="modal-footer border-0 p-4 pt-2">
          <button type="button" class="btn btn-slate-100 rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark rounded-pill px-4">Create Region</button>
        </div>
      </form>
    </div>
  </div>

@endsection
