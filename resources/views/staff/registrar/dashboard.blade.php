@extends('layouts.portal')
@section('title', 'Registrar Dashboard')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Registrar Dashboard</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Supervisory oversight of accreditation and registration processes.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm px-3" onclick="exportDashboardReport()">
        <i class="ri-download-2-line me-1"></i>Export Report
      </button>
    </div>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    {{-- All Applications --}}
    <div class="col-md-3">
      <div class="zmc-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="fw-bold text-muted mb-1">All Applications</h6>
              <h3 class="fw-bold mb-0">{{ $kpis['total_applications'] ?? 0 }}</h3>
              <div class="small text-success mt-1">
                <i class="ri-arrow-up-line"></i>
                {{ $kpis['new_this_week'] ?? 0 }} this week
              </div>
            </div>
            <div class="rounded-circle bg-light p-3">
              <i class="ri-file-list-3-line text-primary" style="font-size:24px;"></i>
            </div>
          </div>
          <div class="mt-2">
            <a href="{{ route('registrar.applications') }}" class="btn btn-sm btn-outline-primary w-100">
              <i class="ri-eye-line me-1"></i>View All
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Awaiting Review --}}
    <div class="col-md-3">
      <div class="zmc-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="fw-bold text-muted mb-1">Awaiting Review</h6>
              <h3 class="fw-bold mb-0">{{ $kpis['awaiting_review'] ?? 0 }}</h3>
              <div class="small text-warning mt-1">
                <i class="ri-time-line"></i>
                Pending registrar review
              </div>
            </div>
            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
              <i class="ri-eye-line text-warning" style="font-size:24px;"></i>
            </div>
          </div>
          <div class="mt-2">
            <a href="{{ route('registrar.applications', ['status' => 'awaiting_review']) }}" class="btn btn-sm btn-outline-warning w-100">
              <i class="ri-eye-line me-1"></i>Review
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Returned for Correction --}}
    <div class="col-md-3">
      <div class="zmc-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="fw-bold text-muted mb-1">Returned for Correction</h6>
              <h3 class="fw-bold mb-0">{{ $kpis['returned_for_correction'] ?? 0 }}</h3>
              <div class="small text-info mt-1">
                <i class="ri-arrow-go-back-line"></i>
                Returned by officer
              </div>
            </div>
            <div class="rounded-circle bg-info bg-opacity-10 p-3">
              <i class="ri-arrow-go-back-line text-info" style="font-size:24px;"></i>
            </div>
          </div>
          <div class="mt-2">
            <a href="{{ route('registrar.applications', ['status' => 'returned']) }}" class="btn btn-sm btn-outline-info w-100">
              <i class="ri-eye-line me-1"></i>View
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Forwarded to Registrar --}}
    <div class="col-md-3">
      <div class="zmc-card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="fw-bold text-muted mb-1">Complex Applications</h6>
              <h3 class="fw-bold mb-0">{{ $kpis['forwarded_to_registrar'] ?? 0 }}</h3>
              <div class="small text-danger mt-1">
                <i class="ri-alert-line"></i>
                Require guidance
              </div>
            </div>
            <div class="rounded-circle bg-danger bg-opacity-10 p-3">
              <i class="ri-user-search-line text-danger" style="font-size:24px;"></i>
            </div>
          </div>
          <div class="mt-2">
            <a href="{{ route('registrar.applications', ['status' => 'forwarded']) }}" class="btn btn-sm btn-outline-danger w-100">
              <i class="ri-user-search-line me-1"></i>Provide Guidance
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Production Overview --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="zmc-card">
        <div class="card-header bg-primary text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 mb-0">
              <i class="ri-printer-line me-2"></i>
              Cards Generated Today
            </h6>
            <span class="badge bg-light text-dark">{{ date('d M Y') }}</span>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6">
              <div class="text-center">
                <h4 class="fw-bold text-primary mb-0">{{ $productionStats['cards_today'] ?? 0 }}</h4>
                <div class="small text-muted">Total Cards</div>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center">
                <h4 class="fw-bold text-success mb-0">{{ $productionStats['accreditation_cards'] ?? 0 }}</h4>
                <div class="small text-muted">Accreditation</div>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center">
                <h4 class="fw-bold text-info mb-0">{{ $productionStats['registration_cards'] ?? 0 }}</h4>
                <div class="small text-muted">Registration</div>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center">
                <h4 class="fw-bold text-warning mb-0">{{ $productionStats['pending_cards'] ?? 0 }}</h4>
                <div class="small text-muted">Pending</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Activity Feed --}}
    <div class="col-md-6">
      <div class="zmc-card">
        <div class="card-header bg-light">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 mb-0">
              <i class="ri-activity-line me-2"></i>
              Activity Feed
            </h6>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshActivity()">
              <i class="ri-refresh-line"></i>
            </button>
          </div>
        </div>
        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
          @if(isset($recentActivities) && $recentActivities->count() > 0)
            @foreach($recentActivities as $activity)
              <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                <div class="flex-shrink-0">
                  <div class="rounded-circle bg-light p-2">
                    <i class="ri-{{ $activity['icon'] ?? 'file-list-3-line' }} text-{{ $activity['color'] ?? 'primary' }}" style="font-size:16px;"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold">{{ $activity['title'] ?? 'Activity' }}</div>
                  <div class="small text-muted">{{ $activity['description'] ?? '' }}</div>
                  <div class="small text-muted">{{ $activity['time'] ?? '' }}</div>
                </div>
              </div>
            @endforeach
          @else
            <div class="text-center text-muted py-4">
              <i class="ri-inbox-line" style="font-size:32px;"></i>
              <div class="mt-2">No recent activity</div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Records Section --}}
  <div class="zmc-card mb-4">
    <div class="card-header bg-success text-white">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-database-2-line me-2"></i>
          Records Management
        </h6>
        <span class="badge bg-light text-dark">Supervisory Access</span>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold mb-0">Accredited Media Practitioners</h6>
              <span class="badge bg-primary">{{ $recordsStats['accredited_count'] ?? 0 }}</span>
            </div>
            <div class="small text-muted mb-3">View and manage accredited journalist records</div>
            <div class="d-flex gap-2">
              <a href="{{ route('registrar.records.accredited-journalists') }}" class="btn btn-sm btn-outline-primary flex-fill">
                <i class="ri-eye-line me-1"></i>View Records
              </a>
              <a href="{{ route('registrar.records.accredited-journalists.export') }}" class="btn btn-sm btn-outline-success">
                <i class="ri-download-line"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold mb-0">Registered Media Houses</h6>
              <span class="badge bg-success">{{ $recordsStats['registered_count'] ?? 0 }}</span>
            </div>
            <div class="small text-muted mb-3">View and manage registered media house records</div>
            <div class="d-flex gap-2">
              <a href="{{ route('registrar.records.registered-mediahouses') }}" class="btn btn-sm btn-outline-primary flex-fill">
                <i class="ri-eye-line me-1"></i>View Records
              </a>
              <a href="{{ route('registrar.records.registered-mediahouses.export') }}" class="btn btn-sm btn-outline-success">
                <i class="ri-download-line"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Quick Actions --}}
  <div class="zmc-card">
    <div class="card-header bg-light">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-flashlight-line me-2"></i>
        Quick Actions
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <a href="{{ route('registrar.applications') }}" class="btn btn-outline-primary w-100">
            <i class="ri-file-list-3-line me-1"></i>All Applications
          </a>
        </div>
        <div class="col-md-3">
          <a href="{{ route('registrar.applications', ['status' => 'forwarded']) }}" class="btn btn-outline-danger w-100">
            <i class="ri-user-search-line me-1"></i>Complex Applications
          </a>
        </div>
        <div class="col-md-3">
          <a href="{{ route('registrar.reports') }}" class="btn btn-outline-success w-100">
            <i class="ri-bar-chart-line me-1"></i>Operational Reports
          </a>
        </div>
        <div class="col-md-3">
          <a href="{{ route('registrar.downloads') }}" class="btn btn-outline-info w-100">
            <i class="ri-download-2-line me-1"></i>Downloads
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function exportDashboardReport() {
  window.location.href = '{{ route("registrar.dashboard.export") }}';
}

function refreshActivity() {
  location.reload();
}
</script>
@endsection
    $k = $kpis ?? [];
    $detailsUrlTemplate = route('staff.applications.details', ['application' => '__ID__']);
  @endphp

  {{-- Row 1: Primary Flow --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">All applications</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($k['all_applications'] ?? 0) }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Approved Today</div>
        <div class="fw-bold text-success" style="font-size:26px;">{{ number_format($k['approved_today'] ?? 0) }}</div>
        <div class="small text-muted mt-1">Week: {{ $k['approved_this_week'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Returned for Correction</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($k['returned_to_officer'] ?? 0) }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Forwarded to Registrar</div>
        <div class="fw-bold text-primary" style="font-size:26px;">{{ number_format($k['forwarded_to_registrar'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Row 2: Payments & Flags --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Awaiting Payment Approval</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($k['awaiting_payment_approval'] ?? 0) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Pending Payment Correction</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($k['returned_to_officer'] ?? 0) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Flagged Reprints</div>
        <div class="fw-bold text-danger" style="font-size:26px;">{{ number_format($k['flagged_reprints'] ?? 0) }}</div>
        <div class="small text-muted mt-1">Prints > Threshold</div>
      </div>
    </div>
  </div>

  {{-- Row 3: Production --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Certificates Generated (Today)</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($k['certificates_generated_today'] ?? 0) }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="zmc-card shadow-sm border-0 p-3 h-100">
        <div class="text-muted small">Cards Generated (Today)</div>
        <div class="fw-bold" style="font-size:26px;">{{ number_format($k['cards_generated'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Quick Filters --}}
  <div class="zmc-card mb-4 bg-light border-0">
    <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="small fw-bold text-muted">Type</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                <option value="accreditation" {{ request('type') == 'accreditation' ? 'selected' : '' }}>Accreditation</option>
                <option value="registration" {{ request('type') == 'registration' ? 'selected' : '' }}>Registration</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="small fw-bold text-muted">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="paid_confirmed" {{ request('status') == 'paid_confirmed' ? 'selected' : '' }}>Paid Confirmed</option>
                <option value="registrar_review" {{ request('status') == 'registrar_review' ? 'selected' : '' }}>Registrar Reviews</option>
                <option value="approved_awaiting_payment" {{ request('status') == 'approved_awaiting_payment' ? 'selected' : '' }}>Awaiting Payment</option>
                <option value="forwarded_to_registrar" {{ request('status') == 'forwarded_to_registrar' ? 'selected' : '' }}>Forwarded to Registrar</option>
                <option value="registrar_fix_request" {{ request('status') == 'registrar_fix_request' ? 'selected' : '' }}>Fix Request</option>
                <option value="registrar_approved" {{ request('status') == 'registrar_approved' ? 'selected' : '' }}>Approved</option>
                <option value="returned_to_officer" {{ request('status') == 'returned_to_officer' ? 'selected' : '' }}>Returned to Officer</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, ID, Media House..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
        </div>
        <div class="col-md-1">
            <a href="{{ url()->current() }}" class="btn btn-outline-dark btn-sm w-100">Reset</a>
        </div>
    </form>
  </div>

  {{-- Trends Chart --}}
  <div class="mb-4">
      @include('partials.analytics.trends')
  </div>

  {{-- Activity feed --}}
  <div class="zmc-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold"><i class="ri-pulse-line me-1"></i> Activity feed</div>
      <div class="small text-muted">Latest submissions / approvals</div>
    </div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr class="text-muted small">
            <th style="width:170px;">Time</th>
            <th style="width:180px;">Action</th>
            <th>Reference</th>
            <th style="width:160px;">From → To</th>
          </tr>
        </thead>
        <tbody>
        @forelse(($activity ?? []) as $log)
          @php
            $ref = null;
            try { $ref = optional($log->entity)->reference; } catch (\Throwable $e) {}
            $ref = $ref ?: ('APP-' . str_pad((int)($log->entity_id ?? 0), 5, '0', STR_PAD_LEFT));
          @endphp
          <tr>
            <td class="small text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
            <td class="small fw-bold">{{ str_replace('_',' ', (string)$log->action) }}</td>
            <td class="small">{{ $ref }}</td>
            <td class="small text-muted">{{ ($log->from_status ?? '—') }} → {{ ($log->to_status ?? '—') }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-muted small">No recent activity.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Table --}}
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0">
        <i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i> Registrar queue
      </h6>
      @if(method_exists($applications, 'currentPage'))
        <div class="small text-muted">Page {{ $applications->currentPage() }} of {{ $applications->lastPage() }}</div>
      @endif
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th><i class="ri-hashtag me-1"></i> Ref</th>
            <th><i class="ri-user-line me-1"></i> Applicant</th>
            <th><i class="ri-file-text-line me-1"></i> Type</th>
            <th><i class="ri-calendar-line me-1"></i> Date</th>
            <th><i class="ri-flag-line me-1"></i> Status</th>
            <th class="text-center"><i class="ri-checkbox-circle-line me-1"></i> Reviewed</th>
            <th class="text-end" style="min-width:210px;">Action</th>
          </tr>
        </thead>

        <tbody>
        @forelse($applications as $i => $app)
          @php
            $status = strtolower((string)($app->status ?? ''));
            $badge = match($status) {
              'registrar_rejected' => 'danger',
              'registrar_approved' => 'success',
              'returned_to_accounts' => 'secondary',
              'forwarded_to_registrar' => 'warning',
              'approved_awaiting_payment' => 'primary',
              'registrar_fix_request' => 'dark',
              default => 'info',
            };

            $canDecide = in_array($status, ['registrar_review','paid_confirmed','accounts_review','returned_to_accounts','approved_awaiting_payment','forwarded_to_registrar'], true);
            $canApproveForPayment = $status === 'registrar_review' && $app->payment_status !== 'paid' && !$app->registrar_reviewed_at;
            $canFixRequest = in_array($status, ['registrar_review','approved_awaiting_payment','forwarded_to_registrar'], true);
            $canPushToAccounts = $status === 'forwarded_to_registrar';

            $rowNo = method_exists($applications,'firstItem') && $applications->firstItem()
              ? ($applications->firstItem() + $i)
              : ($i + 1);

            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
            $type = $app->application_type ?? '—';
            
            // Category variables for reassign modal
            $isRegistration = ($app->application_type ?? '') === 'registration';
            $cats = $isRegistration ? \App\Models\Application::massMediaCategories() : \App\Models\Application::accreditationCategories();
            $currentCat = $isRegistration ? $app->media_house_category_code : $app->accreditation_category_code;
          @endphp

          <tr>
            <td class="text-muted small">{{ $rowNo }}</td>
            <td class="fw-bold text-dark">{{ $ref }}</td>
            <td>{{ $app->applicant?->name ?? '—' }}</td>
            <td>
              <span class="small fw-bold text-uppercase">{{ $type }}</span>
              @php
                $reqType = $app->request_type ?? 'new';
                $reqBadge = match($reqType) { 'renewal' => 'warning', 'replacement' => 'info', default => 'success' };
              @endphp
              <span class="badge bg-{{ $reqBadge }} ms-1">{{ ucfirst($reqType) }}</span>
            </td>
            <td class="small">{{ !empty($app->created_at) ? \Carbon\Carbon::parse($app->created_at)->format('d M Y') : '—' }}</td>
            <td>
               <span class="badge rounded-pill bg-{{ $badge }} px-3">
                 {{ ucwords(str_replace('_',' ', $status ?: '—')) }}
               </span>
               @if($app->is_flagged)
                 <div class="mt-1"><span class="badge bg-danger text-white"><i class="ri-error-warning-fill me-1"></i> FLAGOMALY</span></div>
               @endif
            </td>
            <td class="text-center">
                <form action="{{ route('staff.registrar.applications.toggle-reviewed', $app) }}" method="POST">
                    @csrf
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input shadow-none cursor-pointer" type="checkbox" 
                               onchange="this.form.submit()" {{ $app->registrar_reviewed_at ? 'checked' : '' }}>
                    </div>
                </form>
            </td>

            <td class="text-end">
              <div class="zmc-action-strip">

                {{-- Flag Anomaly --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-danger js-open-modal"
                  data-target="#flagModal{{ $app->id }}"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Flag Anomaly"
                >
                  <i class="fa-solid fa-flag"></i>
                </button>

                {{-- Message Officer --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-info js-open-modal"
                  data-target="#messageOfficerModal{{ $app->id }}"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Message Officer"
                >
                  <i class="fa-solid fa-comment-dots"></i>
                </button>

                {{-- Reassign to Officer --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-warning js-open-modal"
                  data-target="#reassignToOfficerModal{{ $app->id }}"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Reassign to Officer"
                >
                  <i class="fa-solid fa-user-gear"></i>
                </button>

                {{-- Reassign Category --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-warning js-open-modal"
                  data-target="#reassignModal{{ $app->id }}"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Reassign Category"
                >
                  <i class="fa-solid fa-award"></i>
                </button>

                {{-- View --}}
                <button
                  type="button"
                  class="btn btn-sm zmc-icon-btn btn-outline-primary js-view-more"
                  data-app-id="{{ $app->id }}"
                  data-bs-toggle="tooltip" data-bs-placement="top"
                  title="View application"
                >
                  <i class="fa-regular fa-eye"></i>
                </button>

              </div>
            </td>
          </tr>

          {{-- Modals --}}
          @push('zmc_modals')
            {{-- Flag Modal --}}
            <div class="modal fade" id="flagModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.flag-anomaly', $app) }}">
                  @csrf
                  <div class="modal-header zmc-modal-header bg-danger text-white">
                    <div>
                      <div class="zmc-modal-title text-white">
                        <i class="fa-solid fa-flag me-2"></i>
                        Flag Anomaly
                        <span class="ms-2 opacity-75" style="font-weight:800;font-size:12px;">{{ $ref }}</span>
                      </div>
                      <div class="zmc-modal-sub text-white-50">Indicate an anomaly or issue found during review.</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label zmc-lbl">Anomaly Notes <span class="text-danger">*</span></label>
                      <textarea name="flag_notes" class="form-control zmc-input" rows="4" required placeholder="Describe the anomaly or concern..."></textarea>
                    </div>
                  </div>
                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                      <i class="fa-solid fa-flag me-1"></i>Flag Application
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Message Officer Modal --}}
            <div class="modal fade" id="messageOfficerModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.message-officer', $app) }}">
                  @csrf
                  <div class="modal-header zmc-modal-header bg-info text-white">
                    <div>
                      <div class="zmc-modal-title text-white">
                        <i class="fa-solid fa-comment-dots me-2"></i>
                        Message Officer
                        <span class="ms-2 opacity-75" style="font-weight:800;font-size:12px;">{{ $ref }}</span>
                      </div>
                      <div class="zmc-modal-sub text-white-50">Send guidance or a note to the assigned officer.</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label zmc-lbl">Message / Guidance <span class="text-danger">*</span></label>
                      <textarea name="message" class="form-control zmc-input" rows="4" required placeholder="Type your message for the officer..."></textarea>
                    </div>
                  </div>
                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white fw-bold">
                      <i class="fa-solid fa-paper-plane me-1"></i>Send Message
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Reassign to Officer Modal --}}
            <div class="modal fade" id="reassignToOfficerModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.reassign-category', $app) }}">
                  @csrf
                  <div class="modal-header zmc-modal-header bg-warning">
                    <div>
                      <div class="zmc-modal-title">
                        <h5 class="fw-bold mb-0">Registrar Reviews</h5>
                        Reassign to Officer
                        <span class="ms-2 text-muted" style="font-weight:800;font-size:12px;">{{ $ref }}</span>
                      </div>
                      <div class="zmc-modal-sub">Assign this application back to an Accreditation Officer.</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label zmc-lbl">Select Officer <span class="text-danger">*</span></label>
                      <select name="officer_id" class="form-select zmc-input" required>
                        <option value="">-- Select Officer --</option>
                        @foreach($officers as $off)
                          <option value="{{ $off->id }}" {{ $app->assigned_officer_id == $off->id ? 'selected' : '' }}>
                            {{ $off->name }} ({{ $off->region ?? 'No Region' }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label zmc-lbl">Reason for Reassignment <span class="text-danger">*</span></label>
                      <textarea name="reason" class="form-control zmc-input" rows="3" required placeholder="State why the application is being reassigned..."></textarea>
                    </div>
                  </div>
                  <div class="modal-footer zmc-modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold">
                      <i class="fa-solid fa-arrows-rotate me-1"></i>Reassign
                    </button>
                  </div>
                </form>
              </div>
            </div>
          @endpush

        @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">No applications in registrar queue.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $applications->links() }}
  </div>

  @stack('zmc_modals')
</div>

{{-- Global Details Modal (View) --}}
<div class="modal fade zmc-modal-pop" id="appDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header zmc-modal-header">
        <div>
          <div class="zmc-modal-title">
            <i class="fa-regular fa-file-lines me-2" style="color:var(--zmc-accent-dark)"></i>
            Application Review
          </div>
          <div class="zmc-modal-sub" id="mdl_meta">—</div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="mdl_loading" class="d-none text-center py-5">
          <div class="spinner-border" style="color:var(--zmc-accent-dark)"></div>
          <div class="text-muted mt-2" style="font-size:12px;">Loading…</div>
        </div>

        <div id="mdl_error" class="alert alert-danger d-none"></div>

        <div id="mdl_content_area" class="d-none"></div>
      </div>

      <div class="modal-footer zmc-modal-footer">
        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const ZMC_DETAILS_URL = @json($detailsUrlTemplate);

  function zmcFmt(v){
    if(v === null || v === undefined) return '—';
    const s = String(v).trim();
    return s === '' ? '—' : s;
  }

  function zmcOpenModal(selector){
    const el = document.querySelector(selector);
    if(!el) return;
    if (window.bootstrap && typeof bootstrap.Modal === 'function') {
      bootstrap.Modal.getOrCreateInstance(el).show();
      return;
    }
  }

  function zmcBlock(titleHtml, bodyHtml){
    return `
      <div class="zmc-mdl-block">
        <div class="zmc-mdl-title">${titleHtml}</div>
        ${bodyHtml}
      </div>
    `;
  }

  function zmcInput(label, value, col = 4){
    return `
      <div class="col-12 col-md-${col}">
        <label class="form-label zmc-lbl">${label}</label>
        <input type="text" class="form-control zmc-input" value="${zmcFmt(value)}" readonly>
      </div>
    `;
  }

  function zmcTextarea(label, value, col = 12){
    return `
      <div class="col-12 col-md-${col}">
        <label class="form-label zmc-lbl">${label}</label>
        <textarea class="form-control zmc-input" rows="3" readonly>${zmcFmt(value)}</textarea>
      </div>
    `;
  }

  async function loadApplicationDetails(appId) {
    const loader = document.getElementById('mdl_loading');
    const area   = document.getElementById('mdl_content_area');
    const meta   = document.getElementById('mdl_meta');
    const errBox = document.getElementById('mdl_error');

    if (errBox){ errBox.classList.add('d-none'); errBox.textContent = ''; }
    if (loader) loader.classList.remove('d-none');
    if (area){ area.classList.add('d-none'); area.innerHTML = ''; }
    if (meta) meta.textContent = '—';

    try {
      const url = ZMC_DETAILS_URL.replace('__ID__', appId);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || data.ok === false) throw new Error(data.message || 'Failed to load details');

      const app = data.application || {};
      const formCode = String(app.form_code || '').toUpperCase();
      const ref = app.reference || ('APP-' + (app.id || appId));
      const status = app.status || '—';

      if (meta) {
        meta.innerHTML = `
          <span class="badge bg-light text-dark border me-2">${zmcFmt(formCode || 'Form')}</span>
          Ref: <span class="fw-bold">${zmcFmt(ref)}</span> •
          Status: <span class="fw-bold" style="color:var(--zmc-accent-dark)">${zmcFmt(status)}</span>
        `;
      }

      let html = '';

      if (formCode === 'AP3') {
        const body = `
          <div class="row g-3">
            ${zmcInput('Title', app.title)}
            ${zmcInput('First name', app.first_name)}
            ${zmcInput('Surname', app.surname)}
            ${zmcInput('Other names', app.other_names)}
            ${zmcInput('Date of birth', app.dob)}
            ${zmcInput('Sex', app.sex)}
            ${zmcInput('Birth place', app.birth_place)}
            ${zmcInput('Origin', app.origin)}
            ${zmcInput('Nationality', app.nationality)}
            ${zmcInput('ID / Passport', app.id_passport_number)}
            ${zmcInput('Employer', app.employer_name)}
            ${zmcInput('Medium type', app.medium_type)}
            ${zmcInput('Designation', app.designation)}
            ${zmcTextarea('Assignment brief', app.assignment_brief)}
            ${zmcInput('Arrival date', app.arrival_date)}
            ${zmcInput('Departure date', app.departure_date)}
            ${zmcInput('Port of entry', app.port_entry)}
            ${zmcTextarea('Local address', (app.zim_local_address || app.zim_address))}
          </div>
        `;
        html += zmcBlock(`<i class="fa-regular fa-id-card"></i> Media Practitioner details`, body);
      }

      if (formCode === 'AP1') {
        const s = data.ap1 || {};

        html += zmcBlock(
          `<i class="fa-regular fa-building"></i> Organisation details`,
          `
          <div class="row g-3">
            ${zmcInput('Category', s.category)}
            ${zmcInput('Service name', s.service_name)}
            ${zmcInput('Operating model', s.operating_model)}
            ${zmcInput('Organisation', s.org_name)}
            ${zmcInput('Reg no', s.reg_no)}
            ${zmcInput('Website', s.website)}
            ${zmcTextarea('Head office', s.head_office, 6)}
            ${zmcTextarea('Postal address', s.postal_address, 6)}
          </div>
          `
        );

        html += zmcBlock(
          `<i class="fa-regular fa-address-book"></i> Contact`,
          `
          <div class="row g-3">
            ${zmcInput('Contact person', s.contact_person, 4)}
            ${zmcInput('Contact email', s.contact_email, 4)}
            ${zmcInput('Contact phone', s.contact_phone, 4)}
          </div>
          `
        );

        const directors = Array.isArray(data.directors) ? data.directors : [];
        const managers  = Array.isArray(data.managers) ? data.managers : [];

        let dRows = directors.length ? '' : `<tr><td colspan="5" class="text-muted text-center">—</td></tr>`;
        directors.forEach(d => {
          dRows += `
            <tr>
              <td>${zmcFmt(d.full_name || d.name || (d.first_name ? (d.first_name + ' ' + (d.surname||'')) : ''))}</td>
              <td>${zmcFmt(d.id_passport || d.id || d.id_number)}</td>
              <td>${zmcFmt(d.nationality)}</td>
              <td>${zmcFmt(d.role || d.occupation)}</td>
              <td>${zmcFmt(d.shareholding || d.shares)}</td>
            </tr>
          `;
        });

        html += zmcBlock(
          `<i class="fa-solid fa-people-group"></i> Directors`,
          `
          <div class="table-responsive">
            <table class="table table-sm align-middle zmc-table-lite">
              <thead>
                <tr>
                  <th>Full name</th><th>ID / Passport</th><th>Nationality</th><th>Role</th><th>Shareholding</th>
                </tr>
              </thead>
              <tbody>${dRows}</tbody>
            </table>
          </div>
          `
        );

        let mRows = managers.length ? '' : `<tr><td colspan="4" class="text-muted text-center">—</td></tr>`;
        managers.forEach(m => {
          mRows += `
            <tr>
              <td>${zmcFmt(m.full_name || m.name)}</td>
              <td>${zmcFmt(m.position)}</td>
              <td>${zmcFmt(m.qualification)}</td>
              <td>${zmcFmt(m.experience)}</td>
            </tr>
          `;
        });

        html += zmcBlock(
          `<i class="fa-solid fa-user-gear"></i> Managers`,
          `
          <div class="table-responsive">
            <table class="table table-sm align-middle zmc-table-lite">
              <thead>
                <tr>
                  <th>Full name</th><th>Position</th><th>Qualification</th><th>Experience</th>
                </tr>
              </thead>
              <tbody>${mRows}</tbody>
            </table>
          </div>
          `
        );
      }

      const docs = Array.isArray(data.documents) ? data.documents : [];
      let docRows = docs.length ? '' : `<tr><td colspan="3" class="text-muted text-center">—</td></tr>`;
      docs.forEach(doc => {
        const open = doc.url
          ? `<a href="${doc.url}" target="_blank" class="btn btn-sm btn-outline-primary" title="Open document">
               <i class="fa-solid fa-arrow-up-right-from-square"></i>
             </a>`
          : `<span class="text-muted">—</span>`;
        docRows += `
          <tr>
            <td class="fw-bold">${zmcFmt(doc.document_type)}</td>
            <td>${zmcFmt(doc.original_name || doc.file_name)}</td>
            <td class="text-end">${open}</td>
          </tr>
        `;
      });

      html += zmcBlock(
        `<i class="fa-regular fa-folder-open"></i> Attachments`,
        `
        <div class="table-responsive">
          <table class="table table-sm align-middle zmc-table-lite">
            <thead>
              <tr><th>Type</th><th>File</th><th class="text-end">Open</th></tr>
            </thead>
            <tbody>${docRows}</tbody>
          </table>
        </div>
        `
      );

      if (area) {
        area.innerHTML = html;
        area.classList.remove('d-none');
      }

    } catch (e) {
      if (errBox) {
        errBox.textContent = 'Error loading details: ' + (e.message || 'Unknown error');
        errBox.classList.remove('d-none');
      }
    } finally {
      if (loader) loader.classList.add('d-none');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.bootstrap && typeof bootstrap.Tooltip === 'function') {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }

    document.addEventListener('click', function(e){
      const btn = e.target.closest('.js-open-modal');
      if(!btn) return;
      const target = btn.getAttribute('data-target');
      if(target) zmcOpenModal(target);
    });

    document.addEventListener('click', function(e){
      const btn = e.target.closest('.js-view-more');
      if(!btn) return;
      const appId = btn.getAttribute('data-app-id');
      if(!appId) return;

      zmcOpenModal('#appDetailsModal');
      loadApplicationDetails(appId);
    });
  });
</script>
@endpush
@endsection
