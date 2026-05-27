@extends('layouts.portal')

@section('title', 'Core System Settings - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">Core System Configuration</h2>
            <p class="text-slate-600 m-0 small mt-1">Manage Global Environment Settings & System Health</p>
        </div>
        <div>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-slate-100 rounded-pill px-3 fw-bold small text-slate-600">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @include('staff.it.dashboard.partials.system', ['env' => $systemEnv ?? []])

</div>
@endsection
