@extends('layouts.portal')
@section('title', 'Application Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-dark">
    <i class="ri-arrow-left-line me-1"></i> Back
  </a>
  <div>
    @php
      $name = ($application->application_type === 'registration') 
        ? ($application->form_data['org_name'] ?? $application->form_data['organization_name'] ?? $application->applicant?->name)
        : (($application->form_data['first_name'] ?? '') . ' ' . ($application->form_data['surname'] ?? '') ?: $application->applicant?->name);
    @endphp
    <h4 class="fw-bold mb-0">{{ $application->reference }}</h4>
    <div class="fw-bold text-dark my-1" style="font-size: var(--font-size-lg);"><i class="ri-user-line me-1"></i> {{ $name }}</div>
    <div class="text-muted small text-uppercase">
      {{ str_replace('_', ' ', $application->application_type) }} • {{ $application->request_type }} • {{ $application->journalist_scope ?? $application->residency_type ?? 'local' }}
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <form action="{{ route('staff.officer.applications.unlock', $application) }}" method="POST" class="d-inline">
       @csrf
       <button class="btn btn-sm btn-outline-warning">
         <i class="ri-lock-unlock-line me-1"></i> Release & Back
       </button>
    </form>
    <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-secondary d-none d-md-inline">Dashboard</a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3">
  <div class="col-md-7">
    @include('staff.partials.application_details_card', ['application' => $application])

    <div class="card mt-3">
      <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center">
        <span><i class="ri-history-line me-1"></i> Previous Applications for this Applicant</span>
        <span class="badge bg-white text-dark border">{{ $previousApplications->count() }} found</span>
      </div>
      <div class="card-body p-0">
        @if($previousApplications->count())
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle small">
              <thead class="bg-light">
                <tr>
                  <th class="ps-3">Ref</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th class="text-end pe-3">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($previousApplications as $pa)
                  @php
                    $paStatus = strtolower((string)$pa->status);
                    $paBadge = match(true) {
                      str_contains($paStatus, 'approved') || $paStatus === 'issued' => 'success',
                      str_contains($paStatus, 'rejected') => 'danger',
                      default => 'info'
                    };
                  @endphp
                  <tr>
                    <td class="ps-3 fw-bold">{{ $pa->reference }}</td>
                    <td class="text-capitalize">{{ $pa->application_type }}</td>
                    <td><span class="badge bg-{{ $paBadge }}-subtle text-{{ $paBadge }} border border-{{ $paBadge }}">{{ ucwords(str_replace('_',' ', $pa->status)) }}</span></td>
                    <td>{{ $pa->created_at?->format('d M Y') }}</td>
                    <td class="text-end pe-3">
                      <a href="{{ route('staff.officer.applications.show', $pa) }}" class="btn btn-xs btn-outline-primary py-0" title="View this application">
                        <i class="ri-eye-line"></i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="p-4 text-center text-muted small">No other applications found for this applicant.</div>
        @endif
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center">
        <span><i class="ri-history-line me-1"></i> Payment History for this Applicant</span>
        <span class="badge bg-white text-dark border">{{ $userPayments->count() }} found</span>
      </div>
      <div class="card-body p-0">
        @if($userPayments->count())
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle small">
              <thead class="bg-light">
                <tr>
                  <th class="ps-3">Ref</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($userPayments as $p)
                  @php
                    $pStatus = strtolower((string)$p->status);
                    $pBadge = match(true) {
                      $pStatus === 'paid' || $pStatus === 'confirmed' => 'success',
                      $pStatus === 'reversed' || $pStatus === 'failed' => 'danger',
                      default => 'info'
                    };
                  @endphp
                  <tr>
                    <td class="ps-3 fw-bold">{{ $p->reference }}</td>
                    <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                    <td class="text-capitalize">{{ $p->method }}</td>
                    <td><span class="badge bg-{{ $pBadge }}-subtle text-{{ $pBadge }} border border-{{ $pBadge }} text-capitalize">{{ $p->status }}</span></td>
                    <td>{{ $p->created_at?->format('d M Y') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="p-4 text-center text-muted small">No previous payments found for this applicant.</div>
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

  <div class="col-md-5">
    <div class="card">
      <div class="card-header fw-bold">Actions</div>
      <div class="card-body">

        @if(in_array($application->status, ['submitted','needs_correction','returned_from_payments','returned_from_registrar']))
          <form method="POST" action="{{ route('staff.officer.applications.approve', $application) }}" class="mb-3">
            @csrf
            @php
              $isRegistration = ($application->application_type ?? '') === 'registration';
              $cats = $isRegistration ? \App\Models\Application::massMediaCategories() : \App\Models\Application::accreditationCategories();
              $label = $isRegistration ? 'Mass Media Category' : 'Accreditation Category';
            @endphp

            <label class="form-label fw-semibold">{{ $label }} (required)</label>
            <select class="form-select mb-2" name="category_code" required>
              <option value="">-- Select category --</option>
              @foreach($cats as $code => $name)
                <option value="{{ $code }}" @selected(($isRegistration ? $application->media_house_category_code : $application->accreditation_category_code) === $code)>{{ $code }} - {{ $name }}</option>
              @endforeach
            </select>

            <label class="form-label fw-semibold">Approve notes (optional)</label>
            <textarea class="form-control mb-2" name="decision_notes" rows="3"></textarea>
            <button class="btn btn-success w-100">Approve & Send to Registrar</button>
          </form>

          <form method="POST" action="{{ route('staff.officer.applications.requestCorrection', $application) }}" class="mb-3">
            @csrf
            <label class="form-label fw-semibold">Request correction (required)</label>
            <textarea class="form-control mb-2" name="notes" rows="3" required></textarea>
            <button class="btn btn-warning w-100">Request Correction</button>
          </form>

          {{-- Forward Without Approval (for waivers/special cases) --}}
          <button type="button" class="btn btn-outline-warning w-100 mb-3" data-bs-toggle="modal" data-bs-target="#forwardNoApprovalModal">
            <i class="ri-arrow-right-line me-1"></i> Forward to Registrar (No Approval)
          </button>
        @else
          <div class="alert alert-light border">No actions available for this status.</div>
        @endif

        <hr>

        <form method="POST" action="{{ route('staff.officer.applications.message', $application) }}">
          @csrf
          <label class="form-label fw-semibold">Message applicant</label>
          <textarea class="form-control mb-2" name="message" rows="3" required></textarea>
          <button class="btn btn-primary w-100">Send Message</button>
        </form>

      </div>
    </div>
  </div>
</div>

{{-- Forward Without Approval Modal --}}
<div class="modal fade" id="forwardNoApprovalModal" tabindex="-1" aria-labelledby="forwardNoApprovalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #000000; color: #facc15; border: 2px solid #facc15;">
      <div class="modal-header" style="border-bottom: 1px solid #facc15;">
        <h5 class="modal-title" id="forwardNoApprovalModalLabel">
          <i class="ri-alert-line me-2"></i>Forward to Registrar Without Approval
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('staff.officer.applications.forward-no-approval', $application) }}">
        @csrf
        <div class="modal-body">
          <div class="alert" style="background: rgba(250, 204, 21, 0.1); border: 1px solid #facc15; color: #facc15;">
            <i class="ri-information-line me-2"></i>
            <strong>Special Case:</strong> This action forwards the application to the Registrar WITHOUT your approval. Use this for waiver submissions or other special circumstances that require Registrar review before payment verification.
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Reason Type <span class="text-danger">*</span></label>
            <select class="form-select" id="reasonType" style="background: #1a1a1a; color: #facc15; border: 1px solid #facc15;">
              <option value="">-- Select reason type --</option>
              <option value="Waiver submitted">Waiver Submitted</option>
              <option value="Special payment arrangement">Special Payment Arrangement</option>
              <option value="Requires Registrar review">Requires Registrar Review</option>
              <option value="Complicated payment method">Complicated Payment Method</option>
              <option value="Other">Other (specify below)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Detailed Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" name="reason" rows="4" required 
                      style="background: #1a1a1a; color: #facc15; border: 1px solid #facc15;"
                      placeholder="Provide detailed explanation for forwarding without approval..."></textarea>
            <small class="form-text" style="color: #facc15; opacity: 0.7;">
              This reason will be visible to the Registrar and included in the audit trail.
            </small>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid #facc15;">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-color: #facc15; color: #facc15;">
            Cancel
          </button>
          <button type="submit" class="btn" style="background: #facc15; color: #000000; font-weight: 600;">
            <i class="ri-arrow-right-line me-1"></i> Forward to Registrar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Auto-fill reason textarea when reason type is selected
document.getElementById('reasonType')?.addEventListener('change', function() {
  const reasonTextarea = document.querySelector('textarea[name="reason"]');
  if (this.value && this.value !== 'Other' && reasonTextarea) {
    const currentText = reasonTextarea.value.trim();
    if (!currentText || currentText === reasonTextarea.placeholder) {
      reasonTextarea.value = this.value + ': ';
      reasonTextarea.focus();
    }
  }
});
</script>

@endsection
