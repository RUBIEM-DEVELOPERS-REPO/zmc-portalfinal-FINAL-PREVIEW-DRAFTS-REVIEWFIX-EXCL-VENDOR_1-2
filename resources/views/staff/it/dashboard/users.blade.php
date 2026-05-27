@extends('layouts.portal')

@section('title', 'User Management - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">User Management</h2>
            <p class="text-slate-600 m-0 small mt-1">Internal System Users & Access Control</p>
        </div>
        <div>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-slate-100 rounded-pill px-3 fw-bold small text-slate-600">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @include('staff.it.dashboard.partials.users', ['users' => $users, 'roles' => $roles])

</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    .it-dashboard .stats-card:hover {
        transform: translateY(-4px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-color: #cbd5e1 !important;
    }

    .bg-purple-subtle { background: #f3e8ff; color: #a855f7; }
    .bg-orange-subtle { background: #fff7ed; color: #f97316; }
    .bg-success-subtle { background: #f0fdf4; color: #22c55e; }
    
    .uppercase { text-transform: uppercase; }
</style>
@endsection
