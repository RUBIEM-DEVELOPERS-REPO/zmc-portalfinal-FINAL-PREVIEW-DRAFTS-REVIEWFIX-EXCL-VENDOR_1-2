<div class="zmc-action-strip">
  @if($app->is_draft)
    <a class="btn btn-sm zmc-icon-btn btn-outline-secondary" href="{{ request()->routeIs('mediahouse.*') ? route('mediahouse.new') : route('accreditation.new') }}" title="Continue">
      <i class="fa-regular fa-pen-to-square"></i>
    </a>
    <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-danger js-delete-draft" data-app-id="{{ $app->id }}" title="Delete Draft">
      <i class="fa-regular fa-trash-can"></i>
    </button>
  @else
    <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-primary js-view-more" data-app-id="{{ $app->id }}" title="View">
      <i class="fa-regular fa-eye"></i>
    </button>
    @if($status === 'correction_requested')
      <a href="{{ request()->routeIs('mediahouse.*') ? route('mediahouse.applications.edit', $app) : route('accreditation.applications.edit', $app) }}" class="btn btn-sm btn-warning fw-bold" title="Edit & Resubmit">
        <i class="ri-edit-2-line me-1"></i> Edit
      </a>
    @endif
    @if(in_array($status, ['accounts_review','approved_awaiting_payment','registrar_approved_pending_reg_fee']))
      <button type="button" class="btn btn-sm btn-success fw-bold js-pay-now" 
              data-app-id="{{ $app->id }}" 
              data-app-ref="{{ $app->reference }}"
              data-payment-stage="{{ $status === 'registrar_approved_pending_reg_fee' ? 'registration_fee' : (request()->routeIs('mediahouse.*') ? 'application_fee' : 'accreditation_fee') }}"
              title="Pay Now">
        <i class="ri-bank-card-line me-1"></i> Pay
      </button>
    @endif
    @if($status === 'payment_rejected')
      <button type="button" class="btn btn-sm btn-danger fw-bold js-pay-now" 
              data-app-id="{{ $app->id }}" 
              data-app-ref="{{ $app->reference }}"
              data-rejection-reason="{{ $app->proof_review_notes ?? $app->rejection_reason ?? '' }}"
              title="Resubmit Payment">
        <i class="ri-error-warning-line me-1"></i> Resubmit Payment
      </button>
    @endif
    @if($status === 'awaiting_accounts_verification')
      <span class="badge bg-info px-2"><i class="ri-time-line me-1"></i>Verifying Payment</span>
    @endif
    @if($status === 'awaiting_batch_payment')
      <span class="badge bg-soft-warning text-warning px-2"><i class="ri-time-line me-1"></i>Awaiting Media House Payment</span>
    @endif
    @if($status === 'paid_confirmed' && $app->batch_id)
      <a href="{{ route('accreditation.renewals', ['draft' => $app->reference]) }}" class="btn btn-sm btn-primary fw-bold" title="Complete Renewal">
        <i class="ri-edit-2-line me-1"></i> Complete Renewal
      </a>
    @endif
    @if(in_array($status, ['submitted','submitted_with_app_fee','officer_review','registrar_review','accounts_review']))
      <button type="button" class="btn btn-sm zmc-icon-btn btn-outline-danger js-withdraw-app" data-app-id="{{ $app->id }}" title="Withdraw Application">
        <i class="ri-arrow-go-back-line"></i>
      </button>
    @endif
  @endif
</div>
