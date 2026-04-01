@extends('layouts.portal')
@section('title', 'Waiver Requests')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Waiver Requests</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">Review waiver requests and supporting documents, then approve or reject with notes.</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <div class="zmc-card p-0">
    <div class="p-3 border-bottom fw-bold"><i class="ri-inbox-line me-2" style="color:var(--zmc-accent)"></i> Pending waivers</div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th>Applicant</th>
            <th>Application #</th>
            <th>Waiver doc</th>
            <th>Waiver type</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($applications as $app)
            @php
              $ref = $app->reference ?? ('APP-' . str_pad((int)$app->id, 5, '0', STR_PAD_LEFT));
              $waiverUrl = $app->waiver_path ? asset('storage/' . ltrim($app->waiver_path, '/')) : null;
              $waiverType = data_get($app->form_data, 'waiver_type') ?? data_get($app->form_data, 'waiver') ?? null;
            @endphp
            <tr>
              <td>{{ $app->applicant?->name ?? '—' }}</td>
              <td class="fw-bold">{{ $ref }}</td>
              <td>
                @if($waiverUrl)
                  <a href="{{ $waiverUrl }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="ri-attachment-2"></i> View</a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="text-muted">{{ $waiverType ? ucfirst($waiverType) : '—' }}</td>
              <td><span class="badge rounded-pill bg-info px-3">Submitted</span></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.accounts.applications.show', $app->id) }}" title="Open application"><i class="ri-eye-line"></i></a>
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveWaiver{{ $app->id }}" title="Approve"><i class="ri-check-line"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectWaiver{{ $app->id }}" title="Reject"><i class="ri-close-line"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Wire to messaging module">Request info</button>
              </td>
            </tr>

            @push('zmc_modals')
              <div class="modal fade" id="approveWaiver{{ $app->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <form class="modal-content" method="POST" action="{{ route('staff.accounts.waivers.approve', $app->id) }}">
                    @csrf
                    <div class="modal-header zmc-modal-header">
                      <div>
                        <div class="zmc-modal-title"><i class="ri-check-line me-2" style="color:var(--zmc-accent)"></i> Approve waiver <span class="ms-2 text-muted" style="font-weight:800;font-size: var(--font-size-sm);">{{ $ref }}</span></div>
                        <div class="zmc-modal-sub">Approving a waiver should be backed by policy/authority. Notes are optional but recommended.</div>
                      </div>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <label class="form-label">Notes (optional)</label>
                      <textarea class="form-control" name="waiver_review_notes" rows="4" maxlength="5000" placeholder="e.g., approved under policy section X, authorised by ..."></textarea>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-success"><i class="ri-check-line me-1"></i> Approve</button>
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="modal fade" id="rejectWaiver{{ $app->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <form class="modal-content" method="POST" action="{{ route('staff.accounts.waivers.reject', $app->id) }}">
                    @csrf
                    <div class="modal-header zmc-modal-header">
                      <div>
                        <div class="zmc-modal-title"><i class="ri-close-line me-2" style="color:#dc2626"></i> Reject waiver <span class="ms-2 text-muted" style="font-weight:800;font-size: var(--font-size-sm);">{{ $ref }}</span></div>
                        <div class="zmc-modal-sub">Rejection reason is mandatory.</div>
                      </div>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <label class="form-label">Reason</label>
                      <textarea class="form-control" name="waiver_review_notes" rows="4" maxlength="5000" required placeholder="e.g., not eligible, missing authority letter, insufficient documentation"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-danger"><i class="ri-close-line me-1"></i> Reject</button>
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
              </div>
            @endpush
          @empty
            <tr><td colspan="6" class="text-center text-muted p-4">No waiver requests.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">{{ $applications->links() }}</div>
  </div>
</div>
@stack('zmc_modals')
@endsection
