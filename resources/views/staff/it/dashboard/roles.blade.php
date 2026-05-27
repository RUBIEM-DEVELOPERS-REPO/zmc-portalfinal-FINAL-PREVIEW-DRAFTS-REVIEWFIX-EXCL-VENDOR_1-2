@extends('layouts.portal')

@section('title', 'Role Management - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">Role & Permission Management</h2>
            <p class="text-slate-600 m-0 small mt-1">Define Roles and Assign RBAC Permissions</p>
        </div>
        <div>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-slate-100 rounded-pill px-3 fw-bold small text-slate-600">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0">System Roles</h6>
                    <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">Create New Role</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                                <tr>
                                    <th class="ps-4 small text-slate-700 uppercase fw-bold py-3">Role Name</th>
                                    <th class="small text-slate-700 uppercase fw-bold py-3">Description</th>
                                    <th class="small text-slate-700 uppercase fw-bold py-3">Permissions</th>
                                    <th class="small text-slate-700 uppercase fw-bold py-3 text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td class="ps-4 fw-bold text-slate-900 small">{{ strtoupper(str_replace('_',' ', $role->name)) }}</td>
                                    <td class="small text-slate-600">{{ $role->description ?? 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-purple-subtle text-purple border rounded-pill px-2 py-1 small fw-bold">{{ $role->permissions_count ?? 0 }} Assigned</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-icon border-0 bg-transparent text-slate-400">
                                            <i class="ri-settings-3-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-4">Quick Help</h6>
                <p class="small text-slate-600 mb-0">Roles define a collection of permissions. Assign roles to users to control their access to specific modules within the ZMC Portal.</p>
            </div>
        </div>
    </div>

</div>
@endsection
