<!-- TOPBAR -->
@php
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
@endphp

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

      <!-- Global Search (Staff only) -->
      @if($isStaff)
        <form action="{{ route('staff.search') }}" method="GET" class="d-none d-md-flex align-items-center me-2">
          <div class="input-group input-group-sm" style="width: 210px; border: 1px solid rgba(255,255,255,0.2); border-radius: 999px; overflow: hidden; background: rgba(255,255,255,0.05);">
            <input type="text" name="q" class="form-control border-0 bg-transparent text-white placeholder-light" placeholder="Search Ref / Name..." style="font-size: 11px;">
            <button class="btn btn-link text-white p-2" type="submit" style="text-decoration: none;">
              <i class="ri-search-line"></i>
            </button>
          </div>
        </form>
      @else
        <button class="icon-btn" type="button" title="Search disabled for public" disabled>
          <i class="ri-search-line"></i>
        </button>
      @endif

      <!-- MESSAGES DROPDOWN -->
      <div class="dropdown">
        <button class="icon-btn dropdown-toggle hide-caret" type="button" id="messageDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Messages">
          <i class="ri-mail-line"></i>
          @if($unreadCount > 0)
            <span class="icon-badge badge-blue">{{ $unreadCount }}</span>
          @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 mt-2" aria-labelledby="messageDropdown" style="width: 300px; border-radius: 12px; overflow: hidden;">
          <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold">Messages</h6>
            @if($unreadCount > 0)
              <span class="badge bg-primary-subtle text-primary small">{{ $unreadCount }} New</span>
            @else
              <span class="badge bg-primary-subtle text-primary small">Up to date</span>
            @endif
          </div>
          <div class="message-scroll" style="max-height: 250px; overflow-y: auto;">
            @forelse($latestMsgs as $msg)
              @php
                $isUnread = $u && (int)$msg->to_user_id === (int)$u->id && empty($msg->read_at);
                $otherId = $u ? ((int)$msg->from_user_id === (int)$u->id ? $msg->to_user_id : $msg->from_user_id) : null;
                $other = $otherId ? \App\Models\User::find($otherId) : null;
                $who = $other?->name ?? 'User';
                $ref = $msg->application?->reference ?? ('APP-' . $msg->application_id);
                $when = $msg->sent_at ? $msg->sent_at->diffForHumans() : ($msg->created_at?->diffForHumans() ?? '');
              @endphp
              <a href="{{ route('messages.thread', $msg->application_id) }}" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 @if($isUnread) bg-light-subtle @endif">
                <div class="flex-shrink-0 bg-warning-subtle text-warning p-2 rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                  <i class="ri-message-2-line"></i>
                </div>
                <div class="flex-grow-1">
                  <p class="mb-0 fw-bold small">{{ $who }} <span class="text-muted" style="font-weight:800;">• {{ $ref }}</span></p>
                  <p class="mb-0 text-muted small">{{ \Illuminate\Support\Str::limit($msg->body, 60) }}</p>
                  <span class="text-muted" style="font-size: 10px;">{{ $when }}</span>
                </div>
              </a>
            @empty
              <div class="p-3 text-muted small">No messages yet.</div>
            @endforelse
          </div>
          <a href="{{ route('messages.index') }}" class="dropdown-item text-center p-2 bg-light small fw-bold text-primary">View All Messages</a>
        </div>
      </div>

      <!-- Notifications Dropdown (real data) -->
      <div class="dropdown">
        <button class="icon-btn dropdown-toggle hide-caret" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
          <i class="ri-notification-3-line"></i>
          @if($notifCount > 0)
            <span class="icon-badge badge-red">{{ $notifCount }}</span>
          @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 mt-2" aria-labelledby="notifDropdown" style="width: 340px; border-radius: 12px; overflow: hidden;">
          <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold">Notifications</h6>
            @if($notifCount > 0)
              <span class="badge bg-danger-subtle text-danger small">{{ $notifCount }} Unread</span>
            @else
              <span class="badge bg-success-subtle text-success small">All caught up</span>
            @endif
          </div>
          <div style="max-height: 280px; overflow-y: auto;">
            @forelse($latestNotifs as $n)
              @php
                $data = $n->data ?? [];
                $ref = $data['reference'] ?? null;
                $msg = $data['message'] ?? 'Notification';
                $when = $n->created_at?->diffForHumans();
                $unread = is_null($n->read_at);
              @endphp
                <div class="p-3 border-bottom @if($unread) bg-light-subtle @endif">
                <div class="d-flex justify-content-between gap-2">
                  <div class="fw-bold small">{{ $ref ? ($ref . ' • ') : '' }}{{ $msg }}</div>
                  <div class="text-muted" style="font-size: 10px; white-space: nowrap;">{{ $when }}</div>
                </div>
              </div>
            @empty
              <div class="p-3 text-muted small">No notifications yet.</div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Settings -->
      <a class="icon-btn" href="{{ route('settings') }}" title="Settings" data-bs-toggle="tooltip" data-bs-placement="bottom">
        <i class="ri-settings-3-line"></i>
      </a>

      <!-- Logout Pill -->
      <button class="logout-pill" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" type="button">
        <i class="ri-logout-box-r-line"></i>
        <span>LOGOUT</span>
      </button>
      <form id="logout-form" action="{{ $logoutRoute }}" method="POST" class="d-none">@csrf</form>

    </div>
  </div>
</header>
