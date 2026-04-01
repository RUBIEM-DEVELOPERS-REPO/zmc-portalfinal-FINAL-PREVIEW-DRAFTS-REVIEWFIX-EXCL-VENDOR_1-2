<!-- TOPBAR -->
<?php
  $isStaff = request()->is('staff') || request()->is('staff/*') || str_starts_with(request()->path(), 'staff');
  $logoutRoute = $isStaff ? route('staff.logout') : route('logout');

  $u = auth()->user();
  $unreadCount = 0;
  $latestMsgs = collect();
  $notifCount = 0;
  $latestNotifs = collect();
  if ($u && class_exists(\App\Models\ApplicationMessage::class)) {
    $unreadCount = \App\Models\ApplicationMessage::where('to_user_id', $u->id)->whereNull('read_at')->count();
    $latestMsgs = \App\Models\ApplicationMessage::with(['application'])
      ->where(function($q) use ($u){ $q->where('from_user_id',$u->id)->orWhere('to_user_id',$u->id); })
      ->orderByDesc('sent_at')->orderByDesc('id')
      ->take(5)
      ->get();
  }
  if ($u) {
    $notifCount = $u->unreadNotifications()->count();
    $latestNotifs = $u->notifications()->latest()->take(5)->get();
  }
?>

<header class="topbar">
  <div class="topbar-row--main">

    <!-- Left Section: Toggle Only -->
    <div class="topbar-left">
      <button class="topbar-toggle" type="button" id="sidebarToggle" aria-label="Toggle menu">
        <i class="ri-menu-2-line"></i>
      </button>
    </div>

    <!-- Right Section: Utility Actions -->
    <div class="topbar-right">

      <div class="d-flex align-items-center gap-2">
        <!-- Search, Messages, Notifications will follow here -->
      </div>

      <!-- Global Search (Staff only) -->
      <?php if($isStaff): ?>
        <form action="<?php echo e(route('staff.search')); ?>" method="GET" class="d-none d-md-flex align-items-center me-2">
          <div class="input-group input-group-sm" style="width: 210px; border: 1px solid rgba(255,255,255,0.2); border-radius: 999px; overflow: hidden; background: rgba(255,255,255,0.05);">
            <input type="text" name="q" class="form-control border-0 bg-transparent text-white placeholder-light" placeholder="Search Ref / Name..." style="font-size: 10px;">
            <button class="btn btn-link text-white p-2" type="submit" style="text-decoration: none;">
              <i class="ri-search-line"></i>
            </button>
          </div>
        </form>
      <?php else: ?>
        <button class="icon-btn" type="button" title="Search disabled for public" disabled>
          <i class="ri-search-line"></i>
        </button>
      <?php endif; ?>

      <!-- MESSAGES DROPDOWN -->
      <div class="dropdown">
        <button class="icon-btn dropdown-toggle hide-caret" type="button" id="messageDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Messages">
          <i class="ri-mail-line"></i>
          <?php if($unreadCount > 0): ?>
            <span class="icon-badge badge-blue"><?php echo e($unreadCount); ?></span>
          <?php endif; ?>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 mt-2" aria-labelledby="messageDropdown" style="width: 300px; border-radius: 12px; overflow: hidden;">
          <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold" style="font-size: 11px;">Messages</h6>
            <?php if($unreadCount > 0): ?>
              <span class="badge bg-primary-subtle text-primary small"><?php echo e($unreadCount); ?> New</span>
            <?php else: ?>
              <span class="badge bg-primary-subtle text-primary small">Up to date</span>
            <?php endif; ?>
          </div>
          <div class="message-scroll" style="max-height: 250px; overflow-y: auto;">
            <?php $__empty_1 = true; $__currentLoopData = $latestMsgs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $isUnread = $u && (int)$msg->to_user_id === (int)$u->id && empty($msg->read_at);
                $otherId = $u ? ((int)$msg->from_user_id === (int)$u->id ? $msg->to_user_id : $msg->from_user_id) : null;
                $other = $otherId ? \App\Models\User::find($otherId) : null;
                $who = $other?->name ?? 'User';
                $ref = $msg->application?->reference ?? ('APP-' . $msg->application_id);
                $when = $msg->sent_at ? $msg->sent_at->diffForHumans() : ($msg->created_at?->diffForHumans() ?? '');
              ?>
              <a href="<?php echo e(route('messages.thread', $msg->application_id)); ?>" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 <?php echo e($isUnread ? 'bg-light' : ''); ?>">
                <div class="flex-shrink-0 bg-warning-subtle text-warning p-2 rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                  <i class="ri-message-2-line"></i>
                </div>
                <div class="flex-grow-1">
                  <p class="mb-0 fw-bold small text-dark"><?php echo e($who); ?> <span class="text-muted" style="font-weight:800;">• <?php echo e($ref); ?></span></p>
                  <p class="mb-0 text-muted small"><?php echo e(\Illuminate\Support\Str::limit($msg->body, 60)); ?></p>
                  <span class="text-muted" style="font-size: 10px;"><?php echo e($when); ?></span>
                </div>
              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="p-3 text-muted small">No messages yet.</div>
            <?php endif; ?>
          </div>
          <a href="<?php echo e(route('messages.index')); ?>" class="dropdown-item text-center p-2 bg-light small fw-bold text-primary">View All Messages</a>
        </div>
      </div>

      <!-- Notifications Dropdown (real data) -->
      <div class="dropdown">
        <button class="icon-btn dropdown-toggle hide-caret" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
          <i class="ri-notification-3-line"></i>
          <?php if($notifCount > 0): ?>
            <span class="icon-badge badge-red"><?php echo e($notifCount > 99 ? '99+' : $notifCount); ?></span>
          <?php endif; ?>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 mt-2" aria-labelledby="notifDropdown" style="width: 340px; border-radius: 12px; overflow: hidden;">
          <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold" style="font-size: 11px;">Notifications</h6>
            <div class="d-flex align-items-center gap-2">
              <?php if($notifCount > 0): ?>
                <a href="javascript:void(0)" onclick="markAllNotifsRead()" class="text-primary small fw-bold" style="font-size: 10px; text-decoration: none;">Mark all as Read</a>
                <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;"><?php echo e($notifCount); ?> Unread</span>
              <?php else: ?>
                <span class="badge bg-success-subtle text-success" style="font-size: 10px;">All caught up</span>
              <?php endif; ?>
            </div>
          </div>
          <div style="max-height: 280px; overflow-y: auto;">
            <?php $__empty_1 = true; $__currentLoopData = $latestNotifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $data = $n->data ?? [];
                $ref = $data['reference'] ?? null;
                $msg = $data['message'] ?? 'Notification';
                $when = $n->created_at?->diffForHumans();
                $unread = is_null($n->read_at);
              ?>
              <div class="p-3 border-bottom <?php echo e($unread ? 'bg-light' : ''); ?> d-flex justify-content-between align-items-start gap-2 notification-item" data-notification-id="<?php echo e($n->id); ?>">
                <div class="flex-grow-1">
                  <div class="fw-bold small text-dark"><?php echo e($ref ? ($ref . ' • ') : ''); ?><?php echo e($msg); ?></div>
                  <div class="text-muted" style="font-size: 10px;"><?php echo e($when); ?></div>
                </div>
                <?php if($unread): ?>
                  <button onclick="markNotifRead('<?php echo e($n->id); ?>')" class="btn btn-sm btn-link text-primary p-0" style="font-size: 10px; text-decoration: none; white-space: nowrap;" title="Mark as read">
                    <i class="ri-check-line"></i>
                  </button>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="p-3 text-muted small">No notifications yet.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- THEME TOGGLE -->
       <button class="icon-btn theme-toggle-btn me-2" type="button" onclick="toggleZMCTheme()" title="Toggle Light/Dark Mode">
         <i class="ri-moon-line" id="themeIcon"></i>
       </button>
 
       <!-- Logout Pill -->
       <button class="logout-pill" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" type="button">
         <i class="ri-logout-box-r-line"></i>
         <span>LOGOUT</span>
       </button>
      <form id="logout-form" action="<?php echo e($logoutRoute); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>

    </div>
  </div>
</header>

<script>
  async function markAllNotifsRead() {
    try {
      const res = await fetch("<?php echo e(route('notifications.markRead')); ?>", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
          'Accept': 'application/json'
        }
      });
      if (res.ok) {
        window.location.reload();
      }
    } catch (e) {
      console.error('Failed to mark notifications as read', e);
    }
  }

  async function markNotifRead(notificationId) {
    try {
      const res = await fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      });
      
      if (res.ok) {
        // Remove the notification item from the list
        const notifItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notifItem) {
          notifItem.classList.remove('bg-light');
          const checkBtn = notifItem.querySelector('button');
          if (checkBtn) {
            checkBtn.remove();
          }
        }
        
        // Update the counter
        const badge = document.querySelector('#notifDropdown .icon-badge');
        if (badge) {
          let count = parseInt(badge.textContent);
          count = Math.max(0, count - 1);
          if (count === 0) {
            badge.remove();
            // Update header text
            window.location.reload();
          } else {
            badge.textContent = count > 99 ? '99+' : count;
          }
        }
      }
    } catch (e) {
      console.error('Failed to mark notification as read', e);
    }
  }
</script>
<?php /**PATH /Users/patiencemupikeni/Downloads/zmc-portalfinal-FINAL-PREVIEW-DRAFTS-REVIEWFIX-EXCL-VENDOR_1-2/resources/views/layouts/topbar.blade.php ENDPATH**/ ?>