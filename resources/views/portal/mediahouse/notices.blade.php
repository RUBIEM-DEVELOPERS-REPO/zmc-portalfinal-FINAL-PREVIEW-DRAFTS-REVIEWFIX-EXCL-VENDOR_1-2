@extends('layouts.portal')

@section('title', 'Notices & News')
@section('page_title', 'NOTICES & NEWS')

@push('styles')
<style>
  :root {
    --zmc-green: #1e7e34;
    --zmc-green-soft: #f0fdf4;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --border-light: #f1f5f9;
  }

  .notices-container {
    padding: 2.5rem;
    font-size: 10px;
    background-color: white;
    min-height: 100vh;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
  }

  /* Elegant Hero */
  .notices-hero {
    background: #f8fafc;
    border-radius: 12px;
    padding: 2.5rem;
    margin-bottom: 3.5rem;
    border: 1px solid var(--zmc-green-soft);
    position: relative;
    overflow: hidden;
  }

  .hero-content h2 {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--zmc-green);
    margin-bottom: 0.5rem;
  }

  .hero-content p {
    font-size: 0.75rem;
    color: var(--text-muted);
    max-width: 650px;
    margin: 0;
    text-align: justify;
  }

  /* Grid Headers */
  .section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--zmc-green-soft);
  }

  .section-header i {
    font-size: 1rem;
    color: var(--zmc-green);
  }

  .section-header h3 {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    letter-spacing: 0.5px;
  }

  /* Modern Cards */
  .news-card {
    background: white;
    border: 1px solid var(--border-light);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 2rem;
    transition: all 0.25s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .news-card:hover {
    border-color: #e2e8f0;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.04);
  }

  .card-img-top {
    width: 100%;
    height: 170px;
    object-fit: cover;
    background-color: #f8fafc;
  }

  .card-inner {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .card-meta {
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .card-meta span.type-tag {
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    font-weight: 800;
    font-size: 0.5rem;
  }

  .tag-notice { background: #eff6ff; color: #1e40af; }
  .tag-news { background: #fef2f2; color: #991b1b; }
  .tag-event { background: #f0fdf4; color: #166534; }

  .card-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--zmc-green);
    margin-bottom: 0.85rem;
    line-height: 1.45;
  }

  .card-text {
    font-size: 0.72rem;
    color: #475569;
    line-height: 1.6;
    text-align: justify;
    margin-bottom: 1.5rem;
  }

  .card-action {
    margin-top: auto;
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--zmc-green);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: gap 0.2s;
  }

  .card-action:hover {
    gap: 0.75rem;
    color: #155724;
  }

  /* Support Box */
  .help-box {
    background: var(--zmc-green);
    border-radius: 12px;
    padding: 2rem;
    color: white;
    text-align: center;
    margin-top: 1.5rem;
  }

  .help-box h5 {
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
  }

  .help-box p {
    font-size: 0.75rem;
    opacity: 0.85;
    margin-bottom: 1.5rem;
    text-align: justify;
  }

  .btn-light-custom {
    background: white;
    color: var(--zmc-green);
    font-weight: 700;
    font-size: 0.75rem;
    padding: 0.6rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    display: inline-block;
  }

  /* Modal Tweaks */
  .modal-header {
    background: var(--zmc-green-soft);
    padding: 1.5rem 2rem;
    border: none;
  }

  .modal-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--zmc-green);
  }

  .modal-body {
    padding: 2.5rem;
  }

  .modal-body-p {
    font-size: 0.85rem;
    color: #334155;
    line-height: 1.8;
    text-align: justify;
  }

  @media (max-width: 991px) {
    .notices-hero { padding: 2rem; text-align: center; }
  }
</style>
@endpush

@section('content')
<div class="notices-container">
  <!-- Elegant Hero Section -->
  <div class="notices-hero">
    <div class="hero-content">
      <h2>Notices & News Hub</h2>
      <p>Stay up-to-date with official announcements, the latest media landscape updates, and commission events.</p>
    </div>
  </div>

  <div class="row g-4">
    <!-- COLUMN 1: NOTICES -->
    <div class="col-lg-4">
      <div class="section-header">
        <i class="ri-megaphone-line"></i>
        <h3>Official Notices</h3>
      </div>

      @forelse($notices ?? collect() as $n)
        <div class="news-card">
          @if($n->image_path)
            <img src="{{ Storage::url($n->image_path) }}" class="card-img-top" alt="Notice">
          @endif
          <div class="card-inner">
            <div class="card-meta">
              <span class="type-tag tag-notice">ANNOUNCEMENT</span>
              <span><i class="ri-calendar-line me-1"></i>{{ optional($n->published_at)->format('d M Y') }}</span>
            </div>
            <h4 class="card-title">{{ $n->title }}</h4>
            <div class="card-text">
              {{ Str::limit(strip_tags($n->body), 150) }}
            </div>
            <a href="javascript:void(0)" class="card-action" onclick="showContentModal('notice', {{ $n->id }})">
              VIEW DETAILS <i class="ri-arrow-right-line"></i>
            </a>
          </div>
        </div>
      @empty
        <div class="text-center py-5 bg-light rounded-3 border border-dashed">
          <p class="text-muted small">No active notices available.</p>
        </div>
      @endforelse
    </div>

    <!-- COLUMN 2: NEWS -->
    <div class="col-lg-4">
      <div class="section-header">
        <i class="ri-newspaper-line"></i>
        <h3>Latest News</h3>
      </div>

      @forelse($news ?? collect() as $nw)
        <div class="news-card">
          @if($nw->image_path)
            <img src="{{ Storage::url($nw->image_path) }}" class="card-img-top" alt="News">
          @else
            <div class="card-img-top d-flex align-items-center justify-content-center text-muted border-bottom">
              <i class="ri-image-2-line fs-1 opacity-25"></i>
            </div>
          @endif
          <div class="card-inner">
            <div class="card-meta">
              <span class="type-tag tag-news">PRESS RELEASE</span>
              <span><i class="ri-time-line me-1"></i>{{ optional($nw->published_at)->format('d M Y') }}</span>
            </div>
            <h4 class="card-title">{{ $nw->title }}</h4>
            <div class="card-text">
              {{ Str::limit(strip_tags($nw->body), 140) }}
            </div>
            <a href="javascript:void(0)" class="card-action" onclick="showContentModal('news', {{ $nw->id }})">
              READ FULL STORY <i class="ri-arrow-right-line"></i>
            </a>
          </div>
        </div>
      @empty
        <div class="text-center py-5 bg-light rounded-3 border border-dashed">
          <p class="text-muted small">No news articles to show.</p>
        </div>
      @endforelse
    </div>

    <!-- COLUMN 3: EVENTS -->
    <div class="col-lg-4">
      <div class="section-header">
        <i class="ri-calendar-check-line"></i>
        <h3>Upcoming Events</h3>
      </div>

      @forelse($events ?? collect() as $e)
        <div class="news-card">
          @if($e->image_path)
            <img src="{{ Storage::url($e->image_path) }}" class="card-img-top" alt="Event">
          @endif
          <div class="card-inner">
            <div class="card-meta">
              <span class="type-tag tag-event">UPCOMING</span>
              <span><i class="ri-map-pin-2-line me-1"></i>{{ Str::limit($e->location, 20) }}</span>
            </div>
            <h4 class="card-title">{{ $e->title }}</h4>
            <div class="card-text">
              {{ Str::limit(strip_tags($e->description), 130) }}
            </div>
            <a href="javascript:void(0)" class="card-action" onclick="showContentModal('event', {{ $e->id }})">
              EVENT DETAILS <i class="ri-arrow-right-line"></i>
            </a>
          </div>
        </div>
      @empty
        <div class="text-center py-5 bg-light rounded-3 border border-dashed">
          <p class="text-muted small">No scheduled events.</p>
        </div>
      @endforelse

      <div class="help-box">
        <h5>Need Assistance?</h5>
        <p>Having trouble finding information or registering for an event? Use our support channel.</p>
        <a href="{{ route('accreditation.communication') }}" class="btn-light-custom">Contact Support</a>
      </div>
    </div>
  </div>
</div>

<!-- Global View Modal -->
<div class="modal fade" id="contentViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content overflow-hidden" style="border-radius: 12px;">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Content injected via JS -->
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
    let tagClass = 'tag-notice';

    if (type === 'notice') {
      item = appData.notices.find(n => n.id === id);
      label = 'OFFICIAL NOTICE';
      tagClass = 'tag-notice';
    } else if (type === 'news') {
      item = appData.news.find(n => n.id === id);
      label = 'NEWS ARTICLE';
      tagClass = 'tag-news';
    } else if (type === 'event') {
      item = appData.events.find(n => n.id === id);
      label = 'MEDIA EVENT';
      tagClass = 'tag-event';
    }

    if (!item) return;

    title.innerText = item.title;
    
    let html = `
      <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
        <span class="type-tag ${tagClass}" style="padding: 0.3rem 0.8rem; border-radius: 4px; font-weight: 800; font-size: 0.75rem;">${label}</span>
        <span class="text-muted small fw-bold"><i class="ri-calendar-line me-1"></i>${moment(item.published_at || item.starts_at).format('DD MMMM YYYY')}</span>
      </div>
    `;

    const img = item.image_path || item.featured_image;
    if (img) {
      html += `<img src="/storage/${img}" class="img-fluid rounded-3 mb-4 w-100 shadow-sm" style="max-height: 400px; object-fit: cover;">`;
    }

    if (type === 'event') {
      html += `
        <div class="row g-3 mb-4">
          <div class="col-6">
            <div class="p-3 bg-light rounded-3 border">
              <small class="text-muted d-block mb-1 fw-bold">TIME</small>
              <strong><i class="ri-time-line text-success me-1"></i>${moment(item.starts_at).format('HH:mm')}</strong>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-light rounded-3 border">
              <small class="text-muted d-block mb-1 fw-bold">LOCATION</small>
              <strong><i class="ri-map-pin-fill text-danger me-1"></i>${item.location || 'N/A'}</strong>
            </div>
          </div>
        </div>
      `;
    }

    html += `<div class="modal-body-p">${item.body || item.description || ''}</div>`;

    if (item.attachment_path) {
      html += `
        <div class="mt-5 p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-white p-2 rounded shadow-sm"><i class="ri-file-pdf-line text-danger fs-3"></i></div>
            <div>
              <div class="fw-bold small text-dark">${item.attachment_original_name || 'Download PDF'}</div>
              <div class="text-muted small" style="font-size: 11px;">Official Resource</div>
            </div>
          </div>
          <a href="/storage/${item.attachment_path}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-4 fw-bold">DOWNLOAD</a>
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
