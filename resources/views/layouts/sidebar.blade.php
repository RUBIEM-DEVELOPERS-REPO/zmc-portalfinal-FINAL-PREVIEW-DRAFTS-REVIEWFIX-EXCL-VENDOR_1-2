<!-- APPLICANT SIDEBAR (Media Practitioner + Media House) -->
@php
  $isAccred = request()->routeIs('accreditation.*') || str_contains(request()->path(), 'portal/accreditation');
  $isMedia  = request()->routeIs('mediahouse.*') || str_contains(request()->path(), 'media-house/registration');

  $subTitle = $isAccred
    ? 'Media Practitioner Accreditation'
    : 'Mass Media Service Registration';
@endphp

<div class="vertical-menu">
  <div class="navbar-brand-box">
    <a href="{{ route('home') }}">
      <img src="{{ asset('zmc_logo.png') }}" alt="ZMC Logo">
    </a>
    <div class="logo-portal-name">
      {{ $isAccred ? 'Media Practitioner Accreditation Portal' : 'Mass Media Service Registration Portal' }}
    </div>
  </div>

  <ul class="sidebar-menu">

    @if($isAccred)
      <li class="{{ request()->routeIs('accreditation.home') ? 'active' : '' }}">
        <a href="{{ route('accreditation.home') }}">
          <i class="ri-home-4-line"></i><span>Dashboard</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.new') ? 'active' : '' }}">
        <a href="{{ route('accreditation.new') }}">
          <i class="ri-file-add-line"></i><span>New Accreditation (AP3)</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.renewals*') ? 'active' : '' }}">
        <a href="{{ route('accreditation.renewals.index') }}">
          <i class="ri-refresh-line"></i><span>Renewal / Replacement (AP5)</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.payments') ? 'active' : '' }}">
        <a href="{{ route('accreditation.payments') }}">
          <i class="ri-bank-card-line"></i><span>Payment History</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.notices') ? 'active' : '' }}">
        <a href="{{ route('accreditation.notices') }}">
          <i class="ri-megaphone-line"></i><span>Notices & Events</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.downloads*') ? 'active' : '' }}">
        <a href="{{ route('accreditation.downloads') }}">
          <i class="ri-download-2-line"></i><span>Downloads</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.howto') ? 'active' : '' }}">
        <a href="{{ route('accreditation.howto') }}">
          <i class="ri-information-line"></i><span>How it Works</span>
        </a>
      </li>

      <li>
        <a href="https://mediahub.zmc.co.zw" target="_blank">
          <i class="ri-links-line"></i><span>Media Hub</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.profile') ? 'active' : '' }}">
        <a href="{{ route('accreditation.profile') }}">
          <i class="ri-user-line"></i><span>Profile</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.settings') ? 'active' : '' }}">
        <a href="{{ route('accreditation.settings') }}">
          <i class="ri-settings-3-line"></i><span>Settings</span>
        </a>
      </li>

    @elseif($isMedia)

      <li class="{{ request()->routeIs('mediahouse.portal') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.portal') }}">
          <i class="ri-home-4-line"></i><span>Dashboard</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.new') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.new') }}">
          <i class="ri-file-add-line"></i><span>New Registration (AP1)</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.renewals*') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.renewals.index') }}">
          <i class="ri-refresh-line"></i><span>Renewal / Replacement (AP5)</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.payments') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.payments') }}">
          <i class="ri-bank-card-line"></i><span>Payment History</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.notices') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.notices') }}">
          <i class="ri-megaphone-line"></i><span>Notices & Events</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.downloads*') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.downloads') }}">
          <i class="ri-download-2-line"></i><span>Downloads</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.howto') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.howto') }}">
          <i class="ri-information-line"></i><span>How it Works</span>
        </a>
      </li>

      <li>
        <a href="https://mediahub.zmc.co.zw" target="_blank">
          <i class="ri-links-line"></i><span>Media Hub</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.profile') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.profile') }}">
          <i class="ri-building-2-line"></i><span>Organization Profile</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.settings') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.settings') }}">
          <i class="ri-settings-3-line"></i><span>Settings</span>
        </a>
      </li>
    @endif

    {{-- Communication removed: messaging happens via topbar Messages --}}
  </ul>

  <div class="sidebar-user">
    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=facc15&color=000" alt="user">
    <div style="line-height:1.1;">
      <div style="font-weight:700;font-size:11px;color:#fff;">
        {{ Auth::user()->name ?? 'User' }}
      </div>
      <div style="font-size:10px;color:rgba(255,255,255,0.7);">Applicant Portal</div>
    </div>
  </div>
</div>
