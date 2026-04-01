<!-- STAFF SIDEBAR -->
<?php
  $role = session('active_staff_role');
  $user = auth()->user();
  $isAdminPanel = $user?->hasRole('super_admin') || $user?->hasRole('director');

  // Dynamic context detection for a "complete shift" experience
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  if (str_starts_with($currentRoute, 'staff.production.')) {
      $role = 'production';
  } elseif (str_starts_with($currentRoute, 'staff.officer.')) {
      $role = 'accreditation_officer';
  }
?>

<div class="vertical-menu">
  <div class="navbar-brand-box">
    <div class="navbar-brand-circle">
      <img src="<?php echo e(asset('zmc_logo_circular.png')); ?>" alt="ZMC Logo">
    </div>
    <div>
      <span class="logo-text"><span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span></span>
    </div>
  </div>

  <ul class="sidebar-menu">
    <?php if(!$isAdminPanel && $role === 'accreditation_officer'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Accreditation Officer
      </li>

      <li class="<?php echo e(request()->routeIs('staff.officer.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i> <span>Dashboard</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Applications
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.applications.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.applications.index')); ?>">
          <i class="ri-file-list-3-line"></i> <span>All Applications</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.applications.new') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.applications.new')); ?>"><i class="ri-sparkling-2-line"></i> <span>Recent Applications</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.applications.pending') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.applications.pending')); ?>"><i class="ri-time-line"></i> <span>Pending Review</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.applications.approved') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.applications.approved')); ?>"><i class="ri-checkbox-circle-line"></i> <span>Approved</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.applications.rejected') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.applications.rejected')); ?>"><i class="ri-chat-check-line"></i> <span>Returned for Correction</span></a>
      </li>



      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Physical Intake
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.physical-intake') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.physical-intake')); ?>"><i class="ri-walk-line"></i> <span>Walk-in Intake</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Records
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.records.accredited-journalists') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.records.accredited-journalists')); ?>"><i class="ri-id-card-line"></i> <span>Accredited Media Practitioners</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.records.registered-mediahouses') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.records.registered-mediahouses')); ?>"><i class="ri-building-2-line"></i> <span>Registered Media Houses</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Document Verification
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.documents.uploaded') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.documents.uploaded')); ?>"><i class="ri-folder-2-line"></i> <span>Uploaded Documents</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.documents.pending') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.documents.pending')); ?>"><i class="ri-hourglass-line"></i> <span>Pending Verification</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.documents.verified') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.documents.verified')); ?>"><i class="ri-verified-badge-line"></i> <span>Approved</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.officer.documents.rejected') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.officer.documents.rejected')); ?>"><i class="ri-file-warning-line"></i> <span>Returned</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Module Switches
      </li>
      <li class="">
        <a href="<?php echo e(route('staff.switch-role', ['role' => 'production'])); ?>"><i class="ri-printer-line"></i> <span>Switch to Production</span></a>
      </li>
      <li class="">
        <a href="<?php echo e(route('staff.production.designer')); ?>"><i class="ri-paint-brush-line"></i> <span>Designer</span></a>
      </li>
    <?php endif; ?>

    <?php if(!$isAdminPanel && $role === 'accounts_payments'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Accounts / Payments
      </li>

      <li class="<?php echo e(request()->routeIs('staff.accounts.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Payments
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.paynow.transactions') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.paynow.transactions')); ?>">
          <i class="ri-bank-card-line"></i> <span>PayNow Transactions</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.reconciliation') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.reconciliation')); ?>">
          <i class="ri-git-merge-line"></i> <span>Payment Reconciliation</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Payment Proofs
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.proofs.pending') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.proofs.pending')); ?>">
          <i class="ri-time-line"></i> <span>Pending Proofs</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.proofs.approved') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.proofs.approved')); ?>">
          <i class="ri-checkbox-circle-line"></i> <span>Approved Proofs</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Waivers
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.waivers.requests') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.waivers.requests')); ?>">
          <i class="ri-inbox-line"></i> <span>Waiver Requests</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.waivers.approved') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.waivers.approved')); ?>">
          <i class="ri-check-double-line"></i> <span>Approved Waivers</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.waivers.rejected') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.waivers.rejected')); ?>">
          <i class="ri-close-circle-line"></i> <span>Returned for Correction</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Applications
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.apps.paid') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.apps.paid')); ?>">
          <i class="ri-shield-check-line"></i> <span>Approved (Paid)</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.apps.pending') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.apps.pending')); ?>">
          <i class="ri-hourglass-line"></i> <span>Pending Payments</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.apps.waived') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.apps.waived')); ?>">
          <i class="ri-price-tag-3-line"></i> <span>Waived Applications</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Reports
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.reports.revenue') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.reports.revenue')); ?>">
          <i class="ri-funds-line"></i> <span>Revenue Reports</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.reports.exceptions') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.reports.exceptions')); ?>">
          <i class="ri-error-warning-line"></i> <span>Payment Exceptions</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Alerts & Tools
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.alerts') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.alerts')); ?>">
          <i class="ri-notification-3-line"></i> <span>Alerts</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.tools.paynow') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.tools.paynow')); ?>">
          <i class="ri-settings-3-line"></i> <span>PayNow Settings</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.help') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.help')); ?>">
          <i class="ri-question-line"></i> <span>Help & Support</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if(!$isAdminPanel && $role === 'registrar'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Registrar
      </li>

      <li class="<?php echo e(request()->routeIs('staff.registrar.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.registrar.incoming-queue') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.incoming-queue')); ?>">
          <i class="ri-list-check-2"></i> <span>All Applications</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Accreditation
      </li>
      <li class="<?php echo e(request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'all'])) ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'all'])); ?>"><i class="ri-apps-line"></i> <span>All Applications</span></a>
      </li>
      <li class="<?php echo e(request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'approved'])) ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'approved'])); ?>"><i class="ri-checkbox-circle-line"></i> <span>Approved</span></a>
      </li>
      <li class="<?php echo e(request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'corrections'])) ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.apps.list',['type'=>'accreditation','bucket'=>'corrections'])); ?>"><i class="ri-error-warning-line"></i> <span>Returned for Correction</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.registrar.renewals.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.renewals.list','due-soon')); ?>"><i class="ri-calendar-todo-line"></i> <span>Renewals (AP5)</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Registration
      </li>
      <li class="<?php echo e(request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'all'])) ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'all'])); ?>"><i class="ri-apps-line"></i> <span>All Applications</span></a>
      </li>
      <li class="<?php echo e(request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'approved'])) ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'approved'])); ?>"><i class="ri-checkbox-circle-line"></i> <span>Approved</span></a>
      </li>
      <li class="<?php echo e(request()->fullUrlIs(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'corrections'])) ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.apps.list',['type'=>'registration','bucket'=>'corrections'])); ?>"><i class="ri-error-warning-line"></i> <span>Returned for Correction</span></a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Reports
      </li>
      <li class="<?php echo e(request()->routeIs('staff.registrar.reports') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.reports')); ?>">
          <i class="ri-bar-chart-line"></i> <span>Operational Reports</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.reports.accreditation') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.reports.accreditation')); ?>">
          <i class="ri-line-chart-line"></i> <span>Accreditation Performance</span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Communications
      </li>
      <li class="<?php echo e(request()->routeIs('staff.registrar.notices-events') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.notices-events')); ?>"><i class="ri-notification-3-line"></i> <span>Notices &amp; Events</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.registrar.news') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.registrar.news')); ?>"><i class="ri-megaphone-line"></i> <span>Press Statements (News)</span></a>
      </li>
      <li>
        <a href="<?php echo e(route('admin.downloads.index')); ?>"><i class="ri-file-download-line"></i> <span>Downloads</span></a>
      </li>
    <?php endif; ?>

    <?php if(!$isAdminPanel && $role === 'production'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Production
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.queue') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.queue')); ?>">
          <i class="ri-inbox-2-line"></i> <span>Production Queue</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.cards') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.cards')); ?>">
          <i class="ri-id-card-line"></i> <span>Card Production</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.certificates') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.certificates')); ?>">
          <i class="ri-award-line"></i> <span>Certificate Production</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.printing') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.printing')); ?>">
          <i class="ri-printer-line"></i> <span>Printing Queue</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.issuance') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.issuance')); ?>">
          <i class="ri-hand-heart-line"></i> <span>Issuance & Collection</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.registers.issued') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.registers.issued')); ?>">
          <i class="ri-book-open-line"></i> <span>Issued Register</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.reports') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.reports')); ?>">
          <i class="ri-file-chart-line"></i> <span>Reports</span>
        </a>
      </li>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Template Design
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.designer') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.designer')); ?>">
          <i class="ri-palette-line"></i> <span>Designer</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.production.templates') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.production.templates')); ?>">
          <i class="ri-layout-masonry-line"></i> <span>Templates</span>
        </a>
      </li>
    <?php endif; ?>

    
    <?php if(!$isAdminPanel && ($role === 'accountant' || $role === 'chief_accountant')): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        <?php echo e($role === 'chief_accountant' ? 'Chief Accountant' : 'Accountant'); ?>

      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i> <span>Financial Oversight</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.payments.index') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.payments.index')); ?>">
          <i class="ri-bank-card-line"></i> <span>Payment Processing</span>
        </a>
      </li>
      <?php if($role === 'chief_accountant'): ?>
        <li class="<?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>">
          <a href="<?php echo e(route('admin.complaints.index', ['type'=>'appeal'])); ?>">
            <i class="ri-file-shield-2-line"></i> <span>FOIA Appeals Oversight</span>
          </a>
        </li>
      <?php endif; ?>
    <?php endif; ?>
    <?php if($user?->hasRole('super_admin')): ?>
      <?php
        $c = $admin_sidebar_counts ?? [
          'mediahouse_total' => 0,
          'accreditation_total' => 0,
          'pending_total' => 0,
          'pending_mediahouse' => 0,
          'pending_accreditation' => 0,
        ];
      ?>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Super Admin
      </li>

      <li class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i>
          <span>Overview</span>
          <?php if(($c['pending_total'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark" style="margin-left:auto;"><?php echo e($c['pending_total']); ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.analytics') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.analytics')); ?>">
          <i class="ri-line-chart-line"></i> <span>Analytics</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.mediahouse.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.mediahouse.index')); ?>">
          <i class="ri-building-2-line"></i>
          <span>Media House Registrations</span>
          <span class="badge bg-light text-dark" style="margin-left:auto;"><?php echo e($c['mediahouse_total'] ?? 0); ?></span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.accreditation.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.accreditation.index')); ?>">
          <i class="ri-id-card-line"></i>
          <span>Media Practitioners Accreditation</span>
          <span class="badge bg-light text-dark" style="margin-left:auto;"><?php echo e($c['accreditation_total'] ?? 0); ?></span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.users.staff')); ?>">
          <i class="ri-user-settings-line"></i> <span>User & Account Management</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.roles.index')); ?>">
          <i class="ri-lock-2-line"></i> <span>Roles</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.permissions.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.permissions.index')); ?>">
          <i class="ri-key-2-line"></i> <span>Permissions</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.approvals.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.approvals.index')); ?>">
          <i class="ri-user-follow-line"></i> <span>User Approvals</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.content.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(Route::has('admin.content.index') ? route('admin.content.index') : (Route::has('content.index') ? route('content.index') : '#')); ?>">
          <i class="ri-megaphone-line"></i> <span><?php echo e(__("Content")); ?></span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.news.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.news.index')); ?>">
          <i class="ri-newspaper-line"></i> <span><?php echo e(__("News")); ?></span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.complaints.index')); ?>">
          <i class="ri-chat-1-line"></i> <span><?php echo e(__("Complaints & Appeals")); ?></span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.security') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.security')); ?>">
          <i class="ri-shield-line"></i> <span>Security Oversight</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.downloads.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.downloads.index')); ?>">
          <i class="ri-download-2-line"></i> <span><?php echo e(__("Downloads")); ?></span>
        </a>
      </li>

    <?php endif; ?>

    <?php if(!$isAdminPanel && $role === 'it_admin'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        IT Admin
      </li>

      <li class="<?php echo e(request()->routeIs('staff.it.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.it.dashboard')); ?>">
          <i class="ri-dashboard-3-line"></i> <span>Overview</span>
          <span class="badge bg-danger pulse-dot-small ms-auto" style="width:8px; height:8px; border-radius:50%; padding:0;"></span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.users.staff')); ?>">
          <i class="ri-user-settings-line"></i> <span>User & Account Management</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('admin.downloads.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.downloads.index')); ?>">
          <i class="ri-file-search-line"></i> <span><?php echo e(__("Public Portal Documents")); ?></span>
        </a>
      </li>

      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        System Oversight
      </li>
      <li class="<?php echo e(request()->routeIs('admin.audit.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.audit.index')); ?>">
          <i class="ri-file-list-3-line"></i> <span>System Audit Logs</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('admin.users.login_activity') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.users.login_activity')); ?>">
          <i class="ri-login-box-line"></i> <span>User Login Trails</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.auditor.security') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.security')); ?>">
          <i class="ri-shield-line"></i> <span>Security Oversight</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if(!$isAdminPanel && $role === 'auditor'): ?>
      <li class="menu-title" style="margin-top:14px;">AUDITOR</li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.dashboard')); ?>">
          <i class="ri-eye-line"></i> <span>Overview</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.analytics') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.analytics')); ?>">
          <i class="ri-line-chart-line"></i> <span>Analytics</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.logins') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.logins')); ?>">
          <i class="ri-login-box-line"></i> <span>User Logins</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.applications') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.applications')); ?>">
          <i class="ri-file-search-line"></i> <span>Application Audits</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.paynow') || request()->routeIs('staff.auditor.proofs') || request()->routeIs('staff.auditor.waivers') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.paynow')); ?>">
          <i class="ri-exchange-dollar-line"></i> <span>Fees & Payments Audit</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.logs') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.logs')); ?>">
          <i class="ri-file-list-3-line"></i> <span>Audit Logs</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.reports*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.reports')); ?>">
          <i class="ri-bar-chart-2-line"></i> <span>Audit Reports</span>
        </a>
      </li>

      <li class="<?php echo e(request()->routeIs('staff.auditor.security') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.auditor.security')); ?>">
          <i class="ri-shield-line"></i> <span>Security Oversight</span>
        </a>
      </li>
    <?php endif; ?>

    
    <?php if(!$isAdminPanel && $role === 'pr_officer'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        PR Officer
      </li>
      <li class="<?php echo e(request()->routeIs('admin.content.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.content.index')); ?>">
          <i class="ri-notification-3-line"></i> <span>Notices & Events</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('admin.news.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.news.index')); ?>">
          <i class="ri-megaphone-line"></i> <span>Press Statements (News)</span>
        </a>
      </li>
      <li class="">
        <a href="<?php echo e(route('admin.content.index')); ?>#vacancies">
          <i class="ri-briefcase-line"></i> <span>Vacancies & Tenders</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('admin.analytics') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.analytics')); ?>">
          <i class="ri-line-chart-line"></i> <span>Website Traffic Trends</span>
        </a>
      </li>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55); margin-top:10px;">
        Website Admin
      </li>
      <li>
        <a href="https://cpanel.zmc.org.zw" target="_blank">
          <i class="ri-settings-5-line"></i> <span>CPANEL Access</span>
        </a>
      </li>
      <li>
        <a href="https://admin.zmc.org.zw" target="_blank">
          <i class="ri-external-link-line"></i> <span>Admin Portal</span>
        </a>
      </li>
    <?php endif; ?>

    
    <?php if(!$isAdminPanel && $role === 'research_training_standards'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Research & Training
      </li>
      <li class="<?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.complaints.index')); ?>">
          <i class="ri-chat-smile-line"></i> <span>Complaints Management</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('admin.analytics') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.analytics')); ?>">
          <i class="ri-line-chart-line"></i> <span>Enquiries Analytics</span>
        </a>
      </li>
    <?php endif; ?>

    
    <?php if(!$isAdminPanel && $role === 'public_info_compliance'): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Compliance Manager
      </li>
      <li class="<?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.complaints.index')); ?>">
          <i class="ri-question-answer-line"></i> <span>Appeals Management</span>
        </a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.accounts.payments.index') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.accounts.payments.index')); ?>">
          <i class="ri-money-dollar-circle-line"></i> <span>Payment Status Tracking</span>
        </a>
      </li>
      <li class="">
        <a href="<?php echo e(route('admin.downloads.index')); ?>">
          <i class="ri-download-2-line"></i> <span>Compliance Reports</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if($role === 'director' || $user?->hasRole('director')): ?>
      <li class="menu-title" style="padding:10px 18px; font-size:11px; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.55);">
        Director Media Development and Governance Dashboard
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.dashboard') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.dashboard')); ?>"><i class="ri-pulse-line"></i> <span>Overview</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.reports.accreditation') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.reports.accreditation')); ?>"><i class="ri-bar-chart-2-line"></i> <span>Accreditation Performance</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.reports.financial') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.reports.financial')); ?>"><i class="ri-money-dollar-box-line"></i> <span>Financial Overview</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.reports.compliance') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.reports.compliance')); ?>"><i class="ri-shield-flash-line"></i> <span>Compliance & Risk</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.reports.mediahouses') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.reports.mediahouses')); ?>"><i class="ri-building-line"></i> <span>Media House Oversight</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('staff.director.reports.issuance') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.director.reports.issuance')); ?>"><i class="ri-printer-cloud-line"></i> <span>Issuance Oversight</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('admin.downloads.index') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.downloads.index')); ?>"><i class="ri-download-cloud-2-line"></i> <span>Reports & Downloads</span></a>
      </li>
      <li class="<?php echo e(request()->routeIs('admin.complaints.*') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('admin.complaints.index')); ?>"><i class="ri-chat-smile-line"></i> <span>Complaints Oversight</span></a>
      </li>
    <?php endif; ?>

    
    <?php if(!$user?->hasRole('super_admin') && !$user?->hasRole('director')): ?>
      <li class="<?php echo e(request()->routeIs('staff.entry') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('staff.entry')); ?>">
          <i class="ri-shuffle-line"></i> <span>Switch Role</span>
        </a>
      </li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-user">
    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($user?->name ?? 'User')); ?>&background=facc15&color=000" alt="user">
    <div style="line-height:1.1;">
      <div style="font-weight:700;font-size:13px;color:#fff;">
        <?php echo e($user?->name ?? $user?->email); ?>

      </div>
      <div style="font-size:11px;color:rgba(255,255,255,0.7);">
        <?php echo e($user?->designation ?? ($role ? strtoupper(str_replace('_',' ', $role)) : 'STAFF')); ?>

        <?php if(!empty($user?->region)): ?> • <?php echo e(strtoupper($user->region)); ?> <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/layouts/sidebar_staff.blade.php ENDPATH**/ ?>