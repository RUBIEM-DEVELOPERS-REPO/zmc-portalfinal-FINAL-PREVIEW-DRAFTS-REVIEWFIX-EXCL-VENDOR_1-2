@extends('layouts.portal')
@section('title', 'Notices & Events')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Notices & Events</h4>
            <div class="text-muted small">View published notices and upcoming events</div>
        </div>
        <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-light border btn-sm">
            <i class="ri-arrow-left-line"></i> Back to Dashboard
        </a>
    </div>

    <div class="row g-4">
        {{-- Notices Section --}}
        <div class="col-lg-6">
            <div class="zmc-card p-0 shadow-sm border-0">
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold m-0">
                        <i class="ri-notification-line me-2" style="color:#2563eb"></i> Notices
                    </h6>
                </div>

                <div class="p-3">
                    @forelse($notices as $notice)
                        <div class="card mb-3 border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ $notice->title }}</h6>
                                    <span class="badge bg-primary-subtle text-primary border-primary">
                                        {{ $notice->published_at?->format('d M Y') }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">{{ Str::limit($notice->content, 200) }}</p>
                                @if($notice->target_portal)
                                    <span class="badge bg-light text-dark border small">
                                        Target: {{ ucfirst($notice->target_portal) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="ri-notification-off-line" style="font-size: 48px;"></i>
                            <p class="mt-2">No notices published yet.</p>
                        </div>
                    @endforelse

                    @if($notices->hasPages())
                        <div class="mt-3">
                            {{ $notices->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Events Section --}}
        <div class="col-lg-6">
            <div class="zmc-card p-0 shadow-sm border-0">
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold m-0">
                        <i class="ri-calendar-event-line me-2" style="color:#ffffff"></i> Events
                    </h6>
                </div>

                <div class="p-3">
                    @forelse($events as $event)
                        <div class="card mb-3 border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ $event->title }}</h6>
                                    <span class="badge bg-success-subtle text-success border-success">
                                        {{ $event->starts_at?->format('d M Y') }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">{{ Str::limit($event->description, 200) }}</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($event->location)
                                        <span class="badge bg-light text-dark border small">
                                            <i class="ri-map-pin-line me-1"></i>{{ $event->location }}
                                        </span>
                                    @endif
                                    @if($event->starts_at && $event->ends_at)
                                        <span class="badge bg-light text-dark border small">
                                            <i class="ri-time-line me-1"></i>
                                            {{ $event->starts_at->format('H:i') }} - {{ $event->ends_at->format('H:i') }}
                                        </span>
                                    @endif
                                    @if($event->target_portal)
                                        <span class="badge bg-light text-dark border small">
                                            Target: {{ ucfirst($event->target_portal) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="ri-calendar-close-line" style="font-size: 48px;"></i>
                            <p class="mt-2">No events scheduled yet.</p>
                        </div>
                    @endforelse

                    @if($events->hasPages())
                        <div class="mt-3">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
