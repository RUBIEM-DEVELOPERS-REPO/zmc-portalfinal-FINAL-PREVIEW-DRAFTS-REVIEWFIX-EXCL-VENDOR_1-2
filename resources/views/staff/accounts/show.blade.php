@extends('layouts.portal')
@section('title', 'Payment Review')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-dark">
    <i class="ri-arrow-left-line me-1"></i> Back
  </a>
  <div>
    <h4 class="fw-bold mb-1">{{ $application->reference }}</h4>
    <div class="text-muted">{{ strtoupper($application->application_type) }} • {{ str_replace('_',' ', in_array($application->status, ['officer_rejected','registrar_rejected','payment_rejected'], true) ? 'returned_for_correction' : $application->status) }}</div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <form action="{{ route('staff.accounts.applications.unlock', $application) }}" method="POST" class="d-inline">
       @csrf
       <button class="btn btn-sm btn-outline-warning">
         <i class="ri-lock-unlock-line me-1"></i> Release & Back
       </button>
    </form>
    <a href="{{ route('staff.accounts.dashboard') }}" class="btn btn-secondary d-none d-md-inline">Dashboard</a>
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
        <div><b>Name:</b> {{ $application->applicant?->name ?? '—' }}</div>
        <div><b>Email:</b> {{ $application->applicant?->email ?? '—' }}</div>
        <div><b>Collection region:</b> {{ $application->collection_region ?? '—' }}</div>
        <div class="mt-2"><b>Submitted:</b> {{ $application->submitted_at?->format('Y-m-d H:i') ?? '—' }}</div>
        
        @if(in_array($application->status, [\App\Models\Application::PAID_CONFIRMED]) || $application->payment_status === 'paid' || $application->proof_status === 'approved')
          <div class="mt-3">
            <a href="{{ route('staff.accounts.applications.receipt', $application) }}" class="btn btn-sm btn-outline-primary">
              <i class="ri-file-download-line me-1"></i> Download Payment Receipt (PDF)
            </a>
          </div>
        @endif

        <hr>
        <div class="fw-bold mb-3"><i class="ri-file-list-3-line me-1"></i>Application Details</div>
        
        @if($application->form_data)
          @php
            $formData = $application->form_data;
            if (is_string($formData)) $formData = json_decode($formData, true);
            
            $labels = [
              // Accreditation (AP3/AP5)
              'title' => 'Title',
              'surname' => 'Surname',
              'first_name' => 'First Name',
              'other_names' => 'Other Names',
              'dob' => 'Date of Birth',
              'birth_place' => 'Place & Country of Birth',
              'marital_status' => 'Marital Status',
              'gender' => 'Sex',
              'national_reg_no' => 'National ID',
              'passport_no' => 'Passport No',
              'nationality' => 'Nationality',
              'address' => 'Residential Address',
              'phone' => 'Phone',
              'email' => 'Email',
              'employment_type' => 'Employment Type',
              'medium_type' => 'Medium Type',
              'designation' => 'Designation',
              'media_org' => 'Media Organization',
              'collection_region' => 'Collection Office',
              
              // Media House (AP1)
              'contact_name' => 'Contact Person',
              'organization_name' => 'Organization Name',
              'organization_address' => 'Physical Address',
              'organization_email' => 'Organization Email',
              'organization_phone' => 'Organization Phone',
              'website' => 'Website',
            ];
          @endphp

          <div class="form-viewer">
            <table class="table table-sm table-bordered">
              <tbody>
                @foreach($formData as $key => $value)
                  @php if(in_array($key, ['current_step', 'registration_scope', 'journalist_scope'])) continue; @endphp
                  <tr>
                    <td class="bg-light fw-semibold" style="width: 40%;">{{ $labels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td>
                      @if(is_array($value))
                        <pre class="small mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                      @else
                        {{ $value }}
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-muted italic">No specific form data captured.</div>
        @endif
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

  @if(isset($previousApplications) && $previousApplications->count())
  <div class="col-12">
    <div class="card mt-3">
      <div class="card-header fw-bold d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#prevAppsPanel" role="button" aria-expanded="false">
        <span><i class="ri-history-line me-1"></i> Previous Applications by This Applicant ({{ $previousApplications->count() }})</span>
        <i class="ri-arrow-down-s-line"></i>
      </div>
      <div class="collapse" id="prevAppsPanel">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Reference</th>
                  <th>Type</th>
                  <th>Request</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($previousApplications as $prevApp)
                  <tr>
                    <td class="small fw-bold">{{ $prevApp->reference }}</td>
                    <td class="small text-capitalize">{{ $prevApp->application_type ?? '—' }}</td>
                    <td>
                      @php
                        $pReqType = $prevApp->request_type ?? 'new';
                        $pReqBadge = match($pReqType) { 'renewal' => 'warning', 'replacement' => 'info', default => 'success' };
                      @endphp
                      <span class="badge bg-{{ $pReqBadge }}">{{ ucfirst($pReqType) }}</span>
                    </td>
                    <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', in_array($prevApp->status, ['officer_rejected','registrar_rejected','payment_rejected'], true) ? 'returned_for_correction' : $prevApp->status)) }}</span></td>
                    <td class="small text-muted">{{ $prevApp->created_at?->format('d M Y') ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if(isset($previousPayments) && $previousPayments->count())
  <div class="col-12">
    <div class="card mt-3">
      <div class="card-header fw-bold d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#prevPaymentsPanel" role="button" aria-expanded="false">
        <span><i class="ri-bank-card-line me-1"></i> Previous Payments by This Applicant ({{ $previousPayments->count() }})</span>
        <i class="ri-arrow-down-s-line"></i>
      </div>
      <div class="collapse" id="prevPaymentsPanel">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Reference</th>
                  <th>Method</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($previousPayments as $prevPayment)
                  <tr>
                    <td class="small fw-bold">{{ $prevPayment->reference }}</td>
                    <td class="small text-uppercase">{{ $prevPayment->method ?? '—' }}</td>
                    <td class="small">{{ $prevPayment->amount ?? '—' }} {{ $prevPayment->currency ?? 'USD' }}</td>
                    <td>
                      @php
                        $payBadge = match($prevPayment->status) { 'paid' => 'success', 'pending' => 'warning', 'rejected','reversed','voided' => 'danger', default => 'secondary' };
                      @endphp
                      <span class="badge bg-{{ $payBadge }}">{{ ucfirst($prevPayment->status) }}</span>
                    </td>
                    <td class="small text-muted">{{ $prevPayment->created_at?->format('d M Y') ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="col-lg-5">
    <div class="card">
      <div class="card-header fw-bold">Payments Actions</div>
      <div class="card-body">
        <div class="text-muted small mb-3">
          Confirm payment proof / waiver. When approved, the application is sent to the Registrar.
        </div>

        @if(in_array($application->status, ['approved_pending_payment','payment_proof_uploaded','waiver_uploaded','returned_from_payments']))
          <form method="POST" action="{{ route('staff.accounts.applications.paid', $application) }}" class="mb-3">
            @csrf
            <label class="form-label fw-semibold">Decision notes (optional)</label>
            <textarea class="form-control mb-2" name="decision_notes" rows="3" placeholder="e.g. Proof valid / waiver accepted"></textarea>
            <button class="btn btn-success w-100"><i class="ri-check-line me-1"></i>Confirm Payment/Waiver & Send to Registrar</button>
          </form>

          <form method="POST" action="{{ route('staff.accounts.applications.return', $application) }}">
            @csrf
            <label class="form-label fw-semibold">Rejection / Return reason</label>
            <textarea class="form-control mb-2" name="decision_notes" rows="3" required placeholder="Explain why payment/waiver is rejected"></textarea>
            <button class="btn btn-outline-danger w-100"><i class="ri-close-line me-1"></i>Return for Correction</button>
          </form>
        @else
          <div class="alert alert-info mb-0">
            This application is not currently in the payments verification stage.
          </div>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection
