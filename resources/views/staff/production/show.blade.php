@extends('layouts.portal')
@section('title', 'Production')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="fw-bold mb-1">{{ $application->reference }}</h4>
    <div class="text-muted">{{ strtoupper($application->application_type) }} • {{ str_replace('_',' ', $application->status) }}</div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <form action="{{ route('staff.production.unlock', $application) }}" method="POST" class="d-inline">
       @csrf
       <button class="btn btn-sm btn-outline-warning">
         <i class="ri-lock-unlock-line me-1"></i> Release & Back
       </button>
    </form>
    <a href="{{ route('staff.production.dashboard') }}" class="btn btn-secondary">Back</a>
  </div>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-bold">Applicant & Application</div>
      <div class="card-body">
        @include('staff.partials.application_details_card', ['application' => $application])
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header fw-bold">Documents</div>
      <div class="card-body">
        @if($application->documents && $application->documents->count())
          <div class="list-group">
            @foreach($application->documents as $doc)
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                 href="{{ $doc->url }}" target="_blank" rel="noopener">
                <div>
                  <div class="fw-semibold">{{ $doc->original_name ?? $doc->doc_type }}</div>
                  <div class="small text-muted">{{ strtoupper($doc->doc_type) }} • Uploaded {{ $doc->created_at?->format('Y-m-d H:i') }}</div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success">View</span>
              </a>
            @endforeach
          </div>
        @else
          <div class="text-muted">No documents uploaded yet.</div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-header fw-bold">Production Actions</div>
      <div class="card-body">
        <div class="text-muted small mb-3">
          Generate the accreditation card (media practitioners) or certificate (mass media). QR codes are added for verification.
        </div>

        @if($application->application_type === 'accreditation')
        <form method="POST" action="{{ route('staff.production.applications.generate_card', $application) }}" class="mb-3">
          @csrf
          <label class="form-label fw-semibold">Media practitioner type code</label>
          <input class="form-control mb-2" name="journalist_type_code" placeholder="e.g. JV" value="{{ old('journalist_type_code','JV') }}" required>
          <button class="btn btn-success w-100"><i class="ri-id-card-line me-1"></i>Generate Accreditation Card (PDF)</button>
        </form>
        @else
        <form method="POST" action="{{ route('staff.production.applications.generate_certificate', $application) }}" class="mb-3">
          @csrf
          <button class="btn btn-success w-100"><i class="ri-award-line me-1"></i>Generate Certificate (PDF)</button>
        </form>
        @endif

        <form method="POST" action="{{ route('staff.production.applications.issue', $application) }}">
          @csrf
          <button class="btn btn-outline-success w-100"><i class="ri-checkbox-circle-line me-1"></i>Mark as Issued</button>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
