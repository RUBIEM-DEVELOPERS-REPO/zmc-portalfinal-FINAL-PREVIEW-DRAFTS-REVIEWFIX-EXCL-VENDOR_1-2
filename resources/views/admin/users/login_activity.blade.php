@extends('layouts.portal')
 
 @section('title', 'Login Activity')
 
 @section('content')
 <div class="container-fluid py-3">
   <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
     <div>
       <h4 class="fw-bold mb-0">Login Activity</h4>
       <div class="text-muted" style="font-size:13px;">Detailed login history and session tracking.</div>
     </div>
     <form class="d-flex gap-2" method="GET" action="{{ route('admin.users.login_activity') }}">
       <input class="form-control form-control-sm" name="q" value="{{ $q }}" placeholder="Search (email, IP, machine...)" style="width:260px;"/>
       <button class="btn btn-sm btn-primary">Search</button>
     </form>
   </div>
 
   <div class="row g-3">
     <div class="col-lg-12">
       <div class="card border-0 shadow-sm">
         <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Successful Logins</span>
            <span class="badge bg-success-subtle text-success">{{ $lastLogins->total() }} total records</span>
         </div>
         <div class="card-body p-0">
           <div class="table-responsive">
             <table class="table table-sm mb-0 align-middle">
               <thead class="table-light">
                 <tr>
                   <th>Date</th>
                   <th>User</th>
                   <th>IP Address</th>
                   <th>Machine / Device</th>
                   <th>OS</th>
                   <th>Browser</th>
                 </tr>
               </thead>
               <tbody>
                 @forelse($lastLogins as $l)
                   <tr>
                     <td class="text-nowrap">{{ $l->login_at?->format('Y-m-d H:i') }}</td>
                     <td>
                        <div class="fw-semibold">{{ $l->user?->name ?? 'Unknown' }}</div>
                        <div class="small text-muted">{{ $l->account_name }}</div>
                     </td>
                     <td class="text-nowrap">{{ $l->ip_address }}</td>
                     <td class="text-nowrap">{{ $l->device_identifier }}</td>
                     <td class="text-nowrap">
                        <i class="{{ str_contains(strtolower($l->operating_system), 'win') ? 'ri-windows-fill' : (str_contains(strtolower($l->operating_system), 'mac') ? 'ri-apple-fill' : 'ri-terminal-box-line') }} me-1"></i>
                        {{ $l->operating_system }}
                     </td>
                     <td class="text-nowrap">
                        <i class="ri-chrome-line me-1"></i>
                        {{ $l->browser_name }} <span class="text-muted" style="font-size:11px;">v{{ $l->browser_version }}</span>
                     </td>
                   </tr>
                 @empty
                   <tr><td colspan="6" class="text-center text-muted py-3">No login activity found.</td></tr>
                 @endforelse
               </tbody>
             </table>
           </div>
         </div>
         <div class="card-footer bg-white">{{ $lastLogins->links() }}</div>
       </div>
     </div>
 
     <div class="col-lg-8">
       <div class="card border-0 shadow-sm">
         <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Failed Login Attempts</span>
            <span class="badge bg-danger-subtle text-danger">{{ $failedLogins->total() }} attempts</span>
         </div>
         <div class="card-body p-0">
           <div class="table-responsive">
             <table class="table table-sm mb-0 align-middle">
               <thead class="table-light">
                 <tr>
                   <th>Date</th>
                   <th>Account</th>
                   <th>IP</th>
                   <th>Reason</th>
                 </tr>
               </thead>
               <tbody>
                 @forelse($failedLogins as $l)
                   <tr>
                     <td class="text-nowrap">{{ $l->login_at?->format('Y-m-d H:i') }}</td>
                     <td>{{ $l->account_name }}</td>
                     <td class="text-nowrap">{{ $l->ip_address }}</td>
                     <td><span class="badge bg-danger-subtle text-danger">{{ $l->failure_reason }}</span></td>
                   </tr>
                 @empty
                   <tr><td colspan="4" class="text-center text-muted py-3">No failed logins found.</td></tr>
                 @endforelse
               </tbody>
             </table>
           </div>
         </div>
         <div class="card-footer bg-white">{{ $failedLogins->links() }}</div>
       </div>
     </div>
 
     <div class="col-lg-4">
       <div class="card border-0 shadow-sm">
         <div class="card-header bg-white fw-semibold">Last Active (Last 30 Days)</div>
         <div class="card-body p-0">
           <div class="table-responsive">
             <table class="table table-sm mb-0">
               <thead class="table-light"><tr><th>User</th><th>Last Login</th></tr></thead>
               <tbody>
                 @forelse($lastActive as $row)
                   <tr>
                     <td>
                        <div class="fw-semibold">{{ $row['user']?->name ?? 'User #'.$row['user']?->id }}</div>
                        <div class="text-muted small">{{ $row['user']?->email }}</div>
                     </td>
                     <td class="text-nowrap">{{ \Carbon\Carbon::parse($row['last_seen'])->format('Y-m-d H:i') }}</td>
                   </tr>
                 @empty
                   <tr><td colspan="2" class="text-center text-muted py-3">No recent activity.</td></tr>
                 @endforelse
               </tbody>
             </table>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
 @endsection
