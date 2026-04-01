@extends('layouts.portal')
@section('title', 'Create User (IT Admin)')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-dark">
      <i class="ri-arrow-left-line me-1"></i> Back
    </a>
  </div>
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">Create User</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        Users created here require approval by Super Admin or Director.
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('staff.it.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3 d-none d-md-inline">
        <i class="ri-dashboard-3-line me-1"></i> Dashboard
      </a>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="zmc-card">
    <form method="POST" action="{{ route('staff.it.users.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label class="form-label zmc-lbl">Name</label>
          <input class="form-control zmc-input" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label zmc-lbl">Email</label>
          <input class="form-control zmc-input" name="email" type="email" value="{{ old('email') }}" required>
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label zmc-lbl">Temp Password</label>
          <input class="form-control zmc-input" name="password" type="text" value="{{ old('password') }}" required>
        </div>

        <div class="col-12">
          <label class="form-label zmc-lbl">Assign Role(s) (requested)</label>
          <div class="row g-2">
            @foreach($roles as $role)
              <div class="col-12 col-md-3">
                <label class="d-flex align-items-center gap-2 p-2 border rounded" style="background:#fff; cursor:pointer;">
                  <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', [])))>
                  <span class="small fw-bold">{{ strtoupper(str_replace('_',' ', $role->name)) }}</span>
                </label>
              </div>
            @endforeach
          </div>
        </div>

        <div class="col-12 mt-4">
          <label class="form-label zmc-lbl">Assign Regional Jurisdiction (for Accreditation Officers)</label>
          <div class="row g-2">
            @foreach($regions as $region)
              <div class="col-12 col-md-3">
                <label class="d-flex align-items-center gap-2 p-2 border rounded" style="background:#fff; cursor:pointer;">
                  <input type="checkbox" name="assigned_regions[]" value="{{ $region->id }}" @checked(in_array($region->id, old('assigned_regions', [])))>
                  <div>
                    <div class="small fw-bold">{{ $region->name }}</div>
                    @if($region->expires_at)
                      <div class="text-danger" style="font-size:10px;">Expires: {{ $region->expires_at->format('d M Y') }}</div>
                    @endif
                  </div>
                </label>
              </div>
            @endforeach
          </div>
          <div class="form-text mt-2">Accreditation Officers will only see applications for their assigned regions.</div>
        </div>
      </div>

      <div class="mt-3 d-flex justify-content-end">
        <button class="btn btn-dark fw-bold px-4" type="submit">
          <i class="ri-user-add-line me-1"></i> Create & Queue
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
