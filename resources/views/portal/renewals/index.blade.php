@extends('layouts.portal')

@section('title', 'My Renewals & Replacements (AP5)')

@section('content')
<div id="renewals-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">My Renewals & Replacements (AP5)</h4>
    <a class="btn btn-secondary" href="{{ route('accreditation.home') }}">
      <i class="ri-arrow-left-line me-1"></i>Back to Dashboard
    </a>
  </div>

  <div class="form-container">
    <div class="form-header">
      <h1>AP5 — Renewal / Replacement</h1>
      <p>Manage your accreditation and registration renewals under the Zimbabwe Media Commission Act (2020)</p>
    </div>

    <div class="form-steps-container">
      {{-- Step Progress Indicator (showing the 4-step process) --}}
      <div class="step-progress">
        <div class="step-progress-bar">
          <div class="step" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Select Type</div>
          </div>
          <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Number Lookup</div>
          </div>
          <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Confirm Changes</div>
          </div>
          <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Payment</div>
          </div>
        </div>
      </div>

      {{-- My Existing Renewals --}}
      @if($renewals->count() > 0)
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="ri-file-list-3-line me-1"></i>My Renewal Applications</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Original Number</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($renewals as $renewal)
                    <tr>
                      <td class="text-capitalize">{{ $renewal->getRenewalTypeLabel() }}</td>
                      <td class="fw-semibold"><code>{{ $renewal->original_number ?? 'N/A' }}</code></td>
                      <td>
                        <span class="badge 
                          @if($renewal->status === 'renewal_produced_ready_for_collection') bg-success
                          @elseif($renewal->status === 'renewal_payment_rejected') bg-danger
                          @elseif($renewal->status === 'renewal_payment_verified') bg-info
                          @elseif(str_contains($renewal->status, 'awaiting')) bg-warning
                          @else bg-secondary
                          @endif">
                          {{ $renewal->getStatusLabel() }}
                        </span>
                      </td>
                      <td class="text-muted">{{ $renewal->created_at->format('d M Y, H:i') }}</td>
                      <td class="text-end">
                        <a class="btn btn-sm btn-primary" href="{{ route('accreditation.renewals.show', $renewal) }}">
                          <i class="ri-eye-line me-1"></i>View
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="text-muted mt-2" style="font-size:12px;">Track the status of your renewal applications here.</div>
          </div>
        </div>

        {{-- Pagination --}}
        @if($renewals->hasPages())
          <div class="mt-3">
            {{ $renewals->links() }}
      {{-- Select Type Section (Embedded) --}}
      <div class="step-content active">
        <h3 class="step-title">Select Application Type</h3>
        <div class="current-step-info">
          <i class="ri-information-line me-2"></i>
          Choose whether you want to renew your accreditation card or request a replacement.
        </div>

        <form method="POST" action="{{ route('accreditation.renewals.store-type') }}">
          @csrf

          <div class="app-type-container">
            <div class="app-type-cards">
              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="renewal" required style="display: none;">
                <i class="ri-refresh-line"></i>
                <h4>Renewal of Accreditation Card</h4>
                <p>Renew your existing media practitioner accreditation card for another period.</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Annual Renewal</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>

              <label class="app-type-card" style="cursor: pointer;">
                <input type="radio" name="renewal_type" value="replacement" required style="display: none;">
                <i class="ri-file-copy-line"></i>
                <h4>Replacement of Accreditation Card</h4>
                <p>Request a replacement for a lost, damaged, or stolen accreditation card.</p>
                <div style="margin-top:15px;">
                  <span class="badge bg-light text-dark">Lost/Damaged/Stolen</span>
                  <span class="badge bg-light text-dark">AP5 Form</span>
                </div>
              </label>
            </div>
          </div>

          @error('renewal_type')
            <div class="alert alert-danger mt-3">{{ $message }}</div>
          @enderror

          <div class="form-buttons">
            <div></div>
            <button type="submit" class="btn btn-primary">
              Continue <i class="ri-arrow-right-line"></i>
            </button>
          </div>
        </form>
      </div>enewals->count() === 0)
        <div class="text-center py-5">
          <i class="ri-file-list-3-line" style="font-size: 64px; color: #cbd5e1;"></i>
          <h5 class="mt-3">No Renewals Yet</h5>
          <p class="text-muted">Click the button above to start your first renewal application.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
// Add selected state to cards when clicked
document.querySelectorAll('.app-type-card').forEach(card => {
  card.addEventListener('click', function() {
    document.querySelectorAll('.app-type-card').forEach(c => c.classList.remove('selected'));
    this.classList.add('selected');
    this.querySelector('input[type="radio"]').checked = true;
  });
});
</script>
@endpush
