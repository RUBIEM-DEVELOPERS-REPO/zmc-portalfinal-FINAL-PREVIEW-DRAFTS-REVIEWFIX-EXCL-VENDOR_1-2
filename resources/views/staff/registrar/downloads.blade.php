@extends('layouts.portal')
@section('title', 'Downloads - Registrar')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Downloads</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Accreditation and Registration related documents and reports.
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-white border shadow-sm btn-sm px-3" onclick="refreshDownloads()">
        <i class="ri-refresh-line me-1"></i>Refresh
      </button>
    </div>
  </div>

  {{-- Downloads List --}}
  <div class="zmc-card">
    <div class="card-header bg-light">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="fw-bold m-0 mb-0">
          <i class="ri-download-2-line me-2"></i>
          Available Downloads
        </h6>
        <div class="d-flex gap-2">
          <select class="form-select form-select-sm" onchange="filterByCategory(this.value)">
            <option value="">All Categories</option>
            <option value="accreditation">Accreditation</option>
            <option value="registration">Registration</option>
          </select>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="bg-light">
            <tr>
              <th>Document Name</th>
              <th>Category</th>
              <th>Type</th>
              <th>Size</th>
              <th>Uploaded By</th>
              <th>Upload Date</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($downloads as $download)
              <tr>
                <td class="fw-semibold">{{ $download->name }}</td>
                <td>
                  <span class="badge bg-{{ $download->category === 'accreditation' ? 'primary' : 'success' }}">
                    {{ ucfirst($download->category) }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-info">{{ $download->file_type ?? 'Document' }}</span>
                </td>
                <td>{{ $download->size ?? '—' }}</td>
                <td>{{ $download->uploaded_by ?? 'System' }}</td>
                <td>{{ $download->created_at->format('d M Y H:i') }}</td>
                <td class="text-end">
                  <div class="btn-group" role="group">
                    <a href="{{ $download->download_url }}" class="btn btn-sm btn-outline-primary">
                      <i class="ri-download-line"></i>
                    </a>
                    @if($download->preview_url)
                      <a href="{{ $download->preview_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-eye-line"></i>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <i class="ri-download-cloud-line me-2"></i>
                  No downloads available.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      {{-- Pagination --}}
      <div class="mt-3 d-flex justify-content-between align-items-center">
        <div class="small text-muted">
          Showing {{ $downloads->firstItem() }} to {{ $downloads->lastItem() }} of {{ $downloads->total() }} downloads
        </div>
        <div>{{ $downloads->links() }}</div>
      </div>
    </div>
  </div>

  {{-- Quick Links --}}
  <div class="zmc-card mt-4">
    <div class="card-header bg-primary text-white">
      <h6 class="fw-bold m-0 mb-0">
        <i class="ri-links-line me-2"></i>
        Quick Links
      </h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="border rounded p-3 text-center">
            <i class="ri-file-list-3-line text-primary" style="font-size:32px;"></i>
            <h6 class="fw-bold mt-2">Application Forms</h6>
            <p class="small text-muted mb-3">Download latest application forms</p>
            <a href="#" class="btn btn-sm btn-outline-primary">Download Forms</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="border rounded p-3 text-center">
            <i class="ri-file-text-line text-success" style="font-size:32px;"></i>
            <h6 class="fw-bold mt-2">Guidelines</h6>
            <p class="small text-muted mb-3">Accreditation and Registration guidelines</p>
            <a href="#" class="btn btn-sm btn-outline-success">View Guidelines</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="border rounded p-3 text-center">
            <i class="ri-bar-chart-box-line text-info" style="font-size:32px;"></i>
            <h6 class="fw-bold mt-2">Annual Reports</h6>
            <p class="small text-muted mb-3">Download annual reports</p>
            <a href="#" class="btn btn-sm btn-outline-info">Download Reports</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function filterByCategory(category) {
  const url = new URL(window.location);
  if (category) {
    url.searchParams.set('category', category);
  } else {
    url.searchParams.delete('category');
  }
  window.location.href = url.toString();
}

function refreshDownloads() {
  location.reload();
}
</script>
@endsection
