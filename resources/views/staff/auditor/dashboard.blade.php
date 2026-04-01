@extends('layouts.portal')
@section('title', 'Auditor Dashboard')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Auditor Dashboard</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Read-only oversight across applications, payments, logs, and reports. Auditors can <b>flag anomalies</b> but cannot approve/reject.
      </div>
    </div>

    <form class="d-flex flex-wrap gap-2" method="GET">
      <input type="date" class="form-control form-control-sm" name="from" value="{{ optional($from)->format('Y-m-d') }}">
      <input type="date" class="form-control form-control-sm" name="to" value="{{ optional($to)->format('Y-m-d') }}">
      <button class="btn btn-dark btn-sm" type="submit"><i class="ri-filter-3-line me-1"></i>Apply</button>
      <a class="btn btn-white border btn-sm" href="{{ route('staff.auditor.dashboard') }}">Reset</a>
    </form>
  </div>

  @include('partials.analytics.accreditation_summary')

    <div class="row g-3 mb-4 d-none">
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Total Applications</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($totalApplications) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Approved (all stages)</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($approvedCount) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3">
        <div class="text-muted small">Returned for correction</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($rejectedCount) }}</div>
      </div>
    </div>
  </div>

  {{-- Tabbed Detailed Strategic Oversight (Shared with Director) --}}
  <div class="zmc-card bg-white shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <ul class="nav nav-tabs border-0 bg-light p-2 gap-2" id="auditorTabs" role="tablist">
          <li class="nav-item flex-grow-1" role="presentation">
              <button class="nav-link {{ $activeTab === 'overview' ? 'active' : '' }} w-100 rounded-3 border-0 fw-bold py-3" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                  <i class="ri-dashboard-line me-2"></i> Audit Overview
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
      </ul>
      <div class="tab-content" id="auditorTabsContent">
          {{-- Overview Tab --}}
          <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : '' }} p-4" id="overview" role="tabpanel">
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <div class="zmc-card shadow-sm border-0 p-3 bg-light">
                    <div class="text-muted small text-uppercase fw-bold">PayNow Confirmed</div>
                    <div class="fw-bold h3">{{ number_format($paynowConfirmed) }}</div>
                    <a class="smaller text-primary text-decoration-none mt-2 d-block" href="{{ route('staff.auditor.paynow') }}">View PayNow Audit →</a>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="zmc-card shadow-sm border-0 p-3 bg-light">
                    <div class="text-muted small text-uppercase fw-bold">Proofs Approved</div>
                    <div class="fw-bold h3">{{ number_format($proofsApproved) }}</div>
                    <a class="smaller text-primary text-decoration-none mt-2 d-block" href="{{ route('staff.auditor.proofs') }}">View Proofs →</a>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="zmc-card shadow-sm border-0 p-3 bg-light">
                    <div class="text-muted small text-uppercase fw-bold">Waivers Approved</div>
                    <div class="fw-bold h3">{{ number_format($waiversApproved) }}</div>
                    <a class="smaller text-primary text-decoration-none mt-2 d-block" href="{{ route('staff.auditor.waivers') }}">View Waivers →</a>
                  </div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-lg-7">
                  <div class="zmc-card shadow-sm border-0 p-0 border-top border-4 border-dark">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                      <h6 class="fw-bold m-0"><i class="ri-briefcase-4-line me-2 text-dark"></i>Audit Quick Links</h6>
                    </div>
                    <div class="p-3">
                      <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-dark px-3" href="{{ route('staff.auditor.applications') }}"><i class="ri-folder-open-line me-1"></i>Application Audits</a>
                        <a class="btn btn-sm btn-white border px-3" href="{{ route('staff.auditor.logs') }}"><i class="ri-file-list-3-line me-1"></i>Audit Logs</a>
                        <a class="btn btn-sm btn-white border px-3" href="{{ route('staff.auditor.reports') }}"><i class="ri-bar-chart-2-line me-1"></i>Audit Reports</a>
                        <a class="btn btn-sm btn-white border px-3" href="{{ route('staff.auditor.security') }}"><i class="ri-shield-line me-1"></i>Security Oversight</a>
                      </div>
                      <div class="mt-3 text-muted smaller bg-light p-2 rounded">
                        <i class="ri-information-line"></i> Auditors have read-only access. Use the <b>Flag</b> icon on records to report anomalies.
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-5">
                  <div class="zmc-card shadow-sm border-0 p-0 border-top border-4 border-danger">
                    <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                      <h6 class="fw-bold m-0"><i class="ri-flag-2-line me-2 text-danger"></i>Recent Flags</h6>
                    </div>
                    <div class="p-3">
                      @if($recentFlags->isEmpty())
                        <div class="text-muted smaller">No anomalies flagged.</div>
                      @else
                        <div class="table-responsive">
                          <table class="table table-sm mb-0 smaller">
                            <thead><tr><th>Type</th><th>ID</th><th class="text-end">Severity</th></tr></thead>
                            <tbody>
                              @foreach($recentFlags as $f)
                                <tr>
                                  <td>{{ class_basename($f->entity_type) }}</td>
                                  <td>#{{ $f->entity_id }}</td>
                                  <td class="text-end fw-bold {{ $f->severity === 'high' ? 'text-danger' : 'text-warning' }}">{{ strtoupper($f->severity) }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
          </div>
          {{-- Financial Tab --}}
          <div class="tab-pane fade {{ $activeTab === 'fin' ? 'show active' : '' }} p-4" id="fin" role="tabpanel">
              @include('partials.analytics.financial_summary')
          </div>
          {{-- Compliance Tab --}}
          <div class="tab-pane fade {{ $activeTab === 'comp' ? 'show active' : '' }} p-4" id="comp" role="tabpanel">
              @include('staff.director.partials.compliance')
          </div>
      </div>
  </div>

  {{-- Trends Analytics --}}
  @include('partials.analytics.trends')

  {{-- Activity feed --}}
  <div class="zmc-card mb-4 mt-3 shadow-sm border-0 rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
      <div>
        <div class="fw-bold"><i class="ri-pulse-line me-1"></i> System-Wide Activity Feed</div>
        <div class="small text-muted">Real-time audit trail of all staff actions</div>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-success btn-sm rounded-3" href="{{ route('staff.auditor.activity.csv') }}">
          <i class="ri-file-excel-line me-1"></i>Excel
        </a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead class="bg-light smaller">
          <tr class="text-muted">
            <th style="width:170px;">Time</th>
            <th style="width:200px;">Actor</th>
            <th style="width:220px;">Action</th>
            <th>Reference</th>
            <th style="width:200px;">Change</th>
          </tr>
        </thead>
        <tbody class="smaller">
        @forelse(($activity ?? []) as $log)
          @php
            $actor = optional($log->user);
            $actorLabel = $actor?->name ? $actor->name : 'User #' . (int)($log->user_id ?? 0);
            $ref = null;
            try { $ref = optional($log->entity)->reference; } catch (\Throwable $e) {}
            $ref = $ref ?: ($log->entity_type ? class_basename($log->entity_type) . '-' . (int)($log->entity_id ?? 0) : ('Entity-' . (int)($log->entity_id ?? 0)));
          @endphp
          <tr>
            <td class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d M/H:i') }}</td>
            <td class="fw-bold text-dark">{{ $actorLabel }}</td>
            <td><span class="badge bg-light text-dark border">{{ str_replace('_',' ', (string)$log->action) }}</span></td>
            <td>{{ $ref }}</td>
            <td class="text-muted">{{ ($log->from_status ?? '—') }} <i class="ri-arrow-right-s-line px-1"></i> {{ ($log->to_status ?? '—') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center py-4 text-muted">No recent activity.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<style>
.nav-tabs .nav-link { color: #64748b; transition: all 0.2s ease; }
.nav-tabs .nav-link.active { background-color: #fff !important; color: #0f172a !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
.nav-tabs .nav-link:hover:not(.active) { background-color: #f1f5f9; }
.smaller { font-size: 0.75rem; }
.fw-black { font-weight: 900; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggerTabList = document.querySelectorAll('#auditorTabs button[data-bs-toggle="tab"]');
    triggerTabList.forEach(triggerEl => {
        new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', e => { e.preventDefault(); bootstrap.Tab.getInstance(triggerEl).show(); });
    });
});
</script>
@endsection
