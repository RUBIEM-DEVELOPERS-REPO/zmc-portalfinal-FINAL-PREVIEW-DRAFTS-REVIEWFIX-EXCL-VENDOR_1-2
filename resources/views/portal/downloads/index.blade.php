@extends('layouts.portal')

@section('title', 'Downloads')
@section('page_title', 'DOWNLOADS')

@push('styles')
<style>
  .downloads-hero {
    background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
    border-radius: 16px;
    padding: 2.5rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(30, 126, 52, 0.2);
    position: relative;
    overflow: hidden;
  }
  
  .downloads-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
  }
  
  @keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
  }
  
  .download-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
  }
  
  .download-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(180deg, #1e7e34, #28a745);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .download-card:hover {
    transform: translateX(4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    border-color: #1e7e34;
  }
  
  .download-card:hover::before {
    opacity: 1;
  }
  
  .file-icon-box {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  .file-icon-box.pdf {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
  }
  
  .file-icon-box.image {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
  }
  
  .file-icon-box.doc {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
  }
  
  .file-icon-box.default {
    background: linear-gradient(135deg, #6c757d, #545b62);
    color: white;
  }
  
  .file-content {
    flex: 1;
    min-width: 0;
  }
  
  .file-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  .file-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: 0.875rem;
    color: #64748b;
  }
  
  .file-meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .download-btn {
    flex-shrink: 0;
    background: linear-gradient(135deg, #1e7e34, #28a745);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(30, 126, 52, 0.3);
    text-decoration: none;
  }
  
  .download-btn:hover {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(30, 126, 52, 0.4);
    color: white;
  }
  
  .download-btn i {
    font-size: 1.25rem;
  }
  
  .app-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #495057;
    border: 1px solid #dee2e6;
  }
  
  .doc-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    color: #1976d2;
  }
  
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    text-align: center;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1e7e34;
    margin-bottom: 0.25rem;
  }
  
  .stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
  }
  
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    border: 2px dashed #e2e8f0;
  }
  
  .empty-state i {
    font-size: 5rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
  }
  
  .empty-state h5 {
    color: #475569;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  
  .empty-state p {
    color: #94a3b8;
    margin-bottom: 0;
  }
  
  .fade-in {
    animation: fadeIn 0.5s ease-in;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }
  
  .section-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #1e7e34, #28a745);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
  }
  
  .section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
  }
  
  @media (max-width: 768px) {
    .download-card {
      flex-direction: column;
      align-items: flex-start;
    }
    
    .download-btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush

@section('content')
<<<<<<< HEAD
<div id="downloads-page" class="zmc-dashboard-wrapper">
  <div class="downloads-hero fade-in">
    <div class="d-flex align-items-center gap-3 mb-2" style="position: relative; z-index: 1;">
      <i class="ri-folder-download-line" style="font-size: 2.5rem;"></i>
      <div>
        <h2 class="m-0 fw-bold" style="font-size: 1.75rem;">Downloads Center</h2>
        <p class="m-0 mt-1" style="opacity: 0.9; font-size: 0.95rem;">
          Access all your application documents and generated files
        </p>
      </div>
    </div>
  </div>

  @if($docs->count() > 0)
    <div class="stats-grid fade-in">
      <div class="stat-card">
        <div class="stat-number">{{ $docs->count() }}</div>
        <div class="stat-label">Total Files</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ $docs->unique('application_id')->count() }}</div>
        <div class="stat-label">Applications</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">{{ number_format($docs->sum('size') / 1024 / 1024, 2) }} MB</div>
        <div class="stat-label">Total Size</div>
      </div>
    </div>

    <div class="section-header">
      <div class="section-icon">
        <i class="ri-file-list-3-line"></i>
      </div>
      <h3 class="section-title">Your Documents</h3>
    </div>

    @foreach($docs as $index => $doc)
      @php
        $ext = strtolower(pathinfo($doc->original_name ?: $doc->file_path, PATHINFO_EXTENSION));
        $iconClass = 'default';
        $icon = 'ri-file-line';
        
        if ($ext === 'pdf') {
          $iconClass = 'pdf';
          $icon = 'ri-file-pdf-line';
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
          $iconClass = 'image';
          $icon = 'ri-image-line';
        } elseif (in_array($ext, ['doc', 'docx'])) {
          $iconClass = 'doc';
          $icon = 'ri-file-word-line';
        }
        
        $fileSize = $doc->size ? number_format($doc->size / 1024, 2) . ' KB' : 'Unknown';
      @endphp
      
      <div class="download-card fade-in" style="animation-delay: {{ $index * 0.05 }}s;">
        <div class="file-icon-box {{ $iconClass }}">
          <i class="{{ $icon }}"></i>
        </div>
        
        <div class="file-content">
          <div class="file-name" title="{{ $doc->original_name ?: basename($doc->file_path) }}">
            {{ $doc->original_name ?: basename($doc->file_path) }}
          </div>
          
          <div class="file-meta">
            <span class="file-meta-item">
              <i class="ri-folder-line"></i>
              <span class="app-badge">{{ $doc->application->reference }}</span>
            </span>
            
            <span class="file-meta-item">
              <i class="ri-price-tag-3-line"></i>
              <span class="doc-type-badge">{{ str_replace('_', ' ', $doc->doc_type) }}</span>
            </span>
            
            <span class="file-meta-item">
              <i class="ri-file-info-line"></i>
              {{ $fileSize }}
            </span>
            
            <span class="file-meta-item">
              <i class="ri-calendar-line"></i>
              {{ $doc->created_at?->format('d M Y') }}
            </span>
          </div>
        </div>
        
        <a class="download-btn" href="{{ route(str_starts_with(Route::currentRouteName(),'mediahouse.') ? 'mediahouse.downloads.file' : 'accreditation.downloads.file', $doc) }}">
          <i class="ri-download-cloud-line"></i>
          <span>Download</span>
        </a>
      </div>
    @endforeach
  @else
    <div class="empty-state fade-in">
      <i class="ri-folder-open-line"></i>
      <h5>No Documents Available</h5>
      <p>Your uploaded documents and generated files will appear here once you submit applications.</p>
    </div>
=======
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Downloads</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Download your uploaded documents and any generated files associated with your applications.
      </div>
    </div>
    <a class="btn btn-secondary" href="{{ url()->previous() }}"><i class="ri-arrow-left-line me-1"></i>Back</a>
  </div>

  @if($docs->count() === 0)
    <div class="zmc-card text-center py-5">
      <i class="ri-folder-2-line" style="font-size:48px; opacity:0.3;"></i>
      <h5 class="mt-3 mb-2">No downloadable files yet</h5>
      <p class="text-muted mb-0">Your uploaded documents and generated files will appear here.</p>
    </div>
  @else
    <div class="zmc-card">
      <h6 class="fw-bold mb-3"><i class="ri-download-2-line me-2" style="color:var(--zmc-accent)"></i>Your Files</h6>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Application</th>
              <th>Type</th>
              <th>File</th>
              <th>Uploaded</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($docs as $doc)
              <tr>
                <td><span class="badge bg-light text-dark">{{ $doc->application->reference }}</span></td>
                <td>{{ $doc->document_type }}</td>
                <td class="text-truncate" style="max-width:280px;">{{ $doc->original_name ?: basename($doc->file_path) }}</td>
                <td class="text-muted">{{ $doc->created_at?->format('d M Y H:i') }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-primary" href="{{ route(str_starts_with(Route::currentRouteName(),'mediahouse.') ? 'mediahouse.downloads.file' : 'accreditation.downloads.file', $doc) }}">
                    <i class="ri-download-2-line me-1"></i>Download
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
  @endif
</div>

@push('scripts')
<script>
  // Add download tracking or analytics if needed
  document.querySelectorAll('.download-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      // Optional: Track download events
      const fileName = this.closest('.download-card').querySelector('.file-name').textContent.trim();
      console.log('Downloading:', fileName);
    });
  });
</script>
@endpush
@endsection
