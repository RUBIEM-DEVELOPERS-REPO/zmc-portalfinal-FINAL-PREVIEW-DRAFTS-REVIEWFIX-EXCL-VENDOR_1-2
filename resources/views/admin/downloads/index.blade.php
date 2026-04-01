@extends('layouts.portal')

@section('title','Downloads')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h4 class="fw-bold m-0" style="font-size:20px;color:#0f172a;">Downloads</h4>
      <div class="text-muted" style="font-size:13px;">Export reports and data in CSV format with filters.</div>
    </div>
  </div>

  <div class="row g-3 align-items-start" id="downloadAccordion">
    @php
      $items = [
        ['label'=>'Applications','type'=>'applications','icon'=>'ri-file-list-3-line', 'description' => 'Accreditation and Registration records'],
        ['label'=>'Staff Users','type'=>'users_staff','icon'=>'ri-shield-user-line', 'description' => 'System administrators and officers'],
        ['label'=>'Public Users','type'=>'users_public','icon'=>'ri-user-smile-line', 'description' => 'Media practitioners and media house applicants'],
        ['label'=>'Complaints & Appeals','type'=>'complaints','icon'=>'ri-chat-1-line', 'description' => 'Submitted complaints and status'],
        ['label'=>'Notices','type'=>'notices','icon'=>'ri-notification-3-line', 'description' => 'Published portal notices'],
        ['label'=>'Events','type'=>'events','icon'=>'ri-calendar-event-line', 'description' => 'System and industry events'],
        ['label'=>'News','type'=>'news','icon'=>'ri-newspaper-line', 'description' => 'Press statements and news'],
      ];
    @endphp

    @foreach($items as $it)
      <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="bg-light rounded-circle flex-shrink-0" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                <i class="{{ $it['icon'] }}" style="font-size:24px; color: #2563eb;"></i>
              </div>
              <div>
                <div class="fw-bold h6 mb-0">{{ $it['label'] }}</div>
                <div class="text-muted small">{{ $it['description'] }}</div>
              </div>
            </div>

            <button type="button" class="btn btn-sm btn-dark w-100" data-bs-toggle="collapse" data-bs-target="#filter-{{ $it['type'] }}">
              <i class="ri-filter-3-line me-1"></i> Configure & Download
            </button>

            <div class="collapse mt-3" id="filter-{{ $it['type'] }}" data-bs-parent="#downloadAccordion">
              <form action="{{ route('admin.downloads.csv', $it['type']) }}" method="GET" class="p-3 bg-light rounded border">
                <div class="row g-2">
                  <div class="col-6">
                    <label class="small fw-bold text-muted">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm">
                  </div>
                  <div class="col-6">
                    <label class="small fw-bold text-muted">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm">
                  </div>

                  @if($it['type'] === 'applications')
                    <div class="col-12">
                      <label class="small fw-bold text-muted">App Type</label>
                      <select name="application_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="accreditation">Accreditation</option>
                        <option value="registration">Registration</option>
                      </select>
                    </div>
                    <div class="col-6">
                      <label class="small fw-bold text-muted">Residency</label>
                      <select name="residency" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="local">Local</option>
                        <option value="foreign">Foreign</option>
                      </select>
                    </div>
                    <div class="col-6">
                      <label class="small fw-bold text-muted">Is Renewal?</label>
                      <select name="is_renewal" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="yes">Yes (Renewal)</option>
                        <option value="no">No (New)</option>
                      </select>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted">Status</label>
                        <select name="status" class="form-select form-select-sm">
                          <option value="">All Statuses</option>
                          <option value="submitted">Submitted</option>
                          <option value="officer_approved">Officer Approved</option>
                          <option value="registrar_approved">Registrar Approved</option>
                          <option value="paid_confirmed">Paid & Confirmed</option>
                          <option value="issued">Issued</option>
                          <option value="rejected">Rejected</option>
                        </select>
                      </div>
                  @endif

                  @if($it['type'] === 'complaints')
                    <div class="col-12">
                        <label class="small fw-bold text-muted">Status</label>
                        <select name="status" class="form-select form-select-sm">
                          <option value="">All Statuses</option>
                          <option value="open">Open</option>
                          <option value="in_progress">In Progress</option>
                          <option value="resolved">Resolved</option>
                          <option value="closed">Closed</option>
                        </select>
                      </div>
                  @endif

                  <div class="col-12 mt-3">
                    @if(session('active_staff_role') !== 'it_admin')
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                      <i class="ri-download-cloud-2-line me-1"></i> Export to CSV
                    </button>
                    @else
                    <div class="alert alert-info small py-2 mb-0 border-0 shadow-none">
                        <i class="ri-information-line me-1"></i> Download restricted (View Only)
                    </div>
                    @endif
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
