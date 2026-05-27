@extends('layouts.portal')

@section('title', 'Role Assignment Audit Trail')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        <i class="ri-shield-user-line me-2"></i>Role Assignment Audit Trail
      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Audit trail of all IT role assignments and user access changes with timestamps
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-dark">
        <i class="ri-arrow-left-line me-1"></i> Back
      </a>
      <a href="{{ route('admin.users.staff') }}" class="btn btn-sm btn-outline-primary">
        <i class="ri-user-settings-line me-1"></i> User Management
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  {{-- Filter Card --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="d-flex flex-wrap gap-3 align-items-end">
        <div style="min-width:200px;">
          <label class="form-label small fw-bold text-muted">Action Type</label>
          <select name="action" class="form-select form-select-sm">
            <option value="">All Actions</option>
            <option value="assign_roles" {{ request('action') == 'assign_roles' ? 'selected' : '' }}>Role Assignment</option>
            <option value="remove_roles" {{ request('action') == 'remove_roles' ? 'selected' : '' }}>Role Removal</option>
            <option value="it_admin.change_user_role" {{ request('action') == 'it_admin.change_user_role' ? 'selected' : '' }}>IT Role Change</option>
            <option value="user_access_updated" {{ request('action') == 'user_access_updated' ? 'selected' : '' }}>Access Update</option>
          </select>
        </div>
        <div style="min-width:200px;">
          <label class="form-label small fw-bold text-muted">IT Admin</label>
          <select name="admin" class="form-select form-select-sm">
            <option value="">All Admins</option>
            @foreach($itAdmins ?? [] as $admin)
              <option value="{{ $admin->id }}" {{ request('admin') == $admin->id ? 'selected' : '' }}>
                {{ $admin->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div style="min-width:200px;">
          <label class="form-label small fw-bold text-muted">Date Range</label>
          <select name="range" class="form-select form-select-sm">
            <option value="">All Time</option>
            <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="week" {{ request('range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
            <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>Last 30 Days</option>
          </select>
        </div>
        <div>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="ri-filter-search-line me-1"></i> Filter
          </button>
          <a href="{{ route('admin.audit.role-assignments') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-refresh-line me-1"></i> Reset
          </a>
        </div>
        <div class="ms-auto">
          <a href="{{ route('admin.audit.role-assignments.export') }}" class="btn btn-success btn-sm">
            <i class="ri-file-excel-line me-1"></i> Export CSV
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Statistics Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
        <div class="card-body">
          <div class="text-muted small">Total Role Assignments</div>
          <div class="h4 fw-bold mb-0">{{ $totalAssignments ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
        <div class="card-body">
          <div class="text-muted small">This Week</div>
          <div class="h4 fw-bold mb-0">{{ $thisWeekCount ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-info bg-opacity-10">
        <div class="card-body">
          <div class="text-muted small">Active IT Admins</div>
          <div class="h4 fw-bold mb-0">{{ $activeItAdmins ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm bg-success bg-opacity-10">
        <div class="card-body">
          <div class="text-muted small">With Temp Credentials</div>
          <div class="h4 fw-bold mb-0">{{ $withTempCredentials ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Audit Trail Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
      <div class="fw-bold">
        <i class="ri-history-line me-2"></i>Assignment History
      </div>
      <span class="badge bg-dark">{{ $logs->total() }} records</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4 small fw-bold text-muted">Timestamp</th>
              <th class="small fw-bold text-muted">IT Admin</th>
              <th class="small fw-bold text-muted">Action</th>
              <th class="small fw-bold text-muted">Target User</th>
              <th class="small fw-bold text-muted">Role Changes</th>
              <th class="small fw-bold text-muted">Temp Credentials</th>
              <th class="small fw-bold text-muted text-end pe-4">IP Address</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              @php
                $meta = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                $oldMeta = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                $hasTempCredentials = !empty($meta['temp_credentials']) || !empty($meta['temporary_password']);
              @endphp
              <tr>
                <td class="ps-4">
                  <div class="small fw-bold">{{ $log->created_at->format('d M Y') }}</div>
                  <div class="text-muted" style="font-size:11px;">{{ $log->created_at->format('H:i:s') }}</div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px; font-size:12px;">
                      {{ substr($log->user_name ?? 'S', 0, 1) }}
                    </div>
                    <div>
                      <div class="small fw-bold">{{ $log->user_name ?: 'System' }}</div>
                      <div class="text-muted" style="font-size:11px;">{{ $log->user_role ?? 'N/A' }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  @php
                    $badgeClass = match(true) {
                      str_contains($log->action, 'assign') => 'bg-success',
                      str_contains($log->action, 'remove') => 'bg-danger',
                      str_contains($log->action, 'change') => 'bg-warning',
                      default => 'bg-info'
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }} bg-opacity-10 text-dark border-0">
                    {{ str_replace(['it_admin.', '_'], ['', ' '], $log->action) }}
                  </span>
                </td>
                <td>
                  @if($log->entity_type === 'User' && $log->entity_id)
                    <div class="small fw-bold">{{ $meta['user_name'] ?? $log->entity_reference ?? 'User #' . $log->entity_id }}</div>
                    <div class="text-muted" style="font-size:11px;">{{ $meta['user_email'] ?? '' }}</div>
                  @else
                    <span class="text-muted small">{{ $log->description ?? 'N/A' }}</span>
                  @endif
                </td>
                <td>
                  @if(!empty($meta['new_role']))
                    <div class="small">
                      <span class="text-success"><i class="ri-add-circle-line me-1"></i>{{ $meta['new_role'] }}</span>
                    </div>
                  @endif
                  @if(!empty($meta['previous_role']))
                    <div class="small">
                      <span class="text-danger"><i class="ri-indeterminate-circle-line me-1"></i>{{ $meta['previous_role'] }}</span>
                    </div>
                  @endif
                  @if(!empty($meta['roles']))
                    <div class="small">
                      @foreach($meta['roles'] as $role)
                        <span class="badge bg-light text-dark border me-1">{{ $role }}</span>
                      @endforeach
                    </div>
                  @endif
                </td>
                <td>
                  @if($hasTempCredentials)
                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-warning bg-opacity-10 text-warning border-0">
                        <i class="ri-key-2-line me-1"></i>Generated
                      </span>
                      @if(!empty($meta['temp_password_expires']))
                        <span class="text-muted" style="font-size:11px;">
                          Expires: {{ \Carbon\Carbon::parse($meta['temp_password_expires'])->format('d M H:i') }}
                        </span>
                      @endif
                    </div>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
                <td class="text-end pe-4">
                  <code class="small text-muted">{{ $log->ip_address }}</code>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="ri-inbox-line fs-2 d-block mb-2 opacity-50"></i>
                  No role assignment records found
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-4 border-top">
        {{ $logs->withQueryString()->links() }}
      </div>
    </div>
  </div>

  {{-- Legend --}}
  <div class="mt-4 p-3 bg-light rounded">
    <div class="small fw-bold mb-2">Audit Trail Legend:</div>
    <div class="d-flex flex-wrap gap-3 small">
      <span class="badge bg-success bg-opacity-10 text-dark">Role Assignment</span>
      <span class="badge bg-danger bg-opacity-10 text-dark">Role Removal</span>
      <span class="badge bg-warning bg-opacity-10 text-dark">Role Change</span>
      <span class="badge bg-info bg-opacity-10 text-dark">Other Action</span>
    </div>
  </div>
</div>
@endsection
