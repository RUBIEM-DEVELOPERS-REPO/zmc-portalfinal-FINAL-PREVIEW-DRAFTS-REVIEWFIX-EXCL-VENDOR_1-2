@extends('layouts.portal')

@section('title', 'Change Password - Required')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155; min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);">
    <div class="container" style="max-width:480px;">
        <div class="card border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="card-header text-center py-4" style="background:linear-gradient(135deg, #1e293b 0%, #334155 100%); border:none;">
                <div class="mb-3">
                    <i class="ri-shield-keyhole-line" style="font-size:48px; color:#facc15;"></i>
                </div>
                <h4 class="fw-bold text-white m-0">Change Your Password</h4>
                <p class="text-white-50 mt-2 mb-0" style="font-size:14px;">Secure your account with a new password</p>
            </div>
            
            <div class="card-body p-4">
                @if(session('info'))
                    <div class="alert alert-info border-0" style="background:#dbeafe; color:#1e40af;">
                        <i class="ri-information-line me-2"></i>{{ session('info') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0">
                        <div class="fw-bold mb-2"><i class="ri-error-warning-line me-2"></i>Please correct the following:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-warning border-0 mb-4" style="background:#fef3c7; border-left:4px solid #f59e0b!important;">
                    <div class="d-flex gap-2">
                        <i class="ri-alert-line" style="font-size:20px; color:#92400e;"></i>
                        <div>
                            <div class="fw-bold" style="color:#92400e; font-size:14px;">Security Notice</div>
                            <div style="color:#92400e; font-size:13px;">
                                You logged in with a temporary password. For security reasons, you must change your password before accessing the system.
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('staff.password.change.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color:#475569; font-size:14px;">
                            <i class="ri-lock-password-line me-1"></i>New Password
                        </label>
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               style="border-radius:8px; border:2px solid #e2e8f0;" 
                               placeholder="Enter new password (min 8 characters)" required minlength="8">
                        <div class="form-text" style="font-size:12px; color:#64748b;">
                            Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color:#475569; font-size:14px;">
                            <i class="ri-lock-password-line me-1"></i>Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" 
                               style="border-radius:8px; border:2px solid #e2e8f0;" 
                               placeholder="Re-enter your new password" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius:8px; background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border:none;">
                        <i class="ri-save-3-line me-2"></i>Change Password & Continue
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <form method="POST" action="{{ route('staff.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted text-decoration-none" style="font-size:13px;">
                            <i class="ri-logout-box-line me-1"></i>Cancel and Logout
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card-footer text-center py-3" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                <div class="d-flex justify-content-center gap-3 text-muted" style="font-size:12px;">
                    <span><i class="ri-shield-check-line me-1"></i>Secure Connection</span>
                    <span><i class="ri-lock-line me-1"></i>Encrypted</span>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted" style="font-size:12px;">
            <p class="mb-0">© {{ date('Y') }} Zimbabwe Media Commission. All rights reserved.</p>
        </div>
    </div>
</div>
@endsection
