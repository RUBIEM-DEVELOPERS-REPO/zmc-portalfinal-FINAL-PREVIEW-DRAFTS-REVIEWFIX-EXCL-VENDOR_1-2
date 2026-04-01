@extends('layouts.portal')
@section('title', $title ?? 'All Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">{{ $title ?? 'All Applications' }}</h4>
      <div class="text-muted mt-1" style="font-size:13px;"><i class="ri-information-line me-1"></i>Manage and filter all submitted applications.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  {{-- Filters Section --}}
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0"><i class="ri-filter-3-line me-2"></i>Filters</h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
          <i class="ri-refresh-line me-1"></i>Clear All
        </button>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ request()->url() }}" id="filterForm">
        <div class="row g-3">
          {{-- Name Search --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Applicant Name</label>
            <input type="text" name="name" class="form-control" value="{{ request('name') }}" placeholder="Search by name...">
          </div>

          {{-- Application Type --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Application Type</label>
            <select name="application_type" class="form-select">
              <option value="">All Types</option>
              <option value="accreditation" {{ request('application_type') == 'accreditation' ? 'selected' : '' }}>Accreditation</option>
              <option value="registration" {{ request('application_type') == 'registration' ? 'selected' : '' }}>Registration</option>
            </select>
          </div>

          {{-- Request Type --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Request Type</label>
            <select name="request_type" class="form-select">
              <option value="">All Request Types</option>
              <option value="new" {{ request('request_type') == 'new' ? 'selected' : '' }}>New</option>
              <option value="renewal" {{ request('request_type') == 'renewal' ? 'selected' : '' }}>Renewal</option>
              <option value="replacement" {{ request('request_type') == 'replacement' ? 'selected' : '' }}>Replacement</option>
            </select>
          </div>

          {{-- Local/Foreign (for Registration) --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Local/Foreign</label>
            <select name="local_foreign" class="form-select">
              <option value="">All</option>
              <option value="local" {{ request('local_foreign') == 'local' ? 'selected' : '' }}>Local</option>
              <option value="foreign" {{ request('local_foreign') == 'foreign' ? 'selected' : '' }}>Foreign</option>
            </select>
          </div>

          {{-- Email Address --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ request('email') }}" placeholder="Search by email...">
          </div>

          {{-- Application Reference --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Reference Number</label>
            <input type="text" name="reference" class="form-control" value="{{ request('reference') }}" placeholder="Search by reference...">
          </div>

          {{-- Date --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Date</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
          </div>

          {{-- Month --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Month</label>
            <select name="month" class="form-select">
              <option value="">All Months</option>
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                  {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                </option>
              @endfor
            </select>
          </div>

          {{-- Year --}}
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Year</label>
            <select name="year" class="form-select">
              <option value="">All Years</option>
              @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-sm">
              <i class="ri-search-line me-1"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Export Options (for actual database records only) --}}
  @if(($list ?? '') !== 'new' && ($list ?? '') !== 'pending')
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="fw-bold m-0"><i class="ri-download-2-line me-2"></i>Export Data</h6>
          <small class="text-muted">Export filtered results (database records only)</small>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.officer.export.csv') }}?{{ http_build_query(request()->query()) }}" class="btn btn-sm btn-outline-success">
            <i class="ri-file-excel-line me-1"></i>Export CSV
          </a>
          <a href="{{ route('staff.officer.export.pdf') }}?{{ http_build_query(request()->query()) }}" class="btn btn-sm btn-outline-danger">
            <i class="ri-file-pdf-line me-1"></i>Export PDF
          </a>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Applications Table --}}
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th><i class="ri-hashtag me-1"></i>Reference</th>
              <th><i class="ri-user-line me-1"></i>Applicant Details</th>
              <th><i class="ri-file-list-line me-1"></i>Type</th>
              <th><i class="ri-calendar-line me-1"></i>Submission Date & Time</th>
              <th class="text-end"><i class="ri-settings-3-line me-1"></i>Action</th>
            </tr>
          </thead>
          <tbody>
          @forelse($applications as $app)
            <tr>
              <td class="fw-semibold">{{ $app->reference ?? ('#'.$app->id) }}</td>
              <td>
                <div class="fw-semibold">{{ $app->applicant->name ?? '—' }}</div>
                @if($app->applicant && $app->applicant->email)
                  <div class="small text-muted">{{ $app->applicant->email }}</div>
                @endif
              </td>
              <td>
                <div class="fw-semibold">{{ $app->applicationTypeLabel() }}</div>
                @if($app->request_type)
                  <div class="small text-muted">{{ ucfirst($app->request_type) }}</div>
                @endif
                @if($app->application_type === 'registration' && isset($app->form_data['ownership_type']))
                  <div class="small text-muted">{{ ucfirst($app->form_data['ownership_type']) }}</div>
                @endif
              </td>
              <td>
                <div>{{ optional($app->submitted_at)->format('M d, Y') ?? optional($app->created_at)->format('M d, Y') }}</div>
                <div class="small text-muted">{{ optional($app->submitted_at)->format('H:i') ?? optional($app->created_at)->format('H:i') }}</div>
              </td>
              <td class="text-end">
                <div class="zmc-action-strip justify-content-end">
                  {{-- Seek Guidance from Registrar (replaces message functionality) --}}
                  <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-warning" 
                          onclick="seekGuidance({{ $app->id }})" 
                          title="Seek Guidance from Registrar">
                    <i class="ri-question-line"></i>
                  </button>
                  
                  {{-- View Application --}}
                  <a href="{{ route('staff.officer.applications.show', $app) }}" class="btn btn-sm zmc-icon-btn btn-outline-primary" title="View application">
                    <i class="fa-regular fa-eye"></i>
                  </a>
                  
                  {{-- Request Correction --}}
                  @if(in_array($app->status, [\App\Models\Application::SUBMITTED, \App\Models\Application::SUBMITTED_WITH_APP_FEE, \App\Models\Application::OFFICER_REVIEW]))
                  <a href="{{ route('staff.officer.applications.show', $app) }}#correction" class="btn btn-sm zmc-icon-btn btn-outline-dark" title="Request correction">
                    <i class="fa-regular fa-comment-dots"></i>
                  </a>
                  @endif
                  
                  {{-- Approve --}}
                  @if(in_array($app->status, [\App\Models\Application::SUBMITTED, \App\Models\Application::SUBMITTED_WITH_APP_FEE, \App\Models\Application::OFFICER_REVIEW]))
                  <a href="{{ route('staff.officer.applications.show', $app) }}#approve" class="btn btn-sm zmc-icon-btn btn-outline-success" title="Approve">
                    <i class="fa-solid fa-check"></i>
                  </a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No applications found.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($applications, 'links'))
        <div class="mt-3">{{ $applications->links() }}</div>
      @endif
    </div>
  </div>
</div>

{{-- Seek Guidance Modal --}}
<div class="modal fade" id="guidanceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="{{ route('staff.officer.seek-guidance') }}" id="guidanceForm">
      @csrf
      <input type="hidden" name="application_id" id="guidanceApplicationId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="ri-question-line me-2 text-warning"></i>
            Seek Guidance from Registrar
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            Use this when an application is complicated and needs the Registrar's perusal and approval.
          </div>
          <label class="form-label">Reason for seeking guidance *</label>
          <textarea name="guidance_reason" class="form-control" rows="4" required 
                    placeholder="Please explain why this application needs Registrar's guidance..."></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-send-plane-line me-1"></i>Send to Registrar
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function clearFilters() {
  document.getElementById('filterForm').reset();
  window.location.href = '{{ request()->url() }}';
}

function seekGuidance(applicationId) {
  document.getElementById('guidanceApplicationId').value = applicationId;
  var modal = new bootstrap.Modal(document.getElementById('guidanceModal'));
  modal.show();
}
</script>
@endsection
