@extends('layouts.portal')
@section('title', 'News & Press Statements')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">News & Press Statements</h4>
            <div class="text-muted small">View published news articles and press releases</div>
        </div>
        <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-light border btn-sm">
            <i class="ri-arrow-left-line"></i> Back to Dashboard
        </a>
    </div>

    <div class="zmc-card p-0 shadow-sm border-0">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold m-0">
                <i class="ri-newspaper-line me-2" style="color:#2563eb"></i> Latest News
            </h6>
        </div>

        <div class="p-3">
            @forelse($news as $article)
                <div class="card mb-3 border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">{{ $article->title }}</h5>
                            <span class="badge bg-primary-subtle text-primary border-primary">
                                {{ $article->published_at?->format('d M Y') }}
                            </span>
                        </div>
                        
                        @if($article->excerpt)
                            <p class="text-muted mb-2">{{ $article->excerpt }}</p>
                        @endif

                        <div class="mb-3">
                            <p class="text-secondary small mb-0" style="line-height:1.6;">{{ Str::limit($article->content, 200) }}</p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2 align-items-center">
                                @if($article->category)
                                    <span class="badge bg-light text-dark border small" style="font-size:10px;">
                                        <i class="ri-price-tag-3-line me-1"></i>{{ ucfirst($article->category) }}
                                    </span>
                                @endif
                                @if($article->is_featured)
                                    <span class="badge bg-warning-subtle text-warning border-warning small" style="font-size:10px;">
                                        <i class="ri-star-line me-1"></i>Featured
                                    </span>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-outline-primary fw-bold px-3 read-more-btn" 
                                    style="border-radius:20px; font-size:11px;"
                                    data-title="{{ $article->title }}"
                                    data-date="{{ $article->published_at?->format('d M Y') }}"
                                    data-content="{{ $article->content }}"
                                    data-author="{{ $article->author ?? 'Zimbabwe Media Commission' }}"
                                    data-category="{{ ucfirst($article->category ?? 'General') }}">
                                Read More <i class="ri-arrow-right-line ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="ri-newspaper-line" style="font-size: 48px;"></i>
                    <p class="mt-2">No news articles published yet.</p>
                </div>
            @endforelse

            @if($news->hasPages())
                <div class="mt-3">
                    {{ $news->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- News Detail Modal -->
<div class="modal fade" id="newsDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
            <div class="modal-header border-0 p-4 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle p-3 rounded-circle" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-newspaper-line text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold" id="modalTitle" style="color:#0f172a;">Article Title</h5>
                        <p class="text-muted small m-0" id="modalMeta">Published on 01 Jan 2026</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-4">
                <div class="mb-3 d-flex gap-2" id="modalBadges">
                    <!-- Badges will be injected here -->
                </div>
                <div id="modalContent" style="line-height:1.8; color:#334155; font-size:15px; white-space: pre-wrap;">
                    Article content goes here...
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light border px-4 fw-bold" data-bs-dismiss="modal" style="border-radius:12px;">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const readMoreBtns = document.querySelectorAll('.read-more-btn');
    const modal = new bootstrap.Modal(document.getElementById('newsDetailModal'));
    
    const modalTitle = document.getElementById('modalTitle');
    const modalMeta = document.getElementById('modalMeta');
    const modalContent = document.getElementById('modalContent');
    const modalBadges = document.getElementById('modalBadges');

    readMoreBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const date = this.getAttribute('data-date');
            const content = this.getAttribute('data-content');
            const author = this.getAttribute('data-author');
            const category = this.getAttribute('data-category');

            modalTitle.innerText = title;
            modalMeta.innerText = `Published on ${date} • By ${author}`;
            modalContent.innerText = content;
            
            // Re-render badges
            modalBadges.innerHTML = `
                <span class="badge bg-light text-dark border small shadow-sm">
                    <i class="ri-price-tag-3-line me-1 text-primary"></i>${category}
                </span>
                <span class="badge bg-light text-dark border small shadow-sm">
                    <i class="ri-user-line me-1 text-primary"></i>${author}
                </span>
            `;

            modal.show();
        });
    });
});
</script>
@endpush
@endsection
