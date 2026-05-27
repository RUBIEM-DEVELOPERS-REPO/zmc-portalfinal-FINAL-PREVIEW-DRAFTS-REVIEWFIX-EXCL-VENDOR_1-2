@extends('layouts.portal')

@section('title', $title ?? 'Users')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">{{ $title }}</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Users are split into separate lists:
        <strong>Staff Users</strong> and <strong>Public Users</strong>.
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-dark"><i class="ri-arrow-left-line me-1"></i> Back</a>
      <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary"><i class="ri-user-add-line me-1"></i> Create user</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
      <div class="fw-bold me-2"><i class="ri-group-line me-1"></i> Lists</div>

      <a href="{{ route('admin.users.staff', request()->query()) }}"
         class="btn btn-sm {{ $type === 'staff' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="ri-shield-user-line me-1"></i> Staff Users
        <span class="badge bg-light text-dark ms-1">{{ $counts['staff'] ?? 0 }}</span>
      </a>

      <a href="{{ route('admin.users.public', request()->query()) }}"
         class="btn btn-sm {{ $type === 'public' ? 'btn-dark' : 'btn-outline-dark' }}">
        <i class="ri-user-smile-line me-1"></i> Public Users
        <span class="badge bg-light text-dark ms-1">{{ $counts['public'] ?? 0 }}</span>
      </a>

      <div class="ms-auto w-100 w-md-auto">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
          <input name="q" value="{{ $q }}" class="form-control form-control-sm" style="max-width:360px" placeholder="Search name or email...">
          <button class="btn btn-sm btn-outline-primary"><i class="ri-search-line me-1"></i> Search</button>
          @if($q)
            <a href="{{ $type === 'staff' ? route('admin.users.staff') : route('admin.users.public') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
          @endif
        </form>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
      <div class="fw-bold">
        @if($type === 'staff')
          <i class="ri-shield-user-line me-2"></i> Staff Users
        @else
          <i class="ri-user-smile-line me-2"></i> Public Users
        @endif
      </div>
      <span class="badge bg-dark">{{ $users->total() }}</span>
    </div>

    <div class="table-responsive">
      <table class="table zmc-table-lite mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            @if($type === 'staff')
              <th>Designation</th>
              <th>Roles</th>
            @else
              <th>Status</th>
            @endif
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>{{ $u->id }}</td>
              <td class="fw-bold">{{ $u->name }}</td>
              <td class="text-muted">{{ $u->email }}</td>

              @if($type === 'staff')
                <td class="small">{{ $u->designation ?? '—' }}</td>
                <td>
                  @php($r = $u->roles->pluck('name')->values())
                  @if($r->isEmpty())
                    <span class="text-muted">—</span>
                  @else
                    <div class="d-flex flex-wrap gap-1">
                      @foreach($r as $rn)
                        <span class="badge bg-light text-dark" style="border:1px solid rgba(15,23,42,.12)">{{ str_replace('_',' ', $rn) }}</span>
                      @endforeach
                    </div>
                  @endif
                </td>
              @else
                <td>
                  @php($status = $u->account_status ?? 'active')
                  <span class="badge rounded-pill bg-{{ $status === 'active' ? 'success' : 'secondary' }} px-3">
                    {{ strtoupper($status) }}
                  </span>
                </td>
              @endif

              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.access.edit', $u) }}">
                  <i class="ri-user-settings-line me-1"></i> Access
                </a>
                @if(auth()->user()->hasRole('super_admin') && $u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete {{ $u->name }}? This action cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger ms-1">
                    <i class="ri-delete-bin-line me-1"></i> Delete
                  </button>
                </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">No users found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-body pt-3">
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection
