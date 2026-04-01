@php
  $r = request()->route() ? request()->route()->getName() : '';
  $is = fn($name) => $r === $name || str_starts_with($r, $name.'.');
@endphp

<aside class="zmc-admin-sidebar" id="zmcSidebar">
  <div class="brand">
    <p class="title">Zimbabwe Media Commission</p>
    <p class="subtitle">Super Admin Console</p>
  </div>

  <div class="nav-section">System Overview</div>
  <a class="nav-link {{ $is('admin.dashboard') ? 'active' : '' }}" href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}">
    <span class="nav-icon"><i class="ri-dashboard-line"></i></span>
    <span>Dashboard</span>
  </a>
  @hasanyrole('super_admin|it_admin|director')
  <a class="nav-link {{ $is('admin.analytics') ? 'active' : '' }}" href="{{ Route::has('admin.analytics') ? route('admin.analytics') : '#' }}">
    <span class="nav-icon"><i class="ri-line-chart-line"></i></span>
    <span>Analytics</span>
  </a>
  @endhasanyrole

  <a class="nav-link {{ $is('admin.health') ? 'active' : '' }}" href="{{ Route::has('admin.health.index') ? route('admin.health.index') : (Route::has('admin.health') ? route('admin.health') : '#') }}">
    <span class="nav-icon"><i class="ri-heart-pulse-line"></i></span>
    <span>System Health</span>
  </a>

  <a class="nav-link {{ $is('admin.reports') ? 'active' : '' }}" href="{{ Route::has('admin.reports.index') ? route('admin.reports.index') : '#' }}">
    <span class="nav-icon"><i class="ri-file-chart-line"></i></span>
    <span>Reports & Analytics</span>
  </a>

  @hasanyrole('super_admin|it_admin|director')
  <a class="nav-link {{ $is('admin.downloads') ? 'active' : '' }}" href="{{ Route::has('admin.downloads.index') ? route('admin.downloads.index') : '#' }}">
    <span class="nav-icon"><i class="ri-download-2-line"></i></span>
    <span>Downloads</span>
  </a>
  @endhasanyrole

  <div class="nav-section">Applications</div>
  <a class="nav-link {{ $is('admin.mediahouse') ? 'active' : '' }}" href="{{ Route::has('admin.mediahouse.index') ? route('admin.mediahouse.index') : '#' }}">
    <span class="nav-icon"><i class="ri-building-4-line"></i></span>
    <span>Media House Registrations</span>
  </a>
  <a class="nav-link {{ $is('admin.accreditation') ? 'active' : '' }}" href="{{ Route::has('admin.accreditation.index') ? route('admin.accreditation.index') : '#' }}">
    <span class="nav-icon"><i class="ri-id-card-line"></i></span>
    <span>Media Practitioners Accreditation</span>
  </a>

  @hasanyrole('super_admin|it_admin')
  <div class="nav-section">Users & Roles</div>
  <a class="nav-link {{ $is('admin.roles') ? 'active' : '' }}" href="{{ Route::has('admin.roles.index') ? route('admin.roles.index') : '#' }}">
    <span class="nav-icon"><i class="ri-shield-keyhole-line"></i></span>
    <span>Roles</span>
  </a>
  <a class="nav-link {{ $is('admin.permissions') ? 'active' : '' }}" href="{{ Route::has('admin.permissions.index') ? route('admin.permissions.index') : '#' }}">
    <span class="nav-icon"><i class="ri-lock-line"></i></span>
    <span>Permissions</span>
  </a>

  <a class="nav-link {{ $is('admin.permissions.matrix') ? 'active' : '' }}" href="{{ Route::has('admin.permissions.matrix') ? route('admin.permissions.matrix') : '#' }}">
    <span class="nav-icon"><i class="ri-layout-grid-line"></i></span>
    <span>Permission Matrix</span>
  </a>
  @endhasanyrole

  {{-- Users split into separate lists as requested --}}
  @hasanyrole('super_admin|it_admin')
  <a class="nav-link {{ $is('admin.users.staff') ? 'active' : '' }}" href="{{ Route::has('admin.users.staff') ? route('admin.users.staff') : '#' }}">
    <span class="nav-icon"><i class="ri-shield-user-line"></i></span>
    <span>Staff Users</span>
  </a>
  <a class="nav-link {{ $is('admin.users.public') ? 'active' : '' }}" href="{{ Route::has('admin.users.public') ? route('admin.users.public') : '#' }}">
    <span class="nav-icon"><i class="ri-user-smile-line"></i></span>
    <span>Public Users</span>
  </a>
  @endhasanyrole

  <a class="nav-link {{ $is('admin.users.login_activity') ? 'active' : '' }}" href="{{ Route::has('admin.users.login_activity') ? route('admin.users.login_activity') : '#' }}">
    <span class="nav-icon"><i class="ri-shield-check-line"></i></span>
    <span>Login Activity</span>
  </a>

  @hasanyrole('super_admin|it_admin')
  <a class="nav-link {{ $is('admin.approvals') ? 'active' : '' }}" href="{{ Route::has('admin.approvals.index') ? route('admin.approvals.index') : '#' }}">
    <span class="nav-icon"><i class="ri-user-follow-line"></i></span>
    <span>User Approvals</span>
  </a>

  <a class="nav-link {{ $is('admin.audit') ? 'active' : '' }}" href="{{ Route::has('admin.audit.index') ? route('admin.audit.index') : '#' }}">
    <span class="nav-icon"><i class="ri-file-search-line"></i></span>
    <span>Audit & Logs</span>
  </a>
  @endhasanyrole

  <div class="nav-section">Workflow Configuration</div>

  <a class="nav-link {{ $is('admin.workflow.config') ? 'active' : '' }}" href="{{ Route::has('admin.workflow.config') ? route('admin.workflow.config') : '#' }}">
    <span class="nav-icon"><i class="ri-git-merge-line"></i></span>
    <span>Status & SLA Manager</span>
  </a>

  <a class="nav-link {{ $is('admin.workflow.index') ? 'active' : '' }}" href="{{ Route::has('admin.workflow.index') ? route('admin.workflow.index') : '#' }}">
    <span class="nav-icon"><i class="ri-route-line"></i></span>
    <span>Routing Rules</span>
  </a>

  <div class="nav-section">Fees & Payments</div>

  <a class="nav-link {{ $is('admin.fees.config') ? 'active' : '' }}" href="{{ Route::has('admin.fees.config') ? route('admin.fees.config') : '#' }}">
    <span class="nav-icon"><i class="ri-money-dollar-circle-line"></i></span>
    <span>Fee Catalogue & Channels</span>
  </a>

  <a class="nav-link {{ $is('admin.fees.index') ? 'active' : '' }}" href="{{ Route::has('admin.fees.index') ? route('admin.fees.index') : '#' }}">
    <span class="nav-icon"><i class="ri-file-list-3-line"></i></span>
    <span>Reconciliation Dashboard</span>
  </a>

  <div class="nav-section">Templates & Documents</div>

  <a class="nav-link {{ $is('admin.templates.config') ? 'active' : '' }}" href="{{ Route::has('admin.templates.config') ? route('admin.templates.config') : '#' }}">
    <span class="nav-icon"><i class="ri-file-upload-line"></i></span>
    <span>Template Version Control</span>
  </a>

  <a class="nav-link {{ $is('admin.templates.index') ? 'active' : '' }}" href="{{ Route::has('admin.templates.index') ? route('admin.templates.index') : '#' }}">
    <span class="nav-icon"><i class="ri-file-text-line"></i></span>
    <span>Template Catalogue</span>
  </a>

  <div class="nav-section">Content System Control</div>
  @hasanyrole('super_admin|it_admin')
  <a class="nav-link {{ $is('admin.content') ? 'active' : '' }}" href="{{ Route::has('admin.content.index') ? route('admin.content.index') : (Route::has('content.index') ? route('content.index') : '#') }}">
    <span class="nav-icon"><i class="ri-notification-3-line"></i></span>
    <span>Content</span>
  </a>
  @endhasanyrole

  <a class="nav-link {{ $is('admin.content.control') ? 'active' : '' }}" href="{{ Route::has('admin.content.control') ? route('admin.content.control') : '#' }}">
    <span class="nav-icon"><i class="ri-sliders-2-line"></i></span>
    <span>Module Access & Rules</span>
  </a>

  @hasanyrole('super_admin|it_admin')
  <div class="nav-section">Audit & Logs</div>

  <a class="nav-link {{ $is('admin.audit') ? 'active' : '' }}" href="{{ Route::has('admin.audit.index') ? route('admin.audit.index') : '#' }}">
    <span class="nav-icon"><i class="ri-file-search-line"></i></span>
    <span>System Audit Log</span>
  </a>
  @endhasanyrole

  <div class="nav-section">Regions & Offices</div>

  <a class="nav-link {{ $is('admin.regions.offices') ? 'active' : '' }}" href="{{ Route::has('admin.regions.offices') ? route('admin.regions.offices') : '#' }}">
    <span class="nav-icon"><i class="ri-map-pin-2-line"></i></span>
    <span>Offices & Assignments</span>
  </a>

  <a class="nav-link {{ $is('admin.regions.index') ? 'active' : '' }}" href="{{ Route::has('admin.regions.index') ? route('admin.regions.index') : '#' }}">
    <span class="nav-icon"><i class="ri-building-2-line"></i></span>
    <span>Regional Offices Catalogue</span>
  </a>

  <div class="nav-section">System Settings</div>

  <a class="nav-link {{ $is('admin.system.master_settings') ? 'active' : '' }}" href="{{ Route::has('admin.system.master_settings') ? route('admin.system.master_settings') : '#' }}">
    <span class="nav-icon"><i class="ri-settings-4-line"></i></span>
    <span>Master Settings</span>
  </a>

  @hasanyrole('super_admin|it_admin|director')
  <a class="nav-link {{ $is('admin.news') ? 'active' : '' }}" href="{{ Route::has('admin.news.index') ? route('admin.news.index') : '#' }}">
    <span class="nav-icon"><i class="ri-newspaper-line"></i></span>
    <span>News</span>
  </a>
  @endhasanyrole

  @hasanyrole('super_admin|it_admin|director')
  <a class="nav-link {{ $is('admin.complaints') ? 'active' : '' }}" href="{{ Route::has('admin.complaints.index') ? route('admin.complaints.index') : '#' }}">
    <span class="nav-icon"><i class="ri-chat-1-line"></i></span>
    <span>Complaints & Appeals</span>
  </a>
  @endhasanyrole
  <a class="nav-link {{ $is('admin.settings') ? 'active' : '' }}" href="{{ Route::has('admin.settings.index') ? route('admin.settings.index') : '#' }}">
    <span class="nav-icon"><i class="ri-settings-3-line"></i></span>
    <span>Settings</span>
  </a>

  <div class="p-3" style="border-top:1px solid rgba(255,255,255,.08);margin-top:10px;">
    <a class="nav-link" href="{{ Route::has('logout') ? route('logout') : '#' }}" onclick="event.preventDefault(); document.getElementById('logoutForm')?.submit();">
      <span class="nav-icon"><i class="ri-logout-box-r-line"></i></span>
      <span>Logout</span>
    </a>
    <form id="logoutForm" method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}" style="display:none;">@csrf</form>
  </div>
</aside>
