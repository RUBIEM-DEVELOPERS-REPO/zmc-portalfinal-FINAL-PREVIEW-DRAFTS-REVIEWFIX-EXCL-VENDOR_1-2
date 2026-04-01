@extends('layouts.portal')

@section('title', 'Notices & News')
@section('page_title', 'NOTICES & NEWS')

@push('styles')
<style>
  :root {
    --zmc-primary: #166534;
    --zmc-primary-dark: #14532d;
    --zmc-primary-soft: #f0fdf4;
    --zmc-accent: #22c55e;
    --bg-page: #f8fafc;
    --bg-card: rgba(255,255,255,0.9);
    --text-main: #0f172a;
    --text-soft: #64748b;
    --border-soft: #e2e8f0;
    --shadow-sm: 0 8px 24px rgba(15, 23, 42, 0.06);
    --shadow-md: 0 18px 40px rgba(15, 23, 42, 0.08);
    --radius-xl: 24px;
    --radius-lg: 18px;
    --radius-md: 14px;
  }

  .notices-page {
    background:
      radial-gradient(circle at top right, rgba(34, 197, 94, 0.08), transparent 20%),
      linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    padding: 2rem 0 3rem;
  }

  .notices-shell {
    padding: 0 1.25rem;
  }

  .hero-banner {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-xl);
    padding: 2rem;
    background: linear-gradient(135deg, #14532d 0%, #166534 45%, #15803d 100%);
    color: #fff;
    box-shadow: var(--shadow-md);
    margin-bottom: 1.5rem;
  }

  .hero-banner::before,
  .hero-banner::after {
    content: "";
    position: absolute;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    z-index: 0;
  }

  .hero-banner::before {
    width: 260px;
    height: 260px;
    right: -80px;
    top: -80px;
  }

  .hero-banner::after {
    width: 180px;
    height: 180px;
    right: 120px;
    bottom: -90px;
  }

  .hero-content {
    position: relative;
    z-index: 1;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.15);
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    margin-bottom: 1rem;
    backdrop-filter: blur(8px);
  }

  .hero-title {
    font-size: clamp(1.8rem, 2vw, 2.6rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 0.75rem;
  }

  .hero-subtitle {
    max-width: 720px;
    font-size: 0.98rem;
    line-height: 1.7;
    color: rgba(255,255,255,0.88);
    margin: 0;
  }

  .stats-row {
    margin-bottom: 1.75rem;
  }

  .stat-card {
    background: rgba(255,255,255,0.75);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.7);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 1.1rem 1.2rem;
    height: 100%;
  }

  .stat-card-inner {
    display: flex;
    align-items: center;
    gap: 0.9rem;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .icon-notice { background: #ecfeff; color: #0f766e; }
  .icon-news   { background: #eff6ff; color: #1d4ed8; }
  .icon-event  { background: #f0fdf4; color: #15803d; }

  .stat-label {
    font-size: 0.74rem;
    color: var(--text-soft);
    margin-bottom: 0.2rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .stat-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
  }

  .content-section {
    margin-top: 0.75rem;
  }

  .section-panel {
    background: rgba(255,255,255,0.82);
    border: 1px solid rgba(226,232,240,0.9);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 1.25rem;
    height: 100%;
  }

  .section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-soft);
  }

  .section-title-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .section-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--zmc-primary-soft);
    color: var(--zmc-primary);
    font-size: 1.15rem;
  }

  .section-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
  }

  .section-subtitle {
    margin: 0.2rem 0 0;
    font-size: 0.75rem;
    color: var(--text-soft);
  }

  .section-count {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--zmc-primary);
    background: var(--zmc-primary-soft);
    padding: 0.45rem 0.75rem;
    border-radius: 999px;
    white-space: nowrap;
  }

  .modern-card {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 1rem;
    transition: 0.25s ease;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
  }

  .modern-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
    border-color: #dbeafe;
  }

  .modern-card-media {
    position: relative;
    height: 190px;
    overflow: hidden;
    background: linear-gradient(135deg, #e2e8f0, #f8fafc);
  }

  .modern-card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
  }

  .modern-card:hover .modern-card-media img {
    transform: scale(1.03);
  }

  .media-overlay {
    position: absolute;
    inset: auto 0 0 0;
    padding: 1rem 1rem 0.9rem;
    background: linear-gradient(to top, rgba(15, 23, 42, 0.72), transparent);
    color: white;
  }

  .type-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.38rem 0.7rem;
    border-radius: 999px;
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .pill-notice { background: rgba(14,165,233,0.16); color: #0ea5e9; }
  .pill-news   { background: rgba(239,68,68,0.12); color: #dc2626; }
  .pill-event  { background: rgba(34,197,94,0.12); color: #16a34a; }

  .type-pill.is-overlay {
    background: rgba(255,255,255,0.16);
    color: white;
    border: 1px solid rgba(255,255,255,0.14);
    backdrop-filter: blur(6px);
  }

  .modern-card-body {
    padding: 1.05rem 1rem 1rem;
  }

  .meta-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.73rem;
    color: var(--text-soft);
    margin-bottom: 0.85rem;
    font-weight: 600;
  }

  .meta-row span {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  .modern-card-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.4;
    margin-bottom: 0.65rem;
  }

  .modern-card-text {
    font-size: 0.84rem;
    color: #475569;
    line-height: 1.7;
    margin-bottom: 1rem;
  }

  .modern-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
  }

  .action-link {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    color: var(--zmc-primary);
    font-weight: 800;
    font-size: 0.76rem;
    text-decoration: none;
    transition: 0.2s ease;
  }

  .action-link:hover {
    color: var(--zmc-primary-dark);
    gap: 0.65rem;
  }

  .empty-state {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px dashed #cbd5e1;
    border-radius: 18px;
    padding: 2.2rem 1rem;
    text-align: center;
  }

  .empty-state-icon {
    width: 54px;
    height: 54px;
    margin: 0 auto 0.9rem;
    border-radius: 16px;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
  }

  .empty-state h6 {
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.35rem;
  }

  .empty-state p {
    color: var(--text-soft);
    font-size: 0.82rem;
    margin: 0;
  }

  .support-card {
    margin-top: 1.2rem;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px;
    padding: 1.35rem;
    color: white;
    position: relative;
    overflow: hidden;
  }

  .support-card::after {
    content: "";
    position: absolute;
    width: 140px;
    height: 140px;
    right: -40px;
    bottom: -40px;
    border-radius: 999px;
    background: rgba(34,197,94,0.14);
  }

  .support-card-content {
    position: relative;
    z-index: 1;
  }

  .support-card h5 {
    font-size: 1rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
  }

  .support-card p {
    font-size: 0.84rem;
    line-height: 1.7;
    color: rgba(255,255,255,0.8);
    margin-bottom: 1rem;
  }

  .btn-support {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    background: white;
    color: var(--zmc-primary);
    text-decoration: none;
    font-weight: 800;
    font-size: 0.8rem;
    padding: 0.78rem 1.15rem;
    border-radius: 999px;
    transition: 0.2s ease;
  }

  .btn-support:hover {
    background: #f8fafc;
    color: var(--zmc-primary-dark);
    transform: translateY(-1px);
  }

  .modal-content {
    border: none;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 30px 70px rgba(15, 23, 42, 0.18);
  }

  .modal-header {
    border: 0;
    padding: 1.25rem 1.4rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
  }

  .modal-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text-main);
  }

  .modal-body {
    padding: 1.4rem;
  }

  .modal-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: center;
    margin-bottom: 1rem;
  }

  .modal-cover {
    width: 100%;
    max-height: 380px;
    object-fit: cover;
    border-radius: 18px;
    margin-bottom: 1rem;
  }

  .detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0,1fr));
    gap: 0.9rem;
    margin-bottom: 1rem;
  }

  .detail-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1rem;
  }

  .detail-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--text-soft);
    letter-spacing: 0.04em;
    margin-bottom: 0.35rem;
  }

  .detail-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-main);
  }

  .modal-richtext {
    font-size: 0.92rem;
    line-height: 1.85;
    color: #334155;
  }

  .attachment-box {
    margin-top: 1.2rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 1rem;
  }

  .attachment-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .attachment-meta {
    display: flex;
    align-items: center;
    gap: 0.9rem;
  }

  .attachment-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #dc2626;
    font-size: 1.4rem;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
  }

  .attachment-title {
    font-weight: 800;
    font-size: 0.85rem;
    color: var(--text-main);
  }

  .attachment-subtitle {
    color: var(--text-soft);
    font-size: 0.76rem;
  }

  .btn-download {
    border-radius: 999px;
    font-weight: 800;
    padding: 0.65rem 1rem;
  }

  @media (max-width: 991px) {
    .hero-banner {
      padding: 1.5rem;
    }

    .section-panel {
      margin-bottom: 1rem;
    }
  }

  @media (max-width: 767px) {
    .notices-shell {
      padding: 0 0.85rem;
    }

    .hero-title {
      font-size: 1.5rem;
    }

    .detail-grid {
      grid-template-columns: 1fr;
    }

    .modern-card-media {
      height: 170px;
    }
  }
</style>
@endpush

@section('content')
<div class="notices-page">
  <div class="container-fluid notices-shell">

    {{-- HERO --}}
    <div class="hero-banner">
      <div class="hero-content">
        <div class="hero-badge">
          <i class="ri-radar-line"></i>
          Media Updates Portal
        </div>
        <h2 class="hero-title">Stay informed with official notices, latest news and upcoming events</h2>
        <p class="hero-subtitle">
          Access timely announcements, press updates and important commission events from one beautifully organised hub.
        </p>
      </div>
    </div>

    {{-- QUICK STATS --}}
    <div class="row g-3 stats-row">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-icon icon-notice">
              <i class="ri-megaphone-line"></i>
            </div>
            <div>
              <div class="stat-label">Official Notices</div>
              <div class="stat-value">{{ ($notices ?? collect())->count() }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-icon icon-news">
              <i class="ri-newspaper-line"></i>
            </div>
            <div>
              <div class="stat-label">Latest News</div>
              <div class="stat-value">{{ ($news ?? collect())->count() }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-card-inner">
            <div class="stat-icon icon-event">
              <i class="ri-calendar-event-line"></i>
            </div>
            <div>
              <div class="stat-label">Upcoming Events</div>
              <div class="stat-value">{{ ($events ?? collect())->count() }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- CONTENT --}}
    <div class="row g-4 content-section">

      {{-- NOTICES --}}
      <div class="col-xl-4">
        <div class="section-panel">
          <div class="section-header">
            <div class="section-title-wrap">
              <div class="section-icon"><i class="ri-megaphone-line"></i></div>
              <div>
                <h3 class="section-title">Official Notices</h3>
                <p class="section-subtitle">Commission announcements and alerts</p>
              </div>
            </div>
            <div class="section-count">{{ ($notices ?? collect())->count() }} items</div>
          </div>

          @forelse($notices ?? collect() as $n)
            <div class="modern-card">
              @if($n->image_path)
                <div class="modern-card-media">
                  <img src="{{ Storage::url($n->image_path) }}" alt="{{ $n->title }}">
                  <div class="media-overlay">
                    <span class="type-pill is-overlay">Announcement</span>
                  </div>
                </div>
              @endif

              <div class="modern-card-body">
                @if(!$n->image_path)
                  <div class="mb-2">
                    <span class="type-pill pill-notice">Announcement</span>
                  </div>
                @endif

                <div class="meta-row">
                  <span><i class="ri-calendar-line"></i>{{ optional($n->published_at)->format('d M Y') ?: 'No date' }}</span>
                </div>

                <h4 class="modern-card-title">{{ $n->title }}</h4>

                <div class="modern-card-text">
                  {{ Str::limit(strip_tags($n->body), 160) }}
                </div>

                <div class="modern-card-footer">
                  <a href="javascript:void(0)" class="action-link" onclick="showContentModal('notice', {{ $n->id }})">
                    View details <i class="ri-arrow-right-line"></i>
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="ri-inbox-archive-line"></i>
              </div>
              <h6>No active notices</h6>
              <p>Official notices will appear here once published.</p>
            </div>
          @endforelse
        </div>
      </div>

      {{-- NEWS --}}
      <div class="col-xl-4">
        <div class="section-panel">
          <div class="section-header">
            <div class="section-title-wrap">
              <div class="section-icon"><i class="ri-newspaper-line"></i></div>
              <div>
                <h3 class="section-title">Latest News</h3>
                <p class="section-subtitle">Press releases and recent stories</p>
              </div>
            </div>
            <div class="section-count">{{ ($news ?? collect())->count() }} items</div>
          </div>

          @forelse($news ?? collect() as $nw)
            <div class="modern-card">
              <div class="modern-card-media">
                @if($nw->image_path)
                  <img src="{{ Storage::url($nw->image_path) }}" alt="{{ $nw->title }}">
                @else
                  <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                    <i class="ri-image-2-line" style="font-size: 3rem; opacity: .25;"></i>
                  </div>
                @endif

                <div class="media-overlay">
                  <span class="type-pill is-overlay">Press Release</span>
                </div>
              </div>

              <div class="modern-card-body">
                <div class="meta-row">
                  <span><i class="ri-time-line"></i>{{ optional($nw->published_at)->format('d M Y') ?: 'No date' }}</span>
                </div>

                <h4 class="modern-card-title">{{ $nw->title }}</h4>

                <div class="modern-card-text">
                  {{ Str::limit(strip_tags($nw->body), 150) }}
                </div>

                <div class="modern-card-footer">
                  <a href="javascript:void(0)" class="action-link" onclick="showContentModal('news', {{ $nw->id }})">
                    Read full story <i class="ri-arrow-right-line"></i>
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="ri-article-line"></i>
              </div>
              <h6>No news available</h6>
              <p>Latest updates and stories will be displayed here.</p>
            </div>
          @endforelse
        </div>
      </div>

      {{-- EVENTS --}}
      <div class="col-xl-4">
        <div class="section-panel">
          <div class="section-header">
            <div class="section-title-wrap">
              <div class="section-icon"><i class="ri-calendar-check-line"></i></div>
              <div>
                <h3 class="section-title">Upcoming Events</h3>
                <p class="section-subtitle">Important dates and engagements</p>
              </div>
            </div>
            <div class="section-count">{{ ($events ?? collect())->count() }} items</div>
          </div>

          @forelse($events ?? collect() as $e)
            <div class="modern-card">
              @if($e->image_path)
                <div class="modern-card-media">
                  <img src="{{ Storage::url($e->image_path) }}" alt="{{ $e->title }}">
                  <div class="media-overlay">
                    <span class="type-pill is-overlay">Upcoming</span>
                  </div>
                </div>
              @endif

              <div class="modern-card-body">
                @if(!$e->image_path)
                  <div class="mb-2">
                    <span class="type-pill pill-event">Upcoming</span>
                  </div>
                @endif

                <div class="meta-row">
                  <span><i class="ri-map-pin-2-line"></i>{{ Str::limit($e->location, 24) ?: 'No location' }}</span>
                  @if($e->starts_at)
                    <span><i class="ri-calendar-line"></i>{{ \Carbon\Carbon::parse($e->starts_at)->format('d M Y') }}</span>
                  @endif
                </div>

                <h4 class="modern-card-title">{{ $e->title }}</h4>

                <div class="modern-card-text">
                  {{ Str::limit(strip_tags($e->description), 145) }}
                </div>

                <div class="modern-card-footer">
                  <a href="javascript:void(0)" class="action-link" onclick="showContentModal('event', {{ $e->id }})">
                    Event details <i class="ri-arrow-right-line"></i>
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="ri-calendar-close-line"></i>
              </div>
              <h6>No scheduled events</h6>
              <p>Upcoming activities will appear here once available.</p>
            </div>
          @endforelse

          <div class="support-card">
            <div class="support-card-content">
              <h5>Need assistance?</h5>
              <p>
                Having trouble finding information or registering for an event? Reach out through our support channel for help.
              </p>
              <a href="{{ route('accreditation.communication') }}" class="btn-support">
                <i class="ri-customer-service-2-line"></i>
                Contact Support
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Global View Modal -->
<div class="modal fade" id="contentViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const appData = {
    notices: @json($notices),
    news: @json($news),
    events: @json($events)
  };

  function showContentModal(type, id) {
    const modal = new bootstrap.Modal(document.getElementById('contentViewModal'));
    const title = document.getElementById('modalTitle');
    const body = document.getElementById('modalBody');

    let item = null;
    let label = '';
    let pillClass = 'pill-notice';

    if (type === 'notice') {
      item = appData.notices.find(n => n.id === id);
      label = 'Official Notice';
      pillClass = 'pill-notice';
    } else if (type === 'news') {
      item = appData.news.find(n => n.id === id);
      label = 'News Article';
      pillClass = 'pill-news';
    } else if (type === 'event') {
      item = appData.events.find(n => n.id === id);
      label = 'Media Event';
      pillClass = 'pill-event';
    }

    if (!item) return;

    title.innerText = item.title;

    const dateValue = item.published_at || item.starts_at;
    const imageValue = item.image_path || item.featured_image;
    const formattedDate = dateValue ? moment(dateValue).format('DD MMMM YYYY') : 'N/A';
    const formattedTime = item.starts_at ? moment(item.starts_at).format('HH:mm') : 'N/A';

    let html = `
      <div class="modal-meta">
        <span class="type-pill ${pillClass}">${label}</span>
        <span class="text-muted small fw-semibold">
          <i class="ri-calendar-line me-1"></i>${formattedDate}
        </span>
      </div>
    `;

    if (imageValue) {
      html += `<img src="/storage/${imageValue}" class="modal-cover" alt="${item.title}">`;
    }

    if (type === 'event') {
      html += `
        <div class="detail-grid">
          <div class="detail-box">
            <div class="detail-label">TIME</div>
            <div class="detail-value"><i class="ri-time-line me-1 text-success"></i>${formattedTime}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">LOCATION</div>
            <div class="detail-value"><i class="ri-map-pin-line me-1 text-danger"></i>${item.location || 'N/A'}</div>
          </div>
        </div>
      `;
    }

    html += `<div class="modal-richtext">${item.body || item.description || ''}</div>`;

    if (item.attachment_path) {
      html += `
        <div class="attachment-box">
          <div class="attachment-inner">
            <div class="attachment-meta">
              <div class="attachment-icon">
                <i class="ri-file-pdf-line"></i>
              </div>
              <div>
                <div class="attachment-title">${item.attachment_original_name || 'Download attachment'}</div>
                <div class="attachment-subtitle">Official resource document</div>
              </div>
            </div>
            <a href="/storage/${item.attachment_path}" target="_blank" class="btn btn-outline-success btn-download">
              Download
            </a>
          </div>
        </div>
      `;
    }

    body.innerHTML = html;
    modal.show();
  }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
@endpush
@endsection