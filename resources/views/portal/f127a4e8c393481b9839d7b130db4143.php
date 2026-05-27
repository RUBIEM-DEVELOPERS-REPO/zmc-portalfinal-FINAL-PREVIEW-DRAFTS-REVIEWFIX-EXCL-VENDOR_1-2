<?php $__env->startSection('title', 'Registered Media Houses - Records Database'); ?>

<?php $__env->startSection('content'); ?>
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155; padding: 20px;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">Records Database</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-information-line me-1"></i>
        Complete database of all accredited media practitioners and registered media houses.
      </div>
    </div>

    
    <div class="d-flex gap-2">
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-primary active" id="databaseModeBtn">
          <i class="ri-database-2-line me-1"></i>Database
        </button>
        <button type="button" class="btn btn-outline-secondary" id="analyticsModeBtn">
          <i class="ri-bar-chart-line me-1"></i>Analytics
        </button>
      </div>
      <a href="<?php echo e(route('staff.officer.dashboard')); ?>" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-dashboard-3-line me-1"></i>Back to Officer
      </a>
    </div>
  </div>

  
  <ul class="nav nav-tabs mb-4 px-1 border-bottom border-2">
    <li class="nav-item">
      <a class="nav-link fw-semibold text-muted border-0" href="<?php echo e(route('staff.officer.records.accredited-journalists')); ?>">
        <i class="ri-user-star-line me-1"></i> Accredited Media Practitioners
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link active fw-bold text-dark border-0 border-bottom border-dark border-3" href="<?php echo e(route('staff.officer.records.registered-mediahouses')); ?>" style="background: transparent;">
        <i class="ri-building-line me-1"></i> Registered Media Houses
      </a>
    </li>
  </ul>

  <?php if(session('success')): ?>
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="ri-checkbox-circle-line" style="font-size:18px;line-height:1;"></i>
      <div><?php echo e(session('success')); ?></div>
    </div>
  <?php endif; ?>

  
  <div id="databaseMode">
    
    <div class="zmc-card mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0"><i class="ri-search-line me-2"></i>Search & Filter Records</h6>
        <div class="d-flex gap-2">
          <a href="<?php echo e(route('staff.officer.records.registered-mediahouses.export')); ?>?<?php echo e(http_build_query(request()->query())); ?>" class="btn btn-sm btn-outline-success">
            <i class="ri-download-2-line me-1"></i>Export All Fields
          </a>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAdvancedSearch()">
            <i class="ri-filter-3-line me-1"></i>Advanced Search
          </button>
        </div>
      </div>
      
      <form method="GET" action="<?php echo e(route('staff.officer.records.registered-mediahouses')); ?>">
        <div class="row g-3">
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold">Search by Number/Name</label>
            <input type="text" name="search" class="form-control" 
                   value="<?php echo e(request('search')); ?>" 
                   placeholder="Registration number or entity name">
          </div>
          
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold">Year</label>
            <select name="year" class="form-select">
              <option value="">All Years</option>
              <?php for($y = date('Y'); $y >= date('Y') - 10; $y--): ?>
                <option value="<?php echo e($y); ?>" <?php echo e(request('year') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
              <?php endfor; ?>
            </select>
          </div>
          
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
              <option value="expired" <?php echo e(request('status') == 'expired' ? 'selected' : ''); ?>>Expired</option>
              <option value="suspended" <?php echo e(request('status') == 'suspended' ? 'selected' : ''); ?>>Suspended</option>
            </select>
          </div>
          
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold">Collection</label>
            <select name="collection_status" class="form-select">
              <option value="">All</option>
              <option value="collected" <?php echo e(request('collection_status') == 'collected' ? 'selected' : ''); ?>>Collected</option>
              <option value="uncollected" <?php echo e(request('collection_status') == 'uncollected' ? 'selected' : ''); ?>>Uncollected</option>
            </select>
          </div>
          
          <div class="col-12 col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
              <i class="ri-search-line me-1"></i>Search Records
            </button>
          </div>
        </div>
        
        
        <div id="advancedSearch" style="display: none;" class="mt-3 pt-3 border-top">
          <div class="row g-3">
            <div class="col-12 col-md-3">
              <label class="form-label small fw-bold">Ownership Type</label>
              <select name="ownership_type" class="form-select">
                <option value="">All Types</option>
                <option value="local" <?php echo e(request('ownership_type') == 'local' ? 'selected' : ''); ?>>Local</option>
                <option value="foreign" <?php echo e(request('ownership_type') == 'foreign' ? 'selected' : ''); ?>>Foreign</option>
                <option value="joint_venture" <?php echo e(request('ownership_type') == 'joint_venture' ? 'selected' : ''); ?>>Joint Venture</option>
              </select>
            </div>
            
            <div class="col-12 col-md-3">
              <label class="form-label small fw-bold">ZMC Category (Media Type)</label>
              <select name="media_type" class="form-select">
                <option value="">All Categories</option>
                <optgroup label="Mass Media Service Categories">
                  <option value="MC" <?php echo e(request('media_type') == 'MC' ? 'selected' : ''); ?>>MC — Community Media</option>
                  <option value="MA" <?php echo e(request('media_type') == 'MA' ? 'selected' : ''); ?>>MA — Advertising agency as media service</option>
                  <option value="MF" <?php echo e(request('media_type') == 'MF' ? 'selected' : ''); ?>>MF — Local office for foreign media service</option>
                  <option value="MN" <?php echo e(request('media_type') == 'MN' ? 'selected' : ''); ?>>MN — National media service publishing newspaper</option>
                  <option value="DG" <?php echo e(request('media_type') == 'DG' ? 'selected' : ''); ?>>DG — Internet base media service</option>
                  <option value="MP" <?php echo e(request('media_type') == 'MP' ? 'selected' : ''); ?>>MP — Production house as media service</option>
                  <option value="MS" <?php echo e(request('media_type') == 'MS' ? 'selected' : ''); ?>>MS — National media service publishing magazine</option>
                  <option value="MT" <?php echo e(request('media_type') == 'MT' ? 'selected' : ''); ?>>MT — Broadcasting media service free to air</option>
                  <option value="MB" <?php echo e(request('media_type') == 'MB' ? 'selected' : ''); ?>>MB — Satellite broadcast</option>
                  <option value="MV" <?php echo e(request('media_type') == 'MV' ? 'selected' : ''); ?>>MV — Video on demand</option>
                </optgroup>
              </select>
            </div>
            
            <div class="col-12 col-md-3">
              <label class="form-label small fw-bold">Province</label>
              <select name="province" class="form-select">
                <option value="">All Provinces</option>
                <option value="harare" <?php echo e(request('province') == 'harare' ? 'selected' : ''); ?>>Harare</option>
                <option value="bulawayo" <?php echo e(request('province') == 'bulawayo' ? 'selected' : ''); ?>>Bulawayo</option>
                <option value="manicaland" <?php echo e(request('province') == 'manicaland' ? 'selected' : ''); ?>>Manicaland</option>
                <option value="mashonaland_central" <?php echo e(request('province') == 'mashonaland_central' ? 'selected' : ''); ?>>Mashonaland Central</option>
                <option value="mashonaland_east" <?php echo e(request('province') == 'mashonaland_east' ? 'selected' : ''); ?>>Mashonaland East</option>
                <option value="mashonaland_west" <?php echo e(request('province') == 'mashonaland_west' ? 'selected' : ''); ?>>Mashonaland West</option>
                <option value="masvingo" <?php echo e(request('province') == 'masvingo' ? 'selected' : ''); ?>>Masvingo</option>
                <option value="matabeleland_north" <?php echo e(request('province') == 'matabeleland_north' ? 'selected' : ''); ?>>Matabeleland North</option>
                <option value="matabeleland_south" <?php echo e(request('province') == 'matabeleland_south' ? 'selected' : ''); ?>>Matabeleland South</option>
                <option value="midlands" <?php echo e(request('province') == 'midlands' ? 'selected' : ''); ?>>Midlands</option>
              </select>
            </div>
            
            <div class="col-12 col-md-3">
              <label class="form-label small fw-bold">Business Type</label>
              <select name="business_type" class="form-select">
                <option value="">All Types</option>
                <option value="private" <?php echo e(request('business_type') == 'private' ? 'selected' : ''); ?>>Private</option>
                <option value="public" <?php echo e(request('business_type') == 'public' ? 'selected' : ''); ?>>Public</option>
                <option value="government" <?php echo e(request('business_type') == 'government' ? 'selected' : ''); ?>>Government</option>
                <option value="ngo" <?php echo e(request('business_type') == 'ngo' ? 'selected' : ''); ?>>NGO</option>
              </select>
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Registration Number</label>
              <input type="text" name="reg_number" class="form-control" 
                     value="<?php echo e(request('reg_number')); ?>" placeholder="Business registration">
            </div>
            
            <div class="col-12 col-md-2">
              <label class="form-label small fw-bold">Tax Number</label>
              <input type="text" name="tax_number" class="form-control" 
                     value="<?php echo e(request('tax_number')); ?>" placeholder="Tax/BVR number">
            </div>
          </div>
        </div>
      </form>
    </div>

    
    <div class="zmc-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
          <thead class="bg-light sticky-top">
            <tr>
              <th rowspan="2">Registration No</th>
              <th rowspan="2">Entity Name</th>
              <th rowspan="2">Trading Name</th>
              <th colspan="3">Business Details</th>
              <th colspan="2">Ownership</th>
              <th colspan="3">Contact Person</th>
              <th colspan="4">Contact Information</th>
              <th rowspan="2">Media Type</th>
              <th rowspan="2">Actions</th>
            </tr>
            <tr>
              <th>Business Type</th>
              <th>Business Reg</th>
              <th>Tax Number</th>
              <th>Ownership Type</th>
              <th>Shareholding</th>
              <th>Title</th>
              <th>Name</th>
              <th>ID Number</th>
              <th>Physical Address</th>
              <th>Postal Address</th>
              <th>Phone</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $mediaHouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mediahouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $app = $mediahouse->application;
                $formData = $app ? $app->form_data : [];
                $contact = $mediahouse->contact;
              ?>
              <tr>
                <td class="fw-bold"><?php echo e($mediahouse->registration_no ?? '—'); ?></td>
                <td>
                  <div class="fw-semibold"><?php echo e($formData['entity_name'] ?? $formData['company_name'] ?? '—'); ?></div>
                  <?php if($contact && $contact->email): ?>
                    <div class="small text-muted"><?php echo e($contact->email); ?></div>
                  <?php endif; ?>
                </td>
                <td><?php echo e($formData['trading_name'] ?? $formData['trading_as'] ?? '—'); ?></td>
                <td>
                  <span class="badge bg-info"><?php echo e($formData['business_type'] ?? '—'); ?></span>
                </td>
                <td><?php echo e($formData['business_registration'] ?? $formData['company_registration'] ?? '—'); ?></td>
                <td><?php echo e($formData['tax_number'] ?? $formData['bvr_number'] ?? '—'); ?></td>
                <td>
                  <span class="badge bg-<?php echo e($formData['ownership_type'] === 'foreign' ? 'warning' : 'primary'); ?>">
                    <?php echo e(ucfirst($formData['ownership_type'] ?? '—')); ?>

                  </span>
                </td>
                <td><?php echo e($formData['shareholding_percentage'] ?? $formData['local_ownership'] ?? '—'); ?>%</td>
                <td><?php echo e($formData['contact_person_title'] ?? '—'); ?></td>
                <td>
                  <div class="fw-semibold"><?php echo e($contact?->name ?? $formData['contact_person_name'] ?? '—'); ?></div>
                  <div class="small text-muted"><?php echo e($formData['contact_person_surname'] ?? ''); ?></div>
                </td>
                <td><?php echo e($formData['contact_person_id_number'] ?? $contact?->id_number ?? '—'); ?></td>
                <td><?php echo e($formData['physical_address'] ?? $formData['address'] ?? '—'); ?></td>
                <td><?php echo e($formData['postal_address'] ?? $formData['mailing_address'] ?? '—'); ?></td>
                <td>
                  <div class="small"><?php echo e($contact?->phone ?? $formData['phone_number'] ?? '—'); ?></div>
                  <div class="small"><?php echo e($contact?->phone ?? $formData['mobile_number'] ?? '—'); ?></div>
                </td>
                <td>
                  <div class="small text-truncate" style="max-width: 150px;" title="<?php echo e($contact?->email ?? $formData['email_address'] ?? ''); ?>">
                    <?php echo e($contact?->email ?? $formData['email_address'] ?? '—'); ?>

                  </div>
                </td>
                <td>
                  <span class="badge bg-success"><?php echo e($formData['media_type'] ?? '—'); ?></span>
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#" onclick="viewFullRecord(<?php echo e($mediahouse->id); ?>)">
                        <i class="ri-eye-line me-1"></i>View Full Record
                      </a></li>
                      <li><a class="dropdown-item" href="#" onclick="editRecord(<?php echo e($mediahouse->id); ?>)">
                        <i class="ri-edit-line me-1"></i>Edit Record
                      </a></li>
                      <li><a class="dropdown-item" href="#" onclick="downloadDocuments(<?php echo e($mediahouse->id); ?>)">
                        <i class="ri-download-line me-1"></i>Download Documents
                      </a></li>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="17" class="text-center py-4 text-muted">
                  <i class="ri-inbox-line me-2"></i>
                  No registered media houses found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if($mediaHouses->hasPages()): ?>
        <div class="p-3 border-top">
          <?php echo e($mediaHouses->links()); ?>

        </div>
      <?php endif; ?>
    </div>
  </div>

  
  <div id="analyticsMode" style="display: none;">
    <div class="row g-4 mb-4">
      <div class="col-12 col-lg-6">
        <div class="zmc-card h-100">
          <h6 class="fw-bold mb-3"><i class="ri-bar-chart-grouped-line me-2"></i>Registrations by Type (<?php echo e($currentYear); ?>)</h6>
          <div style="height: 300px; position: relative;">
            <canvas id="typeChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="zmc-card h-100">
          <h6 class="fw-bold mb-3"><i class="ri-line-chart-line me-2"></i>Registrations Over Time (<?php echo e($currentYear); ?>)</h6>
          <div style="height: 300px; position: relative;">
            <canvas id="monthChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-12 col-lg-4">
        <div class="zmc-card">
          <h6 class="fw-bold mb-3"><i class="ri-pie-chart-line me-2"></i>Ownership Distribution</h6>
          <div style="height: 250px; position: relative;">
            <canvas id="ownershipChart"></canvas>
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


<div class="modal fade" id="fullRecordModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Complete Registration Record</h5>
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
  fetch(`/staff/officer/records/registered-mediahouses/${id}/full`)
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

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>


<div class="modal fade" id="editRecordModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="<?php echo e(route('staff.officer.records.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="record_type" id="edit_record_type" value="registration">
        <input type="hidden" name="record_id" id="edit_record_id" value="">
        
        <div class="modal-header">
          <h5 class="modal-title">Edit Record Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" id="editRecordSpinner" style="display:none;">
          <div class="text-center my-4"><i class="ri-loader-4-line ri-spin" style="font-size:2rem;color:#ccc;"></i></div>
        </div>
        <div class="modal-body p-4" id="editRecordBody">
           
        </div>
        <div class="modal-footer border-top-0 pt-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="editRecordSaveBtn"><i class="ri-save-line me-1"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openEditRecordModal(type, id) {
    document.getElementById('edit_record_type').value = type;
    document.getElementById('edit_record_id').value = id;
    
    const bodyObj = document.getElementById('editRecordBody');
    const spinObj = document.getElementById('editRecordSpinner');
    
    bodyObj.style.display = 'none';
    spinObj.style.display = 'block';
    
    const modal = new bootstrap.Modal(document.getElementById('editRecordModal'));
    modal.show();

    fetch(`<?php echo e(url('staff/accreditation-officer/records')); ?>/${id}/edit-data?type=${type}`)
        .then(r => r.json())
        .then(res => {
            spinObj.style.display = 'none';
            bodyObj.style.display = 'block';
            
            if (!res.success) {
                bodyObj.innerHTML = `<div class="alert alert-danger">${res.message || 'Failed to load data.'}</div>`;
                return;
            }
            
            let html = `<div class="row g-3">`;
            const keys = Object.keys(res.data).filter(k => typeof res.data[k] === 'string' || typeof res.data[k] === 'number');
            
            const editableKeys = [
                'org_name', 'rep_office_name', 'entity_name', 'contact_name', 'contact_surname',
                'contact_phone', 'contact_email', 'contact_address', 'org_head_office', 'head_office',
                'postal_address', 'org_postal_address', 'mass_media_activity', 'website'
            ];
            
            const activeKeys = keys.filter(k => editableKeys.includes(k) || res.data[k]);
            
            activeKeys.forEach(k => {
                if (typeof res.data[k] === 'object') return;
                let label = k.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                
                html += `
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">${label}</label>
                    <input type="text" class="form-control" name="form_data[${k}]" value="${(res.data[k] || '').replace(/"/g, '&quot;')}">
                  </div>
                `;
            });
            html += `</div>`;
            bodyObj.innerHTML = html;
        })
        .catch(err => {
            spinObj.style.display = 'none';
            bodyObj.style.display = 'block';
            bodyObj.innerHTML = `<div class="alert alert-danger">Network error loading record data.</div>`;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?php echo json_encode($chartData, 15, 512) ?>;

    // Month Line Chart
    const ctxMonth = document.getElementById('monthChart')?.getContext('2d');
    if (ctxMonth) {
        new Chart(ctxMonth, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Registrations',
                    data: chartData.months,
                    borderColor: '#1e7e34',
                    backgroundColor: 'rgba(30, 126, 52, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    // Category Bar Chart (maps to typeChart in new layout)
    const ctxCategory = document.getElementById('typeChart')?.getContext('2d');
    if (ctxCategory) {
        new Chart(ctxCategory, {
            type: 'bar',
            data: {
                labels: chartData.categories.labels,
                datasets: [{
                    label: 'Registrations',
                    data: chartData.categories.data,
                    backgroundColor: [
                        'rgba(26, 86, 219, 0.8)', 'rgba(30, 126, 52, 0.8)', 'rgba(234, 179, 8, 0.8)',
                        'rgba(147, 51, 234, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(14, 165, 233, 0.8)',
                        'rgba(249, 115, 22, 0.8)', 'rgba(75, 85, 99, 0.8)'
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.portal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/patiencemupikeni/Downloads/ZMCPORTAL/resources/views/staff/officer/registered_mediahouses.blade.php ENDPATH**/ ?>