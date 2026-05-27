<!-- STAFF SIDEBAR -->
@php
  $role = session('active_staff_role');
  $user = auth()->user();
  $isAdminPanel = $user?->hasRole('super_admin') || $user?->hasRole('director');

  $portalTitle = match($role) {
    'accreditation_officer' => 'Accreditation Officer',
    'accounts_payments'      => 'Accounts & Payments',
    'registrar'             => 'Registrar',
    'it_admin'              => 'IT Administration',
    'super_admin'           => 'Super Admin',
    'auditor'               => 'System Auditor',
    'production'            => 'Production',
    'director'              => 'Director MDG Strategic Oversight',
    'pr_officer'            => 'Public Relations',
    'public_info_compliance'=> 'Public Info & Compliance',
    'research_training'     => 'Research & Training',
    'chief_accountant'      => 'Chief Accountant',
    default                 => 'Staff',
  };
@endphp

<div class="vertical-menu">
  <div class="navbar-brand-box">
    <a href="{{ route('staff.officer.dashboard') }}">
      <img src="{{ asset('zmc_logo.png') }}" alt="ZMC Logo">
    </a>
    <div class="logo-portal-name">
      {{ $portalTitle }}
    </div>
  </div>

  <ul class="sidebar-menu">
    @if(!$isAdminPanel && ($role === 'accreditation_officer' || $user?->hasRole('accreditation_officer')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Accreditation Officer
      </li>

      <li class="{{ request()->routeIs('staff.officer.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Dashboard</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Applications
      </li>
      <li class="{{ request()->routeIs('staff.officer.applications.*') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.applications.index') }}">
          <i class="ri-file-list-3-line"></i> <span>All Applications</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.applications.new') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.applications.new') }}"><i class="ri-sparkling-2-line"></i> <span>Recent Applications</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.applications.pending') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.applications.pending') }}"><i class="ri-time-line"></i> <span>Pending Accounts Review</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.fix-requests') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.fix-requests') }}">
          <i class="ri-tools-line"></i> <span>Fix Requests</span>
          @if(isset($kpis['pending_fix_requests']) && $kpis['pending_fix_requests'] > 0)
            <span class="badge bg-warning text-dark ms-auto">{{ $kpis['pending_fix_requests'] }}</span>
          @endif
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.applications.approved') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.applications.approved') }}"><i class="ri-checkbox-circle-line"></i> <span>Approved</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.applications.rejected') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.applications.rejected') }}"><i class="ri-arrow-go-back-line"></i> <span>Returned for Correction</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Intake
      </li>
      @if(Route::has('staff.officer.physical-intake'))
      <li class="{{ request()->routeIs('staff.officer.physical-intake*') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.physical-intake') }}"><i class="ri-walk-line"></i> <span>Physical Intake</span></a>
      </li>
      @endif

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Records
      </li>
      <li class="{{ request()->routeIs('staff.officer.records.journalists') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.records.journalists') }}"><i class="ri-id-card-line"></i> <span>Accredited Media Practitioners</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.records.mediahouses') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.records.mediahouses') }}"><i class="ri-building-2-line"></i> <span>Registered Media Houses</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Document Verification
      </li>
      <li class="{{ request()->routeIs('staff.officer.documents.uploaded') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.documents.uploaded') }}"><i class="ri-folder-2-line"></i> <span>Uploaded Documents</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.documents.pending') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.documents.pending') }}"><i class="ri-hourglass-line"></i> <span>Pending Verification</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.documents.verified') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.documents.verified') }}"><i class="ri-verified-badge-line"></i> <span>Verified</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Notices & Events
      </li>
      @if(Route::has('admin.content.index'))
      <li class="{{ request()->routeIs('admin.content.index') ? 'active' : '' }}">
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li class="{{ request()->routeIs('admin.news.index') ? 'active' : '' }}">
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
    @endif

    @if(!$isAdminPanel && ($role === 'accounts_payments' || $user?->hasRole('accounts_payments')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Accounts / Payments
      </li>

      <li class="{{ request()->routeIs('staff.accounts.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Payments
      </li>
      <li class="{{ request()->routeIs('staff.accounts.paynow.transactions') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.paynow.transactions') }}">
          <i class="ri-bank-card-line"></i> <span>Paid via PayNow</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.reconciliation') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.reconciliation') }}">
          <i class="ri-git-merge-line"></i> <span>Payment Reconciliation</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Paid via Uploads
      </li>
      <li class="{{ request()->routeIs('staff.accounts.proofs.pending') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.proofs.pending') }}">
          <i class="ri-time-line"></i> <span>Pending Proofs</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.proofs.approved') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.proofs.approved') }}">
          <i class="ri-checkbox-circle-line"></i> <span>Approved Proofs</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Waivers
      </li>
      <li class="{{ request()->routeIs('staff.accounts.waivers.requests') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.waivers.requests') }}">
          <i class="ri-inbox-line"></i> <span>Waiver Requests</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.waivers.approved') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.waivers.approved') }}">
          <i class="ri-check-double-line"></i> <span>Approved Waivers</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Applications
      </li>
      <li class="{{ request()->routeIs('staff.accounts.apps.paid') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.apps.paid') }}">
          <i class="ri-shield-check-line"></i> <span>Approved (Paid)</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.apps.pending') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.apps.pending') }}">
          <i class="ri-hourglass-line"></i> <span>Pending Payments</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.apps.waived') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.apps.waived') }}">
          <i class="ri-price-tag-3-line"></i> <span>Waived Applications</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Renewals
      </li>
      <li class="{{ request()->routeIs('staff.accounts.renewals.*') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.renewals.queue') }}">
          <i class="ri-refresh-line"></i> <span>Renewals Queue</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Reports
      </li>
      <li class="{{ request()->routeIs('staff.accounts.reports.revenue') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.reports.revenue') }}">
          <i class="ri-funds-line"></i> <span>Revenue Reports</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.reports.exceptions') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.reports.exceptions') }}">
          <i class="ri-error-warning-line"></i> <span>Payment Exceptions</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Tools
      </li>
      <li class="{{ request()->routeIs('staff.accounts.alerts') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.alerts') }}">
          <i class="ri-notification-3-line"></i> <span>Alerts</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.tools.paynow') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.tools.paynow') }}">
          <i class="ri-settings-3-line"></i> <span>PayNow Settings</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.help') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.help') }}">
          <i class="ri-question-line"></i> <span>Help & Support</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Notices & Events
      </li>
      @if(Route::has('admin.content.index'))
      <li>
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li>
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
    @endif

    @if(!$isAdminPanel && ($role === 'registrar' || $user?->hasRole('registrar')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Registrar
      </li>

      <li class="{{ request()->routeIs('staff.registrar.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.registrar.incoming-queue') ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.incoming-queue') }}">
          <i class="ri-list-check-2"></i> <span>Incoming Queue</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.registrar.payment-oversight') || request()->routeIs('staff.registrar.payment-detail') ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.payment-oversight') }}">
          <i class="ri-eye-line"></i> <span>Payment Statuses</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Accreditation
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'new'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'new']) }}"><i class="ri-file-list-3-line"></i> <span>All Applications</span></a>
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'under-review'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'under-review']) }}"><i class="ri-time-line"></i> <span>Under Review</span></a>
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'approved'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'approved']) }}"><i class="ri-checkbox-circle-line"></i> <span>Approved</span></a>
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'corrections'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'corrections']) }}"><i class="ri-arrow-go-back-line"></i> <span>Returned for Correction</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.registrar.renewals.*') ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.renewals.list','due-soon') }}"><i class="ri-calendar-todo-line"></i> <span>Renewals (AP5)</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Registration
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'new'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'new']) }}"><i class="ri-file-list-3-line"></i> <span>All Applications</span></a>
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'under-review'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'under-review']) }}"><i class="ri-time-line"></i> <span>Under Review</span></a>
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'approved'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'approved']) }}"><i class="ri-checkbox-circle-line"></i> <span>Approved</span></a>
      </li>
      <li class="{{ request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'corrections'])) ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'corrections']) }}"><i class="ri-arrow-go-back-line"></i> <span>Returned for Correction</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Reports
      </li>
      <li class="{{ request()->routeIs('staff.registrar.reports') ? 'active' : '' }}">
        <a href="{{ route('staff.registrar.reports') }}">
          <i class="ri-bar-chart-line"></i> <span>Operational Reports</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.accreditation') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.accreditation') }}">
          <i class="ri-line-chart-line"></i> <span>Accreditation Performance</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Communications
      </li>
      @if(Route::has('admin.content.index'))
      <li class="{{ request()->routeIs('admin.content.index') ? 'active' : '' }}">
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li class="{{ request()->routeIs('admin.news.index') ? 'active' : '' }}">
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>Press Statements (News)</span></a>
      </li>
      @endif
      @if(Route::has('admin.downloads.index'))
      <li class="{{ request()->routeIs('admin.downloads.index') ? 'active' : '' }}">
        <a href="{{ route('admin.downloads.index') }}"><i class="ri-file-download-line"></i> <span>Downloads</span></a>
      </li>
      @endif

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Records
      </li>
      <li class="{{ request()->routeIs('staff.officer.records.journalists') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.records.journalists') }}"><i class="ri-id-card-line"></i> <span>Accredited Media Practitioners</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.officer.records.mediahouses') ? 'active' : '' }}">
        <a href="{{ route('staff.officer.records.mediahouses') }}"><i class="ri-building-2-line"></i> <span>Registered Media Houses</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        System Settings
      </li>
      <li class="{{ request()->routeIs('admin.regions.index') ? 'active' : '' }}">
        <a href="{{ route('admin.regions.index') }}"><i class="ri-map-pin-line"></i> <span>Regional Offices</span></a>
      </li>
    @endif

    @if(!$isAdminPanel && ($role === 'production' || $user?->hasRole('production')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Production
      </li>
      <li class="{{ request()->routeIs('staff.production.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.production.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.queue') ? 'active' : '' }}">
        <a href="{{ route('staff.production.queue') }}">
          <i class="ri-inbox-2-line"></i> <span>Production Queue</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.cards') ? 'active' : '' }}">
        <a href="{{ route('staff.production.cards') }}">
          <i class="ri-id-card-line"></i> <span>Card Production</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.certificates') ? 'active' : '' }}">
        <a href="{{ route('staff.production.certificates') }}">
          <i class="ri-award-line"></i> <span>Certificate Production</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.printing') ? 'active' : '' }}">
        <a href="{{ route('staff.production.printing') }}">
          <i class="ri-printer-line"></i> <span>Printing Queue</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.issuance') ? 'active' : '' }}">
        <a href="{{ route('staff.production.issuance') }}">
          <i class="ri-hand-heart-line"></i> <span>Issuance & Collection</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.registers.issued') ? 'active' : '' }}">
        <a href="{{ route('staff.production.registers.issued') }}">
          <i class="ri-book-open-line"></i> <span>Issued Register</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.production.reports') ? 'active' : '' }}">
        <a href="{{ route('staff.production.reports') }}">
          <i class="ri-file-chart-line"></i> <span>Reports</span>
        </a>
      </li>
    @endif

    {{-- Super Admin / Director / IT Admin Oversight Section --}}
    @if($user?->hasAnyRole(['super_admin', 'director', 'it_admin']))
      @php
        $c = $admin_sidebar_counts ?? [
          'mediahouse_total' => 0,
          'accreditation_total' => 0,
          'pending_total' => 0,
          'pending_mediahouse' => 0,
          'pending_accreditation' => 0,
        ];
      @endphp

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        System Oversight & Admin
      </li>

      <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}">
          <i class="ri-dashboard-3-line"></i>
          <span>Dashboard</span>
          @if(($c['pending_total'] ?? 0) > 0)
            <span class="badge bg-warning text-dark" style="margin-left:auto;">{{ $c['pending_total'] }}</span>
          @endif
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
        <a href="{{ route('admin.analytics') }}">
          <i class="ri-line-chart-line"></i> <span>Analytics</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.mediahouse.*') ? 'active' : '' }}">
        <a href="{{ route('admin.mediahouse.index') }}">
          <i class="ri-building-2-line"></i>
          <span>Media House Registrations</span>
          <span class="badge bg-light text-dark" style="margin-left:auto;">{{ $c['mediahouse_total'] ?? 0 }}</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.accreditation.*') ? 'active' : '' }}">
        <a href="{{ route('admin.accreditation.index') }}">
          <i class="ri-id-card-line"></i>
          <span>Media Practitioners Accreditation</span>
          <span class="badge bg-light text-dark" style="margin-left:auto;">{{ $c['accreditation_total'] ?? 0 }}</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
        <a href="{{ route('admin.approvals.index') }}">
          <i class="ri-user-follow-line"></i> <span>User Approvals</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
        <a href="{{ Route::has('admin.content.index') ? route('admin.content.index') : (Route::has('content.index') ? route('content.index') : '#') }}">
          <i class="ri-megaphone-line"></i> <span>{{ __("Content") }}</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
        <a href="{{ route('admin.news.index') }}">
          <i class="ri-newspaper-line"></i> <span>{{ __("News") }}</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
        <a href="{{ route('admin.complaints.index') }}">
          <i class="ri-chat-1-line"></i> <span>{{ __("Complaints & Appeals") }}</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('admin.downloads.*') ? 'active' : '' }}">
        <a href="{{ route('admin.downloads.index') }}">
          <i class="ri-download-2-line"></i> <span>{{ __("Downloads") }}</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Staff Performance
      </li>
      @if(Route::has('staff.director.reports.staff'))
      <li class="{{ request()->routeIs('staff.director.reports.staff') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.staff') }}"><i class="ri-team-line"></i> <span>Staff Performance</span></a>
      </li>
      @endif

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        System Oversight
      </li>
      <li class="zmc-has-submenu {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a href="javascript:void(0)" class="d-flex align-items-center justify-content-between" onclick="this.nextElementSibling.classList.toggle('d-none')">
          <span><i class="ri-group-line me-2"></i> User Accounts & Management</span>
          <i class="ri-arrow-down-s-line"></i>
        </a>
        <ul class="list-unstyled ps-4 mt-2 {{ request()->routeIs('admin.users.*') ? '' : 'd-none' }}" style="font-size: 11px;">
           <li class="mb-2">
             <a href="{{ route('admin.users.index') }}" class="text-decoration-none {{ request()->routeIs('admin.users.index') ? 'text-warning fw-bold' : 'text-white-50' }}">
               <i class="ri-list-check me-1"></i> Overview
             </a>
           </li>
           <li class="mb-2">
             <a href="{{ route('admin.users.staff') }}" class="text-decoration-none {{ request()->routeIs('admin.users.staff') ? 'text-warning fw-bold' : 'text-white-50' }}">
               <i class="ri-user-star-line me-1"></i> Staff Accounts
             </a>
           </li>
           <li class="mb-2">
             <a href="{{ route('admin.users.public') }}" class="text-decoration-none {{ request()->routeIs('admin.users.public') ? 'text-warning fw-bold' : 'text-white-50' }}">
               <i class="ri-user-heart-line me-1"></i> Public Users
             </a>
           </li>
        </ul>
      </li>
      <li class="{{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
        <a href="{{ route('admin.audit.index') }}">
          <i class="ri-file-list-3-line"></i> <span>System Audit Logs</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('admin.users.login_activity') ? 'active' : '' }}">
        <a href="{{ route('admin.users.login_activity') }}">
          <i class="ri-login-box-line"></i> <span>User Login Trails</span>
        </a>
      </li>
    @endif

    @if(!$isAdminPanel && ($role === 'it_admin' || $user?->hasRole('it_admin')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        IT Administration
      </li>

      <li class="{{ request()->routeIs('staff.it.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.it.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>System Dashboard</span>
          <span class="badge bg-danger pulse-dot-small ms-auto" style="width:8px; height:8px; border-radius:50%; padding:0;"></span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Identity & Access
      </li>
      <li class="{{ request()->routeIs('users-mgmt') ? 'active' : '' }}">
        <a href="{{ route('users-mgmt') }}"><i class="ri-user-settings-line"></i> <span>Internal Users</span></a>
      </li>
      <li class="{{ request()->routeIs('roles-mgmt') ? 'active' : '' }}">
        <a href="{{ route('roles-mgmt') }}"><i class="ri-shield-user-line"></i> <span>Roles & Permissions</span></a>
      </li>
      <li class="{{ request()->routeIs('security-mgmt') ? 'active' : '' }}">
        <a href="{{ route('security-mgmt') }}"><i class="ri-lock-password-line"></i> <span>Access Control</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        System Configuration
      </li>
      <li class="{{ request()->routeIs('templates-mgmt') ? 'active' : '' }}">
        <a href="{{ route('templates-mgmt') }}"><i class="ri-layout-masonry-line"></i> <span>Card & Cert Templates</span></a>
      </li>
      <li class="{{ request()->routeIs('printers-mgmt') ? 'active' : '' }}">
        <a href="{{ route('printers-mgmt') }}"><i class="ri-printer-line"></i> <span>Hardware & Printing</span></a>
      </li>
      <li class="{{ request()->routeIs('numbering-mgmt') ? 'active' : '' }}">
        <a href="{{ route('numbering-mgmt') }}"><i class="ri-list-ordered"></i> <span>Automatic Numbering</span></a>
      </li>
      <li class="{{ request()->routeIs('categories-mgmt') ? 'active' : '' }}">
        <a href="{{ route('categories-mgmt') }}"><i class="ri-node-tree"></i> <span>Category Data</span></a>
      </li>
      <li class="{{ request()->routeIs('regions-mgmt') ? 'active' : '' }}">
        <a href="{{ route('regions-mgmt') }}"><i class="ri-map-pin-line"></i> <span>Regional Offices</span></a>
      </li>
      <li class="{{ request()->routeIs('document-settings-mgmt') ? 'active' : '' }}">
        <a href="{{ route('document-settings-mgmt') }}"><i class="ri-folder-settings-line"></i> <span>Document Rules</span></a>
      </li>
      <li class="{{ request()->routeIs('qr-security-mgmt') ? 'active' : '' }}">
        <a href="{{ route('qr-security-mgmt') }}"><i class="ri-qr-code-line"></i> <span>QR & Anti-Fraud</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        System Oversight
      </li>
      <li class="{{ request()->routeIs('audit-mgmt') ? 'active' : '' }}">
        <a href="{{ route('audit-mgmt') }}">
          <i class="ri-list-check-2"></i> <span>Audit Logs</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('reports-mgmt') ? 'active' : '' }}">
        <a href="{{ route('reports-mgmt') }}">
          <i class="ri-bar-chart-boxed-line"></i> <span>IT Reports</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('backup-mgmt') ? 'active' : '' }}">
        <a href="{{ route('backup-mgmt') }}">
          <i class="ri-database-2-line"></i> <span>Data & Backups</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('system-mgmt') ? 'active' : '' }}">
        <a href="{{ route('system-mgmt') }}">
          <i class="ri-settings-3-line"></i> <span>Core Settings</span>
        </a>
      </li>
    @endif

    @if(!$isAdminPanel && ($role === 'auditor' || $user?->hasRole('auditor')))
      <li class="menu-title" style="margin-top:14px;">AUDITOR</li>

      <li class="{{ request()->routeIs('staff.auditor.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.dashboard') }}">
          <i class="ri-eye-line"></i> <span>Dashboard</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.analytics') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.analytics') }}">
          <i class="ri-line-chart-line"></i> <span>Analytics</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.logins') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.logins') }}">
          <i class="ri-login-box-line"></i> <span>User Logins</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.applications') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.applications') }}">
          <i class="ri-file-search-line"></i> <span>Application Audits</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.paynow') || request()->routeIs('staff.auditor.proofs') || request()->routeIs('staff.auditor.waivers') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.paynow') }}">
          <i class="ri-exchange-dollar-line"></i> <span>Fees & Payments Audit</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.logs') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.logs') }}">
          <i class="ri-file-list-3-line"></i> <span>Audit Logs</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.reports*') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.reports') }}">
          <i class="ri-bar-chart-2-line"></i> <span>Audit Reports</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('staff.auditor.security') ? 'active' : '' }}">
        <a href="{{ route('staff.auditor.security') }}">
          <i class="ri-shield-line"></i> <span>Security Oversight</span>
        </a>
      </li>

      @if(Route::has('staff.director.reports.financial'))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Oversight
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.financial') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.financial') }}"><i class="ri-money-dollar-box-line"></i> <span>Financial Overview</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.compliance') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.compliance') }}"><i class="ri-shield-flash-line"></i> <span>Compliance & Risk</span></a>
      </li>
      @endif

      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Notices
      </li>
      @if(Route::has('admin.content.index'))
      <li>
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li>
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
    @endif

    @if($role === 'director' || $user?->hasRole('director'))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-sm); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Director MDG Strategic Oversight
      </li>
      <li class="{{ request()->routeIs('staff.director.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.director.dashboard') }}"><i class="ri-pulse-line"></i> <span>Overview</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.accreditation') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.accreditation') }}"><i class="ri-bar-chart-2-line"></i> <span>Accreditation Performance</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.financial') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.financial') }}"><i class="ri-money-dollar-box-line"></i> <span>Financial Overview</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.compliance') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.compliance') }}"><i class="ri-shield-flash-line"></i> <span>Compliance & Risk</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.mediahouses') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.mediahouses') }}"><i class="ri-building-line"></i> <span>Media House Oversight</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.issuance') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.issuance') }}"><i class="ri-printer-cloud-line"></i> <span>Issuance & Printing</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.geographic') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.geographic') }}"><i class="ri-map-pin-line"></i> <span>Geographic Distribution</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.director.reports.downloads') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.downloads') }}"><i class="ri-download-cloud-2-line"></i> <span>Reports & Downloads</span></a>
      </li>
    @endif

    {{-- PR Officer Section --}}
    @if(!$isAdminPanel && ($role === 'pr_officer' || $user?->hasRole('pr_officer')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Public Relations
      </li>
      <li class="{{ request()->routeIs('staff.pr.dashboard') ? 'active' : '' }}">
        <a href="{{ Route::has('staff.pr.dashboard') ? route('staff.pr.dashboard') : route('staff.officer.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Dashboard</span>
        </a>
      </li>
      @if(Route::has('admin.content.index'))
      <li class="{{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
        <a href="{{ route('admin.content.index') }}"><i class="ri-megaphone-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
        <a href="{{ route('admin.news.index') }}"><i class="ri-newspaper-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
      @if(Route::has('admin.downloads.index'))
      <li class="{{ request()->routeIs('admin.downloads.*') ? 'active' : '' }}">
        <a href="{{ route('admin.downloads.index') }}"><i class="ri-download-2-line"></i> <span>Downloads</span></a>
      </li>
      @endif
    @endif

    {{-- Public Information & Compliance Section --}}
    @if(!$isAdminPanel && ($role === 'public_info_compliance' || $user?->hasRole('public_info_compliance')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Public Info & Compliance
      </li>
      <li class="{{ request()->routeIs('staff.compliance.dashboard') ? 'active' : '' }}">
        <a href="{{ Route::has('staff.compliance.dashboard') ? route('staff.compliance.dashboard') : route('staff.officer.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Dashboard</span>
        </a>
      </li>
      @if(Route::has('admin.complaints.index'))
      <li class="{{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
        <a href="{{ route('admin.complaints.index') }}"><i class="ri-chat-1-line"></i> <span>Complaints & Appeals</span></a>
      </li>
      @endif
      @if(Route::has('admin.content.index'))
      <li>
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li>
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
    @endif

    {{-- Research, Training & Standards Development Section --}}
    @if(!$isAdminPanel && ($role === 'research_training' || $user?->hasRole('research_training')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Research & Training
      </li>
      <li class="{{ request()->routeIs('staff.research.dashboard') ? 'active' : '' }}">
        <a href="{{ Route::has('staff.research.dashboard') ? route('staff.research.dashboard') : route('staff.officer.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Dashboard</span>
        </a>
      </li>
      @if(Route::has('admin.content.index'))
      <li>
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li>
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
      @if(Route::has('admin.downloads.index'))
      <li>
        <a href="{{ route('admin.downloads.index') }}"><i class="ri-download-2-line"></i> <span>Downloads</span></a>
      </li>
      @endif
    @endif

    {{-- Chief Accountant Section --}}
    @if(!$isAdminPanel && ($role === 'chief_accountant' || $user?->hasRole('chief_accountant')))
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Chief Accountant
      </li>
      <li class="{{ request()->routeIs('staff.accounts.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.dashboard') }}">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.paynow.transactions') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.paynow.transactions') }}"><i class="ri-bank-card-line"></i> <span>Paid via PayNow</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.proofs.pending') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.proofs.pending') }}"><i class="ri-time-line"></i> <span>Pending Proofs</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.proofs.approved') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.proofs.approved') }}"><i class="ri-checkbox-circle-line"></i> <span>Approved Proofs</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.reports.revenue') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.reports.revenue') }}"><i class="ri-funds-line"></i> <span>Revenue Reports</span></a>
      </li>
      <li class="{{ request()->routeIs('staff.accounts.reports.audit') ? 'active' : '' }}">
        <a href="{{ route('staff.accounts.reports.audit') }}"><i class="ri-file-search-line"></i> <span>Audit Reports</span></a>
      </li>
      @if(Route::has('staff.director.reports.financial'))
      <li class="{{ request()->routeIs('staff.director.reports.financial') ? 'active' : '' }}">
        <a href="{{ route('staff.director.reports.financial') }}"><i class="ri-money-dollar-box-line"></i> <span>Financial Overview</span></a>
      </li>
      @endif
      <li class="menu-title" style="padding:10px 18px; font-size:var(--font-size-xs); letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Notices
      </li>
      @if(Route::has('admin.content.index'))
      <li>
        <a href="{{ route('admin.content.index') }}"><i class="ri-notification-3-line"></i> <span>Notices & Events</span></a>
      </li>
      @endif
      @if(Route::has('admin.news.index'))
      <li>
        <a href="{{ route('admin.news.index') }}"><i class="ri-megaphone-line"></i> <span>News / Press Statements</span></a>
      </li>
      @endif
    @endif

    {{-- Switch Role for non-admin users --}}
    @if(!$user?->hasRole('super_admin') && !$user?->hasRole('director'))
      <li class="{{ request()->routeIs('staff.entry') ? 'active' : '' }}">
        <a href="{{ route('staff.entry') }}">
          <i class="ri-shuffle-line"></i> <span>Switch Role</span>
        </a>
      </li>
    @endif
  </ul>

  <div class="sidebar-user">
    <img src="https://ui-avatars.com/api/?name={{ urlencode($user?->name ?? 'User') }}&background=facc15&color=000" alt="user">
    <div style="line-height:1.1;">
      <div style="font-weight:700;font-size:var(--font-size-sm);color:#fff;">
        {{ $user?->name ?? $user?->email }}
      </div>
      <div style="font-size:10px;color:rgba(255,255,255,0.7);">
        {{ $user?->designation ?? ($role ? strtoupper(str_replace('_',' ', $role)) : 'STAFF') }}
        @if(!empty($user?->region)) • {{ strtoupper($user->region) }} @endif
      </div>
    </div>
  </div>
</div>
