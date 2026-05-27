@extends('layouts.portal')

@section('title', 'Notices & Events')
@section('page_title', 'Notices & Events')

@section('content')
<div id="notices-page" style="color:#334155;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Notices & Events</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Stay updated with the latest announcements from ZMC.
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="zmc-card">
        <h6 class="fw-bold mb-3"><i class="ri-megaphone-line me-2" style="color:var(--zmc-accent)"></i>Notices</h6>
        @forelse($notices ?? collect() as $n)
          <div class="border-bottom pb-3 mb-3">
            @if($n->image_path)
              <div class="mb-2">
                <img src="{{ asset('storage/' . $n->image_path) }}" alt="{{ $n->title }}" class="rounded" style="max-width:100%;max-height:180px;object-fit:cover;">
              </div>
            @endif
            <div class="fw-bold text-dark" style="text-transform: none;">{{ $n->title }}</div>
            <div class="text-muted small">{{ optional($n->published_at)->format('d M Y') }}</div>
            <div class="mt-2" style="white-space:pre-wrap; font-size:13px;">{{ $n->body }}</div>
          </div>
        @empty
          <div class="text-muted">No notices at the moment.</div>
        @endforelse
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="zmc-card">
        <h6 class="fw-bold mb-3"><i class="ri-calendar-event-line me-2" style="color:var(--zmc-accent)"></i>Events</h6>
        @forelse($events ?? collect() as $e)
          <div class="border-bottom pb-3 mb-3">
            @if($e->image_path)
              <div class="mb-2">
                <img src="{{ asset('storage/' . $e->image_path) }}" alt="{{ $e->title }}" class="rounded" style="max-width:100%;max-height:180px;object-fit:cover;">
              </div>
            @endif
            <div class="fw-bold text-dark" style="text-transform: none;">{{ $e->title }}</div>
            <div class="text-muted small">
              @if($e->starts_at)
                {{ $e->starts_at->format('d M Y, H:i') }}
                @if($e->ends_at) – {{ $e->ends_at->format('d M Y, H:i') }} @endif
              @endif
              @if($e->location) - {{ $e->location }} @endif
            </div>
            @if($e->description)
              <div class="mt-2" style="white-space:pre-wrap; font-size:13px;">{{ $e->description }}</div>
            @endif
          </div>
        @empty
          <div class="text-muted">No upcoming events.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
