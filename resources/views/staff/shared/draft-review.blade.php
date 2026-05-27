@extends('layouts.staff')

@section('title', 'Review Draft Application')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Review Draft Application</h4>
            <p class="text-muted small m-0">Read-only view of draft application details</p>
        </div>
        <a href="{{ route('staff.' . $role . '.drafts') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Back to Drafts
        </a>
    </div>

    {{-- Draft Status Banner --}}
    <div class="alert alert-warning border-0 mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="ri-draft-line fs-4"></i>
            <div>
                <div class="fw-bold">Draft Application</div>
                <div class="small">This is an incomplete application. Applicants must submit it for processing.</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Application Details --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="fw-bold m-0"><i class="ri-file-text-line me-2"></i>Application Details</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted">Reference Number</label>
                            <div class="fw-bold">{{ $draft->reference ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Application Type</label>
                            <div class="fw-bold">{{ ucfirst($draft->application_type ?? 'Unknown') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Request Type</label>
                            <div class="fw-bold">{{ ucfirst($draft->request_type ?? 'New') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Created At</label>
                            <div class="fw-bold">{{ $draft->created_at?->format('d M Y H:i') ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Last Updated</label>
                            <div class="fw-bold">{{ $draft->updated_at?->diffForHumans() ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Draft Expires In</label>
                            <div class="fw-bold {{ $daysRemaining <= 4 ? 'text-danger' : 'text-warning' }}">
                                {{ $daysRemaining }} days
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Applicant Information --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="fw-bold m-0"><i class="ri-user-line me-2"></i>Applicant Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted">Full Name</label>
                            <div class="fw-bold">{{ $draft->applicant?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Email</label>
                            <div class="fw-bold">{{ $draft->applicant?->email ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Phone</label>
                            <div class="fw-bold">{{ $draft->applicant?->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Applicant ID</label>
                            <div class="fw-bold">{{ $draft->applicant?->id ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Data (if available) --}}
            @if(!empty($formData))
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="fw-bold m-0"><i class="ri-clipboard-line me-2"></i>Form Data</h6>
                </div>
                <div class="card-body p-4">
                    <pre class="bg-slate-50 p-3 rounded small" style="max-height: 400px; overflow-y: auto;">{{ json_encode($formData, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Draft Status Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="fw-bold m-0">Draft Status</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="ri-draft-line text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Incomplete</div>
                            <div class="small text-muted">Awaiting applicant submission</div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted mb-2">Retention Policy</div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar {{ $daysRemaining <= 4 ? 'bg-danger' : 'bg-warning' }}" 
                             style="width: {{ ($daysRemaining / 14) * 100 }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>{{ $daysRemaining }} days remaining</span>
                        <span class="text-muted">14 days total</span>
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="fw-bold m-0">Actions</h6>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info small">
                        <i class="ri-information-line me-1"></i>
                        Draft applications can only be edited by the applicant. Staff can view but not modify drafts.
                    </div>
                    
                    @if($draft->applicant)
                    <a href="mailto:{{ $draft->applicant->email }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="ri-mail-line me-1"></i> Contact Applicant
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
