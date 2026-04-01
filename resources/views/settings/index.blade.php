@extends('layouts.portal')

@section('title', 'Settings')

@section('content')
<div class="zmc-dashboard-wrapper">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px;">Settings</h4>
      <div class="text-muted mt-1" style="font-size:13px;">Manage your account, security and preferences.</div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-dark"><i class="ri-arrow-left-line me-1"></i> Back</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-2">
          <div class="list-group list-group-flush" id="settingsTabs" role="tablist">
            <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#tab-profile" role="tab">
              <i class="ri-user-3-line me-2"></i> Profile
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-security" role="tab">
              <i class="ri-lock-password-line me-2"></i> Security
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-appearance" role="tab">
              <i class="ri-palette-line me-2"></i> Appearance
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-notifications" role="tab">
              <i class="ri-notification-3-line me-2"></i> Notifications
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tab-integrations" role="tab">
              <i class="ri-links-line me-2"></i> Integrations
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="tab-content">

        <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
          <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3">
              <div class="fw-bold">Profile Information</div>
              <div class="text-muted" style="font-size:12px;">Full personal details for your ZMC account.</div>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12 text-center mb-3">
                        <div class="position-relative d-inline-block">
                            @php
                                $pic = ($user->profile_data['profile_picture'] ?? null) 
                                    ? asset('storage/' . $user->profile_data['profile_picture']) 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random';
                            @endphp
                            <img src="{{ $pic }}" class="rounded-circle border" style="width:100px; height:100px; object-fit:cover;">
                            <label for="profile_pic_input" class="btn btn-sm btn-light rounded-circle border shadow-sm position-absolute bottom-0 end-0" style="padding:4px 6px;">
                                <i class="ri-camera-line"></i>
                            </label>
                            <input type="file" name="profile_picture" id="profile_pic_input" hidden accept="image/*">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name (as per ID)</label>
                        <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email Address</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" placeholder="+263...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">National ID / Passport Number</label>
                        <input name="profile[national_id]" class="form-control" value="{{ old('profile.national_id', $user->profile_data['national_id'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold d-block">Date of Birth</label>
                        @php 
                            $dob = old('profile.dob', $user->profile_data['dob'] ?? '');
                            $d_y = $dob ? date('Y', strtotime($dob)) : '';
                            $d_m = $dob ? date('m', strtotime($dob)) : '';
                            $d_d = $dob ? date('d', strtotime($dob)) : '';
                        @endphp
                        <div class="d-flex gap-2">
                          <select class="form-control px-1" id="settings_dob_date">
                            <option value="">DD</option>
                            @for($i=1; $i<=31; $i++) <option value="{{ sprintf('%02d', $i) }}" @selected($d_d == sprintf('%02d', $i))>{{ sprintf('%02d', $i) }}</option> @endfor
                          </select>
                          <select class="form-control px-1" id="settings_dob_month">
                            <option value="">MM</option>
                            @for($i=1; $i<=12; $i++) <option value="{{ sprintf('%02d', $i) }}" @selected($d_m == sprintf('%02d', $i))>{{ date('M', mktime(0, 0, 0, $i, 1)) }}</option> @endfor
                          </select>
                          <select class="form-control px-1" id="settings_dob_year">
                            <option value="">YYYY</option>
                            @for($i=date('Y'); $i>=1920; $i--) <option value="{{ $i }}" @selected($d_y == $i)>{{ $i }}</option> @endfor
                          </select>
                        </div>
                        <input type="hidden" name="profile[dob]" id="settings_real_dob" value="{{ $dob }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Gender</label>
                        <select name="profile[gender]" class="form-select">
                            <option value="">Select...</option>
                            <option value="male" @selected(($user->profile_data['gender'] ?? '') === 'male')>Male</option>
                            <option value="female" @selected(($user->profile_data['gender'] ?? '') === 'female')>Female</option>
                            <option value="other" @selected(($user->profile_data['gender'] ?? '') === 'other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nationality</label>
                        <input name="profile[nationality]" class="form-control" value="{{ old('profile.nationality', $user->profile_data['nationality'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Residential Address</label>
                        <textarea name="profile[residential_address]" class="form-control" rows="2">{{ old('profile.residential_address', $user->profile_data['residential_address'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Postal Address</label>
                        <textarea name="profile[postal_address]" class="form-control" rows="2">{{ old('profile.postal_address', $user->profile_data['postal_address'] ?? '') }}</textarea>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary mt-2"><i class="ri-save-3-line me-1"></i> Save Changes</button>
              </form>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tab-security" role="tabpanel">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 py-3">
              <div class="fw-bold">Password</div>
              <div class="text-muted" style="font-size:12px;">Change your login password.</div>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('settings.password') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label fw-bold">Current Password</label>
                  <input name="current_password" type="password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">New Password</label>
                  <input name="password" type="password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Confirm New Password</label>
                  <input name="password_confirmation" type="password" class="form-control" required>
                </div>
                <button class="btn btn-primary"><i class="ri-shield-keyhole-line me-1"></i> Update Password</button>
              </form>
            </div>
          </div>

          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 py-3">
              <div class="fw-bold">Two-Factor Authentication</div>
              <div class="text-muted" style="font-size:12px;">Secure your account with OTP via Email/SMS.</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.security') }}">
                    @csrf
                    <div class="form-check form-switch mb-3">
                        <input name="two_factor_enabled" type="hidden" value="0">
                        <input name="two_factor_enabled" class="form-check-input" type="checkbox" role="switch" id="twoFactorSwitch" value="1" @checked($user->profile_data['two_factor_enabled'] ?? false)>
                        <label class="form-check-label fw-bold" for="twoFactorSwitch">Enable Two-Factor Authentication</label>
                    </div>
                    <button class="btn btn-outline-primary btn-sm">Save Preference</button>
                </form>
            </div>
          </div>

          <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3">
              <div class="fw-bold">Login Activity History</div>
              <div class="text-muted" style="font-size:12px;">Your recent sign-ins.</div>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>IP Address</th>
                                <th>Device / Browser</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginActivity as $act)
                                <tr>
                                    <td>{{ $act->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $act->ip }}</td>
                                    <td><span class="text-muted">{{ substr($act->user_agent, 0, 40) }}...</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-3">No recent activity detected.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
               </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tab-appearance" role="tabpanel">
          <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3">
              <div class="fw-bold">Appearance</div>
              <div class="text-muted" style="font-size:12px;">Choose light or dark theme.</div>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('settings.theme') }}" class="d-flex gap-2 align-items-center flex-wrap">
                @csrf
                <select name="theme" class="form-select" style="max-width:220px;">
                  <option value="light" @selected(($user->theme ?? 'light') === 'light')>Light</option>
                  <option value="dark" @selected(($user->theme ?? 'light') === 'dark')>Dark</option>
                </select>
                <button class="btn btn-outline-primary" type="submit"><i class="ri-palette-line me-1"></i> Apply</button>
                <span class="text-muted small">Refresh to see changes everywhere.</span>
              </form>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tab-notifications" role="tabpanel">
          <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3">
              <div class="fw-bold">Notification Preferences</div>
              <div class="text-muted" style="font-size:12px;">Configure how you receive updates and alerts.</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.notifications') }}">
                    @csrf
                    <div class="list-group list-group-flush mb-3">
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Email Notifications</div>
                                <div class="text-muted small">Receive application status updates via email.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input name="notifications[email]" type="hidden" value="0">
                                <input name="notifications[email]" class="form-check-input" type="checkbox" value="1" @checked($user->profile_data['notifications']['email'] ?? true)>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">SMS Notifications</div>
                                <div class="text-muted small">Receive important alerts via text message.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input name="notifications[sms]" type="hidden" value="0">
                                <input name="notifications[sms]" class="form-check-input" type="checkbox" value="1" @checked($user->profile_data['notifications']['sms'] ?? false)>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Renewal Reminders</div>
                                <div class="text-muted small">Get notified 60 days before accreditation expiry.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input name="notifications[renewals]" type="hidden" value="0">
                                <input name="notifications[renewals]" class="form-check-input" type="checkbox" value="1" @checked($user->profile_data['notifications']['renewals'] ?? true)>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary"><i class="ri-notification-badge-line me-1"></i> Save Preferences</button>
                </form>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tab-integrations" role="tabpanel">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
              <div class="fw-bold">Integrations</div>
              <div class="text-muted" style="font-size:12px;">(Placeholder) Connect external services.</div>
            </div>
            <div class="card-body">
              <div class="alert alert-info mb-0">Integration settings will be added here (e.g., email provider, SMS gateway, analytics).</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateSettingsDob = () => {
        const d = document.getElementById('settings_dob_date').value;
        const m = document.getElementById('settings_dob_month').value;
        const y = document.getElementById('settings_dob_year').value;
        document.getElementById('settings_real_dob').value = (d && m && y) ? `${y}-${m}-${d}` : '';
    };
    ['settings_dob_date', 'settings_dob_month', 'settings_dob_year'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.addEventListener('change', updateSettingsDob);
    });
});
</script>
@endpush

@endsection
