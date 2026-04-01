@extends('layouts.portal')

@section('title', $title ?? 'Document Verification')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">{{ $title ?? 'Document Verification' }}</h4>
      <div class="text-muted mt-1" style="font-size:13px;"><i class="ri-information-line me-1"></i>Documents are grouped under each applicant/application for easy review. Registrar and Accreditation Officer can view applicant uploads.</div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" onclick="window.print()" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-printer-line me-1"></i>Export PDF</button>
    </div>
  </div>

  @php $activeType = request('application_type'); @endphp
  <div class="d-flex flex-wrap gap-2 mb-3">
    <a class="btn btn-sm {{ $activeType ? 'btn-outline-dark' : 'btn-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => null]) }}">All</a>
    <a class="btn btn-sm {{ $activeType==='accreditation' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => 'accreditation']) }}">Accreditation Documents</a>
    <a class="btn btn-sm {{ $activeType==='registration' ? 'btn-dark' : 'btn-outline-dark' }}" href="{{ request()->fullUrlWithQuery(['application_type' => 'registration']) }}">Media House Documents</a>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
          <label class="form-label small fw-bold">From</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" />
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label small fw-bold">To</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" />
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Record Number</label>
          <input type="text" name="record_number" value="{{ request('record_number') }}" class="form-control" placeholder="Accreditation / Registration No" />
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Search</label>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ref / Name / Email" />
        </div>
        <input type="hidden" name="application_type" value="{{ request('application_type') }}" />
        <div class="col-12 col-md-3 d-flex gap-2">
          <button class="btn btn-dark w-100"><i class="ri-filter-3-line me-1"></i>Apply</button>
          <a class="btn btn-outline-secondary w-100" href="{{ url()->current() }}">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="accordion" id="docsAccordion">
    @forelse($applications as $idx => $app)
      @php
        $accId = 'acc_'.$app->id;
      @endphp
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading{{ $accId }}">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $accId }}" aria-expanded="false" aria-controls="collapse{{ $accId }}">
            <div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                @php
                  $recNo = ($app->application_type === 'registration') 
                    ? ($app->registrationRecord?->registration_no ?? '—')
                    : ($app->accreditationRecord?->certificate_no ?? '—');
                @endphp
                <div class="fw-bold">
                  <span class="text-primary me-2">#{{ $recNo }}</span>
                  {{ $app->applicant?->name ?? '—' }} <span class="text-muted fw-normal">({{ $app->applicant?->email ?? '—' }})</span>
                </div>
                <div class="small text-muted">Ref: <span class="fw-semibold">{{ $app->reference }}</span> • {{ $app->applicationTypeLabel() }} • Submitted: {{ optional($app->created_at)->format('d M Y') }}</div>
              </div>
              <span class="badge bg-dark">{{ $app->documents->count() }} docs</span>
            </div>
          </button>
        </h2>
        <div id="collapse{{ $accId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $accId }}" data-bs-parent="#docsAccordion">
          <div class="accordion-body">
            <div class="table-responsive">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Document</th>
                    <th>Original Name</th>
                    <th>Status</th>
                    <th class="text-end">Open</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($app->documents as $doc)
                    <tr>
                      <td class="fw-semibold">{{ $doc->document_type }}</td>
                      <td class="small text-muted">{{ $doc->original_name ?? '—' }}</td>
                      <td class="text-capitalize">{{ $doc->status ?? '—' }}</td>
                      <td class="text-end">
                        <a href="{{ $doc->url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-external-link-line me-1"></i>View</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="text-center py-5 text-muted">No documents found for the selected filters.</div>
    @endforelse
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
</div>
@endsection
