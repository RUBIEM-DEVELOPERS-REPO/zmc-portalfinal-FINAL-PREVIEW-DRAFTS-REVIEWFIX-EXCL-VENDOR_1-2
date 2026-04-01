@extends('layouts.portal')

@section('title', $title ?? 'Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">{{ $title ?? 'Applications' }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Filter and search applications. Export functionality available in Records Section.</div>
    </div>

    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm px-3" data-bs-toggle="modal" data-bs-target="#advancedFiltersModal">
        <i class="ri-filter-3-line me-1"></i> Advanced Filters
      </button>
      <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3" title="Refresh">
        <i class="ri-refresh-line me-1"></i> Refresh
      </a>
    </div>
  </div>

  {{-- Year selector and Processing status tabs --}}
  @php
    $activeYear = request('year', now()->year);
    $activeProcessingStatus = request('processing_status', 'all');
  @endphp
  <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
    <div class="d-flex gap-2 align-items-center">
      <label class="small fw-bold text-muted mb-0">Year:</label>
      @for($y = now()->year; $y >= now()->year - 5; $y--)
        <a class="btn btn-sm {{ $activeYear == $y ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['year' => $y]) }}">{{ $y }}</a>
      @endfor
    </div>

    <span class="mx-2 text-muted">|</span>

    <div class="d-flex gap-2">
      <a class="btn btn-sm {{ $activeProcessingStatus === 'all' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['processing_status' => 'all']) }}">All</a>
      <a class="btn btn-sm {{ $activeProcessingStatus === 'processed' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['processing_status' => 'processed']) }}">Processed</a>
      <a class="btn btn-sm {{ $activeProcessingStatus === 'unprocessed' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['processing_status' => 'unprocessed']) }}">Unprocessed</a>
    </div>
  </div>

  {{-- Type tabs (Accreditations vs Registrations) - REMOVED "All" tab --}}
  @php
    $activeType = request('application_type');
  @endphp
  <div class="d-flex flex-wrap gap-2 mb-3">
    <a class="btn btn-sm {{ $activeType==='accreditation' || !$activeType ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => 'accreditation']) }}">Accreditations</a>
    <a class="btn btn-sm {{ $activeType==='registration' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => 'registration']) }}">Registrations</a>

    @if(($list ?? '') === 'rejected')
      <span class="mx-2 text-muted">|</span>
      @php $activeStatus = request('status'); @endphp
      <a class="btn btn-sm {{ $activeStatus===\App\Models\Application::OFFICER_REJECTED ? 'btn-danger' : 'btn-outline-danger' }}" href="{{ request()->fullUrlWithQuery(['status' => \App\Models\Application::OFFICER_REJECTED]) }}">Rejected</a>
      <a class="btn btn-sm {{ $activeStatus===\App\Models\Application::RETURNED_TO_OFFICER ? 'btn-warning' : 'btn-outline-warning' }}" href="{{ request()->fullUrlWithQuery(['status' => \App\Models\Application::RETURNED_TO_OFFICER]) }}">Returned</a>
      <a class="btn btn-sm {{ !$activeStatus ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['status' => null]) }}">Both</a>
    @endif
  </div>

  {{-- Quick Search --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">

        <div class="col-12 col-md-6">
          <label class="form-label small fw-bold">Quick Search</label>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, Accreditation Number, or Registration Number" />
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label small fw-bold">From</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" />
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label small fw-bold">To</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" />
        </div>

        <input type="hidden" name="year" value="{{ request('year', now()->year) }}" />
        <input type="hidden" name="processing_status" value="{{ request('processing_status', 'all') }}" />
        <input type="hidden" name="application_type" value="{{ request('application_type') }}" />

        <div class="col-12 col-md-2 d-flex gap-2">
          <button class="btn btn-dark w-100"><i class="ri-search-line me-1"></i>Search</button>
          <a class="btn btn-outline-secondary w-100" href="{{ url()->current() }}">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Table --}}
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Ref</th>
              <th>Applicant</th>
              <th>Type</th>
              <th>Status</th>
              <th>Submitted</th>
              <th>New or Renewal</th>
              <th>Foreign or Local</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($applications as $app)
              @php
                // Determine New or Renewal
                $requestType = strtolower((string)($app->request_type ?? 'new'));
                $newOrRenewal = $requestType === 'renewal' ? 'Renewal' : 'New';
                
                // Determine Foreign or Local
                $scope = strtolower((string)($app->journalist_scope ?? $app->residency_type ?? 'local'));
                $foreignOrLocal = $scope === 'foreign' ? 'Foreign' : 'Local';
              @endphp
              <tr>
                <td class="fw-bold">{{ $app->reference }}</td>
                <td>
                  <div class="fw-semibold">{{ $app->applicant?->name ?? '—' }}</div>
                  <div class="text-muted small">{{ $app->applicant?->email ?? '—' }}</div>
                </td>
                <td>
                  <span class="badge bg-dark">{{ $app->applicationTypeLabel() }}</span>
                </td>
                <td class="text-capitalize small">{{ str_replace('_',' ', $app->status) }}</td>
                <td class="small">{{ optional($app->created_at)->format('d M Y') }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $newOrRenewal === 'Renewal' ? 'info' : 'primary' }} px-3">{{ $newOrRenewal }}</span>
                </td>
                <td>
                  <span class="badge rounded-pill bg-{{ $foreignOrLocal === 'Foreign' ? 'warning' : 'success' }} px-3">{{ $foreignOrLocal }}</span>
                </td>
                <td class="text-end">
  <div class="zmc-action-strip justify-content-end">

    {{-- Request correction --}}
    <a
      href="{{ route('staff.officer.applications.show', $app) }}#correction"
      class="btn btn-sm zmc-icon-btn btn-outline-dark"
      @if(!in_array($app->status, [
        \App\Models\Application::SUBMITTED,
        \App\Models\Application::OFFICER_REVIEW,
        \App\Models\Application::RETURNED_TO_OFFICER,
        \App\Models\Application::CORRECTION_REQUESTED,
      ], true)) aria-disabled="true" tabindex="-1" @endif
      data-bs-toggle="tooltip" data-bs-placement="top"
      title="Request correction"
    >
      <i class="fa-regular fa-comment-dots"></i>
    </a>

    {{-- View --}}
    <a
      href="{{ route('staff.officer.applications.show', $app) }}"
      class="btn btn-sm zmc-icon-btn btn-outline-primary"
      data-bs-toggle="tooltip" data-bs-placement="top"
      title="View application"
    >
      <i class="fa-regular fa-eye"></i>
    </a>

    {{-- Approve --}}
    <a
      href="{{ route('staff.officer.applications.show', $app) }}#approve"
      class="btn btn-sm zmc-icon-btn btn-outline-success"
      @if(!in_array($app->status, [
        \App\Models\Application::SUBMITTED,
        \App\Models\Application::OFFICER_REVIEW,
        \App\Models\Application::RETURNED_TO_OFFICER,
        \App\Models\Application::CORRECTION_REQUESTED,
      ], true)) aria-disabled="true" tabindex="-1" @endif
      data-bs-toggle="tooltip" data-bs-placement="top"
      title="Approve"
    >
      <i class="fa-solid fa-check"></i>
    </a>

    {{-- Message --}}
    <a
      href="{{ route('staff.officer.applications.show', $app) }}#message"
      class="btn btn-sm zmc-icon-btn btn-outline-secondary"
      data-bs-toggle="tooltip" data-bs-placement="top"
      title="Message"
    >
      <i class="fa-regular fa-envelope"></i>
    </a>

  </div>
</td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center py-5 text-muted">No applications found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
</div>

{{-- Advanced Filters Modal --}}
<div class="modal fade" id="advancedFiltersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Advanced Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="GET">
        <div class="modal-body">
          <div class="row g-3">
            
            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Gender</label>
              <select class="form-select" name="gender">
                <option value="">All</option>
                <option value="male" @selected(request('gender')==='male')>Male</option>
                <option value="female" @selected(request('gender')==='female')>Female</option>
                <option value="other" @selected(request('gender')==='other')>Other</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Age Range</label>
              <div class="d-flex gap-2 align-items-center">
                <input type="number" name="age_min" value="{{ request('age_min') }}" class="form-control" placeholder="Min" min="0" max="120" />
                <span>to</span>
                <input type="number" name="age_max" value="{{ request('age_max') }}" class="form-control" placeholder="Max" min="0" max="120" />
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Organisation</label>
              <input type="text" name="organisation" value="{{ request('organisation') }}" class="form-control" placeholder="Organisation name" />
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Province</label>
              <select class="form-select" name="province">
                <option value="">All</option>
                <option value="harare" @selected(request('province')==='harare')>Harare</option>
                <option value="bulawayo" @selected(request('province')==='bulawayo')>Bulawayo</option>
                <option value="manicaland" @selected(request('province')==='manicaland')>Manicaland</option>
                <option value="mashonaland_central" @selected(request('province')==='mashonaland_central')>Mashonaland Central</option>
                <option value="mashonaland_east" @selected(request('province')==='mashonaland_east')>Mashonaland East</option>
                <option value="mashonaland_west" @selected(request('province')==='mashonaland_west')>Mashonaland West</option>
                <option value="masvingo" @selected(request('province')==='masvingo')>Masvingo</option>
                <option value="matabeleland_north" @selected(request('province')==='matabeleland_north')>Matabeleland North</option>
                <option value="matabeleland_south" @selected(request('province')==='matabeleland_south')>Matabeleland South</option>
                <option value="midlands" @selected(request('province')==='midlands')>Midlands</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Collection Region</label>
              <select class="form-select" name="collection_region">
                <option value="">All</option>
                <option value="harare" @selected(request('collection_region')==='harare')>Harare</option>
                <option value="bulawayo" @selected(request('collection_region')==='bulawayo')>Bulawayo</option>
                <option value="mutare" @selected(request('collection_region')==='mutare')>Mutare</option>
                <option value="gweru" @selected(request('collection_region')==='gweru')>Gweru</option>
                <option value="masvingo" @selected(request('collection_region')==='masvingo')>Masvingo</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Foreign or Local</label>
              <select class="form-select" name="scope">
                <option value="">All</option>
                <option value="local" @selected(request('scope')==='local')>Local</option>
                <option value="foreign" @selected(request('scope')==='foreign')>Foreign</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">New or Renewal</label>
              <select class="form-select" name="request_type">
                <option value="">All</option>
                <option value="new" @selected(request('request_type')==='new')>New</option>
                <option value="renewal" @selected(request('request_type')==='renewal')>Renewal</option>
                <option value="replacement" @selected(request('request_type')==='replacement')>Replacement</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label small fw-bold">Nationality</label>
              <input type="text" name="nationality" value="{{ request('nationality') }}" class="form-control" placeholder="Nationality" />
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="year" value="{{ request('year', now()->year) }}" />
          <input type="hidden" name="processing_status" value="{{ request('processing_status', 'all') }}" />
          <input type="hidden" name="application_type" value="{{ request('application_type') }}" />
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark"><i class="ri-filter-3-line me-1"></i>Apply Filters</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
