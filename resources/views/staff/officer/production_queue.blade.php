@extends('layouts.portal')
@section('title', 'Production Queue')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0">Production Queue</h4>
  <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Filters --}}
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">

      <div class="col-12 col-md-3">
        <label class="form-label small fw-bold">Search</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ref / name / email" />
      </div>

      <div class="col-12 col-md-3">
        <label class="form-label small fw-bold">Application Type</label>
        <select class="form-select" name="application_type">
          <option value="">All</option>
          <option value="accreditation" @selected(request('application_type')==='accreditation')>Accreditation</option>
          <option value="registration" @selected(request('application_type')==='registration')>Registration</option>
        </select>
      </div>

      <div class="col-12 col-md-2">
        <label class="form-label small fw-bold">Request Type</label>
        <select class="form-select" name="request_type">
          <option value="">All</option>
          <option value="new" @selected(request('request_type')==='new')>New</option>
          <option value="renewal" @selected(request('request_type')==='renewal')>Renewal</option>
          <option value="replacement" @selected(request('request_type')==='replacement')>Replacement</option>
        </select>
      </div>

      <div class="col-12 col-md-2">
        <label class="form-label small fw-bold">Scope</label>
        <select class="form-select" name="scope">
          <option value="">Both</option>
          <option value="local" @selected(request('scope')==='local')>Local</option>
          <option value="foreign" @selected(request('scope')==='foreign')>Foreign</option>
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label small fw-bold">Month</label>
        <select name="month" class="form-select">
          <option value="">All</option>
          @foreach(range(1,12) as $m)
            <option value="{{ $m }}" @selected(request('month') == $m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label small fw-bold">Year</label>
        <select name="year" class="form-select">
          <option value="">All</option>
          @foreach(range(date('Y'), date('Y')-5) as $y)
            <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-2 d-flex gap-2">
        <button class="btn btn-dark w-100"><i class="ri-filter-3-line me-1"></i>Apply</button>
        <a class="btn btn-outline-secondary w-100" href="{{ url()->current() }}">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header fw-bold">Payment Verified — Ready for Production</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th>#</th>
            <th>Reference</th>
            <th>Applicant</th>
            <th>Type</th>
            <th>Request</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $i => $app)
            @php
              $rowNo = $applications->firstItem() + $i;
              $requestBadge = match($app->request_type) {
                'renewal' => 'warning',
                'replacement' => 'info',
                default => 'success',
              };
            @endphp
            <tr>
              <td class="text-muted small">{{ $rowNo }}</td>
              <td class="fw-bold">{{ $app->reference }}</td>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="text-capitalize">{{ $app->application_type ?? '—' }}</td>
              <td>
                <span class="badge bg-{{ $requestBadge }}">{{ ucfirst($app->request_type ?? 'new') }}</span>
              </td>
              <td><span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $app->status)) }}</span></td>
              <td class="small">{{ optional($app->created_at)->format('d M Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">No applications in the production queue.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if(method_exists($applications, 'links'))
  <div class="mt-3">{{ $applications->links() }}</div>
@endif
@endsection
