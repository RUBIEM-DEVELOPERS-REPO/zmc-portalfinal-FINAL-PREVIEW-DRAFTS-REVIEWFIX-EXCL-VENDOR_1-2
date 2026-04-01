@extends('layouts.portal')

@section('title', 'Staff Management - Media House Portal')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Staff Management</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Link media practitioners to your organisation.
      </div>
    </div>
    <button type="button" class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#linkStaffModal">
      <i class="ri-user-add-line me-1"></i> Link Media Practitioner
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4">
      {{ session('error') }}
    </div>
  @endif

  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <h6 class="fw-bold m-0"><i class="ri-group-line me-2" style="color:var(--zmc-accent)"></i>Linked Staff</h6>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-mini-table">
        <thead>
          <tr>
            <th><i class="ri-user-line me-1"></i> Name</th>
            <th><i class="ri-mail-line me-1"></i> Email</th>
            <th><i class="ri-briefcase-line me-1"></i> Role</th>
            <th><i class="ri-calendar-line me-1"></i> Date Linked</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($staff as $member)
            <tr>
              <td class="fw-bold text-dark">{{ $member->journalist->name }}</td>
              <td>{{ $member->journalist->email }}</td>
              <td>{{ $member->role ?? '—' }}</td>
              <td class="small text-muted">{{ $member->created_at->format('d M Y') }}</td>
              <td class="text-end">
                <form action="{{ route('mediahouse.staff.unlink', $member) }}" method="POST" onsubmit="return confirm('Are you sure you want to unlink this media practitioner?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger zmc-icon-btn" title="Unlink">
                    <i class="ri-link-unlink"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">No linked staff found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- Link Staff Modal --}}
<div class="modal fade zmc-modal-pop" id="linkStaffModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('mediahouse.staff.link') }}" method="POST">
        @csrf
        <div class="modal-header zmc-modal-header">
          <div>
            <div class="zmc-modal-title">
              <i class="ri-user-add-line me-2" style="color:var(--zmc-accent-dark)"></i>
              Link Media Practitioner
            </div>
            <div class="zmc-modal-sub">Add a media practitioner to your organisation by email.</div>
          </div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold small text-muted">Media Practitioner Email Address</label>
            <input type="email" name="email" class="form-control zmc-input" placeholder="e.g. practitioner@example.com" required>
            <div class="form-text mt-1" style="font-size:11px;">The media practitioner must already have an account on the ZMC Portal.</div>
          </div>

          <div class="mb-0">
            <label class="form-label fw-bold small text-muted">Role (Optional)</label>
            <input type="text" name="role" class="form-control zmc-input" placeholder="e.g. Senior Reporter, Sub-Editor">
          </div>
        </div>

        <div class="modal-footer zmc-modal-footer">
          <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark fw-bold px-4">Link Staff</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
