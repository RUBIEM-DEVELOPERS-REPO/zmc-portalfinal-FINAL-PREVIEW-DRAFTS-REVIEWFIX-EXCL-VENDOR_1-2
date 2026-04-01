@extends('layouts.portal')
@section('title', $title ?? 'Applications')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">{{ $title ?? 'Applications' }}</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Supervisory view — use this list to review, flag, or message the officer about any application.
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i> Back
      </a>
    </div>
  </div>

  {{-- Filter Tabs for "All Applications" --}}
  @if($bucket === 'all')
  <div class="zmc-card mb-3 p-2 bg-light border-0">
    <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="small fw-bold text-muted">Status Group</label>
        <select name="status_filter" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Applications</option>
          <option value="new" {{ request('status_filter') === 'new' ? 'selected' : '' }}>New Applications</option>
          <option value="awaiting_approval" {{ request('status_filter') === 'awaiting_approval' ? 'selected' : '' }}>Awaiting Approval</option>
          <option value="approved" {{ request('status_filter') === 'approved' ? 'selected' : '' }}>Approved</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="small fw-bold text-muted">Search</label>
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Reference, Name…" value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
      </div>
      <div class="col-md-1">
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
      </div>
    </form>
  </div>
  @endif

  {{-- Sub-tabs for "Approved" bucket --}}
  @if($bucket === 'approved')
  <div class="mb-3 d-flex gap-2 flex-wrap">
    @php $rt = request('request_type', ''); @endphp
    <a href="{{ url()->current() }}" class="btn btn-sm {{ $rt === '' ? 'btn-dark' : 'btn-outline-secondary' }}">
      <i class="ri-checkbox-circle-line me-1"></i> All Approved
    </a>
    <a href="{{ url()->current() . '?request_type=new' }}" class="btn btn-sm {{ $rt === 'new' ? 'btn-success' : 'btn-outline-success' }}">
      <i class="ri-sparkling-2-line me-1"></i> New Applications
    </a>
    <a href="{{ url()->current() . '?request_type=renewal' }}" class="btn btn-sm {{ $rt === 'renewal' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
      <i class="ri-calendar-todo-line me-1"></i> Renewals
    </a>
    <a href="{{ url()->current() . '?request_type=replacement' }}" class="btn btn-sm {{ $rt === 'replacement' ? 'btn-info text-white' : 'btn-outline-info' }}">
      <i class="ri-swap-line me-1"></i> Replacements
    </a>
  </div>
  <div class="mb-3">
    <form action="{{ url()->current() }}" method="GET" class="row g-2">
      @if(request('request_type'))
        <input type="hidden" name="request_type" value="{{ request('request_type') }}">
      @endif
      <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Reference, Name…" value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-dark btn-sm w-100">Search</button>
      </div>
    </form>
  </div>
  @endif

  {{-- Table --}}
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0">
        <i class="ri-list-check-2 me-2" style="color:var(--zmc-accent)"></i>
        {{ $title ?? 'Applications' }}
      </h6>
      @if(method_exists($applications, 'currentPage'))
        <div class="small text-muted">Page {{ $applications->currentPage() }} of {{ $applications->lastPage() }} &nbsp;|&nbsp; {{ $applications->total() }} records</div>
      @endif
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th style="width:50px;">#</th>
            <th>Ref</th>
            <th>Applicant</th>
            <th>Request Type</th>
            <th>Date</th>
            <th>Status</th>
            @if($bucket === 'corrections')
            <th>Payment Status</th>
            <th style="min-width:220px;">Return Reason</th>
            @endif
            <th class="text-end" style="min-width:100px;">Action</th>
          </tr>
        </thead>
        <tbody>
        @forelse($applications as $i => $app)
          @php
            $status = strtolower((string)($app->status ?? ''));
            $badge = match($status) {
              'registrar_approved','accounts_review','production_queue','issued' => 'success',
              'returned_to_accounts','returned_to_officer'                      => 'warning',
              'registrar_rejected'                                               => 'danger',
              'forwarded_to_registrar'                                           => 'info',
              default => 'secondary',
            };
            $reqType = $app->request_type ?? 'new';
            $reqBadge = match($reqType) { 'renewal' => 'warning', 'replacement' => 'info', default => 'success' };
            $rowNo = (method_exists($applications,'firstItem') && $applications->firstItem())
              ? ($applications->firstItem() + $i) : ($i + 1);
            $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));

            // Return reason from decision_notes or form_data
            $returnReason = $app->decision_notes ?? null;
            if (!$returnReason && !empty($app->form_data['return_reason'])) {
              $returnReason = $app->form_data['return_reason'];
            }
          @endphp
          <tr @if($app->is_flagged) style="background:rgba(220,38,38,.05);" @endif>
            <td class="text-muted small">{{ $rowNo }}</td>
            <td class="fw-bold">{{ $ref }}</td>
            <td>
              {{ $app->applicant?->name ?? '—' }}
              @if($app->is_flagged)
                <div class="mt-1"><span class="badge bg-danger text-white" style="font-size:10px;"><i class="ri-error-warning-fill me-1"></i>Flagged</span></div>
              @endif
              @if($app->registrar_reviewed_at)
                <div class="mt-1"><span class="badge bg-success-subtle text-success" style="font-size:10px;"><i class="ri-checkbox-circle-line me-1"></i>Reviewed</span></div>
              @endif
            </td>
            <td>
              <span class="badge bg-{{ $reqBadge }} text-{{ $reqBadge === 'warning' ? 'dark' : 'white' }}">{{ ucfirst($reqType) }}</span>
            </td>
            <td class="small text-muted">{{ \Carbon\Carbon::parse($app->created_at)->format('d M Y') }}</td>
            <td>
              <span class="badge rounded-pill bg-{{ $badge }} px-3">
                {{ ucwords(str_replace('_', ' ', $status ?: '—')) }}
              </span>
              @if(!empty($app->payment_status))
                <div class="mt-1 small text-muted">Pay: {{ ucwords(str_replace('_',' ',$app->payment_status)) }}</div>
              @endif
            </td>
            @if($bucket === 'corrections')
            <td class="small">
              @if(!empty($app->payment_status))
                <span class="badge bg-{{ $app->payment_status === 'paid' ? 'success' : 'warning text-dark' }}">
                  {{ ucwords(str_replace('_',' ',$app->payment_status)) }}
                </span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="small">
              @if($returnReason)
                <span class="text-danger fw-semibold"><i class="ri-error-warning-line me-1"></i>{{ Str::limit($returnReason, 80) }}</span>
              @else
                <span class="text-muted fst-italic">No reason recorded</span>
              @endif
            </td>
            @endif
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.registrar.applications.show', $app) }}">
                <i class="fa-regular fa-eye me-1"></i> Open
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="10" class="text-center text-muted py-4">No records found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($applications, 'links'))
      <div class="p-3 border-top">
        {{ $applications->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
