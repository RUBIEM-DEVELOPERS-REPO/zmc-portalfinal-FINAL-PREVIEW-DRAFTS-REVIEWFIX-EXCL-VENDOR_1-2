@extends('layouts.portal')

@section('title', 'IT Administration Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1 fw-bold">IT Administration</h2>
                    <p class="text-muted mb-0">User management, roles, permissions, and system notices</p>
                </div>
                <div class="badge bg-primary fs-6 py-2 px-3">
                    <i class="ri-settings-3-line me-1"></i> Admin Panel
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="zmc-card shadow-sm border-0 p-3 h-100">
                <div class="text-muted small">Total Users</div>
                <div class="fw-bold" style="font-size:26px;">{{ number_format($stats['total_users'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="zmc-card shadow-sm border-0 p-3 h-100">
                <div class="text-muted small">Staff Members</div>
                <div class="fw-bold" style="font-size:26px;">{{ number_format($stats['staff_users'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="zmc-card shadow-sm border-0 p-3 h-100">
                <div class="text-muted small">New Today</div>
                <div class="fw-bold" style="font-size:26px;">{{ number_format($stats['new_users_today'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="zmc-card shadow-sm border-0 p-3 h-100">
                <div class="text-muted small">Audit Logs</div>
                <div class="fw-bold" style="font-size:26px;">{{ number_format($stats['audit_entries'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="zmc-card shadow-sm border-0 p-3 h-100 d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%); border-left: 4px solid #0f172a !important;">
                <div>
                    <div class="text-muted small fw-bold">SECURITY OVERSIGHT</div>
                    <div class="small text-muted">Monitor system alerts, unauthorized access attempts, and critical flags.</div>
                </div>
                <a href="{{ route('staff.auditor.security') }}" class="btn btn-dark btn-sm px-4">
                    <i class="ri-shield-key-line me-1"></i> Open Console
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="ri-user-settings-line me-2"></i>User Management</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="ri-user-add-line me-1"></i> Create User
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 ps-4">User</th>
                                    <th class="border-0">Email</th>
                                    <th class="border-0">Role(s)</th>
                                    <th class="border-0">Region</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users ?? [] as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary bg-opacity-10 p-2 me-2" style="width:36px;height:36px;">
                                                <i class="ri-user-line text-secondary"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $user->region ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Permissions">
                                            <i class="ri-key-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="ri-user-line fs-1 d-block mb-2"></i>
                                        No staff users found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="ri-megaphone-line me-2"></i>Notices & Events</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                        <i class="ri-add-line me-1"></i> Add Notice/Event
                    </button>
                    <div class="list-group list-group-flush">
                        @forelse($notices ?? [] as $notice)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $notice->title }}</strong>
                                <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted">{{ Str::limit($notice->content, 50) }}</small>
                        </div>
                        @empty
                        <div class="text-center py-3 text-muted">
                            <i class="ri-file-text-line fs-2 d-block mb-2"></i>
                            No notices yet
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="ri-history-line me-2"></i>Recent Audit Trail</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentAudit ?? [] as $log)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-{{ $log->action === 'approve' ? 'success' : ($log->action === 'reject' ? 'danger' : 'info') }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            <small class="d-block mt-1">{{ Str::limit($log->description, 60) }}</small>
                            <small class="text-muted">by {{ $log->user_name }}</small>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="ri-history-line fs-2 d-block mb-2"></i>
                            No audit logs yet
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="ri-key-2-line me-2"></i>Available Roles</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                        $roleList = [
                            ['name' => 'Accreditation Officer', 'desc' => 'Reviews applications, communicates with applicants', 'color' => 'primary'],
                            ['name' => 'Accounts / Payments', 'desc' => 'Verifies payments and waivers', 'color' => 'success'],
                            ['name' => 'Registrar', 'desc' => 'Final approval before production', 'color' => 'info'],
                            ['name' => 'Production', 'desc' => 'Generates cards and certificates', 'color' => 'warning'],
                            ['name' => 'IT Admin', 'desc' => 'User and system management', 'color' => 'secondary'],
                            ['name' => 'Oversight / Audit', 'desc' => 'Read-only audit access', 'color' => 'dark'],
                            ['name' => 'Director', 'desc' => 'Executive oversight', 'color' => 'danger'],
                            ['name' => 'Super Admin', 'desc' => 'Full system access', 'color' => 'primary'],
                        ];
                        @endphp
                        @foreach($roleList as $role)
                        <div class="col-md-3">
                            <div class="border rounded p-3 h-100">
                                <span class="badge bg-{{ $role['color'] }} mb-2">{{ $role['name'] }}</span>
                                <p class="mb-0 small text-muted">{{ $role['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-user-add-line me-2"></i>Create Staff User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createUserForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select role...</option>
                            <option value="accreditation_officer">Accreditation Officer</option>
                            <option value="accounts_payments">Accounts / Payments</option>
                            <option value="registrar">Registrar</option>
                            <option value="production">Production</option>
                            <option value="it_admin">IT Admin</option>
                            <option value="oversight">Oversight / Audit</option>
                            <option value="director">Director</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Region (for regional roles)</label>
                        <select class="form-select" name="region">
                            <option value="">All Regions</option>
                            <option value="harare">Harare</option>
                            <option value="bulawayo">Bulawayo</option>
                            <option value="mutare">Mutare</option>
                            <option value="masvingo">Masvingo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Temporary Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="ri-user-add-line me-1"></i> Create User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createNoticeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-megaphone-line me-2"></i>Add Notice/Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createNoticeForm">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="notice">Notice</option>
                            <option value="event">Event</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i> Publish
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
