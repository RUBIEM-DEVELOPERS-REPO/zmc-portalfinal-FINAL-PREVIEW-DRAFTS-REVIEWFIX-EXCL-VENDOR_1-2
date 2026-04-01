@extends('layouts.portal')
@section('title', 'Payment Detail')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Payment Submission Detail</h4>
            <div class="text-muted small mt-1">
                <i class="ri-eye-line me-1"></i>
                Read-only view
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.registrar.payment-oversight') }}" class="btn btn-light border btn-sm">
                <i class="ri-arrow-left-line"></i> Back to Oversight
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Payment Details --}}
        <div class="col-lg-8">
            {{-- Payment Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-money-dollar-circle-line me-2"></i> Payment Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Payment Stage</label>
                            <span class="badge bg-secondary-subtle text-secondary border-secondary px-3 py-2">
                                {{ $paymentSubmission->getStageLabel() }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Payment Method</label>
                            <span class="fw-bold">{{ $paymentSubmission->getMethodLabel() }}</span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Reference Number</label>
                            <span class="fw-bold">{{ $paymentSubmission->reference ?? '—' }}</span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Amount</label>
                            <span class="fw-bold">
                                @if($paymentSubmission->amount)
                                    {{ $paymentSubmission->currency }} ${{ number_format($paymentSubmission->amount, 2) }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Status</label>
                            <span class="badge bg-{{ $paymentSubmission->getStatusColor() }}-subtle text-{{ $paymentSubmission->getStatusColor() }} border-{{ $paymentSubmission->getStatusColor() }} px-3 py-2">
                                {{ ucfirst($paymentSubmission->status) }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Submitted At</label>
                            <span class="fw-bold">{{ $paymentSubmission->submitted_at?->format('d M Y H:i:s') ?? '—' }}</span>
                        </div>

                        @if($paymentSubmission->verified_at)
                            <div class="col-md-6">
                                <label class="small text-muted d-block">Verified At</label>
                                <span class="fw-bold">{{ $paymentSubmission->verified_at->format('d M Y H:i:s') }}</span>
                            </div>

                            <div class="col-md-6">
                                <label class="small text-muted d-block">Verified By</label>
                                <span class="fw-bold">{{ $paymentSubmission->verifier->name ?? '—' }}</span>
                            </div>
                        @endif

                        @if($paymentSubmission->rejection_reason)
                            <div class="col-12">
                                <label class="small text-muted d-block">Rejection Reason</label>
                                <div class="alert alert-danger mb-0">
                                    {{ $paymentSubmission->rejection_reason }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Proof Metadata --}}
            @if($paymentSubmission->proof_metadata)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        <i class="ri-file-text-line me-2"></i> Proof Details
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @if(isset($paymentSubmission->proof_metadata['payer_name']))
                                <div class="col-md-6">
                                    <label class="small text-muted d-block">Payer Name</label>
                                    <span class="fw-bold">{{ $paymentSubmission->proof_metadata['payer_name'] }}</span>
                                </div>
                            @endif

                            @if(isset($paymentSubmission->proof_metadata['payment_date']))
                                <div class="col-md-6">
                                    <label class="small text-muted d-block">Payment Date</label>
                                    <span class="fw-bold">{{ $paymentSubmission->proof_metadata['payment_date'] }}</span>
                                </div>
                            @endif

                            @if(isset($paymentSubmission->proof_metadata['file_name']))
                                <div class="col-md-6">
                                    <label class="small text-muted d-block">File Name</label>
                                    <span class="fw-bold">{{ $paymentSubmission->proof_metadata['file_name'] }}</span>
                                </div>
                            @endif

                            @if(isset($paymentSubmission->proof_metadata['file_hash']))
                                <div class="col-md-12">
                                    <label class="small text-muted d-block">File Hash (SHA256)</label>
                                    <code class="small">{{ $paymentSubmission->proof_metadata['file_hash'] }}</code>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Application Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-file-list-line me-2"></i> Application Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted d-block">Reference</label>
                            <span class="fw-bold">{{ $paymentSubmission->application->reference }}</span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Application Type</label>
                            <span class="fw-bold text-uppercase">{{ $paymentSubmission->application->application_type }}</span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Applicant</label>
                            <span class="fw-bold">{{ $paymentSubmission->application->applicant->name ?? '—' }}</span>
                        </div>

                        <div class="col-md-6">
                            <label class="small text-muted d-block">Current Status</label>
                            <span class="badge bg-primary-subtle text-primary border-primary">
                                {{ strtoupper(str_replace('_', ' ', $paymentSubmission->application->status)) }}
                            </span>
                        </div>

                        <div class="col-12">
                            <a href="{{ route('staff.registrar.applications.show', $paymentSubmission->application) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-external-link-line me-1"></i> View Full Application
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- All Payment Submissions for This Application --}}
            @if($allPayments->count() > 1)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        <i class="ri-history-line me-2"></i> All Payment Submissions for This Application
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Stage</th>
                                    <th>Method</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Verified By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allPayments as $p)
                                    <tr class="{{ $p->id === $paymentSubmission->id ? 'table-active' : '' }}">
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border-secondary">
                                                {{ $p->getStageLabel() }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $p->getMethodLabel() }}</td>
                                        <td class="small">{{ $p->submitted_at?->format('d M Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $p->getStatusColor() }}-subtle text-{{ $p->getStatusColor() }} border-{{ $p->getStatusColor() }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $p->verifier->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Timeline --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="ri-time-line me-2"></i> Activity Timeline
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($paymentSubmission->verified_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $paymentSubmission->status === 'verified' ? 'success' : 'danger' }}"></div>
                                <div class="timeline-content">
                                    <div class="fw-bold small">
                                        {{ $paymentSubmission->status === 'verified' ? 'Verified' : 'Rejected' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $paymentSubmission->verified_at->format('d M Y H:i:s') }}
                                    </div>
                                    <div class="small">By: {{ $paymentSubmission->verifier->name ?? 'System' }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="fw-bold small">Submitted</div>
                                <div class="text-muted" style="font-size: 11px;">
                                    {{ $paymentSubmission->submitted_at?->format('d M Y H:i:s') }}
                                </div>
                                <div class="small">Method: {{ $paymentSubmission->getMethodLabel() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Read-Only Notice --}}
            <div class="alert alert-info mt-4">
                <div class="d-flex align-items-start">
                    <i class="ri-information-line me-2" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Read-Only Access</strong>
                        <div class="small mt-1">
                            You are viewing this payment submission for oversight purposes only. 
                            All payment verification actions are handled by the Accounts department.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background: #e2e8f0;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-content {
    padding-left: 10px;
}
</style>
@endsection
