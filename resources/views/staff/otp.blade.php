@extends('layouts.portal')
@section('title', 'Staff OTP Verification')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh; background: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.05) 0%, rgba(139, 92, 246, 0.05) 90%);">
    <div class="zmc-card bg-white shadow-lg border-0 rounded-4 overflow-hidden" style="width: 100%; max-width: 450px; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">
        <div class="p-5">
            <div class="text-center mb-5">
                <div class="d-flex justify-content-center mb-4">
                    <div class="logo-circle shadow-sm">
                        <img src="{{ asset('zmc_logo_circular.png') }}" alt="ZMC Logo" style="height: 100%; width: 100%; object-fit: contain;">
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-2">Verify Your Identity</h3>
                <p class="text-muted small">We've sent a 6-digit verification code to your email address. Please enter it below to securely access your dashboard.</p>
            </div>

            @if(session('info'))
                <div class="alert alert-info border-0 rounded-3 small mb-4">
                    <i class="ri-information-line me-2"></i> {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('staff.otp.verify') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="otp" class="form-label small fw-bold text-uppercase text-muted letter-spacing-1">One-Time Password (OTP)</label>
                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="ri-lock-password-line"></i></span>
                        <input type="text" name="otp" id="otp" class="form-control border-start-0 ps-0 fw-bold @error('otp') is-invalid @enderror" placeholder="000000" maxlength="6" autofocus required style="letter-spacing: 0.5em; text-align: center;">
                    </div>
                    @error('otp')
                        <div class="invalid-feedback d-block mt-2 small fw-bold">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold py-3 shadow-sm transition-all hover-translate-y">
                    Verify & Continue <i class="ri-arrow-right-line ms-2"></i>
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-muted small">Didn't receive the code? <a href="{{ route('staff.login') }}" class="text-primary fw-bold text-decoration-none">Try again</a> or contact IT support.</p>
            </div>
        </div>
        <div class="bg-light p-3 text-center border-top">
            <span class="smaller text-muted fw-bold text-uppercase tracking-wider">
                <i class="ri-lock-2-line me-1"></i> Secure Staff Session
            </span>
        </div>
    </div>
</div>

<style>
.logo-circle {
    width: 110px;
    height: 110px;
    flex-shrink: 0;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0 auto;
    overflow: hidden;
}
.logo-circle img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    margin: 0;
    mix-blend-mode: multiply;
}
.transition-all { transition: all 0.3s ease; }
.hover-translate-y:hover { transform: translateY(-3px); }
.letter-spacing-1 { letter-spacing: 0.1em; }
.fw-black { font-weight: 900; }
.smaller { font-size: 0.7rem; }

.zmc-card {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

input:focus {
    box-shadow: none !important;
    border-color: #3b82f6 !important;
}

.input-group-text {
    border-color: #e2e8f0;
}

.form-control {
    border-color: #e2e8f0;
}
</style>
@endsection
