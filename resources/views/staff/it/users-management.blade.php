@extends('layouts.portal')

@section('title', 'User Account Management')

@section('content')
<div class="container-fluid px-4">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-line" style="font-size:18px;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-3">
            <i class="ri-error-warning-line" style="font-size:18px;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">User Account Management</h4>
            <div class="text-muted mt-1" style="font-size:13px;">Manage all user accounts, roles, and access</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.it.users.create') }}" class="btn btn-sm btn-dark">
                <i class="ri-user-add-line me-1"></i> Create Staff User
            </a>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-sm btn-outline-dark">
                <i class="ri-arrow-left-line me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card zmc-stat-card zmc-bg-dark border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $totalUsers }}</h4>
                    <small class="opacity-75">Total Users</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card zmc-stat-card zmc-bg-yellow border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $staffCount }}</h4>
                    <small class="opacity-75">Staff</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card zmc-stat-card zmc-bg-sky border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $publicCount }}</h4>
                    <small class="opacity-75">Public</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card zmc-stat-card zmc-bg-blue border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $activeCount }}</h4>
                    <small class="opacity-75">Active</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card zmc-stat-card zmc-bg-red border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $suspendedCount }}</h4>
                    <small class="opacity-75">Suspended</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card zmc-stat-card zmc-bg-amber border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $pendingCount }}</h4>
                    <small class="opacity-75">Pending</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('staff.it.users-mgmt') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Name or email..." value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="staff" {{ request('type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="pending_setup" {{ request('status') == 'pending_setup' ? 'selected' : '' }}>Pending Setup</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $r->name)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark"><i class="ri-search-line me-1"></i> Filter</button>
                    <a href="{{ route('staff.it.users-mgmt') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0"><i class="ri-group-line me-2"></i>Users ({{ $users->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end" style="min-width:280px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="text-muted">{{ $u->id }}</td>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $u->account_type === 'staff' ? 'bg-dark' : 'bg-info' }} px-2">
                                    {{ strtoupper($u->account_type ?? 'public') }}
                                </span>
                            </td>
                            <td>
                                @foreach($u->roles as $role)
                                    <span class="badge rounded-pill bg-secondary px-2">{{ $role->name }}</span>
                                @endforeach
                                @if($u->roles->isEmpty())
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php($status = $u->account_status ?? 'active')
                                <span class="badge rounded-pill px-2
                                    {{ $status === 'active' ? 'bg-success' : '' }}
                                    {{ $status === 'suspended' ? 'bg-danger' : '' }}
                                    {{ in_array($status, ['pending', 'pending_setup']) ? 'bg-warning text-dark' : '' }}
                                ">{{ strtoupper($status) }}</span>
                            </td>
                            <td class="text-muted">{{ optional($u->created_at)->format('d M Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#roleModal{{ $u->id }}" title="Change Role">
                                        <i class="ri-user-settings-line"></i>
                                    </button>

                                    @if(($u->account_status ?? 'active') !== 'suspended')
                                        <form method="POST" action="{{ route('staff.it.user.suspend', $u) }}" class="d-inline" onsubmit="return confirm('Suspend {{ $u->name }}?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-warning" title="Suspend"><i class="ri-forbid-line"></i></button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('staff.it.user.activate', $u) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" title="Activate"><i class="ri-check-line"></i></button>
                                        </form>
                                    @endif

                                    @if(in_array($u->account_status ?? '', ['pending', 'pending_setup']))
                                        <form method="POST" action="{{ route('staff.it.user.resend_activation', $u) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-info" title="Resend Activation Email"><i class="ri-mail-send-line"></i></button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('staff.it.user.reset_password', $u) }}" class="d-inline" onsubmit="return confirm('Force password reset for {{ $u->name }}?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary" title="Force Password Reset"><i class="ri-lock-password-line"></i></button>
                                    </form>

                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('staff.it.user.delete', $u) }}" class="d-inline" onsubmit="return confirm('PERMANENTLY DELETE {{ $u->name }}? This cannot be undone!')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Delete User"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="roleModal{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('staff.it.user.role', $u) }}">
                                        @csrf
                                        <div class="modal-header py-2">
                                            <h6 class="modal-title fw-bold">Change Role — {{ $u->name }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label small fw-semibold">Select Role</label>
                                            <select name="role" class="form-select form-select-sm">
                                                @foreach($roles as $r)
                                                    <option value="{{ $r->name }}" {{ $u->roles->pluck('name')->contains($r->name) ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $r->name)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="modal-footer py-2">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-dark">Save Role</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-body">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
