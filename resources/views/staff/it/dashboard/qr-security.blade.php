@extends('layouts.portal')

@section('title', 'QR & Anti-Fraud - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">QR Code & Security Features</h2>
            <p class="text-slate-600 m-0 small mt-1">Manage Anti-Fraud & Physical Security Elements</p>
        </div>
        <div>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-slate-100 rounded-pill px-3 fw-bold small text-slate-600">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="ri-qr-code-line fs-1 text-primary mb-3"></i>
                <h4 class="fw-bold mb-3">Secure QR Generation</h4>
                <p class="text-slate-600 mb-4 px-md-5">System-generated QR codes contain encrypted verification strings that allow third parties to validate credentials without database access.</p>
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-dark rounded-pill px-4">Rotate Security Keys</button>
                    <button class="btn btn-outline-dark rounded-pill px-4">Test QR Validator</button>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-4">Verification Stats</h6>
                <div class="small text-slate-600">
                    <div class="d-flex justify-content-between mb-3">
                        <span>External Scans (24h):</span>
                        <span class="fw-bold">142</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Failed Validations:</span>
                        <span class="fw-bold text-danger">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Encryption:</span>
                        <span class="fw-bold text-success">AES-256-GCM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
