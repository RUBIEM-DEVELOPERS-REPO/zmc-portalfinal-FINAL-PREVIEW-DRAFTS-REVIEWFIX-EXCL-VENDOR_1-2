@extends('layouts.portal')

@section('title', 'Document Settings - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">Document Settings & Rules</h2>
            <p class="text-slate-600 m-0 small mt-1">Configure Required Documents & Validation Logic</p>
        </div>
        <div>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-slate-100 rounded-pill px-3 fw-bold small text-slate-600">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0">Document Type Matrix</h6>
                    <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">Add Document Type</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0 small">
                        <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 uppercase fw-bold py-3">Document Name</th>
                                <th class="uppercase fw-bold py-3">Applicability</th>
                                <th class="uppercase fw-bold py-3">Required</th>
                                <th class="uppercase fw-bold py-3">Max Size (MB)</th>
                                <th class="text-end pe-4 uppercase fw-bold py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 fw-bold">ID Photo</td>
                                <td>Journalists</td>
                                <td><span class="badge bg-success-subtle text-success">Yes</span></td>
                                <td>2.0</td>
                                <td class="text-end pe-4"><button class="btn btn-sm btn-icon border-0 bg-transparent text-slate-400"><i class="ri-edit-line"></i></button></td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Letter of Employment</td>
                                <td>Journalists</td>
                                <td><span class="badge bg-success-subtle text-success">Yes</span></td>
                                <td>5.0</td>
                                <td class="text-end pe-4"><button class="btn btn-sm btn-icon border-0 bg-transparent text-slate-400"><i class="ri-edit-line"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-4">Storage Rules</h6>
                <div class="small text-slate-600">
                    <p class="mb-3 d-flex justify-content-between">
                        <span>Allowed Formats:</span>
                        <span class="fw-bold text-slate-900">PDF, JPG, PNG</span>
                    </p>
                    <p class="mb-3 d-flex justify-content-between">
                        <span>Global Upload Limit:</span>
                        <span class="fw-bold text-slate-900">10MB per application</span>
                    </p>
                    <p class="mb-0 d-flex justify-content-between">
                        <span>Retention Period:</span>
                        <span class="fw-bold text-slate-900">7 Years</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
