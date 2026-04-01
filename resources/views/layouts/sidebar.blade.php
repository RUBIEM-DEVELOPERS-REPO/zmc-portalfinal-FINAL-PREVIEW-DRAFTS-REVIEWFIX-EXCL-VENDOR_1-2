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
    <div class="navbar-brand-circle">
      <img src="{{ asset('zmc_logo_circular.png') }}" alt="ZMC Logo">
    </div>
    <div>
      <span class="logo-text"><span class="zimbabwe">ZIMBABWE</span> <span class="media">MEDIA</span> <span class="commission">COMMISSION</span></span>
      <span class="logo-sub">{{ $subTitle }}</span>
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

      <li class="{{ request()->routeIs('accreditation.renewals') ? 'active' : '' }}">
        <a href="{{ route('accreditation.renewals') }}">
          <i class="ri-refresh-line"></i><span>Renewal (AP5)</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('accreditation.replacement') ? 'active' : '' }}">
        <a href="{{ route('accreditation.replacement') }}">
          <i class="ri-file-copy-line"></i><span>Replacement (AP5)</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.drafts') ? 'active' : '' }}">
        <a href="{{ route('accreditation.home') }}#drafts-tab" onclick="event.preventDefault(); window.location.href='{{ route('accreditation.home') }}'; setTimeout(() => { document.getElementById('drafts-tab')?.click(); }, 100);">
          <i class="ri-draft-line"></i><span>Drafts</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('portal.payments.history') ? 'active' : '' }}">
        <a href="{{ route('portal.payments.history') }}">
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

      <li class="{{ request()->routeIs('accreditation.requirements') ? 'active' : '' }}">
        <a href="{{ route('accreditation.requirements') }}">
          <i class="ri-list-check-2"></i><span>Requirements</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('accreditation.howto') ? 'active' : '' }}">
        <a href="{{ route('accreditation.howto') }}">
          <i class="ri-information-line"></i><span>How it Works</span>
        </a>
      </li>

      <li>
        <a href="#" target="_blank">
          <i class="ri-global-line"></i><span>Media Hub</span>
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

      <li class="{{ request()->routeIs('mediahouse.renewals') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.renewals') }}">
          <i class="ri-refresh-line"></i><span>Renewal (AP5)</span>
        </a>
      </li>
      <li class="{{ request()->routeIs('mediahouse.replacement') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.replacement') }}">
          <i class="ri-file-copy-line"></i><span>Replacement (AP5)</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.drafts') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.portal') }}#drafts-tab" onclick="event.preventDefault(); window.location.href='{{ route('mediahouse.portal') }}'; setTimeout(() => { document.getElementById('drafts-tab')?.click(); }, 100);">
          <i class="ri-draft-line"></i><span>Drafts</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('portal.payments.history') ? 'active' : '' }}">
        <a href="{{ route('portal.payments.history') }}">
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

      <li class="{{ request()->routeIs('mediahouse.requirements') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.requirements') }}">
          <i class="ri-list-check-2"></i><span>Requirements</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('mediahouse.howto') ? 'active' : '' }}">
        <a href="{{ route('mediahouse.howto') }}">
          <i class="ri-information-line"></i><span>How it Works</span>
        </a>
      </li>

      <li>
        <a href="#" target="_blank">
          <i class="ri-global-line"></i><span>Media Hub</span>
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
      <div style="font-weight:700;font-size:13px;color:#fff;">
        {{ Auth::user()->name ?? 'User' }}
      </div>
      <div style="font-size:11px;color:rgba(255,255,255,0.7);">Applicant</div>
    </div>
  </div>
</div>
