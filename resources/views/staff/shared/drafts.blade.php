@extends('layouts.staff')

@section('title', 'Draft Applications')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">Draft Applications</h4>
            <p class="text-muted small m-0">View incomplete draft applications from applicants</p>
        </div>
        <a href="{{ route('staff.' . $role . '.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Back to Dashboard
        </a>
    </div>

    {{-- Draft Retention Policy Notice --}}
    <div class="alert alert-info border-0 mb-4" style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6 !important;">
        <div class="d-flex align-items-start gap-2">
            <i class="ri-information-line" style="color: #3b82f6; font-size: 1.2rem; margin-top: 2px;"></i>
            <div class="small">
                <strong style="color: #1e40af;">System Notice:</strong> 
                Draft applications are automatically deleted after <strong>14 days (2 weeks)</strong> of inactivity. 
                Applicants are informed of this retention policy and must complete their applications within this timeframe.
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold m-0">Draft Management</h6>
                <p class="text-slate-600 small m-0 fw-medium">View user draft applications</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-secondary">{{ $drafts->total() }} Total Drafts</span>
                <span class="badge bg-warning text-dark">
                    {{ $drafts->filter(fn($d) => $d->updated_at->diffInDays(now()) > 10)->count() }} Expiring Soon
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                        <tr>
                            <th class="ps-4 small text-slate-700 uppercase fw-bold py-3">Reference</th>
                            <th class="small text-slate-700 uppercase fw-bold py-3">User</th>
                            <th class="small text-slate-700 uppercase fw-bold py-3">Type</th>
                            <th class="small text-slate-700 uppercase fw-bold py-3">Last Activity</th>
                            <th class="small text-slate-700 uppercase fw-bold py-3">Days Remaining</th>
                            <th class="small text-slate-700 uppercase fw-bold py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($drafts as $draft)
                        @php
                            $daysSinceUpdate = $draft->updated_at->diffInDays(now());
                            $daysRemaining = max(0, 14 - $daysSinceUpdate);
                            $isExpiringSoon = $daysRemaining <= 4;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-slate-900 small">{{ $draft->reference ?? 'N/A' }}</div>
                                <div class="text-slate-500" style="font-size: 11px;">ID: {{ $draft->id }}</div>
                            </td>
                            <td>
                                <div class="small fw-medium text-slate-700">{{ $draft->applicant?->name ?? 'Unknown' }}</div>
                                <div class="text-slate-600 fw-medium" style="font-size: 11px;">{{ $draft->applicant?->email ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst($draft->application_type ?? 'Unknown') }}
                                </span>
                                <div class="text-slate-500" style="font-size: 11px;">
                                    {{ ucfirst($draft->request_type ?? 'New') }}
                                </div>
                            </td>
                            <td>
                                <div class="small text-slate-600">{{ $draft->updated_at->diffForHumans() }}</div>
                                <div class="text-slate-500" style="font-size: 11px;">
                                    {{ $draft->updated_at->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                        <div class="progress-bar {{ $isExpiringSoon ? 'bg-danger' : 'bg-warning' }}" 
                                             style="width: {{ ($daysRemaining / 14) * 100 }}%"></div>
                                    </div>
                                    <span class="small fw-bold {{ $isExpiringSoon ? 'text-danger' : 'text-slate-700' }}">
                                        {{ $daysRemaining }}d
                                    </span>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                @if(in_array($role, ['it', 'officer', 'registrar']))
                                    <a href="{{ route('staff.' . $role . '.drafts.review', $draft) }}" 
                                       class="btn btn-sm btn-slate-100 border text-slate-600 py-1 px-3 rounded-pill fw-bold">
                                        <i class="ri-eye-line me-1"></i> Review
                                    </a>
                                @elseif($role === 'accounts')
                                    <a href="{{ route('staff.accounts.drafts.view', $draft) }}" 
                                       class="btn btn-sm btn-slate-100 border text-slate-600 py-1 px-3 rounded-pill fw-bold">
                                        <i class="ri-eye-line me-1"></i> View
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-slate-700">
                                <i class="ri-draft-line fs-1 text-slate-300 mb-2 d-block"></i>
                                <div class="fw-bold">No active drafts found.</div>
                                <div class="small text-slate-500">All draft applications have been submitted or expired.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top border-slate-100">
                {{ $drafts->links() }}
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-4 d-flex gap-4 small text-muted">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-warning rounded" style="width: 12px; height: 12px;"></div>
            <span>Active (5-13 days remaining)</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="bg-danger rounded" style="width: 12px; height: 12px;"></div>
            <span>Expiring Soon (0-4 days remaining)</span>
        </div>
    </div>
</div>
@endsection
