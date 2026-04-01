@extends('layouts.portal')
@section('title', 'Registered Media Houses - Records Database')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Registered Media Houses</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Complete database of all registered media houses with comprehensive business and operational details.
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-primary active" id="tableViewBtn">
          <i class="ri-table-line me-1"></i>Table View
        </button>
        <button type="button" class="btn btn-outline-secondary" id="exportViewBtn">
          <i class="ri-download-2-line me-1"></i>Export Data
        </button>
      </div>
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-dashboard-3-line me-1"></i>Back to Officer
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  {{-- Export Section --}}
  <div id="exportSection" class="zmc-card mb-4" style="display: none;">
    <div class="card-header bg-primary text-white">
      <h6 class="fw-bold m-0"><i class="ri-download-2-line me-2"></i>Export Media Houses Data</h6>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('staff.officer.records.registered-mediahouses.export') }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold">Export Format</label>
            <select name="format" class="form-select">
              <option value="csv">CSV</option>
              <option value="excel">Excel</option>
              <option value="pdf">PDF</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Date Range</label>
            <select name="date_range" class="form-select">
              <option value="">All Records</option>
              <option value="current_year">Current Year</option>
              <option value="last_year">Last Year</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="include_details" id="include_details" checked>
              <label class="form-check-label" for="include_details">
                Include all detailed fields (services, publications, operational data)
              </label>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="ri-download-line me-1"></i>Export All Fields
            </button>
            <a href="{{ route('staff.officer.records.registered-mediahouses.export') }}?format=csv&include_details=1" class="btn btn-outline-success ms-2">
              <i class="ri-file-excel-line me-1"></i>Quick Export CSV
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Search and Filter Section --}}
  <div class="zmc-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-bold m-0"><i class="ri-search-line me-2"></i>Search & Filter Records</h6>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleExportSection()">
          <i class="ri-download-2-line me-1"></i>Export Options
        </button>
      </div>
    </div>
    
    <form method="GET" action="{{ route('staff.officer.records.registered-mediahouses') }}">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label class="form-label small fw-bold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Registration No, Company Name, or Email" value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Category</label>
          <select name="category" class="form-select">
            <option value="">All Categories</option>
            <option value="newspaper">Newspaper</option>
            <option value="magazine">Magazine</option>
            <option value="digital">Digital Platform</option>
            <option value="newsletter">Newsletter</option>
            <option value="agency">News Agency</option>
            <option value="production">Production House</option>
            <option value="advertising">Advertising</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label small fw-bold">Operational Status</label>
          <select name="operational_status" class="form-select">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">
            <i class="ri-search-line me-1"></i> Search
          </button>
        </div>
      </div>
      @if(request()->hasAny(['search', 'category', 'operational_status']))
        <div class="mt-3">
          <a href="{{ route('staff.officer.records.registered-mediahouses') }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-close-line me-1"></i> Clear Filters
          </a>
        </div>
      @endif
    </form>
  </div>

  {{-- Main Table --}}
  <div class="zmc-card p-0 shadow-sm border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th rowspan="2" style="width: 120px;">Registration No</th>
            <th rowspan="2" style="width: 200px;">Organization / Media Company</th>
            <th colspan="2">Directors</th>
            <th rowspan="2">Shareholding Structure</th>
            <th rowspan="2">Office Address</th>
            <th colspan="2">Contact Information</th>
            <th rowspan="2">Website</th>
            <th rowspan="2">Category</th>
            <th rowspan="2">Registration Status</th>
            <th colspan="2">Registration Dates</th>
            <th rowspan="2">License Status</th>
            <th rowspan="2" style="width: 100px;">Actions</th>
          </tr>
          <tr>
            <th>Sex</th>
            <th>Telephone(s)</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Registration Year</th>
          </tr>
        </thead>
        <tbody>
          @forelse($mediaHouses as $mediahouse)
            @php
              $app = $mediahouse->application;
              $formData = $app ? $app->form_data : [];
              $contact = $mediahouse->contact;
              $directors = $formData['directors'] ?? [];
              $services = $formData['services'] ?? [];
            @endphp
            <tr>
              <td class="fw-bold text-primary">{{ $mediahouse->registration_no ?? '—' }}</td>
              <td>
                <div class="fw-semibold">{{ $formData['entity_name'] ?? $formData['company_name'] ?? '—' }}</div>
                @if($contact && $contact->email)
                  <div class="small text-muted">{{ $contact->email }}</div>
                @endif
              </td>
              <td>
                @foreach($directors as $index => $director)
                  <div>{{ $director['name'] ?? '' }}</div>
                  @if($director['sex'])
                    <span class="badge bg-light text-dark small">{{ $director['sex'] }}</span>
                  @endif
                @endforeach
              </td>
              <td>
                @foreach($directors as $director)
                  @if($director['telephone'])
                    <div class="small">{{ $director['telephone'] }}</div>
                  @endif
                @endforeach
              </td>
              <td>
                <div class="small">{{ $formData['shareholding_structure'] ?? '—' }}</div>
                @if($formData['local_ownership_percentage'])
                  <div class="badge bg-success">{{ $formData['local_ownership_percentage'] }}% Local</div>
                @endif
              </td>
              <td class="small">{{ $formData['office_address'] ?? '—' }}</td>
              <td class="small">{{ $contact->phone ?? '—' }}</td>
              <td class="small">{{ $contact->email ?? '—' }}</td>
              <td class="small">
                <a href="{{ $formData['website'] ?? '#' }}" target="_blank">{{ $formData['website'] ?? '—' }}</a>
              </td>
              <td>
                <span class="badge bg-info">{{ $formData['media_category'] ?? '—' }}</span>
              </td>
              <td>
                @if($mediahouse->status === 'active')
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-warning">{{ ucfirst($mediahouse->status ?? '—') }}</span>
                @endif
              </td>
              <td class="small">{{ optional($mediahouse->issued_at)->format('d M Y') ?? '—' }}</td>
              <td class="small">{{ optional($mediahouse->issued_at)->format('Y') ?? '—' }}</td>
              <td>
                @if($mediahouse->license_status === 'valid')
                  <span class="badge bg-success">Valid</span>
                @else
                  <span class="badge bg-danger">{{ ucfirst($mediahouse->license_status ?? '—') }}</span>
                @endif
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="viewDetails({{ $mediahouse->id }})">
                  <i class="ri-eye-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="editRecord({{ $mediahouse->id }})">
                  <i class="ri-edit-line"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="16" class="text-center text-muted py-4">
                <i class="ri-building-line" style="font-size:48px;opacity:0.3;"></i>
                <div class="mt-2">No registered media houses found</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($mediaHouses->hasPages())
      <div class="p-3 border-top">
        {{ $mediaHouses->links() }}
      </div>
    @endif
  </div>

  {{-- Details Modal --}}
  <div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Media House Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="detailsContent">
            <div class="text-center py-4">
              <i class="ri-loader-4-line ri-spin" style="font-size:2rem;color:#ccc;"></i>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="editFromDetailsBtn">
            <i class="ri-edit-line me-1"></i>Edit Record
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Edit Record Modal --}}
  <div class="modal fade" id="editRecordModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form method="POST" action="{{ route('staff.officer.records.update') }}">
          @csrf
          @method('PUT')
          <input type="hidden" name="record_type" id="edit_record_type" value="registration">
          <input type="hidden" name="record_id" id="edit_record_id" value="">
          
          <div class="modal-header">
            <h5 class="modal-title">Edit Media House Record</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" id="editRecordSpinner" style="display:none;">
            <div class="text-center my-4"><i class="ri-loader-4-line ri-spin" style="font-size:2rem;color:#ccc;"></i></div>
          </div>
          <div class="modal-body p-4" id="editRecordBody">
             {{-- Loaded via AJAX --}}
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="editRecordSaveBtn">
              <i class="ri-save-line me-1"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function toggleExportSection() {
  const exportSection = document.getElementById('exportSection');
  exportSection.style.display = exportSection.style.display === 'none' ? 'block' : 'none';
}

function viewDetails(id) {
  // Load detailed view via AJAX
  fetch(`{{ url('staff/accreditation-officer/records') }}/${id}/details`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('detailsContent').innerHTML = data.html;
        const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
        modal.show();
        
        // Set edit button
        document.getElementById('editFromDetailsBtn').onclick = () => editRecord(id);
      }
    })
    .catch(error => {
      console.error('Error loading details:', error);
    });
}

function editRecord(id) {
  // Load edit form via AJAX
  fetch(`{{ url('staff/accreditation-officer/records') }}/${id}/edit-data?type=registration`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('edit_record_id').value = id;
        
        const bodyDiv = document.getElementById('editRecordBody');
        const spinDiv = document.getElementById('editRecordSpinner');
        
        // Build comprehensive edit form
        let html = '<div class="row g-3">';
        
        // Basic Information
        html += '<div class="col-12"><h6 class="text-primary mb-3">Basic Information</h6></div>';
        html += '<div class="col-md-6"><label class="form-label">Registration No</label><input type="text" class="form-control" name="form_data[registration_no]" value="' + (data.data.registration_no || '') + '" readonly></div>';
        html += '<div class="col-md-6"><label class="form-label">Entity Name</label><input type="text" class="form-control" name="form_data[entity_name]" value="' + (data.data.entity_name || '') + '"></div>';
        html += '<div class="col-md-6"><label class="form-label">Trading Name</label><input type="text" class="form-control" name="form_data[trading_name]" value="' + (data.data.trading_name || '') + '"></div>';
        html += '<div class="col-md-6"><label class="form-label">Business Registration</label><input type="text" class="form-control" name="form_data[business_registration]" value="' + (data.data.business_registration || '') + '"></div>';
        
        // Directors
        html += '<div class="col-12 mt-3"><h6 class="text-primary mb-3">Directors</h6></div>';
        if (data.data.directors && data.data.directors.length > 0) {
          data.data.directors.forEach((director, index) => {
            html += '<div class="col-md-4"><label class="form-label">Director ' + (index + 1) + ' Name</label><input type="text" class="form-control" name="form_data[directors][' + index + '][name]" value="' + (director.name || '') + '"></div>';
            html += '<div class="col-md-2"><label class="form-label">Sex</label><select class="form-select" name="form_data[directors][' + index + '][sex]"><option value="male" ' + (director.sex === 'male' ? 'selected' : '') + '>Male</option><option value="female" ' + (director.sex === 'female' ? 'selected' : '') + '>Female</option></select></div>';
            html += '<div class="col-md-3"><label class="form-label">Telephone</label><input type="text" class="form-control" name="form_data[directors][' + index + '][telephone]" value="' + (director.telephone || '') + '"></div>';
            html += '<div class="col-md-3"><label class="form-label">Email</label><input type="email" class="form-control" name="form_data[directors][' + index + '][email]" value="' + (director.email || '') + '"></div>';
          });
        }
        
        // Shareholding
        html += '<div class="col-12 mt-3"><h6 class="text-primary mb-3">Shareholding Structure</h6></div>';
        html += '<div class="col-md-6"><label class="form-label">Local Ownership %</label><input type="number" class="form-control" name="form_data[local_ownership_percentage]" value="' + (data.data.local_ownership_percentage || '') + '" min="0" max="100"></div>';
        html += '<div class="col-md-6"><label class="form-label">Shareholding Structure</label><input type="text" class="form-control" name="form_data[shareholding_structure]" value="' + (data.data.shareholding_structure || '') + '"></div>';
        
        // Contact Information
        html += '<div class="col-12 mt-3"><h6 class="text-primary mb-3">Contact Information</h6></div>';
        html += '<div class="col-md-6"><label class="form-label">Office Address</label><textarea class="form-control" name="form_data[office_address]" rows="2">' + (data.data.office_address || '') + '</textarea></div>';
        html += '<div class="col-md-3"><label class="form-label">Telephone</label><input type="text" class="form-control" name="form_data[telephone]" value="' + (data.data.telephone || '') + '"></div>';
        html += '<div class="col-md-3"><label class="form-label">Phone</label><input type="text" class="form-control" name="form_data[phone]" value="' + (data.data.phone || '') + '"></div>';
        html += '<div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="form_data[email]" value="' + (data.data.email || '') + '"></div>';
        html += '<div class="col-md-6"><label class="form-label">Website</label><input type="url" class="form-control" name="form_data[website]" value="' + (data.data.website || '') + '"></div>';
        
        // Media Information
        html += '<div class="col-12 mt-3"><h6 class="text-primary mb-3">Media Information</h6></div>';
        html += '<div class="col-md-4"><label class="form-label">Category</label><select class="form-select" name="form_data[media_category]"><option value="newspaper" ' + (data.data.media_category === 'newspaper' ? 'selected' : '') + '>Newspaper</option><option value="magazine" ' + (data.data.media_category === 'magazine' ? 'selected' : '') + '>Magazine</option><option value="digital" ' + (data.data.media_category === 'digital' ? 'selected' : '') + '>Digital Platform</option><option value="newsletter" ' + (data.data.media_category === 'newsletter' ? 'selected' : '') + '>Newsletter</option><option value="agency" ' + (data.data.media_category === 'agency' ? 'selected' : '') + '>News Agency</option><option value="production" ' + (data.data.media_category === 'production' ? 'selected' : '') + '>Production House</option><option value="advertising" ' + (data.data.media_category === 'advertising' ? 'selected' : '') + '>Advertising</option></select></div>';
        html += '<div class="col-md-4"><label class="form-label">License Status</label><select class="form-select" name="form_data[license_status]"><option value="valid" ' + (data.data.license_status === 'valid' ? 'selected' : '') + '>Valid</option><option value="expired" ' + (data.data.license_status === 'expired' ? 'selected' : '') + '>Expired</option><option value="suspended" ' + (data.data.license_status === 'suspended' ? 'selected' : '') + '>Suspended</option></select></div>';
        html += '<div class="col-md-4"><label class="form-label">Operational Status</label><select class="form-select" name="form_data[operational_status]"><option value="active" ' + (data.data.operational_status === 'active' ? 'selected' : '') + '>Active</option><option value="inactive" ' + (data.data.operational_status === 'inactive' ? 'selected' : '') + '>Inactive</option><option value="suspended" ' + (data.data.operational_status === 'suspended' ? 'selected' : '') + '>Suspended</option></select></div>';
        
        html += '</div>';
        
        bodyDiv.innerHTML = html;
        spinDiv.style.display = 'none';
        
        const modal = new bootstrap.Modal(document.getElementById('editRecordModal'));
        modal.show();
      }
    })
    .catch(error => {
      console.error('Error loading edit form:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
  // View mode switching
  const tableViewBtn = document.getElementById('tableViewBtn');
  const exportViewBtn = document.getElementById('exportViewBtn');
  
  if (tableViewBtn && exportViewBtn) {
    tableViewBtn.addEventListener('click', function() {
      tableViewBtn.classList.add('active');
      exportViewBtn.classList.remove('active');
      document.getElementById('exportSection').style.display = 'none';
    });
    
    exportViewBtn.addEventListener('click', function() {
      exportViewBtn.classList.add('active');
      tableViewBtn.classList.remove('active');
      document.getElementById('exportSection').style.display = 'block';
    });
  }
});
</script>
@endpush
@endsection
