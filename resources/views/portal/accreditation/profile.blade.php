@extends('layouts.portal')

@section('title', 'Profile')
@section('page_title', 'PROFILE')

@section('content')
<div id="profile-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Media Practitioner Profile</h4>
    <button class="btn btn-primary" id="editProfileBtn">
      <i class="ri-edit-line me-2"></i>Edit Profile
    </button>
  </div>

  <div class="form-container">
    <div class="form-header" style="background: transparent !important; border-bottom: 1px solid #e2e8f0;">
      <h5 class="m-0" style="color: #334155;"><i class="ri-user-settings-line me-2"></i>Personal Information</h5>
    </div>

    <div class="form-steps-container">
      <div class="text-center mb-4">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&size=120&background=facc15&color=000&bold=true"
             class="rounded-circle" alt="Profile" width="120" height="120">
        <h5 class="mt-3 mb-1">{{ Auth::user()->name ?? 'User' }}</h5>
        <p class="text-muted">Accredited Media Practitioner</p>
        <div class="d-flex justify-content-center gap-2 mb-3">
          <span class="badge bg-light text-dark">ID: ZMC-2023-045</span>
          <span class="badge bg-success">Active</span>
        </div>
      </div>

      <div class="form-row">
         <div class="form-field">
           <label class="form-label">National ID</label>
           <input type="text" class="form-control" value="{{ Auth::user()->profile_data['national_id'] ?? 'Not Provided' }}" readonly>
         </div>
         <div class="form-field">
           <label class="form-label">Passport Number</label>
           <input type="text" class="form-control" value="{{ Auth::user()->profile_data['passport_number'] ?? 'Not Provided' }}" readonly>
         </div>
       </div>
 
       <div class="form-row">
         <div class="form-field">
           <label class="form-label">Primary Phone</label>
           <input type="text" class="form-control" value="{{ Auth::user()->phone_country_code }} {{ Auth::user()->phone_number }}" readonly>
         </div>
         <div class="form-field">
           <label class="form-label">Secondary Phone</label>
           <input type="text" class="form-control" value="{{ Auth::user()->profile_data['secondary_phone'] ?? 'Not Provided' }}" readonly>
         </div>
       </div>
 
       <div class="form-row">
          <div class="form-field">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ Auth::user()->email ?? '' }}" readonly>
          </div>
          <div class="form-field">
            <label class="form-label">Nationality</label>
            <input type="text" class="form-control" value="{{ Auth::user()->profile_data['nationality'] ?? 'Not Provided' }}" readonly>
          </div>
       </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('editProfileBtn')?.addEventListener('click', function(){
    const inputs = document.querySelectorAll('#profile-page input');
    const isReadOnly = inputs[0].readOnly;
    inputs.forEach(i => i.readOnly = !isReadOnly);

    if(isReadOnly){
      this.classList.remove('btn-primary');
      this.classList.add('btn-success');
      this.innerHTML = '<i class="ri-save-line me-2"></i>Save Changes';
    }else{
      this.classList.remove('btn-success');
      this.classList.add('btn-primary');
      this.innerHTML = '<i class="ri-edit-line me-2"></i>Edit Profile';
      alert('Demo: Profile saved');
    }
  });
</script>
@endpush
