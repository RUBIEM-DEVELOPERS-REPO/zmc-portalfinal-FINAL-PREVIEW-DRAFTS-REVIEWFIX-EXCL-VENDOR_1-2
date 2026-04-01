@extends('layouts.portal')
@section('title', 'Payment Oversight')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Payment Oversight</h4>
            <div class="text-muted small mt-1">
                <i class="ri-eye-line me-1"></i>
                Read-only view of payment submissions and verification activities
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-light border btn-sm">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    {{-- READ-ONLY Badge --}}
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="ri-information-line me-2" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Read-Only Access</strong>
                <div class="small">You can view payment information but cannot verify or modify payments. All payment verification is handled by the Accounts department.</div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small fw-bold">Pending</div>
                            <div class="h3 fw-bold mb-0" style="color: #334155;">{{ $kpis['pending'] }}</div>
                        </div>
                        <div class="icon-box" style="background: rgba(250, 204, 21, 0.1);">
                            <i class="ri-time-line" style="color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small fw-bold">Verified</div>
                            <div class="h3 fw-bold mb-0 text-success">{{ $kpis['verified'] }}</div>
                        </div>
                        <div class="icon-box bg-success-subtle">
                            <i class="ri-check-line text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small fw-bold">Rejected</div>
                            <div class="h3 fw-bold mb-0 text-danger">{{ $kpis['rejected'] }}</div>
                        </div>
                        <div class="icon-box bg-danger-subtle">
                            <i class="ri-close-line text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small fw-bold mb-2">By Method</div>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>PayNow:</span>
                            <span class="fw-bold">{{ $kpis['paynow'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Proof:</span>
                            <span class="fw-bold">{{ $kpis['proof'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Waiver:</span>
                            <span class="fw-bold">{{ $kpis['waiver'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Stage KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold">Application Fees</div>
                            <div class="h4 fw-bold mb-0">{{ $kpis['app_fee'] }}</div>
                        </div>
                        <i class="ri-file-list-line" style="font-size: 2rem; color: #64748b;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold">Registration Fees</div>
                            <div class="h4 fw-bold mb-0">{{ $kpis['reg_fee'] }}</div>
                        </div>
                        <i class="ri-file-check-line" style="font-size: 2rem; color: #64748b;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Method</label>
                    <select name="method" class="form-select form-select-sm">
                        <option value="">All Methods</option>
                        <option value="PAYNOW" {{ request('method') === 'PAYNOW' ? 'selected' : '' }}>PayNow</option>
                        <option value="PROOF_UPLOAD" {{ request('method') === 'PROOF_UPLOAD' ? 'selected' : '' }}>Proof Upload</option>
                        <option value="WAIVER" {{ request('method') === 'WAIVER' ? 'selected' : '' }}>Waiver</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Payment Stage</label>
                    <select name="payment_stage" class="form-select form-select-sm">
                        <option value="">All Stages</option>
                        <option value="application_fee" {{ request('payment_stage') === 'application_fee' ? 'selected' : '' }}>Application Fee</option>
                        <option value="registration_fee" {{ request('payment_stage') === 'registration_fee' ? 'selected' : '' }}>Registration Fee</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Application ref..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm shadow-sm" style="background: #facc15; color: #000; font-weight: 600;">
                        <i class="ri-filter-line me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('staff.registrar.payment-oversight') }}" class="btn btn-sm btn-light border">
                        <i class="ri-refresh-line me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Payment List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="ri-list-check-2 me-2"></i> Payment Submissions
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Application</th>
                        <th>Stage</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Verified By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="fw-bold">{{ $payment->application->reference ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border-secondary">
                                    {{ $payment->getStageLabel() }}
                                </span>
                            </td>
                            <td>{{ $payment->getMethodLabel() }}</td>
                            <td class="small">{{ $payment->reference ?? '—' }}</td>
                            <td>{{ $payment->amount ? '$' . number_format($payment->amount, 2) : '—' }}</td>
                            <td class="small">{{ $payment->submitted_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->getStatusColor() }}-subtle text-{{ $payment->getStatusColor() }} border-{{ $payment->getStatusColor() }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="small">
                                @if($payment->verifier)
                                    {{ $payment->verifier->name }}
                                    <div class="text-muted" style="font-size: 11px;">{{ $payment->verified_at?->format('d M H:i') }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('staff.registrar.payment-detail', $payment) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ri-eye-line"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                                <div class="mt-2">No payment submissions found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="card-footer bg-white">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
</style>
@endsection
