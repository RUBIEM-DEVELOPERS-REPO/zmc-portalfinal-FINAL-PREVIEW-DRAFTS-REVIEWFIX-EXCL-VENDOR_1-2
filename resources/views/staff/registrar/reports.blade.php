@extends('layouts.portal')
@section('title', 'Registrar Reports')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <h4 class="fw-bold m-0">Registrar Reports</h4>
        <div class="text-muted small">Operational & Compliance Oversight</div>
    </div>

    <form action="{{ url()->current() }}" method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ url()->current() }}" class="btn btn-light border w-100">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="text-muted small fw-bold text-uppercase">Approvals</div>
                <div class="h2 fw-bold text-success mb-0">{{ $approvals }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="text-muted small fw-bold text-uppercase">Reassignments</div>
                <div class="h2 fw-bold text-warning mb-0">{{ $reassignments }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="text-muted small fw-bold text-uppercase">Total Prints</div>
                <div class="h2 fw-bold text-primary mb-0">{{ $totalPrints }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-4">
                <div class="text-muted small fw-bold text-uppercase">Cert Edits</div>
                <div class="h2 fw-bold text-info mb-0">{{ $edits }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Recent Approvals (Last 5)</div>
                <div class="card-body p-0">
                    {{-- Table placeholder --}}
                    <div class="text-center py-5 text-muted small">Detailed breakdown available in Export.</div>
                </div>
                <div class="card-footer bg-white border-0">
                    <button class="btn btn-sm btn-outline-dark w-100">
                        <i class="ri-download-line"></i> Export PDF Report
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Audit Exceptions</div>
                <div class="card-body p-0">
                     {{-- Placeholder for flagged items --}}
                     <div class="text-center py-5 text-muted small">No critical exceptions in selected range.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
