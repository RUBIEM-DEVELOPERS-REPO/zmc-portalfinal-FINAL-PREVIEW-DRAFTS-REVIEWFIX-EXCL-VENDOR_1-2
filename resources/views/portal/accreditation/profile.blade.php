@extends('layouts.portal')

@section('title', 'Profile')
@section('page_title', 'PROFILE')

@section('content')
<<<<<<< HEAD
<div id="profile-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Media Practitioner Profile</h4>
    <button class="btn btn-primary" id="editProfileBtn">
      <i class="ri-edit-line me-2"></i>Edit Profile
    </button>
  </div>
=======
@php $user = Auth::user(); @endphp
<div id="profile-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark m-0" style="font-size:18px;">Media Practitioner Profile</h4>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
    </div>
  @endif
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b

  <div class="form-container">
    <div class="form-header" style="background: transparent !important; border-bottom: 1px solid #e2e8f0;">
      <h5 class="m-0" style="color: #334155;"><i class="ri-user-settings-line me-2"></i>Personal Information</h5>
    </div>

<<<<<<< HEAD
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
=======
    <form method="POST" action="{{ route('accreditation.profile.update') }}" id="profileForm">
      @csrf
      <div class="form-steps-container">
        <div class="text-center mb-4">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? 'User') }}&size=120&background=facc15&color=000&bold=true"
               class="rounded-circle" alt="Profile" width="120" height="120">
          <h5 class="mt-3 mb-1">{{ $user->name ?? 'User' }}</h5>
          <p class="text-muted">Accredited Media Practitioner</p>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label fw-bold">National ID Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="id_number" value="{{ $user->id_number ?? '' }}" placeholder="e.g. 63-123456A78" readonly>
            <small class="text-muted">Required for local (Zimbabwean) practitioners</small>
          </div>
          <div class="form-field">
            <label class="form-label fw-bold">Passport Number</label>
            <input type="text" class="form-control" name="passport_number" value="{{ $user->passport_number ?? '' }}" placeholder="e.g. FN123456" readonly>
            <small class="text-muted">Required for foreign practitioners</small>
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label fw-bold">Primary Phone <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="phone_number" value="{{ $user->phone_number ?? '' }}" placeholder="+263 77 123 4567" readonly>
          </div>
          <div class="form-field">
            <label class="form-label fw-bold">Secondary Phone <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="phone2" value="{{ $user->phone2 ?? '' }}" placeholder="+263 71 234 5678" readonly>
            <small class="text-muted">At least two phone numbers are required</small>
          </div>
        </div>

        <div class="form-row">
          <div class="form-field">
            <label class="form-label fw-bold">Email</label>
            <input type="email" class="form-control" value="{{ $user->email ?? '' }}" readonly disabled>
          </div>
          <div class="form-field">
            <label class="form-label fw-bold">Nationality</label>
            <input type="text" class="form-control" name="nationality" value="{{ $user->profile_data['nationality'] ?? 'Zimbabwean' }}" readonly>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3" id="profileActions">
          <button type="button" class="btn btn-primary" id="editProfileBtn">
            <i class="ri-edit-line me-2"></i>Edit Profile
          </button>
          <button type="submit" class="btn btn-success d-none" id="saveProfileBtn">
            <i class="ri-save-line me-2"></i>Save Changes
          </button>
          <button type="button" class="btn btn-outline-secondary d-none" id="cancelEditBtn">
            Cancel
          </button>
        </div>
      </div>
    </form>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
  </div>
</div>
@endsection

@push('scripts')
<script>
<<<<<<< HEAD
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
=======
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');
  const form = document.getElementById('profileForm');

  function toggleEdit(editing) {
    const inputs = form.querySelectorAll('input:not([disabled])');
    inputs.forEach(i => i.readOnly = !editing);
    editBtn.classList.toggle('d-none', editing);
    saveBtn.classList.toggle('d-none', !editing);
    cancelBtn.classList.toggle('d-none', !editing);
  }

  editBtn?.addEventListener('click', () => toggleEdit(true));
  cancelBtn?.addEventListener('click', () => {
    toggleEdit(false);
    form.reset();
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
  });
</script>
@endpush
