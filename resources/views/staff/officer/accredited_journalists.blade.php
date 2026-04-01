@extends('layouts.portal')
@section('title', 'Accredited Media Practitioners - Records Database')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Records Database</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Complete database of all accredited media practitioners and registered media houses.
      </div>
    </div>

    {{-- Mode Switches --}}
    <div class="d-flex gap-2">
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-primary active" id="databaseModeBtn">
          <i class="ri-database-2-line me-1"></i>Database
        </button>
        <button type="button" class="btn btn-outline-secondary" id="analyticsModeBtn">
          <i class="ri-bar-chart-line me-1"></i>Analytics
        </button>
      </div>
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-dashboard-3-line me-1"></i>Back to Officer
      </a>
    </div>
  </div>

  {{-- Navigation Tabs --}}
  <ul class="nav nav-tabs mb-4 px-1 border-bottom border-2">
    <li class="nav-item">
      <a class="nav-link active fw-bold text-dark border-0 border-bottom border-dark border-3" href="{{ route('staff.officer.records.accredited-journalists') }}" style="background: transparent;">
        <i class="ri-user-star-line me-1"></i> Accredited Media Practitioners
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link fw-semibold text-muted border-0" href="{{ route('staff.officer.records.registered-mediahouses') }}">
        <i class="ri-building-line me-1"></i> Registered Media Houses
      </a>
    </li>
  </ul>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  {{-- Database Mode --}}
  <div id="databaseMode">
    {{-- Search and Filter Section --}}
    <div class="zmc-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0"><i class="ri-search-line me-2"></i>Search & Filter Records</h6>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.officer.records.accredited-journalists.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-sm btn-outline-success">
            <i class="ri-download-2-line me-1"></i>Export All Fields
          </a>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAdvancedSearch()">
            <i class="ri-filter-3-line me-1"></i>Advanced Search
          </button>
        </div>
      </div>
      
      <form method="GET" action="{{ route('staff.officer.records.accredited-journalists') }}">
        <div class="row g-3">
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Search by Number/Name</label>
            <input type="text" name="search" class="form-control" 
                   value="{{ request('search') }}" 
                   placeholder="Accreditation number or name">
          </div>
          
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold">Year</label>
            <select name="year" class="form-select">
              <option value="">All Years</option>
              @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
          </div>
          
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
              <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
          </div>
          
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold">Collection</label>
            <select name="collection_status" class="form-select">
              <option value="">All</option>
              <option value="collected" {{ request('collection_status') == 'collected' ? 'selected' : '' }}>Collected</option>
              <option value="uncollected" {{ request('collection_status') == 'uncollected' ? 'selected' : '' }}>Uncollected</option>
            </select>
          </div>
          
          <div class="col-12 col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
              <i class="ri-search-line me-1"></i>Search Records
            </button>
          </div>
        </div>
        
        {{-- Advanced Search --}}
        <div id="advancedSearch" style="display: none;" class="mt-3 pt-3 border-top">
          <div class="row g-3">
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Organization</label>
              <input type="text" name="organization" class="form-control" 
                     value="{{ request('organization') }}" placeholder="Organization">
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Category</label>
              <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach(\App\Models\Application::accreditationCategories() as $key => $label)
                  <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Province</label>
              <select name="province" class="form-select">
                <option value="">All Provinces</option>
                <option value="harare" {{ request('province') == 'harare' ? 'selected' : '' }}>Harare</option>
                <option value="bulawayo" {{ request('province') == 'bulawayo' ? 'selected' : '' }}>Bulawayo</option>
                <option value="manicaland" {{ request('province') == 'manicaland' ? 'selected' : '' }}>Manicaland</option>
                <option value="mashonaland_central" {{ request('province') == 'mashonaland_central' ? 'selected' : '' }}>Mashonaland Central</option>
                <option value="mashonaland_east" {{ request('province') == 'mashonaland_east' ? 'selected' : '' }}>Mashonaland East</option>
                <option value="mashonaland_west" {{ request('province') == 'mashonaland_west' ? 'selected' : '' }}>Mashonaland West</option>
                <option value="masvingo" {{ request('province') == 'masvingo' ? 'selected' : '' }}>Masvingo</option>
                <option value="matabeleland_north" {{ request('province') == 'matabeleland_north' ? 'selected' : '' }}>Matabeleland North</option>
                <option value="matabeleland_south" {{ request('province') == 'matabeleland_south' ? 'selected' : '' }}>Matabeleland South</option>
                <option value="midlands" {{ request('province') == 'midlands' ? 'selected' : '' }}>Midlands</option>
              </select>
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Medium</label>
              <select name="medium" class="form-select">
                <option value="">All Medium</option>
                <option value="print" {{ request('medium') == 'print' ? 'selected' : '' }}>Print</option>
                <option value="broadcast" {{ request('medium') == 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                <option value="online" {{ request('medium') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="multimedia" {{ request('medium') == 'multimedia' ? 'selected' : '' }}>Multimedia</option>
              </select>
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Sex</label>
              <select name="sex" class="form-select">
                <option value="">All</option>
                <option value="male" {{ request('sex') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('sex') == 'female' ? 'selected' : '' }}>Female</option>
              </select>
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">ID Number</label>
              <input type="text" name="id_number" class="form-control" 
                     value="{{ request('id_number') }}" placeholder="ID Number">
            </div>
          </div>
        </div>
      </form>
    </div>

    {{-- Records Table with All Required Fields --}}
    <div class="zmc-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
          <thead class="bg-light sticky-top">
            <tr>
              <th rowspan="2">Media/Accred No</th>
              <th rowspan="2">Applicant Name</th>
              <th rowspan="2">Organization</th>
              <th rowspan="2">Category</th>
              <th colspan="2">Validity</th>
              <th colspan="2">Identification</th>
              <th colspan="4">Personal Details</th>
              <th colspan="3">Contact</th>
              <th rowspan="2">Medium</th>
              <th rowspan="2">Designation</th>
              <th rowspan="2">Actions</th>
            </tr>
            <tr>
              <th>Valid From</th>
              <th>Valid To</th>
              <th>ID Number</th>
              <th>Photo</th>
              <th>Marital Status</th>
              <th>Sex</th>
              <th>Date of Birth</th>
              <th>Birth Place</th>
              <th>Nationality</th>
              <th>Home Address</th>
              <th>Town</th>
              <th>Phone/Cell</th>
            </tr>
          </thead>
          <tbody>
            @forelse($journalists as $journalist)
              @php
                $app = $journalist->application;
                $formData = $app ? $app->form_data : [];
                $holder = $journalist->holder;
              @endphp
              <tr>
                <td class="fw-bold">{{ $journalist->certificate_no ?? '—' }}</td>
                <td>
                  <div class="fw-semibold">{{ $holder?->name ?? ($formData['first_name'] ?? '' . ' ' . $formData['surname'] ?? '') }}</div>
                  @if($holder && $holder->email)
                    <div class="small text-muted">{{ $holder->email }}</div>
                  @endif
                </td>
                <td>{{ $formData['organization'] ?? $formData['employer'] ?? '—' }}</td>
                <td>
                  <span class="badge bg-primary">{{ $app?->categoryLabel() ?? '—' }}</span>
                </td>
                <td>{{ optional($journalist->issued_at)->format('d M Y') ?? '—' }}</td>
                <td>
                  <div>{{ optional($journalist->expires_at)->format('d M Y') ?? '—' }}</div>
                  <div class="small text-muted">{{ $journalist->year ?? optional($journalist->issued_at)->format('Y') ?? '—' }}</div>
                </td>
                <td>{{ $formData['id_number'] ?? $formData['national_id'] ?? '—' }}</td>
                <td>
                  @if($app && $app->documents)
                    @php
                      $photo = $app->documents->where('doc_type', 'passport_photo')->first();
                    @endphp
                    @if($photo)
                      <img src="{{ asset('storage/' . $photo->file_path) }}" 
                           alt="Photo" class="rounded" style="width: 30px; height: 30px; object-fit: cover;">
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>{{ $formData['marital_status'] ?? '—' }}</td>
                <td>{{ $formData['sex'] ?? $formData['gender'] ?? '—' }}</td>
                <td>{{ $formData['date_of_birth'] ? \Carbon\Carbon::parse($formData['date_of_birth'])->format('d M Y') : '—' }}</td>
                <td>{{ $formData['place_of_birth'] ?? '—' }}</td>
                <td>{{ $formData['nationality'] ?? '—' }}</td>
                <td>{{ $formData['home_address'] ?? $formData['address'] ?? '—' }}</td>
                <td>{{ $formData['town'] ?? $formData['city'] ?? '—' }}</td>
                <td>
                  <div class="small">{{ $holder?->phone ?? $formData['phone_number'] ?? '—' }}</div>
                  <div class="small">{{ $holder?->phone ?? $formData['cell_number'] ?? '—' }}</div>
                </td>
                <td>{{ $formData['medium'] ?? '—' }}</td>
                <td>{{ $formData['designation'] ?? $formData['job_title'] ?? '—' }}</td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#" onclick="viewFullRecord({{ $journalist->id }})">
                        <i class="ri-eye-line me-1"></i>View Full Record
                      </a></li>
                      <li><a class="dropdown-item" href="#" onclick="editRecord({{ $journalist->id }})">
                        <i class="ri-edit-line me-1"></i>Edit Record
                      </a></li>
                      <li><a class="dropdown-item" href="#" onclick="downloadDocuments({{ $journalist->id }})">
                        <i class="ri-download-line me-1"></i>Download Documents
                      </a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-warning" href="#" onclick="requestEditApproval({{ $journalist->id }})">
                        <i class="ri-user-search-line me-1"></i>Request Edit Approval
                      </a></li>
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="18" class="text-center py-4 text-muted">
                  <i class="ri-inbox-line me-2"></i>
                  No accredited media practitioners found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($journalists, 'links'))
      <div class="mt-3">{{ $journalists->links() }}</div>
    @endif
  </div>

  {{-- Analytics Mode --}}
  <div id="analyticsMode" style="display: none;">
    <div class="row g-4 mb-4">
      <div class="col-12 col-lg-6">
        <div class="zmc-card h-100">
          <h6 class="fw-bold mb-3"><i class="ri-bar-chart-grouped-line me-2"></i>Accreditations by Category ({{ $currentYear }})</h6>
          <div style="height: 300px; position: relative;">
            <canvas id="categoryChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="zmc-card h-100">
          <h6 class="fw-bold mb-3"><i class="ri-line-chart-line me-2"></i>Accreditations Over Time ({{ $currentYear }})</h6>
          <div style="height: 300px; position: relative;">
            <canvas id="monthChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-12 col-lg-4">
        <div class="zmc-card">
          <h6 class="fw-bold mb-3"><i class="ri-pie-chart-line me-2"></i>Gender Distribution</h6>
          <div style="height: 250px; position: relative;">
            <canvas id="genderChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="zmc-card">
          <h6 class="fw-bold mb-3"><i class="ri-map-pin-line me-2"></i>Provincial Distribution</h6>
          <div style="height: 250px; position: relative;">
            <canvas id="provinceChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="zmc-card">
          <h6 class="fw-bold mb-3"><i class="ri-newspaper-line me-2"></i>Media Type Distribution</h6>
          <div style="height: 250px; position: relative;">
            <canvas id="mediaChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Full Record Modal --}}
<div class="modal fade" id="fullRecordModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Complete Accreditation Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="fullRecordContent">
          <!-- Content will be loaded via AJAX -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleAdvancedSearch() {
  const advancedSearch = document.getElementById('advancedSearch');
  advancedSearch.style.display = advancedSearch.style.display === 'none' ? 'block' : 'none';
}

function toggleMode(mode) {
  const databaseMode = document.getElementById('databaseMode');
  const analyticsMode = document.getElementById('analyticsMode');
  const databaseBtn = document.getElementById('databaseModeBtn');
  const analyticsBtn = document.getElementById('analyticsModeBtn');
  
  if (mode === 'analytics') {
    databaseMode.style.display = 'none';
    analyticsMode.style.display = 'block';
    databaseBtn.classList.remove('active');
    analyticsBtn.classList.add('active');
  } else {
    databaseMode.style.display = 'block';
    analyticsMode.style.display = 'none';
    databaseBtn.classList.add('active');
    analyticsBtn.classList.remove('active');
  }
}

document.getElementById('analyticsModeBtn').addEventListener('click', function() {
  toggleMode('analytics');
});

document.getElementById('databaseModeBtn').addEventListener('click', function() {
  toggleMode('database');
});

function viewFullRecord(id) {
  // Load full record via AJAX
  fetch(`/staff/officer/records/accredited-journalists/${id}/full`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('fullRecordContent').innerHTML = html;
      new bootstrap.Modal(document.getElementById('fullRecordModal')).show();
    });
}

function editRecord(id) {
  // Implement edit functionality
  console.log('Edit record:', id);
}

function downloadDocuments(id) {
  // Implement document download
  console.log('Download documents:', id);
}

function requestEditApproval(id) {
  // Implement edit approval request
  console.log('Request edit approval:', id);
}
</script>

@push('styles')
<style>
.table th {
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
}

.table td {
  font-size: 11px;
  vertical-align: middle;
}

.dropdown-menu {
  font-size: 12px;
}
</style>
@endpush
@endsection
