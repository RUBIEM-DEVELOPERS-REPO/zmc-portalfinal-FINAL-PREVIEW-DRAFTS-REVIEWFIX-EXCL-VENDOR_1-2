@extends('layouts.portal')
@section('title', 'Registrar Review - ' . $application->reference)

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">{{ $application->reference }}</h4>
            <div class="text-muted small">
                {{ strtoupper($application->application_type) }} •
                <span class="badge bg-primary-subtle text-primary border-primary">
                    {{ strtoupper(str_replace('_',' ', $application->status)) }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.registrar.incoming-queue') }}" class="btn btn-light border btn-sm">
                <i class="ri-arrow-left-line"></i> Back to Queue
            </a>
            <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-light border btn-sm">Dashboard</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Applicant Details & Workspace --}}
        <div class="col-lg-8">
            {{-- A) Applicant Details Panel --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold d-flex justify-content-between">
                    <span><i class="ri-user-line me-1"></i> Applicant Details</span>
                    <span class="small text-muted">ID: {{ $application->id }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small text-muted d-block">Full Name</label>
                            <span class="fw-bold">{{ $application->applicant?->name ?? '—' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted d-block">ID/Passport</label>
                            <span class="fw-bold">{{ $application->form_data['id_passport_number'] ?? '—' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted d-block">Nationality</label>
                            <span class="fw-bold">{{ $application->form_data['nationality'] ?? '—' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted d-block">Residency</label>
                            <span class="fw-bold text-uppercase">{{ $application->residency_type ?? 'local' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted d-block">Contact</label>
                            <span class="fw-bold">{{ $application->applicant?->email }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted d-block">Media House</label>
                            <span class="fw-bold">{{ $application->form_data['employer_name'] ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- B) Category Validation Panel --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-award-line me-1"></i> Category Validation
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="small text-muted d-block">Currently Assigned Category</label>
                            <span class="h5 fw-bold text-primary">
                                {{ $application->accreditation_category_code ?? $application->media_house_category_code ?? 'NOT ASSIGNED' }}
                            </span>
                        </div>
                        @if(in_array($application->status, ['paid_confirmed', 'registrar_review']))
                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reassignModal">
                            <i class="ri-edit-line"></i> Reassign Category
                        </button>
                        @endif
                    </div>

                    <div class="p-3 bg-light rounded">
                        <h6 class="small fw-bold mb-2">Supporting Evidence Summary</h6>
                        <ul class="small mb-0">
                            <li>Designation: {{ $application->form_data['designation'] ?? '—' }}</li>
                            <li>Medium Type: {{ $application->form_data['medium_type'] ?? '—' }}</li>
                            <li>Scope: {{ $application->journalist_scope ?? '—' }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- C) Document Completeness Checklist --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-checkbox-list-line me-1"></i> Document Checklist
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($application->documents as $doc)
                                    <tr>
                                        <td class="small fw-bold">{{ strtoupper(str_replace('_',' ', $doc->doc_type)) }}</td>
                                        <td><span class="badge bg-success-subtle text-success">PRESENT</span></td>
                                        <td class="text-end">
                                            <a href="{{ $doc->url }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                <i class="ri-eye-line"></i> Preview
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-3 text-muted">No documents found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Previous Applications Panel --}}
            @if($previousApplications->count())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#prevAppsPanel" role="button" aria-expanded="false">
                    <span><i class="ri-history-line me-1"></i> Previous Applications by This Applicant ({{ $previousApplications->count() }})</span>
                    <i class="ri-arrow-down-s-line"></i>
                </div>
                <div class="collapse" id="prevAppsPanel">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Request</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previousApplications as $prevApp)
                                        <tr>
                                            <td class="small fw-bold">{{ $prevApp->reference }}</td>
                                            <td class="small text-capitalize">{{ $prevApp->application_type ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $pReqType = $prevApp->request_type ?? 'new';
                                                    $pReqBadge = match($pReqType) { 'renewal' => 'warning', 'replacement' => 'info', default => 'success' };
                                                @endphp
                                                <span class="badge bg-{{ $pReqBadge }}">{{ ucfirst($pReqType) }}</span>
                                            </td>
                                            <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $prevApp->status)) }}</span></td>
                                            <td class="small text-muted">{{ $prevApp->created_at?->format('d M Y') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 4) Full Application Audit Trail --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-history-line me-1"></i> Full Audit Trail (Timeline)
                </div>
                <div class="card-body">
                    <div class="timeline-v2">
                        @foreach($auditTrail as $log)
                            <div class="timeline-item pb-3 mb-3 border-bottom border-light">
                                <div class="d-flex justify-content-between">
                                    <span class="small fw-bold text-dark">{{ strtoupper(str_replace('_',' ', $log->action)) }}</span>
                                    <span class="small text-muted">{{ $log->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="small text-muted">
                                    By: {{ $log->user?->name ?? 'System' }} ({{ $log->user_role ?? '—' }})
                                </div>
                                @if($log->meta)
                                    <div class="mt-1 p-2 bg-light rounded small text-dark">
                                        @foreach($log->meta as $mk => $mv)
                                            @if(is_string($mv))
                                                <div><strong>{{ $mk }}:</strong> {{ $mv }}</div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Oversight & Actions --}}
        <div class="col-lg-4">
            {{-- D) Payment Validation Summary (Read-only) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-success">
                    <i class="ri-bank-card-line me-1"></i> Payment Validation
                </div>
                <div class="card-body">
                    @php
                        $lastPayment = $application->payments->last();
                    @endphp
                    @if($lastPayment)
                        <div class="mb-3">
                            <label class="small text-muted d-block">Status</label>
                            <span class="badge bg-success-subtle text-success border-success px-3">
                                {{ strtoupper($lastPayment->status) }}
                            </span>
                        </div>
                        <div class="mb-2 small">
                            <strong>Method:</strong> {{ strtoupper($lastPayment->method) }} ({{ $lastPayment->source }})<br>
                            <strong>Amount:</strong> {{ $lastPayment->amount }} {{ $lastPayment->currency }}<br>
                            <strong>Reference:</strong> {{ $lastPayment->reference }}<br>
                            <strong>Date:</strong> {{ $lastPayment->confirmed_at?->format('d M Y') ?? '—' }}
                        </div>
                        <hr>
                        <div class="small text-muted">
                            <i class="ri-checkbox-circle-line me-1"></i> Verified by Accounts
                        </div>
                    @else
                        <div class="alert alert-warning small mb-0">No payment records found.</div>
                    @endif
                </div>
            </div>

            {{-- 5) Issuance & Print Tracking --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-printer-line me-1"></i> Issuance & Printing
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted d-block">Current Status</label>
                        <span class="fw-bold">{{ strtoupper(str_replace('_',' ', $application->status)) }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Print Count</label>
                        <span class="h4 fw-bold {{ $application->print_count > 1 ? 'text-danger' : '' }}">
                            {{ $application->print_count }}
                        </span>
                    </div>
                    @if($application->printLogs->count() > 0)
                        <div class="list-group list-group-flush border-top mt-2">
                            @foreach($application->printLogs as $pl)
                                <div class="list-group-item px-0 py-2 small">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ strtoupper($pl->document_type) }}</strong>
                                        <span>{{ $pl->printed_at->format('d M H:i') }}</span>
                                    </div>
                                    <div class="text-muted">By: {{ $pl->printedBy?->name }}</div>
                                    <div class="text-muted">Reason: {{ $pl->reason }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- E) Supervisory Controls --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-shield-check-line me-1" style="color:var(--zmc-accent)"></i> Registrar Oversight
                </div>
                <div class="card-body">

                    {{-- Reviewed Toggle --}}
                    <div class="mb-3 p-3 rounded" style="background:#f0fdf4;">
                        <label class="form-label small fw-bold text-success d-block mb-1">
                            <i class="ri-checkbox-circle-line me-1"></i> Reviewed Status
                        </label>
                        @if($application->registrar_reviewed_at)
                            <div class="small text-success mb-2">
                                ✅ Reviewed by {{ $application->registrarReviewedBy?->name ?? 'Registrar' }}
                                on {{ \Carbon\Carbon::parse($application->registrar_reviewed_at)->format('d M Y H:i') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('staff.registrar.applications.toggle-reviewed', $application) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm w-100 {{ $application->registrar_reviewed_at ? 'btn-outline-secondary' : 'btn-success' }}">
                                {{ $application->registrar_reviewed_at ? 'Unmark Reviewed' : 'Mark as Reviewed' }}
                            </button>
                        </form>
                    </div>

                    {{-- Flag Anomaly --}}
                    <div class="mb-3 p-3 rounded {{ $application->is_flagged ? 'bg-danger-subtle' : 'bg-light' }}">
                        <label class="form-label small fw-bold {{ $application->is_flagged ? 'text-danger' : 'text-muted' }} d-block mb-1">
                            <i class="ri-error-warning-line me-1"></i>
                            {{ $application->is_flagged ? 'Anomaly Flagged' : 'Flag Anomaly' }}
                        </label>
                        @if($application->is_flagged && $application->flag_notes)
                            <div class="small text-danger fst-italic mb-2">{{ $application->flag_notes }}</div>
                        @endif
                        <button type="button" class="btn btn-sm w-100 {{ $application->is_flagged ? 'btn-outline-danger' : 'btn-danger' }}"
                                data-bs-toggle="modal" data-bs-target="#flagModalShow">
                            <i class="ri-flag-2-line me-1"></i> {{ $application->is_flagged ? 'Update Flag' : 'Flag this Application' }}
                        </button>
                    </div>

                    {{-- Message Officer --}}
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary w-100"
                                data-bs-toggle="modal" data-bs-target="#messageModalShow">
                            <i class="ri-message-2-line me-1"></i> Message Accreditation Officer
                        </button>
                    </div>

                    {{-- Reassign to Officer --}}
                    @if(isset($officers) && $officers->count())
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                data-bs-toggle="modal" data-bs-target="#reassignOfficerModalShow">
                            <i class="ri-user-received-line me-1"></i> Reassign to Officer
                        </button>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}

{{-- Flag Anomaly Modal --}}
<div class="modal fade" id="flagModalShow" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.flag-anomaly', $application) }}">
            @csrf
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger"><i class="ri-error-warning-line me-1"></i> Flag Anomaly</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Flagging will alert the Accreditation Officer and mark this application for special attention.</p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nature of Anomaly / Concern</label>
                    <textarea name="flag_notes" class="form-control border-danger" rows="4" required
                        placeholder="Describe the issue clearly…">{{ $application->flag_notes }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="ri-flag-2-line me-1"></i> Submit Flag
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Message Officer Modal --}}
<div class="modal fade" id="messageModalShow" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.message-officer', $application) }}">
            @csrf
            <div class="modal-header border-primary">
                <h5 class="modal-title text-primary"><i class="ri-message-2-line me-1"></i> Message Accreditation Officer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Regarding: <strong>{{ $application->reference }}</strong></p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Message / Guidance</label>
                    <textarea name="message" class="form-control" rows="4" required
                        placeholder="Provide guidance or request clarification from the Officer…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-send-plane-line me-1"></i> Send Message
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reassign to Officer Modal --}}
@if(isset($officers) && $officers->count())
<div class="modal fade" id="reassignOfficerModalShow" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('staff.registrar.applications.reassign-category', $application) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-user-received-line me-1"></i> Reassign to Accreditation Officer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Select Officer</label>
                    <select name="officer_id" class="form-select" required>
                        <option value="">— Choose Officer —</option>
                        @foreach($officers as $off)
                            <option value="{{ $off->id }}" {{ $application->assigned_officer_id == $off->id ? 'selected' : '' }}>
                                {{ $off->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Reason (Optional)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Why is this being reassigned?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark">
                    <i class="ri-user-received-line me-1"></i> Reassign
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .timeline-v2 { position: relative; }
    .timeline-item { position: relative; padding-left: 20px; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #2563eb;
    }
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 15px;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }
</style>
@endpush
